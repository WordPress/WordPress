<?php
function check_upload_size( $file ) {
	if ( get_site_option( 'upload_space_check_disabled' ) ) {
		return $file;
	}
	if( $file['error'] != '0' ) // there's already an error
		return $file;

	if ( defined( 'WP_IMPORTING' ) )
		return $file;

	$space_allowed = 1048576 * get_space_allowed();
	$space_used = get_dirsize( BLOGUPLOADDIR );
	$space_left = $space_allowed - $space_used;
	$file_size = filesize( $file['tmp_name'] );
	if( $space_left < $file_size )
		$file['error'] = sprintf( __( 'Not enough space to upload. %1$s Kb needed.' ), number_format( ($file_size - $space_left) /1024 ) );
	if( $file_size > ( 1024 * get_site_option( 'fileupload_maxk', 1500 ) ) )
		$file['error'] = sprintf(__('This file is too big. Files must be less than %1$s Kb in size.'), get_site_option( 'fileupload_maxk', 1500 ) );
	if( upload_is_user_over_quota( false ) ) {
		$file['error'] = __('You have used your space quota. Please delete files before uploading.');
	}
	if( $file['error'] != '0' )
		wp_die( $file['error'] . ' <a href="javascript:history.go(-1)">' . __( 'Back' ) . '</a>' );

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'check_upload_size' );

function wpmu_delete_blog($blog_id, $drop = false) {
	global $wpdb;

	if ( $blog_id != $wpdb->blogid ) {
		$switch = true;
		switch_to_blog($blog_id);
	}

	do_action('delete_blog', $blog_id, $drop);

	$users = get_users_of_blog($blog_id);

	// Remove users from this blog.
	if ( !empty($users) ) foreach ($users as $user) {
		remove_user_from_blog($user->user_id, $blog_id);
	}

	update_blog_status( $blog_id, 'deleted', 1 );

	if ( $drop ) {
		$drop_tables = $wpdb->get_results("show tables LIKE '". $wpdb->base_prefix . $blog_id . "\_%'", ARRAY_A); 
		$drop_tables = apply_filters( 'wpmu_drop_tables', $drop_tables ); 

		reset( $drop_tables );
		foreach ( (array) $drop_tables as $drop_table) {
			$wpdb->query( "DROP TABLE IF EXISTS ". current( $drop_table ) ."" );
		}
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->blogs WHERE blog_id = %d", $blog_id) );
		$dir = apply_filters( 'wpmu_delete_blog_upload_dir', constant( "WP_CONTENT_DIR" ) . "/blogs.dir/{$blog_id}/files/", $blog_id );
		$dir = rtrim($dir, DIRECTORY_SEPARATOR);
		$top_dir = $dir;
		$stack = array($dir);
		$index = 0;

		while ($index < count($stack)) {
			# Get indexed directory from stack
			$dir = $stack[$index];

			$dh = @ opendir($dir);
			if ($dh) {
				while (($file = @ readdir($dh)) !== false) {
					if ($file == '.' or $file == '..')
						continue;

					if (@ is_dir($dir . DIRECTORY_SEPARATOR . $file))
						$stack[] = $dir . DIRECTORY_SEPARATOR . $file;
					else if (@ is_file($dir . DIRECTORY_SEPARATOR . $file))
						@ unlink($dir . DIRECTORY_SEPARATOR . $file);
				}
			}
			$index++;
		}

		$stack = array_reverse($stack);  // Last added dirs are deepest
		foreach( (array) $stack as $dir) {
			if ( $dir != $top_dir)
			@rmdir($dir);
		}
	}
	$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->usermeta} WHERE meta_key = %s", 'wp_{$blog_id}_autosave_draft_ids') );
	$blogs = get_site_option( "blog_list" );
	if ( is_array( $blogs ) ) {
		foreach( $blogs as $n => $blog ) {
			if( $blog[ 'blog_id' ] == $blog_id ) {
				unset( $blogs[ $n ] );
			}
		}
		update_site_option( 'blog_list', $blogs );
	}

	if ( $switch === true )
		restore_current_blog();
}

function wpmu_delete_user($id) {
	global $wpdb;

	$id = (int) $id;
	$user = get_userdata($id);

	do_action('wpmu_delete_user', $id);

	$blogs = get_blogs_of_user($id);

	if ( ! empty($blogs) ) {
		foreach ($blogs as $blog) {
			switch_to_blog($blog->userblog_id);
			remove_user_from_blog($id, $blog->userblog_id);

			$post_ids = $wpdb->get_col( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_author = %d", $id ) );
			foreach ( (array) $post_ids as $post_id ) {
				wp_delete_post($post_id);
			}

			// Clean links
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->links WHERE link_owner = %d", $id) );

			restore_current_blog();
		}
	}

	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->users WHERE ID = %d", $id) );
	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id = %d", $id) );

	wp_cache_delete($id, 'users');
	wp_cache_delete($user->user_login, 'userlogins');

	return true;
}

function confirm_delete_users( $users ) {
	if( !is_array( $users ) )
		return false;

	echo '<p>' . __( 'Transfer posts before deleting users:' ) . '</p>';

	echo '<form action="ms-edit.php?action=allusers" method="post">';
	echo '<input type="hidden" name="alluser_transfer_delete" />';
	wp_nonce_field( 'allusers' );
	foreach ( (array) $_POST['allusers'] as $key => $val ) {
		if( $val != '' && $val != '0' ) {
			$user = new WP_User( $val );
			if ( in_array( $user->user_login, get_site_option( 'site_admins', array( 'admin' ) ) ) ) {
				wp_die( sprintf( __( 'Warning! User cannot be deleted. The user %s is a site admnistrator.' ), $user->user_login ) );
			}
			echo "<input type='hidden' name='user[]' value='{$val}'/>\n";
			$blogs = get_blogs_of_user( $val, true );
			if( !empty( $blogs ) ) {
				foreach ( (array) $blogs as $key => $details ) {
					$blog_users = get_users_of_blog( $details->userblog_id );
					if( is_array( $blog_users ) && !empty( $blog_users ) ) {
						echo "<p><a href='http://{$details->domain}{$details->path}'>{$details->blogname}</a> ";
						echo "<select name='blog[$val][{$key}]'>";
						$out = '';
						foreach( $blog_users as $user ) {
							if( $user->user_id != $val )
								$out .= "<option value='{$user->user_id}'>{$user->user_login}</option>";
						}
						if( $out == '' )
							$out = "<option value='1'>admin</option>";
						echo $out;
						echo "</select>\n";
					}
				}
			}
		}
	}
	echo "<br /><input type='submit' value='" . __( 'Delete user and transfer posts' ) . "' />";
	echo "</form>";
	return true;
}

function wpmu_get_blog_allowedthemes( $blog_id = 0 ) {
	$themes = get_themes();

	if( $blog_id != 0 )
		switch_to_blog( $blog_id );

	$blog_allowed_themes = get_option( "allowedthemes" );
	if( !is_array( $blog_allowed_themes ) || empty( $blog_allowed_themes ) ) { // convert old allowed_themes to new allowedthemes
		$blog_allowed_themes = get_option( "allowed_themes" );

		if( is_array( $blog_allowed_themes ) ) {
			foreach( (array) $themes as $key => $theme ) {
				$theme_key = wp_specialchars( $theme[ 'Stylesheet' ] );
				if( isset( $blog_allowed_themes[ $key ] ) == true ) {
					$blog_allowedthemes[ $theme_key ] = 1;
				}
			}
			$blog_allowed_themes = $blog_allowedthemes;
			add_option( "allowedthemes", $blog_allowed_themes );
			delete_option( "allowed_themes" );
		}
	}

	if( $blog_id != 0 )
		restore_current_blog();

	return $blog_allowed_themes;
}

function update_option_new_admin_email($old_value, $value) {
	global $current_site;
	if ( $value == get_option( 'admin_email' ) || !is_email( $value ) )
		return;

	$hash = md5( $value. time() .mt_rand() );
	$new_admin_email = array(
		"hash" => $hash,
		"newemail" => $value
	);
	update_option( 'adminhash', $new_admin_email );
	
	$content = apply_filters( 'new_admin_email_content', __("Dear user,

You recently requested to have the administration email address on 
your blog changed.
If this is correct, please click on the following link to change it:
###ADMIN_URL###

You can safely ignore and delete this email if you do not want to
take this action.

This email has been sent to ###EMAIL###

Regards,
All at ###SITENAME###
###SITEURL###"), $new_admin_email );
	
	$content = str_replace('###ADMIN_URL###', clean_url(get_option( "siteurl" ).'/wp-admin/options.php?adminhash='.$hash), $content);
	$content = str_replace('###EMAIL###', $value, $content);
	$content = str_replace('###SITENAME###', get_site_option( 'site_name' ), $content);
	$content = str_replace('###SITEURL###', 'http://' . $current_site->domain . $current_site->path, $content);
	
	wp_mail( $value, sprintf(__('[%s] New Admin Email Address'), get_option('blogname')), $content );
}
add_action('update_option_new_admin_email', 'update_option_new_admin_email', 10, 2);

function profile_page_email_warning_ob_start() {
	ob_start( 'profile_page_email_warning_ob_content' );
}

function profile_page_email_warning_ob_content( $content ) {
	$content = str_replace( ' class="regular-text" /> Required.</td>', ' class="regular-text" /> Required. (You will be sent an email to confirm the change)</td>', $content );
	return $content;
}

function update_profile_email() {
	global $current_user, $wpdb;
	if( isset( $_GET[ 'newuseremail' ] ) && $current_user->ID ) {
		$new_email = get_option( $current_user->ID . '_new_email' );
		if( $new_email[ 'hash' ] == $_GET[ 'newuseremail' ] ) {
			$user->ID = $current_user->ID;
			$user->user_email = wp_specialchars( trim( $new_email[ 'newemail' ] ) );
			if ( $wpdb->get_var( $wpdb->prepare( "SELECT user_login FROM {$wpdb->signups} WHERE user_login = %s", $current_user->user_login ) ) ) {
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->signups} SET user_email = %s WHERE user_login = %s", $user->user_email, $current_user->user_login ) );
			}
			wp_update_user( get_object_vars( $user ) );
			delete_option( $current_user->ID . '_new_email' );
			wp_redirect( add_query_arg( array('updated' => 'true'), admin_url( 'profile.php' ) ) );
			die();
		}
	}
}

function send_confirmation_on_profile_email() {
	global $errors, $wpdb, $current_user, $current_site;
	if ( ! is_object($errors) )
		$errors = new WP_Error();

	if( $current_user->id != $_POST[ 'user_id' ] )
		return false;

	if( $current_user->user_email != $_POST[ 'email' ] ) {
		if ( !is_email( $_POST[ 'email' ] ) ) {
			$errors->add( 'user_email', __( "<strong>ERROR</strong>: The e-mail address isn't correct." ), array( 'form-field' => 'email' ) );
			return;
		}

		if( $wpdb->get_var( $wpdb->prepare( "SELECT user_email FROM {$wpdb->users} WHERE user_email=%s", $_POST[ 'email' ] ) ) ) {
			$errors->add( 'user_email', __( "<strong>ERROR</strong>: The e-mail address is already used." ), array( 'form-field' => 'email' ) );
			delete_option( $current_user->ID . '_new_email' );
			return;
		}

		$hash = md5( $_POST[ 'email' ] . time() . mt_rand() );
		$new_user_email = array(
				"hash" => $hash,
				"newemail" => $_POST[ 'email' ]
				);
		update_option( $current_user->ID . '_new_email', $new_user_email );

		$content = apply_filters( 'new_user_email_content', __("Dear user,

You recently requested to have the email address on your account changed.
If this is correct, please click on the following link to change it:
###ADMIN_URL###

You can safely ignore and delete this email if you do not want to
take this action.

This email has been sent to ###EMAIL###

Regards,
All at ###SITENAME###
###SITEURL###"), $new_user_email );

		$content = str_replace('###ADMIN_URL###', clean_url(get_option( "siteurl" ).'/wp-admin/profile.php?newuseremail='.$hash), $content);
		$content = str_replace('###EMAIL###', $_POST[ 'email' ], $content);
		$content = str_replace('###SITENAME###', get_site_option( 'site_name' ), $content);
		$content = str_replace('###SITEURL###', 'http://' . $current_site->domain . $current_site->path, $content);

		wp_mail( $_POST[ 'email' ], sprintf(__('[%s] New Email Address'), get_option('blogname')), $content );
		$_POST[ 'email' ] = $current_user->user_email;
	}
}
add_action( 'personal_options_update', 'send_confirmation_on_profile_email' );

function new_user_email_admin_notice() {
	global $current_user;
	if( strpos( $_SERVER['PHP_SELF'], 'profile.php' ) && isset( $_GET[ 'updated' ] ) && $email = get_option( $current_user->ID . '_new_email' ) )
		echo "<div id='update-nag'>" . sprintf( __( "Your email address has not been updated yet. Please check your inbox at %s for a confirmation email." ), $email[ 'newemail' ] ) . "</div>";
}
add_action( 'admin_notices', 'new_user_email_admin_notice' );

function get_site_allowed_themes() {
	$themes = get_themes();
	$allowed_themes = get_site_option( 'allowedthemes' );
	if( !is_array( $allowed_themes ) || empty( $allowed_themes ) ) {
		$allowed_themes = get_site_option( "allowed_themes" ); // convert old allowed_themes format
		if( !is_array( $allowed_themes ) ) {
			$allowed_themes = array();
		} else {
			foreach( (array) $themes as $key => $theme ) {
				$theme_key = wp_specialchars( $theme[ 'Stylesheet' ] );
				if( isset( $allowed_themes[ $key ] ) == true ) {
					$allowedthemes[ $theme_key ] = 1;
				}
			}
			$allowed_themes = $allowedthemes;
		}
	}
	return $allowed_themes;
}

function get_space_allowed() {
	$spaceAllowed = get_option("blog_upload_space");
	if( $spaceAllowed == false ) 
		$spaceAllowed = get_site_option("blog_upload_space");
	if( empty($spaceAllowed) || !is_numeric($spaceAllowed) )
		$spaceAllowed = 50;

	return $spaceAllowed;
}

function display_space_usage() {
	$space = get_space_allowed();
	$used = get_dirsize( BLOGUPLOADDIR )/1024/1024;

	if ($used > $space) $percentused = '100';
	else $percentused = ( $used / $space ) * 100;

	if( $space > 1000 ) {
		$space = number_format( $space / 1024 );
		$space .= __('GB');
	} else {
		$space .= __('MB');
	}
	?>
	<strong><?php printf(__('Used: %1s%% of %2s'), number_format($percentused), $space );?></strong> 
	<?php
}

// Display File upload quota on dashboard
function dashboard_quota() {	
	if ( get_site_option( 'upload_space_check_disabled' ) ) {
		return true;
	}
	$quota = get_space_allowed();
	$used = get_dirsize( BLOGUPLOADDIR )/1024/1024;

	if ($used > $quota) $percentused = '100';
	else $percentused = ( $used / $quota ) * 100;
	$percentused = number_format($percentused);
	$used = round($used,2);
	$used_color = ($used < 70) ? (($used >= 40) ? 'waiting' : 'approved') : 'spam';
	?>
	<p class="sub musub"><?php _e("Storage Space <a href='upload.php' title='Manage Uploads...'>&raquo;</a>"); ?></p>
	<div class="table">
	<table>
		<tr class="first">
			<td class="first b b-posts"><?php printf( __( '<a href="upload.php" title="Manage Uploads..." class="musublink">%sMB</a>' ), $quota ); ?></td>
			<td class="t posts"><?php _e('Space Allowed'); ?></td>
			<td class="b b-comments"><?php printf( __( '<a href="upload.php" title="Manage Uploads..." class="musublink">%1sMB (%2s%%)</a>' ), $used, $percentused ); ?>
			<td class="last t comments <?php echo $used_color;?>"><?php _e('Space Used');?></td>
		</tr>
	</table>
	</div>
	<?php
}
if( current_user_can('edit_posts') )
	add_action('activity_box_end', 'dashboard_quota');

// Edit blog upload space setting on Edit Blog page
function upload_space_setting( $id ) {
	$quota = get_blog_option($id, "blog_upload_space"); 
	if( !$quota )
		$quota = '';
	
	?>
	<tr>
		<th><?php _e('Blog Upload Space Quota'); ?></th>
		<td><input type="text" size="3" name="option[blog_upload_space]" value="<?php echo $quota; ?>" /><?php _e('MB (Leave blank for site default)'); ?></td>
	</tr>
	<?php
}
add_action('wpmueditblogaction', 'upload_space_setting');

function update_user_status( $id, $pref, $value, $refresh = 1 ) {
	global $wpdb;

	$wpdb->update( $wpdb->users, array( $pref => $value ), array( 'ID' => $id ) );

	if( $refresh == 1 )
		refresh_user_details($id);
	
	if( $pref == 'spam' ) {
		if( $value == 1 ) 
			do_action( "make_spam_user", $id );
		else
			do_action( "make_ham_user", $id );
	}

	return $value;
}

function refresh_user_details($id) {
	$id = (int) $id;
	
	if ( !$user = get_userdata( $id ) )
		return false;

	wp_cache_delete($id, 'users');
	wp_cache_delete($user->user_login, 'userlogins');
	return $id;
}

/*
  Determines if the available space defined by the admin has been exceeded by the user
*/
function wpmu_checkAvailableSpace() {
	if ( get_site_option( 'upload_space_check_disabled' ) ) {
		return true;
	}
	$spaceAllowed = get_space_allowed();

	$dirName = trailingslashit( BLOGUPLOADDIR );
	if (!(is_dir($dirName) && is_readable($dirName))) 
		return; 

  	$dir = dir($dirName);
   	$size = 0;

	while($file = $dir->read()) {
		if ($file != '.' && $file != '..') {
			if (is_dir( $dirName . $file)) {
				$size += get_dirsize($dirName . $file);
			} else {
				$size += filesize($dirName . $file);
			}
		}
	}
	$dir->close();
	$size = $size / 1024 / 1024;

	if( ($spaceAllowed - $size) <= 0 ) {
		wp_die( __('Sorry, you must delete files before you can upload any more.') );
	}
}
add_action('pre-upload-ui','wpmu_checkAvailableSpace');

function format_code_lang( $code = '' ) {
	$code = strtolower(substr($code, 0, 2));
	$lang_codes = array('aa' => 'Afar',  'ab' => 'Abkhazian',  'af' => 'Afrikaans',  'ak' => 'Akan',  'sq' => 'Albanian',  'am' => 'Amharic',  'ar' => 'Arabic',  'an' => 'Aragonese',  'hy' => 'Armenian',  'as' => 'Assamese',  'av' => 'Avaric',  'ae' => 'Avestan',  'ay' => 'Aymara',  'az' => 'Azerbaijani',  'ba' => 'Bashkir',  'bm' => 'Bambara',  'eu' => 'Basque',  'be' => 'Belarusian',  'bn' => 'Bengali',  'bh' => 'Bihari',  'bi' => 'Bislama',  'bs' => 'Bosnian',  'br' => 'Breton',  'bg' => 'Bulgarian',  'my' => 'Burmese',  'ca' => 'Catalan; Valencian',  'ch' => 'Chamorro',  'ce' => 'Chechen',  'zh' => 'Chinese',  'cu' => 'Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic',  'cv' => 'Chuvash',  'kw' => 'Cornish',  'co' => 'Corsican',  'cr' => 'Cree',  'cs' => 'Czech',  'da' => 'Danish',  'dv' => 'Divehi; Dhivehi; Maldivian',  'nl' => 'Dutch; Flemish',  'dz' => 'Dzongkha',  'en' => 'English',  'eo' => 'Esperanto',  'et' => 'Estonian',  'ee' => 'Ewe',  'fo' => 'Faroese',  'fj' => 'Fijian',  'fi' => 'Finnish',  'fr' => 'French',  'fy' => 'Western Frisian',  'ff' => 'Fulah',  'ka' => 'Georgian',  'de' => 'German',  'gd' => 'Gaelic; Scottish Gaelic',  'ga' => 'Irish',  'gl' => 'Galician',  'gv' => 'Manx',  'el' => 'Greek, Modern',  'gn' => 'Guarani',  'gu' => 'Gujarati',  'ht' => 'Haitian; Haitian Creole',  'ha' => 'Hausa',  'he' => 'Hebrew',  'hz' => 'Herero',  'hi' => 'Hindi',  'ho' => 'Hiri Motu',  'hu' => 'Hungarian',  'ig' => 'Igbo',  'is' => 'Icelandic',  'io' => 'Ido',  'ii' => 'Sichuan Yi',  'iu' => 'Inuktitut',  'ie' => 'Interlingue',  'ia' => 'Interlingua (International Auxiliary Language Association)',  'id' => 'Indonesian',  'ik' => 'Inupiaq',  'it' => 'Italian',  'jv' => 'Javanese',  'ja' => 'Japanese',  'kl' => 'Kalaallisut; Greenlandic',  'kn' => 'Kannada',  'ks' => 'Kashmiri',  'kr' => 'Kanuri',  'kk' => 'Kazakh',  'km' => 'Central Khmer',  'ki' => 'Kikuyu; Gikuyu',  'rw' => 'Kinyarwanda',  'ky' => 'Kirghiz; Kyrgyz',  'kv' => 'Komi',  'kg' => 'Kongo',  'ko' => 'Korean',  'kj' => 'Kuanyama; Kwanyama',  'ku' => 'Kurdish',  'lo' => 'Lao',  'la' => 'Latin',  'lv' => 'Latvian',  'li' => 'Limburgan; Limburger; Limburgish',  'ln' => 'Lingala',  'lt' => 'Lithuanian',  'lb' => 'Luxembourgish; Letzeburgesch',  'lu' => 'Luba-Katanga',  'lg' => 'Ganda',  'mk' => 'Macedonian',  'mh' => 'Marshallese',  'ml' => 'Malayalam',  'mi' => 'Maori',  'mr' => 'Marathi',  'ms' => 'Malay',  'mg' => 'Malagasy',  'mt' => 'Maltese',  'mo' => 'Moldavian',  'mn' => 'Mongolian',  'na' => 'Nauru',  'nv' => 'Navajo; Navaho',  'nr' => 'Ndebele, South; South Ndebele',  'nd' => 'Ndebele, North; North Ndebele',  'ng' => 'Ndonga',  'ne' => 'Nepali',  'nn' => 'Norwegian Nynorsk; Nynorsk, Norwegian',  'nb' => 'Bokmål, Norwegian, Norwegian Bokmål',  'no' => 'Norwegian',  'ny' => 'Chichewa; Chewa; Nyanja',  'oc' => 'Occitan, Provençal',  'oj' => 'Ojibwa',  'or' => 'Oriya',  'om' => 'Oromo',  'os' => 'Ossetian; Ossetic',  'pa' => 'Panjabi; Punjabi',  'fa' => 'Persian',  'pi' => 'Pali',  'pl' => 'Polish',  'pt' => 'Portuguese',  'ps' => 'Pushto',  'qu' => 'Quechua',  'rm' => 'Romansh',  'ro' => 'Romanian',  'rn' => 'Rundi',  'ru' => 'Russian',  'sg' => 'Sango',  'sa' => 'Sanskrit',  'sr' => 'Serbian',  'hr' => 'Croatian',  'si' => 'Sinhala; Sinhalese',  'sk' => 'Slovak',  'sl' => 'Slovenian',  'se' => 'Northern Sami',  'sm' => 'Samoan',  'sn' => 'Shona',  'sd' => 'Sindhi',  'so' => 'Somali',  'st' => 'Sotho, Southern',  'es' => 'Spanish; Castilian',  'sc' => 'Sardinian',  'ss' => 'Swati',  'su' => 'Sundanese',  'sw' => 'Swahili',  'sv' => 'Swedish',  'ty' => 'Tahitian',  'ta' => 'Tamil',  'tt' => 'Tatar',  'te' => 'Telugu',  'tg' => 'Tajik',  'tl' => 'Tagalog',  'th' => 'Thai',  'bo' => 'Tibetan',  'ti' => 'Tigrinya',  'to' => 'Tonga (Tonga Islands)',  'tn' => 'Tswana',  'ts' => 'Tsonga',  'tk' => 'Turkmen',  'tr' => 'Turkish',  'tw' => 'Twi',  'ug' => 'Uighur; Uyghur',  'uk' => 'Ukrainian',  'ur' => 'Urdu',  'uz' => 'Uzbek',  've' => 'Venda',  'vi' => 'Vietnamese',  'vo' => 'Volapük',  'cy' => 'Welsh',  'wa' => 'Walloon',  'wo' => 'Wolof',  'xh' => 'Xhosa',  'yi' => 'Yiddish',  'yo' => 'Yoruba',  'za' => 'Zhuang; Chuang',  'zu' => 'Zulu');
	$lang_codes = apply_filters('lang_codes', $lang_codes, $code);
	return strtr( $code, $lang_codes );
}

function sync_category_tag_slugs( $term, $taxonomy ) {
	if( $taxonomy == 'category' || $taxonomy == 'post_tag' ) {
		if( is_object( $term ) ) {
			$term->slug = sanitize_title( $term->name );
		} else {
			$term[ 'slug' ] = sanitize_title( $term[ 'name' ] );
		}
	}
	return $term;
}
add_filter( 'get_term', 'sync_category_tag_slugs', 10, 2 );

function redirect_user_to_blog() {
	global $current_user, $current_site;
	$c = 0;
	if ( isset( $_GET[ 'c' ] ) )
		$c = (int)$_GET[ 'c' ];
	
	if ( $c >= 5 ) {
		wp_die( __( "You don&#8217;t have permission to view this blog. Please contact the system administrator." ) );
	}
	$c ++;

	$blog = get_active_blog_for_user( $current_user->ID );
	$dashboard_blog = get_dashboard_blog();
	if( is_object( $blog ) ) {
		$protocol = ( is_ssl() ? 'https://' : 'http://' ); 
		wp_redirect( $protocol . $blog->domain . $blog->path . 'wp-admin/?c=' . $c ); // redirect and count to 5, "just in case"
		exit;
	}

	/* 
	   If the user is a member of only 1 blog and the user's primary_blog isn't set to that blog, 
	   then update the primary_blog record to match the user's blog
	 */
	$blogs = get_blogs_of_user( $current_user->ID );

	if ( !empty( $blogs ) ) {
		foreach( $blogs as $blogid => $blog ) {
			if ( $blogid != $dashboard_blog->blog_id && get_usermeta( $current_user->ID , 'primary_blog' ) == $dashboard_blog->blog_id ) {
				update_usermeta( $current_user->ID, 'primary_blog', $blogid );
				continue;
			}
		}
		$blog = get_blog_details( get_usermeta( $current_user->ID , 'primary_blog' ) );
		$protocol = ( is_ssl() ? 'https://' : 'http://' ); 
		wp_redirect( $protocol . $blog->domain . $blog->path . 'wp-admin/?c=' . $c ); // redirect and count to 5, "just in case"
		exit;
	}
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
add_action( 'admin_page_access_denied', 'redirect_user_to_blog', 99 );

function wpmu_menu() {
	// deprecated. See #11763
}

function mu_options( $options ) {
	if ( defined( 'POST_BY_EMAIL' ) ) {
		$writing = array( 'ping_sites' );
	} else {
		$writing = array( 'ping_sites', 'mailserver_login', 'mailserver_pass', 'default_email_category', 'mailserver_port', 'mailserver_url' );
	}
	$removed = array( 
		'general' => array( 'siteurl', 'home', 'admin_email', 'users_can_register', 'default_role' ),
		'reading' => array( 'gzipcompression' ),
		'writing' => $writing,
	);

	$added = array( 'general' => array( 'new_admin_email', 'WPLANG', 'language' ) );

	$options[ 'misc' ] = array();

	$options = remove_option_whitelist( $removed, $options );
	$options = add_option_whitelist( $added, $options );

	return $options;
}
add_filter( 'whitelist_options', 'mu_options' );

function check_import_new_users( $permission ) {
	if ( !is_site_admin() )
		return false;
	return true;
}
add_filter( 'import_allow_create_users', 'check_import_new_users' );
// See "import_allow_fetch_attachments" and "import_attachment_size_limit" filters too.

function mu_css() {
	wp_admin_css( 'css/mu' );
}
add_action( 'admin_head', 'mu_css' );

function mu_dropdown_languages( $lang_files = array(), $current = '' ) {
	$flag = false;	
	$output = array();
					
	foreach ( (array) $lang_files as $val ) {
		$code_lang = basename( $val, '.mo' );
		
		if ( $code_lang == 'en_US' ) { // American English
			$flag = true;
			$ae = __('American English');
			$output[$ae] = '<option value="'.$code_lang.'"'.(($current == $code_lang) ? ' selected="selected"' : '').'> '.$ae.'</option>';
		} elseif ( $code_lang == 'en_GB' ) { // British English
			$flag = true;
			$be = __('British English');
			$output[$be] = '<option value="'.$code_lang.'"'.(($current == $code_lang) ? ' selected="selected"' : '').'> '.$be.'</option>';
		} else {
			$translated = format_code_lang($code_lang);
			$output[$translated] =  '<option value="'.$code_lang.'"'.(($current == $code_lang) ? ' selected="selected"' : '').'> '.$translated.'</option>';
		}
		
	}						
	
	if ( $flag === false ) { // WordPress english
		$output[] = '<option value=""'.((empty($current)) ? ' selected="selected"' : '').'>'.__('English')."</option>";
	}
	
	// Order by name
	uksort($output, 'strnatcasecmp');
	
	$output = apply_filters('mu_dropdown_languages', $output, $lang_files, $current);	
	echo implode("\n\t", $output);	
}

// Only show "Media" upload icon
function mu_media_buttons() {
	global $post_ID, $temp_ID;
	$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);
	$context = apply_filters('media_buttons_context', __('Add media: %s'));
	$media_upload_iframe_src = "media-upload.php?post_id=$uploading_iframe_ID";
	$media_title = __('Add Media');
	$mu_media_buttons = get_site_option( 'mu_media_buttons' );
	$out = '';
	if( $mu_media_buttons[ 'image' ] ) {
		$image_upload_iframe_src = apply_filters('image_upload_iframe_src', "$media_upload_iframe_src&amp;type=image");
		$image_title = __('Add an Image');
		$out .= "<a href='{$image_upload_iframe_src}&amp;TB_iframe=true' id='add_image' class='thickbox' title='$image_title'><img src='images/media-button-image.gif' alt='$image_title' /></a>";
	}
	if( $mu_media_buttons[ 'video' ] ) {
		$video_upload_iframe_src = apply_filters('video_upload_iframe_src', "$media_upload_iframe_src&amp;type=video");
		$video_title = __('Add Video');
		$out .= "<a href='{$video_upload_iframe_src}&amp;TB_iframe=true' id='add_video' class='thickbox' title='$video_title'><img src='images/media-button-video.gif' alt='$video_title' /></a>";
	}
	if( $mu_media_buttons[ 'audio' ] ) {
		$audio_upload_iframe_src = apply_filters('audio_upload_iframe_src', "$media_upload_iframe_src&amp;type=audio");
		$audio_title = __('Add Audio');
		$out .= "<a href='{$audio_upload_iframe_src}&amp;TB_iframe=true' id='add_audio' class='thickbox' title='$audio_title'><img src='images/media-button-music.gif' alt='$audio_title' /></a>";
	}
	$out .= "<a href='{$media_upload_iframe_src}&amp;TB_iframe=true&amp;height=500&amp;width=640' class='thickbox' title='$media_title'><img src='images/media-button-other.gif' alt='$media_title' /></a>";
	printf($context, $out);
}
add_action( 'media_buttons', 'mu_media_buttons' );
remove_action( 'media_buttons', 'media_buttons' );

/* Warn the admin if SECRET SALT information is missing from wp-config.php */
function secret_salt_warning() {
	if( !is_site_admin() )
		return;
	$secret_keys = array( 'NONCE_KEY', 'AUTH_KEY', 'AUTH_SALT', 'LOGGED_IN_KEY', 'LOGGED_IN_SALT', 'SECURE_AUTH_KEY', 'SECURE_AUTH_SALT' );
	$out = '';
	foreach( $secret_keys as $key ) {
		if( !defined( $key ) )
			$out .= "define( '$key', '" . wp_generate_password() . wp_generate_password() . "' );<br />";
	}
	if( $out != '' ) {
		$msg = sprintf( __( 'Warning! WordPress encrypts user cookies, but you must add the following lines to <strong>%swp-config.php</strong> for it to be more secure.<br />Please add the code before the line, <code>/* That\'s all, stop editing! Happy blogging. */</code>' ), ABSPATH );
		$msg .= "<blockquote>$out</blockquote>";

		echo "<div id='update-nag'>$msg</div>";
	}
}
add_action( 'admin_notices', 'secret_salt_warning' );

function mu_dashboard() {
	unregister_sidebar_widget( 'dashboard_plugins' );
}
add_action( 'wp_dashboard_setup', 'mu_dashboard' );

function profile_update_primary_blog() {
	global $current_user;

	$blogs = get_blogs_of_user( $current_user->id );
	if ( isset( $blogs[ $_POST[ 'primary_blog' ] ] ) == false ) {
		return false;
	}

	if ( isset( $_POST['primary_blog'] ) ) {
		update_user_option( $current_user->id, 'primary_blog', (int) $_POST['primary_blog'], true );
	}
}
add_action ( 'myblogs_update', 'profile_update_primary_blog' );

function admin_notice_feed() {
	global $current_user;
	if( substr( $_SERVER[ 'PHP_SELF' ], -19 ) != '/wp-admin/index.php' )
		return;

	if( isset( $_GET[ 'feed_dismiss' ] ) )
		update_user_option( $current_user->id, 'admin_feed_dismiss', $_GET[ 'feed_dismiss' ], true );

	$url = get_site_option( 'admin_notice_feed' );
	if( $url == '' )
		return;
	include_once( ABSPATH . 'wp-includes/rss.php' );
	$rss = @fetch_rss( $url );
	if( isset($rss->items) && 1 <= count($rss->items) ) {
		if( md5( $rss->items[0][ 'title' ] ) == get_user_option( 'admin_feed_dismiss', $current_user->id ) )
			return;
		$item = $rss->items[0];
		$msg = "<h3>" . wp_specialchars( $item[ 'title' ] ) . "</h3>\n";
		if ( isset($item['description']) )
			$content = $item['description'];
		elseif ( isset($item['summary']) )
			$content = $item['summary'];
		elseif ( isset($item['atom_content']) )
			$content = $item['atom_content'];
		else
			$content = __( 'something' );
		$content = wp_html_excerpt($content, 200) . ' ...';
		$link = clean_url( strip_tags( $item['link'] ) );
		$msg .= "<p>" . $content . " <a href='$link'>" . __( 'Read More' ) . "</a> <a href='index.php?feed_dismiss=" . md5( $item[ 'title' ] ) . "'>" . __( "Dismiss" ) . "</a></p>";
		echo "<div class='updated fade'>$msg</div>";
	} elseif( is_site_admin() ) {
		printf("<div id='update-nag'>" . __("Your feed at %s is empty.") . "</div>", wp_specialchars( $url ));
	}
}
add_action( 'admin_notices', 'admin_notice_feed' );

function site_admin_notice() {
	global $current_user, $wp_db_version;
	if( !is_site_admin() )
		return false;
	printf("<div id='update-nag'>" . __("Hi %s! You're logged in as a site administrator.") . "</div>", $current_user->user_login);
	if ( get_site_option( 'wpmu_upgrade_site' ) != $wp_db_version ) {
		echo "<div id='update-nag'>" . __( 'Thank you for Upgrading! Please visit the <a href="wpmu-upgrade-site.php">Upgrade Site</a> page to update all your blogs.' ) . "</div>";
	}
}
add_action( 'admin_notices', 'site_admin_notice' );

function avoid_blog_page_permalink_collision( $data, $postarr ) {
	if( constant( 'VHOST' ) == 'yes' )
		return $data;
	if( $data[ 'post_type' ] != 'page' )
		return $data;
	if( !isset( $data[ 'post_name' ] ) || $data[ 'post_name' ] == '' )
		return $data;
	if( !is_main_blog() )
		return $data;

	$post_name = $data[ 'post_name' ];
	$c = 0;
	while( $c < 10 && get_id_from_blogname( $post_name ) ) {
		$post_name .= mt_rand( 1, 10 );
		$c ++;
	}
	if( $post_name != $data[ 'post_name' ] ) {
		$data[ 'post_name' ] = $post_name;
	}
	return $data;
}
add_filter( 'wp_insert_post_data', 'avoid_blog_page_permalink_collision', 10, 2 );

/**
 * activate_sitewide_plugin()
 *
 * Activates a plugin site wide (for all blogs on an installation)
 */
function activate_sitewide_plugin() {
	if ( !isset( $_GET['sitewide'] ) )
		return false;
		
	/* Add the plugin to the list of sitewide active plugins */
	$active_sitewide_plugins = maybe_unserialize( get_site_option( 'active_sitewide_plugins' ) );
	
	/* Add the activated plugin to the list */
	$active_sitewide_plugins[ $_GET['plugin'] ] = time();

	/* Write the updated option to the DB */
	if ( !update_site_option( 'active_sitewide_plugins', $active_sitewide_plugins ) )
		return false;

	return true;
}
add_action( 'activate_' . $_GET['plugin'], 'activate_sitewide_plugin' ); 

/**
 * deactivate_sitewide_plugin()
 *
 * Deactivates a plugin site wide (for all blogs on an installation)
 */
function deactivate_sitewide_plugin( $plugin = false ) {
	if ( !$plugin )
		$plugin = $_GET['plugin'];
		
	/* Get the active sitewide plugins */
	$active_sitewide_plugins = (array) maybe_unserialize( get_site_option( 'active_sitewide_plugins' ) );

	/* Remove the plugin we are deactivating from the list of active sitewide plugins */
	foreach ( $active_sitewide_plugins as $plugin_file => $activation_time ) {
		if ( $plugin == $plugin_file )
			unset( $active_sitewide_plugins[ $plugin_file ] );
	}

	if ( !update_site_option( 'active_sitewide_plugins', $active_sitewide_plugins ) )
		wp_redirect( 'plugins.php?error=true' );
	
	return true;
}
add_action( 'deactivate_' . $_GET['plugin'], 'deactivate_sitewide_plugin' ); 
add_action( 'deactivate_invalid_plugin', 'deactivate_sitewide_plugin' ); 

/**
 * add_sitewide_activate_row()
 *
 * Adds the "Activate plugin site wide" row for each plugin in the inactive plugins list.
 */
function add_sitewide_activate_row( $file, $plugin_data, $context ) {
	if ( !is_site_admin() )
		return false;
	
	if ( 'sitewide-active' == $context )
		return false;
	
	if ( is_plugin_active( $file ) )
		return false;
		
	echo '<tr><td colspan="5" style="background: #f5f5f5; text-align: right;">';

	echo '<a href="' . wp_nonce_url( admin_url( 'plugins.php?action=activate&amp;sitewide=1&amp;plugin=' . $file ), 'activate-plugin_' . $file ) . '" title="' . __( 'Activate this plugin for all blogs across the entire network' ) . '">&uarr; ' . sprintf( __( 'Activate %s Site Wide' ), strip_tags( $plugin_data["Title"] ) ) . '</a>';
	echo '</td></tr>';
}
add_action( 'after_plugin_row', 'add_sitewide_activate_row', 9, 3 );

/**
 * is_wpmu_sitewide_plugin()
 *
 * Checks for "Site Wide Only: true" in the plugin header to see if this should
 * be activated as a site wide MU plugin.
 */
function is_wpmu_sitewide_plugin( $file ) {
	/* Open the plugin file for reading to check if this is a wpmu-plugin. */
	$fp = @fopen( WP_PLUGIN_DIR . '/' . $file, 'r' );
	
	/* Pull only the first 8kiB of the file in. */
	$plugin_data = @fread( $fp, 8192 );
	
	/* PHP will close file handle, but we are good citizens. */
	@fclose($fp);
	
	if ( preg_match( '|Site Wide Only:(.*)true$|mi', $plugin_data ) )
		return true;

	return false;
}


/**
 * list_activate_sitewide_plugins()
 *
 * Lists all the plugins that have been activated site wide.
 */
function list_activate_sitewide_plugins() {
	$all_plugins = get_plugins();

	if ( !is_site_admin() )
		return false;
		
	$active_sitewide_plugins = maybe_unserialize( get_site_option( 'active_sitewide_plugins') );
	$context = 'sitewide-active';

	if ( $active_sitewide_plugins ) { 
?>
		<h3><?php _e( 'Currently Active Site Wide Plugins' ) ?></h3>
	
		<p><?php _e( 'Plugins that appear in the list below are activate for all blogs across this installation.' ) ?></p>

		<table class="widefat" cellspacing="0" id="<?php echo $context ?>-plugins-table">
			<thead>
				<tr>
					<th scope="col" class="manage-column check-column">&nbsp;</th>
					<th scope="col" class="manage-column"><?php _e('Plugin'); ?></th>
					<th scope="col" class="manage-column"><?php _e('Description'); ?></th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<th scope="col" class="manage-column check-column">&nbsp;</th>
					<th scope="col" class="manage-column"><?php _e('Plugin'); ?></th>
					<th scope="col" class="manage-column"><?php _e('Description'); ?></th>
				</tr>
			</tfoot>

			<tbody class="plugins">
		<?php
			foreach ( (array) $active_sitewide_plugins as $plugin_file => $activated_time ) {
				$action_links = array();
				$action_links[] = '<a href="' . wp_nonce_url( 'plugins.php?action=deactivate&amp;sitewide=1&amp;plugin=' . $plugin_file, 'deactivate-plugin_' . $plugin_file ) . '" title="' . __('Deactivate this plugin site wide') . '">' . __('Deactivate') . '</a>';

				if ( current_user_can('edit_plugins') && is_writable(WP_PLUGIN_DIR . '/' . $plugin_file) )
					$action_links[] = '<a href="plugin-editor.php?file=' . $plugin_file . '" title="' . __('Open this file in the Plugin Editor') . '" class="edit">' . __('Edit') . '</a>';

				$action_links = apply_filters( 'plugin_action_links', $action_links, $plugin_file, $plugin_data, $context );
				$action_links = apply_filters( "plugin_action_links_$plugin_file", $action_links, $plugin_file, $plugin_data, $context );

				$plugin_data = $all_plugins[$plugin_file];
				
				echo "
			<tr class='$context' style='background: #eef2ff;'>
				<th scope='row' class='check-column'>&nbsp;</th>
				<td class='plugin-title'><strong>{$plugin_data['Name']}</strong></td>
				<td class='desc'><p>{$plugin_data['Description']}</p></td>
			</tr>
			<tr class='$context second' style='background: #eef2ff;'>
				<td></td>
				<td class='plugin-title'>";
				echo '<div class="row-actions-visible">';
				foreach ( $action_links as $action => $link ) {
					$sep = end($action_links) == $link ? '' : ' | ';
					echo "<span class='$action'>$link$sep</span>";
				}
				echo "</div></td>
				<td class='desc'>";
				$plugin_meta = array();
				if ( !empty($plugin_data['Version']) )
					$plugin_meta[] = sprintf(__('Version %s'), $plugin_data['Version']);
				if ( !empty($plugin_data['Author']) ) {
					$author = $plugin_data['Author'];
					if ( !empty($plugin_data['AuthorURI']) )
						$author = '<a href="' . $plugin_data['AuthorURI'] . '" title="' . __( 'Visit author homepage' ) . '">' . $plugin_data['Author'] . '</a>';
					$plugin_meta[] = sprintf( __('By %s'), $author );
				}
				if ( ! empty($plugin_data['PluginURI']) )
					$plugin_meta[] = '<a href="' . $plugin_data['PluginURI'] . '" title="' . __( 'Visit plugin site' ) . '">' . __('Visit plugin site') . '</a>';

				$plugin_meta = apply_filters('plugin_row_meta', $plugin_meta, $plugin_file, $plugin_data, $context);
				echo implode(' | ', $plugin_meta);
				echo "</td>
			</tr>\n";
			
				do_action( 'after_plugin_row', $plugin_file, $plugin_data, $context );
				do_action( "after_plugin_row_$plugin_file", $plugin_file, $plugin_data, $context );
			}
		?>
			</tbody>
		</table>
		
		<p><?php _e( 'Plugins that are enabled site wide can only be disabled by a site administrator.' ) ?></p>
		
<?php
	}
}
add_action( 'pre_current_active_plugins', 'list_activate_sitewide_plugins' );

/**
 * sitewide_filter_inactive_plugins_list()
 *
 * Filters the inactive plugins list so that it doesn't include plugins that have
 * been activated site wide, and not for the specific blog.
 */
function sitewide_filter_inactive_plugins_list( $inactive_plugins ) {
	$active_sitewide_plugins = (array) maybe_unserialize( get_site_option('active_sitewide_plugins') );

	foreach ( $active_sitewide_plugins as $sitewide_plugin => $activated_time ) {
		unset( $inactive_plugins[ $sitewide_plugin ] );
	}

	/* Now unset any sitewide only plugins if the user is not a site admin */	
	if ( !is_site_admin() ) {
		foreach ( $inactive_plugins as $plugin_name => $activated_time ) {
			if ( is_wpmu_sitewide_plugin( $plugin_name ) )
				unset( $inactive_plugins[ $plugin_name ] );
		}
	}
	
	return $inactive_plugins;
}
add_filter( 'all_plugins', 'sitewide_filter_inactive_plugins_list' );

/**
 * sitewide_filter_active_plugins_list()
 *
 * Filters the active plugins list so that it doesn't include plugins that have
 * been activated site wide instead of the specific blog.
 */
function sitewide_filter_active_plugins_list( $active_plugins ) {
	$active_sitewide_plugins = (array) maybe_unserialize( get_site_option('active_sitewide_plugins') );

	foreach ( $active_sitewide_plugins as $sitewide_plugin => $activated_time ) {
		unset( $active_plugins[ $sitewide_plugin ] );
	}
	
	return $active_plugins;
}
add_filter( 'all_plugins', 'sitewide_filter_active_plugins_list' );

/**
 * check_is_wpmu_plugin_on_activate()
 *
 * When a plugin is activated, this will check if it should be activated site wide
 * only.
 */
function check_is_wpmu_plugin_on_activate() {
	/***
	 * On plugin activation on a blog level, check to see if this is actually a 
	 * site wide MU plugin. If so, deactivate and activate it site wide.
	 */
	if ( is_wpmu_sitewide_plugin( $_GET['plugin'] ) || isset( $_GET['sitewide'] ) ) {
		deactivate_plugins( $_GET['plugin'], true );
		
		/* Silently activate because the activate_* hook has already run. */
		if ( is_site_admin() ) {
			$_GET['sitewide'] = true;
			activate_sitewide_plugin( $_GET['plugin'], true );
		}
	}
}
add_action( 'activate_' . $_GET['plugin'], 'check_is_wpmu_plugin_on_activate' );

/**
 * check_wpmu_plugins_on_bulk_activate()
 */
function check_wpmu_plugins_on_bulk_activate( $plugins ) {
	if ( $plugins ) {
		foreach ( $plugins as $plugin ) {
			if ( is_wpmu_sitewide_plugin( $plugin ) ) {
				deactivate_plugins( $plugin );

				if ( is_site_admin() )
					activate_sitewide_plugin( $plugin );
			}			
		}
	}
}

function remove_edit_plugin_link( $action_links, $plugin_file, $plugin_data, $context ) {
	foreach( $action_links as $t => $link ) {
		if( !strpos( $link, __( "Open this file in the Plugin Editor" ) ) )
			$links[ $t ] = $link;
	}
	return $links;
}
add_filter( 'plugin_action_links', 'remove_edit_plugin_link', 10, 4 );

function choose_primary_blog() {
	global $current_user;
	?>
	<table class="form-table">
	<tr>
		<th scope="row"><?php _e('Primary Blog'); ?></th>
		<td>
		<?php
		$all_blogs = get_blogs_of_user( $current_user->ID );
		$primary_blog = get_usermeta($current_user->ID, 'primary_blog');
		if( count( $all_blogs ) > 1 ) {
			$found = false;
			?>
			<select name="primary_blog">
				<?php foreach( (array) $all_blogs as $blog ) { 
					if( $primary_blog == $blog->userblog_id )
						$found = true;
					?><option value='<?php echo $blog->userblog_id ?>'<?php if( $primary_blog == $blog->userblog_id ) echo ' selected="selected"' ?>>http://<?php echo $blog->domain.$blog->path ?></option><?php 
				} ?>
			</select>
			<?php
			if( !$found ) {
				$blog = array_shift( $all_blogs );
				update_usermeta( $current_user->ID, 'primary_blog', $blog->userblog_id );
			}
		} elseif( count( $all_blogs ) == 1 ) {
			$blog = array_shift( $all_blogs );
			echo $blog->domain;
			if( $primary_blog != $blog->userblog_id ) // Set the primary blog again if it's out of sync with blog list.
				update_usermeta( $current_user->ID, 'primary_blog', $blog->userblog_id );
		} else {
			echo "N/A";
		}
		?>
		</td>
	</tr>
	</table>
	<?php	
}
add_action ( 'myblogs_allblogs_options', 'choose_primary_blog' );

if( strpos( $_SERVER['PHP_SELF'], 'profile.php' ) ) {
	add_action( 'admin_init', 'update_profile_email' );
	add_action( 'admin_init', 'profile_page_email_warning_ob_start' );
}

function disable_some_pages() {
	global $messages;

	if ( strpos( $_SERVER['PHP_SELF'], 'user-new.php' ) && !get_site_option( 'add_new_users' ) ) {
		if ( is_site_admin() ) {
			$messages[] = '<div id="message" class="updated fade"><p>' . __( 'Warning! Only site administrators may see this page. Everyone else will see a <em>page disabled</em> message. Enable it again on <a href="wpmu-options.php#addnewusers">the options page</a>.' ) . '</p></div>';
		} else {
			wp_die( __('Page disabled by the administrator') );
		}
	}

	$pages = array( 'theme-editor.php', 'plugin-editor.php' );
	foreach( $pages as $page ) {
		if ( strpos( $_SERVER['PHP_SELF'], $page ) ) {
			wp_die( __('Page disabled by the administrator') );
		}
	}

	$pages = array( 'theme-install.php', 'plugin-install.php' );
	foreach( $pages as $page ) {
		if ( strpos( $_SERVER['PHP_SELF'], $page ) && !is_site_admin() ) {
			wp_die( __( "Sorry, you're not allowed here." ) );
		}
	}

}
add_action( 'admin_init', 'disable_some_pages' );

function blogs_listing_post() {
	if ( !isset( $_POST[ 'action' ] ) ) {
		return false;
	}
	switch( $_POST[ 'action' ] ) {
		case "updateblogsettings":
			do_action( 'myblogs_update' );
		wp_redirect( admin_url( 'index.php?page=myblogs&updated=1' ) );
		die();
		break;
	}
}
add_action( 'admin_init', 'blogs_listing_post' );

function blogs_listing() {
	global $current_user;

	$blogs = get_blogs_of_user( $current_user->ID );
	if( !$blogs || ( is_array( $blogs ) && empty( $blogs ) ) ) {
		wp_die( __( 'You must be a member of at least one blog to use this page.' ) );
	}

	if ( empty($title) )
		$title = apply_filters( 'my_blogs_title', __( 'My Blogs' ) );
	?>
	<div class="wrap">
	<?php if( $_GET[ 'updated' ] ) { ?>
		<div id="message" class="updated fade"><p><strong><?php _e( 'Your blog options have been updated.' ); ?></strong></p></div>
	<?php } ?>
	<?php screen_icon(); ?>
	<h2><?php echo wp_specialchars( $title ); ?></h2>
	<form id="myblogs" action="" method="post">
	<?php
	do_action( 'myblogs_allblogs_options' );
	?><table class='widefat'> <?php 
	$settings_html = apply_filters( 'myblogs_options', '', 'global' );
	if ( $settings_html != '' ) {
		echo "<tr><td valign='top'><h3>" . __( 'Global Settings' ) . "</h3></td><td>";
		echo $settings_html;
		echo "</td></tr>";
	}
	reset( $blogs );
	$num = count( $blogs );
	$cols = 1;
	if ( $num >= 20 ) {
		$cols = 4;
	} elseif ( $num >= 10 ) {
		$cols = 2;
	}
	$num_rows = ceil($num/$cols);
	$split = 0;
	for( $i = 1; $i <= $num_rows; $i++ ) {
		$rows[] = array_slice( $blogs, $split, $cols );
		$split = $split + $cols;
	}
  
	foreach( $rows as $row ) {
		$c = $c == "alternate" ? "" : "alternate";
		echo "<tr class='$c'>";
		foreach( $row as $user_blog ) {
			$t = $t == "border-right: 1px solid #ccc;" ? "" : "border-right: 1px solid #ccc;";
			echo "<td valign='top' style='$t; width:50%'>";
			echo "<h3>{$user_blog->blogname}</h3>";
			echo "<p>" . apply_filters( "myblogs_blog_actions", "<a href='{$user_blog->siteurl}'>" . __( 'Visit' ) . "</a> | <a href='{$user_blog->siteurl}/wp-admin/'>" . __( 'Dashboard' ) . "</a>", $user_blog ) . "</p>";
			echo apply_filters( 'myblogs_options', '', $user_blog );
			echo "</td>";
		}
		echo "</tr>";
	}?>
	</table>
	<input type="hidden" name="action" value="updateblogsettings" />
	<p>
	 <input type="submit" class="button-primary" value="<?php _e('Update Options') ?>" name="submit" />
	</p>
	</form>
	</div>
	<?php
}

function blogs_page_init() {
	global $current_user;
	$all_blogs = get_blogs_of_user( $current_user->ID );
	if ( $all_blogs != false && !empty( $all_blogs ) ) {
		$title = apply_filters( 'my_blogs_title', __( 'My Blogs' ) );
		add_submenu_page( 'index.php', $title, $title, 'read', 'myblogs', 'blogs_listing' );
	}
}
add_action('admin_menu', 'blogs_page_init');

function update_signup_email_from_profile( $user_id ) {
	global $wpdb;
	$user_login = $wpdb->get_var( $wpdb->prepare( "SELECT user_login FROM {$wpdb->users} WHERE ID = %d", $user_id ) );
	if ( $user_login && is_email( $_POST[ 'email' ] ) && $wpdb->get_var( $wpdb->prepare( "SELECT user_login FROM {$wpdb->signups} WHERE user_login = %s", $user_login ) ) ) {
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->signups} SET user_email = %s WHERE user_login = %s", $_POST[ 'email' ], $user_login ) );
	}
}
add_action( 'edit_user_profile_update', 'update_signup_email_from_profile' );

function stripslashes_from_options( $blog_id ) {
	global $wpdb;

	if ( $blog_id == 1 ) { // check site_options too
		$start = 0;
		while( $rows = $wpdb->get_results( "SELECT meta_key, meta_value FROM {$wpdb->sitemeta} ORDER BY meta_id LIMIT $start, 20" ) ) {
			foreach( $rows as $row ) {
				$value = $row->meta_value;
				if ( !@unserialize( $value ) ) 
					$value = stripslashes( $value ); 
				if ( $value !== $row->meta_value ) {
					update_site_option( $row->meta_key, $value );
				}
			}
			$start += 20;
		}
	}
	$start = 0;
	$options_table = $wpdb->get_blog_prefix( $blog_id ) . "options";
	while( $rows = $wpdb->get_results( "SELECT option_name, option_value FROM $options_table ORDER BY option_id LIMIT $start, 20" ) ) {
		foreach( $rows as $row ) {
			$value = $row->option_value;
			if ( !@unserialize( $value ) ) 
				$value = stripslashes( $value ); 
			if ( $value !== $row->option_value ) {
				update_blog_option( $blog_id, $row->option_name, $value );
			}
		}
		$start += 20;
	}
	refresh_blog_details( $blog_id );
}
add_action( 'wpmu_upgrade_site', 'stripslashes_from_options' );

function show_post_thumbnail_warning() {
	if ( false == is_site_admin() ) {
		return;
	}
	$mu_media_buttons = get_site_option( 'mu_media_buttons', array() );
	if ( !$mu_media_buttons[ 'image' ] && current_theme_supports( 'post-thumbnails' ) ) {
		echo "<div id='update-nag'>" . sprintf( __( "Warning! The current theme supports post thumbnails. You must enable image uploads on <a href='%s'>the options page</a> for it to work." ), admin_url( 'wpmu-options.php' ) ) . "</div>";
	}
}
add_action( 'admin_notices', 'show_post_thumbnail_warning' );

?>

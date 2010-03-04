<?php

/**
 * Determine if uploaded file exceeds space quota.
 *
 * @since 3.0
 *
 * @param array $file $_FILES array for a given file.
 * @return array $_FILES array with 'error' key set if file exceeds quota. 'error' is empty otherwise.
 */
function check_upload_size( $file ) {
	if ( get_site_option( 'upload_space_check_disabled' ) )
		return $file;

	if ( $file['error'] != '0' ) // there's already an error
		return $file;

	if ( defined( 'WP_IMPORTING' ) )
		return $file;

	$space_allowed = 1048576 * get_space_allowed();
	$space_used = get_dirsize( BLOGUPLOADDIR );
	$space_left = $space_allowed - $space_used;
	$file_size = filesize( $file['tmp_name'] );
	if ( $space_left < $file_size )
		$file['error'] = sprintf( __( 'Not enough space to upload. %1$s Kb needed.' ), number_format( ($file_size - $space_left) /1024 ) );
	if ( $file_size > ( 1024 * get_site_option( 'fileupload_maxk', 1500 ) ) )
		$file['error'] = sprintf(__('This file is too big. Files must be less than %1$s Kb in size.'), get_site_option( 'fileupload_maxk', 1500 ) );
	if ( upload_is_user_over_quota( false ) ) {
		$file['error'] = __('You have used your space quota. Please delete files before uploading.');
	}
	if ( $file['error'] != '0' )
		wp_die( $file['error'] . ' <a href="javascript:history.go(-1)">' . __( 'Back' ) . '</a>' );

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'check_upload_size' );

/**
 * Delete a blog
 *
 * @since 3.0
 *
 * @param int $blog_id Blog ID
 * @param bool $drop True if blog's table should be dropped.  Default is false.
 * @return void
 */
function wpmu_delete_blog($blog_id, $drop = false) {
	global $wpdb;

	$switched = false;
	if ( $blog_id != $wpdb->blogid ) {
		$switch = true;
		switch_to_blog($blog_id);
	}

	do_action('delete_blog', $blog_id, $drop);

	$users = get_users_of_blog($blog_id);

	// Remove users from this blog.
	if ( !empty($users) ) {
		foreach ($users as $user) {
			remove_user_from_blog($user->user_id, $blog_id);
		}
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
		$dir = apply_filters( 'wpmu_delete_blog_upload_dir', WP_CONTENT_DIR . "/blogs.dir/{$blog_id}/files/", $blog_id );
		$dir = rtrim($dir, DIRECTORY_SEPARATOR);
		$top_dir = $dir;
		$stack = array($dir);
		$index = 0;

		while ( $index < count($stack) ) {
			# Get indexed directory from stack
			$dir = $stack[$index];

			$dh = @ opendir($dir);
			if ( $dh ) {
				while ( ($file = @ readdir($dh)) !== false ) {
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
		foreach( (array) $stack as $dir ) {
			if ( $dir != $top_dir)
			@rmdir($dir);
		}
	}
	$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->usermeta} WHERE meta_key = %s", 'wp_{$blog_id}_autosave_draft_ids') );
	$blogs = get_site_option( "blog_list" );
	if ( is_array( $blogs ) ) {
		foreach ( $blogs as $n => $blog ) {
			if ( $blog[ 'blog_id' ] == $blog_id )
				unset( $blogs[ $n ] );
		}
		update_site_option( 'blog_list', $blogs );
	}

	if ( $switch === true )
		restore_current_blog();
}

// @todo Merge with wp_delete_user() ?
function wpmu_delete_user($id) {
	global $wpdb;

	$id = (int) $id;

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
			$link_ids = $wpdb->get_col( $wpdb->prepare("SELECT link_id FROM $wpdb->links WHERE link_owner = %d", $id) );

			if ( $link_ids ) {
				foreach ( $link_ids as $link_id )
					wp_delete_link($link_id);
			}

			restore_current_blog();
		}
	}

	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->users WHERE ID = %d", $id) );
	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id = %d", $id) );

	clean_user_cache($id);

	// allow for commit transaction
	do_action('deleted_user', $id);

	return true;
}

function confirm_delete_users( $users ) {
	global $current_user;
	if ( !is_array( $users ) )
		return false;
        
    screen_icon('tools');
    ?>
	<h2><?php esc_html_e('Users'); ?></h2>
	<p><?php _e( 'Transfer posts before deleting users:' ); ?></p>
	<form action="ms-edit.php?action=allusers" method="post">
	<input type="hidden" name="alluser_transfer_delete" />
    <?php
	wp_nonce_field( 'allusers' );
	$site_admins = get_site_option( 'site_admins', array( 'admin' ) );
	$admin_out = "<option value='$current_user->ID'>$current_user->user_login</option>";

	foreach ( ( $allusers = (array) $_POST['allusers'] ) as $key => $val ) {
		if ( $val != '' && $val != '0' ) {
			$delete_user = new WP_User( $val );
            
			if ( in_array( $delete_user->user_login, $site_admins ) )
				wp_die( sprintf( __( 'Warning! User cannot be deleted. The user %s is a network admnistrator.' ), $delete_user->user_login ) );
                
			echo "<input type='hidden' name='user[]' value='{$val}'/>\n";
			$blogs = get_blogs_of_user( $val, true );
            
			if ( !empty( $blogs ) ) {
				echo '<p><strong>' . sprintf( __( 'Sites from %s:' ), $delete_user->user_login ) . '</strong></p>';
				foreach ( (array) $blogs as $key => $details ) {
					$blog_users = get_users_of_blog( $details->userblog_id );
					if ( is_array( $blog_users ) && !empty( $blog_users ) ) {
						echo "<p><a href='http://{$details->domain}{$details->path}'>{$details->blogname}</a> ";
						echo "<select name='blog[$val][{$key}]'>";
						$out = '';
						foreach ( $blog_users as $user ) {
							if ( $user->user_id != $val && !in_array( $user->user_id, $allusers ) )
								$out .= "<option value='{$user->user_id}'>{$user->user_login}</option>";
						}
						if ( $out == '' )
							$out = $admin_out;
						echo $out;
						echo "</select>\n";
					}
				}
			}
		}
	}
	?>
    <br class="clear" />
    <input type="submit" class="button-secondary delete" value="<?php _e( 'Delete user and transfer posts' ); ?> " />
	</form>
    <?php
	return true;
}

function wpmu_get_blog_allowedthemes( $blog_id = 0 ) {
	$themes = get_themes();

	if ( $blog_id != 0 )
		switch_to_blog( $blog_id );

	$blog_allowed_themes = get_option( "allowedthemes" );
	if ( !is_array( $blog_allowed_themes ) || empty( $blog_allowed_themes ) ) { // convert old allowed_themes to new allowedthemes
		$blog_allowed_themes = get_option( "allowed_themes" );

		if ( is_array( $blog_allowed_themes ) ) {
			foreach( (array) $themes as $key => $theme ) {
				$theme_key = esc_html( $theme[ 'Stylesheet' ] );
				if ( isset( $blog_allowed_themes[ $key ] ) == true ) {
					$blog_allowedthemes[ $theme_key ] = 1;
				}
			}
			$blog_allowed_themes = $blog_allowedthemes;
			add_option( "allowedthemes", $blog_allowed_themes );
			delete_option( "allowed_themes" );
		}
	}

	if ( $blog_id != 0 )
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
your site changed.
If this is correct, please click on the following link to change it:
###ADMIN_URL###

You can safely ignore and delete this email if you do not want to
take this action.

This email has been sent to ###EMAIL###

Regards,
All at ###SITENAME###
###SITEURL###"), $new_admin_email );

	$content = str_replace('###ADMIN_URL###', esc_url(get_option( "siteurl" ).'/wp-admin/options.php?adminhash='.$hash), $content);
	$content = str_replace('###EMAIL###', $value, $content);
	$content = str_replace('###SITENAME###', get_site_option( 'site_name' ), $content);
	$content = str_replace('###SITEURL###', 'http://' . $current_site->domain . $current_site->path, $content);

	wp_mail( $value, sprintf(__('[%s] New Admin Email Address'), get_option('blogname')), $content );
}
add_action('update_option_new_admin_email', 'update_option_new_admin_email', 10, 2);

function send_confirmation_on_profile_email() {
	global $errors, $wpdb, $current_user, $current_site;
	if ( ! is_object($errors) )
		$errors = new WP_Error();

	if ( $current_user->id != $_POST[ 'user_id' ] )
		return false;

	if ( $current_user->user_email != $_POST[ 'email' ] ) {
		if ( !is_email( $_POST[ 'email' ] ) ) {
			$errors->add( 'user_email', __( "<strong>ERROR</strong>: The e-mail address isn't correct." ), array( 'form-field' => 'email' ) );
			return;
		}

		if ( $wpdb->get_var( $wpdb->prepare( "SELECT user_email FROM {$wpdb->users} WHERE user_email=%s", $_POST[ 'email' ] ) ) ) {
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

		$content = str_replace('###ADMIN_URL###', esc_url(get_option( "siteurl" ).'/wp-admin/profile.php?newuseremail='.$hash), $content);
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
	if ( strpos( $_SERVER['PHP_SELF'], 'profile.php' ) && isset( $_GET[ 'updated' ] ) && $email = get_option( $current_user->ID . '_new_email' ) )
		echo "<div id='update-nag'>" . sprintf( __( "Your email address has not been updated yet. Please check your inbox at %s for a confirmation email." ), $email[ 'newemail' ] ) . "</div>";
}
add_action( 'admin_notices', 'new_user_email_admin_notice' );

function get_site_allowed_themes() {
	$themes = get_themes();
	$allowed_themes = get_site_option( 'allowedthemes' );
	if ( !is_array( $allowed_themes ) || empty( $allowed_themes ) ) {
		$allowed_themes = get_site_option( "allowed_themes" ); // convert old allowed_themes format
		if ( !is_array( $allowed_themes ) ) {
			$allowed_themes = array();
		} else {
			foreach( (array) $themes as $key => $theme ) {
				$theme_key = esc_html( $theme[ 'Stylesheet' ] );
				if ( isset( $allowed_themes[ $key ] ) == true ) {
					$allowedthemes[ $theme_key ] = 1;
				}
			}
			$allowed_themes = $allowedthemes;
		}
	}
	return $allowed_themes;
}

/**
 * Determines if there is any upload space left in the current blog's quota.
 *
 * @return bool True if space is available, false otherwise.
 */
function is_upload_space_available() {
	if ( get_site_option( 'upload_space_check_disabled' ) )
		return true;

	$space_allowed = get_space_allowed();

	$dir_name = trailingslashit( BLOGUPLOADDIR );
	if ( !(is_dir($dir_name) && is_readable($dir_name)) )
		return true;

  	$dir = dir($dir_name);
   	$size = 0;

	while ( $file = $dir->read() ) {
		if ( $file != '.' && $file != '..' ) {
			if ( is_dir( $dir_name . $file) ) {
				$size += get_dirsize($dir_name . $file);
			} else {
				$size += filesize($dir_name . $file);
			}
		}
	}
	$dir->close();
	$size = $size / 1024 / 1024;

	if ( ($space_allowed - $size) <= 0 )
		return false;

	return true;
}

/**
 * Returns the upload quota for the current blog.
 *
 * @return int Quota
 */
function get_space_allowed() {
	$space_allowed = get_option('blog_upload_space');
	if ( $space_allowed == false )
		$space_allowed = get_site_option('blog_upload_space');
	if ( empty($space_allowed) || !is_numeric($space_allowed) )
		$space_allowed = 50;

	return $space_allowed;
}

function display_space_usage() {
	$space = get_space_allowed();
	$used = get_dirsize( BLOGUPLOADDIR )/1024/1024;

	if ( $used > $space )
		$percentused = '100';
	else
		$percentused = ( $used / $space ) * 100;

	if ( $space > 1000 ) {
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
	if ( get_site_option( 'upload_space_check_disabled' ) )
		return true;

	$quota = get_space_allowed();
	$used = get_dirsize( BLOGUPLOADDIR )/1024/1024;

	if ( $used > $quota )
		$percentused = '100';
	else
		$percentused = ( $used / $quota ) * 100;
	$percentused = number_format($percentused);
	$used = round($used,2);
	$used_color = ($used < 70) ? (($used >= 40) ? 'waiting' : 'approved') : 'spam';
	?>
	<p class="sub musub"><?php _e('Storage Space'); ?></p>
	<div class="table">
	<table>
		<tr class="first">
			<td class="first b b-posts"><?php printf( __( '<a href="upload.php" title="Manage Uploads" class="musublink">%sMB</a>' ), $quota ); ?></td>
			<td class="t posts"><?php _e('Space Allowed'); ?></td>
			<td class="b b-comments"><?php printf( __( '<a href="upload.php" title="Manage Uploads" class="musublink">%1sMB (%2s%%)</a>' ), $used, $percentused ); ?></td>
			<td class="last t comments <?php echo $used_color;?>"><?php _e('Space Used');?></td>
		</tr>
	</table>
	</div>
	<?php
}
if ( current_user_can('edit_posts') )
	add_action('activity_box_end', 'dashboard_quota');

// Edit blog upload space setting on Edit Blog page
function upload_space_setting( $id ) {
	$quota = get_blog_option($id, "blog_upload_space");
	if ( !$quota )
		$quota = '';

	?>
	<tr>
		<th><?php _e('Site Upload Space Quota'); ?></th>
		<td><input type="text" size="3" name="option[blog_upload_space]" value="<?php echo $quota; ?>" /><?php _e('MB (Leave blank for network default)'); ?></td>
	</tr>
	<?php
}
add_action('wpmueditblogaction', 'upload_space_setting');

function update_user_status( $id, $pref, $value, $refresh = 1 ) {
	global $wpdb;

	$wpdb->update( $wpdb->users, array( $pref => $value ), array( 'ID' => $id ) );

	if ( $refresh == 1 )
		refresh_user_details($id);

	if ( $pref == 'spam' ) {
		if ( $value == 1 )
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

	clean_user_cache($id);

	return $id;
}

function format_code_lang( $code = '' ) {
	$code = strtolower(substr($code, 0, 2));
	$lang_codes = array('aa' => 'Afar',  'ab' => 'Abkhazian',  'af' => 'Afrikaans',  'ak' => 'Akan',  'sq' => 'Albanian',  'am' => 'Amharic',  'ar' => 'Arabic',  'an' => 'Aragonese',  'hy' => 'Armenian',  'as' => 'Assamese',  'av' => 'Avaric',  'ae' => 'Avestan',  'ay' => 'Aymara',  'az' => 'Azerbaijani',  'ba' => 'Bashkir',  'bm' => 'Bambara',  'eu' => 'Basque',  'be' => 'Belarusian',  'bn' => 'Bengali',  'bh' => 'Bihari',  'bi' => 'Bislama',  'bs' => 'Bosnian',  'br' => 'Breton',  'bg' => 'Bulgarian',  'my' => 'Burmese',  'ca' => 'Catalan; Valencian',  'ch' => 'Chamorro',  'ce' => 'Chechen',  'zh' => 'Chinese',  'cu' => 'Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic',  'cv' => 'Chuvash',  'kw' => 'Cornish',  'co' => 'Corsican',  'cr' => 'Cree',  'cs' => 'Czech',  'da' => 'Danish',  'dv' => 'Divehi; Dhivehi; Maldivian',  'nl' => 'Dutch; Flemish',  'dz' => 'Dzongkha',  'en' => 'English',  'eo' => 'Esperanto',  'et' => 'Estonian',  'ee' => 'Ewe',  'fo' => 'Faroese',  'fj' => 'Fijian',  'fi' => 'Finnish',  'fr' => 'French',  'fy' => 'Western Frisian',  'ff' => 'Fulah',  'ka' => 'Georgian',  'de' => 'German',  'gd' => 'Gaelic; Scottish Gaelic',  'ga' => 'Irish',  'gl' => 'Galician',  'gv' => 'Manx',  'el' => 'Greek, Modern',  'gn' => 'Guarani',  'gu' => 'Gujarati',  'ht' => 'Haitian; Haitian Creole',  'ha' => 'Hausa',  'he' => 'Hebrew',  'hz' => 'Herero',  'hi' => 'Hindi',  'ho' => 'Hiri Motu',  'hu' => 'Hungarian',  'ig' => 'Igbo',  'is' => 'Icelandic',  'io' => 'Ido',  'ii' => 'Sichuan Yi',  'iu' => 'Inuktitut',  'ie' => 'Interlingue',  'ia' => 'Interlingua (International Auxiliary Language Association)',  'id' => 'Indonesian',  'ik' => 'Inupiaq',  'it' => 'Italian',  'jv' => 'Javanese',  'ja' => 'Japanese',  'kl' => 'Kalaallisut; Greenlandic',  'kn' => 'Kannada',  'ks' => 'Kashmiri',  'kr' => 'Kanuri',  'kk' => 'Kazakh',  'km' => 'Central Khmer',  'ki' => 'Kikuyu; Gikuyu',  'rw' => 'Kinyarwanda',  'ky' => 'Kirghiz; Kyrgyz',  'kv' => 'Komi',  'kg' => 'Kongo',  'ko' => 'Korean',  'kj' => 'Kuanyama; Kwanyama',  'ku' => 'Kurdish',  'lo' => 'Lao',  'la' => 'Latin',  'lv' => 'Latvian',  'li' => 'Limburgan; Limburger; Limburgish',  'ln' => 'Lingala',  'lt' => 'Lithuanian',  'lb' => 'Luxembourgish; Letzeburgesch',  'lu' => 'Luba-Katanga',  'lg' => 'Ganda',  'mk' => 'Macedonian',  'mh' => 'Marshallese',  'ml' => 'Malayalam',  'mi' => 'Maori',  'mr' => 'Marathi',  'ms' => 'Malay',  'mg' => 'Malagasy',  'mt' => 'Maltese',  'mo' => 'Moldavian',  'mn' => 'Mongolian',  'na' => 'Nauru',  'nv' => 'Navajo; Navaho',  'nr' => 'Ndebele, South; South Ndebele',  'nd' => 'Ndebele, North; North Ndebele',  'ng' => 'Ndonga',  'ne' => 'Nepali',  'nn' => 'Norwegian Nynorsk; Nynorsk, Norwegian',  'nb' => 'Bokmål, Norwegian, Norwegian Bokmål',  'no' => 'Norwegian',  'ny' => 'Chichewa; Chewa; Nyanja',  'oc' => 'Occitan, Provençal',  'oj' => 'Ojibwa',  'or' => 'Oriya',  'om' => 'Oromo',  'os' => 'Ossetian; Ossetic',  'pa' => 'Panjabi; Punjabi',  'fa' => 'Persian',  'pi' => 'Pali',  'pl' => 'Polish',  'pt' => 'Portuguese',  'ps' => 'Pushto',  'qu' => 'Quechua',  'rm' => 'Romansh',  'ro' => 'Romanian',  'rn' => 'Rundi',  'ru' => 'Russian',  'sg' => 'Sango',  'sa' => 'Sanskrit',  'sr' => 'Serbian',  'hr' => 'Croatian',  'si' => 'Sinhala; Sinhalese',  'sk' => 'Slovak',  'sl' => 'Slovenian',  'se' => 'Northern Sami',  'sm' => 'Samoan',  'sn' => 'Shona',  'sd' => 'Sindhi',  'so' => 'Somali',  'st' => 'Sotho, Southern',  'es' => 'Spanish; Castilian',  'sc' => 'Sardinian',  'ss' => 'Swati',  'su' => 'Sundanese',  'sw' => 'Swahili',  'sv' => 'Swedish',  'ty' => 'Tahitian',  'ta' => 'Tamil',  'tt' => 'Tatar',  'te' => 'Telugu',  'tg' => 'Tajik',  'tl' => 'Tagalog',  'th' => 'Thai',  'bo' => 'Tibetan',  'ti' => 'Tigrinya',  'to' => 'Tonga (Tonga Islands)',  'tn' => 'Tswana',  'ts' => 'Tsonga',  'tk' => 'Turkmen',  'tr' => 'Turkish',  'tw' => 'Twi',  'ug' => 'Uighur; Uyghur',  'uk' => 'Ukrainian',  'ur' => 'Urdu',  'uz' => 'Uzbek',  've' => 'Venda',  'vi' => 'Vietnamese',  'vo' => 'Volapük',  'cy' => 'Welsh',  'wa' => 'Walloon',  'wo' => 'Wolof',  'xh' => 'Xhosa',  'yi' => 'Yiddish',  'yo' => 'Yoruba',  'za' => 'Zhuang; Chuang',  'zu' => 'Zulu');
	$lang_codes = apply_filters('lang_codes', $lang_codes, $code);
	return strtr( $code, $lang_codes );
}

function sync_category_tag_slugs( $term, $taxonomy ) {
	if ( $taxonomy == 'category' || $taxonomy == 'post_tag' ) {
		if ( is_object( $term ) ) {
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
		wp_die( __( "You don&#8217;t have permission to view this site. Please contact the system administrator." ) );
	}
	$c ++;

	$blog = get_active_blog_for_user( $current_user->ID );
	$dashboard_blog = get_dashboard_blog();
	if ( is_object( $blog ) ) {
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
			if ( $blogid != $dashboard_blog->blog_id && get_user_meta( $current_user->ID , 'primary_blog', true ) == $dashboard_blog->blog_id ) {
				update_user_meta( $current_user->ID, 'primary_blog', $blogid );
				continue;
			}
		}
		$blog = get_blog_details( get_user_meta( $current_user->ID, 'primary_blog', true ) );
		$protocol = ( is_ssl() ? 'https://' : 'http://' );
		wp_redirect( $protocol . $blog->domain . $blog->path . 'wp-admin/?c=' . $c ); // redirect and count to 5, "just in case"
		exit;
	}
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
add_action( 'admin_page_access_denied', 'redirect_user_to_blog', 99 );

function check_import_new_users( $permission ) {
	if ( !is_super_admin() )
		return false;
	return true;
}
add_filter( 'import_allow_create_users', 'check_import_new_users' );
// See "import_allow_fetch_attachments" and "import_attachment_size_limit" filters too.

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

/* Warn the admin if SECRET SALT information is missing from wp-config.php */
function secret_salt_warning() {
	if ( !is_super_admin() )
		return;
	$secret_keys = array( 'NONCE_KEY', 'AUTH_KEY', 'AUTH_SALT', 'LOGGED_IN_KEY', 'LOGGED_IN_SALT', 'SECURE_AUTH_KEY', 'SECURE_AUTH_SALT' );
	$out = '';
	foreach( $secret_keys as $key ) {
		if ( !defined( $key ) )
			$out .= "define( '$key', '" . wp_generate_password() . wp_generate_password() . "' );<br />";
	}
	if ( $out != '' ) {
		$msg = sprintf( __( 'Warning! WordPress encrypts user cookies, but you must add the following lines to <strong>%swp-config.php</strong> for it to be more secure.<br />Please add the code before the line, <code>/* That\'s all, stop editing! Happy blogging. */</code>' ), ABSPATH );
		$msg .= "<blockquote>$out</blockquote>";

		echo "<div id='update-nag'>$msg</div>";
	}
}
add_action( 'admin_notices', 'secret_salt_warning' );

function admin_notice_feed() {
	global $current_user, $current_screen;
	if ( $current_screen->id != 'index' )
		return;

	if ( !empty( $_GET[ 'feed_dismiss' ] ) )
		update_user_option( $current_user->id, 'admin_feed_dismiss', $_GET[ 'feed_dismiss' ], true );

	$url = get_site_option( 'admin_notice_feed' );
	if ( empty( $url ) )
		return;

	$rss = fetch_feed( $url );
	if ( ! is_wp_error( $rss ) && $item = $rss->get_item() ) {
		$title = $item->get_title();
		if ( md5( $title ) == get_user_option( 'admin_feed_dismiss' ) )
			return;
		$msg = "<h3>" . esc_html( $title ) . "</h3>\n";
		$content = $item->get_description();
		$content = $content ? wp_html_excerpt( $content, 200 ) . ' &hellip; ' : '';
		$link = esc_url( strip_tags( $item->get_link() ) );
		$msg .= "<p>" . $content . "<a href='$link'>" . __( 'Read More' ) . "</a> <a href='index.php?feed_dismiss=" . md5( $title ) . "'>" . __( 'Dismiss' ) . "</a></p>";
		echo "<div class='updated fade'>$msg</div>";
	} elseif ( is_super_admin() ) {
		printf( '<div id="update-nag">' . __( 'Your feed at %s is empty.' ) . '</div>', esc_html( $url ) );
	}
}
add_action( 'admin_notices', 'admin_notice_feed' );

function site_admin_notice() {
	global $current_user, $wp_db_version;
	if ( !is_super_admin() )
		return false;
	if ( get_site_option( 'wpmu_upgrade_site' ) != $wp_db_version )
		echo "<div id='update-nag'>" . __( 'Thank you for Upgrading! Please visit the <a href="ms-upgrade-network.php">Upgrade Network</a> page to update all your sites.' ) . "</div>";
}
add_action( 'admin_notices', 'site_admin_notice' );

function avoid_blog_page_permalink_collision( $data, $postarr ) {
	if ( is_subdomain_install() )
		return $data;
	if ( $data[ 'post_type' ] != 'page' )
		return $data;
	if ( !isset( $data[ 'post_name' ] ) || $data[ 'post_name' ] == '' )
		return $data;
	if ( !is_main_site() )
		return $data;

	$post_name = $data[ 'post_name' ];
	$c = 0;
	while( $c < 10 && get_id_from_blogname( $post_name ) ) {
		$post_name .= mt_rand( 1, 10 );
		$c ++;
	}
	if ( $post_name != $data[ 'post_name' ] ) {
		$data[ 'post_name' ] = $post_name;
	}
	return $data;
}
add_filter( 'wp_insert_post_data', 'avoid_blog_page_permalink_collision', 10, 2 );

function choose_primary_blog() {
	global $current_user;
	?>
	<table class="form-table">
	<tr>
	<?php /* translators: My sites label */ ?>
		<th scope="row"><?php _e('Primary Site'); ?></th>
		<td>
		<?php
		$all_blogs = get_blogs_of_user( $current_user->ID );
		$primary_blog = get_user_meta($current_user->ID, 'primary_blog', true);
		if ( count( $all_blogs ) > 1 ) {
			$found = false;
			?>
			<select name="primary_blog">
				<?php foreach( (array) $all_blogs as $blog ) {
					if ( $primary_blog == $blog->userblog_id )
						$found = true;
					?><option value='<?php echo $blog->userblog_id ?>'<?php if ( $primary_blog == $blog->userblog_id ) echo ' selected="selected"' ?>>http://<?php echo $blog->domain.$blog->path ?></option><?php
				} ?>
			</select>
			<?php
			if ( !$found ) {
				$blog = array_shift( $all_blogs );
				update_user_meta( $current_user->ID, 'primary_blog', $blog->userblog_id );
			}
		} elseif ( count( $all_blogs ) == 1 ) {
			$blog = array_shift( $all_blogs );
			echo $blog->domain;
			if ( $primary_blog != $blog->userblog_id ) // Set the primary blog again if it's out of sync with blog list.
				update_user_meta( $current_user->ID, 'primary_blog', $blog->userblog_id );
		} else {
			echo "N/A";
		}
		?>
		</td>
	</tr>
	</table>
	<?php
}

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
	if ( ! is_super_admin() )
		return;
	$mu_media_buttons = get_site_option( 'mu_media_buttons', array() );
	if ( empty($mu_media_buttons[ 'image' ]) && current_theme_supports( 'post-thumbnails' ) ) {
		echo "<div id='update-nag'>" . sprintf( __( "Warning! The current theme supports post thumbnails. You must enable image uploads on <a href='%s'>the options page</a> for it to work." ), admin_url( 'ms-options.php' ) ) . "</div>";
	}
}
add_action( 'admin_notices', 'show_post_thumbnail_warning' );

function ms_deprecated_blogs_file() {
	if ( ! is_super_admin() )
		return;
	if ( ! file_exists( WP_CONTENT_DIR . '/blogs.php' ) )
		return;
	echo '<div id="update-nag">' . sprintf( __( 'The <code>%1$s</code> file is deprecated. Please remove it and update your server rewrite rules to use <code>%2$s</code> instead.' ), 'wp-content/blogs.php', 'wp-includes/ms-files.php' ) . '</div>';
}
add_action( 'admin_notices', 'ms_deprecated_blogs_file' );

?>

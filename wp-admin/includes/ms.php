<?php
/**
 * Multisite administration functions.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/**
 * Determine if uploaded file exceeds space quota.
 *
 * @since 3.0.0
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
		$file['error'] = sprintf( __( 'Not enough space to upload. %1$s KB needed.' ), number_format( ($file_size - $space_left) /1024 ) );
	if ( $file_size > ( 1024 * get_site_option( 'fileupload_maxk', 1500 ) ) )
		$file['error'] = sprintf(__('This file is too big. Files must be less than %1$s KB in size.'), get_site_option( 'fileupload_maxk', 1500 ) );
	if ( upload_is_user_over_quota( false ) ) {
		$file['error'] = __( 'You have used your space quota. Please delete files before uploading.' );
	}
	if ( $file['error'] != '0' && !isset($_POST['html-upload']) )
		wp_die( $file['error'] . ' <a href="javascript:history.go(-1)">' . __( 'Back' ) . '</a>' );

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'check_upload_size' );

/**
 * Delete a blog
 *
 * @since 3.0.0
 *
 * @param int $blog_id Blog ID
 * @param bool $drop True if blog's table should be dropped.  Default is false.
 * @return void
 */
function wpmu_delete_blog( $blog_id, $drop = false ) {
	global $wpdb;

	$switch = false;
	if ( $blog_id != $wpdb->blogid ) {
		$switch = true;
		switch_to_blog( $blog_id );
	}

	$blog_prefix = $wpdb->get_blog_prefix( $blog_id );

	do_action( 'delete_blog', $blog_id, $drop );

	$users = get_users_of_blog( $blog_id );

	// Remove users from this blog.
	if ( ! empty( $users ) ) {
		foreach ( $users as $user ) {
			remove_user_from_blog( $user->user_id, $blog_id) ;
		}
	}

	update_blog_status( $blog_id, 'deleted', 1 );

	if ( $drop ) {
		if ( substr( $blog_prefix, -1 ) == '_' )
			$blog_prefix =  substr( $blog_prefix, 0, -1 ) . '\_';

		$drop_tables = $wpdb->get_results( "SHOW TABLES LIKE '{$blog_prefix}%'", ARRAY_A );
		$drop_tables = apply_filters( 'wpmu_drop_tables', $drop_tables );

		reset( $drop_tables );
		foreach ( (array) $drop_tables as $drop_table) {
			$wpdb->query( "DROP TABLE IF EXISTS ". current( $drop_table ) ."" );
		}
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->blogs WHERE blog_id = %d", $blog_id ) );
		$dir = apply_filters( 'wpmu_delete_blog_upload_dir', WP_CONTENT_DIR . "/blogs.dir/{$blog_id}/files/", $blog_id );
		$dir = rtrim( $dir, DIRECTORY_SEPARATOR );
		$top_dir = $dir;
		$stack = array($dir);
		$index = 0;

		while ( $index < count( $stack ) ) {
			# Get indexed directory from stack
			$dir = $stack[$index];

			$dh = @opendir( $dir );
			if ( $dh ) {
				while ( ( $file = @readdir( $dh ) ) !== false ) {
					if ( $file == '.' || $file == '..' )
						continue;

					if ( @is_dir( $dir . DIRECTORY_SEPARATOR . $file ) )
						$stack[] = $dir . DIRECTORY_SEPARATOR . $file;
					else if ( @is_file( $dir . DIRECTORY_SEPARATOR . $file ) )
						@unlink( $dir . DIRECTORY_SEPARATOR . $file );
				}
			}
			$index++;
		}

		$stack = array_reverse( $stack );  // Last added dirs are deepest
		foreach( (array) $stack as $dir ) {
			if ( $dir != $top_dir)
			@rmdir( $dir );
		}
	}

	$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key = '{$blog_prefix}autosave_draft_ids'" );
	$blogs = get_site_option( 'blog_list' );
	if ( is_array( $blogs ) ) {
		foreach ( $blogs as $n => $blog ) {
			if ( $blog['blog_id'] == $blog_id )
				unset( $blogs[$n] );
		}
		update_site_option( 'blog_list', $blogs );
	}

	if ( $switch === true )
		restore_current_blog();
}

// @todo Merge with wp_delete_user() ?
function wpmu_delete_user( $id ) {
	global $wpdb;

	$id = (int) $id;

	do_action( 'wpmu_delete_user', $id );

	$blogs = get_blogs_of_user( $id );

	if ( ! empty( $blogs ) ) {
		foreach ( $blogs as $blog ) {
			switch_to_blog( $blog->userblog_id );
			remove_user_from_blog( $id, $blog->userblog_id );

			$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_author = %d", $id ) );
			foreach ( (array) $post_ids as $post_id ) {
				wp_delete_post( $post_id );
			}

			// Clean links
			$link_ids = $wpdb->get_col( $wpdb->prepare( "SELECT link_id FROM $wpdb->links WHERE link_owner = %d", $id ) );

			if ( $link_ids ) {
				foreach ( $link_ids as $link_id )
					wp_delete_link( $link_id );
			}

			restore_current_blog();
		}
	}

	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->users WHERE ID = %d", $id ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->usermeta WHERE user_id = %d", $id ) );

	clean_user_cache( $id );

	// allow for commit transaction
	do_action( 'deleted_user', $id );

	return true;
}

function wpmu_get_blog_allowedthemes( $blog_id = 0 ) {
	$themes = get_themes();

	if ( $blog_id != 0 )
		switch_to_blog( $blog_id );

	$blog_allowed_themes = get_option( 'allowedthemes' );
	if ( !is_array( $blog_allowed_themes ) || empty( $blog_allowed_themes ) ) { // convert old allowed_themes to new allowedthemes
		$blog_allowed_themes = get_option( 'allowed_themes' );

		if ( is_array( $blog_allowed_themes ) ) {
			foreach( (array) $themes as $key => $theme ) {
				$theme_key = esc_html( $theme['Stylesheet'] );
				if ( isset( $blog_allowed_themes[$key] ) == true ) {
					$blog_allowedthemes[$theme_key] = 1;
				}
			}
			$blog_allowed_themes = $blog_allowedthemes;
			add_option( 'allowedthemes', $blog_allowed_themes );
			delete_option( 'allowed_themes' );
		}
	}

	if ( $blog_id != 0 )
		restore_current_blog();

	return $blog_allowed_themes;
}

function update_option_new_admin_email( $old_value, $value ) {
	$email = get_option( 'admin_email' );
	if ( $value == get_option( 'admin_email' ) || !is_email( $value ) )
		return;

	$hash = md5( $value. time() .mt_rand() );
	$new_admin_email = array(
		'hash' => $hash,
		'newemail' => $value
	);
	update_option( 'adminhash', $new_admin_email );

	$content = apply_filters( 'new_admin_email_content', __( "Dear user,

You recently requested to have the administration email address on
your site changed.
If this is correct, please click on the following link to change it:
###ADMIN_URL###

You can safely ignore and delete this email if you do not want to
take this action.

This email has been sent to ###EMAIL###

Regards,
All at ###SITENAME###
###SITEURL### "), $new_admin_email );

	$content = str_replace( '###ADMIN_URL###', esc_url( admin_url( 'options.php?adminhash='.$hash ) ), $content );
	$content = str_replace( '###EMAIL###', $value, $content );
	$content = str_replace( '###SITENAME###', get_site_option( 'site_name' ), $content );
	$content = str_replace( '###SITEURL###', network_home_url(), $content );

	wp_mail( $value, sprintf( __( '[%s] New Admin Email Address' ), get_option( 'blogname' ) ), $content );
}
add_action( 'update_option_new_admin_email', 'update_option_new_admin_email', 10, 2 );
add_action( 'add_option_new_admin_email', 'update_option_new_admin_email', 10, 2 );

function send_confirmation_on_profile_email() {
	global $errors, $wpdb;
	$current_user = wp_get_current_user();
	if ( ! is_object($errors) )
		$errors = new WP_Error();

	if ( $current_user->id != $_POST['user_id'] )
		return false;

	if ( $current_user->user_email != $_POST['email'] ) {
		if ( !is_email( $_POST['email'] ) ) {
			$errors->add( 'user_email', __( "<strong>ERROR</strong>: The e-mail address isn't correct." ), array( 'form-field' => 'email' ) );
			return;
		}

		if ( $wpdb->get_var( $wpdb->prepare( "SELECT user_email FROM {$wpdb->users} WHERE user_email=%s", $_POST['email'] ) ) ) {
			$errors->add( 'user_email', __( "<strong>ERROR</strong>: The e-mail address is already used." ), array( 'form-field' => 'email' ) );
			delete_option( $current_user->ID . '_new_email' );
			return;
		}

		$hash = md5( $_POST['email'] . time() . mt_rand() );
		$new_user_email = array(
				'hash' => $hash,
				'newemail' => $_POST['email']
				);
		update_option( $current_user->ID . '_new_email', $new_user_email );

		$content = apply_filters( 'new_user_email_content', __( "Dear user,

You recently requested to have the email address on your account changed.
If this is correct, please click on the following link to change it:
###ADMIN_URL###

You can safely ignore and delete this email if you do not want to
take this action.

This email has been sent to ###EMAIL###

Regards,
All at ###SITENAME###
###SITEURL###" ), $new_user_email );

		$content = str_replace( '###ADMIN_URL###', esc_url( admin_url( 'profile.php?newuseremail='.$hash ) ), $content );
		$content = str_replace( '###EMAIL###', $_POST['email'], $content);
		$content = str_replace( '###SITENAME###', get_site_option( 'site_name' ), $content );
		$content = str_replace( '###SITEURL###', network_home_url(), $content );

		wp_mail( $_POST['email'], sprintf( __( '[%s] New Email Address' ), get_option( 'blogname' ) ), $content );
		$_POST['email'] = $current_user->user_email;
	}
}
add_action( 'personal_options_update', 'send_confirmation_on_profile_email' );

function new_user_email_admin_notice() {
	if ( strpos( $_SERVER['PHP_SELF'], 'profile.php' ) && isset( $_GET['updated'] ) && $email = get_option( get_current_user_id() . '_new_email' ) )
		echo "<div class='update-nag'>" . sprintf( __( "Your email address has not been updated yet. Please check your inbox at %s for a confirmation email." ), $email['newemail'] ) . "</div>";
}
add_action( 'admin_notices', 'new_user_email_admin_notice' );

function get_site_allowed_themes() {
	$themes = get_themes();
	$allowed_themes = get_site_option( 'allowedthemes' );
	if ( !is_array( $allowed_themes ) || empty( $allowed_themes ) ) {
		$allowed_themes = get_site_option( 'allowed_themes' ); // convert old allowed_themes format
		if ( !is_array( $allowed_themes ) ) {
			$allowed_themes = array();
		} else {
			foreach( (array) $themes as $key => $theme ) {
				$theme_key = esc_html( $theme['Stylesheet'] );
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
 * @since 3.0.0
 * @return bool True if space is available, false otherwise.
 */
function is_upload_space_available() {
	if ( get_site_option( 'upload_space_check_disabled' ) )
		return true;

	if ( !( $space_allowed = get_upload_space_available() ) )
		return false;

	return true;
}

/*
 * @since 3.0.0
 *
 * @return int of upload size limit in bytes
 */
function upload_size_limit_filter( $size ) {
	$fileupload_maxk = 1024 * get_site_option( 'fileupload_maxk', 1500 );
	return min( $size, $fileupload_maxk, get_upload_space_available() );
}
/**
 * Determines if there is any upload space left in the current blog's quota.
 *
 * @return int of upload space available in bytes
 */
function get_upload_space_available() {
	$space_allowed = get_space_allowed() * 1024 * 1024;
	if ( get_site_option( 'upload_space_check_disabled' ) )
		return $space_allowed;

	$dir_name = trailingslashit( BLOGUPLOADDIR );
	if ( !( is_dir( $dir_name) && is_readable( $dir_name ) ) )
		return $space_allowed;

  	$dir = dir( $dir_name );
   	$size = 0;

	while ( $file = $dir->read() ) {
		if ( $file != '.' && $file != '..' ) {
			if ( is_dir( $dir_name . $file) ) {
				$size += get_dirsize( $dir_name . $file );
			} else {
				$size += filesize( $dir_name . $file );
			}
		}
	}
	$dir->close();

	if ( ( $space_allowed - $size ) <= 0 )
		return 0;

	return $space_allowed - $size;
}

/**
 * Returns the upload quota for the current blog.
 *
 * @return int Quota
 */
function get_space_allowed() {
	$space_allowed = get_option( 'blog_upload_space' );
	if ( $space_allowed == false )
		$space_allowed = get_site_option( 'blog_upload_space' );
	if ( empty( $space_allowed ) || !is_numeric( $space_allowed ) )
		$space_allowed = 50;

	return $space_allowed;
}

function display_space_usage() {
	$space = get_space_allowed();
	$used = get_dirsize( BLOGUPLOADDIR ) / 1024 / 1024;

	$percentused = ( $used / $space ) * 100;

	if ( $space > 1000 ) {
		$space = number_format( $space / 1024 );
		/* translators: Gigabytes */
		$space .= __( 'GB' );
	} else {
		/* translators: Megabytes */
		$space .= __( 'MB' );
	}
	?>
	<strong><?php printf( __( 'Used: %1s%% of %2s' ), number_format( $percentused ), $space ); ?></strong>
	<?php
}

// Edit blog upload space setting on Edit Blog page
function upload_space_setting( $id ) {
	$quota = get_blog_option( $id, 'blog_upload_space' );
	if ( !$quota )
		$quota = '';

	?>
	<tr>
		<th><?php _e( 'Site Upload Space Quota '); ?></th>
		<td><input type="text" size="3" name="option[blog_upload_space]" value="<?php echo $quota; ?>" /> <?php _e( 'MB (Leave blank for network default)' ); ?></td>
	</tr>
	<?php
}
add_action( 'wpmueditblogaction', 'upload_space_setting' );

function update_user_status( $id, $pref, $value, $refresh = 1 ) {
	global $wpdb;

	$wpdb->update( $wpdb->users, array( $pref => $value ), array( 'ID' => $id ) );

	if ( $refresh == 1 )
		refresh_user_details( $id );

	if ( $pref == 'spam' ) {
		if ( $value == 1 )
			do_action( 'make_spam_user', $id );
		else
			do_action( 'make_ham_user', $id );
	}

	return $value;
}

function refresh_user_details( $id ) {
	$id = (int) $id;

	if ( !$user = get_userdata( $id ) )
		return false;

	clean_user_cache( $id );

	return $id;
}

function format_code_lang( $code = '' ) {
	$code = strtolower( substr( $code, 0, 2 ) );
	$lang_codes = array(
		'aa' => 'Afar', 'ab' => 'Abkhazian', 'af' => 'Afrikaans', 'ak' => 'Akan', 'sq' => 'Albanian', 'am' => 'Amharic', 'ar' => 'Arabic', 'an' => 'Aragonese', 'hy' => 'Armenian', 'as' => 'Assamese', 'av' => 'Avaric', 'ae' => 'Avestan', 'ay' => 'Aymara', 'az' => 'Azerbaijani', 'ba' => 'Bashkir', 'bm' => 'Bambara', 'eu' => 'Basque', 'be' => 'Belarusian', 'bn' => 'Bengali',
		'bh' => 'Bihari', 'bi' => 'Bislama', 'bs' => 'Bosnian', 'br' => 'Breton', 'bg' => 'Bulgarian', 'my' => 'Burmese', 'ca' => 'Catalan; Valencian', 'ch' => 'Chamorro', 'ce' => 'Chechen', 'zh' => 'Chinese', 'cu' => 'Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic', 'cv' => 'Chuvash', 'kw' => 'Cornish', 'co' => 'Corsican', 'cr' => 'Cree',
		'cs' => 'Czech', 'da' => 'Danish', 'dv' => 'Divehi; Dhivehi; Maldivian', 'nl' => 'Dutch; Flemish', 'dz' => 'Dzongkha', 'en' => 'English', 'eo' => 'Esperanto', 'et' => 'Estonian', 'ee' => 'Ewe', 'fo' => 'Faroese', 'fj' => 'Fijjian', 'fi' => 'Finnish', 'fr' => 'French', 'fy' => 'Western Frisian', 'ff' => 'Fulah', 'ka' => 'Georgian', 'de' => 'German', 'gd' => 'Gaelic; Scottish Gaelic',
		'ga' => 'Irish', 'gl' => 'Galician', 'gv' => 'Manx', 'el' => 'Greek, Modern', 'gn' => 'Guarani', 'gu' => 'Gujarati', 'ht' => 'Haitian; Haitian Creole', 'ha' => 'Hausa', 'he' => 'Hebrew', 'hz' => 'Herero', 'hi' => 'Hindi', 'ho' => 'Hiri Motu', 'hu' => 'Hungarian', 'ig' => 'Igbo', 'is' => 'Icelandic', 'io' => 'Ido', 'ii' => 'Sichuan Yi', 'iu' => 'Inuktitut', 'ie' => 'Interlingue',
		'ia' => 'Interlingua (International Auxiliary Language Association)', 'id' => 'Indonesian', 'ik' => 'Inupiaq', 'it' => 'Italian', 'jv' => 'Javanese', 'ja' => 'Japanese', 'kl' => 'Kalaallisut; Greenlandic', 'kn' => 'Kannada', 'ks' => 'Kashmiri', 'kr' => 'Kanuri', 'kk' => 'Kazakh', 'km' => 'Central Khmer', 'ki' => 'Kikuyu; Gikuyu', 'rw' => 'Kinyarwanda', 'ky' => 'Kirghiz; Kyrgyz',
		'kv' => 'Komi', 'kg' => 'Kongo', 'ko' => 'Korean', 'kj' => 'Kuanyama; Kwanyama', 'ku' => 'Kurdish', 'lo' => 'Lao', 'la' => 'Latin', 'lv' => 'Latvian', 'li' => 'Limburgan; Limburger; Limburgish', 'ln' => 'Lingala', 'lt' => 'Lithuanian', 'lb' => 'Luxembourgish; Letzeburgesch', 'lu' => 'Luba-Katanga', 'lg' => 'Ganda', 'mk' => 'Macedonian', 'mh' => 'Marshallese', 'ml' => 'Malayalam',
		'mi' => 'Maori', 'mr' => 'Marathi', 'ms' => 'Malay', 'mg' => 'Malagasy', 'mt' => 'Maltese', 'mo' => 'Moldavian', 'mn' => 'Mongolian', 'na' => 'Nauru', 'nv' => 'Navajo; Navaho', 'nr' => 'Ndebele, South; South Ndebele', 'nd' => 'Ndebele, North; North Ndebele', 'ng' => 'Ndonga', 'ne' => 'Nepali', 'nn' => 'Norwegian Nynorsk; Nynorsk, Norwegian', 'nb' => 'Bokmål, Norwegian, Norwegian Bokmål',
		'no' => 'Norwegian', 'ny' => 'Chichewa; Chewa; Nyanja', 'oc' => 'Occitan, Provençal', 'oj' => 'Ojibwa', 'or' => 'Oriya', 'om' => 'Oromo', 'os' => 'Ossetian; Ossetic', 'pa' => 'Panjabi; Punjabi', 'fa' => 'Persian', 'pi' => 'Pali', 'pl' => 'Polish', 'pt' => 'Portuguese', 'ps' => 'Pushto', 'qu' => 'Quechua', 'rm' => 'Romansh', 'ro' => 'Romanian', 'rn' => 'Rundi', 'ru' => 'Russian',
		'sg' => 'Sango', 'sa' => 'Sanskrit', 'sr' => 'Serbian', 'hr' => 'Croatian', 'si' => 'Sinhala; Sinhalese', 'sk' => 'Slovak', 'sl' => 'Slovenian', 'se' => 'Northern Sami', 'sm' => 'Samoan', 'sn' => 'Shona', 'sd' => 'Sindhi', 'so' => 'Somali', 'st' => 'Sotho, Southern', 'es' => 'Spanish; Castilian', 'sc' => 'Sardinian', 'ss' => 'Swati', 'su' => 'Sundanese', 'sw' => 'Swahili',
		'sv' => 'Swedish', 'ty' => 'Tahitian', 'ta' => 'Tamil', 'tt' => 'Tatar', 'te' => 'Telugu', 'tg' => 'Tajik', 'tl' => 'Tagalog', 'th' => 'Thai', 'bo' => 'Tibetan', 'ti' => 'Tigrinya', 'to' => 'Tonga (Tonga Islands)', 'tn' => 'Tswana', 'ts' => 'Tsonga', 'tk' => 'Turkmen', 'tr' => 'Turkish', 'tw' => 'Twi', 'ug' => 'Uighur; Uyghur', 'uk' => 'Ukrainian', 'ur' => 'Urdu', 'uz' => 'Uzbek',
		've' => 'Venda', 'vi' => 'Vietnamese', 'vo' => 'Volapük', 'cy' => 'Welsh','wa' => 'Walloon','wo' => 'Wolof', 'xh' => 'Xhosa', 'yi' => 'Yiddish', 'yo' => 'Yoruba', 'za' => 'Zhuang; Chuang', 'zu' => 'Zulu' );
	$lang_codes = apply_filters( 'lang_codes', $lang_codes, $code );
	return strtr( $code, $lang_codes );
}

function sync_category_tag_slugs( $term, $taxonomy ) {
	if ( global_terms_enabled() && ( $taxonomy == 'category' || $taxonomy == 'post_tag' ) ) {
		if ( is_object( $term ) ) {
			$term->slug = sanitize_title( $term->name );
		} else {
			$term['slug'] = sanitize_title( $term['name'] );
		}
	}
	return $term;
}
add_filter( 'get_term', 'sync_category_tag_slugs', 10, 2 );

function redirect_user_to_blog() {
	$c = 0;
	if ( isset( $_GET['c'] ) )
		$c = (int) $_GET['c'];

	if ( $c >= 5 ) {
		wp_die( __( "You don&#8217;t have permission to view this site. Please contact the system administrator." ) );
	}
	$c ++;

	$blog = get_active_blog_for_user( get_current_user_id() );

	if ( is_object( $blog ) ) {
		wp_redirect( get_admin_url( $blog->blog_id, '?c=' . $c ) ); // redirect and count to 5, "just in case"
		exit;
	} else {
		wp_redirect( user_admin_url( '?c=' . $c ) ); // redirect and count to 5, "just in case"
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
			$ae = __( 'American English' );
			$output[$ae] = '<option value="' . esc_attr( $code_lang ) . '"' . selected( $current, $code_lang, false ) . '> ' . $ae . '</option>';
		} elseif ( $code_lang == 'en_GB' ) { // British English
			$flag = true;
			$be = __( 'British English' );
			$output[$be] = '<option value="' . esc_attr( $code_lang ) . '"' . selected( $current, $code_lang, false ) . '> ' . $be . '</option>';
		} else {
			$translated = format_code_lang( $code_lang );
			$output[$translated] =  '<option value="' . esc_attr( $code_lang ) . '"' . selected( $current, $code_lang, false ) . '> ' . esc_html ( $translated ) . '</option>';
		}

	}

	if ( $flag === false ) // WordPress english
		$output[] = '<option value=""' . selected( $current, '', false ) . '>' . __( 'English' ) . "</option>";

	// Order by name
	uksort( $output, 'strnatcasecmp' );

	$output = apply_filters( 'mu_dropdown_languages', $output, $lang_files, $current );
	echo implode( "\n\t", $output );
}

/* Warn the admin if SECRET SALT information is missing from wp-config.php */
function secret_salt_warning() {
	if ( !is_super_admin() )
		return;
	$secret_keys = array( 'AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT' );
	$out = '';
	foreach( $secret_keys as $key ) {
		if ( ! defined( $key ) )
			$out .= "define( '$key', '" . esc_html( wp_generate_password( 64, true, true ) ) . "' );<br />";
	}
	if ( $out != '' ) {
		$msg  = __( 'Warning! WordPress encrypts user cookies, but you must add the following lines to <strong>wp-config.php</strong> for it to be more secure.' );
		$msg .= '<br/>' . __( "Before the line <code>/* That's all, stop editing! Happy blogging. */</code> please add this code:" );
		$msg .= "<br/><br/><code>$out</code>";

		echo "<div class='update-nag'>$msg</div>";
	}
}
add_action( 'admin_notices', 'secret_salt_warning' );

function site_admin_notice() {
	global $wp_db_version;
	if ( !is_super_admin() )
		return false;
	if ( get_site_option( 'wpmu_upgrade_site' ) != $wp_db_version )
		echo "<div class='update-nag'>" . sprintf( __( 'Thank you for Updating! Please visit the <a href="%s">Update Network</a> page to update all your sites.' ), esc_url( network_admin_url( 'upgrade.php' ) ) ) . "</div>";
}
add_action( 'admin_notices', 'site_admin_notice' );

function avoid_blog_page_permalink_collision( $data, $postarr ) {
	if ( is_subdomain_install() )
		return $data;
	if ( $data['post_type'] != 'page' )
		return $data;
	if ( !isset( $data['post_name'] ) || $data['post_name'] == '' )
		return $data;
	if ( !is_main_site() )
		return $data;

	$post_name = $data['post_name'];
	$c = 0;
	while( $c < 10 && get_id_from_blogname( $post_name ) ) {
		$post_name .= mt_rand( 1, 10 );
		$c ++;
	}
	if ( $post_name != $data['post_name'] ) {
		$data['post_name'] = $post_name;
	}
	return $data;
}
add_filter( 'wp_insert_post_data', 'avoid_blog_page_permalink_collision', 10, 2 );

function choose_primary_blog() {
	?>
	<table class="form-table">
	<tr>
	<?php /* translators: My sites label */ ?>
		<th scope="row"><?php _e( 'Primary Site' ); ?></th>
		<td>
		<?php
		$all_blogs = get_blogs_of_user( get_current_user_id() );
		$primary_blog = get_user_meta( get_current_user_id(), 'primary_blog', true );
		if ( count( $all_blogs ) > 1 ) {
			$found = false;
			?>
			<select name="primary_blog">
				<?php foreach( (array) $all_blogs as $blog ) {
					if ( $primary_blog == $blog->userblog_id )
						$found = true;
					?><option value="<?php echo $blog->userblog_id ?>"<?php selected( $primary_blog,  $blog->userblog_id ); ?>><?php echo esc_url( get_home_url( $blog->userblog_id ) ) ?></option><?php
				} ?>
			</select>
			<?php
			if ( !$found ) {
				$blog = array_shift( $all_blogs );
				update_user_meta( get_current_user_id(), 'primary_blog', $blog->userblog_id );
			}
		} elseif ( count( $all_blogs ) == 1 ) {
			$blog = array_shift( $all_blogs );
			echo $blog->domain;
			if ( $primary_blog != $blog->userblog_id ) // Set the primary blog again if it's out of sync with blog list.
				update_user_meta( get_current_user_id(), 'primary_blog', $blog->userblog_id );
		} else {
			echo "N/A";
		}
		?>
		</td>
	</tr>
	<?php if ( in_array( get_site_option( 'registration' ), array( 'all', 'blog' ) ) ) : ?>
		<tr>
			<th scope="row" colspan="2" class="th-full">
				<a href="<?php echo apply_filters( 'wp_signup_location', network_home_url( 'wp-signup.php' ) ); ?>"><?php _e( 'Create a New Site' ); ?></a>
			</th>
		</tr>
	<?php endif; ?>
	</table>
	<?php
}

function ms_deprecated_blogs_file() {
	if ( ! is_super_admin() )
		return;
	if ( ! file_exists( WP_CONTENT_DIR . '/blogs.php' ) )
		return;
	echo '<div class="update-nag">' . sprintf( __( 'The <code>%1$s</code> file is deprecated. Please remove it and update your server rewrite rules to use <code>%2$s</code> instead.' ), 'wp-content/blogs.php', 'wp-includes/ms-files.php' ) . '</div>';
}
add_action( 'admin_notices', 'ms_deprecated_blogs_file' );

/**
 * Grants super admin privileges.
 *
 * @since 3.0.0
 * @param int $user_id
 */
function grant_super_admin( $user_id ) {
	global $super_admins;

	// If global super_admins override is defined, there is nothing to do here.
	if ( isset($super_admins) )
		return false;

	do_action( 'grant_super_admin', $user_id );

	// Directly fetch site_admins instead of using get_super_admins()
	$super_admins = get_site_option( 'site_admins', array( 'admin' ) );

	$user = new WP_User( $user_id );
	if ( ! in_array( $user->user_login, $super_admins ) ) {
		$super_admins[] = $user->user_login;
		update_site_option( 'site_admins' , $super_admins );
		do_action( 'granted_super_admin', $user_id );
		return true;
	}
	return false;
}

/**
 * Revokes super admin privileges.
 *
 * @since 3.0.0
 * @param int $user_id
 */
function revoke_super_admin( $user_id ) {
	global $super_admins;

	// If global super_admins override is defined, there is nothing to do here.
	if ( isset($super_admins) )
		return false;

	do_action( 'revoke_super_admin', $user_id );

	// Directly fetch site_admins instead of using get_super_admins()
	$super_admins = get_site_option( 'site_admins', array( 'admin' ) );

	$user = new WP_User( $user_id );
	if ( $user->user_email != get_site_option( 'admin_email' ) ) {
		if ( false !== ( $key = array_search( $user->user_login, $super_admins ) ) ) {
			unset( $super_admins[$key] );
			update_site_option( 'site_admins', $super_admins );
			do_action( 'revoked_super_admin', $user_id );
			return true;
		}
	}
	return false;
}
?>

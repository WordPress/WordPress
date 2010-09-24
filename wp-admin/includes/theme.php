<?php
/**
 * WordPress Theme Administration API
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function current_theme_info() {
	$themes = get_themes();
	$current_theme = get_current_theme();
	if ( ! isset( $themes[$current_theme] ) ) {
		delete_option( 'current_theme' );
		$current_theme = get_current_theme();
	}
	$ct->name = $current_theme;
	$ct->title = $themes[$current_theme]['Title'];
	$ct->version = $themes[$current_theme]['Version'];
	$ct->parent_theme = $themes[$current_theme]['Parent Theme'];
	$ct->template_dir = $themes[$current_theme]['Template Dir'];
	$ct->stylesheet_dir = $themes[$current_theme]['Stylesheet Dir'];
	$ct->template = $themes[$current_theme]['Template'];
	$ct->stylesheet = $themes[$current_theme]['Stylesheet'];
	$ct->screenshot = $themes[$current_theme]['Screenshot'];
	$ct->description = $themes[$current_theme]['Description'];
	$ct->author = $themes[$current_theme]['Author'];
	$ct->tags = $themes[$current_theme]['Tags'];
	$ct->theme_root = $themes[$current_theme]['Theme Root'];
	$ct->theme_root_uri = $themes[$current_theme]['Theme Root URI'];
	return $ct;
}

/**
 * Remove a theme
 *
 * @since 2.8.0
 *
 * @param string $template Template directory of the theme to delete
 * @return mixed
 */
function delete_theme($template) {
	global $wp_filesystem;

	if ( empty($template) )
		return false;

	ob_start();
	$url = wp_nonce_url('themes.php?action=delete&template=' . $template, 'delete-theme_' . $template);
	if ( false === ($credentials = request_filesystem_credentials($url)) ) {
		$data = ob_get_contents();
		ob_end_clean();
		if ( ! empty($data) ){
			include_once( ABSPATH . 'wp-admin/admin-header.php');
			echo $data;
			include( ABSPATH . 'wp-admin/admin-footer.php');
			exit;
		}
		return;
	}

	if ( ! WP_Filesystem($credentials) ) {
		request_filesystem_credentials($url, '', true); // Failed to connect, Error and request again
		$data = ob_get_contents();
		ob_end_clean();
		if ( ! empty($data) ) {
			include_once( ABSPATH . 'wp-admin/admin-header.php');
			echo $data;
			include( ABSPATH . 'wp-admin/admin-footer.php');
			exit;
		}
		return;
	}


	if ( ! is_object($wp_filesystem) )
		return new WP_Error('fs_unavailable', __('Could not access filesystem.'));

	if ( is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code() )
		return new WP_Error('fs_error', __('Filesystem error.'), $wp_filesystem->errors);

	//Get the base plugin folder
	$themes_dir = $wp_filesystem->wp_themes_dir();
	if ( empty($themes_dir) )
		return new WP_Error('fs_no_themes_dir', __('Unable to locate WordPress theme directory.'));

	$themes_dir = trailingslashit( $themes_dir );
	$theme_dir = trailingslashit($themes_dir . $template);
	$deleted = $wp_filesystem->delete($theme_dir, true);

	if ( ! $deleted )
		return new WP_Error('could_not_remove_theme', sprintf(__('Could not fully remove the theme %s.'), $template) );

	// Force refresh of theme update information
	delete_site_transient('update_themes');

	return true;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function get_broken_themes() {
	global $wp_broken_themes;

	get_themes();
	return $wp_broken_themes;
}

/**
 * Get the allowed themes for the current blog.
 *
 * @since 3.0.0
 *
 * @uses get_themes()
 * @uses current_theme_info()
 * @uses get_site_allowed_themes()
 * @uses wpmu_get_blog_allowedthemes
 *
 * @return array $themes Array of allowed themes.
 */
function get_allowed_themes() {
	if ( !is_multisite() )
		return get_themes();

	$themes = get_themes();
	$ct = current_theme_info();
	$allowed_themes = apply_filters("allowed_themes", get_site_allowed_themes() );
	if ( $allowed_themes == false )
		$allowed_themes = array();

	$blog_allowed_themes = wpmu_get_blog_allowedthemes();
	if ( is_array( $blog_allowed_themes ) )
		$allowed_themes = array_merge( $allowed_themes, $blog_allowed_themes );

	if ( isset( $allowed_themes[ esc_html( $ct->stylesheet ) ] ) == false )
		$allowed_themes[ esc_html( $ct->stylesheet ) ] = true;

	reset( $themes );
	foreach ( $themes as $key => $theme ) {
		if ( isset( $allowed_themes[ esc_html( $theme[ 'Stylesheet' ] ) ] ) == false )
			unset( $themes[ $key ] );
	}
	reset( $themes );

	return $themes;
}

/**
 * Get the Page Templates available in this theme
 *
 * @since unknown
 *
 * @return array Key is template name, Value is template name
 */
function get_page_templates() {
	$themes = get_themes();
	$theme = get_current_theme();
	$templates = $themes[$theme]['Template Files'];
	$page_templates = array();

	if ( is_array( $templates ) ) {
		$base = array( trailingslashit(get_template_directory()), trailingslashit(get_stylesheet_directory()) );

		foreach ( $templates as $template ) {
			$basename = str_replace($base, '', $template);

			// don't allow template files in subdirectories
			if ( false !== strpos($basename, '/') )
				continue;

			$template_data = implode( '', file( $template ));

			$name = '';
			if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ) )
				$name = _cleanup_header_comment($name[1]);

			if ( !empty( $name ) ) {
				$page_templates[trim( $name )] = $basename;
			}
		}
	}

	return $page_templates;
}

/**
 * Tidies a filename for url display by the theme editor.
 *
 * @since 2.9.0
 * @access private
 *
 * @param string $fullpath Full path to the theme file
 * @param string $containingfolder Path of the theme parent folder
 * @return string
 */
function _get_template_edit_filename($fullpath, $containingfolder) {
	return str_replace(dirname(dirname( $containingfolder )) , '', $fullpath);
}

/**
 * Check if there is an update for a theme available.
 *
 * Will display link, if there is an update available.
 *
 * @since 2.7.0
 *
 * @param object $theme Theme data object.
 * @return bool False if no valid info was passed.
 */
function theme_update_available( $theme ) {
	static $themes_update;

	if ( !current_user_can('update_themes' ) )
		return;

	if ( !isset($themes_update) )
		$themes_update = get_site_transient('update_themes');

	if ( is_object($theme) && isset($theme->stylesheet) )
		$stylesheet = $theme->stylesheet;
	elseif ( is_array($theme) && isset($theme['Stylesheet']) )
		$stylesheet = $theme['Stylesheet'];
	else
		return false; //No valid info passed.

	if ( isset($themes_update->response[ $stylesheet ]) ) {
		$update = $themes_update->response[ $stylesheet ];
		$theme_name = is_object($theme) ? $theme->name : (is_array($theme) ? $theme['Name'] : '');
		$details_url = add_query_arg(array('TB_iframe' => 'true', 'width' => 1024, 'height' => 800), $update['url']); //Theme browser inside WP? replace this, Also, theme preview JS will override this on the available list.
		$update_url = wp_nonce_url('update.php?action=upgrade-theme&amp;theme=' . urlencode($stylesheet), 'upgrade-theme_' . $stylesheet);
		$update_onclick = 'onclick="if ( confirm(\'' . esc_js( __("Upgrading this theme will lose any customizations you have made.  'Cancel' to stop, 'OK' to upgrade.") ) . '\') ) {return true;}return false;"';

		if ( ! current_user_can('update_themes') )
			printf( '<p><strong>' . __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%1$s">View version %3$s details</a>.') . '</strong></p>', $theme_name, $details_url, $update['new_version']);
		else if ( empty($update['package']) )
			printf( '<p><strong>' . __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%1$s">View version %3$s details</a>. <em>Automatic upgrade is unavailable for this theme.</em>') . '</strong></p>', $theme_name, $details_url, $update['new_version']);
		else
			printf( '<p><strong>' . __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%1$s">View version %3$s details</a> or <a href="%4$s" %5$s>upgrade automatically</a>.') . '</strong></p>', $theme_name, $details_url, $update['new_version'], $update_url, $update_onclick );
	}
}

/**
 * Retrieve list of WordPress theme features (aka theme tags)
 *
 * @since 3.1.0
 *
 * @return array  Array of features keyed by category with translations keyed by slug.
 */
function get_theme_feature_list() {
	// Hard-coded list is used if api not accessible.
	$features = array(
			__('Colors') => array(
				'black'   => __( 'Black' ),
				'blue'    => __( 'Blue' ),
				'brown'   => __( 'Brown' ),
				'green'   => __( 'Green' ),
				'orange'  => __( 'Orange' ),
				'pink'    => __( 'Pink' ),
				'purple'  => __( 'Purple' ),
				'red'     => __( 'Red' ),
				'silver'  => __( 'Silver' ),
				'tan'     => __( 'Tan' ),
				'white'   => __( 'White' ),
				'yellow'  => __( 'Yellow' ),
				'dark'    => __( 'Dark' ),
				'light'   => __( 'Light ')
			),

		__('Columns') => array(
			'one-column'    => __( 'One Column' ),
			'two-columns'   => __( 'Two Columns' ),
			'three-columns' => __( 'Three Columns' ),
			'four-columns'  => __( 'Four Columns' ),
			'left-sidebar'  => __( 'Left Sidebar' ),
			'right-sidebar' => __( 'Right Sidebar' )
		),

		__('Width') => array(
			'fixed-width'    => __( 'Fixed Width' ),
			'flexible-width' => __( 'Flexible Width' )
		),

		__( 'Features' ) => array(
			'blavatar'             => __( 'Blavatar' ),
			'buddypress'           => __( 'BuddyPress' ),
			'custom-background'    => __( 'Custom Background' ),
			'custom-colors'        => __( 'Custom Colors' ),
			'custom-header'        => __( 'Custom Header' ),
			'custom-menu'          => __( 'Custom Menu' ),
			'editor-style'         => __( 'Editor Style' ),
			'front-page-post-form' => __( 'Front Page Posting' ),
			'microformats'         => __( 'Microformats' ),
			'sticky-post'          => __( 'Sticky Post' ),
			'theme-options'        => __( 'Theme Options' ),
			'threaded-comments'    => __( 'Threaded Comments' ),
			'translation-ready'    => __( 'Translation Ready' ),
			'rtl-language-support' => __( 'RTL Language Support' )
		),

		__( 'Subject' )  => array(
			'holiday' => __( 'Holiday' ),
			'photoblogging' => __( 'Photoblogging' ),
			'seasonal' => __( 'Seasonal' )
		)
	);

	if ( !current_user_can('install_themes') )
		return $features;

	if ( !$feature_list = get_site_transient( 'wporg_theme_feature_list' ) )
		set_site_transient( 'wporg_theme_feature_list', array( ),  10800);

	if ( !$feature_list ) {
		$feature_list = themes_api( 'feature_list', array( ) );
		if ( is_wp_error( $feature_list ) )
			return $features;
	}

	if ( !$feature_list )
		return $features;

	set_site_transient( 'wporg_theme_feature_list', $feature_list, 10800 );

	$category_translations = array( 'Colors' => __('Colors'), 'Columns' => __('Columns'), 'Width' => __('Width'),
								   'Features' => __('Features'), 'Subject' => __('Subject') );

	// Loop over the wporg canonical list and apply translations
	$wporg_features = array();
	foreach ( (array) $feature_list as $feature_category => $feature_items ) {
		if ( isset($category_translations[$feature_category]) )
			$feature_category = $category_translations[$feature_category];
		$wporg_features[$feature_category] = array();

		foreach ( $feature_items as $feature ) {
			if ( isset($features[$feature_category][$feature]) )
				$wporg_features[$feature_category][$feature] = $features[$feature_category][$feature];
			else
				$wporg_features[$feature_category][$feature] = $feature;
		}
	}

	return $wporg_features;
}

?>

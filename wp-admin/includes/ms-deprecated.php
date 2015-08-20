<?php
/**
 * Deprecated multisite admin functions from past WordPress versions and WordPress MU.
 * You shouldn't use these functions and look for the alternatives instead. The functions
 * will be removed in a later version.
 *
 * @package WordPress
 * @subpackage Deprecated
 * @since 3.0.0
 */

/**
 * Outputs the WPMU menu.
 *
 * @deprecated 3.0.0
 */
function wpmu_menu() {
	_deprecated_function(__FUNCTION__, '3.0' );
	// Deprecated. See #11763.
}

/**
 * Determines if the available space defined by the admin has been exceeded by the user.
 *
 * @deprecated 3.0.0 Use is_upload_space_available()
 * @see is_upload_space_available()
 */
function wpmu_checkAvailableSpace() {
	_deprecated_function(__FUNCTION__, '3.0', 'is_upload_space_available()' );

	if ( !is_upload_space_available() )
		wp_die( __('Sorry, you must delete files before you can upload any more.') );
}

/**
 * WPMU options.
 *
 * @deprecated 3.0.0
 */
function mu_options( $options ) {
	_deprecated_function(__FUNCTION__, '3.0' );
	return $options;
}

/**
 * Deprecated functionality for activating a network-only plugin.
 *
 * @deprecated 3.0.0 Use activate_plugin()
 * @see activate_plugin()
 */
function activate_sitewide_plugin() {
	_deprecated_function(__FUNCTION__, '3.0', 'activate_plugin()' );
	return false;
}

/**
 * Deprecated functionality for deactivating a network-only plugin.
 *
 * @deprecated 3.0.0 Use deactivate_sitewide_plugin()
 * @see deactivate_sitewide_plugin()
 */
function deactivate_sitewide_plugin( $plugin = false ) {
	_deprecated_function(__FUNCTION__, '3.0', 'deactivate_plugin()' );
}

/**
 * Deprecated functionality for determining if the current plugin is network-only.
 *
 * @deprecated 3.0.0 Use is_network_only_plugin()
 * @see is_network_only_plugin()
 */
function is_wpmu_sitewide_plugin( $file ) {
	_deprecated_function(__FUNCTION__, '3.0', 'is_network_only_plugin()' );
	return is_network_only_plugin( $file );
}

/**
 * Deprecated functionality for getting themes network-enabled themes.
 *
 * @deprecated 3.4.0 Use WP_Theme::get_allowed_on_network()
 * @see WP_Theme::get_allowed_on_network()
 */
function get_site_allowed_themes() {
	_deprecated_function( __FUNCTION__, '3.4', 'WP_Theme::get_allowed_on_network()' );
	return array_map( 'intval', WP_Theme::get_allowed_on_network() );
}

/**
 * Deprecated functionality for getting themes allowed on a specific site.
 *
 * @deprecated 3.4.0 Use WP_Theme::get_allowed_on_site()
 * @see WP_Theme::get_allowed_on_site()
 */
function wpmu_get_blog_allowedthemes( $blog_id = 0 ) {
	_deprecated_function( __FUNCTION__, '3.4', 'WP_Theme::get_allowed_on_site()' );
	return array_map( 'intval', WP_Theme::get_allowed_on_site( $blog_id ) );
}

/**
 * Deprecated functionality for determining whether a file is deprecated.
 *
 * @deprecated 3.5.0
 */
function ms_deprecated_blogs_file() {}

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
 * @deprecated 3.0.0
 */
function wpmu_menu() {
	_deprecated_function(__FUNCTION__, '3.0' );
	// deprecated. See #11763
}

/**
  * Determines if the available space defined by the admin has been exceeded by the user.
  *
  * @deprecated 3.0.0
  * @see is_upload_space_available()
 */
function wpmu_checkAvailableSpace() {
	_deprecated_function(__FUNCTION__, '3.0', 'is_upload_space_available()' );

	if ( !is_upload_space_available() )
		wp_die( __('Sorry, you must delete files before you can upload any more.') );
}

/**
 * @deprecated 3.0.0
 */
function mu_options( $options ) {
	_deprecated_function(__FUNCTION__, '3.0' );
	return $options;
}

/**
 * @deprecated 3.0.0
 * @see activate_plugin()
 */
function activate_sitewide_plugin() {
	_deprecated_function(__FUNCTION__, '3.0', 'activate_plugin()' );
	return false;
}

/**
 * @deprecated 3.0.0
 * @see deactivate_sitewide_plugin()
 */
function deactivate_sitewide_plugin( $plugin = false ) {
	_deprecated_function(__FUNCTION__, '3.0', 'deactivate_plugin()' );
	return;
}

/**
 * @deprecated 3.0.0
 * @see is_network_only_plugin()
 */
function is_wpmu_sitewide_plugin( $file ) {
	_deprecated_function(__FUNCTION__, '3.0', 'is_network_only_plugin()' );
	return is_network_only_plugin( $file );
}

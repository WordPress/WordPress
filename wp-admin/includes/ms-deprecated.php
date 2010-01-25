<?php

/**
 * Deprecated multisite admin functions from past WordPress versions. You shouldn't use these
 * globals and functions and look for the alternatives instead. The functions
 * and globals will be removed in a later version.
 *
 * @package WordPress
 * @subpackage Deprecated
 */

/**
 * @deprecated 3.0
 */
function wpmu_menu() {
	_deprecated_function(__FUNCTION__, '3.0', '' );
	// deprecated. See #11763
}

/**
  * Determines if the available space defined by the admin has been exceeded by the user. Dies if it has.
  * @deprecated 3.0
 */
function wpmu_checkAvailableSpace() {
	_deprecated_function(__FUNCTION__, '3.0', 'is_upload_space_available' );

	if ( !is_upload_space_available() )
		wp_die( __('Sorry, you must delete files before you can upload any more.') );
}

/**
 * @deprecated 3.0
 */
function mu_options( $options ) {
	_deprecated_function(__FUNCTION__, '3.0', '' );
	return $options;
}

?>
<?php
/**
 * Deprecated functions from WordPress MU and the multisite feature. You shouldn't
 * use these functions and look for the alternatives instead. The functions will be
 * removed in a later version.
 *
 * @package WordPress
 * @subpackage Deprecated
 */

/*
 * Deprecated functions come here to die.
 */
 
/**
 * @since unknown
 * @deprecated 3.0
 * @deprecated Use wp_generate_password()
 * @see wp_generate_password()
 */
function generate_random_password( $len = 8 ) {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_generate_password()' );
	return wp_generate_password($len);
}

?>
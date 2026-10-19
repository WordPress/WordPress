<?php
/**
 * Formerly defined noop functions for load-scripts.php and load-styles.php.
 *
 * Scripts and styles are no longer concatenated, so those files no longer load WordPress without
 * its functions, and these noop functions are no longer defined.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.4.0
 * @deprecated 7.2.0 Scripts and styles are no longer concatenated.
 */

if ( function_exists( '_deprecated_file' ) ) {
	_deprecated_file( basename( __FILE__ ), '7.2.0' );
}

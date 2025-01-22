<?php
/**
 * Custom background script.
 *
 * This file is deprecated, use 'wp-admin/includes/class-custom-background.php' instead.
 *
 * @deprecated 5.3.0
 * @package WordPress
 * @subpackage Administration
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

_deprecated_file( basename( __FILE__ ), '5.3.0', 'wp-admin/includes/class-custom-background.php' );

/** Custom_Background class */
require_once ABSPATH . 'wp-admin/includes/class-custom-background.php';

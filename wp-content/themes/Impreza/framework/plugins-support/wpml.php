<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * WPML Support
 *
 * @link https://wpml.org/
 */

if ( ! ( class_exists( 'SitePress' ) AND defined( 'ICL_LANGUAGE_CODE' ) ) ) {
	return;
}

// Adding class to body in Admin panel for pages in non-default language
global $sitepress;
$default_language = $sitepress->get_default_language();

if ( $default_language != ICL_LANGUAGE_CODE ) {
	global $pagenow;
	// Exception: do not add class on theme options page
	if ( ! ( ( $pagenow == 'admin.php' ) && ( $_GET['page'] == 'us-theme-options' ) ) ) {
		function us_admin_add_wpml_nondefault_class( $class ) {
			return $class . ' us_wpml_non_default';
		}
		add_filter( 'admin_body_class', 'us_admin_add_wpml_nondefault_class' );
	}
}

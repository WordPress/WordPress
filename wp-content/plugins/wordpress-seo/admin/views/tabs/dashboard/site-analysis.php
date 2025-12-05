<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Views
 *
 * @uses Yoast_Form $yform Form object.
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * WARNING: This hook is intended for internal use only.
 * Don't use it in your code as it will be removed shortly.
 */
do_action( 'wpseo_settings_tab_site_analysis_internal', $yform );

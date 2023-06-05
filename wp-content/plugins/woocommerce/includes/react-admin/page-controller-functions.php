<?php
/**
 * Convenience functions for PageController.
 *
 * @package WooCommerce\Admin
 */

use Automattic\WooCommerce\Admin\PageController;

/**
 * Connect an existing page to WooCommerce Admin.
 * Passthrough to PageController::connect_page().
 *
 * @param array $options Options for PageController::connect_page().
 */
function wc_admin_connect_page( $options ) {
	$controller = PageController::get_instance();
	$controller->connect_page( $options );
}

/**
 * Register JS-powered WooCommerce Admin Page.
 * Passthrough to PageController::register_page().
 *
 * @param array $options Options for PageController::register_page().
 */
function wc_admin_register_page( $options ) {
	$controller = PageController::get_instance();
	$controller->register_page( $options );
}

/**
 * Is this page connected to WooCommerce Admin?
 * Passthrough to PageController::is_connected_page().
 *
 * @return boolean True if the page is connected to WooCommerce Admin.
 */
function wc_admin_is_connected_page() {
	$controller = PageController::get_instance();
	return $controller->is_connected_page();
}

/**
 * Is this a WooCommerce Admin Page?
 * Passthrough to PageController::is_registered_page().
 *
 * @return boolean True if the page is a WooCommerce Admin page.
 */
function wc_admin_is_registered_page() {
	$controller = PageController::get_instance();
	return $controller->is_registered_page();
}

/**
 * Get breadcrumbs for WooCommerce Admin Page navigation.
 * Passthrough to PageController::get_breadcrumbs().
 *
 * @return array Navigation pieces (breadcrumbs).
 */
function wc_admin_get_breadcrumbs() {
	$controller = PageController::get_instance();
	return $controller->get_breadcrumbs();
}

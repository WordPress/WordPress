<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Ultimate Addons for Visual Composer Support
 *
 * @link http://codecanyon.net/item/ultimate-addons-for-visual-composer/6892199?ref=UpSolution
 */

if ( ! class_exists( 'Ultimate_VC_Addons' ) ) {
	return;
}

defined( 'ULTIMATE_USE_BUILTIN' ) OR define( 'ULTIMATE_USE_BUILTIN', TRUE );
defined( 'ULTIMATE_NO_EDIT_PAGE_NOTICE' ) OR define( 'ULTIMATE_NO_EDIT_PAGE_NOTICE', TRUE );
defined( 'ULTIMATE_NO_PLUGIN_PAGE_NOTICE' ) OR define( 'ULTIMATE_NO_PLUGIN_PAGE_NOTICE', TRUE );
// Removing potentially dangerous functions
if ( ! function_exists( 'bsf_grant_developer_access' ) ) {
	function bsf_grant_developer_access() {
	}
}
if ( ! function_exists( 'bsf_allow_developer_access' ) ) {
	function bsf_allow_developer_access() {
	}
}
if ( ! function_exists( 'bsf_process_developer_login' ) ) {
	function bsf_process_developer_login() {
	}
}
if ( ! function_exists( 'bsf_notices' ) ) {
	function bsf_notices() {
	}
}
add_action( 'init', 'us_sanitize_ultimate_addons', 20 );
function us_sanitize_ultimate_addons() {
	remove_action( 'admin_init', 'bsf_update_all_product_version', 1000 );
	remove_action( 'admin_notices', 'bsf_notices', 1000 );
	remove_action( 'network_admin_notices', 'bsf_notices', 1000 );
	remove_action( 'admin_footer', 'bsf_update_counter', 999 );
	remove_action( 'wp_ajax_bsf_update_client_license', 'bsf_server_update_client_license' );
	remove_action( 'wp_ajax_nopriv_bsf_update_client_license', 'bsf_server_update_client_license' );
}

// Disabling after-activation redirect to Ultimate Addons Dashboard
if ( get_option( 'ultimate_vc_addons_redirect' ) == TRUE ) {
	update_option( 'ultimate_vc_addons_redirect', FALSE );
}

add_action( 'admin_init', 'us_ultimate_addons_for_vc_integration' );
function us_ultimate_addons_for_vc_integration() {
	if ( get_option( 'ultimate_updater' ) != 'disabled' ) {
		update_option( 'ultimate_updater', 'disabled' );
	}
}
add_action( 'core_upgrade_preamble', 'us_ultimate_addons_core_upgrade_preamble' );
function us_ultimate_addons_core_upgrade_preamble(){
	remove_action( 'core_upgrade_preamble', 'list_bsf_products_updates', 999 );
}

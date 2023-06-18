<?php
/**
 * Load scripts for the admin area.
 *
 * @package WPCode
 */

add_action( 'admin_enqueue_scripts', 'wpcode_admin_scripts' );
add_filter( 'admin_body_class', 'wpcode_admin_body_class' );

/**
 * Load admin scripts here.
 *
 * @return void
 */
function wpcode_admin_scripts() {

	$current_screen = get_current_screen();

	if ( ! isset( $current_screen->id ) || false === strpos( $current_screen->id, 'wpcode' ) ) {
		return;
	}

	$admin_asset_file = WPCODE_PLUGIN_PATH . 'build/admin.asset.php';

	if ( ! file_exists( $admin_asset_file ) ) {
		return;
	}

	$asset = include_once $admin_asset_file;

	wp_enqueue_style( 'wpcode-admin-css', WPCODE_PLUGIN_URL . 'build/admin.css', null, $asset['version'] );

	wp_enqueue_script( 'wpcode-admin-js', WPCODE_PLUGIN_URL . 'build/admin.js', $asset['dependencies'], $asset['version'], true );

	wp_localize_script(
		'wpcode-admin-js',
		'wpcode',
		apply_filters(
			'wpcode_admin_js_data',
			array(
				'nonce'             => wp_create_nonce( 'wpcode_admin' ),
				'code_type_options' => wpcode()->execute->get_code_type_options(),
				'please_wait'       => __( 'Please wait.', 'insert-headers-and-footers' ),
				'ok'                => __( 'OK', 'insert-headers-and-footers' ),
			)
		)
	);

	// Dequeue debug bar console styles on WPCode pages.
	wp_dequeue_style( 'debug-bar-codemirror' );
	wp_dequeue_style( 'debug-bar-console' );
}

/**
 * Scripts needed outside the WPCode admin area (e.g. metabox).
 *
 * @param string $version The version of the scripts to load. Default is 'lite'.
 *
 * @return void
 */
function wpcode_admin_scripts_global( $version = 'lite' ) {

	// Load "globally" but still only on certain screens.
	$current_screen = get_current_screen();

	$dont_load = ! isset( $current_screen->base ) || 'post' !== $current_screen->base;

	// Allow other plugins to modify the screens where the global scripts are loaded.
	if ( apply_filters( 'wpcode_load_global_scripts', $dont_load ) ) {
		return;
	}

	$admin_asset_file = WPCODE_PLUGIN_PATH . "build/admin-global-{$version}.asset.php";

	if ( ! file_exists( $admin_asset_file ) ) {
		return;
	}

	$asset = include_once $admin_asset_file;

	wp_enqueue_style( 'wpcode-admin-global-css', WPCODE_PLUGIN_URL . "build/admin-global-{$version}.css", null, $asset['version'] );

	wp_enqueue_script( 'wpcode-admin-global-js', WPCODE_PLUGIN_URL . "build/admin-global-{$version}.js", $asset['dependencies'], $asset['version'], true );
}

/**
 * Add stable body class that doesn't change with the translation.
 *
 * @param string $classes The body classes.
 *
 * @return string
 */
function wpcode_admin_body_class( $classes ) {

	$page_parent = get_admin_page_parent();

	if ( 'wpcode' === $page_parent ) {
		$classes .= ' wpcode-admin-page';
	}

	return $classes;
}


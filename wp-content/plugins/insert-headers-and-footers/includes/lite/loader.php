<?php
/**
 * File used for importing lite-only files.
 *
 * @package WPCode
 */

if ( is_admin() || defined( 'DOING_CRON' ) && DOING_CRON ) {
	// Class used for loading the scripts metabox.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/admin/class-wpcode-metabox-snippets-lite.php';
	// Load lite-specific scripts.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/admin/admin-scripts.php';
	// Load lite notices.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/admin/notices.php';
	// Lite-specific admin page loader.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/admin/class-wpcode-admin-page-loader-lite.php';
	// Connect to upgrade.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/admin/class-wpcode-connect.php';
	// Usage tracking abstract.
	require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-usage-tracking.php';
	// Usage tracking lite.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/admin/class-wpcode-usage-tracking-lite.php';
	// Load smart tags class.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/class-wpcode-smart-tags-lite.php';
}

add_action( 'plugins_loaded', 'wpcode_plugins_loaded_load_lite_files', 2 );

/**
 * Require files on plugins_loaded.
 *
 * @return void
 */
function wpcode_plugins_loaded_load_lite_files() {
	// Load WooCommerce auto-insert locations.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/auto-insert/class-wpcode-auto-insert-woocommerce.php';
	// Load EDD auto-insert locations.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/auto-insert/class-wpcode-auto-insert-edd.php';
	// Load MemberPress auto-insert locations.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/auto-insert/class-wpcode-auto-insert-memberpress.php';
	// Load the insert-anywhere class.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/auto-insert/class-wpcode-auto-insert-anywhere.php';
	// Load Device type conditional logic.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/conditional-logic/class-wpcode-conditional-device.php';
	// Load WooCommerce conditional logic.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/conditional-logic/class-wpcode-conditional-woocommerce.php';
	// Load EDD conditional logic.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/conditional-logic/class-wpcode-conditional-edd.php';
	// Load MemberPress conditional logic.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/conditional-logic/class-wpcode-conditional-memberpress.php';
	// Load Schedule conditional logic.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/conditional-logic/class-wpcode-conditional-schedule.php';
}

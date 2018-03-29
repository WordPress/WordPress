<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for insert-pages
 * @since 0.7 (builder version)
 * @link https://wordpress.org/plugins/insert-pages/
 */
class ET_Builder_Plugin_Compat_Insert_Pages extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	function __construct() {
		$this->plugin_id = "insert-pages/insert-pages.php";
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 * Note: once this issue is fixed in future version, run version_compare() to limit the scope of the hooked fix
	 * Latest plugin version: 2.7.2
	 * @return void
	 */
	function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		// Up to: latest theme version
		add_action( 'admin_init', array( $this, 'disable_mce_buttons_on_builder' ), 1 );
	}

	/**
	 * insert-pages' tinyMCE button causes sub-module's tinyMCE editor to be empty when being opened.
	 * This might damage user's content. Since there's no hook to filter or modify insert-pages' js events,
	 * it'd be safer to deregister insert-pages' tinyMCE button
	 *
	 * @return void
	 */
	function disable_mce_buttons_on_builder() {
		global $insertPages_plugin;

		if ( is_null( $insertPages_plugin ) ) {
			return;
		}

		remove_action( 'admin_head', array( $insertPages_plugin, 'insertPages_admin_init' ), 1 );
	}
}
new ET_Builder_Plugin_Compat_Insert_Pages;

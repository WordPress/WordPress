<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for siteorigin-panels
 * @since 0.7 (builder version)
 * @link https://wordpress.org/plugins/siteorigin-panels/
 */
class ET_Builder_Plugin_Compat_Siteorigin_Panels extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	function __construct() {
		$this->plugin_id = "siteorigin-panels/siteorigin-panels.php";
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 * Note: once this issue is fixed in future version, run version_compare() to limit the scope of the hooked fix
	 * Latest plugin version: 2.4.21
	 * @return void
	 */
	private function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		// Up to: latest theme version
		add_action( 'siteorigin_panels_filter_content_enabled', array( $this, 'disable_siteorigin_builder_content' ) );
	}

	/**
	 * If Divi Builder is used, disable siteorigin builder content alteration
	 * @return bool
	 */
	function disable_siteorigin_builder_content( $status ) {
		global $post;

		if( isset( $post->ID ) && et_pb_is_pagebuilder_used( $post->ID ) ) {
			$status = false;

			// Remove Site Origin Builder's Live Editor Admin Menu if builder active on current page
			remove_action( 'admin_bar_menu', 'siteorigin_panels_live_edit_link', 100 );
		}

		return $status;
	}
}
new ET_Builder_Plugin_Compat_Siteorigin_Panels;

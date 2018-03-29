<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Base class for plugin compatibility file
 * @since 0.7 (builder version)
 */
class ET_Builder_Plugin_Compat_Base {
	public $plugin_id;

	/**
	 * Get plugin dir path based on plugin_id
	 * @return sting
	 */
	function get_plugin_dir_path() {
		return WP_PLUGIN_DIR . '/' . $this->plugin_id;
	}

	/**
	 * Get plugin data based on initialized plugin_id
	 * @return array
	 */
	function get_plugin_data() {
		return get_plugin_data( $this->get_plugin_dir_path(), false );
	}

	/**
	 * Get plugin version based on initialized plugin_id
	 * @return string
	 */
	function get_plugin_version() {
		$plugin_data = $this->get_plugin_data();

		if ( ! isset( $plugin_data['Version'] ) ) {
			return false;
		}

		return $plugin_data['Version'];
	}
}

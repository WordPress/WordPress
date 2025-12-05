<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Tracking
 */

/**
 * Represents the plugin data.
 */
class WPSEO_Tracking_Plugin_Data implements WPSEO_Collection {

	/**
	 * Plugins with auto updating enabled.
	 *
	 * @var array
	 */
	private $auto_update_plugin_list;

	/**
	 * Returns the collection data.
	 *
	 * @return array The collection data.
	 */
	public function get() {
		return [
			'plugins' => $this->get_plugin_data(),
		];
	}

	/**
	 * Returns all plugins.
	 *
	 * @return array The formatted plugins.
	 */
	protected function get_plugin_data() {

		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins = wp_get_active_and_valid_plugins();

		$plugins = array_map( 'get_plugin_data', $plugins );
		$this->set_auto_update_plugin_list();
		$plugins = array_map( [ $this, 'format_plugin' ], $plugins );

		$plugin_data = [];
		foreach ( $plugins as $plugin ) {
			$plugin_key                 = sanitize_title( $plugin['name'] );
			$plugin_data[ $plugin_key ] = $plugin;
		}

		return $plugin_data;
	}

	/**
	 * Sets all auto updating plugin data so it can be used in the tracking list.
	 *
	 * @return void
	 */
	public function set_auto_update_plugin_list() {

		$auto_update_plugins      = [];
		$auto_update_plugin_files = get_option( 'auto_update_plugins' );
		if ( $auto_update_plugin_files ) {
			foreach ( $auto_update_plugin_files as $auto_update_plugin ) {
				$data                                 = get_plugin_data( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $auto_update_plugin );
				$auto_update_plugins[ $data['Name'] ] = $data;
			}
		}

		$this->auto_update_plugin_list = $auto_update_plugins;
	}

	/**
	 * Formats the plugin array.
	 *
	 * @param array $plugin The plugin details.
	 *
	 * @return array The formatted array.
	 */
	protected function format_plugin( array $plugin ) {

		return [
			'name'          => $plugin['Name'],
			'version'       => $plugin['Version'],
			'auto_updating' => array_key_exists( $plugin['Name'], $this->auto_update_plugin_list ),
		];
	}
}

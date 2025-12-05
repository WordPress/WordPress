<?php

namespace Yoast\WP\SEO\Services\Importing;

use Yoast\WP\SEO\Config\Conflicting_Plugins;

/**
 * Detects plugin conflicts.
 */
class Conflicting_Plugins_Service {

	/**
	 * Detects the conflicting plugins.
	 *
	 * @return array A list of all active conflicting plugins.
	 */
	public function detect_conflicting_plugins() {
		$all_active_plugins = $this->get_active_plugins();

		// Search for active plugins.
		return $this->get_active_conflicting_plugins( $all_active_plugins );
	}

	/**
	 * Deactivates the specified plugin(s) if any, or the entire list of known conflicting plugins.
	 *
	 * @param string|array|false $plugins Optional. The plugin filename, or array of plugin filenames, to deactivate.
	 *
	 * @return void
	 */
	public function deactivate_conflicting_plugins( $plugins = false ) {
		// If no plugins are specified, deactivate any known conflicting plugins that are active.
		if ( ! $plugins ) {
			$plugins = $this->detect_conflicting_plugins();
		}

		// In case of a single plugin, wrap it in an array.
		if ( \is_string( $plugins ) ) {
			$plugins = [ $plugins ];
		}

		if ( ! \is_array( $plugins ) ) {
			return;
		}

		// Deactivate all specified plugins across the network, while retaining their deactivation hook.
		\deactivate_plugins( $plugins );
	}

	/**
	 * Loop through the list of known conflicting plugins to check if one of the plugins is active.
	 *
	 * @param array $all_active_plugins All plugins loaded by WordPress.
	 *
	 * @return array The array of activated conflicting plugins.
	 */
	protected function get_active_conflicting_plugins( $all_active_plugins ) {
		$active_conflicting_plugins = [];

		foreach ( Conflicting_Plugins::all_plugins() as $plugin ) {
			if ( \in_array( $plugin, $all_active_plugins, true ) ) {
				$active_conflicting_plugins[] = $plugin;
			}
		}

		return $active_conflicting_plugins;
	}

	/**
	 * Get a list of all plugins active in the current WordPress instance.
	 *
	 * @return array|false The names of all active plugins.
	 */
	protected function get_active_plugins() {
		// Request a list of active plugins from WordPress.
		$all_active_plugins = \get_option( 'active_plugins' );

		return $this->ignore_deactivating_plugin( $all_active_plugins );
	}

	/**
	 * While deactivating a plugin, we should ignore the plugin currently being deactivated.
	 *
	 * @param array $all_active_plugins All plugins currently loaded by WordPress.
	 *
	 * @return array The remaining active plugins.
	 */
	protected function ignore_deactivating_plugin( $all_active_plugins ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are strictly comparing only.
		if ( isset( $_GET['action'] ) && isset( $_GET['plugin'] ) && \is_string( $_GET['action'] ) && \is_string( $_GET['plugin'] ) && \wp_unslash( $_GET['action'] ) === 'deactivate' ) {
			$deactivated_plugin = \sanitize_text_field( \wp_unslash( $_GET['plugin'] ) );

			\check_admin_referer( 'deactivate-plugin_' . $deactivated_plugin );

			$key_to_remove = \array_search( $deactivated_plugin, $all_active_plugins, true );
			if ( $key_to_remove !== false ) {
				unset( $all_active_plugins[ $key_to_remove ] );
			}
		}

		return $all_active_plugins;
	}
}

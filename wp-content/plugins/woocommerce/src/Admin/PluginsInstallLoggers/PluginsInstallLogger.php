<?php

namespace Automattic\WooCommerce\Admin\PluginsInstallLoggers;

/**
 * A logger used in PluginsHelper::install_plugins to log the installation progress.
 */
interface PluginsInstallLogger {

	/**
	 * Called when a plugin install requested.
	 *
	 * @param string $plugin_name plugin name.
	 * @return mixed
	 */
	public function install_requested( string $plugin_name );

	/**
	 * Called when a plugin installed successfully.
	 *
	 * @param string $plugin_name plugin name.
	 * @param int    $duration # of seconds it took to install $plugin_name.
	 * @return mixed
	 */
	public function installed( string $plugin_name, int $duration);

	/**
	 * Called when an error occurred while installing a plugin.
	 *
	 * @param string      $plugin_name plugin name.
	 * @param string|null $error_message error message.
	 * @return mixed
	 */
	public function add_error( string $plugin_name, string $error_message = null);

	/**
	 * Called when all plugins are processed.
	 *
	 * @return mixed
	 */
	public function complete();
}


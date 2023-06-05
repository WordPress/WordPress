<?php
/**
 * Interface for a provider for getting access to plugin queries,
 * designed to be mockable for unit tests.
 */

namespace Automattic\WooCommerce\Admin\PluginsProvider;

defined( 'ABSPATH' ) || exit;

/**
 * Plugins Provider Interface
 */
interface PluginsProviderInterface {
	/**
	 * Get an array of active plugin slugs.
	 *
	 * @return array
	 */
	public function get_active_plugin_slugs();

	/**
	 * Get plugin data.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory or the plugin directory name.
	 *
	 * @return array|false
	 */
	public function get_plugin_data( $plugin );

	/**
	 * Get the path to the plugin file relative to the plugins directory from the plugin slug.
	 *
	 * E.g. 'woocommerce' returns 'woocommerce/woocommerce.php'
	 *
	 * @param string $slug Plugin slug to get path for.
	 *
	 * @return string|false
	 */
	public function get_plugin_path_from_slug( $slug );
}

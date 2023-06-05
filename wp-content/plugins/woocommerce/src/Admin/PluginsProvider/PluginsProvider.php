<?php
/**
 * A provider for getting access to plugin queries.
 */

namespace Automattic\WooCommerce\Admin\PluginsProvider;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\PluginsProvider\PluginsProviderInterface;
use Automattic\WooCommerce\Admin\PluginsHelper;

/**
 * Plugins Provider.
 *
 * Uses the live PluginsHelper.
 */
class PluginsProvider implements PluginsProviderInterface {
	/**
	 * The deactivated plugin slug.
	 *
	 * @var string
	 */
	private static $deactivated_plugin_slug = '';

	/**
	 * Get an array of active plugin slugs.
	 *
	 * @return array
	 */
	public function get_active_plugin_slugs() {
		return array_filter(
			PluginsHelper::get_active_plugin_slugs(),
			function( $p ) {
				return $p !== self::$deactivated_plugin_slug;
			}
		);
	}

	/**
	 * Set the deactivated plugin. This is needed because the deactivated_plugin
	 * hook happens before the option is updated which means that getting the
	 * active plugins includes the deactivated plugin.
	 *
	 * @param string $plugin_path The path to the plugin being deactivated.
	 */
	public static function set_deactivated_plugin( $plugin_path ) {
		self::$deactivated_plugin_slug = explode( '/', $plugin_path )[0];
	}

	/**
	 * Get plugin data.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory or the plugin directory name.
	 *
	 * @return array|false
	 */
	public function get_plugin_data( $plugin ) {
		return PluginsHelper::get_plugin_data( $plugin );
	}

	/**
	 * Get the path to the plugin file relative to the plugins directory from the plugin slug.
	 *
	 * E.g. 'woocommerce' returns 'woocommerce/woocommerce.php'
	 *
	 * @param string $slug Plugin slug to get path for.
	 *
	 * @return string|false
	 */
	public function get_plugin_path_from_slug( $slug ) {
		return PluginsHelper::get_plugin_path_from_slug( $slug );
	}
}

<?php
/**
 * A class of utilities for dealing with plugins.
 */

namespace Automattic\WooCommerce\Utilities;

use Automattic\WooCommerce\Internal\Traits\AccessiblePrivateMethods;
use Automattic\WooCommerce\Proxies\LegacyProxy;

/**
 * A class of utilities for dealing with plugins.
 */
class PluginUtil {

	use AccessiblePrivateMethods;

	/**
	 * The LegacyProxy instance to use.
	 *
	 * @var LegacyProxy
	 */
	private $proxy;

	/**
	 * The cached list of WooCommerce aware plugin ids.
	 *
	 * @var null|array
	 */
	private $woocommerce_aware_plugins = null;

	/**
	 * The cached list of enabled WooCommerce aware plugin ids.
	 *
	 * @var null|array
	 */
	private $woocommerce_aware_active_plugins = null;

	/**
	 * Creates a new instance of the class.
	 */
	public function __construct() {
		self::add_action( 'activated_plugin', array( $this, 'handle_plugin_de_activation' ), 10, 0 );
		self::add_action( 'deactivated_plugin', array( $this, 'handle_plugin_de_activation' ), 10, 0 );
	}

	/**
	 * Initialize the class instance.
	 *
	 * @internal
	 *
	 * @param LegacyProxy $proxy The instance of LegacyProxy to use.
	 */
	final public function init( LegacyProxy $proxy ) {
		$this->proxy = $proxy;
		require_once ABSPATH . WPINC . '/plugin.php';
	}

	/**
	 * Get a list with the names of the WordPress plugins that are WooCommerce aware
	 * (they have a "WC tested up to" header).
	 *
	 * @param bool $active_only True to return only active plugins, false to return all the active plugins.
	 * @return string[] A list of plugin ids (path/file.php).
	 */
	public function get_woocommerce_aware_plugins( bool $active_only = false ): array {
		if ( is_null( $this->woocommerce_aware_plugins ) ) {
			$all_plugins = $this->proxy->call_function( 'get_plugins' );

			$this->woocommerce_aware_plugins =
				array_keys(
					array_filter(
						$all_plugins,
						array( $this, 'is_woocommerce_aware_plugin' )
					)
				);

			$this->woocommerce_aware_active_plugins =
				array_values(
					array_filter(
						$this->woocommerce_aware_plugins,
						function ( $plugin_name ) {
							return $this->proxy->call_function( 'is_plugin_active', $plugin_name );
						}
					)
				);
		}

		return $active_only ? $this->woocommerce_aware_active_plugins : $this->woocommerce_aware_plugins;
	}

	/**
	 * Get the printable name of a plugin.
	 *
	 * @param string $plugin_id Plugin id (path/file.php).
	 * @return string Printable plugin name, or the plugin id itself if printable name is not available.
	 */
	public function get_plugin_name( string $plugin_id ): string {
		$plugin_data = $this->proxy->call_function( 'get_plugin_data', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin_id );
		return $plugin_data['Name'] ?? $plugin_id;
	}

	/**
	 * Check if a plugin is WooCommerce aware.
	 *
	 * @param string|array $plugin_file_or_data Plugin id (path/file.php) or plugin data (as returned by get_plugins).
	 * @return bool True if the plugin exists and is WooCommerce aware.
	 * @throws \Exception The input is neither a string nor an array.
	 */
	public function is_woocommerce_aware_plugin( $plugin_file_or_data ): bool {
		if ( is_string( $plugin_file_or_data ) ) {
			return in_array( $plugin_file_or_data, $this->get_woocommerce_aware_plugins(), true );
		} elseif ( is_array( $plugin_file_or_data ) ) {
			return '' !== ( $plugin_file_or_data['WC tested up to'] ?? '' );
		} else {
			throw new \Exception( 'is_woocommerce_aware_plugin requires a plugin name or an array of plugin data as input' );
		}
	}

	/**
	 * Match plugin identifier passed as a parameter with the output from `get_plugins()`.
	 *
	 * @param string $plugin_file Plugin identifier, either 'my-plugin/my-plugin.php', or output from __FILE__.
	 *
	 * @return string|false Key from the array returned by `get_plugins` if matched. False if no match.
	 */
	public function get_wp_plugin_id( $plugin_file ) {
		$wp_plugins = array_keys( $this->proxy->call_function( 'get_plugins' ) );

		// Try to match plugin_basename().
		$plugin_basename = $this->proxy->call_function( 'plugin_basename', $plugin_file );
		if ( in_array( $plugin_basename, $wp_plugins, true ) ) {
			return $plugin_basename;
		}

		// Try to match by the my-file/my-file.php (dir + file name), then by my-file.php (file name only).
		$plugin_file     = str_replace( array( '\\', '/' ), DIRECTORY_SEPARATOR, $plugin_file );
		$file_name_parts = explode( DIRECTORY_SEPARATOR, $plugin_file );
		$file_name       = array_pop( $file_name_parts );
		$directory_name  = array_pop( $file_name_parts );
		$full_matches    = array();
		$partial_matches = array();
		foreach ( $wp_plugins as $wp_plugin ) {
			if ( false !== strpos( $wp_plugin, $directory_name . DIRECTORY_SEPARATOR . $file_name ) ) {
				$full_matches[] = $wp_plugin;
			}

			if ( false !== strpos( $wp_plugin, $file_name ) ) {
				$partial_matches[] = $wp_plugin;
			}
		}

		if ( 1 === count( $full_matches ) ) {
			return $full_matches[0];
		}

		if ( 1 === count( $partial_matches ) ) {
			return $partial_matches[0];
		}

		return false;
	}

	/**
	 * Handle plugin activation and deactivation by clearing the WooCommerce aware plugin ids cache.
	 */
	private function handle_plugin_de_activation(): void {
		$this->woocommerce_aware_plugins        = null;
		$this->woocommerce_aware_active_plugins = null;
	}
}

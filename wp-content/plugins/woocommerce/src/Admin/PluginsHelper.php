<?php
/**
 * PluginsHelper
 *
 * Helper class for the site's plugins.
 */

namespace Automattic\WooCommerce\Admin;

use ActionScheduler;
use ActionScheduler_DBStore;
use ActionScheduler_QueueRunner;
use Automatic_Upgrader_Skin;
use Automattic\WooCommerce\Admin\PluginsInstallLoggers\AsyncPluginsInstallLogger;
use Automattic\WooCommerce\Admin\PluginsInstallLoggers\PluginsInstallLogger;
use Plugin_Upgrader;
use WP_Error;
use WP_Upgrader;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Class PluginsHelper
 */
class PluginsHelper {

	/**
	 * Initialize hooks.
	 */
	public static function init() {
		add_action( 'woocommerce_plugins_install_callback', array( __CLASS__, 'install_plugins' ), 10, 2 );
		add_action( 'woocommerce_plugins_install_async_callback', array( __CLASS__, 'install_plugins_async_callback' ), 10, 2 );
		add_action( 'woocommerce_plugins_activate_callback', array( __CLASS__, 'activate_plugins' ), 10, 2 );
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
	public static function get_plugin_path_from_slug( $slug ) {
		$plugins = get_plugins();

		if ( strstr( $slug, '/' ) ) {
			// The slug is already a plugin path.
			return $slug;
		}

		foreach ( $plugins as $plugin_path => $data ) {
			$path_parts = explode( '/', $plugin_path );
			if ( $path_parts[0] === $slug ) {
				return $plugin_path;
			}
		}

		return false;
	}

	/**
	 * Get an array of installed plugin slugs.
	 *
	 * @return array
	 */
	public static function get_installed_plugin_slugs() {
		return array_map(
			function ( $plugin_path ) {
				$path_parts = explode( '/', $plugin_path );

				return $path_parts[0];
			},
			array_keys( get_plugins() )
		);
	}

	/**
	 * Get an array of installed plugins with their file paths as a key value pair.
	 *
	 * @return array
	 */
	public static function get_installed_plugins_paths() {
		$plugins           = get_plugins();
		$installed_plugins = array();

		foreach ( $plugins as $path => $plugin ) {
			$path_parts                 = explode( '/', $path );
			$slug                       = $path_parts[0];
			$installed_plugins[ $slug ] = $path;
		}

		return $installed_plugins;
	}

	/**
	 * Get an array of active plugin slugs.
	 *
	 * @return array
	 */
	public static function get_active_plugin_slugs() {
		return array_map(
			function ( $plugin_path ) {
				$path_parts = explode( '/', $plugin_path );

				return $path_parts[0];
			},
			get_option( 'active_plugins', array() )
		);
	}

	/**
	 * Checks if a plugin is installed.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory or the plugin directory name.
	 *
	 * @return bool
	 */
	public static function is_plugin_installed( $plugin ) {
		$plugin_path = self::get_plugin_path_from_slug( $plugin );

		return $plugin_path ? array_key_exists( $plugin_path, get_plugins() ) : false;
	}

	/**
	 * Checks if a plugin is active.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory or the plugin directory name.
	 *
	 * @return bool
	 */
	public static function is_plugin_active( $plugin ) {
		$plugin_path = self::get_plugin_path_from_slug( $plugin );

		return $plugin_path ? in_array( $plugin_path, get_option( 'active_plugins', array() ), true ) : false;
	}

	/**
	 * Get plugin data.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory or the plugin directory name.
	 *
	 * @return array|false
	 */
	public static function get_plugin_data( $plugin ) {
		$plugin_path = self::get_plugin_path_from_slug( $plugin );
		$plugins     = get_plugins();

		return isset( $plugins[ $plugin_path ] ) ? $plugins[ $plugin_path ] : false;
	}

	/**
	 * Install an array of plugins.
	 *
	 * @param array                     $plugins Plugins to install.
	 * @param PluginsInstallLogger|null $logger an optional logger.
	 *
	 * @return array
	 */
	public static function install_plugins( $plugins, PluginsInstallLogger $logger = null ) {
		/**
		 * Filter the list of plugins to install.
		 *
		 * @param array $plugins A list of the plugins to install.
		 *
		 * @since 6.4.0
		 */
		$plugins = apply_filters( 'woocommerce_admin_plugins_pre_install', $plugins );

		if ( empty( $plugins ) || ! is_array( $plugins ) ) {
			return new WP_Error(
				'woocommerce_plugins_invalid_plugins',
				__( 'Plugins must be a non-empty array.', 'woocommerce' )
			);
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		include_once ABSPATH . '/wp-admin/includes/admin.php';
		include_once ABSPATH . '/wp-admin/includes/plugin-install.php';
		include_once ABSPATH . '/wp-admin/includes/plugin.php';
		include_once ABSPATH . '/wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . '/wp-admin/includes/class-plugin-upgrader.php';

		$existing_plugins  = self::get_installed_plugins_paths();
		$installed_plugins = array();
		$results           = array();
		$time              = array();
		$errors            = new WP_Error();

		foreach ( $plugins as $plugin ) {
			$slug = sanitize_key( $plugin );
			$logger && $logger->install_requested( $plugin );

			if ( isset( $existing_plugins[ $slug ] ) ) {
				$installed_plugins[] = $plugin;
				$logger && $logger->installed( $plugin, 0 );
				continue;
			}

			$start_time = microtime( true );

			$api = plugins_api(
				'plugin_information',
				array(
					'slug'   => $slug,
					'fields' => array(
						'sections' => false,
					),
				)
			);

			if ( is_wp_error( $api ) ) {
				$properties = array(
					'error_message'     => sprintf(
						// translators: %s: plugin slug (example: woocommerce-services).
						__(
							'The requested plugin `%s` could not be installed. Plugin API call failed.',
							'woocommerce'
						),
						$slug
					),
					'api_error_message' => $api->get_error_message(),
					'slug'              => $slug,
				);
				wc_admin_record_tracks_event( 'install_plugin_error', $properties );

				/**
				 * Action triggered when a plugin API call failed.
				 *
				 * @param string $slug The plugin slug.
				 * @param WP_Error $api The API response.
				 *
				 * @since 6.4.0
				 */
				do_action( 'woocommerce_plugins_install_api_error', $slug, $api );

				$error_message = sprintf(
				/* translators: %s: plugin slug (example: woocommerce-services) */
					__( 'The requested plugin `%s` could not be installed. Plugin API call failed.', 'woocommerce' ),
					$slug
				);

				$errors->add( $plugin, $error_message );
				$logger && $logger->add_error( $plugin, $error_message );

				continue;
			}

			$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
			$result   = $upgrader->install( $api->download_link );
			// result can be false or WP_Error.
			$results[ $plugin ] = $result;
			$time[ $plugin ]    = round( ( microtime( true ) - $start_time ) * 1000 );

			if ( is_wp_error( $result ) || is_null( $result ) ) {
				$properties = array(
					'error_message'         => sprintf(
						/* translators: %s: plugin slug (example: woocommerce-services) */
						__(
							'The requested plugin `%s` could not be installed.',
							'woocommerce'
						),
						$slug
					),
					'slug'                  => $slug,
					'api_version'           => $api->version,
					'api_download_link'     => $api->download_link,
					'upgrader_skin_message' => implode( ',', $upgrader->skin->get_upgrade_messages() ),
					'result'                => is_wp_error( $result ) ? $result->get_error_message() : 'null',
				);
				wc_admin_record_tracks_event( 'install_plugin_error', $properties );

				/**
				 * Action triggered when a plugin installation fails.
				 *
				 * @param string $slug The plugin slug.
				 * @param object $api The plugin API object.
				 * @param WP_Error|null $result The result of the plugin installation.
				 * @param Plugin_Upgrader $upgrader The plugin upgrader.
				 *
				 * @since 6.4.0
				 */
				do_action( 'woocommerce_plugins_install_error', $slug, $api, $result, $upgrader );

				$install_error_message = sprintf(
				/* translators: %s: plugin slug (example: woocommerce-services) */
					__( 'The requested plugin `%s` could not be installed. Upgrader install failed.', 'woocommerce' ),
					$slug
				);
				$errors->add(
					$plugin,
					$install_error_message
				);
				$logger && $logger->add_error( $plugin, $install_error_message );

				continue;
			}

			$installed_plugins[] = $plugin;
			$logger && $logger->installed( $plugin, $time[ $plugin ] );
		}

		$logger && $logger->complete();

		$data = array(
			'installed' => $installed_plugins,
			'results'   => $results,
			'errors'    => $errors,
			'time'      => $time,
		);

		return $data;
	}

	/**
	 * Callback regsitered by OnboardingPlugins::install_async.
	 *
	 * It is used to call install_plugins with a custom logger.
	 *
	 * @param array  $plugins A list of plugins to install.
	 * @param string $job_id An unique job I.D.
	 * @return bool
	 */
	public function install_plugins_async_callback( array $plugins, string $job_id ) {
		$option_name = 'woocommerce_onboarding_plugins_install_async_' . $job_id;
		$logger      = new AsyncPluginsInstallLogger( $option_name );
		self::install_plugins( $plugins, $logger );
		return true;
	}

	/**
	 * Schedule plugin installation.
	 *
	 * @param array $plugins Plugins to install.
	 *
	 * @return string Job ID.
	 */
	public static function schedule_install_plugins( $plugins ) {
		if ( empty( $plugins ) || ! is_array( $plugins ) ) {
			return new WP_Error(
				'woocommerce_plugins_invalid_plugins',
				__( 'Plugins must be a non-empty array.', 'woocommerce' ),
				404
			);
		}

		$job_id = uniqid();
		WC()->queue()->schedule_single( time() + 5, 'woocommerce_plugins_install_callback', array( $plugins ) );

		return $job_id;
	}

	/**
	 * Activate the requested plugins.
	 *
	 * @param array $plugins Plugins.
	 *
	 * @return WP_Error|array Plugin Status
	 */
	public static function activate_plugins( $plugins ) {
		if ( empty( $plugins ) || ! is_array( $plugins ) ) {
			return new WP_Error(
				'woocommerce_plugins_invalid_plugins',
				__( 'Plugins must be a non-empty array.', 'woocommerce' ),
				404
			);
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		// the mollie-payments-for-woocommerce plugin calls `WP_Filesystem()` during it's activation hook, which crashes without this include.
		require_once ABSPATH . 'wp-admin/includes/file.php';

		/**
		 * Filter the list of plugins to activate.
		 *
		 * @param array $plugins A list of the plugins to activate.
		 *
		 * @since 6.4.0
		 */
		$plugins = apply_filters( 'woocommerce_admin_plugins_pre_activate', $plugins );

		$plugin_paths      = self::get_installed_plugins_paths();
		$errors            = new WP_Error();
		$activated_plugins = array();

		foreach ( $plugins as $plugin ) {
			$slug = $plugin;
			$path = isset( $plugin_paths[ $slug ] ) ? $plugin_paths[ $slug ] : false;

			if ( ! $path ) {
				$errors->add(
					$plugin,
					/* translators: %s: plugin slug (example: woocommerce-services) */
					sprintf( __( 'The requested plugin `%s`. is not yet installed.', 'woocommerce' ), $slug )
				);
				continue;
			}

			$result = activate_plugin( $path );
			if ( ! is_plugin_active( $path ) ) {
				/**
				 * Action triggered when a plugin activation fails.
				 *
				 * @param string $slug The plugin slug.
				 * @param null|WP_Error $result The result of the plugin activation.
				 *
				 * @since 6.4.0
				 */
				do_action( 'woocommerce_plugins_activate_error', $slug, $result );

				$errors->add(
					$plugin,
					/* translators: %s: plugin slug (example: woocommerce-services) */
					sprintf( __( 'The requested plugin `%s` could not be activated.', 'woocommerce' ), $slug )
				);
				continue;
			}

			$activated_plugins[] = $plugin;
		}

		$data = array(
			'activated' => $activated_plugins,
			'active'    => self::get_active_plugin_slugs(),
			'errors'    => $errors,
		);

		return $data;
	}

	/**
	 * Schedule plugin activation.
	 *
	 * @param array $plugins Plugins to activate.
	 *
	 * @return string Job ID.
	 */
	public static function schedule_activate_plugins( $plugins ) {
		if ( empty( $plugins ) || ! is_array( $plugins ) ) {
			return new WP_Error(
				'woocommerce_plugins_invalid_plugins',
				__( 'Plugins must be a non-empty array.', 'woocommerce' ),
				404
			);
		}

		$job_id = uniqid();
		WC()->queue()->schedule_single(
			time() + 5,
			'woocommerce_plugins_activate_callback',
			array( $plugins, $job_id )
		);

		return $job_id;
	}

	/**
	 * Installation status.
	 *
	 * @param int $job_id Job ID.
	 *
	 * @return array Job data.
	 */
	public static function get_installation_status( $job_id = null ) {
		$actions = WC()->queue()->search(
			array(
				'hook'    => 'woocommerce_plugins_install_callback',
				'search'  => $job_id,
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);

		return self::get_action_data( $actions );
	}

	/**
	 * Gets the plugin data for the first action.
	 *
	 * @param array $actions Array of AS actions.
	 *
	 * @return array Array of action data.
	 */
	public static function get_action_data( $actions ) {
		$data = array();

		foreach ( $actions as $action_id => $action ) {
			$store  = new ActionScheduler_DBStore();
			$args   = $action->get_args();
			$data[] = array(
				'job_id'  => $args[1],
				'plugins' => $args[0],
				'status'  => $store->get_status( $action_id ),
			);
		}

		return $data;
	}

	/**
	 * Activation status.
	 *
	 * @param int $job_id Job ID.
	 *
	 * @return array Array of action data.
	 */
	public static function get_activation_status( $job_id = null ) {
		$actions = WC()->queue()->search(
			array(
				'hook'    => 'woocommerce_plugins_activate_callback',
				'search'  => $job_id,
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);

		return self::get_action_data( $actions );
	}

}

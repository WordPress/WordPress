<?php
/**
 * WooCommerce.com Product Installation.
 *
 * @package WooCommerce\WCCom
 * @since   3.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_WCCOM_Site_Installer Class
 *
 * Contains functionalities to install products via WooCommerce.com helper connection.
 */
class WC_WCCOM_Site_Installer {

	/**
	 * Error message returned install_package if the folder already exists.
	 *
	 * @var string
	 */
	private static $folder_exists = 'folder_exists';

	/**
	 * Default state.
	 *
	 * @var array
	 */
	private static $default_state = array(
		'status'       => 'idle',
		'steps'        => array(),
		'current_step' => null,
	);

	/**
	 * Represents product step state.
	 *
	 * @var array
	 */
	private static $default_step_state = array(
		'download_url'   => '',
		'product_type'   => '',
		'last_step'      => '',
		'last_error'     => '',
		'download_path'  => '',
		'unpacked_path'  => '',
		'installed_path' => '',
		'activate'       => false,
	);

	/**
	 * Product install steps. Each step is a method name in this class that
	 * will be passed with product ID arg \WP_Upgrader instance.
	 *
	 * @var array
	 */
	private static $install_steps = array(
		'get_product_info',
		'download_product',
		'unpack_product',
		'move_product',
		'activate_product',
	);

	/**
	 * An instance of the WP_Upgrader class to be used for installation.
	 *
	 * @var \WP_Upgrader $wp_upgrader
	 */
	private static $wp_upgrader;

	/**
	 * Get the product install state.
	 *
	 * @since 3.7.0
	 * @param string $key Key in state data. If empty key is passed array of
	 *                    state will be returned.
	 * @return array Product install state.
	 */
	public static function get_state( $key = '' ) {
		$state = WC_Helper_Options::get( 'product_install', self::$default_state );
		if ( ! empty( $key ) ) {
			return isset( $state[ $key ] ) ? $state[ $key ] : null;
		}

		return $state;
	}

	/**
	 * Update the product install state.
	 *
	 * @since 3.7.0
	 * @param string $key   Key in state data.
	 * @param mixed  $value Value.
	 */
	public static function update_state( $key, $value ) {
		$state = WC_Helper_Options::get( 'product_install', self::$default_state );

		$state[ $key ] = $value;
		WC_Helper_Options::update( 'product_install', $state );
	}

	/**
	 * Reset product install state.
	 *
	 * @since 3.7.0
	 * @param array $products List of product IDs.
	 */
	public static function reset_state( $products = array() ) {
		WC()->queue()->cancel_all( 'woocommerce_wccom_install_products' );
		WC_Helper_Options::update( 'product_install', self::$default_state );
	}

	/**
	 * Schedule installing given list of products.
	 *
	 * @since 3.7.0
	 * @param array $products Array of products where key is product ID and
	 *                        element is install args.
	 * @return array State.
	 */
	public static function schedule_install( $products ) {
		$state  = self::get_state();
		$status = ! empty( $state['status'] ) ? $state['status'] : '';
		if ( 'in-progress' === $status ) {
			return $state;
		}
		self::update_state( 'status', 'in-progress' );

		$steps = array_fill_keys( array_keys( $products ), self::$default_step_state );
		self::update_state( 'steps', $steps );

		self::update_state( 'current_step', null );

		$args = array(
			'products' => $products,
		);

		// Clear the cache of customer's subscription before asking for them.
		// Thus, they will be re-fetched from WooCommerce.com after a purchase.
		WC_Helper::_flush_subscriptions_cache();

		WC()->queue()->cancel_all( 'woocommerce_wccom_install_products', $args );
		WC()->queue()->add( 'woocommerce_wccom_install_products', $args );

		return self::get_state();
	}

	/**
	 * Install a given product IDs.
	 *
	 * Run via `woocommerce_wccom_install_products` hook.
	 *
	 * @since 3.7.0
	 * @param array $products Array of products where key is product ID and
	 *                        element is install args.
	 */
	public static function install( $products ) {
		$upgrader = self::get_wp_upgrader();

		foreach ( $products as $product_id => $install_args ) {
			self::install_product( $product_id, $install_args, $upgrader );
		}

		self::finish_installation();
	}

	/**
	 * Finish installation by updating the state.
	 *
	 * @since 3.7.0
	 */
	private static function finish_installation() {
		$state = self::get_state();
		if ( empty( $state['steps'] ) ) {
			return;
		}

		foreach ( $state['steps'] as $step ) {
			if ( ! empty( $step['last_error'] ) ) {
				$state['status'] = 'has_error';
				break;
			}
		}

		if ( 'has_error' !== $state['status'] ) {
			$state['status'] = 'finished';
		}

		WC_Helper_Options::update( 'product_install', $state );
	}

	/**
	 * Install a single product given its ID.
	 *
	 * @since 3.7.0
	 * @param int          $product_id   Product ID.
	 * @param array        $install_args Install args.
	 * @param \WP_Upgrader $upgrader     Core class to handle installation.
	 */
	private static function install_product( $product_id, $install_args, $upgrader ) {
		foreach ( self::$install_steps as $step ) {
			self::do_install_step( $product_id, $install_args, $step, $upgrader );
		}
	}

	/**
	 * Perform product installation step.
	 *
	 * @since 3.7.0
	 * @param int          $product_id   Product ID.
	 * @param array        $install_args Install args.
	 * @param string       $step         Installation step.
	 * @param \WP_Upgrader $upgrader     Core class to handle installation.
	 */
	private static function do_install_step( $product_id, $install_args, $step, $upgrader ) {
		$state_steps = self::get_state( 'steps' );
		if ( empty( $state_steps[ $product_id ] ) ) {
			$state_steps[ $product_id ] = self::$default_step_state;
		}

		if ( ! empty( $state_steps[ $product_id ]['last_error'] ) ) {
			return;
		}

		$state_steps[ $product_id ]['last_step'] = $step;

		if ( ! empty( $install_args['activate'] ) ) {
			$state_steps[ $product_id ]['activate'] = true;
		}

		self::update_state(
			'current_step',
			array(
				'product_id' => $product_id,
				'step'       => $step,
			)
		);

		$result = call_user_func( array( __CLASS__, $step ), $product_id, $upgrader );
		if ( is_wp_error( $result ) ) {
			$state_steps[ $product_id ]['last_error'] = $result->get_error_message();
		} else {
			switch ( $step ) {
				case 'get_product_info':
					$state_steps[ $product_id ]['download_url'] = $result['download_url'];
					$state_steps[ $product_id ]['product_type'] = $result['product_type'];
					$state_steps[ $product_id ]['product_name'] = $result['product_name'];
					break;
				case 'download_product':
					$state_steps[ $product_id ]['download_path'] = $result;
					break;
				case 'unpack_product':
					$state_steps[ $product_id ]['unpacked_path'] = $result;
					break;
				case 'move_product':
					$state_steps[ $product_id ]['installed_path'] = $result['destination'];
					if ( isset( $result[ self::$folder_exists ] ) ) {
						$state_steps[ $product_id ]['warning'] = array(
							'message'     => self::$folder_exists,
							'plugin_info' => self::get_plugin_info( $state_steps[ $product_id ]['installed_path'] ),
						);
					}
					break;
			}
		}

		self::update_state( 'steps', $state_steps );
	}

	/**
	 * Get product info from its ID.
	 *
	 * @since 3.7.0
	 * @param int $product_id Product ID.
	 * @return array|\WP_Error
	 */
	private static function get_product_info( $product_id ) {
		$product_info = array(
			'download_url' => '',
			'product_type' => '',
		);

		// Get product info from woocommerce.com.
		$request = WC_Helper_API::get(
			add_query_arg(
				array( 'product_id' => absint( $product_id ) ),
				'info'
			),
			array(
				'authenticated' => true,
			)
		);

		if ( 200 !== wp_remote_retrieve_response_code( $request ) ) {
			return new WP_Error( 'product_info_failed', __( 'Failed to retrieve product info from woocommerce.com', 'woocommerce' ) );
		}

		$result = json_decode( wp_remote_retrieve_body( $request ), true );

		$product_info['product_type'] = $result['_product_type'];
		$product_info['product_name'] = $result['name'];

		if ( ! empty( $result['_wporg_product'] ) && ! empty( $result['download_link'] ) ) {
			// For wporg product, download is set already from info response.
			$product_info['download_url'] = $result['download_link'];
		} elseif ( ! WC_Helper::has_product_subscription( $product_id ) ) {
			// Non-wporg product needs subscription.
			return new WP_Error( 'missing_subscription', __( 'Missing product subscription', 'woocommerce' ) );
		} else {
			// Retrieve download URL for non-wporg product.
			WC_Helper_Updater::flush_updates_cache();
			$updates = WC_Helper_Updater::get_update_data();
			if ( empty( $updates[ $product_id ]['package'] ) ) {
				return new WP_Error( 'missing_product_package', __( 'Could not find product package.', 'woocommerce' ) );
			}

			$product_info['download_url'] = $updates[ $product_id ]['package'];
		}

		return $product_info;
	}

	/**
	 * Download product by its ID and returns the path of the zip package.
	 *
	 * @since 3.7.0
	 * @param int          $product_id Product ID.
	 * @param \WP_Upgrader $upgrader   Core class to handle installation.
	 * @return \WP_Error|string
	 */
	private static function download_product( $product_id, $upgrader ) {
		$steps = self::get_state( 'steps' );
		if ( empty( $steps[ $product_id ]['download_url'] ) ) {
			return new WP_Error( 'missing_download_url', __( 'Could not find download url for the product.', 'woocommerce' ) );
		}
		return $upgrader->download_package( $steps[ $product_id ]['download_url'] );
	}

	/**
	 * Unpack downloaded product.
	 *
	 * @since 3.7.0
	 * @param int          $product_id Product ID.
	 * @param \WP_Upgrader $upgrader   Core class to handle installation.
	 * @return \WP_Error|string
	 */
	private static function unpack_product( $product_id, $upgrader ) {
		$steps = self::get_state( 'steps' );
		if ( empty( $steps[ $product_id ]['download_path'] ) ) {
			return new WP_Error( 'missing_download_path', __( 'Could not find download path.', 'woocommerce' ) );
		}

		return $upgrader->unpack_package( $steps[ $product_id ]['download_path'], true );
	}

	/**
	 * Move product to plugins directory.
	 *
	 * @since 3.7.0
	 * @param int          $product_id Product ID.
	 * @param \WP_Upgrader $upgrader   Core class to handle installation.
	 * @return array|\WP_Error
	 */
	private static function move_product( $product_id, $upgrader ) {
		$steps = self::get_state( 'steps' );
		if ( empty( $steps[ $product_id ]['unpacked_path'] ) ) {
			return new WP_Error( 'missing_unpacked_path', __( 'Could not find unpacked path.', 'woocommerce' ) );
		}

		$destination = 'plugin' === $steps[ $product_id ]['product_type']
			? WP_PLUGIN_DIR
			: get_theme_root();

		$package = array(
			'source'        => $steps[ $product_id ]['unpacked_path'],
			'destination'   => $destination,
			'clear_working' => true,
			'hook_extra'    => array(
				'type'   => $steps[ $product_id ]['product_type'],
				'action' => 'install',
			),
		);

		$result = $upgrader->install_package( $package );

		/**
		 * If install package returns error 'folder_exists' threat as success.
		 */
		if ( is_wp_error( $result ) && array_key_exists( self::$folder_exists, $result->errors ) ) {
			return array(
				self::$folder_exists => true,
				'destination'        => $result->error_data[ self::$folder_exists ],
			);
		}
		return $result;
	}

	/**
	 * Activate product given its product ID.
	 *
	 * @since 3.7.0
	 * @param int $product_id Product ID.
	 * @return \WP_Error|null
	 */
	private static function activate_product( $product_id ) {
		$steps = self::get_state( 'steps' );
		if ( ! $steps[ $product_id ]['activate'] ) {
			return null;
		}

		if ( 'plugin' === $steps[ $product_id ]['product_type'] ) {
			return self::activate_plugin( $product_id );
		}
		return self::activate_theme( $product_id );
	}

	/**
	 * Activate plugin given its product ID.
	 *
	 * @since 3.7.0
	 * @param int $product_id Product ID.
	 * @return \WP_Error|null
	 */
	public static function activate_plugin( $product_id ) {
		// Clear plugins cache used in `WC_Helper::get_local_woo_plugins`.
		wp_clean_plugins_cache();
		$filename = false;

		// If product is WP.org one, find out its filename.
		$dir_name = self::get_wporg_product_dir_name( $product_id );
		if ( false !== $dir_name ) {
			$filename = self::get_wporg_plugin_main_file( $dir_name );
		}

		if ( false === $filename ) {
			$plugins = wp_list_filter(
				WC_Helper::get_local_woo_plugins(),
				array(
					'_product_id' => $product_id,
				)
			);

			$filename = is_array( $plugins ) && ! empty( $plugins ) ? key( $plugins ) : '';
		}

		if ( empty( $filename ) ) {
			return new WP_Error( 'unknown_filename', __( 'Unknown product filename.', 'woocommerce' ) );
		}

		return activate_plugin( $filename );
	}

	/**
	 * Activate theme given its product ID.
	 *
	 * @since 3.7.0
	 * @param int $product_id Product ID.
	 * @return \WP_Error|null
	 */
	private static function activate_theme( $product_id ) {
		// Clear plugins cache used in `WC_Helper::get_local_woo_themes`.
		wp_clean_themes_cache();
		$theme_slug = false;

		// If product is WP.org theme, find out its slug.
		$dir_name = self::get_wporg_product_dir_name( $product_id );
		if ( false !== $dir_name ) {
			$theme_slug = basename( $dir_name );
		}

		if ( false === $theme_slug ) {
			$themes = wp_list_filter(
				WC_Helper::get_local_woo_themes(),
				array(
					'_product_id' => $product_id,
				)
			);

			$theme_slug = is_array( $themes ) && ! empty( $themes ) ? dirname( key( $themes ) ) : '';
		}

		if ( empty( $theme_slug ) ) {
			return new WP_Error( 'unknown_filename', __( 'Unknown product filename.', 'woocommerce' ) );
		}

		return switch_theme( $theme_slug );
	}

	/**
	 * Get installed directory of WP.org product.
	 *
	 * @since 3.7.0
	 * @param int $product_id Product ID.
	 * @return bool|string
	 */
	private static function get_wporg_product_dir_name( $product_id ) {
		$steps   = self::get_state( 'steps' );
		$product = $steps[ $product_id ];

		if ( empty( $product['download_url'] ) || empty( $product['installed_path'] ) ) {
			return false;
		}

		// Check whether product was downloaded from WordPress.org.
		$parsed_url = wp_parse_url( $product['download_url'] );
		if ( ! empty( $parsed_url['host'] ) && 'downloads.wordpress.org' !== $parsed_url['host'] ) {
			return false;
		}

		return basename( $product['installed_path'] );
	}

	/**
	 * Get WP.org plugin's main file.
	 *
	 * @since 3.7.0
	 * @param string $dir Directory name of the plugin.
	 * @return bool|string
	 */
	public static function get_wporg_plugin_main_file( $dir ) {
		// Ensure that exact dir name is used.
		$dir = trailingslashit( $dir );

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();
		foreach ( $plugins as $path => $plugin ) {
			if ( 0 === strpos( $path, $dir ) ) {
				return $path;
			}
		}

		return false;
	}


	/**
	 * Get plugin info
	 *
	 * @since 3.9.0
	 * @param string $dir Directory name of the plugin.
	 * @return bool|array
	 */
	public static function get_plugin_info( $dir ) {
		$plugin_folder = basename( $dir );

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();

		$related_plugins = array_filter(
			$plugins,
			function( $key ) use ( $plugin_folder ) {
				return strpos( $key, $plugin_folder . '/' ) === 0;
			},
			ARRAY_FILTER_USE_KEY
		);

		if ( 1 === count( $related_plugins ) ) {
			$plugin_key  = array_keys( $related_plugins )[0];
			$plugin_data = $plugins[ $plugin_key ];
			return array(
				'name'    => $plugin_data['Name'],
				'version' => $plugin_data['Version'],
				'active'  => is_plugin_active( $plugin_key ),
			);
		}
		return false;
	}

	/**
	 * Get an instance of WP_Upgrader to use for installing plugins.
	 *
	 * @return WP_Upgrader
	 */
	public static function get_wp_upgrader() {
		if ( ! empty( self::$wp_upgrader ) ) {
			return self::$wp_upgrader;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		WP_Filesystem();
		self::$wp_upgrader = new WP_Upgrader( new Automatic_Upgrader_Skin() );
		self::$wp_upgrader->init();
		wp_clean_plugins_cache();

		return self::$wp_upgrader;
	}
}

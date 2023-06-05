<?php
/**
 * WooCommerce Admin Helper
 *
 * @package WooCommerce\Admin\Helper
 */

use Automattic\Jetpack\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Helper Class
 *
 * The main entry-point for all things related to the Helper.
 */
class WC_Helper {
	/**
	 * A log object returned by wc_get_logger().
	 *
	 * @var $log
	 */
	public static $log;

	/**
	 * Get an absolute path to the requested helper view.
	 *
	 * @param string $view The requested view file.
	 *
	 * @return string The absolute path to the view file.
	 */
	public static function get_view_filename( $view ) {
		return dirname( __FILE__ ) . "/views/$view";
	}

	/**
	 * Loads the helper class, runs on init.
	 */
	public static function load() {
		self::includes();

		add_action( 'current_screen', array( __CLASS__, 'current_screen' ) );
		add_action( 'woocommerce_helper_output', array( __CLASS__, 'render_helper_output' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );

		do_action( 'woocommerce_helper_loaded' );
	}

	/**
	 * Include supporting helper classes.
	 */
	protected static function includes() {
		include_once dirname( __FILE__ ) . '/class-wc-helper-options.php';
		include_once dirname( __FILE__ ) . '/class-wc-helper-api.php';
		include_once dirname( __FILE__ ) . '/class-wc-helper-updater.php';
		include_once dirname( __FILE__ ) . '/class-wc-helper-plugin-info.php';
		include_once dirname( __FILE__ ) . '/class-wc-helper-compat.php';
	}

	/**
	 * Render the helper section content based on context.
	 */
	public static function render_helper_output() {
		$auth           = WC_Helper_Options::get( 'auth' );
		$auth_user_data = WC_Helper_Options::get( 'auth_user_data' );

		// Return success/error notices.
		$notices = self::_get_return_notices();

		// No active connection.
		if ( ! self::is_site_connected() ) {
			$connect_url = add_query_arg(
				array(
					'page'              => 'wc-addons',
					'section'           => 'helper',
					'wc-helper-connect' => 1,
					'wc-helper-nonce'   => wp_create_nonce( 'connect' ),
				),
				admin_url( 'admin.php' )
			);

			include self::get_view_filename( 'html-oauth-start.php' );
			return;
		}
		$disconnect_url = add_query_arg(
			array(
				'page'                 => 'wc-addons',
				'section'              => 'helper',
				'wc-helper-disconnect' => 1,
				'wc-helper-nonce'      => wp_create_nonce( 'disconnect' ),
			),
			admin_url( 'admin.php' )
		);

		$current_filter = self::get_current_filter();
		$refresh_url    = add_query_arg(
			array(
				'page'              => 'wc-addons',
				'section'           => 'helper',
				'filter'            => $current_filter,
				'wc-helper-refresh' => 1,
				'wc-helper-nonce'   => wp_create_nonce( 'refresh' ),
			),
			admin_url( 'admin.php' )
		);

		// Installed plugins and themes, with or without an active subscription.
		$woo_plugins = self::get_local_woo_plugins();
		$woo_themes  = self::get_local_woo_themes();

		$site_id                   = absint( $auth['site_id'] );
		$subscriptions             = self::get_subscriptions();
		$updates                   = WC_Helper_Updater::get_update_data();
		$subscriptions_product_ids = wp_list_pluck( $subscriptions, 'product_id' );

		foreach ( $subscriptions as &$subscription ) {
			$subscription['active'] = in_array( $site_id, $subscription['connections'] );

			$subscription['activate_url'] = add_query_arg(
				array(
					'page'                  => 'wc-addons',
					'section'               => 'helper',
					'filter'                => $current_filter,
					'wc-helper-activate'    => 1,
					'wc-helper-product-key' => $subscription['product_key'],
					'wc-helper-product-id'  => $subscription['product_id'],
					'wc-helper-nonce'       => wp_create_nonce( 'activate:' . $subscription['product_key'] ),
				),
				admin_url( 'admin.php' )
			);

			$subscription['deactivate_url'] = add_query_arg(
				array(
					'page'                  => 'wc-addons',
					'section'               => 'helper',
					'filter'                => $current_filter,
					'wc-helper-deactivate'  => 1,
					'wc-helper-product-key' => $subscription['product_key'],
					'wc-helper-product-id'  => $subscription['product_id'],
					'wc-helper-nonce'       => wp_create_nonce( 'deactivate:' . $subscription['product_key'] ),
				),
				admin_url( 'admin.php' )
			);

			$subscription['local'] = array(
				'installed' => false,
				'active'    => false,
				'version'   => null,
			);

			$subscription['update_url'] = admin_url( 'update-core.php' );

			$local = wp_list_filter( array_merge( $woo_plugins, $woo_themes ), array( '_product_id' => $subscription['product_id'] ) );

			if ( ! empty( $local ) ) {
				$local                              = array_shift( $local );
				$subscription['local']['installed'] = true;
				$subscription['local']['version']   = $local['Version'];

				if ( 'plugin' == $local['_type'] ) {
					if ( is_plugin_active( $local['_filename'] ) ) {
						$subscription['local']['active'] = true;
					} elseif ( is_multisite() && is_plugin_active_for_network( $local['_filename'] ) ) {
						$subscription['local']['active'] = true;
					}

					// A magic update_url.
					$subscription['update_url'] = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $local['_filename'], 'upgrade-plugin_' . $local['_filename'] );

				} elseif ( 'theme' == $local['_type'] ) {
					if ( in_array( $local['_stylesheet'], array( get_stylesheet(), get_template() ) ) ) {
						$subscription['local']['active'] = true;
					}

					// Another magic update_url.
					$subscription['update_url'] = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-theme&theme=' . $local['_stylesheet'] ), 'upgrade-theme_' . $local['_stylesheet'] );
				}
			}

			$subscription['has_update'] = false;
			if ( $subscription['local']['installed'] && ! empty( $updates[ $subscription['product_id'] ] ) ) {
				$subscription['has_update'] = version_compare( $updates[ $subscription['product_id'] ]['version'], $subscription['local']['version'], '>' );
			}

			$subscription['download_primary'] = true;
			$subscription['download_url']     = 'https://woocommerce.com/my-account/downloads/';
			if ( ! $subscription['local']['installed'] && ! empty( $updates[ $subscription['product_id'] ] ) ) {
				$subscription['download_url'] = $updates[ $subscription['product_id'] ]['package'];
			}

			$subscription['actions'] = array();

			if ( $subscription['has_update'] && ! $subscription['expired'] ) {
				$action = array(
					/* translators: %s: version number */
					'message'      => sprintf( __( 'Version %s is <strong>available</strong>.', 'woocommerce' ), esc_html( $updates[ $subscription['product_id'] ]['version'] ) ),
					'button_label' => __( 'Update', 'woocommerce' ),
					'button_url'   => $subscription['update_url'],
					'status'       => 'update-available',
					'icon'         => 'dashicons-update',
				);

				// Subscription is not active on this site.
				if ( ! $subscription['active'] ) {
					$action['message']     .= ' ' . __( 'To enable this update you need to <strong>activate</strong> this subscription.', 'woocommerce' );
					$action['button_label'] = null;
					$action['button_url']   = null;
				}

				$subscription['actions'][] = $action;
			}

			if ( $subscription['has_update'] && $subscription['expired'] ) {
				$action = array(
					/* translators: %s: version number */
					'message' => sprintf( __( 'Version %s is <strong>available</strong>.', 'woocommerce' ), esc_html( $updates[ $subscription['product_id'] ]['version'] ) ),
					'status'  => 'expired',
					'icon'    => 'dashicons-info',
				);

				$action['message']     .= ' ' . __( 'To enable this update you need to <strong>purchase</strong> a new subscription.', 'woocommerce' );
				$action['button_label'] = __( 'Purchase', 'woocommerce' );
				$action['button_url']   = self::add_utm_params_to_url_for_subscription_link(
					$subscription['product_url'],
					'purchase'
				);

				$subscription['actions'][] = $action;
			} elseif ( $subscription['expired'] && ! empty( $subscription['master_user_email'] ) ) {
				$action = array(
					'message' => sprintf( __( 'This subscription has expired. Contact the owner to <strong>renew</strong> the subscription to receive updates and support.', 'woocommerce' ) ),
					'status'  => 'expired',
					'icon'    => 'dashicons-info',
				);

				$subscription['actions'][] = $action;
			} elseif ( $subscription['expired'] ) {
				$action = array(
					'message'      => sprintf( __( 'This subscription has expired. Please <strong>renew</strong> to receive updates and support.', 'woocommerce' ) ),
					'button_label' => __( 'Renew', 'woocommerce' ),
					'button_url'   => self::add_utm_params_to_url_for_subscription_link(
						'https://woocommerce.com/my-account/my-subscriptions/',
						'renew'
					),
					'status'       => 'expired',
					'icon'         => 'dashicons-info',
				);

				$subscription['actions'][] = $action;
			}

			if ( $subscription['expiring'] && ! $subscription['autorenew'] ) {
				$action = array(
					'message'      => __( 'Subscription is <strong>expiring</strong> soon.', 'woocommerce' ),
					'button_label' => __( 'Enable auto-renew', 'woocommerce' ),
					'button_url'   => self::add_utm_params_to_url_for_subscription_link(
						'https://woocommerce.com/my-account/my-subscriptions/',
						'auto-renew'
					),
					'status'       => 'expired',
					'icon'         => 'dashicons-info',
				);

				$subscription['download_primary'] = false;
				$subscription['actions'][]        = $action;
			} elseif ( $subscription['expiring'] ) {
				$action = array(
					'message'      => sprintf( __( 'This subscription is expiring soon. Please <strong>renew</strong> to continue receiving updates and support.', 'woocommerce' ) ),
					'button_label' => __( 'Renew', 'woocommerce' ),
					'button_url'   => self::add_utm_params_to_url_for_subscription_link(
						'https://woocommerce.com/my-account/my-subscriptions/',
						'renew'
					),
					'status'       => 'expired',
					'icon'         => 'dashicons-info',
				);

				$subscription['download_primary'] = false;
				$subscription['actions'][]        = $action;
			}

			// Mark the first action primary.
			foreach ( $subscription['actions'] as $key => $action ) {
				if ( ! empty( $action['button_label'] ) ) {
					$subscription['actions'][ $key ]['primary'] = true;
					break;
				}
			}
		}

		// Break the by-ref.
		unset( $subscription );

		// Installed products without a subscription.
		$no_subscriptions = array();
		foreach ( array_merge( $woo_plugins, $woo_themes ) as $filename => $data ) {
			if ( in_array( $data['_product_id'], $subscriptions_product_ids ) ) {
				continue;
			}

			$data['_product_url'] = '#';
			$data['_has_update']  = false;

			if ( ! empty( $updates[ $data['_product_id'] ] ) ) {
				$data['_has_update'] = version_compare( $updates[ $data['_product_id'] ]['version'], $data['Version'], '>' );

				if ( ! empty( $updates[ $data['_product_id'] ]['url'] ) ) {
					$data['_product_url'] = $updates[ $data['_product_id'] ]['url'];
				} elseif ( ! empty( $data['PluginURI'] ) ) {
					$data['_product_url'] = $data['PluginURI'];
				}
			}

			$data['_actions'] = array();

			if ( $data['_has_update'] ) {
				$action = array(
					/* translators: %s: version number */
					'message'      => sprintf( __( 'Version %s is <strong>available</strong>. To enable this update you need to <strong>purchase</strong> a new subscription.', 'woocommerce' ), esc_html( $updates[ $data['_product_id'] ]['version'] ) ),
					'button_label' => __( 'Purchase', 'woocommerce' ),
					'button_url'   => self::add_utm_params_to_url_for_subscription_link(
						$data['_product_url'],
						'purchase'
					),
					'status'       => 'expired',
					'icon'         => 'dashicons-info',
				);

				$data['_actions'][] = $action;
			} else {
				$action = array(
					/* translators: 1: subscriptions docs 2: subscriptions docs */
					'message'      => sprintf( __( 'To receive updates and support for this extension, you need to <strong>purchase</strong> a new subscription or consolidate your extensions to one connected account by <strong><a href="%1$s" title="Sharing Docs">sharing</a> or <a href="%2$s" title="Transferring Docs">transferring</a></strong> this extension to this connected account.', 'woocommerce' ), 'https://docs.woocommerce.com/document/managing-woocommerce-com-subscriptions/#section-10', 'https://docs.woocommerce.com/document/managing-woocommerce-com-subscriptions/#section-5' ),
					'button_label' => __( 'Purchase', 'woocommerce' ),
					'button_url'   => self::add_utm_params_to_url_for_subscription_link(
						$data['_product_url'],
						'purchase'
					),
					'status'       => 'expired',
					'icon'         => 'dashicons-info',
				);

				$data['_actions'][] = $action;
			}

			$no_subscriptions[ $filename ] = $data;
		}

		// Update the user id if it came from a migrated connection.
		if ( empty( $auth['user_id'] ) ) {
			$auth['user_id'] = get_current_user_id();
			WC_Helper_Options::update( 'auth', $auth );
		}

		// Sort alphabetically.
		uasort( $subscriptions, array( __CLASS__, '_sort_by_product_name' ) );
		uasort( $no_subscriptions, array( __CLASS__, '_sort_by_name' ) );

		// Filters.
		self::get_filters_counts( $subscriptions ); // Warm it up.
		self::_filter( $subscriptions, self::get_current_filter() );

		// We have an active connection.
		include self::get_view_filename( 'html-main.php' );
		return;
	}

	/**
	 * Add tracking parameters to buttons (Renew, Purchase, etc.) on subscriptions page
	 *
	 * @param string $url URL to product page or to https://woocommerce.com/my-account/my-subscriptions/
	 * @param string $utm_content value of utm_content query parameter used for tracking
	 *
	 * @return string URL including utm parameters for tracking
	 */
	public static function add_utm_params_to_url_for_subscription_link( $url, $utm_content ) {
		$utm_params = 'utm_source=subscriptionsscreen&' .
					  'utm_medium=product&' .
					  'utm_campaign=wcaddons&' .
					  'utm_content=' . $utm_content;

		// there are already some URL parameters
		if ( strpos( $url, '?' ) ) {
			return $url . '&' . $utm_params;
		}

		return $url . '?' . $utm_params;
	}

	/**
	 * Get available subscriptions filters.
	 *
	 * @return array An array of filter keys and labels.
	 */
	public static function get_filters() {
		$filters = array(
			'all'              => __( 'All', 'woocommerce' ),
			'active'           => __( 'Active', 'woocommerce' ),
			'inactive'         => __( 'Inactive', 'woocommerce' ),
			'installed'        => __( 'Installed', 'woocommerce' ),
			'update-available' => __( 'Update Available', 'woocommerce' ),
			'expiring'         => __( 'Expiring Soon', 'woocommerce' ),
			'expired'          => __( 'Expired', 'woocommerce' ),
			'download'         => __( 'Download', 'woocommerce' ),
		);

		return $filters;
	}

	/**
	 * Get counts data for the filters array.
	 *
	 * @param array $subscriptions The array of all available subscriptions.
	 *
	 * @return array Filter counts (filter => count).
	 */
	public static function get_filters_counts( $subscriptions = null ) {
		static $filters;

		if ( isset( $filters ) ) {
			return $filters;
		}

		$filters = array_fill_keys( array_keys( self::get_filters() ), 0 );
		if ( empty( $subscriptions ) ) {
			return array();
		}

		foreach ( $filters as $key => $count ) {
			$_subs = $subscriptions;
			self::_filter( $_subs, $key );
			$filters[ $key ] = count( $_subs );
		}

		return $filters;
	}

	/**
	 * Get current filter.
	 *
	 * @return string The current filter.
	 */
	public static function get_current_filter() {
		$current_filter = 'all';
		$valid_filters  = array_keys( self::get_filters() );

		if ( ! empty( $_GET['filter'] ) && in_array( wp_unslash( $_GET['filter'] ), $valid_filters ) ) {
			$current_filter = wc_clean( wp_unslash( $_GET['filter'] ) );
		}

		return $current_filter;
	}

	/**
	 * Filter an array of subscriptions by $filter.
	 *
	 * @param array  $subscriptions The subscriptions array, passed by ref.
	 * @param string $filter The filter.
	 */
	private static function _filter( &$subscriptions, $filter ) {
		switch ( $filter ) {
			case 'active':
				$subscriptions = wp_list_filter( $subscriptions, array( 'active' => true ) );
				break;

			case 'inactive':
				$subscriptions = wp_list_filter( $subscriptions, array( 'active' => false ) );
				break;

			case 'installed':
				foreach ( $subscriptions as $key => $subscription ) {
					if ( empty( $subscription['local']['installed'] ) ) {
						unset( $subscriptions[ $key ] );
					}
				}
				break;

			case 'update-available':
				$subscriptions = wp_list_filter( $subscriptions, array( 'has_update' => true ) );
				break;

			case 'expiring':
				$subscriptions = wp_list_filter( $subscriptions, array( 'expiring' => true ) );
				break;

			case 'expired':
				$subscriptions = wp_list_filter( $subscriptions, array( 'expired' => true ) );
				break;

			case 'download':
				foreach ( $subscriptions as $key => $subscription ) {
					if ( $subscription['local']['installed'] || $subscription['expired'] ) {
						unset( $subscriptions[ $key ] );
					}
				}
				break;
		}
	}

	/**
	 * Enqueue admin scripts and styles.
	 */
	public static function admin_enqueue_scripts() {
		$screen       = get_current_screen();
		$screen_id    = $screen ? $screen->id : '';
		$wc_screen_id = 'woocommerce';

		if ( $wc_screen_id . '_page_wc-addons' === $screen_id && isset( $_GET['section'] ) && 'helper' === $_GET['section'] ) {
			wp_enqueue_style( 'woocommerce-helper', WC()->plugin_url() . '/assets/css/helper.css', array(), Constants::get_constant( 'WC_VERSION' ) );
			wp_style_add_data( 'woocommerce-helper', 'rtl', 'replace' );
		}
	}

	/**
	 * Various success/error notices.
	 *
	 * Runs during admin page render, so no headers/redirects here.
	 *
	 * @return array Array pairs of message/type strings with notices.
	 */
	private static function _get_return_notices() {
		$return_status = isset( $_GET['wc-helper-status'] ) ? wc_clean( wp_unslash( $_GET['wc-helper-status'] ) ) : null;
		$notices       = array();

		switch ( $return_status ) {
			case 'activate-success':
				$product_id   = isset( $_GET['wc-helper-product-id'] ) ? absint( $_GET['wc-helper-product-id'] ) : 0;
				$subscription = self::_get_subscriptions_from_product_id( $product_id );
				$notices[]    = array(
					'type'    => 'updated',
					'message' => sprintf(
						/* translators: %s: product name */
						__( '%s activated successfully. You will now receive updates for this product.', 'woocommerce' ),
						'<strong>' . esc_html( $subscription['product_name'] ) . '</strong>'
					),
				);
				break;

			case 'activate-error':
				$product_id   = isset( $_GET['wc-helper-product-id'] ) ? absint( $_GET['wc-helper-product-id'] ) : 0;
				$subscription = self::_get_subscriptions_from_product_id( $product_id );
				$notices[]    = array(
					'type'    => 'error',
					'message' => sprintf(
						/* translators: %s: product name */
						__( 'An error has occurred when activating %s. Please try again later.', 'woocommerce' ),
						'<strong>' . esc_html( $subscription['product_name'] ) . '</strong>'
					),
				);
				break;

			case 'deactivate-success':
				$product_id   = isset( $_GET['wc-helper-product-id'] ) ? absint( $_GET['wc-helper-product-id'] ) : 0;
				$subscription = self::_get_subscriptions_from_product_id( $product_id );
				$local        = self::_get_local_from_product_id( $product_id );

				$message = sprintf(
					/* translators: %s: product name */
					__( 'Subscription for %s deactivated successfully. You will no longer receive updates for this product.', 'woocommerce' ),
					'<strong>' . esc_html( $subscription['product_name'] ) . '</strong>'
				);

				if ( $local && is_plugin_active( $local['_filename'] ) && current_user_can( 'activate_plugins' ) ) {
					$deactivate_plugin_url = add_query_arg(
						array(
							'page'                        => 'wc-addons',
							'section'                     => 'helper',
							'filter'                      => self::get_current_filter(),
							'wc-helper-deactivate-plugin' => 1,
							'wc-helper-product-id'        => $subscription['product_id'],
							'wc-helper-nonce'             => wp_create_nonce( 'deactivate-plugin:' . $subscription['product_id'] ),
						),
						admin_url( 'admin.php' )
					);

					$message = sprintf(
						/* translators: %1$s: product name, %2$s: deactivate url */
						__( 'Subscription for %1$s deactivated successfully. You will no longer receive updates for this product. <a href="%2$s">Click here</a> if you wish to deactivate the plugin as well.', 'woocommerce' ),
						'<strong>' . esc_html( $subscription['product_name'] ) . '</strong>',
						esc_url( $deactivate_plugin_url )
					);
				}

				$notices[] = array(
					'message' => $message,
					'type'    => 'updated',
				);
				break;

			case 'deactivate-error':
				$product_id   = isset( $_GET['wc-helper-product-id'] ) ? absint( $_GET['wc-helper-product-id'] ) : 0;
				$subscription = self::_get_subscriptions_from_product_id( $product_id );
				$notices[]    = array(
					'type'    => 'error',
					'message' => sprintf(
						/* translators: %s: product name */
						__( 'An error has occurred when deactivating the subscription for %s. Please try again later.', 'woocommerce' ),
						'<strong>' . esc_html( $subscription['product_name'] ) . '</strong>'
					),
				);
				break;

			case 'deactivate-plugin-success':
				$product_id   = isset( $_GET['wc-helper-product-id'] ) ? absint( $_GET['wc-helper-product-id'] ) : 0;
				$subscription = self::_get_subscriptions_from_product_id( $product_id );
				$notices[]    = array(
					'type'    => 'updated',
					'message' => sprintf(
						/* translators: %s: product name */
						__( 'The extension %s has been deactivated successfully.', 'woocommerce' ),
						'<strong>' . esc_html( $subscription['product_name'] ) . '</strong>'
					),
				);
				break;

			case 'deactivate-plugin-error':
				$product_id   = isset( $_GET['wc-helper-product-id'] ) ? absint( $_GET['wc-helper-product-id'] ) : 0;
				$subscription = self::_get_subscriptions_from_product_id( $product_id );
				$notices[]    = array(
					'type'    => 'error',
					'message' => sprintf(
						/* translators: %1$s: product name, %2$s: plugins screen url */
						__( 'An error has occurred when deactivating the extension %1$s. Please proceed to the <a href="%2$s">Plugins screen</a> to deactivate it manually.', 'woocommerce' ),
						'<strong>' . esc_html( $subscription['product_name'] ) . '</strong>',
						admin_url( 'plugins.php' )
					),
				);
				break;

			case 'helper-connected':
				$notices[] = array(
					'message' => __( 'You have successfully connected your store to WooCommerce.com', 'woocommerce' ),
					'type'    => 'updated',
				);
				break;

			case 'helper-disconnected':
				$notices[] = array(
					'message' => __( 'You have successfully disconnected your store from WooCommerce.com', 'woocommerce' ),
					'type'    => 'updated',
				);
				break;

			case 'helper-refreshed':
				$notices[] = array(
					'message' => __( 'Authentication and subscription caches refreshed successfully.', 'woocommerce' ),
					'type'    => 'updated',
				);
				break;
		}

		return $notices;
	}

	/**
	 * Various early-phase actions with possible redirects.
	 *
	 * @param object $screen WP screen object.
	 */
	public static function current_screen( $screen ) {
		$wc_screen_id = 'woocommerce';

		if ( $wc_screen_id . '_page_wc-addons' !== $screen->id ) {
			return;
		}

		if ( empty( $_GET['section'] ) || 'helper' !== $_GET['section'] ) {
			return;
		}

		if ( ! empty( $_GET['wc-helper-connect'] ) ) {
			return self::_helper_auth_connect();
		}

		if ( ! empty( $_GET['wc-helper-return'] ) ) {
			return self::_helper_auth_return();
		}

		if ( ! empty( $_GET['wc-helper-disconnect'] ) ) {
			return self::_helper_auth_disconnect();
		}

		if ( ! empty( $_GET['wc-helper-refresh'] ) ) {
			return self::_helper_auth_refresh();
		}

		if ( ! empty( $_GET['wc-helper-activate'] ) ) {
			return self::_helper_subscription_activate();
		}

		if ( ! empty( $_GET['wc-helper-deactivate'] ) ) {
			return self::_helper_subscription_deactivate();
		}

		if ( ! empty( $_GET['wc-helper-deactivate-plugin'] ) ) {
			return self::_helper_plugin_deactivate();
		}
	}

	/**
	 * Initiate a new OAuth connection.
	 */
	private static function _helper_auth_connect() {
		if ( empty( $_GET['wc-helper-nonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['wc-helper-nonce'] ), 'connect' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			self::log( 'Could not verify nonce in _helper_auth_connect' );
			wp_die( 'Could not verify nonce' );
		}

		$redirect_uri = add_query_arg(
			array(
				'page'             => 'wc-addons',
				'section'          => 'helper',
				'wc-helper-return' => 1,
				'wc-helper-nonce'  => wp_create_nonce( 'connect' ),
			),
			admin_url( 'admin.php' )
		);

		$request = WC_Helper_API::post(
			'oauth/request_token',
			array(
				'body' => array(
					'home_url'     => home_url(),
					'redirect_uri' => $redirect_uri,
				),
			)
		);

		$code = wp_remote_retrieve_response_code( $request );

		if ( 200 !== $code ) {
			self::log( sprintf( 'Call to oauth/request_token returned a non-200 response code (%d)', $code ) );
			wp_die( 'Something went wrong' );
		}

		$secret = json_decode( wp_remote_retrieve_body( $request ) );
		if ( empty( $secret ) ) {
			self::log( sprintf( 'Call to oauth/request_token returned an invalid body: %s', wp_remote_retrieve_body( $request ) ) );
			wp_die( 'Something went wrong' );
		}

		/**
		 * Fires when the Helper connection process is initiated.
		 */
		do_action( 'woocommerce_helper_connect_start' );

		$connect_url = add_query_arg(
			array(
				'home_url'     => rawurlencode( home_url() ),
				'redirect_uri' => rawurlencode( $redirect_uri ),
				'secret'       => rawurlencode( $secret ),
			),
			WC_Helper_API::url( 'oauth/authorize' )
		);

		wp_redirect( esc_url_raw( $connect_url ) );
		die();
	}

	/**
	 * Return from WooCommerce.com OAuth flow.
	 */
	private static function _helper_auth_return() {
		if ( empty( $_GET['wc-helper-nonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['wc-helper-nonce'] ), 'connect' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			self::log( 'Could not verify nonce in _helper_auth_return' );
			wp_die( 'Something went wrong' );
		}

		// Bail if the user clicked deny.
		if ( ! empty( $_GET['deny'] ) ) {
			/**
			 * Fires when the Helper connection process is denied/cancelled.
			 */
			do_action( 'woocommerce_helper_denied' );
			wp_safe_redirect( admin_url( 'admin.php?page=wc-addons&section=helper' ) );
			die();
		}

		// We do need a request token...
		if ( empty( $_GET['request_token'] ) ) {
			self::log( 'Request token not found in _helper_auth_return' );
			wp_die( 'Something went wrong' );
		}

		// Obtain an access token.
		$request = WC_Helper_API::post(
			'oauth/access_token',
			array(
				'body' => array(
					'request_token' => wp_unslash( $_GET['request_token'] ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					'home_url'      => home_url(),
				),
			)
		);

		$code = wp_remote_retrieve_response_code( $request );

		if ( 200 !== $code ) {
			self::log( sprintf( 'Call to oauth/access_token returned a non-200 response code (%d)', $code ) );
			wp_die( 'Something went wrong' );
		}

		$access_token = json_decode( wp_remote_retrieve_body( $request ), true );
		if ( ! $access_token ) {
			self::log( sprintf( 'Call to oauth/access_token returned an invalid body: %s', wp_remote_retrieve_body( $request ) ) );
			wp_die( 'Something went wrong' );
		}

		self::update_auth_option( $access_token['access_token'], $access_token['access_token_secret'], $access_token['site_id'] );

		/**
		 * Fires when the Helper connection process has completed successfully.
		 */
		do_action( 'woocommerce_helper_connected' );

		// Enable tracking when connected.
		if ( class_exists( 'WC_Tracker' ) ) {
			update_option( 'woocommerce_allow_tracking', 'yes' );
			WC_Tracker::send_tracking_data( true );
		}

		// If connecting through in-app purchase, redirects back to WooCommerce.com
		// for product installation.
		if ( ! empty( $_GET['wccom-install-url'] ) ) {
			wp_redirect( wp_unslash( $_GET['wccom-install-url'] ) );
			exit;
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'             => 'wc-addons',
					'section'          => 'helper',
					'wc-helper-status' => 'helper-connected',
				),
				admin_url( 'admin.php' )
			)
		);
		die();
	}

	/**
	 * Disconnect from WooCommerce.com, clear OAuth tokens.
	 */
	private static function _helper_auth_disconnect() {
		if ( empty( $_GET['wc-helper-nonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['wc-helper-nonce'] ), 'disconnect' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			self::log( 'Could not verify nonce in _helper_auth_disconnect' );
			wp_die( 'Could not verify nonce' );
		}

		/**
		 * Fires when the Helper has been disconnected.
		 */
		do_action( 'woocommerce_helper_disconnected' );

		$redirect_uri = add_query_arg(
			array(
				'page'             => 'wc-addons',
				'section'          => 'helper',
				'wc-helper-status' => 'helper-disconnected',
			),
			admin_url( 'admin.php' )
		);

		self::disconnect();

		wp_safe_redirect( $redirect_uri );
		die();
	}

	/**
	 * User hit the Refresh button, clear all caches.
	 */
	private static function _helper_auth_refresh() {
		if ( empty( $_GET['wc-helper-nonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['wc-helper-nonce'] ), 'refresh' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			self::log( 'Could not verify nonce in _helper_auth_refresh' );
			wp_die( 'Could not verify nonce' );
		}

		/**
		 * Fires when Helper subscriptions are refreshed.
		 */
		do_action( 'woocommerce_helper_subscriptions_refresh' );

		$redirect_uri = add_query_arg(
			array(
				'page'             => 'wc-addons',
				'section'          => 'helper',
				'filter'           => self::get_current_filter(),
				'wc-helper-status' => 'helper-refreshed',
			),
			admin_url( 'admin.php' )
		);

		self::_flush_authentication_cache();
		self::_flush_subscriptions_cache();
		self::_flush_updates_cache();

		wp_safe_redirect( $redirect_uri );
		die();
	}

	/**
	 * Active a product subscription.
	 */
	private static function _helper_subscription_activate() {
		$product_key = isset( $_GET['wc-helper-product-key'] ) ? wc_clean( wp_unslash( $_GET['wc-helper-product-key'] ) ) : '';
		$product_id  = isset( $_GET['wc-helper-product-id'] ) ? absint( $_GET['wc-helper-product-id'] ) : 0;

		if ( empty( $_GET['wc-helper-nonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['wc-helper-nonce'] ), 'activate:' . $product_key ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			self::log( 'Could not verify nonce in _helper_subscription_activate' );
			wp_die( 'Could not verify nonce' );
		}

		// Activate subscription.
		$activation_response = WC_Helper_API::post(
			'activate',
			array(
				'authenticated' => true,
				'body'          => wp_json_encode(
					array(
						'product_key' => $product_key,
					)
				),
			)
		);

		$activated = wp_remote_retrieve_response_code( $activation_response ) === 200;
		$body      = json_decode( wp_remote_retrieve_body( $activation_response ), true );

		if ( ! $activated && ! empty( $body['code'] ) && 'already_connected' === $body['code'] ) {
			$activated = true;
		}

		if ( $activated ) {
			/**
			 * Fires when the Helper activates a product successfully.
			 *
			 * @param int    $product_id Product ID being activated.
			 * @param string $product_key Subscription product key.
			 * @param array  $activation_response The response object from wp_safe_remote_request().
			 */
			do_action( 'woocommerce_helper_subscription_activate_success', $product_id, $product_key, $activation_response );
		} else {
			/**
			 * Fires when the Helper fails to activate a product.
			 *
			 * @param int    $product_id Product ID being activated.
			 * @param string $product_key Subscription product key.
			 * @param array  $activation_response The response object from wp_safe_remote_request().
			 */
			do_action( 'woocommerce_helper_subscription_activate_error', $product_id, $product_key, $activation_response );
		}

		// Attempt to activate this plugin.
		$local = self::_get_local_from_product_id( $product_id );
		if ( $local && 'plugin' == $local['_type'] && current_user_can( 'activate_plugins' ) && ! is_plugin_active( $local['_filename'] ) ) {
			activate_plugin( $local['_filename'] );
		}

		self::_flush_subscriptions_cache();
		self::_flush_updates_cache();

		$redirect_uri = add_query_arg(
			array(
				'page'                 => 'wc-addons',
				'section'              => 'helper',
				'filter'               => self::get_current_filter(),
				'wc-helper-status'     => $activated ? 'activate-success' : 'activate-error',
				'wc-helper-product-id' => $product_id,
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect_uri );
		die();
	}

	/**
	 * Deactivate a product subscription.
	 */
	private static function _helper_subscription_deactivate() {
		$product_key = isset( $_GET['wc-helper-product-key'] ) ? wc_clean( wp_unslash( $_GET['wc-helper-product-key'] ) ) : '';
		$product_id  = isset( $_GET['wc-helper-product-id'] ) ? absint( $_GET['wc-helper-product-id'] ) : 0;

		if ( empty( $_GET['wc-helper-nonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['wc-helper-nonce'] ), 'deactivate:' . $product_key ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			self::log( 'Could not verify nonce in _helper_subscription_deactivate' );
			wp_die( 'Could not verify nonce' );
		}

		$deactivation_response = WC_Helper_API::post(
			'deactivate',
			array(
				'authenticated' => true,
				'body'          => wp_json_encode(
					array(
						'product_key' => $product_key,
					)
				),
			)
		);

		$code        = wp_remote_retrieve_response_code( $deactivation_response );
		$deactivated = 200 === $code;

		if ( $deactivated ) {
			/**
			 * Fires when the Helper activates a product successfully.
			 *
			 * @param int    $product_id Product ID being deactivated.
			 * @param string $product_key Subscription product key.
			 * @param array  $deactivation_response The response object from wp_safe_remote_request().
			 */
			do_action( 'woocommerce_helper_subscription_deactivate_success', $product_id, $product_key, $deactivation_response );
		} else {
			self::log( sprintf( 'Deactivate API call returned a non-200 response code (%d)', $code ) );

			/**
			 * Fires when the Helper fails to activate a product.
			 *
			 * @param int    $product_id Product ID being deactivated.
			 * @param string $product_key Subscription product key.
			 * @param array  $deactivation_response The response object from wp_safe_remote_request().
			 */
			do_action( 'woocommerce_helper_subscription_deactivate_error', $product_id, $product_key, $deactivation_response );
		}

		self::_flush_subscriptions_cache();

		$redirect_uri = add_query_arg(
			array(
				'page'                 => 'wc-addons',
				'section'              => 'helper',
				'filter'               => self::get_current_filter(),
				'wc-helper-status'     => $deactivated ? 'deactivate-success' : 'deactivate-error',
				'wc-helper-product-id' => $product_id,
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect_uri );
		die();
	}

	/**
	 * Deactivate a plugin.
	 */
	private static function _helper_plugin_deactivate() {
		$product_id  = isset( $_GET['wc-helper-product-id'] ) ? absint( $_GET['wc-helper-product-id'] ) : 0;
		$deactivated = false;

		if ( empty( $_GET['wc-helper-nonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['wc-helper-nonce'] ), 'deactivate-plugin:' . $product_id ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			self::log( 'Could not verify nonce in _helper_plugin_deactivate' );
			wp_die( 'Could not verify nonce' );
		}

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_die( 'You are not allowed to manage plugins on this site.' );
		}

		$local = wp_list_filter(
			array_merge(
				self::get_local_woo_plugins(),
				self::get_local_woo_themes()
			),
			array( '_product_id' => $product_id )
		);

		// Attempt to deactivate this plugin or theme.
		if ( ! empty( $local ) ) {
			$local = array_shift( $local );
			if ( is_plugin_active( $local['_filename'] ) ) {
				deactivate_plugins( $local['_filename'] );
			}

			$deactivated = ! is_plugin_active( $local['_filename'] );
		}

		$redirect_uri = add_query_arg(
			array(
				'page'                 => 'wc-addons',
				'section'              => 'helper',
				'filter'               => self::get_current_filter(),
				'wc-helper-status'     => $deactivated ? 'deactivate-plugin-success' : 'deactivate-plugin-error',
				'wc-helper-product-id' => $product_id,
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect_uri );
		die();
	}

	/**
	 * Get a local plugin/theme entry from product_id.
	 *
	 * @param int $product_id The product id.
	 *
	 * @return array|bool The array containing the local plugin/theme data or false.
	 */
	private static function _get_local_from_product_id( $product_id ) {
		$local = wp_list_filter(
			array_merge(
				self::get_local_woo_plugins(),
				self::get_local_woo_themes()
			),
			array( '_product_id' => $product_id )
		);

		if ( ! empty( $local ) ) {
			return array_shift( $local );
		}

		return false;
	}

	/**
	 * Checks whether current site has product subscription of a given ID.
	 *
	 * @since 3.7.0
	 *
	 * @param int $product_id The product id.
	 *
	 * @return bool Returns true if product subscription exists, false otherwise.
	 */
	public static function has_product_subscription( $product_id ) {
		$subscription = self::_get_subscriptions_from_product_id( $product_id, true );
		return ! empty( $subscription );
	}

	/**
	 * Get a subscription entry from product_id. If multiple subscriptions are
	 * found with the same product id and $single is set to true, will return the
	 * first one in the list, so you can use this method to get things like extension
	 * name, version, etc.
	 *
	 * @param int  $product_id The product id.
	 * @param bool $single Whether to return a single subscription or all matching a product id.
	 *
	 * @return array|bool The array containing sub data or false.
	 */
	private static function _get_subscriptions_from_product_id( $product_id, $single = true ) {
		$subscriptions = wp_list_filter( self::get_subscriptions(), array( 'product_id' => $product_id ) );
		if ( ! empty( $subscriptions ) ) {
			return $single ? array_shift( $subscriptions ) : $subscriptions;
		}

		return false;
	}

	/**
	 * Obtain a list of data about locally installed Woo extensions.
	 */
	public static function get_local_woo_plugins() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();

		/**
		 * Check if plugins have WC headers, if not then clear cache and fetch again.
		 * WC Headers will not be present if `wc_enable_wc_plugin_headers` hook was added after a `get_plugins` call -- for example when WC is activated/updated.
		 * Also, get_plugins call is expensive, so we should clear this cache very conservatively.
		 */
		if ( ! empty( $plugins ) && ! array_key_exists( 'Woo', current( $plugins ) ) ) {
			wp_clean_plugins_cache( false );
			$plugins = get_plugins();
		}

		$woo_plugins = array();

		// Backwards compatibility for woothemes_queue_update().
		$_compat = array();
		if ( ! empty( $GLOBALS['woothemes_queued_updates'] ) ) {
			foreach ( $GLOBALS['woothemes_queued_updates'] as $_compat_plugin ) {
				$_compat[ $_compat_plugin->file ] = array(
					'product_id' => $_compat_plugin->product_id,
					'file_id'    => $_compat_plugin->file_id,
				);
			}
		}

		foreach ( $plugins as $filename => $data ) {
			if ( empty( $data['Woo'] ) && ! empty( $_compat[ $filename ] ) ) {
				$data['Woo'] = sprintf( '%d:%s', $_compat[ $filename ]['product_id'], $_compat[ $filename ]['file_id'] );
			}

			if ( empty( $data['Woo'] ) ) {
				continue;
			}

			list( $product_id, $file_id ) = explode( ':', $data['Woo'] );
			if ( empty( $product_id ) || empty( $file_id ) ) {
				continue;
			}

			$data['_filename']        = $filename;
			$data['_product_id']      = absint( $product_id );
			$data['_file_id']         = $file_id;
			$data['_type']            = 'plugin';
			$data['slug']             = dirname( $filename );
			$woo_plugins[ $filename ] = $data;
		}

		return $woo_plugins;
	}

	/**
	 * Get locally installed Woo themes.
	 */
	public static function get_local_woo_themes() {
		$themes     = wp_get_themes();
		$woo_themes = array();

		foreach ( $themes as $theme ) {
			$header = $theme->get( 'Woo' );

			// Backwards compatibility for theme_info.txt.
			if ( ! $header ) {
				$txt = $theme->get_stylesheet_directory() . '/theme_info.txt';
				if ( is_readable( $txt ) ) {
					$txt = file_get_contents( $txt );
					$txt = preg_split( '#\s#', $txt );
					if ( count( $txt ) >= 2 ) {
						$header = sprintf( '%d:%s', $txt[0], $txt[1] );
					}
				}
			}

			if ( empty( $header ) ) {
				continue;
			}

			list( $product_id, $file_id ) = explode( ':', $header );
			if ( empty( $product_id ) || empty( $file_id ) ) {
				continue;
			}

			$data = array(
				'Name'        => $theme->get( 'Name' ),
				'Version'     => $theme->get( 'Version' ),
				'Woo'         => $header,

				'_filename'   => $theme->get_stylesheet() . '/style.css',
				'_stylesheet' => $theme->get_stylesheet(),
				'_product_id' => absint( $product_id ),
				'_file_id'    => $file_id,
				'_type'       => 'theme',
			);

			$woo_themes[ $data['_filename'] ] = $data;
		}

		return $woo_themes;
	}

	/**
	 * Get the connected user's subscriptions.
	 *
	 * @return array
	 */
	public static function get_subscriptions() {
		$cache_key = '_woocommerce_helper_subscriptions';
		$data      = get_transient( $cache_key );
		if ( false !== $data ) {
			return $data;
		}

		$request_uri = wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$source      = '';
		if ( stripos( $request_uri, 'wc-addons' ) ) :
			$source = 'my-subscriptions';
		elseif ( stripos( $request_uri, 'plugins.php' ) ) :
			$source = 'plugins';
		elseif ( stripos( $request_uri, 'wc-admin' ) ) :
			$source = 'inbox-notes';
		elseif ( stripos( $request_uri, 'admin-ajax.php' ) ) :
			$source = 'heartbeat-api';
		elseif ( defined( 'WP_CLI' ) && WP_CLI ) :
			$source = 'wc-cli';
		endif;

		// Obtain the connected user info.
		$request = WC_Helper_API::get(
			'subscriptions',
			array(
				'authenticated' => true,
				'query_string'  => '' !== $source ? esc_url( '?source=' . $source ) : '',
			)
		);

		if ( wp_remote_retrieve_response_code( $request ) !== 200 ) {
			set_transient( $cache_key, array(), 15 * MINUTE_IN_SECONDS );
			return array();
		}

		$data = json_decode( wp_remote_retrieve_body( $request ), true );
		if ( empty( $data ) || ! is_array( $data ) ) {
			$data = array();
		}

		set_transient( $cache_key, $data, 1 * HOUR_IN_SECONDS );
		return $data;
	}

	/**
	 * Runs when any plugin is activated.
	 *
	 * Depending on the activated plugin attempts to look through available
	 * subscriptions and auto-activate one if possible, so the user does not
	 * need to visit the Helper UI at all after installing a new extension.
	 *
	 * @param string $filename The filename of the activated plugin.
	 */
	public static function activated_plugin( $filename ) {
		$plugins = self::get_local_woo_plugins();

		// Not a local woo plugin.
		if ( empty( $plugins[ $filename ] ) ) {
			return;
		}

		// Make sure we have a connection.
		$auth = WC_Helper_Options::get( 'auth' );
		if ( empty( $auth ) ) {
			return;
		}

		$plugin        = $plugins[ $filename ];
		$product_id    = $plugin['_product_id'];
		$subscriptions = self::_get_subscriptions_from_product_id( $product_id, false );

		// No valid subscriptions for this product.
		if ( empty( $subscriptions ) ) {
			return;
		}

		$subscription = null;
		foreach ( $subscriptions as $_sub ) {

			// Don't attempt to activate expired subscriptions.
			if ( $_sub['expired'] ) {
				continue;
			}

			// No more sites available in this subscription.
			if ( $_sub['sites_max'] && $_sub['sites_active'] >= $_sub['sites_max'] ) {
				continue;
			}

			// Looks good.
			$subscription = $_sub;
			break;
		}

		// No valid subscription found.
		if ( ! $subscription ) {
			return;
		}

		$product_key         = $subscription['product_key'];
		$activation_response = WC_Helper_API::post(
			'activate',
			array(
				'authenticated' => true,
				'body'          => wp_json_encode(
					array(
						'product_key' => $product_key,
					)
				),
			)
		);

		$activated = wp_remote_retrieve_response_code( $activation_response ) === 200;
		$body      = json_decode( wp_remote_retrieve_body( $activation_response ), true );

		if ( ! $activated && ! empty( $body['code'] ) && 'already_connected' === $body['code'] ) {
			$activated = true;
		}

		if ( $activated ) {
			self::log( 'Auto-activated a subscription for ' . $filename );
			/**
			 * Fires when the Helper activates a product successfully.
			 *
			 * @param int    $product_id Product ID being activated.
			 * @param string $product_key Subscription product key.
			 * @param array  $activation_response The response object from wp_safe_remote_request().
			 */
			do_action( 'woocommerce_helper_subscription_activate_success', $product_id, $product_key, $activation_response );
		} else {
			self::log( 'Could not activate a subscription upon plugin activation: ' . $filename );

			/**
			 * Fires when the Helper fails to activate a product.
			 *
			 * @param int    $product_id Product ID being activated.
			 * @param string $product_key Subscription product key.
			 * @param array  $activation_response The response object from wp_safe_remote_request().
			 */
			do_action( 'woocommerce_helper_subscription_activate_error', $product_id, $product_key, $activation_response );
		}

		self::_flush_subscriptions_cache();
		self::_flush_updates_cache();
	}

	/**
	 * Runs when any plugin is deactivated.
	 *
	 * When a user deactivates a plugin, attempt to deactivate any subscriptions
	 * associated with the extension.
	 *
	 * @param string $filename The filename of the deactivated plugin.
	 */
	public static function deactivated_plugin( $filename ) {
		$plugins = self::get_local_woo_plugins();

		// Not a local woo plugin.
		if ( empty( $plugins[ $filename ] ) ) {
			return;
		}

		// Make sure we have a connection.
		$auth = WC_Helper_Options::get( 'auth' );
		if ( empty( $auth ) ) {
			return;
		}

		$plugin        = $plugins[ $filename ];
		$product_id    = $plugin['_product_id'];
		$subscriptions = self::_get_subscriptions_from_product_id( $product_id, false );
		$site_id       = absint( $auth['site_id'] );

		// No valid subscriptions for this product.
		if ( empty( $subscriptions ) ) {
			return;
		}

		$deactivated = 0;

		foreach ( $subscriptions as $subscription ) {
			// Don't touch subscriptions that aren't activated on this site.
			if ( ! in_array( $site_id, $subscription['connections'], true ) ) {
				continue;
			}

			$product_key           = $subscription['product_key'];
			$deactivation_response = WC_Helper_API::post(
				'deactivate',
				array(
					'authenticated' => true,
					'body'          => wp_json_encode(
						array(
							'product_key' => $product_key,
						)
					),
				)
			);

			if ( wp_remote_retrieve_response_code( $deactivation_response ) === 200 ) {
				$deactivated++;

				/**
				 * Fires when the Helper activates a product successfully.
				 *
				 * @param int    $product_id Product ID being deactivated.
				 * @param string $product_key Subscription product key.
				 * @param array  $deactivation_response The response object from wp_safe_remote_request().
				 */
				do_action( 'woocommerce_helper_subscription_deactivate_success', $product_id, $product_key, $deactivation_response );
			} else {
				/**
				 * Fires when the Helper fails to activate a product.
				 *
				 * @param int    $product_id Product ID being deactivated.
				 * @param string $product_key Subscription product key.
				 * @param array  $deactivation_response The response object from wp_safe_remote_request().
				 */
				do_action( 'woocommerce_helper_subscription_deactivate_error', $product_id, $product_key, $deactivation_response );
			}
		}

		if ( $deactivated ) {
			self::log( sprintf( 'Auto-deactivated %d subscription(s) for %s', $deactivated, $filename ) );
			self::_flush_subscriptions_cache();
			self::_flush_updates_cache();
		}
	}

	/**
	 * Various Helper-related admin notices.
	 */
	public static function admin_notices() {
		if ( apply_filters( 'woocommerce_helper_suppress_admin_notices', false ) ) {
			return;
		}

		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( 'update-core' !== $screen_id ) {
			return;
		}

		// Don't nag if Woo doesn't have an update available.
		if ( ! self::_woo_core_update_available() ) {
			return;
		}

		// Add a note about available extension updates if Woo core has an update available.
		$notice = self::_get_extensions_update_notice();
		if ( ! empty( $notice ) ) {
			echo '<div class="updated woocommerce-message"><p>' . $notice . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Get an update notice if one or more Woo extensions has an update available.
	 *
	 * @return string|null The update notice or null if everything is up to date.
	 */
	private static function _get_extensions_update_notice() {
		$plugins   = self::get_local_woo_plugins();
		$updates   = WC_Helper_Updater::get_update_data();
		$available = 0;

		foreach ( $plugins as $data ) {
			if ( empty( $updates[ $data['_product_id'] ] ) ) {
				continue;
			}

			$product_id = $data['_product_id'];
			if ( version_compare( $updates[ $product_id ]['version'], $data['Version'], '>' ) ) {
				$available++;
			}
		}

		if ( ! $available ) {
			return;
		}

		return sprintf(
			/* translators: %1$s: helper url, %2$d: number of extensions */
			_n( 'Note: You currently have <a href="%1$s">%2$d paid extension</a> which should be updated first before updating WooCommerce.', 'Note: You currently have <a href="%1$s">%2$d paid extensions</a> which should be updated first before updating WooCommerce.', $available, 'woocommerce' ),
			admin_url( 'admin.php?page=wc-addons&section=helper' ),
			$available
		);
	}

	/**
	 * Whether WooCommerce has an update available.
	 *
	 * @return bool True if a Woo core update is available.
	 */
	private static function _woo_core_update_available() {
		$updates = get_site_transient( 'update_plugins' );
		if ( empty( $updates->response ) ) {
			return false;
		}

		if ( empty( $updates->response['woocommerce/woocommerce.php'] ) ) {
			return false;
		}

		$data = $updates->response['woocommerce/woocommerce.php'];
		if ( version_compare( Constants::get_constant( 'WC_VERSION' ), $data->new_version, '>=' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Flush subscriptions cache.
	 */
	public static function _flush_subscriptions_cache() {
		delete_transient( '_woocommerce_helper_subscriptions' );
	}

	/**
	 * Flush auth cache.
	 */
	public static function _flush_authentication_cache() {
		$request = WC_Helper_API::get(
			'oauth/me',
			array(
				'authenticated' => true,
			)
		);

		if ( wp_remote_retrieve_response_code( $request ) !== 200 ) {
			return false;
		}

		$user_data = json_decode( wp_remote_retrieve_body( $request ), true );
		if ( ! $user_data ) {
			return false;
		}

		WC_Helper_Options::update(
			'auth_user_data',
			array(
				'name'  => $user_data['name'],
				'email' => $user_data['email'],
			)
		);

		return true;
	}

	/**
	 * Flush updates cache.
	 */
	private static function _flush_updates_cache() {
		WC_Helper_Updater::flush_updates_cache();
	}

	/**
	 * Sort subscriptions by the product_name.
	 *
	 * @param array $a Subscription array.
	 * @param array $b Subscription array.
	 *
	 * @return int
	 */
	public static function _sort_by_product_name( $a, $b ) {
		return strcmp( $a['product_name'], $b['product_name'] );
	}

	/**
	 * Sort subscriptions by the Name.
	 *
	 * @param array $a Product array.
	 * @param array $b Product array.
	 *
	 * @return int
	 */
	public static function _sort_by_name( $a, $b ) {
		return strcmp( $a['Name'], $b['Name'] );
	}

	/**
	 * Log a helper event.
	 *
	 * @param string $message Log message.
	 * @param string $level Optional, defaults to info, valid levels: emergency|alert|critical|error|warning|notice|info|debug.
	 */
	public static function log( $message, $level = 'info' ) {
		if ( ! Constants::is_true( 'WP_DEBUG' ) ) {
			return;
		}

		if ( ! isset( self::$log ) ) {
			self::$log = wc_get_logger();
		}

		self::$log->log( $level, $message, array( 'source' => 'helper' ) );
	}

	/**
	 * Handles WC Helper disconnect tasks.
	 *
	 * @return void
	 */
	public static function disconnect() {
		WC_Helper_API::post(
			'oauth/invalidate_token',
			array(
				'authenticated' => true,
			)
		);

		WC_Helper_Options::update( 'auth', array() );
		WC_Helper_Options::update( 'auth_user_data', array() );

		self::_flush_subscriptions_cache();
		self::_flush_updates_cache();
	}

	/**
	 * Checks if `access_token` exists in `auth` option.
	 *
	 * @return bool
	 */
	public static function is_site_connected(): bool {
		$auth = WC_Helper_Options::get( 'auth' );

		// If `access_token` is empty, there's no active connection.
		return ! empty( $auth['access_token'] );
	}

	/**
	 * Allows to connect with WCCOM using application password. used it to connect via CLI
	 *
	 * @param string $password The application password.
	 *
	 * @return void|WP_Error
	 */
	public static function connect_with_password( string $password ) {
		$request = WC_Helper_API::post(
			'connect',
			array(
				'headers'       => array(
					'X-API-Key'    => $password,
					'Content-Type' => 'application/json',
				),
				'body'          => wp_json_encode( array( 'home_url' => home_url() ) ),
				'authenticated' => false,
			)
		);

		$code = wp_remote_retrieve_response_code( $request );

		if ( $code === 403 ) {
			$message = 'Invalid password';
			self::log( $message );

			return new WP_Error( 'connect-with-password-invalid-password', $message );
		} elseif ( $code !== 200 ) {
			$message = sprintf( 'Call to /connect returned a non-200 response code (%d)', $code );
			self::log( $message );

			return new WP_Error( 'connect-with-password-' . $code, $message );
		}

		$access_data = json_decode( wp_remote_retrieve_body( $request ), true );
		if ( empty( $access_data['access_token'] ) || empty( $access_data['access_token_secret'] ) ) {
			$message = sprintf( 'Call to /connect returned an invalid body: %s', wp_remote_retrieve_body( $request ) );
			self::log( $message );

			return new WP_Error( 'connect-with-password-invalid-response', $message );
		}

		self::update_auth_option( $access_data['access_token'], $access_data['access_token_secret'], $access_data['site_id'] );
	}

	/**
	 * Updates auth options and flushes cache
	 *
	 * @param string $access_token The access token.
	 * @param string $access_token_secret The secret access token.
	 * @param int    $site_id The site id returned by the API.
	 *
	 * @return void
	 */
	public static function update_auth_option( string $access_token, string $access_token_secret, int $site_id ): void {
		WC_Helper_Options::update(
			'auth',
			array(
				'access_token'        => $access_token,
				'access_token_secret' => $access_token_secret,
				'site_id'             => $site_id,
				'user_id'             => get_current_user_id(),
				'updated'             => time(),
			)
		);

		// Obtain the connected user info.
		if ( ! self::_flush_authentication_cache() ) {
			self::log( 'Could not obtain connected user info in _helper_auth_return.' );
			WC_Helper_Options::update( 'auth', array() );
			wp_die( 'Something went wrong. Could not obtain connected user info in _helper_auth_return.' );
		}

		self::_flush_subscriptions_cache();
		self::_flush_updates_cache();
	}
}

WC_Helper::load();

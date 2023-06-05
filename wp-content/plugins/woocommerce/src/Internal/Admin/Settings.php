<?php
/**
 * WooCommerce Settings.
 */

namespace Automattic\WooCommerce\Internal\Admin;

use Automattic\WooCommerce\Admin\API\Plugins;
use Automattic\WooCommerce\Admin\PageController;
use Automattic\WooCommerce\Admin\API\Reports\Orders\DataStore as OrdersDataStore;
use Automattic\WooCommerce\Admin\PluginsHelper;
use WC_Marketplace_Suggestions;

/**
 * Contains logic in regards to WooCommerce Admin Settings.
 */
class Settings {

	/**
	 * Class instance.
	 *
	 * @var Settings instance
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Hook into WooCommerce.
	 */
	public function __construct() {
		// Old settings injection.
		add_filter( 'woocommerce_components_settings', array( $this, 'add_component_settings' ) );
		// New settings injection.
		add_filter( 'woocommerce_admin_shared_settings', array( $this, 'add_component_settings' ) );
		add_filter( 'woocommerce_settings_groups', array( $this, 'add_settings_group' ) );
		add_filter( 'woocommerce_settings-wc_admin', array( $this, 'add_settings' ) );
	}

	/**
	 * Format order statuses by removing a leading 'wc-' if present.
	 *
	 * @param array $statuses Order statuses.
	 * @return array formatted statuses.
	 */
	public static function get_order_statuses( $statuses ) {
		$formatted_statuses = array();
		foreach ( $statuses as $key => $value ) {
			$formatted_key                        = preg_replace( '/^wc-/', '', $key );
			$formatted_statuses[ $formatted_key ] = $value;
		}
		return $formatted_statuses;
	}

	/**
	 * Get all order statuses present in analytics tables that aren't registered.
	 *
	 * @return array Unregistered order statuses.
	 */
	private function get_unregistered_order_statuses() {
		$registered_statuses   = wc_get_order_statuses();
		$all_synced_statuses   = OrdersDataStore::get_all_statuses();
		$unregistered_statuses = array_diff( $all_synced_statuses, array_keys( $registered_statuses ) );
		$formatted_status_keys = self::get_order_statuses( array_fill_keys( $unregistered_statuses, '' ) );
		$formatted_statuses    = array_keys( $formatted_status_keys );

		return array_combine( $formatted_statuses, $formatted_statuses );
	}

	/**
	 * Return an object defining the currecy options for the site's current currency
	 *
	 * @return  array  Settings for the current currency {
	 *     Array of settings.
	 *
	 *     @type string $code       Currency code.
	 *     @type string $precision  Number of decimals.
	 *     @type string $symbol     Symbol for currency.
	 * }
	 */
	public static function get_currency_settings() {
		$code = get_woocommerce_currency();

		//phpcs:ignore
		return apply_filters(
			'wc_currency_settings',
			array(
				'code'              => $code,
				'precision'         => wc_get_price_decimals(),
				'symbol'            => html_entity_decode( get_woocommerce_currency_symbol( $code ) ),
				'symbolPosition'    => get_option( 'woocommerce_currency_pos' ),
				'decimalSeparator'  => wc_get_price_decimal_separator(),
				'thousandSeparator' => wc_get_price_thousand_separator(),
				'priceFormat'       => html_entity_decode( get_woocommerce_price_format() ),
			)
		);
	}

	/**
	 * Hooks extra neccessary data into the component settings array already set in WooCommerce core.
	 *
	 * @param array $settings Array of component settings.
	 * @return array Array of component settings.
	 */
	public function add_component_settings( $settings ) {
		if ( ! is_admin() ) {
			return $settings;
		}

		if ( ! function_exists( 'wc_blocks_container' ) ) {
			global $wp_locale;
			// inject data not available via older versions of wc_blocks/woo.
			$settings['orderStatuses'] = self::get_order_statuses( wc_get_order_statuses() );
			$settings['stockStatuses'] = self::get_order_statuses( wc_get_product_stock_status_options() );
			$settings['currency']      = self::get_currency_settings();
			$settings['locale']        = array(
				'siteLocale'    => isset( $settings['siteLocale'] )
					? $settings['siteLocale']
					: get_locale(),
				'userLocale'    => isset( $settings['l10n']['userLocale'] )
					? $settings['l10n']['userLocale']
					: get_user_locale(),
				'weekdaysShort' => isset( $settings['l10n']['weekdaysShort'] )
					? $settings['l10n']['weekdaysShort']
					: array_values( $wp_locale->weekday_abbrev ),
			);
		}

		//phpcs:ignore
		$preload_data_endpoints = apply_filters( 'woocommerce_component_settings_preload_endpoints', array() );
		if ( class_exists( 'Jetpack' ) ) {
			$preload_data_endpoints['jetpackStatus'] = '/jetpack/v4/connection';
		}
		if ( ! empty( $preload_data_endpoints ) ) {
			$preload_data = array_reduce(
				array_values( $preload_data_endpoints ),
				'rest_preload_api_request'
			);
		}

		//phpcs:ignore
		$preload_options = apply_filters( 'woocommerce_admin_preload_options', array() );
		if ( ! empty( $preload_options ) ) {
			foreach ( $preload_options as $option ) {
				$settings['preloadOptions'][ $option ] = get_option( $option );
			}
		}

		//phpcs:ignore
		$preload_settings = apply_filters( 'woocommerce_admin_preload_settings', array() );
		if ( ! empty( $preload_settings ) ) {
			$setting_options = new \WC_REST_Setting_Options_V2_Controller();
			foreach ( $preload_settings as $group ) {
				$group_settings   = $setting_options->get_group_settings( $group );
				$preload_settings = array();
				foreach ( $group_settings as $option ) {
					if ( array_key_exists( 'id', $option ) && array_key_exists( 'value', $option ) ) {
						$preload_settings[ $option['id'] ] = $option['value'];
					}
				}
				$settings['preloadSettings'][ $group ] = $preload_settings;
			}
		}

		$user_controller = new \WP_REST_Users_Controller();
		$request         = new \WP_REST_Request();
		$request->set_query_params( array( 'context' => 'edit' ) );
		$user_response     = $user_controller->get_current_item( $request );
		$current_user_data = is_wp_error( $user_response ) ? (object) array() : $user_response->get_data();

		$settings['currentUserData']      = $current_user_data;
		$settings['reviewsEnabled']       = get_option( 'woocommerce_enable_reviews' );
		$settings['manageStock']          = get_option( 'woocommerce_manage_stock' );
		$settings['commentModeration']    = get_option( 'comment_moderation' );
		$settings['notifyLowStockAmount'] = get_option( 'woocommerce_notify_low_stock_amount' );

		/**
		 * Deprecate wcAdminAssetUrl as we no longer need it after The Merge.
		 * Use wcAssetUrl instead.
		 *
		 * @deprecated 6.7.0
		 * @var string
		 */
		$settings['wcAdminAssetUrl'] = WC_ADMIN_IMAGES_FOLDER_URL;
		$settings['wcVersion']       = WC_VERSION;
		$settings['siteUrl']         = site_url();
		$settings['shopUrl']         = get_permalink( wc_get_page_id( 'shop' ) );
		$settings['homeUrl']         = home_url();
		$settings['dateFormat']      = get_option( 'date_format' );
		$settings['timeZone']        = wc_timezone_string();
		$settings['plugins']         = array(
			'installedPlugins' => PluginsHelper::get_installed_plugin_slugs(),
			'activePlugins'    => Plugins::get_active_plugins(),
		);
		// Plugins that depend on changing the translation work on the server but not the client -
		// WooCommerce Branding is an example of this - so pass through the translation of
		// 'WooCommerce' to wcSettings.
		$settings['woocommerceTranslation'] = __( 'WooCommerce', 'woocommerce' );
		// We may have synced orders with a now-unregistered status.
		// E.g An extension that added statuses is now inactive or removed.
		$settings['unregisteredOrderStatuses'] = $this->get_unregistered_order_statuses();
		// The separator used for attributes found in Variation titles.
		//phpcs:ignore
		$settings['variationTitleAttributesSeparator'] = apply_filters( 'woocommerce_product_variation_title_attributes_separator', ' - ', new \WC_Product() );

		if ( ! empty( $preload_data_endpoints ) ) {
			$settings['dataEndpoints'] = isset( $settings['dataEndpoints'] )
				? $settings['dataEndpoints']
				: array();
			foreach ( $preload_data_endpoints as $key => $endpoint ) {
				// Handle error case: rest_do_request() doesn't guarantee success.
				if ( empty( $preload_data[ $endpoint ] ) ) {
					$settings['dataEndpoints'][ $key ] = array();
				} else {
					$settings['dataEndpoints'][ $key ] = $preload_data[ $endpoint ]['body'];
				}
			}
		}
		$settings = $this->get_custom_settings( $settings );
		if ( PageController::is_embed_page() ) {
			$settings['embedBreadcrumbs'] = wc_admin_get_breadcrumbs();
		}

		$settings['allowMarketplaceSuggestions']      = WC_Marketplace_Suggestions::allow_suggestions();
		$settings['connectNonce']                     = wp_create_nonce( 'connect' );
		$settings['wcpay_welcome_page_connect_nonce'] = wp_create_nonce( 'wcpay-connect' );

		return $settings;
	}

	/**
	 * Register the admin settings for use in the WC REST API
	 *
	 * @param array $groups Array of setting groups.
	 * @return array
	 */
	public function add_settings_group( $groups ) {
		$groups[] = array(
			'id'          => 'wc_admin',
			'label'       => __( 'WooCommerce Admin', 'woocommerce' ),
			'description' => __( 'Settings for WooCommerce admin reporting.', 'woocommerce' ),
		);
		return $groups;
	}

	/**
	 * Add WC Admin specific settings
	 *
	 * @param array $settings Array of settings in wc admin group.
	 * @return array
	 */
	public function add_settings( $settings ) {
		$unregistered_statuses = $this->get_unregistered_order_statuses();
		$registered_statuses   = self::get_order_statuses( wc_get_order_statuses() );
		$all_statuses          = array_merge( $unregistered_statuses, $registered_statuses );

		$settings[] = array(
			'id'          => 'woocommerce_excluded_report_order_statuses',
			'option_key'  => 'woocommerce_excluded_report_order_statuses',
			'label'       => __( 'Excluded report order statuses', 'woocommerce' ),
			'description' => __( 'Statuses that should not be included when calculating report totals.', 'woocommerce' ),
			'default'     => array( 'pending', 'cancelled', 'failed' ),
			'type'        => 'multiselect',
			'options'     => $all_statuses,
		);
		$settings[] = array(
			'id'          => 'woocommerce_actionable_order_statuses',
			'option_key'  => 'woocommerce_actionable_order_statuses',
			'label'       => __( 'Actionable order statuses', 'woocommerce' ),
			'description' => __( 'Statuses that require extra action on behalf of the store admin.', 'woocommerce' ),
			'default'     => array( 'processing', 'on-hold' ),
			'type'        => 'multiselect',
			'options'     => $all_statuses,
		);
		$settings[] = array(
			'id'          => 'woocommerce_default_date_range',
			'option_key'  => 'woocommerce_default_date_range',
			'label'       => __( 'Default Date Range', 'woocommerce' ),
			'description' => __( 'Default Date Range', 'woocommerce' ),
			'default'     => 'period=month&compare=previous_year',
			'type'        => 'text',
		);
		$settings[] = array(
			'id'          => 'woocommerce_date_type',
			'option_key'  => 'woocommerce_date_type',
			'label'       => __( 'Date Type', 'woocommerce' ),
			'description' => __( 'Database date field considered for Revenue and Orders reports', 'woocommerce' ),
			'type'        => 'select',
			'options'     => array(
				'date_created'   => 'date_created',
				'date_paid'      => 'date_paid',
				'date_completed' => 'date_completed',
			),
		);
		return $settings;
	}

	/**
	 * Gets custom settings used for WC Admin.
	 *
	 * @param array $settings Array of settings to merge into.
	 * @return array
	 */
	private function get_custom_settings( $settings ) {
		$wc_rest_settings_options_controller = new \WC_REST_Setting_Options_Controller();
		$wc_admin_group_settings             = $wc_rest_settings_options_controller->get_group_settings( 'wc_admin' );
		$settings['wcAdminSettings']         = array();

		foreach ( $wc_admin_group_settings as $setting ) {
			if ( ! empty( $setting['id'] ) ) {
				$settings['wcAdminSettings'][ $setting['id'] ] = $setting['value'];
			}
		}
		return $settings;
	}
}

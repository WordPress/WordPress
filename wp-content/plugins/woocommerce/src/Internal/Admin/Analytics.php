<?php
/**
 * WooCommerce Analytics.
 */

namespace Automattic\WooCommerce\Internal\Admin;

use Automattic\WooCommerce\Admin\API\Reports\Cache;
use Automattic\WooCommerce\Admin\Features\Features;

/**
 * Contains backend logic for the Analytics feature.
 */
class Analytics {
	/**
	 * Option name used to toggle this feature.
	 */
	const TOGGLE_OPTION_NAME = 'woocommerce_analytics_enabled';
	/**
	 * Clear cache tool identifier.
	 */
	const CACHE_TOOL_ID = 'clear_woocommerce_analytics_cache';

	/**
	 * Class instance.
	 *
	 * @var Analytics instance
	 */
	protected static $instance = null;

	/**
	 * Determines if the feature has been toggled on or off.
	 *
	 * @var boolean
	 */
	protected static $is_updated = false;

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
		add_action( 'update_option_' . self::TOGGLE_OPTION_NAME, array( $this, 'reload_page_on_toggle' ), 10, 2 );
		add_action( 'woocommerce_settings_saved', array( $this, 'maybe_reload_page' ) );

		if ( ! Features::is_enabled( 'analytics' ) ) {
			return;
		}

		add_filter( 'woocommerce_component_settings_preload_endpoints', array( $this, 'add_preload_endpoints' ) );
		add_filter( 'woocommerce_admin_get_user_data_fields', array( $this, 'add_user_data_fields' ) );
		add_action( 'admin_menu', array( $this, 'register_pages' ) );
		add_filter( 'woocommerce_debug_tools', array( $this, 'register_cache_clear_tool' ) );
	}

	/**
	 * Add the feature toggle to the features settings.
	 *
	 * @deprecated 7.0 The WooCommerce Admin features are now handled by the WooCommerce features engine (see the FeaturesController class).
	 *
	 * @param array $features Feature sections.
	 * @return array
	 */
	public static function add_feature_toggle( $features ) {
		return $features;
	}

	/**
	 * Reloads the page when the option is toggled to make sure all Analytics features are loaded.
	 *
	 * @param string $old_value Old value.
	 * @param string $value     New value.
	 */
	public static function reload_page_on_toggle( $old_value, $value ) {
		if ( $old_value === $value ) {
			return;
		}

		self::$is_updated = true;
	}

	/**
	 * Reload the page if the setting has been updated.
	 */
	public static function maybe_reload_page() {
		if ( ! isset( $_SERVER['REQUEST_URI'] ) || ! self::$is_updated ) {
			return;
		}

		wp_safe_redirect( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		exit();
	}

	/**
	 * Preload data from the countries endpoint.
	 *
	 * @param array $endpoints Array of preloaded endpoints.
	 * @return array
	 */
	public function add_preload_endpoints( $endpoints ) {
		$endpoints['performanceIndicators'] = '/wc-analytics/reports/performance-indicators/allowed';
		$endpoints['leaderboards']          = '/wc-analytics/leaderboards/allowed';
		return $endpoints;
	}

	/**
	 * Adds fields so that we can store user preferences for the columns to display on a report.
	 *
	 * @param array $user_data_fields User data fields.
	 * @return array
	 */
	public function add_user_data_fields( $user_data_fields ) {
		return array_merge(
			$user_data_fields,
			array(
				'categories_report_columns',
				'coupons_report_columns',
				'customers_report_columns',
				'orders_report_columns',
				'products_report_columns',
				'revenue_report_columns',
				'taxes_report_columns',
				'variations_report_columns',
				'dashboard_sections',
				'dashboard_chart_type',
				'dashboard_chart_interval',
				'dashboard_leaderboard_rows',
			)
		);
	}

	/**
	 * Register the cache clearing tool on the WooCommerce > Status > Tools page.
	 *
	 * @param array $debug_tools Available debug tool registrations.
	 * @return array Filtered debug tool registrations.
	 */
	public function register_cache_clear_tool( $debug_tools ) {
		$settings_url = add_query_arg(
			array(
				'page' => 'wc-admin',
				'path' => '/analytics/settings',
			),
			get_admin_url( null, 'admin.php' )
		);

		$debug_tools[ self::CACHE_TOOL_ID ] = array(
			'name'     => __( 'Clear analytics cache', 'woocommerce' ),
			'button'   => __( 'Clear', 'woocommerce' ),
			'desc'     => sprintf(
				/* translators: 1: opening link tag, 2: closing tag */
				__( 'This tool will reset the cached values used in WooCommerce Analytics. If numbers still look off, try %1$sReimporting Historical Data%2$s.', 'woocommerce' ),
				'<a href="' . esc_url( $settings_url ) . '">',
				'</a>'
			),
			'callback' => array( $this, 'run_clear_cache_tool' ),
		);

		return $debug_tools;
	}

	/**
	 * Registers report pages.
	 */
	public function register_pages() {
		$report_pages = self::get_report_pages();
		foreach ( $report_pages as $report_page ) {
			if ( ! is_null( $report_page ) ) {
				wc_admin_register_page( $report_page );
			}
		}
	}

	/**
	 * Get report pages.
	 */
	public static function get_report_pages() {
		$overview_page = array(
			'id'       => 'woocommerce-analytics',
			'title'    => __( 'Analytics', 'woocommerce' ),
			'path'     => '/analytics/overview',
			'icon'     => 'dashicons-chart-bar',
			'position' => 57, // After WooCommerce & Product menu items.
		);

		$report_pages = array(
			$overview_page,
			array(
				'id'       => 'woocommerce-analytics-overview',
				'title'    => __( 'Overview', 'woocommerce' ),
				'parent'   => 'woocommerce-analytics',
				'path'     => '/analytics/overview',
				'nav_args' => array(
					'order'  => 10,
					'parent' => 'woocommerce-analytics',
				),
			),
			array(
				'id'       => 'woocommerce-analytics-products',
				'title'    => __( 'Products', 'woocommerce' ),
				'parent'   => 'woocommerce-analytics',
				'path'     => '/analytics/products',
				'nav_args' => array(
					'order'  => 20,
					'parent' => 'woocommerce-analytics',
				),
			),
			array(
				'id'       => 'woocommerce-analytics-revenue',
				'title'    => __( 'Revenue', 'woocommerce' ),
				'parent'   => 'woocommerce-analytics',
				'path'     => '/analytics/revenue',
				'nav_args' => array(
					'order'  => 30,
					'parent' => 'woocommerce-analytics',
				),
			),
			array(
				'id'       => 'woocommerce-analytics-orders',
				'title'    => __( 'Orders', 'woocommerce' ),
				'parent'   => 'woocommerce-analytics',
				'path'     => '/analytics/orders',
				'nav_args' => array(
					'order'  => 40,
					'parent' => 'woocommerce-analytics',
				),
			),
			array(
				'id'       => 'woocommerce-analytics-variations',
				'title'    => __( 'Variations', 'woocommerce' ),
				'parent'   => 'woocommerce-analytics',
				'path'     => '/analytics/variations',
				'nav_args' => array(
					'order'  => 50,
					'parent' => 'woocommerce-analytics',
				),
			),
			array(
				'id'       => 'woocommerce-analytics-categories',
				'title'    => __( 'Categories', 'woocommerce' ),
				'parent'   => 'woocommerce-analytics',
				'path'     => '/analytics/categories',
				'nav_args' => array(
					'order'  => 60,
					'parent' => 'woocommerce-analytics',
				),
			),
			array(
				'id'       => 'woocommerce-analytics-coupons',
				'title'    => __( 'Coupons', 'woocommerce' ),
				'parent'   => 'woocommerce-analytics',
				'path'     => '/analytics/coupons',
				'nav_args' => array(
					'order'  => 70,
					'parent' => 'woocommerce-analytics',
				),
			),
			array(
				'id'       => 'woocommerce-analytics-taxes',
				'title'    => __( 'Taxes', 'woocommerce' ),
				'parent'   => 'woocommerce-analytics',
				'path'     => '/analytics/taxes',
				'nav_args' => array(
					'order'  => 80,
					'parent' => 'woocommerce-analytics',
				),
			),
			array(
				'id'       => 'woocommerce-analytics-downloads',
				'title'    => __( 'Downloads', 'woocommerce' ),
				'parent'   => 'woocommerce-analytics',
				'path'     => '/analytics/downloads',
				'nav_args' => array(
					'order'  => 90,
					'parent' => 'woocommerce-analytics',
				),
			),
			'yes' === get_option( 'woocommerce_manage_stock' ) ? array(
				'id'       => 'woocommerce-analytics-stock',
				'title'    => __( 'Stock', 'woocommerce' ),
				'parent'   => 'woocommerce-analytics',
				'path'     => '/analytics/stock',
				'nav_args' => array(
					'order'  => 100,
					'parent' => 'woocommerce-analytics',
				),
			) : null,
			array(
				'id'     => 'woocommerce-analytics-customers',
				'title'  => __( 'Customers', 'woocommerce' ),
				'parent' => 'woocommerce',
				'path'   => '/customers',
			),
			array(
				'id'       => 'woocommerce-analytics-settings',
				'title'    => __( 'Settings', 'woocommerce' ),
				'parent'   => 'woocommerce-analytics',
				'path'     => '/analytics/settings',
				'nav_args' => array(
					'title'  => __( 'Analytics', 'woocommerce' ),
					'parent' => 'woocommerce-settings',
				),
			),
		);

		/**
		 * The analytics report items used in the menu.
		 *
		 * @since 6.4.0
		 */
		return apply_filters( 'woocommerce_analytics_report_menu_items', $report_pages );
	}

	/**
	 * "Clear" analytics cache by invalidating it.
	 */
	public function run_clear_cache_tool() {
		Cache::invalidate();

		return __( 'Analytics cache cleared.', 'woocommerce' );
	}
}

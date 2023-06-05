<?php
/**
 * WooCommerce Homescreen.
 */

namespace Automattic\WooCommerce\Internal\Admin;

use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\Shipping;

/**
 * Contains backend logic for the homescreen feature.
 */
class Homescreen {
	/**
	 * Menu slug.
	 */
	const MENU_SLUG = 'wc-admin';

	/**
	 * Class instance.
	 *
	 * @var Homescreen instance
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
		add_filter( 'woocommerce_admin_get_user_data_fields', array( $this, 'add_user_data_fields' ) );
		add_action( 'admin_menu', array( $this, 'register_page' ) );
		// In WC Core 5.1 $submenu manipulation occurs in admin_menu, not admin_head. See https://github.com/woocommerce/woocommerce/pull/29088.
		if ( version_compare( WC_VERSION, '5.1', '>=' ) ) {
			// priority is 20 to run after admin_menu hook for woocommerce runs, so that submenu is populated.
			add_action( 'admin_menu', array( $this, 'possibly_remove_woocommerce_menu' ) );
			add_action( 'admin_menu', array( $this, 'update_link_structure' ), 20 );
		} else {
			// priority is 20 to run after https://github.com/woocommerce/woocommerce/blob/a55ae325306fc2179149ba9b97e66f32f84fdd9c/includes/admin/class-wc-admin-menus.php#L165.
			add_action( 'admin_head', array( $this, 'update_link_structure' ), 20 );
		}

		add_filter( 'woocommerce_admin_preload_options', array( $this, 'preload_options' ) );

		if ( Features::is_enabled( 'shipping-smart-defaults' ) ) {
			add_filter(
				'woocommerce_admin_shared_settings',
				array( $this, 'maybe_set_default_shipping_options_on_home' ),
				9999
			);
		}
	}

	/**
	 * Set free shipping in the same country as the store default
	 * Flag rate in all other countries when any of the following conditions are ture
	 *
	 * - The store sells physical products, has JP and WCS installed and connected, and is located in the US.
	 * - The store sells physical products, and is not located in US/Canada/Australia/UK (irrelevant if JP is installed or not).
	 * - The store sells physical products and is located in US, but JP and WCS are not installed.
	 *
	 * @param array $settings shared admin settings.
	 * @return array
	 */
	public function maybe_set_default_shipping_options_on_home( $settings ) {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return $settings;
		}

		$current_screen = get_current_screen();

		// Abort if it's not the homescreen.
		if ( ! isset( $current_screen->id ) || 'woocommerce_page_wc-admin' !== $current_screen->id ) {
			return $settings;
		}

		// Abort if we already created the shipping options.
		$already_created = get_option( 'woocommerce_admin_created_default_shipping_zones' );
		if ( $already_created === 'yes' ) {
			return $settings;
		}

		$zone_count = count( \WC_Data_Store::load( 'shipping-zone' )->get_zones() );
		if ( $zone_count ) {
			update_option( 'woocommerce_admin_created_default_shipping_zones', 'yes' );
			update_option( 'woocommerce_admin_reviewed_default_shipping_zones', 'yes' );
			return $settings;
		}

		$user_skipped_obw           = $settings['onboarding']['profile']['skipped'] ?? false;
		$store_address              = $settings['preloadSettings']['general']['woocommerce_store_address'] ?? '';
		$product_types              = $settings['onboarding']['profile']['product_types'] ?? array();
		$user_has_set_store_country = $settings['onboarding']['profile']['is_store_country_set'] ?? false;

		// Do not proceed if user has not filled out their country in the onboarding profiler.
		if ( ! $user_has_set_store_country ) {
			return $settings;
		}

		// If user skipped the obw or has not completed the store_details
		// then we assume the user is going to sell physical products.
		if ( $user_skipped_obw || '' === $store_address ) {
			$product_types[] = 'physical';
		}

		if ( false === in_array( 'physical', $product_types, true ) ) {
			return $settings;
		}

		$country_code = wc_format_country_state_string( $settings['preloadSettings']['general']['woocommerce_default_country'] )['country'];
		$country_name = WC()->countries->get_countries()[ $country_code ] ?? null;

		$is_jetpack_installed = in_array( 'jetpack', $settings['plugins']['installedPlugins'] ?? array(), true );
		$is_wcs_installed     = in_array( 'woocommerce-services', $settings['plugins']['installedPlugins'] ?? array(), true );

		if (
			( 'US' === $country_code && $is_jetpack_installed )
			||
			( ! in_array( $country_code, array( 'CA', 'AU', 'GB', 'ES', 'IT', 'DE', 'FR', 'MX', 'CO', 'CL', 'AR', 'PE', 'BR', 'UY', 'GT', 'NL', 'AT', 'BE' ), true ) )
			||
			( 'US' === $country_code && false === $is_jetpack_installed && false === $is_wcs_installed )
		) {
			$zone = new \WC_Shipping_Zone();
			$zone->set_zone_name( $country_name );
			$zone->add_location( $country_code, 'country' );
			$zone->add_shipping_method( 'free_shipping' );
			update_option( 'woocommerce_admin_created_default_shipping_zones', 'yes' );
			Shipping::delete_zone_count_transient();
		}

		return $settings;
	}

	/**
	 * Adds fields so that we can store performance indicators, row settings, and chart type settings for users.
	 *
	 * @param array $user_data_fields User data fields.
	 * @return array
	 */
	public function add_user_data_fields( $user_data_fields ) {
		return array_merge(
			$user_data_fields,
			array(
				'homepage_layout',
				'homepage_stats',
				'task_list_tracked_started_tasks',
				'help_panel_highlight_shown',
			)
		);
	}

	/**
	 * Registers home page.
	 */
	public function register_page() {
		// Register a top-level item for users who cannot view the core WooCommerce menu.
		if ( ! self::is_admin_user() ) {
			wc_admin_register_page(
				array(
					'id'         => 'woocommerce-home',
					'title'      => __( 'WooCommerce', 'woocommerce' ),
					'path'       => self::MENU_SLUG,
					'capability' => 'read',
				)
			);
			return;
		}

		wc_admin_register_page(
			array(
				'id'         => 'woocommerce-home',
				'title'      => __( 'Home', 'woocommerce' ),
				'parent'     => 'woocommerce',
				'path'       => self::MENU_SLUG,
				'order'      => 0,
				'capability' => 'read',
			)
		);
	}

	/**
	 * Check if the user can access the top-level WooCommerce item.
	 *
	 * @return bool
	 */
	public static function is_admin_user() {
		if ( ! class_exists( 'WC_Admin_Menus', false ) ) {
			include_once WC_ABSPATH . 'includes/admin/class-wc-admin-menus.php';
		}
		if ( method_exists( 'WC_Admin_Menus', 'can_view_woocommerce_menu_item' ) ) {
			return \WC_Admin_Menus::can_view_woocommerce_menu_item() || current_user_can( 'manage_woocommerce' );
		} else {
			// We leave this line for WC versions <= 6.2.
			return current_user_can( 'edit_others_shop_orders' ) || current_user_can( 'manage_woocommerce' );
		}
	}

	/**
	 * Possibly remove the WooCommerce menu item if it was purely used to access wc-admin pages.
	 */
	public function possibly_remove_woocommerce_menu() {
		global $menu;

		if ( self::is_admin_user() ) {
			return;
		}

		foreach ( $menu as $key => $menu_item ) {
			if ( self::MENU_SLUG !== $menu_item[2] || 'read' !== $menu_item[1] ) {
				continue;
			}

			unset( $menu[ $key ] );
		}
	}

	/**
	 * Update the WooCommerce menu structure to make our main dashboard/handler
	 * the top level link for 'WooCommerce'.
	 */
	public function update_link_structure() {
		global $submenu;
		// User does not have capabilites to see the submenu.
		if ( ! current_user_can( 'manage_woocommerce' ) || empty( $submenu['woocommerce'] ) ) {
			return;
		}

		$wc_admin_key = null;
		foreach ( $submenu['woocommerce'] as $submenu_key => $submenu_item ) {
			if ( self::MENU_SLUG === $submenu_item[2] ) {
				$wc_admin_key = $submenu_key;
				break;
			}
		}

		if ( ! $wc_admin_key ) {
			return;
		}

		$menu = $submenu['woocommerce'][ $wc_admin_key ];

		// Move menu item to top of array.
		unset( $submenu['woocommerce'][ $wc_admin_key ] );
		array_unshift( $submenu['woocommerce'], $menu );
	}

	/**
	 * Preload options to prime state of the application.
	 *
	 * @param array $options Array of options to preload.
	 * @return array
	 */
	public function preload_options( $options ) {
		$options[] = 'woocommerce_default_homepage_layout';
		$options[] = 'woocommerce_admin_install_timestamp';

		return $options;
	}
}

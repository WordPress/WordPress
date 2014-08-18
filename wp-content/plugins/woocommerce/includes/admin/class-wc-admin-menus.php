<?php
/**
 * Setup menus in WP admin.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Admin_Menus' ) ) :

/**
 * WC_Admin_Menus Class
 */
class WC_Admin_Menus {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Add menus
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'reports_menu' ), 20 );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 50 );
		add_action( 'admin_menu', array( $this, 'status_menu' ), 60 );

		if ( apply_filters( 'woocommerce_show_addons_page', true ) ) {
			add_action( 'admin_menu', array( $this, 'addons_menu' ), 70 );
		}

		add_action( 'admin_head', array( $this, 'menu_highlight' ) );
		add_filter( 'menu_order', array( $this, 'menu_order' ) );
		add_filter( 'custom_menu_order', array( $this, 'custom_menu_order' ) );
	}

	/**
	 * Add menu items
	 */
	public function admin_menu() {
		global $menu, $woocommerce;

	    if ( current_user_can( 'manage_woocommerce' ) )
	    	$menu[] = array( '', 'read', 'separator-woocommerce', '', 'wp-menu-separator woocommerce' );

	    $main_page = add_menu_page( __( 'WooCommerce', 'woocommerce' ), __( 'WooCommerce', 'woocommerce' ), 'manage_woocommerce', 'woocommerce' , array( $this, 'settings_page' ), null, '55.5' );

	    add_submenu_page( 'edit.php?post_type=product', __( 'Attributes', 'woocommerce' ), __( 'Attributes', 'woocommerce' ), 'manage_product_terms', 'product_attributes', array( $this, 'attributes_page' ) );
	}

	/**
	 * Add menu item
	 */
	public function reports_menu() {
		add_submenu_page( 'woocommerce', __( 'Reports', 'woocommerce' ),  __( 'Reports', 'woocommerce' ) , 'view_woocommerce_reports', 'wc-reports', array( $this, 'reports_page' ) );
	}

	/**
	 * Add menu item
	 */
	public function settings_menu() {
		$settings_page = add_submenu_page( 'woocommerce', __( 'WooCommerce Settings', 'woocommerce' ),  __( 'Settings', 'woocommerce' ) , 'manage_woocommerce', 'wc-settings', array( $this, 'settings_page' ) );

		add_action( 'load-' . $settings_page, array( $this, 'settings_page_init' ) );
	}

	/**
	 * Loads gateways and shipping methods into memory for use within settings.
	 */
	public function settings_page_init() {
		WC()->payment_gateways();
		WC()->shipping();
	}

	/**
	 * Add menu item
	 */
	public function status_menu() {
		add_submenu_page( 'woocommerce', __( 'WooCommerce Status', 'woocommerce' ),  __( 'System Status', 'woocommerce' ) , 'manage_woocommerce', 'wc-status', array( $this, 'status_page' ) );
		register_setting( 'woocommerce_status_settings_fields', 'woocommerce_status_options' );
	}

	/**
	 * Addons menu item
	 */
	public function addons_menu() {
		add_submenu_page( 'woocommerce', __( 'WooCommerce Add-ons/Extensions', 'woocommerce' ),  __( 'Add-ons', 'woocommerce' ) , 'manage_woocommerce', 'wc-addons', array( $this, 'addons_page' ) );
	}

	/**
	 * Highlights the correct top level admin menu item for post type add screens.
	 *
	 * @access public
	 * @return void
	 */
	public function menu_highlight() {
		global $menu, $submenu, $parent_file, $submenu_file, $self, $post_type, $taxonomy;

		$to_highlight_types = array( 'shop_order', 'shop_coupon' );

		if ( isset( $post_type ) ) {
			if ( in_array( $post_type, $to_highlight_types ) ) {
				$submenu_file = 'edit.php?post_type=' . esc_attr( $post_type );
				$parent_file  = 'woocommerce';
			}

			if ( 'product' == $post_type ) {
				$screen = get_current_screen();

				if ( $screen->base == 'edit-tags' && taxonomy_is_product_attribute( $taxonomy ) ) {
					$submenu_file = 'product_attributes';
					$parent_file  = 'edit.php?post_type=' . esc_attr( $post_type );
				}
			}
		}

		if ( isset( $submenu['woocommerce'] ) && isset( $submenu['woocommerce'][1] ) ) {
			$submenu['woocommerce'][0] = $submenu['woocommerce'][1];
			unset( $submenu['woocommerce'][1] );
		}

		// Sort out Orders menu when on the top level
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			foreach ( $menu as $key => $menu_item ) {
				if ( strpos( $menu_item[0], _x('Orders', 'Admin menu name', 'woocommerce') ) === 0 ) {

					$menu_name = _x('Orders', 'Admin menu name', 'woocommerce');
					$menu_name_count = '';
					if ( $order_count = wc_processing_order_count() ) {
						$menu_name_count = " <span class='awaiting-mod update-plugins count-$order_count'><span class='processing-count'>" . number_format_i18n( $order_count ) . "</span></span>" ;
					}

					$menu[$key][0] = $menu_name . $menu_name_count;
					$submenu['edit.php?post_type=shop_order'][5][0] = $menu_name;
					break;
				}
			}
		}
	}

	/**
	 * Reorder the WC menu items in admin.
	 *
	 * @param mixed $menu_order
	 * @return array
	 */
	public function menu_order( $menu_order ) {
		// Initialize our custom order array
		$woocommerce_menu_order = array();

		// Get the index of our custom separator
		$woocommerce_separator = array_search( 'separator-woocommerce', $menu_order );

		// Get index of product menu
		$woocommerce_product = array_search( 'edit.php?post_type=product', $menu_order );

		// Loop through menu order and do some rearranging
		foreach ( $menu_order as $index => $item ) :

			if ( ( ( 'woocommerce' ) == $item ) ) :
				$woocommerce_menu_order[] = 'separator-woocommerce';
				$woocommerce_menu_order[] = $item;
				$woocommerce_menu_order[] = 'edit.php?post_type=product';
				unset( $menu_order[$woocommerce_separator] );
				unset( $menu_order[$woocommerce_product] );
			elseif ( !in_array( $item, array( 'separator-woocommerce' ) ) ) :
				$woocommerce_menu_order[] = $item;
			endif;

		endforeach;

		// Return order
		return $woocommerce_menu_order;
	}

	/**
	 * custom_menu_order
	 * @return bool
	 */
	public function custom_menu_order() {
		if ( ! current_user_can( 'manage_woocommerce' ) )
			return false;
		return true;
	}

	/**
	 * Init the reports page
	 */
	public function reports_page() {
		include_once( 'class-wc-admin-reports.php' );
		WC_Admin_Reports::output();
	}

	/**
	 * Init the settings page
	 */
	public function settings_page() {
		include_once( 'class-wc-admin-settings.php' );
		WC_Admin_Settings::output();
	}

	/**
	 * Init the attributes page
	 */
	public function attributes_page() {
		$page = include( 'class-wc-admin-attributes.php' );
		$page->output();
	}

	/**
	 * Init the status page
	 */
	public function status_page() {
		$page = include( 'class-wc-admin-status.php' );
		$page->output();
	}

	/**
	 * Init the addons page
	 */
	public function addons_page() {
		$page = include( 'class-wc-admin-addons.php' );
		$page->output();
	}
}

endif;

return new WC_Admin_Menus();
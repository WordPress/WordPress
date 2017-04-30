<?php
/**
 * Load assets.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Admin_Assets' ) ) :

/**
 * WC_Admin_Assets Class
 */
class WC_Admin_Assets {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_head', array( $this, 'product_taxonomy_styles' ) );
	}

	/**
	 * Enqueue styles
	 */
	public function admin_styles() {
		global $wp_scripts;

		// Sitewide menu CSS
		wp_enqueue_style( 'woocommerce_admin_menu_styles', WC()->plugin_url() . '/assets/css/menu.css', array(), WC_VERSION );

		$screen = get_current_screen();

		if ( in_array( $screen->id, wc_get_screen_ids() ) ) {

			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

			// Admin styles for WC pages only
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
			wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), WC_VERSION );
			wp_enqueue_style( 'wp-color-picker' );
		}

		if ( in_array( $screen->id, array( 'dashboard' ) ) ) {
			wp_enqueue_style( 'woocommerce_admin_dashboard_styles', WC()->plugin_url() . '/assets/css/dashboard.css', array(), WC_VERSION );
		}

		do_action( 'woocommerce_admin_css' );
	}


	/**
	 * Enqueue scripts
	 */
	public function admin_scripts() {
		global $wp_query, $post;

		$screen       = get_current_screen();
		$wc_screen_id = sanitize_title( __( 'WooCommerce', 'woocommerce' ) );
		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register scripts
		wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), WC_VERSION );

		wp_register_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.66', true );

		wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );

		wp_register_script( 'accounting', WC()->plugin_url() . '/assets/js/admin/accounting' . $suffix . '.js', array( 'jquery' ), '0.3.2' );

		wp_register_script( 'round', WC()->plugin_url() . '/assets/js/admin/round' . $suffix . '.js', array( 'jquery' ), WC_VERSION );

		wp_register_script( 'woocommerce_admin_meta_boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'accounting', 'round' ), WC_VERSION );

		wp_register_script( 'woocommerce_admin_meta_boxes_variations', WC()->plugin_url() . '/assets/js/admin/meta-boxes-variations' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable' ), WC_VERSION );

		wp_register_script( 'ajax-chosen', WC()->plugin_url() . '/assets/js/chosen/ajax-chosen.jquery' . $suffix . '.js', array('jquery', 'chosen'), WC_VERSION );

		wp_register_script( 'chosen', WC()->plugin_url() . '/assets/js/chosen/chosen.jquery' . $suffix . '.js', array('jquery'), WC_VERSION );

		// Accounting
    	$params = array(
			'mon_decimal_point' => get_option( 'woocommerce_price_decimal_sep' )
    	);

    	wp_localize_script( 'accounting', 'accounting_params', $params );

		// WooCommerce admin pages
	    if ( in_array( $screen->id, wc_get_screen_ids() ) ) {

	    	wp_enqueue_script( 'woocommerce_admin' );
	    	wp_enqueue_script( 'iris' );
	    	wp_enqueue_script( 'ajax-chosen' );
	    	wp_enqueue_script( 'chosen' );
	    	wp_enqueue_script( 'jquery-ui-sortable' );
	    	wp_enqueue_script( 'jquery-ui-autocomplete' );

	    	$locale  = localeconv();
	    	$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';

	    	$params = array(
				'i18n_decimal_error'     => sprintf( __( 'Please enter in decimal (%s) format without thousand separators.', 'woocommerce' ), $decimal ),
				'i18n_mon_decimal_error' => sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency symbols.', 'woocommerce' ), get_option( 'woocommerce_price_decimal_sep' ) ),
				'decimal_point'          => $decimal,
				'mon_decimal_point'      => get_option( 'woocommerce_price_decimal_sep' )
	    	);

	    	wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );
	    }

	    // Edit product category pages
	    if ( in_array( $screen->id, array( 'edit-product_cat' ) ) )
			wp_enqueue_media();

		// Products
		if ( in_array( $screen->id, array( 'edit-product' ) ) )
			wp_enqueue_script( 'woocommerce_quick-edit', WC()->plugin_url() . '/assets/js/admin/quick-edit' . $suffix . '.js', array('jquery'), WC_VERSION );

		// Product/Coupon/Orders
		if ( in_array( $screen->id, array( 'shop_coupon', 'shop_order', 'product', 'edit-shop_coupon', 'edit-shop_order', 'edit-product' ) ) ) {

			wp_enqueue_script( 'woocommerce_admin_meta_boxes' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_media();
			wp_enqueue_script( 'ajax-chosen' );
			wp_enqueue_script( 'chosen' );
			wp_enqueue_script( 'plupload-all' );

			$params = array(
				'remove_item_notice' 			=> __( 'Are you sure you want to remove the selected items? If you have previously reduced this item\'s stock, or this order was submitted by a customer, you will need to manually restore the item\'s stock.', 'woocommerce' ),
				'i18n_select_items'				=> __( 'Please select some items.', 'woocommerce' ),
				'remove_item_meta'				=> __( 'Remove this item meta?', 'woocommerce' ),
				'remove_attribute'				=> __( 'Remove this attribute?', 'woocommerce' ),
				'name_label'					=> __( 'Name', 'woocommerce' ),
				'remove_label'					=> __( 'Remove', 'woocommerce' ),
				'click_to_toggle'				=> __( 'Click to toggle', 'woocommerce' ),
				'values_label'					=> __( 'Value(s)', 'woocommerce' ),
				'text_attribute_tip'			=> __( 'Enter some text, or some attributes by pipe (|) separating values.', 'woocommerce' ),
				'visible_label'					=> __( 'Visible on the product page', 'woocommerce' ),
				'used_for_variations_label'		=> __( 'Used for variations', 'woocommerce' ),
				'new_attribute_prompt'			=> __( 'Enter a name for the new attribute term:', 'woocommerce' ),
				'calc_totals' 					=> __( 'Calculate totals based on order items, discounts, and shipping?', 'woocommerce' ),
				'calc_line_taxes' 				=> __( 'Calculate line taxes? This will calculate taxes based on the customers country. If no billing/shipping is set it will use the store base country.', 'woocommerce' ),
				'copy_billing' 					=> __( 'Copy billing information to shipping information? This will remove any currently entered shipping information.', 'woocommerce' ),
				'load_billing' 					=> __( 'Load the customer\'s billing information? This will remove any currently entered billing information.', 'woocommerce' ),
				'load_shipping' 				=> __( 'Load the customer\'s shipping information? This will remove any currently entered shipping information.', 'woocommerce' ),
				'featured_label'				=> __( 'Featured', 'woocommerce' ),
				'prices_include_tax' 			=> esc_attr( get_option('woocommerce_prices_include_tax') ),
				'round_at_subtotal'				=> esc_attr( get_option( 'woocommerce_tax_round_at_subtotal' ) ),
				'no_customer_selected'			=> __( 'No customer selected', 'woocommerce' ),
				'plugin_url' 					=> WC()->plugin_url(),
				'ajax_url' 						=> admin_url('admin-ajax.php'),
				'order_item_nonce' 				=> wp_create_nonce("order-item"),
				'add_attribute_nonce' 			=> wp_create_nonce("add-attribute"),
				'save_attributes_nonce' 		=> wp_create_nonce("save-attributes"),
				'calc_totals_nonce' 			=> wp_create_nonce("calc-totals"),
				'get_customer_details_nonce' 	=> wp_create_nonce("get-customer-details"),
				'search_products_nonce' 		=> wp_create_nonce("search-products"),
				'grant_access_nonce'			=> wp_create_nonce("grant-access"),
				'revoke_access_nonce'			=> wp_create_nonce("revoke-access"),
				'add_order_note_nonce'			=> wp_create_nonce("add-order-note"),
				'delete_order_note_nonce'		=> wp_create_nonce("delete-order-note"),
				'calendar_image'				=> WC()->plugin_url().'/assets/images/calendar.png',
				'post_id'						=> isset( $post->ID ) ? $post->ID : '',
				'base_country'					=> WC()->countries->get_base_country(),
				'currency_format_num_decimals'	=> absint( get_option( 'woocommerce_price_num_decimals' ) ),
				'currency_format_symbol'		=> get_woocommerce_currency_symbol(),
				'currency_format_decimal_sep'	=> esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
				'currency_format_thousand_sep'	=> esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
				'currency_format'				=> esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
				'rounding_precision'            => WC_ROUNDING_PRECISION,
				'tax_rounding_mode'             => WC_TAX_ROUNDING_MODE,
				'product_types'					=> array_map( 'sanitize_title', get_terms( 'product_type', array( 'hide_empty' => false, 'fields' => 'names' ) ) ),
				'default_attribute_visibility'  => apply_filters( 'default_attribute_visibility', false ),
				'default_attribute_variation'   => apply_filters( 'default_attribute_variation', false ),
				'i18n_download_permission_fail' => __( 'Could not grant access - the user may already have permission for this file or billing email is not set. Ensure the billing email is set, and the order has been saved.', 'woocommerce' ),
				'i18n_permission_revoke'		=> __( 'Are you sure you want to revoke access to this download?', 'woocommerce' ),
			);

			wp_localize_script( 'woocommerce_admin_meta_boxes', 'woocommerce_admin_meta_boxes', $params );
		}

		// Product specific
		if ( in_array( $screen->id, array( 'product', 'edit-product' ) ) ) {

			wp_enqueue_script( 'woocommerce_admin_meta_boxes_variations' );

			$params = array(
				'post_id'                             => isset( $post->ID ) ? $post->ID : '',
				'plugin_url'                          => WC()->plugin_url(),
				'ajax_url'                            => admin_url('admin-ajax.php'),
				'woocommerce_placeholder_img_src'     => wc_placeholder_img_src(),
				'add_variation_nonce'                 => wp_create_nonce("add-variation"),
				'link_variation_nonce'                => wp_create_nonce("link-variations"),
				'delete_variation_nonce'              => wp_create_nonce("delete-variation"),
				'delete_variations_nonce'             => wp_create_nonce("delete-variations"),
				'i18n_link_all_variations'            => esc_js( __( 'Are you sure you want to link all variations? This will create a new variation for each and every possible combination of variation attributes (max 50 per run).', 'woocommerce' ) ),
				'i18n_enter_a_value'                  => esc_js( __( 'Enter a value', 'woocommerce' ) ),
				'i18n_enter_a_value_fixed_or_percent' => esc_js( __( 'Enter a value (fixed or %)', 'woocommerce' ) ),
				'i18n_delete_all_variations'          => esc_js( __( 'Are you sure you want to delete all variations? This cannot be undone.', 'woocommerce' ) ),
				'i18n_last_warning'                   => esc_js( __( 'Last warning, are you sure?', 'woocommerce' ) ),
				'i18n_choose_image'                   => esc_js( __( 'Choose an image', 'woocommerce' ) ),
				'i18n_set_image'                      => esc_js( __( 'Set variation image', 'woocommerce' ) ),
				'i18n_variation_added'                => esc_js( __( "variation added", 'woocommerce' ) ),
				'i18n_variations_added'               => esc_js( __( "variations added", 'woocommerce' ) ),
				'i18n_no_variations_added'            => esc_js( __( "No variations added", 'woocommerce' ) ),
				'i18n_remove_variation'               => esc_js( __( 'Are you sure you want to remove this variation?', 'woocommerce' ) )
			);

			wp_localize_script( 'woocommerce_admin_meta_boxes_variations', 'woocommerce_admin_meta_boxes_variations', $params );
		}

		// Term ordering - only when sorting by term_order
		if ( ( strstr( $screen->id, 'edit-pa_' ) || ( ! empty( $_GET['taxonomy'] ) && in_array( $_GET['taxonomy'], apply_filters( 'woocommerce_sortable_taxonomies', array( 'product_cat' ) ) ) ) ) && ! isset( $_GET['orderby'] ) ) {

			wp_register_script( 'woocommerce_term_ordering', WC()->plugin_url() . '/assets/js/admin/term-ordering.js', array('jquery-ui-sortable'), WC_VERSION );
			wp_enqueue_script( 'woocommerce_term_ordering' );

			$taxonomy = isset( $_GET['taxonomy'] ) ? wc_clean( $_GET['taxonomy'] ) : '';

			$woocommerce_term_order_params = array(
				'taxonomy' 			=>  $taxonomy
			 );

			wp_localize_script( 'woocommerce_term_ordering', 'woocommerce_term_ordering_params', $woocommerce_term_order_params );
		}

		// Product sorting - only when sorting by menu order on the products page
		if ( current_user_can('edit_others_pages') && $screen->id == 'edit-product' && isset( $wp_query->query['orderby'] ) && $wp_query->query['orderby'] == 'menu_order title' ) {

			wp_enqueue_script( 'woocommerce_product_ordering', WC()->plugin_url() . '/assets/js/admin/product-ordering.js', array('jquery-ui-sortable'), WC_VERSION, true );

		}

		// Reports Pages
		if ( in_array( $screen->id, apply_filters( 'woocommerce_reports_screen_ids', array( $wc_screen_id . '_page_wc-reports', 'dashboard' ) ) ) ) {
			wp_enqueue_script( 'wc-reports', WC()->plugin_url() . '/assets/js/admin/reports' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker' ), WC_VERSION );
			wp_enqueue_script( 'flot', WC()->plugin_url() . '/assets/js/admin/jquery.flot' . $suffix . '.js', array( 'jquery' ), WC_VERSION );
			wp_enqueue_script( 'flot-resize', WC()->plugin_url() . '/assets/js/admin/jquery.flot.resize' . $suffix . '.js', array('jquery', 'flot'), WC_VERSION );
			wp_enqueue_script( 'flot-time', WC()->plugin_url() . '/assets/js/admin/jquery.flot.time' . $suffix . '.js', array( 'jquery', 'flot' ), WC_VERSION );
			wp_enqueue_script( 'flot-pie', WC()->plugin_url() . '/assets/js/admin/jquery.flot.pie' . $suffix . '.js', array( 'jquery', 'flot' ), WC_VERSION );
			wp_enqueue_script( 'flot-stack', WC()->plugin_url() . '/assets/js/admin/jquery.flot.stack' . $suffix . '.js', array( 'jquery', 'flot' ), WC_VERSION );
		}

		// Chosen RTL
		if ( is_rtl() ) {
			wp_enqueue_script( 'chosen-rtl', WC()->plugin_url() . '/assets/js/chosen/chosen-rtl' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
		}
	}

	/**
	 * Admin Head
	 *
	 * Outputs some styles in the admin <head> to show icons on the woocommerce admin pages
	 *
	 * @access public
	 * @return void
	 */
	public function product_taxonomy_styles() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) return;
		?>
		<style type="text/css">
			<?php if ( isset($_GET['taxonomy']) && $_GET['taxonomy']=='product_cat' ) : ?>
				.icon32-posts-product { background-position: -243px -5px !important; }
			<?php elseif ( isset($_GET['taxonomy']) && $_GET['taxonomy']=='product_tag' ) : ?>
				.icon32-posts-product { background-position: -301px -5px !important; }
			<?php endif; ?>
		</style>
		<?php
	}
}

endif;

return new WC_Admin_Assets();

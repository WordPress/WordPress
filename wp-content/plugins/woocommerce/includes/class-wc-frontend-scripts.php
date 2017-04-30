<?php
/**
 * Handle frontend forms
 *
 * @class 		WC_Frontend_Scripts
 * @version		2.1.0
 * @package		WooCommerce/Classes/
 * @category	Class
 * @author 		WooThemes
 */
class WC_Frontend_Scripts {

	/**
	 * Constructor
	 */
	public function __construct () {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'wp_print_scripts', array( $this, 'check_jquery' ), 25 );
		add_filter( 'woocommerce_enqueue_styles', array( $this, 'backwards_compat' ) );
	}

	/**
	 * Get styles for the frontend
	 * @return array
	 */
	public static function get_styles() {
		return apply_filters( 'woocommerce_enqueue_styles', array(
			'woocommerce-layout' => array(
				'src'     => str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/woocommerce-layout.css',
				'deps'    => '',
				'version' => WC_VERSION,
				'media'   => 'all'
			),
			'woocommerce-smallscreen' => array(
				'src'     => str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/woocommerce-smallscreen.css',
				'deps'    => 'woocommerce-layout',
				'version' => WC_VERSION,
				'media'   => 'only screen and (max-width: ' . apply_filters( 'woocommerce_style_smallscreen_breakpoint', $breakpoint = '768px' ) . ')'
			),
			'woocommerce-general' => array(
				'src'     => str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/woocommerce.css',
				'deps'    => '',
				'version' => WC_VERSION,
				'media'   => 'all'
			),
		) );
	}

	/**
	 * Register/queue frontend scripts.
	 *
	 * @access public
	 * @return void
	 */
	public function load_scripts() {
		global $post, $wp;

		$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$lightbox_en          = get_option( 'woocommerce_enable_lightbox' ) == 'yes' ? true : false;
		$ajax_cart_en         = get_option( 'woocommerce_enable_ajax_add_to_cart' ) == 'yes' ? true : false;
		$assets_path          = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
		$frontend_script_path = $assets_path . 'js/frontend/';

		// Register any scripts for later use, or used as dependencies
		wp_register_script( 'chosen', $assets_path . 'js/chosen/chosen.jquery' . $suffix . '.js', array( 'jquery' ), '1.0.0', true );
		wp_register_script( 'jquery-blockui', $assets_path . 'js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.60', true );
		wp_register_script( 'jquery-payment', $assets_path . 'js/jquery-payment/jquery.payment' . $suffix . '.js', array( 'jquery' ), '1.0.2', true );
		wp_register_script( 'wc-credit-card-form', $assets_path . 'js/frontend/credit-card-form' . $suffix . '.js', array( 'jquery', 'jquery-payment' ), WC_VERSION, true );

		wp_register_script( 'wc-add-to-cart-variation', $frontend_script_path . 'add-to-cart-variation' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
		wp_register_script( 'wc-single-product', $frontend_script_path . 'single-product' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
		wp_register_script( 'wc-country-select', $frontend_script_path . 'country-select' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
		wp_register_script( 'wc-address-i18n', $frontend_script_path . 'address-i18n' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
		wp_register_script( 'jquery-cookie', $assets_path . 'js/jquery-cookie/jquery.cookie' . $suffix . '.js', array( 'jquery' ), '1.3.1', true );

		// Queue frontend scripts conditionally
		if ( $ajax_cart_en )
			wp_enqueue_script( 'wc-add-to-cart', $frontend_script_path . 'add-to-cart' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );

		if ( is_cart() )
			wp_enqueue_script( 'wc-cart', $frontend_script_path . 'cart' . $suffix . '.js', array( 'jquery', 'wc-country-select' ), WC_VERSION, true );

		if ( is_checkout() ) {

			if ( get_option( 'woocommerce_enable_chosen' ) == 'yes' ) {
				wp_enqueue_script( 'wc-chosen', $frontend_script_path . 'chosen-frontend' . $suffix . '.js', array( 'chosen' ), WC_VERSION, true );
				wp_enqueue_style( 'woocommerce_chosen_styles', $assets_path . 'css/chosen.css' );
			}

			wp_enqueue_script( 'wc-checkout', $frontend_script_path . 'checkout' . $suffix . '.js', array( 'jquery', 'woocommerce', 'wc-country-select', 'wc-address-i18n' ), WC_VERSION, true );
		}

		if ( is_page( get_option( 'woocommerce_myaccount_page_id' ) ) ) {
			if ( get_option( 'woocommerce_enable_chosen' ) == 'yes' ) {
				wp_enqueue_script( 'wc-chosen', $frontend_script_path . 'chosen-frontend' . $suffix . '.js', array( 'chosen' ), WC_VERSION, true );
				wp_enqueue_style( 'woocommerce_chosen_styles', $assets_path . 'css/chosen.css' );
			}
		}

		if ( is_add_payment_method_page() )
			wp_enqueue_script( 'wc-add-payment-method', $frontend_script_path . 'add-payment-method' . $suffix . '.js', array( 'jquery', 'woocommerce' ), WC_VERSION, true );

		if ( $lightbox_en && ( is_product() || ( ! empty( $post->post_content ) && strstr( $post->post_content, '[product_page' ) ) ) ) {
			wp_enqueue_script( 'prettyPhoto', $assets_path . 'js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.5', true );
			wp_enqueue_script( 'prettyPhoto-init', $assets_path . 'js/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js', array( 'jquery','prettyPhoto' ), WC_VERSION, true );
			wp_enqueue_style( 'woocommerce_prettyPhoto_css', $assets_path . 'css/prettyPhoto.css' );
		}

		if ( is_product() )
			wp_enqueue_script( 'wc-single-product' );

		// Global frontend scripts
		wp_enqueue_script( 'woocommerce', $frontend_script_path . 'woocommerce' . $suffix . '.js', array( 'jquery', 'jquery-blockui' ), WC_VERSION, true );
		wp_enqueue_script( 'wc-cart-fragments', $frontend_script_path . 'cart-fragments' . $suffix . '.js', array( 'jquery', 'jquery-cookie' ), WC_VERSION, true );

		// Variables for JS scripts
		wp_localize_script( 'woocommerce', 'woocommerce_params', apply_filters( 'woocommerce_params', array(
			'ajax_url'        => WC()->ajax_url(),
			'ajax_loader_url' => apply_filters( 'woocommerce_ajax_loader_url', $assets_path . 'images/ajax-loader@2x.gif' ),
		) ) );

		wp_localize_script( 'wc-single-product', 'wc_single_product_params', apply_filters( 'wc_single_product_params', array(
			'i18n_required_rating_text' => esc_attr__( 'Please select a rating', 'woocommerce' ),
			'review_rating_required'    => get_option( 'woocommerce_review_rating_required' ),
		) ) );

		wp_localize_script( 'wc-checkout', 'wc_checkout_params', apply_filters( 'wc_checkout_params', array(
			'ajax_url'                  => WC()->ajax_url(),
			'ajax_loader_url'           => apply_filters( 'woocommerce_ajax_loader_url', $assets_path . 'images/ajax-loader@2x.gif' ),
			'update_order_review_nonce' => wp_create_nonce( "update-order-review" ),
			'apply_coupon_nonce'        => wp_create_nonce( "apply-coupon" ),
			'option_guest_checkout'     => get_option( 'woocommerce_enable_guest_checkout' ),
			'checkout_url'              => add_query_arg( 'action', 'woocommerce_checkout', WC()->ajax_url() ),
			'is_checkout'               => is_page( wc_get_page_id( 'checkout' ) ) && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) ? 1 : 0
		) ) );

		wp_localize_script( 'wc-address-i18n', 'wc_address_i18n_params', apply_filters( 'wc_address_i18n_params', array(
			'locale'                    => json_encode( WC()->countries->get_country_locale() ),
			'locale_fields'             => json_encode( WC()->countries->get_country_locale_field_selectors() ),
			'i18n_required_text'        => esc_attr__( 'required', 'woocommerce' ),
		) ) );

		wp_localize_script( 'wc-cart', 'wc_cart_params', apply_filters( 'wc_cart_params', array(
			'ajax_url'                     => WC()->ajax_url(),
			'ajax_loader_url'              => apply_filters( 'woocommerce_ajax_loader_url', $assets_path . 'images/ajax-loader@2x.gif' ),
			'update_shipping_method_nonce' => wp_create_nonce( "update-shipping-method" ),
		) ) );

		wp_localize_script( 'wc-cart-fragments', 'wc_cart_fragments_params', apply_filters( 'wc_cart_fragments_params', array(
			'ajax_url'      => WC()->ajax_url(),
			'fragment_name' => apply_filters( 'woocommerce_cart_fragment_name', 'wc_fragments' )
		) ) );

		wp_localize_script( 'wc-add-to-cart', 'wc_add_to_cart_params', apply_filters( 'wc_add_to_cart_params', array(
			'ajax_url'                => WC()->ajax_url(),
			'ajax_loader_url'         => apply_filters( 'woocommerce_ajax_loader_url', $assets_path . 'images/ajax-loader@2x.gif' ),
			'i18n_view_cart'          => esc_attr__( 'View Cart', 'woocommerce' ),
			'cart_url'                => get_permalink( wc_get_page_id( 'cart' ) ),
			'is_cart'                 => is_cart(),
			'cart_redirect_after_add' => get_option( 'woocommerce_cart_redirect_after_add' )
		) ) );

		wp_localize_script( 'wc-add-to-cart-variation', 'wc_add_to_cart_variation_params', apply_filters( 'wc_add_to_cart_variation_params', array(
			'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ),
			'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ),
		) ) );

		wp_localize_script( 'wc-country-select', 'wc_country_select_params', apply_filters( 'wc_country_select_params', array(
			'countries'              => json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
			'i18n_select_state_text' => esc_attr__( 'Select an option&hellip;', 'woocommerce' ),
		) ) );

		// CSS Styles
		$enqueue_styles = $this->get_styles();

		if ( $enqueue_styles )
			foreach ( $enqueue_styles as $handle => $args )
				wp_enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
	}

	/**
	 * WC requires jQuery 1.8 since it uses functions like .on() for events and .parseHTML.
	 * If, by the time wp_print_scrips is called, jQuery is outdated (i.e not
	 * using the version in core) we need to deregister it and register the
	 * core version of the file.
	 *
	 * @access public
	 * @return void
	 */
	public function check_jquery() {
		global $wp_scripts;

		// Enforce minimum version of jQuery
		if ( ! empty( $wp_scripts->registered['jquery']->ver ) && ! empty( $wp_scripts->registered['jquery']->src ) && 0 >= version_compare( $wp_scripts->registered['jquery']->ver, '1.8' ) ) {
			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', '/wp-includes/js/jquery/jquery.js', array(), '1.8' );
			wp_enqueue_script( 'jquery' );
		}
	}

	/**
	 * Provide backwards compat for old constant
	 * @param  array $styles
	 * @return array
	 */
	public function backwards_compat( $styles ) {
		if ( defined( 'WOOCOMMERCE_USE_CSS' ) ) {

			_deprecated_function( 'WOOCOMMERCE_USE_CSS', '2.1', 'Styles should be removed using wp_deregister_style or the woocommerce_enqueue_styles filter rather than the WOOCOMMERCE_USE_CSS constant.' );

			if ( ! WOOCOMMERCE_USE_CSS )
				return false;
		}

		return $styles;
	}
}

new WC_Frontend_Scripts();

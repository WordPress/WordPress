<?php
/**
 * Handle frontend scripts
 *
 * @package WooCommerce\Classes
 * @version 2.3.0
 */

 // phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment

use Automattic\Jetpack\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend scripts class.
 */
class WC_Frontend_Scripts {

	/**
	 * Contains an array of script handles registered by WC.
	 *
	 * @var array
	 */
	private static $scripts = array();

	/**
	 * Contains an array of script handles registered by WC.
	 *
	 * @var array
	 */
	private static $styles = array();

	/**
	 * Contains an array of script handles localized by WC.
	 *
	 * @var array
	 */
	private static $wp_localize_scripts = array();

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		add_action( 'wp_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
		add_action( 'wp_print_footer_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
	}

	/**
	 * Get styles for the frontend.
	 *
	 * @return array
	 */
	public static function get_styles() {
		$version = Constants::get_constant( 'WC_VERSION' );

		/**
		 * Filter list of WooCommerce styles to enqueue.
		 *
		 * @since 2.1.0
		 * @param array List of default WooCommerce styles.
		 * @return array List of styles to enqueue.
		 */
		$styles = apply_filters(
			'woocommerce_enqueue_styles',
			array(
				'woocommerce-layout'      => array(
					'src'     => self::get_asset_url( 'assets/css/woocommerce-layout.css' ),
					'deps'    => '',
					'version' => $version,
					'media'   => 'all',
					'has_rtl' => true,
				),
				'woocommerce-smallscreen' => array(
					'src'     => self::get_asset_url( 'assets/css/woocommerce-smallscreen.css' ),
					'deps'    => 'woocommerce-layout',
					'version' => $version,
					'media'   => 'only screen and (max-width: ' . apply_filters( 'woocommerce_style_smallscreen_breakpoint', '768px' ) . ')',
					'has_rtl' => true,
				),
				'woocommerce-general'     => array(
					'src'     => self::get_asset_url( 'assets/css/woocommerce.css' ),
					'deps'    => '',
					'version' => $version,
					'media'   => 'all',
					'has_rtl' => true,
				),
				'woocommerce-blocktheme'  => wc_current_theme_is_fse_theme() ? array(
					'src'     => self::get_asset_url( 'assets/css/woocommerce-blocktheme.css' ),
					'deps'    => '',
					'version' => $version,
					'media'   => 'all',
					'has_rtl' => true,
				) : false,
			)
		);
		return is_array( $styles ) ? array_filter( $styles ) : array();
	}

	/**
	 * Return asset URL.
	 *
	 * @param string $path Assets path.
	 * @return string
	 */
	private static function get_asset_url( $path ) {
		return apply_filters( 'woocommerce_get_asset_url', plugins_url( $path, WC_PLUGIN_FILE ), $path );
	}

	/**
	 * Register a script for use.
	 *
	 * @uses   wp_register_script()
	 * @param  string   $handle    Name of the script. Should be unique.
	 * @param  string   $path      Full URL of the script, or path of the script relative to the WordPress root directory.
	 * @param  string[] $deps      An array of registered script handles this script depends on.
	 * @param  string   $version   String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  boolean  $in_footer Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
	 */
	private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = WC_VERSION, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}

	/**
	 * Register and enqueue a script for use.
	 *
	 * @uses   wp_enqueue_script()
	 * @param  string   $handle    Name of the script. Should be unique.
	 * @param  string   $path      Full URL of the script, or path of the script relative to the WordPress root directory.
	 * @param  string[] $deps      An array of registered script handles this script depends on.
	 * @param  string   $version   String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  boolean  $in_footer Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
	 */
	private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = WC_VERSION, $in_footer = true ) {
		if ( ! in_array( $handle, self::$scripts, true ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}

	/**
	 * Register a style for use.
	 *
	 * @uses   wp_register_style()
	 * @param  string   $handle  Name of the stylesheet. Should be unique.
	 * @param  string   $path    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
	 * @param  string[] $deps    An array of registered stylesheet handles this stylesheet depends on.
	 * @param  string   $version String specifying stylesheet version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  string   $media   The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
	 * @param  boolean  $has_rtl If has RTL version to load too.
	 */
	private static function register_style( $handle, $path, $deps = array(), $version = WC_VERSION, $media = 'all', $has_rtl = false ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $path, $deps, $version, $media );

		if ( $has_rtl ) {
			wp_style_add_data( $handle, 'rtl', 'replace' );
		}
	}

	/**
	 * Register and enqueue a styles for use.
	 *
	 * @uses   wp_enqueue_style()
	 * @param  string   $handle  Name of the stylesheet. Should be unique.
	 * @param  string   $path    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
	 * @param  string[] $deps    An array of registered stylesheet handles this stylesheet depends on.
	 * @param  string   $version String specifying stylesheet version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  string   $media   The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
	 * @param  boolean  $has_rtl If has RTL version to load too.
	 */
	private static function enqueue_style( $handle, $path = '', $deps = array(), $version = WC_VERSION, $media = 'all', $has_rtl = false ) {
		if ( ! in_array( $handle, self::$styles, true ) && $path ) {
			self::register_style( $handle, $path, $deps, $version, $media, $has_rtl );
		}
		wp_enqueue_style( $handle );
	}

	/**
	 * Register all WC scripts.
	 */
	private static function register_scripts() {
		$suffix  = Constants::is_true( 'SCRIPT_DEBUG' ) ? '' : '.min';
		$version = Constants::get_constant( 'WC_VERSION' );

		$register_scripts = array(
			'flexslider'                 => array(
				'src'     => self::get_asset_url( 'assets/js/flexslider/jquery.flexslider' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => '2.7.2-wc.' . $version,
			),
			'js-cookie'                  => array(
				'src'     => self::get_asset_url( 'assets/js/js-cookie/js.cookie' . $suffix . '.js' ),
				'deps'    => array(),
				'version' => '2.1.4-wc.' . $version,
			),
			'jquery-blockui'             => array(
				'src'     => self::get_asset_url( 'assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => '2.7.0-wc.' . $version,
			),
			'jquery-cookie'              => array( // deprecated.
				'src'     => self::get_asset_url( 'assets/js/jquery-cookie/jquery.cookie' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => '1.4.1-wc.' . $version,
			),
			'jquery-payment'             => array(
				'src'     => self::get_asset_url( 'assets/js/jquery-payment/jquery.payment' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => '3.0.0-wc.' . $version,
			),
			'photoswipe'                 => array(
				'src'     => self::get_asset_url( 'assets/js/photoswipe/photoswipe' . $suffix . '.js' ),
				'deps'    => array(),
				'version' => '4.1.1-wc.' . $version,
			),
			'photoswipe-ui-default'      => array(
				'src'     => self::get_asset_url( 'assets/js/photoswipe/photoswipe-ui-default' . $suffix . '.js' ),
				'deps'    => array( 'photoswipe' ),
				'version' => '4.1.1-wc.' . $version,
			),
			'prettyPhoto'                => array( // deprecated.
				'src'     => self::get_asset_url( 'assets/js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => '3.1.6-wc.' . $version,
			),
			'prettyPhoto-init'           => array( // deprecated.
				'src'     => self::get_asset_url( 'assets/js/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js' ),
				'deps'    => array( 'jquery', 'prettyPhoto' ),
				'version' => $version,
			),
			'select2'                    => array(
				'src'     => self::get_asset_url( 'assets/js/select2/select2.full' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => '4.0.3-wc.' . $version,
			),
			'selectWoo'                  => array(
				'src'     => self::get_asset_url( 'assets/js/selectWoo/selectWoo.full' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => '1.0.9-wc.' . $version,
			),
			'wc-address-i18n'            => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/address-i18n' . $suffix . '.js' ),
				'deps'    => array( 'jquery', 'wc-country-select' ),
				'version' => $version,
			),
			'wc-add-payment-method'      => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/add-payment-method' . $suffix . '.js' ),
				'deps'    => array( 'jquery', 'woocommerce' ),
				'version' => $version,
			),
			'wc-cart'                    => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/cart' . $suffix . '.js' ),
				'deps'    => array( 'jquery', 'woocommerce', 'wc-country-select', 'wc-address-i18n' ),
				'version' => $version,
			),
			'wc-cart-fragments'          => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/cart-fragments' . $suffix . '.js' ),
				'deps'    => array( 'jquery', 'js-cookie' ),
				'version' => $version,
			),
			'wc-checkout'                => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/checkout' . $suffix . '.js' ),
				'deps'    => array( 'jquery', 'woocommerce', 'wc-country-select', 'wc-address-i18n' ),
				'version' => $version,
			),
			'wc-country-select'          => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/country-select' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => $version,
			),
			'wc-credit-card-form'        => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/credit-card-form' . $suffix . '.js' ),
				'deps'    => array( 'jquery', 'jquery-payment' ),
				'version' => $version,
			),
			'wc-add-to-cart'             => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/add-to-cart' . $suffix . '.js' ),
				'deps'    => array( 'jquery', 'jquery-blockui' ),
				'version' => $version,
			),
			'wc-add-to-cart-variation'   => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/add-to-cart-variation' . $suffix . '.js' ),
				'deps'    => array( 'jquery', 'wp-util', 'jquery-blockui' ),
				'version' => $version,
			),
			'wc-geolocation'             => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/geolocation' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => $version,
			),
			'wc-lost-password'           => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/lost-password' . $suffix . '.js' ),
				'deps'    => array( 'jquery', 'woocommerce' ),
				'version' => $version,
			),
			'wc-password-strength-meter' => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/password-strength-meter' . $suffix . '.js' ),
				'deps'    => array( 'jquery', 'password-strength-meter' ),
				'version' => $version,
			),
			'wc-single-product'          => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/single-product' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => $version,
			),
			'woocommerce'                => array(
				'src'     => self::get_asset_url( 'assets/js/frontend/woocommerce' . $suffix . '.js' ),
				'deps'    => array( 'jquery', 'jquery-blockui', 'js-cookie' ),
				'version' => $version,
			),
			'zoom'                       => array(
				'src'     => self::get_asset_url( 'assets/js/zoom/jquery.zoom' . $suffix . '.js' ),
				'deps'    => array( 'jquery' ),
				'version' => '1.7.21-wc.' . $version,
			),
		);
		foreach ( $register_scripts as $name => $props ) {
			self::register_script( $name, $props['src'], $props['deps'], $props['version'] );
		}
	}

	/**
	 * Register all WC styles.
	 */
	private static function register_styles() {
		$version = Constants::get_constant( 'WC_VERSION' );

		$register_styles = array(
			'photoswipe'                  => array(
				'src'     => self::get_asset_url( 'assets/css/photoswipe/photoswipe.min.css' ),
				'deps'    => array(),
				'version' => $version,
				'has_rtl' => false,
			),
			'photoswipe-default-skin'     => array(
				'src'     => self::get_asset_url( 'assets/css/photoswipe/default-skin/default-skin.min.css' ),
				'deps'    => array( 'photoswipe' ),
				'version' => $version,
				'has_rtl' => false,
			),
			'select2'                     => array(
				'src'     => self::get_asset_url( 'assets/css/select2.css' ),
				'deps'    => array(),
				'version' => $version,
				'has_rtl' => false,
			),
			'woocommerce_prettyPhoto_css' => array( // deprecated.
				'src'     => self::get_asset_url( 'assets/css/prettyPhoto.css' ),
				'deps'    => array(),
				'version' => $version,
				'has_rtl' => true,
			),
		);
		foreach ( $register_styles as $name => $props ) {
			self::register_style( $name, $props['src'], $props['deps'], $props['version'], 'all', $props['has_rtl'] );
		}
	}

	/**
	 * Register/queue frontend scripts.
	 */
	public static function load_scripts() {
		global $post;

		if ( ! did_action( 'before_woocommerce_init' ) ) {
			return;
		}

		self::register_scripts();
		self::register_styles();

		if ( 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) ) {
			self::enqueue_script( 'wc-add-to-cart' );
		}
		if ( is_cart() ) {
			self::enqueue_script( 'wc-cart' );
		}
		if ( is_cart() || is_checkout() || is_account_page() ) {
			self::enqueue_script( 'selectWoo' );
			self::enqueue_style( 'select2' );

			// Password strength meter. Load in checkout, account login and edit account page.
			if ( ( 'no' === get_option( 'woocommerce_registration_generate_password' ) && ! is_user_logged_in() ) || is_edit_account_page() || is_lost_password_page() ) {
				self::enqueue_script( 'wc-password-strength-meter' );
			}
		}
		if ( is_checkout() ) {
			self::enqueue_script( 'wc-checkout' );
		}
		if ( is_add_payment_method_page() ) {
			self::enqueue_script( 'wc-add-payment-method' );
		}
		if ( is_lost_password_page() ) {
			self::enqueue_script( 'wc-lost-password' );
		}

		// Load gallery scripts on product pages only if supported.
		if ( is_product() || ( ! empty( $post->post_content ) && strstr( $post->post_content, '[product_page' ) ) ) {
			if ( current_theme_supports( 'wc-product-gallery-zoom' ) ) {
				self::enqueue_script( 'zoom' );
			}
			if ( current_theme_supports( 'wc-product-gallery-slider' ) ) {
				self::enqueue_script( 'flexslider' );
			}
			if ( current_theme_supports( 'wc-product-gallery-lightbox' ) ) {
				self::enqueue_script( 'photoswipe-ui-default' );
				self::enqueue_style( 'photoswipe-default-skin' );
				add_action( 'wp_footer', 'woocommerce_photoswipe' );
			}
			self::enqueue_script( 'wc-single-product' );
		}

		// Only enqueue the geolocation script if the Default Current Address is set to "Geolocate
		// (with Page Caching Support) and outside of the cart, checkout, account and customizer preview.
		if (
			'geolocation_ajax' === get_option( 'woocommerce_default_customer_address' )
			&& ! ( is_cart() || is_account_page() || is_checkout() || is_customize_preview() )
		) {
			$ua = strtolower( wc_get_user_agent() ); // Exclude common bots from geolocation by user agent.

			if ( ! strstr( $ua, 'bot' ) && ! strstr( $ua, 'spider' ) && ! strstr( $ua, 'crawl' ) ) {
				self::enqueue_script( 'wc-geolocation' );
			}
		}

		// Global frontend scripts.
		self::enqueue_script( 'woocommerce' );

		// CSS Styles.
		$enqueue_styles = self::get_styles();
		if ( $enqueue_styles ) {
			foreach ( $enqueue_styles as $handle => $args ) {
				if ( ! isset( $args['has_rtl'] ) ) {
					$args['has_rtl'] = false;
				}

				self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'], $args['has_rtl'] );
			}
		}

		// Placeholder style.
		wp_register_style( 'woocommerce-inline', false ); // phpcs:ignore
		wp_enqueue_style( 'woocommerce-inline' );

		if ( true === wc_string_to_bool( get_option( 'woocommerce_checkout_highlight_required_fields', 'yes' ) ) ) {
			wp_add_inline_style( 'woocommerce-inline', '.woocommerce form .form-row .required { visibility: visible; }' );
		} else {
			wp_add_inline_style( 'woocommerce-inline', '.woocommerce form .form-row .required { visibility: hidden; }' );
		}
	}

	/**
	 * Localize a WC script once.
	 *
	 * @since 2.3.0 this needs less wp_script_is() calls due to https://core.trac.wordpress.org/ticket/28404 being added in WP 4.0.
	 * @param string $handle Script handle the data will be attached to.
	 */
	private static function localize_script( $handle ) {
		if ( ! in_array( $handle, self::$wp_localize_scripts, true ) && wp_script_is( $handle ) ) {
			$data = self::get_script_data( $handle );

			if ( ! $data ) {
				return;
			}

			$name                        = str_replace( '-', '_', $handle ) . '_params';
			self::$wp_localize_scripts[] = $handle;
			wp_localize_script( $handle, $name, apply_filters( $name, $data ) );
		}
	}

	/**
	 * Return data for script handles.
	 *
	 * @param  string $handle Script handle the data will be attached to.
	 * @return array|bool
	 */
	private static function get_script_data( $handle ) {
		global $wp;

		switch ( $handle ) {
			case 'woocommerce':
				$params = array(
					'ajax_url'    => WC()->ajax_url(),
					'wc_ajax_url' => WC_AJAX::get_endpoint( '%%endpoint%%' ),
				);
				break;
			case 'wc-geolocation':
				$params = array(
					'wc_ajax_url' => WC_AJAX::get_endpoint( '%%endpoint%%' ),
					'home_url'    => remove_query_arg( 'lang', home_url() ), // FIX for WPML compatibility.
				);
				break;
			case 'wc-single-product':
				$params = array(
					'i18n_required_rating_text' => esc_attr__( 'Please select a rating', 'woocommerce' ),
					'review_rating_required'    => wc_review_ratings_required() ? 'yes' : 'no',
					'flexslider'                => apply_filters(
						'woocommerce_single_product_carousel_options',
						array(
							'rtl'            => is_rtl(),
							'animation'      => 'slide',
							'smoothHeight'   => true,
							'directionNav'   => false,
							'controlNav'     => 'thumbnails',
							'slideshow'      => false,
							'animationSpeed' => 500,
							'animationLoop'  => false, // Breaks photoswipe pagination if true.
							'allowOneSlide'  => false,
						)
					),
					'zoom_enabled'              => apply_filters( 'woocommerce_single_product_zoom_enabled', get_theme_support( 'wc-product-gallery-zoom' ) ),
					'zoom_options'              => apply_filters( 'woocommerce_single_product_zoom_options', array() ),
					'photoswipe_enabled'        => apply_filters( 'woocommerce_single_product_photoswipe_enabled', get_theme_support( 'wc-product-gallery-lightbox' ) ),
					'photoswipe_options'        => apply_filters(
						'woocommerce_single_product_photoswipe_options',
						array(
							'shareEl'               => false,
							'closeOnScroll'         => false,
							'history'               => false,
							'hideAnimationDuration' => 0,
							'showAnimationDuration' => 0,
						)
					),
					'flexslider_enabled'        => apply_filters( 'woocommerce_single_product_flexslider_enabled', get_theme_support( 'wc-product-gallery-slider' ) ),
				);
				break;
			case 'wc-checkout':
				$params = array(
					'ajax_url'                  => WC()->ajax_url(),
					'wc_ajax_url'               => WC_AJAX::get_endpoint( '%%endpoint%%' ),
					'update_order_review_nonce' => wp_create_nonce( 'update-order-review' ),
					'apply_coupon_nonce'        => wp_create_nonce( 'apply-coupon' ),
					'remove_coupon_nonce'       => wp_create_nonce( 'remove-coupon' ),
					'option_guest_checkout'     => get_option( 'woocommerce_enable_guest_checkout' ),
					'checkout_url'              => WC_AJAX::get_endpoint( 'checkout' ),
					'is_checkout'               => is_checkout() && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) ? 1 : 0,
					'debug_mode'                => Constants::is_true( 'WP_DEBUG' ),
					'i18n_checkout_error'       => esc_attr__( 'Error processing checkout. Please try again.', 'woocommerce' ),
				);
				break;
			case 'wc-address-i18n':
				$params = array(
					'locale'             => wp_json_encode( WC()->countries->get_country_locale() ),
					'locale_fields'      => wp_json_encode( WC()->countries->get_country_locale_field_selectors() ),
					'i18n_required_text' => esc_attr__( 'required', 'woocommerce' ),
					'i18n_optional_text' => esc_html__( 'optional', 'woocommerce' ),
				);
				break;
			case 'wc-cart':
				$params = array(
					'ajax_url'                     => WC()->ajax_url(),
					'wc_ajax_url'                  => WC_AJAX::get_endpoint( '%%endpoint%%' ),
					'update_shipping_method_nonce' => wp_create_nonce( 'update-shipping-method' ),
					'apply_coupon_nonce'           => wp_create_nonce( 'apply-coupon' ),
					'remove_coupon_nonce'          => wp_create_nonce( 'remove-coupon' ),
				);
				break;
			case 'wc-cart-fragments':
				$params = array(
					'ajax_url'        => WC()->ajax_url(),
					'wc_ajax_url'     => WC_AJAX::get_endpoint( '%%endpoint%%' ),
					'cart_hash_key'   => apply_filters( 'woocommerce_cart_hash_key', 'wc_cart_hash_' . md5( get_current_blog_id() . '_' . get_site_url( get_current_blog_id(), '/' ) . get_template() ) ),
					'fragment_name'   => apply_filters( 'woocommerce_cart_fragment_name', 'wc_fragments_' . md5( get_current_blog_id() . '_' . get_site_url( get_current_blog_id(), '/' ) . get_template() ) ),
					'request_timeout' => 5000,
				);
				break;
			case 'wc-add-to-cart':
				$params = array(
					'ajax_url'                => WC()->ajax_url(),
					'wc_ajax_url'             => WC_AJAX::get_endpoint( '%%endpoint%%' ),
					'i18n_view_cart'          => esc_attr__( 'View cart', 'woocommerce' ),
					'cart_url'                => apply_filters( 'woocommerce_add_to_cart_redirect', wc_get_cart_url(), null ),
					'is_cart'                 => is_cart(),
					'cart_redirect_after_add' => get_option( 'woocommerce_cart_redirect_after_add' ),
				);
				break;
			case 'wc-add-to-cart-variation':
				// We also need the wp.template for this script :).
				wc_get_template( 'single-product/add-to-cart/variation.php' );

				$params = array(
					'wc_ajax_url'                      => WC_AJAX::get_endpoint( '%%endpoint%%' ),
					'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ),
					'i18n_make_a_selection_text'       => esc_attr__( 'Please select some product options before adding this product to your cart.', 'woocommerce' ),
					'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ),
				);
				break;
			case 'wc-country-select':
				$params = array(
					'countries'                 => wp_json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
					'i18n_select_state_text'    => esc_attr__( 'Select an option&hellip;', 'woocommerce' ),
					'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
					'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
					'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
					'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
					'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
					'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
				);
				break;
			case 'wc-password-strength-meter':
				$params = array(
					'min_password_strength' => apply_filters( 'woocommerce_min_password_strength', 3 ),
					'stop_checkout'         => apply_filters( 'woocommerce_enforce_password_strength_meter_on_checkout', false ),
					'i18n_password_error'   => esc_attr__( 'Please enter a stronger password.', 'woocommerce' ),
					'i18n_password_hint'    => esc_attr( wp_get_password_hint() ),
				);
				break;
			default:
				$params = false;
		}

		$params = apply_filters_deprecated( $handle . '_params', array( $params ), '3.0.0', 'woocommerce_get_script_data' );

		return apply_filters( 'woocommerce_get_script_data', $params, $handle );
	}

	/**
	 * Localize scripts only when enqueued.
	 */
	public static function localize_printed_scripts() {
		foreach ( self::$scripts as $handle ) {
			self::localize_script( $handle );
		}
	}
}

WC_Frontend_Scripts::init();

<?php
/**
 * Plugin Name: WooCommerce
 * Plugin URI: http://www.woothemes.com/woocommerce/
 * Description: An e-commerce toolkit that helps you sell anything. Beautifully.
 * Version: 2.1.12
 * Author: WooThemes
 * Author URI: http://woothemes.com
 * Requires at least: 3.8
 * Tested up to: 3.9
 *
 * Text Domain: woocommerce
 * Domain Path: /i18n/languages/
 *
 * @package WooCommerce
 * @category Core
 * @author WooThemes
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WooCommerce' ) ) :

/**
 * Main WooCommerce Class
 *
 * @class WooCommerce
 * @version	2.1.0
 */
final class WooCommerce {

	/**
	 * @var string
	 */
	public $version = '2.1.12';

	/**
	 * @var WooCommerce The single instance of the class
	 * @since 2.1
	 */
	protected static $_instance = null;

	/**
	 * @var WC_Session session
	 */
	public $session = null;

	/**
	 * @var WC_Query $query
	 */
	public $query = null;

	/**
	 * @var WC_Product_Factory $product_factory
	 */
	public $product_factory = null;

	/**
	 * @var WC_Countries $countries
	 */
	public $countries = null;

	/**
	 * @var WC_Integrations $integrations
	 */
	public $integrations = null;

	/**
	 * @var WC_Cart $cart
	 */
	public $cart = null;

	/**
	 * @var WC_Customer $customer
	 */
	public $customer = null;

	/**
	 * Main WooCommerce Instance
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 2.1
	 * @static
	 * @see WC()
	 * @return WooCommerce - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.1' );
	}

	/**
	 * WooCommerce Constructor.
	 * @access public
	 * @return WooCommerce
	 */
	public function __construct() {
		// Auto-load classes on demand
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		// Define constants
		$this->define_constants();

		// Include required files
		$this->includes();

		// Init API
		$this->api = new WC_API();

		// Hooks
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		add_action( 'widgets_init', array( $this, 'include_widgets' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( $this, 'include_template_functions' ) );
		add_action( 'init', array( 'WC_Shortcodes', 'init' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );

		// Loaded action
		do_action( 'woocommerce_loaded' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( method_exists( $this, $key ) ) {
			return $this->$key();
		}
		else switch( $key ) {
			case 'template_url':
				_deprecated_argument( 'Woocommerce->template_url', '2.1', 'WC_TEMPLATE_PATH constant' );
				return WC_TEMPLATE_PATH;
			case 'messages':
				_deprecated_argument( 'Woocommerce->messages', '2.1', 'Use wc_get_notices' );
				return wc_get_notices( 'success' );
			case 'errors':
				_deprecated_argument( 'Woocommerce->errors', '2.1', 'Use wc_get_notices' );
				return wc_get_notices( 'error' );
			default:
				return false;
		}
	}

	/**
	 * Show action links on the plugin screen
	 *
	 * @param mixed $links
	 * @return array
	 */
	public function action_links( $links ) {
		return array_merge( array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>',
			'<a href="' . esc_url( apply_filters( 'woocommerce_docs_url', 'http://docs.woothemes.com/documentation/plugins/woocommerce/', 'woocommerce' ) ) . '">' . __( 'Docs', 'woocommerce' ) . '</a>',
			'<a href="' . esc_url( apply_filters( 'woocommerce_support_url', 'http://support.woothemes.com/' ) ) . '">' . __( 'Premium Support', 'woocommerce' ) . '</a>',
		), $links );
	}

	/**
	 * Auto-load WC classes on demand to reduce memory consumption.
	 *
	 * @param mixed $class
	 * @return void
	 */
	public function autoload( $class ) {
		$path  = null;
		$class = strtolower( $class );
		$file = 'class-' . str_replace( '_', '-', $class ) . '.php';

		if ( strpos( $class, 'wc_gateway_' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/gateways/' . trailingslashit( substr( str_replace( '_', '-', $class ), 11 ) );
		} elseif ( strpos( $class, 'wc_shipping_' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/shipping/' . trailingslashit( substr( str_replace( '_', '-', $class ), 12 ) );
		} elseif ( strpos( $class, 'wc_shortcode_' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/shortcodes/';
		} elseif ( strpos( $class, 'wc_meta_box' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/admin/post-types/meta-boxes/';
		} elseif ( strpos( $class, 'wc_admin' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/admin/';
		}

		if ( $path && is_readable( $path . $file ) ) {
			include_once( $path . $file );
			return;
		}

		// Fallback
		if ( strpos( $class, 'wc_' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/';
		}

		if ( $path && is_readable( $path . $file ) ) {
			include_once( $path . $file );
			return;
		}
	}

	/**
	 * Define WC Constants
	 */
	private function define_constants() {
		define( 'WC_PLUGIN_FILE', __FILE__ );
		define( 'WC_VERSION', $this->version );
		define( 'WOOCOMMERCE_VERSION', WC_VERSION ); // Backwards compat

		if ( ! defined( 'WC_TEMPLATE_PATH' ) ) {
			define( 'WC_TEMPLATE_PATH', $this->template_path() );
		}
		
		if ( ! defined( 'WC_ROUNDING_PRECISION' ) ) {
			define( 'WC_ROUNDING_PRECISION', 4 );
		}

		// 1 = PHP_ROUND_HALF_UP, 2 = PHP_ROUND_HALF_DOWN
		if ( ! defined( 'WC_TAX_ROUNDING_MODE' ) ) {
			define( 'WC_TAX_ROUNDING_MODE', get_option( 'woocommerce_prices_include_tax' ) === 'yes' ? 2 : 1 ); 
		}

		if ( ! defined( 'WC_DELIMITER' ) ) {
			define( 'WC_DELIMITER', '|' );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
		include_once( 'includes/wc-core-functions.php' );
		include_once( 'includes/class-wc-install.php' );
		include_once( 'includes/class-wc-download-handler.php' );
		include_once( 'includes/class-wc-comments.php' );
		include_once( 'includes/class-wc-post-data.php' );
		include_once( 'includes/abstracts/abstract-wc-session.php' );
		include_once( 'includes/class-wc-session-handler.php' );

		if ( is_admin() ) {
			include_once( 'includes/admin/class-wc-admin.php' );
		}

		if ( defined( 'DOING_AJAX' ) ) {
			$this->ajax_includes();
		}

		if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
			$this->frontend_includes();
		}

		// Query class
		$this->query = include( 'includes/class-wc-query.php' );				// The main query class

		// Post types
		include_once( 'includes/class-wc-post-types.php' );						// Registers post types

		// API Class
		include_once( 'includes/class-wc-api.php' );

		// Include abstract classes
		include_once( 'includes/abstracts/abstract-wc-product.php' );			// Products
		include_once( 'includes/abstracts/abstract-wc-settings-api.php' );		// Settings API (for gateways, shipping, and integrations)
		include_once( 'includes/abstracts/abstract-wc-shipping-method.php' );	// A Shipping method
		include_once( 'includes/abstracts/abstract-wc-payment-gateway.php' ); 	// A Payment gateway
		include_once( 'includes/abstracts/abstract-wc-integration.php' );		// An integration with a service

		// Classes (used on all pages)
		include_once( 'includes/class-wc-product-factory.php' );				// Product factory
		include_once( 'includes/class-wc-countries.php' );						// Defines countries and states
		include_once( 'includes/class-wc-integrations.php' );					// Loads integrations
		include_once( 'includes/class-wc-cache-helper.php' );					// Cache Helper
		include_once( 'includes/class-wc-https.php' );							// https Helper

		// Include template hooks in time for themes to remove/modify them
		include_once( 'includes/wc-template-hooks.php' );
	}

	/**
	 * Include required ajax files.
	 */
	public function ajax_includes() {
		include_once( 'includes/class-wc-ajax.php' );					// Ajax functions for admin and the front-end
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		include_once( 'includes/class-wc-template-loader.php' );		// Template Loader
		include_once( 'includes/class-wc-frontend-scripts.php' );		// Frontend Scripts
		include_once( 'includes/class-wc-form-handler.php' );			// Form Handlers
		include_once( 'includes/class-wc-cart.php' );					// The main cart class
		include_once( 'includes/class-wc-tax.php' );					// Tax class
		include_once( 'includes/class-wc-customer.php' ); 				// Customer class
		include_once( 'includes/class-wc-shortcodes.php' );				// Shortcodes class
	}

	/**
	 * Function used to Init WooCommerce Template Functions - This makes them pluggable by plugins and themes.
	 */
	public function include_template_functions() {
		include_once( 'includes/wc-template-functions.php' );
	}

	/**
	 * Include core widgets
	 */
	public function include_widgets() {
		include_once( 'includes/abstracts/abstract-wc-widget.php' );
		include_once( 'includes/widgets/class-wc-widget-cart.php' );
		include_once( 'includes/widgets/class-wc-widget-products.php' );
		include_once( 'includes/widgets/class-wc-widget-layered-nav.php' );
		include_once( 'includes/widgets/class-wc-widget-layered-nav-filters.php' );
		include_once( 'includes/widgets/class-wc-widget-price-filter.php' );
		include_once( 'includes/widgets/class-wc-widget-product-categories.php' );
		include_once( 'includes/widgets/class-wc-widget-product-search.php' );
		include_once( 'includes/widgets/class-wc-widget-product-tag-cloud.php' );
		include_once( 'includes/widgets/class-wc-widget-recent-reviews.php' );
		include_once( 'includes/widgets/class-wc-widget-recently-viewed.php' );
		include_once( 'includes/widgets/class-wc-widget-top-rated-products.php' );
	}

	/**
	 * Init WooCommerce when WordPress Initialises.
	 */
	public function init() {
		// Before init action
		do_action( 'before_woocommerce_init' );

		// Set up localisation
		$this->load_plugin_textdomain();

		// Session class, handles session data for users - can be overwritten if custom handler is needed
		$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );

		// Load class instances
		$this->product_factory = new WC_Product_Factory();     // Product Factory to create new product instances
		$this->countries       = new WC_Countries();			// Countries class
		$this->integrations    = new WC_Integrations();		// Integrations class
		$this->session         = new $session_class();

		// Classes/actions loaded for the frontend and for ajax requests
		if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
			// Class instances
			$this->cart     = new WC_Cart();				// Cart class, stores the cart contents
			$this->customer = new WC_Customer();			// Customer class, handles data such as customer location
		}

		// Email Actions
		$email_actions = array(
			'woocommerce_low_stock',
			'woocommerce_no_stock',
			'woocommerce_product_on_backorder',
			'woocommerce_order_status_pending_to_processing',
			'woocommerce_order_status_pending_to_completed',
			'woocommerce_order_status_pending_to_on-hold',
			'woocommerce_order_status_failed_to_processing',
			'woocommerce_order_status_failed_to_completed',
			'woocommerce_order_status_completed',
			'woocommerce_new_customer_note',
			'woocommerce_created_customer'
		);

		foreach ( $email_actions as $action )
			add_action( $action, array( $this, 'send_transactional_email' ), 10, 10 );

		// Init action
		do_action( 'woocommerce_init' );
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce' );

		// Admin Locale
		if ( is_admin() ) {
			load_textdomain( 'woocommerce', WP_LANG_DIR . "/woocommerce/woocommerce-admin-$locale.mo" );
			load_textdomain( 'woocommerce', dirname( __FILE__ ) . "/i18n/languages/woocommerce-admin-$locale.mo" );
		}
		
		// Global + Frontend Locale
		load_textdomain( 'woocommerce', WP_LANG_DIR . "/woocommerce/woocommerce-$locale.mo" );
		load_plugin_textdomain( 'woocommerce', false, plugin_basename( dirname( __FILE__ ) ) . "/i18n/languages" );
	}

	/**
	 * Ensure theme and server variable compatibility and setup image sizes..
	 */
	public function setup_environment() {
		// Post thumbnail support
		if ( ! current_theme_supports( 'post-thumbnails', 'product' ) ) {
			add_theme_support( 'post-thumbnails' );
			remove_post_type_support( 'post', 'thumbnail' );
			remove_post_type_support( 'page', 'thumbnail' );
		} else {
			add_post_type_support( 'product', 'thumbnail' );
		}

		// Add image sizes
		$shop_thumbnail = wc_get_image_size( 'shop_thumbnail' );
		$shop_catalog	= wc_get_image_size( 'shop_catalog' );
		$shop_single	= wc_get_image_size( 'shop_single' );

		add_image_size( 'shop_thumbnail', $shop_thumbnail['width'], $shop_thumbnail['height'], $shop_thumbnail['crop'] );
		add_image_size( 'shop_catalog', $shop_catalog['width'], $shop_catalog['height'], $shop_catalog['crop'] );
		add_image_size( 'shop_single', $shop_single['width'], $shop_single['height'], $shop_single['crop'] );

		// IIS
		if ( ! isset($_SERVER['REQUEST_URI'] ) ) {
			$_SERVER['REQUEST_URI'] = substr( $_SERVER['PHP_SELF'], 1 );
			if ( isset( $_SERVER['QUERY_STRING'] ) ) {
				$_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING'];
			}
		}

		// NGINX Proxy
		if ( ! isset( $_SERVER['REMOTE_ADDR'] ) && isset( $_SERVER['HTTP_REMOTE_ADDR'] ) ) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_REMOTE_ADDR'];
		}

		if ( ! isset( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['HTTP_HTTPS'] ) ) {
			$_SERVER['HTTPS'] = $_SERVER['HTTP_HTTPS'];
		}

		// Support for hosts which don't use HTTPS, and use HTTP_X_FORWARDED_PROTO
		if ( ! isset( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
			$_SERVER['HTTPS'] = '1';
		}
	}

	/** Helper functions ******************************************************/

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'WC_TEMPLATE_PATH', 'woocommerce/' );
	}

	/**
	 * Get Ajax URL.
	 *
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	/**
	 * Return the WC API URL for a given request
	 *
	 * @param mixed $request
	 * @param mixed $ssl (default: null)
	 * @return string
	 */
	public function api_request_url( $request, $ssl = null ) {
		if ( is_null( $ssl ) ) {
			$scheme = parse_url( get_option( 'home' ), PHP_URL_SCHEME );
		} elseif ( $ssl ) {
			$scheme = 'https';
		} else {
			$scheme = 'http';
		}

		if ( get_option('permalink_structure') ) {
			return esc_url_raw( trailingslashit( home_url( '/wc-api/' . $request, $scheme ) ) );
		} else {
			return esc_url_raw( add_query_arg( 'wc-api', $request, trailingslashit( home_url( '', $scheme ) ) ) );
		}
	}

	/**
	 * Init the mailer and call the notifications for the current filter.
	 * @internal param array $args (default: array())
	 * @return void
	 */
	public function send_transactional_email() {
		$this->mailer();
		$args = func_get_args();
		do_action_ref_array( current_filter() . '_notification', $args );
	}

	/** Load Instances on demand **********************************************/

	/**
	 * Get Checkout Class.
	 *
	 * @return WC_Checkout
	 */
	public function checkout() {
		return WC_Checkout::instance();
	}

	/**
	 * Get gateways class
	 *
	 * @return WC_Payment_Gateways
	 */
	public function payment_gateways() {
		return WC_Payment_Gateways::instance();
	}

	/**
	 * Get shipping class
	 *
	 * @return WC_Shipping
	 */
	public function shipping() {
		return WC_Shipping::instance();
	}

	/**
	 * Email Class.
	 *
	 * @return WC_Email
	 */
	public function mailer() {
		return WC_Emails::instance();
	}

	/** Deprecated methods *********************************************************/

	/**
	 * @deprecated 2.1.0
	 * @param $image_size
	 * @return array
	 */
	public function get_image_size( $image_size ) {
		_deprecated_function( 'Woocommerce->get_image_size', '2.1', 'wc_get_image_size()' );
		return wc_get_image_size( $image_size );
	}

	/**
	 * @deprecated 2.1.0
	 * @return WC_Logger
	 */
	public function logger() {
		_deprecated_function( 'Woocommerce->logger', '2.1', 'new WC_Logger()' );
		return new WC_Logger();
	}

	/**
	 * @deprecated 2.1.0
	 * @return WC_Validation
	 */
	public function validation() {
		_deprecated_function( 'Woocommerce->validation', '2.1', 'new WC_Validation()' );
		return new WC_Validation();
	}

	/**
	 * @deprecated 2.1.0
	 * @param $post
	 * @return WC_Product
	 */
	public function setup_product_data( $post ) {
		_deprecated_function( 'Woocommerce->setup_product_data', '2.1', 'wc_setup_product_data' );
		return wc_setup_product_data( $post );
	}

	/**
	 * @deprecated 2.1.0
	 * @param $content
	 * @return string
	 */
	public function force_ssl( $content ) {
		_deprecated_function( 'Woocommerce->force_ssl', '2.1', 'WC_HTTPS::force_https_url' );
		return WC_HTTPS::force_https_url( $content );
	}

	/**
	 * @deprecated 2.1.0
	 * @param int $post_id
	 */
	public function clear_product_transients( $post_id = 0 ) {
		_deprecated_function( 'Woocommerce->clear_product_transients', '2.1', 'wc_delete_product_transients' );
		wc_delete_product_transients( $post_id );
	}

	/**
	 * @deprecated 2.1.0 Access via the WC_Inline_Javascript_Helper helper
	 * @param $code
	 */
	public function add_inline_js( $code ) {
		_deprecated_function( 'Woocommerce->add_inline_js', '2.1', 'wc_enqueue_js' );
		wc_enqueue_js( $code );
	}

	/**
	 * @deprecated 2.1.0
	 * @param      $action
	 * @param bool $referer
	 * @param bool $echo
	 * @return string
	 */
	public function nonce_field( $action, $referer = true , $echo = true ) {
		_deprecated_function( 'Woocommerce->nonce_field', '2.1', 'wp_nonce_field' );
		return wp_nonce_field('woocommerce-' . $action, '_wpnonce', $referer, $echo );
	}

	/**
	 * @deprecated 2.1.0
	 * @param        $action
	 * @param string $url
	 * @return string
	 */
	public function nonce_url( $action, $url = '' ) {
		_deprecated_function( 'Woocommerce->nonce_url', '2.1', 'wp_nonce_url' );
		return wp_nonce_url( $url , 'woocommerce-' . $action );
	}

	/**
	 * @deprecated 2.1.0
	 * @param        $action
	 * @param string $method
	 * @param bool   $error_message
	 * @return bool
	 */
	public function verify_nonce( $action, $method = '_POST', $error_message = false ) {
		_deprecated_function( 'Woocommerce->verify_nonce', '2.1', 'wp_verify_nonce' );
		if ( ! isset( $method[ '_wpnonce' ] ) ) {
			return false;
		}
		return wp_verify_nonce( $method[ '_wpnonce' ], 'woocommerce-' . $action );
	}

	/**
	 * @deprecated 2.1.0
	 * @param       $function
	 * @param array $atts
	 * @param array $wrapper
	 * @return string
	 */
	public function shortcode_wrapper( $function, $atts = array(), $wrapper = array( 'class' => 'woocommerce', 'before' => null, 'after' => null ) ) {
		_deprecated_function( 'Woocommerce->shortcode_wrapper', '2.1', 'WC_Shortcodes::shortcode_wrapper' );
		return WC_Shortcodes::shortcode_wrapper( $function, $atts, $wrapper );
	}

	/**
	 * @deprecated 2.1.0
	 * @return object
	 */
	public function get_attribute_taxonomies() {
		_deprecated_function( 'Woocommerce->get_attribute_taxonomies', '2.1', 'wc_get_attribute_taxonomies' );
		return wc_get_attribute_taxonomies();
	}

	/**
	 * @deprecated 2.1.0
	 * @param $name
	 * @return string
	 */
	public function attribute_taxonomy_name( $name ) {
		_deprecated_function( 'Woocommerce->attribute_taxonomy_name', '2.1', 'wc_attribute_taxonomy_name' );
		return wc_attribute_taxonomy_name( $name );
	}

	/**
	 * @deprecated 2.1.0
	 * @param $name
	 * @return string
	 */
	public function attribute_label( $name ) {
		_deprecated_function( 'Woocommerce->attribute_label', '2.1', 'wc_attribute_label' );
		return wc_attribute_label( $name );
	}

	/**
	 * @deprecated 2.1.0
	 * @param $name
	 * @return string
	 */
	public function attribute_orderby( $name ) {
		_deprecated_function( 'Woocommerce->attribute_orderby', '2.1', 'wc_attribute_orderby' );
		return wc_attribute_orderby( $name );
	}

	/**
	 * @deprecated 2.1.0
	 * @return array
	 */
	public function get_attribute_taxonomy_names() {
		_deprecated_function( 'Woocommerce->get_attribute_taxonomy_names', '2.1', 'wc_get_attribute_taxonomy_names' );
		return wc_get_attribute_taxonomy_names();
	}

	/**
	 * @deprecated 2.1.0
	 * @return array
	 */
	public function get_coupon_discount_types() {
		_deprecated_function( 'Woocommerce->get_coupon_discount_types', '2.1', 'wc_get_coupon_types' );
		return wc_get_coupon_types();
	}

	/**
	 * @deprecated 2.1.0
	 * @param string $type
	 * @return string
	 */
	public function get_coupon_discount_type( $type = '' ) {
		_deprecated_function( 'Woocommerce->get_coupon_discount_type', '2.1', 'wc_get_coupon_type' );
		return wc_get_coupon_type( $type );
	}

	/**
	 * @deprecated 2.1.0
	 * @param $class
	 */
	public function add_body_class( $class ) {
		_deprecated_function( 'Woocommerce->add_body_class', '2.1' );
	}

	/**
	 * @deprecated 2.1.0
	 * @param $classes
	 */
	public function output_body_class( $classes ) {
		_deprecated_function( 'Woocommerce->output_body_class', '2.1' );
	}

	/**
	 * @deprecated 2.1.0
	 * @param $error
	 */
	public function add_error( $error ) {
		_deprecated_function( 'Woocommerce->add_error', '2.1', 'wc_add_notice' );
		wc_add_notice( $error, 'error' );
	}

	/**
	 * @deprecated 2.1.0
	 * @param $message
	 */
	public function add_message( $message ) {
		_deprecated_function( 'Woocommerce->add_message', '2.1', 'wc_add_notice' );
		wc_add_notice( $message );
	}

	/**
	 * @deprecated 2.1.0
	 */
	public function clear_messages() {
		_deprecated_function( 'Woocommerce->clear_messages', '2.1', 'wc_clear_notices' );
		wc_clear_notices();
	}

	/**
	 * @deprecated 2.1.0
	 * @return int
	 */
	public function error_count() {
		_deprecated_function( 'Woocommerce->error_count', '2.1', 'wc_notice_count' );
		return wc_notice_count( 'error' );
	}

	/**
	 * @deprecated 2.1.0
	 * @return int
	 */
	public function message_count() {
		_deprecated_function( 'Woocommerce->message_count', '2.1', 'wc_notice_count' );
		return wc_notice_count( 'message' );
	}

	/**
	 * @deprecated 2.1.0
	 * @return mixed
	 */
	public function get_errors() {
		_deprecated_function( 'Woocommerce->get_errors', '2.1', 'wc_get_notices( "error" )' );
		return wc_get_notices( 'error' );
	}

	/**
	 * @deprecated 2.1.0
	 * @return mixed
	 */
	public function get_messages() {
		_deprecated_function( 'Woocommerce->get_messages', '2.1', 'wc_get_notices( "success" )' );
		return wc_get_notices( 'success' );
	}

	/**
	 * @deprecated 2.1.0
	 */
	public function show_messages() {
		_deprecated_function( 'Woocommerce->show_messages', '2.1', 'wc_print_notices()' );
		wc_print_notices();
	}

	/**
	 * @deprecated 2.1.0
	 */
	public function set_messages() {
		_deprecated_function( 'Woocommerce->set_messages', '2.1' );
	}
}

endif;

/**
 * Returns the main instance of WC to prevent the need to use globals.
 *
 * @since  2.1
 * @return WooCommerce
 */
function WC() {
	return WooCommerce::instance();
}

// Global for backwards compatibility.
$GLOBALS['woocommerce'] = WC();

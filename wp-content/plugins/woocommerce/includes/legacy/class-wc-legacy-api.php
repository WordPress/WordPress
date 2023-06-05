<?php
/**
 * WooCommerce Legacy API. Was deprecated with 2.6.0.
 *
 * @author   WooThemes
 * @category API
 * @package  WooCommerce\RestApi
 * @since    2.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Legacy API.
 */
class WC_Legacy_API {

	/**
	 * This is the major version for the REST API and takes
	 * first-order position in endpoint URLs.
	 *
	 * @deprecated 2.6.0
	 * @var string
	 */
	const VERSION = '3.1.0';

	/**
	 * The REST API server.
	 *
	 * @deprecated 2.6.0
	 * @var WC_API_Server
	 */
	public $server;

	/**
	 * REST API authentication class instance.
	 *
	 * @deprecated 2.6.0
	 * @var WC_API_Authentication
	 */
	public $authentication;

	/**
	 * Init the legacy API.
	 */
	public function init() {
		add_action( 'parse_request', array( $this, 'handle_rest_api_requests' ), 0 );
	}

	/**
	 * Add new query vars.
	 *
	 * @since 2.0
	 * @param array $vars Vars.
	 * @return string[]
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'wc-api-version'; // Deprecated since 2.6.0.
		$vars[] = 'wc-api-route'; // Deprecated since 2.6.0.
		return $vars;
	}

	/**
	 * Add new endpoints.
	 *
	 * @since 2.0
	 */
	public static function add_endpoint() {
		// REST API, deprecated since 2.6.0.
		add_rewrite_rule( '^wc-api/v([1-3]{1})/?$', 'index.php?wc-api-version=$matches[1]&wc-api-route=/', 'top' );
		add_rewrite_rule( '^wc-api/v([1-3]{1})(.*)?', 'index.php?wc-api-version=$matches[1]&wc-api-route=$matches[2]', 'top' );
	}

	/**
	 * Handle REST API requests.
	 *
	 * @since 2.2
	 * @deprecated 2.6.0
	 */
	public function handle_rest_api_requests() {
		global $wp;

		if ( ! empty( $_GET['wc-api-version'] ) ) {
			$wp->query_vars['wc-api-version'] = $_GET['wc-api-version'];
		}

		if ( ! empty( $_GET['wc-api-route'] ) ) {
			$wp->query_vars['wc-api-route'] = $_GET['wc-api-route'];
		}

		// REST API request.
		if ( ! empty( $wp->query_vars['wc-api-version'] ) && ! empty( $wp->query_vars['wc-api-route'] ) ) {

			wc_maybe_define_constant( 'WC_API_REQUEST', true );
			wc_maybe_define_constant( 'WC_API_REQUEST_VERSION', absint( $wp->query_vars['wc-api-version'] ) );

			// Legacy v1 API request.
			if ( 1 === WC_API_REQUEST_VERSION ) {
				$this->handle_v1_rest_api_request();
			} elseif ( 2 === WC_API_REQUEST_VERSION ) {
				$this->handle_v2_rest_api_request();
			} else {
				$this->includes();

				$this->server = new WC_API_Server( $wp->query_vars['wc-api-route'] );

				// load API resource classes.
				$this->register_resources( $this->server );

				// Fire off the request.
				$this->server->serve_request();
			}

			exit;
		}
	}

	/**
	 * Include required files for REST API request.
	 *
	 * @since 2.1
	 * @deprecated 2.6.0
	 */
	public function includes() {

		// API server / response handlers.
		include_once( dirname( __FILE__ ) . '/api/v3/class-wc-api-exception.php' );
		include_once( dirname( __FILE__ ) . '/api/v3/class-wc-api-server.php' );
		include_once( dirname( __FILE__ ) . '/api/v3/interface-wc-api-handler.php' );
		include_once( dirname( __FILE__ ) . '/api/v3/class-wc-api-json-handler.php' );

		// Authentication.
		include_once( dirname( __FILE__ ) . '/api/v3/class-wc-api-authentication.php' );
		$this->authentication = new WC_API_Authentication();

		include_once( dirname( __FILE__ ) . '/api/v3/class-wc-api-resource.php' );
		include_once( dirname( __FILE__ ) . '/api/v3/class-wc-api-coupons.php' );
		include_once( dirname( __FILE__ ) . '/api/v3/class-wc-api-customers.php' );
		include_once( dirname( __FILE__ ) . '/api/v3/class-wc-api-orders.php' );
		include_once( dirname( __FILE__ ) . '/api/v3/class-wc-api-products.php' );
		include_once( dirname( __FILE__ ) . '/api/v3/class-wc-api-reports.php' );
		include_once( dirname( __FILE__ ) . '/api/v3/class-wc-api-taxes.php' );
		include_once( dirname( __FILE__ ) . '/api/v3/class-wc-api-webhooks.php' );

		// Allow plugins to load other response handlers or resource classes.
		do_action( 'woocommerce_api_loaded' );
	}

	/**
	 * Register available API resources.
	 *
	 * @since 2.1
	 * @deprecated 2.6.0
	 * @param WC_API_Server $server the REST server.
	 */
	public function register_resources( $server ) {

		$api_classes = apply_filters( 'woocommerce_api_classes',
			array(
				'WC_API_Coupons',
				'WC_API_Customers',
				'WC_API_Orders',
				'WC_API_Products',
				'WC_API_Reports',
				'WC_API_Taxes',
				'WC_API_Webhooks',
			)
		);

		foreach ( $api_classes as $api_class ) {
			$this->$api_class = new $api_class( $server );
		}
	}


	/**
	 * Handle legacy v1 REST API requests.
	 *
	 * @since 2.2
	 * @deprecated 2.6.0
	 */
	private function handle_v1_rest_api_request() {

		// Include legacy required files for v1 REST API request.
		include_once( dirname( __FILE__ ) . '/api/v1/class-wc-api-server.php' );
		include_once( dirname( __FILE__ ) . '/api/v1/interface-wc-api-handler.php' );
		include_once( dirname( __FILE__ ) . '/api/v1/class-wc-api-json-handler.php' );
		include_once( dirname( __FILE__ ) . '/api/v1/class-wc-api-xml-handler.php' );

		include_once( dirname( __FILE__ ) . '/api/v1/class-wc-api-authentication.php' );
		$this->authentication = new WC_API_Authentication();

		include_once( dirname( __FILE__ ) . '/api/v1/class-wc-api-resource.php' );
		include_once( dirname( __FILE__ ) . '/api/v1/class-wc-api-coupons.php' );
		include_once( dirname( __FILE__ ) . '/api/v1/class-wc-api-customers.php' );
		include_once( dirname( __FILE__ ) . '/api/v1/class-wc-api-orders.php' );
		include_once( dirname( __FILE__ ) . '/api/v1/class-wc-api-products.php' );
		include_once( dirname( __FILE__ ) . '/api/v1/class-wc-api-reports.php' );

		// Allow plugins to load other response handlers or resource classes.
		do_action( 'woocommerce_api_loaded' );

		$this->server = new WC_API_Server( $GLOBALS['wp']->query_vars['wc-api-route'] );

		// Register available resources for legacy v1 REST API request.
		$api_classes = apply_filters( 'woocommerce_api_classes',
			array(
				'WC_API_Customers',
				'WC_API_Orders',
				'WC_API_Products',
				'WC_API_Coupons',
				'WC_API_Reports',
			)
		);

		foreach ( $api_classes as $api_class ) {
			$this->$api_class = new $api_class( $this->server );
		}

		// Fire off the request.
		$this->server->serve_request();
	}

	/**
	 * Handle legacy v2 REST API requests.
	 *
	 * @since 2.4
	 * @deprecated 2.6.0
	 */
	private function handle_v2_rest_api_request() {
		include_once( dirname( __FILE__ ) . '/api/v2/class-wc-api-exception.php' );
		include_once( dirname( __FILE__ ) . '/api/v2/class-wc-api-server.php' );
		include_once( dirname( __FILE__ ) . '/api/v2/interface-wc-api-handler.php' );
		include_once( dirname( __FILE__ ) . '/api/v2/class-wc-api-json-handler.php' );

		include_once( dirname( __FILE__ ) . '/api/v2/class-wc-api-authentication.php' );
		$this->authentication = new WC_API_Authentication();

		include_once( dirname( __FILE__ ) . '/api/v2/class-wc-api-resource.php' );
		include_once( dirname( __FILE__ ) . '/api/v2/class-wc-api-coupons.php' );
		include_once( dirname( __FILE__ ) . '/api/v2/class-wc-api-customers.php' );
		include_once( dirname( __FILE__ ) . '/api/v2/class-wc-api-orders.php' );
		include_once( dirname( __FILE__ ) . '/api/v2/class-wc-api-products.php' );
		include_once( dirname( __FILE__ ) . '/api/v2/class-wc-api-reports.php' );
		include_once( dirname( __FILE__ ) . '/api/v2/class-wc-api-webhooks.php' );

		// allow plugins to load other response handlers or resource classes.
		do_action( 'woocommerce_api_loaded' );

		$this->server = new WC_API_Server( $GLOBALS['wp']->query_vars['wc-api-route'] );

		// Register available resources for legacy v2 REST API request.
		$api_classes = apply_filters( 'woocommerce_api_classes',
			array(
				'WC_API_Customers',
				'WC_API_Orders',
				'WC_API_Products',
				'WC_API_Coupons',
				'WC_API_Reports',
				'WC_API_Webhooks',
			)
		);

		foreach ( $api_classes as $api_class ) {
			$this->$api_class = new $api_class( $this->server );
		}

		// Fire off the request.
		$this->server->serve_request();
	}

	/**
	 * Rest API Init.
	 *
	 * @deprecated 3.7.0 - REST API classes autoload.
	 */
	public function rest_api_init() {}

	/**
	 * Include REST API classes.
	 *
	 * @deprecated 3.7.0 - REST API classes autoload.
	 */
	public function rest_api_includes() {
		$this->rest_api_init();
	}
	/**
	 * Register REST API routes.
	 *
	 * @deprecated 3.7.0
	 */
	public function register_rest_routes() {
		wc_deprecated_function( 'WC_Legacy_API::register_rest_routes', '3.7.0', '' );
		$this->register_wp_admin_settings();
	}
}

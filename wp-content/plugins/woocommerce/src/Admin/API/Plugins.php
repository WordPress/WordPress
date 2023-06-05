<?php
/**
 * REST API Plugins Controller
 *
 * Handles requests to install and activate depedent plugins.
 */

namespace Automattic\WooCommerce\Admin\API;

use Automattic\WooCommerce\Internal\Admin\Onboarding\OnboardingProfile;
use Automattic\WooCommerce\Admin\PaymentMethodSuggestionsDataSourcePoller;
use Automattic\WooCommerce\Admin\PluginsHelper;
use Automattic\WooCommerce\Internal\Admin\Notes\InstallJPAndWCSPlugins;

defined( 'ABSPATH' ) || exit;

/**
 * Plugins Controller.
 *
 * @internal
 * @extends WC_REST_Data_Controller
 */
class Plugins extends \WC_REST_Data_Controller {
	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-admin';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'plugins';

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/install',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'install_plugins' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/install/status',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_installation_status' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/install/status/(?P<job_id>[a-z0-9_\-]+)',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_job_installation_status' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/active',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'active_plugins' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/installed',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'installed_plugins' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/activate',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'activate_plugins' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/activate/status',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_activation_status' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/activate/status/(?P<job_id>[a-z0-9_\-]+)',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_job_activation_status' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/connect-jetpack',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'connect_jetpack' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_connect_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/request-wccom-connect',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'request_wccom_connect' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_connect_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/finish-wccom-connect',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'finish_wccom_connect' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_connect_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/connect-wcpay',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'connect_wcpay' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_connect_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/connect-square',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'connect_square' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_connect_schema' ),
			)
		);
	}

	/**
	 * Check if a given request has access to manage plugins.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function update_item_permissions_check( $request ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return new \WP_Error( 'woocommerce_rest_cannot_update', __( 'Sorry, you cannot manage plugins.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}
		return true;
	}

	/**
	 * Install the requested plugin.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|array Plugin Status
	 */
	public function install_plugin( $request ) {
		wc_deprecated_function( 'install_plugin', '4.3', '\Automattic\WooCommerce\Admin\API\Plugins()->install_plugins' );
		// This method expects a `plugin` argument to be sent, install plugins requires plugins.
		$request['plugins'] = $request['plugin'];
		return self::install_plugins( $request );
	}

	/**
	 * Installs the requested plugins.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|array Plugin Status
	 */
	public function install_plugins( $request ) {
		$plugins = explode( ',', $request['plugins'] );

		if ( empty( $request['plugins'] ) || ! is_array( $plugins ) ) {
			return new \WP_Error( 'woocommerce_rest_invalid_plugins', __( 'Plugins must be a non-empty array.', 'woocommerce' ), 404 );
		}

		if ( isset( $request['async'] ) && $request['async'] ) {
			$job_id = PluginsHelper::schedule_install_plugins( $plugins );

			return array(
				'data'    => array(
					'job_id'  => $job_id,
					'plugins' => $plugins,
				),
				'message' => __( 'Plugin installation has been scheduled.', 'woocommerce' ),
			);
		}

		$data = PluginsHelper::install_plugins( $plugins );

		return array(
			'data'    => array(
				'installed'    => $data['installed'],
				'results'      => $data['results'],
				'install_time' => $data['time'],
			),
			'errors'  => $data['errors'],
			'success' => count( $data['errors']->errors ) === 0,
			'message' => count( $data['errors']->errors ) === 0
				? __( 'Plugins were successfully installed.', 'woocommerce' )
				: __( 'There was a problem installing some of the requested plugins.', 'woocommerce' ),
		);
	}

	/**
	 * Returns a list of recently scheduled installation jobs.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return array Jobs.
	 */
	public function get_installation_status( $request ) {
		return PluginsHelper::get_installation_status();
	}

	/**
	 * Returns a list of recently scheduled installation jobs.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return array Job.
	 */
	public function get_job_installation_status( $request ) {
		$job_id = $request->get_param( 'job_id' );
		$jobs   = PluginsHelper::get_installation_status( $job_id );
		return reset( $jobs );
	}

	/**
	 * Returns a list of active plugins in API format.
	 *
	 * @return array Active plugins
	 */
	public static function active_plugins() {
		return( array(
			'plugins' => array_values( PluginsHelper::get_active_plugin_slugs() ),
		) );
	}
	/**
	 * Returns a list of active plugins.
	 *
	 * @internal
	 * @return array Active plugins
	 */
	public static function get_active_plugins() {
		$data = self::active_plugins();
		return $data['plugins'];
	}

	/**
	 * Returns a list of installed plugins.
	 *
	 * @return array Installed plugins
	 */
	public function installed_plugins() {
		return( array(
			'plugins' => PluginsHelper::get_installed_plugin_slugs(),
		) );
	}

	/**
	 * Activate the requested plugin.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|array Plugin Status
	 */
	public function activate_plugins( $request ) {
		$plugins = explode( ',', $request['plugins'] );

		if ( empty( $request['plugins'] ) || ! is_array( $plugins ) ) {
			return new \WP_Error( 'woocommerce_rest_invalid_plugins', __( 'Plugins must be a non-empty array.', 'woocommerce' ), 404 );
		}

		if ( isset( $request['async'] ) && $request['async'] ) {
			$job_id = PluginsHelper::schedule_activate_plugins( $plugins );

			return array(
				'data'    => array(
					'job_id'  => $job_id,
					'plugins' => $plugins,
				),
				'message' => __( 'Plugin activation has been scheduled.', 'woocommerce' ),
			);
		}

		$data = PluginsHelper::activate_plugins( $plugins );

		return( array(
			'data'    => array(
				'activated' => $data['activated'],
				'active'    => $data['active'],
			),
			'errors'  => $data['errors'],
			'success' => count( $data['errors']->errors ) === 0,
			'message' => count( $data['errors']->errors ) === 0
				? __( 'Plugins were successfully activated.', 'woocommerce' )
				: __( 'There was a problem activating some of the requested plugins.', 'woocommerce' ),
		) );
	}

	/**
	 * Returns a list of recently scheduled activation jobs.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return array Job.
	 */
	public function get_activation_status( $request ) {
		return PluginsHelper::get_activation_status();
	}

	/**
	 * Returns a list of recently scheduled activation jobs.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return array Jobs.
	 */
	public function get_job_activation_status( $request ) {
		$job_id = $request->get_param( 'job_id' );
		$jobs   = PluginsHelper::get_activation_status( $job_id );
		return reset( $jobs );
	}

	/**
	 * Generates a Jetpack Connect URL.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|array Connection URL for Jetpack
	 */
	public function connect_jetpack( $request ) {
		if ( ! class_exists( '\Jetpack' ) ) {
			return new \WP_Error( 'woocommerce_rest_jetpack_not_active', __( 'Jetpack is not installed or active.', 'woocommerce' ), 404 );
		}

		$redirect_url = apply_filters( 'woocommerce_admin_onboarding_jetpack_connect_redirect_url', esc_url_raw( $request['redirect_url'] ) );
		$connect_url  = \Jetpack::init()->build_connect_url( true, $redirect_url, 'woocommerce-onboarding' );

		$calypso_env = defined( 'WOOCOMMERCE_CALYPSO_ENVIRONMENT' ) && in_array( WOOCOMMERCE_CALYPSO_ENVIRONMENT, array( 'development', 'wpcalypso', 'horizon', 'stage' ), true ) ? WOOCOMMERCE_CALYPSO_ENVIRONMENT : 'production';
		$connect_url = add_query_arg( array( 'calypso_env' => $calypso_env ), $connect_url );

		return( array(
			'slug'          => 'jetpack',
			'name'          => __( 'Jetpack', 'woocommerce' ),
			'connectAction' => $connect_url,
		) );
	}

	/**
	 *  Kicks off the WCCOM Connect process.
	 *
	 * @return WP_Error|array Connection URL for WooCommerce.com
	 */
	public function request_wccom_connect() {
		include_once WC_ABSPATH . 'includes/admin/helper/class-wc-helper-api.php';
		if ( ! class_exists( 'WC_Helper_API' ) ) {
			return new \WP_Error( 'woocommerce_rest_helper_not_active', __( 'There was an error loading the WooCommerce.com Helper API.', 'woocommerce' ), 404 );
		}

		$redirect_uri = wc_admin_url( '&task=connect&wccom-connected=1' );

		$request = \WC_Helper_API::post(
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
			return new \WP_Error( 'woocommerce_rest_helper_connect', __( 'There was an error connecting to WooCommerce.com. Please try again.', 'woocommerce' ), 500 );
		}

		$secret = json_decode( wp_remote_retrieve_body( $request ) );
		if ( empty( $secret ) ) {
			return new \WP_Error( 'woocommerce_rest_helper_connect', __( 'There was an error connecting to WooCommerce.com. Please try again.', 'woocommerce' ), 500 );
		}

		do_action( 'woocommerce_helper_connect_start' );

		$connect_url = add_query_arg(
			array(
				'home_url'     => rawurlencode( home_url() ),
				'redirect_uri' => rawurlencode( $redirect_uri ),
				'secret'       => rawurlencode( $secret ),
				'wccom-from'   => 'onboarding',
			),
			\WC_Helper_API::url( 'oauth/authorize' )
		);

		if ( defined( 'WOOCOMMERCE_CALYPSO_ENVIRONMENT' ) && in_array( WOOCOMMERCE_CALYPSO_ENVIRONMENT, array( 'development', 'wpcalypso', 'horizon', 'stage' ), true ) ) {
			$connect_url = add_query_arg(
				array(
					'calypso_env' => WOOCOMMERCE_CALYPSO_ENVIRONMENT,
				),
				$connect_url
			);
		}

		return( array(
			'connectAction' => $connect_url,
		) );
	}

	/**
	 * Finishes connecting to WooCommerce.com.
	 *
	 * @param  object $rest_request Request details.
	 * @return WP_Error|array Contains success status.
	 */
	public function finish_wccom_connect( $rest_request ) {
		include_once WC_ABSPATH . 'includes/admin/helper/class-wc-helper.php';
		include_once WC_ABSPATH . 'includes/admin/helper/class-wc-helper-api.php';
		include_once WC_ABSPATH . 'includes/admin/helper/class-wc-helper-updater.php';
		include_once WC_ABSPATH . 'includes/admin/helper/class-wc-helper-options.php';
		if ( ! class_exists( 'WC_Helper_API' ) ) {
			return new \WP_Error( 'woocommerce_rest_helper_not_active', __( 'There was an error loading the WooCommerce.com Helper API.', 'woocommerce' ), 404 );
		}

		// Obtain an access token.
		$request = \WC_Helper_API::post(
			'oauth/access_token',
			array(
				'body' => array(
					'request_token' => wp_unslash( $rest_request['request_token'] ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					'home_url'      => home_url(),
				),
			)
		);

		$code = wp_remote_retrieve_response_code( $request );
		if ( 200 !== $code ) {
			return new \WP_Error( 'woocommerce_rest_helper_connect', __( 'There was an error connecting to WooCommerce.com. Please try again.', 'woocommerce' ), 500 );
		}

		$access_token = json_decode( wp_remote_retrieve_body( $request ), true );
		if ( ! $access_token ) {
			return new \WP_Error( 'woocommerce_rest_helper_connect', __( 'There was an error connecting to WooCommerce.com. Please try again.', 'woocommerce' ), 500 );
		}

		\WC_Helper_Options::update(
			'auth',
			array(
				'access_token'        => $access_token['access_token'],
				'access_token_secret' => $access_token['access_token_secret'],
				'site_id'             => $access_token['site_id'],
				'user_id'             => get_current_user_id(),
				'updated'             => time(),
			)
		);

		if ( ! \WC_Helper::_flush_authentication_cache() ) {
			\WC_Helper_Options::update( 'auth', array() );
			return new \WP_Error( 'woocommerce_rest_helper_connect', __( 'There was an error connecting to WooCommerce.com. Please try again.', 'woocommerce' ), 500 );
		}

		delete_transient( '_woocommerce_helper_subscriptions' );
		\WC_Helper_Updater::flush_updates_cache();

		do_action( 'woocommerce_helper_connected' );

		return array(
			'success' => true,
		);
	}


	/**
	 * Returns a URL that can be used to connect to Square.
	 *
	 * @return WP_Error|array Connect URL.
	 */
	public function connect_square() {
		if ( ! class_exists( '\WooCommerce\Square\Handlers\Connection' ) ) {
			return new \WP_Error( 'woocommerce_rest_helper_connect', __( 'There was an error connecting to Square.', 'woocommerce' ), 500 );
		}

		if ( 'US' === WC()->countries->get_base_country() ) {
			$profile = get_option( OnboardingProfile::DATA_OPTION, array() );
			if ( ! empty( $profile['industry'] ) ) {
				$has_cbd_industry = in_array( 'cbd-other-hemp-derived-products', array_column( $profile['industry'], 'slug' ), true );
			}
		}

		if ( $has_cbd_industry ) {
			$url = 'https://squareup.com/t/f_partnerships/d_referrals/p_woocommerce/c_general/o_none/l_us/dt_alldevice/pr_payments/?route=/solutions/cbd';
		} else {
			$url = \WooCommerce\Square\Handlers\Connection::CONNECT_URL_PRODUCTION;
		}

		$redirect_url = wp_nonce_url( wc_admin_url( '&task=payments&method=square&square-connect-finish=1' ), 'wc_square_connected' );
		$args         = array(
			'redirect' => rawurlencode( rawurlencode( $redirect_url ) ),
			'scopes'   => implode(
				',',
				array(
					'MERCHANT_PROFILE_READ',
					'PAYMENTS_READ',
					'PAYMENTS_WRITE',
					'ORDERS_READ',
					'ORDERS_WRITE',
					'CUSTOMERS_READ',
					'CUSTOMERS_WRITE',
					'SETTLEMENTS_READ',
					'ITEMS_READ',
					'ITEMS_WRITE',
					'INVENTORY_READ',
					'INVENTORY_WRITE',
				)
			),
		);

		$connect_url = add_query_arg( $args, $url );

		return( array(
			'connectUrl' => $connect_url,
		) );
	}

	/**
	 * Returns a URL that can be used to by WCPay to verify business details with Stripe.
	 *
	 * @return WP_Error|array Connect URL.
	 */
	public function connect_wcpay() {
		if ( ! class_exists( 'WC_Payments_Account' ) ) {
			return new \WP_Error( 'woocommerce_rest_helper_connect', __( 'There was an error communicating with the WooCommerce Payments plugin.', 'woocommerce' ), 500 );
		}

		$connect_url = add_query_arg(
			array(
				'wcpay-connect' => 'WCADMIN_PAYMENT_TASK',
				'_wpnonce'      => wp_create_nonce( 'wcpay-connect' ),
			),
			admin_url()
		);

		return( array(
			'connectUrl' => $connect_url,
		) );
	}

	/**
	 * Get the schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'plugins',
			'type'       => 'object',
			'properties' => array(
				'slug'   => array(
					'description' => __( 'Plugin slug.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'name'   => array(
					'description' => __( 'Plugin name.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'status' => array(
					'description' => __( 'Plugin status.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_connect_schema() {
		$schema = $this->get_item_schema();
		unset( $schema['properties']['status'] );
		$schema['properties']['connectAction'] = array(
			'description' => __( 'Action that should be completed to connect Jetpack.', 'woocommerce' ),
			'type'        => 'string',
			'context'     => array( 'view', 'edit' ),
			'readonly'    => true,
		);
		return $schema;
	}
}

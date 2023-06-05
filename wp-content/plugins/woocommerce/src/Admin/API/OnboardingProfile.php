<?php
/**
 * REST API Onboarding Profile Controller
 *
 * Handles requests to /onboarding/profile
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Internal\Admin\Onboarding\OnboardingProfile as Profile;
use Automattic\WooCommerce\Internal\Admin\Onboarding\OnboardingProducts;
use Automattic\Jetpack\Connection\Manager as Jetpack_Connection_Manager;

/**
 * Onboarding Profile controller.
 *
 * @internal
 * @extends WC_REST_Data_Controller
 */
class OnboardingProfile extends \WC_REST_Data_Controller {
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
	protected $rest_base = 'onboarding/profile';

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_items' ),
					'permission_callback' => array( $this, 'update_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		// This endpoint is experimental. For internal use only.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/experimental_get_email_prefill',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_email_prefill' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check whether a given request has permission to read onboarding profile data.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'read' ) ) {
			return new \WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check whether a given request has permission to edit onboarding profile data.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function update_items_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'edit' ) ) {
			return new \WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot edit this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Return all onboarding profile data.
	 *
	 * @param  WP_REST_Request $request Request data.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		include_once WC_ABSPATH . 'includes/admin/helper/class-wc-helper-options.php';

		$onboarding_data             = get_option( Profile::DATA_OPTION, array() );
		$onboarding_data['industry'] = isset( $onboarding_data['industry'] ) ? $this->filter_industries( $onboarding_data['industry'] ) : null;
		$item_schema                 = $this->get_item_schema();
		$items                       = array();
		foreach ( $item_schema['properties'] as $key => $property_schema ) {
			$items[ $key ] = isset( $onboarding_data[ $key ] ) ? $onboarding_data[ $key ] : null;
		}

		$wccom_auth               = \WC_Helper_Options::get( 'auth' );
		$items['wccom_connected'] = empty( $wccom_auth['access_token'] ) ? false : true;

		$item = $this->prepare_item_for_response( $items, $request );
		$data = $this->prepare_response_for_collection( $item );

		return rest_ensure_response( $data );
	}

	/**
	 * Filter the industries.
	 *
	 * @param  array $industries list of industries.
	 * @return array
	 */
	protected function filter_industries( $industries ) {
		return apply_filters(
			'woocommerce_admin_onboarding_industries',
			$industries
		);
	}

	/**
	 * Update onboarding profile data.
	 *
	 * @param  WP_REST_Request $request Request data.
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_items( $request ) {
		$params          = $request->get_json_params();
		$query_args      = $this->prepare_objects_query( $params );
		$onboarding_data = (array) get_option( Profile::DATA_OPTION, array() );
		$profile_data    = array_merge( $onboarding_data, $query_args );
		update_option( Profile::DATA_OPTION, $profile_data );
		do_action( 'woocommerce_onboarding_profile_data_updated', $onboarding_data, $query_args );

		$result = array(
			'status'  => 'success',
			'message' => __( 'Onboarding profile data has been updated.', 'woocommerce' ),
		);

		$response = $this->prepare_item_for_response( $result, $request );
		$data     = $this->prepare_response_for_collection( $response );

		return rest_ensure_response( $data );
	}

	/**
	 * Returns a default email to be pre-filled in OBW. Prioritizes Jetpack if connected,
	 * otherwise will default to WordPress general settings.
	 *
	 * @param  WP_REST_Request $request Request data.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_email_prefill( $request ) {
		$result = array(
			'email' => '',
		);

		// Attempt to get email from Jetpack.
		if ( class_exists( Jetpack_Connection_Manager::class ) ) {
			$jetpack_connection_manager = new Jetpack_Connection_Manager();
			if ( $jetpack_connection_manager->is_active() ) {
				$jetpack_user = $jetpack_connection_manager->get_connected_user_data();

				$result['email'] = $jetpack_user['email'];
			}
		}

		// Attempt to get email from WordPress general settings.
		if ( empty( $result['email'] ) ) {
			$result['email'] = get_option( 'admin_email' );
		}

		return rest_ensure_response( $result );
	}

	/**
	 * Prepare objects query.
	 *
	 * @param  array $params The params sent in the request.
	 * @return array
	 */
	protected function prepare_objects_query( $params ) {
		$args       = array();
		$properties = self::get_profile_properties();

		foreach ( $properties as $key => $property ) {
			if ( isset( $params[ $key ] ) ) {
				$args[ $key ] = $params[ $key ];
			}
		}

		/**
		 * Filter the query arguments for a request.
		 *
		 * Enables adding extra arguments or setting defaults for a post
		 * collection request.
		 *
		 * @param array $args    Key value array of query var to query value.
		 * @param array $params The params sent in the request.
		 */
		$args = apply_filters( 'woocommerce_rest_onboarding_profile_object_query', $args, $params );

		return $args;
	}


	/**
	 * Prepare the data object for response.
	 *
	 * @param object          $item Data object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data     = $this->add_additional_fields_to_object( $item, $request );
		$data     = $this->filter_response_by_context( $data, 'view' );
		$response = rest_ensure_response( $data );

		/**
		 * Filter the list returned from the API.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param array            $item     The original item.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'woocommerce_rest_onboarding_prepare_profile', $response, $item, $request );
	}

	/**
	 * Get onboarding profile properties.
	 *
	 * @return array
	 */
	public static function get_profile_properties() {
		$properties = array(
			'completed'            => array(
				'type'              => 'boolean',
				'description'       => __( 'Whether or not the profile was completed.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'validate_callback' => 'rest_validate_request_arg',
			),
			'skipped'              => array(
				'type'              => 'boolean',
				'description'       => __( 'Whether or not the profile was skipped.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'validate_callback' => 'rest_validate_request_arg',
			),
			'industry'             => array(
				'type'              => 'array',
				'description'       => __( 'Industry.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'validate_callback' => 'rest_validate_request_arg',
				'items'             => array(
					'type' => 'object',
				),
			),
			'product_types'        => array(
				'type'              => 'array',
				'description'       => __( 'Types of products sold.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'sanitize_callback' => 'wp_parse_slug_list',
				'validate_callback' => 'rest_validate_request_arg',
				'items'             => array(
					'enum' => array_keys( OnboardingProducts::get_allowed_product_types() ),
					'type' => 'string',
				),
			),
			'product_count'        => array(
				'type'              => 'string',
				'description'       => __( 'Number of products to be added.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'validate_callback' => 'rest_validate_request_arg',
				'enum'              => array(
					'0',
					'1-10',
					'11-100',
					'101-1000',
					'1000+',
				),
			),
			'selling_venues'       => array(
				'type'              => 'string',
				'description'       => __( 'Other places the store is selling products.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'validate_callback' => 'rest_validate_request_arg',
				'enum'              => array(
					'no',
					'other',
					'brick-mortar',
					'brick-mortar-other',
					'other-woocommerce',
				),
			),
			'number_employees'     => array(
				'type'              => 'string',
				'description'       => __( 'Number of employees of the store.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'validate_callback' => 'rest_validate_request_arg',
				'enum'              => array(
					'1',
					'<10',
					'10-50',
					'50-250',
					'+250',
					'not specified',
				),
			),
			'revenue'              => array(
				'type'              => 'string',
				'description'       => __( 'Current annual revenue of the store.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'validate_callback' => 'rest_validate_request_arg',
				'enum'              => array(
					'none',
					'up-to-2500',
					'2500-10000',
					'10000-50000',
					'50000-250000',
					'more-than-250000',
					'rather-not-say',
				),
			),
			'other_platform'       => array(
				'type'              => 'string',
				'description'       => __( 'Name of other platform used to sell.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'validate_callback' => 'rest_validate_request_arg',
				'enum'              => array(
					'shopify',
					'bigcommerce',
					'magento',
					'wix',
					'amazon',
					'ebay',
					'etsy',
					'squarespace',
					'other',
				),
			),
			'other_platform_name'  => array(
				'type'              => 'string',
				'description'       => __( 'Name of other platform used to sell (not listed).', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'validate_callback' => 'rest_validate_request_arg',
			),
			'business_extensions'  => array(
				'type'              => 'array',
				'description'       => __( 'Extra business extensions to install.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'sanitize_callback' => 'wp_parse_slug_list',
				'validate_callback' => 'rest_validate_request_arg',
				'items'             => array(
					'enum' => array(
						'jetpack',
						'woocommerce-services',
						'woocommerce-payments',
						'mailchimp-for-woocommerce',
						'creative-mail-by-constant-contact',
						'facebook-for-woocommerce',
						'google-listings-and-ads',
						'pinterest-for-woocommerce',
						'mailpoet',
						'codistoconnect',
						'tiktok-for-business',
						'tiktok-for-business:alt'
					),
					'type' => 'string',
				),
			),
			'theme'                => array(
				'type'              => 'string',
				'description'       => __( 'Selected store theme.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'sanitize_callback' => 'sanitize_title_with_dashes',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'setup_client'         => array(
				'type'              => 'boolean',
				'description'       => __( 'Whether or not this store was setup for a client.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'validate_callback' => 'rest_validate_request_arg',
			),
			'wccom_connected'      => array(
				'type'              => 'boolean',
				'description'       => __( 'Whether or not the store was connected to WooCommerce.com during the extension flow.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'validate_callback' => 'rest_validate_request_arg',
			),
			'is_agree_marketing'   => array(
				'type'              => 'boolean',
				'description'       => __( 'Whether or not this store agreed to receiving marketing contents from WooCommerce.com.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'validate_callback' => 'rest_validate_request_arg',
			),
			'store_email'          => array(
				'type'              => 'string',
				'description'       => __( 'Store email address.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'validate_callback' => array( __CLASS__, 'rest_validate_marketing_email' ),
			),
			'is_store_country_set' => array(
				'type'              => 'boolean',
				'description'       => __( 'Whether or not this store country is set via onboarding profiler.', 'woocommerce' ),
				'context'           => array( 'view' ),
				'readonly'          => true,
				'validate_callback' => 'rest_validate_request_arg',
			),
		);

		return apply_filters( 'woocommerce_rest_onboarding_profile_properties', $properties );
	}

	/**
	 * Optionally validates email if user agreed to marketing or if email is not empty.
	 *
	 * @param mixed           $value Email value.
	 * @param WP_REST_Request $request Request object.
	 * @param string          $param Parameter name.
	 * @return true|WP_Error
	 */
	public static function rest_validate_marketing_email( $value, $request, $param ) {
		$is_agree_marketing = $request->get_param( 'is_agree_marketing' );
		if (
			( $is_agree_marketing || ! empty( $value ) ) &&
			! is_email( $value ) ) {
			return new \WP_Error( 'rest_invalid_email', __( 'Invalid email address', 'woocommerce' ) );
		};
		return true;
	}

	/**
	 * Get the schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		// Unset properties used for collection params.
		$properties = self::get_profile_properties();
		foreach ( $properties as $key => $property ) {
			unset( $properties[ $key ]['default'] );
			unset( $properties[ $key ]['items'] );
			unset( $properties[ $key ]['validate_callback'] );
			unset( $properties[ $key ]['sanitize_callback'] );
		}

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'onboarding_profile',
			'type'       => 'object',
			'properties' => $properties,
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		// Unset properties used for item schema.
		$params = self::get_profile_properties();
		foreach ( $params as $key => $param ) {
			unset( $params[ $key ]['context'] );
			unset( $params[ $key ]['readonly'] );
		}

		$params['context'] = $this->get_context_param( array( 'default' => 'view' ) );

		return apply_filters( 'woocommerce_rest_onboarding_profile_collection_params', $params );
	}
}

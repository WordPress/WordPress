<?php
/**
 * REST API Coupons controller
 *
 * Handles requests to the /coupons endpoint.
 *
 * @author   WooThemes
 * @category API
 * @package WooCommerce\RestApi
 * @since    3.0.0
 */

use Automattic\WooCommerce\Utilities\StringUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Coupons controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Posts_Controller
 */
class WC_REST_Coupons_V1_Controller extends WC_REST_Posts_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'coupons';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'shop_coupon';

	/**
	 * Coupons actions.
	 */
	public function __construct() {
		add_filter( "woocommerce_rest_{$this->post_type}_query", array( $this, 'query_args' ), 10, 2 );
	}

	/**
	 * Register the routes for coupons.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
				'args'                => array_merge( $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ), array(
					'code' => array(
						'description' => __( 'Coupon code.', 'woocommerce' ),
						'required'    => true,
						'type'        => 'string',
					),
				) ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
			'args' => array(
				'id' => array(
					'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => array(
					'context'         => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),
			array(
				'methods'         => WP_REST_Server::EDITABLE,
				'callback'        => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args'            => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
			),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				'args'                => array(
					'force' => array(
						'default'     => false,
						'type'        => 'boolean',
						'description' => __( 'Whether to bypass trash and force deletion.', 'woocommerce' ),
					),
				),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/batch', array(
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'batch_items' ),
				'permission_callback' => array( $this, 'batch_items_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
			),
			'schema' => array( $this, 'get_public_batch_schema' ),
		) );
	}

	/**
	 * Query args.
	 *
	 * @param array $args Query args
	 * @param WP_REST_Request $request Request data.
	 * @return array
	 */
	public function query_args( $args, $request ) {
		$coupon_code = $request['code'] ?? null;
		if ( ! StringUtil::is_null_or_whitespace( $coupon_code ) ) {
			$id               = wc_get_coupon_id_by_code( $coupon_code );
			$args['post__in'] = array( $id );
		}

		return $args;
	}

	/**
	 * Prepare a single coupon output for response.
	 *
	 * @param WP_Post $post Post object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $data
	 */
	public function prepare_item_for_response( $post, $request ) {
		$coupon = new WC_Coupon( (int) $post->ID );
		$_data  = $coupon->get_data();

		$format_decimal  = array( 'amount', 'minimum_amount', 'maximum_amount' );
		$format_date     = array( 'date_created', 'date_modified' );
		$format_date_utc = array( 'date_expires' );
		$format_null     = array( 'usage_limit', 'usage_limit_per_user' );

		// Format decimal values.
		foreach ( $format_decimal as $key ) {
			$_data[ $key ] = wc_format_decimal( $_data[ $key ], 2 );
		}

		// Format date values.
		foreach ( $format_date as $key ) {
			$_data[ $key ] = $_data[ $key ] ? wc_rest_prepare_date_response( $_data[ $key ], false ) : null;
		}
		foreach ( $format_date_utc as $key ) {
			$_data[ $key ] = $_data[ $key ] ? wc_rest_prepare_date_response( $_data[ $key ] ) : null;
		}

		// Format null values.
		foreach ( $format_null as $key ) {
			$_data[ $key ] = $_data[ $key ] ? $_data[ $key ] : null;
		}

		$data = array(
			'id'                          => $_data['id'],
			'code'                        => $_data['code'],
			'date_created'                => $_data['date_created'],
			'date_modified'               => $_data['date_modified'],
			'discount_type'               => $_data['discount_type'],
			'description'                 => $_data['description'],
			'amount'                      => $_data['amount'],
			'expiry_date'                 => $_data['date_expires'],
			'usage_count'                 => $_data['usage_count'],
			'individual_use'              => $_data['individual_use'],
			'product_ids'                 => $_data['product_ids'],
			'exclude_product_ids'         => $_data['excluded_product_ids'],
			'usage_limit'                 => $_data['usage_limit'],
			'usage_limit_per_user'        => $_data['usage_limit_per_user'],
			'limit_usage_to_x_items'      => $_data['limit_usage_to_x_items'],
			'free_shipping'               => $_data['free_shipping'],
			'product_categories'          => $_data['product_categories'],
			'excluded_product_categories' => $_data['excluded_product_categories'],
			'exclude_sale_items'          => $_data['exclude_sale_items'],
			'minimum_amount'              => $_data['minimum_amount'],
			'maximum_amount'              => $_data['maximum_amount'],
			'email_restrictions'          => $_data['email_restrictions'],
			'used_by'                     => $_data['used_by'],
		);

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $post, $request ) );

		/**
		 * Filter the data for a response.
		 *
		 * The dynamic portion of the hook name, $this->post_type, refers to post_type of the post being
		 * prepared for the response.
		 *
		 * @param WP_REST_Response   $response   The response object.
		 * @param WP_Post            $post       Post object.
		 * @param WP_REST_Request    $request    Request object.
		 */
		return apply_filters( "woocommerce_rest_prepare_{$this->post_type}", $response, $post, $request );
	}

	/**
	 * Only return writable props from schema.
	 * @param  array $schema
	 * @return bool
	 */
	protected function filter_writable_props( $schema ) {
		return empty( $schema['readonly'] );
	}

	/**
	 * Prepare a single coupon for create or update.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_Error|stdClass $data Post object.
	 */
	protected function prepare_item_for_database( $request ) {
		$id        = isset( $request['id'] ) ? absint( $request['id'] ) : 0;
		$coupon    = new WC_Coupon( $id );
		$schema    = $this->get_item_schema();
		$data_keys = array_keys( array_filter( $schema['properties'], array( $this, 'filter_writable_props' ) ) );

		// Update to schema to make compatible with CRUD schema.
		if ( $request['exclude_product_ids'] ) {
			$request['excluded_product_ids'] = $request['exclude_product_ids'];
		}
		if ( $request['expiry_date'] ) {
			$request['date_expires'] = $request['expiry_date'];
		}

		// Validate required POST fields.
		if ( 'POST' === $request->get_method() && 0 === $coupon->get_id() ) {
			if ( StringUtil::is_null_or_whitespace( $request['code'] ?? null ) ) {
				return new WP_Error( 'woocommerce_rest_empty_coupon_code', sprintf( __( 'The coupon code cannot be empty.', 'woocommerce' ), 'code' ), array( 'status' => 400 ) );
			}
		}

		// Handle all writable props.
		foreach ( $data_keys as $key ) {
			$value = $request[ $key ];

			if ( ! is_null( $value ) ) {
				switch ( $key ) {
					case 'code' :
						$coupon_code = wc_format_coupon_code( $value );
						$id          = $coupon->get_id() ? $coupon->get_id() : 0;
						$id_from_code = wc_get_coupon_id_by_code( $coupon_code, $id );

						if ( $id_from_code ) {
							return new WP_Error( 'woocommerce_rest_coupon_code_already_exists', __( 'The coupon code already exists', 'woocommerce' ), array( 'status' => 400 ) );
						}

						$coupon->set_code( $coupon_code );
						break;
					case 'description' :
						$coupon->set_description( wp_filter_post_kses( $value ) );
						break;
					case 'expiry_date' :
						$coupon->set_date_expires( $value );
						break;
					default :
						if ( is_callable( array( $coupon, "set_{$key}" ) ) ) {
							$coupon->{"set_{$key}"}( $value );
						}
						break;
				}
			}
		}

		/**
		 * Filter the query_vars used in `get_items` for the constructed query.
		 *
		 * The dynamic portion of the hook name, $this->post_type, refers to post_type of the post being
		 * prepared for insertion.
		 *
		 * @param WC_Coupon       $coupon        The coupon object.
		 * @param WP_REST_Request $request       Request object.
		 */
		return apply_filters( "woocommerce_rest_pre_insert_{$this->post_type}", $coupon, $request );
	}

	/**
	 * Create a single item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			/* translators: %s: post type */
			return new WP_Error( "woocommerce_rest_{$this->post_type}_exists", sprintf( __( 'Cannot create existing %s.', 'woocommerce' ), $this->post_type ), array( 'status' => 400 ) );
		}

		$coupon_id = $this->save_coupon( $request );
		if ( is_wp_error( $coupon_id ) ) {
			return $coupon_id;
		}

		$post = get_post( $coupon_id );
		$this->update_additional_fields_for_object( $post, $request );

		$this->add_post_meta_fields( $post, $request );

		/**
		 * Fires after a single item is created or updated via the REST API.
		 *
		 * @param WP_Post         $post      Post object.
		 * @param WP_REST_Request $request   Request object.
		 * @param boolean         $creating  True when creating item, false when updating.
		 */
		do_action( "woocommerce_rest_insert_{$this->post_type}", $post, $request, true );
		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $post, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $post->ID ) ) );

		return $response;
	}

	/**
	 * Update a single coupon.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_item( $request ) {
		try {
			$post_id = (int) $request['id'];

			if ( empty( $post_id ) || get_post_type( $post_id ) !== $this->post_type ) {
				return new WP_Error( "woocommerce_rest_{$this->post_type}_invalid_id", __( 'ID is invalid.', 'woocommerce' ), array( 'status' => 400 ) );
			}

			$coupon_id = $this->save_coupon( $request );
			if ( is_wp_error( $coupon_id ) ) {
				return $coupon_id;
			}

			$post = get_post( $coupon_id );
			$this->update_additional_fields_for_object( $post, $request );

			/**
			 * Fires after a single item is created or updated via the REST API.
			 *
			 * @param WP_Post         $post      Post object.
			 * @param WP_REST_Request $request   Request object.
			 * @param boolean         $creating  True when creating item, false when updating.
			 */
			do_action( "woocommerce_rest_insert_{$this->post_type}", $post, $request, false );
			$request->set_param( 'context', 'edit' );
			$response = $this->prepare_item_for_response( $post, $request );
			return rest_ensure_response( $response );

		} catch ( Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Saves a coupon to the database.
	 *
	 * @since 3.0.0
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|int
	 */
	protected function save_coupon( $request ) {
		try {
			$coupon = $this->prepare_item_for_database( $request );

			if ( is_wp_error( $coupon ) ) {
				return $coupon;
			}

			$coupon->save();
			return $coupon->get_id();
		} catch ( WC_Data_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), $e->getErrorData() );
		} catch ( WC_REST_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Get the Coupon's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->post_type,
			'type'       => 'object',
			'properties' => array(
				'id' => array(
					'description' => __( 'Unique identifier for the object.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'code' => array(
					'description' => __( 'Coupon code.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'date_created' => array(
					'description' => __( "The date the coupon was created, in the site's timezone.", 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_modified' => array(
					'description' => __( "The date the coupon was last modified, in the site's timezone.", 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'description' => array(
					'description' => __( 'Coupon description.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'discount_type' => array(
					'description' => __( 'Determines the type of discount that will be applied.', 'woocommerce' ),
					'type'        => 'string',
					'default'     => 'fixed_cart',
					'enum'        => array_keys( wc_get_coupon_types() ),
					'context'     => array( 'view', 'edit' ),
				),
				'amount' => array(
					'description' => __( 'The amount of discount. Should always be numeric, even if setting a percentage.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'expiry_date' => array(
					'description' => __( 'UTC DateTime when the coupon expires.', 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'usage_count' => array(
					'description' => __( 'Number of times the coupon has been used already.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'individual_use' => array(
					'description' => __( 'If true, the coupon can only be used individually. Other applied coupons will be removed from the cart.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'view', 'edit' ),
				),
				'product_ids' => array(
					'description' => __( "List of product IDs the coupon can be used on.", 'woocommerce' ),
					'type'        => 'array',
					'items'       => array(
						'type'    => 'integer',
					),
					'context'     => array( 'view', 'edit' ),
				),
				'exclude_product_ids' => array(
					'description' => __( "List of product IDs the coupon cannot be used on.", 'woocommerce' ),
					'type'        => 'array',
					'items'       => array(
						'type'    => 'integer',
					),
					'context'     => array( 'view', 'edit' ),
				),
				'usage_limit' => array(
					'description' => __( 'How many times the coupon can be used in total.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'usage_limit_per_user' => array(
					'description' => __( 'How many times the coupon can be used per customer.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'limit_usage_to_x_items' => array(
					'description' => __( 'Max number of items in the cart the coupon can be applied to.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'free_shipping' => array(
					'description' => __( 'If true and if the free shipping method requires a coupon, this coupon will enable free shipping.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'view', 'edit' ),
				),
				'product_categories' => array(
					'description' => __( "List of category IDs the coupon applies to.", 'woocommerce' ),
					'type'        => 'array',
					'items'       => array(
						'type'    => 'integer',
					),
					'context'     => array( 'view', 'edit' ),
				),
				'excluded_product_categories' => array(
					'description' => __( "List of category IDs the coupon does not apply to.", 'woocommerce' ),
					'type'        => 'array',
					'items'       => array(
						'type'    => 'integer',
					),
					'context'     => array( 'view', 'edit' ),
				),
				'exclude_sale_items' => array(
					'description' => __( 'If true, this coupon will not be applied to items that have sale prices.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'view', 'edit' ),
				),
				'minimum_amount' => array(
					'description' => __( 'Minimum order amount that needs to be in the cart before coupon applies.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'maximum_amount' => array(
					'description' => __( 'Maximum order amount allowed when using the coupon.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'email_restrictions' => array(
					'description' => __( 'List of email addresses that can use this coupon.', 'woocommerce' ),
					'type'        => 'array',
					'items'       => array(
						'type'    => 'string',
					),
					'context'     => array( 'view', 'edit' ),
				),
				'used_by' => array(
					'description' => __( 'List of user IDs (or guest email addresses) that have used the coupon.', 'woocommerce' ),
					'type'        => 'array',
					'items'       => array(
						'type'    => 'integer',
					),
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the query params for collections of attachments.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['code'] = array(
			'description'       => __( 'Limit result set to resources with a specific code.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}
}

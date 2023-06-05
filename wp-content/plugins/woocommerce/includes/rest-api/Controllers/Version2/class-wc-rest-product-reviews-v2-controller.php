<?php
/**
 * REST API Product Reviews Controller
 *
 * Handles requests to /products/<product_id>/reviews.
 *
 * @package WooCommerce\RestApi
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Product Reviews Controller Class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Product_Reviews_V1_Controller
 */
class WC_REST_Product_Reviews_V2_Controller extends WC_REST_Product_Reviews_V1_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v2';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'products/(?P<product_id>[\d]+)/reviews';

	/**
	 * Register the routes for product reviews.
	 */
	public function register_routes() {
		parent::register_routes();

		register_rest_route(
			$this->namespace, '/' . $this->rest_base . '/batch', array(
				'args'   => array(
					'product_id' => array(
						'description' => __( 'Unique identifier for the variable product.', 'woocommerce' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'batch_items' ),
					'permission_callback' => array( $this, 'batch_items_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				'schema' => array( $this, 'get_public_batch_schema' ),
			)
		);
	}

	/**
	 * Check if a given request has access to batch manage product reviews.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function batch_items_permissions_check( $request ) {
		if ( ! wc_rest_check_product_reviews_permissions( 'batch' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_batch', __( 'Sorry, you are not allowed to batch manipulate this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}
		return true;
	}

	/**
	 * Prepare a single product review output for response.
	 *
	 * @param WP_Comment      $review Product review object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $review, $request ) {
		$data = array(
			'id'               => (int) $review->comment_ID,
			'date_created'     => wc_rest_prepare_date_response( $review->comment_date ),
			'date_created_gmt' => wc_rest_prepare_date_response( $review->comment_date_gmt ),
			'review'           => $review->comment_content,
			'rating'           => (int) get_comment_meta( $review->comment_ID, 'rating', true ),
			'name'             => $review->comment_author,
			'email'            => $review->comment_author_email,
			'verified'         => wc_review_is_from_verified_owner( $review->comment_ID ),
		);

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $review, $request ) );

		/**
		 * Filter product reviews object returned from the REST API.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WP_Comment       $review   Product review object used to create response.
		 * @param WP_REST_Request  $request  Request object.
		 */
		return apply_filters( 'woocommerce_rest_prepare_product_review', $response, $review, $request );
	}


	/**
	 * Bulk create, update and delete items.
	 *
	 * @since  3.0.0
	 * @param WP_REST_Request $request Full details about the request.
	 * @return array Of WP_Error or WP_REST_Response.
	 */
	public function batch_items( $request ) {
		$items       = array_filter( $request->get_params() );
		$params      = $request->get_url_params();
		$product_id  = $params['product_id'];
		$body_params = array();

		foreach ( array( 'update', 'create', 'delete' ) as $batch_type ) {
			if ( ! empty( $items[ $batch_type ] ) ) {
				$injected_items = array();
				foreach ( $items[ $batch_type ] as $item ) {
					$injected_items[] = is_array( $item ) ? array_merge( array( 'product_id' => $product_id ), $item ) : $item;
				}
				$body_params[ $batch_type ] = $injected_items;
			}
		}

		$request = new WP_REST_Request( $request->get_method() );
		$request->set_body_params( $body_params );

		return parent::batch_items( $request );
	}

	/**
	 * Get the Product Review's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'product_review',
			'type'       => 'object',
			'properties' => array(
				'id'               => array(
					'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'review'           => array(
					'description' => __( 'The content of the review.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'date_created'     => array(
					'description' => __( "The date the review was created, in the site's timezone.", 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'date_created_gmt' => array(
					'description' => __( 'The date the review was created, as GMT.', 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'rating'           => array(
					'description' => __( 'Review rating (0 to 5).', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'name'             => array(
					'description' => __( 'Reviewer name.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'email'            => array(
					'description' => __( 'Reviewer email.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'verified'         => array(
					'description' => __( 'Shows if the reviewer bought the product or not.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}

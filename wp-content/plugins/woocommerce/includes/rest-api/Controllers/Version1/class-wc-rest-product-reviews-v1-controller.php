<?php
/**
 * REST API Product Reviews Controller
 *
 * Handles requests to /products/<product_id>/reviews.
 *
 * @author   WooThemes
 * @category API
 * @package WooCommerce\RestApi
 * @since    3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Product Reviews Controller Class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Controller
 */
class WC_REST_Product_Reviews_V1_Controller extends WC_REST_Controller {

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
	protected $rest_base = 'products/(?P<product_id>[\d]+)/reviews';

	/**
	 * Register the routes for product reviews.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			'args' => array(
				'product_id' => array(
					'description' => __( 'Unique identifier for the variable product.', 'woocommerce' ),
					'type'        => 'integer',
				),
				'id' => array(
					'description' => __( 'Unique identifier for the variation.', 'woocommerce' ),
					'type'        => 'integer',
				),
			),
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
					'review' => array(
						'required'    => true,
						'type'        => 'string',
						'description' => __( 'Review content.', 'woocommerce' ),
					),
					'name' => array(
						'required'    => true,
						'type'        => 'string',
						'description' => __( 'Name of the reviewer.', 'woocommerce' ),
					),
					'email' => array(
						'required'    => true,
						'type'        => 'string',
						'description' => __( 'Email of the reviewer.', 'woocommerce' ),
					),
				) ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
			'args' => array(
				'product_id' => array(
					'description' => __( 'Unique identifier for the variable product.', 'woocommerce' ),
					'type'        => 'integer',
				),
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
					'context' => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
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
	}

	/**
	 * Check whether a given request has permission to read webhook deliveries.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! wc_rest_check_product_reviews_permissions( 'read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to read a product review.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		if ( ! wc_rest_check_product_reviews_permissions( 'read', (int) $request['id'] ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to create a new product review.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! wc_rest_check_product_reviews_permissions( 'create' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_create', __( 'Sorry, you are not allowed to create resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to update a product review.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function update_item_permissions_check( $request ) {
		if ( ! wc_rest_check_product_reviews_permissions( 'edit', (int) $request['id'] ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_edit', __( 'Sorry, you cannot edit this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to delete a product review.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function delete_item_permissions_check( $request ) {
		if ( ! wc_rest_check_product_reviews_permissions( 'delete', (int) $request['id'] ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_delete', __( 'Sorry, you cannot delete this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Get all reviews from a product.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return array|WP_Error
	 */
	public function get_items( $request ) {
		$product_id = (int) $request['product_id'];

		if ( 'product' !== get_post_type( $product_id ) ) {
			return new WP_Error( 'woocommerce_rest_product_invalid_id', __( 'Invalid product ID.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$reviews = get_approved_comments( $product_id );
		$data    = array();
		foreach ( $reviews as $review_data ) {
			$review = $this->prepare_item_for_response( $review_data, $request );
			$review = $this->prepare_response_for_collection( $review );
			$data[] = $review;
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Get a single product review.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
		$id         = (int) $request['id'];
		$product_id = (int) $request['product_id'];

		if ( 'product' !== get_post_type( $product_id ) ) {
			return new WP_Error( 'woocommerce_rest_product_invalid_id', __( 'Invalid product ID.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$review = get_comment( $id );

		if ( empty( $id ) || empty( $review ) || intval( $review->comment_post_ID ) !== $product_id ) {
			return new WP_Error( 'woocommerce_rest_invalid_id', __( 'Invalid resource ID.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$delivery = $this->prepare_item_for_response( $review, $request );
		$response = rest_ensure_response( $delivery );

		return $response;
	}


	/**
	 * Create a product review.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {
		$product_id = (int) $request['product_id'];

		if ( 'product' !== get_post_type( $product_id ) ) {
			return new WP_Error( 'woocommerce_rest_product_invalid_id', __( 'Invalid product ID.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$prepared_review = $this->prepare_item_for_database( $request );

		/**
		 * Filter a product review (comment) before it is inserted via the REST API.
		 *
		 * Allows modification of the comment right before it is inserted via `wp_insert_comment`.
		 *
		 * @param array           $prepared_review The prepared comment data for `wp_insert_comment`.
		 * @param WP_REST_Request $request          Request used to insert the comment.
		 */
		$prepared_review = apply_filters( 'rest_pre_insert_product_review', $prepared_review, $request );

		$product_review_id = wp_insert_comment( $prepared_review );
		if ( ! $product_review_id ) {
			return new WP_Error( 'rest_product_review_failed_create', __( 'Creating product review failed.', 'woocommerce' ), array( 'status' => 500 ) );
		}

		update_comment_meta( $product_review_id, 'rating', ( ! empty( $request['rating'] ) ? $request['rating'] : '0' ) );

		$product_review = get_comment( $product_review_id );
		$this->update_additional_fields_for_object( $product_review, $request );

		/**
		 * Fires after a single item is created or updated via the REST API.
		 *
		 * @param WP_Comment      $product_review Inserted object.
		 * @param WP_REST_Request $request        Request object.
		 * @param boolean         $creating       True when creating item, false when updating.
		 */
		do_action( "woocommerce_rest_insert_product_review", $product_review, $request, true );

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $product_review, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );
		$base = str_replace( '(?P<product_id>[\d]+)', $product_id, $this->rest_base );
		$response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $base, $product_review_id ) ) );

		return $response;
	}

	/**
	 * Update a single product review.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_item( $request ) {
		$product_review_id = (int) $request['id'];
		$product_id        = (int) $request['product_id'];

		if ( 'product' !== get_post_type( $product_id ) ) {
			return new WP_Error( 'woocommerce_rest_product_invalid_id', __( 'Invalid product ID.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$review = get_comment( $product_review_id );

		if ( empty( $product_review_id ) || empty( $review ) || intval( $review->comment_post_ID ) !== $product_id ) {
			return new WP_Error( 'woocommerce_rest_product_review_invalid_id', __( 'Invalid resource ID.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$prepared_review = $this->prepare_item_for_database( $request );

		$updated = wp_update_comment( $prepared_review );
		if ( 0 === $updated ) {
			return new WP_Error( 'rest_product_review_failed_edit', __( 'Updating product review failed.', 'woocommerce' ), array( 'status' => 500 ) );
		}

		if ( ! empty( $request['rating'] ) ) {
			update_comment_meta( $product_review_id, 'rating', $request['rating'] );
		}

		$product_review = get_comment( $product_review_id );
		$this->update_additional_fields_for_object( $product_review, $request );

		/**
		 * Fires after a single item is created or updated via the REST API.
		 *
		 * @param WP_Comment         $comment      Inserted object.
		 * @param WP_REST_Request $request   Request object.
		 * @param boolean         $creating  True when creating item, false when updating.
		 */
		do_action( "woocommerce_rest_insert_product_review", $product_review, $request, true );

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $product_review, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Delete a product review.
	 *
	 * @param WP_REST_Request $request Full details about the request
	 *
	 * @return bool|WP_Error|WP_REST_Response
	 */
	public function delete_item( $request ) {
		$product_id        = (int) $request['product_id'];
		$product_review_id = (int) $request['id'];
		$force             = isset( $request['force'] ) ? (bool) $request['force']     : false;

		if ( 'product' !== get_post_type( $product_id ) ) {
			return new WP_Error( 'woocommerce_rest_product_invalid_id', __( 'Invalid product ID.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$product_review = get_comment( $product_review_id );
		if ( empty( $product_review_id ) || empty( $product_review->comment_ID ) || empty( $product_review->comment_post_ID ) ) {
			return new WP_Error( 'woocommerce_rest_product_review_invalid_id', __( 'Invalid product review ID.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		/**
		 * Filter whether a product review is trashable.
		 *
		 * Return false to disable trash support for the product review.
		 *
		 * @param boolean $supports_trash        Whether the object supports trashing.
		 * @param WP_Post $product_review        The object being considered for trashing support.
		 */
		$supports_trash = apply_filters( 'rest_product_review_trashable', ( EMPTY_TRASH_DAYS > 0 ), $product_review );

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $product_review, $request );

		if ( $force ) {
			$result = wp_delete_comment( $product_review_id, true );
		} else {
			if ( ! $supports_trash ) {
				return new WP_Error( 'rest_trash_not_supported', __( 'The product review does not support trashing.', 'woocommerce' ), array( 'status' => 501 ) );
			}

			if ( 'trash' === $product_review->comment_approved ) {
				return new WP_Error( 'rest_already_trashed', __( 'The comment has already been trashed.', 'woocommerce' ), array( 'status' => 410 ) );
			}

			$result = wp_trash_comment( $product_review->comment_ID );
		}

		if ( ! $result ) {
			return new WP_Error( 'rest_cannot_delete', __( 'The product review cannot be deleted.', 'woocommerce' ), array( 'status' => 500 ) );
		}

		/**
		 * Fires after a product review is deleted via the REST API.
		 *
		 * @param object           $product_review  The deleted item.
		 * @param WP_REST_Response $response        The response data.
		 * @param WP_REST_Request  $request         The request sent to the API.
		 */
		do_action( 'rest_delete_product_review', $product_review, $response, $request );

		return $response;
	}

	/**
	 * Prepare a single product review output for response.
	 *
	 * @param WP_Comment $review Product review object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $review, $request ) {
		$data = array(
			'id'           => (int) $review->comment_ID,
			'date_created' => wc_rest_prepare_date_response( $review->comment_date_gmt ),
			'review'       => $review->comment_content,
			'rating'       => (int) get_comment_meta( $review->comment_ID, 'rating', true ),
			'name'         => $review->comment_author,
			'email'        => $review->comment_author_email,
			'verified'     => wc_review_is_from_verified_owner( $review->comment_ID ),
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
	 * Prepare a single product review to be inserted into the database.
	 *
	 * @param  WP_REST_Request $request Request object.
	 * @return array|WP_Error  $prepared_review
	 */
	protected function prepare_item_for_database( $request ) {
		$prepared_review = array( 'comment_approved' => 1, 'comment_type' => 'review' );

		if ( isset( $request['id'] ) ) {
			$prepared_review['comment_ID'] = (int) $request['id'];
		}

		if ( isset( $request['review'] ) ) {
			$prepared_review['comment_content'] = $request['review'];
		}

		if ( isset( $request['product_id'] ) ) {
			$prepared_review['comment_post_ID'] = (int) $request['product_id'];
		}

		if ( isset( $request['name'] ) ) {
			$prepared_review['comment_author'] = $request['name'];
		}

		if ( isset( $request['email'] ) ) {
			$prepared_review['comment_author_email'] = $request['email'];
		}

		if ( isset( $request['date_created'] ) ) {
			$prepared_review['comment_date'] = $request['date_created'];
		}

		if ( isset( $request['date_created_gmt'] ) ) {
			$prepared_review['comment_date_gmt'] = $request['date_created_gmt'];
		}

		return apply_filters( 'rest_preprocess_product_review', $prepared_review, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param WP_Comment $review Product review object.
	 * @param WP_REST_Request $request Request object.
	 * @return array Links for the given product review.
	 */
	protected function prepare_links( $review, $request ) {
		$product_id = (int) $request['product_id'];
		$base       = str_replace( '(?P<product_id>[\d]+)', $product_id, $this->rest_base );
		$links      = array(
			'self' => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $base, $review->comment_ID ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $base ) ),
			),
			'up' => array(
				'href' => rest_url( sprintf( '/%s/products/%d', $this->namespace, $product_id ) ),
			),
		);

		return $links;
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
				'id' => array(
					'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'review' => array(
					'description' => __( 'The content of the review.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'date_created' => array(
					'description' => __( "The date the review was created, in the site's timezone.", 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'rating' => array(
					'description' => __( 'Review rating (0 to 5).', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'name' => array(
					'description' => __( 'Reviewer name.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'email' => array(
					'description' => __( 'Reviewer email.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'verified' => array(
					'description' => __( 'Shows if the reviewer bought the product or not.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}
}

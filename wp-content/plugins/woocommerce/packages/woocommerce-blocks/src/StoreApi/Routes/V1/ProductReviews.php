<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use WP_Comment_Query;
use Automattic\WooCommerce\StoreApi\Utilities\Pagination;

/**
 * ProductReviews class.
 */
class ProductReviews extends AbstractRoute {
	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'product-reviews';

	/**
	 * The routes schema.
	 *
	 * @var string
	 */
	const SCHEMA_TYPE = 'product-review';

	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/products/reviews';
	}

	/**
	 * Get method arguments for this REST route.
	 *
	 * @return array An array of endpoints.
	 */
	public function get_args() {
		return [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_response' ],
				'permission_callback' => '__return_true',
				'args'                => $this->get_collection_params(),
			],
			'schema' => [ $this->schema, 'get_public_item_schema' ],
		];
	}

	/**
	 * Get a collection of reviews.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_response( \WP_REST_Request $request ) {
		$prepared_args = array(
			'type'          => 'review',
			'status'        => 'approve',
			'no_found_rows' => false,
			'offset'        => $request['offset'],
			'order'         => $request['order'],
			'number'        => $request['per_page'],
			'post__in'      => $request['product_id'],
		);

		/**
		 * Map category id to list of product ids.
		 */
		if ( ! empty( $request['category_id'] ) ) {
			$category_ids = $request['category_id'];
			$child_ids    = [];
			foreach ( $category_ids as $category_id ) {
				$child_ids = array_merge( $child_ids, get_term_children( $category_id, 'product_cat' ) );
			}
			$category_ids              = array_unique( array_merge( $category_ids, $child_ids ) );
			$product_ids               = get_objects_in_term( $category_ids, 'product_cat' );
			$prepared_args['post__in'] = isset( $prepared_args['post__in'] ) ? array_merge( $prepared_args['post__in'], $product_ids ) : $product_ids;
		}

		if ( 'rating' === $request['orderby'] ) {
			$prepared_args['meta_query'] = array( // phpcs:ignore
				'relation' => 'OR',
				array(
					'key'     => 'rating',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => 'rating',
					'compare' => 'NOT EXISTS',
				),
			);
		}
		$prepared_args['orderby'] = $this->normalize_query_param( $request['orderby'] );

		if ( empty( $request['offset'] ) ) {
			$prepared_args['offset'] = $prepared_args['number'] * ( absint( $request['page'] ) - 1 );
		}

		$query            = new WP_Comment_Query();
		$query_result     = $query->query( $prepared_args );
		$response_objects = array();

		foreach ( $query_result as $review ) {
			$data               = $this->prepare_item_for_response( $review, $request );
			$response_objects[] = $this->prepare_response_for_collection( $data );
		}

		$total_reviews = (int) $query->found_comments;
		$max_pages     = (int) $query->max_num_pages;

		if ( $total_reviews < 1 ) {
			// Out-of-bounds, run the query again without LIMIT for total count.
			unset( $prepared_args['number'], $prepared_args['offset'] );

			$query                  = new WP_Comment_Query();
			$prepared_args['count'] = true;

			$total_reviews = $query->query( $prepared_args );
			$max_pages     = $request['per_page'] ? ceil( $total_reviews / $request['per_page'] ) : 1;
		}

		$response = rest_ensure_response( $response_objects );
		$response = ( new Pagination() )->add_headers( $response, $request, $total_reviews, $max_pages );

		return $response;
	}

	/**
	 * Prepends internal property prefix to query parameters to match our response fields.
	 *
	 * @param string $query_param Query parameter.
	 * @return string
	 */
	protected function normalize_query_param( $query_param ) {
		$prefix = 'comment_';

		switch ( $query_param ) {
			case 'id':
				$normalized = $prefix . 'ID';
				break;
			case 'product':
				$normalized = $prefix . 'post_ID';
				break;
			case 'rating':
				$normalized = 'meta_value_num';
				break;
			default:
				$normalized = $prefix . $query_param;
				break;
		}

		return $normalized;
	}

	/**
	 * Get the query params for collections of products.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params                       = array();
		$params['context']            = $this->get_context_param();
		$params['context']['default'] = 'view';

		$params['page'] = array(
			'description'       => __( 'Current page of the collection.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
			'minimum'           => 1,
		);

		$params['per_page'] = array(
			'description'       => __( 'Maximum number of items to be returned in result set. Defaults to no limit if left blank.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 10,
			'minimum'           => 0,
			'maximum'           => 100,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['offset'] = array(
			'description'       => __( 'Offset the result set by a specific number of items.', 'woocommerce' ),
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['order'] = array(
			'description'       => __( 'Order sort attribute ascending or descending.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'desc',
			'enum'              => array( 'asc', 'desc' ),
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['orderby'] = array(
			'description'       => __( 'Sort collection by object attribute.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'date',
			'enum'              => array(
				'date',
				'date_gmt',
				'id',
				'rating',
				'product',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['category_id'] = array(
			'description'       => __( 'Limit result set to reviews from specific category IDs.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['product_id'] = array(
			'description'       => __( 'Limit result set to reviews from specific product IDs.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}
}

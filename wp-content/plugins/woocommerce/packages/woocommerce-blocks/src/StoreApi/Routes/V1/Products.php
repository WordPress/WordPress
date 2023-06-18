<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\Utilities\Pagination;
use Automattic\WooCommerce\StoreApi\Utilities\ProductQuery;

/**
 * Products class.
 */
class Products extends AbstractRoute {
	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'products';

	/**
	 * The routes schema.
	 *
	 * @var string
	 */
	const SCHEMA_TYPE = 'product';

	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/products';
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
	 * Get a collection of posts and add the post title filter option to \WP_Query.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_response( \WP_REST_Request $request ) {
		$response      = new \WP_REST_Response();
		$product_query = new ProductQuery();

		// Only get objects during GET requests.
		if ( \WP_REST_Server::READABLE === $request->get_method() ) {
			$query_results    = $product_query->get_objects( $request );
			$response_objects = [];

			foreach ( $query_results['objects'] as $object ) {
				$data               = rest_ensure_response( $this->schema->get_item_response( $object ) );
				$response_objects[] = $this->prepare_response_for_collection( $data );
			}

			$response->set_data( $response_objects );
		} else {
			$query_results = $product_query->get_results( $request );
		}

		$response = ( new Pagination() )->add_headers( $response, $request, $query_results['total'], $query_results['pages'] );
		$response->header( 'Last-Modified', $product_query->get_last_modified() );

		return $response;
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param \WC_Product      $item Product object.
	 * @param \WP_REST_Request $request Request object.
	 * @return array
	 */
	protected function prepare_links( $item, $request ) {
		$links = array(
			'self'       => array(
				'href' => rest_url( $this->get_namespace() . $this->get_path() . '/' . $item->get_id() ),
			),
			'collection' => array(
				'href' => rest_url( $this->get_namespace() . $this->get_path() ),
			),
		);

		if ( $item->get_parent_id() ) {
			$links['up'] = array(
				'href' => rest_url( $this->get_namespace() . $this->get_path() . '/' . $item->get_parent_id() ),
			);
		}

		return $links;
	}

	/**
	 * Get the query params for collections of products.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params                       = [];
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

		$params['search'] = array(
			'description'       => __( 'Limit results to those matching a string.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['slug'] = array(
			'description'       => __( 'Limit result set to products with specific slug(s). Use commas to separate.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['after'] = array(
			'description'       => __( 'Limit response to resources created after a given ISO8601 compliant date.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['before'] = array(
			'description'       => __( 'Limit response to resources created before a given ISO8601 compliant date.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['date_column'] = array(
			'description'       => __( 'When limiting response using after/before, which date column to compare against.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'date',
			'enum'              => array(
				'date',
				'date_gmt',
				'modified',
				'modified_gmt',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['exclude'] = array(
			'description'       => __( 'Ensure result set excludes specific IDs.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => [],
			'sanitize_callback' => 'wp_parse_id_list',
		);

		$params['include'] = array(
			'description'       => __( 'Limit result set to specific ids.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => [],
			'sanitize_callback' => 'wp_parse_id_list',
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
				'modified',
				'id',
				'include',
				'title',
				'slug',
				'price',
				'popularity',
				'rating',
				'menu_order',
				'comment_count',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['parent'] = array(
			'description'       => __( 'Limit result set to those of particular parent IDs.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => [],
			'sanitize_callback' => 'wp_parse_id_list',
		);

		$params['parent_exclude'] = array(
			'description'       => __( 'Limit result set to all items except those of a particular parent ID.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'sanitize_callback' => 'wp_parse_id_list',
			'default'           => [],
		);

		$params['type'] = array(
			'description'       => __( 'Limit result set to products assigned a specific type.', 'woocommerce' ),
			'type'              => 'string',
			'enum'              => array_merge( array_keys( wc_get_product_types() ), [ 'variation' ] ),
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['sku'] = array(
			'description'       => __( 'Limit result set to products with specific SKU(s). Use commas to separate.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['featured'] = array(
			'description'       => __( 'Limit result set to featured products.', 'woocommerce' ),
			'type'              => 'boolean',
			'sanitize_callback' => 'wc_string_to_bool',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['category'] = array(
			'description'       => __( 'Limit result set to products assigned a specific category ID.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['category_operator'] = array(
			'description'       => __( 'Operator to compare product category terms.', 'woocommerce' ),
			'type'              => 'string',
			'enum'              => [ 'in', 'not_in', 'and' ],
			'default'           => 'in',
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => 'rest_validate_request_arg',
		);

		// If the $_REQUEST contains a taxonomy query, add it to the params and sanitize it.
		foreach ( $_REQUEST as $param => $value ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( str_starts_with( $param, '_unstable_tax_' ) && ! str_ends_with( $param, '_operator' ) ) {
				$params[ $param ] = array(
					'description'       => __( 'Limit result set to products assigned a specific category ID.', 'woocommerce' ),
					'type'              => 'string',
					'sanitize_callback' => 'wp_parse_id_list',
					'validate_callback' => 'rest_validate_request_arg',
				);
			}
			if ( str_starts_with( $param, '_unstable_tax_' ) && str_ends_with( $param, '_operator' ) ) {
				$params[ $param ] = array(
					'description'       => __( 'Operator to compare product category terms.', 'woocommerce' ),
					'type'              => 'string',
					'enum'              => [ 'in', 'not_in', 'and' ],
					'default'           => 'in',
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => 'rest_validate_request_arg',
				);
			}
		}

		$params['tag'] = array(
			'description'       => __( 'Limit result set to products assigned a specific tag ID.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['tag_operator'] = array(
			'description'       => __( 'Operator to compare product tags.', 'woocommerce' ),
			'type'              => 'string',
			'enum'              => [ 'in', 'not_in', 'and' ],
			'default'           => 'in',
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['on_sale'] = array(
			'description'       => __( 'Limit result set to products on sale.', 'woocommerce' ),
			'type'              => 'boolean',
			'sanitize_callback' => 'wc_string_to_bool',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['min_price'] = array(
			'description'       => __( 'Limit result set to products based on a minimum price, provided using the smallest unit of the currency.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['max_price'] = array(
			'description'       => __( 'Limit result set to products based on a maximum price, provided using the smallest unit of the currency.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['stock_status'] = array(
			'description' => __( 'Limit result set to products with specified stock status.', 'woocommerce' ),
			'type'        => 'array',
			'items'       => array(
				'type'              => 'string',
				'enum'              => array_keys( wc_get_product_stock_status_options() ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'default'     => [],
		);

		$params['attributes'] = array(
			'description' => __( 'Limit result set to products with selected global attributes.', 'woocommerce' ),
			'type'        => 'array',
			'items'       => array(
				'type'       => 'object',
				'properties' => array(
					'attribute' => array(
						'description'       => __( 'Attribute taxonomy name.', 'woocommerce' ),
						'type'              => 'string',
						'sanitize_callback' => 'wc_sanitize_taxonomy_name',
					),
					'term_id'   => array(
						'description'       => __( 'List of attribute term IDs.', 'woocommerce' ),
						'type'              => 'array',
						'items'             => [
							'type' => 'integer',
						],
						'sanitize_callback' => 'wp_parse_id_list',
					),
					'slug'      => array(
						'description'       => __( 'List of attribute slug(s). If a term ID is provided, this will be ignored.', 'woocommerce' ),
						'type'              => 'array',
						'items'             => [
							'type' => 'string',
						],
						'sanitize_callback' => 'wp_parse_slug_list',
					),
					'operator'  => array(
						'description' => __( 'Operator to compare product attribute terms.', 'woocommerce' ),
						'type'        => 'string',
						'enum'        => [ 'in', 'not_in', 'and' ],
					),
				),
			),
			'default'     => [],
		);

		$params['attribute_relation'] = array(
			'description'       => __( 'The logical relationship between attributes when filtering across multiple at once.', 'woocommerce' ),
			'type'              => 'string',
			'enum'              => [ 'in', 'and' ],
			'default'           => 'and',
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['catalog_visibility'] = array(
			'description'       => __( 'Determines if hidden or visible catalog products are shown.', 'woocommerce' ),
			'type'              => 'string',
			'enum'              => array( 'any', 'visible', 'catalog', 'search', 'hidden' ),
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['rating'] = array(
			'description'       => __( 'Limit result set to products with a certain average rating.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
				'enum' => range( 1, 5 ),
			),
			'default'           => [],
			'sanitize_callback' => 'wp_parse_id_list',
		);

		return $params;
	}
}

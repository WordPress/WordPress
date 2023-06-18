<?php
/**
 * REST API Product Variations Controller
 *
 * Handles requests to /products/variations.
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

/**
 * Product variations controller.
 *
 * @internal
 * @extends WC_REST_Product_Variations_Controller
 */
class ProductVariations extends \WC_REST_Product_Variations_Controller {
	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-analytics';

	/**
	 * Register the routes for products.
	 */
	public function register_routes() {
		parent::register_routes();

		// Add a route for listing variations without specifying the parent product ID.
		register_rest_route(
			$this->namespace,
			'/variations',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params           = parent::get_collection_params();
		$params['search'] = array(
			'description'       => __( 'Search by similar product name, sku, or attribute value.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		return $params;
	}

	/**
	 * Add in conditional search filters for variations.
	 *
	 * @internal
	 * @param string $where Where clause used to search posts.
	 * @param object $wp_query WP_Query object.
	 * @return string
	 */
	public static function add_wp_query_filter( $where, $wp_query ) {
		global $wpdb;

		$search = $wp_query->get( 'search' );
		if ( $search ) {
			$like       = '%' . $wpdb->esc_like( $search ) . '%';
			$conditions = array(
				$wpdb->prepare( "{$wpdb->posts}.post_title LIKE %s", $like ), // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$wpdb->prepare( 'attr_search_meta.meta_value LIKE %s', $like ), // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			);

			if ( wc_product_sku_enabled() ) {
				$conditions[] = $wpdb->prepare( 'wc_product_meta_lookup.sku LIKE %s', $like );
			}

			$where .= ' AND (' . implode( ' OR ', $conditions ) . ')';
		}

		return $where;
	}

	/**
	 * Join posts meta tables when variation search query is present.
	 *
	 * @internal
	 * @param string $join Join clause used to search posts.
	 * @param object $wp_query WP_Query object.
	 * @return string
	 */
	public static function add_wp_query_join( $join, $wp_query ) {
		global $wpdb;

		$search = $wp_query->get( 'search' );
		if ( $search ) {
			$join .= " LEFT JOIN {$wpdb->postmeta} AS attr_search_meta
						ON {$wpdb->posts}.ID = attr_search_meta.post_id
						AND attr_search_meta.meta_key LIKE 'attribute_%' ";
		}

		if ( wc_product_sku_enabled() && ! strstr( $join, 'wc_product_meta_lookup' ) ) {
			$join .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup
						ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
		}

		return $join;
	}

	/**
	 * Add product name and sku filtering to the WC API.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return array
	 */
	protected function prepare_objects_query( $request ) {
		$args = parent::prepare_objects_query( $request );

		if ( ! empty( $request['search'] ) ) {
			$args['search'] = $request['search'];
			unset( $args['s'] );
		}

		// Retrieve variations without specifying a parent product.
		if ( "/{$this->namespace}/variations" === $request->get_route() ) {
			unset( $args['post_parent'] );
		}

		return $args;
	}

	/**
	 * Get a collection of posts and add the post title filter option to WP_Query.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		add_filter( 'posts_where', array( __CLASS__, 'add_wp_query_filter' ), 10, 2 );
		add_filter( 'posts_join', array( __CLASS__, 'add_wp_query_join' ), 10, 2 );
		add_filter( 'posts_groupby', array( 'Automattic\WooCommerce\Admin\API\Products', 'add_wp_query_group_by' ), 10, 2 );
		$response = parent::get_items( $request );
		remove_filter( 'posts_where', array( __CLASS__, 'add_wp_query_filter' ), 10 );
		remove_filter( 'posts_join', array( __CLASS__, 'add_wp_query_join' ), 10 );
		remove_filter( 'posts_groupby', array( 'Automattic\WooCommerce\Admin\API\Products', 'add_wp_query_group_by' ), 10 );
		return $response;
	}

	/**
	 * Get the Product's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = parent::get_item_schema();

		$schema['properties']['name']      = array(
			'description' => __( 'Product parent name.', 'woocommerce' ),
			'type'        => 'string',
			'context'     => array( 'view', 'edit' ),
		);
		$schema['properties']['type']      = array(
			'description' => __( 'Product type.', 'woocommerce' ),
			'type'        => 'string',
			'default'     => 'variation',
			'enum'        => array( 'variation' ),
			'context'     => array( 'view', 'edit' ),
		);
		$schema['properties']['parent_id'] = array(
			'description' => __( 'Product parent ID.', 'woocommerce' ),
			'type'        => 'integer',
			'context'     => array( 'view', 'edit' ),
		);

		return $schema;
	}

	/**
	 * Prepare a single variation output for response.
	 *
	 * @param  WC_Data         $object  Object data.
	 * @param  WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_object_for_response( $object, $request ) {
		$context  = empty( $request['context'] ) ? 'view' : $request['context'];
		$response = parent::prepare_object_for_response( $object, $request );
		$data     = $response->get_data();

		$data['name']      = $object->get_name( $context );
		$data['type']      = $object->get_type();
		$data['parent_id'] = $object->get_parent_id( $context );

		$response->set_data( $data );

		return $response;
	}
}

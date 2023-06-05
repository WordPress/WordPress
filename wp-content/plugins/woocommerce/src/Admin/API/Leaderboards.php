<?php
/**
 * REST API Leaderboards Controller
 *
 * Handles requests to /leaderboards
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\Categories\DataStore as CategoriesDataStore;
use Automattic\WooCommerce\Admin\API\Reports\Coupons\DataStore as CouponsDataStore;
use Automattic\WooCommerce\Admin\API\Reports\Customers\DataStore as CustomersDataStore;
use Automattic\WooCommerce\Admin\API\Reports\Products\DataStore as ProductsDataStore;

/**
 * Leaderboards controller.
 *
 * @internal
 * @extends WC_REST_Data_Controller
 */
class Leaderboards extends \WC_REST_Data_Controller {
	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-analytics';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'leaderboards';

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
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/allowed',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_allowed_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_allowed_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<leaderboard>\w+)',
			array(
				'args' => array(
					'leaderboard' => array(
						'type' => 'string',
						'enum' => array( 'customers', 'coupons', 'categories', 'products' ),
					),
				),
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
	 * Get the data for the coupons leaderboard.
	 *
	 * @param int    $per_page Number of rows.
	 * @param string $after Items after date.
	 * @param string $before Items before date.
	 * @param string $persisted_query URL query string.
	 */
	protected function get_coupons_leaderboard( $per_page, $after, $before, $persisted_query ) {
		$coupons_data_store = new CouponsDataStore();
		$coupons_data       = $per_page > 0 ? $coupons_data_store->get_data(
			apply_filters(
				'woocommerce_analytics_coupons_query_args',
				array(
					'orderby'       => 'orders_count',
					'order'         => 'desc',
					'after'         => $after,
					'before'        => $before,
					'per_page'      => $per_page,
					'extended_info' => true,
				)
			)
		)->data : array();

		$rows = array();
		foreach ( $coupons_data as $coupon ) {
			$url_query   = wp_parse_args(
				array(
					'filter'  => 'single_coupon',
					'coupons' => $coupon['coupon_id'],
				),
				$persisted_query
			);
			$coupon_url  = wc_admin_url( '/analytics/coupons', $url_query );
			$coupon_code = isset( $coupon['extended_info'] ) && isset( $coupon['extended_info']['code'] ) ? $coupon['extended_info']['code'] : '';
			$rows[]      = array(
				array(
					'display' => "<a href='{$coupon_url}'>{$coupon_code}</a>",
					'value'   => $coupon_code,
				),
				array(
					'display' => wc_admin_number_format( $coupon['orders_count'] ),
					'value'   => $coupon['orders_count'],
				),
				array(
					'display' => wc_price( $coupon['amount'] ),
					'value'   => $coupon['amount'],
				),
			);
		}

		return array(
			'id'      => 'coupons',
			'label'   => __( 'Top Coupons - Number of Orders', 'woocommerce' ),
			'headers' => array(
				array(
					'label' => __( 'Coupon code', 'woocommerce' ),
				),
				array(
					'label' => __( 'Orders', 'woocommerce' ),
				),
				array(
					'label' => __( 'Amount discounted', 'woocommerce' ),
				),
			),
			'rows'    => $rows,
		);
	}

	/**
	 * Get the data for the categories leaderboard.
	 *
	 * @param int    $per_page Number of rows.
	 * @param string $after Items after date.
	 * @param string $before Items before date.
	 * @param string $persisted_query URL query string.
	 */
	protected function get_categories_leaderboard( $per_page, $after, $before, $persisted_query ) {
		$categories_data_store = new CategoriesDataStore();
		$categories_data       = $per_page > 0 ? $categories_data_store->get_data(
			apply_filters(
				'woocommerce_analytics_categories_query_args',
				array(
					'orderby'       => 'items_sold',
					'order'         => 'desc',
					'after'         => $after,
					'before'        => $before,
					'per_page'      => $per_page,
					'extended_info' => true,
				)
			)
		)->data : array();

		$rows = array();
		foreach ( $categories_data as $category ) {
			$url_query     = wp_parse_args(
				array(
					'filter'     => 'single_category',
					'categories' => $category['category_id'],
				),
				$persisted_query
			);
			$category_url  = wc_admin_url( '/analytics/categories', $url_query );
			$category_name = isset( $category['extended_info'] ) && isset( $category['extended_info']['name'] ) ? $category['extended_info']['name'] : '';
			$rows[]        = array(
				array(
					'display' => "<a href='{$category_url}'>{$category_name}</a>",
					'value'   => $category_name,
				),
				array(
					'display' => wc_admin_number_format( $category['items_sold'] ),
					'value'   => $category['items_sold'],
				),
				array(
					'display' => wc_price( $category['net_revenue'] ),
					'value'   => $category['net_revenue'],
				),
			);
		}

		return array(
			'id'      => 'categories',
			'label'   => __( 'Top categories - Items sold', 'woocommerce' ),
			'headers' => array(
				array(
					'label' => __( 'Category', 'woocommerce' ),
				),
				array(
					'label' => __( 'Items sold', 'woocommerce' ),
				),
				array(
					'label' => __( 'Net sales', 'woocommerce' ),
				),
			),
			'rows'    => $rows,
		);
	}

	/**
	 * Get the data for the customers leaderboard.
	 *
	 * @param int    $per_page Number of rows.
	 * @param string $after Items after date.
	 * @param string $before Items before date.
	 * @param string $persisted_query URL query string.
	 */
	protected function get_customers_leaderboard( $per_page, $after, $before, $persisted_query ) {
		$customers_data_store = new CustomersDataStore();
		$customers_data       = $per_page > 0 ? $customers_data_store->get_data(
			apply_filters(
				'woocommerce_analytics_customers_query_args',
				array(
					'orderby'      => 'total_spend',
					'order'        => 'desc',
					'order_after'  => $after,
					'order_before' => $before,
					'per_page'     => $per_page,
				)
			)
		)->data : array();

		$rows = array();
		foreach ( $customers_data as $customer ) {
			$url_query    = wp_parse_args(
				array(
					'filter'    => 'single_customer',
					'customers' => $customer['id'],
				),
				$persisted_query
			);
			$customer_url = wc_admin_url( '/analytics/customers', $url_query );
			$rows[]       = array(
				array(
					'display' => "<a href='{$customer_url}'>{$customer['name']}</a>",
					'value'   => $customer['name'],
				),
				array(
					'display' => wc_admin_number_format( $customer['orders_count'] ),
					'value'   => $customer['orders_count'],
				),
				array(
					'display' => wc_price( $customer['total_spend'] ),
					'value'   => $customer['total_spend'],
				),
			);
		}

		return array(
			'id'      => 'customers',
			'label'   => __( 'Top Customers - Total Spend', 'woocommerce' ),
			'headers' => array(
				array(
					'label' => __( 'Customer Name', 'woocommerce' ),
				),
				array(
					'label' => __( 'Orders', 'woocommerce' ),
				),
				array(
					'label' => __( 'Total Spend', 'woocommerce' ),
				),
			),
			'rows'    => $rows,
		);
	}

	/**
	 * Get the data for the products leaderboard.
	 *
	 * @param int    $per_page Number of rows.
	 * @param string $after Items after date.
	 * @param string $before Items before date.
	 * @param string $persisted_query URL query string.
	 */
	protected function get_products_leaderboard( $per_page, $after, $before, $persisted_query ) {
		$products_data_store = new ProductsDataStore();
		$products_data       = $per_page > 0 ? $products_data_store->get_data(
			apply_filters(
				'woocommerce_analytics_products_query_args',
				array(
					'orderby'       => 'items_sold',
					'order'         => 'desc',
					'after'         => $after,
					'before'        => $before,
					'per_page'      => $per_page,
					'extended_info' => true,
				)
			)
		)->data : array();

		$rows = array();
		foreach ( $products_data as $product ) {
			$url_query    = wp_parse_args(
				array(
					'filter'   => 'single_product',
					'products' => $product['product_id'],
				),
				$persisted_query
			);
			$product_url  = wc_admin_url( '/analytics/products', $url_query );
			$product_name = isset( $product['extended_info'] ) && isset( $product['extended_info']['name'] ) ? $product['extended_info']['name'] : '';
			$rows[]       = array(
				array(
					'display' => "<a href='{$product_url}'>{$product_name}</a>",
					'value'   => $product_name,
				),
				array(
					'display' => wc_admin_number_format( $product['items_sold'] ),
					'value'   => $product['items_sold'],
				),
				array(
					'display' => wc_price( $product['net_revenue'] ),
					'value'   => $product['net_revenue'],
				),
			);
		}

		return array(
			'id'      => 'products',
			'label'   => __( 'Top products - Items sold', 'woocommerce' ),
			'headers' => array(
				array(
					'label' => __( 'Product', 'woocommerce' ),
				),
				array(
					'label' => __( 'Items sold', 'woocommerce' ),
				),
				array(
					'label' => __( 'Net sales', 'woocommerce' ),
				),
			),
			'rows'    => $rows,
		);
	}

	/**
	 * Get an array of all leaderboards.
	 *
	 * @param int    $per_page Number of rows.
	 * @param string $after Items after date.
	 * @param string $before Items before date.
	 * @param string $persisted_query URL query string.
	 * @return array
	 */
	public function get_leaderboards( $per_page, $after, $before, $persisted_query ) {
		$leaderboards = array(
			$this->get_customers_leaderboard( $per_page, $after, $before, $persisted_query ),
			$this->get_coupons_leaderboard( $per_page, $after, $before, $persisted_query ),
			$this->get_categories_leaderboard( $per_page, $after, $before, $persisted_query ),
			$this->get_products_leaderboard( $per_page, $after, $before, $persisted_query ),
		);

		return apply_filters( 'woocommerce_leaderboards', $leaderboards, $per_page, $after, $before, $persisted_query );
	}

	/**
	 * Return all leaderboards.
	 *
	 * @param  WP_REST_Request $request Request data.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$persisted_query = json_decode( $request['persisted_query'], true );

		switch ( $request['leaderboard'] ) {
			case 'customers':
				$leaderboards = array( $this->get_customers_leaderboard( $request['per_page'], $request['after'], $request['before'], $persisted_query ) );
				break;
			case 'coupons':
				$leaderboards = array( $this->get_coupons_leaderboard( $request['per_page'], $request['after'], $request['before'], $persisted_query ) );
				break;
			case 'categories':
				$leaderboards = array( $this->get_categories_leaderboard( $request['per_page'], $request['after'], $request['before'], $persisted_query ) );
				break;
			case 'products':
				$leaderboards = array( $this->get_products_leaderboard( $request['per_page'], $request['after'], $request['before'], $persisted_query ) );
				break;
			default:
				$leaderboards = $this->get_leaderboards( $request['per_page'], $request['after'], $request['before'], $persisted_query );
				break;
		}

		$data = array();
		if ( ! empty( $leaderboards ) ) {
			foreach ( $leaderboards as $leaderboard ) {
				$response = $this->prepare_item_for_response( $leaderboard, $request );
				$data[]   = $this->prepare_response_for_collection( $response );
			}
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Returns a list of allowed leaderboards.
	 *
	 * @param  WP_REST_Request $request Request data.
	 * @return array|WP_Error
	 */
	public function get_allowed_items( $request ) {
		$leaderboards = $this->get_leaderboards( 0, null, null, null );

		$data = array();
		foreach ( $leaderboards as $leaderboard ) {
			$data[] = (object) array(
				'id'      => $leaderboard['id'],
				'label'   => $leaderboard['label'],
				'headers' => $leaderboard['headers'],
			);
		}

		$objects = array();
		foreach ( $data as $item ) {
			$prepared  = $this->prepare_item_for_response( $item, $request );
			$objects[] = $this->prepare_response_for_collection( $prepared );
		}

		$response = rest_ensure_response( $objects );
		$response->header( 'X-WP-Total', count( $data ) );
		$response->header( 'X-WP-TotalPages', 1 );

		$base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );

		return $response;
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
		return apply_filters( 'woocommerce_rest_prepare_leaderboard', $response, $item, $request );
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params                    = array();
		$params['page']            = array(
			'description'       => __( 'Current page of the collection.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
			'minimum'           => 1,
		);
		$params['per_page']        = array(
			'description'       => __( 'Maximum number of items to be returned in result set.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 5,
			'minimum'           => 1,
			'maximum'           => 20,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['after']           = array(
			'description'       => __( 'Limit response to resources published after a given ISO8601 compliant date.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['before']          = array(
			'description'       => __( 'Limit response to resources published before a given ISO8601 compliant date.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['persisted_query'] = array(
			'description'       => __( 'URL query to persist across links.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		return $params;
	}

	/**
	 * Get the schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'leaderboard',
			'type'       => 'object',
			'properties' => array(
				'id'      => array(
					'type'        => 'string',
					'description' => __( 'Leaderboard ID.', 'woocommerce' ),
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'label'   => array(
					'type'        => 'string',
					'description' => __( 'Displayed title for the leaderboard.', 'woocommerce' ),
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'headers' => array(
					'type'        => 'array',
					'description' => __( 'Table headers.', 'woocommerce' ),
					'context'     => array( 'view' ),
					'readonly'    => true,
					'items'       => array(
						'type'       => 'array',
						'properties' => array(
							'label' => array(
								'description' => __( 'Table column header.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
						),
					),
				),
				'rows'    => array(
					'type'        => 'array',
					'description' => __( 'Table rows.', 'woocommerce' ),
					'context'     => array( 'view' ),
					'readonly'    => true,
					'items'       => array(
						'type'       => 'array',
						'properties' => array(
							'display' => array(
								'description' => __( 'Table cell display.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
							'value'   => array(
								'description' => __( 'Table cell value.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
						),
					),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get schema for the list of allowed leaderboards.
	 *
	 * @return array $schema
	 */
	public function get_public_allowed_item_schema() {
		$schema = $this->get_public_item_schema();
		unset( $schema['properties']['rows'] );
		return $schema;
	}
}

<?php
/**
 * REST API Reports customers controller
 *
 * Handles requests to the /reports/customers endpoint.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Customers;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\ExportableTraits;
use Automattic\WooCommerce\Admin\API\Reports\ExportableInterface;
use Automattic\WooCommerce\Admin\API\Reports\TimeInterval;

/**
 * REST API Reports customers controller class.
 *
 * @internal
 * @extends WC_REST_Reports_Controller
 */
class Controller extends \WC_REST_Reports_Controller implements ExportableInterface {
	/**
	 * Exportable traits.
	 */
	use ExportableTraits;

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
	protected $rest_base = 'reports/customers';

	/**
	 * Maps query arguments from the REST request.
	 *
	 * @param array $request Request array.
	 * @return array
	 */
	protected function prepare_reports_query( $request ) {
		$args                        = array();
		$args['registered_before']   = $request['registered_before'];
		$args['registered_after']    = $request['registered_after'];
		$args['order_before']        = $request['before'];
		$args['order_after']         = $request['after'];
		$args['page']                = $request['page'];
		$args['per_page']            = $request['per_page'];
		$args['order']               = $request['order'];
		$args['orderby']             = $request['orderby'];
		$args['match']               = $request['match'];
		$args['search']              = $request['search'];
		$args['searchby']            = $request['searchby'];
		$args['name_includes']       = $request['name_includes'];
		$args['name_excludes']       = $request['name_excludes'];
		$args['username_includes']   = $request['username_includes'];
		$args['username_excludes']   = $request['username_excludes'];
		$args['email_includes']      = $request['email_includes'];
		$args['email_excludes']      = $request['email_excludes'];
		$args['country_includes']    = $request['country_includes'];
		$args['country_excludes']    = $request['country_excludes'];
		$args['last_active_before']  = $request['last_active_before'];
		$args['last_active_after']   = $request['last_active_after'];
		$args['orders_count_min']    = $request['orders_count_min'];
		$args['orders_count_max']    = $request['orders_count_max'];
		$args['total_spend_min']     = $request['total_spend_min'];
		$args['total_spend_max']     = $request['total_spend_max'];
		$args['avg_order_value_min'] = $request['avg_order_value_min'];
		$args['avg_order_value_max'] = $request['avg_order_value_max'];
		$args['last_order_before']   = $request['last_order_before'];
		$args['last_order_after']    = $request['last_order_after'];
		$args['customers']           = $request['customers'];
		$args['users']               = $request['users'];
		$args['force_cache_refresh'] = $request['force_cache_refresh'];

		$between_params_numeric    = array( 'orders_count', 'total_spend', 'avg_order_value' );
		$normalized_params_numeric = TimeInterval::normalize_between_params( $request, $between_params_numeric, false );
		$between_params_date       = array( 'last_active', 'registered' );
		$normalized_params_date    = TimeInterval::normalize_between_params( $request, $between_params_date, true );
		$args                      = array_merge( $args, $normalized_params_numeric, $normalized_params_date );

		return $args;
	}

	/**
	 * Get all reports.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return array|WP_Error
	 */
	public function get_items( $request ) {
		$query_args      = $this->prepare_reports_query( $request );
		$customers_query = new Query( $query_args );
		$report_data     = $customers_query->get_data();

		$data = array();

		foreach ( $report_data->data as $customer_data ) {
			$item   = $this->prepare_item_for_response( $customer_data, $request );
			$data[] = $this->prepare_response_for_collection( $item );
		}

		$response = rest_ensure_response( $data );
		$response->header( 'X-WP-Total', (int) $report_data->total );
		$response->header( 'X-WP-TotalPages', (int) $report_data->pages );

		$page      = $report_data->page_no;
		$max_pages = $report_data->pages;
		$base      = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );
		if ( $page > 1 ) {
			$prev_page = $page - 1;
			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}
			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );
			$response->link_header( 'next', $next_link );
		}

		return $response;
	}


	/**
	 * Get one report.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return array|WP_Error
	 */
	public function get_item( $request ) {
		$query_args              = $this->prepare_reports_query( $request );
		$query_args['customers'] = array( $request->get_param( 'id' ) );
		$customers_query         = new Query( $query_args );
		$report_data             = $customers_query->get_data();

		$data = array();

		foreach ( $report_data->data as $customer_data ) {
			$item   = $this->prepare_item_for_response( $customer_data, $request );
			$data[] = $this->prepare_response_for_collection( $item );
		}

		$response = rest_ensure_response( $data );
		$response->header( 'X-WP-Total', (int) $report_data->total );
		$response->header( 'X-WP-TotalPages', (int) $report_data->pages );

		return $response;
	}

	/**
	 * Prepare a report object for serialization.
	 *
	 * @param array           $report  Report data.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $report, $request ) {
		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $report, $request );
		// Registered date is UTC.
		$data['date_registered_gmt'] = wc_rest_prepare_date_response( $data['date_registered'] );
		$data['date_registered']     = wc_rest_prepare_date_response( $data['date_registered'], false );
		// Last active date is local time.
		$data['date_last_active_gmt'] = wc_rest_prepare_date_response( $data['date_last_active'], false );
		$data['date_last_active']     = wc_rest_prepare_date_response( $data['date_last_active'] );
		$data                         = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $report ) );
		/**
		 * Filter a report returned from the API.
		 *
		 * Allows modification of the report data right before it is returned.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param object           $report   The original report object.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 * @since 4.0.0
		 */
		return apply_filters( 'woocommerce_rest_prepare_report_customers', $response, $report, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param array $object Object data.
	 * @return array
	 */
	protected function prepare_links( $object ) {
		if ( empty( $object['user_id'] ) ) {
			return array();
		}

		return array(
			'customer'   => array(
				'href' => rest_url( sprintf( '/%s/customers/%d', $this->namespace, $object['id'] ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/customers', $this->namespace ) ),
			),
		);
	}

	/**
	 * Get the Report's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'report_customers',
			'type'       => 'object',
			'properties' => array(
				'id'                   => array(
					'description' => __( 'Customer ID.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'user_id'              => array(
					'description' => __( 'User ID.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'name'                 => array(
					'description' => __( 'Name.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'username'             => array(
					'description' => __( 'Username.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'country'              => array(
					'description' => __( 'Country / Region.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'city'                 => array(
					'description' => __( 'City.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'state'                => array(
					'description' => __( 'Region.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'postcode'             => array(
					'description' => __( 'Postal code.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_registered'      => array(
					'description' => __( 'Date registered.', 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_registered_gmt'  => array(
					'description' => __( 'Date registered GMT.', 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_last_active'     => array(
					'description' => __( 'Date last active.', 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_last_active_gmt' => array(
					'description' => __( 'Date last active GMT.', 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'orders_count'         => array(
					'description' => __( 'Order count.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'total_spend'          => array(
					'description' => __( 'Total spend.', 'woocommerce' ),
					'type'        => 'number',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'avg_order_value'      => array(
					'description' => __( 'Avg order value.', 'woocommerce' ),
					'type'        => 'number',
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
		$params                            = array();
		$params['context']                 = $this->get_context_param( array( 'default' => 'view' ) );
		$params['registered_before']       = array(
			'description'       => __( 'Limit response to objects registered before (or at) a given ISO8601 compliant datetime.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['registered_after']        = array(
			'description'       => __( 'Limit response to objects registered after (or at) a given ISO8601 compliant datetime.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['after']                   = array(
			'description'       => __( 'Limit response to resources with orders published after a given ISO8601 compliant date.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['before']                  = array(
			'description'       => __( 'Limit response to resources with orders published before a given ISO8601 compliant date.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['page']                    = array(
			'description'       => __( 'Current page of the collection.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
			'minimum'           => 1,
		);
		$params['per_page']                = array(
			'description'       => __( 'Maximum number of items to be returned in result set.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 10,
			'minimum'           => 1,
			'maximum'           => 100,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['order']                   = array(
			'description'       => __( 'Order sort attribute ascending or descending.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'desc',
			'enum'              => array( 'asc', 'desc' ),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['orderby']                 = array(
			'description'       => __( 'Sort collection by object attribute.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'date_registered',
			'enum'              => array(
				'username',
				'name',
				'country',
				'city',
				'state',
				'postcode',
				'date_registered',
				'date_last_active',
				'orders_count',
				'total_spend',
				'avg_order_value',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['match']                   = array(
			'description'       => __( 'Indicates whether all the conditions should be true for the resulting set, or if any one of them is sufficient. Match affects the following parameters: status_is, status_is_not, product_includes, product_excludes, coupon_includes, coupon_excludes, customer, categories', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'all',
			'enum'              => array(
				'all',
				'any',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['search']                  = array(
			'description'       => __( 'Limit response to objects with a customer field containing the search term. Searches the field provided by `searchby`.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['searchby']                = array(
			'description' => 'Limit results with `search` and `searchby` to specific fields containing the search term.',
			'type'        => 'string',
			'default'     => 'name',
			'enum'        => array(
				'name',
				'username',
				'email',
			),
		);
		$params['name_includes']           = array(
			'description'       => __( 'Limit response to objects with specific names.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['name_excludes']           = array(
			'description'       => __( 'Limit response to objects excluding specific names.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['username_includes']       = array(
			'description'       => __( 'Limit response to objects with specific usernames.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['username_excludes']       = array(
			'description'       => __( 'Limit response to objects excluding specific usernames.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['email_includes']          = array(
			'description'       => __( 'Limit response to objects including emails.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['email_excludes']          = array(
			'description'       => __( 'Limit response to objects excluding emails.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['country_includes']        = array(
			'description'       => __( 'Limit response to objects with specific countries.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['country_excludes']        = array(
			'description'       => __( 'Limit response to objects excluding specific countries.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['last_active_before']      = array(
			'description'       => __( 'Limit response to objects last active before (or at) a given ISO8601 compliant datetime.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['last_active_after']       = array(
			'description'       => __( 'Limit response to objects last active after (or at) a given ISO8601 compliant datetime.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['last_active_between']     = array(
			'description'       => __( 'Limit response to objects last active between two given ISO8601 compliant datetime.', 'woocommerce' ),
			'type'              => 'array',
			'validate_callback' => array( '\Automattic\WooCommerce\Admin\API\Reports\TimeInterval', 'rest_validate_between_date_arg' ),
			'items'             => array(
				'type' => 'string',
			),
		);
		$params['registered_before']       = array(
			'description'       => __( 'Limit response to objects registered before (or at) a given ISO8601 compliant datetime.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['registered_after']        = array(
			'description'       => __( 'Limit response to objects registered after (or at) a given ISO8601 compliant datetime.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['registered_between']      = array(
			'description'       => __( 'Limit response to objects last active between two given ISO8601 compliant datetime.', 'woocommerce' ),
			'type'              => 'array',
			'validate_callback' => array( '\Automattic\WooCommerce\Admin\API\Reports\TimeInterval', 'rest_validate_between_date_arg' ),
			'items'             => array(
				'type' => 'string',
			),
		);
		$params['orders_count_min']        = array(
			'description'       => __( 'Limit response to objects with an order count greater than or equal to given integer.', 'woocommerce' ),
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['orders_count_max']        = array(
			'description'       => __( 'Limit response to objects with an order count less than or equal to given integer.', 'woocommerce' ),
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['orders_count_between']    = array(
			'description'       => __( 'Limit response to objects with an order count between two given integers.', 'woocommerce' ),
			'type'              => 'array',
			'validate_callback' => array( '\Automattic\WooCommerce\Admin\API\Reports\TimeInterval', 'rest_validate_between_numeric_arg' ),
			'items'             => array(
				'type' => 'integer',
			),
		);
		$params['total_spend_min']         = array(
			'description'       => __( 'Limit response to objects with a total order spend greater than or equal to given number.', 'woocommerce' ),
			'type'              => 'number',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['total_spend_max']         = array(
			'description'       => __( 'Limit response to objects with a total order spend less than or equal to given number.', 'woocommerce' ),
			'type'              => 'number',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['total_spend_between']     = array(
			'description'       => __( 'Limit response to objects with a total order spend between two given numbers.', 'woocommerce' ),
			'type'              => 'array',
			'validate_callback' => array( '\Automattic\WooCommerce\Admin\API\Reports\TimeInterval', 'rest_validate_between_numeric_arg' ),
			'items'             => array(
				'type' => 'integer',
			),
		);
		$params['avg_order_value_min']     = array(
			'description'       => __( 'Limit response to objects with an average order spend greater than or equal to given number.', 'woocommerce' ),
			'type'              => 'number',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['avg_order_value_max']     = array(
			'description'       => __( 'Limit response to objects with an average order spend less than or equal to given number.', 'woocommerce' ),
			'type'              => 'number',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['avg_order_value_between'] = array(
			'description'       => __( 'Limit response to objects with an average order spend between two given numbers.', 'woocommerce' ),
			'type'              => 'array',
			'validate_callback' => array( '\Automattic\WooCommerce\Admin\API\Reports\TimeInterval', 'rest_validate_between_numeric_arg' ),
			'items'             => array(
				'type' => 'integer',
			),
		);
		$params['last_order_before']       = array(
			'description'       => __( 'Limit response to objects with last order before (or at) a given ISO8601 compliant datetime.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['last_order_after']        = array(
			'description'       => __( 'Limit response to objects with last order after (or at) a given ISO8601 compliant datetime.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['customers']               = array(
			'description'       => __( 'Limit result to items with specified customer ids.', 'woocommerce' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'type' => 'integer',
			),
		);
		$params['users']                   = array(
			'description'       => __( 'Limit result to items with specified user ids.', 'woocommerce' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'type' => 'integer',
			),
		);
		$params['force_cache_refresh'] = array(
			'description'       => __( 'Force retrieval of fresh data instead of from the cache.', 'woocommerce' ),
			'type'              => 'boolean',
			'sanitize_callback' => 'wp_validate_boolean',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}

	/**
	 * Get the column names for export.
	 *
	 * @return array Key value pair of Column ID => Label.
	 */
	public function get_export_columns() {
		$export_columns = array(
			'name'            => __( 'Name', 'woocommerce' ),
			'username'        => __( 'Username', 'woocommerce' ),
			'last_active'     => __( 'Last Active', 'woocommerce' ),
			'registered'      => __( 'Sign Up', 'woocommerce' ),
			'email'           => __( 'Email', 'woocommerce' ),
			'orders_count'    => __( 'Orders', 'woocommerce' ),
			'total_spend'     => __( 'Total Spend', 'woocommerce' ),
			'avg_order_value' => __( 'AOV', 'woocommerce' ),
			'country'         => __( 'Country / Region', 'woocommerce' ),
			'city'            => __( 'City', 'woocommerce' ),
			'region'          => __( 'Region', 'woocommerce' ),
			'postcode'        => __( 'Postal Code', 'woocommerce' ),
		);

		/**
		 * Filter to add or remove column names from the customers report for
		 * export.
		 *
		 * @since 1.6.0
		 */
		return apply_filters(
			'woocommerce_report_customers_export_columns',
			$export_columns
		);
	}

	/**
	 * Get the column values for export.
	 *
	 * @param array $item Single report item/row.
	 * @return array Key value pair of Column ID => Row Value.
	 */
	public function prepare_item_for_export( $item ) {
		$export_item = array(
			'name'            => $item['name'],
			'username'        => $item['username'],
			'last_active'     => $item['date_last_active'],
			'registered'      => $item['date_registered'],
			'email'           => $item['email'],
			'orders_count'    => $item['orders_count'],
			'total_spend'     => self::csv_number_format( $item['total_spend'] ),
			'avg_order_value' => self::csv_number_format( $item['avg_order_value'] ),
			'country'         => $item['country'],
			'city'            => $item['city'],
			'region'          => $item['state'],
			'postcode'        => $item['postcode'],
		);

		/**
		 * Filter the column values of an item being exported.
		 *
		 * @param object $export_item Key value pair of Column ID => Row Value.
		 * @param object $item        Single report item/row.
		 * @since 4.0.0
		 */
		return apply_filters(
			'woocommerce_report_customers_prepare_export_item',
			$export_item,
			$item
		);
	}
}

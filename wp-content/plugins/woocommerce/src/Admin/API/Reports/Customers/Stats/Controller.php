<?php
/**
 * REST API Reports customers stats controller
 *
 * Handles requests to the /reports/customers/stats endpoint.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Customers\Stats;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\TimeInterval;

/**
 * REST API Reports customers stats controller class.
 *
 * @internal
 * @extends WC_REST_Reports_Controller
 */
class Controller extends \WC_REST_Reports_Controller {
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
	protected $rest_base = 'reports/customers/stats';

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
		$args['match']               = $request['match'];
		$args['search']              = $request['search'];
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
		$args['fields']              = $request['fields'];
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
		$out_data        = array(
			'totals' => $report_data,
		);

		return rest_ensure_response( $out_data );
	}

	/**
	 * Prepare a report object for serialization.
	 *
	 * @param Array           $report  Report data.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $report, $request ) {
		$data = $report;

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		/**
		 * Filter a report returned from the API.
		 *
		 * Allows modification of the report data right before it is returned.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param object           $report   The original report object.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'woocommerce_rest_prepare_report_customers_stats', $response, $report, $request );
	}

	/**
	 * Get the Report's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		// @todo Should any of these be 'indicator's?
		$totals = array(
			'customers_count'     => array(
				'description' => __( 'Number of customers.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'avg_orders_count'    => array(
				'description' => __( 'Average number of orders.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'avg_total_spend'     => array(
				'description' => __( 'Average total spend per customer.', 'woocommerce' ),
				'type'        => 'number',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
				'format'      => 'currency',
			),
			'avg_avg_order_value' => array(
				'description' => __( 'Average AOV per customer.', 'woocommerce' ),
				'type'        => 'number',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
				'format'      => 'currency',
			),
		);

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'report_customers_stats',
			'type'       => 'object',
			'properties' => array(
				'totals' => array(
					'description' => __( 'Totals data.', 'woocommerce' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
					'properties'  => $totals,
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
		$params['fields']                  = array(
			'description'       => __( 'Limit stats fields to the specified items.', 'woocommerce' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_slug_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'type' => 'string',
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
}

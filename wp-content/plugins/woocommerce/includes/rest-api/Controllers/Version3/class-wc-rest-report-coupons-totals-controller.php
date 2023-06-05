<?php
/**
 * REST API Reports Coupons Totals controller
 *
 * Handles requests to the /reports/coupons/count endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   3.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Reports Coupons Totals controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Reports_Controller
 */
class WC_REST_Report_Coupons_Totals_Controller extends WC_REST_Reports_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'reports/coupons/totals';

	/**
	 * Get reports list.
	 *
	 * @since 3.5.0
	 * @return array
	 */
	protected function get_reports() {
		global $wpdb;

		$data = get_transient( 'rest_api_coupons_type_count' );
		if ( false !== $data ) {
			return $data;
		}

		$types = wc_get_coupon_types();
		$data  = array();

		foreach ( $types as $slug => $name ) {
			$results = $wpdb->get_results(
				$wpdb->prepare( "
					SELECT count(meta_id) AS total
					FROM $wpdb->postmeta
					WHERE meta_key = 'discount_type'
					AND meta_value = %s
				", $slug )
			);

			$total = isset( $results[0] ) ? (int) $results[0]->total : 0;

			$data[] = array(
				'slug'  => $slug,
				'name'  => $name,
				'total' => $total,
			);
		}

		set_transient( 'rest_api_coupons_type_count', $data, YEAR_IN_SECONDS );

		return $data;
	}

	/**
	 * Prepare a report object for serialization.
	 *
	 * @param  stdClass        $report Report data.
	 * @param  WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $report, $request ) {
		$data = array(
			'slug'  => $report->slug,
			'name'  => $report->name,
			'total' => $report->total,
		);

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
		return apply_filters( 'woocommerce_rest_prepare_report_coupons_count', $response, $report, $request );
	}

	/**
	 * Get the Report's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'report_coupon_total',
			'type'       => 'object',
			'properties' => array(
				'slug'  => array(
					'description' => __( 'An alphanumeric identifier for the resource.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'name'  => array(
					'description' => __( 'Coupon type name.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'total' => array(
					'description' => __( 'Amount of coupons.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}

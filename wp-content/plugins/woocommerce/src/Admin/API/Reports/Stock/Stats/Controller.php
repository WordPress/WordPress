<?php
/**
 * REST API Reports stock stats controller
 *
 * Handles requests to the /reports/stock/stats endpoint.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Stock\Stats;

defined( 'ABSPATH' ) || exit;

/**
 * REST API Reports stock stats controller class.
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
	protected $rest_base = 'reports/stock/stats';

	/**
	 * Get Stock Status Totals.
	 *
	 * @param  WP_REST_Request $request Request data.
	 * @return array|WP_Error
	 */
	public function get_items( $request ) {
		$stock_query = new Query();
		$report_data = $stock_query->get_data();
		$out_data    = array(
			'totals' => $report_data,
		);
		return rest_ensure_response( $out_data );
	}

	/**
	 * Prepare a report object for serialization.
	 *
	 * @param  WC_Product      $report  Report data.
	 * @param  WP_REST_Request $request Request object.
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
		 * @since 6.5.0
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WC_Product       $report   The original object.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'woocommerce_rest_prepare_report_stock_stats', $response, $report, $request );
	}

	/**
	 * Get the Report's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$totals = array(
			'products' => array(
				'description' => __( 'Number of products.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'lowstock' => array(
				'description' => __( 'Number of low stock products.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
		);

		$status_options = wc_get_product_stock_status_options();
		foreach ( $status_options as $status => $label ) {
			$totals[ $status ] = array(
				/* translators: Stock status. Example: "Number of low stock products */
				'description' => sprintf( __( 'Number of %s products.', 'woocommerce' ), $label ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			);
		}

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
		$params            = array();
		$params['context'] = $this->get_context_param( array( 'default' => 'view' ) );
		return $params;
	}
}

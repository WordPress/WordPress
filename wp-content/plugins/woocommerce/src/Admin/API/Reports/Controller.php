<?php
/**
 * REST API Reports controller extended by WC Admin plugin.
 *
 * Handles requests to the reports endpoint.
 */

namespace Automattic\WooCommerce\Admin\API\Reports;

defined( 'ABSPATH' ) || exit;

/**
 * REST API Reports controller class.
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
	protected $rest_base = 'reports';

	/**
	 * Get all reports.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return array|WP_Error
	 */
	public function get_items( $request ) {
		$data    = array();
		$reports = array(
			array(
				'slug'        => 'performance-indicators',
				'description' => __( 'Batch endpoint for getting specific performance indicators from `stats` endpoints.', 'woocommerce' ),
			),
			array(
				'slug'        => 'revenue/stats',
				'description' => __( 'Stats about revenue.', 'woocommerce' ),
			),
			array(
				'slug'        => 'orders/stats',
				'description' => __( 'Stats about orders.', 'woocommerce' ),
			),
			array(
				'slug'        => 'products',
				'description' => __( 'Products detailed reports.', 'woocommerce' ),
			),
			array(
				'slug'        => 'products/stats',
				'description' => __( 'Stats about products.', 'woocommerce' ),
			),
			array(
				'slug'        => 'variations',
				'description' => __( 'Variations detailed reports.', 'woocommerce' ),
			),
			array(
				'slug'        => 'variations/stats',
				'description' => __( 'Stats about variations.', 'woocommerce' ),
			),
			array(
				'slug'        => 'categories',
				'description' => __( 'Product categories detailed reports.', 'woocommerce' ),
			),
			array(
				'slug'        => 'categories/stats',
				'description' => __( 'Stats about product categories.', 'woocommerce' ),
			),
			array(
				'slug'        => 'coupons',
				'description' => __( 'Coupons detailed reports.', 'woocommerce' ),
			),
			array(
				'slug'        => 'coupons/stats',
				'description' => __( 'Stats about coupons.', 'woocommerce' ),
			),
			array(
				'slug'        => 'taxes',
				'description' => __( 'Taxes detailed reports.', 'woocommerce' ),
			),
			array(
				'slug'        => 'taxes/stats',
				'description' => __( 'Stats about taxes.', 'woocommerce' ),
			),
			array(
				'slug'        => 'downloads',
				'description' => __( 'Product downloads detailed reports.', 'woocommerce' ),
			),
			array(
				'slug'        => 'downloads/files',
				'description' => __( 'Product download files detailed reports.', 'woocommerce' ),
			),
			array(
				'slug'        => 'downloads/stats',
				'description' => __( 'Stats about product downloads.', 'woocommerce' ),
			),
			array(
				'slug'        => 'customers',
				'description' => __( 'Customers detailed reports.', 'woocommerce' ),
			),
		);

		/**
		 * Filter the list of allowed reports, so that data can be loaded from third party extensions in addition to WooCommerce core.
		 * Array items should be in format of array( 'slug' => 'downloads/stats', 'description' =>  '',
		 * 'url' => '', and 'path' => '/wc-ext/v1/...'.
		 *
		 * @param array $endpoints The list of allowed reports..
		 */
		$reports = apply_filters( 'woocommerce_admin_reports', $reports );

		foreach ( $reports as $report ) {
			if ( empty( $report['slug'] ) ) {
				continue;
			}

			if ( empty( $report['path'] ) ) {
				$report['path'] = '/' . $this->namespace . '/reports/' . $report['slug'];
			}

			// Allows a different admin page to be loaded here,
			// or allows an empty url if no report exists for a set of performance indicators.
			if ( ! isset( $report['url'] ) ) {
				if ( '/stats' === substr( $report['slug'], -6 ) ) {
					$url_slug = substr( $report['slug'], 0, -6 );
				} else {
					$url_slug = $report['slug'];
				}

				$report['url'] = '/analytics/' . $url_slug;
			}

			$item   = $this->prepare_item_for_response( (object) $report, $request );
			$data[] = $this->prepare_response_for_collection( $item );
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Get the order number for an order. If no filter is present for `woocommerce_order_number`, we can just return the ID.
	 * Returns the parent order number if the order is actually a refund.
	 *
	 * @param  int $order_id Order ID.
	 * @return string
	 */
	protected function get_order_number( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order instanceof \WC_Order && ! $order instanceof \WC_Order_Refund ) {
			return null;
		}

		if ( 'shop_order_refund' === $order->get_type() ) {
			$order = wc_get_order( $order->get_parent_id() );
		}

		if ( ! has_filter( 'woocommerce_order_number' ) ) {
			return $order->get_id();
		}

		return $order->get_order_number();
	}

	/**
	 * Get the order total with the related currency formatting.
	 * Returns the parent order total if the order is actually a refund.
	 *
	 * @param  int $order_id Order ID.
	 * @return string
	 */
	protected function get_total_formatted( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order instanceof \WC_Order && ! $order instanceof \WC_Order_Refund ) {
			return null;
		}

		if ( 'shop_order_refund' === $order->get_type() ) {
			$order = wc_get_order( $order->get_parent_id() );
		}

		return wp_strip_all_tags( html_entity_decode( $order->get_formatted_order_total() ), true );
	}

	/**
	 * Prepare a report object for serialization.
	 *
	 * @param stdClass        $report  Report data.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $report, $request ) {
		$data = array(
			'slug'        => $report->slug,
			'description' => $report->description,
			'path'        => $report->path,
		);

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );
		$response->add_links(
			array(
				'self'       => array(
					'href' => rest_url( $report->path ),
				),
				'report'     => array(
					'href' => $report->url,
				),
				'collection' => array(
					'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
				),
			)
		);

		/**
		 * Filter a report returned from the API.
		 *
		 * Allows modification of the report data right before it is returned.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param object           $report   The original report object.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'woocommerce_rest_prepare_report', $response, $report, $request );
	}

	/**
	 * Get the Report's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'report',
			'type'       => 'object',
			'properties' => array(
				'slug'        => array(
					'description' => __( 'An alphanumeric identifier for the resource.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'description' => array(
					'description' => __( 'A human-readable description of the resource.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'path'        => array(
					'description' => __( 'API path.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
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

	/**
	 * Get order statuses without prefixes.
	 * Includes unregistered statuses that have been marked "actionable".
	 *
	 * @internal
	 * @return array
	 */
	public static function get_order_statuses() {
		// Allow all statuses selected as "actionable" - this may include unregistered statuses.
		// See: https://github.com/woocommerce/woocommerce-admin/issues/5592.
		$actionable_statuses = get_option( 'woocommerce_actionable_order_statuses', array() );

		// See WC_REST_Orders_V2_Controller::get_collection_params() re: any/trash statuses.
		$registered_statuses = array_merge( array( 'any', 'trash' ), array_keys( self::get_order_status_labels() ) );

		// Merge the status arrays (using flip to avoid array_unique()).
		$allowed_statuses = array_keys( array_merge( array_flip( $registered_statuses ), array_flip( $actionable_statuses ) ) );

		return $allowed_statuses;
	}

	/**
	 * Get order statuses (and labels) without prefixes.
	 *
	 * @internal
	 * @return array
	 */
	public static function get_order_status_labels() {
		$order_statuses = array();

		foreach ( wc_get_order_statuses() as $key => $label ) {
			$new_key                    = str_replace( 'wc-', '', $key );
			$order_statuses[ $new_key ] = $label;
		}

		return $order_statuses;
	}
}

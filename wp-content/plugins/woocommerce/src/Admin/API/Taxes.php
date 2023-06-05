<?php
/**
 * REST API Taxes Controller
 *
 * Handles requests to /taxes/*
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

/**
 * Taxes controller.
 *
 * @internal
 * @extends WC_REST_Taxes_Controller
 */
class Taxes extends \WC_REST_Taxes_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-analytics';

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params            = parent::get_collection_params();
		$params['search']  = array(
			'description'       => __( 'Search by similar tax code.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['include'] = array(
			'description'       => __( 'Limit result set to items that have the specified rate ID(s) assigned.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'validate_callback' => 'rest_validate_request_arg',
		);
		return $params;
	}

	/**
	 * Get all taxes and allow filtering by tax code.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		global $wpdb;

		$prepared_args           = array();
		$prepared_args['order']  = $request['order'];
		$prepared_args['number'] = $request['per_page'];
		if ( ! empty( $request['offset'] ) ) {
			$prepared_args['offset'] = $request['offset'];
		} else {
			$prepared_args['offset'] = ( $request['page'] - 1 ) * $prepared_args['number'];
		}
		$orderby_possibles        = array(
			'id'    => 'tax_rate_id',
			'order' => 'tax_rate_order',
		);
		$prepared_args['orderby'] = $orderby_possibles[ $request['orderby'] ];
		$prepared_args['class']   = $request['class'];
		$prepared_args['search']  = $request['search'];
		$prepared_args['include'] = $request['include'];

		/**
		 * Filter arguments, before passing to $wpdb->get_results(), when querying taxes via the REST API.
		 *
		 * @param array           $prepared_args Array of arguments for $wpdb->get_results().
		 * @param WP_REST_Request $request       The current request.
		 */
		$prepared_args = apply_filters( 'woocommerce_rest_tax_query', $prepared_args, $request );

		$query = "
			SELECT *
			FROM {$wpdb->prefix}woocommerce_tax_rates
			WHERE 1 = 1
		";

		// Filter by tax class.
		if ( ! empty( $prepared_args['class'] ) ) {
			$class  = 'standard' !== $prepared_args['class'] ? sanitize_title( $prepared_args['class'] ) : '';
			$query .= " AND tax_rate_class = '$class'";
		}

		// Filter by tax code.
		$tax_code_search = $prepared_args['search'];
		if ( $tax_code_search ) {
			$code_like = '%' . $wpdb->esc_like( $tax_code_search ) . '%';
			$query    .= $wpdb->prepare( ' AND CONCAT_WS( "-", NULLIF(tax_rate_country, ""), NULLIF(tax_rate_state, ""), NULLIF(tax_rate_name, ""), NULLIF(tax_rate_priority, "") ) LIKE %s', $code_like );
		}

		// Filter by included tax rate IDs.
		$included_taxes = array_map( 'absint', $prepared_args['include'] );
		if ( ! empty( $included_taxes ) ) {
			$included_taxes = implode( ',', $prepared_args['include'] );
			$query         .= " AND tax_rate_id IN ({$included_taxes})";
		}

		// Order tax rates.
		$order_by = sprintf( ' ORDER BY %s', sanitize_key( $prepared_args['orderby'] ) );

		// Pagination.
		$pagination = sprintf( ' LIMIT %d, %d', $prepared_args['offset'], $prepared_args['number'] );

		// Query taxes.
		$results = $wpdb->get_results( $query . $order_by . $pagination ); // @codingStandardsIgnoreLine.

		$taxes = array();
		foreach ( $results as $tax ) {
			$data    = $this->prepare_item_for_response( $tax, $request );
			$taxes[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $taxes );

		// Store pagination values for headers then unset for count query.
		$per_page = (int) $prepared_args['number'];
		$page     = ceil( ( ( (int) $prepared_args['offset'] ) / $per_page ) + 1 );

		// Query only for ids.
		$wpdb->get_results( str_replace( 'SELECT *', 'SELECT tax_rate_id', $query ) ); // @codingStandardsIgnoreLine.

		// Calculate totals.
		$total_taxes = (int) $wpdb->num_rows;
		$response->header( 'X-WP-Total', (int) $total_taxes );
		$max_pages = ceil( $total_taxes / $per_page );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );
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
}

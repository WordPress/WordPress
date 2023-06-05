<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\Utilities\Pagination;
use WP_Term_Query;

/**
 * AbstractTermsRoute class.
 */
abstract class AbstractTermsRoute extends AbstractRoute {
	/**
	 * The routes schema.
	 *
	 * @var string
	 */
	const SCHEMA_TYPE = 'term';

	/**
	 * Get the query params for collections of attributes.
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

		$params['exclude'] = array(
			'description'       => __( 'Ensure result set excludes specific IDs.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
		);

		$params['include'] = array(
			'description'       => __( 'Limit result set to specific ids.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
		);

		$params['order'] = array(
			'description'       => __( 'Sort ascending or descending.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'asc',
			'enum'              => array( 'asc', 'desc' ),
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['orderby'] = array(
			'description'       => __( 'Sort by term property.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'name',
			'enum'              => array(
				'name',
				'slug',
				'count',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['hide_empty'] = array(
			'description' => __( 'If true, empty terms will not be returned.', 'woocommerce' ),
			'type'        => 'boolean',
			'default'     => true,
		);

		return $params;
	}

	/**
	 * Get terms matching passed in args.
	 *
	 * @param string           $taxonomy Taxonomy to get terms from.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response
	 */
	protected function get_terms_response( $taxonomy, $request ) {
		$page          = (int) $request['page'];
		$per_page      = $request['per_page'] ? (int) $request['per_page'] : 0;
		$prepared_args = array(
			'taxonomy'   => $taxonomy,
			'exclude'    => $request['exclude'],
			'include'    => $request['include'],
			'order'      => $request['order'],
			'orderby'    => $request['orderby'],
			'hide_empty' => (bool) $request['hide_empty'],
			'number'     => $per_page,
			'offset'     => $per_page > 0 ? ( $page - 1 ) * $per_page : 0,
			'search'     => $request['search'],
		);

		$term_query = new WP_Term_Query();
		$objects    = $term_query->query( $prepared_args );
		$return     = [];

		foreach ( $objects as $object ) {
			$data     = $this->prepare_item_for_response( $object, $request );
			$return[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $return );

		// See if pagination is needed before calculating.
		if ( $per_page > 0 && ( count( $objects ) === $per_page || $page > 1 ) ) {
			$term_count = $this->get_term_count( $taxonomy, $prepared_args );
			$response   = ( new Pagination() )->add_headers( $response, $request, $term_count, ceil( $term_count / $per_page ) );
		}

		return $response;
	}

	/**
	 * Get count of terms for current query.
	 *
	 * @param string $taxonomy Taxonomy to get terms from.
	 * @param array  $args Array of args to pass to wp_count_terms.
	 * @return int
	 */
	protected function get_term_count( $taxonomy, $args ) {
		$count_args = $args;
		unset( $count_args['number'], $count_args['offset'] );
		return (int) wp_count_terms( $taxonomy, $count_args );
	}
}

<?php
/**
 * REST API: WP_REST_Term_Search_Handler class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 5.6.0
 */

/**
 * Core class representing a search handler for terms in the REST API.
 *
 * @since 5.6.0
 *
 * @see WP_REST_Search_Handler
 */
class WP_REST_Term_Search_Handler extends WP_REST_Search_Handler {

	/**
	 * Constructor.
	 *
	 * @since 5.6.0
	 */
	public function __construct() {
		$this->type = 'term';

		$this->subtypes = array_values(
			get_taxonomies(
				array(
					'public'       => true,
					'show_in_rest' => true,
				),
				'names'
			)
		);
	}

	/**
	 * Searches the object type content for a given search request.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full REST request.
	 * @return array Associative array containing an `WP_REST_Search_Handler::RESULT_IDS` containing
	 *               an array of found IDs and `WP_REST_Search_Handler::RESULT_TOTAL` containing the
	 *               total count for the matching search results.
	 */
	public function search_items( WP_REST_Request $request ) {
		$taxonomies = $request[ WP_REST_Search_Controller::PROP_SUBTYPE ];
		if ( in_array( WP_REST_Search_Controller::TYPE_ANY, $taxonomies, true ) ) {
			$taxonomies = $this->subtypes;
		}

		$page     = (int) $request['page'];
		$per_page = (int) $request['per_page'];

		$query_args = array(
			'taxonomy'   => $taxonomies,
			'hide_empty' => false,
			'offset'     => ( $page - 1 ) * $per_page,
			'number'     => $per_page,
		);

		if ( ! empty( $request['search'] ) ) {
			$query_args['search'] = $request['search'];
		}

		/**
		 * Filters the query arguments for a REST API search request.
		 *
		 * Enables adding extra arguments or setting defaults for a term search request.
		 *
		 * @since 5.6.0
		 *
		 * @param array           $query_args Key value array of query var to query value.
		 * @param WP_REST_Request $request    The request used.
		 */
		$query_args = apply_filters( 'rest_term_search_query', $query_args, $request );

		$query       = new WP_Term_Query();
		$found_terms = $query->query( $query_args );
		$found_ids   = wp_list_pluck( $found_terms, 'term_id' );

		unset( $query_args['offset'], $query_args['number'] );

		$total = wp_count_terms( $query_args );

		// wp_count_terms() can return a falsey value when the term has no children.
		if ( ! $total ) {
			$total = 0;
		}

		return array(
			self::RESULT_IDS   => $found_ids,
			self::RESULT_TOTAL => $total,
		);
	}

	/**
	 * Prepares the search result for a given ID.
	 *
	 * @since 5.6.0
	 *
	 * @param int   $id     Item ID.
	 * @param array $fields Fields to include for the item.
	 * @return array Associative array containing all fields for the item.
	 */
	public function prepare_item( $id, array $fields ) {
		$term = get_term( $id );

		$data = array();

		if ( in_array( WP_REST_Search_Controller::PROP_ID, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_ID ] = (int) $id;
		}
		if ( in_array( WP_REST_Search_Controller::PROP_TITLE, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_TITLE ] = $term->name;
		}
		if ( in_array( WP_REST_Search_Controller::PROP_URL, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_URL ] = get_term_link( $id );
		}
		if ( in_array( WP_REST_Search_Controller::PROP_TYPE, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_TYPE ] = $term->taxonomy;
		}

		return $data;
	}

	/**
	 * Prepares links for the search result of a given ID.
	 *
	 * @since 5.6.0
	 *
	 * @param int $id Item ID.
	 * @return array Links for the given item.
	 */
	public function prepare_item_links( $id ) {
		$term = get_term( $id );

		$links = array();

		$item_route = rest_get_route_for_term( $term );
		if ( $item_route ) {
			$links['self'] = array(
				'href'       => rest_url( $item_route ),
				'embeddable' => true,
			);
		}

		$links['about'] = array(
			'href' => rest_url( sprintf( 'wp/v2/taxonomies/%s', $term->taxonomy ) ),
		);

		return $links;
	}
}

<?php
/**
 * REST API: WP_REST_Post_Format_Search_Handler class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 5.6.0
 */

/**
 * Core class representing a search handler for post formats in the REST API.
 *
 * @since 5.6.0
 *
 * @see WP_REST_Search_Handler
 */
class WP_REST_Post_Format_Search_Handler extends WP_REST_Search_Handler {

	/**
	 * Constructor.
	 *
	 * @since 5.6.0
	 */
	public function __construct() {
		$this->type = 'post-format';
	}

	/**
	 * Searches the post formats for a given search request.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full REST request.
	 * @return array {
	 *     Associative array containing found IDs and total count for the matching search results.
	 *
	 *     @type string[] $ids   Array containing slugs for the matching post formats.
	 *     @type int      $total Total count for the matching search results.
	 * }
	 */
	public function search_items( WP_REST_Request $request ) {
		$format_strings = get_post_format_strings();
		$format_slugs   = array_keys( $format_strings );

		$query_args = array();

		if ( ! empty( $request['search'] ) ) {
			$query_args['search'] = $request['search'];
		}

		/**
		 * Filters the query arguments for a REST API post format search request.
		 *
		 * Enables adding extra arguments or setting defaults for a post format search request.
		 *
		 * @since 5.6.0
		 *
		 * @param array           $query_args Key value array of query var to query value.
		 * @param WP_REST_Request $request    The request used.
		 */
		$query_args = apply_filters( 'rest_post_format_search_query', $query_args, $request );

		$found_ids = array();
		foreach ( $format_slugs as $index => $format_slug ) {
			if ( ! empty( $query_args['search'] ) ) {
				$format_string       = get_post_format_string( $format_slug );
				$format_slug_match   = stripos( $format_slug, $query_args['search'] ) !== false;
				$format_string_match = stripos( $format_string, $query_args['search'] ) !== false;
				if ( ! $format_slug_match && ! $format_string_match ) {
					continue;
				}
			}

			$format_link = get_post_format_link( $format_slug );
			if ( $format_link ) {
				$found_ids[] = $format_slug;
			}
		}

		$page     = (int) $request['page'];
		$per_page = (int) $request['per_page'];

		return array(
			self::RESULT_IDS   => array_slice( $found_ids, ( $page - 1 ) * $per_page, $per_page ),
			self::RESULT_TOTAL => count( $found_ids ),
		);
	}

	/**
	 * Prepares the search result for a given post format.
	 *
	 * @since 5.6.0
	 *
	 * @param string $id     Item ID, the post format slug.
	 * @param array  $fields Fields to include for the item.
	 * @return array {
	 *     Associative array containing fields for the post format based on the `$fields` parameter.
	 *
	 *     @type string $id    Optional. Post format slug.
	 *     @type string $title Optional. Post format name.
	 *     @type string $url   Optional. Post format permalink URL.
	 *     @type string $type  Optional. String 'post-format'.
	 *}
	 */
	public function prepare_item( $id, array $fields ) {
		$data = array();

		if ( in_array( WP_REST_Search_Controller::PROP_ID, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_ID ] = $id;
		}

		if ( in_array( WP_REST_Search_Controller::PROP_TITLE, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_TITLE ] = get_post_format_string( $id );
		}

		if ( in_array( WP_REST_Search_Controller::PROP_URL, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_URL ] = get_post_format_link( $id );
		}

		if ( in_array( WP_REST_Search_Controller::PROP_TYPE, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_TYPE ] = $this->type;
		}

		return $data;
	}

	/**
	 * Prepares links for the search result.
	 *
	 * @since 5.6.0
	 *
	 * @param string $id Item ID, the post format slug.
	 * @return array Links for the given item.
	 */
	public function prepare_item_links( $id ) {
		return array();
	}
}

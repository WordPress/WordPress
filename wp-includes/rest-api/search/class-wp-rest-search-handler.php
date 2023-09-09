<?php
/**
 * REST API: WP_REST_Search_Handler class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 5.0.0
 */

/**
 * Core base class representing a search handler for an object type in the REST API.
 *
 * @since 5.0.0
 */
<<<<<<< HEAD
#[AllowDynamicProperties]
=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
abstract class WP_REST_Search_Handler {

	/**
	 * Field containing the IDs in the search result.
	 */
	const RESULT_IDS = 'ids';

	/**
	 * Field containing the total count in the search result.
	 */
	const RESULT_TOTAL = 'total';

	/**
	 * Object type managed by this search handler.
	 *
	 * @since 5.0.0
	 * @var string
	 */
	protected $type = '';

	/**
	 * Object subtypes managed by this search handler.
	 *
	 * @since 5.0.0
<<<<<<< HEAD
	 * @var string[]
=======
	 * @var array
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 */
	protected $subtypes = array();

	/**
	 * Gets the object type managed by this search handler.
	 *
	 * @since 5.0.0
	 *
	 * @return string Object type identifier.
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Gets the object subtypes managed by this search handler.
	 *
	 * @since 5.0.0
	 *
<<<<<<< HEAD
	 * @return string[] Array of object subtype identifiers.
=======
	 * @return array Array of object subtype identifiers.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 */
	public function get_subtypes() {
		return $this->subtypes;
	}

	/**
	 * Searches the object type content for a given search request.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full REST request.
	 * @return array Associative array containing an `WP_REST_Search_Handler::RESULT_IDS` containing
	 *               an array of found IDs and `WP_REST_Search_Handler::RESULT_TOTAL` containing the
	 *               total count for the matching search results.
	 */
	abstract public function search_items( WP_REST_Request $request );

	/**
	 * Prepares the search result for a given ID.
	 *
	 * @since 5.0.0
<<<<<<< HEAD
	 * @since 5.6.0 The `$id` parameter can accept a string.
	 *
	 * @param int|string $id     Item ID.
	 * @param array      $fields Fields to include for the item.
=======
	 *
	 * @param int   $id     Item ID.
	 * @param array $fields Fields to include for the item.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 * @return array Associative array containing all fields for the item.
	 */
	abstract public function prepare_item( $id, array $fields );

	/**
	 * Prepares links for the search result of a given ID.
	 *
	 * @since 5.0.0
<<<<<<< HEAD
	 * @since 5.6.0 The `$id` parameter can accept a string.
	 *
	 * @param int|string $id Item ID.
=======
	 *
	 * @param int $id Item ID.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 * @return array Links for the given item.
	 */
	abstract public function prepare_item_links( $id );
}

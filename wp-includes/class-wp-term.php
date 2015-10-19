<?php
/**
 * Taxonomy API: WP_Term class
 *
 * @package WordPress
 * @subpackage Taxonomy
 * @since 4.4.0
 */

/**
 * Core class used to implement the WP_Term object.
 *
 * @since 4.4.0
 */
final class WP_Term {

	/**
	 * Term ID.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var int
	 */
	public $term_id;

	/**
	 * The term's name.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $name = '';

	/**
	 * The term's slug.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $slug = '';

	/**
	 * The term's term_group.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $term_group = '';

	/**
	 * Term Taxonomy ID.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var int
	 */
	public $term_taxonomy_id = 0;

	/**
	 * The term's taxonomy name.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $taxonomy = '';

	/**
	 * The term's description.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $description = '';

	/**
	 * ID of a term's parent term.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var int
	 */
	public $parent = 0;

	/**
	 * Cached object count for this term.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var int
	 */
	public $count = 0;

	/**
	 * Stores the term object's sanitization level.
	 *
	 * Does not correspond to a database field.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $filter = 'raw';

	/**
	 * Retrieve WP_Term instance.
	 *
	 * @since 4.4.0
	 * @access public
	 * @static
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param int $term_id Term ID.
	 * @return WP_Term|false Term object, false otherwise.
	 */
	public static function get_instance( $term_id ) {
		global $wpdb;

		$term_id = (int) $term_id;
		if ( ! $term_id ) {
			return false;
		}

		$_term = wp_cache_get( $term_id, 'terms' );

		// If there isn't a cached version, hit the database.
		if ( ! $_term ) {
			$_term = $wpdb->get_row( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE t.term_id = %d LIMIT 1", $term_id ) );
			if ( ! $_term ) {
				return false;
			}

			$_term = sanitize_term( $_term, $_term->taxonomy, 'raw' );
			wp_cache_add( $term_id, $_term, 'terms' );
		}

		$term_obj = new WP_Term( $_term );
		$term_obj->filter( $term_obj->filter );

		return $term_obj;
	}

	/**
	 * Constructor.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param WP_Term|object $term Term object.
	 */
	public function __construct( $term ) {
		foreach ( get_object_vars( $term ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Sanitizes term fields, according to the filter type provided.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $filter Filter context. Accepts 'edit', 'db', 'display', 'attribute', 'js', 'raw'.
	 */
	public function filter( $filter ) {
		sanitize_term( $this, $this->taxonomy, $filter );
	}

	/**
	 * Converts an object to array.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array Object as array.
	 */
	public function to_array() {
		return get_object_vars( $this );
	}

	/**
	 * Getter.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		switch ( $key ) {
			case 'data' :
				$data = new stdClass();
				$columns = array( 'term_id', 'name', 'slug', 'term_group', 'term_taxonomy_id', 'taxonomy', 'description', 'parent', 'count' );
				foreach ( $columns as $column ) {
					$data->{$column} = isset( $this->{$column} ) ? $this->{$column} : null;
				}

				return sanitize_term( $data, $data->taxonomy, 'raw' );
				break;
		}
	}
}

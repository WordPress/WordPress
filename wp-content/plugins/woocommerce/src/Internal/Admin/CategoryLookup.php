<?php
/**
 * Keeps the product category lookup table in sync with live data.
 */

namespace Automattic\WooCommerce\Internal\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * \Automattic\WooCommerce\Internal\Admin\CategoryLookup class.
 */
class CategoryLookup {

	/**
	 * Stores changes to categories we need to sync.
	 *
	 * @var array
	 */
	protected $edited_product_cats = array();

	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function __construct() {}

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Init hooks.
	 */
	public function init() {
		add_action( 'generate_category_lookup_table', array( $this, 'regenerate' ) );
		add_action( 'edit_product_cat', array( $this, 'before_edit' ), 99 );
		add_action( 'edited_product_cat', array( $this, 'on_edit' ), 99 );
		add_action( 'created_product_cat', array( $this, 'on_create' ), 99 );
		add_action( 'init', array( $this, 'define_category_lookup_tables_in_wpdb' ) );
	}

	/**
	 * Regenerate all lookup table data.
	 */
	public function regenerate() {
		global $wpdb;

		$wpdb->query( "TRUNCATE TABLE $wpdb->wc_category_lookup" );

		$terms = get_terms(
			'product_cat',
			array(
				'hide_empty' => false,
				'fields'     => 'id=>parent',
			)
		);

		$hierarchy = array();
		$inserts   = array();

		$this->unflatten_terms( $hierarchy, $terms, 0 );
		$this->get_term_insert_values( $inserts, $hierarchy );

		if ( ! $inserts ) {
			return;
		}

		$insert_string = implode(
			'),(',
			array_map(
				function( $item ) {
					return implode( ',', $item );
				},
				$inserts
			)
		);

		$wpdb->query( "INSERT IGNORE INTO $wpdb->wc_category_lookup (category_tree_id,category_id) VALUES ({$insert_string})" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Store edits so we know when the parent ID changes.
	 *
	 * @param int $category_id Term ID being edited.
	 */
	public function before_edit( $category_id ) {
		$category                                  = get_term( $category_id, 'product_cat' );
		$this->edited_product_cats[ $category_id ] = $category->parent;
	}

	/**
	 * When a product category gets edited, see if we need to sync the table.
	 *
	 * @param int $category_id Term ID being edited.
	 */
	public function on_edit( $category_id ) {
		global $wpdb;

		if ( ! isset( $this->edited_product_cats[ $category_id ] ) ) {
			return;
		}

		$category_object = get_term( $category_id, 'product_cat' );
		$prev_parent     = $this->edited_product_cats[ $category_id ];
		$new_parent      = $category_object->parent;

		// No edits - no need to modify relationships.
		if ( $prev_parent === $new_parent ) {
			return;
		}

		$this->delete( $category_id, $prev_parent );
		$this->update( $category_id );
	}

	/**
	 * When a product category gets created, add a new lookup row.
	 *
	 * @param int $category_id Term ID being created.
	 */
	public function on_create( $category_id ) {
		// If WooCommerce is being installed on a multisite, lookup tables haven't been created yet.
		if ( 'yes' === get_transient( 'wc_installing' ) ) {
			return;
		}

		$this->update( $category_id );
	}

	/**
	 * Delete lookup table data from a tree.
	 *
	 * @param int $category_id Category ID to delete.
	 * @param int $category_tree_id Tree to delete from.
	 * @return void
	 */
	protected function delete( $category_id, $category_tree_id ) {
		global $wpdb;

		if ( ! $category_tree_id ) {
			return;
		}

		$ancestors   = get_ancestors( $category_tree_id, 'product_cat', 'taxonomy' );
		$ancestors[] = $category_tree_id;
		$children    = get_term_children( $category_id, 'product_cat' );
		$children[]  = $category_id;
		$id_list     = implode( ',', array_map( 'intval', array_unique( array_filter( $children ) ) ) );

		foreach ( $ancestors as $ancestor ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->wc_category_lookup WHERE category_tree_id = %d AND category_id IN ({$id_list})", $ancestor ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}
	}

	/**
	 * Updates lookup table data for a category by ID.
	 *
	 * @param int $category_id Category ID to update.
	 */
	protected function update( $category_id ) {
		global $wpdb;

		$ancestors    = get_ancestors( $category_id, 'product_cat', 'taxonomy' );
		$children     = get_term_children( $category_id, 'product_cat' );
		$inserts      = array();
		$inserts[]    = $this->get_insert_sql( $category_id, $category_id );
		$children_ids = array_map( 'intval', array_unique( array_filter( $children ) ) );

		foreach ( $ancestors as $ancestor ) {
			$inserts[] = $this->get_insert_sql( $category_id, $ancestor );

			foreach ( $children_ids as $child_category_id ) {
				$inserts[] = $this->get_insert_sql( $child_category_id, $ancestor );
			}
		}

		$insert_string = implode( ',', $inserts );

		$wpdb->query( "INSERT IGNORE INTO $wpdb->wc_category_lookup (category_id, category_tree_id) VALUES {$insert_string}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Get category lookup table values to insert.
	 *
	 * @param int $category_id Category ID to insert.
	 * @param int $category_tree_id Tree to insert into.
	 * @return string
	 */
	protected function get_insert_sql( $category_id, $category_tree_id ) {
		global $wpdb;
		return $wpdb->prepare( '(%d,%d)', $category_id, $category_tree_id );
	}

	/**
	 * Used to construct insert query recursively.
	 *
	 * @param  array $inserts Array of data to insert.
	 * @param  array $terms   Terms to insert.
	 * @param  array $parents Parent IDs the terms belong to.
	 */
	protected function get_term_insert_values( &$inserts, $terms, $parents = array() ) {
		foreach ( $terms as $term ) {
			$insert_parents = array_merge( array( $term['term_id'] ), $parents );

			foreach ( $insert_parents as $parent ) {
				$inserts[] = array(
					$parent,
					$term['term_id'],
				);
			}

			$this->get_term_insert_values( $inserts, $term['descendants'], $insert_parents );
		}
	}

	/**
	 * Convert flat terms array into nested array.
	 *
	 * @param array   $hierarchy Array to put terms into.
	 * @param array   $terms Array of terms (id=>parent).
	 * @param integer $parent Parent ID.
	 */
	protected function unflatten_terms( &$hierarchy, &$terms, $parent = 0 ) {
		foreach ( $terms as $term_id => $parent_id ) {
			if ( (int) $parent_id === $parent ) {
				$hierarchy[ $term_id ] = array(
					'term_id'     => $term_id,
					'descendants' => array(),
				);
				unset( $terms[ $term_id ] );
			}
		}
		foreach ( $hierarchy as $term_id => $terms_array ) {
			$this->unflatten_terms( $hierarchy[ $term_id ]['descendants'], $terms, $term_id );
		}
	}

	/**
	 * Get category descendants.
	 *
	 * @param int $category_id The category ID to lookup.
	 * @return array
	 */
	protected function get_descendants( $category_id ) {
		global $wpdb;

		return wp_parse_id_list(
			$wpdb->get_col(
				$wpdb->prepare(
					"SELECT category_id FROM $wpdb->wc_category_lookup WHERE category_tree_id = %d",
					$category_id
				)
			)
		);
	}

	/**
	 * Return all ancestor category ids for a category.
	 *
	 * @param int $category_id The category ID to lookup.
	 * @return array
	 */
	protected function get_ancestors( $category_id ) {
		global $wpdb;

		return wp_parse_id_list(
			$wpdb->get_col(
				$wpdb->prepare(
					"SELECT category_tree_id FROM $wpdb->wc_category_lookup WHERE category_id = %d",
					$category_id
				)
			)
		);
	}

	/**
	 * Add category lookup table to $wpdb object.
	 */
	public static function define_category_lookup_tables_in_wpdb() {
		global $wpdb;

		// List of tables without prefixes.
		$tables = array(
			'wc_category_lookup' => 'wc_category_lookup',
		);

		foreach ( $tables as $name => $table ) {
			$wpdb->$name    = $wpdb->prefix . $table;
			$wpdb->tables[] = $table;
		}
	}
}

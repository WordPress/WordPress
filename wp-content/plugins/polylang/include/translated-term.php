<?php

/**
 * Setups the taxonomies languages and translations model
 *
 * @since 1.8
 */
class PLL_Translated_Term extends PLL_Translated_Object {

	/**
	 * Constructor
	 *
	 * @since 1.8
	 *
	 * @param object $model
	 */
	public function __construct( &$model ) {
		$this->object_type = 'term';
		$this->tax_language = 'term_language';
		$this->tax_translations = 'term_translations';
		$this->tax_tt = 'tl_term_taxonomy_id';

		parent::__construct( $model );

		// Filters to prime terms cache
		add_filter( 'get_terms', array( $this, '_prime_terms_cache' ), 10, 2 );
		add_filter( 'wp_get_object_terms', array( $this, 'wp_get_object_terms' ), 10, 3 );

		add_action( 'clean_term_cache', array( $this, 'clean_term_cache' ) );
	}

	/**
	 * Stores the term language in the database
	 *
	 * @since 0.6
	 *
	 * @param int               $term_id term id
	 * @param int|string|object $lang    language ( term_id or slug or object )
	 */
	public function set_language( $term_id, $lang ) {
		$term_id = (int) $term_id;
		wp_set_object_terms( $term_id, $lang ? $this->model->get_language( $lang )->tl_term_id : '', 'term_language' );

		// Add translation group for correct WXR export
		$translations = $this->get_translations( $term_id );
		if ( $slug = array_search( $term_id, $translations ) ) {
			unset( $translations[ $slug ] );
		}

		$this->save_translations( $term_id, $translations );
	}

	/**
	 * Removes the term language in database
	 *
	 * @since 0.5
	 *
	 * @param int $term_id term id
	 */
	public function delete_language( $term_id ) {
		wp_delete_object_term_relationships( $term_id, 'term_language' );
	}

	/**
	 * Returns the language of a term
	 *
	 * @since 0.1
	 *
	 * @param int|string $value    term id or term slug
	 * @param string     $taxonomy optional taxonomy needed when the term slug is passed as first parameter
	 * @return bool|object PLL_Language object, false if no language is associated to that term
	 */
	public function get_language( $value, $taxonomy = '' ) {
		if ( is_numeric( $value ) ) {
			$term_id = $value;
		}

		// get_term_by still not cached in WP 3.5.1 but internally, the function is always called by term_id
		elseif ( is_string( $value ) && $taxonomy ) {
			$term_id = wpcom_vip_get_term_by( 'slug', $value, $taxonomy )->term_id;
		}

		// Get the language and make sure it is a PLL_Language object
		return isset( $term_id ) && ( $lang = $this->get_object_term( $term_id, 'term_language' ) ) ? $this->model->get_language( $lang->term_id ) : false;
	}

	/**
	 * Tells the parent class to always store a translation term
	 *
	 * @since 1.8
	 *
	 * @param array $translations An associative array of translations with language code as key and translation id as value
	 */
	protected function keep_translation_group( $translations ) {
		return true;
	}

	/**
	 * Deletes a translation
	 *
	 * @since 0.5
	 *
	 * @param int $id term id
	 */
	public function delete_translation( $id ) {
		global $wpdb;
		$slug = array_search( $id, $this->get_translations( $id ) ); // in case some plugin stores the same value with different key

		parent::delete_translation( $id );
		wp_delete_object_term_relationships( $id, 'term_translations' );

		if ( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( * ) FROM $wpdb->terms WHERE term_id = %d;", $id ) ) ) {
			// Always keep a group for terms to allow relationships remap when importing from a WXR file
			$translations[ $slug ] = $id;
			wp_insert_term( $group = uniqid( 'pll_' ), 'term_translations', array( 'description' => serialize( $translations ) ) );
			wp_set_object_terms( $id, $group, 'term_translations' );
		}
	}

	/**
	 * A join clause to add to sql queries when filtering by language is needed directly in query
	 *
	 * @since 1.2
	 *
	 * @return string join clause
	 */
	public function join_clause() {
		global $wpdb;
		return " INNER JOIN $wpdb->term_relationships AS pll_tr ON pll_tr.object_id = t.term_id";
	}

	/**
	 * Cache language and translations when terms are queried by get_terms
	 *
	 * @since 1.2
	 *
	 * @param array $terms      queried terms
	 * @param array $taxonomies queried taxonomies
	 * @return array unmodified $terms
	 */
	public function _prime_terms_cache( $terms, $taxonomies ) {
		if ( is_array( $terms ) && $this->model->is_translated_taxonomy( $taxonomies ) ) {
			foreach ( $terms as $term ) {
				$term_ids[] = is_object( $term ) ? $term->term_id : (int) $term;
			}
		}

		if ( ! empty( $term_ids ) ) {
			update_object_term_cache( array_unique( $term_ids ), 'term' ); // Adds language and translation of terms to cache
		}
		return $terms;
	}

	/**
	 * When terms are found for posts, add their language and translations to cache
	 *
	 * @since 1.2
	 *
	 * @param array $terms      terms found
	 * @param array $object_ids not used
	 * @param array $taxonomies terms taxonomies
	 * @return array unmodified $terms
	 */
	public function wp_get_object_terms( $terms, $object_ids, $taxonomies ) {
		$taxonomies = explode( "', '", trim( $taxonomies, "'" ) );
		if ( ! in_array( 'term_translations', $taxonomies ) ) {
			$this->_prime_terms_cache( $terms, $taxonomies );
		}
		return $terms;
	}

	/**
	 * When the term cache is cleaned, clean the object term cache too
	 *
	 * @since 2.0
	 *
	 * @param array $ids An array of term IDs.
	 */
	function clean_term_cache( $ids ) {
		clean_object_term_cache( $ids, 'term' );
	}
}

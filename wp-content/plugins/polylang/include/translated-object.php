<?php

/**
 * Setups the objects languages and translations model
 *
 * @since 1.8
 */
abstract class PLL_Translated_Object {
	public $model;
	protected $object_type, $tax_language, $tax_translations, $tax_tt;

	/**
	 * Constructor
	 *
	 * @since 1.8
	 *
	 * @param object $model
	 */
	public function __construct( &$model ) {
		$this->model = &$model;

		// register our taxonomies as soon as possible
		// this is early registration, not ready for rewrite rules as wp_rewrite will be setup later
		$args = array( 'label' => false, 'public' => false, 'query_var' => false, 'rewrite' => false, '_pll' => true );
		register_taxonomy( $this->tax_language, $this->object_type, $args );
		$args['update_count_callback'] = '_update_generic_term_count'; // count *all* posts to avoid deleting in clean_translations_terms
		register_taxonomy( $this->tax_translations, $this->object_type, $args );
	}

	/**
	 * Wrap wp_get_object_terms to cache it and return only one object
	 * inspired by the function get_the_terms
	 *
	 * @since 1.2
	 *
	 * @param int    $object_id post_id or term_id
	 * @param string $taxonomy  Polylang taxonomy depending if we are looking for a post ( or term ) language ( or translation )
	 * @return bool|object the term associated to the object in the requested taxonomy if exists, false otherwise
	 */
	public function get_object_term( $object_id, $taxonomy ) {
		if ( empty( $object_id ) || is_wp_error( $object_id ) ) {
			return false;
		}

		$object_id = (int) $object_id;
		$term = get_object_term_cache( $object_id, $taxonomy );

		if ( false === $term ) {
			// query language and translations at the same time
			$taxonomies = array( $this->tax_language, $this->tax_translations );

			// query terms
			foreach ( wp_get_object_terms( $object_id, $taxonomies, array( 'update_term_meta_cache' => false ) ) as $t ) {
				$terms[ $t->taxonomy ] = $t;
				if ( $t->taxonomy == $taxonomy ) {
					$term = $t;
				}
			}

			// store it the way WP wants it
			// set an empty cache if no term found in the taxonomy
			foreach ( $taxonomies as $tax ) {
				wp_cache_add( $object_id, empty( $terms[ $tax ] ) ? array() : array( $terms[ $tax ] ), $tax . '_relationships' );
			}
		}
		else {
			$term = reset( $term );
		}

		return empty( $term ) ? false : $term;
	}

	/**
	 * Tells whether to store a translation term
	 *
	 * @since 1.8
	 *
	 * @param array $translations an associative array of translations with language code as key and translation id as value
	 */
	protected function keep_translation_group( $translations ) {
		return count( $translations ) > 1;
	}

	/**
	 * Saves translations for posts or terms
	 *
	 * @since 0.5
	 *
	 * @param int   $id           post id or term id
	 * @param array $translations an associative array of translations with language code as key and translation id as value
	 */
	public function save_translations( $id, $translations ) {
		$id = (int) $id;

		if ( ( $lang = $this->get_language( $id ) ) && isset( $translations ) && is_array( $translations ) ) {
			// sanitize the translations array
			$translations = array_map( 'intval', $translations );
			$translations = array_merge( array( $lang->slug => $id ), $translations ); // make sure this object is in translations
			$translations = array_diff( $translations, array( 0 ) ); // don't keep non translated languages
			$translations = array_intersect_key( $translations, array_flip( $this->model->get_languages_list( array( 'fields' => 'slug' ) ) ) ); // keep only valid languages slugs as keys

			// unlink removed translations
			$old_translations = $this->get_translations( $id );
			foreach ( array_diff_assoc( $old_translations, $translations ) as $object_id ) {
				$this->delete_translation( $object_id );
			}

			// don't create a translation group for untranslated posts as it is useless
			// but we need one for terms to allow relationships remap when importing from a WXR file
			if ( $this->keep_translation_group( $translations ) ) {
				$terms = wp_get_object_terms( $translations, $this->tax_translations );
				$term = reset( $terms );

				// create a new term if necessary
				if ( empty( $term ) ) {
					wp_insert_term( $group = uniqid( 'pll_' ), $this->tax_translations, array( 'description' => serialize( $translations ) ) );
				}
				else {
					// take care not to overwrite extra data stored in description field, if any
					$d = unserialize( $term->description );
					$d = is_array( $d ) ? array_diff_key( $d, $old_translations ) : array(); // remove old translations
					$d = array_merge( $d, $translations ); // add new one
					wp_update_term( $group = (int) $term->term_id, $this->tax_translations, array( 'description' => serialize( $d ) ) );
				}

				// link all translations to the new term
				foreach ( $translations as $p ) {
					wp_set_object_terms( $p, $group, $this->tax_translations );
				}

				// clean now unused translation groups
				foreach ( wp_list_pluck( $terms, 'term_id' ) as $term_id ) {
					$term = get_term( $term_id, $this->tax_translations );
					if ( empty( $term->count ) ) {
						wp_delete_term( $term_id, $this->tax_translations );
					}
				}
			}
		}
	}

	/**
	 * Deletes a translation of a post or term
	 *
	 * @since 0.5
	 *
	 * @param int $id post id or term id
	 */
	public function delete_translation( $id ) {
		$id = (int) $id;
		$term = $this->get_object_term( $id, $this->tax_translations );

		if ( ! empty( $term ) ) {
			$d = unserialize( $term->description );
			$slug = array_search( $id, $this->get_translations( $id ) ); // in case some plugin stores the same value with different key
			unset( $d[ $slug ] );

			if ( empty( $d ) ) {
				wp_delete_term( (int) $term->term_id, $this->tax_translations );
			}
			else {
				wp_update_term( (int) $term->term_id, $this->tax_translations, array( 'description' => serialize( $d ) ) );
			}
		}
	}

	/**
	 * Returns an array of translations of a post or term
	 *
	 * @since 0.5
	 *
	 * @param int $id post id or term id
	 * @return array an associative array of translations with language code as key and translation id as value
	 */
	public function get_translations( $id ) {
		$term = $this->get_object_term( $id, $this->tax_translations );
		$translations = empty( $term ) ? array() : unserialize( $term->description );

		// make sure we return only translations ( thus we allow plugins to store other information in the array )
		if ( is_array( $translations ) ) {
			$translations = array_intersect_key( $translations, array_flip( $this->model->get_languages_list( array( 'fields' => 'slug' ) ) ) );
		}

		// make sure to return at least the passed post or term in its translation array
		if ( empty( $translations ) && $lang = $this->get_language( $id ) ) {
			$translations = array( $lang->slug => $id );
		}

		return $translations;
	}

	/**
	 * Returns the id of the translation of a post or term
	 *
	 * @since 0.5
	 *
	 * @param int           $id   post id or term id
	 * @param object|string $lang object or slug
	 * @return bool|int post id or term id of the translation, false if there is none
	 */
	public function get_translation( $id, $lang ) {
		if ( ! $lang = $this->model->get_language( $lang ) ) {
			return false;
		}

		$translations = $this->get_translations( $id );

		return isset( $translations[ $lang->slug ] ) ? $translations[ $lang->slug ] : false;
	}

	/**
	 * Among the object and its translations, returns the id of the object which is in $lang
	 *
	 * @since 0.1
	 *
	 * @param int               $id   post id or term id
	 * @param int|string|object $lang language ( term_id or slug or object )
	 * @return bool|int the translation post id  or term id if exists, otherwise the post id or term id, false if the post has no language
	 */
	public function get( $id, $lang ) {
		$obj_lang = $this->get_language( $id ); // FIXME is this necessary?
		if ( ! $lang || ! $obj_lang ) {
			return false;
		}

		$lang = $this->model->get_language( $lang );
		return $obj_lang->term_id == $lang->term_id ? $id : $this->get_translation( $id, $lang );
	}

	/**
	 * A where clause to add to sql queries when filtering by language is needed directly in query
	 *
	 * @since 1.2
	 *
	 * @param object|array|string $lang a PLL_Language object or a comma separated list of language slug or an array of language slugs
	 * @return string where clause
	 */
	public function where_clause( $lang ) {
		global $wpdb;
		$tt_id = $this->tax_tt;

		// $lang is an object
		// generally the case if the query is coming from Polylang
		if ( is_object( $lang ) ) {
			return ' AND pll_tr.term_taxonomy_id = ' . absint( $lang->$tt_id );
		}

		// $lang is a comma separated list of slugs ( or an array of slugs )
		// generally the case is the query is coming from outside with 'lang' parameter
		$slugs = is_array( $lang ) ? $lang : explode( ',', $lang );
		foreach ( $slugs as $slug ) {
			$languages[] = absint( $this->model->get_language( $slug )->$tt_id );
		}

		return ' AND pll_tr.term_taxonomy_id IN ( ' . implode( ',', $languages ) . ' )';
	}

	/**
	 * Returns ids of objects in a language similarly to get_objects_in_term for a taxonomy
	 * faster than get_objects_in_term as it avoids a JOIN
	 *
	 * @since 1.4
	 *
	 * @param object $lang a PLL_Language object
	 * @return array
	 */
	public function get_objects_in_language( $lang ) {
		global $wpdb;
		$tt_id = $this->tax_tt;
		return $wpdb->get_col( $wpdb->prepare( "SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $lang->$tt_id ) );
	}
}

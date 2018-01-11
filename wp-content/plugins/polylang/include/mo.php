<?php

/**
 * Manages strings translations storage
 *
 * @since 1.2
 * @since 2.1 Stores the strings in a post meta instead of post content to avoid unserialize issues (See #63)
 */
class PLL_MO extends MO {

	/**
	 * Registers the polylang_mo custom post type, only at first object creation
	 *
	 * @since 1.2
	 */
	public function __construct() {
		if ( ! post_type_exists( 'polylang_mo' ) ) {
			$labels = array( 'name' => __( 'Strings translations', 'polylang' ) );
			register_post_type( 'polylang_mo', array( 'labels' => $labels, 'rewrite' => false, 'query_var' => false, '_pll' => true ) );

			add_action( 'pll_add_language', array( $this, 'clean_cache' ) );
		}
	}

	/**
	 * Writes a PLL_MO object into a custom post meta
	 *
	 * @since 1.2
	 *
	 * @param object $lang The language in which we want to export strings
	 */
	public function export_to_db( $lang ) {
		$this->add_entry( $this->make_entry( '', '' ) ); // Empty string translation, just in case

		// Would be convenient to store the whole object but it would take a huge space in DB
		// So let's keep only the strings in an array
		$strings = array();
		foreach ( $this->entries as $entry ) {
			$strings[] = array( $entry->singular, $this->translate( $entry->singular ) );
		}

		$strings = wp_slash( $strings ); // Avoid breaking slashed strings in update_post_meta. See https://codex.wordpress.org/Function_Reference/update_post_meta#Character_Escaping

		if ( empty( $lang->mo_id ) ) {
			$post = array(
				'post_title'  => 'polylang_mo_' . $lang->term_id,
				'post_status' => 'private', // To avoid a conflict with WP Super Cache. See https://wordpress.org/support/topic/polylang_mo-and-404s-take-2
				'post_type'   => 'polylang_mo',
			);
			$mo_id = wp_insert_post( $post );
			update_post_meta( $mo_id, '_pll_strings_translations', $strings );
		} else {
			update_post_meta( $lang->mo_id, '_pll_strings_translations', $strings );
		}
	}

	/**
	 * Reads a PLL_MO object from a custom post meta
	 *
	 * @since 1.2
	 *
	 * @param object $lang The language in which we want to get strings
	 */
	public function import_from_db( $lang ) {
		if ( ! empty( $lang->mo_id ) ) {
			$strings = get_post_meta( $lang->mo_id, '_pll_strings_translations', true );
			if ( is_array( $strings ) ) {
				foreach ( $strings as $msg ) {
					$this->add_entry( $this->make_entry( $msg[0], $msg[1] ) );
				}
			}
		}
	}

	/**
	 * Returns the post id of the post storing the strings translations
	 *
	 * @since 1.4
	 *
	 * @param object $lang
	 * @return int
	 */
	public static function get_id( $lang ) {
		global $wpdb;

		$ids = wp_cache_get( 'polylang_mo_ids' );

		if ( empty( $ids ) ) {
			$ids = $wpdb->get_results( "SELECT post_title, ID FROM $wpdb->posts WHERE post_type='polylang_mo'", OBJECT_K );
			wp_cache_add( 'polylang_mo_ids', $ids );
		}

		// The mo id for a language can be transiently empty
		return isset( $ids[ 'polylang_mo_' . $lang->term_id ] ) ? $ids[ 'polylang_mo_' . $lang->term_id ]->ID : null;
	}

	/**
	 * Invalidate the cache when adding a new language
	 *
	 * @since 2.0.5
	 */
	public function clean_cache() {
		wp_cache_delete( 'polylang_mo_ids' );
	}
}

<?php

/**
 * a class to import languages and translations information form a WXR file
 *
 * @since 1.2
 */
class PLL_WP_Import extends WP_Import {
	public $post_translations = array();

	/**
	 * overrides WP_Import::process_terms to remap terms translations
	 *
	 * @since 1.2
	 */
	function process_terms() {
		$term_translations = array();

		// store this for future usage as parent function unsets $this->terms
		foreach ( $this->terms as $term ) {
			if ( 'post_translations' == $term['term_taxonomy'] ) {
				$this->post_translations[] = $term;
			}
			if ( 'term_translations' == $term['term_taxonomy'] ) {
				$term_translations[] = $term;
			}
		}

		parent::process_terms();

		// update the languages list if needed
		// first reset the core terms cache as WordPress Importer calls wp_suspend_cache_invalidation( true );
		wp_cache_set( 'last_changed', microtime(), 'terms' );
		PLL()->model->clean_languages_cache();

		if ( ( $options = get_option( 'polylang' ) ) && empty( $options['default_lang'] ) && ( $languages = PLL()->model->get_languages_list() ) ) {
			// assign the default language if importer created the first language
			$default_lang = reset( $languages );
			$options['default_lang'] = $default_lang->slug;
			update_option( 'polylang', $options );
		}

		$this->remap_terms_relations( $term_translations );
		$this->remap_translations( $term_translations, $this->processed_terms );
	}

	/**
	 * overrides WP_Import::process_post to remap posts translations
	 * also merges strings translations from the WXR file to the existing ones
	 *
	 * @since 1.2
	 */
	function process_posts() {
		$menu_items = $mo_posts = array();

		// store this for future usage as parent function unset $this->posts
		foreach ( $this->posts as $post ) {
			if ( 'nav_menu_item' == $post['post_type'] ) {
				$menu_items[] = $post;
			}

			if ( 0 === strpos( $post['post_title'], 'polylang_mo_' ) ) {
				$mo_posts[] = $post;
			}
		}

		if ( ! empty( $mo_posts ) ) {
			new PLL_MO(); // just to register the polylang_mo post type before processing posts
		}

		parent::process_posts();

		PLL()->model->clean_languages_cache(); // to update the posts count in ( cached ) languages list

		$this->remap_translations( $this->post_translations, $this->processed_posts );
		unset( $this->post_translations );

		// language switcher menu items
		foreach ( $menu_items as $item ) {
			foreach ( $item['postmeta'] as $meta ) {
				if ( '_pll_menu_item' == $meta['key'] ) {
					update_post_meta( $this->processed_menu_items[ $item['post_id'] ], '_pll_menu_item', maybe_unserialize( $meta['value'] ) );
				}
			}
		}

		// merge strings translations
		foreach ( $mo_posts as $post ) {
			$lang_id = (int) substr( $post['post_title'], 12 );

			if ( ! empty( $this->processed_terms[ $lang_id ] ) ) {
				if ( $strings = unserialize( $post['post_content'] ) ) {
					$mo = new PLL_MO();
					$mo->import_from_db( $this->processed_terms[ $lang_id ] );
					foreach ( $strings as $msg ) {
						$mo->add_entry_or_merge( $mo->make_entry( $msg[0], $msg[1] ) );
					}
					$mo->export_to_db( $this->processed_terms[ $lang_id ] );
				}
			}
			// delete the now useless imported post
			wp_delete_post( $this->processed_posts[ $post['post_id'] ], true );
		}
	}

	/**
	 * remaps terms languages
	 *
	 * @since 1.2
	 *
	 * @param array $terms array of terms in 'term_translations' taxonomy
	 */
	function remap_terms_relations( &$terms ) {
		global $wpdb;

		foreach ( $terms as $term ) {
			$translations = unserialize( $term['term_description'] );
			foreach ( $translations as $slug => $old_id ) {
				if ( $old_id && ! empty( $this->processed_terms[ $old_id ] ) && $lang = PLL()->model->get_language( $slug ) ) {
					// language relationship
					$trs[] = $wpdb->prepare( '( %d, %d )', $this->processed_terms[ $old_id ], $lang->tl_term_taxonomy_id );

					// translation relationship
					$trs[] = $wpdb->prepare( '( %d, %d )', $this->processed_terms[ $old_id ], get_term( $this->processed_terms[ $term['term_id'] ], 'term_translations' )->term_taxonomy_id );
				}
			}
		}

		// insert term_relationships
		if ( ! empty( $trs ) ) {
			$trs = array_unique( $trs );

			// make sure we don't attempt to insert already existing term relationships
			$existing_trs = $wpdb->get_results( "
				SELECT tr.object_id, tr.term_taxonomy_id FROM $wpdb->term_relationships AS tr
				INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
				WHERE tt.taxonomy IN ( 'term_language', 'term_translations' )
			" );

			foreach ( $existing_trs as $key => $tr ) {
				$existing_trs[ $key ] = $wpdb->prepare( '( %d, %d )', $tr->object_id, $tr->term_taxonomy_id );
			}

			$trs = array_diff( $trs, $existing_trs );

			if ( ! empty( $trs ) ) {
				$wpdb->query( "INSERT INTO $wpdb->term_relationships ( object_id, term_taxonomy_id ) VALUES " . implode( ',', $trs ) );
			}
		}
	}

	/**
	 * remaps translations for both posts and terms
	 *
	 * @since 1.2
	 *
	 * @param array $terms array of terms in 'post_translations' or 'term_translations' taxonomies
	 * @param array $processed_objects array of posts or terms processed by WordPress Importer
	 */
	function remap_translations( &$terms, &$processed_objects ) {
		global $wpdb;

		foreach ( $terms as $term ) {
			$translations = unserialize( $term['term_description'] );
			$new_translations = array();

			foreach ( $translations as $slug => $old_id ) {
				if ( $old_id && ! empty( $processed_objects[ $old_id ] ) ) {
					$new_translations[ $slug ] = $processed_objects[ $old_id ];
				}
			}

			if ( ! empty( $new_translations ) ) {
				$u['case'][] = $wpdb->prepare( 'WHEN %d THEN %s', $this->processed_terms[ $term['term_id'] ], serialize( $new_translations ) );
				$u['in'][] = (int) $this->processed_terms[ $term['term_id'] ];
			}
		}

		if ( ! empty( $u ) ) {
			$wpdb->query( "UPDATE $wpdb->term_taxonomy
				SET description = ( CASE term_id " . implode( ' ', $u['case'] ) . ' END )
				WHERE term_id IN ( ' . implode( ',', $u['in'] ) . ' )' );
		}
	}
}

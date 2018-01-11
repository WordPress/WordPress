<?php

/**
 * Manages copy and synchronization of terms and post metas
 *
 * @since 1.2
 */
class PLL_Admin_Sync {

	/**
	 * Constructor
	 *
	 * @since 1.2
	 *
	 * @param object $polylang
	 */
	public function __construct( &$polylang ) {
		$this->model = &$polylang->model;
		$this->options = &$polylang->options;

		add_filter( 'wp_insert_post_parent', array( $this, 'wp_insert_post_parent' ), 10, 3 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 5, 2 ); // Before Types which populates custom fields in same hook with priority 10

		add_action( 'pll_save_post', array( $this, 'pll_save_post' ), 10, 3 );
		add_action( 'pll_save_term', array( $this, 'pll_save_term' ), 10, 3 );

		if ( $this->options['media_support'] ) {
			add_action( 'pll_translate_media', array( $this, 'copy_taxonomies' ), 10, 3 );
			add_action( 'pll_translate_media', array( $this, 'copy_post_metas' ), 10, 3 );
			add_action( 'edit_attachment', array( $this, 'edit_attachment' ) );
		}
	}

	/**
	 * Translate post parent if exists when using "Add new" ( translation )
	 *
	 * @since 0.6
	 *
	 * @param int   $post_parent Post parent ID
	 * @param int   $post_id     Post ID, unused
	 * @param array $postarr     Array of parsed post data
	 * @return int
	 */
	public function wp_insert_post_parent( $post_parent, $post_id, $postarr ) {
		// Make sure not to impact media translations created at the same time
		return isset( $_GET['from_post'], $_GET['new_lang'], $_GET['post_type'] ) && $_GET['post_type'] === $postarr['post_type'] && ( $id = wp_get_post_parent_id( (int) $_GET['from_post'] ) ) && ( $parent = $this->model->post->get_translation( $id, $_GET['new_lang'] ) ) ? $parent : $post_parent;
	}

	/**
	 * Copy post metas, menu order, comment and ping status when using "Add new" ( translation )
	 * formerly used dbx_post_advanced deprecated in WP 3.7
	 *
	 * @since 1.2
	 *
	 * @param string $post_type unused
	 * @param object $post      current post object
	 */
	public function add_meta_boxes( $post_type, $post ) {
		if ( 'post-new.php' == $GLOBALS['pagenow'] && isset( $_GET['from_post'], $_GET['new_lang'] ) && $this->model->is_translated_post_type( $post->post_type ) ) {
			// Capability check already done in post-new.php
			$from_post_id = (int) $_GET['from_post'];
			$from_post = get_post( $from_post_id );
			$lang = $this->model->get_language( $_GET['new_lang'] );

			if ( ! $from_post || ! $lang ) {
				return;
			}

			$this->copy_taxonomies( $from_post_id, $post->ID, $lang->slug );
			$this->copy_post_metas( $from_post_id, $post->ID, $lang->slug );

			foreach ( array( 'menu_order', 'comment_status', 'ping_status' ) as $property ) {
				$post->$property = $from_post->$property;
			}

			// Copy the date only if the synchronization is activated
			if ( in_array( 'post_date', $this->options['sync'] ) ) {
				$post->post_date = $from_post->post_date;
				$post->post_date_gmt = $from_post->post_date_gmt;
			}

			if ( is_sticky( $from_post_id ) ) {
				stick_post( $post->ID );
			}
		}
	}

	/**
	 * Get the list of taxonomies to copy or to synchronize
	 *
	 * @since 1.7
	 * @since 2.1 The `$from`, `$to`, `$lang` parameters were added.
	 *
	 * @param bool   $sync true if it is synchronization, false if it is a copy
	 * @param int    $from id of the post from which we copy informations, optional, defaults to null
	 * @param int    $to   id of the post to which we paste informations, optional, defaults to null
	 * @param string $lang language slug, optional, defaults to null
	 * @return array list of taxonomy names
	 */
	public function get_taxonomies_to_copy( $sync, $from = null, $to = null, $lang = null ) {
		$taxonomies = ! $sync || in_array( 'taxonomies', $this->options['sync'] ) ? $this->model->get_translated_taxonomies() : array();
		if ( ! $sync || in_array( 'post_format', $this->options['sync'] ) ) {
			$taxonomies[] = 'post_format';
		}

		/**
		 * Filter the taxonomies to copy or synchronize
		 *
		 * @since 1.7
		 * @since 2.1 The `$from`, `$to`, `$lang` parameters were added.
		 *
		 * @param array  $taxonomies list of taxonomy names
		 * @param bool   $sync       true if it is synchronization, false if it is a copy
		 * @param int    $from       id of the post from which we copy informations
		 * @param int    $to         id of the post to which we paste informations
		 * @param string $lang       language slug
		 */
		return array_unique( apply_filters( 'pll_copy_taxonomies', $taxonomies, $sync, $from, $to, $lang ) );
	}

	/**
	 * Copy or synchronize terms
	 *
	 * @since 1.8
	 *
	 * @param int    $from id of the post from which we copy informations
	 * @param int    $to   id of the post to which we paste informations
	 * @param string $lang language slug
	 * @param bool   $sync true if it is synchronization, false if it is a copy, defaults to false
	 */
	public function copy_taxonomies( $from, $to, $lang, $sync = false ) {
		// Get taxonomies to sync for this post type
		$taxonomies = array_intersect( get_post_taxonomies( $from ), $this->get_taxonomies_to_copy( $sync, $from, $to, $lang ) );

		// Update the term cache to reduce the number of queries in the loop
		update_object_term_cache( $sync ? array( $from, $to ) : $from, get_post_type( $from ) );

		// Copy or synchronize terms
		// FIXME quite a lot of query in foreach
		foreach ( $taxonomies as $tax ) {
			$terms = get_the_terms( $from, $tax );

			// Translated taxonomy
			if ( $this->model->is_translated_taxonomy( $tax ) ) {
				$newterms = array();
				if ( is_array( $terms ) ) {
					foreach ( $terms as $term ) {
						if ( $term_id = $this->model->term->get_translation( $term->term_id, $lang ) ) {
							$newterms[] = (int) $term_id; // Cast is important otherwise we get 'numeric' tags
						}
					}
				}

				// For some reasons, the user may have untranslated terms in the translation. don't forget them.
				if ( $sync ) {
					$tr_terms = get_the_terms( $to, $tax );
					if ( is_array( $tr_terms ) ) {
						foreach ( $tr_terms as $term ) {
							if ( ! $this->model->term->get_translation( $term->term_id, $this->model->post->get_language( $from ) ) ) {
								$newterms[] = (int) $term->term_id;
							}
						}
					}
				}

				if ( ! empty( $newterms ) || $sync ) {
					wp_set_object_terms( $to, $newterms, $tax ); // replace terms in translation
				}
			}

			// Untranslated taxonomy ( post format )
			// Don't use simple get_post_format / set_post_format to generalize the case to other taxonomies
			else {
				wp_set_object_terms( $to, is_array( $terms ) ? array_map( 'intval', wp_list_pluck( $terms, 'term_id' ) ) : null, $tax );
			}
		}
	}

	/**
	 * Copy or synchronize metas (custom fields)
	 *
	 * @since 0.9
	 *
	 * @param int    $from id of the post from which we copy informations
	 * @param int    $to   id of the post to which we paste informations
	 * @param string $lang language slug
	 * @param bool   $sync true if it is synchronization, false if it is a copy, defaults to false
	 */
	public function copy_post_metas( $from, $to, $lang, $sync = false ) {
		// Copy or synchronize post metas and allow plugins to do the same
		$metas = get_post_custom( $from );
		$keys = array();

		// Get public meta keys ( including from translated post in case we just deleted a custom field )
		if ( ! $sync || in_array( 'post_meta', $this->options['sync'] ) ) {
			foreach ( $keys = array_unique( array_merge( array_keys( $metas ), array_keys( get_post_custom( $to ) ) ) ) as $k => $meta_key ) {
				if ( is_protected_meta( $meta_key ) ) {
					unset( $keys[ $k ] );
				}
			}
		}

		// Add page template and featured image
		foreach ( array( '_wp_page_template', '_thumbnail_id' ) as $meta ) {
			if ( ! $sync || in_array( $meta, $this->options['sync'] ) ) {
				$keys[] = $meta;
			}
		}

		/**
		 * Filter the custom fields to copy or synchronize
		 *
		 * @since 0.6
		 * @since 1.9.2 The `$from`, `$to`, `$lang` parameters were added.
		 *
		 * @param array  $keys list of custom fields names
		 * @param bool   $sync true if it is synchronization, false if it is a copy
		 * @param int    $from id of the post from which we copy informations
		 * @param int    $to   id of the post to which we paste informations
		 * @param string $lang language slug
		 */
		$keys = array_unique( apply_filters( 'pll_copy_post_metas', $keys, $sync, $from, $to, $lang ) );

		// And now copy / synchronize
		foreach ( $keys as $key ) {
			delete_post_meta( $to, $key ); // The synchronization process of multiple values custom fields is easier if we delete all metas first
			if ( isset( $metas[ $key ] ) ) {
				foreach ( $metas[ $key ] as $value ) {
					// Important: always maybe_unserialize value coming from get_post_custom. See codex.
					// Thanks to goncalveshugo http://wordpress.org/support/topic/plugin-polylang-pll_copy_post_meta
					$value = maybe_unserialize( $value );
					// Special case for featured images which can be translated
					add_post_meta( $to, $key, ( '_thumbnail_id' == $key && $tr_value = $this->model->post->get_translation( $value, $lang ) ) ? $tr_value : $value );
				}
			}
		}
	}

	/**
	 * Synchronizes terms and metas in translations
	 *
	 * @since 1.2
	 *
	 * @param int    $post_id      post id
	 * @param object $post         post object
	 * @param array  $translations post translations
	 */
	public function pll_save_post( $post_id, $post, $translations ) {
		global $wpdb;

		// Prepare properties to synchronize
		foreach ( array( 'comment_status', 'ping_status', 'menu_order' ) as $property ) {
			if ( in_array( $property, $this->options['sync'] ) ) {
				$postarr[ $property ] = $post->$property;
			}
		}

		if ( in_array( 'post_date', $this->options['sync'] ) ) {
			// For new drafts, save the date now otherwise it is overriden by WP. Thanks to JoryHogeveen. See #32.
			if ( 'post-new.php' === $GLOBALS['pagenow'] && isset( $_GET['from_post'], $_GET['new_lang'] ) ) {
				$original = get_post( (int) $_GET['from_post'] );
				$wpdb->update(
					$wpdb->posts, array(
						'post_date' => $original->post_date,
						'post_date_gmt' => $original->post_date_gmt,
					),
					array( 'ID' => $post_id )
				);
			} else {
				$postarr['post_date'] = $post->post_date;
				$postarr['post_date_gmt'] = $post->post_date_gmt;
			}
		}

		// Synchronize terms and metas in translations
		foreach ( $translations as $lang => $tr_id ) {
			if ( ! $tr_id || $tr_id === $post_id ) {
				continue;
			}

			// Synchronize terms and metas
			$this->copy_taxonomies( $post_id, $tr_id, $lang, true );
			$this->copy_post_metas( $post_id, $tr_id, $lang, true );

			// Sticky posts
			if ( in_array( 'sticky_posts', $this->options['sync'] ) ) {
				isset( $_REQUEST['sticky'] ) && 'sticky' === $_REQUEST['sticky'] ? stick_post( $tr_id ) : unstick_post( $tr_id );
			}

			// Add comment status, ping status, menu order... to synchronization
			$tr_arr = empty( $postarr ) ? array() : $postarr;

			if ( isset( $GLOBALS['post_type'] ) ) {
				$post_type = $GLOBALS['post_type'];
			} elseif ( isset( $_REQUEST['post_type'] ) ) {
				$post_type = $_REQUEST['post_type']; // 2nd case for quick edit
			}

			// Add post parent to synchronization
			// Make sure not to impact media translations when creating them at the same time as post
			// Do not udpate the translation parent if the user set a parent with no translation
			if ( in_array( 'post_parent', $this->options['sync'] ) && isset( $post_type ) && $post_type === $post->post_type ) {
				$post_parent = ( $parent_id = wp_get_post_parent_id( $post_id ) ) ? $this->model->post->get_translation( $parent_id, $lang ) : 0;
				if ( ! ( $parent_id && ! $post_parent ) ) {
					$tr_arr['post_parent'] = $post_parent;
				}
			}

			// Update all the row at once
			// Don't use wp_update_post to avoid infinite loop
			if ( ! empty( $tr_arr ) ) {
				$wpdb->update( $wpdb->posts, $tr_arr, array( 'ID' => $tr_id ) );
				clean_post_cache( $tr_id );
			}
		}
	}

	/**
	 * Synchronize translations of a term in all posts
	 *
	 * @since 1.2
	 *
	 * @param int    $term_id      term id
	 * @param string $taxonomy     taxonomy name of the term
	 * @param array  $translations translations of the term
	 */
	public function pll_save_term( $term_id, $taxonomy, $translations ) {
		// Sync term metas
		foreach ( $translations as $lang => $tr_id ) {
			if ( $tr_id && $tr_id !== $term_id ) {
				$this->copy_term_metas( $term_id, $tr_id, $lang, true );
			}
		}

		// Check if the taxonomy is synchronized
		if ( ! in_array( $taxonomy, $this->get_taxonomies_to_copy( true ) ) ) {
			return;
		}

		// Get all posts associated to this term
		$posts = get_posts( array(
			'numberposts' => -1,
			'nopaging'    => true,
			'post_type'   => 'any',
			'post_status' => 'any',
			'fields'      => 'ids',
			'tax_query'   => array(
				array(
					'taxonomy'         => $taxonomy,
					'field'            => 'id',
					'terms'            => array_merge( array( $term_id ), array_values( $translations ) ),
					'include_children' => false,
				),
			),
		) );

		// Associate translated term to translated post
		// FIXME quite a lot of query in foreach
		foreach ( $this->model->get_languages_list() as $language ) {
			if ( $translated_term = $this->model->term->get( $term_id, $language ) ) {
				foreach ( $posts as $post_id ) {
					if ( $translated_post = $this->model->post->get( $post_id, $language ) ) {
						wp_set_object_terms( $translated_post, $translated_term, $taxonomy, true );
					}
				}
			}
		}

		// Synchronize parent in translations
		// Calling clean_term_cache *after* this is mandatory otherwise the $taxonomy_children option is not correctly updated
		// Before WP 3.9 clean_term_cache could be called ( efficiently ) only one time due to static array which prevented to update the option more than once
		// This is the reason to use the edit_term filter and not edited_term
		// Take care that $_POST contains the only valid values for the current term
		// FIXME can I synchronize parent without using $_POST instead?
		if ( isset( $_POST['term_tr_lang'] ) ) {
			foreach ( $_POST['term_tr_lang'] as $lang => $tr_id ) {
				if ( $tr_id ) {
					if ( isset( $_POST['parent'] ) && -1 != $_POST['parent'] ) { // Since WP 3.1
						$term_parent = $this->model->term->get_translation( (int) $_POST['parent'], $lang );
					}

					global $wpdb;
					$wpdb->update( $wpdb->term_taxonomy,
						array( 'parent' => isset( $term_parent ) ? $term_parent : 0 ),
						array( 'term_taxonomy_id' => get_term( (int) $tr_id, $taxonomy )->term_taxonomy_id )
					);

					clean_term_cache( $tr_id, $taxonomy ); // OK since WP 3.9
				}
			}
		}
	}

	/**
	 * Synchronizes terms and metas in translations for media
	 *
	 * @since 1.8
	 *
	 * @param int $post_id post id
	 */
	public function edit_attachment( $post_id ) {
		$this->pll_save_post( $post_id, get_post( $post_id ), $this->model->post->get_translations( $post_id ) );
	}

	/**
	 * Copy or synchronize term metas (custom fields)
	 *
	 * @since 2.2
	 *
	 * @param int    $from id of the term from which we copy informations
	 * @param int    $to   id of the term to which we paste informations
	 * @param string $lang language slug
	 * @param bool   $sync true if it is synchronization, false if it is a copy, defaults to false
	 */
	public function copy_term_metas( $from, $to, $lang, $sync = false ) {
		$metas = get_term_meta( $from );

		/**
		 * Filter the term metas to copy or synchronize
		 *
		 * @since 2.2
		 *
		 * @param array  $keys list of term meta names
		 * @param bool   $sync true if it is synchronization, false if it is a copy
		 * @param int    $from id of the term from which we copy informations
		 * @param int    $to   id of the term to which we paste informations
		 * @param string $lang language slug
		 */
		$keys = array_unique( apply_filters( 'pll_copy_term_metas', array(), $sync, $from, $to, $lang ) );

		// And now copy / synchronize
		foreach ( $keys as $key ) {
			delete_term_meta( $to, $key ); // The synchronization process of multiple values term metas is easier if we delete all metas first
			if ( isset( $metas[ $key ] ) ) {
				foreach ( $metas[ $key ] as $value ) {
					$value = maybe_unserialize( $value );
					add_term_meta( $to, $key, $value );
				}
			}
		}
	}
}

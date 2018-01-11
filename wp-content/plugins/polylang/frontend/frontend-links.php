<?php

/**
 * Manages links filters and url of translations on frontend
 *
 * @since 1.2
 */
class PLL_Frontend_Links extends PLL_Links {
	public $curlang;
	public $cache; // Our internal non persistent cache object

	/**
	 * Constructor
	 *
	 * @since 1.2
	 *
	 * @param object $polylang
	 */
	public function __construct( &$polylang ) {
		parent::__construct( $polylang );

		$this->curlang = &$polylang->curlang;
		$this->cache = new PLL_Cache();

	}

	/**
	 * Returns the url of the translation ( if exists ) of the current page
	 *
	 * @since 0.1
	 *
	 * @param object $language
	 * @return string
	 */
	public function get_translation_url( $language ) {
		global $wp_query;

		if ( false !== $translation_url = $this->cache->get( 'translation_url:' . $language->slug ) ) {
			return $translation_url;
		}

		// Make sure that we have the queried object
		// See https://wordpress.org/support/topic/patch-for-fixing-a-notice
		$queried_object_id = $wp_query->get_queried_object_id();

		/**
		 * Filter the translation url before Polylang attempts to find one
		 * Internally used by Polylang for the static front page and posts page
		 *
		 * @since 1.8
		 *
		 * @param string $url               Empty or the url of the translation of teh current page
		 * @param object $language          Language of the translation
		 * @param int    $queried_object_id Queried object id
		 */
		if ( ! $url = apply_filters( 'pll_pre_translation_url', '', $language, $queried_object_id ) ) {
			$qv = $wp_query->query_vars;
			$hide = $this->options['default_lang'] == $language->slug && $this->options['hide_default'];

			// Post and attachment
			if ( is_single() && ( $this->options['media_support'] || ! is_attachment() ) && ( $id = $this->model->post->get( $queried_object_id, $language ) ) && $this->current_user_can_read( $id ) ) {
				$url = get_permalink( $id );
			}

			// Page
			elseif ( is_page() && ( $id = $this->model->post->get( $queried_object_id, $language ) ) && $this->current_user_can_read( $id ) ) {
				$url = get_page_link( $id );
			}

			elseif ( is_search() ) {
				$url = $this->get_archive_url( $language );

				// Special case for search filtered by translated taxonomies: taxonomy terms are translated in the translation url
				if ( ! empty( $wp_query->tax_query->queries ) ) {
					foreach ( $wp_query->tax_query->queries as $tax_query ) {
						if ( ! empty( $tax_query['taxonomy'] ) && $this->model->is_translated_taxonomy( $tax_query['taxonomy'] ) ) {

							$tax = get_taxonomy( $tax_query['taxonomy'] );
							$terms = get_terms( $tax->name, array( 'fields' => 'id=>slug' ) ); // Filtered by current language

							foreach ( $tax_query['terms'] as $slug ) {
								$term_id = array_search( $slug, $terms ); // What is the term_id corresponding to taxonomy term?
								if ( $term_id && $term_id = $this->model->term->get_translation( $term_id, $language ) ) { // Get the translated term_id
									$term = get_term( $term_id, $tax->name );
									$url = str_replace( $slug, $term->slug, $url );
								}
							}
						}
					}
				}
			}

			// Translated taxonomy
			// Take care that is_tax() is false for categories and tags
			elseif ( ( is_category() || is_tag() || is_tax() ) && ( $term = get_queried_object() ) && $this->model->is_translated_taxonomy( $term->taxonomy ) ) {
				$lang = $this->model->term->get_language( $term->term_id );

				if ( ! $lang || $language->slug == $lang->slug ) {
					$url = wpcom_vip_get_term_link( $term, $term->taxonomy ); // Self link
				}

				elseif ( $tr_id = $this->model->term->get_translation( $term->term_id, $language ) ) {
					if ( $tr_term = get_term( $tr_id, $term->taxonomy ) ) {
						// Check if translated term ( or children ) have posts
						$count = $tr_term->count || ( is_taxonomy_hierarchical( $term->taxonomy ) && array_sum( wp_list_pluck( get_terms( $term->taxonomy, array( 'child_of' => $tr_term->term_id, 'lang' => $language->slug ) ), 'count' ) ) );

						/**
						 * Filter whether to hide an archive translation url
						 *
						 * @since 2.2.4
						 *
						 * @param bool   $hide True to hide the translation url.
						 *                     defaults to true when the translated archive is empty, false otherwise.
						 * @param string $lang The language code of the translation
						 * @param array  $args Arguments used to evaluated the number of posts in the archive
						 */
						if ( ! apply_filters( 'pll_hide_archive_translation_url', ! $count, $language->slug, array( 'taxonomy' => $term->taxonomy ) ) ) {
							$url = wpcom_vip_get_term_link( $tr_term, $term->taxonomy );
						}
					}
				}
			}

			// Post type archive
			elseif ( is_post_type_archive() ) {
				if ( $this->model->is_translated_post_type( $qv['post_type'] ) ) {
					$args = array( 'post_type' => $qv['post_type'] );
					$count = $this->model->count_posts( $language, $args );

					/** This filter is documented in frontend/frontend-links.php */
					if ( ! apply_filters( 'pll_hide_archive_translation_url', ! $count, $language->slug, $args ) ) {
						$url = $this->get_archive_url( $language );
					}
				}
			}

			// Other archives
			elseif ( is_archive() ) {
				$keys = array( 'post_type', 'm', 'year', 'monthnum', 'day', 'author', 'author_name' );
				$keys = array_merge( $keys, $this->model->get_filtered_taxonomies_query_vars() );
				$args = array_intersect_key( $qv, array_flip( $keys ) );
				$count = $this->model->count_posts( $language, $args );

				/** This filter is documented in frontend/frontend-links.php */
				if ( ! apply_filters( 'pll_hide_archive_translation_url', ! $count, $language->slug, $args ) ) {
					$url = $this->get_archive_url( $language );
				}
			}

			// Front page when it is the list of posts
			elseif ( is_front_page() ) {
				$url = $this->get_home_url( $language );
			}
		}

		/**
		 * Filter the translation url of the current page before Polylang caches it
		 *
		 * @since 1.1.2
		 *
		 * @param null|string $url      The translation url, null if none was found
		 * @param string      $language The language code of the translation
		 */
		$translation_url = apply_filters( 'pll_translation_url', ( isset( $url ) && ! is_wp_error( $url ) ? $url : null ), $language->slug );
		$this->cache->set( 'translation_url:' . $language->slug, $translation_url );
		return $translation_url;
	}

	/**
	 * Get the translation of the current archive url
	 * used also for search
	 *
	 * @since 1.2
	 *
	 * @param object $language
	 * @return string
	 */
	public function get_archive_url( $language ) {
		$url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$url = $this->links_model->switch_language_in_link( $url, $language );
		$url = $this->links_model->remove_paged_from_link( $url );

		/**
		 * Filter the archive url
		 *
		 * @since 1.6
		 *
		 * @param string $url      Url of the archive
		 * @param object $language Language of the archive
		 */
		return apply_filters( 'pll_get_archive_url', $url, $language );
	}

	/**
	 * Returns the home url in the right language
	 *
	 * @since 0.1
	 *
	 * @param object $language  Optional, defaults to current language
	 * @param bool   $is_search Optional, whether we need the home url for a search form, defaults to false
	 */
	public function get_home_url( $language = '', $is_search = false ) {
		if ( empty( $language ) ) {
			$language = $this->curlang;
		}

		return parent::get_home_url( $language, $is_search );
	}
}

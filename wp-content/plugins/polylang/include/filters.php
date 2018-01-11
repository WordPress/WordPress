<?php

/**
 * Setup filters common to admin and frontend
 *
 * @since 1.4
 */
class PLL_Filters {
	public $links_model, $model, $options, $curlang;

	/**
	 * Constructor: setups filters
	 *
	 * @since 1.4
	 *
	 * @param object $polylang
	 */
	public function __construct( &$polylang ) {
		$this->links_model = &$polylang->links_model;
		$this->model = &$polylang->model;
		$this->options = &$polylang->options;
		$this->curlang = &$polylang->curlang;

		// Filters the comments according to the current language
		add_action( 'parse_comment_query', array( $this, 'parse_comment_query' ) );
		add_filter( 'comments_clauses', array( $this, 'comments_clauses' ), 10, 2 );

		// Filters the get_pages function according to the current language
		add_filter( 'get_pages', array( $this, 'get_pages' ), 10, 2 );

		// Converts the locale to a valid W3C locale
		add_filter( 'language_attributes', array( $this, 'language_attributes' ) );

		// Prevents deleting all the translations of the default category
		add_filter( 'map_meta_cap', array( $this, 'fix_delete_default_category' ), 10, 4 );

		// Translate the site title in emails sent to users
		add_filter( 'password_change_email', array( $this, 'translate_user_email' ) );
		add_filter( 'email_change_email', array( $this, 'translate_user_email' ) );
	}

	/**
	 * Get the language to filter a comments query
	 *
	 * @since 2.0
	 *
	 * @param object $query
	 * @return object|bool the language(s) to use in the filter, false otherwise
	 */
	protected function get_comments_queried_language( $query ) {
		// Don't filter comments if comment ids or post ids are specified
		$plucked = wp_array_slice_assoc( $query->query_vars, array( 'comment__in', 'parent', 'post_id', 'post__in', 'post_parent' ) );
		$fields = array_filter( $plucked );
		if ( ! empty( $fields ) ) {
			return false;
		}

		// Don't filter comments if a non translated post type is specified
		if ( ! empty( $query->query_vars['post_type'] ) && ! $this->model->is_translated_post_type( $query->query_vars['post_type'] ) ) {
			return false;
		}

		return empty( $query->query_vars['lang'] ) ? $this->curlang : $this->model->get_language( $query->query_vars['lang'] );
	}

	/**
	 * Adds language dependent cache domain when querying comments
	 * Useful as the 'lang' parameter is not included in cache key by WordPress
	 * Needed since WP 4.6 as comments have been added to persistent cache. See #36906, #37419
	 *
	 * @since 2.0
	 *
	 * @param object $query
	 */
	public function parse_comment_query( $query ) {
		if ( $lang = $this->get_comments_queried_language( $query ) ) {
			$key = '_' . ( is_array( $lang ) ? implode( ',', $lang ) : $this->model->get_language( $lang )->slug );
			$query->query_vars['cache_domain'] = empty( $query->query_vars['cache_domain'] ) ? 'pll' . $key : $query->query_vars['cache_domain'] . $key;
		}
	}

	/**
	 * Filters the comments according to the current language
	 * Used by the recent comments widget and admin language filter
	 *
	 * @since 0.2
	 *
	 * @param array  $clauses sql clauses
	 * @param object $query   WP_Comment_Query object
	 * @return array modified $clauses
	 */
	public function comments_clauses( $clauses, $query ) {
		global $wpdb;

		$lang = $this->get_comments_queried_language( $query );

		if ( ! empty( $lang ) ) {
			// If this clause is not already added by WP
			if ( ! strpos( $clauses['join'], '.ID' ) ) {
				$clauses['join'] .= " JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID";
			}

			$clauses['join'] .= $this->model->post->join_clause();
			$clauses['where'] .= $this->model->post->where_clause( $lang );
		}
		return $clauses;
	}

	/**
	 * Filters get_pages per language
	 *
	 * @since 1.4
	 *
	 * @param array $pages an array of pages already queried
	 * @param array $args  get_pages arguments
	 * @return array modified list of pages
	 */
	public function get_pages( $pages, $args ) {
		if ( isset( $args['lang'] ) && empty( $args['lang'] ) ) {
			return $pages;
		}

		$language = empty( $args['lang'] ) ? $this->curlang : $this->model->get_language( $args['lang'] );

		if ( empty( $language ) || empty( $pages ) || ! $this->model->is_translated_post_type( $args['post_type'] ) ) {
			return $pages;
		}

		static $once = false;

		// Obliged to redo the get_pages query if we want to get the right number
		if ( ! empty( $args['number'] ) && ! $once ) {
			$once = true; // avoid infinite loop

			$r = array(
				'lang' => 0, // So this query is not filtered
				'numberposts' => -1,
				'nopaging'    => true,
				'post_type'   => $args['post_type'],
				'fields'      => 'ids',
				'tax_query'   => array(
					array(
						'taxonomy' => 'language',
						'field'    => 'term_taxonomy_id', // Since WP 3.5
						'terms'    => $language->term_taxonomy_id,
						'operator' => 'NOT IN',
					),
				),
			);

			$args['exclude'] = array_merge( $args['exclude'], get_posts( $r ) );
			$pages = get_pages( $args );
		}

		$ids = wp_list_pluck( $pages, 'ID' );

		// Filters the queried list of pages by language
		if ( ! $once ) {
			$ids = array_intersect( $ids, $this->model->post->get_objects_in_language( $language ) );

			foreach ( $pages as $key => $page ) {
				if ( ! in_array( $page->ID, $ids ) ) {
					unset( $pages[ $key ] );
				}
			}

			$pages = array_values( $pages ); // In case 3rd parties suppose the existence of $pages[0]
		}

		// Not done by WP but extremely useful for performance when manipulating taxonomies
		update_object_term_cache( $ids, $args['post_type'] );

		$once = false; // In case get_pages is called another time
		return $pages;
	}

	/**
	 * Converts WordPress locale to valid W3 locale in html language attributes
	 *
	 * @since 1.8
	 *
	 * @param string $output language attributes
	 * @return string
	 */
	public function language_attributes( $output ) {
		if ( $language = $this->model->get_language( get_locale() ) ) {
			$output = str_replace( '"' . get_bloginfo( 'language' ) . '"', '"' . $language->get_locale( 'display' ) . '"', $output );
		}
		return $output;
	}


	/**
	 * Prevents deleting all the translations of the default category
	 *
	 * @since 2.1
	 *
	 * @param array  $caps    The user's actual capabilities.
	 * @param string $cap     Capability name.
	 * @param int    $user_id The user ID.
	 * @param array  $args    Adds the context to the cap. The category id.
	 * @return array
	 */
	public function fix_delete_default_category( $caps, $cap, $user_id, $args ) {
		if ( 'delete_term' === $cap ) {
			$term = get_term( reset( $args ) ); // Since WP 4.4, we can get the term to get the taxonomy
			if ( $term instanceof WP_Term ) {
				$default_cat = get_option( 'default_' . $term->taxonomy );
				if ( $default_cat && array_intersect( $args, $this->model->term->get_translations( $default_cat ) ) ) {
					$caps[] = 'do_not_allow';
				}
			}
		}

		return $caps;
	}

	/**
	 * Translates the site title in emails sent to the user (change email, reset password)
	 * It is necessary to filter the email because WP evaluates the site title before calling switch_to_locale()
	 *
	 * @since 2.1.3
	 *
	 * @param array $email
	 * @return array
	 */
	function translate_user_email( $email ) {
		$blog_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$email['subject'] = sprintf( $email['subject'], $blog_name );
		$email['message'] = str_replace( '###SITENAME###', $blog_name, $email['message'] );
		return $email;
	}
}

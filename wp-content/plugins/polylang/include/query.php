<?php

/**
 * A class to manipulate the language query var in WP_Query
 *
 * @since 2.2
 */
class PLL_Query {

	static protected $excludes = array(
		'p',
		'post_parent',
		'attachment',
		'attachment_id',
		'name',
		'pagename',
		'page_id',
		'category_name',
		'tag',
		'tag_id',
		'category__in',
		'category__and',
		'post__in',
		'post_name__in',
		'tag__in',
		'tag__and',
		'tag_slug__in',
		'tag_slug__and',
		'post_parent__in',
	);

	/**
	 * Constructor
	 *
	 * @since 2.2
	 *
	 * @param array  $query Reference to the WP_Query object
	 * @param object $model
	 */
	public function __construct( &$query, &$model ) {
		$this->query = &$query;
		$this->model = &$model;
	}

	/**
	 * Check if translated taxonomy is queried
	 * Compatible with nested queries introduced in WP 4.1
	 * @see https://wordpress.org/support/topic/tax_query-bug
	 *
	 * @since 1.7
	 *
	 * @param array $tax_queries
	 * @return bool
	 */
	protected function have_translated_taxonomy( $tax_queries ) {
		if ( is_array( $tax_queries ) ) {
			foreach ( $tax_queries as $tax_query ) {
				if ( isset( $tax_query['taxonomy'] ) && $this->model->is_translated_taxonomy( $tax_query['taxonomy'] ) && ! ( isset( $tax_query['operator'] ) && 'NOT IN' === $tax_query['operator'] ) ) {
					return true;
				}

				// Nested queries
				elseif ( is_array( $tax_query ) && $this->have_translated_taxonomy( $tax_query ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get queried taxonomies
	 *
	 * @since 2.2
	 *
	 * @return array queried taxonomies
	 */
	public function get_queried_taxonomies() {
		return isset( $this->query->tax_query->queried_terms ) ? array_keys( wp_list_filter( $this->query->tax_query->queried_terms, array( 'operator' => 'NOT IN' ), 'NOT' ) ) : array();
	}

	/**
	 * Sets the language in query
	 * Optimized for ( needs ) WP 3.5+
	 *
	 * @since 2.2
	 *
	 * @param object $lang
	 */
	public function set_language( $lang ) {
		// Defining directly the tax_query ( rather than setting 'lang' avoids transforming the query by WP )
		$this->query->query_vars['tax_query'][] = array(
			'taxonomy' => 'language',
			'field'    => 'term_taxonomy_id', // Since WP 3.5
			'terms'    => $lang->term_taxonomy_id,
			'operator' => 'IN',
		);
	}

	/**
	 * Add the language in query after it has checked that it won't conflict with other query vars
	 *
	 * @since 2.2
	 *
	 * @param object $lang Language
	 */
	public function filter_query( $lang ) {
		$qvars = &$this->query->query_vars;

		if ( ! isset( $qvars['lang'] ) ) {
			// Do not filter the query if the language is already specified in another way
			foreach ( self::$excludes as $k ) {
				if ( ! empty( $qvars[ $k ] ) ) {
					return;
				}
			}

			// Specific case for 'cat' as it can contain negative values
			if ( ! empty( $qvars['cat'] ) ) {
				foreach ( explode( ',', $qvars['cat'] ) as $cat ) {
					if ( $cat > 0 ) {
						return;
					}
				}
			}

			$taxonomies = array_intersect( $this->model->get_translated_taxonomies(), get_taxonomies( array( '_builtin' => false ) ) );

			foreach ( $taxonomies as $tax ) {
				$tax = get_taxonomy( $tax );
				if ( ! empty( $qvars[ $tax->query_var ] ) ) {
					return;
				}
			}

			if ( ! empty( $qvars['tax_query'] ) && $this->have_translated_taxonomy( $qvars['tax_query'] ) ) {
				return;
			}

			// Filter queries according to the requested language
			if ( ! empty( $lang ) ) {
				$taxonomies = $this->get_queried_taxonomies();

				if ( $taxonomies && ( empty( $qvars['post_type'] ) || 'any' === $qvars['post_type'] ) ) {
					foreach ( $taxonomies as $taxonomy ) {
						$tax_object = get_taxonomy( $taxonomy );
						if ( $this->model->is_translated_post_type( $tax_object->object_type ) ) {
							$this->set_language( $lang );
							break;
						}
					}
				} elseif ( empty( $qvars['post_type'] ) || $this->model->is_translated_post_type( $qvars['post_type'] ) ) {
					$this->set_language( $lang );
				}
			}
		} else {
			// Do not filter untranslatable post types such as nav_menu_item
			if ( isset( $qvars['post_type'] ) && ! $this->model->is_translated_post_type( $qvars['post_type'] ) && ( empty( $qvars['tax_query'] ) || ! $this->have_translated_taxonomy( $qvars['tax_query'] ) ) ) {
				unset( $qvars['lang'] );
			}

			// Unset 'all' query var (mainly for admin language filter).
			if ( isset( $qvars['lang'] ) && 'all' === $qvars['lang'] ) {
				unset( $qvars['lang'] );
			}
		}
	}
}

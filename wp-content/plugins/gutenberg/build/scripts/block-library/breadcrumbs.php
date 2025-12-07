<?php
/**
 * Server-side rendering of the `core/breadcrumbs` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/breadcrumbs` block on the server.
 *
 * @since 6.9.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the post breadcrumb for hierarchical post types.
 */
function gutenberg_render_block_core_breadcrumbs( $attributes, $content, $block ) {
	$is_home_or_front_page = is_home() || is_front_page();
	if ( ! $attributes['showOnHomePage'] && $is_home_or_front_page ) {
		return '';
	}

	$breadcrumb_items = array();

	if ( $attributes['showHomeLink'] ) {
		if ( ! $is_home_or_front_page ) {
			$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_link(
				home_url( '/' ),
				__( 'Home' )
			);
		} else {
			$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_item( __( 'Home' ), gutenberg_block_core_breadcrumbs_is_paged() );
		}
	}

	// Handle home and front page.
	if ( $is_home_or_front_page ) {
		// This check is explicitly nested in order not to execute the `else` branch.
		if ( gutenberg_block_core_breadcrumbs_is_paged() ) {
			$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_page_number_item();
		}
	} elseif ( is_search() ) {
		// Handle search results.
		$is_paged = gutenberg_block_core_breadcrumbs_is_paged();
		/* translators: %s: search query */
		$text               = sprintf( __( 'Search results for: "%s"' ), wp_trim_words( get_search_query(), 10 ) );
		$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_item( $text, $is_paged );
		// Add the "Page X" as the current page if paginated.
		if ( $is_paged ) {
			$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_page_number_item();
		}
	} elseif ( is_404() ) {
		// Handle 404 pages.
		$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_current_item(
			__( 'Page not found' )
		);
	} elseif ( is_archive() ) {
		// Handle archive pages (taxonomy, post type, date, author archives).
		$archive_breadcrumbs = gutenberg_block_core_breadcrumbs_get_archive_breadcrumbs();
		if ( ! empty( $archive_breadcrumbs ) ) {
			$breadcrumb_items = array_merge( $breadcrumb_items, $archive_breadcrumbs );
		}
	} else {
		// Handle single post/page breadcrumbs.
		if ( ! isset( $block->context['postId'] ) || ! isset( $block->context['postType'] ) ) {
			return '';
		}

		$post_id   = $block->context['postId'];
		$post_type = $block->context['postType'];

		$post = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}

		// Determine breadcrumb type for accurate rendering (matching JavaScript logic).
		$show_terms = false;
		if ( ! is_post_type_hierarchical( $post_type ) ) {
			$show_terms = true;
		} elseif ( empty( get_object_taxonomies( $post_type, 'objects' ) ) ) {
			// Hierarchical post type without taxonomies can only use ancestors.
			$show_terms = false;
		} else {
			// For hierarchical post types with taxonomies, use the attribute.
			$show_terms = $attributes['prefersTaxonomy'];
		}

		if ( ! $show_terms ) {
			$breadcrumb_items = array_merge( $breadcrumb_items, gutenberg_block_core_breadcrumbs_get_hierarchical_post_type_breadcrumbs( $post_id ) );
		} else {
			$breadcrumb_items = array_merge( $breadcrumb_items, gutenberg_block_core_breadcrumbs_get_terms_breadcrumbs( $post_id, $post_type ) );
		}
		// Add current post title (not linked).
		$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_current_item( gutenberg_block_core_breadcrumbs_get_post_title( $post ), true );
	}

	// Remove last item if disabled.
	if ( ! $attributes['showLastItem'] && ! empty( $breadcrumb_items ) ) {
		array_pop( $breadcrumb_items );
	}

	if ( empty( $breadcrumb_items ) ) {
		return '';
	}

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'style'      => '--separator: "' . addcslashes( $attributes['separator'], '\\"' ) . '";',
			'aria-label' => __( 'Breadcrumbs' ),
		)
	);

	$breadcrumb_html = sprintf(
		'<nav %s><ol>%s</ol></nav>',
		$wrapper_attributes,
		implode(
			'',
			array_map(
				static function ( $item ) {
					return '<li>' . $item . '</li>';
				},
				$breadcrumb_items
			)
		)
	);

	return $breadcrumb_html;
}

/**
 * Checks if we're on a paginated view (page 2 or higher).
 *
 * @since 6.9.0
 *
 * @return bool True if paged > 1, false otherwise.
 */
function gutenberg_block_core_breadcrumbs_is_paged() {
	$paged = (int) get_query_var( 'paged' );
	return $paged > 1;
}

/**
 * Creates a "Page X" breadcrumb item for paginated views.
 *
 * @since 6.9.0
 *
 * @return string The "Page X" breadcrumb HTML.
 */
function gutenberg_block_core_breadcrumbs_create_page_number_item() {
	$paged = (int) get_query_var( 'paged' );
	return gutenberg_block_core_breadcrumbs_create_current_item(
		/* translators: %s: page number */
		sprintf( __( 'Page %s' ), number_format_i18n( $paged ) )
	);
}

/**
 * Creates a breadcrumb link item.
 *
 * @since 6.9.0
 *
 * @param string $url        The URL for the link (will be escaped).
 * @param string $text       The link text (will be escaped).
 * @param bool   $allow_html Whether to allow HTML in the text. If true, uses wp_kses_post(), otherwise uses esc_html(). Default false.
 *
 * @return string The breadcrumb link HTML.
 */
function gutenberg_block_core_breadcrumbs_create_link( $url, $text, $allow_html = false ) {
	return sprintf(
		'<a href="%s">%s</a>',
		esc_url( $url ),
		$allow_html ? wp_kses_post( $text ) : esc_html( $text )
	);
}

/**
 * Creates a breadcrumb current page item.
 *
 * @since 6.9.0
 *
 * @param string $text       The text content (will be escaped).
 * @param bool   $allow_html Whether to allow HTML in the text. If true, uses wp_kses_post(), otherwise uses esc_html(). Default false.
 *
 * @return string The breadcrumb current page HTML.
 */
function gutenberg_block_core_breadcrumbs_create_current_item( $text, $allow_html = false ) {
	return sprintf(
		'<span aria-current="page">%s</span>',
		$allow_html ? wp_kses_post( $text ) : esc_html( $text )
	);
}

/**
 * Creates a breadcrumb item that's either a link or current page item.
 *
 * When paginated (is_paged is true), creates a link to page 1.
 * Otherwise, creates a span marked as the current page.
 *
 * @since 6.9.0
 *
 * @param string $text       The text content (will be escaped).
 * @param bool   $is_paged   Whether we're on a paginated view.
 * @param bool   $allow_html Whether to allow HTML in the text. If true, uses wp_kses_post(), otherwise uses esc_html(). Default false.
 *
 * @return string The breadcrumb HTML.
 */
function gutenberg_block_core_breadcrumbs_create_item( $text, $is_paged = false, $allow_html = false ) {
	if ( $is_paged ) {
		return gutenberg_block_core_breadcrumbs_create_link( get_pagenum_link( 1 ), $text, $allow_html );
	}
	return gutenberg_block_core_breadcrumbs_create_current_item( $text, $allow_html );
}

/**
 * Gets a post title with fallback for empty titles.
 *
 * @since 6.9.0
 *
 * @param int|WP_Post $post_id_or_object The post ID or post object.
 *
 * @return string The post title or fallback text.
 */
function gutenberg_block_core_breadcrumbs_get_post_title( $post_id_or_object ) {
	$title = get_the_title( $post_id_or_object );
	if ( strlen( $title ) === 0 ) {
		$title = __( '(no title)' );
	}
	return $title;
}

/**
 * Generates breadcrumb items from hierarchical post type ancestors.
 *
 * @since 6.9.0
 *
 * @param int    $post_id   The post ID.
 *
 * @return array Array of breadcrumb HTML items.
 */
function gutenberg_block_core_breadcrumbs_get_hierarchical_post_type_breadcrumbs( $post_id ) {
	$breadcrumb_items = array();
	$ancestors        = get_post_ancestors( $post_id );
	$ancestors        = array_reverse( $ancestors );

	foreach ( $ancestors as $ancestor_id ) {
		$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_link(
			get_permalink( $ancestor_id ),
			gutenberg_block_core_breadcrumbs_get_post_title( $ancestor_id ),
			true
		);
	}
	return $breadcrumb_items;
}

/**
 * Generates breadcrumb items for hierarchical term ancestors.
 *
 * For hierarchical taxonomies, retrieves and formats ancestor terms as breadcrumb links.
 *
 * @since 6.9.0
 *
 * @param int    $term_id  The term ID.
 * @param string $taxonomy The taxonomy name.
 *
 * @return array Array of breadcrumb HTML items for ancestors.
 */
function gutenberg_block_core_breadcrumbs_get_term_ancestors_items( $term_id, $taxonomy ) {
	$breadcrumb_items = array();

	// Check if taxonomy is hierarchical and add ancestor term links.
	if ( is_taxonomy_hierarchical( $taxonomy ) ) {
		$term_ancestors = get_ancestors( $term_id, $taxonomy, 'taxonomy' );
		$term_ancestors = array_reverse( $term_ancestors );
		foreach ( $term_ancestors as $ancestor_id ) {
			$ancestor_term = get_term( $ancestor_id, $taxonomy );
			if ( $ancestor_term && ! is_wp_error( $ancestor_term ) ) {
				$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_link(
					get_term_link( $ancestor_term ),
					$ancestor_term->name
				);
			}
		}
	}

	return $breadcrumb_items;
}

/**
 * Generates breadcrumb items for archive pages.
 *
 * Handles taxonomy archives, post type archives, date archives, and author archives.
 * For hierarchical taxonomies, includes ancestor terms in the breadcrumb trail.
 *
 * @since 6.9.0
 *
 * @return array Array of breadcrumb HTML items.
 */
function gutenberg_block_core_breadcrumbs_get_archive_breadcrumbs() {
	$breadcrumb_items = array();

	// Date archive (check first since it doesn't have a queried object).
	if ( is_date() ) {
		$year  = get_query_var( 'year' );
		$month = get_query_var( 'monthnum' );
		$day   = get_query_var( 'day' );

		// Fallback to 'm' query var for plain permalinks.
		// Plain permalinks use ?m=YYYYMMDD format instead of separate query vars.
		if ( ! $year ) {
			$m = get_query_var( 'm' );
			if ( $m ) {
				$year  = substr( $m, 0, 4 );
				$month = substr( $m, 4, 2 );
				$day   = (int) substr( $m, 6, 2 );
			}
		}

		$is_paged = gutenberg_block_core_breadcrumbs_is_paged();

		if ( $year ) {
			if ( $month ) {
				// Year is linked if we have month.
				$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_link(
					get_year_link( $year ),
					$year
				);

				if ( $day ) {
					// Month is linked if we have day.
					$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_link(
						get_month_link( $year, $month ),
						date_i18n( 'F', mktime( 0, 0, 0, $month, 1, $year ) )
					);
					// Add day (current if not paginated, link if paginated).
					$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_item(
						$day,
						$is_paged
					);
				} else {
					// Add month (current if not paginated, link if paginated).
					$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_item(
						date_i18n( 'F', mktime( 0, 0, 0, $month, 1, $year ) ),
						$is_paged
					);
				}
			} else {
				// Add year (current if not paginated, link if paginated).
				$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_item(
					$year,
					$is_paged
				);
			}
		}

		// Add pagination breadcrumb if on a paged date archive.
		if ( $is_paged ) {
			$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_page_number_item();
		}

		return $breadcrumb_items;
	}

	// For other archive types, we need a queried object.
	$queried_object = get_queried_object();

	if ( ! $queried_object ) {
		return array();
	}

	$is_paged = gutenberg_block_core_breadcrumbs_is_paged();

	// Taxonomy archive (category, tag, custom taxonomy).
	if ( $queried_object instanceof WP_Term ) {
		$term     = $queried_object;
		$taxonomy = $term->taxonomy;

		// Add hierarchical term ancestors if applicable.
		$breadcrumb_items = array_merge(
			$breadcrumb_items,
			gutenberg_block_core_breadcrumbs_get_term_ancestors_items( $term->term_id, $taxonomy )
		);

		// Add current term (current if not paginated, link if paginated).
		$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_item(
			$term->name,
			$is_paged
		);
	} elseif ( is_post_type_archive() ) {
		// Post type archive.
		$post_type = get_query_var( 'post_type' );
		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type );
		}
		$post_type_object = get_post_type_object( $post_type );
		if ( $post_type_object ) {
			// Add post type (current if not paginated, link if paginated).
			$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_item(
				$post_type_object->labels->name,
				$is_paged
			);
		}
	} elseif ( is_author() ) {
		// Author archive.
		$author = $queried_object;
		// Add author (current if not paginated, link if paginated).
		$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_item(
			$author->display_name,
			$is_paged
		);
	}

	// Add pagination breadcrumb if on a paged archive.
	if ( $is_paged ) {
		$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_page_number_item();
	}

	return $breadcrumb_items;
}

/**
 * Generates breadcrumb items from taxonomy terms.
 *
 * Finds the first publicly queryable taxonomy with terms assigned to the post
 * and generates breadcrumb links, including hierarchical term ancestors if applicable.
 *
 * @since 6.9.0
 *
 * @param int    $post_id   The post ID.
 * @param string $post_type The post type name.
 *
 * @return array Array of breadcrumb HTML items.
 */
function gutenberg_block_core_breadcrumbs_get_terms_breadcrumbs( $post_id, $post_type ) {
	$breadcrumb_items = array();
	// Get public taxonomies for this post type.
	$taxonomies = wp_filter_object_list(
		get_object_taxonomies( $post_type, 'objects' ),
		array(
			'publicly_queryable' => true,
			'show_in_rest'       => true,
		)
	);

	if ( empty( $taxonomies ) ) {
		return array();
	}

	// Find the first taxonomy that has terms assigned to this post.
	$taxonomy_name = null;
	$terms         = array();
	foreach ( $taxonomies as $taxonomy ) {
		$post_terms = get_the_terms( $post_id, $taxonomy->name );
		if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ) {
			$taxonomy_name = $taxonomy->name;
			$terms         = $post_terms;
			break;
		}
	}

	if ( ! empty( $terms ) ) {
		// Use the first term (if multiple are assigned).
		$term = reset( $terms );
		// Add hierarchical term ancestors if applicable.
		$breadcrumb_items   = array_merge(
			$breadcrumb_items,
			gutenberg_block_core_breadcrumbs_get_term_ancestors_items( $term->term_id, $taxonomy_name )
		);
		$breadcrumb_items[] = gutenberg_block_core_breadcrumbs_create_link(
			get_term_link( $term ),
			$term->name
		);
	}
	return $breadcrumb_items;
}

/**
 * Registers the `core/breadcrumbs` block on the server.
 *
 * @since 6.9.0
 */
function gutenberg_register_block_core_breadcrumbs() {
	register_block_type_from_metadata(
		__DIR__ . '/breadcrumbs',
		array(
			'render_callback' => 'gutenberg_render_block_core_breadcrumbs',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_breadcrumbs', 20 );

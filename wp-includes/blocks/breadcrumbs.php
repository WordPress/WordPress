<?php
/**
 * Server-side rendering of the `core/breadcrumbs` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/breadcrumbs` block on the server.
 *
 * @since 7.0.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the post breadcrumb for hierarchical post types.
 */
function render_block_core_breadcrumbs( $attributes, $content, $block ) {
	$is_front_page = is_front_page();

	if ( ! $attributes['showOnHomePage'] && $is_front_page ) {
		return '';
	}

	$is_home          = is_home();
	$page_for_posts   = get_option( 'page_for_posts' );
	$breadcrumb_items = array();

	if ( $attributes['showHomeItem'] ) {
		// We make `home` a link if not on front page, or if front page
		// is set to a custom page and is paged.
		if ( ! $is_front_page || ( 'page' === get_option( 'show_on_front' ) && (int) get_query_var( 'page' ) > 1 ) ) {
			$breadcrumb_items[] = array(
				'label' => __( 'Home' ),
				'url'   => home_url( '/' ),
			);
		} else {
			$breadcrumb_items[] = block_core_breadcrumbs_create_item( __( 'Home' ), block_core_breadcrumbs_is_paged() );
		}
	}

	// Handle home.
	if ( $is_home ) {
		// These checks are explicitly nested in order not to execute the `else` branch.
		if ( $page_for_posts ) {
			$breadcrumb_items[] = block_core_breadcrumbs_create_item( block_core_breadcrumbs_get_post_title( $page_for_posts ), block_core_breadcrumbs_is_paged() );
		}
		if ( block_core_breadcrumbs_is_paged() ) {
			$breadcrumb_items[] = block_core_breadcrumbs_create_page_number_item();
		}
	} elseif ( $is_front_page ) {
		// Handle front page.
		// This check is explicitly nested in order not to execute the `else` branch.
		// If front page is set to custom page and is paged, add the page number.
		if ( (int) get_query_var( 'page' ) > 1 ) {
			$breadcrumb_items[] = block_core_breadcrumbs_create_page_number_item( 'page' );
		}
	} elseif ( is_search() ) {
		// Handle search results.
		$is_paged = block_core_breadcrumbs_is_paged();
		/* translators: %s: search query */
		$text               = sprintf( __( 'Search results for: "%s"' ), wp_trim_words( get_search_query(), 10 ) );
		$breadcrumb_items[] = block_core_breadcrumbs_create_item( $text, $is_paged );
		// Add the "Page X" as the current page if paginated.
		if ( $is_paged ) {
			$breadcrumb_items[] = block_core_breadcrumbs_create_page_number_item();
		}
	} elseif ( is_404() ) {
		// Handle 404 pages.
		$breadcrumb_items[] = array(
			'label' => __( 'Page not found' ),
		);
	} elseif ( is_archive() ) {
		// Handle archive pages (taxonomy, post type, date, author archives).
		$archive_breadcrumbs = block_core_breadcrumbs_get_archive_breadcrumbs();
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

		// For non-hierarchical post types with parents (e.g., attachments), build trail for the parent.
		$post_parent = $post->post_parent;
		$parent_post = null;
		if ( ! is_post_type_hierarchical( $post_type ) && $post_parent ) {
			$parent_post = get_post( $post_parent );
			if ( $parent_post ) {
				$post_id     = $parent_post->ID;
				$post_type   = $parent_post->post_type;
				$post_parent = $parent_post->post_parent;
			}
		}

		// Determine breadcrumb type.
		// Some non-hierarchical post types (e.g., attachments) can have parents.
		// Use hierarchical breadcrumbs if a parent exists, otherwise use taxonomy breadcrumbs.
		$show_terms = false;
		if ( ! is_post_type_hierarchical( $post_type ) && ! $post_parent ) {
			$show_terms = true;
		} elseif ( empty( get_object_taxonomies( $post_type, 'objects' ) ) ) {
			$show_terms = false;
		} else {
			$show_terms = $attributes['prefersTaxonomy'];
		}

		// Add post type archive link if applicable.
		$post_type_object = get_post_type_object( $post_type );
		$archive_link     = get_post_type_archive_link( $post_type );
		if ( $archive_link && untrailingslashit( home_url() ) !== untrailingslashit( $archive_link ) ) {
			$label = $post_type_object->labels->archives;
			if ( 'post' === $post_type && $page_for_posts ) {
				$label = block_core_breadcrumbs_get_post_title( $page_for_posts );
			}
			$breadcrumb_items[] = array(
				'label' => $label,
				'url'   => $archive_link,
			);
		}
		// Build breadcrumb trail based on hierarchical structure or taxonomy terms.
		if ( ! $show_terms ) {
			$breadcrumb_items = array_merge( $breadcrumb_items, block_core_breadcrumbs_get_hierarchical_post_type_breadcrumbs( $post_id ) );
		} else {
			$breadcrumb_items = array_merge( $breadcrumb_items, block_core_breadcrumbs_get_terms_breadcrumbs( $post_id, $post_type ) );
		}

		// Add post title: linked when viewing a paginated page, plain text otherwise.
		$is_paged = (int) get_query_var( 'page' ) > 1 || (int) get_query_var( 'cpage' ) > 1;
		$title    = block_core_breadcrumbs_get_post_title( $post );

		if ( $is_paged ) {
			$breadcrumb_items[] = array(
				'label'      => $title,
				'url'        => get_permalink( $post ),
				'allow_html' => true,
			);
			$breadcrumb_items[] = block_core_breadcrumbs_create_page_number_item( (int) get_query_var( 'cpage' ) > 1 ? 'cpage' : 'page' );
		} else {
			$breadcrumb_items[] = array(
				'label'      => $title,
				'allow_html' => true,
			);
		}
	}

	// Remove current item if disabled.
	if ( ! $attributes['showCurrentItem'] && ! empty( $breadcrumb_items ) ) {
		array_pop( $breadcrumb_items );
	}

	/**
	 * Filters the breadcrumb items array before rendering.
	 *
	 * Allows developers to modify, add, or remove breadcrumb items.
	 *
	 * @since 7.0.0
	 *
	 * @param array[] $breadcrumb_items {
	 *     Array of breadcrumb item data.
	 *
	 *     @type string $label      The breadcrumb text.
	 *     @type string $url        Optional. The breadcrumb link URL.
	 *     @type bool   $allow_html Optional. Whether to allow HTML in the label.
	 *                              When true, the label will be sanitized with wp_kses_post(),
	 *                              allowing only safe HTML tags. When false or omitted, all HTML
	 *                              will be escaped with esc_html(). Default false.
	 * }
	 */
	$breadcrumb_items = apply_filters( 'block_core_breadcrumbs_items', $breadcrumb_items );

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
					$label = ! empty( $item['allow_html'] ) ? wp_kses_post( $item['label'] ) : esc_html( $item['label'] );
					if ( ! empty( $item['url'] ) ) {
						return '<li><a href="' . esc_url( $item['url'] ) . '">' . $label . '</a></li>';
					}
					return '<li><span aria-current="page">' . $label . '</span></li>';
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
 * @since 7.0.0
 *
 * @return bool True if paged > 1, false otherwise.
 */
function block_core_breadcrumbs_is_paged() {
	$paged = (int) get_query_var( 'paged' );
	return $paged > 1;
}

/**
 * Creates a "Page X" breadcrumb item for paginated views.
 *
 * @since 7.0.0
 * @param string $query_var Optional. Query variable to get current page number. Default 'paged'.
 * @return array The "Page X" breadcrumb item data.
 */
function block_core_breadcrumbs_create_page_number_item( $query_var = 'paged' ) {
	$paged = (int) get_query_var( $query_var );

	if ( 'cpage' === $query_var ) {
		return array(
			'label' => sprintf(
				/* translators: %s: comment page number */
				__( 'Comments Page %s' ),
				number_format_i18n( $paged )
			),
		);
	}

	return array(
		'label' => sprintf(
			/* translators: %s: page number */
			__( 'Page %s' ),
			number_format_i18n( $paged )
		),
	);
}


/**
 * Creates a breadcrumb item that's either a link or current page item.
 *
 * When paginated (is_paged is true), creates a link to page 1.
 * Otherwise, creates a span marked as the current page.
 *
 * @since 7.0.0
 *
 * @param string $text       The text content.
 * @param bool   $is_paged   Whether we're on a paginated view.
 *
 * @return array The breadcrumb item data.
 */
function block_core_breadcrumbs_create_item( $text, $is_paged = false ) {
	$item = array( 'label' => $text );
	if ( $is_paged ) {
		$item['url'] = get_pagenum_link( 1 );
	}
	return $item;
}

/**
 * Gets a post title with fallback for empty titles.
 *
 * @since 7.0.0
 *
 * @param int|WP_Post $post_id_or_object The post ID or post object.
 *
 * @return string The post title or fallback text.
 */
function block_core_breadcrumbs_get_post_title( $post_id_or_object ) {
	$title = get_the_title( $post_id_or_object );
	if ( strlen( $title ) === 0 ) {
		$title = __( '(no title)' );
	}
	return $title;
}

/**
 * Generates breadcrumb items from hierarchical post type ancestors.
 *
 * @since 7.0.0
 *
 * @param int    $post_id   The post ID.
 *
 * @return array Array of breadcrumb item data.
 */
function block_core_breadcrumbs_get_hierarchical_post_type_breadcrumbs( $post_id ) {
	$breadcrumb_items = array();
	$ancestors        = get_post_ancestors( $post_id );
	$ancestors        = array_reverse( $ancestors );

	foreach ( $ancestors as $ancestor_id ) {
		$breadcrumb_items[] = array(
			'label'      => block_core_breadcrumbs_get_post_title( $ancestor_id ),
			'url'        => get_permalink( $ancestor_id ),
			'allow_html' => true,
		);
	}
	return $breadcrumb_items;
}

/**
 * Generates breadcrumb items for hierarchical term ancestors.
 *
 * For hierarchical taxonomies, retrieves and formats ancestor terms as breadcrumb links.
 *
 * @since 7.0.0
 *
 * @param int    $term_id  The term ID.
 * @param string $taxonomy The taxonomy name.
 *
 * @return array Array of breadcrumb item data for ancestors.
 */
function block_core_breadcrumbs_get_term_ancestors_items( $term_id, $taxonomy ) {
	$breadcrumb_items = array();

	// Check if taxonomy is hierarchical and add ancestor term links.
	if ( is_taxonomy_hierarchical( $taxonomy ) ) {
		$term_ancestors = get_ancestors( $term_id, $taxonomy, 'taxonomy' );
		$term_ancestors = array_reverse( $term_ancestors );
		foreach ( $term_ancestors as $ancestor_id ) {
			$ancestor_term = get_term( $ancestor_id, $taxonomy );
			if ( $ancestor_term && ! is_wp_error( $ancestor_term ) ) {
				$breadcrumb_items[] = array(
					'label' => $ancestor_term->name,
					'url'   => get_term_link( $ancestor_term ),
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
 * @since 7.0.0
 *
 * @return array Array of breadcrumb item data.
 */
function block_core_breadcrumbs_get_archive_breadcrumbs() {
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

		$is_paged = block_core_breadcrumbs_is_paged();

		if ( $year ) {
			if ( $month ) {
				// Year is linked if we have month.
				$breadcrumb_items[] = array(
					'label' => $year,
					'url'   => get_year_link( $year ),
				);

				if ( $day ) {
					// Month is linked if we have day.
					$breadcrumb_items[] = array(
						'label' => date_i18n( 'F', mktime( 0, 0, 0, $month, 1, $year ) ),
						'url'   => get_month_link( $year, $month ),
					);
					// Add day (current if not paginated, link if paginated).
					$breadcrumb_items[] = block_core_breadcrumbs_create_item(
						$day,
						$is_paged
					);
				} else {
					// Add month (current if not paginated, link if paginated).
					$breadcrumb_items[] = block_core_breadcrumbs_create_item(
						date_i18n( 'F', mktime( 0, 0, 0, $month, 1, $year ) ),
						$is_paged
					);
				}
			} else {
				// Add year (current if not paginated, link if paginated).
				$breadcrumb_items[] = block_core_breadcrumbs_create_item(
					$year,
					$is_paged
				);
			}
		}

		// Add pagination breadcrumb if on a paged date archive.
		if ( $is_paged ) {
			$breadcrumb_items[] = block_core_breadcrumbs_create_page_number_item();
		}

		return $breadcrumb_items;
	}

	// For other archive types, we need a queried object.
	$queried_object = get_queried_object();

	if ( ! $queried_object ) {
		return array();
	}

	$is_paged = block_core_breadcrumbs_is_paged();

	// Taxonomy archive (category, tag, custom taxonomy).
	if ( $queried_object instanceof WP_Term ) {
		$term     = $queried_object;
		$taxonomy = $term->taxonomy;

		// Add hierarchical term ancestors if applicable.
		$breadcrumb_items = array_merge(
			$breadcrumb_items,
			block_core_breadcrumbs_get_term_ancestors_items( $term->term_id, $taxonomy )
		);

		// Add current term (current if not paginated, link if paginated).
		$breadcrumb_items[] = block_core_breadcrumbs_create_item(
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

		/** This filter is documented in wp-includes/general-template.php */
		$title = apply_filters( 'post_type_archive_title', $post_type_object->labels->archives, $post_type ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

		if ( $post_type_object ) {
			// Add post type (current if not paginated, link if paginated).
			$breadcrumb_items[] = block_core_breadcrumbs_create_item(
				$title ? $title : $post_type_object->labels->archives,
				$is_paged
			);
		}
	} elseif ( is_author() ) {
		// Author archive.
		$author = $queried_object;
		// Add author (current if not paginated, link if paginated).
		$breadcrumb_items[] = block_core_breadcrumbs_create_item(
			$author->display_name,
			$is_paged
		);
	}

	// Add pagination breadcrumb if on a paged archive.
	if ( $is_paged ) {
		$breadcrumb_items[] = block_core_breadcrumbs_create_page_number_item();
	}

	return $breadcrumb_items;
}

/**
 * Generates breadcrumb items from taxonomy terms.
 *
 * Finds the first publicly queryable taxonomy with terms assigned to the post
 * and generates breadcrumb links, including hierarchical term ancestors if applicable.
 *
 * @since 7.0.0
 *
 * @param int    $post_id   The post ID.
 * @param string $post_type The post type name.
 *
 * @return array Array of breadcrumb item data.
 */
function block_core_breadcrumbs_get_terms_breadcrumbs( $post_id, $post_type ) {
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
		return $breadcrumb_items;
	}

	/**
	 * Filters breadcrumb settings (taxonomy and term selection) for a post or post type.
	 *
	 * Allows developers to specify which taxonomy and term should be used in the
	 * breadcrumb trail when a post type has multiple taxonomies or when a post is
	 * assigned to multiple terms within a taxonomy.
	 *
	 * @since 7.0.0
	 *
	 * @param array  $settings {
	 *     Array of breadcrumb settings. Default empty array.
	 *
	 *     @type string $taxonomy Optional. Taxonomy slug to use for breadcrumbs.
	 *                            The taxonomy must be registered for the post type and have
	 *                            terms assigned to the post. If not found or has no terms,
	 *                            fall back to the first available taxonomy with terms.
	 *     @type string $term     Optional. Term slug to use when the post has multiple terms
	 *                            in the selected taxonomy. If the term is not found or not
	 *                            assigned to the post, fall back to the first term. If the
	 *                            post has only one term, that term is used regardless.
	 * }
	 * @param string $post_type The post type slug.
	 * @param int    $post_id   The post ID.
	 */
	$settings = apply_filters( 'block_core_breadcrumbs_post_type_settings', array(), $post_type, $post_id );

	$taxonomy_name = null;
	$terms         = array();

	// Try preferred taxonomy first if specified.
	if ( ! empty( $settings['taxonomy'] ) ) {
		foreach ( $taxonomies as $taxonomy ) {
			if ( $taxonomy->name === $settings['taxonomy'] ) {
				$post_terms = get_the_terms( $post_id, $taxonomy->name );
				if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ) {
					$taxonomy_name = $taxonomy->name;
					$terms         = $post_terms;
				}
				break;
			}
		}
	}

	// If no preferred taxonomy or it didn't have terms, find the first taxonomy with terms.
	if ( empty( $terms ) ) {
		foreach ( $taxonomies as $taxonomy ) {
			$post_terms = get_the_terms( $post_id, $taxonomy->name );
			if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ) {
				$taxonomy_name = $taxonomy->name;
				$terms         = $post_terms;
				break;
			}
		}
	}

	if ( ! empty( $terms ) ) {
		// Select which term to use.
		$term = reset( $terms );

		// Try preferred term if specified and post has multiple terms.
		if ( ! empty( $settings['term'] ) && count( $terms ) > 1 ) {
			foreach ( $terms as $candidate_term ) {
				if ( $candidate_term->slug === $settings['term'] ) {
					$term = $candidate_term;
					break;
				}
			}
		}

		// Add hierarchical term ancestors if applicable.
		$breadcrumb_items   = array_merge(
			$breadcrumb_items,
			block_core_breadcrumbs_get_term_ancestors_items( $term->term_id, $taxonomy_name )
		);
		$breadcrumb_items[] = array(
			'label' => $term->name,
			'url'   => get_term_link( $term ),
		);
	}
	return $breadcrumb_items;
}

/**
 * Registers the `core/breadcrumbs` block on the server.
 *
 * @since 7.0.0
 */
function register_block_core_breadcrumbs() {
	register_block_type_from_metadata(
		__DIR__ . '/breadcrumbs',
		array(
			'render_callback' => 'render_block_core_breadcrumbs',
		)
	);
}
add_action( 'init', 'register_block_core_breadcrumbs' );

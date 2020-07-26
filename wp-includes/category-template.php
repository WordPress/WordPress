<?php
/**
 * Taxonomy API: Core category-specific template tags
 *
 * @package WordPress
 * @subpackage Template
 * @since 1.2.0
 */

/**
 * Retrieves category link URL.
 *
 * @since 1.0.0
 *
 * @see get_term_link()
 *
 * @param int|object $category Category ID or object.
 * @return string Link on success, empty string if category does not exist.
 */
function get_category_link( $category ) {
	if ( ! is_object( $category ) ) {
		$category = (int) $category;
	}

	$category = get_term_link( $category );

	if ( is_wp_error( $category ) ) {
		return '';
	}

	return $category;
}

/**
 * Retrieves category parents with separator.
 *
 * @since 1.2.0
 * @since 4.8.0 The `$visited` parameter was deprecated and renamed to `$deprecated`.
 *
 * @param int    $category_id Category ID.
 * @param bool   $link        Optional. Whether to format with link. Default false.
 * @param string $separator   Optional. How to separate categories. Default '/'.
 * @param bool   $nicename    Optional. Whether to use nice name for display. Default false.
 * @param array  $deprecated  Not used.
 * @return string|WP_Error A list of category parents on success, WP_Error on failure.
 */
function get_category_parents( $category_id, $link = false, $separator = '/', $nicename = false, $deprecated = array() ) {

	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '4.8.0' );
	}

	$format = $nicename ? 'slug' : 'name';

	$args = array(
		'separator' => $separator,
		'link'      => $link,
		'format'    => $format,
	);

	return get_term_parents_list( $category_id, 'category', $args );
}

/**
 * Retrieves post categories.
 *
 * This tag may be used outside The Loop by passing a post ID as the parameter.
 *
 * Note: This function only returns results from the default "category" taxonomy.
 * For custom taxonomies use get_the_terms().
 *
 * @since 0.71
 *
 * @param int $post_id Optional. The post ID. Defaults to current post ID.
 * @return WP_Term[] Array of WP_Term objects, one for each category assigned to the post.
 */
function get_the_category( $post_id = false ) {
	$categories = get_the_terms( $post_id, 'category' );
	if ( ! $categories || is_wp_error( $categories ) ) {
		$categories = array();
	}

	$categories = array_values( $categories );

	foreach ( array_keys( $categories ) as $key ) {
		_make_cat_compat( $categories[ $key ] );
	}

	/**
	 * Filters the array of categories to return for a post.
	 *
	 * @since 3.1.0
	 * @since 4.4.0 Added `$post_id` parameter.
	 *
	 * @param WP_Term[] $categories An array of categories to return for the post.
	 * @param int|false $post_id    ID of the post.
	 */
	return apply_filters( 'get_the_categories', $categories, $post_id );
}

/**
 * Retrieves category name based on category ID.
 *
 * @since 0.71
 *
 * @param int $cat_ID Category ID.
 * @return string|WP_Error Category name on success, WP_Error on failure.
 */
function get_the_category_by_ID( $cat_ID ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	$cat_ID   = (int) $cat_ID;
	$category = get_term( $cat_ID );

	if ( is_wp_error( $category ) ) {
		return $category;
	}

	return ( $category ) ? $category->name : '';
}

/**
 * Retrieves category list for a post in either HTML list or custom format.
 *
 * Generally used for quick, delimited (e.g. comma-separated) lists of categories,
 * as part of a post entry meta.
 *
 * For a more powerful, list-based function, see wp_list_categories().
 *
 * @since 1.5.1
 *
 * @see wp_list_categories()
 *
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 *
 * @param string $separator Optional. Separator between the categories. By default, the links are placed
 *                          in an unordered list. An empty string will result in the default behavior.
 * @param string $parents   Optional. How to display the parents.
 * @param int    $post_id   Optional. Post ID to retrieve categories.
 * @return string Category list for a post.
 */
function get_the_category_list( $separator = '', $parents = '', $post_id = false ) {
	global $wp_rewrite;

	if ( ! is_object_in_taxonomy( get_post_type( $post_id ), 'category' ) ) {
		/** This filter is documented in wp-includes/category-template.php */
		return apply_filters( 'the_category', '', $separator, $parents );
	}

	/**
	 * Filters the categories before building the category list.
	 *
	 * @since 4.4.0
	 *
	 * @param WP_Term[] $categories An array of the post's categories.
	 * @param int|bool  $post_id    ID of the post we're retrieving categories for.
	 *                              When `false`, we assume the current post in the loop.
	 */
	$categories = apply_filters( 'the_category_list', get_the_category( $post_id ), $post_id );

	if ( empty( $categories ) ) {
		/** This filter is documented in wp-includes/category-template.php */
		return apply_filters( 'the_category', __( 'Uncategorized' ), $separator, $parents );
	}

	$rel = ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';

	$thelist = '';
	if ( '' === $separator ) {
		$thelist .= '<ul class="post-categories">';
		foreach ( $categories as $category ) {
			$thelist .= "\n\t<li>";
			switch ( strtolower( $parents ) ) {
				case 'multiple':
					if ( $category->parent ) {
						$thelist .= get_category_parents( $category->parent, true, $separator );
					}
					$thelist .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" ' . $rel . '>' . $category->name . '</a></li>';
					break;
				case 'single':
					$thelist .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '"  ' . $rel . '>';
					if ( $category->parent ) {
						$thelist .= get_category_parents( $category->parent, false, $separator );
					}
					$thelist .= $category->name . '</a></li>';
					break;
				case '':
				default:
					$thelist .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" ' . $rel . '>' . $category->name . '</a></li>';
			}
		}
		$thelist .= '</ul>';
	} else {
		$i = 0;
		foreach ( $categories as $category ) {
			if ( 0 < $i ) {
				$thelist .= $separator;
			}
			switch ( strtolower( $parents ) ) {
				case 'multiple':
					if ( $category->parent ) {
						$thelist .= get_category_parents( $category->parent, true, $separator );
					}
					$thelist .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" ' . $rel . '>' . $category->name . '</a>';
					break;
				case 'single':
					$thelist .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" ' . $rel . '>';
					if ( $category->parent ) {
						$thelist .= get_category_parents( $category->parent, false, $separator );
					}
					$thelist .= "$category->name</a>";
					break;
				case '':
				default:
					$thelist .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" ' . $rel . '>' . $category->name . '</a>';
			}
			++$i;
		}
	}

	/**
	 * Filters the category or list of categories.
	 *
	 * @since 1.2.0
	 *
	 * @param string $thelist   List of categories for the current post.
	 * @param string $separator Separator used between the categories.
	 * @param string $parents   How to display the category parents. Accepts 'multiple',
	 *                          'single', or empty.
	 */
	return apply_filters( 'the_category', $thelist, $separator, $parents );
}

/**
 * Checks if the current post is within any of the given categories.
 *
 * The given categories are checked against the post's categories' term_ids, names and slugs.
 * Categories given as integers will only be checked against the post's categories' term_ids.
 *
 * Prior to v2.5 of WordPress, category names were not supported.
 * Prior to v2.7, category slugs were not supported.
 * Prior to v2.7, only one category could be compared: in_category( $single_category ).
 * Prior to v2.7, this function could only be used in the WordPress Loop.
 * As of 2.7, the function can be used anywhere if it is provided a post ID or post object.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.2.0
 * @since 2.7.0 The `$post` parameter was added.
 *
 * @param int|string|array $category Category ID, name or slug, or array of said.
 * @param int|object       $post     Optional. Post to check instead of the current post.
 * @return bool True if the current post is in any of the given categories.
 */
function in_category( $category, $post = null ) {
	if ( empty( $category ) ) {
		return false;
	}

	return has_category( $category, $post );
}

/**
 * Displays category list for a post in either HTML list or custom format.
 *
 * @since 0.71
 *
 * @param string $separator Optional. Separator between the categories. By default, the links are placed
 *                          in an unordered list. An empty string will result in the default behavior.
 * @param string $parents   Optional. How to display the parents.
 * @param int    $post_id   Optional. Post ID to retrieve categories.
 */
function the_category( $separator = '', $parents = '', $post_id = false ) {
	echo get_the_category_list( $separator, $parents, $post_id );
}

/**
 * Retrieves category description.
 *
 * @since 1.0.0
 *
 * @param int $category Optional. Category ID. Defaults to the current category ID.
 * @return string Category description, if available.
 */
function category_description( $category = 0 ) {
	return term_description( $category );
}

/**
 * Displays or retrieves the HTML dropdown list of categories.
 *
 * The 'hierarchical' argument, which is disabled by default, will override the
 * depth argument, unless it is true. When the argument is false, it will
 * display all of the categories. When it is enabled it will use the value in
 * the 'depth' argument.
 *
 * @since 2.1.0
 * @since 4.2.0 Introduced the `value_field` argument.
 * @since 4.6.0 Introduced the `required` argument.
 *
 * @param array|string $args {
 *     Optional. Array or string of arguments to generate a categories drop-down element. See WP_Term_Query::__construct()
 *     for information on additional accepted arguments.
 *
 *     @type string       $show_option_all   Text to display for showing all categories. Default empty.
 *     @type string       $show_option_none  Text to display for showing no categories. Default empty.
 *     @type string       $option_none_value Value to use when no category is selected. Default empty.
 *     @type string       $orderby           Which column to use for ordering categories. See get_terms() for a list
 *                                           of accepted values. Default 'id' (term_id).
 *     @type bool         $pad_counts        See get_terms() for an argument description. Default false.
 *     @type bool|int     $show_count        Whether to include post counts. Accepts 0, 1, or their bool equivalents.
 *                                           Default 0.
 *     @type bool|int     $echo              Whether to echo or return the generated markup. Accepts 0, 1, or their
 *                                           bool equivalents. Default 1.
 *     @type bool|int     $hierarchical      Whether to traverse the taxonomy hierarchy. Accepts 0, 1, or their bool
 *                                           equivalents. Default 0.
 *     @type int          $depth             Maximum depth. Default 0.
 *     @type int          $tab_index         Tab index for the select element. Default 0 (no tabindex).
 *     @type string       $name              Value for the 'name' attribute of the select element. Default 'cat'.
 *     @type string       $id                Value for the 'id' attribute of the select element. Defaults to the value
 *                                           of `$name`.
 *     @type string       $class             Value for the 'class' attribute of the select element. Default 'postform'.
 *     @type int|string   $selected          Value of the option that should be selected. Default 0.
 *     @type string       $value_field       Term field that should be used to populate the 'value' attribute
 *                                           of the option elements. Accepts any valid term field: 'term_id', 'name',
 *                                           'slug', 'term_group', 'term_taxonomy_id', 'taxonomy', 'description',
 *                                           'parent', 'count'. Default 'term_id'.
 *     @type string|array $taxonomy          Name of the category or categories to retrieve. Default 'category'.
 *     @type bool         $hide_if_empty     True to skip generating markup if no categories are found.
 *                                           Default false (create select element even if no categories are found).
 *     @type bool         $required          Whether the `<select>` element should have the HTML5 'required' attribute.
 *                                           Default false.
 * }
 * @return string HTML dropdown list of categories.
 */
function wp_dropdown_categories( $args = '' ) {
	$defaults = array(
		'show_option_all'   => '',
		'show_option_none'  => '',
		'orderby'           => 'id',
		'order'             => 'ASC',
		'show_count'        => 0,
		'hide_empty'        => 1,
		'child_of'          => 0,
		'exclude'           => '',
		'echo'              => 1,
		'selected'          => 0,
		'hierarchical'      => 0,
		'name'              => 'cat',
		'id'                => '',
		'class'             => 'postform',
		'depth'             => 0,
		'tab_index'         => 0,
		'taxonomy'          => 'category',
		'hide_if_empty'     => false,
		'option_none_value' => -1,
		'value_field'       => 'term_id',
		'required'          => false,
	);

	$defaults['selected'] = ( is_category() ) ? get_query_var( 'cat' ) : 0;

	// Back compat.
	if ( isset( $args['type'] ) && 'link' === $args['type'] ) {
		_deprecated_argument(
			__FUNCTION__,
			'3.0.0',
			sprintf(
				/* translators: 1: "type => link", 2: "taxonomy => link_category" */
				__( '%1$s is deprecated. Use %2$s instead.' ),
				'<code>type => link</code>',
				'<code>taxonomy => link_category</code>'
			)
		);
		$args['taxonomy'] = 'link_category';
	}

	// Parse incoming $args into an array and merge it with $defaults.
	$parsed_args = wp_parse_args( $args, $defaults );

	$option_none_value = $parsed_args['option_none_value'];

	if ( ! isset( $parsed_args['pad_counts'] ) && $parsed_args['show_count'] && $parsed_args['hierarchical'] ) {
		$parsed_args['pad_counts'] = true;
	}

	$tab_index = $parsed_args['tab_index'];

	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 ) {
		$tab_index_attribute = " tabindex=\"$tab_index\"";
	}

	// Avoid clashes with the 'name' param of get_terms().
	$get_terms_args = $parsed_args;
	unset( $get_terms_args['name'] );
	$categories = get_terms( $get_terms_args );

	$name     = esc_attr( $parsed_args['name'] );
	$class    = esc_attr( $parsed_args['class'] );
	$id       = $parsed_args['id'] ? esc_attr( $parsed_args['id'] ) : $name;
	$required = $parsed_args['required'] ? 'required' : '';

	if ( ! $parsed_args['hide_if_empty'] || ! empty( $categories ) ) {
		$output = "<select $required name='$name' id='$id' class='$class' $tab_index_attribute>\n";
	} else {
		$output = '';
	}
	if ( empty( $categories ) && ! $parsed_args['hide_if_empty'] && ! empty( $parsed_args['show_option_none'] ) ) {

		/**
		 * Filters a taxonomy drop-down display element.
		 *
		 * A variety of taxonomy drop-down display elements can be modified
		 * just prior to display via this filter. Filterable arguments include
		 * 'show_option_none', 'show_option_all', and various forms of the
		 * term name.
		 *
		 * @since 1.2.0
		 *
		 * @see wp_dropdown_categories()
		 *
		 * @param string       $element  Category name.
		 * @param WP_Term|null $category The category object, or null if there's no corresponding category.
		 */
		$show_option_none = apply_filters( 'list_cats', $parsed_args['show_option_none'], null );
		$output          .= "\t<option value='" . esc_attr( $option_none_value ) . "' selected='selected'>$show_option_none</option>\n";
	}

	if ( ! empty( $categories ) ) {

		if ( $parsed_args['show_option_all'] ) {

			/** This filter is documented in wp-includes/category-template.php */
			$show_option_all = apply_filters( 'list_cats', $parsed_args['show_option_all'], null );
			$selected        = ( '0' === strval( $parsed_args['selected'] ) ) ? " selected='selected'" : '';
			$output         .= "\t<option value='0'$selected>$show_option_all</option>\n";
		}

		if ( $parsed_args['show_option_none'] ) {

			/** This filter is documented in wp-includes/category-template.php */
			$show_option_none = apply_filters( 'list_cats', $parsed_args['show_option_none'], null );
			$selected         = selected( $option_none_value, $parsed_args['selected'], false );
			$output          .= "\t<option value='" . esc_attr( $option_none_value ) . "'$selected>$show_option_none</option>\n";
		}

		if ( $parsed_args['hierarchical'] ) {
			$depth = $parsed_args['depth'];  // Walk the full depth.
		} else {
			$depth = -1; // Flat.
		}
		$output .= walk_category_dropdown_tree( $categories, $depth, $parsed_args );
	}

	if ( ! $parsed_args['hide_if_empty'] || ! empty( $categories ) ) {
		$output .= "</select>\n";
	}

	/**
	 * Filters the taxonomy drop-down output.
	 *
	 * @since 2.1.0
	 *
	 * @param string $output      HTML output.
	 * @param array  $parsed_args Arguments used to build the drop-down.
	 */
	$output = apply_filters( 'wp_dropdown_cats', $output, $parsed_args );

	if ( $parsed_args['echo'] ) {
		echo $output;
	}

	return $output;
}

/**
 * Displays or retrieves the HTML list of categories.
 *
 * @since 2.1.0
 * @since 4.4.0 Introduced the `hide_title_if_empty` and `separator` arguments.
 * @since 4.4.0 The `current_category` argument was modified to optionally accept an array of values.
 *
 * @param array|string $args {
 *     Array of optional arguments. See get_categories(), get_terms(), and WP_Term_Query::__construct()
 *     for information on additional accepted arguments.
 *
 *     @type int|array    $current_category      ID of category, or array of IDs of categories, that should get the
 *                                               'current-cat' class. Default 0.
 *     @type int          $depth                 Category depth. Used for tab indentation. Default 0.
 *     @type bool|int     $echo                  Whether to echo or return the generated markup. Accepts 0, 1, or their
 *                                               bool equivalents. Default 1.
 *     @type array|string $exclude               Array or comma/space-separated string of term IDs to exclude.
 *                                               If `$hierarchical` is true, descendants of `$exclude` terms will also
 *                                               be excluded; see `$exclude_tree`. See get_terms().
 *                                               Default empty string.
 *     @type array|string $exclude_tree          Array or comma/space-separated string of term IDs to exclude, along
 *                                               with their descendants. See get_terms(). Default empty string.
 *     @type string       $feed                  Text to use for the feed link. Default 'Feed for all posts filed
 *                                               under [cat name]'.
 *     @type string       $feed_image            URL of an image to use for the feed link. Default empty string.
 *     @type string       $feed_type             Feed type. Used to build feed link. See get_term_feed_link().
 *                                               Default empty string (default feed).
 *     @type bool         $hide_title_if_empty   Whether to hide the `$title_li` element if there are no terms in
 *                                               the list. Default false (title will always be shown).
 *     @type string       $separator             Separator between links. Default '<br />'.
 *     @type bool|int     $show_count            Whether to include post counts. Accepts 0, 1, or their bool equivalents.
 *                                               Default 0.
 *     @type string       $show_option_all       Text to display for showing all categories. Default empty string.
 *     @type string       $show_option_none      Text to display for the 'no categories' option.
 *                                               Default 'No categories'.
 *     @type string       $style                 The style used to display the categories list. If 'list', categories
 *                                               will be output as an unordered list. If left empty or another value,
 *                                               categories will be output separated by `<br>` tags. Default 'list'.
 *     @type string       $title_li              Text to use for the list title `<li>` element. Pass an empty string
 *                                               to disable. Default 'Categories'.
 *     @type bool|int     $use_desc_for_title    Whether to use the category description as the title attribute.
 *                                               Accepts 0, 1, or their bool equivalents. Default 1.
 * }
 * @return void|string|false Void if 'echo' argument is true, HTML list of categories if 'echo' is false.
 *                           False if the taxonomy does not exist.
 */
function wp_list_categories( $args = '' ) {
	$defaults = array(
		'child_of'            => 0,
		'current_category'    => 0,
		'depth'               => 0,
		'echo'                => 1,
		'exclude'             => '',
		'exclude_tree'        => '',
		'feed'                => '',
		'feed_image'          => '',
		'feed_type'           => '',
		'hide_empty'          => 1,
		'hide_title_if_empty' => false,
		'hierarchical'        => true,
		'order'               => 'ASC',
		'orderby'             => 'name',
		'separator'           => '<br />',
		'show_count'          => 0,
		'show_option_all'     => '',
		'show_option_none'    => __( 'No categories' ),
		'style'               => 'list',
		'taxonomy'            => 'category',
		'title_li'            => __( 'Categories' ),
		'use_desc_for_title'  => 1,
	);

	$parsed_args = wp_parse_args( $args, $defaults );

	if ( ! isset( $parsed_args['pad_counts'] ) && $parsed_args['show_count'] && $parsed_args['hierarchical'] ) {
		$parsed_args['pad_counts'] = true;
	}

	// Descendants of exclusions should be excluded too.
	if ( true == $parsed_args['hierarchical'] ) {
		$exclude_tree = array();

		if ( $parsed_args['exclude_tree'] ) {
			$exclude_tree = array_merge( $exclude_tree, wp_parse_id_list( $parsed_args['exclude_tree'] ) );
		}

		if ( $parsed_args['exclude'] ) {
			$exclude_tree = array_merge( $exclude_tree, wp_parse_id_list( $parsed_args['exclude'] ) );
		}

		$parsed_args['exclude_tree'] = $exclude_tree;
		$parsed_args['exclude']      = '';
	}

	if ( ! isset( $parsed_args['class'] ) ) {
		$parsed_args['class'] = ( 'category' === $parsed_args['taxonomy'] ) ? 'categories' : $parsed_args['taxonomy'];
	}

	if ( ! taxonomy_exists( $parsed_args['taxonomy'] ) ) {
		return false;
	}

	$show_option_all  = $parsed_args['show_option_all'];
	$show_option_none = $parsed_args['show_option_none'];

	$categories = get_categories( $parsed_args );

	$output = '';

	if ( $parsed_args['title_li'] && 'list' === $parsed_args['style']
		&& ( ! empty( $categories ) || ! $parsed_args['hide_title_if_empty'] )
	) {
		$output = '<li class="' . esc_attr( $parsed_args['class'] ) . '">' . $parsed_args['title_li'] . '<ul>';
	}

	if ( empty( $categories ) ) {
		if ( ! empty( $show_option_none ) ) {
			if ( 'list' === $parsed_args['style'] ) {
				$output .= '<li class="cat-item-none">' . $show_option_none . '</li>';
			} else {
				$output .= $show_option_none;
			}
		}
	} else {
		if ( ! empty( $show_option_all ) ) {

			$posts_page = '';

			// For taxonomies that belong only to custom post types, point to a valid archive.
			$taxonomy_object = get_taxonomy( $parsed_args['taxonomy'] );
			if ( ! in_array( 'post', $taxonomy_object->object_type, true ) && ! in_array( 'page', $taxonomy_object->object_type, true ) ) {
				foreach ( $taxonomy_object->object_type as $object_type ) {
					$_object_type = get_post_type_object( $object_type );

					// Grab the first one.
					if ( ! empty( $_object_type->has_archive ) ) {
						$posts_page = get_post_type_archive_link( $object_type );
						break;
					}
				}
			}

			// Fallback for the 'All' link is the posts page.
			if ( ! $posts_page ) {
				if ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) ) {
					$posts_page = get_permalink( get_option( 'page_for_posts' ) );
				} else {
					$posts_page = home_url( '/' );
				}
			}

			$posts_page = esc_url( $posts_page );
			if ( 'list' === $parsed_args['style'] ) {
				$output .= "<li class='cat-item-all'><a href='$posts_page'>$show_option_all</a></li>";
			} else {
				$output .= "<a href='$posts_page'>$show_option_all</a>";
			}
		}

		if ( empty( $parsed_args['current_category'] ) && ( is_category() || is_tax() || is_tag() ) ) {
			$current_term_object = get_queried_object();
			if ( $current_term_object && $parsed_args['taxonomy'] === $current_term_object->taxonomy ) {
				$parsed_args['current_category'] = get_queried_object_id();
			}
		}

		if ( $parsed_args['hierarchical'] ) {
			$depth = $parsed_args['depth'];
		} else {
			$depth = -1; // Flat.
		}
		$output .= walk_category_tree( $categories, $depth, $parsed_args );
	}

	if ( $parsed_args['title_li'] && 'list' === $parsed_args['style']
		&& ( ! empty( $categories ) || ! $parsed_args['hide_title_if_empty'] )
	) {
		$output .= '</ul></li>';
	}

	/**
	 * Filters the HTML output of a taxonomy list.
	 *
	 * @since 2.1.0
	 *
	 * @param string $output HTML output.
	 * @param array  $args   An array of taxonomy-listing arguments.
	 */
	$html = apply_filters( 'wp_list_categories', $output, $args );

	if ( $parsed_args['echo'] ) {
		echo $html;
	} else {
		return $html;
	}
}

/**
 * Displays a tag cloud.
 *
 * Outputs a list of tags in what is called a 'tag cloud', where the size of each tag
 * is determined by how many times that particular tag has been assigned to posts.
 *
 * @since 2.3.0
 * @since 2.8.0 Added the `taxonomy` argument.
 * @since 4.8.0 Added the `show_count` argument.
 *
 * @param array|string $args {
 *     Optional. Array or string of arguments for displaying a tag cloud. See wp_generate_tag_cloud()
 *     and get_terms() for the full lists of arguments that can be passed in `$args`.
 *
 *     @type int    $number    The number of tags to display. Accepts any positive integer
 *                             or zero to return all. Default 0 (all tags).
 *     @type string $link      Whether to display term editing links or term permalinks.
 *                             Accepts 'edit' and 'view'. Default 'view'.
 *     @type string $post_type The post type. Used to highlight the proper post type menu
 *                             on the linked edit page. Defaults to the first post type
 *                             associated with the taxonomy.
 *     @type bool   $echo      Whether or not to echo the return value. Default true.
 * }
 * @return void|string|array Void if 'echo' argument is true, or on failure. Otherwise, tag cloud
 *                           as a string or an array, depending on 'format' argument.
 */
function wp_tag_cloud( $args = '' ) {
	$defaults = array(
		'smallest'   => 8,
		'largest'    => 22,
		'unit'       => 'pt',
		'number'     => 45,
		'format'     => 'flat',
		'separator'  => "\n",
		'orderby'    => 'name',
		'order'      => 'ASC',
		'exclude'    => '',
		'include'    => '',
		'link'       => 'view',
		'taxonomy'   => 'post_tag',
		'post_type'  => '',
		'echo'       => true,
		'show_count' => 0,
	);

	$args = wp_parse_args( $args, $defaults );

	$tags = get_terms(
		array_merge(
			$args,
			array(
				'orderby' => 'count',
				'order'   => 'DESC',
			)
		)
	); // Always query top tags.

	if ( empty( $tags ) || is_wp_error( $tags ) ) {
		return;
	}

	foreach ( $tags as $key => $tag ) {
		if ( 'edit' === $args['link'] ) {
			$link = get_edit_term_link( $tag->term_id, $tag->taxonomy, $args['post_type'] );
		} else {
			$link = get_term_link( intval( $tag->term_id ), $tag->taxonomy );
		}

		if ( is_wp_error( $link ) ) {
			return;
		}

		$tags[ $key ]->link = $link;
		$tags[ $key ]->id   = $tag->term_id;
	}

	// Here's where those top tags get sorted according to $args.
	$return = wp_generate_tag_cloud( $tags, $args );

	/**
	 * Filters the tag cloud output.
	 *
	 * @since 2.3.0
	 *
	 * @param string|array $return Tag cloud as a string or an array, depending on 'format' argument.
	 * @param array        $args   An array of tag cloud arguments.
	 */
	$return = apply_filters( 'wp_tag_cloud', $return, $args );

	if ( 'array' === $args['format'] || empty( $args['echo'] ) ) {
		return $return;
	}

	echo $return;
}

/**
 * Default topic count scaling for tag links.
 *
 * @since 2.9.0
 *
 * @param int $count Number of posts with that tag.
 * @return int Scaled count.
 */
function default_topic_count_scale( $count ) {
	return round( log10( $count + 1 ) * 100 );
}

/**
 * Generates a tag cloud (heatmap) from provided data.
 *
 * @todo Complete functionality.
 * @since 2.3.0
 * @since 4.8.0 Added the `show_count` argument.
 *
 * @param WP_Term[]    $tags Array of WP_Term objects to generate the tag cloud for.
 * @param string|array $args {
 *     Optional. Array or string of arguments for generating a tag cloud.
 *
 *     @type int      $smallest                   Smallest font size used to display tags. Paired
 *                                                with the value of `$unit`, to determine CSS text
 *                                                size unit. Default 8 (pt).
 *     @type int      $largest                    Largest font size used to display tags. Paired
 *                                                with the value of `$unit`, to determine CSS text
 *                                                size unit. Default 22 (pt).
 *     @type string   $unit                       CSS text size unit to use with the `$smallest`
 *                                                and `$largest` values. Accepts any valid CSS text
 *                                                size unit. Default 'pt'.
 *     @type int      $number                     The number of tags to return. Accepts any
 *                                                positive integer or zero to return all.
 *                                                Default 0.
 *     @type string   $format                     Format to display the tag cloud in. Accepts 'flat'
 *                                                (tags separated with spaces), 'list' (tags displayed
 *                                                in an unordered list), or 'array' (returns an array).
 *                                                Default 'flat'.
 *     @type string   $separator                  HTML or text to separate the tags. Default "\n" (newline).
 *     @type string   $orderby                    Value to order tags by. Accepts 'name' or 'count'.
 *                                                Default 'name'. The {@see 'tag_cloud_sort'} filter
 *                                                can also affect how tags are sorted.
 *     @type string   $order                      How to order the tags. Accepts 'ASC' (ascending),
 *                                                'DESC' (descending), or 'RAND' (random). Default 'ASC'.
 *     @type int|bool $filter                     Whether to enable filtering of the final output
 *                                                via {@see 'wp_generate_tag_cloud'}. Default 1|true.
 *     @type string   $topic_count_text           Nooped plural text from _n_noop() to supply to
 *                                                tag counts. Default null.
 *     @type callable $topic_count_text_callback  Callback used to generate nooped plural text for
 *                                                tag counts based on the count. Default null.
 *     @type callable $topic_count_scale_callback Callback used to determine the tag count scaling
 *                                                value. Default default_topic_count_scale().
 *     @type bool|int $show_count                 Whether to display the tag counts. Default 0. Accepts
 *                                                0, 1, or their bool equivalents.
 * }
 * @return string|array Tag cloud as a string or an array, depending on 'format' argument.
 */
function wp_generate_tag_cloud( $tags, $args = '' ) {
	$defaults = array(
		'smallest'                   => 8,
		'largest'                    => 22,
		'unit'                       => 'pt',
		'number'                     => 0,
		'format'                     => 'flat',
		'separator'                  => "\n",
		'orderby'                    => 'name',
		'order'                      => 'ASC',
		'topic_count_text'           => null,
		'topic_count_text_callback'  => null,
		'topic_count_scale_callback' => 'default_topic_count_scale',
		'filter'                     => 1,
		'show_count'                 => 0,
	);

	$args = wp_parse_args( $args, $defaults );

	$return = ( 'array' === $args['format'] ) ? array() : '';

	if ( empty( $tags ) ) {
		return $return;
	}

	// Juggle topic counts.
	if ( isset( $args['topic_count_text'] ) ) {
		// First look for nooped plural support via topic_count_text.
		$translate_nooped_plural = $args['topic_count_text'];
	} elseif ( ! empty( $args['topic_count_text_callback'] ) ) {
		// Look for the alternative callback style. Ignore the previous default.
		if ( 'default_topic_count_text' === $args['topic_count_text_callback'] ) {
			/* translators: %s: Number of items (tags). */
			$translate_nooped_plural = _n_noop( '%s item', '%s items' );
		} else {
			$translate_nooped_plural = false;
		}
	} elseif ( isset( $args['single_text'] ) && isset( $args['multiple_text'] ) ) {
		// If no callback exists, look for the old-style single_text and multiple_text arguments.
		// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralSingle,WordPress.WP.I18n.NonSingularStringLiteralPlural
		$translate_nooped_plural = _n_noop( $args['single_text'], $args['multiple_text'] );
	} else {
		// This is the default for when no callback, plural, or argument is passed in.
		/* translators: %s: Number of items (tags). */
		$translate_nooped_plural = _n_noop( '%s item', '%s items' );
	}

	/**
	 * Filters how the items in a tag cloud are sorted.
	 *
	 * @since 2.8.0
	 *
	 * @param WP_Term[] $tags Ordered array of terms.
	 * @param array     $args An array of tag cloud arguments.
	 */
	$tags_sorted = apply_filters( 'tag_cloud_sort', $tags, $args );
	if ( empty( $tags_sorted ) ) {
		return $return;
	}

	if ( $tags_sorted !== $tags ) {
		$tags = $tags_sorted;
		unset( $tags_sorted );
	} else {
		if ( 'RAND' === $args['order'] ) {
			shuffle( $tags );
		} else {
			// SQL cannot save you; this is a second (potentially different) sort on a subset of data.
			if ( 'name' === $args['orderby'] ) {
				uasort( $tags, '_wp_object_name_sort_cb' );
			} else {
				uasort( $tags, '_wp_object_count_sort_cb' );
			}

			if ( 'DESC' === $args['order'] ) {
				$tags = array_reverse( $tags, true );
			}
		}
	}

	if ( $args['number'] > 0 ) {
		$tags = array_slice( $tags, 0, $args['number'] );
	}

	$counts      = array();
	$real_counts = array(); // For the alt tag.
	foreach ( (array) $tags as $key => $tag ) {
		$real_counts[ $key ] = $tag->count;
		$counts[ $key ]      = call_user_func( $args['topic_count_scale_callback'], $tag->count );
	}

	$min_count = min( $counts );
	$spread    = max( $counts ) - $min_count;
	if ( $spread <= 0 ) {
		$spread = 1;
	}
	$font_spread = $args['largest'] - $args['smallest'];
	if ( $font_spread < 0 ) {
		$font_spread = 1;
	}
	$font_step = $font_spread / $spread;

	$aria_label = false;
	/*
	 * Determine whether to output an 'aria-label' attribute with the tag name and count.
	 * When tags have a different font size, they visually convey an important information
	 * that should be available to assistive technologies too. On the other hand, sometimes
	 * themes set up the Tag Cloud to display all tags with the same font size (setting
	 * the 'smallest' and 'largest' arguments to the same value).
	 * In order to always serve the same content to all users, the 'aria-label' gets printed out:
	 * - when tags have a different size
	 * - when the tag count is displayed (for example when users check the checkbox in the
	 *   Tag Cloud widget), regardless of the tags font size
	 */
	if ( $args['show_count'] || 0 !== $font_spread ) {
		$aria_label = true;
	}

	// Assemble the data that will be used to generate the tag cloud markup.
	$tags_data = array();
	foreach ( $tags as $key => $tag ) {
		$tag_id = isset( $tag->id ) ? $tag->id : $key;

		$count      = $counts[ $key ];
		$real_count = $real_counts[ $key ];

		if ( $translate_nooped_plural ) {
			$formatted_count = sprintf( translate_nooped_plural( $translate_nooped_plural, $real_count ), number_format_i18n( $real_count ) );
		} else {
			$formatted_count = call_user_func( $args['topic_count_text_callback'], $real_count, $tag, $args );
		}

		$tags_data[] = array(
			'id'              => $tag_id,
			'url'             => ( '#' !== $tag->link ) ? $tag->link : '#',
			'role'            => ( '#' !== $tag->link ) ? '' : ' role="button"',
			'name'            => $tag->name,
			'formatted_count' => $formatted_count,
			'slug'            => $tag->slug,
			'real_count'      => $real_count,
			'class'           => 'tag-cloud-link tag-link-' . $tag_id,
			'font_size'       => $args['smallest'] + ( $count - $min_count ) * $font_step,
			'aria_label'      => $aria_label ? sprintf( ' aria-label="%1$s (%2$s)"', esc_attr( $tag->name ), esc_attr( $formatted_count ) ) : '',
			'show_count'      => $args['show_count'] ? '<span class="tag-link-count"> (' . $real_count . ')</span>' : '',
		);
	}

	/**
	 * Filters the data used to generate the tag cloud.
	 *
	 * @since 4.3.0
	 *
	 * @param array $tags_data An array of term data for term used to generate the tag cloud.
	 */
	$tags_data = apply_filters( 'wp_generate_tag_cloud_data', $tags_data );

	$a = array();

	// Generate the output links array.
	foreach ( $tags_data as $key => $tag_data ) {
		$class = $tag_data['class'] . ' tag-link-position-' . ( $key + 1 );
		$a[]   = sprintf(
			'<a href="%1$s"%2$s class="%3$s" style="font-size: %4$s;"%5$s>%6$s%7$s</a>',
			esc_url( $tag_data['url'] ),
			$tag_data['role'],
			esc_attr( $class ),
			esc_attr( str_replace( ',', '.', $tag_data['font_size'] ) . $args['unit'] ),
			$tag_data['aria_label'],
			esc_html( $tag_data['name'] ),
			$tag_data['show_count']
		);
	}

	switch ( $args['format'] ) {
		case 'array':
			$return =& $a;
			break;
		case 'list':
			/*
			 * Force role="list", as some browsers (sic: Safari 10) don't expose to assistive
			 * technologies the default role when the list is styled with `list-style: none`.
			 * Note: this is redundant but doesn't harm.
			 */
			$return  = "<ul class='wp-tag-cloud' role='list'>\n\t<li>";
			$return .= join( "</li>\n\t<li>", $a );
			$return .= "</li>\n</ul>\n";
			break;
		default:
			$return = join( $args['separator'], $a );
			break;
	}

	if ( $args['filter'] ) {
		/**
		 * Filters the generated output of a tag cloud.
		 *
		 * The filter is only evaluated if a true value is passed
		 * to the $filter argument in wp_generate_tag_cloud().
		 *
		 * @since 2.3.0
		 *
		 * @see wp_generate_tag_cloud()
		 *
		 * @param array|string $return String containing the generated HTML tag cloud output
		 *                             or an array of tag links if the 'format' argument
		 *                             equals 'array'.
		 * @param WP_Term[]    $tags   An array of terms used in the tag cloud.
		 * @param array        $args   An array of wp_generate_tag_cloud() arguments.
		 */
		return apply_filters( 'wp_generate_tag_cloud', $return, $tags, $args );
	} else {
		return $return;
	}
}

/**
 * Serves as a callback for comparing objects based on name.
 *
 * Used with `uasort()`.
 *
 * @since 3.1.0
 * @access private
 *
 * @param object $a The first object to compare.
 * @param object $b The second object to compare.
 * @return int Negative number if `$a->name` is less than `$b->name`, zero if they are equal,
 *             or greater than zero if `$a->name` is greater than `$b->name`.
 */
function _wp_object_name_sort_cb( $a, $b ) {
	return strnatcasecmp( $a->name, $b->name );
}

/**
 * Serves as a callback for comparing objects based on count.
 *
 * Used with `uasort()`.
 *
 * @since 3.1.0
 * @access private
 *
 * @param object $a The first object to compare.
 * @param object $b The second object to compare.
 * @return bool Whether the count value for `$a` is greater than the count value for `$b`.
 */
function _wp_object_count_sort_cb( $a, $b ) {
	return ( $a->count > $b->count );
}

//
// Helper functions.
//

/**
 * Retrieves HTML list content for category list.
 *
 * @since 2.1.0
 * @since 5.3.0 Formalized the existing `...$args` parameter by adding it
 *              to the function signature.
 *
 * @uses Walker_Category to create HTML list content.
 * @see Walker::walk() for parameters and return description.
 *
 * @param mixed ...$args Elements array, maximum hierarchical depth and optional additional arguments.
 * @return string
 */
function walk_category_tree( ...$args ) {
	// The user's options are the third parameter.
	if ( empty( $args[2]['walker'] ) || ! ( $args[2]['walker'] instanceof Walker ) ) {
		$walker = new Walker_Category;
	} else {
		/**
		 * @var Walker $walker
		 */
		$walker = $args[2]['walker'];
	}
	return $walker->walk( ...$args );
}

/**
 * Retrieves HTML dropdown (select) content for category list.
 *
 * @since 2.1.0
 * @since 5.3.0 Formalized the existing `...$args` parameter by adding it
 *              to the function signature.
 *
 * @uses Walker_CategoryDropdown to create HTML dropdown content.
 * @see Walker::walk() for parameters and return description.
 *
 * @param mixed ...$args Elements array, maximum hierarchical depth and optional additional arguments.
 * @return string
 */
function walk_category_dropdown_tree( ...$args ) {
	// The user's options are the third parameter.
	if ( empty( $args[2]['walker'] ) || ! ( $args[2]['walker'] instanceof Walker ) ) {
		$walker = new Walker_CategoryDropdown;
	} else {
		/**
		 * @var Walker $walker
		 */
		$walker = $args[2]['walker'];
	}
	return $walker->walk( ...$args );
}

//
// Tags.
//

/**
 * Retrieves the link to the tag.
 *
 * @since 2.3.0
 *
 * @see get_term_link()
 *
 * @param int|object $tag Tag ID or object.
 * @return string Link on success, empty string if tag does not exist.
 */
function get_tag_link( $tag ) {
	return get_category_link( $tag );
}

/**
 * Retrieves the tags for a post.
 *
 * @since 2.3.0
 *
 * @param int $post_id Post ID.
 * @return array|false|WP_Error Array of tag objects on success, false on failure.
 */
function get_the_tags( $post_id = 0 ) {
	$terms = get_the_terms( $post_id, 'post_tag' );

	/**
	 * Filters the array of tags for the given post.
	 *
	 * @since 2.3.0
	 *
	 * @see get_the_terms()
	 *
	 * @param WP_Term[] $terms An array of tags for the given post.
	 */
	return apply_filters( 'get_the_tags', $terms );
}

/**
 * Retrieves the tags for a post formatted as a string.
 *
 * @since 2.3.0
 *
 * @param string $before  Optional. String to use before the tags. Default empty.
 * @param string $sep     Optional. String to use between the tags. Default empty.
 * @param string $after   Optional. String to use after the tags. Default empty.
 * @param int    $post_id Optional. Post ID. Defaults to the current post ID.
 * @return string|false|WP_Error A list of tags on success, false if there are no terms,
 *                               WP_Error on failure.
 */
function get_the_tag_list( $before = '', $sep = '', $after = '', $post_id = 0 ) {
	$tag_list = get_the_term_list( $post_id, 'post_tag', $before, $sep, $after );

	/**
	 * Filters the tags list for a given post.
	 *
	 * @since 2.3.0
	 *
	 * @param string $tag_list List of tags.
	 * @param string $before   String to use before the tags.
	 * @param string $sep      String to use between the tags.
	 * @param string $after    String to use after the tags.
	 * @param int    $post_id  Post ID.
	 */
	return apply_filters( 'the_tags', $tag_list, $before, $sep, $after, $post_id );
}

/**
 * Displays the tags for a post.
 *
 * @since 2.3.0
 *
 * @param string $before Optional. String to use before the tags. Defaults to 'Tags:'.
 * @param string $sep    Optional. String to use between the tags. Default ', '.
 * @param string $after  Optional. String to use after the tags. Default empty.
 */
function the_tags( $before = null, $sep = ', ', $after = '' ) {
	if ( null === $before ) {
		$before = __( 'Tags: ' );
	}

	$the_tags = get_the_tag_list( $before, $sep, $after );

	if ( ! is_wp_error( $the_tags ) ) {
		echo $the_tags;
	}
}

/**
 * Retrieves tag description.
 *
 * @since 2.8.0
 *
 * @param int $tag Optional. Tag ID. Defaults to the current tag ID.
 * @return string Tag description, if available.
 */
function tag_description( $tag = 0 ) {
	return term_description( $tag );
}

/**
 * Retrieves term description.
 *
 * @since 2.8.0
 * @since 4.9.2 The `$taxonomy` parameter was deprecated.
 *
 * @param int  $term       Optional. Term ID. Defaults to the current term ID.
 * @param null $deprecated Deprecated. Not used.
 * @return string Term description, if available.
 */
function term_description( $term = 0, $deprecated = null ) {
	if ( ! $term && ( is_tax() || is_tag() || is_category() ) ) {
		$term = get_queried_object();
		if ( $term ) {
			$term = $term->term_id;
		}
	}

	$description = get_term_field( 'description', $term );

	return is_wp_error( $description ) ? '' : $description;
}

/**
 * Retrieves the terms of the taxonomy that are attached to the post.
 *
 * @since 2.5.0
 *
 * @param int|WP_Post $post     Post ID or object.
 * @param string      $taxonomy Taxonomy name.
 * @return WP_Term[]|false|WP_Error Array of WP_Term objects on success, false if there are no terms
 *                                  or the post does not exist, WP_Error on failure.
 */
function get_the_terms( $post, $taxonomy ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}

	$terms = get_object_term_cache( $post->ID, $taxonomy );
	if ( false === $terms ) {
		$terms = wp_get_object_terms( $post->ID, $taxonomy );
		if ( ! is_wp_error( $terms ) ) {
			$term_ids = wp_list_pluck( $terms, 'term_id' );
			wp_cache_add( $post->ID, $term_ids, $taxonomy . '_relationships' );
		}
	}

	/**
	 * Filters the list of terms attached to the given post.
	 *
	 * @since 3.1.0
	 *
	 * @param WP_Term[]|WP_Error $terms    Array of attached terms, or WP_Error on failure.
	 * @param int                $post_id  Post ID.
	 * @param string             $taxonomy Name of the taxonomy.
	 */
	$terms = apply_filters( 'get_the_terms', $terms, $post->ID, $taxonomy );

	if ( empty( $terms ) ) {
		return false;
	}

	return $terms;
}

/**
 * Retrieves a post's terms as a list with specified format.
 *
 * Terms are linked to their respective term listing pages.
 *
 * @since 2.5.0
 *
 * @param int    $post_id  Post ID.
 * @param string $taxonomy Taxonomy name.
 * @param string $before   Optional. String to use before the terms. Default empty.
 * @param string $sep      Optional. String to use between the terms. Default empty.
 * @param string $after    Optional. String to use after the terms. Default empty.
 * @return string|false|WP_Error A list of terms on success, false if there are no terms,
 *                               WP_Error on failure.
 */
function get_the_term_list( $post_id, $taxonomy, $before = '', $sep = '', $after = '' ) {
	$terms = get_the_terms( $post_id, $taxonomy );

	if ( is_wp_error( $terms ) ) {
		return $terms;
	}

	if ( empty( $terms ) ) {
		return false;
	}

	$links = array();

	foreach ( $terms as $term ) {
		$link = get_term_link( $term, $taxonomy );
		if ( is_wp_error( $link ) ) {
			return $link;
		}
		$links[] = '<a href="' . esc_url( $link ) . '" rel="tag">' . $term->name . '</a>';
	}

	/**
	 * Filters the term links for a given taxonomy.
	 *
	 * The dynamic portion of the filter name, `$taxonomy`, refers
	 * to the taxonomy slug.
	 *
	 * @since 2.5.0
	 *
	 * @param string[] $links An array of term links.
	 */
	$term_links = apply_filters( "term_links-{$taxonomy}", $links );  // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

	return $before . join( $sep, $term_links ) . $after;
}

/**
 * Retrieves term parents with separator.
 *
 * @since 4.8.0
 *
 * @param int          $term_id  Term ID.
 * @param string       $taxonomy Taxonomy name.
 * @param string|array $args {
 *     Array of optional arguments.
 *
 *     @type string $format    Use term names or slugs for display. Accepts 'name' or 'slug'.
 *                             Default 'name'.
 *     @type string $separator Separator for between the terms. Default '/'.
 *     @type bool   $link      Whether to format as a link. Default true.
 *     @type bool   $inclusive Include the term to get the parents for. Default true.
 * }
 * @return string|WP_Error A list of term parents on success, WP_Error or empty string on failure.
 */
function get_term_parents_list( $term_id, $taxonomy, $args = array() ) {
	$list = '';
	$term = get_term( $term_id, $taxonomy );

	if ( is_wp_error( $term ) ) {
		return $term;
	}

	if ( ! $term ) {
		return $list;
	}

	$term_id = $term->term_id;

	$defaults = array(
		'format'    => 'name',
		'separator' => '/',
		'link'      => true,
		'inclusive' => true,
	);

	$args = wp_parse_args( $args, $defaults );

	foreach ( array( 'link', 'inclusive' ) as $bool ) {
		$args[ $bool ] = wp_validate_boolean( $args[ $bool ] );
	}

	$parents = get_ancestors( $term_id, $taxonomy, 'taxonomy' );

	if ( $args['inclusive'] ) {
		array_unshift( $parents, $term_id );
	}

	foreach ( array_reverse( $parents ) as $term_id ) {
		$parent = get_term( $term_id, $taxonomy );
		$name   = ( 'slug' === $args['format'] ) ? $parent->slug : $parent->name;

		if ( $args['link'] ) {
			$list .= '<a href="' . esc_url( get_term_link( $parent->term_id, $taxonomy ) ) . '">' . $name . '</a>' . $args['separator'];
		} else {
			$list .= $name . $args['separator'];
		}
	}

	return $list;
}

/**
 * Displays the terms for a post in a list.
 *
 * @since 2.5.0
 *
 * @param int    $post_id  Post ID.
 * @param string $taxonomy Taxonomy name.
 * @param string $before   Optional. String to use before the terms. Default empty.
 * @param string $sep      Optional. String to use between the terms. Default ', '.
 * @param string $after    Optional. String to use after the terms. Default empty.
 * @return void|false Void on success, false on failure.
 */
function the_terms( $post_id, $taxonomy, $before = '', $sep = ', ', $after = '' ) {
	$term_list = get_the_term_list( $post_id, $taxonomy, $before, $sep, $after );

	if ( is_wp_error( $term_list ) ) {
		return false;
	}

	/**
	 * Filters the list of terms to display.
	 *
	 * @since 2.9.0
	 *
	 * @param string $term_list List of terms to display.
	 * @param string $taxonomy  The taxonomy name.
	 * @param string $before    String to use before the terms.
	 * @param string $sep       String to use between the terms.
	 * @param string $after     String to use after the terms.
	 */
	echo apply_filters( 'the_terms', $term_list, $taxonomy, $before, $sep, $after );
}

/**
 * Checks if the current post has any of given category.
 *
 * The given categories are checked against the post's categories' term_ids, names and slugs.
 * Categories given as integers will only be checked against the post's categories' term_ids.
 *
 * If no categories are given, determines if post has any categories.
 *
 * @since 3.1.0
 *
 * @param string|int|array $category Optional. The category name/term_id/slug,
 *                                   or an array of them to check for. Default empty.
 * @param int|object       $post     Optional. Post to check instead of the current post.
 * @return bool True if the current post has any of the given categories
 *              (or any category, if no category specified). False otherwise.
 */
function has_category( $category = '', $post = null ) {
	return has_term( $category, 'category', $post );
}

/**
 * Checks if the current post has any of given tags.
 *
 * The given tags are checked against the post's tags' term_ids, names and slugs.
 * Tags given as integers will only be checked against the post's tags' term_ids.
 *
 * If no tags are given, determines if post has any tags.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 2.6.0
 * @since 2.7.0 Tags given as integers are only checked against
 *              the post's tags' term_ids, not names or slugs.
 * @since 2.7.0 Can be used outside of the WordPress Loop if `$post` is provided.
 *
 * @param string|int|array $tag  Optional. The tag name/term_id/slug,
 *                               or an array of them to check for. Default empty.
 * @param int|object       $post Optional. Post to check instead of the current post.
 * @return bool True if the current post has any of the given tags
 *              (or any tag, if no tag specified). False otherwise.
 */
function has_tag( $tag = '', $post = null ) {
	return has_term( $tag, 'post_tag', $post );
}

/**
 * Checks if the current post has any of given terms.
 *
 * The given terms are checked against the post's terms' term_ids, names and slugs.
 * Terms given as integers will only be checked against the post's terms' term_ids.
 *
 * If no terms are given, determines if post has any terms.
 *
 * @since 3.1.0
 *
 * @param string|int|array $term     Optional. The term name/term_id/slug,
 *                                   or an array of them to check for. Default empty.
 * @param string           $taxonomy Optional. Taxonomy name. Default empty.
 * @param int|WP_Post      $post     Optional. Post to check instead of the current post.
 * @return bool True if the current post has any of the given terms
 *              (or any term, if no term specified). False otherwise.
 */
function has_term( $term = '', $taxonomy = '', $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$r = is_object_in_term( $post->ID, $taxonomy, $term );
	if ( is_wp_error( $r ) ) {
		return false;
	}

	return $r;
}

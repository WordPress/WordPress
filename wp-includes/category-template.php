<?php
/**
 * Category Template Tags and API.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Retrieve category link URL.
 *
 * @since 1.0.0
 * @uses apply_filters() Calls 'category_link' filter on category link and category ID.
 *
 * @param int $category_id Category ID.
 * @return string
 */
function get_category_link( $category_id ) {
	global $wp_rewrite;
	$catlink = $wp_rewrite->get_category_permastruct();

	if ( empty( $catlink ) ) {
		$catlink = home_url('?cat=' . $category_id);
	} else {
		$category = &get_category( $category_id );
		if ( is_wp_error( $category ) )
			return $category;
		$category_nicename = $category->slug;

		if ( $category->parent == $category_id ) // recursive recursion
			$category->parent = 0;
		elseif ($category->parent != 0 )
			$category_nicename = get_category_parents( $category->parent, false, '/', true ) . $category_nicename;

		$catlink = str_replace( '%category%', $category_nicename, $catlink );
		$catlink = home_url( user_trailingslashit( $catlink, 'category' ) );
	}
	return apply_filters( 'category_link', $catlink, $category_id );
}

/**
 * Retrieve category parents with separator.
 *
 * @since 1.2.0
 *
 * @param int $id Category ID.
 * @param bool $link Optional, default is false. Whether to format with link.
 * @param string $separator Optional, default is '/'. How to separate categories.
 * @param bool $nicename Optional, default is false. Whether to use nice name for display.
 * @param array $visited Optional. Already linked to categories to prevent duplicates.
 * @return string
 */
function get_category_parents( $id, $link = false, $separator = '/', $nicename = false, $visited = array() ) {
	$chain = '';
	$parent = &get_category( $id );
	if ( is_wp_error( $parent ) )
		return $parent;

	if ( $nicename )
		$name = $parent->slug;
	else
		$name = $parent->cat_name;

	if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
		$visited[] = $parent->parent;
		$chain .= get_category_parents( $parent->parent, $link, $separator, $nicename, $visited );
	}

	if ( $link )
		$chain .= '<a href="' . get_category_link( $parent->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $parent->cat_name ) ) . '">'.$name.'</a>' . $separator;
	else
		$chain .= $name.$separator;
	return $chain;
}

/**
 * Retrieve post categories.
 *
 * @since 0.71
 * @uses $post
 *
 * @param int $id Optional, default to current post ID. The post ID.
 * @return array
 */
function get_the_category( $id = false ) {
	global $post;

	$id = (int) $id;
	if ( !$id )
		$id = (int) $post->ID;

	$categories = get_object_term_cache( $id, 'category' );
	if ( false === $categories ) {
		$categories = wp_get_object_terms( $id, 'category' );
		wp_cache_add($id, $categories, 'category_relationships');
	}

	if ( !empty( $categories ) )
		usort( $categories, '_usort_terms_by_name' );
	else
		$categories = array();

	foreach ( (array) array_keys( $categories ) as $key ) {
		_make_cat_compat( $categories[$key] );
	}

	return $categories;
}

/**
 * Sort categories by name.
 *
 * Used by usort() as a callback, should not be used directly. Can actually be
 * used to sort any term object.
 *
 * @since 2.3.0
 * @access private
 *
 * @param object $a
 * @param object $b
 * @return int
 */
function _usort_terms_by_name( $a, $b ) {
	return strcmp( $a->name, $b->name );
}

/**
 * Sort categories by ID.
 *
 * Used by usort() as a callback, should not be used directly. Can actually be
 * used to sort any term object.
 *
 * @since 2.3.0
 * @access private
 *
 * @param object $a
 * @param object $b
 * @return int
 */
function _usort_terms_by_ID( $a, $b ) {
	if ( $a->term_id > $b->term_id )
		return 1;
	elseif ( $a->term_id < $b->term_id )
		return -1;
	else
		return 0;
}

/**
 * Retrieve category name based on category ID.
 *
 * @since 0.71
 *
 * @param int $cat_ID Category ID.
 * @return string Category name.
 */
function get_the_category_by_ID( $cat_ID ) {
	$cat_ID = (int) $cat_ID;
	$category = &get_category( $cat_ID );
	if ( is_wp_error( $category ) )
		return $category;
	return $category->name;
}

/**
 * Retrieve category list in either HTML list or custom format.
 *
 * @since 1.5.1
 *
 * @param string $separator Optional, default is empty string. Separator for between the categories.
 * @param string $parents Optional. How to display the parents.
 * @param int $post_id Optional. Post ID to retrieve categories.
 * @return string
 */
function get_the_category_list( $separator = '', $parents='', $post_id = false ) {
	global $wp_rewrite;
	$categories = get_the_category( $post_id );
	if ( empty( $categories ) )
		return apply_filters( 'the_category', __( 'Uncategorized' ), $separator, $parents );

	$rel = ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';

	$thelist = '';
	if ( '' == $separator ) {
		$thelist .= '<ul class="post-categories">';
		foreach ( $categories as $category ) {
			$thelist .= "\n\t<li>";
			switch ( strtolower( $parents ) ) {
				case 'multiple':
					if ( $category->parent )
						$thelist .= get_category_parents( $category->parent, true, $separator );
					$thelist .= '<a href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ' . $rel . '>' . $category->name.'</a></li>';
					break;
				case 'single':
					$thelist .= '<a href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ' . $rel . '>';
					if ( $category->parent )
						$thelist .= get_category_parents( $category->parent, false, $separator );
					$thelist .= $category->name.'</a></li>';
					break;
				case '':
				default:
					$thelist .= '<a href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ' . $rel . '>' . $category->cat_name.'</a></li>';
			}
		}
		$thelist .= '</ul>';
	} else {
		$i = 0;
		foreach ( $categories as $category ) {
			if ( 0 < $i )
				$thelist .= $separator . ' ';
			switch ( strtolower( $parents ) ) {
				case 'multiple':
					if ( $category->parent )
						$thelist .= get_category_parents( $category->parent, true, $separator );
					$thelist .= '<a href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ' . $rel . '>' . $category->cat_name.'</a>';
					break;
				case 'single':
					$thelist .= '<a href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ' . $rel . '>';
					if ( $category->parent )
						$thelist .= get_category_parents( $category->parent, false, $separator );
					$thelist .= "$category->cat_name</a>";
					break;
				case '':
				default:
					$thelist .= '<a href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ' . $rel . '>' . $category->name.'</a>';
			}
			++$i;
		}
	}
	return apply_filters( 'the_category', $thelist, $separator, $parents );
}


/**
 * Check if the current post in within any of the given categories.
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
 * @since 1.2.0
 *
 * @uses is_object_in_term()
 *
 * @param int|string|array $category. Category ID, name or slug, or array of said.
 * @param int|post object Optional.  Post to check instead of the current post. @since 2.7.0
 * @return bool True if the current post is in any of the given categories.
 */
function in_category( $category, $_post = null ) {
	if ( empty( $category ) )
		return false;

	if ( $_post ) {
		$_post = get_post( $_post );
	} else {
		$_post =& $GLOBALS['post'];
	}

	if ( !$_post )
		return false;

	$r = is_object_in_term( $_post->ID, 'category', $category );
	if ( is_wp_error( $r ) )
		return false;
	return $r;
}

/**
 * Display the category list for the post.
 *
 * @since 0.71
 *
 * @param string $separator Optional, default is empty string. Separator for between the categories.
 * @param string $parents Optional. How to display the parents.
 * @param int $post_id Optional. Post ID to retrieve categories.
 */
function the_category( $separator = '', $parents='', $post_id = false ) {
	echo get_the_category_list( $separator, $parents, $post_id );
}

/**
 * Retrieve category description.
 *
 * @since 1.0.0
 *
 * @param int $category Optional. Category ID. Will use global category ID by default.
 * @return string Category description, available.
 */
function category_description( $category = 0 ) {
	return term_description( $category, 'category' );
}

/**
 * Display or retrieve the HTML dropdown list of categories.
 *
 * The list of arguments is below:
 *     'show_option_all' (string) - Text to display for showing all categories.
 *     'show_option_none' (string) - Text to display for showing no categories.
 *     'orderby' (string) default is 'ID' - What column to use for ordering the
 * categories.
 *     'order' (string) default is 'ASC' - What direction to order categories.
 *     'show_last_update' (bool|int) default is 0 - See {@link get_categories()}
 *     'show_count' (bool|int) default is 0 - Whether to show how many posts are
 * in the category.
 *     'hide_empty' (bool|int) default is 1 - Whether to hide categories that
 * don't have any posts attached to them.
 *     'child_of' (int) default is 0 - See {@link get_categories()}.
 *     'exclude' (string) - See {@link get_categories()}.
 *     'echo' (bool|int) default is 1 - Whether to display or retrieve content.
 *     'depth' (int) - The max depth.
 *     'tab_index' (int) - Tab index for select element.
 *     'name' (string) - The name attribute value for selected element.
 *     'class' (string) - The class attribute value for selected element.
 *     'selected' (int) - Which category ID is selected.
 *
 * The 'hierarchical' argument, which is disabled by default, will override the
 * depth argument, unless it is true. When the argument is false, it will
 * display all of the categories. When it is enabled it will use the value in
 * the 'depth' argument.
 *
 * @since 2.1.0
 *
 * @param string|array $args Optional. Override default arguments.
 * @return string HTML content only if 'echo' argument is 0.
 */
function wp_dropdown_categories( $args = '' ) {
	$defaults = array(
		'show_option_all' => '', 'show_option_none' => '',
		'orderby' => 'id', 'order' => 'ASC',
		'show_last_update' => 0, 'show_count' => 0,
		'hide_empty' => 1, 'child_of' => 0,
		'exclude' => '', 'echo' => 1,
		'selected' => 0, 'hierarchical' => 0,
		'name' => 'cat', 'class' => 'postform',
		'depth' => 0, 'tab_index' => 0,
		'taxonomy' => 'category', 'hide_if_empty' => false
	);

	$defaults['selected'] = ( is_category() ) ? get_query_var( 'cat' ) : 0;

	$r = wp_parse_args( $args, $defaults );

	if ( !isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] ) {
		$r['pad_counts'] = true;
	}

	$r['include_last_update_time'] = $r['show_last_update'];
	extract( $r );

	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 )
		$tab_index_attribute = " tabindex=\"$tab_index\"";

	$categories = get_terms( $taxonomy, $r );
	$name = esc_attr($name);
	$class = esc_attr($class);

	if ( ! $r['hide_if_empty'] || ! empty($categories) )
		$output = "<select name='$name' id='$name' class='$class' $tab_index_attribute>\n";
	else
		$output = '';

	if ( empty($categories) && ! $r['hide_if_empty'] && !empty($show_option_none) ) {
		$show_option_none = apply_filters( 'list_cats', $show_option_none );
		$output .= "\t<option value='-1' selected='selected'>$show_option_none</option>\n";
	}

	if ( ! empty( $categories ) ) {

		if ( $show_option_all ) {
			$show_option_all = apply_filters( 'list_cats', $show_option_all );
			$selected = ( '0' === strval($r['selected']) ) ? " selected='selected'" : '';
			$output .= "\t<option value='0'$selected>$show_option_all</option>\n";
		}

		if ( $show_option_none ) {
			$show_option_none = apply_filters( 'list_cats', $show_option_none );
			$selected = ( '-1' === strval($r['selected']) ) ? " selected='selected'" : '';
			$output .= "\t<option value='-1'$selected>$show_option_none</option>\n";
		}

		if ( $hierarchical )
			$depth = $r['depth'];  // Walk the full depth.
		else
			$depth = -1; // Flat.

		$output .= walk_category_dropdown_tree( $categories, $depth, $r );
	}
	if ( ! $r['hide_if_empty'] || ! empty($categories) )
		$output .= "</select>\n";


	$output = apply_filters( 'wp_dropdown_cats', $output );

	if ( $echo )
		echo $output;

	return $output;
}

/**
 * Display or retrieve the HTML list of categories.
 *
 * The list of arguments is below:
 *     'show_option_all' (string) - Text to display for showing all categories.
 *     'orderby' (string) default is 'ID' - What column to use for ordering the
 * categories.
 *     'order' (string) default is 'ASC' - What direction to order categories.
 *     'show_last_update' (bool|int) default is 0 - See {@link
 * walk_category_dropdown_tree()}
 *     'show_count' (bool|int) default is 0 - Whether to show how many posts are
 * in the category.
 *     'hide_empty' (bool|int) default is 1 - Whether to hide categories that
 * don't have any posts attached to them.
 *     'use_desc_for_title' (bool|int) default is 1 - Whether to use the
 * description instead of the category title.
 *     'feed' - See {@link get_categories()}.
 *     'feed_type' - See {@link get_categories()}.
 *     'feed_image' - See {@link get_categories()}.
 *     'child_of' (int) default is 0 - See {@link get_categories()}.
 *     'exclude' (string) - See {@link get_categories()}.
 *     'exclude_tree' (string) - See {@link get_categories()}.
 *     'echo' (bool|int) default is 1 - Whether to display or retrieve content.
 *     'current_category' (int) - See {@link get_categories()}.
 *     'hierarchical' (bool) - See {@link get_categories()}.
 *     'title_li' (string) - See {@link get_categories()}.
 *     'depth' (int) - The max depth.
 *
 * @since 2.1.0
 *
 * @param string|array $args Optional. Override default arguments.
 * @return string HTML content only if 'echo' argument is 0.
 */
function wp_list_categories( $args = '' ) {
	$defaults = array(
		'show_option_all' => '', 'orderby' => 'name',
		'order' => 'ASC', 'show_last_update' => 0,
		'style' => 'list', 'show_count' => 0,
		'hide_empty' => 1, 'use_desc_for_title' => 1,
		'child_of' => 0, 'feed' => '', 'feed_type' => '',
		'feed_image' => '', 'exclude' => '', 'exclude_tree' => '', 'current_category' => 0,
		'hierarchical' => true, 'title_li' => __( 'Categories' ),
		'echo' => 1, 'depth' => 0
	);

	$r = wp_parse_args( $args, $defaults );

	if ( !isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] ) {
		$r['pad_counts'] = true;
	}

	if ( isset( $r['show_date'] ) ) {
		$r['include_last_update_time'] = $r['show_date'];
	}

	if ( true == $r['hierarchical'] ) {
		$r['exclude_tree'] = $r['exclude'];
		$r['exclude'] = '';
	}

	extract( $r );

	$categories = get_categories( $r );

	$output = '';
	if ( $title_li && 'list' == $style )
			$output = '<li class="categories">' . $r['title_li'] . '<ul>';

	if ( empty( $categories ) ) {
		if ( 'list' == $style )
			$output .= '<li>' . __( "No categories" ) . '</li>';
		else
			$output .= __( "No categories" );
	} else {
		global $wp_query;

		if( !empty( $show_option_all ) )
			if ( 'list' == $style )
				$output .= '<li><a href="' .  get_bloginfo( 'url' )  . '">' . $show_option_all . '</a></li>';
			else
				$output .= '<a href="' .  get_bloginfo( 'url' )  . '">' . $show_option_all . '</a>';

		if ( empty( $r['current_category'] ) && is_category() )
			$r['current_category'] = $wp_query->get_queried_object_id();

		if ( $hierarchical )
			$depth = $r['depth'];
		else
			$depth = -1; // Flat.

		$output .= walk_category_tree( $categories, $depth, $r );
	}

	if ( $title_li && 'list' == $style )
		$output .= '</ul></li>';

	$output = apply_filters( 'wp_list_categories', $output );

	if ( $echo )
		echo $output;
	else
		return $output;
}

/**
 * Display tag cloud.
 *
 * The text size is set by the 'smallest' and 'largest' arguments, which will
 * use the 'unit' argument value for the CSS text size unit. The 'format'
 * argument can be 'flat' (default), 'list', or 'array'. The flat value for the
 * 'format' argument will separate tags with spaces. The list value for the
 * 'format' argument will format the tags in a UL HTML list. The array value for
 * the 'format' argument will return in PHP array type format.
 *
 * The 'orderby' argument will accept 'name' or 'count' and defaults to 'name'.
 * The 'order' is the direction to sort, defaults to 'ASC' and can be 'DESC'.
 *
 * The 'number' argument is how many tags to return. By default, the limit will
 * be to return the top 45 tags in the tag cloud list.
 *
 * The 'topic_count_text_callback' argument is a function, which, given the count
 * of the posts  with that tag, returns a text for the tooltip of the tag link.
 *
 * The 'exclude' and 'include' arguments are used for the {@link get_tags()}
 * function. Only one should be used, because only one will be used and the
 * other ignored, if they are both set.
 *
 * @since 2.3.0
 *
 * @param array|string $args Optional. Override default arguments.
 * @return array Generated tag cloud, only if no failures and 'array' is set for the 'format' argument.
 */
function wp_tag_cloud( $args = '' ) {
	$defaults = array(
		'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
		'format' => 'flat', 'separator' => "\n", 'orderby' => 'name', 'order' => 'ASC',
		'exclude' => '', 'include' => '', 'link' => 'view', 'taxonomy' => 'post_tag', 'echo' => true
	);
	$args = wp_parse_args( $args, $defaults );

	$tags = get_terms( $args['taxonomy'], array_merge( $args, array( 'orderby' => 'count', 'order' => 'DESC' ) ) ); // Always query top tags

	if ( empty( $tags ) )
		return;

	foreach ( $tags as $key => $tag ) {
		if ( 'edit' == $args['link'] )
			$link = get_edit_tag_link( $tag->term_id, $args['taxonomy'] );
		else
			$link = get_term_link( intval($tag->term_id), $args['taxonomy'] );
		if ( is_wp_error( $link ) )
			return false;

		$tags[ $key ]->link = $link;
		$tags[ $key ]->id = $tag->term_id;
	}

	$return = wp_generate_tag_cloud( $tags, $args ); // Here's where those top tags get sorted according to $args

	$return = apply_filters( 'wp_tag_cloud', $return, $args );

	if ( 'array' == $args['format'] || empty($args['echo']) )
		return $return;

	echo $return;
}

/**
 * Default text for tooltip for tag links
 *
 * @param integer $count number of posts with that tag
 * @return string text for the tooltip of a tag link.
 */
function default_topic_count_text( $count ) {
	return sprintf( _n('%s topic', '%s topics', $count), number_format_i18n( $count ) );
}

/**
 * Default topic count scaling for tag links
 *
 * @param integer $count number of posts with that tag
 * @return integer scaled count
 */
function default_topic_count_scale( $count ) {
	return round(log10($count + 1) * 100);
}


/**
 * Generates a tag cloud (heatmap) from provided data.
 *
 * The text size is set by the 'smallest' and 'largest' arguments, which will
 * use the 'unit' argument value for the CSS text size unit. The 'format'
 * argument can be 'flat' (default), 'list', or 'array'. The flat value for the
 * 'format' argument will separate tags with spaces. The list value for the
 * 'format' argument will format the tags in a UL HTML list. The array value for
 * the 'format' argument will return in PHP array type format.
 *
 * The 'tag_cloud_sort' filter allows you to override the sorting.
 * Passed to the filter: $tags array and $args array, has to return the $tags array
 * after sorting it.
 *
 * The 'orderby' argument will accept 'name' or 'count' and defaults to 'name'.
 * The 'order' is the direction to sort, defaults to 'ASC' and can be 'DESC' or
 * 'RAND'.
 *
 * The 'number' argument is how many tags to return. By default, the limit will
 * be to return the entire tag cloud list.
 *
 * The 'topic_count_text_callback' argument is a function, which given the count
 * of the posts  with that tag returns a text for the tooltip of the tag link.
 *
 * @todo Complete functionality.
 * @since 2.3.0
 *
 * @param array $tags List of tags.
 * @param string|array $args Optional, override default arguments.
 * @return string
 */
function wp_generate_tag_cloud( $tags, $args = '' ) {
	global $wp_rewrite;
	$defaults = array(
		'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 0,
		'format' => 'flat', 'separator' => "\n", 'orderby' => 'name', 'order' => 'ASC',
		'topic_count_text_callback' => 'default_topic_count_text',
		'topic_count_scale_callback' => 'default_topic_count_scale', 'filter' => 1,
	);

	if ( !isset( $args['topic_count_text_callback'] ) && isset( $args['single_text'] ) && isset( $args['multiple_text'] ) ) {
		$body = 'return sprintf (
			_n(' . var_export($args['single_text'], true) . ', ' . var_export($args['multiple_text'], true) . ', $count),
			number_format_i18n( $count ));';
		$args['topic_count_text_callback'] = create_function('$count', $body);
	}

	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	if ( empty( $tags ) )
		return;

	$tags_sorted = apply_filters( 'tag_cloud_sort', $tags, $args );
	if ( $tags_sorted != $tags  ) { // the tags have been sorted by a plugin
		$tags = $tags_sorted;
		unset($tags_sorted);
	} else {
		if ( 'RAND' == $order ) {
			shuffle($tags);
		} else {
			// SQL cannot save you; this is a second (potentially different) sort on a subset of data.
			if ( 'name' == $orderby )
				uasort( $tags, create_function('$a, $b', 'return strnatcasecmp($a->name, $b->name);') );
			else
				uasort( $tags, create_function('$a, $b', 'return ($a->count > $b->count);') );

			if ( 'DESC' == $order )
				$tags = array_reverse( $tags, true );
		}
	}

	if ( $number > 0 )
		$tags = array_slice($tags, 0, $number);

	$counts = array();
	$real_counts = array(); // For the alt tag
	foreach ( (array) $tags as $key => $tag ) {
		$real_counts[ $key ] = $tag->count;
		$counts[ $key ] = $topic_count_scale_callback($tag->count);
	}

	$min_count = min( $counts );
	$spread = max( $counts ) - $min_count;
	if ( $spread <= 0 )
		$spread = 1;
	$font_spread = $largest - $smallest;
	if ( $font_spread < 0 )
		$font_spread = 1;
	$font_step = $font_spread / $spread;

	$a = array();

	foreach ( $tags as $key => $tag ) {
		$count = $counts[ $key ];
		$real_count = $real_counts[ $key ];
		$tag_link = '#' != $tag->link ? esc_url( $tag->link ) : '#';
		$tag_id = isset($tags[ $key ]->id) ? $tags[ $key ]->id : $key;
		$tag_name = $tags[ $key ]->name;
		$a[] = "<a href='$tag_link' class='tag-link-$tag_id' title='" . esc_attr( $topic_count_text_callback( $real_count ) ) . "' style='font-size: " .
			( $smallest + ( ( $count - $min_count ) * $font_step ) )
			. "$unit;'>$tag_name</a>";
	}

	switch ( $format ) :
	case 'array' :
		$return =& $a;
		break;
	case 'list' :
		$return = "<ul class='wp-tag-cloud'>\n\t<li>";
		$return .= join( "</li>\n\t<li>", $a );
		$return .= "</li>\n</ul>\n";
		break;
	default :
		$return = join( $separator, $a );
		break;
	endswitch;

    if ( $filter )
		return apply_filters( 'wp_generate_tag_cloud', $return, $tags, $args );
    else
		return $return;
}

//
// Helper functions
//

/**
 * Retrieve HTML list content for category list.
 *
 * @uses Walker_Category to create HTML list content.
 * @since 2.1.0
 * @see Walker_Category::walk() for parameters and return description.
 */
function walk_category_tree() {
	$args = func_get_args();
	// the user's options are the third parameter
	if ( empty($args[2]['walker']) || !is_a($args[2]['walker'], 'Walker') )
		$walker = new Walker_Category;
	else
		$walker = $args[2]['walker'];

	return call_user_func_array(array( &$walker, 'walk' ), $args );
}

/**
 * Retrieve HTML dropdown (select) content for category list.
 *
 * @uses Walker_CategoryDropdown to create HTML dropdown content.
 * @since 2.1.0
 * @see Walker_CategoryDropdown::walk() for parameters and return description.
 */
function walk_category_dropdown_tree() {
	$args = func_get_args();
	// the user's options are the third parameter
	if ( empty($args[2]['walker']) || !is_a($args[2]['walker'], 'Walker') )
		$walker = new Walker_CategoryDropdown;
	else
		$walker = $args[2]['walker'];

	return call_user_func_array(array( &$walker, 'walk' ), $args );
}

//
// Tags
//

/**
 * Retrieve the link to the tag.
 *
 * @since 2.3.0
 * @uses apply_filters() Calls 'tag_link' with tag link and tag ID as parameters.
 *
 * @param int $tag_id Tag (term) ID.
 * @return string
 */
function get_tag_link( $tag_id ) {
	global $wp_rewrite;
	$taglink = $wp_rewrite->get_tag_permastruct();

	$tag = &get_term( $tag_id, 'post_tag' );
	if ( is_wp_error( $tag ) )
		return $tag;
	$slug = $tag->slug;

	if ( empty( $taglink ) ) {
		$file = get_option( 'home' ) . '/';
		$taglink = $file . '?tag=' . $slug;
	} else {
		$taglink = str_replace( '%tag%', $slug, $taglink );
		$taglink = get_option( 'home' ) . user_trailingslashit( $taglink, 'category' );
	}
	return apply_filters( 'tag_link', $taglink, $tag_id );
}

/**
 * Retrieve the tags for a post.
 *
 * @since 2.3.0
 * @uses apply_filters() Calls 'get_the_tags' filter on the list of post tags.
 *
 * @param int $id Post ID.
 * @return array
 */
function get_the_tags( $id = 0 ) {
	return apply_filters( 'get_the_tags', get_the_terms( $id, 'post_tag' ) );
}

/**
 * Retrieve the tags for a post formatted as a string.
 *
 * @since 2.3.0
 * @uses apply_filters() Calls 'the_tags' filter on string list of tags.
 *
 * @param string $before Optional. Before tags.
 * @param string $sep Optional. Between tags.
 * @param string $after Optional. After tags.
 * @return string
 */
function get_the_tag_list( $before = '', $sep = '', $after = '' ) {
	return apply_filters( 'the_tags', get_the_term_list( 0, 'post_tag', $before, $sep, $after ), $before, $sep, $after);
}

/**
 * Retrieve the tags for a post.
 *
 * @since 2.3.0
 *
 * @param string $before Optional. Before list.
 * @param string $sep Optional. Separate items using this.
 * @param string $after Optional. After list.
 * @return string
 */
function the_tags( $before = null, $sep = ', ', $after = '' ) {
	if ( null === $before )
		$before = __('Tags: ');
	echo get_the_tag_list($before, $sep, $after);
}

/**
 * Retrieve tag description.
 *
 * @since 2.8
 *
 * @param int $tag Optional. Tag ID. Will use global tag ID by default.
 * @return string Tag description, available.
 */
function tag_description( $tag = 0 ) {
	return term_description( $tag );
}

/**
 * Retrieve term description.
 *
 * @since 2.8
 *
 * @param int $term Optional. Term ID. Will use global term ID by default.
 * @return string Term description, available.
 */
function term_description( $term = 0, $taxonomy = 'post_tag' ) {
	if ( !$term && ( is_tax() || is_tag() || is_category() ) ) {
		global $wp_query;
		$term = $wp_query->get_queried_object();
		$taxonomy = $term->taxonomy;
		$term = $term->term_id;
	}
	return get_term_field( 'description', $term, $taxonomy );
}

/**
 * Retrieve the terms of the taxonomy that are attached to the post.
 *
 * This function can only be used within the loop.
 *
 * @since 2.5.0
 *
 * @param int $id Post ID. Is not optional.
 * @param string $taxonomy Taxonomy name.
 * @return array|bool False on failure. Array of term objects on success.
 */
function get_the_terms( $id = 0, $taxonomy ) {
	global $post;

 	$id = (int) $id;

	if ( !$id ) {
		if ( !$post->ID )
			return false;
		else
			$id = (int) $post->ID;
	}

	$terms = get_object_term_cache( $id, $taxonomy );
	if ( false === $terms )
		$terms = wp_get_object_terms( $id, $taxonomy );

	if ( empty( $terms ) )
		return false;

	return $terms;
}

/**
 * Retrieve a post's terms as a list with specified format.
 *
 * @since 2.5.0
 *
 * @param int $id Post ID.
 * @param string $taxonomy Taxonomy name.
 * @param string $before Optional. Before list.
 * @param string $sep Optional. Separate items using this.
 * @param string $after Optional. After list.
 * @return string
 */
function get_the_term_list( $id = 0, $taxonomy, $before = '', $sep = '', $after = '' ) {
	$terms = get_the_terms( $id, $taxonomy );

	if ( is_wp_error( $terms ) )
		return $terms;

	if ( empty( $terms ) )
		return false;

	foreach ( $terms as $term ) {
		$link = get_term_link( $term, $taxonomy );
		if ( is_wp_error( $link ) )
			return $link;
		$term_links[] = '<a href="' . $link . '" rel="tag">' . $term->name . '</a>';
	}

	$term_links = apply_filters( "term_links-$taxonomy", $term_links );

	return $before . join( $sep, $term_links ) . $after;
}

/**
 * Display the terms in a list.
 *
 * @since 2.5.0
 *
 * @param int $id Term ID.
 * @param string $taxonomy Taxonomy name.
 * @param string $before Optional. Before list.
 * @param string $sep Optional. Separate items using this.
 * @param string $after Optional. After list.
 * @return null|bool False on WordPress error. Returns null when displaying.
 */
function the_terms( $id, $taxonomy, $before = '', $sep = ', ', $after = '' ) {
	$term_list = get_the_term_list( $id, $taxonomy, $before, $sep, $after );

	if ( is_wp_error( $term_list ) )
		return false;

	echo apply_filters('the_terms', $term_list, $taxonomy, $before, $sep, $after);
}

/**
 * Check if the current post has any of given tags.
 *
 * The given tags are checked against the post's tags' term_ids, names and slugs.
 * Tags given as integers will only be checked against the post's tags' term_ids.
 * If no tags are given, determines if post has any tags.
 *
 * Prior to v2.7 of WordPress, tags given as integers would also be checked against the post's tags' names and slugs (in addition to term_ids)
 * Prior to v2.7, this function could only be used in the WordPress Loop.
 * As of 2.7, the function can be used anywhere if it is provided a post ID or post object.
 *
 * @since 2.6.0
 *
 * @uses is_object_in_term()
 *
 * @param string|int|array $tag Optional. The tag name/term_id/slug or array of them to check for.
 * @param int|post object Optional.  Post to check instead of the current post. @since 2.7.0
 * @return bool True if the current post has any of the the given tags (or any tag, if no tag specified).
 */
function has_tag( $tag = '', $_post = null ) {
	if ( $_post ) {
		$_post = get_post( $_post );
	} else {
		$_post =& $GLOBALS['post'];
	}

	if ( !$_post )
		return false;

	$r = is_object_in_term( $_post->ID, 'post_tag', $tag );
	if ( is_wp_error( $r ) )
		return false;
	return $r;
}

?>

<?php

function get_category_children($id, $before = '/', $after = '') {
	if ( 0 == $id )
		return '';

	$chain = '';
	// TODO: consult hierarchy
	$cat_ids = get_all_category_ids();
	foreach ( $cat_ids as $cat_id ) {
		if ( $cat_id == $id )
			continue;

		$category = get_category($cat_id);
		if ( is_wp_error( $category ) )
			return $category;
		if ( $category->parent == $id ) {
			$chain .= $before.$category->term_id.$after;
			$chain .= get_category_children($category->term_id, $before, $after);
		}
	}
	return $chain;
}

function get_category_link($category_id) {
	global $wp_rewrite;
	$catlink = $wp_rewrite->get_category_permastruct();

	if ( empty($catlink) ) {
		$file = get_option('home') . '/';
		$catlink = $file . '?cat=' . $category_id;
	} else {
		$category = &get_category($category_id);
		if ( is_wp_error( $category ) )
			return $category;
		$category_nicename = $category->slug;

		if ( $parent = $category->parent )
			$category_nicename = get_category_parents($parent, false, '/', true) . $category_nicename;

		$catlink = str_replace('%category%', $category_nicename, $catlink);
		$catlink = get_option('home') . user_trailingslashit($catlink, 'category');
	}
	return apply_filters('category_link', $catlink, $category_id);
}

function get_category_parents($id, $link = FALSE, $separator = '/', $nicename = FALSE){
	$chain = '';
	$parent = &get_category($id);
	if ( is_wp_error( $parent ) )
		return $parent;

	if ( $nicename )
		$name = $parent->slug;
	else
		$name = $parent->cat_name;

	if ( $parent->parent && ($parent->parent != $parent->term_id) )
		$chain .= get_category_parents($parent->parent, $link, $separator, $nicename);

	if ( $link )
		$chain .= '<a href="' . get_category_link($parent->term_id) . '" title="' . sprintf(__("View all posts in %s"), $parent->cat_name) . '">'.$name.'</a>' . $separator;
	else
		$chain .= $name.$separator;
	return $chain;
}

function get_the_category($id = false) {
	global $post;

	$id = (int) $id;
	if ( !$id )
		$id = (int) $post->ID;

	$categories = get_object_term_cache($id, 'category');
	if ( false === $categories )
		$categories = wp_get_object_terms($id, 'category');

	if ( !empty($categories) )
		usort($categories, '_usort_terms_by_name');
	else
		$categories = array();

	foreach(array_keys($categories) as $key) {
		_make_cat_compat($categories[$key]);
	}

	return $categories;
}

function _usort_terms_by_name($a, $b) {
	return strcmp($a->name, $b->name);
}

function _usort_terms_by_ID($a, $b) {
	if ( $a->term_id > $b->term_id )
		return 1;
	elseif ( $a->term_id < $b->term_id )
		return -1;
	else
		return 0;
}

function get_the_category_by_ID($cat_ID) {
	$cat_ID = (int) $cat_ID;
	$category = &get_category($cat_ID);
	if ( is_wp_error( $category ) )
		return $category;
	return $category->name;
}

function get_the_category_list($separator = '', $parents='', $post_id = false) {
	global $wp_rewrite;
	$categories = get_the_category($post_id);
	if (empty($categories))
		return apply_filters('the_category', __('Uncategorized'), $separator, $parents);

	$rel = ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';

	$thelist = '';
	if ( '' == $separator ) {
		$thelist .= '<ul class="post-categories">';
		foreach ( $categories as $category ) {
			$thelist .= "\n\t<li>";
			switch ( strtolower($parents) ) {
				case 'multiple':
					if ($category->parent)
						$thelist .= get_category_parents($category->parent, TRUE);
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__("View all posts in %s"), $category->name) . '" ' . $rel . '>' . $category->name.'</a></li>';
					break;
				case 'single':
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__("View all posts in %s"), $category->name) . '" ' . $rel . '>';
					if ($category->parent)
						$thelist .= get_category_parents($category->parent, FALSE);
					$thelist .= $category->name.'</a></li>';
					break;
				case '':
				default:
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__("View all posts in %s"), $category->name) . '" ' . $rel . '>' . $category->cat_name.'</a></li>';
			}
		}
		$thelist .= '</ul>';
	} else {
		$i = 0;
		foreach ( $categories as $category ) {
			if ( 0 < $i )
				$thelist .= $separator . ' ';
			switch ( strtolower($parents) ) {
				case 'multiple':
					if ( $category->parent )
						$thelist .= get_category_parents($category->parent, TRUE);
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__("View all posts in %s"), $category->name) . '" ' . $rel . '>' . $category->cat_name.'</a>';
					break;
				case 'single':
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__("View all posts in %s"), $category->name) . '" ' . $rel . '>';
					if ( $category->parent )
						$thelist .= get_category_parents($category->parent, FALSE);
					$thelist .= "$category->cat_name</a>";
					break;
				case '':
				default:
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__("View all posts in %s"), $category->name) . '" ' . $rel . '>' . $category->name.'</a>';
			}
			++$i;
		}
	}
	return apply_filters('the_category', $thelist, $separator, $parents);
}

/*
 * in_category() - Checks whether the current post is within a particular category
 *
 * This function checks to see if the post is within the supplied category.  The categoy
 * can be specified by number or name and will be checked as a name first to allow for categories with numeric names.
 * Note: Prior to v2.5 of WordPress category names where not supported.
 *
 * @since 1.2.0
 *
 * @param int|string $category
 * @return bool true if the post is in the supplied category
*/
function in_category( $category ) { // Check if the current post is in the given category
	global $post;

	if ( empty($category) )
		return false;

	$cat_ID = get_cat_ID($category);
	if ( $cat_ID )
		$category = $cat_ID;

	$categories = get_object_term_cache($post->ID, 'category');
	if ( false === $categories )
		$categories = wp_get_object_terms($post->ID, 'category');
	if ( array_key_exists($category, $categories) )
		return true;
	else
		return false;
}

function the_category($separator = '', $parents='', $post_id = false) {
	echo get_the_category_list($separator, $parents, $post_id);
}

function category_description($category = 0) {
	global $cat;
	if ( !$category )
		$category = $cat;

	return get_term_field('description', $category, 'category');
}

function wp_dropdown_categories($args = '') {
	$defaults = array(
		'show_option_all' => '', 'show_option_none' => '',
		'orderby' => 'ID', 'order' => 'ASC',
		'show_last_update' => 0, 'show_count' => 0,
		'hide_empty' => 1, 'child_of' => 0,
		'exclude' => '', 'echo' => 1,
		'selected' => 0, 'hierarchical' => 0,
		'name' => 'cat', 'class' => 'postform',
		'depth' => 0, 'tab_index' => 0
	);

	$defaults['selected'] = ( is_category() ) ? get_query_var('cat') : 0;

	$r = wp_parse_args( $args, $defaults );
	$r['include_last_update_time'] = $r['show_last_update'];
	extract( $r );

	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 )
		$tab_index_attribute = " tabindex=\"$tab_index\"";

	$categories = get_categories($r);

	$output = '';
	if ( ! empty($categories) ) {
		$output = "<select name='$name' id='$name' class='$class' $tab_index_attribute>\n";

		if ( $show_option_all ) {
			$show_option_all = apply_filters('list_cats', $show_option_all);
			$output .= "\t<option value='0'>$show_option_all</option>\n";
		}

		if ( $show_option_none) {
			$show_option_none = apply_filters('list_cats', $show_option_none);
			$output .= "\t<option value='-1'>$show_option_none</option>\n";
		}

		if ( $hierarchical )
			$depth = $r['depth'];  // Walk the full depth.
		else
			$depth = -1; // Flat.

		$output .= walk_category_dropdown_tree($categories, $depth, $r);
		$output .= "</select>\n";
	}

	$output = apply_filters('wp_dropdown_cats', $output);

	if ( $echo )
		echo $output;

	return $output;
}

function wp_list_categories($args = '') {
	$defaults = array(
		'show_option_all' => '', 'orderby' => 'name',
		'order' => 'ASC', 'show_last_update' => 0,
		'style' => 'list', 'show_count' => 0,
		'hide_empty' => 1, 'use_desc_for_title' => 1,
		'child_of' => 0, 'feed' => '', 'feed_type' => '',
		'feed_image' => '', 'exclude' => '',
		'hierarchical' => true, 'title_li' => __('Categories'),
		'echo' => 1, 'depth' => 0
	);

	$r = wp_parse_args( $args, $defaults );

	if ( !isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] ) {
		$r['pad_counts'] = true;
	}

	if ( isset( $r['show_date'] ) ) {
		$r['include_last_update_time'] = $r['show_date'];
	}

	extract( $r );

	$categories = get_categories($r);

	$output = '';
	if ( $title_li && 'list' == $style )
			$output = '<li class="categories">' . $r['title_li'] . '<ul>';

	if ( empty($categories) ) {
		if ( 'list' == $style )
			$output .= '<li>' . __("No categories") . '</li>';
		else
			$output .= __("No categories");
	} else {
		global $wp_query;

		if( !empty($show_option_all) )
			if ('list' == $style )
				$output .= '<li><a href="' .  get_bloginfo('url')  . '">' . $show_option_all . '</a></li>';
			else
				$output .= '<a href="' .  get_bloginfo('url')  . '">' . $show_option_all . '</a>';

		if ( is_category() )
			$r['current_category'] = $wp_query->get_queried_object_id();

		if ( $hierarchical )
			$depth = $r['depth'];
		else
			$depth = -1; // Flat.

		$output .= walk_category_tree($categories, $depth, $r);
	}

	if ( $title_li && 'list' == $style )
		$output .= '</ul></li>';

	$output = apply_filters('wp_list_categories', $output);

	if ( $echo )
		echo $output;
	else
		return $output;
}

function wp_tag_cloud( $args = '' ) {
	$defaults = array(
		'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
		'format' => 'flat', 'orderby' => 'name', 'order' => 'ASC',
		'exclude' => '', 'include' => ''
	);
	$args = wp_parse_args( $args, $defaults );

	$tags = get_tags( array_merge($args, array('orderby' => 'count', 'order' => 'DESC')) ); // Always query top tags

	if ( empty($tags) )
		return;

	$return = wp_generate_tag_cloud( $tags, $args ); // Here's where those top tags get sorted according to $args

	if ( is_wp_error( $return ) )
		return false;

	$return = apply_filters( 'wp_tag_cloud', $return, $args );

	if ( 'array' == $args['format'] )
		return $return;

	echo $return;
}

// $tags = prefetched tag array ( get_tags() )
// $args['format'] = 'flat' => whitespace separated, 'list' => UL, 'array' => array()
// $args['orderby'] = 'name', 'count'
function wp_generate_tag_cloud( $tags, $args = '' ) {
	global $wp_rewrite;
	$defaults = array(
		'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
		'format' => 'flat', 'orderby' => 'name', 'order' => 'ASC'
	);
	$args = wp_parse_args( $args, $defaults );
	extract($args);

	if ( !$tags )
		return;
	$counts = $tag_links = array();
	foreach ( (array) $tags as $tag ) {
		$counts[$tag->name] = $tag->count;
		$tag_links[$tag->name] = get_tag_link( $tag->term_id );
		if ( is_wp_error( $tag_links[$tag->name] ) )
			return $tag_links[$tag->name];
		$tag_ids[$tag->name] = $tag->term_id;
	}

	$min_count = min($counts);
	$spread = max($counts) - $min_count;
	if ( $spread <= 0 )
		$spread = 1;
	$font_spread = $largest - $smallest;
	if ( $font_spread <= 0 )
		$font_spread = 1;
	$font_step = $font_spread / $spread;

	// SQL cannot save you; this is a second (potentially different) sort on a subset of data.
	if ( 'name' == $orderby )
		uksort($counts, 'strnatcasecmp');
	else
		asort($counts);

	if ( 'DESC' == $order )
		$counts = array_reverse( $counts, true );
	elseif ( 'RAND' == $order ) {
		$keys = array_rand( $counts, count($counts) );
		foreach ( $keys as $key )
			$temp[$key] = $counts[$key];
		$counts = $temp;
		unset($temp);
	}

	$a = array();

	$rel = ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) ? ' rel="tag"' : '';

	foreach ( $counts as $tag => $count ) {
		$tag_id = $tag_ids[$tag];
		$tag_link = clean_url($tag_links[$tag]);
		$tag = str_replace(' ', '&nbsp;', wp_specialchars( $tag ));
		$a[] = "<a href='$tag_link' class='tag-link-$tag_id' title='" . attribute_escape( sprintf( __ngettext('%d topic','%d topics',$count), $count ) ) . "'$rel style='font-size: " .
			( $smallest + ( ( $count - $min_count ) * $font_step ) )
			. "$unit;'>$tag</a>";
	}

	switch ( $format ) :
	case 'array' :
		$return =& $a;
		break;
	case 'list' :
		$return = "<ul class='wp-tag-cloud'>\n\t<li>";
		$return .= join("</li>\n\t<li>", $a);
		$return .= "</li>\n</ul>\n";
		break;
	default :
		$return = join("\n", $a);
		break;
	endswitch;

	return apply_filters( 'wp_generate_tag_cloud', $return, $tags, $args );
}

//
// Helper functions
//

function walk_category_tree() {
	$walker = new Walker_Category;
	$args = func_get_args();
	return call_user_func_array(array(&$walker, 'walk'), $args);
}

function walk_category_dropdown_tree() {
	$walker = new Walker_CategoryDropdown;
	$args = func_get_args();
	return call_user_func_array(array(&$walker, 'walk'), $args);
}

//
// Tags
//

function get_tag_link( $tag_id ) {
	global $wp_rewrite;
	$taglink = $wp_rewrite->get_tag_permastruct();

	$tag = &get_term($tag_id, 'post_tag');
	if ( is_wp_error( $tag ) )
		return $tag;
	$slug = $tag->slug;

	if ( empty($taglink) ) {
		$file = get_option('home') . '/';
		$taglink = $file . '?tag=' . $slug;
	} else {
		$taglink = str_replace('%tag%', $slug, $taglink);
		$taglink = get_option('home') . user_trailingslashit($taglink, 'category');
	}
	return apply_filters('tag_link', $taglink, $tag_id);
}

function get_the_tags( $id = 0 ) {
	return apply_filters( 'get_the_tags', get_the_terms($id, 'post_tag') );
}

function get_the_tag_list( $before = '', $sep = '', $after = '' ) {
	return apply_filters( 'the_tags', get_the_term_list(0, 'post_tag', $before, $sep, $after) );
}

function the_tags( $before = 'Tags: ', $sep = ', ', $after = '' ) {
	return the_terms( 0, 'post_tag', $before, $sep, $after );
}

function get_the_terms( $id = 0, $taxonomy ) {
	global $post;

 	$id = (int) $id;

	if ( ! $id && ! in_the_loop() )
		return false; // in-the-loop function

	if ( !$id )
		$id = (int) $post->ID;

	$terms = get_object_term_cache($id, $taxonomy);
	if ( false === $terms )
		$terms = wp_get_object_terms($id, $taxonomy);

	if ( empty( $terms ) )
		return false;

	return $terms;
}

function get_the_term_list( $id = 0, $taxonomy, $before = '', $sep = '', $after = '' ) {
	$terms = get_the_terms($id, $taxonomy);

	if ( is_wp_error($terms) )
		return $terms;

	if ( empty( $terms ) )
		return false;

	foreach ( $terms as $term ) {
		$link = get_term_link($term, $taxonomy);
		if ( is_wp_error( $link ) )
			return $link;
		$term_links[] = '<a href="' . $link . '" rel="tag">' . $term->name . '</a>';
	}

	$term_links = apply_filters( "term_links-$taxonomy", $term_links );

	return $before . join($sep, $term_links) . $after;
}

function the_terms( $id, $taxonomy, $before = '', $sep = '', $after = '' ) {
	$return = get_the_term_list($id, $taxonomy, $before, $sep, $after);
	if ( is_wp_error( $return ) )
		return false;
	else
		echo $return;
}

?>

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
	global $post, $category_cache, $blog_id;

	$id = (int) $id;
	if ( !$id )
		$id = (int) $post->ID;

	$categories = get_object_terms($id, 'category');

	if ( !empty($categories) )
		usort($categories, '_get_the_category_usort');
	else
		$categories = array();

	return $categories;
}

function _get_the_category_usort($a, $b) {
	return strcmp($a->category_name, $b->category_name);
}

function get_the_category_by_ID($cat_ID) {
	$cat_ID = (int) $cat_ID;
	$category = &get_category($cat_ID);
	return $category->name;
}

function get_the_category_list($separator = '', $parents='') {
	global $wp_rewrite;
	$categories = get_the_category();
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

function in_category($category) { // Check if the current post is in the given category
	global $category_cache, $post, $blog_id;

	if ( isset( $category_cache[$blog_id][$post->ID][$category] ) )
		return true;
	else
		return false;
}

function the_category($separator = '', $parents='') {
	echo get_the_category_list($separator, $parents);
}

function category_description($category = 0) {
	global $cat;
	if ( !$category )
		$category = $cat;
	$category = & get_category($category);
	return apply_filters('category_description', $category->description, $category->term_id);
}

function wp_dropdown_categories($args = '') {
	$defaults = array(
		'show_option_all' => '', 'show_option_none' => '', 
		'orderby' => 'ID', 'order' => 'ASC', 
		'show_last_update' => 0, 'show_count' => 0, 
		'hide_empty' => 1, 'child_of' => 0, 
		'exclude' => '', 'echo' => 1, 
		'selected' => 0, 'hierarchical' => 0, 
		'name' => 'cat', 'class' => 'postform'
	);
	
	$defaults['selected'] = ( is_category() ) ? get_query_var('cat') : 0;
	
	$r = wp_parse_args( $args, $defaults );
	$r['include_last_update_time'] = $r['show_last_update'];
	extract( $r );

	$categories = get_categories($r);

	$output = '';
	if ( ! empty($categories) ) {
		$output = "<select name='$name' id='$name' class='$class'>\n";

		if ( $show_option_all ) {
			$show_option_all = apply_filters('list_cats', $show_option_all);
			$output .= "\t<option value='0'>$show_option_all</option>\n";
		}

		if ( $show_option_none) {
			$show_option_none = apply_filters('list_cats', $show_option_none);
			$output .= "\t<option value='-1'>$show_option_none</option>\n";
		}

		if ( $hierarchical )
			$depth = 0;  // Walk the full depth.
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
		'child_of' => 0, 'feed' => '', 
		'feed_image' => '', 'exclude' => '', 
		'hierarchical' => true, 'title_li' => __('Categories')
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
			$depth = 0;  // Walk the full depth.
		else
			$depth = -1; // Flat.

		$output .= walk_category_tree($categories, $depth, $r);
	}

	if ( $title_li && 'list' == $style )
		$output .= '</ul></li>';

	echo apply_filters('wp_list_categories', $output);
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
	echo apply_filters( 'wp_tag_cloud', $return, $args );
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
		$counts = array_reverse( $tag_counts, true );

	$a = array();

	$rel = ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) ? ' rel="tag"' : '';

	foreach ( $counts as $tag => $count ) {
		$tag_link = clean_url($tag_links[$tag]);
		$tag = str_replace(' ', '&nbsp;', wp_specialchars( $tag ));
		$a[] = "<a href='$tag_link' title='" . attribute_escape( sprintf( __('%d topics'), $count ) ) . "'$rel style='font-size: " .
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
	global $post; 
 
 	$id = (int) $id;

	if ( ! $id && ! in_the_loop() ) 
		return false; // in-the-loop function 
 
	if ( !$id ) 
		$id = (int) $post->ID;

	$tags = wp_get_post_tags( $id );
	$tags = apply_filters( 'get_the_tags', $tags );
	if ( empty( $tags ) ) 
		return false; 
	return $tags; 
}

function the_tags( $before = 'Tags: ', $sep = ', ', $after = '' ) {
	$tags = get_the_tags();

	if ( empty( $tags ) )
		return false;
	
	$tag_list = $before;
	foreach ( $tags as $tag )
		$tag_links[] = '<a href="' . get_tag_link($tag->term_id) . '">' . $tag->slug . '</a>';

	$tag_links = join( $sep, $tag_links );
	$tag_links = apply_filters( 'the_tags', $tag_links );
	$tag_list .= $tag_links;

	$tag_list .= $after;

	echo $tag_list;
}

?>

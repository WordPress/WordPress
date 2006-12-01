<?php

function get_category_children($id, $before = '/', $after = '') {
	if ( 0 == $id )
		return '';

	$chain = '';

	$cat_ids = get_all_category_ids();
	foreach ( $cat_ids as $cat_id ) {
		if ( $cat_id == $id)
			continue;

		$category = get_category($cat_id);
		if ( $category->category_parent == $id ) {
			$chain .= $before.$category->cat_ID.$after;
			$chain .= get_category_children($category->cat_ID, $before, $after);
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
		$category_nicename = $category->category_nicename;

		if ( $parent = $category->category_parent )
			$category_nicename = get_category_parents($parent, false, '/', true) . $category_nicename . '/';

		$catlink = str_replace('%category%', $category_nicename, $catlink);
		$catlink = get_option('home') . trailingslashit($catlink);
	}
	return apply_filters('category_link', $catlink, $category_id);
}

function get_category_parents($id, $link = FALSE, $separator = '/', $nicename = FALSE){
	$chain = '';
	$parent = &get_category($id);

	if ( $nicename )
		$name = $parent->category_nicename;
	else
		$name = $parent->cat_name;

	if ( $parent->category_parent && ($parent->category_parent != $parent->cat_ID) )
		$chain .= get_category_parents($parent->category_parent, $link, $separator, $nicename);

	if ( $link )
		$chain .= '<a href="' . get_category_link($parent->cat_ID) . '" title="' . sprintf(__("View all posts in %s"), $parent->cat_name) . '">'.$name.'</a>' . $separator;
	else
		$chain .= $name.$separator;
	return $chain;
}

function get_the_category($id = false) {
global $post, $category_cache, $blog_id;

	if ( !$id )
		$id = $post->ID;

	if ( !isset($category_cache[$blog_id][$id]) )
		update_post_category_cache($id);

	$categories = $category_cache[$blog_id][$id];

	if ( !empty($categories) )
		sort($categories);
	else
		$categories = array();

	return $categories;
}

function get_the_category_by_ID($cat_ID) {
	$cat_ID = (int) $cat_ID;
	$category = &get_category($cat_ID);
	return $category->cat_name;
}

function get_the_category_list($separator = '', $parents='') {
	$categories = get_the_category();
	if (empty($categories))
		return apply_filters('the_category', __('Uncategorized'), $separator, $parents);

	$thelist = '';
	if ( '' == $separator ) {
		$thelist .= '<ul class="post-categories">';
		foreach ( $categories as $category ) {
			$thelist .= "\n\t<li>";
			switch ( strtolower($parents) ) {
				case 'multiple':
					if ($category->category_parent)
						$thelist .= get_category_parents($category->category_parent, TRUE);
					$thelist .= '<a href="' . get_category_link($category->cat_ID) . '" title="' . sprintf(__("View all posts in %s"), $category->cat_name) . '" rel="category tag">'.$category->cat_name.'</a></li>';
					break;
				case 'single':
					$thelist .= '<a href="' . get_category_link($category->cat_ID) . '" title="' . sprintf(__("View all posts in %s"), $category->cat_name) . ' rel="category tag">';
					if ($category->category_parent)
						$thelist .= get_category_parents($category->category_parent, FALSE);
					$thelist .= $category->cat_name.'</a></li>';
					break;
				case '':
				default:
					$thelist .= '<a href="' . get_category_link($category->cat_ID) . '" title="' . sprintf(__("View all posts in %s"), $category->cat_name) . '" rel="category tag">'.$category->cat_name.'</a></li>';
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
					if ( $category->category_parent )
						$thelist .= get_category_parents($category->category_parent, TRUE);
					$thelist .= '<a href="' . get_category_link($category->cat_ID) . '" title="' . sprintf(__("View all posts in %s"), $category->cat_name) . '" rel="category tag">'.$category->cat_name.'</a>';
					break;
				case 'single':
					$thelist .= '<a href="' . get_category_link($category->cat_ID) . '" title="' . sprintf(__("View all posts in %s"), $category->cat_name) . '" rel="category tag">';
					if ( $category->category_parent )
						$thelist .= get_category_parents($category->category_parent, FALSE);
					$thelist .= "$category->cat_name</a>";
					break;
				case '':
				default:
					$thelist .= '<a href="' . get_category_link($category->cat_ID) . '" title="' . sprintf(__("View all posts in %s"), $category->cat_name) . '" rel="category tag">'.$category->cat_name.'</a>';
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
	return apply_filters('category_description', $category->category_description, $category->cat_ID);
}

function wp_dropdown_categories($args = '') {
	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);

	$defaults = array('show_option_all' => '', 'show_option_none' => '', 'orderby' => 'ID',
		'order' => 'ASC', 'show_last_update' => 0, 'show_count' => 0,
		'hide_empty' => 1, 'child_of' => 0, 'exclude' => '', 'echo' => 1,
		'selected' => 0, 'hierarchical' => 0, 'name' => 'cat',
		'class' => 'postform');
	$defaults['selected'] = ( is_category() ) ? get_query_var('cat') : 0;
	$r = array_merge($defaults, $r);
	$r['include_last_update_time'] = $r['show_last_update'];
	extract($r);

	$categories = get_categories($r);

	$output = '';
	if ( ! empty($categories) ) {
		$output = "<select name='$name' class='$class'>\n";

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
	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);

	$defaults = array('show_option_all' => '', 'orderby' => 'name',
		'order' => 'ASC', 'show_last_update' => 0, 'style' => 'list',
		'show_count' => 0, 'hide_empty' => 1, 'use_desc_for_title' => 1,
		'child_of' => 0, 'feed' => '', 'feed_image' => '', 'exclude' => '',
		'hierarchical' => true, 'title_li' => __('Categories'));
	$r = array_merge($defaults, $r);
	if ( isset($r['show_date']) )
		$r['include_last_update_time'] = $r['show_date'];
	extract($r);

	$categories = get_categories($r);

	$output = '';
	if ( $title_li && 'list' == $style )
			$output = '<li class="categories">' . $r['title_li'] . '<ul>';

	if ( empty($categories) ) {
		if ( $list)
			$output .= '<li>' . __("No categories") . '</li>';
		else
			$output .= __("No categories");
	} else {
		global $wp_query;
		
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

	echo apply_filters('list_cats', $output);
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

?>

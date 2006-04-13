<?php

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

function get_the_category($id = false) {
global $post, $category_cache;

	if ( !$id )
		$id = $post->ID;

	if ( !isset($category_cache[$id]) )
		update_post_category_cache($id);

	$categories = $category_cache[$id];

	if ( !empty($categories) )
		sort($categories);
	else
		$categories = array();

	return $categories;
}

function get_category_link($category_id) {
	global $wp_rewrite;
	$catlink = $wp_rewrite->get_category_permastruct();

	if ( empty($catlink) ) {
		$file = get_settings('home') . '/';
		$catlink = $file . '?cat=' . $category_id;
	} else {
		$category = &get_category($category_id);
		$category_nicename = $category->category_nicename;

		if ( $parent = $category->category_parent )
			$category_nicename = get_category_parents($parent, false, '/', true) . $category_nicename . '/';

		$catlink = str_replace('%category%', $category_nicename, $catlink);
		$catlink = get_settings('home') . trailingslashit($catlink);
	}
	return apply_filters('category_link', $catlink, $category_id);
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

function the_category($separator = '', $parents='') {
	echo get_the_category_list($separator, $parents);
}

function get_the_category_by_ID($cat_ID) {
	$cat_ID = (int) $cat_ID;
	$category = &get_category($cat_ID);
	return $category->cat_name;
}

function get_category_parents($id, $link = FALSE, $separator = '/', $nicename = FALSE){
	$chain = '';
	$parent = &get_category($id);

	if ( $nicename )
		$name = $parent->category_nicename;
	else
		$name = $parent->cat_name;

	if ( $parent->category_parent )
		$chain .= get_category_parents($parent->category_parent, $link, $separator, $nicename);

	if ( $link )
		$chain .= '<a href="' . get_category_link($parent->cat_ID) . '" title="' . sprintf(__("View all posts in %s"), $parent->cat_name) . '">'.$name.'</a>' . $separator;
	else
		$chain .= $name.$separator;
	return $chain;
}

function get_category_children($id, $before = '/', $after = '') {
	if ( 0 == $id )
		return '';

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

	$defaults = array('show_option_all' => '', 'orderby' => 'ID',
		'order' => 'asc', 'style' => 'list', 'show_last_update' => 0,
		'show_count' => 0, 'hide_empty' => 1, 'use_desc_for_title' => 1,
		'child_of' => 0, 'feed' => '', 'feed_image' => '', 'exclude' => '',
		'hierarchical' => false, 'title_li' => '');
	$r = array_merge($defaults, $r);
	$r['include_last_update_time'] = $r['show_date'];
	extract($r);

	$categories = get_categories($r);
	
	$output = '';
	if ( $title_li && $list )
			$output = '<li class="categories">' . $r['title_li'] . '<ul>';

	if ( empty($categories) ) {
		if ( $list)
			$output .= '<li>' . __("No categories") . '</li>';
		else
			$output .= __("No categories");
	} else {
		global $wp_query;
		$r['current_category'] = $wp_query->get_queried_object_id();
		if ( $hierarchical )
			$depth = 0;  // Walk the full depth.
		else
			$depth = -1; // Flat.

		$output .= walk_category_tree($categories, $depth, $r);
	}

	if ( $title_li && $list )
		$output .= '</ul></li>';
			
	echo apply_filters('list_cats', $output);
}

function in_category($category) { // Check if the current post is in the given category
	global $category_cache, $post;

	if ( isset( $category_cache[$post->ID][$category] ) )
		return true;
	else
		return false;
}

function &_get_cat_children($category_id, $categories) {
	if ( empty($categories) )
		return array();

	$category_list = array();
	foreach ( $categories as $category ) {
		if ( $category->category_parent == $category_id ) {
			$category_list[] = $category;
			if ( $children = _get_cat_children($category->cat_ID, $categories) )
				$category_list = array_merge($category_list, $children);
		}
	}

	return $category_list;
}

function &get_categories($args = '') {
	global $wpdb, $category_links;

	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);

	$defaults = array('type' => 'post', 'child_of' => 0, 'orderby' => 'name', 'order' => 'ASC',
		'hide_empty' => true, 'include_last_update_time' => false, 'hierarchical' => 1, $exclude => '', $include => '');
	$r = array_merge($defaults, $r);
	$r['orderby'] = "cat_" . $r['orderby'];  // restricts order by to cat_ID and cat_name fields
	extract($r);

	$where = 'cat_ID > 0';
	$inclusions = '';
	if ( !empty($include) ) {
		$child_of = 0; //ignore child_of and exclude params if using include 
		$exclude = '';  
		$incategories = preg_split('/[\s,]+/',$include);
		if ( count($incategories) ) {
			foreach ( $incategories as $incat ) {
				if (empty($inclusions))
					$inclusions = ' AND ( cat_ID = ' . intval($incat) . ' ';
				else
					$inclusions .= ' OR cat_ID = ' . intval($incat) . ' ';
			}
		}
	}
	if (!empty($inclusions)) 
		$inclusions .= ')';	
	$where .= $inclusions;

	$exclusions = '';
	if ( !empty($exclude) ) {
		$excategories = preg_split('/[\s,]+/',$exclude);
		if ( count($excategories) ) {
			foreach ( $excategories as $excat ) {
				if (empty($exclusions))
					$exclusions = ' AND ( cat_ID <> ' . intval($excat) . ' ';
				else
					$exclusions .= ' AND cat_ID <> ' . intval($excat) . ' ';
				// TODO: Exclude children of excluded cats?   Note: children are getting excluded
			}
		}
	}
	if (!empty($exclusions)) 
		$exclusions .= ')';
	$exclusions = apply_filters('list_cats_exclusions', $exclusions );
	$where .= $exclusions;

	$having = '';
	if ( $hide_empty ) {
		if ( 'link' == $type )
			$having = 'HAVING link_count > 0';
		else
			$having = 'HAVING category_count > 0';
	}

	$categories = $wpdb->get_results("SELECT * FROM $wpdb->categories WHERE $where $having ORDER BY $orderby $order");

	if ( empty($categories) )
		return array();

	// TODO: Integrate this into the main query.
	if ( $include_last_update_time ) {
		$stamps = $wpdb->get_results("SELECT category_id, UNIX_TIMESTAMP( MAX(post_date) ) AS ts FROM $wpdb->posts, $wpdb->post2cat, $wpdb->categories
							WHERE post_status = 'publish' AND post_id = ID AND $where GROUP BY category_id");
		global $cat_stamps;
		foreach ($stamps as $stamp)
			$cat_stamps[$stamp->category_id] = $stamp->ts;
		function stamp_cat($cat) {
			global $cat_stamps;
			$cat->last_update_timestamp = $cat_stamps[$cat->cat_ID];
			return $cat;	
		}
		$categories = array_map('stamp_cat', $categories);
		unset($cat_stamps);
	}

	if ( $child_of || $hierarchical )
		$categories = & _get_cat_children($child_of, $categories);

	return apply_filters('get_categories', $categories);
}

?>

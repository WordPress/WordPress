<?php

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
	parse_str($args, $r);
	if ( !isset($r['show_option_all']))
		$r['show_option_all'] = '';
	if ( !isset($r['show_option_none']))
		$r['show_option_none'] = '';
	if ( !isset($r['orderby']) )
		$r['orderby'] = 'ID';
	if ( !isset($r['order']) )
		$r['order'] = 'ASC';
	if ( !isset($r['show_last_update']) )
		$r['show_last_update'] = 0;
	if ( !isset($r['show_counts']) )
		$r['show_counts'] = 0;
	if ( !isset($r['hide_empty']) )
		$r['hide_empty'] = 1;
	if ( !isset($r['child_of']) )
		$r['child_of'] = 0;
	if ( !isset($r['exclude']) )
		$r['exclude'] = '';
	if ( !isset($r['echo']) )
		$r['echo'] = 1;
	if ( !isset($r['selected']) )
		$r['selected'] = 0;
	if ( !isset($r['hierarchical']) )
		$r['hierarchical'] = 0;
	if ( !isset($r['name']) )
		$r['name'] = 'cat';
	if ( !isset($r['class']) )
		$r['class'] = 'postform';

	$r['include_last_update_time'] = $r['show_last_update'];

	extract($r);

	$query = add_query_arg($r, '');
	$categories = get_categories($query);

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

		$output .= walk_category_tree($categories, $depth, '_category_dropdown_element', '', '', '', $selected, $r);
		$output .= "</select>\n";
	}

	$output = apply_filters('wp_dropdown_cats', $output);

	if ( $echo )
		echo $output;

	return $output;
}

function _category_dropdown_element($output, $category, $depth, $selected, $args) {
	$pad = str_repeat('&nbsp;', $depth * 3);

	$cat_name = apply_filters('list_cats', $category->cat_name, $category);
	$output .= "\t<option value=\"".$category->cat_ID."\"";
	if ( $category->cat_ID == $selected )
		$output .= ' selected="selected"';
	$output .= '>';
	$output .= $cat_name;
	if ( $args['show_counts'] )
		$output .= '&nbsp;&nbsp;('. $category->category_count .')';
	if ( $args['show_last_update'] ) {
		$format = 'Y-m-d';
		$output .= '&nbsp;&nbsp;' . gmdate($format, $category->last_update_timestamp);
	}
	$output .= "</option>\n";

	return $output;
}

function wp_list_cats($args = '') {
	return wp_list_categories($args);	
}

function wp_list_categories($args = '') {
	parse_str($args, $r);
	if ( !isset($r['optionall']))
		$r['optionall'] = 0;
	if ( !isset($r['all']))
		$r['all'] = 'All';
	if ( !isset($r['sort_column']) )
		$r['sort_column'] = 'ID';
	if ( !isset($r['sort_order']) )
		$r['sort_order'] = 'asc';
	if ( !isset($r['file']) )
		$r['file'] = '';
	if ( !isset($r['list']) )
		$r['list'] = true;
	if ( !isset($r['optiondates']) )
		$r['optiondates'] = 0;
	if ( !isset($r['optioncount']) )
		$r['optioncount'] = 0;
	if ( !isset($r['hide_empty']) )
		$r['hide_empty'] = 1;
	if ( !isset($r['use_desc_for_title']) )
		$r['use_desc_for_title'] = 1;
	if ( !isset($r['child_of']) )
		$r['child_of'] = 0;
	if ( !isset($r['feed']) )
		$r['feed'] = '';
	if ( !isset($r['feed_image']) )
		$r['feed_image'] = '';
	if ( !isset($r['exclude']) )
		$r['exclude'] = '';
	if ( !isset($r['hierarchical']) )
		$r['hierarchical'] = false;
	if ( !isset($r['title_li']) )
		$r['title_li'] = '';
	if ( !isset($r['orderby']) )
		$r['orderby'] = $r['sort_column'];
	if ( !isset($r['order']) )
		$r['order'] = $r['sort_order'];		
	$r['include_last_update_time'] = $r['optiondates'];
	
	extract($r);

	$query = add_query_arg($r, '');
	$categories = get_categories($query);
	
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
		$current_category = $wp_query->get_queried_object_id();
		if ( $hierarchical )
			$depth = 0;  // Walk the full depth.
		else
			$depth = -1; // Flat.

		$output .= walk_category_tree($categories, $depth, '_category_list_element_start', '_category_list_element_end', '_category_list_level_start', '_category_list_level_end', $current_category, $r);
	}

	if ( $title_li && $list )
		$output .= '</ul></li>';
			
	echo apply_filters('list_cats', $output);
}

function _category_list_level_start($output, $depth, $cat, $args) {
	if (! $args['list'])
		return $output;

	$indent = str_repeat("\t", $depth);
	$output .= "$indent<ul class='children'>\n";
	return $output;
}

function _category_list_level_end($output, $depth, $cat, $args) {
	if (! $args['list'])
		return $output;

	$indent = str_repeat("\t", $depth);
	$output .= "$indent</ul>\n";
	return $output;
}

function _category_list_element_start($output, $category, $depth, $current_category, $args) {
	extract($args);

	$link = '<a href="' . get_category_link($category->cat_ID) . '" ';
	if ( $use_desc_for_title == 0 || empty($category->category_description) )
		$link .= 'title="'. sprintf(__("View all posts filed under %s"), wp_specialchars($category->cat_name)) . '"';
	else
		$link .= 'title="' . wp_specialchars(apply_filters('category_description',$category->category_description,$category)) . '"';
	$link .= '>';
	$link .= apply_filters('list_cats', $category->cat_name, $category).'</a>';

	if ( (! empty($feed_image)) || (! empty($feed)) ) {
		$link .= ' ';

		if ( empty($feed_image) )
			$link .= '(';

		$link .= '<a href="' . get_category_rss_link(0, $category->cat_ID, $category->category_nicename) . '"';

		if ( !empty($feed) ) {
			$title = ' title="' . $feed . '"';
			$alt = ' alt="' . $feed . '"';
			$name = $feed;
			$link .= $title;
		}

		$link .= '>';

		if ( !empty($feed_image) )
			$link .= "<img src='$feed_image' $alt$title" . ' />';
		else
			$link .= $name;
		$link .= '</a>';
		if (empty($feed_image))
			$link .= ')';
	}

	if ( intval($optioncount) == 1 )
		$link .= ' ('.intval($category->category_count).')';

	if ( $optiondates ) {
		if ( $optiondates == 1 )
			$optiondates = 'Y-m-d';
		$link .= ' ' . gmdate($optiondates,$category->last_update_timestamp);
	}

	if ( $list ) {
		$output .= "\t<li";
		if ( ($category->cat_ID == $current_category) && is_category() )
			$output .=  ' class="current-cat"';
		$output .= ">$link\n";
	} else {
		$output .= "\t$link<br />\n";
	}

	return $output;
}

function _category_list_element_end($output, $category, $depth, $cat, $args) {
	if (! $args['list'])
		return $output;

	$output .= "</li>\n";
	return $output;
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

	parse_str($args, $r);

	if ( !isset($r['type']) )  // 'post' or 'link'
		$r['type'] = 'post';
	if ( !isset($r['child_of']) )
		$r['child_of'] = 0;
	if ( !isset($r['orderby']) )
		$r['orderby'] = 'name';
	if ( !isset($r['order']) )
		$r['order'] = 'ASC';
	if ( !isset($r['hide_empty']) )
		$r['hide_empty'] = true;
	if ( !isset($r['include_last_update_time']) )
		$r['include_last_update_time'] = false;
	if ( !isset($r['hierarchical']) )
		$r['hierarchical'] = 1;

	$r['orderby'] = "cat_" . $r['orderby'];

	extract($r);

	$exclusions = '';
	$having = '';
	$where = 'cat_ID > 0';

	$exclusions = '';
	if ( !empty($exclude) ) {
		$excategories = preg_split('/[\s,]+/',$exclude);
		if ( count($excategories) ) {
			foreach ( $excategories as $excat ) {
				$exclusions .= ' AND cat_ID <> ' . intval($excat) . ' ';
				// TODO: Exclude children of excluded cats?
			}
		}
	}
	$exclusions = apply_filters('list_cats_exclusions', $exclusions );
	$where .= $exclusions;

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

	return $categories;
}

?>

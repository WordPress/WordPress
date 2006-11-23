<?php

function get_all_category_ids() {
	global $wpdb;

	if ( ! $cat_ids = wp_cache_get('all_category_ids', 'category') ) {
		$cat_ids = $wpdb->get_col("SELECT cat_ID FROM $wpdb->categories");
		wp_cache_add('all_category_ids', $cat_ids, 'category');
	}

	return $cat_ids;
}

function &get_categories($args = '') {
	global $wpdb, $category_links;

	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);

	$defaults = array('type' => 'post', 'child_of' => 0, 'orderby' => 'name', 'order' => 'ASC',
		'hide_empty' => true, 'include_last_update_time' => false, 'hierarchical' => 1, 'exclude' => '', 'include' => '',
		'number' => '');
	$r = array_merge($defaults, $r);
	if ( 'count' == $r['orderby'] )
		$r['orderby'] = 'category_count';
	else
		$r['orderby'] = "cat_" . $r['orderby'];  // restricts order by to cat_ID and cat_name fields
	$r['number'] = (int) $r['number'];
	extract($r);

	$key = md5( serialize( $r ) );
	if ( $cache = wp_cache_get( 'get_categories', 'category' ) )
		if ( isset( $cache[ $key ] ) )
			return $cache[ $key ];

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
	$exclusions = apply_filters('list_cats_exclusions', $exclusions, $r );
	$where .= $exclusions;

	if ( $hide_empty && !$hierarchical ) {
		if ( 'link' == $type )
			$where .= ' AND link_count > 0';
		else
			$where .= ' AND category_count > 0';
	}

	if ( !empty($number) )
		$number = 'LIMIT ' . $number;
	else
		$number = '';

	$categories = $wpdb->get_results("SELECT * FROM $wpdb->categories WHERE $where ORDER BY $orderby $order $number");

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

	// Update category counts to include children.
	if ( $hierarchical ) {
		foreach ( $categories as $k => $category ) {
			$progeny = $category->category_count;
			if ( $children = _get_cat_children($category->cat_ID, $categories) ) {
				foreach ( $children as $child )
					$progeny += $child->category_count;
			}
			if ( !$progeny && $hide_empty )
				unset($categories[$k]);
			else
				$categories[$k]->category_count = $progeny;
		}
	}
	reset ( $categories );

	$cache[ $key ] = $categories;
	wp_cache_set( 'get_categories', $cache, 'category' );

	return apply_filters('get_categories', $categories, $r);
}

function delete_get_categories_cache() {
	wp_cache_delete('get_categories', 'category');
}
add_action( 'wp_insert_post', 'delete_get_categories_cache' );
add_action( 'edit_category', 'delete_get_categories_cache' );
add_action( 'add_category', 'delete_get_categories_cache' );
add_action( 'delete_category', 'delete_get_categories_cache' );

// Retrieves category data given a category ID or category object.
// Handles category caching.
function &get_category(&$category, $output = OBJECT) {
	global $wpdb;

	if ( empty($category) )
		return null;

	if ( is_object($category) ) {
		wp_cache_add($category->cat_ID, $category, 'category');
		$_category = $category;
	} else {
		if ( ! $_category = wp_cache_get($category, 'category') ) {
			$_category = $wpdb->get_row("SELECT * FROM $wpdb->categories WHERE cat_ID = '$category' LIMIT 1");
			wp_cache_add($category, $_category, 'category');
		}
	}

	$_category = apply_filters('get_category', $_category);

	if ( $output == OBJECT ) {
		return $_category;
	} elseif ( $output == ARRAY_A ) {
		return get_object_vars($_category);
	} elseif ( $output == ARRAY_N ) {
		return array_values(get_object_vars($_category));
	} else {
		return $_category;
	}
}

function get_category_by_path($category_path, $full_match = true, $output = OBJECT) {
	global $wpdb;
	$category_path = rawurlencode(urldecode($category_path));
	$category_path = str_replace('%2F', '/', $category_path);
	$category_path = str_replace('%20', ' ', $category_path);
	$category_paths = '/' . trim($category_path, '/');
	$leaf_path  = sanitize_title(basename($category_paths));
	$category_paths = explode('/', $category_paths);
	foreach($category_paths as $pathdir)
		$full_path .= ($pathdir!=''?'/':'') . sanitize_title($pathdir);

	$categories = $wpdb->get_results("SELECT cat_ID, category_nicename, category_parent FROM $wpdb->categories WHERE category_nicename = '$leaf_path'");

	if ( empty($categories) )
		return NULL;

	foreach ($categories as $category) {
		$path = '/' . $leaf_path;
		$curcategory = $category;
		while ( ($curcategory->category_parent != 0) && ($curcategory->category_parent != $curcategory->cat_ID) ) {
			$curcategory = $wpdb->get_row("SELECT cat_ID, category_nicename, category_parent FROM $wpdb->categories WHERE cat_ID = '$curcategory->category_parent'");
			$path = '/' . $curcategory->category_nicename . $path;
		}

		if ( $path == $full_path )
			return get_category($category->cat_ID, $output);
	}

	// If full matching is not required, return the first cat that matches the leaf.
	if ( ! $full_match )
		return get_category($categories[0]->cat_ID, $output);

	return NULL;
}

// Get the ID of a category from its name
function get_cat_ID($cat_name='General') {
	global $wpdb;

	$cid = $wpdb->get_var("SELECT cat_ID FROM $wpdb->categories WHERE cat_name='$cat_name'");

	return $cid?$cid:1;	// default to cat 1
}

// Deprecate
function get_catname($cat_ID) {
	return get_cat_name($cat_ID);
}

// Get the name of a category from its ID
function get_cat_name($cat_id) {
	$cat_id = (int) $cat_id;
	$category = &get_category($cat_id);
	return $category->cat_name;
}

//
// Private
//

function &_get_cat_children($category_id, $categories) {
	if ( empty($categories) )
		return array();

	$category_list = array();
	foreach ( $categories as $category ) {
		if ( $category->cat_ID == $category_id )
			continue;

		if ( $category->category_parent == $category_id ) {
			$category_list[] = $category;
			if ( $children = _get_cat_children($category->cat_ID, $categories) )
				$category_list = array_merge($category_list, $children);
		}
	}

	return $category_list;
}

?>

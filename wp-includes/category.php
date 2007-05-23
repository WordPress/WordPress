<?php

define('TAXONOMY_CATEGORY', 1);
define('TAXONOMY_TAG', 2);

function get_all_category_ids() {
	global $wpdb;

	if ( ! $cat_ids = wp_cache_get('all_category_ids', 'category') ) {
		$cat_ids = get_terms('category', 'get=ids&hierarchical=0&hide_empty=0');
		wp_cache_add('all_category_ids', $cat_ids, 'category');
	}

	return $cat_ids;
}

function &get_categories($args = '') {
	// TODO Add back compat fields into each object.
	return get_terms('category', $args);
}

// Retrieves category data given a category ID or category object.
// Handles category caching.
function &get_category(&$category, $output = OBJECT) {
	return get_term($category, 'category', $output);
}

function get_category_by_path($category_path, $full_match = true, $output = OBJECT) {
	global $wpdb;
	$category_path = rawurlencode(urldecode($category_path));
	$category_path = str_replace('%2F', '/', $category_path);
	$category_path = str_replace('%20', ' ', $category_path);
	$category_paths = '/' . trim($category_path, '/');
	$leaf_path  = sanitize_title(basename($category_paths));
	$category_paths = explode('/', $category_paths);
	$full_path = '';
	foreach ( (array) $category_paths as $pathdir )
		$full_path .= ( $pathdir != '' ? '/' : '' ) . sanitize_title($pathdir);

	$categories = get_terms('category', "slug=$leaf_path");

	if ( empty($categories) )
		return NULL;

	foreach ($categories as $category) {
		$path = '/' . $leaf_path;
		$curcategory = $category;
		while ( ($curcategory->parent != 0) && ($curcategory->parent != $curcategory->term_id) ) {
			$curcategory = get_term($curcategory->parent);
			$path = '/' . $curcategory->slug . $path;
		}

		if ( $path == $full_path )
			return get_category($category->term_id, $output);
	}

	// If full matching is not required, return the first cat that matches the leaf.
	if ( ! $full_match )
		return get_category($categories[0]->term_id, $output);

	return NULL;
}

function get_category_by_slug( $slug  ) {
	return get_term_by('slug', $slug, 'category');
}

// Get the ID of a category from its name
function get_cat_ID($cat_name='General') {
	global $wpdb;

	$cat = get_term_by('name', $cat_name, 'category');
	if ($cat)
		return $cat->term_id;
	return 0;
}

// Deprecate
function get_catname($cat_ID) {
	return get_cat_name($cat_ID);
}

// Get the name of a category from its ID
function get_cat_name($cat_id) {
	$cat_id = (int) $cat_id;
	$category = &get_category($cat_id);
	return $category->name;
}

function cat_is_ancestor_of($cat1, $cat2) { 
	if ( is_int($cat1) ) 
		$cat1 = & get_category($cat1); 
	if ( is_int($cat2) ) 
		$cat2 = & get_category($cat2); 

	if ( !$cat1->term_id || !$cat2->parent ) 
		return false; 

	if ( $cat2->parent == $cat1->term_id ) 
		return true; 

	return cat_is_ancestor_of($cat1, get_category($cat2->parent)); 
} 

//
// Private
//

function &_get_cat_children($category_id, $categories) {
	if ( empty($categories) )
		return array();

	$category_list = array();
	$has_children = _get_category_hierarchy();

	if  ( ( 0 != $category_id ) && ! isset($has_children[$category_id]) )
		return array();

	foreach ( $categories as $category ) {
		if ( $category->cat_ID == $category_id )
			continue;

		if ( $category->category_parent == $category_id ) {
			$category_list[] = $category;

			if ( !isset($has_children[$category->cat_ID]) )
				continue;

			if ( $children = _get_cat_children($category->cat_ID, $categories) )
				$category_list = array_merge($category_list, $children);
		}
	}

	return $category_list;
}

// Recalculates link or post counts by including items from child categories
// Assumes all relevant children are already in the $categories argument
function _pad_category_counts($type, &$categories) {
	global $wpdb;

	// Set up some useful arrays
	foreach ( $categories as $key => $cat ) {
		$cats[$cat->cat_ID] = & $categories[$key];
		$cat_IDs[] = $cat->cat_ID;
	}

	// Get the relevant post2cat or link2cat records and stick them in a lookup table
	if ( $type == 'post' ) {
		$results = $wpdb->get_results("SELECT post_id, category_id FROM $wpdb->post2cat LEFT JOIN $wpdb->posts ON post_id = ID WHERE category_id IN (".join(',', $cat_IDs).") AND post_type = 'post' AND post_status = 'publish'");
		foreach ( $results as $row )
			++$cat_items[$row->category_id][$row->post_id];
	} else {
		$results = $wpdb->get_results("SELECT $wpdb->link2cat.link_id, category_id FROM $wpdb->link2cat LEFT JOIN $wpdb->links USING (link_id) WHERE category_id IN (".join(',', $cat_IDs).") AND link_visible = 'Y'");
		foreach ( $results as $row )
			++$cat_items[$row->category_id][$row->link_id];
	}

	// Touch every ancestor's lookup row for each post in each category
	foreach ( $cat_IDs as $cat_ID ) {
		$child = $cat_ID;
		while ( $parent = $cats[$child]->category_parent ) {
			if ( !empty($cat_items[$cat_ID]) )
				foreach ( $cat_items[$cat_ID] as $item_id => $touches )
					++$cat_items[$parent][$item_id];
			$child = $parent;
		}
	}

	// Transfer the touched cells 
	foreach ( (array) $cat_items as $id => $items )
		if ( isset($cats[$id]) )
			$cats[$id]->{'link' == $type ? 'link_count' : 'category_count'} = count($items);
}

function _get_category_hierarchy() {
	return _get_term_hierarchy('category');
}

// Tags

function &get_tags($args = '') {
	global $wpdb, $category_links;

	$key = md5( serialize( $args ) );
	if ( $cache = wp_cache_get( 'get_tags', 'category' ) )
		if ( isset( $cache[ $key ] ) )
			return apply_filters('get_tags', $cache[$key], $args);


	$tags = get_terms('post_tag');

	if ( empty($tags) )
		return array();

	$cache[ $key ] = $tags;
	wp_cache_set( 'get_tags', $cache, 'category' );

	$tags = apply_filters('get_tags', $tags, $args);
	return $tags;
}

?>

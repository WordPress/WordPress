<?php

function get_all_category_ids() {
	if ( ! $cat_ids = wp_cache_get('all_category_ids', 'category') ) {
		$cat_ids = get_terms('category', 'fields=ids&get=all');
		wp_cache_add('all_category_ids', $cat_ids, 'category');
	}

	return $cat_ids;
}

function &get_categories($args = '') {
	$defaults = array('type' => 'category');
	$args = wp_parse_args($args, $defaults);

	$taxonomy = 'category';
	if ( 'link' == $args['type'] )
		$taxonomy = 'link_category';
	$categories = get_terms($taxonomy, $args);

	foreach ( array_keys($categories) as $k )
		_make_cat_compat($categories[$k]);

	return $categories;
}

// Retrieves category data given a category ID or category object.
// Handles category caching.
function &get_category($category, $output = OBJECT, $filter = 'raw') {
	$category = get_term($category, 'category', $output, $filter);
	if ( is_wp_error( $category ) )
		return $category;

	_make_cat_compat($category);

	return $category;
}

function get_category_by_path($category_path, $full_match = true, $output = OBJECT) {
	$category_path = rawurlencode(urldecode($category_path));
	$category_path = str_replace('%2F', '/', $category_path);
	$category_path = str_replace('%20', ' ', $category_path);
	$category_paths = '/' . trim($category_path, '/');
	$leaf_path  = sanitize_title(basename($category_paths));
	$category_paths = explode('/', $category_paths);
	$full_path = '';
	foreach ( (array) $category_paths as $pathdir )
		$full_path .= ( $pathdir != '' ? '/' : '' ) . sanitize_title($pathdir);

	$categories = get_terms('category', "get=all&slug=$leaf_path");

	if ( empty($categories) )
		return NULL;

	foreach ($categories as $category) {
		$path = '/' . $leaf_path;
		$curcategory = $category;
		while ( ($curcategory->parent != 0) && ($curcategory->parent != $curcategory->term_id) ) {
			$curcategory = get_term($curcategory->parent, 'category');
			if ( is_wp_error( $curcategory ) )
				return $curcategory;
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
	$category = get_term_by('slug', $slug, 'category');
	if ( $category )
		_make_cat_compat($category);

	return $category;
}

// Get the ID of a category from its name
function get_cat_ID($cat_name='General') {
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

function sanitize_category($category, $context = 'display') {
	return sanitize_term($category, 'category', $context);
}

function sanitize_category_field($field, $value, $cat_id, $context) {
	return sanitize_term_field($field, $value, $cat_id, 'category', $context);
}

// Tags

function &get_tags($args = '') {
	$tags = get_terms('post_tag', $args);

	if ( empty($tags) )
		return array();

	$tags = apply_filters('get_tags', $tags, $args);
	return $tags;
}

function &get_tag($tag, $output = OBJECT, $filter = 'raw') {
	return get_term($tag, 'post_tag', $output, $filter);
}

//
// Cache
//

function update_category_cache() {
	return true;
}

function clean_category_cache($id) {
	clean_term_cache($id, 'category');
}

//
// Private helpers
//

function _make_cat_compat( &$category) {
	if ( is_object($category) ) {
		$category->cat_ID = &$category->term_id;
		$category->category_count = &$category->count;
		$category->category_description = &$category->description;
		$category->cat_name = &$category->name;
		$category->category_nicename = &$category->slug;
		$category->category_parent = &$category->parent;
	} else if ( is_array($category) && isset($category['term_id']) ) {
		$category['cat_ID'] = &$category['term_id'];
		$category['category_count'] = &$category['count'];
		$category['category_description'] = &$category['description'];
		$category['cat_name'] = &$category['name'];
		$category['category_nicename'] = &$category['slug'];
		$category['category_parent'] = &$category['parent'];
	}
}

?>

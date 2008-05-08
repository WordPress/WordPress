<?php

//
// Category
//

function category_exists($cat_name) {
	$id = is_term($cat_name, 'category');
	if ( is_array($id) )
		$id = $id['term_id'];
	return $id;
}

function get_category_to_edit( $id ) {
	$category = get_category( $id, OBJECT, 'edit' );
	return $category;
}

function wp_create_category( $cat_name, $parent = 0 ) {
	if ( $id = category_exists($cat_name) )
		return $id;

	return wp_insert_category( array('cat_name' => $cat_name, 'category_parent' => $parent) );
}

function wp_create_categories($categories, $post_id = '') {
	$cat_ids = array ();
	foreach ($categories as $category) {
		if ($id = category_exists($category))
			$cat_ids[] = $id;
		else
			if ($id = wp_create_category($category))
				$cat_ids[] = $id;
	}

	if ($post_id)
		wp_set_post_categories($post_id, $cat_ids);

	return $cat_ids;
}

function wp_delete_category($cat_ID) {
	$cat_ID = (int) $cat_ID;
	$default = get_option('default_category');

	// Don't delete the default cat
	if ( $cat_ID == $default )
		return 0;

	return wp_delete_term($cat_ID, 'category', array('default' => $default));
}

function wp_insert_category($catarr, $wp_error = false) {
	$cat_defaults = array('cat_ID' => 0, 'cat_name' => '', 'category_description' => '', 'category_nicename' => '', 'category_parent' => '');
	$cat_arr = wp_parse_args($cat_arr, $cat_defaults);
	extract($catarr, EXTR_SKIP);

	if ( trim( $cat_name ) == '' ) {
		if ( ! $wp_error )
			return 0;
		else
			return new WP_Error( 'cat_name', __('You did not enter a category name.') );
	}

	$cat_ID = (int) $cat_ID;

	// Are we updating or creating?
	if ( !empty ($cat_ID) )
		$update = true;
	else
		$update = false;

	$name = $cat_name;
	$description = $category_description;
	$slug = $category_nicename;
	$parent = $category_parent;

	$parent = (int) $parent;
	if ( $parent < 0 )
		$parent = 0;

	if ( empty($parent) || !category_exists( $parent ) || ($cat_ID && cat_is_ancestor_of($cat_ID, $parent) ) )
		$parent = 0;

	$args = compact('name', 'slug', 'parent', 'description');

	if ( $update )
		$cat_ID = wp_update_term($cat_ID, 'category', $args);
	else
		$cat_ID = wp_insert_term($cat_name, 'category', $args);

	if ( is_wp_error($cat_ID) ) {
		if ( $wp_error )
			return $cat_ID;
		else
			return 0;
	}

	return $cat_ID['term_id'];
}

function wp_update_category($catarr) {
	$cat_ID = (int) $catarr['cat_ID'];

	if ( $cat_ID == $catarr['category_parent'] )
		return false;

	// First, get all of the original fields
	$category = get_category($cat_ID, ARRAY_A);

	// Escape data pulled from DB.
	$category = add_magic_quotes($category);

	// Merge old and new fields with new fields overwriting old ones.
	$catarr = array_merge($category, $catarr);

	return wp_insert_category($catarr);
}

//
// Tags
//

function get_tags_to_edit( $post_id ) {
	$post_id = (int) $post_id;
	if ( !$post_id )
		return false;

	$tags = wp_get_post_tags($post_id);

	if ( !$tags )
		return false;

	foreach ( $tags as $tag )
		$tag_names[] = $tag->name;
	$tags_to_edit = join( ', ', $tag_names );
	$tags_to_edit = attribute_escape( $tags_to_edit );
	$tags_to_edit = apply_filters( 'tags_to_edit', $tags_to_edit );
	return $tags_to_edit;
}

function tag_exists($tag_name) {
	return is_term($tag_name, 'post_tag');
}

function wp_create_tag($tag_name) {
	if ( $id = tag_exists($tag_name) )
		return $id;

	return wp_insert_term($tag_name, 'post_tag');
}

?>

<?php
/**
 * WordPress Taxonomy Administration API.
 *
 * @package WordPress
 * @subpackage Administration
 */

//
// Category
//

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $cat_name
 * @return unknown
 */
function category_exists($cat_name, $parent = 0) {
	$id = is_term($cat_name, 'category', $parent);
	if ( is_array($id) )
		$id = $id['term_id'];
	return $id;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $id
 * @return unknown
 */
function get_category_to_edit( $id ) {
	$category = get_category( $id, OBJECT, 'edit' );
	return $category;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $cat_name
 * @param unknown_type $parent
 * @return unknown
 */
function wp_create_category( $cat_name, $parent = 0 ) {
	if ( $id = category_exists($cat_name, $parent) )
		return $id;

	return wp_insert_category( array('cat_name' => $cat_name, 'category_parent' => $parent) );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $categories
 * @param unknown_type $post_id
 * @return unknown
 */
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

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $cat_ID
 * @return unknown
 */
function wp_delete_category($cat_ID) {
	$cat_ID = (int) $cat_ID;
	$default = get_option('default_category');

	// Don't delete the default cat
	if ( $cat_ID == $default )
		return 0;

	return wp_delete_term($cat_ID, 'category', array('default' => $default));
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $catarr
 * @param unknown_type $wp_error
 * @return unknown
 */
function wp_insert_category($catarr, $wp_error = false) {
	$cat_defaults = array('cat_ID' => 0, 'cat_name' => '', 'category_description' => '', 'category_nicename' => '', 'category_parent' => '');
	$catarr = wp_parse_args($catarr, $cat_defaults);
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

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $catarr
 * @return unknown
 */
function wp_update_category($catarr) {
	$cat_ID = (int) $catarr['cat_ID'];

	if ( isset($catarr['category_parent']) && ($cat_ID == $catarr['category_parent']) )
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

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $post_id
 * @return unknown
 */
function get_tags_to_edit( $post_id, $taxonomy = 'post_tag' ) {
	return get_terms_to_edit( $post_id, $taxonomy);
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $post_id
 * @return unknown
 */
function get_terms_to_edit( $post_id, $taxonomy = 'post_tag' ) {
	$post_id = (int) $post_id;
	if ( !$post_id )
		return false;

	$tags = wp_get_post_terms($post_id, $taxonomy, array());

	if ( !$tags )
		return false;

	if ( is_wp_error($tags) )
		return $tags;

	foreach ( $tags as $tag )
		$tag_names[] = $tag->name;
	$tags_to_edit = join( ',', $tag_names );
	$tags_to_edit = esc_attr( $tags_to_edit );
	$tags_to_edit = apply_filters( 'terms_to_edit', $tags_to_edit, $taxonomy );

	return $tags_to_edit;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $tag_name
 * @return unknown
 */
function tag_exists($tag_name) {
	return is_term($tag_name, 'post_tag');
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $tag_name
 * @return unknown
 */
function wp_create_tag($tag_name) {
	return wp_create_term( $tag_name, 'post_tag');
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $tag_name
 * @return unknown
 */
function wp_create_term($tag_name, $taxonomy = 'post_tag') {
	if ( $id = is_term($tag_name, $taxonomy) )
		return $id;

	return wp_insert_term($tag_name, $taxonomy);
}

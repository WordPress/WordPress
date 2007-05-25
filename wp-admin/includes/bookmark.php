<?php

function add_link() {
	return edit_link();
}

function edit_link( $link_id = '' ) {
	if (!current_user_can( 'manage_links' ))
		wp_die( __( 'Cheatin&#8217; uh?' ));

	$_POST['link_url'] = wp_specialchars( $_POST['link_url'] );
	$_POST['link_url'] = clean_url($_POST['link_url']);
	$_POST['link_name'] = wp_specialchars( $_POST['link_name'] );
	$_POST['link_image'] = wp_specialchars( $_POST['link_image'] );
	$_POST['link_rss'] = clean_url($_POST['link_rss']);
	$_POST['link_category'] = $_POST['post_category'];

	if ( !empty( $link_id ) ) {
		$_POST['link_id'] = $link_id;
		return wp_update_link( $_POST);
	} else {
		return wp_insert_link( $_POST);
	}
}

function get_default_link_to_edit() {
	if ( isset( $_GET['linkurl'] ) )
		$link->link_url = clean_url( $_GET['linkurl']);
	else
		$link->link_url = '';

	if ( isset( $_GET['name'] ) )
		$link->link_name = attribute_escape( $_GET['name']);
	else
		$link->link_name = '';

	$link->link_visible = 'Y';

	return $link;
}

function wp_delete_link($link_id) {
	global $wpdb;

	do_action('delete_link', $link_id);

	$categories = wp_get_link_cats($link_id);
	if( is_array( $categories ) ) {
		foreach ( $categories as $category ) {
			$wpdb->query("UPDATE $wpdb->categories SET link_count = link_count - 1 WHERE cat_ID = '$category'");
			wp_cache_delete($category, 'category');
			do_action('edit_category', $cat_id);
		}
	}

	$wpdb->query("DELETE FROM $wpdb->link2cat WHERE link_id = '$link_id'");
	return $wpdb->query("DELETE FROM $wpdb->links WHERE link_id = '$link_id'");
	
	do_action('deleted_link', $link_id);
}

function wp_get_link_cats($link_id = 0) {

	$cats = get_object_terms($link_id, 'link_category', 'get=ids');

	return array_unique($cats);
}

function get_link_to_edit( $link_id ) {
	$link = get_link( $link_id );

	$link->link_url         = clean_url($link->link_url);
	$link->link_name        = attribute_escape($link->link_name);
	$link->link_image       = attribute_escape($link->link_image);
	$link->link_description = attribute_escape($link->link_description);
	$link->link_rss         = clean_url($link->link_rss);
	$link->link_rel         = attribute_escape($link->link_rel);
	$link->link_notes       =  wp_specialchars($link->link_notes);
	$link->post_category    = $link->link_category;

	return $link;
}

function wp_insert_link($linkdata) {
	global $wpdb, $current_user;

	extract($linkdata);

	$update = false;

	if ( !empty($link_id) )
		$update = true;

	$link_id = (int) $link_id;

	if( trim( $link_name ) == '' )
		return 0;
	$link_name = apply_filters('pre_link_name', $link_name);

	if( trim( $link_url ) == '' )
		return 0;
	$link_url = apply_filters('pre_link_url', $link_url);

	if ( empty($link_rating) )
		$link_rating = 0;
	else
		$link_rating = (int) $link_rating;

	if ( empty($link_image) )
		$link_image = '';
	$link_image = apply_filters('pre_link_image', $link_image);

	if ( empty($link_target) )
		$link_target = '';
	$link_target = apply_filters('pre_link_target', $link_target);

	if ( empty($link_visible) )
		$link_visible = 'Y';
	$link_visibile = preg_replace('/[^YNyn]/', '', $link_visible);

	if ( empty($link_owner) )
		$link_owner = $current_user->id;
	else
		$link_owner = (int) $link_owner;

	if ( empty($link_notes) )
		$link_notes = '';
	$link_notes = apply_filters('pre_link_notes', $link_notes);

	if ( empty($link_description) )
		$link_description = '';
	$link_description = apply_filters('pre_link_description', $link_description);

	if ( empty($link_rss) )
		$link_rss = '';
	$link_rss = apply_filters('pre_link_rss', $link_rss);

	if ( empty($link_rel) )
		$link_rel = '';
	$link_rel = apply_filters('pre_link_rel', $link_rel);

	// Make sure we set a valid category
	if (0 == count($link_category) || !is_array($link_category)) {
		$link_category = array(get_option('default_link_category'));
	}

	if ( $update ) {
		$wpdb->query("UPDATE $wpdb->links SET link_url='$link_url',
			link_name='$link_name', link_image='$link_image',
			link_target='$link_target',
			link_visible='$link_visible', link_description='$link_description',
			link_rating='$link_rating', link_rel='$link_rel',
			link_notes='$link_notes', link_rss = '$link_rss'
			WHERE link_id='$link_id'");
	} else {
		$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_image, link_target, link_description, link_visible, link_owner, link_rating, link_rel, link_notes, link_rss) VALUES('$link_url','$link_name', '$link_image', '$link_target', '$link_description', '$link_visible', '$link_owner', '$link_rating', '$link_rel', '$link_notes', '$link_rss')");
		$link_id = (int) $wpdb->insert_id;
	}

	wp_set_link_cats($link_id, $link_category);

	if ( $update )
		do_action('edit_link', $link_id);
	else
		do_action('add_link', $link_id);

	return $link_id;
}

function wp_set_link_cats($link_id = 0, $link_categories = array()) {
	// If $link_categories isn't already an array, make it one:
	if (!is_array($link_categories) || 0 == count($link_categories))
		$link_categories = array(get_option('default_link_category'));

	$link_categories = array_map('intval', $link_categories);
	$link_categories = array_unique($link_categories);

	wp_set_object_terms($link_id, $link_categories, 'link_category');
}	// wp_set_link_cats()

function wp_update_link($linkdata) {
	global $wpdb;

	$link_id = (int) $linkdata['link_id'];

	$link = get_link($link_id, ARRAY_A);

	// Escape data pulled from DB.
	$link = add_magic_quotes($link);

	// Passed link category list overwrites existing category list if not empty.
	if ( isset($linkdata['link_category']) && is_array($linkdata['link_category'])
			 && 0 != count($linkdata['link_category']) )
		$link_cats = $linkdata['link_category'];
	else
		$link_cats = $link['link_category'];

	// Merge old and new fields with new fields overwriting old ones.
	$linkdata = array_merge($link, $linkdata);
	$linkdata['link_category'] = $link_cats;

	return wp_insert_link($linkdata);
}

?>
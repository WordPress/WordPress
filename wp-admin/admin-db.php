<?php

function get_users_drafts( $user_id ) {
	global $wpdb;
	$user_id = (int) $user_id;
	$query = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'draft' AND post_author = $user_id ORDER BY ID DESC";
	$query = apply_filters('get_users_drafts', $query);
	return $wpdb->get_results( $query );
}

function get_others_drafts( $user_id ) {
	global $wpdb;
	$user = get_userdata( $user_id );
	$level_key = $wpdb->prefix . 'user_level';

	$editable = get_editable_user_ids( $user_id );

	if( !$editable ) {
		$other_drafts = '';
	} else {
		$editable = join(',', $editable);
		$other_drafts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'draft' AND post_author IN ($editable) AND post_author != '$user_id' ");
	}

	return apply_filters('get_others_drafts', $other_drafts);
}

function get_editable_authors( $user_id ) {
	global $wpdb;

	$editable = get_editable_user_ids( $user_id );

	if( !$editable ) {
		return false;
	} else {
		$editable = join(',', $editable);
		$authors = $wpdb->get_results( "SELECT * FROM $wpdb->users WHERE ID IN ($editable) ORDER BY display_name" );
	}

	return apply_filters('get_editable_authors', $authors);
}

function get_editable_user_ids( $user_id, $exclude_zeros = true ) {
	global $wpdb;

	$user = new WP_User( $user_id );

	if ( ! $user->has_cap('edit_others_posts') ) {
		if ( $user->has_cap('edit_posts') || $exclude_zeros == false )
			return array($user->id);
		else 
			return false;
	}

	$level_key = $wpdb->prefix . 'user_level';

	$query = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$level_key'";
	if ( $exclude_zeros )
		$query .= " AND meta_value != '0'";

	return $wpdb->get_col( $query );
}

function get_author_user_ids() {
	global $wpdb;
	$level_key = $wpdb->prefix . 'user_level';

	$query = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$level_key' AND meta_value != '0'";

	return $wpdb->get_col( $query );
}

function get_nonauthor_user_ids() {
	global $wpdb;
	$level_key = $wpdb->prefix . 'user_level';

	$query = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$level_key' AND meta_value = '0'";

	return $wpdb->get_col( $query );
}

function wp_insert_category($catarr) {
	global $wpdb;

	extract($catarr);

	if( trim( $cat_name ) == '' )
		return 0;

	$cat_ID = (int) $cat_ID;

	// Are we updating or creating?
	if (!empty ($cat_ID))
		$update = true;
	else
		$update = false;

	$cat_name = apply_filters('pre_category_name', $cat_name);
	
	if (empty ($category_nicename))
		$category_nicename = sanitize_title($cat_name);
	else
		$category_nicename = sanitize_title($category_nicename);
	$category_nicename = apply_filters('pre_category_nicename', $category_nicename);

	if (empty ($category_description))
		$category_description = '';
	$category_description = apply_filters('pre_category_description', $category_description);

	$category_parent = (int) $category_parent;
	if ( empty($category_parent) || !get_category( $category_parent ) || $category_parent == $cat_ID )
		$category_parent = 0;

	if ( isset($posts_private) )
		$posts_private = (int) $posts_private;
	else
		$posts_private = 0;

	if ( isset($links_private) )
		$links_private = (int) $links_private;
	else
		$links_private = 0;

	if (!$update) {
		$wpdb->query("INSERT INTO $wpdb->categories (cat_ID, cat_name, category_nicename, category_description, category_parent, links_private, posts_private) VALUES ('0', '$cat_name', '$category_nicename', '$category_description', '$category_parent', '$links_private', '$posts_private')");
		$cat_ID = $wpdb->insert_id;
	} else {
		$wpdb->query ("UPDATE $wpdb->categories SET cat_name = '$cat_name', category_nicename = '$category_nicename', category_description = '$category_description', category_parent = '$category_parent', links_private = '$links_private', posts_private = '$posts_private' WHERE cat_ID = '$cat_ID'");
	}

	if ( $category_nicename == '' ) {
		$category_nicename = sanitize_title($cat_name, $cat_ID );
		$wpdb->query( "UPDATE $wpdb->categories SET category_nicename = '$category_nicename' WHERE cat_ID = '$cat_ID'" );
	}

	wp_cache_delete($cat_ID, 'category');

	if ($update) {
		do_action('edit_category', $cat_ID);
	} else {
		wp_cache_delete('all_category_ids', 'category');
		do_action('create_category', $cat_ID);
		do_action('add_category', $cat_ID);
	}

	return $cat_ID;
}

function wp_update_category($catarr) {
	global $wpdb;

	$cat_ID = (int) $catarr['cat_ID'];

	// First, get all of the original fields
	$category = get_category($cat_ID, ARRAY_A);

	// Escape data pulled from DB.
	$category = add_magic_quotes($category);

	// Merge old and new fields with new fields overwriting old ones.
	$catarr = array_merge($category, $catarr);

	return wp_insert_category($catarr);
}

function wp_delete_category($cat_ID) {
	global $wpdb;

	$cat_ID = (int) $cat_ID;

	// Don't delete the default cat.
	if ( $cat_ID == get_option('default_category') )
		return 0;

	if ( $cat_ID == get_option('default_link_category') )
		return 0;

	$category = get_category($cat_ID);

	$parent = $category->category_parent;

	// Delete the category.
	if ( !$wpdb->query("DELETE FROM $wpdb->categories WHERE cat_ID = '$cat_ID'") )
		return 0;

	// Update children to point to new parent.
	$wpdb->query("UPDATE $wpdb->categories SET category_parent = '$parent' WHERE category_parent = '$cat_ID'");

	// Only set posts and links to the default category if they're not in another category already.
	$default_cat = get_option('default_category');
	$posts = $wpdb->get_col("SELECT post_id FROM $wpdb->post2cat WHERE category_id='$cat_ID'");
	if ( is_array($posts) ) foreach ($posts as $post_id) {
		$cats = wp_get_post_categories($post_id);
		if ( 1 == count($cats) )
			$cats = array($default_cat);
		else
			$cats = array_diff($cats, array($cat_ID));
		wp_set_post_categories($post_id, $cats); 
	}

	$default_link_cat = get_option('default_link_category');
	$links = $wpdb->get_col("SELECT link_id FROM $wpdb->link2cat WHERE category_id='$cat_ID'");
	if ( is_array($links) ) foreach ($links as $link_id) {
		$cats = wp_get_link_cats($link_id);
		if ( 1 == count($cats) )
			$cats = array($default_link_cat);
		else
			$cats = array_diff($cats, array($cat_ID));
		wp_set_link_cats($link_id, $cats); 
	}
	
	wp_cache_delete($cat_ID, 'category');
	wp_cache_delete('all_category_ids', 'category');

	do_action('delete_category', $cat_ID);

	return 1;
}

function wp_create_category($cat_name) {
	$cat_array = compact('cat_name');
	return wp_insert_category($cat_array);
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

function category_exists($cat_name) {
	global $wpdb;
	if (!$category_nicename = sanitize_title($cat_name))
		return 0;

	return $wpdb->get_var("SELECT cat_ID FROM $wpdb->categories WHERE category_nicename = '$category_nicename'");
}

function wp_delete_user($id, $reassign = 'novalue') {
	global $wpdb;

	$id = (int) $id;
	$user = get_userdata($id);

	if ($reassign == 'novalue') {
		$post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_author = $id");

		if ($post_ids) {
			foreach ($post_ids as $post_id)
				wp_delete_post($post_id);
		}

		// Clean links
		$wpdb->query("DELETE FROM $wpdb->links WHERE link_owner = $id");
	} else {
		$reassign = (int) $reassign;
		$wpdb->query("UPDATE $wpdb->posts SET post_author = {$reassign} WHERE post_author = {$id}");
		$wpdb->query("UPDATE $wpdb->links SET link_owner = {$reassign} WHERE link_owner = {$id}");
	}

	// FINALLY, delete user
	do_action('delete_user', $id);

	$wpdb->query("DELETE FROM $wpdb->users WHERE ID = $id");
	$wpdb->query("DELETE FROM $wpdb->usermeta WHERE user_id = '$id'");

	wp_cache_delete($id, 'users');
	wp_cache_delete($user->user_login, 'userlogins');

	return true;
}

function wp_revoke_user($id) {
	$id = (int) $id;
	
	$user = new WP_User($id);
	$user->remove_all_caps();	
}

function wp_insert_link($linkdata) {
	global $wpdb, $current_user;

	extract($linkdata);

	$update = false;

	if ( !empty($link_id) )
		$update = true;

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
		$link_id = $wpdb->insert_id;
	}

	wp_set_link_cats($link_id, $link_category);

	if ( $update )
		do_action('edit_link', $link_id);
	else
		do_action('add_link', $link_id);

	return $link_id;
}

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

function wp_delete_link($link_id) {
	global $wpdb;

	do_action('delete_link', $link_id);
	
	$categories = wp_get_link_cats($link_id);
	if( is_array( $categories ) ) {
		foreach ( $categories as $category ) {
			$wpdb->query("UPDATE $wpdb->categories SET link_count = link_count - 1 WHERE cat_ID = '$category'");
			wp_cache_delete($category, 'category');
		}
	}

	$wpdb->query("DELETE FROM $wpdb->link2cat WHERE link_id = '$link_id'");
	return $wpdb->query("DELETE FROM $wpdb->links WHERE link_id = '$link_id'");
}

function wp_get_link_cats($link_ID = 0) {
	global $wpdb;

	$sql = "SELECT category_id 
		FROM $wpdb->link2cat 
		WHERE link_id = $link_ID 
		ORDER BY category_id";

	$result = $wpdb->get_col($sql);

	if ( !$result )
		$result = array();

	return array_unique($result);
}

function wp_set_link_cats($link_ID = 0, $link_categories = array()) {
	global $wpdb;
	// If $link_categories isn't already an array, make it one:
	if (!is_array($link_categories) || 0 == count($link_categories))
		$link_categories = array(get_option('default_link_category'));

	$link_categories = array_unique($link_categories);

	// First the old categories
	$old_categories = $wpdb->get_col("
		SELECT category_id 
		FROM $wpdb->link2cat 
		WHERE link_id = $link_ID");

	if (!$old_categories) {
		$old_categories = array();
	} else {
		$old_categories = array_unique($old_categories);
	}

	// Delete any?
	$delete_cats = array_diff($old_categories,$link_categories);

	if ($delete_cats) {
		foreach ($delete_cats as $del) {
			$wpdb->query("
				DELETE FROM $wpdb->link2cat 
				WHERE category_id = $del 
					AND link_id = $link_ID 
				");
		}
	}

	// Add any?
	$add_cats = array_diff($link_categories, $old_categories);

	if ($add_cats) {
		foreach ($add_cats as $new_cat) {
			$wpdb->query("
				INSERT INTO $wpdb->link2cat (link_id, category_id) 
				VALUES ($link_ID, $new_cat)");
		}
	}
	
	// Update category counts.
	$all_affected_cats = array_unique(array_merge($link_categories, $old_categories));
	foreach ( $all_affected_cats as $cat_id ) {
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->link2cat, $wpdb->links WHERE $wpdb->links.link_id = $wpdb->link2cat.link_id AND category_id = '$cat_id'");
		$wpdb->query("UPDATE $wpdb->categories SET link_count = '$count' WHERE cat_ID = '$cat_id'");
		wp_cache_delete($cat_id, 'category');
	}
}	// wp_set_link_cats()

function post_exists($title, $content = '', $post_date = '') {
	global $wpdb;

	if (!empty ($post_date))
		$post_date = "AND post_date = '$post_date'";

	if (!empty ($title))
		return $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '$title' $post_date");
	else
		if (!empty ($content))
			return $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_content = '$content' $post_date");

	return 0;
}

function comment_exists($comment_author, $comment_date) {
	global $wpdb;

	return $wpdb->get_var("SELECT comment_post_ID FROM $wpdb->comments
			WHERE comment_author = '$comment_author' AND comment_date = '$comment_date'");
}

?>

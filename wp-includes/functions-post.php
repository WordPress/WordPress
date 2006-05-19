<?php

/**** DB Functions ****/

/*
 * generic function for inserting data into the posts table.
 */
function wp_insert_post($postarr = array()) {
	global $wpdb, $wp_rewrite, $allowedtags, $user_ID;

	if ( is_object($postarr) )
		$postarr = get_object_vars($postarr);

	// export array as variables
	extract($postarr);

	// Are we updating or creating?
	$update = false;
	if ( !empty($ID) ) {
		$update = true;
		$post = & get_post($ID);
		$previous_status = $post->post_status;
	}

	// Get the basics.
	$post_content    = apply_filters('content_save_pre',   $post_content);
	$post_excerpt    = apply_filters('excerpt_save_pre',   $post_excerpt);
	$post_title      = apply_filters('title_save_pre',     $post_title);
	$post_category   = apply_filters('category_save_pre',  $post_category);
	$post_status     = apply_filters('status_save_pre',    $post_status);
	$post_name       = apply_filters('name_save_pre',      $post_name);
	$comment_status  = apply_filters('comment_status_pre', $comment_status);
	$ping_status     = apply_filters('ping_status_pre',    $ping_status);

	// Make sure we set a valid category
	if (0 == count($post_category) || !is_array($post_category)) {
		$post_category = array(get_option('default_category'));
	}
	$post_cat = $post_category[0];

	if ( empty($post_author) )
		$post_author = $user_ID;

	if ( empty($post_status) )
		$post_status = 'draft';

	if ( empty($post_type) )
		$post_type = 'post';

	// Get the post ID.
	if ( $update )
		$post_ID = $ID;

	// Create a valid post name.  Drafts are allowed to have an empty
	// post name.
	if ( empty($post_name) ) {
		if ( 'draft' != $post_status )
			$post_name = sanitize_title($post_title);
	} else {
		$post_name = sanitize_title($post_name);
	}


	// If the post date is empty (due to having been new or a draft) and status is not 'draft', set date to now
	if (empty($post_date)) {
		if ( 'draft' != $post_status )
			$post_date = current_time('mysql');
	}

	if (empty($post_date_gmt)) {
		if ( 'draft' != $post_status )
			$post_date_gmt = get_gmt_from_date($post_date);
	}

	if ( 'publish' == $post_status ) {
		$now = gmdate('Y-m-d H:i:59');
		if ( mysql2date('U', $post_date_gmt) > mysql2date('U', $now) )
			$post_status = 'future';
	}

	if ( empty($comment_status) ) {
		if ( $update )
			$comment_status = 'closed';
		else
			$comment_status = get_settings('default_comment_status');
	}
	if ( empty($ping_status) )
		$ping_status = get_settings('default_ping_status');
	if ( empty($post_pingback) )
		$post_pingback = get_option('default_pingback_flag');

	if ( isset($to_ping) )
		$to_ping = preg_replace('|\s+|', "\n", $to_ping);
	else
		$to_ping = '';

	if ( ! isset($pinged) )
		$pinged = '';

	if ( isset($post_parent) )
		$post_parent = (int) $post_parent;
	else
		$post_parent = 0;

	if ( isset($menu_order) )
		$menu_order = (int) $menu_order;
	else
		$menu_order = 0;

	if ( !isset($post_password) )
		$post_password = '';

	if ( 'draft' != $post_status ) {
		$post_name_check = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE post_name = '$post_name' AND post_type = '$post_type' AND ID != '$post_ID' AND post_parent = '$post_parent' LIMIT 1");

		if ($post_name_check) {
			$suffix = 2;
			while ($post_name_check) {
				$alt_post_name = $post_name . "-$suffix";
				$post_name_check = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE post_name = '$alt_post_name' AND post_type = '$post_type' AND ID != '$post_ID' AND post_parent = '$post_parent' LIMIT 1");
				$suffix++;
			}
			$post_name = $alt_post_name;
		}
	}

	if ($update) {
		$wpdb->query(
			"UPDATE IGNORE $wpdb->posts SET
			post_author = '$post_author',
			post_date = '$post_date',
			post_date_gmt = '$post_date_gmt',
			post_content = '$post_content',
			post_content_filtered = '$post_content_filtered',
			post_title = '$post_title',
			post_excerpt = '$post_excerpt',
			post_status = '$post_status',
			post_type = '$post_type',
			comment_status = '$comment_status',
			ping_status = '$ping_status',
			post_password = '$post_password',
			post_name = '$post_name',
			to_ping = '$to_ping',
			pinged = '$pinged',
			post_modified = '".current_time('mysql')."',
			post_modified_gmt = '".current_time('mysql',1)."',
			post_parent = '$post_parent',
			menu_order = '$menu_order'
			WHERE ID = $post_ID");
	} else {
		$wpdb->query(
			"INSERT IGNORE INTO $wpdb->posts
			(post_author, post_date, post_date_gmt, post_content, post_content_filtered, post_title, post_excerpt,  post_status, post_type, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_parent, menu_order, post_mime_type)
			VALUES
			('$post_author', '$post_date', '$post_date_gmt', '$post_content', '$post_content_filtered', '$post_title', '$post_excerpt', '$post_status', '$post_type', '$comment_status', '$ping_status', '$post_password', '$post_name', '$to_ping', '$pinged', '$post_date', '$post_date_gmt', '$post_parent', '$menu_order', '$post_mime_type')");
			$post_ID = $wpdb->insert_id;
	}

	if ( empty($post_name) && 'draft' != $post_status ) {
		$post_name = sanitize_title($post_title, $post_ID);
		$wpdb->query( "UPDATE $wpdb->posts SET post_name = '$post_name' WHERE ID = '$post_ID'" );
	}

	wp_set_post_cats('', $post_ID, $post_category);

	if ( 'page' == $post_type ) {
		clean_page_cache($post_ID);
		wp_cache_delete($post_ID, 'pages');
	} else {
		clean_post_cache($post_ID);
	}

	// Set GUID
	if ( ! $update )
		$wpdb->query("UPDATE $wpdb->posts SET guid = '" . get_permalink($post_ID) . "' WHERE ID = '$post_ID'");

	if ( $update) {
		if ($previous_status != 'publish' && $post_status == 'publish') {
			// Reset GUID if transitioning to publish.
			$wpdb->query("UPDATE $wpdb->posts SET guid = '" . get_permalink($post_ID) . "' WHERE ID = '$post_ID'");
			do_action('private_to_published', $post_ID);
		}

		do_action('edit_post', $post_ID);
	}

	if ($post_status == 'publish' && $post_type == 'post') {
		do_action('publish_post', $post_ID);

		if ( !defined('WP_IMPORTING') ) {
			if ( $post_pingback )
				$result = $wpdb->query("
					INSERT INTO $wpdb->postmeta 
					(post_id,meta_key,meta_value) 
					VALUES ('$post_ID','_pingme','1')
				");
			$result = $wpdb->query("
				INSERT INTO $wpdb->postmeta 
				(post_id,meta_key,meta_value) 
				VALUES ('$post_ID','_encloseme','1')
			");
			wp_schedule_single_event(time(), 'do_pings');
		}
	} else if ($post_type == 'page') {
		wp_cache_delete('all_page_ids', 'pages');
		$wp_rewrite->flush_rules();

		if ( !empty($page_template) )
			if ( ! update_post_meta($post_ID, '_wp_page_template',  $page_template))
				add_post_meta($post_ID, '_wp_page_template',  $page_template, true);
				
		if ( $post_status == 'publish' )
			do_action('publish_page', $post_ID);
	}

	if ( 'future' == $post_status ) {
		wp_schedule_single_event(mysql2date('U', $post_date), 'publish_future_post', $post_ID);
	}
		
	do_action('save_post', $post_ID);
	do_action('wp_insert_post', $post_ID);

	return $post_ID;
}

function wp_insert_attachment($object, $file = false, $post_parent = 0) {
	global $wpdb, $user_ID;

	if ( is_object($object) )
		$object = get_object_vars($object);

	// Export array as variables
	extract($object);

	// Get the basics.
	$post_content    = apply_filters('content_save_pre',   $post_content);
	$post_excerpt    = apply_filters('excerpt_save_pre',   $post_excerpt);
	$post_title      = apply_filters('title_save_pre',     $post_title);
	$post_category   = apply_filters('category_save_pre',  $post_category);
	$post_name       = apply_filters('name_save_pre',      $post_name);
	$comment_status  = apply_filters('comment_status_pre', $comment_status);
	$ping_status     = apply_filters('ping_status_pre',    $ping_status);
	$post_mime_type  = apply_filters('post_mime_type_pre', $post_mime_type);

	// Make sure we set a valid category
	if (0 == count($post_category) || !is_array($post_category)) {
		$post_category = array(get_option('default_category'));
	}
	$post_cat = $post_category[0];

	if ( empty($post_author) )
		$post_author = $user_ID;

	$post_type = 'attachment';
	$post_status = 'inherit';

	// Are we updating or creating?
	$update = false;
	if ( !empty($ID) ) {
		$update = true;
		$post_ID = $ID;
	}

	// Create a valid post name.
	if ( empty($post_name) )
		$post_name = sanitize_title($post_title);
	else
		$post_name = sanitize_title($post_name);

	if (empty($post_date))
		$post_date = current_time('mysql');
	if (empty($post_date_gmt)) 
		$post_date_gmt = current_time('mysql', 1);

	if ( empty($comment_status) ) {
		if ( $update )
			$comment_status = 'closed';
		else
			$comment_status = get_settings('default_comment_status');
	}
	if ( empty($ping_status) )
		$ping_status = get_settings('default_ping_status');
	if ( empty($post_pingback) )
		$post_pingback = get_option('default_pingback_flag');

	if ( isset($to_ping) )
		$to_ping = preg_replace('|\s+|', "\n", $to_ping);
	else
		$to_ping = '';

	if ( isset($post_parent) )
		$post_parent = (int) $post_parent;
	else
		$post_parent = 0;

	if ( isset($menu_order) )
		$menu_order = (int) $menu_order;
	else
		$menu_order = 0;

	if ( !isset($post_password) )
		$post_password = '';

	if ( isset($to_ping) )
		$to_ping = preg_replace('|\s+|', "\n", $to_ping);
	else
		$to_ping = '';

	if ( ! isset($pinged) )
		$pinged = '';

	if ($update) {
		$wpdb->query(
			"UPDATE $wpdb->posts SET
			post_author = '$post_author',
			post_date = '$post_date',
			post_date_gmt = '$post_date_gmt',
			post_content = '$post_content',
			post_content_filtered = '$post_content_filtered',
			post_title = '$post_title',
			post_excerpt = '$post_excerpt',
			post_status = '$post_status',
			post_type = '$post_type',
			comment_status = '$comment_status',
			ping_status = '$ping_status',
			post_password = '$post_password',
			post_name = '$post_name',
			to_ping = '$to_ping',
			pinged = '$pinged',
			post_modified = '".current_time('mysql')."',
			post_modified_gmt = '".current_time('mysql',1)."',
			post_parent = '$post_parent',
			menu_order = '$menu_order',
			post_mime_type = '$post_mime_type',
			guid = '$guid'
			WHERE ID = $post_ID");
	} else {
		$wpdb->query(
			"INSERT INTO $wpdb->posts
			(post_author, post_date, post_date_gmt, post_content, post_content_filtered, post_title, post_excerpt,  post_status, post_type, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_parent, menu_order, post_mime_type, guid)
			VALUES
			('$post_author', '$post_date', '$post_date_gmt', '$post_content', '$post_content_filtered', '$post_title', '$post_excerpt', '$post_status', '$post_type', '$comment_status', '$ping_status', '$post_password', '$post_name', '$to_ping', '$pinged', '$post_date', '$post_date_gmt', '$post_parent', '$menu_order', '$post_mime_type', '$guid')");
			$post_ID = $wpdb->insert_id;
	}

	if ( empty($post_name) ) {
		$post_name = sanitize_title($post_title, $post_ID);
		$wpdb->query( "UPDATE $wpdb->posts SET post_name = '$post_name' WHERE ID = '$post_ID'" );
	}

	wp_set_post_cats('', $post_ID, $post_category);

	if ( $file )
		add_post_meta($post_ID, '_wp_attached_file', $file );

	clean_post_cache($post_ID);

	if ( $update) {
		do_action('edit_attachment', $post_ID);
	} else {
		do_action('add_attachment', $post_ID);
	}

	return $post_ID;
}

function wp_delete_attachment($postid) {
	global $wpdb;
	$postid = (int) $postid;

	if ( !$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = '$postid'") )
		return $post;

	if ( 'attachment' != $post->post_type )
		return false;

	$meta = get_post_meta($postid, '_wp_attachment_metadata', true);
	$file = get_post_meta($postid, '_wp_attached_file', true);

	$wpdb->query("DELETE FROM $wpdb->posts WHERE ID = '$postid'");

	$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_post_ID = '$postid'");

	$wpdb->query("DELETE FROM $wpdb->post2cat WHERE post_id = '$postid'");

	$wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id = '$postid'");

	if ( ! empty($meta['thumb']) ) {
		// Don't delete the thumb if another attachment uses it
		if (! $wpdb->get_row("SELECT meta_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attachment_metadata' AND meta_value LIKE '%".$wpdb->escape($meta['thumb'])."%' AND post_id <> $postid")) {
			$thumbfile = str_replace(basename($file), $meta['thumb'], $file);
			$thumbfile = apply_filters('wp_delete_file', $thumbfile);
			@ unlink($thumbfile);
		}
	}

	$file = apply_filters('wp_delete_file', $file);

	if ( ! empty($file) )
		@ unlink($file);

	do_action('delete_attachment', $postid);

	return $post;
}

function wp_get_single_post($postid = 0, $mode = OBJECT) {
	global $wpdb;

	$post = get_post($postid, $mode);

	// Set categories
	if($mode == OBJECT) {
		$post->post_category = wp_get_post_cats('',$postid);
	} 
	else {
		$post['post_category'] = wp_get_post_cats('',$postid);
	}

	return $post;
}

function wp_get_recent_posts($num = 10) {
	global $wpdb;

	// Set the limit clause, if we got a limit
	if ($num) {
		$limit = "LIMIT $num";
	}

	$sql = "SELECT * FROM $wpdb->posts WHERE post_type = 'post' ORDER BY post_date DESC $limit";
	$result = $wpdb->get_results($sql,ARRAY_A);

	return $result?$result:array();
}

function wp_update_post($postarr = array()) {
	global $wpdb;

	if ( is_object($postarr) )
		$postarr = get_object_vars($postarr);

	// First, get all of the original fields
	$post = wp_get_single_post($postarr['ID'], ARRAY_A);

	// Escape data pulled from DB.
	$post = add_magic_quotes($post);

	// Passed post category list overwrites existing category list if not empty.
 	if ( isset($postarr['post_category']) && is_array($postarr['post_category'])
			 && 0 != count($postarr['post_category']) )
 		$post_cats = $postarr['post_category'];
 	else 
 		$post_cats = $post['post_category'];

	// Drafts shouldn't be assigned a date unless explicitly done so by the user
	if ( 'draft' == $post['post_status'] && empty($postarr['edit_date']) && empty($postarr['post_date']) && 
	     ('0000-00-00 00:00:00' == $post['post_date']) )
		$clear_date = true;
	else
		$clear_date = false;

 	// Merge old and new fields with new fields overwriting old ones.
 	$postarr = array_merge($post, $postarr);
 	$postarr['post_category'] = $post_cats;
	if ( $clear_date ) {
		$postarr['post_date'] = '';
		$postarr['post_date_gmt'] = '';
	}

	if ($postarr['post_type'] == 'attachment')
		return wp_insert_attachment($postarr);

	return wp_insert_post($postarr);
}

function wp_publish_post($post_id) {
	$post = get_post($post_id);

	if ( empty($post) )
		return;

	if ( 'publish' == $post->post_status )
		return;

	return wp_update_post(array('post_status' => 'publish', 'ID' => $post_id));	
}

function wp_get_post_cats($blogid = '1', $post_ID = 0) {
	global $wpdb;

	$post_ID = (int) $post_ID;

	$sql = "SELECT category_id 
		FROM $wpdb->post2cat 
		WHERE post_id = '$post_ID' 
		ORDER BY category_id";

	$result = $wpdb->get_col($sql);

	if ( !$result )
		$result = array();

	return array_unique($result);
}

function wp_set_post_cats($blogid = '1', $post_ID = 0, $post_categories = array()) {
	global $wpdb;
	// If $post_categories isn't already an array, make it one:
	if (!is_array($post_categories) || 0 == count($post_categories))
		$post_categories = array(get_option('default_category'));

	$post_categories = array_unique($post_categories);

	// First the old categories
	$old_categories = $wpdb->get_col("
		SELECT category_id 
		FROM $wpdb->post2cat 
		WHERE post_id = $post_ID");

	if (!$old_categories) {
		$old_categories = array();
	} else {
		$old_categories = array_unique($old_categories);
	}

	// Delete any?
	$delete_cats = array_diff($old_categories,$post_categories);

	if ($delete_cats) {
		foreach ($delete_cats as $del) {
			$wpdb->query("
				DELETE FROM $wpdb->post2cat 
				WHERE category_id = $del 
					AND post_id = $post_ID 
				");
		}
	}

	// Add any?
	$add_cats = array_diff($post_categories, $old_categories);

	if ($add_cats) {
		foreach ($add_cats as $new_cat) {
			$wpdb->query("
				INSERT INTO $wpdb->post2cat (post_id, category_id) 
				VALUES ($post_ID, $new_cat)");
		}
	}

	// Update category counts.
	$all_affected_cats = array_unique(array_merge($post_categories, $old_categories));
	foreach ( $all_affected_cats as $cat_id ) {
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->post2cat, $wpdb->posts WHERE $wpdb->posts.ID=$wpdb->post2cat.post_id AND post_status = 'publish' AND post_type = 'post' AND category_id = '$cat_id'");
		$wpdb->query("UPDATE $wpdb->categories SET category_count = '$count' WHERE cat_ID = '$cat_id'");
		wp_cache_delete($cat_id, 'category');
	}
}	// wp_set_post_cats()

function wp_delete_post($postid = 0) {
	global $wpdb, $wp_rewrite;
	$postid = (int) $postid;

	if ( !$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = $postid") )
		return $post;

	if ( 'attachment' == $post->post_type )
		return wp_delete_attachment($postid);

	do_action('delete_post', $postid);

	if ( 'publish' == $post->post_status && 'post' == $post->post_type ) {
		$categories = wp_get_post_cats('', $post->ID);
		if( is_array( $categories ) ) {
			foreach ( $categories as $cat_id ) {
				$wpdb->query("UPDATE $wpdb->categories SET category_count = category_count - 1 WHERE cat_ID = '$cat_id'");
				wp_cache_delete($cat_id, 'category');
			}
		}
	}

	if ( 'page' == $post->post_type )
		$wpdb->query("UPDATE $wpdb->posts SET post_parent = $post->post_parent WHERE post_parent = $postid AND post_type = 'page'");

	$wpdb->query("DELETE FROM $wpdb->posts WHERE ID = $postid");

	$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_post_ID = $postid");

	$wpdb->query("DELETE FROM $wpdb->post2cat WHERE post_id = $postid");

	$wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id = $postid");

	if ( 'page' == $post->type ) {
		wp_cache_delete('all_page_ids', 'pages');
		$wp_rewrite->flush_rules();
	}

	return $post;
}

/**** /DB Functions ****/

/**** Misc ****/

// get permalink from post ID
function post_permalink($post_id = 0, $mode = '') { // $mode legacy
	return get_permalink($post_id);
}

// Get the name of a category from its ID
function get_cat_name($cat_id) {
	global $wpdb;

	$cat_id -= 0; 	// force numeric
	$name = $wpdb->get_var("SELECT cat_name FROM $wpdb->categories WHERE cat_ID=$cat_id");

	return $name;
}

// Get the ID of a category from its name
function get_cat_ID($cat_name='General') {
	global $wpdb;

	$cid = $wpdb->get_var("SELECT cat_ID FROM $wpdb->categories WHERE cat_name='$cat_name'");

	return $cid?$cid:1;	// default to cat 1
}

// Get author's preferred display name
function get_author_name( $auth_id ) {
	$authordata = get_userdata( $auth_id );

	return $authordata->display_name;
}

// get extended entry info (<!--more-->)
function get_extended($post) {
	list($main,$extended) = explode('<!--more-->', $post, 2);

	// Strip leading and trailing whitespace
	$main = preg_replace('/^[\s]*(.*)[\s]*$/','\\1',$main);
	$extended = preg_replace('/^[\s]*(.*)[\s]*$/','\\1',$extended);

	return array('main' => $main, 'extended' => $extended);
}

// do trackbacks for a list of urls
// borrowed from edit.php
// accepts a comma-separated list of trackback urls and a post id
function trackback_url_list($tb_list, $post_id) {
	if (!empty($tb_list)) {
		// get post data
		$postdata = wp_get_single_post($post_id, ARRAY_A);

		// import postdata as variables
		extract($postdata);

		// form an excerpt
		$excerpt = strip_tags($post_excerpt?$post_excerpt:$post_content);

		if (strlen($excerpt) > 255) {
			$excerpt = substr($excerpt,0,252) . '...';
		}

		$trackback_urls = explode(',', $tb_list);
		foreach($trackback_urls as $tb_url) {
		    $tb_url = trim($tb_url);
		    trackback($tb_url, stripslashes($post_title), $excerpt, $post_id);
		}
    }
}

function wp_blacklist_check($author, $email, $url, $comment, $user_ip, $user_agent) {
	global $wpdb;

	do_action('wp_blacklist_check', $author, $email, $url, $comment, $user_ip, $user_agent);

	if ( preg_match_all('/&#(\d+);/', $comment . $author . $url, $chars) ) {
		foreach ($chars[1] as $char) {
			// If it's an encoded char in the normal ASCII set, reject
			if ( 38 == $char )
				continue; // Unless it's &
			if ($char < 128)
				return true;
		}
	}

	$mod_keys = trim( get_settings('blacklist_keys') );
	if ('' == $mod_keys )
		return false; // If moderation keys are empty
	$words = explode("\n", $mod_keys );

	foreach ($words as $word) {
		$word = trim($word);

		// Skip empty lines
		if ( empty($word) ) { continue; }

		// Do some escaping magic so that '#' chars in the 
		// spam words don't break things:
		$word = preg_quote($word, '#');

		$pattern = "#$word#i"; 
		if ( preg_match($pattern, $author    ) ) return true;
		if ( preg_match($pattern, $email     ) ) return true;
		if ( preg_match($pattern, $url       ) ) return true;
		if ( preg_match($pattern, $comment   ) ) return true;
		if ( preg_match($pattern, $user_ip   ) ) return true;
		if ( preg_match($pattern, $user_agent) ) return true;
	}

	if ( isset($_SERVER['REMOTE_ADDR']) ) {
		if ( wp_proxy_check($_SERVER['REMOTE_ADDR']) ) return true;
	}

	return false;
}

function wp_proxy_check($ipnum) {
	if ( get_option('open_proxy_check') && isset($ipnum) ) {
		$rev_ip = implode( '.', array_reverse( explode( '.', $ipnum ) ) );
		$lookup = $rev_ip . '.opm.blitzed.org.';
		if ( $lookup != gethostbyname( $lookup ) )
			return true;
	}

	return false;
}

function do_trackbacks($post_id) {
	global $wpdb;

	$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = $post_id");
	$to_ping = get_to_ping($post_id);
	$pinged  = get_pung($post_id);
	if ( empty($to_ping) ) {
		$wpdb->query("UPDATE $wpdb->posts SET to_ping = '' WHERE ID = '$post_id'");
		return;
	}

	if (empty($post->post_excerpt))
		$excerpt = apply_filters('the_content', $post->post_content);
	else
		$excerpt = apply_filters('the_excerpt', $post->post_excerpt);
	$excerpt = str_replace(']]>', ']]&gt;', $excerpt);
	$excerpt = strip_tags($excerpt);
	if ( function_exists('mb_strcut') ) // For international trackbacks
    	$excerpt = mb_strcut($excerpt, 0, 252, get_settings('blog_charset')) . '...';
	else
		$excerpt = substr($excerpt, 0, 252) . '...';

	$post_title = apply_filters('the_title', $post->post_title);
	$post_title = strip_tags($post_title);

	if ($to_ping) : foreach ($to_ping as $tb_ping) :
		$tb_ping = trim($tb_ping);
		if ( !in_array($tb_ping, $pinged) ) {
			trackback($tb_ping, $post_title, $excerpt, $post_id);
			$pinged[] = $tb_ping;
		} else {
			$wpdb->query("UPDATE $wpdb->posts SET to_ping = TRIM(REPLACE(to_ping, '$tb_ping', '')) WHERE ID = '$post_id'");
		}
	endforeach; endif;
}

function get_pung($post_id) { // Get URIs already pung for a post
	global $wpdb;
	$pung = $wpdb->get_var("SELECT pinged FROM $wpdb->posts WHERE ID = $post_id");
	$pung = trim($pung);
	$pung = preg_split('/\s/', $pung);
	$pung = apply_filters('get_pung', $pung);
	return $pung;
}

function get_enclosed($post_id) { // Get enclosures already enclosed for a post
	global $wpdb;
	$custom_fields = get_post_custom( $post_id );
	$pung = array();
	if ( !is_array( $custom_fields ) )
		return $pung;

	foreach ( $custom_fields as $key => $val ) {
		if ( 'enclosure' != $key || !is_array( $val ) )
			continue;
		foreach( $val as $enc ) {
			$enclosure = split( "\n", $enc );
			$pung[] = trim( $enclosure[ 0 ] );
		}
	}
	$pung = apply_filters('get_enclosed', $pung);
	return $pung;
}

function get_to_ping($post_id) { // Get any URIs in the todo list
	global $wpdb;
	$to_ping = $wpdb->get_var("SELECT to_ping FROM $wpdb->posts WHERE ID = $post_id");
	$to_ping = trim($to_ping);
	$to_ping = preg_split('/\s/', $to_ping, -1, PREG_SPLIT_NO_EMPTY);
	$to_ping = apply_filters('get_to_ping',  $to_ping);
	return $to_ping;
}

function add_ping($post_id, $uri) { // Add a URI to those already pung
	global $wpdb;
	$pung = $wpdb->get_var("SELECT pinged FROM $wpdb->posts WHERE ID = $post_id");
	$pung = trim($pung);
	$pung = preg_split('/\s/', $pung);
	$pung[] = $uri;
	$new = implode("\n", $pung);
	$new = apply_filters('add_ping', $new);
	return $wpdb->query("UPDATE $wpdb->posts SET pinged = '$new' WHERE ID = $post_id");
}

//fetches the pages returned as a FLAT list, but arranged in order of their hierarchy, i.e., child parents
//immediately follow their parents
function get_page_hierarchy($posts, $parent = 0) {
	$result = array ( );
	if ($posts) { foreach ($posts as $post) {
		if ($post->post_parent == $parent) {
			$result[$post->ID] = $post->post_name;
			$children = get_page_hierarchy($posts, $post->ID);
			$result += $children; //append $children to $result
		}
	} }
	return $result;
}

function generate_page_uri_index() {
	global $wpdb;

	//get pages in order of hierarchy, i.e. children after parents
	$posts = get_page_hierarchy($wpdb->get_results("SELECT ID, post_name, post_parent FROM $wpdb->posts WHERE post_type = 'page'"));
	//now reverse it, because we need parents after children for rewrite rules to work properly
	$posts = array_reverse($posts, true);

	$page_uris = array();
	$page_attachment_uris = array();

	if ($posts) {

		foreach ($posts as $id => $post) {

			// URI => page name
			$uri = get_page_uri($id);
			$attachments = $wpdb->get_results("SELECT ID, post_name, post_parent FROM $wpdb->posts WHERE post_type = 'attachment' AND post_parent = '$id'");
			if ( $attachments ) {
				foreach ( $attachments as $attachment ) {
					$attach_uri = get_page_uri($attachment->ID);
					$page_attachment_uris[$attach_uri] = $attachment->ID;
				}
			}

			$page_uris[$uri] = $id;
		}

		update_option('page_uris', $page_uris);

		if ( $page_attachment_uris )
			update_option('page_attachment_uris', $page_attachment_uris);
	}
}

function get_post_status($ID = '') {
	$post = get_post($ID);

	if ( is_object($post) ) {
		if ( ('attachment' == $post->post_type) && $post->post_parent && ($post->ID != $post->post_parent) )
			return get_post_status($post->post_parent);
		else
			return $post->post_status;
	}

	return false;
}

function get_post_type($post = false) {
	global $wpdb, $posts;

	if ( false === $post )
		$post = $posts[0];
	elseif ( (int) $post )
		$post = get_post($post, OBJECT);

	if ( is_object($post) )
		return $post->post_type;

	return false;
}

// Takes a post ID, returns its mime type.
function get_post_mime_type($ID = '') {
	$post = & get_post($ID);

	if ( is_object($post) )
		return $post->post_mime_type;

	return false;
}

function get_attached_file($attachment_id) {
	return get_post_meta($attachment_id, '_wp_attached_file', true);
}

function wp_mkdir_p($target) {
	// from php.net/mkdir user contributed notes
	if (file_exists($target)) {
		if (! @ is_dir($target))
			return false;
		else
			return true;
	}

	// Attempting to create the directory may clutter up our display.
	if (@ mkdir($target)) {
		$stat = @ stat(dirname($target));
		$dir_perms = $stat['mode'] & 0007777;  // Get the permission bits.
		@ chmod($target, $dir_perms);
		return true;
	} else {
		if ( is_dir(dirname($target)) )
			return false;
	}

	// If the above failed, attempt to create the parent node, then try again.
	if (wp_mkdir_p(dirname($target)))
		return wp_mkdir_p($target);

	return false;
}

// Returns an array containing the current upload directory's path and url, or an error message.
function wp_upload_dir() {
	$siteurl = get_settings('siteurl');
	//prepend ABSPATH to $dir and $siteurl to $url if they're not already there
	$path = str_replace(ABSPATH, '', trim(get_settings('upload_path')));
	$dir = ABSPATH . $path;
	$url = trailingslashit($siteurl) . $path;

	if ( $dir == ABSPATH ) { //the option was empty
		$dir = ABSPATH . 'wp-content/uploads';
	}

	if ( defined('UPLOADS') ) {
		$dir = ABSPATH . UPLOADS;
		$url = trailingslashit($siteurl) . UPLOADS;
	}

	if ( get_settings('uploads_use_yearmonth_folders')) {
		// Generate the yearly and monthly dirs
		$time = current_time( 'mysql' );
		$y = substr( $time, 0, 4 );
		$m = substr( $time, 5, 2 );
		$dir = $dir . "/$y/$m";
		$url = $url . "/$y/$m";
	}

	// Make sure we have an uploads dir
	if ( ! wp_mkdir_p( $dir ) ) {
		$message = sprintf(__('Unable to create directory %s. Is its parent directory writable by the server?'), $dir);
		return array('error' => $message);
	}

    $uploads = array('path' => $dir, 'url' => $url, 'error' => false);
	return apply_filters('upload_dir', $uploads);
}

function wp_upload_bits($name, $type, $bits) {
	if ( empty($name) )
		return array('error' => "Empty filename");

	$upload = wp_upload_dir();

	if ( $upload['error'] !== false )
		return $upload;

	$number = '';
	$filename = $name;
	$path_parts = pathinfo($filename);
	$ext = $path_parts['extension'];
	if ( empty($ext) )
		$ext = '';
	else
		$ext = ".$ext";
	while ( file_exists($upload['path'] . "/$filename") ) {
		if ( '' == "$number$ext" )
			$filename = $filename . ++$number . $ext;
		else
			$filename = str_replace("$number$ext", ++$number . $ext, $filename);
	}

	$new_file = $upload['path'] . "/$filename";
	if ( ! wp_mkdir_p( dirname($new_file) ) ) {
		$message = sprintf(__('Unable to create directory %s. Is its parent directory writable by the server?'), dirname($new_file));
		return array('error' => $message);
	}

	$ifp = @ fopen($new_file, 'wb');
	if ( ! $ifp )
		return array('error' => "Could not write file $new_file.");

	$success = @ fwrite($ifp, $bits);
	fclose($ifp);
	// Set correct file permissions
	$stat = @ stat(dirname($new_file));
	$perms = $stat['mode'] & 0007777;
	$perms = $perms & 0000666;
	@ chmod($new_file, $perms);

	// Compute the URL
	$url = $upload['url'] . "/$filename";

	return array('file' => $new_file, 'url' => $url, 'error' => false);
}

function do_all_pings() {
	global $wpdb;

	// Do pingbacks
	while ($ping = $wpdb->get_row("SELECT * FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_pingme' LIMIT 1")) {
		$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id = {$ping->ID} AND meta_key = '_pingme';");
		pingback($ping->post_content, $ping->ID);
	}
	
	// Do Enclosures
	while ($enclosure = $wpdb->get_row("SELECT * FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_encloseme' LIMIT 1")) {
		$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id = {$enclosure->ID} AND meta_key = '_encloseme';");
		do_enclose($enclosure->post_content, $enclosure->ID);
	}

	// Do Trackbacks
	$trackbacks = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE CHAR_LENGTH(TRIM(to_ping)) > 7 AND post_status = 'publish'");
	if ( is_array($trackbacks) ) {
		foreach ( $trackbacks as $trackback ) {
			do_trackbacks($trackback->ID);
		}
	}

	//Do Update Services/Generic Pings
	generic_ping();
}

/**
 * Places two script links in <head>: one to get tinyMCE (big), one to configure and start it (small)
 */
function tinymce_include() {
	$ver = '04162006';
	$src1 = get_settings('siteurl') . "/wp-includes/js/tinymce/tiny_mce_gzip.php?ver=$ver";
	$src2 = get_settings('siteurl') . "/wp-includes/js/tinymce/tiny_mce_config.php?ver=$ver";

	echo "<script type='text/javascript' src='$src1'></script>\n";
	echo "<script type='text/javascript' src='$src2'></script>\n";
}

/**
 * Places a textarea according to the current user's preferences, filled with $content.
 * Also places a script block that enables tabbing between Title and Content.
 *
 * @param string Editor contents
 * @param string (optional) Previous form field's ID (for tabbing support)
 */
function the_editor($content, $id = 'content', $prev_id = 'title') {
	$rows = get_settings('default_post_edit_rows');
	if (($rows < 3) || ($rows > 100))
		$rows = 12;

	$rows = "rows='$rows'";

	the_quicktags();

	if ( user_can_richedit() )
		add_filter('the_editor_content', 'wp_richedit_pre');

	$the_editor = apply_filters('the_editor', "<div><textarea class='mceEditor' $rows cols='40' name='$id' tabindex='2' id='$id'>%s</textarea></div>\n");
	$the_editor_content = apply_filters('the_editor_content', $content);

	printf($the_editor, $the_editor_content);

	?>
	<script type="text/javascript">
	//<!--
	edCanvas = document.getElementById('<?php echo $id; ?>');
	<?php if ( user_can_richedit() ) : ?>
	// This code is meant to allow tabbing from Title to Post (TinyMCE).
	if ( tinyMCE.isMSIE )
		document.getElementById('<?php echo $prev_id; ?>').onkeydown = function (e)
			{
				e = e ? e : window.event;
				if (e.keyCode == 9 && !e.shiftKey && !e.controlKey && !e.altKey) {
					var i = tinyMCE.selectedInstance;
					if(typeof i ==  'undefined')
						return true;
	                                tinyMCE.execCommand("mceStartTyping");
					this.blur();
					i.contentWindow.focus();
					e.returnValue = false;
					return false;
				}
			}
	else
		document.getElementById('<?php echo $prev_id; ?>').onkeypress = function (e)
			{
				e = e ? e : window.event;
				if (e.keyCode == 9 && !e.shiftKey && !e.controlKey && !e.altKey) {
					var i = tinyMCE.selectedInstance;
					if(typeof i ==  'undefined')
						return true;
	                                tinyMCE.execCommand("mceStartTyping");
					this.blur();
					i.contentWindow.focus();
					e.returnValue = false;
					return false;
				}
			}
	<?php endif; ?>
	//-->
	</script>
	<?php
}

?>

<?php

/**** DB Functions ****/

/*
 * generic function for inserting data into the posts table.
 */
function wp_insert_post($postarr = array()) {
	global $wpdb, $post_default_category;
	
	// export array as variables
	extract($postarr);
	
	// Do some escapes for safety
	$post_title = $wpdb->escape($post_title);
	$post_name = sanitize_title($post_title);
	$post_excerpt = $wpdb->escape($post_excerpt);
	$post_content = $wpdb->escape($post_content);
	$post_author = (int) $post_author;

	// Make sure we set a valid category
	if (0 == count($post_category) || !is_array($post_category)) {
		$post_category = array($post_default_category);
	}

	$post_cat = $post_category[0];
	
	if (empty($post_date))
		$post_date = current_time('mysql');
	// Make sure we have a good gmt date:
	if (empty($post_date_gmt)) 
		$post_date_gmt = get_gmt_from_date($post_date);
	if (empty($comment_status))
		$comment_status = get_settings('default_comment_status');
	if (empty($ping_status))
		$ping_status = get_settings('default_ping_status');
	
	$sql = "INSERT INTO $wpdb->posts 
		(post_author, post_date, post_date_gmt, post_modified, post_modified_gmt, post_content, post_title, post_excerpt, post_category, post_status, post_name, comment_status, ping_status) 
		VALUES ('$post_author', '$post_date', '$post_date_gmt', '$post_date', '$post_date_gmt', '$post_content', '$post_title', '$post_excerpt', '$post_cat', '$post_status', '$post_name', '$comment_status', '$ping_status')";
	
	$result = $wpdb->query($sql);
	$post_ID = $wpdb->insert_id;

	// Set GUID
	$wpdb->query("UPDATE $wpdb->posts SET guid = '" . get_permalink($post_ID) . "' WHERE ID = '$post_ID'");
	
	wp_set_post_cats('', $post_ID, $post_category);
	
	if ($post_status == 'publish') {
		do_action('publish_post', $post_ID);
	}

	pingback($content, $post_ID);

	// Return insert_id if we got a good result, otherwise return zero.
	return $result ? $post_ID : 0;
}

function wp_get_single_post($postid = 0, $mode = OBJECT) {
	global $wpdb;

	$sql = "SELECT * FROM $wpdb->posts WHERE ID=$postid";
	$result = $wpdb->get_row($sql, $mode);
	
	// Set categories
	$result['post_category'] = wp_get_post_cats('',$postid);

	return $result;
}

function wp_get_recent_posts($num = 10) {
	global $wpdb;

	// Set the limit clause, if we got a limit
	if ($num) {
		$limit = "LIMIT $num";
	}

	$sql = "SELECT * FROM $wpdb->posts ORDER BY post_date DESC $limit";
	$result = $wpdb->get_results($sql,ARRAY_A);

	return $result?$result:array();
}

function wp_update_post($postarr = array()) {
	global $wpdb;

	// First get all of the original fields
	extract(wp_get_single_post($postarr['ID'],ARRAY_A));	

	// Now overwrite any changed values being passed in
	extract($postarr);
	
	// Make sure we set a valid category
	if (0 == count($post_category) || !is_array($post_category)) {
		$post_category = array($post_default_category);
	}

	// Do some escapes for safety
	$post_title = $wpdb->escape($post_title);
	$post_excerpt = $wpdb->escape($post_excerpt);
	$post_content = $wpdb->escape($post_content);

	$post_modified = current_time('mysql');
	$post_modified_gmt = current_time('mysql', 1);

	$sql = "UPDATE $wpdb->posts 
		SET post_content = '$post_content',
		post_title = '$post_title',
		post_category = $post_category[0],
		post_status = '$post_status',
		post_date = '$post_date',
		post_date_gmt = '$post_date_gmt',
		post_modified = '$post_modified',
		post_modified_gmt = '$post_modified_gmt',
		post_excerpt = '$post_excerpt',
		ping_status = '$ping_status',
		comment_status = '$comment_status'
		WHERE ID = $ID";
		
	$result = $wpdb->query($sql);

	wp_set_post_cats('',$ID,$post_category);
	
	return $wpdb->rows_affected;
}

function wp_get_post_cats($blogid = '1', $post_ID = 0) {
	global $wpdb;
	
	$sql = "SELECT category_id 
		FROM $wpdb->post2cat 
		WHERE post_id = $post_ID 
		ORDER BY category_id";

	$result = $wpdb->get_col($sql);

	return array_unique($result);
}

function wp_set_post_cats($blogid = '1', $post_ID = 0, $post_categories = array()) {
	global $wpdb;
	// If $post_categories isn't already an array, make it one:
	if (!is_array($post_categories)) {
		if (!$post_categories) {
			$post_categories = 1;
		}
		$post_categories = array($post_categories);
	}

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


	$oldies = print_r($old_categories,1);
	$newbies = print_r($post_categories,1);

	logio("O","Old: $oldies\nNew: $newbies\n");

	// Delete any?
	$delete_cats = array_diff($old_categories,$post_categories);

	logio("O","Delete: " . print_r($delete_cats,1));
		
	if ($delete_cats) {
		foreach ($delete_cats as $del) {
			$wpdb->query("
				DELETE FROM $wpdb->post2cat 
				WHERE category_id = $del 
					AND post_id = $post_ID 
				");

			logio("O","deleting post/cat: $post_ID, $del");
		}
	}

	// Add any?
	$add_cats = array_diff($post_categories, $old_categories);

	logio("O","Add: " . print_r($add_cats,1));
		
	if ($add_cats) {
		foreach ($add_cats as $new_cat) {
			$wpdb->query("
				INSERT INTO $wpdb->post2cat (post_id, category_id) 
				VALUES ($post_ID, $new_cat)");

				logio("O","adding post/cat: $post_ID, $new_cat");
		}
	}
}	// wp_set_post_cats()

function wp_delete_post($postid = 0) {
	global $wpdb;
	
	$sql = "DELETE FROM $wpdb->post2cat WHERE post_id = $postid";
	$wpdb->query($sql);
		
	$sql = "DELETE FROM $wpdb->posts WHERE ID = $postid";
	
	$wpdb->query($sql);

	$result = $wpdb->rows_affected;
	
	return $result;
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
function get_author_name($auth_id) {
	$authordata = get_userdata($auth_id);

	switch($authordata["user_idmode"]) {
		case "nickname":
			$authorname = $authordata["user_nickname"];

		case "login":
			$authorname = $authordata["user_login"];
			break;
	
		case "firstname":
			$authorname = $authordata["user_firstname"];
			break;

		case "lastname":
			$authorname = $authordata["user_lastname"];
			break;

		case "namefl":
			$authorname = $authordata["user_firstname"]." ".$authordata["user_lastname"];
			break;

		case "namelf":
			$authorname = $authordata["user_lastname"]." ".$authordata["user_firstname"];
			break;

		default:
			$authorname = $authordata["user_nickname"];
			break;
	}

	return $authorname;
}

// get extended entry info (<!--more-->)
function get_extended($post) {
	list($main,$extended) = explode('<!--more-->',$post);

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


// query user capabilities

/* returns true if a given $user_id can edit a given $post_id.
   note: optional $blog_id for future usage?                   */
function user_can_edit_post($user_id, $post_id, $blog_id = 1) {
	$author_data = get_userdata($user_id);
	$post_data   = get_postdata($post_id);
	$post_author_data = get_userdata($post_data['Author_ID']);

	if ( ($user_id == $post_author_data->ID)
	     || ($author_data->user_level > $post_author_data->user_level) ) {
		return true;
	} else {
		return false;
	}
}

/* returns true if a given $user_id can delete a given $post_id.
   note: optional $blog_id for future usage?                   */
function user_can_delete_post($user_id, $post_id, $blog_id = 1) {
	// right now if one can edit, one can delete
	return user_can_edit_post($user_id, $post_id, $blog_id);
}

function wp_new_comment($commentdata) {
	global $wpdb;
	extract($commentdata);

	$comment_post_id = (int) $comment_post_id;

	$comment_author = strip_tags($comment_author);
	$comment_author = htmlspecialchars($comment_author);
	$comment_author = $wpdb->escape($comment_author);

	$comment_author_email = preg_replace('/[^a-z+_.@-]/i', '', $comment_author_email);

	$comment_author_url = strip_tags($comment_author_url);
	$comment_author_url = htmlspecialchars($comment_author_url);
	$comment_author_url = $wpdb->escape($comment_author_url);

	$comment_content = apply_filters('comment_content_presave', $comment_content);
	$comment_content = $wpdb->escape($comment_content);

	$user_ip = addslashes($_SERVER['REMOTE_ADDR']);
	$user_domain = addslashes( gethostbyaddr($user_ip) );
	$now = current_time('mysql');
	$now_gmt = current_time('mysql', 1);
	$user_agent = addslashes($_SERVER['HTTP_USER_AGENT']);

	// Simple flood-protection
	if ( $lasttime = $wpdb->get_var("SELECT comment_date FROM $wpdb->comments WHERE comment_author_IP = '$user_ip' ORDER BY comment_date DESC LIMIT 1") ) {
		$time_lastcomment= mysql2date('U', $lasttime);
		$time_newcomment= mysql2date('U', $now);
		if ( ($time_newcomment - $time_lastcomment) < 15 )
			die( __('Sorry, you can only post a new comment once every 15 seconds. Slow down cowboy.') );
	}

	if( check_comment($author, $email, $url, $comment, $user_ip, $user_agent) )
		$approved = 1;
	else
		$approved = 0;

	$result = $wpdb->query("INSERT INTO $wpdb->comments 
	(comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_date_gmt, comment_content, comment_approved, comment_agent)
	VALUES 
	('$comment_post_ID', '$author', '$email', '$tb_url', '$user_ip', '$now', '$now_gmt', '$comment', '$approved', '$user_agent')
	");

	if ( get_option('comments_notify') )
		wp_notify_postauthor($wpdb->insert_id, $comment_type);

	return $result;
}

?>
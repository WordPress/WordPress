<?php

//
// Post functions
//

function get_attached_file($attachment_id) {
	return get_post_meta($attachment_id, '_wp_attached_file', true);
}

function &get_children($post = 0, $output = OBJECT) {
	global $post_cache, $wpdb;

	if ( empty($post) ) {
		if ( isset($GLOBALS['post']) )
			$post_parent = & $GLOBALS['post']->post_parent;
		else
			return false;
	} elseif ( is_object($post) ) {
		$post_parent = $post->post_parent;
	} else {
		$post_parent = $post;
	}

	$post_parent = (int) $post_parent;

	$query = "SELECT * FROM $wpdb->posts WHERE post_parent = $post_parent";

	$children = $wpdb->get_results($query);

	if ( $children ) {
		foreach ( $children as $key => $child ) {
			$post_cache[$child->ID] =& $children[$key];
			$kids[$child->ID] =& $children[$key];
		}
	} else {
		return false;
	}

	if ( $output == OBJECT ) {
		return $kids;
	} elseif ( $output == ARRAY_A ) {
		foreach ( $kids as $kid )
			$weeuns[$kid->ID] = get_object_vars($kids[$kid->ID]);
		return $weeuns;
	} elseif ( $output == ARRAY_N ) {
		foreach ( $kids as $kid )
			$babes[$kid->ID] = array_values(get_object_vars($kids[$kid->ID]));
		return $babes;
	} else {
		return $kids;
	}
}

// get extended entry info (<!--more-->)
function get_extended($post) {
	//Match the new style more links
	if (preg_match('/<!--more(.+?)?-->/', $post, $matches)) {
		list($main,$extended) = explode($matches[0],$post,2);
	} else {
		$main = $post;
		$extended = '';
	}
	
	// Strip leading and trailing whitespace
	$main = preg_replace('/^[\s]*(.*)[\s]*$/','\\1',$main);
	$extended = preg_replace('/^[\s]*(.*)[\s]*$/','\\1',$extended);

	return array('main' => $main, 'extended' => $extended);
}

// Retrieves post data given a post ID or post object.
// Handles post caching.
function &get_post(&$post, $output = OBJECT) {
	global $post_cache, $wpdb;

	if ( empty($post) ) {
		if ( isset($GLOBALS['post']) )
			$_post = & $GLOBALS['post'];
		else
			$_post = null;
	} elseif ( is_object($post) ) {
		if ( 'page' == $post->post_type )
			return get_page($post, $output);
		if ( !isset($post_cache[$post->ID]) )
			$post_cache[$post->ID] = &$post;
		$_post = & $post_cache[$post->ID];
	} else {
		if ( $_post = wp_cache_get($post, 'pages') )
			return get_page($_post, $output);
		elseif ( isset($post_cache[$post]) )
			$_post = & $post_cache[$post];
		else {
			$query = "SELECT * FROM $wpdb->posts WHERE ID = '$post' LIMIT 1";
			$_post = & $wpdb->get_row($query);
			if ( 'page' == $_post->post_type )
				return get_page($_post, $output);
			$post_cache[$post] = & $_post;
		}
	}

	if ( defined('WP_IMPORTING') )
		unset($post_cache);

	if ( $output == OBJECT ) {
		return $_post;
	} elseif ( $output == ARRAY_A ) {
		return get_object_vars($_post);
	} elseif ( $output == ARRAY_N ) {
		return array_values(get_object_vars($_post));
	} else {
		return $_post;
	}
}

// Takes a post ID, returns its mime type.
function get_post_mime_type($ID = '') {
	$post = & get_post($ID);

	if ( is_object($post) )
		return $post->post_mime_type;

	return false;
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

function get_posts($args) {
	global $wpdb;

	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);

	$defaults = array('numberposts' => 5, 'offset' => 0, 'category' => '',
		'orderby' => 'post_date', 'order' => 'DESC', 'include' => '', 'exclude' => '', 'meta_key' => '', 'meta_value' =>'');
	$r = array_merge($defaults, $r);
	extract($r);

	$inclusions = '';
	if ( !empty($include) ) {
		$offset = 0;	//ignore offset, category, exclude, meta_key, and meta_value params if using include
		$category = ''; 
		$exclude = '';  
		$meta_key = '';
		$meta_value = '';
		$incposts = preg_split('/[\s,]+/',$include);
		$numberposts = count($incposts);  // only the number of posts included
		if ( count($incposts) ) {
			foreach ( $incposts as $incpost ) {
				if (empty($inclusions))
					$inclusions = ' AND ( ID = ' . intval($incpost) . ' ';
				else
					$inclusions .= ' OR ID = ' . intval($incpost) . ' ';
			}
		}
	}
	if (!empty($inclusions)) 
		$inclusions .= ')';	

	$exclusions = '';
	if ( !empty($exclude) ) {
		$exposts = preg_split('/[\s,]+/',$exclude);
		if ( count($exposts) ) {
			foreach ( $exposts as $expost ) {
				if (empty($exclusions))
					$exclusions = ' AND ( ID <> ' . intval($expost) . ' ';
				else
					$exclusions .= ' AND ID <> ' . intval($expost) . ' ';
			}
		}
	}
	if (!empty($exclusions)) 
		$exclusions .= ')';

	$query ="SELECT DISTINCT * FROM $wpdb->posts " ;
	$query .= ( empty( $category ) ? "" : ", $wpdb->post2cat " ) ; 
	$query .= ( empty( $meta_key ) ? "" : ", $wpdb->postmeta " ) ; 
	$query .= " WHERE (post_type = 'post' AND post_status = 'publish') $exclusions $inclusions " ;
	$query .= ( empty( $category ) ? "" : "AND ($wpdb->posts.ID = $wpdb->post2cat.post_id AND $wpdb->post2cat.category_id = " . $category. ") " ) ;
	$query .= ( empty( $meta_key ) | empty($meta_value)  ? "" : " AND ($wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key = '$meta_key' AND $wpdb->postmeta.meta_value = '$meta_value' )" ) ;
	$query .= " GROUP BY $wpdb->posts.ID ORDER BY " . $orderby . " " . $order . " LIMIT " . $offset . ',' . $numberposts ;

	$posts = $wpdb->get_results($query);

	update_post_caches($posts);

	return $posts;
}

//
// Post meta functions
//

function add_post_meta($post_id, $key, $value, $unique = false) {
	global $wpdb, $post_meta_cache;

	$post_id = (int) $post_id;

	if ( $unique ) {
		if ( $wpdb->get_var("SELECT meta_key FROM $wpdb->postmeta WHERE meta_key = '$key' AND post_id = '$post_id'") ) {
			return false;
		}
	}

	$post_meta_cache[$post_id][$key][] = $value;

	$value = maybe_serialize($value);
	$value = $wpdb->escape($value);

	$wpdb->query("INSERT INTO $wpdb->postmeta (post_id,meta_key,meta_value) VALUES ('$post_id','$key','$value')");

	return true;
}

function delete_post_meta($post_id, $key, $value = '') {
	global $wpdb, $post_meta_cache;

	$post_id = (int) $post_id;

	if ( empty($value) ) {
		$meta_id = $wpdb->get_var("SELECT meta_id FROM $wpdb->postmeta WHERE post_id = '$post_id' AND meta_key = '$key'");
	} else {
		$meta_id = $wpdb->get_var("SELECT meta_id FROM $wpdb->postmeta WHERE post_id = '$post_id' AND meta_key = '$key' AND meta_value = '$value'");
	}

	if ( !$meta_id )
		return false;

	if ( empty($value) ) {
		$wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id = '$post_id' AND meta_key = '$key'");
		unset($post_meta_cache[$post_id][$key]);
	} else {
		$wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id = '$post_id' AND meta_key = '$key' AND meta_value = '$value'");
		$cache_key = $post_meta_cache[$post_id][$key];
		if ($cache_key) foreach ( $cache_key as $index => $data )
			if ( $data == $value )
				unset($post_meta_cache[$post_id][$key][$index]);
	}

	unset($post_meta_cache[$post_id][$key]);

	return true;
}

function get_post_meta($post_id, $key, $single = false) {
	global $wpdb, $post_meta_cache;

	$post_id = (int) $post_id;

	if ( isset($post_meta_cache[$post_id][$key]) ) {
		if ( $single ) {
			return maybe_unserialize( $post_meta_cache[$post_id][$key][0] );
		} else {
			return maybe_unserialize( $post_meta_cache[$post_id][$key] );
		}
	}

	$metalist = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE post_id = '$post_id' AND meta_key = '$key'", ARRAY_N);

	$values = array();
	if ( $metalist ) {
		foreach ($metalist as $metarow) {
			$values[] = $metarow[0];
		}
	}

	if ( $single ) {
		if ( count($values) ) {
			$return = maybe_unserialize( $values[0] );
		} else {
			return '';
		}
	} else {
		$return = $values;
	}

	return maybe_unserialize($return);
}

function update_post_meta($post_id, $key, $value, $prev_value = '') {
	global $wpdb, $post_meta_cache;

	$post_id = (int) $post_id;

	$original_value = $value;
	$value = maybe_serialize($value);
	$value = $wpdb->escape($value);

	$original_prev = $prev_value;
	$prev_value = maybe_serialize($prev_value);
	$prev_value = $wpdb->escape($prev_value);

	if (! $wpdb->get_var("SELECT meta_key FROM $wpdb->postmeta WHERE meta_key = '$key' AND post_id = '$post_id'") ) {
		return false;
	}

	if ( empty($prev_value) ) {
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = '$value' WHERE meta_key = '$key' AND post_id = '$post_id'");
		$cache_key = $post_meta_cache[$post_id][$key];
		if ( !empty($cache_key) )
			foreach ($cache_key as $index => $data)
				$post_meta_cache[$post_id][$key][$index] = $original_value;
	} else {
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = '$value' WHERE meta_key = '$key' AND post_id = '$post_id' AND meta_value = '$prev_value'");
		$cache_key = $post_meta_cache[$post_id][$key];
		if ( !empty($cache_key) )
			foreach ($cache_key as $index => $data)
				if ( $data == $original_prev )
					$post_meta_cache[$post_id][$key][$index] = $original_value;
	}

	return true;
}


function get_post_custom( $post_id = 0 ) {
	global $id, $post_meta_cache, $wpdb;

	if ( ! $post_id )
		$post_id = $id;

	$post_id = (int) $post_id;

	if ( isset($post_meta_cache[$post_id]) )
		return $post_meta_cache[$post_id];

	if ( $meta_list = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta	WHERE post_id = '$post_id' ORDER BY post_id, meta_key", ARRAY_A) ) {
		// Change from flat structure to hierarchical:
		$post_meta_cache = array();
		foreach ( $meta_list as $metarow ) {
			$mpid = (int) $metarow['post_id'];
			$mkey = $metarow['meta_key'];
			$mval = $metarow['meta_value'];

			// Force subkeys to be array type:
			if ( !isset($post_meta_cache[$mpid]) || !is_array($post_meta_cache[$mpid]) )
				$post_meta_cache[$mpid] = array();

			if ( !isset($post_meta_cache[$mpid]["$mkey"]) || !is_array($post_meta_cache[$mpid]["$mkey"]) )
				$post_meta_cache[$mpid]["$mkey"] = array();

			// Add a value to the current pid/key:
			$post_meta_cache[$mpid][$mkey][] = $mval;
		}
		return $post_meta_cache[$mpid];
	}
}

function get_post_custom_keys( $post_id = 0 ) {
	$custom = get_post_custom( $post_id );

	if ( ! is_array($custom) )
		return;

	if ( $keys = array_keys($custom) )
		return $keys;
}


function get_post_custom_values( $key = '', $post_id = 0 ) {
	$custom = get_post_custom($post_id);

	return $custom[$key];
}

function wp_delete_post($postid = 0) {
	global $wpdb, $wp_rewrite;
	$postid = (int) $postid;

	if ( !$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = $postid") )
		return $post;

	if ( 'attachment' == $post->post_type )
		return wp_delete_attachment($postid);

	do_action('delete_post', $postid);

	if ( 'publish' == $post->post_status && 'post' == $post->post_type ) {
		$categories = wp_get_post_categories($post->ID);
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

function wp_get_post_categories($post_ID = 0) {
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

function wp_get_single_post($postid = 0, $mode = OBJECT) {
	global $wpdb;

	$post = get_post($postid, $mode);

	// Set categories
	if($mode == OBJECT) {
		$post->post_category = wp_get_post_categories($postid);
	} 
	else {
		$post['post_category'] = wp_get_post_categories($postid);
	}

	return $post;
}

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
	if ( empty($no_filter) ) {
		$post_content    = apply_filters('content_save_pre',   $post_content);
		$post_excerpt    = apply_filters('excerpt_save_pre',   $post_excerpt);
		$post_title      = apply_filters('title_save_pre',     $post_title);
		$post_category   = apply_filters('category_save_pre',  $post_category);
		$post_status     = apply_filters('status_save_pre',    $post_status);
		$post_name       = apply_filters('name_save_pre',      $post_name);
		$comment_status  = apply_filters('comment_status_pre', $comment_status);
		$ping_status     = apply_filters('ping_status_pre',    $ping_status);
	}

	if ( ('' == $post_content) && ('' == $post_title) && ('' == $post_excerpt) )
		return 0;

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
			$comment_status = get_option('default_comment_status');
	}
	if ( empty($ping_status) )
		$ping_status = get_option('default_ping_status');
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

		if ($post_name_check || in_array($post_name, $wp_rewrite->feeds) ) {
			$suffix = 2;
			do {
				$alt_post_name = $post_name . "-$suffix";
				$post_name_check = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE post_name = '$alt_post_name' AND post_type = '$post_type' AND ID != '$post_ID' AND post_parent = '$post_parent' LIMIT 1");
				$suffix++;
			} while ($post_name_check);
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

	wp_set_post_categories($post_ID, $post_category);

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
		if ( defined('XMLRPC_REQUEST') )
			do_action('xmlrpc_publish_post', $post_ID);

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

	// Always clears the hook in case the post status bounced from future to draft.
	wp_clear_scheduled_hook('publish_future_post', $post_ID);

	// Schedule publication.
	if ( 'future' == $post_status )
		wp_schedule_single_event(strtotime($post_date_gmt. ' GMT'), 'publish_future_post', array($post_ID));
		
	do_action('save_post', $post_ID);
	do_action('wp_insert_post', $post_ID);

	return $post_ID;
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

	return wp_update_post(array('post_status' => 'publish', 'ID' => $post_id, 'no_filter' => true));
}

function wp_set_post_categories($post_ID = 0, $post_categories = array()) {
	global $wpdb;
	// If $post_categories isn't already an array, make it one:
	if (!is_array($post_categories) || 0 == count($post_categories) || empty($post_categories))
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
			if ( !empty($new_cat) )
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
}	// wp_set_post_categories()

//
// Trackback and ping functions
//

function add_ping($post_id, $uri) { // Add a URL to those already pung
	global $wpdb;
	$pung = $wpdb->get_var("SELECT pinged FROM $wpdb->posts WHERE ID = $post_id");
	$pung = trim($pung);
	$pung = preg_split('/\s/', $pung);
	$pung[] = $uri;
	$new = implode("\n", $pung);
	$new = apply_filters('add_ping', $new);
	return $wpdb->query("UPDATE $wpdb->posts SET pinged = '$new' WHERE ID = $post_id");
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

function get_pung($post_id) { // Get URLs already pung for a post
	global $wpdb;
	$pung = $wpdb->get_var("SELECT pinged FROM $wpdb->posts WHERE ID = $post_id");
	$pung = trim($pung);
	$pung = preg_split('/\s/', $pung);
	$pung = apply_filters('get_pung', $pung);
	return $pung;
}

function get_to_ping($post_id) { // Get any URLs in the todo list
	global $wpdb;
	$to_ping = $wpdb->get_var("SELECT to_ping FROM $wpdb->posts WHERE ID = $post_id");
	$to_ping = trim($to_ping);
	$to_ping = preg_split('/\s/', $to_ping, -1, PREG_SPLIT_NO_EMPTY);
	$to_ping = apply_filters('get_to_ping',  $to_ping);
	return $to_ping;
}

// do trackbacks for a list of urls
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

//
// Page functions
//

function get_all_page_ids() {
	global $wpdb;

	if ( ! $page_ids = wp_cache_get('all_page_ids', 'pages') ) {
		$page_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = 'page'");
		wp_cache_add('all_page_ids', $page_ids, 'pages');
	}

	return $page_ids;
}


// Retrieves page data given a page ID or page object.
// Handles page caching.
function &get_page(&$page, $output = OBJECT) {
	global $wpdb;

	if ( empty($page) ) {
		if ( isset($GLOBALS['page']) ) {
			$_page = & $GLOBALS['page'];
			wp_cache_add($_page->ID, $_page, 'pages');
		} else {
			$_page = null;
		}
	} elseif ( is_object($page) ) {
		if ( 'post' == $page->post_type )
			return get_post($page, $output);
		wp_cache_add($page->ID, $page, 'pages');
		$_page = $page;
	} else {
		if ( isset($GLOBALS['page']->ID) && ($page == $GLOBALS['page']->ID) ) {
			$_page = & $GLOBALS['page'];
			wp_cache_add($_page->ID, $_page, 'pages');
		} elseif ( isset($_page) && $_page == $GLOBALS['post_cache'][$page] ) {
			return get_post($page, $output);
		} elseif ( isset($_page) && $_page == wp_cache_get($page, 'pages') ) {
			// Got it.
		} else {
			$query = "SELECT * FROM $wpdb->posts WHERE ID= '$page' LIMIT 1";
			$_page = & $wpdb->get_row($query);
			if ( 'post' == $_page->post_type )
				return get_post($_page, $output);
			wp_cache_add($_page->ID, $_page, 'pages');
		}
	}

	if ( $output == OBJECT ) {
		return $_page;
	} elseif ( $output == ARRAY_A ) {
		return get_object_vars($_page);
	} elseif ( $output == ARRAY_N ) {
		return array_values(get_object_vars($_page));
	} else {
		return $_page;
	}
}

function get_page_by_path($page_path, $output = OBJECT) {
	global $wpdb;
	$page_path = rawurlencode(urldecode($page_path));
	$page_path = str_replace('%2F', '/', $page_path);
	$page_path = str_replace('%20', ' ', $page_path);
	$page_paths = '/' . trim($page_path, '/');
	$leaf_path  = sanitize_title(basename($page_paths));
	$page_paths = explode('/', $page_paths);
	foreach($page_paths as $pathdir)
		$full_path .= ($pathdir!=''?'/':'') . sanitize_title($pathdir);

	$pages = $wpdb->get_results("SELECT ID, post_name, post_parent FROM $wpdb->posts WHERE post_name = '$leaf_path' AND post_type='page'");

	if ( empty($pages) ) 
		return NULL;

	foreach ($pages as $page) {
		$path = '/' . $leaf_path;
		$curpage = $page;
		while ($curpage->post_parent != 0) {
			$curpage = $wpdb->get_row("SELECT ID, post_name, post_parent FROM $wpdb->posts WHERE ID = '$curpage->post_parent' and post_type='page'");
			$path = '/' . $curpage->post_name . $path;
		}

		if ( $path == $full_path )
			return get_page($page->ID, $output);
	}

	return NULL;
}

function &get_page_children($page_id, $pages) {
	global $page_cache;

	if ( empty($pages) )
		$pages = &$page_cache;

	$page_list = array();
	foreach ( $pages as $page ) {
		if ( $page->post_parent == $page_id ) {
			$page_list[] = $page;
			if ( $children = get_page_children($page->ID, $pages) )
				$page_list = array_merge($page_list, $children);
		}
	}
	return $page_list;
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

function get_page_uri($page_id) {
	$page = get_page($page_id);
	$uri = urldecode($page->post_name);

	// A page cannot be it's own parent.
	if ( $page->post_parent == $page->ID )
		return $uri;

	while ($page->post_parent != 0) {
		$page = get_page($page->post_parent);
		$uri = urldecode($page->post_name) . "/" . $uri;
	}

	return $uri;
}

function &get_pages($args = '') {
	global $wpdb;

	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);

	$defaults = array('child_of' => 0, 'sort_order' => 'ASC', 'sort_column' => 'post_title',
				'hierarchical' => 1, 'exclude' => '', 'include' => '', 'meta_key' => '', 'meta_value' => '', 'authors' => '');
	$r = array_merge($defaults, $r);
	extract($r);

	$inclusions = '';
	if ( !empty($include) ) {
		$child_of = 0; //ignore child_of, exclude, meta_key, and meta_value params if using include 
		$exclude = '';  
		$meta_key = '';
		$meta_value = '';
		$incpages = preg_split('/[\s,]+/',$include);
		if ( count($incpages) ) {
			foreach ( $incpages as $incpage ) {
				if (empty($inclusions))
					$inclusions = ' AND ( ID = ' . intval($incpage) . ' ';
				else
					$inclusions .= ' OR ID = ' . intval($incpage) . ' ';
			}
		}
	}
	if (!empty($inclusions)) 
		$inclusions .= ')';	

	$exclusions = '';
	if ( !empty($exclude) ) {
		$expages = preg_split('/[\s,]+/',$exclude);
		if ( count($expages) ) {
			foreach ( $expages as $expage ) {
				if (empty($exclusions))
					$exclusions = ' AND ( ID <> ' . intval($expage) . ' ';
				else
					$exclusions .= ' AND ID <> ' . intval($expage) . ' ';
			}
		}
	}
	if (!empty($exclusions)) 
		$exclusions .= ')';

	$author_query = '';
	if (!empty($authors)) {
		$post_authors = preg_split('/[\s,]+/',$authors);
		
		if ( count($post_authors) ) {
			foreach ( $post_authors as $post_author ) {
				//Do we have an author id or an author login?
				if ( 0 == intval($post_author) ) {
					$post_author = get_userdatabylogin($post_author);
					if ( empty($post_author) )
						continue;
					if ( empty($post_author->ID) )
						continue;
					$post_author = $post_author->ID;
				}

				if ( '' == $author_query )
					$author_query = ' post_author = ' . intval($post_author) . ' ';
				else
					$author_query .= ' OR post_author = ' . intval($post_author) . ' ';
			}
			if ( '' != $author_query )
				$author_query = " AND ($author_query)";
		}
	}

	$query = "SELECT * FROM $wpdb->posts " ;
	$query .= ( empty( $meta_key ) ? "" : ", $wpdb->postmeta " ) ; 
	$query .= " WHERE (post_type = 'page' AND post_status = 'publish') $exclusions $inclusions " ;
	$query .= ( empty( $meta_key ) | empty($meta_value)  ? "" : " AND ($wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key = '$meta_key' AND $wpdb->postmeta.meta_value = '$meta_value' )" ) ;
	$query .= $author_query;
	$query .= " ORDER BY " . $sort_column . " " . $sort_order ;

	$pages = $wpdb->get_results($query);
	$pages = apply_filters('get_pages', $pages, $r);

	if ( empty($pages) )
		return array();

	// Update cache.
	update_page_cache($pages);

	if ( $child_of || $hierarchical )
		$pages = & get_page_children($child_of, $pages);

	return $pages;
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

			// URL => page name
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

//
// Attachment functions
//

function is_local_attachment($url) {
	if ( !strstr($url, get_bloginfo('home') ) )
		return false;
	if ( strstr($url, get_bloginfo('home') . '/?attachment_id=') )
		return true;
	if ( $id = url_to_postid($url) ) {
		$post = & get_post($id);
		if ( 'attachment' == $post->post_type )
			return true;
	}
	return false;
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
			$comment_status = get_option('default_comment_status');
	}
	if ( empty($ping_status) )
		$ping_status = get_option('default_ping_status');
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

	wp_set_post_categories($post_ID, $post_category);

	if ( $file )
		add_post_meta($post_ID, '_wp_attached_file', quotemeta( $file ) );

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

?>

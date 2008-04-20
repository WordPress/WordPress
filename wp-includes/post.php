<?php
/**
 * Post functions and post utility function
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.5
 */

/**
 * get_attached_file() - Get metadata for an attached file
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.0
 *
 * @param int $attachment_id Attachment ID
 * @param bool $unfiltered Whether to apply filters or not
 * @return array {@internal Missing Description}}
 */
function get_attached_file( $attachment_id, $unfiltered = false ) {
	$file = get_post_meta( $attachment_id, '_wp_attached_file', true );
	if ( $unfiltered )
		return $file;
	return apply_filters( 'get_attached_file', $file, $attachment_id );
}

/**
 * update_attached_file() - Update attached file metadata
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.1
 *
 * @param int $attachment_id Attachment ID
 * @param string $file {@internal Missing Description}}
 * @return bool|mixed {@internal Missing Description}}
 */
function update_attached_file( $attachment_id, $file ) {
	if ( !get_post( $attachment_id ) )
		return false;

	$old_file = get_attached_file( $attachment_id, true );

	$file = apply_filters( 'update_attached_file', $file, $attachment_id );

	if ( $old_file )
		return update_post_meta( $attachment_id, '_wp_attached_file', $file, $old_file );
	else
		return add_post_meta( $attachment_id, '_wp_attached_file', $file );
}

/**
 * get_children() - Get post children
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.0
 *
 * @param mixed $args {@internal Missing Description}}
 * @param string $output {@internal Missing Description}}
 * @return mixed {@internal Missing Description}}
 */
function &get_children($args = '', $output = OBJECT) {
	if ( empty( $args ) ) {
		if ( isset( $GLOBALS['post'] ) ) {
			$args = 'post_parent=' . (int) $GLOBALS['post']->post_parent;
		} else {
			return false;
		}
	} elseif ( is_object( $args ) ) {
		$args = 'post_parent=' . (int) $args->post_parent;
	} elseif ( is_numeric( $args ) ) {
		$args = 'post_parent=' . (int) $args;
	}

	$defaults = array(
		'numberposts' => -1, 'post_type' => '',
		'post_status' => '', 'post_parent' => 0
	);

	$r = wp_parse_args( $args, $defaults );

	$children = get_posts( $r );

	if ( !$children )
		return false;

	update_post_cache($children);

	foreach ( $children as $key => $child )
		$kids[$child->ID] =& $children[$key];

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

/**
 * get_extended() - get extended entry info (<!--more-->)
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.0.1
 *
 * @param string $post {@internal Missing Description}}
 * @return array {@internal Missing Description}}
 */
function get_extended($post) {
	//Match the new style more links
	if ( preg_match('/<!--more(.*?)?-->/', $post, $matches) ) {
		list($main, $extended) = explode($matches[0], $post, 2);
	} else {
		$main = $post;
		$extended = '';
	}

	// Strip leading and trailing whitespace
	$main = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $main);
	$extended = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $extended);

	return array('main' => $main, 'extended' => $extended);
}

/**
 * get_post() - Retrieves post data given a post ID or post object.
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.5.1
 * @uses $wpdb
 *
 * @param int|object &$post post ID or post object
 * @param string $output {@internal Missing Description}}
 * @param string $filter {@internal Missing Description}}
 * @return mixed {@internal Missing Description}}
 */
function &get_post(&$post, $output = OBJECT, $filter = 'raw') {
	global $wpdb;
	$null = null;

	if ( empty($post) ) {
		if ( isset($GLOBALS['post']) )
			$_post = & $GLOBALS['post'];
		else
			return $null;
	} elseif ( is_object($post) ) {
		_get_post_ancestors($post);
		wp_cache_add($post->ID, $post, 'posts');
		$_post = &$post;
	} else {
		$post = (int) $post;
		if ( ! $_post = wp_cache_get($post, 'posts') ) {
			$_post = & $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE ID = %d LIMIT 1", $post));
			_get_post_ancestors($_post);
			wp_cache_add($_post->ID, $_post, 'posts');
		}
	}

	$_post = sanitize_post($_post, $filter);

	if ( $output == OBJECT ) {
		return $_post;
	} elseif ( $output == ARRAY_A ) {
		$__post = get_object_vars($_post);
		return $__post;
	} elseif ( $output == ARRAY_N ) {
		$__post = array_values(get_object_vars($_post));
		return $__post;
	} else {
		return $_post;
	}
}

/**
 * get_post_ancestors() - Retrieve ancestors for a post
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.5
 *
 * @param string $field {@internal Missing Description}}
 * @param int|object &$post post ID or post object
 * @return array of ancestor IDs
 */
function get_post_ancestors($post) {
	$post = get_post();

	if ( !empty($post->ancestors) )
		return $post->ancestors;

	return array();
}

/**
 * get_post_field() - Retrieve a field based on a post ID.
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.3
 *
 * @param string $field {@internal Missing Description}}
 * @param id $post Post ID
 * @param string $context Optional. How to filter the field
 * @return WP_Error|string Value in post field or WP_Error on failure
 */
function get_post_field( $field, $post, $context = 'display' ) {
	$post = (int) $post;
	$post = get_post( $post );

	if ( is_wp_error($post) )
		return $post;

	if ( !is_object($post) )
		return '';

	if ( !isset($post->$field) )
		return '';

	return sanitize_post_field($field, $post->$field, $post->ID, $context);
}

/**
 * get_post_mime_type() - Takes a post ID, returns its mime type.
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.0
 *
 * @param int $ID Post ID
 * @return bool|string False on failure or returns the mime type
 */
function get_post_mime_type($ID = '') {
	$post = & get_post($ID);

	if ( is_object($post) )
		return $post->post_mime_type;

	return false;
}

/**
 * get_post_status() - Takes a post ID and returns its status
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.0
 *
 * @param int $ID {@internal Missing Description}}
 * @return string|bool post status or false
 */
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

/**
 * get_post_statuses( ) - Retuns the possible user post status values
 *
 * Posts have a limited set of valid status values, this provides the
 * post_status values and descriptions.
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.5
 *
 * @return array
 */
function get_post_statuses( ) {
	$status = array(
		'draft'			=> __('Draft'),
		'pending'		=> __('Pending Review'),
		'private'		=> __('Private'),
		'publish'		=> __('Published')
	);

	return $status;
}

/**
 * get_page_statuses( ) - Retuns the possible user page status values
 *
 * Pages have a limited set of valid status values, this provides the
 * post_status values and descriptions.
 *
 * @package WordPress
 * @subpackage Page
 * @since 2.5
 *
 * @return array
 */
function get_page_statuses( ) {
	$status = array(
		'draft'			=> __('Draft'),
		'private'		=> __('Private'),
		'publish'		=> __('Published')
	);

	return $status;
}

/**
 * get_post_type() - Returns post type
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.1
 *
 * @uses $wpdb
 * @uses $posts {@internal Missing Description}}
 *
 * @param mixed $post post object or post ID
 * @return mixed post type or false
 */
function get_post_type($post = false) {
	global $posts;

	if ( false === $post )
		$post = $posts[0];
	elseif ( (int) $post )
		$post = get_post($post, OBJECT);

	if ( is_object($post) )
		return $post->post_type;

	return false;
}

/**
 * set_post_type() - Set post type
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.5
 *
 * @uses $wpdb
 * @uses $posts {@internal Missing Description}}
 *
 * @param mixed $post_id post ID
 * @param mixed post type
 * @return bool {@internal Missing Description}}
 */
function set_post_type( $post_id = 0, $post_type = 'post' ) {
	global $wpdb;

	$post_type = sanitize_post_field('post_type', $post_type, $post_id, 'db');
	$return = $wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET post_type = %s WHERE ID = %d", $post_type, $post_id) );

	if ( 'page' == $post_type )
		clean_page_cache($post_id);
	else
		clean_post_cache($post_id);

	return $return;
}

/**
 * get_posts() - Returns a number of posts
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.2
 * @uses $wpdb
 *
 * @param array $args {@internal Missing Description}}
 * @return array {@internal Missing Description}}
 */
function get_posts($args = null) {
	global $wpdb;

	$defaults = array(
		'numberposts' => 5, 'offset' => 0,
		'category' => 0, 'orderby' => 'post_date',
		'order' => 'DESC', 'include' => '',
		'exclude' => '', 'meta_key' => '',
		'meta_value' =>'', 'post_type' => 'post',
		'post_status' => 'publish', 'post_parent' => 0
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$numberposts = (int) $numberposts;
	$offset = (int) $offset;
	$category = (int) $category;
	$post_parent = (int) $post_parent;

	$inclusions = '';
	if ( !empty($include) ) {
		$offset = 0;    //ignore offset, category, exclude, meta_key, and meta_value, post_parent if using include
		$category = 0;
		$exclude = '';
		$meta_key = '';
		$meta_value = '';
		$post_parent = 0;
		$incposts = preg_split('/[\s,]+/',$include);
		$numberposts = count($incposts);  // only the number of posts included
		if ( count($incposts) ) {
			foreach ( $incposts as $incpost ) {
				if (empty($inclusions))
					$inclusions = $wpdb->prepare(' AND ( ID = %d ', $incpost);
				else
					$inclusions .= $wpdb->prepare(' OR ID = %d ', $incpost);
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
					$exclusions = $wpdb->prepare(' AND ( ID <> %d ', $expost);
				else
					$exclusions .= $wpdb->prepare(' AND ID <> %d ', $expost);
			}
		}
	}
	if (!empty($exclusions))
		$exclusions .= ')';

	// orderby
	if ( preg_match( '/.+ +(ASC|DESC)/i', $orderby ) )
		$order = ''; // orderby has its own order, so we'll use that

	$query  = "SELECT DISTINCT * FROM $wpdb->posts ";
	$query .= empty( $category ) ? '' : ", $wpdb->term_relationships, $wpdb->term_taxonomy  ";
	$query .= empty( $meta_key ) ? '' : ", $wpdb->postmeta ";
	$query .= " WHERE 1=1 ";
	$query .= empty( $post_type ) ? '' : $wpdb->prepare("AND post_type = %s ", $post_type);
	$query .= empty( $post_status ) ? '' : $wpdb->prepare("AND post_status = %s ", $post_status);
	$query .= "$exclusions $inclusions " ;
	$query .= empty( $category ) ? '' : $wpdb->prepare("AND ($wpdb->posts.ID = $wpdb->term_relationships.object_id AND $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id AND $wpdb->term_taxonomy.term_id = %d AND $wpdb->term_taxonomy.taxonomy = 'category')", $category);
	$query .= empty( $post_parent ) ? '' : $wpdb->prepare("AND $wpdb->posts.post_parent = %d ", $post_parent);
	// expected_slashed ($meta_key, $meta_value) -- Also, this looks really funky, doesn't seem like it works
	$query .= empty( $meta_key ) | empty($meta_value)  ? '' : " AND ($wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key = '$meta_key' AND $wpdb->postmeta.meta_value = '$meta_value' )";
	$query .= empty( $post_mime_type ) ? '' : wp_post_mime_type_where($post_mime_type);
	$query .= " GROUP BY $wpdb->posts.ID ORDER BY " . $orderby . ' ' . $order;
	if ( 0 < $numberposts )
		$query .= $wpdb->prepare(" LIMIT %d,%d", $offset, $numberposts);

	$posts = $wpdb->get_results($query);

	update_post_caches($posts);

	return $posts;
}

//
// Post meta functions
//

/**
 * add_post_meta() - adds metadata for post
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.5
 * @uses $wpdb
 *
 * @param int $post_id post ID
 * @param string $key {@internal Missing Description}}
 * @param mixed $value {@internal Missing Description}}
 * @param bool $unique whether to check for a value with the same key
 * @return bool {@internal Missing Description}}
 */
function add_post_meta($post_id, $meta_key, $meta_value, $unique = false) {
	global $wpdb;

	// expected_slashed ($meta_key)
	$meta_key = stripslashes($meta_key);

	if ( $unique && $wpdb->get_var( $wpdb->prepare( "SELECT meta_key FROM $wpdb->postmeta WHERE meta_key = %s AND post_id = %d", $meta_key, $post_id ) ) )
		return false;

	$meta_value = maybe_serialize($meta_value);

	$wpdb->insert( $wpdb->postmeta, compact( 'post_id', 'meta_key', 'meta_value' ) );

	wp_cache_delete($post_id, 'post_meta');

	return true;
}

/**
 * delete_post_meta() - delete post metadata
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.5
 * @uses $wpdb
 *
 * @param int $post_id post ID
 * @param string $key {@internal Missing Description}}
 * @param mixed $value {@internal Missing Description}}
 * @return bool {@internal Missing Description}}
 */
function delete_post_meta($post_id, $key, $value = '') {
	global $wpdb;

	$post_id = absint( $post_id );

	// expected_slashed ($key, $value)
	$key = stripslashes( $key );
	$value = stripslashes( $value );

	if ( empty( $value ) )
		$meta_id = $wpdb->get_var( $wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s", $post_id, $key ) );
	else
		$meta_id = $wpdb->get_var( $wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s AND meta_value = %s", $post_id, $key, $value ) );

	if ( !$meta_id )
		return false;

	if ( empty( $value ) )
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s", $post_id, $key ) );
	else
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s AND meta_value = %s", $post_id, $key, $value ) );

	wp_cache_delete($post_id, 'post_meta');

	return true;
}

/**
 * get_post_meta() - Get a post meta field
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.5
 * @uses $wpdb
 *
 * @param int $post_id post ID
 * @param string $key The meta key to retrieve
 * @param bool $single Whether to return a single value
 * @return mixed {@internal Missing Description}}
 */
function get_post_meta($post_id, $key, $single = false) {
	$post_id = (int) $post_id;

	$meta_cache = wp_cache_get($post_id, 'post_meta');

	if ( isset($meta_cache[$key]) ) {
		if ( $single ) {
			return maybe_unserialize( $meta_cache[$key][0] );
		} else {
			return maybe_unserialize( $meta_cache[$key] );
		}
	}

	if ( !$meta_cache ) {
		update_postmeta_cache($post_id);
		$meta_cache = wp_cache_get($post_id, 'post_meta');
	}

	if ( $single ) {
		if ( isset($meta_cache[$key][0]) )
			return maybe_unserialize($meta_cache[$key][0]);
		else
			return '';
	} else {
		return maybe_unserialize($meta_cache[$key]);
	}
}

/**
 * update_post_meta() - Update a post meta field
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.5
 * @uses $wpdb
 *
 * @param int $post_id post ID
 * @param string $key {@internal Missing Description}}
 * @param mixed $value {@internal Missing Description}}
 * @param mixed $prev_value previous value (for differentiating between meta fields with the same key and post ID)
 * @return bool {@internal Missing Description}}
 */
function update_post_meta($post_id, $meta_key, $meta_value, $prev_value = '') {
	global $wpdb;

	$meta_value = maybe_serialize($meta_value);
	$prev_value = maybe_serialize($prev_value);

	// expected_slashed ($meta_key)
	$meta_key = stripslashes($meta_key);

	if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT meta_key FROM $wpdb->postmeta WHERE meta_key = %s AND post_id = %d", $meta_key, $post_id ) ) )
		return false;

	$data  = compact( 'meta_value' );
	$where = compact( 'meta_key', 'post_id' );

	if ( !empty( $prev_value ) )
		$where['meta_value'] = $prev_value;

	$wpdb->update( $wpdb->postmeta, $data, $where );
	wp_cache_delete($post_id, 'post_meta');
	return true;
}

/**
 * delete_post_meta_by_key() - Delete everything from post meta matching $post_meta_key
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.3
 * @uses $wpdb
 *
 * @param string $post_meta_key What to search for when deleting
 * @return bool Whether the post meta key was deleted from the database
 */
function delete_post_meta_by_key($post_meta_key) {
	global $wpdb;
	if ( $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE meta_key = %s", $post_meta_key)) ) {
		/** @todo Get post_ids and delete cache */
		// wp_cache_delete($post_id, 'post_meta');
		return true;
	}
	return false;
}

/**
 * get_post_custom() - Retrieve post custom fields
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.2
 *
 * @uses $id
 * @uses $wpdb
 *
 * @param int $post_id post ID
 * @return array {@internal Missing Description}}
 */
function get_post_custom($post_id = 0) {
	global $id;

	if ( !$post_id )
		$post_id = (int) $id;

	$post_id = (int) $post_id;

	if ( ! wp_cache_get($post_id, 'post_meta') )
		update_postmeta_cache($post_id);

	return wp_cache_get($post_id, 'post_meta');
}

/**
 * get_post_custom_keys() - Retrieve post custom field names
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.2
 *
 * @param int $post_id post ID
 * @return array|null Either array of the keys, or null if keys would not be retrieved
 */
function get_post_custom_keys( $post_id = 0 ) {
	$custom = get_post_custom( $post_id );

	if ( !is_array($custom) )
		return;

	if ( $keys = array_keys($custom) )
		return $keys;
}


function get_post_custom_values( $key = '', $post_id = 0 ) {
	$custom = get_post_custom($post_id);

	return $custom[$key];
}

function sanitize_post($post, $context = 'display') {
	if ( 'raw' == $context )
		return $post;
	if ( is_object($post) ) {
		foreach ( array_keys(get_object_vars($post)) as $field )
			$post->$field = sanitize_post_field($field, $post->$field, $post->ID, $context);
	} else {
		foreach ( array_keys($post) as $field )
			$post[$field] = sanitize_post_field($field, $post[$field], $post['ID'], $context);
	}
	return $post;
}

/**
 * sanitize_post_field() - Sanitize post field based on context
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.3
 *
 * @param string $field The Post Object field name
 * @param string $value The Post Object value
 * @param int $postid Post ID
 * @param string $context How to sanitize post fields
 * @return string Sanitized value
 */
function sanitize_post_field($field, $value, $post_id, $context) {
	$int_fields = array('ID', 'post_parent', 'menu_order');
	if ( in_array($field, $int_fields) )
		$value = (int) $value;

	if ( 'raw' == $context )
		return $value;

	$prefixed = false;
	if ( false !== strpos($field, 'post_') ) {
		$prefixed = true;
		$field_no_prefix = str_replace('post_', '', $field);
	}

	if ( 'edit' == $context ) {
		$format_to_edit = array('post_content', 'post_excerpt', 'post_title', 'post_password');

		if ( $prefixed ) {
			$value = apply_filters("edit_$field", $value, $post_id);
			// Old school
			$value = apply_filters("${field_no_prefix}_edit_pre", $value, $post_id);
		} else {
			$value = apply_filters("edit_post_$field", $value, $post_id);
		}

		if ( in_array($field, $format_to_edit) ) {
			if ( 'post_content' == $field )
				$value = format_to_edit($value, user_can_richedit());
			else
				$value = format_to_edit($value);
		} else {
			$value = attribute_escape($value);
		}
	} else if ( 'db' == $context ) {
		if ( $prefixed ) {
			$value = apply_filters("pre_$field", $value);
			$value = apply_filters("${field_no_prefix}_save_pre", $value);
		} else {
			$value = apply_filters("pre_post_$field", $value);
			$value = apply_filters("${field}_pre", $value);
		}
	} else {
		// Use display filters by default.
		if ( $prefixed )
			$value = apply_filters($field, $value, $post_id, $context);
		else
			$value = apply_filters("post_$field", $value, $post_id, $context);
	}

	if ( 'attribute' == $context )
		$value = attribute_escape($value);
	else if ( 'js' == $context )
		$value = js_escape($value);

	return $value;
}

/**
 * wp_count_posts() - Count number of posts with a given type
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.5
 *
 * @param string $type Post type
 * @return array Number of posts for each status
 */
function wp_count_posts( $type = 'post', $perm = '' ) {
	global $wpdb;

	$user = wp_get_current_user();

	$cache_key = $type;

	$query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s";
	if ( 'readable' == $perm && is_user_logged_in() ) {
		if ( !current_user_can("read_private_{$type}s") ) {
			$cache_key .= '_' . $perm . '_' . $user->ID;
			$query .= " AND (post_status != 'private' OR ( post_author = '$user->ID' AND post_status = 'private' ))";
		}
	}
	$query .= ' GROUP BY post_status';

	$count = wp_cache_get($cache_key, 'counts');
	if ( false !== $count )
		return $count;

	$count = $wpdb->get_results( $wpdb->prepare( $query, $type ), ARRAY_A );

	$stats = array( );
	foreach( (array) $count as $row_num => $row ) {
		$stats[$row['post_status']] = $row['num_posts'];
	}

	$stats = (object) $stats;
	wp_cache_set($cache_key, $stats, 'counts');

	return $stats;
}


/**
 * wp_count_attachments() - Count number of attachments
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.5
 *
 * @param string|array $post_mime_type Array or comma-separated list of MIME patterns
 * @return array Number of posts for each post_mime_type
 */

function wp_count_attachments( $mime_type = '' ) {
	global $wpdb;

	$and = wp_post_mime_type_where( $mime_type );
	$count = $wpdb->get_results( "SELECT post_mime_type, COUNT( * ) AS num_posts FROM $wpdb->posts WHERE post_type = 'attachment' $and GROUP BY post_mime_type", ARRAY_A );

	$stats = array( );
	foreach( (array) $count as $row ) {
		$stats[$row['post_mime_type']] = $row['num_posts'];
	}

	return (object) $stats;
}

/**
 * wp_match_mime_type() - Check a MIME-Type against a list
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.5
 *
 * @param string|array $wildcard_mime_types e.g. audio/mpeg or image (same as image/*) or flash (same as *flash*)
 * @param string|array $real_mime_types post_mime_type values
 * @return array array(wildcard=>array(real types))
 */
function wp_match_mime_types($wildcard_mime_types, $real_mime_types) {
	$matches = array();
	if ( is_string($wildcard_mime_types) )
		$wildcard_mime_types = array_map('trim', explode(',', $wildcard_mime_types));
	if ( is_string($real_mime_types) )
		$real_mime_types = array_map('trim', explode(',', $real_mime_types));
	$wild = '[-._a-z0-9]*';
	foreach ( (array) $wildcard_mime_types as $type ) {
		$type = str_replace('*', $wild, $type);
		$patternses[1][$type] = "^$type$";
		if ( false === strpos($type, '/') ) {
			$patternses[2][$type] = "^$type/";
			$patternses[3][$type] = $type;
		}
	}
	asort($patternses);
	foreach ( $patternses as $patterns )
		foreach ( $patterns as $type => $pattern )
			foreach ( (array) $real_mime_types as $real )
				if ( preg_match("#$pattern#", $real) && ( empty($matches[$type]) || false === array_search($real, $matches[$type]) ) )
					$matches[$type][] = $real;
	return $matches;
}

/**
 * wp_get_post_mime_type_where() - Convert MIME types into SQL
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.5
 *
 * @param string|array $mime_types MIME types
 * @return string SQL AND clause
 */
function wp_post_mime_type_where($post_mime_types) {
	$where = '';
	$wildcards = array('', '%', '%/%');
	if ( is_string($post_mime_types) )
		$post_mime_types = array_map('trim', explode(',', $post_mime_types));
	foreach ( (array) $post_mime_types as $mime_type ) {
		$mime_type = preg_replace('/\s/', '', $mime_type);
		$slashpos = strpos($mime_type, '/');
		if ( false !== $slashpos ) {
			$mime_group = preg_replace('/[^-*.a-zA-Z0-9]/', '', substr($mime_type, 0, $slashpos));
			$mime_subgroup = preg_replace('/[^-*.a-zA-Z0-9]/', '', substr($mime_type, $slashpos + 1));
			if ( empty($mime_subgroup) )
				$mime_subgroup = '*';
			else
				$mime_subgroup = str_replace('/', '', $mime_subgroup);
			$mime_pattern = "$mime_group/$mime_subgroup";
		} else {
			$mime_pattern = preg_replace('/[^-*.a-zA-Z0-9]/', '', $mime_type);
			if ( false === strpos($mime_pattern, '*') )
				$mime_pattern .= '/*';
		}

		$mime_pattern = preg_replace('/\*+/', '%', $mime_pattern);

		if ( in_array( $mime_type, $wildcards ) )
			return '';

		if ( false !== strpos($mime_pattern, '%') )
			$wheres[] = "post_mime_type LIKE '$mime_pattern'";
		else
			$wheres[] = "post_mime_type = '$mime_pattern'";
	}
	if ( !empty($wheres) )
		$where = ' AND (' . join(' OR ', $wheres) . ') ';
	return $where;
}

/**
 * wp_delete_post() - Deletes a Post
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.0.1
 *
 * @param int $postid post ID
 * @return mixed {@internal Missing Description}}
 */
function wp_delete_post($postid = 0) {
	global $wpdb, $wp_rewrite;

	if ( !$post = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE ID = %d", $postid)) )
		return $post;

	if ( 'attachment' == $post->post_type )
		return wp_delete_attachment($postid);

	do_action('delete_post', $postid);

	/** @todo delete for pluggable post taxonomies too */
	wp_delete_object_term_relationships($postid, array('category', 'post_tag'));

	$parent_data = array( 'post_parent' => $post->post_parent );
	$parent_where = array( 'post_parent' => $postid );

	if ( 'page' == $post->post_type) {
	 	// if the page is defined in option page_on_front or post_for_posts,
		// adjust the corresponding options
		if ( get_option('page_on_front') == $postid ) {
			update_option('show_on_front', 'posts');
			delete_option('page_on_front');
		}
		if ( get_option('page_for_posts') == $postid ) {
			delete_option('page_for_posts');
		}

		// Point children of this page to its parent, also clean the cache of affected children
		$children_query = $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_parent = %d AND post_type='page'", $postid);
		$children = $wpdb->get_results($children_query);

		$wpdb->update( $wpdb->posts, $parent_data, $parent_where + array( 'post_type' => 'page' ) );
	}

	// Point all attachments to this post up one level
	$wpdb->update( $wpdb->posts, $parent_data, $parent_where + array( 'post_type' => 'attachment' ) );

	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE ID = %d", $postid ));

	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->comments WHERE comment_post_ID = %d", $postid ));

	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d", $postid ));

	if ( 'page' == $post->post_type ) {
		clean_page_cache($postid);

		foreach ( (array) $children as $child )
			clean_page_cache($child->ID);

		$wp_rewrite->flush_rules();
	} else {
		clean_post_cache($postid);
	}

	do_action('deleted_post', $postid);

	return $post;
}

/**
 * wp_get_post_categories() - Retrieve the list of categories for a post
 *
 * Compatibility layer for themes and plugins. Also an easy layer of abstraction
 * away from the complexity of the taxonomy layer.
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.1
 *
 * @uses wp_get_object_terms() Retrieves the categories. Args details can be found here
 *
 * @param int $post_id Optional. The Post ID
 * @param array $args Optional. Overwrite the defaults
 * @return array {@internal Missing Description}}
 */
function wp_get_post_categories( $post_id = 0, $args = array() ) {
	$post_id = (int) $post_id;

	$defaults = array('fields' => 'ids');
	$args = wp_parse_args( $args, $defaults );

	$cats = wp_get_object_terms($post_id, 'category', $args);
	return $cats;
}

/**
 * wp_get_post_tags() - Retrieve the post tags
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.3
 *
 * @uses wp_get_object_terms() Gets the tags for returning. Args can be found here
 *
 * @param int $post_id Optional. The Post ID
 * @param array $args Optional. Overwrite the defaults
 * @return mixed The tags the post has currently
 */
function wp_get_post_tags( $post_id = 0, $args = array() ) {
	$post_id = (int) $post_id;

	$defaults = array('fields' => 'all');
	$args = wp_parse_args( $args, $defaults );

	$tags = wp_get_object_terms($post_id, 'post_tag', $args);

	return $tags;
}

/**
 * wp_get_recent_posts() - Get the $num most recent posts
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.0.1
 *
 * @param int $num number of posts to get
 * @return array {@internal Missing Description}}
 */
function wp_get_recent_posts($num = 10) {
	global $wpdb;

	// Set the limit clause, if we got a limit
	$num = (int) $num;
	if ($num) {
		$limit = "LIMIT $num";
	}

	$sql = "SELECT * FROM $wpdb->posts WHERE post_type = 'post' ORDER BY post_date DESC $limit";
	$result = $wpdb->get_results($sql,ARRAY_A);

	return $result?$result:array();
}

/**
 * wp_get_single_post() - Get one post
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.0.1
 * @uses $wpdb
 *
 * @param int $postid post ID
 * @param string $mode How to return result, either OBJECT, ARRAY_N, or ARRAY_A
 * @return object|array Post object or array holding post contents and information
 */
function wp_get_single_post($postid = 0, $mode = OBJECT) {
	$postid = (int) $postid;

	$post = get_post($postid, $mode);

	// Set categories and tags
	if($mode == OBJECT) {
		$post->post_category = wp_get_post_categories($postid);
		$post->tags_input = wp_get_post_tags($postid, array('fields' => 'names'));
	}
	else {
		$post['post_category'] = wp_get_post_categories($postid);
		$post['tags_input'] = wp_get_post_tags($postid, array('fields' => 'names'));
	}

	return $post;
}

/**
 * wp_insert_post() - Insert a post
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.0.1
 *
 * @uses $wpdb
 * @uses $wp_rewrite
 * @uses $user_ID
 * @uses $allowedtags
 *
 * @param array $postarr post contents
 * @return int post ID or 0 on error
 */
function wp_insert_post($postarr = array()) {
	global $wpdb, $wp_rewrite, $user_ID;

	$defaults = array('post_status' => 'draft', 'post_type' => 'post', 'post_author' => $user_ID,
		'ping_status' => get_option('default_ping_status'), 'post_parent' => 0,
		'menu_order' => 0, 'to_ping' =>  '', 'pinged' => '', 'post_password' => '',
		'guid' => '', 'post_content_filtered' => '', 'post_excerpt' => '');

	$postarr = wp_parse_args($postarr, $defaults);
	$postarr = sanitize_post($postarr, 'db');

	// export array as variables
	extract($postarr, EXTR_SKIP);

	// Are we updating or creating?
	$update = false;
	if ( !empty($ID) ) {
		$update = true;
		$previous_status = get_post_field('post_status', $ID);
	} else {
		$previous_status = 'new';
	}

	if ( ('' == $post_content) && ('' == $post_title) && ('' == $post_excerpt) )
		return 0;

	// Make sure we set a valid category
	if (0 == count($post_category) || !is_array($post_category)) {
		$post_category = array(get_option('default_category'));
	}

	if ( empty($post_author) )
		$post_author = $user_ID;

	if ( empty($post_status) )
		$post_status = 'draft';

	if ( empty($post_type) )
		$post_type = 'post';

	// Get the post ID and GUID
	if ( $update ) {
		$post_ID = (int) $ID;
		$guid = get_post_field( 'guid', $post_ID );
	}

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
		if ( !in_array($post_status, array('draft', 'pending')) )
			$post_date = current_time('mysql');
		else
			$post_date = '0000-00-00 00:00:00';
	}

	if (empty($post_date_gmt)) {
		if ( !in_array($post_status, array('draft', 'pending')) )
			$post_date_gmt = get_gmt_from_date($post_date);
		else
			$post_date_gmt = '0000-00-00 00:00:00';
	}

	if ( $update || '0000-00-00 00:00:00' == $post_date ) {
		$post_modified     = current_time( 'mysql' );
		$post_modified_gmt = current_time( 'mysql', 1 );
	} else {
		$post_modified     = $post_date;
		$post_modified_gmt = $post_date_gmt;
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
		$post_name_check = $wpdb->get_var($wpdb->prepare("SELECT post_name FROM $wpdb->posts WHERE post_name = %s AND post_type = %s AND ID != %d AND post_parent = %d LIMIT 1", $post_name, $post_type, $post_ID, $post_parent));

		if ($post_name_check || in_array($post_name, $wp_rewrite->feeds) ) {
			$suffix = 2;
			do {
				$alt_post_name = substr($post_name, 0, 200-(strlen($suffix)+1)). "-$suffix";
				// expected_slashed ($alt_post_name, $post_name, $post_type)
				$post_name_check = $wpdb->get_var($wpdb->prepare("SELECT post_name FROM $wpdb->posts WHERE post_name = '$alt_post_name' AND post_type = '$post_type' AND ID != %d AND post_parent = %d LIMIT 1", $post_ID, $post_parent));
				$suffix++;
			} while ($post_name_check);
			$post_name = $alt_post_name;
		}
	}

	// expected_slashed (everything!)
	$data = compact( array( 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_content_filtered', 'post_title', 'post_excerpt', 'post_status', 'post_type', 'comment_status', 'ping_status', 'post_password', 'post_name', 'to_ping', 'pinged', 'post_modified', 'post_modified_gmt', 'post_parent', 'menu_order', 'guid' ) );
	$data = stripslashes_deep( $data );
	$where = array( 'ID' => $post_ID );

	if ($update) {
		do_action( 'pre_post_update', $post_ID );
		$wpdb->update( $wpdb->posts, $data, $where );
	} else {
		$data['post_mime_type'] = stripslashes( $post_mime_type ); // This isn't in the update
		$wpdb->insert( $wpdb->posts, $data );
		$post_ID = (int) $wpdb->insert_id;

		// use the newly generated $post_ID
		$where = array( 'ID' => $post_ID );
	}

	if ( empty($post_name) && 'draft' != $post_status ) {
		$post_name = sanitize_title($post_title, $post_ID);
		$wpdb->update( $wpdb->posts, compact( 'post_name' ), $where );
	}

	wp_set_post_categories( $post_ID, $post_category );
	wp_set_post_tags( $post_ID, $tags_input );

	$current_guid = get_post_field( 'guid', $post_ID );

	if ( 'page' == $post_type ) {
		clean_page_cache($post_ID);
	} else {
		clean_post_cache($post_ID);
	}

	// Set GUID
	if ( !$update && '' == $current_guid )
		$wpdb->update( $wpdb->posts, array( 'guid' => get_permalink( $post_ID ) ), $where );

	$post = get_post($post_ID);
	if ( !empty($page_template) )
		$post->page_template = $page_template;

	wp_transition_post_status($post_status, $previous_status, $post);

	if ( $update)
		do_action('edit_post', $post_ID, $post);

	do_action('save_post', $post_ID, $post);
	do_action('wp_insert_post', $post_ID, $post);

	return $post_ID;
}

/**
 * wp_update_post() - Update a post
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.0.1
 * @uses $wpdb
 *
 * @param array $postarr post data
 * @return int {@internal Missing Description}}
 */
function wp_update_post($postarr = array()) {
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
	if ( in_array($post['post_status'], array('draft', 'pending')) && empty($postarr['edit_date']) && empty($postarr['post_date']) &&
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

/**
 * wp_publish_post() - Mark a post as "published"
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.1
 * @uses $wpdb
 *
 * @param int $post_id Post ID
 * @return int|null {@internal Missing Description}}
 */
function wp_publish_post($post_id) {
	global $wpdb;

	$post = get_post($post_id);

	if ( empty($post) )
		return;

	if ( 'publish' == $post->post_status )
		return;

	$wpdb->update( $wpdb->posts, array( 'post_status' => 'publish' ), array( 'ID' => $post_id ) );

	$old_status = $post->post_status;
	$post->post_status = 'publish';
	wp_transition_post_status('publish', $old_status, $post);

	// Update counts for the post's terms.
	foreach ( get_object_taxonomies('post') as $taxonomy ) {
		$terms = wp_get_object_terms($post_id, $taxonomy, 'fields=tt_ids');
		wp_update_term_count($terms, $taxonomy);
	}

	do_action('edit_post', $post_id, $post);
	do_action('save_post', $post_id, $post);
	do_action('wp_insert_post', $post_id, $post);
}

/**
 * check_and_publish_future_post() - check to make sure post has correct status before
 * passing it on to be published. Invoked by cron 'publish_future_post' event
 * This safeguard prevents cron from publishing drafts, etc.
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.5
 * @uses $wpdb
 *
 * @param int $post_id Post ID
 * @return int|null {@internal Missing Description}}
 */
function check_and_publish_future_post($post_id) {

	$post = get_post($post_id);

	if ( empty($post) )
		return;

	if ( 'future' != $post->post_status )
		return;

	return wp_publish_post($post_id);
}

function wp_add_post_tags($post_id = 0, $tags = '') {
	return wp_set_post_tags($post_id, $tags, true);
}

function wp_set_post_tags( $post_id = 0, $tags = '', $append = false ) {
	/* $append - true = don't delete existing tags, just add on, false = replace the tags with the new tags */

	$post_id = (int) $post_id;

	if ( !$post_id )
		return false;

	if ( empty($tags) )
		$tags = array();
	$tags = (is_array($tags)) ? $tags : explode( ',', trim($tags, " \n\t\r\0\x0B,") );
	wp_set_object_terms($post_id, $tags, 'post_tag', $append);
}

/**
 * wp_set_post_categories() - Set categories for a post
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.1
 * @uses $wpdb
 *
 * @param int $post_ID post ID
 * @param array $post_categories
 * @return bool|mixed {@internal Missing Description}}
 */
function wp_set_post_categories($post_ID = 0, $post_categories = array()) {
	$post_ID = (int) $post_ID;
	// If $post_categories isn't already an array, make it one:
	if (!is_array($post_categories) || 0 == count($post_categories) || empty($post_categories))
		$post_categories = array(get_option('default_category'));
	else if ( 1 == count($post_categories) && '' == $post_categories[0] )
		return true;

	$post_categories = array_map('intval', $post_categories);
	$post_categories = array_unique($post_categories);

	return wp_set_object_terms($post_ID, $post_categories, 'category');
}	// wp_set_post_categories()

/**
 * wp_transition_post_status() - Change the post transition status
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.3
 *
 * @param string $new_status {@internal Missing Description}}
 * @param string $old_status {@internal Missing Description}}
 * @param int $post {@internal Missing Description}}
 */
function wp_transition_post_status($new_status, $old_status, $post) {
	if ( $new_status != $old_status ) {
		do_action('transition_post_status', $new_status, $old_status, $post);
		do_action("${old_status}_to_$new_status", $post);
	}
	do_action("${new_status}_$post->post_type", $post->ID, $post);
}

//
// Trackback and ping functions
//

/**
 * add_ping() - Add a URL to those already pung
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.5
 * @uses $wpdb
 *
 * @param int $post_id post ID
 * @param string $uri {@internal Missing Description}}
 * @return mixed {@internal Missing Description}}
 */
function add_ping($post_id, $uri) {
	global $wpdb;
	$pung = $wpdb->get_var( $wpdb->prepare( "SELECT pinged FROM $wpdb->posts WHERE ID = %d", $post_id ));
	$pung = trim($pung);
	$pung = preg_split('/\s/', $pung);
	$pung[] = $uri;
	$new = implode("\n", $pung);
	$new = apply_filters('add_ping', $new);
	// expected_slashed ($new)
	$new = stripslashes($new);
	return $wpdb->update( $wpdb->posts, array( 'pinged' => $new ), array( 'ID' => $post_id ) );
}

function get_enclosed($post_id) { // Get enclosures already enclosed for a post
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

/**
 * get_pung() - Get URLs already pinged for a post
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.5
 * @uses $wpdb
 *
 * @param int $post_id post ID
 * @return array {@internal Missing Description}}
 */
function get_pung($post_id) {
	global $wpdb;
	$pung = $wpdb->get_var( $wpdb->prepare( "SELECT pinged FROM $wpdb->posts WHERE ID = %d", $post_id ));
	$pung = trim($pung);
	$pung = preg_split('/\s/', $pung);
	$pung = apply_filters('get_pung', $pung);
	return $pung;
}

/**
 * get_to_ping() - Get any URLs in the todo list
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.5
 * @uses $wpdb
 *
 * @param int $post_id post ID
 * @return array {@internal Missing Description}}
 */
function get_to_ping($post_id) {
	global $wpdb;
	$to_ping = $wpdb->get_var( $wpdb->prepare( "SELECT to_ping FROM $wpdb->posts WHERE ID = %d", $post_id ));
	$to_ping = trim($to_ping);
	$to_ping = preg_split('/\s/', $to_ping, -1, PREG_SPLIT_NO_EMPTY);
	$to_ping = apply_filters('get_to_ping',  $to_ping);
	return $to_ping;
}

/**
 * trackback_url_list() - Do trackbacks for a list of urls
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.0.1
 *
 * @param string $tb_list comma separated list of URLs
 * @param int $post_id post ID
 */
function trackback_url_list($tb_list, $post_id) {
	if (!empty($tb_list)) {
		// get post data
		$postdata = wp_get_single_post($post_id, ARRAY_A);

		// import postdata as variables
		extract($postdata, EXTR_SKIP);

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

/**
 * get_all_page_ids() - Get a list of page IDs
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.0
 * @uses $wpdb
 *
 * @return array {@internal Missing Description}}
 */
function get_all_page_ids() {
	global $wpdb;

	if ( ! $page_ids = wp_cache_get('all_page_ids', 'posts') ) {
		$page_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = 'page'");
		wp_cache_add('all_page_ids', $page_ids, 'posts');
	}

	return $page_ids;
}

/**
 * get_page() - Retrieves page data given a page ID or page object
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.5.1
 *
 * @param mixed &$page page object or page ID
 * @param string $output what to output
 * @param string $filter How the return value should be filtered.
 * @return mixed {@internal Missing Description}}
 */
function &get_page(&$page, $output = OBJECT, $filter = 'raw') {
	if ( empty($page) ) {
		if ( isset( $GLOBALS['page'] ) && isset( $GLOBALS['page']->ID ) )
			return get_post($GLOBALS['page'], $output, $filter);
		else
			return null;
	}

	return get_post($page, $output, $filter);
}

/**
 * get_page_by_path() - Retrieves a page given its path
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.1
 * @uses $wpdb
 *
 * @param string $page_path page path
 * @param string $output output type
 * @return mixed {@internal Missing Description}}
 */
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

	$pages = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_name, post_parent FROM $wpdb->posts WHERE post_name = %s AND (post_type = 'page' OR post_type = 'attachment')", $leaf_path ));

	if ( empty($pages) )
		return NULL;

	foreach ($pages as $page) {
		$path = '/' . $leaf_path;
		$curpage = $page;
		while ($curpage->post_parent != 0) {
			$curpage = $wpdb->get_row( $wpdb->prepare( "SELECT ID, post_name, post_parent FROM $wpdb->posts WHERE ID = %d and post_type='page'", $curpage->post_parent ));
			$path = '/' . $curpage->post_name . $path;
		}

		if ( $path == $full_path )
			return get_page($page->ID, $output);
	}

	return NULL;
}

/**
 * get_page_by_title() - Retrieve a page given its title
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.1
 * @uses $wpdb
 *
 * @param string $page_title page title
 * @param string $output output type
 * @return mixed {@internal Missing Description}}
 */
function get_page_by_title($page_title, $output = OBJECT) {
	global $wpdb;
	$page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='page'", $page_title ));
	if ( $page )
		return get_page($page, $output);

	return NULL;
}

/**
 * get_page_children() - Retrieve child pages
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.5.1
 *
 * @param int $page_id page ID
 * @param array $pages list of pages
 * @return array {@internal Missing Description}}
 */
function &get_page_children($page_id, $pages) {
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

/**
 * get_page_hierarchy() - {@internal Missing Short Description}}
 *
 * Fetches the pages returned as a FLAT list, but arranged in order of their hierarchy,
 * i.e., child parents immediately follow their parents.
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.0
 *
 * @param array $posts posts array
 * @param int $parent parent page ID
 * @return array {@internal Missing Description}}
 */
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

/**
 * get_page_uri() - Builds a page URI
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.5
 *
 * @param int $page_id page ID
 * @return string {@internal Missing Description}}
 */
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

/**
 * get_pages() - Retrieve a list of pages
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.5
 * @uses $wpdb
 *
 * @param mixed $args Optional. Array or string of options
 * @return array List of pages matching defaults or $args
 */
function &get_pages($args = '') {
	global $wpdb;

	$defaults = array(
		'child_of' => 0, 'sort_order' => 'ASC',
		'sort_column' => 'post_title', 'hierarchical' => 1,
		'exclude' => '', 'include' => '',
		'meta_key' => '', 'meta_value' => '',
		'authors' => ''
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$key = md5( serialize( $r ) );
	if ( $cache = wp_cache_get( 'get_pages', 'posts' ) )
		if ( isset( $cache[ $key ] ) )
			return apply_filters('get_pages', $cache[ $key ], $r );

	$inclusions = '';
	if ( !empty($include) ) {
		$child_of = 0; //ignore child_of, exclude, meta_key, and meta_value params if using include
		$exclude = '';
		$meta_key = '';
		$meta_value = '';
		$hierarchical = false;
		$incpages = preg_split('/[\s,]+/',$include);
		if ( count($incpages) ) {
			foreach ( $incpages as $incpage ) {
				if (empty($inclusions))
					$inclusions = $wpdb->prepare(' AND ( ID = %d ', $incpage);
				else
					$inclusions .= $wpdb->prepare(' OR ID = %d ', $incpage);
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
					$exclusions = $wpdb->prepare(' AND ( ID <> %d ', $expage);
				else
					$exclusions .= $wpdb->prepare(' AND ID <> %d ', $expage);
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
					$author_query = $wpdb->prepare(' post_author = %d ', $post_author);
				else
					$author_query .= $wpdb->prepare(' OR post_author = %d ', $post_author);
			}
			if ( '' != $author_query )
				$author_query = " AND ($author_query)";
		}
	}

	$query = "SELECT * FROM $wpdb->posts " ;
	$query .= ( empty( $meta_key ) ? "" : ", $wpdb->postmeta " ) ;
	$query .= " WHERE (post_type = 'page' AND post_status = 'publish') $exclusions $inclusions " ;
	// expected_slashed ($meta_key, $meta_value) -- also, it looks funky
	$query .= ( empty( $meta_key ) | empty($meta_value)  ? "" : " AND ($wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key = '$meta_key' AND $wpdb->postmeta.meta_value = '$meta_value' )" ) ;
	$query .= $author_query;
	$query .= " ORDER BY " . $sort_column . " " . $sort_order ;

	$pages = $wpdb->get_results($query);

	if ( empty($pages) )
		return apply_filters('get_pages', array(), $r);

	// Update cache.
	update_page_cache($pages);

	if ( $child_of || $hierarchical )
		$pages = & get_page_children($child_of, $pages);

	$cache[ $key ] = $pages;
	wp_cache_set( 'get_pages', $cache, 'posts' );

	$pages = apply_filters('get_pages', $pages, $r);

	return $pages;
}

//
// Attachment functions
//

/**
 * is_local_attachment() - Check if the attachment URI is local one and is really an attachment.
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.0
 *
 * @param string $url URL to check
 * @return bool {@internal Missing Description}}
 */
function is_local_attachment($url) {
	if (strpos($url, get_bloginfo('url')) === false)
		return false;
	if (strpos($url, get_bloginfo('url') . '/?attachment_id=') !== false)
		return true;
	if ( $id = url_to_postid($url) ) {
		$post = & get_post($id);
		if ( 'attachment' == $post->post_type )
			return true;
	}
	return false;
}

/**
 * wp_insert_attachment() - Insert an attachment
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.0
 *
 * @uses $wpdb
 * @uses $user_ID
 *
 * @param object $object attachment object
 * @param string $file filename
 * @param int $post_parent parent post ID
 * @return int {@internal Missing Description}}
 */
function wp_insert_attachment($object, $file = false, $parent = 0) {
	global $wpdb, $user_ID;

	$defaults = array('post_status' => 'draft', 'post_type' => 'post', 'post_author' => $user_ID,
		'ping_status' => get_option('default_ping_status'), 'post_parent' => 0,
		'menu_order' => 0, 'to_ping' =>  '', 'pinged' => '', 'post_password' => '',
		'guid' => '', 'post_content_filtered' => '', 'post_excerpt' => '');

	$object = wp_parse_args($object, $defaults);
	if ( !empty($parent) )
		$object['post_parent'] = $parent;

	$object = sanitize_post($object, 'db');

	// export array as variables
	extract($object, EXTR_SKIP);

	// Make sure we set a valid category
	if (0 == count($post_category) || !is_array($post_category)) {
		$post_category = array(get_option('default_category'));
	}

	if ( empty($post_author) )
		$post_author = $user_ID;

	$post_type = 'attachment';
	$post_status = 'inherit';

	// Are we updating or creating?
	$update = false;
	if ( !empty($ID) ) {
		$update = true;
		$post_ID = (int) $ID;
	}

	// Create a valid post name.
	if ( empty($post_name) )
		$post_name = sanitize_title($post_title);
	else
		$post_name = sanitize_title($post_name);

	// expected_slashed ($post_name)
	$post_name_check = $wpdb->get_var( $wpdb->prepare( "SELECT post_name FROM $wpdb->posts WHERE post_name = '$post_name' AND post_status = 'inherit' AND ID != %d LIMIT 1", $post_ID));

	if ($post_name_check) {
		$suffix = 2;
		while ($post_name_check) {
			$alt_post_name = $post_name . "-$suffix";
			// expected_slashed ($alt_post_name, $post_name)
			$post_name_check = $wpdb->get_var( $wpdb->prepare( "SELECT post_name FROM $wpdb->posts WHERE post_name = '$alt_post_name' AND post_status = 'inherit' AND ID != %d AND post_parent = %d LIMIT 1", $post_ID, $post_parent));
			$suffix++;
		}
		$post_name = $alt_post_name;
	}

	if ( empty($post_date) )
		$post_date = current_time('mysql');
	if ( empty($post_date_gmt) )
		$post_date_gmt = current_time('mysql', 1);

	if ( empty($post_modified) )
                $post_modified = $post_date;
	if ( empty($post_modified_gmt) )
                $post_modified_gmt = $post_date_gmt;

	if ( empty($comment_status) ) {
		if ( $update )
			$comment_status = 'closed';
		else
			$comment_status = get_option('default_comment_status');
	}
	if ( empty($ping_status) )
		$ping_status = get_option('default_ping_status');

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

	if ( ! isset($pinged) )
		$pinged = '';

	// expected_slashed (everything!)
	$data = compact( array( 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_content_filtered', 'post_title', 'post_excerpt', 'post_status', 'post_type', 'comment_status', 'ping_status', 'post_password', 'post_name', 'to_ping', 'pinged', 'post_modified', 'post_modified_gmt', 'post_parent', 'menu_order', 'post_mime_type', 'guid' ) );
	$data = stripslashes_deep( $data );

	if ( $update ) {
		$wpdb->update( $wpdb->posts, $data, array( 'ID' => $post_ID ) );
	} else {
		$wpdb->insert( $wpdb->posts, $data );
		$post_ID = (int) $wpdb->insert_id;
	}

	if ( empty($post_name) ) {
		$post_name = sanitize_title($post_title, $post_ID);
		$wpdb->update( $wpdb->posts, compact("post_name"), array( 'ID' => $post_ID ) );
	}

	wp_set_post_categories($post_ID, $post_category);

	if ( $file )
		update_attached_file( $post_ID, $file );
		
	clean_post_cache($post_ID);

	if ( $update) {
		do_action('edit_attachment', $post_ID);
	} else {
		do_action('add_attachment', $post_ID);
	}

	return $post_ID;
}

/**
 * wp_delete_attachment() - Delete an attachment
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.0
 * @uses $wpdb
 *
 * @param int $postid attachment Id
 * @return mixed {@internal Missing Description}}
 */
function wp_delete_attachment($postid) {
	global $wpdb;

	if ( !$post = $wpdb->get_row(  $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d", $postid)) )
		return $post;

	if ( 'attachment' != $post->post_type )
		return false;

	$meta = wp_get_attachment_metadata( $postid );
	$file = get_attached_file( $postid );

	/** @todo Delete for pluggable post taxonomies too */
	wp_delete_object_term_relationships($postid, array('category', 'post_tag'));

	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE ID = %d", $postid ));

	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->comments WHERE comment_post_ID = %d", $postid ));

	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d ", $postid ));

	if ( ! empty($meta['thumb']) ) {
		// Don't delete the thumb if another attachment uses it
		if (! $wpdb->get_row( $wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attachment_metadata' AND meta_value LIKE %s AND post_id <> %d", '%'.$meta['thumb'].'%', $postid)) ) {
			$thumbfile = str_replace(basename($file), $meta['thumb'], $file);
			$thumbfile = apply_filters('wp_delete_file', $thumbfile);
			@ unlink($thumbfile);
		}
	}

	// remove intermediate images if there are any
	$sizes = apply_filters('intermediate_image_sizes', array('thumbnail', 'medium'));
	foreach ( $sizes as $size ) {
		if ( $intermediate = image_get_intermediate_size($postid, $size) ) {
			$intermediate_file = apply_filters('wp_delete_file', $intermediate['path']);
			@ unlink($intermediate_file);
		}
	}

	$file = apply_filters('wp_delete_file', $file);

	if ( ! empty($file) )
		@ unlink($file);

	clean_post_cache($postid);

	do_action('delete_attachment', $postid);

	return $post;
}

/**
 * wp_get_attachment_metadata() - Retrieve metadata for an attachment
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.1
 *
 * @param int $post_id attachment ID
 * @param bool $unfiltered Optional, default is false. If true, filters are not run
 * @return array {@internal Missing Description}}
 */
function wp_get_attachment_metadata( $post_id, $unfiltered = false ) {
	$post_id = (int) $post_id;
	if ( !$post =& get_post( $post_id ) )
		return false;

	$data = get_post_meta( $post->ID, '_wp_attachment_metadata', true );
	if ( $unfiltered )
		return $data;
	return apply_filters( 'wp_get_attachment_metadata', $data, $post->ID );
}

/**
 * wp_update_attachment_metadata() - Update metadata for an attachment
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.1
 *
 * @param int $post_id attachment ID
 * @param array $data attachment data
 * @return int {@internal Missing Description}}
 */
function wp_update_attachment_metadata( $post_id, $data ) {
	$post_id = (int) $post_id;
	if ( !$post =& get_post( $post_id ) )
		return false;

	$old_data = wp_get_attachment_metadata( $post->ID, true );

	$data = apply_filters( 'wp_update_attachment_metadata', $data, $post->ID );

	if ( $old_data )
		return update_post_meta( $post->ID, '_wp_attachment_metadata', $data, $old_data );
	else
		return add_post_meta( $post->ID, '_wp_attachment_metadata', $data );
}

/**
 * wp_get_attachment_url() - Retrieve the URL for an attachment
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.1
 *
 * @param int $post_id attachment ID
 * @return string {@internal Missing Description}}
 */
function wp_get_attachment_url( $post_id = 0 ) {
	$post_id = (int) $post_id;
	if ( !$post =& get_post( $post_id ) )
		return false;

	$url = get_the_guid( $post->ID );

	if ( 'attachment' != $post->post_type || !$url )
		return false;

	return apply_filters( 'wp_get_attachment_url', $url, $post->ID );
}

/**
 * wp_get_attachment_thumb_file() - Retrieve thumbnail for an attachment
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.1
 *
 * @param int $post_id attachment ID
 * @return mixed {@internal Missing Description}}
 */
function wp_get_attachment_thumb_file( $post_id = 0 ) {
	$post_id = (int) $post_id;
	if ( !$post =& get_post( $post_id ) )
		return false;
	if ( !$imagedata = wp_get_attachment_metadata( $post->ID ) )
		return false;

	$file = get_attached_file( $post->ID );

	if ( !empty($imagedata['thumb']) && ($thumbfile = str_replace(basename($file), $imagedata['thumb'], $file)) && file_exists($thumbfile) )
		return apply_filters( 'wp_get_attachment_thumb_file', $thumbfile, $post->ID );
	return false;
}

/**
 * wp_get_attachment_thumb_url() - Retrieve URL for an attachment thumbnail
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.1
 *
 * @param int $post_id attachment ID
 * @return string {@internal Missing Description}}
 */
function wp_get_attachment_thumb_url( $post_id = 0 ) {
	$post_id = (int) $post_id;
	if ( !$post =& get_post( $post_id ) )
		return false;
	if ( !$url = wp_get_attachment_url( $post->ID ) )
		return false;
		
	$sized = image_downsize( $post_id, 'thumbnail' );
	if ( $sized )
		return $sized[0];

	if ( !$thumb = wp_get_attachment_thumb_file( $post->ID ) )
		return false;

	$url = str_replace(basename($url), basename($thumb), $url);

	return apply_filters( 'wp_get_attachment_thumb_url', $url, $post->ID );
}

/**
 * wp_attachment_is_image() - Check if the attachment is an image
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.1
 *
 * @param int $post_id attachment ID
 * @return bool {@internal Missing Description}}
 */
function wp_attachment_is_image( $post_id = 0 ) {
	$post_id = (int) $post_id;
	if ( !$post =& get_post( $post_id ) )
		return false;

	if ( !$file = get_attached_file( $post->ID ) )
		return false;

	$ext = preg_match('/\.([^.]+)$/', $file, $matches) ? strtolower($matches[1]) : false;

	$image_exts = array('jpg', 'jpeg', 'gif', 'png');

	if ( 'image/' == substr($post->post_mime_type, 0, 6) || $ext && 'import' == $post->post_mime_type && in_array($ext, $image_exts) )
		return true;
	return false;
}

/**
 * wp_mime_type_icon() - Retrieve the icon for a MIME type
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.1
 *
 * @param string $mime MIME type
 * @return string|bool {@internal Missing Description}}
 */
function wp_mime_type_icon( $mime = 0 ) {
	if ( !is_numeric($mime) )
		$icon = wp_cache_get("mime_type_icon_$mime");
	if ( empty($icon) ) {
		$post_id = 0;
		$post_mimes = array();
		if ( is_numeric($mime) ) {
			$mime = (int) $mime;
			if ( $post =& get_post( $mime ) ) {
				$post_id = (int) $post->ID;
				$ext = preg_replace('/^.+?\.([^.]+)$/', '$1', $post->guid);
				if ( !empty($ext) ) {
					$post_mimes[] = $ext;
					if ( $ext_type = wp_ext2type( $ext ) )
						$post_mimes[] = $ext_type;
				}
				$mime = $post->post_mime_type;
			} else {
				$mime = 0;
			}
		} else {
			$post_mimes[] = $mime;
		}

		$icon_files = wp_cache_get('icon_files');

		if ( !is_array($icon_files) ) {
			$icon_dir = apply_filters( 'icon_dir', ABSPATH . WPINC . '/images/crystal' );
			$icon_dir_uri = apply_filters( 'icon_dir_uri', trailingslashit(get_option('siteurl')) . WPINC . '/images/crystal' );
			$dirs = apply_filters( 'icon_dirs', array($icon_dir => $icon_dir_uri) );
			$icon_files = array();
			while ( $dirs ) {
				$dir = array_shift($keys = array_keys($dirs));
				$uri = array_shift($dirs);
				if ( $dh = opendir($dir) ) {
					while ( false !== $file = readdir($dh) ) {
						$file = basename($file);
						if ( substr($file, 0, 1) == '.' )
							continue;
						if ( !in_array(strtolower(substr($file, -4)), array('.png', '.gif', '.jpg') ) ) {
							if ( is_dir("$dir/$file") )
								$dirs["$dir/$file"] = "$uri/$file";
							continue;
						}
						$icon_files["$dir/$file"] = "$uri/$file";
					}
					closedir($dh);
				}
			}
			wp_cache_set('icon_files', $icon_files, 600);
		}

		// Icon basename - extension = MIME wildcard
		foreach ( $icon_files as $file => $uri )
			$types[ preg_replace('/^([^.]*).*$/', '$1', basename($file)) ] =& $icon_files[$file];

		if ( ! empty($mime) ) {
			$post_mimes[] = substr($mime, 0, strpos($mime, '/'));
			$post_mimes[] = substr($mime, strpos($mime, '/') + 1);
			$post_mimes[] = str_replace('/', '_', $mime);
		}

		$matches = wp_match_mime_types(array_keys($types), $post_mimes);
		$matches['default'] = array('default');

		foreach ( $matches as $match => $wilds ) {
			if ( isset($types[$wilds[0]])) {
				$icon = $types[$wilds[0]];
				if ( !is_numeric($mime) )
					wp_cache_set("mime_type_icon_$mime", $icon);
				break;
			}
		}
	}

	return apply_filters( 'wp_mime_type_icon', $icon, $mime, $post_id ); // Last arg is 0 if function pass mime type.
}

/**
 * wp_check_for_changed_slugs() - {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.1
 *
 * @param int $post_id The Post ID
 * @return int Same as $post_id
 */
function wp_check_for_changed_slugs($post_id) {
	if ( !isset($_POST['wp-old-slug']) || !strlen($_POST['wp-old-slug']) )
		return $post_id;

	$post = &get_post($post_id);

	// we're only concerned with published posts
	if ( $post->post_status != 'publish' || $post->post_type != 'post' )
		return $post_id;

	// only bother if the slug has changed
	if ( $post->post_name == $_POST['wp-old-slug'] )
		return $post_id;

	$old_slugs = (array) get_post_meta($post_id, '_wp_old_slug');

	// if we haven't added this old slug before, add it now
	if ( !count($old_slugs) || !in_array($_POST['wp-old-slug'], $old_slugs) )
		add_post_meta($post_id, '_wp_old_slug', $_POST['wp-old-slug']);

	// if the new slug was used previously, delete it from the list
	if ( in_array($post->post_name, $old_slugs) )
		delete_post_meta($post_id, '_wp_old_slug', $post->post_name);

	return $post_id;
}

/**
 * get_private_posts_cap_sql() - {@internal Missing Short Description}}
 *
 * This function provides a standardized way to appropriately select on
 * the post_status of posts/pages. The function will return a piece of
 * SQL code that can be added to a WHERE clause; this SQL is constructed
 * to allow all published posts, and all private posts to which the user
 * has access.
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.2
 *
 * @uses $user_ID
 * @uses apply_filters() Call 'pub_priv_sql_capability' filter for plugins with different post types
 *
 * @param string $post_type currently only supports 'post' or 'page'.
 * @return string SQL code that can be added to a where clause.
 */
function get_private_posts_cap_sql($post_type) {
	global $user_ID;
	$cap = '';

	// Private posts
	if ($post_type == 'post') {
		$cap = 'read_private_posts';
	// Private pages
	} elseif ($post_type == 'page') {
		$cap = 'read_private_pages';
	// Dunno what it is, maybe plugins have their own post type?
	} else {
		$cap = apply_filters('pub_priv_sql_capability', $cap);

		if (empty($cap)) {
			// We don't know what it is, filters don't change anything,
			// so set the SQL up to return nothing.
			return '1 = 0';
		}
	}

	$sql = '(post_status = \'publish\'';

	if (current_user_can($cap)) {
		// Does the user have the capability to view private posts? Guess so.
		$sql .= ' OR post_status = \'private\'';
	} elseif (is_user_logged_in()) {
		// Users can view their own private posts.
		$sql .= ' OR post_status = \'private\' AND post_author = \'' . $user_ID . '\'';
	}

	$sql .= ')';

	return $sql;
}

/**
 * get_lastpostdate() - {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 0.71
 *
 * @uses $wpdb
 * @uses $blog_id
 * @uses apply_filters() Calls 'get_lastpostdate' filter
 *
 * @global mixed $cache_lastpostdate Stores the last post date
 * @global mixed $pagenow The current page being viewed
 *
 * @param string $timezone The location to get the time. Can be 'gmt', 'blog', or 'server'.
 * @return string The date of the last post.
 */
function get_lastpostdate($timezone = 'server') {
	global $cache_lastpostdate, $wpdb, $blog_id;
	$add_seconds_server = date('Z');
	if ( !isset($cache_lastpostdate[$blog_id][$timezone]) ) {
		switch(strtolower($timezone)) {
			case 'gmt':
				$lastpostdate = $wpdb->get_var("SELECT post_date_gmt FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1");
				break;
			case 'blog':
				$lastpostdate = $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1");
				break;
			case 'server':
				$lastpostdate = $wpdb->get_var("SELECT DATE_ADD(post_date_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1");
				break;
		}
		$cache_lastpostdate[$blog_id][$timezone] = $lastpostdate;
	} else {
		$lastpostdate = $cache_lastpostdate[$blog_id][$timezone];
	}
	return apply_filters( 'get_lastpostdate', $lastpostdate, $timezone );
}

/**
 * get_lastpostmodified() - {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.2
 *
 * @uses $wpdb
 * @uses $blog_id
 * @uses apply_filters() Calls 'get_lastpostmodified' filter
 *
 * @global mixed $cache_lastpostmodified Stores the date the last post was modified
 * @global mixed $pagenow The current page being viewed
 *
 * @param string $timezone The location to get the time. Can be 'gmt', 'blog', or 'server'.
 * @return string The date the post was last modified.
 */
function get_lastpostmodified($timezone = 'server') {
	global $cache_lastpostmodified, $wpdb, $blog_id;
	$add_seconds_server = date('Z');
	if ( !isset($cache_lastpostmodified[$blog_id][$timezone]) ) {
		switch(strtolower($timezone)) {
			case 'gmt':
				$lastpostmodified = $wpdb->get_var("SELECT post_modified_gmt FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_modified_gmt DESC LIMIT 1");
				break;
			case 'blog':
				$lastpostmodified = $wpdb->get_var("SELECT post_modified FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_modified_gmt DESC LIMIT 1");
				break;
			case 'server':
				$lastpostmodified = $wpdb->get_var("SELECT DATE_ADD(post_modified_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_modified_gmt DESC LIMIT 1");
				break;
		}
		$lastpostdate = get_lastpostdate($timezone);
		if ( $lastpostdate > $lastpostmodified ) {
			$lastpostmodified = $lastpostdate;
		}
		$cache_lastpostmodified[$blog_id][$timezone] = $lastpostmodified;
	} else {
		$lastpostmodified = $cache_lastpostmodified[$blog_id][$timezone];
	}
	return apply_filters( 'get_lastpostmodified', $lastpostmodified, $timezone );
}

/**
 * update_post_cache() - Updates posts in cache
 *
 * @usedby update_page_cache() update_page_cache() aliased by this function.
 *
 * @package WordPress
 * @subpackage Cache
 * @since 1.5.1
 *
 * @param array $posts Array of post objects
 */
function update_post_cache(&$posts) {
	if ( !$posts )
		return;

	foreach ( $posts as $post )
		wp_cache_add($post->ID, $post, 'posts');
}

/**
 * clean_post_cache() - Will clean the post in the cache
 *
 * Cleaning means delete from the cache of the post. Will call to clean
 * the term object cache associated with the post ID.
 *
 * @package WordPress
 * @subpackage Cache
 * @since 2.0
 *
 * @uses do_action() Will call the 'clean_post_cache' hook action.
 *
 * @param int $id The Post ID in the cache to clean
 */
function clean_post_cache($id) {
	global $wpdb;
	$id = (int) $id;

	wp_cache_delete($id, 'posts');
	wp_cache_delete($id, 'post_meta');

	clean_object_term_cache($id, 'post');

	wp_cache_delete( 'wp_get_archives', 'general' );

	do_action('clean_post_cache', $id);

	if ( $children = $wpdb->get_col( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_parent = %d", $id) ) ) {
		foreach( $children as $cid )
			clean_post_cache( $cid );
	}
}

/**
 * update_page_cache() - Alias of update_post_cache()
 *
 * @see update_post_cache() Posts and pages are the same, alias is intentional
 *
 * @package WordPress
 * @subpackage Cache
 * @since 1.5.1
 *
 * @param array $pages list of page objects
 */
function update_page_cache(&$pages) {
	update_post_cache($pages);
}

/**
 * clean_page_cache() - Will clean the page in the cache
 *
 * Clean (read: delete) page from cache that matches $id. Will also clean
 * cache associated with 'all_page_ids' and 'get_pages'.
 *
 * @package WordPress
 * @subpackage Cache
 * @since 2.0
 *
 * @uses do_action() Will call the 'clean_page_cache' hook action.
 *
 * @param int $id Page ID to clean
 */
function clean_page_cache($id) {
	clean_post_cache($id);

	wp_cache_delete( 'all_page_ids', 'posts' );
	wp_cache_delete( 'get_pages', 'posts' );

	do_action('clean_page_cache', $id);
}

/**
 * update_post_caches() - Call major cache updating functions for list of Post objects.
 *
 * @package WordPress
 * @subpackage Cache
 * @since 1.5
 *
 * @uses $wpdb
 * @uses update_post_cache()
 * @uses update_object_term_cache()
 * @uses update_postmeta_cache()
 *
 * @param array $posts Array of Post objects
 */
function update_post_caches(&$posts) {
	// No point in doing all this work if we didn't match any posts.
	if ( !$posts )
		return;

	update_post_cache($posts);

	$post_ids = array();

	for ($i = 0; $i < count($posts); $i++)
		$post_ids[] = $posts[$i]->ID;

	update_object_term_cache($post_ids, 'post');

	update_postmeta_cache($post_ids);
}

/**
 * update_postmeta_cache() - {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Cache
 * @since 2.1
 *
 * @uses $wpdb
 *
 * @param array $post_ids {@internal Missing Description}}
 * @return bool|array Returns false if there is nothing to update or an array of metadata
 */
function update_postmeta_cache($post_ids) {
	global $wpdb;

	if ( empty( $post_ids ) )
		return false;

	if ( !is_array($post_ids) ) {
		$post_ids = preg_replace('|[^0-9,]|', '', $post_ids);
		$post_ids = explode(',', $post_ids);
	}

	$post_ids = array_map('intval', $post_ids);

	$ids = array();
	foreach ( (array) $post_ids as $id ) {
		if ( false === wp_cache_get($id, 'post_meta') )
			$ids[] = $id;
	}

	if ( empty( $ids ) )
		return false;

	// Get post-meta info
	$id_list = join(',', $ids);
	$cache = array();
	if ( $meta_list = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta WHERE post_id IN ($id_list) ORDER BY post_id, meta_key", ARRAY_A) ) {
		foreach ( (array) $meta_list as $metarow) {
			$mpid = (int) $metarow['post_id'];
			$mkey = $metarow['meta_key'];
			$mval = $metarow['meta_value'];

			// Force subkeys to be array type:
			if ( !isset($cache[$mpid]) || !is_array($cache[$mpid]) )
				$cache[$mpid] = array();
			if ( !isset($cache[$mpid][$mkey]) || !is_array($cache[$mpid][$mkey]) )
				$cache[$mpid][$mkey] = array();

			// Add a value to the current pid/key:
			$cache[$mpid][$mkey][] = $mval;
		}
	}

	foreach ( (array) $ids as $id ) {
		if ( ! isset($cache[$id]) )
			$cache[$id] = array();
	}

	foreach ( array_keys($cache) as $post)
		wp_cache_set($post, $cache[$post], 'post_meta');

	return $cache;
}

//
// Hooks
//

/**
 * _transition_post_status() - Hook {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.3
 *
 * @uses $wpdb
 *
 * @param string $new_status {@internal Missing Description}}
 * @param string $old_status {@internal Missing Description}}
 * @param object $post Object type containing the post information
 */
function _transition_post_status($new_status, $old_status, $post) {
	global $wpdb;

	if ( $old_status != 'publish' && $new_status == 'publish' ) {
		// Reset GUID if transitioning to publish and it is empty
		if ( '' == get_the_guid($post->ID) )
			$wpdb->update( $wpdb->posts, array( 'guid' => get_permalink( $post->ID ) ), array( 'ID' => $post->ID ) );
		do_action('private_to_published', $post->ID);  // Deprecated, use private_to_publish
	}

	// Always clears the hook in case the post status bounced from future to draft.
	wp_clear_scheduled_hook('publish_future_post', $post->ID);
}

/**
 * _future_post_hook() - Hook used to schedule publication for a post marked for the future.
 *
 * The $post properties used and must exist are 'ID' and 'post_date_gmt'.
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.3
 *
 * @param int $post_id Not Used. Can be set to null.
 * @param object $post Object type containing the post information
 */
function _future_post_hook($deprecated = '', $post) {
	wp_clear_scheduled_hook( 'publish_future_post', $post->ID );
	wp_schedule_single_event(strtotime($post->post_date_gmt. ' GMT'), 'publish_future_post', array($post->ID));
}

/**
 * _publish_post_hook() - Hook {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.3
 *
 * @uses $wpdb
 * @uses XMLRPC_REQUEST
 * @uses APP_REQUEST
 * @uses do_action Calls 'xmlprc_publish_post' action if XMLRPC_REQUEST is defined. Calls 'app_publish_post'
 *	action if APP_REQUEST is defined.
 *
 * @param int $post_id The ID in the database table of the post being published
 */
function _publish_post_hook($post_id) {
	global $wpdb;

	if ( defined('XMLRPC_REQUEST') )
		do_action('xmlrpc_publish_post', $post_id);
	if ( defined('APP_REQUEST') )
		do_action('app_publish_post', $post_id);

	if ( defined('WP_IMPORTING') )
		return;

	$data = array( 'post_id' => $post_id, 'meta_value' => '1' );
	if ( get_option('default_pingback_flag') )
		$wpdb->insert( $wpdb->postmeta, $data + array( 'meta_key' => '_pingme' ) );
	$wpdb->insert( $wpdb->postmeta, $data + array( 'meta_key' => '_encloseme' ) );
	wp_schedule_single_event(time(), 'do_pings');
}

/**
 * _save_post_hook() - Hook used to prevent page/post cache and rewrite rules from staying dirty
 *
 * Does two things. If the post is a page and has a template then it will update/add that
 * template to the meta. For both pages and posts, it will clean the post cache to make sure
 * that the cache updates to the changes done recently. For pages, the rewrite rules of
 * WordPress are flushed to allow for any changes.
 *
 * The $post parameter, only uses 'post_type' property and 'page_template' property.
 *
 * @package WordPress
 * @subpackage Post
 * @since 2.3
 *
 * @uses $wp_rewrite Flushes Rewrite Rules.
 *
 * @param int $post_id The ID in the database table for the $post
 * @param object $post Object type containing the post information
 */
function _save_post_hook($post_id, $post) {
	if ( $post->post_type == 'page' ) {
		if ( !empty($post->page_template) )
			if ( ! update_post_meta($post_id, '_wp_page_template',  $post->page_template))
				add_post_meta($post_id, '_wp_page_template',  $post->page_template, true);

		clean_page_cache($post_id);
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	} else {
		clean_post_cache($post_id);
	}
}

//
// Private
//

function _get_post_ancestors(&$_post) {
	global $wpdb;

	if ( isset($_post->ancestors) )
		return;

	$_post->ancestors = array();

	if ( empty($_post->post_parent) || $_post->ID == $_post->post_parent )
		return;

	$id = $_post->ancestors[] = $_post->post_parent;
	while ( $ancestor = $wpdb->get_var( $wpdb->prepare("SELECT `post_parent` FROM $wpdb->posts WHERE ID = %d LIMIT 1", $id) ) ) {
		if ( $id == $ancestor )
			break;
		$id = $_post->ancestors[] = $ancestor;
	}
}

?>

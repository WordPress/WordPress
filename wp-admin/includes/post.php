<?php
/**
 * WordPress Post Administration API.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Rename $_POST data from form names to DB post columns.
 *
 * Manipulates $_POST directly.
 *
 * @package WordPress
 * @since 2.6.0
 *
 * @param bool $update Are we updating a pre-existing post?
 * @param post_data array Array of post data. Defaults to the contents of $_POST.
 * @return object|bool WP_Error on failure, true on success.
 */
function _wp_translate_postdata( $update = false, $post_data = null ) {

	if ( empty($post_data) )
		$post_data = &$_POST;

	if ( $update )
		$post_data['ID'] = (int) $post_data['post_ID'];
	$post_data['post_content'] = isset($post_data['content']) ? $post_data['content'] : '';
	$post_data['post_excerpt'] = isset($post_data['excerpt']) ? $post_data['excerpt'] : '';
	$post_data['post_parent'] = isset($post_data['parent_id'])? $post_data['parent_id'] : '';
	if ( isset($post_data['trackback_url']) )
		$post_data['to_ping'] = $post_data['trackback_url'];

	if (!empty ( $post_data['post_author_override'] ) ) {
		$post_data['post_author'] = (int) $post_data['post_author_override'];
	} else {
		if (!empty ( $post_data['post_author'] ) ) {
			$post_data['post_author'] = (int) $post_data['post_author'];
		} else {
			$post_data['post_author'] = (int) $post_data['user_ID'];
		}
	}

	if ( isset($post_data['user_ID']) && ($post_data['post_author'] != $post_data['user_ID']) ) {
		if ( 'page' == $post_data['post_type'] ) {
			if ( !current_user_can( 'edit_others_pages' ) ) {
				return new WP_Error( 'edit_others_pages', $update ?
					__( 'You are not allowed to edit pages as this user.' ) :
					__( 'You are not allowed to create pages as this user.' )
				);
			}
		} else {
			if ( !current_user_can( 'edit_others_posts' ) ) {
				return new WP_Error( 'edit_others_posts', $update ?
					__( 'You are not allowed to edit posts as this user.' ) :
					__( 'You are not allowed to post as this user.' )
				);
			}
		}
	}

	// What to do based on which button they pressed
	if ( isset($post_data['saveasdraft']) && '' != $post_data['saveasdraft'] )
		$post_data['post_status'] = 'draft';
	if ( isset($post_data['saveasprivate']) && '' != $post_data['saveasprivate'] )
		$post_data['post_status'] = 'private';
	if ( isset($post_data['publish']) && ( '' != $post_data['publish'] ) && ( $post_data['post_status'] != 'private' ) )
		$post_data['post_status'] = 'publish';
	if ( isset($post_data['advanced']) && '' != $post_data['advanced'] )
		$post_data['post_status'] = 'draft';
	if ( isset($post_data['pending']) && '' != $post_data['pending'] )
		$post_data['post_status'] = 'pending';

	$previous_status = get_post_field('post_status',  isset($post_data['ID']) ? $post_data['ID'] : $post_data['temp_ID']);

	// Posts 'submitted for approval' present are submitted to $_POST the same as if they were being published.
	// Change status from 'publish' to 'pending' if user lacks permissions to publish or to resave published posts.
	if ( 'page' == $post_data['post_type'] ) {
		$publish_cap = 'publish_pages';
		$edit_cap = 'edit_published_pages';
	} else {
		$publish_cap = 'publish_posts';
		$edit_cap = 'edit_published_posts';
	}
	if ( isset($post_data['post_status']) && ('publish' == $post_data['post_status'] && !current_user_can( $publish_cap )) )
		if ( $previous_status != 'publish' || !current_user_can( $edit_cap ) )
			$post_data['post_status'] = 'pending';

	if ( ! isset($post_data['post_status']) )
		$post_data['post_status'] = $previous_status;

	if (!isset( $post_data['comment_status'] ))
		$post_data['comment_status'] = 'closed';

	if (!isset( $post_data['ping_status'] ))
		$post_data['ping_status'] = 'closed';

	foreach ( array('aa', 'mm', 'jj', 'hh', 'mn') as $timeunit ) {
		if ( !empty( $post_data['hidden_' . $timeunit] ) && $post_data['hidden_' . $timeunit] != $post_data[$timeunit] ) {
			$post_data['edit_date'] = '1';
			break;
		}
	}

	if ( !empty( $post_data['edit_date'] ) ) {
		$aa = $post_data['aa'];
		$mm = $post_data['mm'];
		$jj = $post_data['jj'];
		$hh = $post_data['hh'];
		$mn = $post_data['mn'];
		$ss = $post_data['ss'];
		$aa = ($aa <= 0 ) ? date('Y') : $aa;
		$mm = ($mm <= 0 ) ? date('n') : $mm;
		$jj = ($jj > 31 ) ? 31 : $jj;
		$jj = ($jj <= 0 ) ? date('j') : $jj;
		$hh = ($hh > 23 ) ? $hh -24 : $hh;
		$mn = ($mn > 59 ) ? $mn -60 : $mn;
		$ss = ($ss > 59 ) ? $ss -60 : $ss;
		$post_data['post_date'] = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $aa, $mm, $jj, $hh, $mn, $ss );
		$post_data['post_date_gmt'] = get_gmt_from_date( $post_data['post_date'] );
	}

	return $post_data;
}

/**
 * Update an existing post with values provided in $_POST.
 *
 * @since unknown
 *
 * @param array $post_data Optional.
 * @return int Post ID.
 */
function edit_post( $post_data = null ) {

	if ( empty($post_data) )
		$post_data = &$_POST;

	$post_ID = (int) $post_data['post_ID'];

	if ( 'page' == $post_data['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_ID ) )
			wp_die( __('You are not allowed to edit this page.' ));
	} else {
		if ( !current_user_can( 'edit_post', $post_ID ) )
			wp_die( __('You are not allowed to edit this post.' ));
	}

	// Autosave shouldn't save too soon after a real save
	if ( 'autosave' == $post_data['action'] ) {
		$post =& get_post( $post_ID );
		$now = time();
		$then = strtotime($post->post_date_gmt . ' +0000');
		$delta = AUTOSAVE_INTERVAL / 2;
		if ( ($now - $then) < $delta )
			return $post_ID;
	}

	$post_data = _wp_translate_postdata( true, $post_data );
	if ( is_wp_error($post_data) )
		wp_die( $post_data->get_error_message() );

	if ( isset($post_data['visibility']) ) {
		switch ( $post_data['visibility'] ) {
			case 'public' :
				$post_data['post_password'] = '';
				break;
			case 'password' :
				unset( $post_data['sticky'] );
				break;
			case 'private' :
				$post_data['post_status'] = 'private';
				$post_data['post_password'] = '';
				unset( $post_data['sticky'] );
				break;
		}
	}

	// Meta Stuff
	if ( isset($post_data['meta']) && $post_data['meta'] ) {
		foreach ( $post_data['meta'] as $key => $value )
			update_meta( $key, $value['key'], $value['value'] );
	}

	if ( isset($post_data['deletemeta']) && $post_data['deletemeta'] ) {
		foreach ( $post_data['deletemeta'] as $key => $value )
			delete_meta( $key );
	}

	add_meta( $post_ID );

	wp_update_post( $post_data );

	// Reunite any orphaned attachments with their parent
	if ( !$draft_ids = get_user_option( 'autosave_draft_ids' ) )
		$draft_ids = array();
	if ( $draft_temp_id = (int) array_search( $post_ID, $draft_ids ) )
		_relocate_children( $draft_temp_id, $post_ID );

	// Now that we have an ID we can fix any attachment anchor hrefs
	_fix_attachment_links( $post_ID );

	wp_set_post_lock( $post_ID, $GLOBALS['current_user']->ID );

	if ( current_user_can( 'edit_others_posts' ) ) {
		if ( !empty($post_data['sticky']) )
			stick_post($post_ID);
		else
			unstick_post($post_ID);
	}

	return $post_ID;
}

/**
 * {@internal Missing Short Description}}
 *
 * Updates all bulk edited posts/pages, adding (but not removing) tags and
 * categories. Skips pages when they would be their own parent or child.
 *
 * @since unknown
 *
 * @return array
 */
function bulk_edit_posts( $post_data = null ) {
	global $wpdb;

	if ( empty($post_data) )
		$post_data = &$_POST;

	if ( isset($post_data['post_type']) && 'page' == $post_data['post_type'] ) {
		if ( ! current_user_can( 'edit_pages' ) )
			wp_die( __('You are not allowed to edit pages.') );
	} else {
		if ( ! current_user_can( 'edit_posts' ) )
			wp_die( __('You are not allowed to edit posts.') );
	}

	$post_IDs = array_map( 'intval', (array) $post_data['post'] );

	$reset = array( 'post_author', 'post_status', 'post_password', 'post_parent', 'page_template', 'comment_status', 'ping_status', 'keep_private', 'tags_input', 'post_category', 'sticky' );
	foreach ( $reset as $field ) {
		if ( isset($post_data[$field]) && ( '' == $post_data[$field] || -1 == $post_data[$field] ) )
			unset($post_data[$field]);
	}

	if ( isset($post_data['post_category']) ) {
		if ( is_array($post_data['post_category']) && ! empty($post_data['post_category']) )
			$new_cats = array_map( absint, $post_data['post_category'] );
		else
			unset($post_data['post_category']);
	}

	if ( isset($post_data['tags_input']) ) {
		$new_tags = preg_replace( '/\s*,\s*/', ',', rtrim( trim($post_data['tags_input']), ' ,' ) );
		$new_tags = explode(',', $new_tags);
	}

	if ( isset($post_data['post_parent']) && ($parent = (int) $post_data['post_parent']) ) {
		$pages = $wpdb->get_results("SELECT ID, post_parent FROM $wpdb->posts WHERE post_type = 'page'");
		$children = array();

		for ( $i = 0; $i < 50 && $parent > 0; $i++ ) {
			$children[] = $parent;

			foreach ( $pages as $page ) {
				if ( $page->ID == $parent ) {
					$parent = $page->post_parent;
					break;
				}
			}
		}
	}

	$updated = $skipped = $locked = array();
	foreach ( $post_IDs as $post_ID ) {

		if ( isset($children) && in_array($post_ID, $children) ) {
			$skipped[] = $post_ID;
			continue;
		}

		if ( wp_check_post_lock( $post_ID ) ) {
			$locked[] = $post_ID;
			continue;
		}

		if ( isset($new_cats) ) {
			$cats = (array) wp_get_post_categories($post_ID);
			$post_data['post_category'] = array_unique( array_merge($cats, $new_cats) );
		}

		if ( isset($new_tags) ) {
			$tags = wp_get_post_tags($post_ID, array('fields' => 'names'));
			$post_data['tags_input'] = array_unique( array_merge($tags, $new_tags) );
		}

		$post_data['ID'] = $post_ID;
		$updated[] = wp_update_post( $post_data );

		if ( current_user_can( 'edit_others_posts' ) && isset( $post_data['sticky'] ) ) {
			if ( 'sticky' == $post_data['sticky'] )
				stick_post( $post_ID );
			else
				unstick_post( $post_ID );
		}

	}

	return array( 'updated' => $updated, 'skipped' => $skipped, 'locked' => $locked );
}

/**
 * Default post information to use when populating the "Write Post" form.
 *
 * @since unknown
 *
 * @return unknown
 */
function get_default_post_to_edit() {
	if ( !empty( $_REQUEST['post_title'] ) )
		$post_title = esc_html( stripslashes( $_REQUEST['post_title'] ));
	else if ( !empty( $_REQUEST['popuptitle'] ) ) {
		$post_title = esc_html( stripslashes( $_REQUEST['popuptitle'] ));
		$post_title = funky_javascript_fix( $post_title );
	} else {
		$post_title = '';
	}

	$post_content = '';
	if ( !empty( $_REQUEST['content'] ) )
		$post_content = esc_html( stripslashes( $_REQUEST['content'] ));
	else if ( !empty( $post_title ) ) {
		$text       = esc_html( stripslashes( urldecode( $_REQUEST['text'] ) ) );
		$text       = funky_javascript_fix( $text);
		$popupurl   = esc_url($_REQUEST['popupurl']);
		$post_content = '<a href="'.$popupurl.'">'.$post_title.'</a>'."\n$text";
	}

	if ( !empty( $_REQUEST['excerpt'] ) )
		$post_excerpt = esc_html( stripslashes( $_REQUEST['excerpt'] ));
	else
		$post_excerpt = '';

	$post->ID = 0;
	$post->post_name = '';
	$post->post_author = '';
	$post->post_date = '';
	$post->post_date_gmt = '';
	$post->post_password = '';
	$post->post_status = 'draft';
	$post->post_type = 'post';
	$post->to_ping = '';
	$post->pinged = '';
	$post->comment_status = get_option( 'default_comment_status' );
	$post->ping_status = get_option( 'default_ping_status' );
	$post->post_pingback = get_option( 'default_pingback_flag' );
	$post->post_category = get_option( 'default_category' );
	$post->post_content = apply_filters( 'default_content', $post_content);
	$post->post_title = apply_filters( 'default_title', $post_title );
	$post->post_excerpt = apply_filters( 'default_excerpt', $post_excerpt);
	$post->page_template = 'default';
	$post->post_parent = 0;
	$post->menu_order = 0;

	return $post;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function get_default_page_to_edit() {
	$page = get_default_post_to_edit();
	$page->post_type = 'page';
	return $page;
}

/**
 * Get an existing post and format it for editing.
 *
 * @since unknown
 *
 * @param unknown_type $id
 * @return unknown
 */
function get_post_to_edit( $id ) {

	$post = get_post( $id, OBJECT, 'edit' );

	if ( $post->post_type == 'page' )
		$post->page_template = get_post_meta( $id, '_wp_page_template', true );

	return $post;
}

/**
 * Determine if a post exists based on title, content, and date
 *
 * @since unknown
 *
 * @param string $title Post title
 * @param string $content Optional post content
 * @param string $date Optional post date
 * @return int Post ID if post exists, 0 otherwise.
 */
function post_exists($title, $content = '', $date = '') {
	global $wpdb;

	$post_title = stripslashes( sanitize_post_field( 'post_title', $title, 0, 'db' ) );
	$post_content = stripslashes( sanitize_post_field( 'post_content', $content, 0, 'db' ) );
	$post_date = stripslashes( sanitize_post_field( 'post_date', $date, 0, 'db' ) );

	$query = "SELECT ID FROM $wpdb->posts WHERE 1=1";
	$args = array();

	if ( !empty ( $date ) ) {
		$query .= ' AND post_date = %s';
		$args[] = $post_date;
	}

	if ( !empty ( $title ) ) {
		$query .= ' AND post_title = %s';
		$args[] = $post_title;
	}

	if ( !empty ( $content ) ) {
		$query .= 'AND post_content = %s';
		$args[] = $post_content;
	}

	if ( !empty ( $args ) )
		return $wpdb->get_var( $wpdb->prepare($query, $args) );

	return 0;
}

/**
 * Creates a new post from the "Write Post" form using $_POST information.
 *
 * @since unknown
 *
 * @return unknown
 */
function wp_write_post() {
	global $user_ID;

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_pages' ) )
			return new WP_Error( 'edit_pages', __( 'You are not allowed to create pages on this blog.' ) );
	} else {
		if ( !current_user_can( 'edit_posts' ) )
			return new WP_Error( 'edit_posts', __( 'You are not allowed to create posts or drafts on this blog.' ) );
	}


	// Check for autosave collisions
	$temp_id = false;
	if ( isset($_POST['temp_ID']) ) {
		$temp_id = (int) $_POST['temp_ID'];
		if ( !$draft_ids = get_user_option( 'autosave_draft_ids' ) )
			$draft_ids = array();
		foreach ( $draft_ids as $temp => $real )
			if ( time() + $temp > 86400 ) // 1 day: $temp is equal to -1 * time( then )
				unset($draft_ids[$temp]);

		if ( isset($draft_ids[$temp_id]) ) { // Edit, don't write
			$_POST['post_ID'] = $draft_ids[$temp_id];
			unset($_POST['temp_ID']);
			update_user_option( $user_ID, 'autosave_draft_ids', $draft_ids );
			return edit_post();
		}
	}

	$translated = _wp_translate_postdata( false );
	if ( is_wp_error($translated) )
		return $translated;

	if ( isset($_POST['visibility']) ) {
		switch ( $_POST['visibility'] ) {
			case 'public' :
				$_POST['post_password'] = '';
				break;
			case 'password' :
				unset( $_POST['sticky'] );
				break;
			case 'private' :
				$_POST['post_status'] = 'private';
				$_POST['post_password'] = '';
				unset( $_POST['sticky'] );
				break;
		}
	}

	// Create the post.
	$post_ID = wp_insert_post( $_POST );
	if ( is_wp_error( $post_ID ) )
		return $post_ID;

	if ( empty($post_ID) )
		return 0;

	add_meta( $post_ID );

	// Reunite any orphaned attachments with their parent
	if ( !$draft_ids = get_user_option( 'autosave_draft_ids' ) )
		$draft_ids = array();
	if ( $draft_temp_id = (int) array_search( $post_ID, $draft_ids ) )
		_relocate_children( $draft_temp_id, $post_ID );
	if ( $temp_id && $temp_id != $draft_temp_id )
		_relocate_children( $temp_id, $post_ID );

	// Update autosave collision detection
	if ( $temp_id ) {
		$draft_ids[$temp_id] = $post_ID;
		update_user_option( $user_ID, 'autosave_draft_ids', $draft_ids );
	}

	// Now that we have an ID we can fix any attachment anchor hrefs
	_fix_attachment_links( $post_ID );

	wp_set_post_lock( $post_ID, $GLOBALS['current_user']->ID );

	return $post_ID;
}

/**
 * Calls wp_write_post() and handles the errors.
 *
 * @since unknown
 *
 * @return unknown
 */
function write_post() {
	$result = wp_write_post();
	if( is_wp_error( $result ) )
		wp_die( $result->get_error_message() );
	else
		return $result;
}

//
// Post Meta
//

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $post_ID
 * @return unknown
 */
function add_meta( $post_ID ) {
	global $wpdb;
	$post_ID = (int) $post_ID;

	$protected = array( '_wp_attached_file', '_wp_attachment_metadata', '_wp_old_slug', '_wp_page_template' );

	$metakeyselect = isset($_POST['metakeyselect']) ? stripslashes( trim( $_POST['metakeyselect'] ) ) : '';
	$metakeyinput = isset($_POST['metakeyinput']) ? stripslashes( trim( $_POST['metakeyinput'] ) ) : '';
	$metavalue = isset($_POST['metavalue']) ? maybe_serialize( stripslashes( trim( $_POST['metavalue'] ) ) ) : '';

	if ( ('0' === $metavalue || !empty ( $metavalue ) ) && ((('#NONE#' != $metakeyselect) && !empty ( $metakeyselect) ) || !empty ( $metakeyinput) ) ) {
		// We have a key/value pair. If both the select and the
		// input for the key have data, the input takes precedence:

 		if ('#NONE#' != $metakeyselect)
			$metakey = $metakeyselect;

		if ( $metakeyinput)
			$metakey = $metakeyinput; // default

		if ( in_array($metakey, $protected) )
			return false;

		wp_cache_delete($post_ID, 'post_meta');

		$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta (post_id,meta_key,meta_value ) VALUES (%s, %s, %s)", $post_ID, $metakey, $metavalue) );
		return $wpdb->insert_id;
	}
	return false;
} // add_meta

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $mid
 * @return unknown
 */
function delete_meta( $mid ) {
	global $wpdb;
	$mid = (int) $mid;

	$post_id = $wpdb->get_var( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_id = %d", $mid) );
	wp_cache_delete($post_id, 'post_meta');

	return $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE meta_id = %d", $mid) );
}

/**
 * Get a list of previously defined keys.
 *
 * @since unknown
 *
 * @return unknown
 */
function get_meta_keys() {
	global $wpdb;

	$keys = $wpdb->get_col( "
			SELECT meta_key
			FROM $wpdb->postmeta
			GROUP BY meta_key
			ORDER BY meta_key" );

	return $keys;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $mid
 * @return unknown
 */
function get_post_meta_by_id( $mid ) {
	global $wpdb;
	$mid = (int) $mid;

	$meta = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE meta_id = %d", $mid) );
	if ( is_serialized_string( $meta->meta_value ) )
		$meta->meta_value = maybe_unserialize( $meta->meta_value );
	return $meta;
}

/**
 * {@internal Missing Short Description}}
 *
 * Some postmeta stuff.
 *
 * @since unknown
 *
 * @param unknown_type $postid
 * @return unknown
 */
function has_meta( $postid ) {
	global $wpdb;

	return $wpdb->get_results( $wpdb->prepare("SELECT meta_key, meta_value, meta_id, post_id
			FROM $wpdb->postmeta WHERE post_id = %d
			ORDER BY meta_key,meta_id", $postid), ARRAY_A );

}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $meta_id
 * @param unknown_type $meta_key
 * @param unknown_type $meta_value
 * @return unknown
 */
function update_meta( $meta_id, $meta_key, $meta_value ) {
	global $wpdb;

	$protected = array( '_wp_attached_file', '_wp_attachment_metadata', '_wp_old_slug', '_wp_page_template' );

	if ( in_array($meta_key, $protected) )
		return false;

	if ( '' === trim( $meta_value ) )
		return false;

	$post_id = $wpdb->get_var( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_id = %d", $meta_id) );
	wp_cache_delete($post_id, 'post_meta');

	$meta_value = maybe_serialize( stripslashes( $meta_value ) );
	$meta_id = (int) $meta_id;

	$data  = compact( 'meta_key', 'meta_value' );
	$where = compact( 'meta_id' );

	return $wpdb->update( $wpdb->postmeta, $data, $where );
}

//
// Private
//

/**
 * Replace hrefs of attachment anchors with up-to-date permalinks.
 *
 * @since unknown
 * @access private
 *
 * @param unknown_type $post_ID
 * @return unknown
 */
function _fix_attachment_links( $post_ID ) {

	$post = & get_post( $post_ID, ARRAY_A );

	$search = "#<a[^>]+rel=('|\")[^'\"]*attachment[^>]*>#ie";

	// See if we have any rel="attachment" links
	if ( 0 == preg_match_all( $search, $post['post_content'], $anchor_matches, PREG_PATTERN_ORDER ) )
		return;

	$i = 0;
	$search = "#[\s]+rel=(\"|')(.*?)wp-att-(\d+)\\1#i";
	foreach ( $anchor_matches[0] as $anchor ) {
		if ( 0 == preg_match( $search, $anchor, $id_matches ) )
			continue;

		$id = (int) $id_matches[3];

		// While we have the attachment ID, let's adopt any orphans.
		$attachment = & get_post( $id, ARRAY_A );
		if ( ! empty( $attachment) && ! is_object( get_post( $attachment['post_parent'] ) ) ) {
			$attachment['post_parent'] = $post_ID;
			// Escape data pulled from DB.
			$attachment = add_magic_quotes( $attachment);
			wp_update_post( $attachment);
		}

		$post_search[$i] = $anchor;
		$post_replace[$i] = preg_replace( "#href=(\"|')[^'\"]*\\1#e", "stripslashes( 'href=\\1' ).get_attachment_link( $id ).stripslashes( '\\1' )", $anchor );
		++$i;
	}

	$post['post_content'] = str_replace( $post_search, $post_replace, $post['post_content'] );

	// Escape data pulled from DB.
	$post = add_magic_quotes( $post);

	return wp_update_post( $post);
}

/**
 * Move child posts to a new parent.
 *
 * @since unknown
 * @access private
 *
 * @param unknown_type $old_ID
 * @param unknown_type $new_ID
 * @return unknown
 */
function _relocate_children( $old_ID, $new_ID ) {
	global $wpdb;
	$old_ID = (int) $old_ID;
	$new_ID = (int) $new_ID;
	return $wpdb->update($wpdb->posts, array('post_parent' => $new_ID), array('post_parent' => $old_ID) );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $type
 * @return unknown
 */
function get_available_post_statuses($type = 'post') {
	$stati = wp_count_posts($type);

	return array_keys(get_object_vars($stati));
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $q
 * @return unknown
 */
function wp_edit_posts_query( $q = false ) {
	if ( false === $q )
		$q = $_GET;
	$q['m']   = isset($q['m']) ? (int) $q['m'] : 0;
	$q['cat'] = isset($q['cat']) ? (int) $q['cat'] : 0;
	$post_stati  = array(	//	array( adj, noun )
				'publish' => array(_x('Published', 'post'), __('Published posts'), _n_noop('Published <span class="count">(%s)</span>', 'Published <span class="count">(%s)</span>')),
				'future' => array(_x('Scheduled', 'post'), __('Scheduled posts'), _n_noop('Scheduled <span class="count">(%s)</span>', 'Scheduled <span class="count">(%s)</span>')),
				'pending' => array(_x('Pending Review', 'post'), __('Pending posts'), _n_noop('Pending Review <span class="count">(%s)</span>', 'Pending Review <span class="count">(%s)</span>')),
				'draft' => array(_x('Draft', 'post'), _x('Drafts', 'manage posts header'), _n_noop('Draft <span class="count">(%s)</span>', 'Drafts <span class="count">(%s)</span>')),
				'private' => array(_x('Private', 'post'), __('Private posts'), _n_noop('Private <span class="count">(%s)</span>', 'Private <span class="count">(%s)</span>')),
			);

	$post_stati = apply_filters('post_stati', $post_stati);

	$avail_post_stati = get_available_post_statuses('post');

	$post_status_q = '';
	if ( isset($q['post_status']) && in_array( $q['post_status'], array_keys($post_stati) ) ) {
		$post_status_q = '&post_status=' . $q['post_status'];
		$post_status_q .= '&perm=readable';
	}

	if ( isset($q['post_status']) && 'pending' === $q['post_status'] ) {
		$order = 'ASC';
		$orderby = 'modified';
	} elseif ( isset($q['post_status']) && 'draft' === $q['post_status'] ) {
		$order = 'DESC';
		$orderby = 'modified';
	} else {
		$order = 'DESC';
		$orderby = 'date';
	}

	$posts_per_page = get_user_option('edit_per_page');
	if ( empty($posts_per_page) )
		$posts_per_page = 15;
	$posts_per_page = apply_filters('edit_posts_per_page', $posts_per_page);

	wp("post_type=post&$post_status_q&posts_per_page=$posts_per_page&order=$order&orderby=$orderby");

	return array($post_stati, $avail_post_stati);
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $type
 * @return unknown
 */
function get_available_post_mime_types($type = 'attachment') {
	global $wpdb;

	$types = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT post_mime_type FROM $wpdb->posts WHERE post_type = %s", $type));
	return $types;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $q
 * @return unknown
 */
function wp_edit_attachments_query( $q = false ) {
	if ( false === $q )
		$q = $_GET;

	$q['m']   = isset( $q['m'] ) ? (int) $q['m'] : 0;
	$q['cat'] = isset( $q['cat'] ) ? (int) $q['cat'] : 0;
	$q['post_type'] = 'attachment';
	$q['post_status'] = 'any';
	$media_per_page = get_user_option('upload_per_page');
	if ( empty($media_per_page) )
		$media_per_page = 20;
	$q['posts_per_page'] = $media_per_page;
	$post_mime_types = array(	//	array( adj, noun )
				'image' => array(__('Images'), __('Manage Images'), _n_noop('Image <span class="count">(%s)</span>', 'Images <span class="count">(%s)</span>')),
				'audio' => array(__('Audio'), __('Manage Audio'), _n_noop('Audio <span class="count">(%s)</span>', 'Audio <span class="count">(%s)</span>')),
				'video' => array(__('Video'), __('Manage Video'), _n_noop('Video <span class="count">(%s)</span>', 'Video <span class="count">(%s)</span>')),
			);
	$post_mime_types = apply_filters('post_mime_types', $post_mime_types);

	$avail_post_mime_types = get_available_post_mime_types('attachment');

	if ( isset($q['post_mime_type']) && !array_intersect( (array) $q['post_mime_type'], array_keys($post_mime_types) ) )
		unset($q['post_mime_type']);

	wp($q);

	return array($post_mime_types, $avail_post_mime_types);
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $id
 * @param unknown_type $page
 * @return unknown
 */
function postbox_classes( $id, $page ) {
	if ( isset( $_GET['edit'] ) && $_GET['edit'] == $id )
		return '';
	$current_user = wp_get_current_user();
	if ( $closed = get_user_option('closedpostboxes_'.$page, 0, false ) ) {
		if ( !is_array( $closed ) ) return '';
		return in_array( $id, $closed )? 'closed' : '';
	} else {
		return '';
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $id
 * @param unknown_type $title
 * @param unknown_type $name
 * @return unknown
 */
function get_sample_permalink($id, $title=null, $name = null) {
	$post = &get_post($id);
	if (!$post->ID) {
		return array('', '');
	}
	$original_status = $post->post_status;
	$original_date = $post->post_date;
	$original_name = $post->post_name;

	// Hack: get_permalink would return ugly permalink for
	// drafts, so we will fake, that our post is published
	if (in_array($post->post_status, array('draft', 'pending'))) {
		$post->post_status = 'publish';
		$post->post_name = sanitize_title($post->post_name? $post->post_name : $post->post_title, $post->ID);
	}

	$post->post_name = wp_unique_post_slug($post->post_name, $post->ID, $post->post_status, $post->post_type, $post->post_parent);

	// If the user wants to set a new name -- override the current one
	// Note: if empty name is supplied -- use the title instead, see #6072
	if (!is_null($name)) {
		$post->post_name = sanitize_title($name? $name : $title, $post->ID);
	}

	$post->filter = 'sample';

	$permalink = get_permalink($post, true);

	// Handle page hierarchy
	if ( 'page' == $post->post_type ) {
		$uri = get_page_uri($post->ID);
		$uri = untrailingslashit($uri);
		$uri = strrev( stristr( strrev( $uri ), '/' ) );
		$uri = untrailingslashit($uri);
		if ( !empty($uri) )
			$uri .='/';
		$permalink = str_replace('%pagename%', "${uri}%pagename%", $permalink);
	}

	$permalink = array($permalink, apply_filters('editable_slug', $post->post_name));
	$post->post_status = $original_status;
	$post->post_date = $original_date;
	$post->post_name = $original_name;
	unset($post->filter);

	return $permalink;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $id
 * @param unknown_type $new_title
 * @param unknown_type $new_slug
 * @return unknown
 */
function get_sample_permalink_html( $id, $new_title = null, $new_slug = null ) {
	$post = &get_post($id);
	list($permalink, $post_name) = get_sample_permalink($post->ID, $new_title, $new_slug);
	if ( 'publish' == $post->post_status )
		$view_post = 'post' == $post->post_type ? __('View Post') : __('View Page');

	if ( false === strpos($permalink, '%postname%') && false === strpos($permalink, '%pagename%') ) {
		$return = '<strong>' . __('Permalink:') . "</strong>\n" . '<span id="sample-permalink">' . $permalink . "</span>\n";
		if ( current_user_can( 'manage_options' ) )
			$return .= '<span id="change-permalinks"><a href="options-permalink.php" class="button" target="_blank">' . __('Change Permalinks') . "</a></span>\n";
		if ( isset($view_post) )
			$return .= "<span id='view-post-btn'><a href='$permalink' class='button' target='_blank'>$view_post</a></span>\n";

		return $return;
	}

	$title = __('Click to edit this part of the permalink');
	if (function_exists('mb_strlen')) {
		if (mb_strlen($post_name) > 30) {
			$post_name_abridged = mb_substr($post_name, 0, 14). '&hellip;' . mb_substr($post_name, -14);
		} else {
			$post_name_abridged = $post_name;
		}
	} else {
		if (strlen($post_name) > 30) {
			$post_name_abridged = substr($post_name, 0, 14). '&hellip;' . substr($post_name, -14);
		} else {
			$post_name_abridged = $post_name;
		}
	}

	$post_name_html = '<span id="editable-post-name" title="' . $title . '">' . $post_name_abridged . '</span>';
	$display_link = str_replace(array('%pagename%','%postname%'), $post_name_html, $permalink);
	$view_link = str_replace(array('%pagename%','%postname%'), $post_name, $permalink);
	$return = '<strong>' . __('Permalink:') . "</strong>\n" . '<span id="sample-permalink">' . $display_link . "</span>\n";
	$return .= '<span id="edit-slug-buttons"><a href="#post_name" class="edit-slug button hide-if-no-js" onclick="edit_permalink(' . $id . '); return false;">' . __('Edit') . "</a></span>\n";
	$return .= '<span id="editable-post-name-full">' . $post_name . "</span>\n";
	if ( isset($view_post) )
		$return .= "<span id='view-post-btn'><a href='$view_link' class='button' target='_blank'>$view_post</a></span>\n";

	return $return;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $post_id
 * @return bool|int False: not locked or locked by current user. Int: user ID of user with lock.
 */
function wp_check_post_lock( $post_id ) {
	global $current_user;

	if ( !$post = get_post( $post_id ) )
		return false;

	$lock = get_post_meta( $post->ID, '_edit_lock', true );
	$last = get_post_meta( $post->ID, '_edit_last', true );

	$time_window = apply_filters( 'wp_check_post_lock_window', AUTOSAVE_INTERVAL * 2 );

	if ( $lock && $lock > time() - $time_window && $last != $current_user->ID )
		return $last;
	return false;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $post_id
 * @return unknown
 */
function wp_set_post_lock( $post_id ) {
	global $current_user;
	if ( !$post = get_post( $post_id ) )
		return false;
	if ( !$current_user || !$current_user->ID )
		return false;

	$now = time();

	if ( !add_post_meta( $post->ID, '_edit_lock', $now, true ) )
		update_post_meta( $post->ID, '_edit_lock', $now );
	if ( !add_post_meta( $post->ID, '_edit_last', $current_user->ID, true ) )
		update_post_meta( $post->ID, '_edit_last', $current_user->ID );
}

/**
 * Creates autosave data for the specified post from $_POST data.
 *
 * @package WordPress
 * @subpackage Post_Revisions
 * @since 2.6.0
 *
 * @uses _wp_translate_postdata()
 * @uses _wp_post_revision_fields()
 */
function wp_create_post_autosave( $post_id ) {
	$translated = _wp_translate_postdata( true );
	if ( is_wp_error( $translated ) )
		return $translated;

	// Only store one autosave.  If there is already an autosave, overwrite it.
	if ( $old_autosave = wp_get_post_autosave( $post_id ) ) {
		$new_autosave = _wp_post_revision_fields( $_POST, true );
		$new_autosave['ID'] = $old_autosave->ID;
		return wp_update_post( $new_autosave );
	}

	// _wp_put_post_revision() expects unescaped.
	$_POST = stripslashes_deep($_POST);

	// Otherwise create the new autosave as a special post revision
	return _wp_put_post_revision( $_POST, true );
}

/**
 * Save draft or manually autosave for showing preview.
 *
 * @package WordPress
 * @since 2.7
 *
 * @uses wp_write_post()
 * @uses edit_post()
 * @uses get_post()
 * @uses current_user_can()
 * @uses wp_create_post_autosave()
 *
 * @return str URL to redirect to show the preview
 */
function post_preview() {

	$post_ID = (int) $_POST['post_ID'];
	if ( $post_ID < 1 )
		wp_die( __('Preview not available. Please save as a draft first.') );

	if ( isset($_POST['catslist']) )
		$_POST['post_category'] = explode(",", $_POST['catslist']);

	if ( isset($_POST['tags_input']) )
		$_POST['tags_input'] = explode(",", $_POST['tags_input']);

	if ( $_POST['post_type'] == 'page' || empty($_POST['post_category']) )
		unset($_POST['post_category']);

	$_POST['ID'] = $post_ID;
	$post = get_post($post_ID);

	if ( 'page' == $post->post_type ) {
		if ( !current_user_can('edit_page', $post_ID) )
			wp_die(__('You are not allowed to edit this page.'));
	} else {
		if ( !current_user_can('edit_post', $post_ID) )
			wp_die(__('You are not allowed to edit this post.'));
	}

	if ( 'draft' == $post->post_status ) {
		$id = edit_post();
	} else { // Non drafts are not overwritten.  The autosave is stored in a special post revision.
		$id = wp_create_post_autosave( $post->ID );
		if ( ! is_wp_error($id) )
			$id = $post->ID;
	}

	if ( is_wp_error($id) )
		wp_die( $id->get_error_message() );

	if ( $_POST['post_status'] == 'draft'  ) {
		$url = add_query_arg( 'preview', 'true', get_permalink($id) );
	} else {
		$nonce = wp_create_nonce('post_preview_' . $id);
		$url = add_query_arg( array( 'preview' => 'true', 'preview_id' => $id, 'preview_nonce' => $nonce ), get_permalink($id) );
	}

	return $url;
}

/**
 * Adds the TinyMCE editor used on the Write and Edit screens.
 *
 * @package WordPress
 * @since 2.7
 *
 * TinyMCE is loaded separately from other Javascript by using wp-tinymce.php. It outputs concatenated
 * and optionaly pre-compressed version of the core and all default plugins. Additional plugins are loaded
 * directly by TinyMCE using non-blocking method. Custom plugins can be refreshed by adding a query string
 * to the URL when queueing them with the mce_external_plugins filter.
 *
 * @param bool $teeny optional Output a trimmed down version used in Press This.
 */
function wp_tiny_mce( $teeny = false ) {
	global $concatenate_scripts, $compress_scripts, $tinymce_version;

	if ( ! user_can_richedit() )
		return;

	$baseurl = includes_url('js/tinymce');

	$mce_locale = ( '' == get_locale() ) ? 'en' : strtolower( substr(get_locale(), 0, 2) ); // only ISO 639-1

	/*
	The following filter allows localization scripts to change the languages displayed in the spellchecker's drop-down menu.
	By default it uses Google's spellchecker API, but can be configured to use PSpell/ASpell if installed on the server.
	The + sign marks the default language. More information:
	http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/spellchecker
	*/
	$mce_spellchecker_languages = apply_filters('mce_spellchecker_languages', '+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv');

	if ( $teeny ) {
		$plugins = apply_filters( 'teeny_mce_plugins', array('safari', 'inlinepopups', 'media', 'autosave', 'fullscreen') );
		$ext_plugins = '';
	} else {
		$plugins = array( 'safari', 'inlinepopups', 'spellchecker', 'paste', 'wordpress', 'media', 'fullscreen', 'wpeditimage', 'wpgallery', 'tabfocus' );

		/*
		The following filter takes an associative array of external plugins for TinyMCE in the form 'plugin_name' => 'url'.
		It adds the plugin's name to TinyMCE's plugins init and the call to PluginManager to load the plugin.
		The url should be absolute and should include the js file name to be loaded. Example:
		array( 'myplugin' => 'http://my-site.com/wp-content/plugins/myfolder/mce_plugin.js' )
		If the plugin uses a button, it should be added with one of the "$mce_buttons" filters.
		*/
		$mce_external_plugins = apply_filters('mce_external_plugins', array());

		$ext_plugins = '';
		if ( ! empty($mce_external_plugins) ) {

			/*
			The following filter loads external language files for TinyMCE plugins.
			It takes an associative array 'plugin_name' => 'path', where path is the
			include path to the file. The language file should follow the same format as
			/tinymce/langs/wp-langs.php and should define a variable $strings that
			holds all translated strings.
			When this filter is not used, the function will try to load {mce_locale}.js.
			If that is not found, en.js will be tried next.
			*/
			$mce_external_languages = apply_filters('mce_external_languages', array());

			$loaded_langs = array();
			$strings = '';

			if ( ! empty($mce_external_languages) ) {
				foreach ( $mce_external_languages as $name => $path ) {
					if ( @is_file($path) && @is_readable($path) ) {
						include_once($path);
						$ext_plugins .= $strings . "\n";
						$loaded_langs[] = $name;
					}
				}
			}

			foreach ( $mce_external_plugins as $name => $url ) {

				if ( is_ssl() ) $url = str_replace('http://', 'https://', $url);

				$plugins[] = '-' . $name;

				$plugurl = dirname($url);
				$strings = $str1 = $str2 = '';
				if ( ! in_array($name, $loaded_langs) ) {
					$path = str_replace( WP_PLUGIN_URL, '', $plugurl );
					$path = WP_PLUGIN_DIR . $path . '/langs/';

					if ( function_exists('realpath') )
						$path = trailingslashit( realpath($path) );

					if ( @is_file($path . $mce_locale . '.js') )
						$strings .= @file_get_contents($path . $mce_locale . '.js') . "\n";

					if ( @is_file($path . $mce_locale . '_dlg.js') )
						$strings .= @file_get_contents($path . $mce_locale . '_dlg.js') . "\n";

					if ( 'en' != $mce_locale && empty($strings) ) {
						if ( @is_file($path . 'en.js') ) {
							$str1 = @file_get_contents($path . 'en.js');
							$strings .= preg_replace( '/([\'"])en\./', '$1' . $mce_locale . '.', $str1, 1 ) . "\n";
						}

						if ( @is_file($path . 'en_dlg.js') ) {
							$str2 = @file_get_contents($path . 'en_dlg.js');
							$strings .= preg_replace( '/([\'"])en\./', '$1' . $mce_locale . '.', $str2, 1 ) . "\n";
						}
					}

					if ( ! empty($strings) )
						$ext_plugins .= "\n" . $strings . "\n";
				}

				$ext_plugins .= 'tinyMCEPreInit.load_ext("' . $plugurl . '", "' . $mce_locale . '");' . "\n";
				$ext_plugins .= 'tinymce.PluginManager.load("' . $name . '", "' . $url . '");' . "\n";
			}
		}
	}

	$plugins = implode($plugins, ',');

	if ( $teeny ) {
		$mce_buttons = apply_filters( 'teeny_mce_buttons', array('bold, italic, underline, blockquote, separator, strikethrough, bullist, numlist,justifyleft, justifycenter, justifyright, undo, redo, link, unlink, fullscreen') );
		$mce_buttons = implode($mce_buttons, ',');
		$mce_buttons_2 = $mce_buttons_3 = $mce_buttons_4 = '';
	} else {
		$mce_buttons = apply_filters('mce_buttons', array('bold', 'italic', 'strikethrough', '|', 'bullist', 'numlist', 'blockquote', '|', 'justifyleft', 'justifycenter', 'justifyright', '|', 'link', 'unlink', 'wp_more', '|', 'spellchecker', 'fullscreen', 'wp_adv' ));
		$mce_buttons = implode($mce_buttons, ',');

		$mce_buttons_2 = apply_filters('mce_buttons_2', array('formatselect', 'underline', 'justifyfull', 'forecolor', '|', 'pastetext', 'pasteword', 'removeformat', '|', 'media', 'charmap', '|', 'outdent', 'indent', '|', 'undo', 'redo', 'wp_help' ));
		$mce_buttons_2 = implode($mce_buttons_2, ',');

		$mce_buttons_3 = apply_filters('mce_buttons_3', array());
		$mce_buttons_3 = implode($mce_buttons_3, ',');

		$mce_buttons_4 = apply_filters('mce_buttons_4', array());
		$mce_buttons_4 = implode($mce_buttons_4, ',');
	}
	$no_captions = ( apply_filters( 'disable_captions', '' ) ) ? true : false;

	// TinyMCE init settings
	$initArray = array (
		'mode' => 'specific_textareas',
		'editor_selector' => 'theEditor',
		'width' => '100%',
		'theme' => 'advanced',
		'skin' => 'wp_theme',
		'theme_advanced_buttons1' => "$mce_buttons",
		'theme_advanced_buttons2' => "$mce_buttons_2",
		'theme_advanced_buttons3' => "$mce_buttons_3",
		'theme_advanced_buttons4' => "$mce_buttons_4",
		'language' => "$mce_locale",
		'spellchecker_languages' => "$mce_spellchecker_languages",
		'theme_advanced_toolbar_location' => 'top',
		'theme_advanced_toolbar_align' => 'left',
		'theme_advanced_statusbar_location' => 'bottom',
		'theme_advanced_resizing' => true,
		'theme_advanced_resize_horizontal' => false,
		'dialog_type' => 'modal',
		'relative_urls' => false,
		'remove_script_host' => false,
		'convert_urls' => false,
		'apply_source_formatting' => false,
		'remove_linebreaks' => true,
		'gecko_spellcheck' => true,
		'entities' => '38,amp,60,lt,62,gt',
		'accessibility_focus' => true,
		'tabfocus_elements' => 'major-publishing-actions',
		'media_strict' => false,
		'save_callback' => 'switchEditors.saveCallback',
		'wpeditimage_disable_captions' => $no_captions,
		'plugins' => "$plugins"
	);

	$mce_css = trim(apply_filters('mce_css', ''), ' ,');

	if ( ! empty($mce_css) )
		$initArray['content_css'] = "$mce_css";

	// For people who really REALLY know what they're doing with TinyMCE
	// You can modify initArray to add, remove, change elements of the config before tinyMCE.init
	// Setting "valid_elements", "invalid_elements" and "extended_valid_elements" can be done through "tiny_mce_before_init".
	// Best is to use the default cleanup by not specifying valid_elements, as TinyMCE contains full set of XHTML 1.0.
	if ( $teeny ) {
		$initArray = apply_filters('teeny_mce_before_init', $initArray);
	} else {
		$initArray = apply_filters('tiny_mce_before_init', $initArray);
	}

	if ( empty($initArray['theme_advanced_buttons3']) && !empty($initArray['theme_advanced_buttons4']) ) {
		$initArray['theme_advanced_buttons3'] = $initArray['theme_advanced_buttons4'];
		$initArray['theme_advanced_buttons4'] = '';
	}

	if ( ! isset($concatenate_scripts) )
		script_concat_settings();

	$language = $initArray['language'];
	$zip = $compress_scripts ? 1 : 0;

	/**
	 * Deprecated
	 *
	 * The tiny_mce_version filter is not needed since external plugins are loaded directly by TinyMCE.
	 * These plugins can be refreshed by appending query string to the URL passed to mce_external_plugins filter.
	 * If the plugin has a popup dialog, a query string can be added to the button action that opens it (in the plugin's code).
	 */
	$version = apply_filters('tiny_mce_version', '');
	$version = 'ver=' . $tinymce_version . $version;

	if ( 'en' != $language )
		include_once(ABSPATH . WPINC . '/js/tinymce/langs/wp-langs.php');

	$mce_options = '';
	foreach ( $initArray as $k => $v )
	    $mce_options .= $k . ':"' . $v . '", ';

	$mce_options = rtrim( trim($mce_options), '\n\r,' ); ?>

<script type="text/javascript">
/* <![CDATA[ */
tinyMCEPreInit = {
	base : "<?php echo $baseurl; ?>",
	suffix : "",
	query : "<?php echo $version; ?>",
	mceInit : {<?php echo $mce_options; ?>},
	load_ext : function(url,lang){var sl=tinymce.ScriptLoader;sl.markDone(url+'/langs/'+lang+'.js');sl.markDone(url+'/langs/'+lang+'_dlg.js');}
};
/* ]]> */
</script>

<?php
	if ( $concatenate_scripts )
		echo "<script type='text/javascript' src='$baseurl/wp-tinymce.php?c=$zip&amp;$version'></script>\n";
	else
		echo "<script type='text/javascript' src='$baseurl/tiny_mce.js?$version'></script>\n";

	if ( 'en' != $language && isset($lang) )
		echo "<script type='text/javascript'>\n$lang\n</script>\n";
	else
		echo "<script type='text/javascript' src='$baseurl/langs/wp-langs-en.js?$version'></script>\n";
?>

<script type="text/javascript">
/* <![CDATA[ */
<?php if ( $ext_plugins ) echo "$ext_plugins\n"; ?>
<?php if ( $concatenate_scripts ) { ?>
tinyMCEPreInit.go();
<?php } else { ?>
(function(){var t=tinyMCEPreInit,sl=tinymce.ScriptLoader,ln=t.mceInit.language,th=t.mceInit.theme,pl=t.mceInit.plugins;sl.markDone(t.base+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'_dlg.js');tinymce.each(pl.split(','),function(n){if(n&&n.charAt(0)!='-'){sl.markDone(t.base+'/plugins/'+n+'/langs/'+ln+'.js');sl.markDone(t.base+'/plugins/'+n+'/langs/'+ln+'_dlg.js');}});})();
<?php } ?>
tinyMCE.init(tinyMCEPreInit.mceInit);
/* ]]> */
</script>
<?php
}

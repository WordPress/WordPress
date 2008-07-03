<?php

/**
 * _wp_translate_postdata() - Rename $_POST data from form names to DB post columns.
 *
 * Manipulates $_POST directly.
 *
 * @package WordPress
 * @since 2.6
 *
 * @param bool $update Are we updating a pre-existing post?
 * @return object|bool WP_Error on failure, true on success.
 */
function _wp_translate_postdata( $update = false ) {
	if ( $update )
		$_POST['ID'] = (int) $_POST['post_ID'];
	$_POST['post_content'] = $_POST['content'];
	$_POST['post_excerpt'] = $_POST['excerpt'];
	$_POST['post_parent'] = isset($_POST['parent_id'])? $_POST['parent_id'] : '';
	$_POST['to_ping'] = $_POST['trackback_url'];

	if (!empty ( $_POST['post_author_override'] ) ) {
		$_POST['post_author'] = (int) $_POST['post_author_override'];
	} else {
		if (!empty ( $_POST['post_author'] ) ) {
			$_POST['post_author'] = (int) $_POST['post_author'];
		} else {
			$_POST['post_author'] = (int) $_POST['user_ID'];
		}
	}

	if ( $_POST['post_author'] != $_POST['user_ID'] ) {
		if ( 'page' == $_POST['post_type'] ) {
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
	if ( isset($_POST['saveasdraft']) && '' != $_POST['saveasdraft'] )
		$_POST['post_status'] = 'draft';
	if ( isset($_POST['saveasprivate']) && '' != $_POST['saveasprivate'] )
		$_POST['post_status'] = 'private';
	if ( isset($_POST['publish']) && ( '' != $_POST['publish'] ) && ( $_POST['post_status'] != 'private' ) )
		$_POST['post_status'] = 'publish';
	if ( isset($_POST['advanced']) && '' != $_POST['advanced'] )
		$_POST['post_status'] = 'draft';

	$previous_status = get_post_field('post_status',  $_POST['ID']);

	// Posts 'submitted for approval' present are submitted to $_POST the same as if they were being published. 
	// Change status from 'publish' to 'pending' if user lacks permissions to publish or to resave published posts.
	if ( 'page' == $_POST['post_type'] ) {
		if ( 'publish' == $_POST['post_status'] && !current_user_can( 'publish_pages' ) )
			if ( $previous_status != 'publish' OR !current_user_can( 'edit_published_pages') )
				$_POST['post_status'] = 'pending';
	} else {
		if ( 'publish' == $_POST['post_status'] && !current_user_can( 'publish_posts' ) ) :
			// Stop attempts to publish new posts, but allow already published posts to be saved if appropriate.
			if ( $previous_status != 'publish' OR !current_user_can( 'edit_published_posts') )
				$_POST['post_status'] = 'pending';
		endif;
	}

	if (!isset( $_POST['comment_status'] ))
		$_POST['comment_status'] = 'closed';

	if (!isset( $_POST['ping_status'] ))
		$_POST['ping_status'] = 'closed';

	foreach ( array('aa', 'mm', 'jj', 'hh', 'mn') as $timeunit ) {
		if ( !empty( $_POST['hidden_' . $timeunit] ) && $_POST['hidden_' . $timeunit] != $_POST[$timeunit] ) {
			$_POST['edit_date'] = '1';
			break;
		}
	}

	if ( !empty( $_POST['edit_date'] ) ) {
		$aa = $_POST['aa'];
		$mm = $_POST['mm'];
		$jj = $_POST['jj'];
		$hh = $_POST['hh'];
		$mn = $_POST['mn'];
		$ss = $_POST['ss'];
		$aa = ($aa <= 0 ) ? date('Y') : $aa;
		$mm = ($mm <= 0 ) ? date('n') : $mm;
		$jj = ($jj > 31 ) ? 31 : $jj;
		$jj = ($jj <= 0 ) ? date('j') : $jj;
		$hh = ($hh > 23 ) ? $hh -24 : $hh;
		$mn = ($mn > 59 ) ? $mn -60 : $mn;
		$ss = ($ss > 59 ) ? $ss -60 : $ss;
		$_POST['post_date'] = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $aa, $mm, $jj, $hh, $mn, $ss );
		$_POST['post_date_gmt'] = get_gmt_from_date( $_POST['post_date'] );
	}

	return true;
}


// Update an existing post with values provided in $_POST.
function edit_post() {

	$post_ID = (int) $_POST['post_ID'];

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_ID ) )
			wp_die( __('You are not allowed to edit this page.' ));
	} else {
		if ( !current_user_can( 'edit_post', $post_ID ) )
			wp_die( __('You are not allowed to edit this post.' ));
	}

	// Autosave shouldn't save too soon after a real save
	if ( 'autosave' == $_POST['action'] ) {
		$post =& get_post( $post_ID );
		$now = time();
		$then = strtotime($post->post_date_gmt . ' +0000');
		$delta = AUTOSAVE_INTERVAL / 2;
		if ( ($now - $then) < $delta )
			return $post_ID;
	}

	$translated = _wp_translate_postdata( true );
	if ( is_wp_error($translated) )
		wp_die( $translated->get_error_message() );

	// Meta Stuff
	if ( isset($_POST['meta']) && $_POST['meta'] ) {
		foreach ( $_POST['meta'] as $key => $value )
			update_meta( $key, $value['key'], $value['value'] );
	}

	if ( isset($_POST['deletemeta']) && $_POST['deletemeta'] ) {
		foreach ( $_POST['deletemeta'] as $key => $value )
			delete_meta( $key );
	}

	add_meta( $post_ID );

	wp_update_post( $_POST );

	// Reunite any orphaned attachments with their parent
	if ( !$draft_ids = get_user_option( 'autosave_draft_ids' ) )
		$draft_ids = array();
	if ( $draft_temp_id = (int) array_search( $post_ID, $draft_ids ) )
		_relocate_children( $draft_temp_id, $post_ID );

	// Now that we have an ID we can fix any attachment anchor hrefs
	_fix_attachment_links( $post_ID );

	wp_set_post_lock( $post_ID, $GLOBALS['current_user']->ID );

	return $post_ID;
}

// Default post information to use when populating the "Write Post" form.
function get_default_post_to_edit() {
	if ( !empty( $_REQUEST['post_title'] ) )
		$post_title = wp_specialchars( stripslashes( $_REQUEST['post_title'] ));
	else if ( !empty( $_REQUEST['popuptitle'] ) ) {
		$post_title = wp_specialchars( stripslashes( $_REQUEST['popuptitle'] ));
		$post_title = funky_javascript_fix( $post_title );
	} else {
		$post_title = '';
	}

	$post_content = '';
	if ( !empty( $_REQUEST['content'] ) )
		$post_content = wp_specialchars( stripslashes( $_REQUEST['content'] ));
	else if ( !empty( $post_title ) ) {
		$text       = wp_specialchars( stripslashes( urldecode( $_REQUEST['text'] ) ) );
		$text       = funky_javascript_fix( $text);
		$popupurl   = clean_url($_REQUEST['popupurl']);
        $post_content = '<a href="'.$popupurl.'">'.$post_title.'</a>'."\n$text";
    }

	if ( !empty( $_REQUEST['excerpt'] ) )
		$post_excerpt = wp_specialchars( stripslashes( $_REQUEST['excerpt'] ));
	else
		$post_excerpt = '';

	$post->ID = 0;
	$post->post_name = '';
	$post->post_author = '';
	$post->post_date = '';
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

function get_default_page_to_edit() {
 	$page = get_default_post_to_edit();
 	$page->post_type = 'page';
 	return $page;
}

// Get an existing post and format it for editing.
function get_post_to_edit( $id ) {

	$post = get_post( $id, OBJECT, 'edit' );

	if ( $post->post_type == 'page' )
		$post->page_template = get_post_meta( $id, '_wp_page_template', true );

	return $post;
}

function post_exists($title, $content = '', $post_date = '') {
	global $wpdb;

	if (!empty ($post_date))
		$post_date = $wpdb->prepare("AND post_date = %s", $post_date);

	if (!empty ($title))
		return $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_title = %s $post_date", $title) );
	else
		if (!empty ($content))
			return $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_content = %s $post_date", $content) );

	return 0;
}

// Creates a new post from the "Write Post" form using $_POST information.
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

function add_meta( $post_ID ) {
	global $wpdb;
	$post_ID = (int) $post_ID;

	$protected = array( '_wp_attached_file', '_wp_attachment_metadata', '_wp_old_slug', '_wp_page_template' );

	$metakeyselect = $wpdb->escape( stripslashes( trim( $_POST['metakeyselect'] ) ) );
	$metakeyinput = $wpdb->escape( stripslashes( trim( $_POST['metakeyinput'] ) ) );
	$metavalue = maybe_serialize( stripslashes( (trim( $_POST['metavalue'] ) ) ));
	$metavalue = $wpdb->escape( $metavalue );

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

		$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta 
			(post_id,meta_key,meta_value ) VALUES (%s, %s, %s)",
			$post_ID, $metakey, $metavalue) );
		return $wpdb->insert_id;
	}
	return false;
} // add_meta

function delete_meta( $mid ) {
	global $wpdb;
	$mid = (int) $mid;

	$post_id = $wpdb->get_var( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_id = %d", $mid) );
	wp_cache_delete($post_id, 'post_meta');

	return $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE meta_id = %d", $mid) );
}

// Get a list of previously defined keys
function get_meta_keys() {
	global $wpdb;

	$keys = $wpdb->get_col( "
			SELECT meta_key
			FROM $wpdb->postmeta
			GROUP BY meta_key
			ORDER BY meta_key" );

	return $keys;
}

function get_post_meta_by_id( $mid ) {
	global $wpdb;
	$mid = (int) $mid;

	$meta = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE meta_id = %d", $mid) );
	if ( is_serialized_string( $meta->meta_value ) )
		$meta->meta_value = maybe_unserialize( $meta->meta_value );
	return $meta;
}

// Some postmeta stuff
function has_meta( $postid ) {
	global $wpdb;

	return $wpdb->get_results( $wpdb->prepare("SELECT meta_key, meta_value, meta_id, post_id
			FROM $wpdb->postmeta WHERE post_id = %d
			ORDER BY meta_key,meta_id", $postid), ARRAY_A );

}

function update_meta( $meta_id, $meta_key, $meta_value ) {
	global $wpdb;

	$protected = array( '_wp_attached_file', '_wp_attachment_metadata', '_wp_old_slug', '_wp_page_template' );

	if ( in_array($meta_key, $protected) )
		return false;

	$post_id = $wpdb->get_var( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_id = %d", $meta_id) );
	wp_cache_delete($post_id, 'post_meta');

	$meta_value = maybe_serialize( stripslashes( $meta_value ));
	$meta_id = (int) $meta_id;
	
	$data  = compact( 'meta_key', 'meta_value' );
	$where = compact( 'meta_id' );

	return $wpdb->update( $wpdb->postmeta, $data, $where );
}

//
// Private
//

// Replace hrefs of attachment anchors with up-to-date permalinks.
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

// Move child posts to a new parent
function _relocate_children( $old_ID, $new_ID ) {
	global $wpdb;
	$old_ID = (int) $old_ID;
	$new_ID = (int) $new_ID;
	return $wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET post_parent = %d WHERE post_parent = %d", $new_ID, $old_ID) );
}

function get_available_post_statuses($type = 'post') {
	$stati = wp_count_posts($type);

	return array_keys(get_object_vars($stati));
}

function wp_edit_posts_query( $q = false ) {
	global $wpdb;
	if ( false === $q )
		$q = $_GET;
	$q['m']   = (int) $q['m'];
	$q['cat'] = (int) $q['cat'];
	$post_stati  = array(	//	array( adj, noun )
				'publish' => array(__('Published'), __('Published posts'), __ngettext_noop('Published (%s)', 'Published (%s)')),
				'future' => array(__('Scheduled'), __('Scheduled posts'), __ngettext_noop('Scheduled (%s)', 'Scheduled (%s)')),
				'pending' => array(__('Pending Review'), __('Pending posts'), __ngettext_noop('Pending Review (%s)', 'Pending Review (%s)')),
				'draft' => array(__('Draft'), _c('Drafts|manage posts header'), __ngettext_noop('Draft (%s)', 'Drafts (%s)')),
				'private' => array(__('Private'), __('Private posts'), __ngettext_noop('Private (%s)', 'Private (%s)')),
			);

	$post_stati = apply_filters('post_stati', $post_stati);

	$avail_post_stati = get_available_post_statuses('post');

	$post_status_q = '';
	if ( isset($q['post_status']) && in_array( $q['post_status'], array_keys($post_stati) ) ) {
		$post_status_q = '&post_status=' . $q['post_status'];
		$post_status_q .= '&perm=readable';
	}

	if ( 'pending' === $q['post_status'] ) {
		$order = 'ASC';
		$orderby = 'modified';
	} elseif ( 'draft' === $q['post_status'] ) {
		$order = 'DESC';
		$orderby = 'modified';
	} else {
		$order = 'DESC';
		$orderby = 'date';
	}

	wp("post_type=post&what_to_show=posts$post_status_q&posts_per_page=15&order=$order&orderby=$orderby");

	return array($post_stati, $avail_post_stati);
}

function get_available_post_mime_types($type = 'attachment') {
	global $wpdb;

	$types = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT post_mime_type FROM $wpdb->posts WHERE post_type = %s", $type));
	return $types;
}

function wp_edit_attachments_query( $q = false ) {
	global $wpdb;
	if ( false === $q )
		$q = $_GET;
	$q['m']   = (int) $q['m'];
	$q['cat'] = (int) $q['cat'];
	$q['post_type'] = 'attachment';
	$q['post_status'] = 'any';
	$q['posts_per_page'] = 15;
	$post_mime_types = array(	//	array( adj, noun )
				'image' => array(__('Images'), __('Manage Images'), __ngettext_noop('Image (%s)', 'Images (%s)')),
				'audio' => array(__('Audio'), __('Manage Audio'), __ngettext_noop('Audio (%s)', 'Audio (%s)')),
				'video' => array(__('Video'), __('Manage Video'), __ngettext_noop('Video (%s)', 'Video (%s)')),
			);
	$post_mime_types = apply_filters('post_mime_types', $post_mime_types);

	$avail_post_mime_types = get_available_post_mime_types('attachment');

	if ( isset($q['post_mime_type']) && !array_intersect( (array) $q['post_mime_type'], array_keys($post_mime_types) ) )
		unset($q['post_mime_type']);

	wp($q);

	return array($post_mime_types, $avail_post_mime_types);
}

function postbox_classes( $id, $page ) {
	$current_user = wp_get_current_user();
	if ( $closed = get_usermeta( $current_user->ID, 'closedpostboxes_'.$page ) ) {
		if ( !is_array( $closed ) ) return '';
		return in_array( $id, $closed )? 'if-js-closed' : '';
	} else {
		if ( 'tagsdiv' == $id || 'categorydiv' == $id ) return '';
		else return 'if-js-closed';
	}
}

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
		$post->post_date = date('Y-m-d H:i:s');
		$post->post_name = sanitize_title($post->post_name? $post->post_name : $post->post_title, $post->ID); 
	}

	// If the user wants to set a new name -- override the current one
	// Note: if empty name is supplied -- use the title instead, see #6072
	if (!is_null($name)) {
		$post->post_name = sanitize_title($name? $name : $title, $post->ID);
	}

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
	$post->post_title = $original_title;
	return $permalink;
}

function get_sample_permalink_html($id, $new_title=null, $new_slug=null) {
	$post = &get_post($id);
	list($permalink, $post_name) = get_sample_permalink($post->ID, $new_title, $new_slug);
	if (false === strpos($permalink, '%postname%') && false === strpos($permalink, '%pagename%')) {
		return '';
	}
	$title = __('Click to edit this part of the permalink');
	if (strlen($post_name) > 30) {
		$post_name_abridged = substr($post_name, 0, 14). '&hellip;' . substr($post_name, -14);
	} else {
		$post_name_abridged = $post_name;
	}
	$post_name_html = '<span id="editable-post-name" title="'.$title.'">'.$post_name_abridged.'</span><span id="editable-post-name-full">'.$post_name.'</span>';
	$display_link = str_replace(array('%pagename%','%postname%'), $post_name_html, $permalink);
	$return = '<strong>' . __('Permalink:') . "</strong>\n" . '<span id="sample-permalink">' . $display_link . "</span>\n";
	$return .= '<span id="edit-slug-buttons"><a href="#post_name" class="edit-slug" onclick="edit_permalink(' . $id . '); return false;">' . __('Edit') . "</a></span>\n";
	return $return;
}

// false: not locked or locked by current user
// int: user ID of user with lock
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
 * wp_create_post_autosave() - creates autosave data for the specified post from $_POST data
 *
 * @package WordPress
 * @subpackage Post Revisions
 * @since 2.6
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

	// Otherwise create the new autosave as a special post revision
	return _wp_put_post_revision( $_POST, true );
}

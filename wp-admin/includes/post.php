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
 * @param array $post_data Array of post data. Defaults to the contents of $_POST.
 * @return object|bool WP_Error on failure, true on success.
 */
function _wp_translate_postdata( $update = false, $post_data = null ) {

	if ( empty($post_data) )
		$post_data = &$_POST;

	if ( $update )
		$post_data['ID'] = (int) $post_data['post_ID'];

	if ( isset( $post_data['content'] ) )
		$post_data['post_content'] = $post_data['content'];

	if ( isset( $post_data['excerpt'] ) )
		$post_data['post_excerpt'] = $post_data['excerpt'];

	if ( isset( $post_data['parent_id'] ) )
		$post_data['post_parent'] = (int) $post_data['parent_id'];

	if ( isset($post_data['trackback_url']) )
		$post_data['to_ping'] = $post_data['trackback_url'];

	if ( !isset($post_data['user_ID']) )
		$post_data['user_ID'] = $GLOBALS['user_ID'];

	if (!empty ( $post_data['post_author_override'] ) ) {
		$post_data['post_author'] = (int) $post_data['post_author_override'];
	} else {
		if (!empty ( $post_data['post_author'] ) ) {
			$post_data['post_author'] = (int) $post_data['post_author'];
		} else {
			$post_data['post_author'] = (int) $post_data['user_ID'];
		}
	}

	$ptype = get_post_type_object( $post_data['post_type'] );
	if ( isset($post_data['user_ID']) && ($post_data['post_author'] != $post_data['user_ID']) ) {
		if ( !current_user_can( $ptype->cap->edit_others_posts ) ) {
			if ( 'page' == $post_data['post_type'] ) {
				return new WP_Error( 'edit_others_pages', $update ?
					__( 'You are not allowed to edit pages as this user.' ) :
					__( 'You are not allowed to create pages as this user.' )
				);
			} else {
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
	if ( isset($post_data['publish']) && ( '' != $post_data['publish'] ) && ( !isset($post_data['post_status']) || $post_data['post_status'] != 'private' ) )
		$post_data['post_status'] = 'publish';
	if ( isset($post_data['advanced']) && '' != $post_data['advanced'] )
		$post_data['post_status'] = 'draft';
	if ( isset($post_data['pending']) && '' != $post_data['pending'] )
		$post_data['post_status'] = 'pending';

	if ( isset( $post_data['ID'] ) )
		$post_id = $post_data['ID'];
	else
		$post_id = false;
	$previous_status = $post_id ? get_post_field( 'post_status', $post_id ) : false;

	// Posts 'submitted for approval' present are submitted to $_POST the same as if they were being published.
	// Change status from 'publish' to 'pending' if user lacks permissions to publish or to resave published posts.
	if ( isset($post_data['post_status']) && ('publish' == $post_data['post_status'] && !current_user_can( $ptype->cap->publish_posts )) )
		if ( $previous_status != 'publish' || !current_user_can( 'edit_post', $post_id ) )
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
 * @since 1.5.0
 *
 * @param array $post_data Optional.
 * @return int Post ID.
 */
function edit_post( $post_data = null ) {

	if ( empty($post_data) )
		$post_data = &$_POST;

	// Clear out any data in internal vars.
	unset( $post_data['filter'] );

	$post_ID = (int) $post_data['post_ID'];
	$post = get_post( $post_ID );
	$post_data['post_type'] = $post->post_type;
	$post_data['post_mime_type'] = $post->post_mime_type;

	$ptype = get_post_type_object($post_data['post_type']);
	if ( !current_user_can( $ptype->cap->edit_post, $post_ID ) ) {
		if ( 'page' == $post_data['post_type'] )
			wp_die( __('You are not allowed to edit this page.' ));
		else
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
	if ( 'autosave' != $post_data['action']  && 'auto-draft' == $post_data['post_status'] )
		$post_data['post_status'] = 'draft';

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

	// Post Formats
	if ( current_theme_supports( 'post-formats' ) && isset( $post_data['post_format'] ) ) {
		$formats = get_theme_support( 'post-formats' );
		if ( is_array( $formats ) ) {
			$formats = $formats[0];
			if ( in_array( $post_data['post_format'], $formats ) ) {
				set_post_format( $post_ID, $post_data['post_format'] );
			} elseif ( '0' == $post_data['post_format'] ) {
				set_post_format( $post_ID, false );
			}
		}
	}

	// Meta Stuff
	if ( isset($post_data['meta']) && $post_data['meta'] ) {
		foreach ( $post_data['meta'] as $key => $value ) {
			if ( !$meta = get_post_meta_by_id( $key ) )
				continue;
			if ( $meta->post_id != $post_ID )
				continue;
			if ( is_protected_meta( $value['key'], 'post' ) || ! current_user_can( 'edit_post_meta', $post_ID, $value['key'] ) )
				continue;
			update_meta( $key, $value['key'], $value['value'] );
		}
	}

	if ( isset($post_data['deletemeta']) && $post_data['deletemeta'] ) {
		foreach ( $post_data['deletemeta'] as $key => $value ) {
			if ( !$meta = get_post_meta_by_id( $key ) )
				continue;
			if ( $meta->post_id != $post_ID )
				continue;
			if ( is_protected_meta( $meta->meta_key, 'post' ) || ! current_user_can( 'delete_post_meta', $post_ID, $meta->meta_key ) )
				continue;
			delete_meta( $key );
		}
	}

	add_meta( $post_ID );

	update_post_meta( $post_ID, '_edit_last', $GLOBALS['current_user']->ID );

	wp_update_post( $post_data );

	// Reunite any orphaned attachments with their parent
	if ( !$draft_ids = get_user_option( 'autosave_draft_ids' ) )
		$draft_ids = array();
	if ( $draft_temp_id = (int) array_search( $post_ID, $draft_ids ) )
		_relocate_children( $draft_temp_id, $post_ID );

	// Now that we have an ID we can fix any attachment anchor hrefs
	_fix_attachment_links( $post_ID );

	wp_set_post_lock( $post_ID, $GLOBALS['current_user']->ID );

	if ( current_user_can( $ptype->cap->edit_others_posts ) ) {
		if ( ! empty( $post_data['sticky'] ) )
			stick_post( $post_ID );
		else
			unstick_post( $post_ID );
	}

	return $post_ID;
}

/**
 * Process the post data for the bulk editing of posts.
 *
 * Updates all bulk edited posts/pages, adding (but not removing) tags and
 * categories. Skips pages when they would be their own parent or child.
 *
 * @since 2.7.0
 *
 * @param array $post_data Optional, the array of post data to process if not provided will use $_POST superglobal.
 * @return array
 */
function bulk_edit_posts( $post_data = null ) {
	global $wpdb;

	if ( empty($post_data) )
		$post_data = &$_POST;

	if ( isset($post_data['post_type']) )
		$ptype = get_post_type_object($post_data['post_type']);
	else
		$ptype = get_post_type_object('post');

	if ( !current_user_can( $ptype->cap->edit_posts ) ) {
		if ( 'page' == $ptype->name )
			wp_die( __('You are not allowed to edit pages.'));
		else
			wp_die( __('You are not allowed to edit posts.'));
	}

	if ( -1 == $post_data['_status'] ) {
		$post_data['post_status'] = null;
		unset($post_data['post_status']);
	} else {
		$post_data['post_status'] = $post_data['_status'];
	}
	unset($post_data['_status']);

	$post_IDs = array_map( 'intval', (array) $post_data['post'] );

	$reset = array( 'post_author', 'post_status', 'post_password', 'post_parent', 'page_template', 'comment_status', 'ping_status', 'keep_private', 'tax_input', 'post_category', 'sticky' );
	foreach ( $reset as $field ) {
		if ( isset($post_data[$field]) && ( '' == $post_data[$field] || -1 == $post_data[$field] ) )
			unset($post_data[$field]);
	}

	if ( isset($post_data['post_category']) ) {
		if ( is_array($post_data['post_category']) && ! empty($post_data['post_category']) )
			$new_cats = array_map( 'absint', $post_data['post_category'] );
		else
			unset($post_data['post_category']);
	}

	$tax_input = array();
	if ( isset($post_data['tax_input'])) {
		foreach ( $post_data['tax_input'] as $tax_name => $terms ) {
			if ( empty($terms) )
				continue;
			if ( is_taxonomy_hierarchical( $tax_name ) )
				$tax_input[$tax_name] = array_map( 'absint', $terms );
			else {
				$tax_input[$tax_name] = preg_replace( '/\s*,\s*/', ',', rtrim( trim($terms), ' ,' ) );
				$tax_input[$tax_name] = explode(',', $tax_input[$tax_name]);
			}
		}
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
		$post_type_object = get_post_type_object( get_post_type( $post_ID ) );

		if ( !isset( $post_type_object ) || ( isset($children) && in_array($post_ID, $children) ) || !current_user_can( $post_type_object->cap->edit_post, $post_ID ) ) {
			$skipped[] = $post_ID;
			continue;
		}

		if ( wp_check_post_lock( $post_ID ) ) {
			$locked[] = $post_ID;
			continue;
		}

		$post = get_post( $post_ID );
		$tax_names = get_object_taxonomies( $post );
		foreach ( $tax_names as $tax_name ) {
			$taxonomy_obj = get_taxonomy($tax_name);
			if (  isset( $tax_input[$tax_name]) && current_user_can( $taxonomy_obj->cap->assign_terms ) )
				$new_terms = $tax_input[$tax_name];
			else
				$new_terms = array();

			if ( $taxonomy_obj->hierarchical )
				$current_terms = (array) wp_get_object_terms( $post_ID, $tax_name, array('fields' => 'ids') );
			else
				$current_terms = (array) wp_get_object_terms( $post_ID, $tax_name, array('fields' => 'names') );

			$post_data['tax_input'][$tax_name] = array_merge( $current_terms, $new_terms );
		}

		if ( isset($new_cats) && in_array( 'category', $tax_names ) ) {
			$cats = (array) wp_get_post_categories($post_ID);
			$post_data['post_category'] = array_unique( array_merge($cats, $new_cats) );
			unset( $post_data['tax_input']['category'] );
		}

		$post_data['post_mime_type'] = $post->post_mime_type;
		$post_data['guid'] = $post->guid;

		$post_data['ID'] = $post_ID;
		$updated[] = wp_update_post( $post_data );

		if ( isset( $post_data['sticky'] ) && current_user_can( $ptype->cap->edit_others_posts ) ) {
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
 * @since 2.0.0
 *
 * @param string $post_type A post type string, defaults to 'post'.
 * @return object stdClass object containing all the default post data as attributes
 */
function get_default_post_to_edit( $post_type = 'post', $create_in_db = false ) {
	global $wpdb;

	$post_title = '';
	if ( !empty( $_REQUEST['post_title'] ) )
		$post_title = esc_html( stripslashes( $_REQUEST['post_title'] ));

	$post_content = '';
	if ( !empty( $_REQUEST['content'] ) )
		$post_content = esc_html( stripslashes( $_REQUEST['content'] ));

	$post_excerpt = '';
	if ( !empty( $_REQUEST['excerpt'] ) )
		$post_excerpt = esc_html( stripslashes( $_REQUEST['excerpt'] ));

	if ( $create_in_db ) {
		// Cleanup old auto-drafts more than 7 days old
		$old_posts = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_status = 'auto-draft' AND DATE_SUB( NOW(), INTERVAL 7 DAY ) > post_date" );
		foreach ( (array) $old_posts as $delete )
			wp_delete_post( $delete, true ); // Force delete
		$post_id = wp_insert_post( array( 'post_title' => __( 'Auto Draft' ), 'post_type' => $post_type, 'post_status' => 'auto-draft' ) );
		$post = get_post( $post_id );
		if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post->post_type, 'post-formats' ) && get_option( 'default_post_format' ) )
			set_post_format( $post, get_option( 'default_post_format' ) );
	} else {
		$post->ID = 0;
		$post->post_author = '';
		$post->post_date = '';
		$post->post_date_gmt = '';
		$post->post_password = '';
		$post->post_type = $post_type;
		$post->post_status = 'draft';
		$post->to_ping = '';
		$post->pinged = '';
		$post->comment_status = get_option( 'default_comment_status' );
		$post->ping_status = get_option( 'default_ping_status' );
		$post->post_pingback = get_option( 'default_pingback_flag' );
		$post->post_category = get_option( 'default_category' );
		$post->page_template = 'default';
		$post->post_parent = 0;
		$post->menu_order = 0;
	}

	$post->post_content = apply_filters( 'default_content', $post_content, $post );
	$post->post_title   = apply_filters( 'default_title',   $post_title, $post   );
	$post->post_excerpt = apply_filters( 'default_excerpt', $post_excerpt, $post );
	$post->post_name = '';

	return $post;
}

/**
 * Get the default page information to use.
 *
 * @since 2.5.0
 *
 * @return object stdClass object containing all the default post data as attributes
 */
function get_default_page_to_edit() {
	$page = get_default_post_to_edit();
	$page->post_type = 'page';
	return $page;
}

/**
 * Get an existing post and format it for editing.
 *
 * @since 2.0.0
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
 * @since 2.0.0
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
 * @since 2.1.0
 *
 * @return unknown
 */
function wp_write_post() {
	global $user_ID;


	if ( isset($_POST['post_type']) )
		$ptype = get_post_type_object($_POST['post_type']);
	else
		$ptype = get_post_type_object('post');

	if ( !current_user_can( $ptype->cap->edit_posts ) ) {
		if ( 'page' == $ptype->name )
			return new WP_Error( 'edit_pages', __( 'You are not allowed to create pages on this site.' ) );
		else
			return new WP_Error( 'edit_posts', __( 'You are not allowed to create posts or drafts on this site.' ) );
	}

	$_POST['post_mime_type'] = '';

	// Clear out any data in internal vars.
	unset( $_POST['filter'] );

	// Check for autosave collisions
	// Does this need to be updated? ~ Mark
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

	// Edit don't write if we have a post id.
	if ( isset( $_POST['ID'] ) ) {
		$_POST['post_ID'] = $_POST['ID'];
		unset ( $_POST['ID'] );
	}
	if ( isset( $_POST['post_ID'] ) ) {
		return edit_post();
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

	add_post_meta( $post_ID, '_edit_last', $GLOBALS['current_user']->ID );

	// Reunite any orphaned attachments with their parent
	// Does this need to be udpated? ~ Mark
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
 * @since 2.0.0
 *
 * @return unknown
 */
function write_post() {
	$result = wp_write_post();
	if ( is_wp_error( $result ) )
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
 * @since 1.2.0
 *
 * @param unknown_type $post_ID
 * @return unknown
 */
function add_meta( $post_ID ) {
	global $wpdb;
	$post_ID = (int) $post_ID;

	$metakeyselect = isset($_POST['metakeyselect']) ? stripslashes( trim( $_POST['metakeyselect'] ) ) : '';
	$metakeyinput = isset($_POST['metakeyinput']) ? stripslashes( trim( $_POST['metakeyinput'] ) ) : '';
	$metavalue = isset($_POST['metavalue']) ? $_POST['metavalue'] : '';
	if ( is_string( $metavalue ) )
		$metavalue = trim( $metavalue );

	if ( ('0' === $metavalue || ! empty ( $metavalue ) ) && ( ( ( '#NONE#' != $metakeyselect ) && !empty ( $metakeyselect) ) || !empty ( $metakeyinput ) ) ) {
		// We have a key/value pair. If both the select and the
		// input for the key have data, the input takes precedence:

 		if ( '#NONE#' != $metakeyselect )
			$metakey = $metakeyselect;

		if ( $metakeyinput )
			$metakey = $metakeyinput; // default

		if ( is_protected_meta( $metakey, 'post' ) || ! current_user_can( 'add_post_meta', $post_ID, $metakey ) )
			return false;

		$metakey = esc_sql( $metakey );

		return add_post_meta( $post_ID, $metakey, $metavalue );
	}

	return false;
} // add_meta

/**
 * {@internal Missing Short Description}}
 *
 * @since 1.2.0
 *
 * @param unknown_type $mid
 * @return unknown
 */
function delete_meta( $mid ) {
	global $wpdb;
	$mid = (int) $mid;

	$post_id = $wpdb->get_var( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_id = %d", $mid) );

	do_action( 'delete_postmeta', $mid );
	wp_cache_delete($post_id, 'post_meta');
	$rval = $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE meta_id = %d", $mid) );
	do_action( 'deleted_postmeta', $mid );

	return $rval;
}

/**
 * Get a list of previously defined keys.
 *
 * @since 1.2.0
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
 * @since 2.1.0
 *
 * @param unknown_type $mid
 * @return unknown
 */
function get_post_meta_by_id( $mid ) {
	global $wpdb;
	$mid = (int) $mid;

	$meta = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE meta_id = %d", $mid) );
	if ( empty($meta) )
		return false;
	if ( is_serialized_string( $meta->meta_value ) )
		$meta->meta_value = maybe_unserialize( $meta->meta_value );
	return $meta;
}

/**
 * {@internal Missing Short Description}}
 *
 * Some postmeta stuff.
 *
 * @since 1.2.0
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
 * @since 1.2.0
 *
 * @param unknown_type $meta_id
 * @param unknown_type $meta_key Expect Slashed
 * @param unknown_type $meta_value Expect Slashed
 * @return unknown
 */
function update_meta( $meta_id, $meta_key, $meta_value ) {
	global $wpdb;

	$meta_key = stripslashes($meta_key);

	if ( '' === trim( $meta_value ) )
		return false;

	$post_id = $wpdb->get_var( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_id = %d", $meta_id) );

	$meta_value = maybe_serialize( stripslashes_deep( $meta_value ) );
	$meta_id = (int) $meta_id;

	$data  = compact( 'meta_key', 'meta_value' );
	$where = compact( 'meta_id' );

	do_action( 'update_postmeta', $meta_id, $post_id, $meta_key, $meta_value );
	$rval = $wpdb->update( $wpdb->postmeta, $data, $where );
	wp_cache_delete($post_id, 'post_meta');
	do_action( 'updated_postmeta', $meta_id, $post_id, $meta_key, $meta_value );

	return $rval;
}

//
// Private
//

/**
 * Replace hrefs of attachment anchors with up-to-date permalinks.
 *
 * @since 2.3.0
 * @access private
 *
 * @param unknown_type $post_ID
 * @return unknown
 */
function _fix_attachment_links( $post_ID ) {
	global $_fix_attachment_link_id;

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
			$attachment = add_magic_quotes( $attachment );
			wp_update_post( $attachment );
		}

		$post_search[$i] = $anchor;
		 $_fix_attachment_link_id = $id;
		$post_replace[$i] = preg_replace_callback( "#href=(\"|')[^'\"]*\\1#", '_fix_attachment_links_replace_cb', $anchor );
		++$i;
	}

	$post['post_content'] = str_replace( $post_search, $post_replace, $post['post_content'] );

	// Escape data pulled from DB.
	$post = add_magic_quotes( $post);

	return wp_update_post( $post);
}

function _fix_attachment_links_replace_cb($match) {
        global $_fix_attachment_link_id;
        return stripslashes( 'href='.$match[1] ).get_attachment_link( $_fix_attachment_link_id ).stripslashes( $match[1] );
}

/**
 * Move child posts to a new parent.
 *
 * @since 2.3.0
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

	$children = $wpdb->get_col( $wpdb->prepare("
		SELECT post_id
		FROM $wpdb->postmeta
		WHERE meta_key = '_wp_attachment_temp_parent'
		AND meta_value = %d", $old_ID) );

	foreach ( $children as $child_id ) {
		$wpdb->update($wpdb->posts, array('post_parent' => $new_ID), array('ID' => $child_id) );
		delete_post_meta($child_id, '_wp_attachment_temp_parent');
	}
}

/**
 * Get all the possible statuses for a post_type
 *
 * @since 2.5.0
 *
 * @param string $type The post_type you want the statuses for
 * @return array As array of all the statuses for the supplied post type
 */
function get_available_post_statuses($type = 'post') {
	$stati = wp_count_posts($type);

	return array_keys(get_object_vars($stati));
}

/**
 * Run the wp query to fetch the posts for listing on the edit posts page
 *
 * @since 2.5.0
 *
 * @param array|bool $q Array of query variables to use to build the query or false to use $_GET superglobal.
 * @return array
 */
function wp_edit_posts_query( $q = false ) {
	if ( false === $q )
		$q = $_GET;
	$q['m'] = isset($q['m']) ? (int) $q['m'] : 0;
	$q['cat'] = isset($q['cat']) ? (int) $q['cat'] : 0;
	$post_stati  = get_post_stati();

	if ( isset($q['post_type']) && in_array( $q['post_type'], get_post_types() ) )
		$post_type = $q['post_type'];
	else
		$post_type = 'post';

	$avail_post_stati = get_available_post_statuses($post_type);

	if ( isset($q['post_status']) && in_array( $q['post_status'], $post_stati ) ) {
		$post_status = $q['post_status'];
		$perm = 'readable';
	}

	if ( isset($q['orderby']) )
		$orderby = $q['orderby'];
	elseif ( isset($q['post_status']) && in_array($q['post_status'], array('pending', 'draft')) )
		$orderby = 'modified';

	if ( isset($q['order']) )
		$order = $q['order'];
	elseif ( isset($q['post_status']) && 'pending' == $q['post_status'] )
		$order = 'ASC';

	$per_page = 'edit_' . $post_type . '_per_page';
	$posts_per_page = (int) get_user_option( $per_page );
	if ( empty( $posts_per_page ) || $posts_per_page < 1 )
		$posts_per_page = 20;

	$posts_per_page = apply_filters( $per_page, $posts_per_page );
	$posts_per_page = apply_filters( 'edit_posts_per_page', $posts_per_page, $post_type );

	$query = compact('post_type', 'post_status', 'perm', 'order', 'orderby', 'posts_per_page');

	// Hierarchical types require special args.
	if ( is_post_type_hierarchical( $post_type ) && !isset($orderby) ) {
		$query['orderby'] = 'menu_order title';
		$query['order'] = 'asc';
		$query['posts_per_page'] = -1;
		$query['posts_per_archive_page'] = -1;
	}

	if ( ! empty( $q['show_sticky'] ) )
		$query['post__in'] = (array) get_option( 'sticky_posts' );

	wp( $query );

	return $avail_post_stati;
}

/**
 * Get default post mime types
 *
 * @since 2.9.0
 *
 * @return array
 */
function get_post_mime_types() {
	$post_mime_types = array(	//	array( adj, noun )
		'image' => array(__('Images'), __('Manage Images'), _n_noop('Image <span class="count">(%s)</span>', 'Images <span class="count">(%s)</span>')),
		'audio' => array(__('Audio'), __('Manage Audio'), _n_noop('Audio <span class="count">(%s)</span>', 'Audio <span class="count">(%s)</span>')),
		'video' => array(__('Video'), __('Manage Video'), _n_noop('Video <span class="count">(%s)</span>', 'Video <span class="count">(%s)</span>')),
	);

	return apply_filters('post_mime_types', $post_mime_types);
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.0
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
 * @since 2.5.0
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
	$post_type = get_post_type_object( 'attachment' );
	$states = 'inherit';
	if ( current_user_can( $post_type->cap->read_private_posts ) )
		$states .= ',private';

	$q['post_status'] = isset( $q['status'] ) && 'trash' == $q['status'] ? 'trash' : $states;
	$media_per_page = (int) get_user_option( 'upload_per_page' );
	if ( empty( $media_per_page ) || $media_per_page < 1 )
		$media_per_page = 20;
	$q['posts_per_page'] = apply_filters( 'upload_per_page', $media_per_page );

	$post_mime_types = get_post_mime_types();
	$avail_post_mime_types = get_available_post_mime_types('attachment');

	if ( isset($q['post_mime_type']) && !array_intersect( (array) $q['post_mime_type'], array_keys($post_mime_types) ) )
		unset($q['post_mime_type']);

	if ( isset($q['detached']) )
		add_filter('posts_where', '_edit_attachments_query_helper');

	wp( $q );

	if ( isset($q['detached']) )
		remove_filter('posts_where', '_edit_attachments_query_helper');

	return array($post_mime_types, $avail_post_mime_types);
}

function _edit_attachments_query_helper($where) {
	return $where .= ' AND post_parent < 1';
}

/**
 * Returns the list of classes to be used by a metabox
 *
 * @uses get_user_option()
 * @since 2.5.0
 *
 * @param unknown_type $id
 * @param unknown_type $page
 * @return unknown
 */
function postbox_classes( $id, $page ) {
	if ( isset( $_GET['edit'] ) && $_GET['edit'] == $id ) {
		$classes = array( '' );
	} elseif ( $closed = get_user_option('closedpostboxes_'.$page ) ) {
		if ( !is_array( $closed ) ) {
			$classes = array( '' );
		} else {
			$classes = in_array( $id, $closed ) ? array( 'closed' ) : array( '' );
		}
	} else {
		$classes = array( '' );
	}

	$classes = apply_filters( "postbox_classes_{$page}_{$id}", $classes );
	return implode( ' ', $classes );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.0
 *
 * @param int|object $id    Post ID or post object.
 * @param string $title (optional) Title
 * @param string $name (optional) Name
 * @return array With two entries of type string
 */
function get_sample_permalink($id, $title = null, $name = null) {
	$post = &get_post($id);
	if ( !$post->ID )
		return array('', '');

	$ptype = get_post_type_object($post->post_type);

	$original_status = $post->post_status;
	$original_date = $post->post_date;
	$original_name = $post->post_name;

	// Hack: get_permalink would return ugly permalink for
	// drafts, so we will fake, that our post is published
	if ( in_array($post->post_status, array('draft', 'pending')) ) {
		$post->post_status = 'publish';
		$post->post_name = sanitize_title($post->post_name ? $post->post_name : $post->post_title, $post->ID);
	}

	// If the user wants to set a new name -- override the current one
	// Note: if empty name is supplied -- use the title instead, see #6072
	if ( !is_null($name) )
		$post->post_name = sanitize_title($name ? $name : $title, $post->ID);

	$post->post_name = wp_unique_post_slug($post->post_name, $post->ID, $post->post_status, $post->post_type, $post->post_parent);

	$post->filter = 'sample';

	$permalink = get_permalink($post, true);

	// Replace custom post_type Token with generic pagename token for ease of use.
	$permalink = str_replace("%$post->post_type%", '%pagename%', $permalink);

	// Handle page hierarchy
	if ( $ptype->hierarchical ) {
		$uri = get_page_uri($post);
		$uri = untrailingslashit($uri);
		$uri = strrev( stristr( strrev( $uri ), '/' ) );
		$uri = untrailingslashit($uri);
		$uri = apply_filters( 'editable_slug', $uri );
		if ( !empty($uri) )
			$uri .= '/';
		$permalink = str_replace('%pagename%', "{$uri}%pagename%", $permalink);
	}

	$permalink = array($permalink, apply_filters('editable_slug', $post->post_name));
	$post->post_status = $original_status;
	$post->post_date = $original_date;
	$post->post_name = $original_name;
	unset($post->filter);

	return $permalink;
}

/**
 * sample permalink html
 *
 * intended to be used for the inplace editor of the permalink post slug on in the post (and page?) editor.
 *
 * @since 2.5.0
 *
 * @param int|object $id Post ID or post object.
 * @param string $new_title (optional) New title
 * @param string $new_slug (optional) New slug
 * @return string intended to be used for the inplace editor of the permalink post slug on in the post (and page?) editor.
 */
function get_sample_permalink_html( $id, $new_title = null, $new_slug = null ) {
	global $wpdb;
	$post = &get_post($id);

	list($permalink, $post_name) = get_sample_permalink($post->ID, $new_title, $new_slug);

	if ( 'publish' == $post->post_status ) {
		$ptype = get_post_type_object($post->post_type);
		$view_post = $ptype->labels->view_item;
		$title = __('Click to edit this part of the permalink');
	} else {
		$title = __('Temporary permalink. Click to edit this part.');
	}

	if ( false === strpos($permalink, '%postname%') && false === strpos($permalink, '%pagename%') ) {
		$return = '<strong>' . __('Permalink:') . "</strong>\n" . '<span id="sample-permalink">' . $permalink . "</span>\n";
		if ( '' == get_option( 'permalink_structure' ) && current_user_can( 'manage_options' ) && !( 'page' == get_option('show_on_front') && $id == get_option('page_on_front') ) )
			$return .= '<span id="change-permalinks"><a href="options-permalink.php" class="button" target="_blank">' . __('Change Permalinks') . "</a></span>\n";
		if ( isset($view_post) )
			$return .= "<span id='view-post-btn'><a href='$permalink' class='button' target='_blank'>$view_post</a></span>\n";

		$return = apply_filters('get_sample_permalink_html', $return, $id, $new_title, $new_slug);

		return $return;
	}

	if ( function_exists('mb_strlen') ) {
		if ( mb_strlen($post_name) > 30 ) {
			$post_name_abridged = mb_substr($post_name, 0, 14). '&hellip;' . mb_substr($post_name, -14);
		} else {
			$post_name_abridged = $post_name;
		}
	} else {
		if ( strlen($post_name) > 30 ) {
			$post_name_abridged = substr($post_name, 0, 14). '&hellip;' . substr($post_name, -14);
		} else {
			$post_name_abridged = $post_name;
		}
	}

	$post_name_html = '<span id="editable-post-name" title="' . $title . '">' . $post_name_abridged . '</span>';
	$display_link = str_replace(array('%pagename%','%postname%'), $post_name_html, $permalink);
	$view_link = str_replace(array('%pagename%','%postname%'), $post_name, $permalink);
	$return =  '<strong>' . __('Permalink:') . "</strong>\n";
	$return .= '<span id="sample-permalink">' . $display_link . "</span>\n";
	$return .= '&lrm;'; // Fix bi-directional text display defect in RTL languages.
	$return .= '<span id="edit-slug-buttons"><a href="#post_name" class="edit-slug button hide-if-no-js" onclick="editPermalink(' . $id . '); return false;">' . __('Edit') . "</a></span>\n";
	$return .= '<span id="editable-post-name-full">' . $post_name . "</span>\n";
	if ( isset($view_post) )
		$return .= "<span id='view-post-btn'><a href='$view_link' class='button' target='_blank'>$view_post</a></span>\n";

	$return = apply_filters('get_sample_permalink_html', $return, $id, $new_title, $new_slug);

	return $return;
}

/**
 * Output HTML for the post thumbnail meta-box.
 *
 * @since 2.9.0
 *
 * @param int $thumbnail_id ID of the attachment used for thumbnail
 * @return string html
 */
function _wp_post_thumbnail_html( $thumbnail_id = NULL ) {
	global $content_width, $_wp_additional_image_sizes, $post_ID;
	$set_thumbnail_link = '<p class="hide-if-no-js"><a title="' . esc_attr__( 'Set featured image' ) . '" href="' . esc_url( get_upload_iframe_src('image') ) . '" id="set-post-thumbnail" class="thickbox">%s</a></p>';
	$content = sprintf($set_thumbnail_link, esc_html__( 'Set featured image' ));

	if ( $thumbnail_id && get_post( $thumbnail_id ) ) {
		$old_content_width = $content_width;
		$content_width = 266;
		if ( !isset( $_wp_additional_image_sizes['post-thumbnail'] ) )
			$thumbnail_html = wp_get_attachment_image( $thumbnail_id, array( $content_width, $content_width ) );
		else
			$thumbnail_html = wp_get_attachment_image( $thumbnail_id, 'post-thumbnail' );
		if ( !empty( $thumbnail_html ) ) {
			$ajax_nonce = wp_create_nonce( "set_post_thumbnail-$post_ID" );
			$content = sprintf($set_thumbnail_link, $thumbnail_html);
			$content .= '<p class="hide-if-no-js"><a href="#" id="remove-post-thumbnail" onclick="WPRemoveThumbnail(\'' . $ajax_nonce . '\');return false;">' . esc_html__( 'Remove featured image' ) . '</a></p>';
		}
		$content_width = $old_content_width;
	}

	return apply_filters( 'admin_post_thumbnail_html', $content );
}

/**
 * Check to see if the post is currently being edited by another user.
 *
 * @since 2.5.0
 *
 * @param int $post_id ID of the post to check for editing
 * @return bool|int False: not locked or locked by current user. Int: user ID of user with lock.
 */
function wp_check_post_lock( $post_id ) {
	if ( !$post = get_post( $post_id ) )
		return false;

	if ( !$lock = get_post_meta( $post->ID, '_edit_lock', true ) )
		return false;

	$lock = explode( ':', $lock );
	$time = $lock[0];
	$user = isset( $lock[1] ) ? $lock[1] : get_post_meta( $post->ID, '_edit_last', true );

	$time_window = apply_filters( 'wp_check_post_lock_window', AUTOSAVE_INTERVAL * 2 );

	if ( $time && $time > time() - $time_window && $user != get_current_user_id() )
		return $user;
	return false;
}

/**
 * Mark the post as currently being edited by the current user
 *
 * @since 2.5.0
 *
 * @param int $post_id ID of the post to being edited
 * @return bool Returns false if the post doesn't exist of there is no current user
 */
function wp_set_post_lock( $post_id ) {
	if ( !$post = get_post( $post_id ) )
		return false;
	if ( 0 == ($user_id = get_current_user_id()) )
		return false;

	$now = time();
	$lock = "$now:$user_id";

	update_post_meta( $post->ID, '_edit_lock', $lock );
}

/**
 * Outputs the notice message to say that someone else is editing this post at the moment.
 *
 * @since 2.8.5
 * @return none
 */
function _admin_notice_post_locked() {
	global $post;

	$lock = explode( ':', get_post_meta( $post->ID, '_edit_lock', true ) );
	$user = isset( $lock[1] ) ? $lock[1] : get_post_meta( $post->ID, '_edit_last', true );
	$last_user = get_userdata( $user );
	$last_user_name = $last_user ? $last_user->display_name : __('Somebody');

	switch ($post->post_type) {
		case 'post':
			$message = __( 'Warning: %s is currently editing this post' );
			break;
		case 'page':
			$message = __( 'Warning: %s is currently editing this page' );
			break;
		default:
			$message = __( 'Warning: %s is currently editing this.' );
	}

	$message = sprintf( $message, esc_html( $last_user_name ) );
	echo "<div class='error'><p>$message</p></div>";
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
 *
 * @return unknown
 */
function wp_create_post_autosave( $post_id ) {
	$translated = _wp_translate_postdata( true );
	if ( is_wp_error( $translated ) )
		return $translated;

	// Only store one autosave.  If there is already an autosave, overwrite it.
	if ( $old_autosave = wp_get_post_autosave( $post_id ) ) {
		$new_autosave = _wp_post_revision_fields( $_POST, true );
		$new_autosave['ID'] = $old_autosave->ID;
		$new_autosave['post_author'] = get_current_user_id();
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
 * @since 2.7.0
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
	$status = get_post_status( $post_ID );
	if ( 'auto-draft' == $status )
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

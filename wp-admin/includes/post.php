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
	$post_data['post_content'] = isset($post_data['content']) ? $post_data['content'] : '';
	$post_data['post_excerpt'] = isset($post_data['excerpt']) ? $post_data['excerpt'] : '';
	$post_data['post_parent'] = isset($post_data['parent_id'])? $post_data['parent_id'] : '';
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

	$post_ID = (int) $post_data['post_ID'];
	$post = get_post( $post_ID );
	$post_data['post_type'] = $post->post_type;

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
			update_meta( $key, $value['key'], $value['value'] );
		}
	}

	if ( isset($post_data['deletemeta']) && $post_data['deletemeta'] ) {
		foreach ( $post_data['deletemeta'] as $key => $value ) {
			if ( !$meta = get_post_meta_by_id( $key ) )
				continue;
			if ( $meta->post_id != $post_ID )
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

		$tax_names = get_object_taxonomies( get_post($post_ID) );
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

	$protected = array( '_wp_attached_file', '_wp_attachment_metadata', '_wp_old_slug', '_wp_page_template' );

	$metakeyselect = isset($_POST['metakeyselect']) ? stripslashes( trim( $_POST['metakeyselect'] ) ) : '';
	$metakeyinput = isset($_POST['metakeyinput']) ? stripslashes( trim( $_POST['metakeyinput'] ) ) : '';
	$metavalue = isset($_POST['metavalue']) ? maybe_serialize( stripslashes_deep( $_POST['metavalue'] ) ) : '';
	if ( is_string($metavalue) )
		$metavalue = trim( $metavalue );

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
		$wpdb->insert( $wpdb->postmeta, array( 'post_id' => $post_ID, 'meta_key' => $metakey, 'meta_value' => $metavalue ) );
		$meta_id = $wpdb->insert_id;
		do_action( 'added_postmeta', $meta_id, $post_ID, $metakey, $metavalue );

		return $meta_id;
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

	$protected = array( '_wp_attached_file', '_wp_attachment_metadata', '_wp_old_slug', '_wp_page_template' );

	$meta_key = stripslashes($meta_key);

	if ( in_array($meta_key, $protected) )
		return false;

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
	$q['post_status'] = isset( $q['status'] ) && 'trash' == $q['status'] ? 'trash' : 'inherit';
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
 * {@internal Missing Short Description}}
 *
 * @uses get_user_option()
 * @since 2.5.0
 *
 * @param unknown_type $id
 * @param unknown_type $page
 * @return unknown
 */
function postbox_classes( $id, $page ) {
	if ( isset( $_GET['edit'] ) && $_GET['edit'] == $id )
		return '';

	if ( $closed = get_user_option('closedpostboxes_'.$page ) ) {
		if ( !is_array( $closed ) ) {
			return '';
		}
		return in_array( $id, $closed )? 'closed' : '';
	} else {
		return '';
	}
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

/**
 * Adds the TinyMCE editor used on the Write and Edit screens.
 *
 * @package WordPress
 * @since 2.7.0
 *
 * TinyMCE is loaded separately from other Javascript by using wp-tinymce.php. It outputs concatenated
 * and optionaly pre-compressed version of the core and all default plugins. Additional plugins are loaded
 * directly by TinyMCE using non-blocking method. Custom plugins can be refreshed by adding a query string
 * to the URL when queueing them with the mce_external_plugins filter.
 *
 * @param bool $teeny optional Output a trimmed down version used in Press This.
 * @param mixed $settings optional An array that can add to or overwrite the default TinyMCE settings.
 */
function wp_tiny_mce( $teeny = false, $settings = false ) {
	global $concatenate_scripts, $compress_scripts, $tinymce_version, $editor_styles;

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
		$plugins = apply_filters( 'teeny_mce_plugins', array('inlinepopups', 'fullscreen', 'wordpress', 'wplink', 'wpdialogs') );
		$ext_plugins = '';
	} else {
		$plugins = array( 'inlinepopups', 'spellchecker', 'tabfocus', 'paste', 'media', 'wordpress', 'wpfullscreen', 'wpeditimage', 'wpgallery', 'wplink', 'wpdialogs' );

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

	if ( $teeny ) {
		$mce_buttons = apply_filters( 'teeny_mce_buttons', array('bold, italic, underline, blockquote, separator, strikethrough, bullist, numlist,justifyleft, justifycenter, justifyright, undo, redo, link, unlink, fullscreen') );
		$mce_buttons = implode($mce_buttons, ',');
		$mce_buttons_2 = $mce_buttons_3 = $mce_buttons_4 = '';
	} else {
		$mce_buttons = apply_filters('mce_buttons', array('bold', 'italic', 'strikethrough', '|', 'bullist', 'numlist', 'blockquote', '|', 'justifyleft', 'justifycenter', 'justifyright', '|', 'link', 'unlink', 'wp_more', '|', 'spellchecker', 'fullscreen', 'wp_adv' ));
		$mce_buttons = implode($mce_buttons, ',');

		$mce_buttons_2 = array( 'formatselect', 'underline', 'justifyfull', 'forecolor', '|', 'pastetext', 'pasteword', 'removeformat', '|', 'charmap', '|', 'outdent', 'indent', '|', 'undo', 'redo', 'wp_help' );
		$mce_buttons_2 = apply_filters('mce_buttons_2', $mce_buttons_2);
		$mce_buttons_2 = implode($mce_buttons_2, ',');

		$mce_buttons_3 = apply_filters('mce_buttons_3', array());
		$mce_buttons_3 = implode($mce_buttons_3, ',');

		$mce_buttons_4 = apply_filters('mce_buttons_4', array());
		$mce_buttons_4 = implode($mce_buttons_4, ',');
	}
	$no_captions = (bool) apply_filters( 'disable_captions', '' );

	// TinyMCE init settings
	$initArray = array (
		'mode' => 'specific_textareas',
		'editor_selector' => 'theEditor',
		'width' => '100%',
		'theme' => 'advanced',
		'skin' => 'wp_theme',
		'theme_advanced_buttons1' => $mce_buttons,
		'theme_advanced_buttons2' => $mce_buttons_2,
		'theme_advanced_buttons3' => $mce_buttons_3,
		'theme_advanced_buttons4' => $mce_buttons_4,
		'language' => $mce_locale,
		'spellchecker_languages' => $mce_spellchecker_languages,
		'theme_advanced_toolbar_location' => 'top',
		'theme_advanced_toolbar_align' => 'left',
		'theme_advanced_statusbar_location' => 'bottom',
		'theme_advanced_resizing' => true,
		'theme_advanced_resize_horizontal' => false,
		'dialog_type' => 'modal',
		'formats' => "{
			alignleft : [
				{selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles : {textAlign : 'left'}},
				{selector : 'img,table', classes : 'alignleft'}
			],
			aligncenter : [
				{selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles : {textAlign : 'center'}},
				{selector : 'img,table', classes : 'aligncenter'}
			],
			alignright : [
				{selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles : {textAlign : 'right'}},
				{selector : 'img,table', classes : 'alignright'}
			],
			strikethrough : {inline : 'del'}
		}",
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
		'paste_remove_styles' => true,
		'paste_remove_spans' => true,
		'paste_strip_class_attributes' => 'all',
		'paste_text_use_dialog' => true,
		'extended_valid_elements' => 'article[*],aside[*],audio[*],canvas[*],command[*],datalist[*],details[*],embed[*],figcaption[*],figure[*],footer[*],header[*],hgroup[*],keygen[*],mark[*],meter[*],nav[*],output[*],progress[*],section[*],source[*],summary,time[*],video[*],wbr',
		'wpeditimage_disable_captions' => $no_captions,
		'wp_fullscreen_content_css' => "$baseurl/plugins/wpfullscreen/css/content.css",
		'plugins' => implode( ',', $plugins ),
	);

	if ( ! empty( $editor_styles ) && is_array( $editor_styles ) ) {
		$mce_css = array();
		$style_uri = get_stylesheet_directory_uri();
		if ( ! is_child_theme() ) {
			foreach ( $editor_styles as $file )
				$mce_css[] = "$style_uri/$file";
		} else {
			$style_dir    = get_stylesheet_directory();
			$template_uri = get_template_directory_uri();
			$template_dir = get_template_directory();
			foreach ( $editor_styles as $file ) {
				if ( file_exists( "$template_dir/$file" ) )
					$mce_css[] = "$template_uri/$file";
				if ( file_exists( "$style_dir/$file" ) )
					$mce_css[] = "$style_uri/$file";
			}
		}
		$mce_css = implode( ',', $mce_css );
	} else {
		$mce_css = '';
	}

	$mce_css = trim( apply_filters( 'mce_css', $mce_css ), ' ,' );

	if ( ! empty($mce_css) )
		$initArray['content_css'] = $mce_css;

	if ( is_array($settings) )
		$initArray = array_merge($initArray, $settings);

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

	$compressed = $compress_scripts && $concatenate_scripts && isset($_SERVER['HTTP_ACCEPT_ENCODING'])
		&& false !== stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip');

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
	foreach ( $initArray as $k => $v ) {
		if ( is_bool($v) ) {
			$val = $v ? 'true' : 'false';
			$mce_options .= $k . ':' . $val . ', ';
			continue;
		} elseif ( !empty($v) && is_string($v) && ( '{' == $v{0} || '[' == $v{0} || preg_match('/^\(?function ?\(/', $v) ) ) {
			$mce_options .= $k . ':' . $v . ', ';
			continue;
		}

		$mce_options .= $k . ':"' . $v . '", ';
	}

	$mce_options = rtrim( trim($mce_options), '\n\r,' );

	wp_print_scripts('editor'); ?>

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
	if ( $compressed )
		echo "<script type='text/javascript' src='$baseurl/wp-tinymce.php?c=1&amp;$version'></script>\n";
	else
		echo "<script type='text/javascript' src='$baseurl/tiny_mce.js?$version'></script>\n";

	if ( 'en' != $language && isset($lang) )
		echo "<script type='text/javascript'>\n$lang\n</script>\n";
	else
		echo "<script type='text/javascript' src='$baseurl/langs/wp-langs-en.js?$version'></script>\n";
?>

<script type="text/javascript">
/* <![CDATA[ */
<?php
	if ( $ext_plugins )
		echo "$ext_plugins\n";

	if ( ! $compressed ) {
?>
(function(){var t=tinyMCEPreInit,sl=tinymce.ScriptLoader,ln=t.mceInit.language,th=t.mceInit.theme,pl=t.mceInit.plugins;sl.markDone(t.base+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'_dlg.js');tinymce.each(pl.split(','),function(n){if(n&&n.charAt(0)!='-'){sl.markDone(t.base+'/plugins/'+n+'/langs/'+ln+'.js');sl.markDone(t.base+'/plugins/'+n+'/langs/'+ln+'_dlg.js');}});})();
<?php } ?>
tinyMCE.init(tinyMCEPreInit.mceInit);
/* ]]> */
</script>
<?php

do_action('tiny_mce_preload_dialogs', $plugins);
}

// Load additional inline scripts based on active plugins.
function wp_preload_dialogs($plugins) {

	if ( in_array( 'wpdialogs', $plugins, true ) ) {
		wp_print_scripts('wpdialogs-popup');
		wp_print_styles('wp-jquery-ui-dialog');
	}

	if ( in_array( 'wplink', $plugins, true ) ) {
		require_once ABSPATH . 'wp-admin/includes/internal-linking.php';
		?><div style="display:none;"><?php wp_link_dialog(); ?></div><?php
		wp_print_scripts('wplink');
		wp_print_styles('wplink');
	}

	// Distraction Free Writing mode
	if ( in_array( 'wpfullscreen', $plugins, true ) ) {
		wp_fullscreen_html();
		wp_print_scripts('wp-fullscreen');
	}

	wp_print_scripts('word-count');
}

function wp_quicktags() {
	wp_preload_dialogs( array( 'wpdialogs', 'wplink', 'wp_fullscreen' ) );
}

function wp_fullscreen_html() {
	global $content_width, $post;

	$width = isset($content_width) && 800 > $content_width ? $content_width : 800;
	$width = $width + 10; // compensate for the padding
	$save = $post->post_status == 'publish' ? __('Update') : __('Save');
?> 
<div id="wp-fullscreen-body">
<div id="fullscreen-topbar" class="fade-600">
	<div id="wp-fullscreen-info">
		<span id="wp-fullscreen-saved"> </span>
		<span class="autosave-message">&nbsp;</span>
		<span id="wp-fullscreen-last-edit"> </span> 
	</div>

	<div id="wp-fullscreen-toolbar">
		<div id="wp-fullscreen-close"><a href="#" onclick="fullscreen.off();return false;"><?php _e('Back'); ?></a></div>
		<div id="wp-fullscreen-save"><input type="button" class="button-primary" value="<?php echo $save; ?>" onclick="fullscreen.save();" /></div>
		<div id="wp-fullscreen-buttons" style="width:<?php echo $width; ?>px;" class="wp_themeSkin">
			<div>
			<a title="<?php _e('Bold (Ctrl + B)'); ?>" aria-labelledby="wp_fs_bold_voice" onclick="fullscreen.b();return false;" class="mceButton mceButtonEnabled mce_bold" href="javascript:;" id="wp_fs_bold" role="button" tabindex="-1" aria-pressed="false">
			<span class="mceIcon mce_bold"></span>
			<span id="wp_fs_bold_voice" style="display: none;" class="mceVoiceLabel mceIconOnly"><?php _e('Bold (Ctrl + B)'); ?></span>
			</a>
			</div>

			<div>
			<a title="<?php _e('Italic (Ctrl + I)'); ?>" aria-labelledby="wp_fs_italic_voice" onclick="fullscreen.i();return false;" class="mceButton mceButtonEnabled mce_italic" href="javascript:;" id="wp_fs_italic" role="button" tabindex="-1" aria-pressed="false">
			<span class="mceIcon mce_italic"></span>
			<span id="wp_fs_italic_voice" style="display: none;" class="mceVoiceLabel mceIconOnly"><?php _e('Italic (Ctrl + I)'); ?></span>
			</a>
			</div>

			<div>
			<span tabindex="-1" aria-orientation="vertical" role="separator" class="mceSeparator"></span>
			</div>

			<div>
			<a title="<?php _e('Unordered list (Alt + Shift + U)'); ?>" aria-labelledby="wp_fs_bullist_voice" onclick="fullscreen.ul();return false;" onmousedown="return false;" class="mceButton mceButtonEnabled mce_bullist" href="javascript:;" id="wp_fs_bullist" role="button" tabindex="-1" aria-pressed="false">
			<span class="mceIcon mce_bullist"></span>
			<span id="wp_fs_bullist_voice" style="display: none;" class="mceVoiceLabel mceIconOnly"><?php _e('Unordered list (Alt + Shift + U)'); ?></span>
			</a>
			</div>

			<div>
			<a title="<?php _e('Ordered list (Alt + Shift + O)'); ?>" aria-labelledby="wp_fs_numlist_voice" onclick="fullscreen.ol();return false;" class="mceButton mceButtonEnabled mce_numlist" href="javascript:;" id="wp_fs_numlist" role="button" tabindex="-1" aria-pressed="false">
			<span class="mceIcon mce_numlist"></span>
			<span id="wp_fs_numlist_voice" style="display: none;" class="mceVoiceLabel mceIconOnly"><?php _e('Ordered list (Alt + Shift + O)'); ?></span>
			</a>
			</div>

			<div>
			<span tabindex="-1" aria-orientation="vertical" role="separator" class="mceSeparator"></span>
			</div>

			<div>
			<a title="<?php _e('Insert/edit image (Alt + Shift + M)'); ?>" aria-labelledby="wp_fs_image_voice" onclick="jQuery('#add_image').click();return false;" class="mceButton mceButtonEnabled mce_image" href="javascript:;" id="wp_fs_image" role="button" tabindex="-1">
			<span class="mceIcon mce_image"></span>
			<span id="wp_fs_image_voice" style="display: none;" class="mceVoiceLabel mceIconOnly"><?php _e('Insert/edit image (Alt + Shift + M)'); ?></span>
			</a>
			</div>

			<div>
			<span tabindex="-1" aria-orientation="vertical" role="separator" class="mceSeparator"></span>
			</div>

			<div>
			<a title="<?php _e('Insert/edit link (Alt + Shift + A)'); ?>" aria-labelledby="wp_fs_link_voice" onclick="fullscreen.link();return false;" class="mceButton mce_link mceButtonEnabled" href="javascript:;" id="wp_fs_link" role="button" tabindex="-1" aria-pressed="false">
			<span class="mceIcon mce_link"></span>
			<span id="wp_fs_link_voice" style="display: none;" class="mceVoiceLabel mceIconOnly"><?php _e('Insert/edit link (Alt + Shift + A)'); ?></span>
			</a>
			</div>

			<div>
			<a title="<?php _e('Unlink (Alt + Shift + S)'); ?>" aria-labelledby="wp_fs_unlink_voice" onclick="fullscreen.unlink();return false;" class="mceButton mce_unlink mceButtonEnabled" href="javascript:;" id="wp_fs_unlink" role="button" tabindex="-1" aria-pressed="false">
			<span class="mceIcon mce_unlink"></span>
			<span id="wp_fs_unlink_voice" style="display: none;" class="mceVoiceLabel mceIconOnly"><?php _e('Unlink (Alt + Shift + S)'); ?></span>
			</a>
			</div>

			<div id="wp-fullscreen-count"><?php _e('Word Count:'); ?> <span class="word-count">0</span></div>
		</div>
	</div>
</div>

<div id="wp-fullscreen-wrap" style="width:<?php echo $width; ?>px;">
	<label id="wp-fullscreen-title-prompt-text" for="wp-fullscreen-title"><?php echo apply_filters( 'enter_title_here', __( 'Enter title here' ), $post ); ?></label>
	<input type="text" id="wp-fullscreen-title" value="" autocomplete="off" />

	<div id="wp-fullscreen-container">
		<textarea id="wp_mce_fullscreen"></textarea>
	</div>
</div>
</div>

<div class="fullscreen-overlay" id="fullscreen-overlay"></div>
<div class="fullscreen-overlay fullscreen-fader fade-600" id="fullscreen-fader"></div>
<?php
}



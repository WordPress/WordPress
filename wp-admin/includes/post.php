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

	$ptype = get_post_type_object( $post_data['post_type'] );

	if ( $update && ! current_user_can( 'edit_post', $post_data['ID'] ) ) {
		if ( 'page' == $post_data['post_type'] )
			return new WP_Error( 'edit_others_pages', __( 'You are not allowed to edit pages as this user.' ) );
		else
			return new WP_Error( 'edit_others_posts', __( 'You are not allowed to edit posts as this user.' ) );
	} elseif ( ! $update && ! current_user_can( $ptype->cap->create_posts ) ) {
		if ( 'page' == $post_data['post_type'] )
			return new WP_Error( 'edit_others_pages', __( 'You are not allowed to create pages as this user.' ) );
		else
			return new WP_Error( 'edit_others_posts', __( 'You are not allowed to create posts as this user.' ) );
	}

	if ( isset( $post_data['content'] ) )
		$post_data['post_content'] = $post_data['content'];

	if ( isset( $post_data['excerpt'] ) )
		$post_data['post_excerpt'] = $post_data['excerpt'];

	if ( isset( $post_data['parent_id'] ) )
		$post_data['post_parent'] = (int) $post_data['parent_id'];

	if ( isset($post_data['trackback_url']) )
		$post_data['to_ping'] = $post_data['trackback_url'];

	$post_data['user_ID'] = get_current_user_id();

	if (!empty ( $post_data['post_author_override'] ) ) {
		$post_data['post_author'] = (int) $post_data['post_author_override'];
	} else {
		if (!empty ( $post_data['post_author'] ) ) {
			$post_data['post_author'] = (int) $post_data['post_author'];
		} else {
			$post_data['post_author'] = (int) $post_data['user_ID'];
		}
	}

	if ( isset( $post_data['user_ID'] ) && ( $post_data['post_author'] != $post_data['user_ID'] )
		 && ! current_user_can( $ptype->cap->edit_others_posts ) ) {
		if ( $update ) {
			if ( 'page' == $post_data['post_type'] )
				return new WP_Error( 'edit_others_pages', __( 'You are not allowed to edit pages as this user.' ) );
			else
				return new WP_Error( 'edit_others_posts', __( 'You are not allowed to edit posts as this user.' ) );
		} else {
			if ( 'page' == $post_data['post_type'] )
				return new WP_Error( 'edit_others_pages', __( 'You are not allowed to create pages as this user.' ) );
			else
				return new WP_Error( 'edit_others_posts', __( 'You are not allowed to create posts as this user.' ) );
		}
	}

	if ( ! empty( $post_data['post_status'] ) ) {
		$post_data['post_status'] = sanitize_key( $post_data['post_status'] );

		// No longer an auto-draft
		if ( 'auto-draft' === $post_data['post_status'] ) {
			$post_data['post_status'] = 'draft';
		}

		if ( ! get_post_status_object( $post_data['post_status'] ) ) {
			unset( $post_data['post_status'] );
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

	if ( isset( $post_data['post_status'] ) && 'private' == $post_data['post_status'] && ! current_user_can( $ptype->cap->publish_posts ) ) {
		$post_data['post_status'] = $previous_status ? $previous_status : 'pending';
	}

	$published_statuses = array( 'publish', 'future' );

	// Posts 'submitted for approval' present are submitted to $_POST the same as if they were being published.
	// Change status from 'publish' to 'pending' if user lacks permissions to publish or to resave published posts.
	if ( isset($post_data['post_status']) && (in_array( $post_data['post_status'], $published_statuses ) && !current_user_can( $ptype->cap->publish_posts )) )
		if ( ! in_array( $previous_status, $published_statuses ) || !current_user_can( 'edit_post', $post_id ) )
			$post_data['post_status'] = 'pending';

	if ( ! isset( $post_data['post_status'] ) ) {
		$post_data['post_status'] = 'auto-draft' === $previous_status ? 'draft' : $previous_status;
	}

	if ( isset( $post_data['post_password'] ) && ! current_user_can( $ptype->cap->publish_posts ) ) {
		unset( $post_data['post_password'] );
	}

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
		$valid_date = wp_checkdate( $mm, $jj, $aa, $post_data['post_date'] );
		if ( !$valid_date ) {
			return new WP_Error( 'invalid_date', __( 'Whoops, the provided date is invalid.' ) );
		}
		$post_data['post_date_gmt'] = get_gmt_from_date( $post_data['post_date'] );
	}

	return $post_data;
}

/**
 * Update an existing post with values provided in $_POST.
 *
 * @since 1.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $post_data Optional.
 * @return int Post ID.
 */
function edit_post( $post_data = null ) {
	global $wpdb;

	if ( empty($post_data) )
		$post_data = &$_POST;

	// Clear out any data in internal vars.
	unset( $post_data['filter'] );

	$post_ID = (int) $post_data['post_ID'];
	$post = get_post( $post_ID );
	$post_data['post_type'] = $post->post_type;
	$post_data['post_mime_type'] = $post->post_mime_type;

	if ( ! empty( $post_data['post_status'] ) ) {
		$post_data['post_status'] = sanitize_key( $post_data['post_status'] );

		if ( 'inherit' == $post_data['post_status'] ) {
			unset( $post_data['post_status'] );
		}
	}

	$ptype = get_post_type_object($post_data['post_type']);
	if ( !current_user_can( 'edit_post', $post_ID ) ) {
		if ( 'page' == $post_data['post_type'] )
			wp_die( __('You are not allowed to edit this page.' ));
		else
			wp_die( __('You are not allowed to edit this post.' ));
	}

	if ( post_type_supports( $ptype->name, 'revisions' ) ) {
		$revisions = wp_get_post_revisions( $post_ID, array( 'order' => 'ASC', 'posts_per_page' => 1 ) );
		$revision = current( $revisions );

		// Check if the revisions have been upgraded
		if ( $revisions && _wp_get_post_revision_version( $revision ) < 1 )
			_wp_upgrade_revisions_of_post( $post, wp_get_post_revisions( $post_ID ) );
	}

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

	$post_data = _wp_translate_postdata( true, $post_data );
	if ( is_wp_error($post_data) )
		wp_die( $post_data->get_error_message() );

	// Post Formats
	if ( isset( $post_data['post_format'] ) )
		set_post_format( $post_ID, $post_data['post_format'] );

	$format_meta_urls = array( 'url', 'link_url', 'quote_source_url' );
	foreach ( $format_meta_urls as $format_meta_url ) {
		$keyed = '_format_' . $format_meta_url;
		if ( isset( $post_data[ $keyed ] ) )
			update_post_meta( $post_ID, $keyed, wp_slash( esc_url_raw( wp_unslash( $post_data[ $keyed ] ) ) ) );
	}

	$format_keys = array( 'quote', 'quote_source_name', 'image', 'gallery', 'audio_embed', 'video_embed' );

	foreach ( $format_keys as $key ) {
		$keyed = '_format_' . $key;
		if ( isset( $post_data[ $keyed ] ) ) {
			if ( current_user_can( 'unfiltered_html' ) )
				update_post_meta( $post_ID, $keyed, $post_data[ $keyed ] );
			else
				update_post_meta( $post_ID, $keyed, wp_filter_post_kses( $post_data[ $keyed ] ) );
		}
	}

	if ( 'attachment' === $post_data['post_type'] && preg_match( '#^(audio|video)/#', $post_data['post_mime_type'] ) ) {
		$id3data = wp_get_attachment_metadata( $post_ID );
		if ( ! is_array( $id3data ) ) {
			$id3data = array();
		}

		foreach ( wp_get_attachment_id3_keys( $post, 'edit' ) as $key => $label ) {
			if ( isset( $post_data[ 'id3_' . $key ] ) ) {
				$id3data[ $key ] = sanitize_text_field( wp_unslash( $post_data[ 'id3_' . $key ] ) );
			}
		}
		wp_update_attachment_metadata( $post_ID, $id3data );
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

	// Attachment stuff
	if ( 'attachment' == $post_data['post_type'] ) {
		if ( isset( $post_data[ '_wp_attachment_image_alt' ] ) ) {
			$image_alt = wp_unslash( $post_data['_wp_attachment_image_alt'] );
			if ( $image_alt != get_post_meta( $post_ID, '_wp_attachment_image_alt', true ) ) {
				$image_alt = wp_strip_all_tags( $image_alt, true );
				// update_meta expects slashed.
				update_post_meta( $post_ID, '_wp_attachment_image_alt', wp_slash( $image_alt ) );
			}
		}

		$attachment_data = isset( $post_data['attachments'][ $post_ID ] ) ? $post_data['attachments'][ $post_ID ] : array();

		/** This filter is documented in wp-admin/includes/media.php */
		$post_data = apply_filters( 'attachment_fields_to_save', $post_data, $attachment_data );
	}

	// Convert taxonomy input to term IDs, to avoid ambiguity.
	if ( isset( $post_data['tax_input'] ) ) {
		foreach ( (array) $post_data['tax_input'] as $taxonomy => $terms ) {
			// Hierarchical taxonomy data is already sent as term IDs, so no conversion is necessary.
			if ( is_taxonomy_hierarchical( $taxonomy ) ) {
				continue;
			}

			/*
			 * Assume that a 'tax_input' string is a comma-separated list of term names.
			 * Some languages may use a character other than a comma as a delimiter, so we standardize on
			 * commas before parsing the list.
			 */
			if ( ! is_array( $terms ) ) {
				$comma = _x( ',', 'tag delimiter' );
				if ( ',' !== $comma ) {
					$terms = str_replace( $comma, ',', $terms );
				}
				$terms = explode( ',', trim( $terms, " \n\t\r\0\x0B," ) );
			}

			$clean_terms = array();
			foreach ( $terms as $term ) {
				// Empty terms are invalid input.
				if ( empty( $term ) ) {
					continue;
				}

				$_term = get_terms( $taxonomy, array(
					'name' => $term,
					'fields' => 'ids',
					'hide_empty' => false,
				) );

				if ( ! empty( $_term ) ) {
					$clean_terms[] = intval( $_term[0] );
				} else {
					// No existing term was found, so pass the string. A new term will be created.
					$clean_terms[] = $term;
				}
			}

			$post_data['tax_input'][ $taxonomy ] = $clean_terms;
		}
	}

	add_meta( $post_ID );

	update_post_meta( $post_ID, '_edit_last', get_current_user_id() );

	$success = wp_update_post( $post_data );
	// If the save failed, see if we can sanity check the main fields and try again
	if ( ! $success && is_callable( array( $wpdb, 'strip_invalid_text_for_column' ) ) ) {
		$fields = array( 'post_title', 'post_content', 'post_excerpt' );

		foreach ( $fields as $field ) {
			if ( isset( $post_data[ $field ] ) ) {
				$post_data[ $field ] = $wpdb->strip_invalid_text_for_column( $wpdb->posts, $field, $post_data[ $field ] );
			}
		}

		wp_update_post( $post_data );
	}

	// Now that we have an ID we can fix any attachment anchor hrefs
	_fix_attachment_links( $post_ID );

	wp_set_post_lock( $post_ID );

	if ( current_user_can( $ptype->cap->edit_others_posts ) && current_user_can( $ptype->cap->publish_posts ) ) {
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
 * @global wpdb $wpdb WordPress database abstraction object.
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

	if ( ! empty( $post_data['post_status'] ) ) {
		$post_data['post_status'] = sanitize_key( $post_data['post_status'] );

		if ( 'inherit' == $post_data['post_status'] ) {
			unset( $post_data['post_status'] );
		}
	}

	$post_IDs = array_map( 'intval', (array) $post_data['post'] );

	$reset = array(
		'post_author', 'post_status', 'post_password',
		'post_parent', 'page_template', 'comment_status',
		'ping_status', 'keep_private', 'tax_input',
		'post_category', 'sticky', 'post_format',
	);

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
			if ( is_taxonomy_hierarchical( $tax_name ) ) {
				$tax_input[ $tax_name ] = array_map( 'absint', $terms );
			} else {
				$comma = _x( ',', 'tag delimiter' );
				if ( ',' !== $comma )
					$terms = str_replace( $comma, ',', $terms );
				$tax_input[ $tax_name ] = explode( ',', trim( $terms, " \n\t\r\0\x0B," ) );
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
	$shared_post_data = $post_data;

	foreach ( $post_IDs as $post_ID ) {
		// Start with fresh post data with each iteration.
		$post_data = $shared_post_data;

		$post_type_object = get_post_type_object( get_post_type( $post_ID ) );

		if ( !isset( $post_type_object ) || ( isset($children) && in_array($post_ID, $children) ) || !current_user_can( 'edit_post', $post_ID ) ) {
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
			if ( isset( $tax_input[$tax_name]) && current_user_can( $taxonomy_obj->cap->assign_terms ) )
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

		$post_data['post_type'] = $post->post_type;
		$post_data['post_mime_type'] = $post->post_mime_type;
		$post_data['guid'] = $post->guid;

		foreach ( array( 'comment_status', 'ping_status', 'post_author' ) as $field ) {
			if ( ! isset( $post_data[ $field ] ) ) {
				$post_data[ $field ] = $post->$field;
			}
		}

		$post_data['ID'] = $post_ID;
		$post_data['post_ID'] = $post_ID;

		$post_data = _wp_translate_postdata( true, $post_data );
		if ( is_wp_error( $post_data ) ) {
			$skipped[] = $post_ID;
			continue;
		}

		$updated[] = wp_update_post( $post_data );

		if ( isset( $post_data['sticky'] ) && current_user_can( $ptype->cap->edit_others_posts ) ) {
			if ( 'sticky' == $post_data['sticky'] )
				stick_post( $post_ID );
			else
				unstick_post( $post_ID );
		}

		if ( isset( $post_data['post_format'] ) )
			set_post_format( $post_ID, $post_data['post_format'] );
	}

	return array( 'updated' => $updated, 'skipped' => $skipped, 'locked' => $locked );
}

/**
 * Default post information to use when populating the "Write Post" form.
 *
 * @since 2.0.0
 *
 * @param string $post_type    Optional. A post type string. Default 'post'.
 * @param bool   $create_in_db Optional. Whether to insert the post into database. Default false.
 * @return WP_Post Post object containing all the default post data as attributes
 */
function get_default_post_to_edit( $post_type = 'post', $create_in_db = false ) {
	$post_title = '';
	if ( !empty( $_REQUEST['post_title'] ) )
		$post_title = esc_html( wp_unslash( $_REQUEST['post_title'] ));

	$post_content = '';
	if ( !empty( $_REQUEST['content'] ) )
		$post_content = esc_html( wp_unslash( $_REQUEST['content'] ));

	$post_excerpt = '';
	if ( !empty( $_REQUEST['excerpt'] ) )
		$post_excerpt = esc_html( wp_unslash( $_REQUEST['excerpt'] ));

	if ( $create_in_db ) {
		$post_id = wp_insert_post( array( 'post_title' => __( 'Auto Draft' ), 'post_type' => $post_type, 'post_status' => 'auto-draft' ) );
		$post = get_post( $post_id );
		if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post->post_type, 'post-formats' ) && get_option( 'default_post_format' ) )
			set_post_format( $post, get_option( 'default_post_format' ) );
	} else {
		$post = new stdClass;
		$post->ID = 0;
		$post->post_author = '';
		$post->post_date = '';
		$post->post_date_gmt = '';
		$post->post_password = '';
		$post->post_name = '';
		$post->post_type = $post_type;
		$post->post_status = 'draft';
		$post->to_ping = '';
		$post->pinged = '';
		$post->comment_status = get_default_comment_status( $post_type );
		$post->ping_status = get_default_comment_status( $post_type, 'pingback' );
		$post->post_pingback = get_option( 'default_pingback_flag' );
		$post->post_category = get_option( 'default_category' );
		$post->page_template = 'default';
		$post->post_parent = 0;
		$post->menu_order = 0;
		$post = new WP_Post( $post );
	}

	/**
	 * Filter the default post content initially used in the "Write Post" form.
	 *
	 * @since 1.5.0
	 *
	 * @param string  $post_content Default post content.
	 * @param WP_Post $post         Post object.
	 */
	$post->post_content = apply_filters( 'default_content', $post_content, $post );

	/**
	 * Filter the default post title initially used in the "Write Post" form.
	 *
	 * @since 1.5.0
	 *
	 * @param string  $post_title Default post title.
	 * @param WP_Post $post       Post object.
	 */
	$post->post_title = apply_filters( 'default_title', $post_title, $post );

	/**
	 * Filter the default post excerpt initially used in the "Write Post" form.
	 *
	 * @since 1.5.0
	 *
	 * @param string  $post_excerpt Default post excerpt.
	 * @param WP_Post $post         Post object.
	 */
	$post->post_excerpt = apply_filters( 'default_excerpt', $post_excerpt, $post );

	return $post;
}

/**
 * Determine if a post exists based on title, content, and date
 *
 * @since 2.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $title Post title
 * @param string $content Optional post content
 * @param string $date Optional post date
 * @return int Post ID if post exists, 0 otherwise.
 */
function post_exists($title, $content = '', $date = '') {
	global $wpdb;

	$post_title = wp_unslash( sanitize_post_field( 'post_title', $title, 0, 'db' ) );
	$post_content = wp_unslash( sanitize_post_field( 'post_content', $content, 0, 'db' ) );
	$post_date = wp_unslash( sanitize_post_field( 'post_date', $date, 0, 'db' ) );

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
		$query .= ' AND post_content = %s';
		$args[] = $post_content;
	}

	if ( !empty ( $args ) )
		return (int) $wpdb->get_var( $wpdb->prepare($query, $args) );

	return 0;
}

/**
 * Creates a new post from the "Write Post" form using $_POST information.
 *
 * @since 2.1.0
 *
 * @global WP_User $current_user
 *
 * @return int|WP_Error
 */
function wp_write_post() {
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

	// Edit don't write if we have a post id.
	if ( isset( $_POST['post_ID'] ) )
		return edit_post();

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

	add_post_meta( $post_ID, '_edit_last', $GLOBALS['current_user']->ID );

	// Now that we have an ID we can fix any attachment anchor hrefs
	_fix_attachment_links( $post_ID );

	wp_set_post_lock( $post_ID );

	return $post_ID;
}

/**
 * Calls wp_write_post() and handles the errors.
 *
 * @since 2.0.0
 *
 * @return int|null
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
 * Add post meta data defined in $_POST superglobal for post with given ID.
 *
 * @since 1.2.0
 *
 * @param int $post_ID
 * @return int|bool
 */
function add_meta( $post_ID ) {
	$post_ID = (int) $post_ID;

	$metakeyselect = isset($_POST['metakeyselect']) ? wp_unslash( trim( $_POST['metakeyselect'] ) ) : '';
	$metakeyinput = isset($_POST['metakeyinput']) ? wp_unslash( trim( $_POST['metakeyinput'] ) ) : '';
	$metavalue = isset($_POST['metavalue']) ? $_POST['metavalue'] : '';
	if ( is_string( $metavalue ) )
		$metavalue = trim( $metavalue );

	if ( ('0' === $metavalue || ! empty ( $metavalue ) ) && ( ( ( '#NONE#' != $metakeyselect ) && !empty ( $metakeyselect) ) || !empty ( $metakeyinput ) ) ) {
		/*
		 * We have a key/value pair. If both the select and the input
		 * for the key have data, the input takes precedence.
		 */
 		if ( '#NONE#' != $metakeyselect )
			$metakey = $metakeyselect;

		if ( $metakeyinput )
			$metakey = $metakeyinput; // default

		if ( is_protected_meta( $metakey, 'post' ) || ! current_user_can( 'add_post_meta', $post_ID, $metakey ) )
			return false;

		$metakey = wp_slash( $metakey );

		return add_post_meta( $post_ID, $metakey, $metavalue );
	}

	return false;
} // add_meta

/**
 * Delete post meta data by meta ID.
 *
 * @since 1.2.0
 *
 * @param int $mid
 * @return bool
 */
function delete_meta( $mid ) {
	return delete_metadata_by_mid( 'post' , $mid );
}

/**
 * Get a list of previously defined keys.
 *
 * @since 1.2.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return mixed
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
 * Get post meta data by meta ID.
 *
 * @since 2.1.0
 *
 * @param int $mid
 * @return object|bool
 */
function get_post_meta_by_id( $mid ) {
	return get_metadata_by_mid( 'post', $mid );
}

/**
 * Get meta data for the given post ID.
 *
 * @since 1.2.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $postid
 * @return mixed
 */
function has_meta( $postid ) {
	global $wpdb;

	return $wpdb->get_results( $wpdb->prepare("SELECT meta_key, meta_value, meta_id, post_id
			FROM $wpdb->postmeta WHERE post_id = %d
			ORDER BY meta_key,meta_id", $postid), ARRAY_A );
}

/**
 * Update post meta data by meta ID.
 *
 * @since 1.2.0
 *
 * @param int    $meta_id
 * @param string $meta_key Expect Slashed
 * @param string $meta_value Expect Slashed
 * @return bool
 */
function update_meta( $meta_id, $meta_key, $meta_value ) {
	$meta_key = wp_unslash( $meta_key );
	$meta_value = wp_unslash( $meta_value );

	return update_metadata_by_mid( 'post', $meta_id, $meta_value, $meta_key );
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
 * @param int|object $post Post ID or post object.
 * @return void|int|WP_Error Void if nothing fixed. 0 or WP_Error on update failure. The post ID on update success.
 */
function _fix_attachment_links( $post ) {
	$post = get_post( $post, ARRAY_A );
	$content = $post['post_content'];

	// Don't run if no pretty permalinks or post is not published, scheduled, or privately published.
	if ( ! get_option( 'permalink_structure' ) || ! in_array( $post['post_status'], array( 'publish', 'future', 'private' ) ) )
		return;

	// Short if there aren't any links or no '?attachment_id=' strings (strpos cannot be zero)
	if ( !strpos($content, '?attachment_id=') || !preg_match_all( '/<a ([^>]+)>[\s\S]+?<\/a>/', $content, $link_matches ) )
		return;

	$site_url = get_bloginfo('url');
	$site_url = substr( $site_url, (int) strpos($site_url, '://') ); // remove the http(s)
	$replace = '';

	foreach ( $link_matches[1] as $key => $value ) {
		if ( !strpos($value, '?attachment_id=') || !strpos($value, 'wp-att-')
			|| !preg_match( '/href=(["\'])[^"\']*\?attachment_id=(\d+)[^"\']*\\1/', $value, $url_match )
			|| !preg_match( '/rel=["\'][^"\']*wp-att-(\d+)/', $value, $rel_match ) )
				continue;

		$quote = $url_match[1]; // the quote (single or double)
		$url_id = (int) $url_match[2];
		$rel_id = (int) $rel_match[1];

		if ( !$url_id || !$rel_id || $url_id != $rel_id || strpos($url_match[0], $site_url) === false )
			continue;

		$link = $link_matches[0][$key];
		$replace = str_replace( $url_match[0], 'href=' . $quote . get_attachment_link( $url_id ) . $quote, $link );

		$content = str_replace( $link, $replace, $content );
	}

	if ( $replace ) {
		$post['post_content'] = $content;
		// Escape data pulled from DB.
		$post = add_magic_quotes($post);

		return wp_update_post($post);
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

	if ( isset( $q['orderby'] ) ) {
		$orderby = $q['orderby'];
	} elseif ( isset( $q['post_status'] ) && in_array( $q['post_status'], array( 'pending', 'draft' ) ) ) {
		$orderby = 'modified';
	}

	if ( isset( $q['order'] ) ) {
		$order = $q['order'];
	} elseif ( isset( $q['post_status'] ) && 'pending' == $q['post_status'] ) {
		$order = 'ASC';
	}

	$per_page = "edit_{$post_type}_per_page";
	$posts_per_page = (int) get_user_option( $per_page );
	if ( empty( $posts_per_page ) || $posts_per_page < 1 )
		$posts_per_page = 20;

	/**
	 * Filter the number of items per page to show for a specific 'per_page' type.
	 *
	 * The dynamic portion of the hook name, `$post_type`, refers to the post type.
	 *
	 * Some examples of filter hooks generated here include: 'edit_attachment_per_page',
	 * 'edit_post_per_page', 'edit_page_per_page', etc.
	 *
	 * @since 3.0.0
	 *
	 * @param int $posts_per_page Number of posts to display per page for the given post
	 *                            type. Default 20.
	 */
	$posts_per_page = apply_filters( "edit_{$post_type}_per_page", $posts_per_page );

	/**
	 * Filter the number of posts displayed per page when specifically listing "posts".
	 *
	 * @since 2.8.0
	 *
	 * @param int    $posts_per_page Number of posts to be displayed. Default 20.
	 * @param string $post_type      The post type.
	 */
	$posts_per_page = apply_filters( 'edit_posts_per_page', $posts_per_page, $post_type );

	$query = compact('post_type', 'post_status', 'perm', 'order', 'orderby', 'posts_per_page');

	// Hierarchical types require special args.
	if ( is_post_type_hierarchical( $post_type ) && !isset($orderby) ) {
		$query['orderby'] = 'menu_order title';
		$query['order'] = 'asc';
		$query['posts_per_page'] = -1;
		$query['posts_per_archive_page'] = -1;
		$query['fields'] = 'id=>parent';
	}

	if ( ! empty( $q['show_sticky'] ) )
		$query['post__in'] = (array) get_option( 'sticky_posts' );

	wp( $query );

	return $avail_post_stati;
}

/**
 * Get all available post MIME types for a given post type.
 *
 * @since 2.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $type
 * @return mixed
 */
function get_available_post_mime_types($type = 'attachment') {
	global $wpdb;

	$types = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT post_mime_type FROM $wpdb->posts WHERE post_type = %s", $type));
	return $types;
}

/**
 * Get the query variables for the current attachments request.
 *
 * @since 4.2.0
 *
 * @param array|false $q Optional. Array of query variables to use to build the query or false
 *                       to use $_GET superglobal. Default false.
 * @return array The parsed query vars.
 */
function wp_edit_attachments_query_vars( $q = false ) {
	if ( false === $q ) {
		$q = $_GET;
	}
	$q['m']   = isset( $q['m'] ) ? (int) $q['m'] : 0;
	$q['cat'] = isset( $q['cat'] ) ? (int) $q['cat'] : 0;
	$q['post_type'] = 'attachment';
	$post_type = get_post_type_object( 'attachment' );
	$states = 'inherit';
	if ( current_user_can( $post_type->cap->read_private_posts ) ) {
		$states .= ',private';
	}

	$q['post_status'] = isset( $q['status'] ) && 'trash' == $q['status'] ? 'trash' : $states;
	$q['post_status'] = isset( $q['attachment-filter'] ) && 'trash' == $q['attachment-filter'] ? 'trash' : $states;

	$media_per_page = (int) get_user_option( 'upload_per_page' );
	if ( empty( $media_per_page ) || $media_per_page < 1 ) {
		$media_per_page = 20;
	}

	/**
	 * Filter the number of items to list per page when listing media items.
	 *
	 * @since 2.9.0
	 *
	 * @param int $media_per_page Number of media to list. Default 20.
	 */
	$q['posts_per_page'] = apply_filters( 'upload_per_page', $media_per_page );

	$post_mime_types = get_post_mime_types();
	if ( isset($q['post_mime_type']) && !array_intersect( (array) $q['post_mime_type'], array_keys($post_mime_types) ) ) {
		unset($q['post_mime_type']);
	}

	foreach ( array_keys( $post_mime_types ) as $type ) {
		if ( isset( $q['attachment-filter'] ) && "post_mime_type:$type" == $q['attachment-filter'] ) {
			$q['post_mime_type'] = $type;
			break;
		}
	}

	if ( isset( $q['detached'] ) || ( isset( $q['attachment-filter'] ) && 'detached' == $q['attachment-filter'] ) ) {
		$q['post_parent'] = 0;
	}

	return $q;
}

/**
 * Executes a query for attachments. An array of WP_Query arguments
 * can be passed in, which will override the arguments set by this function.
 *
 * @since 2.5.0
 *
 * @param array|false $q Array of query variables to use to build the query or false to use $_GET superglobal.
 * @return array
 */
function wp_edit_attachments_query( $q = false ) {
	wp( wp_edit_attachments_query_vars( $q ) );

	$post_mime_types = get_post_mime_types();
	$avail_post_mime_types = get_available_post_mime_types( 'attachment' );

	return array( $post_mime_types, $avail_post_mime_types );
}

/**
 * Returns the list of classes to be used by a metabox
 *
 * @since 2.5.0
 *
 * @param string $id
 * @param string $page
 * @return string
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

	/**
	 * Filter the postbox classes for a specific screen and screen ID combo.
	 *
	 * The dynamic portions of the hook name, `$page` and `$id`, refer to
	 * the screen and screen ID, respectively.
	 *
	 * @since 3.2.0
	 *
	 * @param array $classes An array of postbox classes.
	 */
	$classes = apply_filters( "postbox_classes_{$page}_{$id}", $classes );
	return implode( ' ', $classes );
}

/**
 * Get a sample permalink based off of the post name.
 *
 * @since 2.5.0
 *
 * @param int    $id    Post ID or post object.
 * @param string $title Optional. Title. Default null.
 * @param string $name  Optional. Name. Default null.
 * @return array Array with two entries of type string.
 */
function get_sample_permalink($id, $title = null, $name = null) {
	$post = get_post( $id );
	if ( ! $post )
		return array( '', '' );

	$ptype = get_post_type_object($post->post_type);

	$original_status = $post->post_status;
	$original_date = $post->post_date;
	$original_name = $post->post_name;

	// Hack: get_permalink() would return ugly permalink for drafts, so we will fake that our post is published.
	if ( in_array( $post->post_status, array( 'draft', 'pending', 'future' ) ) ) {
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
		if ( $uri ) {
			$uri = untrailingslashit($uri);
			$uri = strrev( stristr( strrev( $uri ), '/' ) );
			$uri = untrailingslashit($uri);
		}

		/** This filter is documented in wp-admin/edit-tag-form.php */
		$uri = apply_filters( 'editable_slug', $uri, $post );
		if ( !empty($uri) )
			$uri .= '/';
		$permalink = str_replace('%pagename%', "{$uri}%pagename%", $permalink);
	}

	/** This filter is documented in wp-admin/edit-tag-form.php */
	$permalink = array( $permalink, apply_filters( 'editable_slug', $post->post_name, $post ) );
	$post->post_status = $original_status;
	$post->post_date = $original_date;
	$post->post_name = $original_name;
	unset($post->filter);

	/**
	 * Filter the sample permalink.
	 *
	 * @since 4.4.0
	 *
	 * @param string  $permalink Sample permalink.
	 * @param int     $post_id   Post ID.
	 * @param string  $title     Post title.
	 * @param string  $name      Post name (slug).
	 * @param WP_Post $post      Post object.
	 */
	return apply_filters( 'get_sample_permalink', $permalink, $post->ID, $title, $name, $post );
}

/**
 * Returns the HTML of the sample permalink slug editor.
 *
 * @since 2.5.0
 *
 * @param int    $id        Post ID or post object.
 * @param string $new_title Optional. New title. Default null.
 * @param string $new_slug  Optional. New slug. Default null.
 * @return string The HTML of the sample permalink slug editor.
 */
function get_sample_permalink_html( $id, $new_title = null, $new_slug = null ) {
	$post = get_post( $id );
	if ( ! $post )
		return '';

	list($permalink, $post_name) = get_sample_permalink($post->ID, $new_title, $new_slug);

	$view_link = false;
	$preview_target = '';

	if ( current_user_can( 'read_post', $post->ID ) ) {
		if ( 'draft' === $post->post_status ) {
			$draft_link = set_url_scheme( get_permalink( $post->ID ) );
			$view_link = get_preview_post_link( $post, array(), $draft_link );
			$preview_target = " target='wp-preview-{$post->ID}'";
		} else {
			if ( 'publish' === $post->post_status || 'attachment' === $post->post_type ) {
				$view_link = get_permalink( $post );
			} else {
				// Allow non-published (private, future) to be viewed at a pretty permalink.
				$view_link = str_replace( array( '%pagename%', '%postname%' ), $post->post_name, urldecode( $permalink ) );
			}
		}
	}

	// Permalinks without a post/page name placeholder don't have anything to edit
	if ( false === strpos( $permalink, '%postname%' ) && false === strpos( $permalink, '%pagename%' ) ) {
		$return = '<strong>' . __( 'Permalink:' ) . "</strong>\n";

		if ( false !== $view_link ) {
			$return .= '<a id="sample-permalink" href="' . esc_url( $view_link ) . '"' . $preview_target . '>' . $view_link . "</a>\n";
		} else {
			$return .= '<span id="sample-permalink">' . $permalink . "</span>\n";
		}

		// Encourage a pretty permalink setting
		if ( '' == get_option( 'permalink_structure' ) && current_user_can( 'manage_options' ) && !( 'page' == get_option('show_on_front') && $id == get_option('page_on_front') ) ) {
			$return .= '<span id="change-permalinks"><a href="options-permalink.php" class="button button-small" target="_blank">' . __('Change Permalinks') . "</a></span>\n";
		}
	} else {
		if ( function_exists( 'mb_strlen' ) ) {
			if ( mb_strlen( $post_name ) > 34 ) {
				$post_name_abridged = mb_substr( $post_name, 0, 16 ) . '&hellip;' . mb_substr( $post_name, -16 );
			} else {
				$post_name_abridged = $post_name;
			}
		} else {
			if ( strlen( $post_name ) > 34 ) {
				$post_name_abridged = substr( $post_name, 0, 16 ) . '&hellip;' . substr( $post_name, -16 );
			} else {
				$post_name_abridged = $post_name;
			}
		}

		$post_name_html = '<span id="editable-post-name">' . $post_name_abridged . '</span>';
		$display_link = str_replace( array( '%pagename%', '%postname%' ), $post_name_html, urldecode( $permalink ) );

		$return = '<strong>' . __( 'Permalink:' ) . "</strong>\n";
		$return .= '<span id="sample-permalink"><a href="' . esc_url( $view_link ) . '"' . $preview_target . '>' . $display_link . "</a></span>\n";
		$return .= '&lrm;'; // Fix bi-directional text display defect in RTL languages.
		$return .= '<span id="edit-slug-buttons"><button type="button" class="edit-slug button button-small hide-if-no-js" aria-label="' . __( 'Edit permalink' ) . '">' . __( 'Edit' ) . "</button></span>\n";
		$return .= '<span id="editable-post-name-full">' . $post_name . "</span>\n";
	}

	/**
	 * Filter the sample permalink HTML markup.
	 *
	 * @since 2.9.0
	 * @since 4.4.0 Added `$post` parameter.
	 *
	 * @param string  $return    Sample permalink HTML markup.
	 * @param int     $post_id   Post ID.
	 * @param string  $new_title New sample permalink title.
	 * @param string  $new_slug  New sample permalink slug.
	 * @param WP_Post $post      Post object.
	 */
	$return = apply_filters( 'get_sample_permalink_html', $return, $post->ID, $new_title, $new_slug, $post );

	return $return;
}

/**
 * Output HTML for the post thumbnail meta-box.
 *
 * @since 2.9.0
 *
 * @global int   $content_width
 * @global array $_wp_additional_image_sizes
 *
 * @param int $thumbnail_id ID of the attachment used for thumbnail
 * @param mixed $post The post ID or object associated with the thumbnail, defaults to global $post.
 * @return string html
 */
function _wp_post_thumbnail_html( $thumbnail_id = null, $post = null ) {
	global $content_width, $_wp_additional_image_sizes;

	$post               = get_post( $post );
	$post_type_object   = get_post_type_object( $post->post_type );
	$set_thumbnail_link = '<p class="hide-if-no-js"><a href="%s" id="set-post-thumbnail"%s class="thickbox">%s</a></p>';
	$upload_iframe_src  = get_upload_iframe_src( 'image', $post->ID );

	$content = sprintf( $set_thumbnail_link,
		esc_url( $upload_iframe_src ),
		'', // Empty when there's no featured image set, `aria-describedby` attribute otherwise.
		esc_html( $post_type_object->labels->set_featured_image )
	);

	if ( $thumbnail_id && get_post( $thumbnail_id ) ) {
		$size = isset( $_wp_additional_image_sizes['post-thumbnail'] ) ? 'post-thumbnail' : array( 266, 266 );

		/**
		 * Filter the size used to display the post thumbnail image in the 'Featured Image' meta box.
		 *
		 * Note: When a theme adds 'post-thumbnail' support, a special 'post-thumbnail'
		 * image size is registered, which differs from the 'thumbnail' image size
		 * managed via the Settings > Media screen. See the `$size` parameter description
		 * for more information on default values.
		 *
		 * @since 4.4.0
		 *
		 * @param string|array $size         Post thumbnail image size to display in the meta box. Accepts any valid
		 *                                   image size, or an array of width and height values in pixels (in that order).
		 *                                   If the 'post-thumbnail' size is set, default is 'post-thumbnail'. Otherwise,
		 *                                   default is an array with 266 as both the height and width values.
		 * @param int          $thumbnail_id Post thumbnail attachment ID.
		 * @param WP_Post      $post         The post object associated with the thumbnail.
		 */
		$size = apply_filters( 'admin_post_thumbnail_size', $size, $thumbnail_id, $post );

		$thumbnail_html = wp_get_attachment_image( $thumbnail_id, $size );

		if ( !empty( $thumbnail_html ) ) {
			$ajax_nonce = wp_create_nonce( 'set_post_thumbnail-' . $post->ID );
			$content = sprintf( $set_thumbnail_link,
				esc_url( $upload_iframe_src ),
				' aria-describedby="set-post-thumbnail-desc"',
				$thumbnail_html
			);
			$content .= '<p class="hide-if-no-js howto" id="set-post-thumbnail-desc">' . __( 'Click the image to edit or update' ) . '</p>';
			$content .= '<p class="hide-if-no-js"><a href="#" id="remove-post-thumbnail" onclick="WPRemoveThumbnail(\'' . $ajax_nonce . '\');return false;">' . esc_html( $post_type_object->labels->remove_featured_image ) . '</a></p>';
		}
	}

	/**
	 * Filter the admin post thumbnail HTML markup to return.
	 *
	 * @since 2.9.0
	 *
	 * @param string $content Admin post thumbnail HTML markup.
	 * @param int    $post_id Post ID.
	 */
	return apply_filters( 'admin_post_thumbnail_html', $content, $post->ID );
}

/**
 * Check to see if the post is currently being edited by another user.
 *
 * @since 2.5.0
 *
 * @param int $post_id ID of the post to check for editing
 * @return integer False: not locked or locked by current user. Int: user ID of user with lock.
 */
function wp_check_post_lock( $post_id ) {
	if ( !$post = get_post( $post_id ) )
		return false;

	if ( !$lock = get_post_meta( $post->ID, '_edit_lock', true ) )
		return false;

	$lock = explode( ':', $lock );
	$time = $lock[0];
	$user = isset( $lock[1] ) ? $lock[1] : get_post_meta( $post->ID, '_edit_last', true );

	/** This filter is documented in wp-admin/includes/ajax-actions.php */
	$time_window = apply_filters( 'wp_check_post_lock_window', 150 );

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
 * @return bool|array Returns false if the post doesn't exist of there is no current user, or
 * 	an array of the lock time and the user ID.
 */
function wp_set_post_lock( $post_id ) {
	if ( !$post = get_post( $post_id ) )
		return false;
	if ( 0 == ($user_id = get_current_user_id()) )
		return false;

	$now = time();
	$lock = "$now:$user_id";

	update_post_meta( $post->ID, '_edit_lock', $lock );
	return array( $now, $user_id );
}

/**
 * Outputs the HTML for the notice to say that someone else is editing or has taken over editing of this post.
 *
 * @since 2.8.5
 * @return none
 */
function _admin_notice_post_locked() {
	if ( ! $post = get_post() )
		return;

	$user = null;
	if (  $user_id = wp_check_post_lock( $post->ID ) )
		$user = get_userdata( $user_id );

	if ( $user ) {

		/**
		 * Filter whether to show the post locked dialog.
		 *
		 * Returning a falsey value to the filter will short-circuit displaying the dialog.
		 *
		 * @since 3.6.0
		 *
		 * @param bool         $display Whether to display the dialog. Default true.
		 * @param WP_User|bool $user    WP_User object on success, false otherwise.
		 */
		if ( ! apply_filters( 'show_post_locked_dialog', true, $post, $user ) )
			return;

		$locked = true;
	} else {
		$locked = false;
	}

	if ( $locked && ( $sendback = wp_get_referer() ) &&
		false === strpos( $sendback, 'post.php' ) && false === strpos( $sendback, 'post-new.php' ) ) {

		$sendback_text = __('Go back');
	} else {
		$sendback = admin_url( 'edit.php' );

		if ( 'post' != $post->post_type )
			$sendback = add_query_arg( 'post_type', $post->post_type, $sendback );

		$sendback_text = get_post_type_object( $post->post_type )->labels->all_items;
	}

	$hidden = $locked ? '' : ' hidden';

	?>
	<div id="post-lock-dialog" class="notification-dialog-wrap<?php echo $hidden; ?>">
	<div class="notification-dialog-background"></div>
	<div class="notification-dialog">
	<?php

	if ( $locked ) {
		$query_args = array();
		if ( get_post_type_object( $post->post_type )->public ) {
			if ( 'publish' == $post->post_status || $user->ID != $post->post_author ) {
				// Latest content is in autosave
				$nonce = wp_create_nonce( 'post_preview_' . $post->ID );
				$query_args['preview_id'] = $post->ID;
				$query_args['preview_nonce'] = $nonce;
			}
		}

		$preview_link = get_preview_post_link( $post->ID, $query_args );

		/**
		 * Filter whether to allow the post lock to be overridden.
		 *
		 * Returning a falsey value to the filter will disable the ability
		 * to override the post lock.
		 *
		 * @since 3.6.0
		 *
		 * @param bool    $override Whether to allow overriding post locks. Default true.
		 * @param WP_Post $post     Post object.
		 * @param WP_User $user     User object.
		 */
		$override = apply_filters( 'override_post_lock', true, $post, $user );
		$tab_last = $override ? '' : ' wp-tab-last';

		?>
		<div class="post-locked-message">
		<div class="post-locked-avatar"><?php echo get_avatar( $user->ID, 64 ); ?></div>
		<p class="currently-editing wp-tab-first" tabindex="0">
		<?php
			_e( 'This content is currently locked.' );
			if ( $override )
				printf( ' ' . __( 'If you take over, %s will be blocked from continuing to edit.' ), esc_html( $user->display_name ) );
		?>
		</p>
		<?php
		/**
		 * Fires inside the post locked dialog before the buttons are displayed.
		 *
		 * @since 3.6.0
		 *
		 * @param WP_Post $post Post object.
		 */
		do_action( 'post_locked_dialog', $post );
		?>
		<p>
		<a class="button" href="<?php echo esc_url( $sendback ); ?>"><?php echo $sendback_text; ?></a>
		<?php if ( $preview_link ) { ?>
		<a class="button<?php echo $tab_last; ?>" href="<?php echo esc_url( $preview_link ); ?>"><?php _e('Preview'); ?></a>
		<?php
		}

		// Allow plugins to prevent some users overriding the post lock
		if ( $override ) {
			?>
			<a class="button button-primary wp-tab-last" href="<?php echo esc_url( add_query_arg( 'get-post-lock', '1', wp_nonce_url( get_edit_post_link( $post->ID, 'url' ), 'lock-post_' . $post->ID ) ) ); ?>"><?php _e('Take over'); ?></a>
			<?php
		}

		?>
		</p>
		</div>
		<?php
	} else {
		?>
		<div class="post-taken-over">
			<div class="post-locked-avatar"></div>
			<p class="wp-tab-first" tabindex="0">
			<span class="currently-editing"></span><br />
			<span class="locked-saving hidden"><img src="<?php echo esc_url( admin_url( 'images/spinner-2x.gif' ) ); ?>" width="16" height="16" alt="" /> <?php _e( 'Saving revision&hellip;' ); ?></span>
			<span class="locked-saved hidden"><?php _e('Your latest changes were saved as a revision.'); ?></span>
			</p>
			<?php
			/**
			 * Fires inside the dialog displayed when a user has lost the post lock.
			 *
			 * @since 3.6.0
			 *
			 * @param WP_Post $post Post object.
			 */
			do_action( 'post_lock_lost_dialog', $post );
			?>
			<p><a class="button button-primary wp-tab-last" href="<?php echo esc_url( $sendback ); ?>"><?php echo $sendback_text; ?></a></p>
		</div>
		<?php
	}

	?>
	</div>
	</div>
	<?php
}

/**
 * Creates autosave data for the specified post from $_POST data.
 *
 * @package WordPress
 * @subpackage Post_Revisions
 * @since 2.6.0
 *
 * @param mixed $post_data Associative array containing the post data or int post ID.
 * @return mixed The autosave revision ID. WP_Error or 0 on error.
 */
function wp_create_post_autosave( $post_data ) {
	if ( is_numeric( $post_data ) ) {
		$post_id = $post_data;
		$post_data = $_POST;
	} else {
		$post_id = (int) $post_data['post_ID'];
	}

	$post_data = _wp_translate_postdata( true, $post_data );
	if ( is_wp_error( $post_data ) )
		return $post_data;

	$post_author = get_current_user_id();

	// Store one autosave per author. If there is already an autosave, overwrite it.
	if ( $old_autosave = wp_get_post_autosave( $post_id, $post_author ) ) {
		$new_autosave = _wp_post_revision_data( $post_data, true );
		$new_autosave['ID'] = $old_autosave->ID;
		$new_autosave['post_author'] = $post_author;

		// If the new autosave has the same content as the post, delete the autosave.
		$post = get_post( $post_id );
		$autosave_is_different = false;
		foreach ( array_intersect( array_keys( $new_autosave ), array_keys( _wp_post_revision_fields( $post ) ) ) as $field ) {
			if ( normalize_whitespace( $new_autosave[ $field ] ) != normalize_whitespace( $post->$field ) ) {
				$autosave_is_different = true;
				break;
			}
		}

		if ( ! $autosave_is_different ) {
			wp_delete_post_revision( $old_autosave->ID );
			return 0;
		}

		/**
		 * Fires before an autosave is stored.
		 *
		 * @since 4.1.0
		 *
		 * @param array $new_autosave Post array - the autosave that is about to be saved.
		 */
		do_action( 'wp_creating_autosave', $new_autosave );

		return wp_update_post( $new_autosave );
	}

	// _wp_put_post_revision() expects unescaped.
	$post_data = wp_unslash( $post_data );

	// Otherwise create the new autosave as a special post revision
	return _wp_put_post_revision( $post_data, true );
}

/**
 * Save draft or manually autosave for showing preview.
 *
 * @package WordPress
 * @since 2.7.0
 *
 * @return str URL to redirect to show the preview
 */
function post_preview() {

	$post_ID = (int) $_POST['post_ID'];
	$_POST['ID'] = $post_ID;

	if ( ! $post = get_post( $post_ID ) ) {
		wp_die( __( 'You are not allowed to edit this post.' ) );
	}

	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		wp_die( __( 'You are not allowed to edit this post.' ) );
	}

	$is_autosave = false;

	if ( ! wp_check_post_lock( $post->ID ) && get_current_user_id() == $post->post_author && ( 'draft' == $post->post_status || 'auto-draft' == $post->post_status ) ) {
		$saved_post_id = edit_post();
	} else {
		$is_autosave = true;

		if ( isset( $_POST['post_status'] ) && 'auto-draft' == $_POST['post_status'] )
			$_POST['post_status'] = 'draft';

		$saved_post_id = wp_create_post_autosave( $post->ID );
	}

	if ( is_wp_error( $saved_post_id ) )
		wp_die( $saved_post_id->get_error_message() );

	$query_args = array();

	if ( $is_autosave && $saved_post_id ) {
		$query_args['preview_id'] = $post->ID;
		$query_args['preview_nonce'] = wp_create_nonce( 'post_preview_' . $post->ID );

		if ( isset( $_POST['post_format'] ) )
			$query_args['post_format'] = empty( $_POST['post_format'] ) ? 'standard' : sanitize_key( $_POST['post_format'] );
	}

	return get_preview_post_link( $post, $query_args );
}

/**
 * Save a post submitted with XHR
 *
 * Intended for use with heartbeat and autosave.js
 *
 * @since 3.9.0
 *
 * @param array $post_data Associative array of the submitted post data.
 * @return mixed The value 0 or WP_Error on failure. The saved post ID on success.
 *               The ID can be the draft post_id or the autosave revision post_id.
 */
function wp_autosave( $post_data ) {
	// Back-compat
	if ( ! defined( 'DOING_AUTOSAVE' ) )
		define( 'DOING_AUTOSAVE', true );

	$post_id = (int) $post_data['post_id'];
	$post_data['ID'] = $post_data['post_ID'] = $post_id;

	if ( false === wp_verify_nonce( $post_data['_wpnonce'], 'update-post_' . $post_id ) ) {
		return new WP_Error( 'invalid_nonce', __( 'Error while saving.' ) );
	}

	$post = get_post( $post_id );

	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return new WP_Error( 'edit_posts', __( 'You are not allowed to edit this item.' ) );
	}

	if ( 'auto-draft' == $post->post_status )
		$post_data['post_status'] = 'draft';

	if ( $post_data['post_type'] != 'page' && ! empty( $post_data['catslist'] ) )
		$post_data['post_category'] = explode( ',', $post_data['catslist'] );

	if ( ! wp_check_post_lock( $post->ID ) && get_current_user_id() == $post->post_author && ( 'auto-draft' == $post->post_status || 'draft' == $post->post_status ) ) {
		// Drafts and auto-drafts are just overwritten by autosave for the same user if the post is not locked
		return edit_post( wp_slash( $post_data ) );
	} else {
		// Non drafts or other users drafts are not overwritten. The autosave is stored in a special post revision for each user.
		return wp_create_post_autosave( wp_slash( $post_data ) );
	}
}

/**
 * Redirect to previous page.
 *
 * @param int $post_id Optional. Post ID.
 */
function redirect_post($post_id = '') {
	if ( isset($_POST['save']) || isset($_POST['publish']) ) {
		$status = get_post_status( $post_id );

		if ( isset( $_POST['publish'] ) ) {
			switch ( $status ) {
				case 'pending':
					$message = 8;
					break;
				case 'future':
					$message = 9;
					break;
				default:
					$message = 6;
			}
		} else {
			$message = 'draft' == $status ? 10 : 1;
		}

		$location = add_query_arg( 'message', $message, get_edit_post_link( $post_id, 'url' ) );
	} elseif ( isset($_POST['addmeta']) && $_POST['addmeta'] ) {
		$location = add_query_arg( 'message', 2, wp_get_referer() );
		$location = explode('#', $location);
		$location = $location[0] . '#postcustom';
	} elseif ( isset($_POST['deletemeta']) && $_POST['deletemeta'] ) {
		$location = add_query_arg( 'message', 3, wp_get_referer() );
		$location = explode('#', $location);
		$location = $location[0] . '#postcustom';
	} else {
		$location = add_query_arg( 'message', 4, get_edit_post_link( $post_id, 'url' ) );
	}

	/**
	 * Filter the post redirect destination URL.
	 *
	 * @since 2.9.0
	 *
	 * @param string $location The destination URL.
	 * @param int    $post_id  The post ID.
	 */
	wp_redirect( apply_filters( 'redirect_post_location', $location, $post_id ) );
	exit;
}

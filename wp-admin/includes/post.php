<?php
/**
 * WordPress Post Administration API.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Renames `$_POST` data from form names to DB post columns.
 *
 * Manipulates `$_POST` directly.
 *
 * @since 2.6.0
 *
 * @param bool       $update    Whether the post already exists.
 * @param array|null $post_data Optional. The array of post data to process.
 *                              Defaults to the `$_POST` superglobal.
 * @return array|WP_Error Array of post data on success, WP_Error on failure.
 */
function _wp_translate_postdata( $update = false, $post_data = null ) {

	if ( empty( $post_data ) ) {
		$post_data = &$_POST;
	}

	if ( $update ) {
		$post_data['ID'] = (int) $post_data['post_ID'];
	}

	$ptype = get_post_type_object( $post_data['post_type'] );

	if ( $update && ! current_user_can( 'edit_post', $post_data['ID'] ) ) {
		if ( 'page' === $post_data['post_type'] ) {
			return new WP_Error( 'edit_others_pages', __( 'Sorry, you are not allowed to edit pages as this user.' ) );
		} else {
			return new WP_Error( 'edit_others_posts', __( 'Sorry, you are not allowed to edit posts as this user.' ) );
		}
	} elseif ( ! $update && ! current_user_can( $ptype->cap->create_posts ) ) {
		if ( 'page' === $post_data['post_type'] ) {
			return new WP_Error( 'edit_others_pages', __( 'Sorry, you are not allowed to create pages as this user.' ) );
		} else {
			return new WP_Error( 'edit_others_posts', __( 'Sorry, you are not allowed to create posts as this user.' ) );
		}
	}

	if ( isset( $post_data['content'] ) ) {
		$post_data['post_content'] = $post_data['content'];
	}

	if ( isset( $post_data['excerpt'] ) ) {
		$post_data['post_excerpt'] = $post_data['excerpt'];
	}

	if ( isset( $post_data['parent_id'] ) ) {
		$post_data['post_parent'] = (int) $post_data['parent_id'];
	}

	if ( isset( $post_data['trackback_url'] ) ) {
		$post_data['to_ping'] = $post_data['trackback_url'];
	}

	$post_data['user_ID'] = get_current_user_id();

	if ( ! empty( $post_data['post_author_override'] ) ) {
		$post_data['post_author'] = (int) $post_data['post_author_override'];
	} else {
		if ( ! empty( $post_data['post_author'] ) ) {
			$post_data['post_author'] = (int) $post_data['post_author'];
		} else {
			$post_data['post_author'] = (int) $post_data['user_ID'];
		}
	}

	if ( isset( $post_data['user_ID'] ) && ( $post_data['post_author'] != $post_data['user_ID'] )
		&& ! current_user_can( $ptype->cap->edit_others_posts ) ) {

		if ( $update ) {
			if ( 'page' === $post_data['post_type'] ) {
				return new WP_Error( 'edit_others_pages', __( 'Sorry, you are not allowed to edit pages as this user.' ) );
			} else {
				return new WP_Error( 'edit_others_posts', __( 'Sorry, you are not allowed to edit posts as this user.' ) );
			}
		} else {
			if ( 'page' === $post_data['post_type'] ) {
				return new WP_Error( 'edit_others_pages', __( 'Sorry, you are not allowed to create pages as this user.' ) );
			} else {
				return new WP_Error( 'edit_others_posts', __( 'Sorry, you are not allowed to create posts as this user.' ) );
			}
		}
	}

	if ( ! empty( $post_data['post_status'] ) ) {
		$post_data['post_status'] = sanitize_key( $post_data['post_status'] );

		// No longer an auto-draft.
		if ( 'auto-draft' === $post_data['post_status'] ) {
			$post_data['post_status'] = 'draft';
		}

		if ( ! get_post_status_object( $post_data['post_status'] ) ) {
			unset( $post_data['post_status'] );
		}
	}

	// What to do based on which button they pressed.
	if ( isset( $post_data['saveasdraft'] ) && '' !== $post_data['saveasdraft'] ) {
		$post_data['post_status'] = 'draft';
	}
	if ( isset( $post_data['saveasprivate'] ) && '' !== $post_data['saveasprivate'] ) {
		$post_data['post_status'] = 'private';
	}
	if ( isset( $post_data['publish'] ) && ( '' !== $post_data['publish'] )
		&& ( ! isset( $post_data['post_status'] ) || 'private' !== $post_data['post_status'] )
	) {
		$post_data['post_status'] = 'publish';
	}
	if ( isset( $post_data['advanced'] ) && '' !== $post_data['advanced'] ) {
		$post_data['post_status'] = 'draft';
	}
	if ( isset( $post_data['pending'] ) && '' !== $post_data['pending'] ) {
		$post_data['post_status'] = 'pending';
	}

	if ( isset( $post_data['ID'] ) ) {
		$post_id = $post_data['ID'];
	} else {
		$post_id = false;
	}
	$previous_status = $post_id ? get_post_field( 'post_status', $post_id ) : false;

	if ( isset( $post_data['post_status'] ) && 'private' === $post_data['post_status'] && ! current_user_can( $ptype->cap->publish_posts ) ) {
		$post_data['post_status'] = $previous_status ? $previous_status : 'pending';
	}

	$published_statuses = array( 'publish', 'future' );

	// Posts 'submitted for approval' are submitted to $_POST the same as if they were being published.
	// Change status from 'publish' to 'pending' if user lacks permissions to publish or to resave published posts.
	if ( isset( $post_data['post_status'] )
		&& ( in_array( $post_data['post_status'], $published_statuses, true )
		&& ! current_user_can( $ptype->cap->publish_posts ) )
	) {
		if ( ! in_array( $previous_status, $published_statuses, true ) || ! current_user_can( 'edit_post', $post_id ) ) {
			$post_data['post_status'] = 'pending';
		}
	}

	if ( ! isset( $post_data['post_status'] ) ) {
		$post_data['post_status'] = 'auto-draft' === $previous_status ? 'draft' : $previous_status;
	}

	if ( isset( $post_data['post_password'] ) && ! current_user_can( $ptype->cap->publish_posts ) ) {
		unset( $post_data['post_password'] );
	}

	if ( ! isset( $post_data['comment_status'] ) ) {
		$post_data['comment_status'] = 'closed';
	}

	if ( ! isset( $post_data['ping_status'] ) ) {
		$post_data['ping_status'] = 'closed';
	}

	foreach ( array( 'aa', 'mm', 'jj', 'hh', 'mn' ) as $timeunit ) {
		if ( ! empty( $post_data[ 'hidden_' . $timeunit ] ) && $post_data[ 'hidden_' . $timeunit ] != $post_data[ $timeunit ] ) {
			$post_data['edit_date'] = '1';
			break;
		}
	}

	if ( ! empty( $post_data['edit_date'] ) ) {
		$aa = $post_data['aa'];
		$mm = $post_data['mm'];
		$jj = $post_data['jj'];
		$hh = $post_data['hh'];
		$mn = $post_data['mn'];
		$ss = $post_data['ss'];
		$aa = ( $aa <= 0 ) ? gmdate( 'Y' ) : $aa;
		$mm = ( $mm <= 0 ) ? gmdate( 'n' ) : $mm;
		$jj = ( $jj > 31 ) ? 31 : $jj;
		$jj = ( $jj <= 0 ) ? gmdate( 'j' ) : $jj;
		$hh = ( $hh > 23 ) ? $hh - 24 : $hh;
		$mn = ( $mn > 59 ) ? $mn - 60 : $mn;
		$ss = ( $ss > 59 ) ? $ss - 60 : $ss;

		$post_data['post_date'] = sprintf( '%04d-%02d-%02d %02d:%02d:%02d', $aa, $mm, $jj, $hh, $mn, $ss );

		$valid_date = wp_checkdate( $mm, $jj, $aa, $post_data['post_date'] );
		if ( ! $valid_date ) {
			return new WP_Error( 'invalid_date', __( 'Invalid date.' ) );
		}

		$post_data['post_date_gmt'] = get_gmt_from_date( $post_data['post_date'] );
	}

	if ( isset( $post_data['post_category'] ) ) {
		$category_object = get_taxonomy( 'category' );
		if ( ! current_user_can( $category_object->cap->assign_terms ) ) {
			unset( $post_data['post_category'] );
		}
	}

	return $post_data;
}

/**
 * Returns only allowed post data fields.
 *
 * @since 5.0.1
 *
 * @param array|WP_Error|null $post_data The array of post data to process, or an error object.
 *                                       Defaults to the `$_POST` superglobal.
 * @return array|WP_Error Array of post data on success, WP_Error on failure.
 */
function _wp_get_allowed_postdata( $post_data = null ) {
	if ( empty( $post_data ) ) {
		$post_data = $_POST;
	}

	// Pass through errors.
	if ( is_wp_error( $post_data ) ) {
		return $post_data;
	}

	return array_diff_key( $post_data, array_flip( array( 'meta_input', 'file', 'guid' ) ) );
}

/**
 * Updates an existing post with values provided in `$_POST`.
 *
 * If post data is passed as an argument, it is treated as an array of data
 * keyed appropriately for turning into a post object.
 *
 * If post data is not passed, the `$_POST` global variable is used instead.
 *
 * @since 1.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array|null $post_data Optional. The array of post data to process.
 *                              Defaults to the `$_POST` superglobal.
 * @return int Post ID.
 */
function edit_post( $post_data = null ) {
	global $wpdb;

	if ( empty( $post_data ) ) {
		$post_data = &$_POST;
	}

	// Clear out any data in internal vars.
	unset( $post_data['filter'] );

	$post_ID = (int) $post_data['post_ID'];
	$post    = get_post( $post_ID );

	$post_data['post_type']      = $post->post_type;
	$post_data['post_mime_type'] = $post->post_mime_type;

	if ( ! empty( $post_data['post_status'] ) ) {
		$post_data['post_status'] = sanitize_key( $post_data['post_status'] );

		if ( 'inherit' === $post_data['post_status'] ) {
			unset( $post_data['post_status'] );
		}
	}

	$ptype = get_post_type_object( $post_data['post_type'] );
	if ( ! current_user_can( 'edit_post', $post_ID ) ) {
		if ( 'page' === $post_data['post_type'] ) {
			wp_die( __( 'Sorry, you are not allowed to edit this page.' ) );
		} else {
			wp_die( __( 'Sorry, you are not allowed to edit this post.' ) );
		}
	}

	if ( post_type_supports( $ptype->name, 'revisions' ) ) {
		$revisions = wp_get_post_revisions(
			$post_ID,
			array(
				'order'          => 'ASC',
				'posts_per_page' => 1,
			)
		);
		$revision  = current( $revisions );

		// Check if the revisions have been upgraded.
		if ( $revisions && _wp_get_post_revision_version( $revision ) < 1 ) {
			_wp_upgrade_revisions_of_post( $post, wp_get_post_revisions( $post_ID ) );
		}
	}

	if ( isset( $post_data['visibility'] ) ) {
		switch ( $post_data['visibility'] ) {
			case 'public':
				$post_data['post_password'] = '';
				break;
			case 'password':
				unset( $post_data['sticky'] );
				break;
			case 'private':
				$post_data['post_status']   = 'private';
				$post_data['post_password'] = '';
				unset( $post_data['sticky'] );
				break;
		}
	}

	$post_data = _wp_translate_postdata( true, $post_data );
	if ( is_wp_error( $post_data ) ) {
		wp_die( $post_data->get_error_message() );
	}
	$translated = _wp_get_allowed_postdata( $post_data );

	// Post formats.
	if ( isset( $post_data['post_format'] ) ) {
		set_post_format( $post_ID, $post_data['post_format'] );
	}

	$format_meta_urls = array( 'url', 'link_url', 'quote_source_url' );
	foreach ( $format_meta_urls as $format_meta_url ) {
		$keyed = '_format_' . $format_meta_url;
		if ( isset( $post_data[ $keyed ] ) ) {
			update_post_meta( $post_ID, $keyed, wp_slash( sanitize_url( wp_unslash( $post_data[ $keyed ] ) ) ) );
		}
	}

	$format_keys = array( 'quote', 'quote_source_name', 'image', 'gallery', 'audio_embed', 'video_embed' );

	foreach ( $format_keys as $key ) {
		$keyed = '_format_' . $key;
		if ( isset( $post_data[ $keyed ] ) ) {
			if ( current_user_can( 'unfiltered_html' ) ) {
				update_post_meta( $post_ID, $keyed, $post_data[ $keyed ] );
			} else {
				update_post_meta( $post_ID, $keyed, wp_filter_post_kses( $post_data[ $keyed ] ) );
			}
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

	// Meta stuff.
	if ( isset( $post_data['meta'] ) && $post_data['meta'] ) {
		foreach ( $post_data['meta'] as $key => $value ) {
			$meta = get_post_meta_by_id( $key );
			if ( ! $meta ) {
				continue;
			}
			if ( $meta->post_id != $post_ID ) {
				continue;
			}
			if ( is_protected_meta( $meta->meta_key, 'post' ) || ! current_user_can( 'edit_post_meta', $post_ID, $meta->meta_key ) ) {
				continue;
			}
			if ( is_protected_meta( $value['key'], 'post' ) || ! current_user_can( 'edit_post_meta', $post_ID, $value['key'] ) ) {
				continue;
			}
			update_meta( $key, $value['key'], $value['value'] );
		}
	}

	if ( isset( $post_data['deletemeta'] ) && $post_data['deletemeta'] ) {
		foreach ( $post_data['deletemeta'] as $key => $value ) {
			$meta = get_post_meta_by_id( $key );
			if ( ! $meta ) {
				continue;
			}
			if ( $meta->post_id != $post_ID ) {
				continue;
			}
			if ( is_protected_meta( $meta->meta_key, 'post' ) || ! current_user_can( 'delete_post_meta', $post_ID, $meta->meta_key ) ) {
				continue;
			}
			delete_meta( $key );
		}
	}

	// Attachment stuff.
	if ( 'attachment' === $post_data['post_type'] ) {
		if ( isset( $post_data['_wp_attachment_image_alt'] ) ) {
			$image_alt = wp_unslash( $post_data['_wp_attachment_image_alt'] );

			if ( get_post_meta( $post_ID, '_wp_attachment_image_alt', true ) !== $image_alt ) {
				$image_alt = wp_strip_all_tags( $image_alt, true );

				// update_post_meta() expects slashed.
				update_post_meta( $post_ID, '_wp_attachment_image_alt', wp_slash( $image_alt ) );
			}
		}

		$attachment_data = isset( $post_data['attachments'][ $post_ID ] ) ? $post_data['attachments'][ $post_ID ] : array();

		/** This filter is documented in wp-admin/includes/media.php */
		$translated = apply_filters( 'attachment_fields_to_save', $translated, $attachment_data );
	}

	// Convert taxonomy input to term IDs, to avoid ambiguity.
	if ( isset( $post_data['tax_input'] ) ) {
		foreach ( (array) $post_data['tax_input'] as $taxonomy => $terms ) {
			$tax_object = get_taxonomy( $taxonomy );

			if ( $tax_object && isset( $tax_object->meta_box_sanitize_cb ) ) {
				$translated['tax_input'][ $taxonomy ] = call_user_func_array( $tax_object->meta_box_sanitize_cb, array( $taxonomy, $terms ) );
			}
		}
	}

	add_meta( $post_ID );

	update_post_meta( $post_ID, '_edit_last', get_current_user_id() );

	$success = wp_update_post( $translated );

	// If the save failed, see if we can sanity check the main fields and try again.
	if ( ! $success && is_callable( array( $wpdb, 'strip_invalid_text_for_column' ) ) ) {
		$fields = array( 'post_title', 'post_content', 'post_excerpt' );

		foreach ( $fields as $field ) {
			if ( isset( $translated[ $field ] ) ) {
				$translated[ $field ] = $wpdb->strip_invalid_text_for_column( $wpdb->posts, $field, $translated[ $field ] );
			}
		}

		wp_update_post( $translated );
	}

	// Now that we have an ID we can fix any attachment anchor hrefs.
	_fix_attachment_links( $post_ID );

	wp_set_post_lock( $post_ID );

	if ( current_user_can( $ptype->cap->edit_others_posts ) && current_user_can( $ptype->cap->publish_posts ) ) {
		if ( ! empty( $post_data['sticky'] ) ) {
			stick_post( $post_ID );
		} else {
			unstick_post( $post_ID );
		}
	}

	return $post_ID;
}

/**
 * Processes the post data for the bulk editing of posts.
 *
 * Updates all bulk edited posts/pages, adding (but not removing) tags and
 * categories. Skips pages when they would be their own parent or child.
 *
 * @since 2.7.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array|null $post_data Optional. The array of post data to process.
 *                              Defaults to the `$_POST` superglobal.
 * @return array
 */
function bulk_edit_posts( $post_data = null ) {
	global $wpdb;

	if ( empty( $post_data ) ) {
		$post_data = &$_POST;
	}

	if ( isset( $post_data['post_type'] ) ) {
		$ptype = get_post_type_object( $post_data['post_type'] );
	} else {
		$ptype = get_post_type_object( 'post' );
	}

	if ( ! current_user_can( $ptype->cap->edit_posts ) ) {
		if ( 'page' === $ptype->name ) {
			wp_die( __( 'Sorry, you are not allowed to edit pages.' ) );
		} else {
			wp_die( __( 'Sorry, you are not allowed to edit posts.' ) );
		}
	}

	if ( -1 == $post_data['_status'] ) {
		$post_data['post_status'] = null;
		unset( $post_data['post_status'] );
	} else {
		$post_data['post_status'] = $post_data['_status'];
	}
	unset( $post_data['_status'] );

	if ( ! empty( $post_data['post_status'] ) ) {
		$post_data['post_status'] = sanitize_key( $post_data['post_status'] );

		if ( 'inherit' === $post_data['post_status'] ) {
			unset( $post_data['post_status'] );
		}
	}

	$post_IDs = array_map( 'intval', (array) $post_data['post'] );

	$reset = array(
		'post_author',
		'post_status',
		'post_password',
		'post_parent',
		'page_template',
		'comment_status',
		'ping_status',
		'keep_private',
		'tax_input',
		'post_category',
		'sticky',
		'post_format',
	);

	foreach ( $reset as $field ) {
		if ( isset( $post_data[ $field ] ) && ( '' === $post_data[ $field ] || -1 == $post_data[ $field ] ) ) {
			unset( $post_data[ $field ] );
		}
	}

	if ( isset( $post_data['post_category'] ) ) {
		if ( is_array( $post_data['post_category'] ) && ! empty( $post_data['post_category'] ) ) {
			$new_cats = array_map( 'absint', $post_data['post_category'] );
		} else {
			unset( $post_data['post_category'] );
		}
	}

	$tax_input = array();
	if ( isset( $post_data['tax_input'] ) ) {
		foreach ( $post_data['tax_input'] as $tax_name => $terms ) {
			if ( empty( $terms ) ) {
				continue;
			}
			if ( is_taxonomy_hierarchical( $tax_name ) ) {
				$tax_input[ $tax_name ] = array_map( 'absint', $terms );
			} else {
				$comma = _x( ',', 'tag delimiter' );
				if ( ',' !== $comma ) {
					$terms = str_replace( $comma, ',', $terms );
				}
				$tax_input[ $tax_name ] = explode( ',', trim( $terms, " \n\t\r\0\x0B," ) );
			}
		}
	}

	if ( isset( $post_data['post_parent'] ) && (int) $post_data['post_parent'] ) {
		$parent   = (int) $post_data['post_parent'];
		$pages    = $wpdb->get_results( "SELECT ID, post_parent FROM $wpdb->posts WHERE post_type = 'page'" );
		$children = array();

		for ( $i = 0; $i < 50 && $parent > 0; $i++ ) {
			$children[] = $parent;

			foreach ( $pages as $page ) {
				if ( (int) $page->ID === $parent ) {
					$parent = (int) $page->post_parent;
					break;
				}
			}
		}
	}

	$updated          = array();
	$skipped          = array();
	$locked           = array();
	$shared_post_data = $post_data;

	foreach ( $post_IDs as $post_ID ) {
		// Start with fresh post data with each iteration.
		$post_data = $shared_post_data;

		$post_type_object = get_post_type_object( get_post_type( $post_ID ) );

		if ( ! isset( $post_type_object )
			|| ( isset( $children ) && in_array( $post_ID, $children, true ) )
			|| ! current_user_can( 'edit_post', $post_ID )
		) {
			$skipped[] = $post_ID;
			continue;
		}

		if ( wp_check_post_lock( $post_ID ) ) {
			$locked[] = $post_ID;
			continue;
		}

		$post      = get_post( $post_ID );
		$tax_names = get_object_taxonomies( $post );

		foreach ( $tax_names as $tax_name ) {
			$taxonomy_obj = get_taxonomy( $tax_name );

			if ( ! $taxonomy_obj->show_in_quick_edit ) {
				continue;
			}

			if ( isset( $tax_input[ $tax_name ] ) && current_user_can( $taxonomy_obj->cap->assign_terms ) ) {
				$new_terms = $tax_input[ $tax_name ];
			} else {
				$new_terms = array();
			}

			if ( $taxonomy_obj->hierarchical ) {
				$current_terms = (array) wp_get_object_terms( $post_ID, $tax_name, array( 'fields' => 'ids' ) );
			} else {
				$current_terms = (array) wp_get_object_terms( $post_ID, $tax_name, array( 'fields' => 'names' ) );
			}

			$post_data['tax_input'][ $tax_name ] = array_merge( $current_terms, $new_terms );
		}

		if ( isset( $new_cats ) && in_array( 'category', $tax_names, true ) ) {
			$cats                       = (array) wp_get_post_categories( $post_ID );
			$post_data['post_category'] = array_unique( array_merge( $cats, $new_cats ) );
			unset( $post_data['tax_input']['category'] );
		}

		$post_data['post_ID']        = $post_ID;
		$post_data['post_type']      = $post->post_type;
		$post_data['post_mime_type'] = $post->post_mime_type;

		foreach ( array( 'comment_status', 'ping_status', 'post_author' ) as $field ) {
			if ( ! isset( $post_data[ $field ] ) ) {
				$post_data[ $field ] = $post->$field;
			}
		}

		$post_data = _wp_translate_postdata( true, $post_data );
		if ( is_wp_error( $post_data ) ) {
			$skipped[] = $post_ID;
			continue;
		}
		$post_data = _wp_get_allowed_postdata( $post_data );

		if ( isset( $shared_post_data['post_format'] ) ) {
			set_post_format( $post_ID, $shared_post_data['post_format'] );
		}

		// Prevent wp_insert_post() from overwriting post format with the old data.
		unset( $post_data['tax_input']['post_format'] );

		$post_id = wp_update_post( $post_data );
		update_post_meta( $post_id, '_edit_last', get_current_user_id() );
		$updated[] = $post_id;

		if ( isset( $post_data['sticky'] ) && current_user_can( $ptype->cap->edit_others_posts ) ) {
			if ( 'sticky' === $post_data['sticky'] ) {
				stick_post( $post_ID );
			} else {
				unstick_post( $post_ID );
			}
		}
	}

	return array(
		'updated' => $updated,
		'skipped' => $skipped,
		'locked'  => $locked,
	);
}

/**
 * Returns default post information to use when populating the "Write Post" form.
 *
 * @since 2.0.0
 *
 * @param string $post_type    Optional. A post type string. Default 'post'.
 * @param bool   $create_in_db Optional. Whether to insert the post into database. Default false.
 * @return WP_Post Post object containing all the default post data as attributes
 */
function get_default_post_to_edit( $post_type = 'post', $create_in_db = false ) {
	$post_title = '';
	if ( ! empty( $_REQUEST['post_title'] ) ) {
		$post_title = esc_html( wp_unslash( $_REQUEST['post_title'] ) );
	}

	$post_content = '';
	if ( ! empty( $_REQUEST['content'] ) ) {
		$post_content = esc_html( wp_unslash( $_REQUEST['content'] ) );
	}

	$post_excerpt = '';
	if ( ! empty( $_REQUEST['excerpt'] ) ) {
		$post_excerpt = esc_html( wp_unslash( $_REQUEST['excerpt'] ) );
	}

	if ( $create_in_db ) {
		$post_id = wp_insert_post(
			array(
				'post_title'  => __( 'Auto Draft' ),
				'post_type'   => $post_type,
				'post_status' => 'auto-draft',
			),
			false,
			false
		);
		$post    = get_post( $post_id );
		if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post->post_type, 'post-formats' ) && get_option( 'default_post_format' ) ) {
			set_post_format( $post, get_option( 'default_post_format' ) );
		}
		wp_after_insert_post( $post, false, null );

		// Schedule auto-draft cleanup.
		if ( ! wp_next_scheduled( 'wp_scheduled_auto_draft_delete' ) ) {
			wp_schedule_event( time(), 'daily', 'wp_scheduled_auto_draft_delete' );
		}
	} else {
		$post                 = new stdClass;
		$post->ID             = 0;
		$post->post_author    = '';
		$post->post_date      = '';
		$post->post_date_gmt  = '';
		$post->post_password  = '';
		$post->post_name      = '';
		$post->post_type      = $post_type;
		$post->post_status    = 'draft';
		$post->to_ping        = '';
		$post->pinged         = '';
		$post->comment_status = get_default_comment_status( $post_type );
		$post->ping_status    = get_default_comment_status( $post_type, 'pingback' );
		$post->post_pingback  = get_option( 'default_pingback_flag' );
		$post->post_category  = get_option( 'default_category' );
		$post->page_template  = 'default';
		$post->post_parent    = 0;
		$post->menu_order     = 0;
		$post                 = new WP_Post( $post );
	}

	/**
	 * Filters the default post content initially used in the "Write Post" form.
	 *
	 * @since 1.5.0
	 *
	 * @param string  $post_content Default post content.
	 * @param WP_Post $post         Post object.
	 */
	$post->post_content = (string) apply_filters( 'default_content', $post_content, $post );

	/**
	 * Filters the default post title initially used in the "Write Post" form.
	 *
	 * @since 1.5.0
	 *
	 * @param string  $post_title Default post title.
	 * @param WP_Post $post       Post object.
	 */
	$post->post_title = (string) apply_filters( 'default_title', $post_title, $post );

	/**
	 * Filters the default post excerpt initially used in the "Write Post" form.
	 *
	 * @since 1.5.0
	 *
	 * @param string  $post_excerpt Default post excerpt.
	 * @param WP_Post $post         Post object.
	 */
	$post->post_excerpt = (string) apply_filters( 'default_excerpt', $post_excerpt, $post );

	return $post;
}

/**
 * Determines if a post exists based on title, content, date and type.
 *
 * @since 2.0.0
 * @since 5.2.0 Added the `$type` parameter.
 * @since 5.8.0 Added the `$status` parameter.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $title   Post title.
 * @param string $content Optional. Post content.
 * @param string $date    Optional. Post date.
 * @param string $type    Optional. Post type.
 * @param string $status  Optional. Post status.
 * @return int Post ID if post exists, 0 otherwise.
 */
function post_exists( $title, $content = '', $date = '', $type = '', $status = '' ) {
	global $wpdb;

	$post_title   = wp_unslash( sanitize_post_field( 'post_title', $title, 0, 'db' ) );
	$post_content = wp_unslash( sanitize_post_field( 'post_content', $content, 0, 'db' ) );
	$post_date    = wp_unslash( sanitize_post_field( 'post_date', $date, 0, 'db' ) );
	$post_type    = wp_unslash( sanitize_post_field( 'post_type', $type, 0, 'db' ) );
	$post_status  = wp_unslash( sanitize_post_field( 'post_status', $status, 0, 'db' ) );

	$query = "SELECT ID FROM $wpdb->posts WHERE 1=1";
	$args  = array();

	if ( ! empty( $date ) ) {
		$query .= ' AND post_date = %s';
		$args[] = $post_date;
	}

	if ( ! empty( $title ) ) {
		$query .= ' AND post_title = %s';
		$args[] = $post_title;
	}

	if ( ! empty( $content ) ) {
		$query .= ' AND post_content = %s';
		$args[] = $post_content;
	}

	if ( ! empty( $type ) ) {
		$query .= ' AND post_type = %s';
		$args[] = $post_type;
	}

	if ( ! empty( $status ) ) {
		$query .= ' AND post_status = %s';
		$args[] = $post_status;
	}

	if ( ! empty( $args ) ) {
		return (int) $wpdb->get_var( $wpdb->prepare( $query, $args ) );
	}

	return 0;
}

/**
 * Creates a new post from the "Write Post" form using `$_POST` information.
 *
 * @since 2.1.0
 *
 * @global WP_User $current_user
 *
 * @return int|WP_Error Post ID on success, WP_Error on failure.
 */
function wp_write_post() {
	if ( isset( $_POST['post_type'] ) ) {
		$ptype = get_post_type_object( $_POST['post_type'] );
	} else {
		$ptype = get_post_type_object( 'post' );
	}

	if ( ! current_user_can( $ptype->cap->edit_posts ) ) {
		if ( 'page' === $ptype->name ) {
			return new WP_Error( 'edit_pages', __( 'Sorry, you are not allowed to create pages on this site.' ) );
		} else {
			return new WP_Error( 'edit_posts', __( 'Sorry, you are not allowed to create posts or drafts on this site.' ) );
		}
	}

	$_POST['post_mime_type'] = '';

	// Clear out any data in internal vars.
	unset( $_POST['filter'] );

	// Edit, don't write, if we have a post ID.
	if ( isset( $_POST['post_ID'] ) ) {
		return edit_post();
	}

	if ( isset( $_POST['visibility'] ) ) {
		switch ( $_POST['visibility'] ) {
			case 'public':
				$_POST['post_password'] = '';
				break;
			case 'password':
				unset( $_POST['sticky'] );
				break;
			case 'private':
				$_POST['post_status']   = 'private';
				$_POST['post_password'] = '';
				unset( $_POST['sticky'] );
				break;
		}
	}

	$translated = _wp_translate_postdata( false );
	if ( is_wp_error( $translated ) ) {
		return $translated;
	}
	$translated = _wp_get_allowed_postdata( $translated );

	// Create the post.
	$post_ID = wp_insert_post( $translated );
	if ( is_wp_error( $post_ID ) ) {
		return $post_ID;
	}

	if ( empty( $post_ID ) ) {
		return 0;
	}

	add_meta( $post_ID );

	add_post_meta( $post_ID, '_edit_last', $GLOBALS['current_user']->ID );

	// Now that we have an ID we can fix any attachment anchor hrefs.
	_fix_attachment_links( $post_ID );

	wp_set_post_lock( $post_ID );

	return $post_ID;
}

/**
 * Calls wp_write_post() and handles the errors.
 *
 * @since 2.0.0
 *
 * @return int|void Post ID on success, void on failure.
 */
function write_post() {
	$result = wp_write_post();
	if ( is_wp_error( $result ) ) {
		wp_die( $result->get_error_message() );
	} else {
		return $result;
	}
}

//
// Post Meta.
//

/**
 * Adds post meta data defined in the `$_POST` superglobal for a post with given ID.
 *
 * @since 1.2.0
 *
 * @param int $post_ID
 * @return int|bool
 */
function add_meta( $post_ID ) {
	$post_ID = (int) $post_ID;

	$metakeyselect = isset( $_POST['metakeyselect'] ) ? wp_unslash( trim( $_POST['metakeyselect'] ) ) : '';
	$metakeyinput  = isset( $_POST['metakeyinput'] ) ? wp_unslash( trim( $_POST['metakeyinput'] ) ) : '';
	$metavalue     = isset( $_POST['metavalue'] ) ? $_POST['metavalue'] : '';
	if ( is_string( $metavalue ) ) {
		$metavalue = trim( $metavalue );
	}

	if ( ( ( '#NONE#' !== $metakeyselect ) && ! empty( $metakeyselect ) ) || ! empty( $metakeyinput ) ) {
		/*
		 * We have a key/value pair. If both the select and the input
		 * for the key have data, the input takes precedence.
		 */
		if ( '#NONE#' !== $metakeyselect ) {
			$metakey = $metakeyselect;
		}

		if ( $metakeyinput ) {
			$metakey = $metakeyinput; // Default.
		}

		if ( is_protected_meta( $metakey, 'post' ) || ! current_user_can( 'add_post_meta', $post_ID, $metakey ) ) {
			return false;
		}

		$metakey = wp_slash( $metakey );

		return add_post_meta( $post_ID, $metakey, $metavalue );
	}

	return false;
}

/**
 * Deletes post meta data by meta ID.
 *
 * @since 1.2.0
 *
 * @param int $mid
 * @return bool
 */
function delete_meta( $mid ) {
	return delete_metadata_by_mid( 'post', $mid );
}

/**
 * Returns a list of previously defined keys.
 *
 * @since 1.2.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return string[] Array of meta key names.
 */
function get_meta_keys() {
	global $wpdb;

	$keys = $wpdb->get_col(
		"
			SELECT meta_key
			FROM $wpdb->postmeta
			GROUP BY meta_key
			ORDER BY meta_key"
	);

	return $keys;
}

/**
 * Returns post meta data by meta ID.
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
 * Returns meta data for the given post ID.
 *
 * @since 1.2.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $postid A post ID.
 * @return array[] {
 *     Array of meta data arrays for the given post ID.
 *
 *     @type array ...$0 {
 *         Associative array of meta data.
 *
 *         @type string $meta_key   Meta key.
 *         @type mixed  $meta_value Meta value.
 *         @type string $meta_id    Meta ID as a numeric string.
 *         @type string $post_id    Post ID as a numeric string.
 *     }
 * }
 */
function has_meta( $postid ) {
	global $wpdb;

	return $wpdb->get_results(
		$wpdb->prepare(
			"SELECT meta_key, meta_value, meta_id, post_id
			FROM $wpdb->postmeta WHERE post_id = %d
			ORDER BY meta_key,meta_id",
			$postid
		),
		ARRAY_A
	);
}

/**
 * Updates post meta data by meta ID.
 *
 * @since 1.2.0
 *
 * @param int    $meta_id    Meta ID.
 * @param string $meta_key   Meta key. Expect slashed.
 * @param string $meta_value Meta value. Expect slashed.
 * @return bool
 */
function update_meta( $meta_id, $meta_key, $meta_value ) {
	$meta_key   = wp_unslash( $meta_key );
	$meta_value = wp_unslash( $meta_value );

	return update_metadata_by_mid( 'post', $meta_id, $meta_value, $meta_key );
}

//
// Private.
//

/**
 * Replaces hrefs of attachment anchors with up-to-date permalinks.
 *
 * @since 2.3.0
 * @access private
 *
 * @param int|object $post Post ID or post object.
 * @return void|int|WP_Error Void if nothing fixed. 0 or WP_Error on update failure. The post ID on update success.
 */
function _fix_attachment_links( $post ) {
	$post    = get_post( $post, ARRAY_A );
	$content = $post['post_content'];

	// Don't run if no pretty permalinks or post is not published, scheduled, or privately published.
	if ( ! get_option( 'permalink_structure' ) || ! in_array( $post['post_status'], array( 'publish', 'future', 'private' ), true ) ) {
		return;
	}

	// Short if there aren't any links or no '?attachment_id=' strings (strpos cannot be zero).
	if ( ! strpos( $content, '?attachment_id=' ) || ! preg_match_all( '/<a ([^>]+)>[\s\S]+?<\/a>/', $content, $link_matches ) ) {
		return;
	}

	$site_url = get_bloginfo( 'url' );
	$site_url = substr( $site_url, (int) strpos( $site_url, '://' ) ); // Remove the http(s).
	$replace  = '';

	foreach ( $link_matches[1] as $key => $value ) {
		if ( ! strpos( $value, '?attachment_id=' ) || ! strpos( $value, 'wp-att-' )
			|| ! preg_match( '/href=(["\'])[^"\']*\?attachment_id=(\d+)[^"\']*\\1/', $value, $url_match )
			|| ! preg_match( '/rel=["\'][^"\']*wp-att-(\d+)/', $value, $rel_match ) ) {
				continue;
		}

		$quote  = $url_match[1]; // The quote (single or double).
		$url_id = (int) $url_match[2];
		$rel_id = (int) $rel_match[1];

		if ( ! $url_id || ! $rel_id || $url_id != $rel_id || strpos( $url_match[0], $site_url ) === false ) {
			continue;
		}

		$link    = $link_matches[0][ $key ];
		$replace = str_replace( $url_match[0], 'href=' . $quote . get_attachment_link( $url_id ) . $quote, $link );

		$content = str_replace( $link, $replace, $content );
	}

	if ( $replace ) {
		$post['post_content'] = $content;
		// Escape data pulled from DB.
		$post = add_magic_quotes( $post );

		return wp_update_post( $post );
	}
}

/**
 * Returns all the possible statuses for a post type.
 *
 * @since 2.5.0
 *
 * @param string $type The post_type you want the statuses for. Default 'post'.
 * @return string[] An array of all the statuses for the supplied post type.
 */
function get_available_post_statuses( $type = 'post' ) {
	$stati = wp_count_posts( $type );

	return array_keys( get_object_vars( $stati ) );
}

/**
 * Runs the query to fetch the posts for listing on the edit posts page.
 *
 * @since 2.5.0
 *
 * @param array|false $q Optional. Array of query variables to use to build the query.
 *                       Defaults to the `$_GET` superglobal.
 * @return array
 */
function wp_edit_posts_query( $q = false ) {
	if ( false === $q ) {
		$q = $_GET;
	}
	$q['m']     = isset( $q['m'] ) ? (int) $q['m'] : 0;
	$q['cat']   = isset( $q['cat'] ) ? (int) $q['cat'] : 0;
	$post_stati = get_post_stati();

	if ( isset( $q['post_type'] ) && in_array( $q['post_type'], get_post_types(), true ) ) {
		$post_type = $q['post_type'];
	} else {
		$post_type = 'post';
	}

	$avail_post_stati = get_available_post_statuses( $post_type );
	$post_status      = '';
	$perm             = '';

	if ( isset( $q['post_status'] ) && in_array( $q['post_status'], $post_stati, true ) ) {
		$post_status = $q['post_status'];
		$perm        = 'readable';
	}

	$orderby = '';

	if ( isset( $q['orderby'] ) ) {
		$orderby = $q['orderby'];
	} elseif ( isset( $q['post_status'] ) && in_array( $q['post_status'], array( 'pending', 'draft' ), true ) ) {
		$orderby = 'modified';
	}

	$order = '';

	if ( isset( $q['order'] ) ) {
		$order = $q['order'];
	} elseif ( isset( $q['post_status'] ) && 'pending' === $q['post_status'] ) {
		$order = 'ASC';
	}

	$per_page       = "edit_{$post_type}_per_page";
	$posts_per_page = (int) get_user_option( $per_page );
	if ( empty( $posts_per_page ) || $posts_per_page < 1 ) {
		$posts_per_page = 20;
	}

	/**
	 * Filters the number of items per page to show for a specific 'per_page' type.
	 *
	 * The dynamic portion of the hook name, `$post_type`, refers to the post type.
	 *
	 * Possible hook names include:
	 *
	 *  - `edit_post_per_page`
	 *  - `edit_page_per_page`
	 *  - `edit_attachment_per_page`
	 *
	 * @since 3.0.0
	 *
	 * @param int $posts_per_page Number of posts to display per page for the given post
	 *                            type. Default 20.
	 */
	$posts_per_page = apply_filters( "edit_{$post_type}_per_page", $posts_per_page );

	/**
	 * Filters the number of posts displayed per page when specifically listing "posts".
	 *
	 * @since 2.8.0
	 *
	 * @param int    $posts_per_page Number of posts to be displayed. Default 20.
	 * @param string $post_type      The post type.
	 */
	$posts_per_page = apply_filters( 'edit_posts_per_page', $posts_per_page, $post_type );

	$query = compact( 'post_type', 'post_status', 'perm', 'order', 'orderby', 'posts_per_page' );

	// Hierarchical types require special args.
	if ( is_post_type_hierarchical( $post_type ) && empty( $orderby ) ) {
		$query['orderby']                = 'menu_order title';
		$query['order']                  = 'asc';
		$query['posts_per_page']         = -1;
		$query['posts_per_archive_page'] = -1;
		$query['fields']                 = 'id=>parent';
	}

	if ( ! empty( $q['show_sticky'] ) ) {
		$query['post__in'] = (array) get_option( 'sticky_posts' );
	}

	wp( $query );

	return $avail_post_stati;
}

/**
 * Returns the query variables for the current attachments request.
 *
 * @since 4.2.0
 *
 * @param array|false $q Optional. Array of query variables to use to build the query.
 *                       Defaults to the `$_GET` superglobal.
 * @return array The parsed query vars.
 */
function wp_edit_attachments_query_vars( $q = false ) {
	if ( false === $q ) {
		$q = $_GET;
	}
	$q['m']         = isset( $q['m'] ) ? (int) $q['m'] : 0;
	$q['cat']       = isset( $q['cat'] ) ? (int) $q['cat'] : 0;
	$q['post_type'] = 'attachment';
	$post_type      = get_post_type_object( 'attachment' );
	$states         = 'inherit';
	if ( current_user_can( $post_type->cap->read_private_posts ) ) {
		$states .= ',private';
	}

	$q['post_status'] = isset( $q['status'] ) && 'trash' === $q['status'] ? 'trash' : $states;
	$q['post_status'] = isset( $q['attachment-filter'] ) && 'trash' === $q['attachment-filter'] ? 'trash' : $states;

	$media_per_page = (int) get_user_option( 'upload_per_page' );
	if ( empty( $media_per_page ) || $media_per_page < 1 ) {
		$media_per_page = 20;
	}

	/**
	 * Filters the number of items to list per page when listing media items.
	 *
	 * @since 2.9.0
	 *
	 * @param int $media_per_page Number of media to list. Default 20.
	 */
	$q['posts_per_page'] = apply_filters( 'upload_per_page', $media_per_page );

	$post_mime_types = get_post_mime_types();
	if ( isset( $q['post_mime_type'] ) && ! array_intersect( (array) $q['post_mime_type'], array_keys( $post_mime_types ) ) ) {
		unset( $q['post_mime_type'] );
	}

	foreach ( array_keys( $post_mime_types ) as $type ) {
		if ( isset( $q['attachment-filter'] ) && "post_mime_type:$type" === $q['attachment-filter'] ) {
			$q['post_mime_type'] = $type;
			break;
		}
	}

	if ( isset( $q['detached'] ) || ( isset( $q['attachment-filter'] ) && 'detached' === $q['attachment-filter'] ) ) {
		$q['post_parent'] = 0;
	}

	if ( isset( $q['mine'] ) || ( isset( $q['attachment-filter'] ) && 'mine' === $q['attachment-filter'] ) ) {
		$q['author'] = get_current_user_id();
	}

	// Filter query clauses to include filenames.
	if ( isset( $q['s'] ) ) {
		add_filter( 'wp_allow_query_attachment_by_filename', '__return_true' );
	}

	return $q;
}

/**
 * Executes a query for attachments. An array of WP_Query arguments
 * can be passed in, which will override the arguments set by this function.
 *
 * @since 2.5.0
 *
 * @param array|false $q Optional. Array of query variables to use to build the query.
 *                       Defaults to the `$_GET` superglobal.
 * @return array
 */
function wp_edit_attachments_query( $q = false ) {
	wp( wp_edit_attachments_query_vars( $q ) );

	$post_mime_types       = get_post_mime_types();
	$avail_post_mime_types = get_available_post_mime_types( 'attachment' );

	return array( $post_mime_types, $avail_post_mime_types );
}

/**
 * Returns the list of classes to be used by a meta box.
 *
 * @since 2.5.0
 *
 * @param string $box_id    Meta box ID (used in the 'id' attribute for the meta box).
 * @param string $screen_id The screen on which the meta box is shown.
 * @return string Space-separated string of class names.
 */
function postbox_classes( $box_id, $screen_id ) {
	if ( isset( $_GET['edit'] ) && $_GET['edit'] == $box_id ) {
		$classes = array( '' );
	} elseif ( get_user_option( 'closedpostboxes_' . $screen_id ) ) {
		$closed = get_user_option( 'closedpostboxes_' . $screen_id );
		if ( ! is_array( $closed ) ) {
			$classes = array( '' );
		} else {
			$classes = in_array( $box_id, $closed, true ) ? array( 'closed' ) : array( '' );
		}
	} else {
		$classes = array( '' );
	}

	/**
	 * Filters the postbox classes for a specific screen and box ID combo.
	 *
	 * The dynamic portions of the hook name, `$screen_id` and `$box_id`, refer to
	 * the screen ID and meta box ID, respectively.
	 *
	 * @since 3.2.0
	 *
	 * @param string[] $classes An array of postbox classes.
	 */
	$classes = apply_filters( "postbox_classes_{$screen_id}_{$box_id}", $classes );

	return implode( ' ', $classes );
}

/**
 * Returns a sample permalink based on the post name.
 *
 * @since 2.5.0
 *
 * @param int|WP_Post $post  Post ID or post object.
 * @param string|null $title Optional. Title to override the post's current title
 *                           when generating the post name. Default null.
 * @param string|null $name  Optional. Name to override the post name. Default null.
 * @return array {
 *     Array containing the sample permalink with placeholder for the post name, and the post name.
 *
 *     @type string $0 The permalink with placeholder for the post name.
 *     @type string $1 The post name.
 * }
 */
function get_sample_permalink( $post, $title = null, $name = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return array( '', '' );
	}

	$ptype = get_post_type_object( $post->post_type );

	$original_status = $post->post_status;
	$original_date   = $post->post_date;
	$original_name   = $post->post_name;
	$original_filter = $post->filter;

	// Hack: get_permalink() would return plain permalink for drafts, so we will fake that our post is published.
	if ( in_array( $post->post_status, array( 'draft', 'pending', 'future' ), true ) ) {
		$post->post_status = 'publish';
		$post->post_name   = sanitize_title( $post->post_name ? $post->post_name : $post->post_title, $post->ID );
	}

	// If the user wants to set a new name -- override the current one.
	// Note: if empty name is supplied -- use the title instead, see #6072.
	if ( ! is_null( $name ) ) {
		$post->post_name = sanitize_title( $name ? $name : $title, $post->ID );
	}

	$post->post_name = wp_unique_post_slug( $post->post_name, $post->ID, $post->post_status, $post->post_type, $post->post_parent );

	$post->filter = 'sample';

	$permalink = get_permalink( $post, true );

	// Replace custom post_type token with generic pagename token for ease of use.
	$permalink = str_replace( "%$post->post_type%", '%pagename%', $permalink );

	// Handle page hierarchy.
	if ( $ptype->hierarchical ) {
		$uri = get_page_uri( $post );
		if ( $uri ) {
			$uri = untrailingslashit( $uri );
			$uri = strrev( stristr( strrev( $uri ), '/' ) );
			$uri = untrailingslashit( $uri );
		}

		/** This filter is documented in wp-admin/edit-tag-form.php */
		$uri = apply_filters( 'editable_slug', $uri, $post );
		if ( ! empty( $uri ) ) {
			$uri .= '/';
		}
		$permalink = str_replace( '%pagename%', "{$uri}%pagename%", $permalink );
	}

	/** This filter is documented in wp-admin/edit-tag-form.php */
	$permalink         = array( $permalink, apply_filters( 'editable_slug', $post->post_name, $post ) );
	$post->post_status = $original_status;
	$post->post_date   = $original_date;
	$post->post_name   = $original_name;
	$post->filter      = $original_filter;

	/**
	 * Filters the sample permalink.
	 *
	 * @since 4.4.0
	 *
	 * @param array   $permalink {
	 *     Array containing the sample permalink with placeholder for the post name, and the post name.
	 *
	 *     @type string $0 The permalink with placeholder for the post name.
	 *     @type string $1 The post name.
	 * }
	 * @param int     $post_id Post ID.
	 * @param string  $title   Post title.
	 * @param string  $name    Post name (slug).
	 * @param WP_Post $post    Post object.
	 */
	return apply_filters( 'get_sample_permalink', $permalink, $post->ID, $title, $name, $post );
}

/**
 * Returns the HTML of the sample permalink slug editor.
 *
 * @since 2.5.0
 *
 * @param int|WP_Post $post      Post ID or post object.
 * @param string|null $new_title Optional. New title. Default null.
 * @param string|null $new_slug  Optional. New slug. Default null.
 * @return string The HTML of the sample permalink slug editor.
 */
function get_sample_permalink_html( $post, $new_title = null, $new_slug = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return '';
	}

	list($permalink, $post_name) = get_sample_permalink( $post->ID, $new_title, $new_slug );

	$view_link      = false;
	$preview_target = '';

	if ( current_user_can( 'read_post', $post->ID ) ) {
		if ( 'draft' === $post->post_status || empty( $post->post_name ) ) {
			$view_link      = get_preview_post_link( $post );
			$preview_target = " target='wp-preview-{$post->ID}'";
		} else {
			if ( 'publish' === $post->post_status || 'attachment' === $post->post_type ) {
				$view_link = get_permalink( $post );
			} else {
				// Allow non-published (private, future) to be viewed at a pretty permalink, in case $post->post_name is set.
				$view_link = str_replace( array( '%pagename%', '%postname%' ), $post->post_name, $permalink );
			}
		}
	}

	// Permalinks without a post/page name placeholder don't have anything to edit.
	if ( false === strpos( $permalink, '%postname%' ) && false === strpos( $permalink, '%pagename%' ) ) {
		$return = '<strong>' . __( 'Permalink:' ) . "</strong>\n";

		if ( false !== $view_link ) {
			$display_link = urldecode( $view_link );
			$return      .= '<a id="sample-permalink" href="' . esc_url( $view_link ) . '"' . $preview_target . '>' . esc_html( $display_link ) . "</a>\n";
		} else {
			$return .= '<span id="sample-permalink">' . $permalink . "</span>\n";
		}

		// Encourage a pretty permalink setting.
		if ( ! get_option( 'permalink_structure' ) && current_user_can( 'manage_options' )
			&& ! ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) == $post->ID )
		) {
			$return .= '<span id="change-permalinks"><a href="options-permalink.php" class="button button-small">' . __( 'Change Permalink Structure' ) . "</a></span>\n";
		}
	} else {
		if ( mb_strlen( $post_name ) > 34 ) {
			$post_name_abridged = mb_substr( $post_name, 0, 16 ) . '&hellip;' . mb_substr( $post_name, -16 );
		} else {
			$post_name_abridged = $post_name;
		}

		$post_name_html = '<span id="editable-post-name">' . esc_html( $post_name_abridged ) . '</span>';
		$display_link   = str_replace( array( '%pagename%', '%postname%' ), $post_name_html, esc_html( urldecode( $permalink ) ) );

		$return  = '<strong>' . __( 'Permalink:' ) . "</strong>\n";
		$return .= '<span id="sample-permalink"><a href="' . esc_url( $view_link ) . '"' . $preview_target . '>' . $display_link . "</a></span>\n";
		$return .= '&lrm;'; // Fix bi-directional text display defect in RTL languages.
		$return .= '<span id="edit-slug-buttons"><button type="button" class="edit-slug button button-small hide-if-no-js" aria-label="' . __( 'Edit permalink' ) . '">' . __( 'Edit' ) . "</button></span>\n";
		$return .= '<span id="editable-post-name-full">' . esc_html( $post_name ) . "</span>\n";
	}

	/**
	 * Filters the sample permalink HTML markup.
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
 * Returns HTML for the post thumbnail meta box.
 *
 * @since 2.9.0
 *
 * @param int|null         $thumbnail_id Optional. Thumbnail attachment ID. Default null.
 * @param int|WP_Post|null $post         Optional. The post ID or object associated
 *                                       with the thumbnail. Defaults to global $post.
 * @return string The post thumbnail HTML.
 */
function _wp_post_thumbnail_html( $thumbnail_id = null, $post = null ) {
	$_wp_additional_image_sizes = wp_get_additional_image_sizes();

	$post               = get_post( $post );
	$post_type_object   = get_post_type_object( $post->post_type );
	$set_thumbnail_link = '<p class="hide-if-no-js"><a href="%s" id="set-post-thumbnail"%s class="thickbox">%s</a></p>';
	$upload_iframe_src  = get_upload_iframe_src( 'image', $post->ID );

	$content = sprintf(
		$set_thumbnail_link,
		esc_url( $upload_iframe_src ),
		'', // Empty when there's no featured image set, `aria-describedby` attribute otherwise.
		esc_html( $post_type_object->labels->set_featured_image )
	);

	if ( $thumbnail_id && get_post( $thumbnail_id ) ) {
		$size = isset( $_wp_additional_image_sizes['post-thumbnail'] ) ? 'post-thumbnail' : array( 266, 266 );

		/**
		 * Filters the size used to display the post thumbnail image in the 'Featured image' meta box.
		 *
		 * Note: When a theme adds 'post-thumbnail' support, a special 'post-thumbnail'
		 * image size is registered, which differs from the 'thumbnail' image size
		 * managed via the Settings > Media screen.
		 *
		 * @since 4.4.0
		 *
		 * @param string|int[] $size         Requested image size. Can be any registered image size name, or
		 *                                   an array of width and height values in pixels (in that order).
		 * @param int          $thumbnail_id Post thumbnail attachment ID.
		 * @param WP_Post      $post         The post object associated with the thumbnail.
		 */
		$size = apply_filters( 'admin_post_thumbnail_size', $size, $thumbnail_id, $post );

		$thumbnail_html = wp_get_attachment_image( $thumbnail_id, $size );

		if ( ! empty( $thumbnail_html ) ) {
			$content  = sprintf(
				$set_thumbnail_link,
				esc_url( $upload_iframe_src ),
				' aria-describedby="set-post-thumbnail-desc"',
				$thumbnail_html
			);
			$content .= '<p class="hide-if-no-js howto" id="set-post-thumbnail-desc">' . __( 'Click the image to edit or update' ) . '</p>';
			$content .= '<p class="hide-if-no-js"><a href="#" id="remove-post-thumbnail">' . esc_html( $post_type_object->labels->remove_featured_image ) . '</a></p>';
		}
	}

	$content .= '<input type="hidden" id="_thumbnail_id" name="_thumbnail_id" value="' . esc_attr( $thumbnail_id ? $thumbnail_id : '-1' ) . '" />';

	/**
	 * Filters the admin post thumbnail HTML markup to return.
	 *
	 * @since 2.9.0
	 * @since 3.5.0 Added the `$post_id` parameter.
	 * @since 4.6.0 Added the `$thumbnail_id` parameter.
	 *
	 * @param string   $content      Admin post thumbnail HTML markup.
	 * @param int      $post_id      Post ID.
	 * @param int|null $thumbnail_id Thumbnail attachment ID, or null if there isn't one.
	 */
	return apply_filters( 'admin_post_thumbnail_html', $content, $post->ID, $thumbnail_id );
}

/**
 * Determines whether the post is currently being edited by another user.
 *
 * @since 2.5.0
 *
 * @param int|WP_Post $post ID or object of the post to check for editing.
 * @return int|false ID of the user with lock. False if the post does not exist, post is not locked,
 *                   the user with lock does not exist, or the post is locked by current user.
 */
function wp_check_post_lock( $post ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$lock = get_post_meta( $post->ID, '_edit_lock', true );

	if ( ! $lock ) {
		return false;
	}

	$lock = explode( ':', $lock );
	$time = $lock[0];
	$user = isset( $lock[1] ) ? $lock[1] : get_post_meta( $post->ID, '_edit_last', true );

	if ( ! get_userdata( $user ) ) {
		return false;
	}

	/** This filter is documented in wp-admin/includes/ajax-actions.php */
	$time_window = apply_filters( 'wp_check_post_lock_window', 150 );

	if ( $time && $time > time() - $time_window && get_current_user_id() != $user ) {
		return $user;
	}

	return false;
}

/**
 * Marks the post as currently being edited by the current user.
 *
 * @since 2.5.0
 *
 * @param int|WP_Post $post ID or object of the post being edited.
 * @return array|false {
 *     Array of the lock time and user ID. False if the post does not exist, or there
 *     is no current user.
 *
 *     @type int $0 The current time as a Unix timestamp.
 *     @type int $1 The ID of the current user.
 * }
 */
function wp_set_post_lock( $post ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$user_id = get_current_user_id();

	if ( 0 == $user_id ) {
		return false;
	}

	$now  = time();
	$lock = "$now:$user_id";

	update_post_meta( $post->ID, '_edit_lock', $lock );

	return array( $now, $user_id );
}

/**
 * Outputs the HTML for the notice to say that someone else is editing or has taken over editing of this post.
 *
 * @since 2.8.5
 */
function _admin_notice_post_locked() {
	$post = get_post();

	if ( ! $post ) {
		return;
	}

	$user    = null;
	$user_id = wp_check_post_lock( $post->ID );

	if ( $user_id ) {
		$user = get_userdata( $user_id );
	}

	if ( $user ) {
		/**
		 * Filters whether to show the post locked dialog.
		 *
		 * Returning false from the filter will prevent the dialog from being displayed.
		 *
		 * @since 3.6.0
		 *
		 * @param bool    $display Whether to display the dialog. Default true.
		 * @param WP_Post $post    Post object.
		 * @param WP_User $user    The user with the lock for the post.
		 */
		if ( ! apply_filters( 'show_post_locked_dialog', true, $post, $user ) ) {
			return;
		}

		$locked = true;
	} else {
		$locked = false;
	}

	$sendback = wp_get_referer();
	if ( $locked && $sendback && false === strpos( $sendback, 'post.php' ) && false === strpos( $sendback, 'post-new.php' ) ) {

		$sendback_text = __( 'Go back' );
	} else {
		$sendback = admin_url( 'edit.php' );

		if ( 'post' !== $post->post_type ) {
			$sendback = add_query_arg( 'post_type', $post->post_type, $sendback );
		}

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
			if ( 'publish' === $post->post_status || $user->ID != $post->post_author ) {
				// Latest content is in autosave.
				$nonce                       = wp_create_nonce( 'post_preview_' . $post->ID );
				$query_args['preview_id']    = $post->ID;
				$query_args['preview_nonce'] = $nonce;
			}
		}

		$preview_link = get_preview_post_link( $post->ID, $query_args );

		/**
		 * Filters whether to allow the post lock to be overridden.
		 *
		 * Returning false from the filter will disable the ability
		 * to override the post lock.
		 *
		 * @since 3.6.0
		 *
		 * @param bool    $override Whether to allow the post lock to be overridden. Default true.
		 * @param WP_Post $post     Post object.
		 * @param WP_User $user     The user with the lock for the post.
		 */
		$override = apply_filters( 'override_post_lock', true, $post, $user );
		$tab_last = $override ? '' : ' wp-tab-last';

		?>
		<div class="post-locked-message">
		<div class="post-locked-avatar"><?php echo get_avatar( $user->ID, 64 ); ?></div>
		<p class="currently-editing wp-tab-first" tabindex="0">
		<?php
		if ( $override ) {
			/* translators: %s: User's display name. */
			printf( __( '%s is currently editing this post. Do you want to take over?' ), esc_html( $user->display_name ) );
		} else {
			/* translators: %s: User's display name. */
			printf( __( '%s is currently editing this post.' ), esc_html( $user->display_name ) );
		}
		?>
		</p>
		<?php
		/**
		 * Fires inside the post locked dialog before the buttons are displayed.
		 *
		 * @since 3.6.0
		 * @since 5.4.0 The $user parameter was added.
		 *
		 * @param WP_Post $post Post object.
		 * @param WP_User $user The user with the lock for the post.
		 */
		do_action( 'post_locked_dialog', $post, $user );
		?>
		<p>
		<a class="button" href="<?php echo esc_url( $sendback ); ?>"><?php echo $sendback_text; ?></a>
		<?php if ( $preview_link ) { ?>
		<a class="button<?php echo $tab_last; ?>" href="<?php echo esc_url( $preview_link ); ?>"><?php _e( 'Preview' ); ?></a>
			<?php
		}

		// Allow plugins to prevent some users overriding the post lock.
		if ( $override ) {
			?>
	<a class="button button-primary wp-tab-last" href="<?php echo esc_url( add_query_arg( 'get-post-lock', '1', wp_nonce_url( get_edit_post_link( $post->ID, 'url' ), 'lock-post_' . $post->ID ) ) ); ?>"><?php _e( 'Take over' ); ?></a>
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
			<span class="locked-saved hidden"><?php _e( 'Your latest changes were saved as a revision.' ); ?></span>
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
 * Creates autosave data for the specified post from `$_POST` data.
 *
 * @since 2.6.0
 *
 * @param array|int $post_data Associative array containing the post data, or integer post ID.
 *                             If a numeric post ID is provided, will use the `$_POST` superglobal.
 * @return int|WP_Error The autosave revision ID. WP_Error or 0 on error.
 */
function wp_create_post_autosave( $post_data ) {
	if ( is_numeric( $post_data ) ) {
		$post_id   = $post_data;
		$post_data = $_POST;
	} else {
		$post_id = (int) $post_data['post_ID'];
	}

	$post_data = _wp_translate_postdata( true, $post_data );
	if ( is_wp_error( $post_data ) ) {
		return $post_data;
	}
	$post_data = _wp_get_allowed_postdata( $post_data );

	$post_author = get_current_user_id();

	// Store one autosave per author. If there is already an autosave, overwrite it.
	$old_autosave = wp_get_post_autosave( $post_id, $post_author );
	if ( $old_autosave ) {
		$new_autosave                = _wp_post_revision_data( $post_data, true );
		$new_autosave['ID']          = $old_autosave->ID;
		$new_autosave['post_author'] = $post_author;

		$post = get_post( $post_id );

		// If the new autosave has the same content as the post, delete the autosave.
		$autosave_is_different = false;
		foreach ( array_intersect( array_keys( $new_autosave ), array_keys( _wp_post_revision_fields( $post ) ) ) as $field ) {
			if ( normalize_whitespace( $new_autosave[ $field ] ) !== normalize_whitespace( $post->$field ) ) {
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

	// Otherwise create the new autosave as a special post revision.
	return _wp_put_post_revision( $post_data, true );
}

/**
 * Saves a draft or manually autosaves for the purpose of showing a post preview.
 *
 * @since 2.7.0
 *
 * @return string URL to redirect to show the preview.
 */
function post_preview() {

	$post_ID     = (int) $_POST['post_ID'];
	$_POST['ID'] = $post_ID;

	$post = get_post( $post_ID );

	if ( ! $post ) {
		wp_die( __( 'Sorry, you are not allowed to edit this post.' ) );
	}

	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		wp_die( __( 'Sorry, you are not allowed to edit this post.' ) );
	}

	$is_autosave = false;

	if ( ! wp_check_post_lock( $post->ID ) && get_current_user_id() == $post->post_author
		&& ( 'draft' === $post->post_status || 'auto-draft' === $post->post_status )
	) {
		$saved_post_id = edit_post();
	} else {
		$is_autosave = true;

		if ( isset( $_POST['post_status'] ) && 'auto-draft' === $_POST['post_status'] ) {
			$_POST['post_status'] = 'draft';
		}

		$saved_post_id = wp_create_post_autosave( $post->ID );
	}

	if ( is_wp_error( $saved_post_id ) ) {
		wp_die( $saved_post_id->get_error_message() );
	}

	$query_args = array();

	if ( $is_autosave && $saved_post_id ) {
		$query_args['preview_id']    = $post->ID;
		$query_args['preview_nonce'] = wp_create_nonce( 'post_preview_' . $post->ID );

		if ( isset( $_POST['post_format'] ) ) {
			$query_args['post_format'] = empty( $_POST['post_format'] ) ? 'standard' : sanitize_key( $_POST['post_format'] );
		}

		if ( isset( $_POST['_thumbnail_id'] ) ) {
			$query_args['_thumbnail_id'] = ( (int) $_POST['_thumbnail_id'] <= 0 ) ? '-1' : (int) $_POST['_thumbnail_id'];
		}
	}

	return get_preview_post_link( $post, $query_args );
}

/**
 * Saves a post submitted with XHR.
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
	// Back-compat.
	if ( ! defined( 'DOING_AUTOSAVE' ) ) {
		define( 'DOING_AUTOSAVE', true );
	}

	$post_id              = (int) $post_data['post_id'];
	$post_data['ID']      = $post_id;
	$post_data['post_ID'] = $post_id;

	if ( false === wp_verify_nonce( $post_data['_wpnonce'], 'update-post_' . $post_id ) ) {
		return new WP_Error( 'invalid_nonce', __( 'Error while saving.' ) );
	}

	$post = get_post( $post_id );

	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return new WP_Error( 'edit_posts', __( 'Sorry, you are not allowed to edit this item.' ) );
	}

	if ( 'auto-draft' === $post->post_status ) {
		$post_data['post_status'] = 'draft';
	}

	if ( 'page' !== $post_data['post_type'] && ! empty( $post_data['catslist'] ) ) {
		$post_data['post_category'] = explode( ',', $post_data['catslist'] );
	}

	if ( ! wp_check_post_lock( $post->ID ) && get_current_user_id() == $post->post_author
		&& ( 'auto-draft' === $post->post_status || 'draft' === $post->post_status )
	) {
		// Drafts and auto-drafts are just overwritten by autosave for the same user if the post is not locked.
		return edit_post( wp_slash( $post_data ) );
	} else {
		// Non-drafts or other users' drafts are not overwritten.
		// The autosave is stored in a special post revision for each user.
		return wp_create_post_autosave( wp_slash( $post_data ) );
	}
}

/**
 * Redirects to previous page.
 *
 * @since 2.7.0
 *
 * @param int $post_id Optional. Post ID.
 */
function redirect_post( $post_id = '' ) {
	if ( isset( $_POST['save'] ) || isset( $_POST['publish'] ) ) {
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
			$message = 'draft' === $status ? 10 : 1;
		}

		$location = add_query_arg( 'message', $message, get_edit_post_link( $post_id, 'url' ) );
	} elseif ( isset( $_POST['addmeta'] ) && $_POST['addmeta'] ) {
		$location = add_query_arg( 'message', 2, wp_get_referer() );
		$location = explode( '#', $location );
		$location = $location[0] . '#postcustom';
	} elseif ( isset( $_POST['deletemeta'] ) && $_POST['deletemeta'] ) {
		$location = add_query_arg( 'message', 3, wp_get_referer() );
		$location = explode( '#', $location );
		$location = $location[0] . '#postcustom';
	} else {
		$location = add_query_arg( 'message', 4, get_edit_post_link( $post_id, 'url' ) );
	}

	/**
	 * Filters the post redirect destination URL.
	 *
	 * @since 2.9.0
	 *
	 * @param string $location The destination URL.
	 * @param int    $post_id  The post ID.
	 */
	wp_redirect( apply_filters( 'redirect_post_location', $location, $post_id ) );
	exit;
}

/**
 * Sanitizes POST values from a checkbox taxonomy metabox.
 *
 * @since 5.1.0
 *
 * @param string $taxonomy The taxonomy name.
 * @param array  $terms    Raw term data from the 'tax_input' field.
 * @return int[] Array of sanitized term IDs.
 */
function taxonomy_meta_box_sanitize_cb_checkboxes( $taxonomy, $terms ) {
	return array_map( 'intval', $terms );
}

/**
 * Sanitizes POST values from an input taxonomy metabox.
 *
 * @since 5.1.0
 *
 * @param string       $taxonomy The taxonomy name.
 * @param array|string $terms    Raw term data from the 'tax_input' field.
 * @return array
 */
function taxonomy_meta_box_sanitize_cb_input( $taxonomy, $terms ) {
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

		$_term = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'name'       => $term,
				'fields'     => 'ids',
				'hide_empty' => false,
			)
		);

		if ( ! empty( $_term ) ) {
			$clean_terms[] = (int) $_term[0];
		} else {
			// No existing term was found, so pass the string. A new term will be created.
			$clean_terms[] = $term;
		}
	}

	return $clean_terms;
}

/**
 * Prepares server-registered blocks for the block editor.
 *
 * Returns an associative array of registered block data keyed by block name. Data includes properties
 * of a block relevant for client registration.
 *
 * @since 5.0.0
 *
 * @return array An associative array of registered block data.
 */
function get_block_editor_server_block_settings() {
	$block_registry = WP_Block_Type_Registry::get_instance();
	$blocks         = array();
	$fields_to_pick = array(
		'api_version'      => 'apiVersion',
		'title'            => 'title',
		'description'      => 'description',
		'icon'             => 'icon',
		'attributes'       => 'attributes',
		'provides_context' => 'providesContext',
		'uses_context'     => 'usesContext',
		'supports'         => 'supports',
		'category'         => 'category',
		'styles'           => 'styles',
		'textdomain'       => 'textdomain',
		'parent'           => 'parent',
		'ancestor'         => 'ancestor',
		'keywords'         => 'keywords',
		'example'          => 'example',
		'variations'       => 'variations',
	);

	foreach ( $block_registry->get_all_registered() as $block_name => $block_type ) {
		foreach ( $fields_to_pick as $field => $key ) {
			if ( ! isset( $block_type->{ $field } ) ) {
				continue;
			}

			if ( ! isset( $blocks[ $block_name ] ) ) {
				$blocks[ $block_name ] = array();
			}

			$blocks[ $block_name ][ $key ] = $block_type->{ $field };
		}
	}

	return $blocks;
}

/**
 * Renders the meta boxes forms.
 *
 * @since 5.0.0
 */
function the_block_editor_meta_boxes() {
	global $post, $current_screen, $wp_meta_boxes;

	// Handle meta box state.
	$_original_meta_boxes = $wp_meta_boxes;

	/**
	 * Fires right before the meta boxes are rendered.
	 *
	 * This allows for the filtering of meta box data, that should already be
	 * present by this point. Do not use as a means of adding meta box data.
	 *
	 * @since 5.0.0
	 *
	 * @param array $wp_meta_boxes Global meta box state.
	 */
	$wp_meta_boxes = apply_filters( 'filter_block_editor_meta_boxes', $wp_meta_boxes );
	$locations     = array( 'side', 'normal', 'advanced' );
	$priorities    = array( 'high', 'sorted', 'core', 'default', 'low' );

	// Render meta boxes.
	?>
	<form class="metabox-base-form">
	<?php the_block_editor_meta_box_post_form_hidden_fields( $post ); ?>
	</form>
	<form id="toggle-custom-fields-form" method="post" action="<?php echo esc_url( admin_url( 'post.php' ) ); ?>">
		<?php wp_nonce_field( 'toggle-custom-fields', 'toggle-custom-fields-nonce' ); ?>
		<input type="hidden" name="action" value="toggle-custom-fields" />
	</form>
	<?php foreach ( $locations as $location ) : ?>
		<form class="metabox-location-<?php echo esc_attr( $location ); ?>" onsubmit="return false;">
			<div id="poststuff" class="sidebar-open">
				<div id="postbox-container-2" class="postbox-container">
					<?php
					do_meta_boxes(
						$current_screen,
						$location,
						$post
					);
					?>
				</div>
			</div>
		</form>
	<?php endforeach; ?>
	<?php

	$meta_boxes_per_location = array();
	foreach ( $locations as $location ) {
		$meta_boxes_per_location[ $location ] = array();

		if ( ! isset( $wp_meta_boxes[ $current_screen->id ][ $location ] ) ) {
			continue;
		}

		foreach ( $priorities as $priority ) {
			if ( ! isset( $wp_meta_boxes[ $current_screen->id ][ $location ][ $priority ] ) ) {
				continue;
			}

			$meta_boxes = (array) $wp_meta_boxes[ $current_screen->id ][ $location ][ $priority ];
			foreach ( $meta_boxes as $meta_box ) {
				if ( false == $meta_box || ! $meta_box['title'] ) {
					continue;
				}

				// If a meta box is just here for back compat, don't show it in the block editor.
				if ( isset( $meta_box['args']['__back_compat_meta_box'] ) && $meta_box['args']['__back_compat_meta_box'] ) {
					continue;
				}

				$meta_boxes_per_location[ $location ][] = array(
					'id'    => $meta_box['id'],
					'title' => $meta_box['title'],
				);
			}
		}
	}

	/*
	 * Sadly we probably cannot add this data directly into editor settings.
	 *
	 * Some meta boxes need `admin_head` to fire for meta box registry.
	 * `admin_head` fires after `admin_enqueue_scripts`, which is where we create
	 * our editor instance.
	 */
	$script = 'window._wpLoadBlockEditor.then( function() {
		wp.data.dispatch( \'core/edit-post\' ).setAvailableMetaBoxesPerLocation( ' . wp_json_encode( $meta_boxes_per_location ) . ' );
	} );';

	wp_add_inline_script( 'wp-edit-post', $script );

	/*
	 * When `wp-edit-post` is output in the `<head>`, the inline script needs to be manually printed.
	 * Otherwise, meta boxes will not display because inline scripts for `wp-edit-post`
	 * will not be printed again after this point.
	 */
	if ( wp_script_is( 'wp-edit-post', 'done' ) ) {
		printf( "<script type='text/javascript'>\n%s\n</script>\n", trim( $script ) );
	}

	/*
	 * If the 'postcustom' meta box is enabled, then we need to perform
	 * some extra initialization on it.
	 */
	$enable_custom_fields = (bool) get_user_meta( get_current_user_id(), 'enable_custom_fields', true );

	if ( $enable_custom_fields ) {
		$script = "( function( $ ) {
			if ( $('#postcustom').length ) {
				$( '#the-list' ).wpList( {
					addBefore: function( s ) {
						s.data += '&post_id=$post->ID';
						return s;
					},
					addAfter: function() {
						$('table#list-table').show();
					}
				});
			}
		} )( jQuery );";
		wp_enqueue_script( 'wp-lists' );
		wp_add_inline_script( 'wp-lists', $script );
	}

	/*
	 * Refresh nonces used by the meta box loader.
	 *
	 * The logic is very similar to that provided by post.js for the classic editor.
	 */
	$script = "( function( $ ) {
		var check, timeout;

		function schedule() {
			check = false;
			window.clearTimeout( timeout );
			timeout = window.setTimeout( function() { check = true; }, 300000 );
		}

		$( document ).on( 'heartbeat-send.wp-refresh-nonces', function( e, data ) {
			var post_id, \$authCheck = $( '#wp-auth-check-wrap' );

			if ( check || ( \$authCheck.length && ! \$authCheck.hasClass( 'hidden' ) ) ) {
				if ( ( post_id = $( '#post_ID' ).val() ) && $( '#_wpnonce' ).val() ) {
					data['wp-refresh-metabox-loader-nonces'] = {
						post_id: post_id
					};
				}
			}
		}).on( 'heartbeat-tick.wp-refresh-nonces', function( e, data ) {
			var nonces = data['wp-refresh-metabox-loader-nonces'];

			if ( nonces ) {
				if ( nonces.replace ) {
					if ( nonces.replace.metabox_loader_nonce && window._wpMetaBoxUrl && wp.url ) {
						window._wpMetaBoxUrl= wp.url.addQueryArgs( window._wpMetaBoxUrl, { 'meta-box-loader-nonce': nonces.replace.metabox_loader_nonce } );
					}

					if ( nonces.replace._wpnonce ) {
						$( '#_wpnonce' ).val( nonces.replace._wpnonce );
					}
				}
			}
		}).ready( function() {
			schedule();
		});
	} )( jQuery );";
	wp_add_inline_script( 'heartbeat', $script );

	// Reset meta box data.
	$wp_meta_boxes = $_original_meta_boxes;
}

/**
 * Renders the hidden form required for the meta boxes form.
 *
 * @since 5.0.0
 *
 * @param WP_Post $post Current post object.
 */
function the_block_editor_meta_box_post_form_hidden_fields( $post ) {
	$form_extra = '';
	if ( 'auto-draft' === $post->post_status ) {
		$form_extra .= "<input type='hidden' id='auto_draft' name='auto_draft' value='1' />";
	}
	$form_action  = 'editpost';
	$nonce_action = 'update-post_' . $post->ID;
	$form_extra  .= "<input type='hidden' id='post_ID' name='post_ID' value='" . esc_attr( $post->ID ) . "' />";
	$referer      = wp_get_referer();
	$current_user = wp_get_current_user();
	$user_id      = $current_user->ID;
	wp_nonce_field( $nonce_action );

	/*
	 * Some meta boxes hook into these actions to add hidden input fields in the classic post form.
	 * For backward compatibility, we can capture the output from these actions,
	 * and extract the hidden input fields.
	 */
	ob_start();
	/** This filter is documented in wp-admin/edit-form-advanced.php */
	do_action( 'edit_form_after_title', $post );
	/** This filter is documented in wp-admin/edit-form-advanced.php */
	do_action( 'edit_form_advanced', $post );
	$classic_output = ob_get_clean();

	$classic_elements = wp_html_split( $classic_output );
	$hidden_inputs    = '';
	foreach ( $classic_elements as $element ) {
		if ( 0 !== strpos( $element, '<input ' ) ) {
			continue;
		}

		if ( preg_match( '/\stype=[\'"]hidden[\'"]\s/', $element ) ) {
			echo $element;
		}
	}
	?>
	<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_id; ?>" />
	<input type="hidden" id="hiddenaction" name="action" value="<?php echo esc_attr( $form_action ); ?>" />
	<input type="hidden" id="originalaction" name="originalaction" value="<?php echo esc_attr( $form_action ); ?>" />
	<input type="hidden" id="post_type" name="post_type" value="<?php echo esc_attr( $post->post_type ); ?>" />
	<input type="hidden" id="original_post_status" name="original_post_status" value="<?php echo esc_attr( $post->post_status ); ?>" />
	<input type="hidden" id="referredby" name="referredby" value="<?php echo $referer ? esc_url( $referer ) : ''; ?>" />

	<?php
	if ( 'draft' !== get_post_status( $post ) ) {
		wp_original_referer_field( true, 'previous' );
	}
	echo $form_extra;
	wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
	wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
	// Permalink title nonce.
	wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false );

	/**
	 * Adds hidden input fields to the meta box save form.
	 *
	 * Hook into this action to print `<input type="hidden" ... />` fields, which will be POSTed back to
	 * the server when meta boxes are saved.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_Post $post The post that is being edited.
	 */
	do_action( 'block_editor_meta_box_hidden_fields', $post );
}

/**
 * Disables block editor for wp_navigation type posts so they can be managed via the UI.
 *
 * @since 5.9.0
 * @access private
 *
 * @param bool   $value Whether the CPT supports block editor or not.
 * @param string $post_type Post type.
 * @return bool Whether the block editor should be disabled or not.
 */
function _disable_block_editor_for_navigation_post_type( $value, $post_type ) {
	if ( 'wp_navigation' === $post_type ) {
		return false;
	}

	return $value;
}

/**
 * This callback disables the content editor for wp_navigation type posts.
 * Content editor cannot handle wp_navigation type posts correctly.
 * We cannot disable the "editor" feature in the wp_navigation's CPT definition
 * because it disables the ability to save navigation blocks via REST API.
 *
 * @since 5.9.0
 * @access private
 *
 * @param WP_Post $post An instance of WP_Post class.
 */
function _disable_content_editor_for_navigation_post_type( $post ) {
	$post_type = get_post_type( $post );
	if ( 'wp_navigation' !== $post_type ) {
		return;
	}

	remove_post_type_support( $post_type, 'editor' );
}

/**
 * This callback enables content editor for wp_navigation type posts.
 * We need to enable it back because we disable it to hide
 * the content editor for wp_navigation type posts.
 *
 * @since 5.9.0
 * @access private
 *
 * @see _disable_content_editor_for_navigation_post_type
 *
 * @param WP_Post $post An instance of WP_Post class.
 */
function _enable_content_editor_for_navigation_post_type( $post ) {
	$post_type = get_post_type( $post );
	if ( 'wp_navigation' !== $post_type ) {
		return;
	}

	add_post_type_support( $post_type, 'editor' );
}

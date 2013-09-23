<?php
/**
 * WordPress Administration Revisions API
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Get the revision UI diff.
 *
 * @since 3.6.0
 *
 * @param object|int $post         The post object. Also accepts a post ID.
 * @param int        $compare_from The revision ID to compare from.
 * @param int        $compare_to   The revision ID to come to.
 *
 * @return array|bool Associative array of a post's revisioned fields and their diffs.
 *                    Or, false on failure.
 */
function wp_get_revision_ui_diff( $post, $compare_from, $compare_to ) {
	if ( ! $post = get_post( $post ) )
		return false;

	if ( $compare_from ) {
		if ( ! $compare_from = get_post( $compare_from ) )
			return false;
	} else {
		// If we're dealing with the first revision...
		$compare_from = false;
	}

	if ( ! $compare_to = get_post( $compare_to ) )
		return false;

	// If comparing revisions, make sure we're dealing with the right post parent.
	// The parent post may be a 'revision' when revisions are disabled and we're looking at autosaves.
	if ( $compare_from && $compare_from->post_parent !== $post->ID && $compare_from->ID !== $post->ID )
		return false;
	if ( $compare_to->post_parent !== $post->ID && $compare_to->ID !== $post->ID )
		return false;

	if ( $compare_from && strtotime( $compare_from->post_date_gmt ) > strtotime( $compare_to->post_date_gmt ) ) {
		$temp = $compare_from;
		$compare_from = $compare_to;
		$compare_to = $temp;
	}

	// Add default title if title field is empty
	if ( $compare_from && empty( $compare_from->post_title ) )
		$compare_from->post_title = __( '(no title)' );
	if ( empty( $compare_to->post_title ) )
		$compare_to->post_title = __( '(no title)' );

	$return = array();

	foreach ( _wp_post_revision_fields() as $field => $name ) {
		/**
		 * Contextually filter a post revision field.
		 *
		 * The dynamic portion of the hook name, $field, corresponds to each of the post
		 * fields of the revision object being iterated over in a foreach statement.
		 *
		 * @since 3.6.0
		 *
		 * @param string  $compare_from->$field The current revision field to compare to or from.
		 * @param string  $field                The current revision field.
		 * @param WP_Post $compare_from         The revision post object to compare to or from.
		 * @param string  null                  The context of whether the current revision is the old or the new one. Values are 'to' or 'from'.
		 */
		$content_from = $compare_from ? apply_filters( "_wp_post_revision_field_$field", $compare_from->$field, $field, $compare_from, 'from' ) : '';

		//duplicate_hook
		$content_to = apply_filters( "_wp_post_revision_field_$field", $compare_to->$field, $field, $compare_to, 'to' );

		$diff = wp_text_diff( $content_from, $content_to, array( 'show_split_view' => true ) );

		if ( ! $diff && 'post_title' === $field ) {
			// It's a better user experience to still show the Title, even if it didn't change.
			// No, you didn't see this.
			$diff = '<table class="diff"><colgroup><col class="content diffsplit left"><col class="content diffsplit middle"><col class="content diffsplit right"></colgroup><tbody><tr>';
			$diff .= '<td>' . esc_html( $compare_from->post_title ) . '</td><td></td><td>' . esc_html( $compare_to->post_title ) . '</td>';
			$diff .= '</tr></tbody>';
			$diff .= '</table>';
		}

		if ( $diff ) {
			$return[] = array(
				'id' => $field,
				'name' => $name,
				'diff' => $diff,
			);
		}
	}
	return $return;
}

/**
 * Prepare revisions for JavaScript.
 *
 * @since 3.6.0
 *
 * @param object|int $post                 The post object. Also accepts a post ID.
 * @param int        $selected_revision_id The selected revision ID.
 * @param int        $from                 Optional. The revision ID to compare from.
 *
 * @return array An associative array of revision data and related settings.
 */
function wp_prepare_revisions_for_js( $post, $selected_revision_id, $from = null ) {
	$post = get_post( $post );
	$revisions = $authors = array();
	$now_gmt = time();

	$revisions = wp_get_post_revisions( $post->ID, array( 'order' => 'ASC', 'check_enabled' => false ) );
	// If revisions are disabled, we only want autosaves and the current post.
	if ( ! wp_revisions_enabled( $post ) ) {
		foreach ( $revisions as $revision_id => $revision ) {
			if ( ! wp_is_post_autosave( $revision ) )
				unset( $revisions[ $revision_id ] );
		}
		$revisions = array( $post->ID => $post ) + $revisions;
	}

	$show_avatars = get_option( 'show_avatars' );

	cache_users( wp_list_pluck( $revisions, 'post_author' ) );

	$can_restore = current_user_can( 'edit_post', $post->ID );

	foreach ( $revisions as $revision ) {
		$modified = strtotime( $revision->post_modified );
		$modified_gmt = strtotime( $revision->post_modified_gmt );
		if ( $can_restore ) {
			$restore_link = str_replace( '&amp;', '&', wp_nonce_url(
				add_query_arg(
					array( 'revision' => $revision->ID,
						'action' => 'restore' ),
						admin_url( 'revision.php' )
				),
				"restore-post_{$revision->ID}"
			) );
		}

		if ( ! isset( $authors[ $revision->post_author ] ) ) {
			$authors[ $revision->post_author ] = array(
				'id' => (int) $revision->post_author,
				'avatar' => $show_avatars ? get_avatar( $revision->post_author, 32 ) : '',
				'name' => get_the_author_meta( 'display_name', $revision->post_author ),
			);
		}

		$autosave = (bool) wp_is_post_autosave( $revision );
		$current = ! $autosave && $revision->post_modified_gmt === $post->post_modified_gmt;
		if ( $current && ! empty( $current_id ) ) {
			// If multiple revisions have the same post_modified_gmt, highest ID is current.
			if ( $current_id < $revision->ID ) {
				$revisions[ $current_id ]['current'] = false;
				$current_id = $revision->ID;
			} else {
				$current = false;
			}
		} elseif ( $current ) {
			$current_id = $revision->ID;
		}

		$revisions[ $revision->ID ] = array(
			'id'         => $revision->ID,
			'title'      => get_the_title( $post->ID ),
			'author'     => $authors[ $revision->post_author ],
			'date'       => date_i18n( __( 'M j, Y @ G:i' ), $modified ),
			'dateShort'  => date_i18n( _x( 'j M @ G:i', 'revision date short format' ), $modified ),
			'timeAgo'    => sprintf( __( '%s ago' ), human_time_diff( $modified_gmt, $now_gmt ) ),
			'autosave'   => $autosave,
			'current'    => $current,
			'restoreUrl' => $can_restore ? $restore_link : false,
		);
	}

	// If a post has been saved since the last revision (no revisioned fields were changed)
	// we may not have a "current" revision. Mark the latest revision as "current".
	if ( empty( $current_id ) ) {
		if ( $revisions[ $revision->ID ]['autosave'] ) {
			$revision = end( $revisions );
			while ( $revision['autosave'] ) {
				$revision = prev( $revisions );
			}
			$current_id = $revision['id'];
		} else {
			$current_id = $revision->ID;
		}
		$revisions[ $current_id ]['current'] = true;
	}

	// Now, grab the initial diff
	$compare_two_mode = is_numeric( $from );
	if ( ! $compare_two_mode ) {
		$found = array_search( $selected_revision_id, array_keys( $revisions ) );
		if ( $found ) {
			$from = array_keys( array_slice( $revisions, $found - 1, 1, true ) );
			$from = reset( $from );
		} else {
			$from = 0;
		}
	}

	$from = absint( $from );

	$diffs = array( array(
		'id' => $from . ':' . $selected_revision_id,
		'fields' => wp_get_revision_ui_diff( $post->ID, $from, $selected_revision_id ),
	));

	return array(
		'postId'           => $post->ID,
		'nonce'            => wp_create_nonce( 'revisions-ajax-nonce' ),
		'revisionData'     => array_values( $revisions ),
		'to'               => $selected_revision_id,
		'from'             => $from,
		'diffData'         => $diffs,
		'baseUrl'          => parse_url( admin_url( 'revision.php' ), PHP_URL_PATH ),
		'compareTwoMode'   => absint( $compare_two_mode ), // Apparently booleans are not allowed
		'revisionIds'      => array_keys( $revisions ),
	);
}

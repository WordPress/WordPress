<?php

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
	if ( $compare_from && $compare_from->post_parent !== $post->ID )
		return false;
	if ( $compare_to->post_parent !== $post->ID )
		return false;

	if ( $compare_from && strtotime( $compare_from->post_date_gmt ) > strtotime( $compare_to->post_date_gmt ) ) {
		$temp = $compare_from;
		$compare_from = $compare_to;
		$compare_to = $temp;
	}

	$return = array();

	foreach ( _wp_post_revision_fields() as $field => $name ) {
		$content_from = $compare_from ? apply_filters( "_wp_post_revision_field_$field", $compare_from->$field, $field, $compare_from, 'left' ) : '';
		$content_to = apply_filters( "_wp_post_revision_field_$field", $compare_to->$field, $field, $compare_to, 'right' );

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

function wp_prepare_revisions_for_js( $post, $selected_revision_id ) {
	$post = get_post( $post );
	$revisions = array();
	$current = current_time( 'timestamp' );

	$revisions = wp_get_post_revisions( $post->ID );

	cache_users( wp_list_pluck( $revisions, 'post_author' ) );

	foreach ( $revisions as $revision ) {
		$modified_gmt = strtotime( $revision->post_modified_gmt );
		$restore_link = wp_nonce_url(
			add_query_arg(
				array( 'revision' => $revision->ID,
					'action' => 'restore' ),
					admin_url( 'revision.php' )
			),
			"restore-post_{$revision->ID}"
		);
		$revisions[ $revision->ID ] = array(
			'id'           => $revision->ID,
			'title'        => get_the_title( $post->ID ),
			'author' => array(
				'id'     => (int) $revision->post_author,
				'avatar' => get_avatar( $revision->post_author, 24 ),
				'name'   => get_the_author_meta( 'display_name', $revision->post_author ),
			),
			'date'         => date_i18n( __( 'M j, Y @ G:i' ), $modified_gmt ),
			'dateShort'    => date_i18n( _x( 'j M @ G:i', 'revision date short format' ), $modified_gmt ),
			'timeAgo'      => human_time_diff( $modified_gmt, $current ),
			'autosave'     => wp_is_post_autosave( $revision ),
			'current'      => $revision->post_modified_gmt === $post->post_modified_gmt,
			'restoreUrl'   => urldecode( $restore_link ),
		);
	}

	return array(
		'postId'           => $post->ID,
		'nonce'            => wp_create_nonce( 'revisions-ajax-nonce' ),
		'revisionData'     => array_values( $revisions ),
		'selectedRevision' => $selected_revision_id,
	);
}

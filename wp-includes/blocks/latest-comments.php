<?php
/**
 * Server-side rendering of the `core/latest-comments` block.
 *
 * @package WordPress
 */

/**
 * Get the post title.
 *
 * The post title is fetched and if it is blank then a default string is
 * returned.
 *
 * Copied from `wp-admin/includes/template.php`, but we can't include that
 * file because:
 *
 * 1. It causes bugs with test fixture generation and strange Docker 255 error
 *    codes.
 * 2. It's in the admin; ideally we *shouldn't* be including files from the
 *    admin for a block's output. It's a very small/simple function as well,
 *    so duplicating it isn't too terrible.
 *
 * @since 3.3.0
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return string The post title if set; "(no title)" if no title is set.
 */
function wp_latest_comments_draft_or_post_title( $post = 0 ) {
	$title = get_the_title( $post );
	if ( empty( $title ) ) {
		$title = __( '(no title)' );
	}
	return esc_html( $title );
}

/**
 * Renders the `core/latest-comments` block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the post content with latest comments added.
 */
function render_block_core_latest_comments( $attributes = array() ) {
	// This filter is documented in wp-includes/widgets/class-wp-widget-recent-comments.php.
	$comments = get_comments(
		apply_filters(
			'widget_comments_args',
			array(
				'number'      => $attributes['commentsToShow'],
				'status'      => 'approve',
				'post_status' => 'publish',
			)
		)
	);

	$list_items_markup = '';
	if ( ! empty( $comments ) ) {
		// Prime the cache for associated posts. This is copied from \WP_Widget_Recent_Comments::widget().
		$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
		_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

		foreach ( $comments as $comment ) {
			$list_items_markup .= '<li class="wp-block-latest-comments__comment">';
			if ( $attributes['displayAvatar'] ) {
				$avatar = get_avatar(
					$comment,
					48,
					'',
					'',
					array(
						'class' => 'wp-block-latest-comments__comment-avatar',
					)
				);
				if ( $avatar ) {
					$list_items_markup .= $avatar;
				}
			}

			$list_items_markup .= '<article>';
			$list_items_markup .= '<footer class="wp-block-latest-comments__comment-meta">';
			$author_url         = get_comment_author_url( $comment );
			if ( empty( $author_url ) && ! empty( $comment->user_id ) ) {
				$author_url = get_author_posts_url( $comment->user_id );
			}

			$author_markup = '';
			if ( $author_url ) {
				$author_markup .= '<a class="wp-block-latest-comments__comment-author" href="' . esc_url( $author_url ) . '">' . get_comment_author( $comment ) . '</a>';
			} else {
				$author_markup .= '<span class="wp-block-latest-comments__comment-author">' . get_comment_author( $comment ) . '</span>';
			}

			// `_draft_or_post_title` calls `esc_html()` so we don't need to wrap that call in
			// `esc_html`.
			$post_title = '<a class="wp-block-latest-comments__comment-link" href="' . esc_url( get_comment_link( $comment ) ) . '">' . wp_latest_comments_draft_or_post_title( $comment->comment_post_ID ) . '</a>';

			$list_items_markup .= sprintf(
				/* translators: 1: author name (inside <a> or <span> tag, based on if they have a URL), 2: post title related to this comment */
				__( '%1$s on %2$s' ),
				$author_markup,
				$post_title
			);

			if ( $attributes['displayDate'] ) {
				$list_items_markup .= sprintf(
					'<time datetime="%1$s" class="wp-block-latest-comments__comment-date">%2$s</time>',
					esc_attr( get_comment_date( 'c', $comment ) ),
					date_i18n( get_option( 'date_format' ), get_comment_date( 'U', $comment ) )
				);
			}
			$list_items_markup .= '</footer>';
			if ( $attributes['displayExcerpt'] ) {
				$list_items_markup .= '<div class="wp-block-latest-comments__comment-excerpt">' . wpautop( get_comment_excerpt( $comment ) ) . '</div>';
			}
			$list_items_markup .= '</article></li>';
		}
	}

	$classnames = array();
	if ( $attributes['displayAvatar'] ) {
		$classnames[] = 'has-avatars';
	}
	if ( $attributes['displayDate'] ) {
		$classnames[] = 'has-dates';
	}
	if ( $attributes['displayExcerpt'] ) {
		$classnames[] = 'has-excerpts';
	}
	if ( empty( $comments ) ) {
		$classnames[] = 'no-comments';
	}
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classnames ) ) );

	return ! empty( $comments ) ? sprintf(
		'<ol %1$s>%2$s</ol>',
		$wrapper_attributes,
		$list_items_markup
	) : sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		__( 'No comments to show.' )
	);
}

/**
 * Registers the `core/latest-comments` block.
 */
function register_block_core_latest_comments() {
	register_block_type_from_metadata(
		__DIR__ . '/latest-comments',
		array(
			'render_callback' => 'render_block_core_latest_comments',
		)
	);
}

add_action( 'init', 'register_block_core_latest_comments' );

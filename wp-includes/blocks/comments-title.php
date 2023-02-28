<?php
/**
 * Server-side rendering of the `core/comments-title` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/comments-title` block on the server.
 *
 * @param array $attributes Block attributes.
 *
 * @return string Return the post comments title.
 */
function render_block_core_comments_title( $attributes ) {

	if ( post_password_required() ) {
		return;
	}

	$align_class_name    = empty( $attributes['textAlign'] ) ? '' : "has-text-align-{$attributes['textAlign']}";
	$show_post_title     = ! empty( $attributes['showPostTitle'] ) && $attributes['showPostTitle'];
	$show_comments_count = ! empty( $attributes['showCommentsCount'] ) && $attributes['showCommentsCount'];
	$wrapper_attributes  = get_block_wrapper_attributes( array( 'class' => $align_class_name ) );
	$comments_count      = get_comments_number();
	/* translators: %s: Post title. */
	$post_title = sprintf( __( '&#8220;%s&#8221;' ), get_the_title() );
	$tag_name   = 'h2';
	if ( isset( $attributes['level'] ) ) {
		$tag_name = 'h' . $attributes['level'];
	}

	if ( '0' === $comments_count ) {
		return;
	}

	if ( $show_comments_count ) {
		if ( $show_post_title ) {
			if ( '1' === $comments_count ) {
				/* translators: %s: Post title. */
				$comments_title = sprintf( __( 'One response to %s' ), $post_title );
			} else {
				$comments_title = sprintf(
					/* translators: 1: Number of comments, 2: Post title. */
					_n(
						'%1$s response to %2$s',
						'%1$s responses to %2$s',
						$comments_count
					),
					number_format_i18n( $comments_count ),
					$post_title
				);
			}
		} elseif ( '1' === $comments_count ) {
			$comments_title = __( 'One response' );
		} else {
			$comments_title = sprintf(
				/* translators: %s: Number of comments. */
				_n( '%s response', '%s responses', $comments_count ),
				number_format_i18n( $comments_count )
			);
		}
	} elseif ( $show_post_title ) {
		if ( '1' === $comments_count ) {
			/* translators: %s: Post title. */
			$comments_title = sprintf( __( 'Response to %s' ), $post_title );
		} else {
			/* translators: %s: Post title. */
			$comments_title = sprintf( __( 'Responses to %s' ), $post_title );
		}
	} elseif ( '1' === $comments_count ) {
		$comments_title = __( 'Response' );
	} else {
		$comments_title = __( 'Responses' );
	}

	return sprintf(
		'<%1$s id="comments" %2$s>%3$s</%1$s>',
		$tag_name,
		$wrapper_attributes,
		$comments_title
	);
}

/**
 * Registers the `core/comments-title` block on the server.
 */
function register_block_core_comments_title() {
	register_block_type_from_metadata(
		__DIR__ . '/comments-title',
		array(
			'render_callback' => 'render_block_core_comments_title',
		)
	);
}

add_action( 'init', 'register_block_core_comments_title' );

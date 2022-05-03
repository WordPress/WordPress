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
	$post_title          = $show_post_title ? sprintf( '"%1$s"', get_the_title() ) : null;
	$comments_count      = number_format_i18n( get_comments_number() );
	$tag_name            = 'h2';
	if ( isset( $attributes['level'] ) ) {
		$tag_name = 'h' . $attributes['level'];
	}

	if ( '0' === $comments_count ) {
		return;
	}

	$single_default_comment_label = $show_post_title ? __( 'Response to' ) : __( 'Response' );
	if ( $show_comments_count ) {
		$single_default_comment_label = $show_post_title ? __( 'One response to' ) : __( 'One response' );
	}
	$single_comment_label = ! empty( $attributes['singleCommentLabel'] ) ? $attributes['singleCommentLabel'] : $single_default_comment_label;

	$multiple_default_comment_label = $show_post_title ? __( 'Responses to' ) : __( 'Responses' );
	if ( $show_comments_count ) {
		$multiple_default_comment_label = $show_post_title ? __( 'responses to' ) : __( 'responses' );
	}

	$multiple_comment_label = ! empty( $attributes['multipleCommentsLabel'] ) ? $attributes['multipleCommentsLabel'] : $multiple_default_comment_label;

	$comments_title = '%1$s %2$s %3$s';

	$comments_title = sprintf(
		$comments_title,
		// If there is only one comment, only display the label.
		'1' !== $comments_count && $show_comments_count ? $comments_count : null,
		'1' === $comments_count ? $single_comment_label : $multiple_comment_label,
		$post_title
	);

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

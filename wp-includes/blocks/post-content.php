<?php
/**
 * Server-side rendering of the `core/post-content` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-content` block on the server.
 *
 * @since 5.8.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the filtered post content of the current post.
 */
function render_block_core_post_content( $attributes, $content, $block ) {
	static $seen_ids = array();

	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$post_id = $block->context['postId'];

	if ( isset( $seen_ids[ $post_id ] ) ) {
		// WP_DEBUG_DISPLAY must only be honored when WP_DEBUG. This precedent
		// is set in `wp_debug_mode()`.
		$is_debug = WP_DEBUG && WP_DEBUG_DISPLAY;

		return $is_debug ?
			// translators: Visible only in the front end, this warning takes the place of a faulty block.
			__( '[block rendering halted]' ) :
			'';
	}

	$seen_ids[ $post_id ] = true;

	// When inside the main loop, we want to use queried object
	// so that `the_preview` for the current post can apply.
	// We force this behavior by omitting the third argument (post ID) from the `get_the_content`.
	$content = get_the_content();
	// Check for nextpage to display page links for paginated posts.
	if ( has_block( 'core/nextpage' ) ) {
		$content .= wp_link_pages( array( 'echo' => 0 ) );
	}

	/** This filter is documented in wp-includes/post-template.php */
	$content = apply_filters( 'the_content', str_replace( ']]>', ']]&gt;', $content ) );
	unset( $seen_ids[ $post_id ] );

	if ( empty( $content ) ) {
		return '';
	}

	$tag_name = 'div';

	if ( ! empty( $attributes['tagName'] ) && tag_escape( $attributes['tagName'] ) === $attributes['tagName'] ) {
		$tag_name = $attributes['tagName'];
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'entry-content' ) );

	return sprintf(
		'<%1$s %2$s>%3$s</%1$s>',
		$tag_name,
		$wrapper_attributes,
		$content
	);
}

/**
 * Registers the `core/post-content` block on the server.
 *
 * @since 5.8.0
 */
function register_block_core_post_content() {
	register_block_type_from_metadata(
		__DIR__ . '/post-content',
		array(
			'render_callback' => 'render_block_core_post_content',
		)
	);
}
add_action( 'init', 'register_block_core_post_content' );

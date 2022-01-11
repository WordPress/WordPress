<?php
/**
 * Server-side rendering of the `core/post-content` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-content` block on the server.
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
		$is_debug = defined( 'WP_DEBUG' ) && WP_DEBUG &&
			defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY;

		return $is_debug ?
			// translators: Visible only in the front end, this warning takes the place of a faulty block.
			__( '[block rendering halted]' ) :
			'';
	}

	$seen_ids[ $post_id ] = true;

	// Check is needed for backward compatibility with third-party plugins
	// that might rely on the `in_the_loop` check; calling `the_post` sets it to true.
	if ( ! in_the_loop() && have_posts() ) {
		the_post();
	}

	// When inside the main loop, we want to use queried object
	// so that `the_preview` for the current post can apply.
	// We force this behavior by omitting the third argument (post ID) from the `get_the_content`.
	$content = get_the_content( null, false );
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

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'entry-content' ) );

	return (
		'<div ' . $wrapper_attributes . '>' .
			$content .
		'</div>'
	);
}

/**
 * Registers the `core/post-content` block on the server.
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

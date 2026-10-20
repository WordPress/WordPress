<?php
/**
 * Server-side rendering of the `core/video` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/video` block on the server to supply the width and height attributes from the attachment metadata.
 *
 * @since 6.9.0
 *
 * @phpstan-param  array{ "id"?: positive-int } $attributes
 *
 * @param array   $attributes The block attributes.
 * @param string  $content    The block content.
 * @return string The block content with the dimensions added.
 */
function render_block_core_video( array $attributes, string $content ): string {
	// If the content lacks any video tag (either <video> or <VIDEO>), abort. Note that str_contains() is case-sensitive.
	if ( stripos( $content, '<video' ) === false ) {
		return $content;
	}

	/*
	 * Inspect the video to determine whether autoplay is enabled and, when it
	 * is, set the interactivity directives that stop playback for visitors who
	 * prefer reduced motion.
	 *
	 * The directives are added here rather than in the saved markup so existing
	 * content does not need a deprecation.
	 *
	 * `autoplay` is read from the markup rather than from `$attributes`, because
	 * it is sourced from the `video` element and so is never stored in the block
	 * comment delimiter the server parses.
	 */
	$autoplay_processor = new WP_HTML_Tag_Processor( $content );
	if (
		$autoplay_processor->next_tag( array( 'tag_name' => 'VIDEO' ) ) &&
		null !== $autoplay_processor->get_attribute( 'autoplay' )
	) {
		wp_enqueue_script_module( '@wordpress/block-library/video/view' );

		$autoplay_processor->set_attribute( 'data-wp-interactive', 'core/video' );
		$autoplay_processor->set_attribute( 'data-wp-init', 'callbacks.stopAutoplay' );

		$content = $autoplay_processor->get_updated_html();
	}

	// If the 'id' attribute is not populated for a video attachment, abort.
	if (
		! isset( $attributes['id'] ) ||
		! is_int( $attributes['id'] ) ||
		$attributes['id'] <= 0
	) {
		return $content;
	}

	// If the 'id' attribute wasn't for an attachment, abort.
	if ( get_post_type( $attributes['id'] ) !== 'attachment' ) {
		return $content;
	}

	// Get the width and height metadata for the video, and abort if absent or invalid.
	$metadata = wp_get_attachment_metadata( $attributes['id'] );
	if (
		! isset( $metadata['width'], $metadata['height'] ) ||
		! ( is_int( $metadata['width'] ) && is_int( $metadata['height'] ) ) ||
		! ( $metadata['width'] > 0 && $metadata['height'] > 0 )
	) {
		return $content;
	}

	// Locate the VIDEO tag to add the dimensions.
	$p = new WP_HTML_Tag_Processor( $content );
	if ( ! $p->next_tag( array( 'tag_name' => 'VIDEO' ) ) ) {
		return $content;
	}

	$p->set_attribute( 'width', (string) $metadata['width'] );
	$p->set_attribute( 'height', (string) $metadata['height'] );

	/*
	 * The aspect-ratio style is needed due to an issue with the CSS spec: <https://github.com/w3c/csswg-drafts/issues/7524>.
	 * Note that a style rule using attr() like the following cannot currently be used:
	 *
	 *     .wp-block-video video[width][height] {
	 *         aspect-ratio: attr(width type(<number>)) / attr(height type(<number>));
	 *     }
	 *
	 * This is because this attr() is yet only implemented in Chromium: <https://caniuse.com/css3-attr>.
	 */
	$style = $p->get_attribute( 'style' );
	if ( ! is_string( $style ) ) {
		$style = '';
	}
	$aspect_ratio_style = sprintf( 'aspect-ratio: %d / %d;', $metadata['width'], $metadata['height'] );
	$p->set_attribute( 'style', $aspect_ratio_style . $style );

	return $p->get_updated_html();
}

/**
 * Registers the `core/video` block on server.
 *
 * @since 6.9.0
 */
function register_block_core_video(): void {
	register_block_type_from_metadata(
		__DIR__ . '/video',
		array(
			'render_callback' => 'render_block_core_video',
		)
	);
}
add_action( 'init', 'register_block_core_video' );

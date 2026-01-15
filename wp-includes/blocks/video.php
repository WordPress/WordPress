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
	// if the content lacks any video tag, abort.
	if ( ! str_contains( $content, '<video' ) ) {
		return $content;
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

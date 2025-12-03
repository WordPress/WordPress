<?php
/**
 * Server-side rendering of the `core/media-text` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/media-text` block on server.
 *
 * @since 6.6.0
 *
 * @param array  $attributes The block attributes.
 * @param string $content    The block rendered content.
 *
 * @return string Returns the Media & Text block markup, if useFeaturedImage is true.
 */
function gutenberg_render_block_core_media_text( $attributes, $content ) {
	if ( false === $attributes['useFeaturedImage'] ) {
		return $content;
	}

	if ( in_the_loop() ) {
		update_post_thumbnail_cache();
	}

	$current_featured_image = get_the_post_thumbnail_url();
	if ( ! $current_featured_image ) {
		return $content;
	}

	$has_media_on_right = isset( $attributes['mediaPosition'] ) && 'right' === $attributes['mediaPosition'];
	$image_fill         = isset( $attributes['imageFill'] ) && $attributes['imageFill'];
	$focal_point        = isset( $attributes['focalPoint'] ) ? round( $attributes['focalPoint']['x'] * 100 ) . '% ' . round( $attributes['focalPoint']['y'] * 100 ) . '%' : '50% 50%';
	$unique_id          = 'wp-block-media-text__media-' . wp_unique_id();

	$block_tag_processor = new WP_HTML_Tag_Processor( $content );
	$block_query         = array(
		'tag_name'   => 'div',
		'class_name' => 'wp-block-media-text',
	);

	while ( $block_tag_processor->next_tag( $block_query ) ) {
		if ( $image_fill ) {
			// The markup below does not work with the deprecated `is-image-fill` class.
			$block_tag_processor->remove_class( 'is-image-fill' );
			$block_tag_processor->add_class( 'is-image-fill-element' );
		}
	}

	$content = $block_tag_processor->get_updated_html();

	$media_tag_processor   = new WP_HTML_Tag_Processor( $content );
	$wrapping_figure_query = array(
		'tag_name'   => 'figure',
		'class_name' => 'wp-block-media-text__media',
	);

	if ( $has_media_on_right ) {
		// Loop through all the figure tags and set a bookmark on the last figure tag.
		while ( $media_tag_processor->next_tag( $wrapping_figure_query ) ) {
			$media_tag_processor->set_bookmark( 'last_figure' );
		}
		if ( $media_tag_processor->has_bookmark( 'last_figure' ) ) {
			$media_tag_processor->seek( 'last_figure' );
			// Insert a unique ID to identify the figure tag.
			$media_tag_processor->set_attribute( 'id', $unique_id );
		}
	} else {
		if ( $media_tag_processor->next_tag( $wrapping_figure_query ) ) {
			// Insert a unique ID to identify the figure tag.
			$media_tag_processor->set_attribute( 'id', $unique_id );
		}
	}

	$content = $media_tag_processor->get_updated_html();

	// Add the image tag inside the figure tag, and update the image attributes
	// in order to display the featured image.
	$media_size_slug = isset( $attributes['mediaSizeSlug'] ) ? $attributes['mediaSizeSlug'] : 'full';
	$image_tag       = '<img class="wp-block-media-text__featured_image">';
	$content         = preg_replace(
		'/(<figure\s+id="' . preg_quote( $unique_id, '/' ) . '"\s+class="wp-block-media-text__media"\s*>)/',
		'$1' . $image_tag,
		$content
	);

	$image_tag_processor = new WP_HTML_Tag_Processor( $content );
	if ( $image_tag_processor->next_tag(
		array(
			'tag_name' => 'figure',
			'id'       => $unique_id,
		)
	) ) {
		// The ID is only used to ensure that the correct figure tag is selected,
		// and can now be removed.
		$image_tag_processor->remove_attribute( 'id' );
		if ( $image_tag_processor->next_tag(
			array(
				'tag_name'   => 'img',
				'class_name' => 'wp-block-media-text__featured_image',
			)
		) ) {
			$image_tag_processor->set_attribute( 'src', esc_url( $current_featured_image ) );
			$image_tag_processor->set_attribute( 'class', 'wp-image-' . get_post_thumbnail_id() . ' size-' . $media_size_slug );
			$image_tag_processor->set_attribute( 'alt', trim( strip_tags( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) ) );
			if ( $image_fill ) {
				$image_tag_processor->set_attribute( 'style', 'object-position:' . $focal_point . ';' );
			}

			$content = $image_tag_processor->get_updated_html();
		}
	}

	return $content;
}

/**
 * Registers the `core/media-text` block renderer on server.
 *
 * @since 6.6.0
 */
function gutenberg_register_block_core_media_text() {
	register_block_type_from_metadata(
		__DIR__ . '/media-text',
		array(
			'render_callback' => 'gutenberg_render_block_core_media_text',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_media_text', 20 );

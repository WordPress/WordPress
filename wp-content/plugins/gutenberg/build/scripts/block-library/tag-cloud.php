<?php
/**
 * Server-side rendering of the `core/tag-cloud` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/tag-cloud` block on server.
 *
 * @since 5.2.0
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the tag cloud for selected taxonomy.
 */
function gutenberg_render_block_core_tag_cloud( $attributes ) {
	$smallest_font_size = $attributes['smallestFontSize'];
	$unit               = ( preg_match( '/^[0-9.]+(?P<unit>[a-z%]+)$/i', $smallest_font_size, $m ) ? $m['unit'] : 'pt' );

	$args      = array(
		'echo'       => false,
		'unit'       => $unit,
		'taxonomy'   => $attributes['taxonomy'],
		'show_count' => $attributes['showTagCounts'],
		'number'     => $attributes['numberOfTags'],
		'smallest'   => floatVal( $attributes['smallestFontSize'] ),
		'largest'    => floatVal( $attributes['largestFontSize'] ),
	);
	$tag_cloud = wp_tag_cloud( $args );

	if ( empty( $tag_cloud ) ) {
		// Display placeholder content when there are no tags only in editor.
		if ( wp_is_serving_rest_request() ) {
			$tag_cloud = __( 'There&#8217;s no content to show here yet.' );
		} else {
			return '';
		}
	}

	$wrapper_attributes = get_block_wrapper_attributes();

	return sprintf(
		'<p %1$s>%2$s</p>',
		$wrapper_attributes,
		$tag_cloud
	);
}

/**
 * Registers the `core/tag-cloud` block on server.
 *
 * @since 5.2.0
 */
function gutenberg_register_block_core_tag_cloud() {
	register_block_type_from_metadata(
		__DIR__ . '/tag-cloud',
		array(
			'render_callback' => 'gutenberg_render_block_core_tag_cloud',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_tag_cloud', 20 );

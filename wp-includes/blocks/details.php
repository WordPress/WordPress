<?php
/**
 * Server-side rendering of the `core/details` block.
 *
 * @package WordPress
 */

/**
 * Sets fetchpriority="low" on all IMG tags within the collapsed Details block.
 *
 * Images in a collapsed Details block are hidden until the block is expanded, so they should
 * not compete with any resources in the critical rendering path, such as the LCP element image.
 *
 * @since 7.0.0
 *
 * @param string $block_content The block content.
 * @param array  $block         The full block, including name and attributes.
 * @return string Modified HTML with fetchpriority="low" on all IMG tags when the showContent attribute is false.
 */
function block_core_details_set_img_fetchpriority_low( $block_content, array $block ): string {
	if ( ! is_string( $block_content ) ) {
		return '';
	}

	// If the Details block is open by default, short-circuit to let core add fetchpriority=high if appropriate.
	if ( $block['attrs']['showContent'] ?? false ) {
		return $block_content;
	}

	$tags = new WP_HTML_Tag_Processor( $block_content );
	while ( $tags->next_tag( 'IMG' ) ) {
		$tags->set_attribute( 'fetchpriority', 'low' );
	}
	return $tags->get_updated_html();
}

add_filter( 'render_block_core/details', 'block_core_details_set_img_fetchpriority_low', 10, 2 );

/**
 * Registers the `core/details` block on server.
 *
 * @since 7.0.0
 */
function register_block_core_details() {
	register_block_type_from_metadata( __DIR__ . '/details' );
}
add_action( 'init', 'register_block_core_details' );

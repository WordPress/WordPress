<?php
/**
 * Server-side rendering of the `core/table-of-contents` block.
 *
 * @package WordPress
 */

/**
 * Adds an aria-label to the table of contents block content.
 *
 * @param array  $attributes Attributes of the block being rendered.
 * @param string $content Content of the block being rendered.
 *
 * @return string The content of the block being rendered.
 */
function gutenberg_block_core_table_of_contents_render( $attributes, $content ) {
	if ( ! $content ) {
		return $content;
	}

	// Get the aria-label from block attributes, or fallback to localized default.
	$aria_label = empty( $attributes['ariaLabel'] ) ? __( 'Table of Contents' ) : wp_strip_all_tags( $attributes['ariaLabel'] );

	$p = new WP_HTML_Tag_Processor( $content );

	if ( $p->next_tag( 'nav' ) ) {
		$p->set_attribute( 'aria-label', $aria_label );
	}

	return $p->get_updated_html();
}

/**
 * Registers the `core/table-of-contents` block on the server.
 */
function gutenberg_register_block_core_table_of_contents() {
	register_block_type_from_metadata(
		__DIR__ . '/table-of-contents',
		array(
			'render_callback' => 'gutenberg_block_core_table_of_contents_render',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_table_of_contents', 20 );

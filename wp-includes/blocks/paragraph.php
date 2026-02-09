<?php
/**
 * Server-side rendering of the `core/paragraph` block.
 *
 * @package WordPress
 */

/**
 * Append the `wp-block-paragraph` class before rendering the stored
 * `core/paragraph` block contents.
 *
 * For example, the following block content:
 *  <p class="align-left">Hello World</p>
 *
 * Would be transformed to:
 *  <p class="align-left wp-block-paragraph">Hello World</p>
 *
 * @since 7.0.0
 *
 * @param string $block_content The block content.
 *
 * @return string Filtered block content.
 */
function block_core_paragraph_add_class( $block_content ) {
	if ( ! $block_content ) {
		return $block_content;
	}

	$processor = new WP_HTML_Tag_Processor( $block_content );

	if ( $processor->next_tag( 'p' ) ) {
		$processor->add_class( 'wp-block-paragraph' );
	}

	return $processor->get_updated_html();
}

add_filter( 'render_block_core/paragraph', 'block_core_paragraph_add_class' );

/**
 * Registers the `core/paragraph` block on server.
 *
 * @since 7.0.0
 */
function register_block_core_paragraph() {
	register_block_type_from_metadata( __DIR__ . '/paragraph' );
}
add_action( 'init', 'register_block_core_paragraph' );

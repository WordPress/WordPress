<?php
/**
 * Appending the wp-block-heading to before rendering the stored `core/heading` block contents.
 *
 * @package WordPress
 */

/**
 * Adds a wp-block-heading class to the heading block content.
 *
 * For example, the following block content:
 *  <h2 class="align-left">Hello World</h2>
 *
 * Would be transformed to:
 *  <h2 class="align-left wp-block-heading">Hello World</h2>
 *
 * @since 6.2.0
 *
 * @param array  $attributes Attributes of the block being rendered.
 * @param string $content Content of the block being rendered.
 *
 * @return string The content of the block being rendered.
 */
function block_core_heading_render( $attributes, $content ) {
	if ( ! $content ) {
		return $content;
	}

	$p = new WP_HTML_Tag_Processor( $content );

	$header_tags = array( 'H1', 'H2', 'H3', 'H4', 'H5', 'H6' );
	while ( $p->next_tag() ) {
		if ( in_array( $p->get_tag(), $header_tags, true ) ) {
			$p->add_class( 'wp-block-heading' );
			break;
		}
	}

	return $p->get_updated_html();
}

/**
 * Registers the `core/heading` block on server.
 *
 * @since 6.2.0
 */
function register_block_core_heading() {
	register_block_type_from_metadata(
		__DIR__ . '/heading',
		array(
			'render_callback' => 'block_core_heading_render',
		)
	);
}

add_action( 'init', 'register_block_core_heading' );

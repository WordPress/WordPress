<?php
/**
 * Server-side rendering of the `core/shortcode` block.
 *
 * @package WordPress
 */

/**
 * Performs wpautop() on the shortcode block content.
 *
 * @param array  $attributes The block attributes.
 * @param string $content    The block content.
 *
 * @return string Returns the block content.
 */
function render_block_core_shortcode( $attributes, $content ) {
	return wpautop( $content );
}

/**
 * Registers the `core/shortcode` block on server.
 */
function register_block_core_shortcode() {
<<<<<<< HEAD
	register_block_type_from_metadata(
		__DIR__ . '/shortcode',
=======
	register_block_type(
		'core/shortcode',
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
		array(
			'render_callback' => 'render_block_core_shortcode',
		)
	);
}
<<<<<<< HEAD
=======

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
add_action( 'init', 'register_block_core_shortcode' );

<?php
/**
 * Server-side rendering of the `core/accordion` block.
 *
 * @package WordPress
 * @since 6.9.0
 *
 * @param array $attributes The block attributes.
 * @param string $content The block content.
 *
 * @return string Returns the updated markup.
 */
function render_block_core_accordion( $attributes, $content ) {
	if ( ! $content ) {
		return $content;
	}

	$p         = new WP_HTML_Tag_Processor( $content );
	$autoclose = $attributes['autoclose'] ? 'true' : 'false';

	if ( $p->next_tag( array( 'class_name' => 'wp-block-accordion' ) ) ) {
		$p->set_attribute( 'data-wp-interactive', 'core/accordion' );
		$p->set_attribute( 'data-wp-context', '{ "autoclose": ' . $autoclose . ', "accordionItems": [] }' );

		// Only modify content if directives have been set.
		$content = $p->get_updated_html();
	}

	return $content;
}

/**
 * Registers the `core/accordion` block on server.
 *
 * @since 6.9.0
 */
function register_block_core_accordion() {
	register_block_type_from_metadata(
		__DIR__ . '/accordion',
		array(
			'render_callback' => 'render_block_core_accordion',
		)
	);
}
add_action( 'init', 'register_block_core_accordion' );

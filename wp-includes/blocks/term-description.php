<?php
/**
 * Server-side rendering of the `core/term-description` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/term-description` block on the server.
 *
 * @since 5.9.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the description of the current taxonomy term, if available
 */
function render_block_core_term_description( $attributes, $content, $block ) {
	$term_description = '';

	// Get term from context or from the current query.
	if ( isset( $block->context['termId'] ) && isset( $block->context['taxonomy'] ) ) {
		$term = get_term( $block->context['termId'], $block->context['taxonomy'] );
		if ( $term && ! is_wp_error( $term ) ) {
			$term_description = $term->description;
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term_description = term_description();
	}

	if ( empty( $term_description ) ) {
		return '';
	}

	$classes = array();
	if ( isset( $attributes['textAlign'] ) ) {
		$classes[] = 'has-text-align-' . $attributes['textAlign'];
	}
	if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
		$classes[] = 'has-link-color';
	}
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

	return '<div ' . $wrapper_attributes . '>' . $term_description . '</div>';
}

/**
 * Registers the `core/term-description` block on the server.
 *
 * @since 5.9.0
 */
function register_block_core_term_description() {
	register_block_type_from_metadata(
		__DIR__ . '/term-description',
		array(
			'render_callback' => 'render_block_core_term_description',
		)
	);
}
add_action( 'init', 'register_block_core_term_description' );

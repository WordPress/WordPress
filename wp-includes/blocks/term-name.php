<?php
/**
 * Server-side rendering of the `core/term-name` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/term-name` block on the server.
 *
 * @since 6.9.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the name of the current taxonomy term wrapped inside a heading tag.
 */
function render_block_core_term_name( $attributes, $content, $block ) {
	$term_name = '';

	// Get term from context or from the current query.
	if ( isset( $block->context['termId'] ) && isset( $block->context['taxonomy'] ) ) {
		$term = get_term( $block->context['termId'], $block->context['taxonomy'] );
	} else {
		$term = get_queried_object();
		if ( ! $term instanceof WP_Term ) {
			$term = null;
		}
	}

	if ( ! $term || is_wp_error( $term ) ) {
		return '';
	}

	$term_name = $term->name;
	$level     = isset( $attributes['level'] ) ? $attributes['level'] : 0;
	$tag_name  = 0 === $level ? 'p' : 'h' . (int) $level;

	if ( isset( $attributes['isLink'] ) && $attributes['isLink'] ) {
		$term_link = get_term_link( $term );
		if ( ! is_wp_error( $term_link ) ) {
			$term_name = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( $term_link ),
				$term_name
			);
		}
	}

	$classes = array();
	if ( isset( $attributes['textAlign'] ) ) {
		$classes[] = 'has-text-align-' . $attributes['textAlign'];
	}
	if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
		$classes[] = 'has-link-color';
	}
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

	return sprintf(
		'<%1$s %2$s>%3$s</%1$s>',
		$tag_name,
		$wrapper_attributes,
		$term_name
	);
}

/**
 * Registers the `core/term-name` block on the server.
 *
 * @since 6.9.0
 */
function register_block_core_term_name() {
	register_block_type_from_metadata(
		__DIR__ . '/term-name',
		array(
			'render_callback' => 'render_block_core_term_name',
		)
	);
}
add_action( 'init', 'register_block_core_term_name' );

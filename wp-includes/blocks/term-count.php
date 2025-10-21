<?php
/**
 * Server-side rendering of the `core/term-count` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/term-count` block on the server.
 *
 * @since 6.9.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the count of the current taxonomy term wrapped inside a heading tag.
 */
function render_block_core_term_count( $attributes, $content, $block ) {
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

	$term_count = $term->count;

	// Format the term count based on bracket type.
	switch ( $attributes['bracketType'] ) {
		case 'none':
			// No formatting needed.
			break;
		case 'round':
			$term_count = "({$term_count})";
			break;
		case 'square':
			$term_count = "[{$term_count}]";
			break;
		case 'curly':
			$term_count = "{{$term_count}}";
			break;
		case 'angle':
			$term_count = "<{$term_count}>";
			break;
		default:
			// Default to no formatting for unknown types.
			break;
	}

	$wrapper_attributes = get_block_wrapper_attributes();

	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		$term_count
	);
}

/**
 * Registers the `core/term-count` block on the server.
 *
 * @since 6.9.0
 */
function register_block_core_term_count() {
	register_block_type_from_metadata(
		__DIR__ . '/term-count',
		array(
			'render_callback' => 'render_block_core_term_count',
		)
	);
}
add_action( 'init', 'register_block_core_term_count' );

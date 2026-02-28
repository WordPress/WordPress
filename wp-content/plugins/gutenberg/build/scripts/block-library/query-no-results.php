<?php
/**
 * Server-side rendering of the `core/query-no-results` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/query-no-results` block on the server.
 *
 * @since 6.0.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the wrapper for the no results block.
 */
function gutenberg_render_block_core_query_no_results( $attributes, $content, $block ) {
	if ( empty( trim( $content ) ) ) {
		return '';
	}

	$page_key = isset( $block->context['queryId'] ) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
	$page     = empty( $_GET[ $page_key ] ) ? 1 : (int) $_GET[ $page_key ];

	// Override the custom query with the global query if needed.
	$use_global_query = ( isset( $block->context['query']['inherit'] ) && $block->context['query']['inherit'] );
	if ( $use_global_query ) {
		global $wp_query;
		$query = $wp_query;
	} else {
		$query_args = build_query_vars_from_query_block( $block, $page );
		$query      = new WP_Query( $query_args );
	}

	if ( $query->post_count > 0 ) {
		return '';
	}

	$classes            = ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) ? 'has-link-color' : '';
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $classes ) );
	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		$content
	);
}

/**
 * Registers the `core/query-no-results` block on the server.
 *
 * @since 6.0.0
 */
function gutenberg_register_block_core_query_no_results() {
	register_block_type_from_metadata(
		__DIR__ . '/query-no-results',
		array(
			'render_callback' => 'gutenberg_render_block_core_query_no_results',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_query_no_results', 20 );

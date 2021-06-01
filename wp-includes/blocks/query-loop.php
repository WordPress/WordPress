<?php
/**
 * Server-side rendering of the `core/query-loop` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/query-loop` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the output of the query, structured using the layout defined by the block's inner blocks.
 */
function render_block_core_query_loop( $attributes, $content, $block ) {
	$page_key = isset( $block->context['queryId'] ) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
	$page     = empty( $_GET[ $page_key ] ) ? 1 : (int) $_GET[ $page_key ];

	$query_args = build_query_vars_from_query_block( $block, $page );
	// Override the custom query with the global query if needed.
	$use_global_query = ( isset( $block->context['query']['inherit'] ) && $block->context['query']['inherit'] );
	if ( $use_global_query ) {
		global $wp_query;
		if ( $wp_query && isset( $wp_query->query_vars ) && is_array( $wp_query->query_vars ) ) {
			// Unset `offset` because if is set, $wp_query overrides/ignores the paged parameter and breaks pagination.
			unset( $query_args['offset'] );
			$query_args = wp_parse_args( $wp_query->query_vars, $query_args );

			if ( empty( $query_args['post_type'] ) && is_singular() ) {
				$query_args['post_type'] = get_post_type( get_the_ID() );
			}
		}
	}

	$query = new WP_Query( $query_args );

	if ( ! $query->have_posts() ) {
		return '';
	}

	$classnames = '';
	if ( isset( $block->context['displayLayout'] ) && isset( $block->context['query'] ) ) {
		if ( isset( $block->context['displayLayout']['type'] ) && 'flex' === $block->context['displayLayout']['type'] ) {
			$classnames = "is-flex-container columns-{$block->context['displayLayout']['columns']}";
		}
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $classnames ) );

	$content = '';
	while ( $query->have_posts() ) {
		$query->the_post();
		$block_content = (
			new WP_Block(
				$block->parsed_block,
				array(
					'postType' => get_post_type(),
					'postId'   => get_the_ID(),
				)
			)
		)->render( array( 'dynamic' => false ) );
		$content      .= "<li>{$block_content}</li>";
	}

	wp_reset_postdata();

	return sprintf(
		'<ul %1$s>%2$s</ul>',
		$wrapper_attributes,
		$content
	);
}

/**
 * Registers the `core/query-loop` block on the server.
 */
function register_block_core_query_loop() {
	register_block_type_from_metadata(
		__DIR__ . '/query-loop',
		array(
			'render_callback'   => 'render_block_core_query_loop',
			'skip_inner_blocks' => true,
		)
	);
}
add_action( 'init', 'register_block_core_query_loop' );

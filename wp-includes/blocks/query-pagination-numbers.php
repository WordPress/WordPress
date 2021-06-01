<?php
/**
 * Server-side rendering of the `core/query-pagination-numbers` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/query-pagination-numbers` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the pagination numbers for the Query.
 */
function render_block_core_query_pagination_numbers( $attributes, $content, $block ) {
	$page_key = isset( $block->context['queryId'] ) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
	$page     = empty( $_GET[ $page_key ] ) ? 1 : (int) $_GET[ $page_key ];
	$max_page = isset( $block->context['query']['pages'] ) ? (int) $block->context['query']['pages'] : 0;

	$wrapper_attributes = get_block_wrapper_attributes();
	$content            = '';
	global $wp_query;
	if ( isset( $block->context['query']['inherit'] ) && $block->context['query']['inherit'] ) {
		// Take into account if we have set a bigger `max page`
		// than what the query has.
		$total         = ! $max_page || $max_page > $wp_query->max_num_pages ? $wp_query->max_num_pages : $max_page;
		$paginate_args = array(
			'prev_next' => false,
			'total'     => $total,
		);
		$content       = paginate_links( $paginate_args );
	} else {
		$block_query = new WP_Query( build_query_vars_from_query_block( $block, $page ) );
		// `paginate_links` works with the global $wp_query, so we have to
		// temporarily switch it with our custom query.
		$prev_wp_query = $wp_query;
		$wp_query      = $block_query;
		$total         = ! $max_page || $max_page > $wp_query->max_num_pages ? $wp_query->max_num_pages : $max_page;
		$paginate_args = array(
			'base'      => '%_%',
			'format'    => "?$page_key=%#%",
			'current'   => max( 1, $page ),
			'total'     => $total,
			'prev_next' => false,
		);
		// We still need to preserve `paged` query param if exists, as is used
		// for Queries that inherit from global context.
		$paged = empty( $_GET['paged'] ) ? null : (int) $_GET['paged'];
		if ( $paged ) {
			$paginate_args['add_args'] = array( 'paged' => $paged );
		}
		$content = paginate_links( $paginate_args );
		wp_reset_postdata(); // Restore original Post Data.
		$wp_query = $prev_wp_query;
	}
	if ( empty( $content ) ) {
		return '';
	}
	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		$content
	);
}

/**
 * Registers the `core/query-pagination-numbers` block on the server.
 */
function register_block_core_query_pagination_numbers() {
	register_block_type_from_metadata(
		__DIR__ . '/query-pagination-numbers',
		array(
			'render_callback' => 'render_block_core_query_pagination_numbers',
		)
	);
}
add_action( 'init', 'register_block_core_query_pagination_numbers' );

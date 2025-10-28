<?php
/**
 * Server-side rendering of the `core/query-pagination-next` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/query-pagination-next` block on the server.
 *
 * @since 5.8.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the next posts link for the query pagination.
 */
function render_block_core_query_pagination_next( $attributes, $content, $block ) {
	$page_key            = isset( $block->context['queryId'] ) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
	$enhanced_pagination = isset( $block->context['enhancedPagination'] ) && $block->context['enhancedPagination'];
	$page                = empty( $_GET[ $page_key ] ) ? 1 : (int) $_GET[ $page_key ];
	$max_page            = isset( $block->context['query']['pages'] ) ? (int) $block->context['query']['pages'] : 0;

	$wrapper_attributes = get_block_wrapper_attributes();
	$show_label         = isset( $block->context['showLabel'] ) ? (bool) $block->context['showLabel'] : true;
	$default_label      = __( 'Next Page' );
	$label_text         = isset( $attributes['label'] ) && ! empty( $attributes['label'] ) ? esc_html( $attributes['label'] ) : $default_label;
	$label              = $show_label ? $label_text : '';
	$pagination_arrow   = get_query_pagination_arrow( $block, true );

	if ( ! $label ) {
		$wrapper_attributes .= ' aria-label="' . $label_text . '"';
	}
	if ( $pagination_arrow ) {
		$label .= $pagination_arrow;
	}
	$content = '';

	// Check if the pagination is for Query that inherits the global context.
	if ( isset( $block->context['query']['inherit'] ) && $block->context['query']['inherit'] ) {
		$filter_link_attributes = static function () use ( $wrapper_attributes ) {
			return $wrapper_attributes;
		};
		add_filter( 'next_posts_link_attributes', $filter_link_attributes );
		// Take into account if we have set a bigger `max page`
		// than what the query has.
		global $wp_query;
		if ( $max_page > $wp_query->max_num_pages ) {
			$max_page = $wp_query->max_num_pages;
		}
		$content = get_next_posts_link( $label, $max_page );
		remove_filter( 'next_posts_link_attributes', $filter_link_attributes );
	} elseif ( ! $max_page || $max_page > $page ) {
		$custom_query           = new WP_Query( build_query_vars_from_query_block( $block, $page ) );
		$custom_query_max_pages = (int) $custom_query->max_num_pages;
		if ( $custom_query_max_pages && $custom_query_max_pages !== $page ) {
			$content = sprintf(
				'<a href="%1$s" %2$s>%3$s</a>',
				esc_url( add_query_arg( $page_key, $page + 1 ) ),
				$wrapper_attributes,
				$label
			);
		}
		wp_reset_postdata(); // Restore original Post Data.
	}

	if ( $enhanced_pagination && isset( $content ) ) {
		$p = new WP_HTML_Tag_Processor( $content );
		if ( $p->next_tag(
			array(
				'tag_name'   => 'a',
				'class_name' => 'wp-block-query-pagination-next',
			)
		) ) {
			$p->set_attribute( 'data-wp-key', 'query-pagination-next' );
			$p->set_attribute( 'data-wp-on--click', 'core/query::actions.navigate' );
			$p->set_attribute( 'data-wp-on--mouseenter', 'core/query::actions.prefetch' );
			$p->set_attribute( 'data-wp-watch', 'core/query::callbacks.prefetch' );
			$content = $p->get_updated_html();
		}
	}

	return $content;
}

/**
 * Registers the `core/query-pagination-next` block on the server.
 *
 * @since 5.8.0
 */
function register_block_core_query_pagination_next() {
	register_block_type_from_metadata(
		__DIR__ . '/query-pagination-next',
		array(
			'render_callback' => 'render_block_core_query_pagination_next',
		)
	);
}
add_action( 'init', 'register_block_core_query_pagination_next' );

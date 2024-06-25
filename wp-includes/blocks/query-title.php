<?php
/**
 * Server-side rendering of the `core/query-title` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/query-title` block on the server.
 * For now it only supports Archive title,
 * using queried object information
 *
 * @since 5.8.0
 *
 * @param array $attributes Block attributes.
 *
 * @return string Returns the query title based on the queried object.
 */
function render_block_core_query_title( $attributes ) {
	$type       = isset( $attributes['type'] ) ? $attributes['type'] : null;
	$is_archive = is_archive();
	$is_search  = is_search();
	if ( ! $type ||
		( 'archive' === $type && ! $is_archive ) ||
		( 'search' === $type && ! $is_search )
		) {
		return '';
	}
	$title = '';
	if ( $is_archive ) {
		$show_prefix = isset( $attributes['showPrefix'] ) ? $attributes['showPrefix'] : true;
		if ( ! $show_prefix ) {
			add_filter( 'get_the_archive_title_prefix', '__return_empty_string', 1 );
			$title = get_the_archive_title();
			remove_filter( 'get_the_archive_title_prefix', '__return_empty_string', 1 );
		} else {
			$title = get_the_archive_title();
		}
	}
	if ( $is_search ) {
		$title = __( 'Search results' );

		if ( isset( $attributes['showSearchTerm'] ) && $attributes['showSearchTerm'] ) {
			$title = sprintf(
				/* translators: %s is the search term. */
				__( 'Search results for: "%s"' ),
				get_search_query()
			);
		}
	}

	$tag_name           = isset( $attributes['level'] ) ? 'h' . (int) $attributes['level'] : 'h1';
	$align_class_name   = empty( $attributes['textAlign'] ) ? '' : "has-text-align-{$attributes['textAlign']}";
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $align_class_name ) );
	return sprintf(
		'<%1$s %2$s>%3$s</%1$s>',
		$tag_name,
		$wrapper_attributes,
		$title
	);
}

/**
 * Registers the `core/query-title` block on the server.
 *
 * @since 5.8.0
 */
function register_block_core_query_title() {
	register_block_type_from_metadata(
		__DIR__ . '/query-title',
		array(
			'render_callback' => 'render_block_core_query_title',
		)
	);
}
add_action( 'init', 'register_block_core_query_title' );

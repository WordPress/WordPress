<?php
/**
 * Server-side rendering of the `core/post-template` block.
 *
 * @package WordPress
 */

/**
 * Determines whether a block list contains a block that uses the featured image.
 *
 * @since 6.0.0
 *
 * @param WP_Block_List $inner_blocks Inner block instance.
 *
 * @return bool Whether the block list contains a block that uses the featured image.
 */
function gutenberg_block_core_post_template_uses_featured_image( $inner_blocks ) {
	foreach ( $inner_blocks as $block ) {
		if ( 'core/post-featured-image' === $block->name ) {
			return true;
		}
		if (
			'core/cover' === $block->name &&
			! empty( $block->attributes['useFeaturedImage'] )
		) {
			return true;
		}
		if ( $block->inner_blocks && gutenberg_block_core_post_template_uses_featured_image( $block->inner_blocks ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Renders the `core/post-template` block on the server.
 *
 * @since 6.3.0 Changed render_block_context priority to `1`.
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the output of the query, structured using the layout defined by the block's inner blocks.
 */
function gutenberg_render_block_core_post_template( $attributes, $content, $block ) {
	$page_key            = isset( $block->context['queryId'] ) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
	$enhanced_pagination = isset( $block->context['enhancedPagination'] ) && $block->context['enhancedPagination'];
	$page                = empty( $_GET[ $page_key ] ) ? 1 : (int) $_GET[ $page_key ];

	// Use global query if needed.
	$use_global_query = ( isset( $block->context['query']['inherit'] ) && $block->context['query']['inherit'] );
	if ( $use_global_query ) {
		global $wp_query;

		/*
		 * If already in the main query loop, duplicate the query instance to not tamper with the main instance.
		 * Since this is a nested query, it should start at the beginning, therefore rewind posts.
		 * Otherwise, the main query loop has not started yet and this block is responsible for doing so.
		 */
		if ( in_the_loop() ) {
			$query = clone $wp_query;
			$query->rewind_posts();
		} else {
			$query = $wp_query;
		}
	} else {
		$query_args = build_query_vars_from_query_block( $block, $page );
		$query      = new WP_Query( $query_args );
	}

	if ( ! $query->have_posts() ) {
		return '';
	}

	if ( gutenberg_block_core_post_template_uses_featured_image( $block->inner_blocks ) ) {
		update_post_thumbnail_cache( $query );
	}

	$classnames = '';
	if ( isset( $block->context['displayLayout'] ) && isset( $block->context['query'] ) ) {
		if ( isset( $block->context['displayLayout']['type'] ) && 'flex' === $block->context['displayLayout']['type'] ) {
			$classnames = "is-flex-container columns-{$block->context['displayLayout']['columns']}";
		}
	}
	if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
		$classnames .= ' has-link-color';
	}

	// Ensure backwards compatibility by flagging the number of columns via classname when using grid layout.
	if ( isset( $attributes['layout']['type'] ) && 'grid' === $attributes['layout']['type'] && ! empty( $attributes['layout']['columnCount'] ) ) {
		$classnames .= ' ' . sanitize_title( 'columns-' . $attributes['layout']['columnCount'] );
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => trim( $classnames ) ) );

	$content = '';
	while ( $query->have_posts() ) {
		$query->the_post();

		// Get an instance of the current Post Template block.
		$block_instance = $block->parsed_block;

		// Set the block name to one that does not correspond to an existing registered block.
		// This ensures that for the inner instances of the Post Template block, we do not render any block supports.
		$block_instance['blockName'] = 'core/null';

		$post_id              = get_the_ID();
		$post_type            = get_post_type();
		$filter_block_context = static function ( $context ) use ( $post_id, $post_type ) {
			$context['postType'] = $post_type;
			$context['postId']   = $post_id;
			return $context;
		};

		// Use an early priority to so that other 'render_block_context' filters have access to the values.
		add_filter( 'render_block_context', $filter_block_context, 1 );
		// Render the inner blocks of the Post Template block with `dynamic` set to `false` to prevent calling
		// `render_callback` and ensure that no wrapper markup is included.
		$block_content = ( new WP_Block( $block_instance ) )->render( array( 'dynamic' => false ) );
		remove_filter( 'render_block_context', $filter_block_context, 1 );

		// Wrap the render inner blocks in a `li` element with the appropriate post classes.
		$post_classes = implode( ' ', get_post_class( 'wp-block-post' ) );

		$inner_block_directives = $enhanced_pagination ? ' data-wp-key="post-template-item-' . $post_id . '"' : '';

		$content .= '<li' . $inner_block_directives . ' class="' . esc_attr( $post_classes ) . '">' . $block_content . '</li>';
	}

	/*
	 * Use this function to restore the context of the template tags
	 * from a secondary query loop back to the main query loop.
	 * Since we use two custom loops, it's safest to always restore.
	*/
	wp_reset_postdata();

	return sprintf(
		'<ul %1$s>%2$s</ul>',
		$wrapper_attributes,
		$content
	);
}

/**
 * Registers the `core/post-template` block on the server.
 *
 * @since 5.8.0
 */
function gutenberg_register_block_core_post_template() {
	register_block_type_from_metadata(
		__DIR__ . '/post-template',
		array(
			'render_callback'   => 'gutenberg_render_block_core_post_template',
			'skip_inner_blocks' => true,
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_post_template', 20 );

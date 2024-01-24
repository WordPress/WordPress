<?php
/**
 * Server-side rendering of the `core/query` block.
 *
 * @package WordPress
 */

/**
 * Modifies the static `core/query` block on the server.
 *
 * @since 6.4.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      The block instance.
 *
 * @return string Returns the modified output of the query block.
 */
function render_block_core_query( $attributes, $content, $block ) {
	if ( $attributes['enhancedPagination'] && isset( $attributes['queryId'] ) ) {
		$p = new WP_HTML_Tag_Processor( $content );
		if ( $p->next_tag() ) {
			// Add the necessary directives.
			$p->set_attribute( 'data-wp-interactive', true );
			$p->set_attribute( 'data-wp-navigation-id', 'query-' . $attributes['queryId'] );
			// Use context to send translated strings.
			$p->set_attribute(
				'data-wp-context',
				wp_json_encode(
					array(
						'core' => array(
							'query' => array(
								'loadingText' => __( 'Loading page, please wait.' ),
								'loadedText'  => __( 'Page Loaded.' ),
							),
						),
					),
					JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP
				)
			);
			$content = $p->get_updated_html();

			// Mark the block as interactive.
			$block->block_type->supports['interactivity'] = true;

			// Add a div to announce messages using `aria-live`.
			$html_tag = 'div';
			if ( ! empty( $attributes['tagName'] ) ) {
				$html_tag = esc_attr( $attributes['tagName'] );
			}
			$last_tag_position = strripos( $content, '</' . $html_tag . '>' );
			$content           = substr_replace(
				$content,
				'<div
					class="screen-reader-text"
					aria-live="polite"
					data-wp-text="context.core.query.message"
				></div>
				<div
					class="wp-block-query__enhanced-pagination-animation"
					data-wp-class--start-animation="selectors.core.query.startAnimation"
					data-wp-class--finish-animation="selectors.core.query.finishAnimation"
				></div>',
				$last_tag_position,
				0
			);
		}
	}

	$view_asset = 'wp-block-query-view';
	if ( ! wp_script_is( $view_asset ) ) {
		$script_handles = $block->block_type->view_script_handles;
		// If the script is not needed, and it is still in the `view_script_handles`, remove it.
		if (
			( ! $attributes['enhancedPagination'] || ! isset( $attributes['queryId'] ) )
			&& in_array( $view_asset, $script_handles, true )
		) {
			$block->block_type->view_script_handles = array_diff( $script_handles, array( $view_asset ) );
		}
		// If the script is needed, but it was previously removed, add it again.
		if ( $attributes['enhancedPagination'] && isset( $attributes['queryId'] ) && ! in_array( $view_asset, $script_handles, true ) ) {
			$block->block_type->view_script_handles = array_merge( $script_handles, array( $view_asset ) );
		}
	}

	$style_asset = 'wp-block-query';
	if ( ! wp_style_is( $style_asset ) ) {
		$style_handles = $block->block_type->style_handles;
		// If the styles are not needed, and they are still in the `style_handles`, remove them.
		if (
			( ! $attributes['enhancedPagination'] || ! isset( $attributes['queryId'] ) )
			&& in_array( $style_asset, $style_handles, true )
		) {
			$block->block_type->style_handles = array_diff( $style_handles, array( $style_asset ) );
		}
		// If the styles are needed, but they were previously removed, add them again.
		if ( $attributes['enhancedPagination'] && isset( $attributes['queryId'] ) && ! in_array( $style_asset, $style_handles, true ) ) {
			$block->block_type->style_handles = array_merge( $style_handles, array( $style_asset ) );
		}
	}

	return $content;
}

/**
 * Ensure that the view script has the `wp-interactivity` dependency.
 *
 * @since 6.4.0
 *
 * @global WP_Scripts $wp_scripts
 */
function block_core_query_ensure_interactivity_dependency() {
	global $wp_scripts;
	if (
		isset( $wp_scripts->registered['wp-block-query-view'] ) &&
		! in_array( 'wp-interactivity', $wp_scripts->registered['wp-block-query-view']->deps, true )
	) {
		$wp_scripts->registered['wp-block-query-view']->deps[] = 'wp-interactivity';
	}
}

add_action( 'wp_print_scripts', 'block_core_query_ensure_interactivity_dependency' );

/**
 * Registers the `core/query` block on the server.
 */
function register_block_core_query() {
	register_block_type_from_metadata(
		__DIR__ . '/query',
		array(
			'render_callback' => 'render_block_core_query',
		)
	);
}
add_action( 'init', 'register_block_core_query' );

/**
 * Traverse the tree of blocks looking for any plugin block (i.e., a block from
 * an installed plugin) inside a Query block with the enhanced pagination
 * enabled. If at least one is found, the enhanced pagination is effectively
 * disabled to prevent any potential incompatibilities.
 *
 * @since 6.4.0
 *
 * @param array $parsed_block The block being rendered.
 * @return string Returns the parsed block, unmodified.
 */
function block_core_query_disable_enhanced_pagination( $parsed_block ) {
	static $enhanced_query_stack   = array();
	static $dirty_enhanced_queries = array();
	static $render_query_callback  = null;

	$block_name = $parsed_block['blockName'];

	if (
		'core/query' === $block_name &&
		isset( $parsed_block['attrs']['enhancedPagination'] ) &&
		true === $parsed_block['attrs']['enhancedPagination'] &&
		isset( $parsed_block['attrs']['queryId'] )
	) {
		$enhanced_query_stack[] = $parsed_block['attrs']['queryId'];

		if ( ! isset( $render_query_callback ) ) {
			/**
			 * Filter that disables the enhanced pagination feature during block
			 * rendering when a plugin block has been found inside. It does so
			 * by adding an attribute called `data-wp-navigation-disabled` which
			 * is later handled by the front-end logic.
			 *
			 * @param string   $content  The block content.
			 * @param array    $block    The full block, including name and attributes.
			 * @return string Returns the modified output of the query block.
			 */
			$render_query_callback = static function ( $content, $block ) use ( &$enhanced_query_stack, &$dirty_enhanced_queries, &$render_query_callback ) {
				$has_enhanced_pagination =
					isset( $block['attrs']['enhancedPagination'] ) &&
					true === $block['attrs']['enhancedPagination'] &&
					isset( $block['attrs']['queryId'] );

				if ( ! $has_enhanced_pagination ) {
					return $content;
				}

				if ( isset( $dirty_enhanced_queries[ $block['attrs']['queryId'] ] ) ) {
					$p = new WP_HTML_Tag_Processor( $content );
					if ( $p->next_tag() ) {
						$p->set_attribute( 'data-wp-navigation-disabled', 'true' );
					}
					$content = $p->get_updated_html();
					$dirty_enhanced_queries[ $block['attrs']['queryId'] ] = null;
				}

				array_pop( $enhanced_query_stack );

				if ( empty( $enhanced_query_stack ) ) {
					remove_filter( 'render_block_core/query', $render_query_callback );
					$render_query_callback = null;
				}

				return $content;
			};

			add_filter( 'render_block_core/query', $render_query_callback, 10, 2 );
		}
	} elseif (
		! empty( $enhanced_query_stack ) &&
		isset( $block_name ) &&
		( ! str_starts_with( $block_name, 'core/' ) || 'core/post-content' === $block_name )
	) {
		foreach ( $enhanced_query_stack as $query_id ) {
			$dirty_enhanced_queries[ $query_id ] = true;
		}
	}

	return $parsed_block;
}

add_filter( 'render_block_data', 'block_core_query_disable_enhanced_pagination', 10, 1 );

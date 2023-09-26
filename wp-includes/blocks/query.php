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
 * @param array  $attributes Block attributes.
 * @param string $content    Block default content.
 * @param string $block      Block instance.
 *
 * @return string Returns the modified output of the query block.
 */
function render_block_core_query( $attributes, $content, $block ) {
	if ( $attributes['enhancedPagination'] ) {
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
					)
				)
			);
			$content = $p->get_updated_html();

			// Mark the block as interactive.
			$block->block_type->supports['interactivity'] = true;

			// Add a div to announce messages using `aria-live`.
			$last_div_position = strripos( $content, '</div>' );
			$content           = substr_replace(
				$content,
				'<div
					class="wp-block-query__enhanced-pagination-navigation-announce"
					aria-live="polite"
					data-wp-text="context.core.query.message"
				></div>
				<div
					class="wp-block-query__enhanced-pagination-animation"
					data-wp-class--start-animation="selectors.core.query.startAnimation"
					data-wp-class--finish-animation="selectors.core.query.finishAnimation"
				></div>',
				$last_div_position,
				0
			);
		}
	}

	$view_asset = 'wp-block-query-view';
	if ( ! wp_script_is( $view_asset ) ) {
		$script_handles = $block->block_type->view_script_handles;
		// If the script is not needed, and it is still in the `view_script_handles`, remove it.
		if ( ! $attributes['enhancedPagination'] && in_array( $view_asset, $script_handles, true ) ) {
			$block->block_type->view_script_handles = array_diff( $script_handles, array( $view_asset ) );
		}
		// If the script is needed, but it was previously removed, add it again.
		if ( $attributes['enhancedPagination'] && ! in_array( $view_asset, $script_handles, true ) ) {
			$block->block_type->view_script_handles = array_merge( $script_handles, array( $view_asset ) );
		}
	}

	$style_asset = 'wp-block-query';
	if ( ! wp_style_is( $style_asset ) ) {
		$style_handles = $block->block_type->style_handles;
		// If the styles are not needed, and they are still in the `style_handles`, remove them.
		if ( ! $attributes['enhancedPagination'] && in_array( $style_asset, $style_handles, true ) ) {
			$block->block_type->style_handles = array_diff( $style_handles, array( $style_asset ) );
		}
		// If the styles are needed, but they were previously removed, add them again.
		if ( $attributes['enhancedPagination'] && ! in_array( $style_asset, $style_handles, true ) ) {
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

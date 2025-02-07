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
	$is_interactive = isset( $attributes['enhancedPagination'] )
		&& true === $attributes['enhancedPagination']
		&& isset( $attributes['queryId'] );

	// Enqueue the script module and add the necessary directives if the block is
	// interactive.
	if ( $is_interactive ) {
		wp_enqueue_script_module( '@wordpress/block-library/query/view' );

		$p = new WP_HTML_Tag_Processor( $content );
		if ( $p->next_tag() ) {
			// Add the necessary directives.
			$p->set_attribute( 'data-wp-interactive', 'core/query' );
			$p->set_attribute( 'data-wp-router-region', 'query-' . $attributes['queryId'] );
			$p->set_attribute( 'data-wp-context', '{}' );
			$p->set_attribute( 'data-wp-key', $attributes['queryId'] );
			$content = $p->get_updated_html();
		}
	}

	// Add the styles to the block type if the block is interactive and remove
	// them if it's not.
	$style_asset = 'wp-block-query';
	if ( ! wp_style_is( $style_asset ) ) {
		$style_handles = $block->block_type->style_handles;
		// If the styles are not needed, and they are still in the `style_handles`, remove them.
		if ( ! $is_interactive && in_array( $style_asset, $style_handles, true ) ) {
			$block->block_type->style_handles = array_diff( $style_handles, array( $style_asset ) );
		}
		// If the styles are needed, but they were previously removed, add them again.
		if ( $is_interactive && ! in_array( $style_asset, $style_handles, true ) ) {
			$block->block_type->style_handles = array_merge( $style_handles, array( $style_asset ) );
		}
	}

	return $content;
}

/**
 * Registers the `core/query` block on the server.
 *
 * @since 5.8.0
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
 * @return array Returns the parsed block, unmodified.
 */
function block_core_query_disable_enhanced_pagination( $parsed_block ) {
	static $enhanced_query_stack   = array();
	static $dirty_enhanced_queries = array();
	static $render_query_callback  = null;

	$block_name              = $parsed_block['blockName'];
	$block_type              = WP_Block_Type_Registry::get_instance()->get_registered( $block_name );
	$has_enhanced_pagination = isset( $parsed_block['attrs']['enhancedPagination'] ) && true === $parsed_block['attrs']['enhancedPagination'] && isset( $parsed_block['attrs']['queryId'] );
	/*
	 * Client side navigation can be true in two states:
	 *  - supports.interactivity = true;
	 *  - supports.interactivity.clientNavigation = true;
	 */
	$supports_client_navigation = ( isset( $block_type->supports['interactivity']['clientNavigation'] ) && true === $block_type->supports['interactivity']['clientNavigation'] )
		|| ( isset( $block_type->supports['interactivity'] ) && true === $block_type->supports['interactivity'] );

	if ( 'core/query' === $block_name && $has_enhanced_pagination ) {
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
				$has_enhanced_pagination = isset( $block['attrs']['enhancedPagination'] ) && true === $block['attrs']['enhancedPagination'] && isset( $block['attrs']['queryId'] );

				if ( ! $has_enhanced_pagination ) {
					return $content;
				}

				if ( isset( $dirty_enhanced_queries[ $block['attrs']['queryId'] ] ) ) {
					// Disable navigation in the router store config.
					wp_interactivity_config( 'core/router', array( 'clientNavigationDisabled' => true ) );
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
		( ! $supports_client_navigation )
	) {
		foreach ( $enhanced_query_stack as $query_id ) {
			$dirty_enhanced_queries[ $query_id ] = true;
		}
	}

	return $parsed_block;
}

add_filter( 'render_block_data', 'block_core_query_disable_enhanced_pagination', 10, 1 );

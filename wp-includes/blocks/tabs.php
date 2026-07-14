<?php
/**
 * Tabs Block
 *
 * @package WordPress
 */

/**
 * Extract tabs list from tab-panel innerblocks.
 *
 * @since 7.1.0
 *
 * @param array  $innerblocks Parsed inner blocks of tabs block.
 * @param string $tabs_id     Unique ID for the tabs instance, used to generate tab IDs.
 *
 * @return array List of tab IDs.
 */
function block_core_tabs_generate_tabs_list( array $innerblocks = array(), string $tabs_id = '' ): array {
	$tabs_list = array();

	// Find tab-panel block
	foreach ( $innerblocks as $inner_block ) {
		if ( 'core/tab-panels' === ( $inner_block['blockName'] ?? '' ) ) {
			$tab_index = 0;
			foreach ( $inner_block['innerBlocks'] ?? array() as $tab_block ) {
				if ( 'core/tab-panel' === ( $tab_block['blockName'] ?? '' ) ) {
					$attrs = $tab_block['attrs'] ?? array();

					$tab_id = ! empty( $attrs['anchor'] )
						? $attrs['anchor']
						: ( ! empty( $tabs_id )
							? $tabs_id . '-tab-' . $tab_index
							: 'tab-' . $tab_index );

					$tabs_list[] = esc_attr( $tab_id );
					++$tab_index;
				}
			}
			break;
		}
	}

	return $tabs_list;
}

/**
 * Filter to provide tabs list context to core/tabs and core/tab-list blocks.
 * It is more performant to do this here, once, rather than in the tabs render and tabs context filters.
 * In this way core/tabs is both a provider and a consumer of the core/tabs-list context.
 *
 * @since 7.1.0
 *
 * @param array $context      Default block context.
 * @param array $parsed_block The block being rendered.
 *
 * @return array Modified context.
 */
function block_core_tabs_provide_context( array $context, array $parsed_block ): array {
	if ( 'core/tabs' === $parsed_block['blockName'] ) {
		// Generate a unique ID for the tabs instance first, so it can be used
		// to derive stable tab IDs.
		$tabs_id                   = $parsed_block['attrs']['anchor'] ?? wp_unique_id( 'tabs_' );
		$tabs_list                 = block_core_tabs_generate_tabs_list( $parsed_block['innerBlocks'] ?? array(), $tabs_id );
		$context['core/tabs-list'] = $tabs_list;
		$context['core/tabs-id']   = $tabs_id;
	}

	return $context;
}
add_filter( 'render_block_context', 'block_core_tabs_provide_context', 10, 2 );

/**
 * Render callback for core/tabs.
 *
 * @since 7.1.0
 *
 * @param array     $attributes Block attributes.
 * @param string    $content    Block content.
 * @param \WP_Block $block      WP_Block instance.
 *
 * @return string Updated HTML.
 */
function block_core_tabs_render_block_callback( array $attributes, string $content, \WP_Block $block ): string {
	$active_tab_index = $attributes['activeTabIndex'] ?? 0;
	$tabs_list        = $block->context['core/tabs-list'] ?? array();
	$tabs_id          = $block->context['core/tabs-id'] ?? null;

	if ( empty( $tabs_id ) ) {
		// If malformed tabs, return early to avoid errors.
		return '';
	}

	$tag_processor = new WP_HTML_Tag_Processor( $content );

	$tag_processor->next_tag( array( 'class_name' => 'wp-block-tabs' ) );
	$tag_processor->set_attribute( 'data-wp-interactive', 'core/tabs' );

	$tag_processor->set_attribute(
		'data-wp-context',
		wp_json_encode(
			array(
				'tabsId'         => $tabs_id,
				'activeTabIndex' => $active_tab_index,
			)
		)
	);
	$tag_processor->set_attribute( 'data-wp-init', 'callbacks.onTabsInit' );
	$tag_processor->set_attribute( 'data-wp-on--keydown', 'actions.handleTabKeyDown' );

	$output = $tag_processor->get_updated_html();

	/**
	 * Builds a client side state for just this tabs instance, so each
	 * core/tabs block manages its own state, like context.
	 */
	wp_interactivity_state(
		'core/tabs',
		array(
			$tabs_id => $tabs_list,
		)
	);

	return $output;
}

/**
 * Registers the `core/tabs` block on the server.
 *
 * @since 7.1.0
 */
function register_block_core_tabs() {
	register_block_type_from_metadata(
		__DIR__ . '/tabs',
		array(
			'render_callback' => 'block_core_tabs_render_block_callback',
		)
	);
}
add_action( 'init', 'register_block_core_tabs' );

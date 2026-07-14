<?php
/**
 * Tab Panel Block
 *
 * @package WordPress
 */

/**
 * Render callback for core/tab-panel.
 *
 * @since 7.1.0
 *
 * @param array     $attributes Block attributes.
 * @param string    $content    Block content.
 * @param \WP_Block $block      Block instance.
 *
 * @return string Updated HTML.
 */
function block_core_tab_panel_render( array $attributes, string $content, \WP_Block $block ): string {
	$tabs_id = $block->context['core/tabs-id'] ?? '';

	static $tab_counters = array();

	if ( ! isset( $tab_counters[ $tabs_id ] ) ) {
		$tab_counters[ $tabs_id ] = 0;
	}

	$tab_index = $tab_counters[ $tabs_id ];
	++$tab_counters[ $tabs_id ];

	$tag_processor = new WP_HTML_Tag_Processor( $content );
	$tag_processor->next_tag( array( 'class_name' => 'wp-block-tab-panel' ) );

	// Use the user's custom anchor if present, otherwise fall back to
	// the generated position-based ID.
	$tab_id = (string) $tag_processor->get_attribute( 'id' );
	if ( empty( $tab_id ) ) {
		$tab_id = ! empty( $tabs_id )
			? $tabs_id . '-tab-' . $tab_index
			: 'tab-' . $tab_index;
		$tag_processor->set_attribute( 'id', $tab_id );
	}

	$tag_processor->set_attribute( 'aria-labelledby', 'tab__' . $tab_id );
	$tag_processor->set_attribute( 'data-wp-bind--hidden', '!state.isActiveTab' );

	return (string) $tag_processor->get_updated_html();
}

/**
 * Registers the `core/tab-panel` block on the server.
 *
 * @hook init
 *
 * @since 7.1.0
 */
function register_block_core_tab_panel() {
	register_block_type_from_metadata(
		__DIR__ . '/tab-panel',
		array(
			'render_callback' => 'block_core_tab_panel_render',
		)
	);
}
add_action( 'init', 'register_block_core_tab_panel' );

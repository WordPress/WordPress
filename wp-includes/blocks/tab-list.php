<?php
/**
 * Tab List Block
 *
 * @package WordPress
 */

/**
 * Render callback for core/tab-list.
 *
 * Injects IAPI directives into the saved button HTML. The buttons already
 * carry color/border/padding styles from save.js; this callback adds
 * tab-specific attributes (id, aria-controls, context) and interactivity
 * directives using data from the tabs-list context.
 *
 * @since 7.1.0
 *
 * @param array     $attributes Block attributes.
 * @param string    $content    Block content (rendered buttons from save.js).
 * @param \WP_Block $block      WP_Block instance.
 *
 * @return string Updated HTML.
 */
function block_core_tab_list_render_callback( array $attributes, string $content, \WP_Block $block ): string {
	$tabs_list  = $block->context['core/tabs-list'] ?? array();
	$aria_label = empty( $attributes['ariaLabel'] ) ? __( 'Tabbed content' ) : wp_strip_all_tags( $attributes['ariaLabel'] );

	$tag_processor = new WP_HTML_Tag_Processor( $content );
	if ( $tag_processor->next_tag( array( 'class_name' => 'wp-block-tab-list' ) ) ) {
		$tag_processor->set_attribute( 'aria-label', $aria_label );
	}

	if ( empty( $tabs_list ) ) {
		return $tag_processor->get_updated_html();
	}

	$tab_index = 0;

	while ( $tag_processor->next_tag( 'button' ) ) {
		$tab_id = $tabs_list[ $tab_index ] ?? null;

		if ( null === $tab_id ) {
			break;
		}

		$tag_processor->set_attribute( 'id', 'tab__' . $tab_id );
		$tag_processor->set_attribute( 'aria-controls', $tab_id );
		$tag_processor->set_attribute( 'data-wp-on--click', 'actions.handleTabClick' );
		$tag_processor->set_attribute( 'data-wp-on--keydown', 'actions.handleTabKeyDown' );
		$tag_processor->set_attribute( 'data-wp-bind--aria-selected', 'state.isActiveTab' );
		$tag_processor->set_attribute( 'data-wp-bind--tabindex', 'state.tabIndexAttribute' );
		$tag_processor->set_attribute(
			'data-wp-context',
			wp_json_encode( array( 'tabIndex' => $tab_index ) )
		);

		++$tab_index;
	}

	return $tag_processor->get_updated_html();
}

/**
 * Registers the `core/tab-list` block on the server.
 *
 * @since 7.1.0
 */
function register_block_core_tab_list() {
	register_block_type_from_metadata(
		__DIR__ . '/tab-list',
		array(
			'render_callback' => 'block_core_tab_list_render_callback',
		)
	);
}
add_action( 'init', 'register_block_core_tab_list' );

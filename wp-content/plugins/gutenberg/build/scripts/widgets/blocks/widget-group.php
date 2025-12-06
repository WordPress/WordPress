<?php
/**
 * Server-side rendering of the `core/widget-group` block.
 *
 * @package WordPress
 */

/**
 * Renders the 'core/widget-group' block.
 *
 * @since 5.9.0
 *
 * @global array      $wp_registered_sidebars
 * @global int|string $_sidebar_being_rendered
 *
 * @param array    $attributes The block attributes.
 * @param string   $content The block content.
 * @param WP_Block $block The block.
 *
 * @return string Rendered block.
 */
function gutenberg_render_block_core_widget_group( $attributes, $content, $block ) {
	global $wp_registered_sidebars, $_sidebar_being_rendered;

	if ( isset( $wp_registered_sidebars[ $_sidebar_being_rendered ] ) ) {
		$before_title = $wp_registered_sidebars[ $_sidebar_being_rendered ]['before_title'];
		$after_title  = $wp_registered_sidebars[ $_sidebar_being_rendered ]['after_title'];
	} else {
		$before_title = '<h2 class="widget-title">';
		$after_title  = '</h2>';
	}

	$html = '';

	if ( ! empty( $attributes['title'] ) ) {
		$html .= $before_title . esc_html( $attributes['title'] ) . $after_title;
	}

	$html .= '<div class="wp-widget-group__inner-blocks">';
	foreach ( $block->inner_blocks as $inner_block ) {
		$html .= $inner_block->render();
	}
	$html .= '</div>';

	return $html;
}

/**
 * Registers the 'core/widget-group' block.
 *
 * @since 5.9.0
 */
function gutenberg_register_block_core_widget_group() {
	register_block_type_from_metadata(
		__DIR__ . '/widget-group',
		array(
			'render_callback' => 'gutenberg_render_block_core_widget_group',
		)
	);
}

add_action( 'init', 'gutenberg_register_block_core_widget_group', 20 );

/**
 * Make a note of the sidebar being rendered before WordPress starts rendering
 * it. This lets us get to the current sidebar in
 * gutenberg_render_block_core_widget_group().
 *
 * @since 5.9.0
 *
 * @global int|string $_sidebar_being_rendered
 *
 * @param int|string $index       Index, name, or ID of the dynamic sidebar.
 */
function gutenberg_note_sidebar_being_rendered( $index ) {
	global $_sidebar_being_rendered;
	$_sidebar_being_rendered = $index;
}
add_action( 'dynamic_sidebar_before', 'gutenberg_note_sidebar_being_rendered' );

/**
 * Clear whatever we set in gutenberg_note_sidebar_being_rendered() after WordPress
 * finishes rendering a sidebar.
 *
 * @since 5.9.0
 *
 * @global int|string $_sidebar_being_rendered
 */
function gutenberg_discard_sidebar_being_rendered() {
	global $_sidebar_being_rendered;
	unset( $_sidebar_being_rendered );
}
add_action( 'dynamic_sidebar_after', 'gutenberg_discard_sidebar_being_rendered' );

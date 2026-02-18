<?php
/**
 * Server-side registering of the `core/navigation-overlay-close` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/navigation-overlay-close` block on server.
 *
 * @since 7.0.0
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the block content.
 */
function render_block_core_navigation_overlay_close( $attributes ) {
	$text         = empty( $attributes['text'] ) ? __( 'Close' ) : $attributes['text'];
	$display_mode = empty( $attributes['displayMode'] ) ? 'icon' : $attributes['displayMode'];
	$show_icon    = 'both' === $display_mode || 'icon' === $display_mode;
	$show_text    = 'both' === $display_mode || 'text' === $display_mode;
	$button_text  = '';

	if ( $show_icon ) {
		$button_text .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M13 11.8l6.1-6.3-1.1-1-6.1 6.2-6.1-6.2-1.1 1 6.1 6.3-6.5 6.7 1.1 1 6.5-6.6 6.5 6.6 1.1-1z" /></svg>';
	}

	if ( $show_text ) {
		$button_text .= '<span class="wp-block-navigation-overlay-close__text">' . wp_kses_post( $text ) . '</span>';
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	$html_content       = sprintf(
		'<button %1$s type="button" %2$s >%3$s</button>',
		$wrapper_attributes,
		! $show_text ? 'aria-label="' . __( 'Close' ) . '"' : '',
		$button_text
	);

	return $html_content;
}

/**
 * Registers the navigation overlay close block.
 *
 * @since 7.0.0
 */
function register_block_core_navigation_overlay_close() {
	register_block_type_from_metadata(
		__DIR__ . '/navigation-overlay-close',
		array(
			'render_callback' => 'render_block_core_navigation_overlay_close',
		)
	);
}
add_action( 'init', 'register_block_core_navigation_overlay_close' );

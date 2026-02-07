<?php
/**
 * Tabs Block
 *
 * @package WordPress
 */

/**
 * Build typography classnames from named size/family.
 *
 * @param array $attributes Block attributes.
 * @return string Classnames.
 */
function gutenberg_block_core_tab_get_typography_classes( array $attributes ): string {
	$typography_classes    = array();
	$has_named_font_family = ! empty( $attributes['fontFamily'] );
	$has_named_font_size   = ! empty( $attributes['fontSize'] );

	if ( $has_named_font_size ) {
		$typography_classes[] = sprintf( 'has-%s-font-size', esc_attr( (string) $attributes['fontSize'] ) );
	}

	if ( $has_named_font_family ) {
		$typography_classes[] = sprintf( 'has-%s-font-family', esc_attr( (string) $attributes['fontFamily'] ) );
	}

	return implode( ' ', $typography_classes );
}

/**
 * Build inline typography styles.
 *
 * @param array $attributes Block attributes.
 * @return string Inline CSS.
 */
function gutenberg_block_core_tab_get_typography_styles( array $attributes ): string {
	$typography_styles = array();

	if ( ! empty( $attributes['style']['typography']['fontSize'] ) ) {
		$typography_styles[] = sprintf(
			'font-size: %s;',
			gutenberg_get_typography_font_size_value(
				array(
					'size' => $attributes['style']['typography']['fontSize'],
				)
			)
		);
	}

	if ( ! empty( $attributes['style']['typography']['fontFamily'] ) ) {
		$typography_styles[] = sprintf( 'font-family: %s;', $attributes['style']['typography']['fontFamily'] );
	}

	return implode( '', $typography_styles );
}

/**
 * Render callback for core/tab.
 *
 * @param array     $attributes Block attributes.
 * @param string    $content    Block content.
 *
 * @return string Updated HTML.
 */
function gutenberg_block_core_tab_render( array $attributes, string $content ): string {
	$tag_processor = new WP_HTML_Tag_Processor( $content );
	$tag_processor->next_tag( array( 'class_name' => 'wp-block-tab' ) );
	$tab_id = (string) $tag_processor->get_attribute( 'id' );

	/**
	 * Add interactivity to the tab element.
	 */
	$tag_processor->set_attribute(
		'data-wp-interactive',
		'core/tabs/private'
	);
	$tag_processor->set_attribute(
		'data-wp-context',
		wp_json_encode(
			array(
				'tab' => array(
					'id' => $tab_id,
				),
			)
		)
	);

	/**
	 * Process style classnames.
	 */
	$classname  = (string) $tag_processor->get_attribute( 'class' );
	$classname .= ' ' . gutenberg_block_core_tab_get_typography_classes( $attributes );
	$tag_processor->set_attribute( 'class', $classname );

	/**
	 * Process accessibility and interactivity attributes.
	 */
	$tag_processor->set_attribute( 'role', 'tabpanel' );
	$tag_processor->set_attribute( 'aria-labelledby', 'tab__' . $tab_id );
	$tag_processor->set_attribute( 'data-wp-bind--hidden', '!state.isActiveTab' );
	$tag_processor->set_attribute( 'data-wp-bind--tabindex', 'state.tabIndexAttribute' );

	/**
	 * Process style attribute.
	 */
	$style  = (string) $tag_processor->get_attribute( 'style' );
	$style .= gutenberg_block_core_tab_get_typography_styles( $attributes );
	$tag_processor->set_attribute( 'style', $style );

	return (string) $tag_processor->get_updated_html();
}

/**
 * Registers the `core/tab` block on the server.
 *
 * @hook init
 *
 * @since 6.9.0
 */
function gutenberg_register_block_core_tab() {
	register_block_type_from_metadata(
		__DIR__ . '/tab',
		array(
			'render_callback' => 'gutenberg_block_core_tab_render',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_tab', 20 );

<?php
/**
 * Tabs Block
 *
 * @package WordPress
 */

/**
 * Build inline CSS custom properties for color settings.
 *
 * @param array $attributes Block attributes.
 *
 * @return string Inline CSS string.
 */
function gutenberg_block_core_tabs_generate_color_styles( array $attributes ): string {
	$tab_inactive = $attributes['customTabInactiveColor'] ?? '';
	$tab_hover    = $attributes['customTabHoverColor'] ?? '';
	$tab_active   = $attributes['customTabActiveColor'] ?? '';
	$tab_text     = $attributes['customTabTextColor'] ?? '';
	$hover_text   = $attributes['customTabHoverTextColor'] ?? '';
	$active_text  = $attributes['customTabActiveTextColor'] ?? '';

	$styles = array(
		'--custom-tab-inactive-color'    => $tab_inactive,
		'--custom-tab-hover-color'       => $tab_hover,
		'--custom-tab-active-color'      => $tab_active,
		'--custom-tab-text-color'        => $tab_text,
		'--custom-tab-hover-text-color'  => $hover_text,
		'--custom-tab-active-text-color' => $active_text,
	);

	$style_string = array_map(
		static function ( string $key, string $value ): string {
			return ! empty( $value ) ? $key . ': ' . $value . ';' : '';
		},
		array_keys( $styles ),
		$styles
	);

	return implode( ' ', array_filter( $style_string ) );
}

/**
 * Build inline CSS custom properties for gap settings.
 *
 * @param array $attributes Block attributes.
 * @param bool  $is_vertical Whether the tabs are vertical.
 *
 * @return string Inline CSS string.
 */
function gutenberg_block_core_tabs_generate_gap_styles( array $attributes, bool $is_vertical ): string {
	if ( empty( $attributes['style'] ) || ! is_array( $attributes['style'] ) ) {
		return '--wp--style--tabs-gap-default: 0.5em;';
	}
	if ( empty( $attributes['style']['spacing'] ) || ! is_array( $attributes['style']['spacing'] ) ) {
		return '--wp--style--tabs-gap-default: 0.5em;';
	}
	if ( ! array_key_exists( 'blockGap', $attributes['style']['spacing'] ) ) {
		return '--wp--style--tabs-gap-default: 0.5em;';
	}

	$block_gap = $attributes['style']['spacing']['blockGap'];

	if ( is_array( $block_gap ) ) {
		if ( array_key_exists( 'left', $block_gap ) && array_key_exists( 'top', $block_gap ) ) {
			$block_gap_horizontal = $block_gap['left'];
			$block_gap_vertical   = $block_gap['top'];
		} elseif ( array_key_exists( 'left', $block_gap ) ) {
			$block_gap_horizontal = $block_gap['left'];
			$block_gap_vertical   = '0.5em';
		} elseif ( array_key_exists( 'top', $block_gap ) ) {
			$block_gap_horizontal = '0.5em';
			$block_gap_vertical   = $block_gap['top'];
		} else {
			return '--wp--style--tabs-gap-default: 0.5em;';
		}
	} elseif ( is_string( $block_gap ) ) {
		return '--wp--style--tabs-gap-default: 0.5em;';
	}

	$block_gap_horizontal = preg_match( '/^var:preset\|spacing\|\d+$/', (string) $block_gap_horizontal )
		? 'var(--wp--preset--spacing--' . substr( (string) $block_gap_horizontal, strrpos( (string) $block_gap_horizontal, '|' ) + 1 ) . ')'
		: (string) $block_gap_horizontal;

	$block_gap_vertical = preg_match( '/^var:preset\|spacing\|\d+$/', (string) $block_gap_vertical )
		? 'var(--wp--preset--spacing--' . substr( (string) $block_gap_vertical, strrpos( (string) $block_gap_vertical, '|' ) + 1 ) . ')'
		: (string) $block_gap_vertical;

	$list_gap  = $block_gap_horizontal;
	$block_gap = $block_gap_vertical;

	if ( $is_vertical ) {
		$list_gap  = $block_gap_vertical;
		$block_gap = $block_gap_horizontal;
	}

	return wp_sprintf(
		'--wp--style--unstable-tabs-list-gap: %s; --wp--style--unstable-tabs-gap: %s;',
		$list_gap,
		$block_gap
	);
}

/**
 * Extract tabs list from inner blocks for hydration.
 *
 * @param array $innerblocks Parsed inner blocks.
 *
 * @return array List of tabs with id, label, index.
 */
function gutenberg_block_core_tabs_generate_tabs_list_from_innerblocks( array $innerblocks = array() ): array {
	$tab_index = 0;

	return array_map(
		static function ( array $tab ) use ( &$tab_index ): array {
			$attrs = $tab['attrs'] ?? array();

			$tag_processor = new WP_HTML_Tag_Processor( $tab['innerHTML'] ?? '' );
			$tag_processor->next_tag( array( 'class_name' => 'wp-block-tab' ) );

			$tab_id    = $tag_processor->get_attribute( 'id' );
			$tab_label = $attrs['label'] ?? '';

			$attrs['id']    = $tab_id;
			$attrs['label'] = esc_html( (string) $tab_label );

			$tab_index++;

			return $attrs;
		},
		$innerblocks
	);
}

/**
 * Render callback for core/tabs.
 *
 * @param array     $attributes Block attributes.
 * @param string    $content    Block content.
 * @param \WP_Block $block      WP_Block instance.
 *
 * @return string Updated HTML.
 */
function gutenberg_block_core_tabs_render_block_callback( array $attributes, string $content, \WP_Block $block ): string {
	$active_tab_index = $attributes['activeTabIndex'] ?? 0;

	$tabs_list = gutenberg_block_core_tabs_generate_tabs_list_from_innerblocks( $block->parsed_block['innerBlocks'] ?? array() );

	$tabs_id = wp_unique_id( 'tabs_' );

	/**
	 * Builds a client side state for just this tabs instance.
	 * This allows 3rd party extensibility of tabs while retaining
	 * client side state management per core/tabs instance, like context.
	 */
	wp_interactivity_state(
		'core/tabs/private',
		array(
			$tabs_id => $tabs_list,
		)
	);

	$is_vertical = 'vertical' === ( $attributes['orientation'] ?? 'horizontal' );

	$tag_processor = new WP_HTML_Tag_Processor( $content );
	$tag_processor->next_tag( array( 'class_name' => 'wp-block-tabs' ) );
	$tag_processor->add_class( $is_vertical ? 'is-vertical' : 'is-horizontal' );
	$tag_processor->set_attribute( 'data-wp-interactive', 'core/tabs/private' );
	$tag_processor->set_attribute(
		'data-wp-context',
		wp_json_encode(
			array(
				'tabsId'         => $tabs_id,
				'activeTabIndex' => $active_tab_index,
				'isVertical'     => $is_vertical,
			)
		)
	);
	$tag_processor->set_attribute( 'data-wp-init', 'callbacks.onTabsInit' );
	$tag_processor->set_attribute( 'data-wp-on--keydown', 'actions.handleTabKeyDown' );

	/**
	 * Process style attribute.
	 */
	$style  = (string) $tag_processor->get_attribute( 'style' );
	$style .= gutenberg_block_core_tabs_generate_color_styles( $attributes );
	$style .= gutenberg_block_core_tabs_generate_gap_styles( $attributes, $is_vertical );
	$tag_processor->set_attribute( 'style', $style );

	$updated_content = $tag_processor->get_updated_html();

	/**
	 * Build the tabs list markup.
	 * We're doing this manually instead of using <template/> to make it possible
	 * for other blocks to extend the tabs list via HTML api.
	 */
	$tabs_list_markup = array_map(
		static function ( array $tab ): string {
			return wp_sprintf(
				'<a id="tab__%1$s" class="tabs__tab-label" href="#%1$s" role="tab" aria-controls="%1$s" data-wp-on--click="actions.handleTabClick" data-wp-on--keydown="actions.handleTabKeyDown" data-wp-bind--aria-selected="state.isActiveTab" data-wp-bind--tabindex="state.tabIndexAttribute">%2$s</a>',
				$tab['id'],
				html_entity_decode( $tab['label'] )
			);
		},
		$tabs_list
	);
	$tabs_list_markup = implode( '', $tabs_list_markup );

	/**
	 * Splice the tabs list into the content.
	 */
	$content = preg_replace(
		'/<ul\s+class="tabs__list">\s*<\/ul>/i',
		'<div class="tabs__list" role="tablist">' . $tabs_list_markup . '</div>',
		(string) $updated_content
	);

	/**
	 * In the event preg_replace fails, return the tabs content without the list spliced in.
	 * This ensures the block content is still rendered, albeit without the tabs list.
	 */
	return is_string( $content ) ? $content : (string) $updated_content;
}

/**
 * Registers the `core/tabs` block on the server.
 *
 * @since 6.8.0
 */
function gutenberg_register_block_core_tabs() {
	register_block_type_from_metadata(
		__DIR__ . '/tabs',
		array(
			'render_callback' => 'gutenberg_block_core_tabs_render_block_callback',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_tabs', 20 );

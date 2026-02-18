<?php
/**
 * Custom CSS block support.
 *
 * @package WordPress
 */

/**
 * Render the custom CSS stylesheet and add class name to block as required.
 *
 * @since 7.0.0
 *
 * @param array $parsed_block The parsed block.
 * @return array The same parsed block with custom CSS class name added if appropriate.
 */
function wp_render_custom_css_support_styles( $parsed_block ) {
	$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $parsed_block['blockName'] );

	if ( ! block_has_support( $block_type, 'customCSS', true ) ) {
		return $parsed_block;
	}

	$custom_css = trim( $parsed_block['attrs']['style']['css'] ?? '' );

	if ( empty( $custom_css ) ) {
		return $parsed_block;
	}

	// Validate CSS doesn't contain HTML markup (same validation as global styles REST API).
	if ( preg_match( '#</?\w+#', $custom_css ) ) {
		return $parsed_block;
	}

	// Generate a unique class name for this block instance.
	$class_name         = wp_unique_id_from_values( $parsed_block, 'wp-custom-css-' );
	$updated_class_name = isset( $parsed_block['attrs']['className'] )
		? $parsed_block['attrs']['className'] . " $class_name"
		: $class_name;

	_wp_array_set( $parsed_block, array( 'attrs', 'className' ), $updated_class_name );

	// Process the custom CSS using the same method as global styles.
	$selector      = '.' . $class_name;
	$processed_css = WP_Theme_JSON::process_blocks_custom_css( $custom_css, $selector );

	if ( ! empty( $processed_css ) ) {
		/*
		 * Register and add inline style for block custom CSS.
		 * The style depends on global-styles to ensure custom CSS loads after
		 * and can override global styles.
		 */
		wp_register_style( 'wp-block-custom-css', false, array( 'global-styles' ) );
		wp_add_inline_style( 'wp-block-custom-css', $processed_css );
	}

	return $parsed_block;
}

/**
 * Enqueues the block custom CSS styles.
 *
 * @since 7.0.0
 */
function wp_enqueue_block_custom_css() {
	wp_enqueue_style( 'wp-block-custom-css' );
}

/**
 * Applies the custom CSS class name to the block's rendered HTML.
 *
 * The class name is generated in `wp_render_custom_css_support_styles`
 * and stored in block attributes. This filter adds it to the actual markup.
 *
 * @since 7.0.0
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 * @return string               Filtered block content.
 */
function wp_render_custom_css_class_name( $block_content, $block ) {
	$class_string = $block['attrs']['className'] ?? '';
	preg_match( '/\bwp-custom-css-\S+\b/', $class_string, $matches );

	if ( empty( $matches ) ) {
		return $block_content;
	}

	$tags = new WP_HTML_Tag_Processor( $block_content );

	if ( $tags->next_tag() ) {
		$tags->add_class( 'has-custom-css' );
		$tags->add_class( $matches[0] );
	}

	return $tags->get_updated_html();
}

add_filter( 'render_block', 'wp_render_custom_css_class_name', 10, 2 );
add_filter( 'render_block_data', 'wp_render_custom_css_support_styles', 10, 1 );
add_action( 'wp_enqueue_scripts', 'wp_enqueue_block_custom_css', 1 );

/**
 * Registers the style block attribute for block types that support it.
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_custom_css_support( $block_type ) {
	// Setup attributes and styles within that if needed.
	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	// Check for existing style attribute definition e.g. from block.json.
	if ( array_key_exists( 'style', $block_type->attributes ) ) {
		return;
	}

	$has_custom_css_support = block_has_support( $block_type, array( 'customCSS' ), true );

	if ( $has_custom_css_support ) {
		$block_type->attributes['style'] = array(
			'type' => 'object',
		);
	}
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'custom-css',
	array(
		'register_attribute' => 'wp_register_custom_css_support',
	)
);

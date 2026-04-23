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

/**
 * Strips `style.css` attributes from all blocks in post content.
 *
 * Uses {@see WP_Block_Parser::next_token()} to scan block tokens and surgically
 * replace only the attribute JSON that changed — no parse_blocks() +
 * serialize_blocks() round-trip needed.
 *
 * @since 7.0.0
 * @access private
 *
 * @param string $content Post content to filter, expected to be escaped with slashes.
 * @return string Filtered post content with block custom CSS removed.
 */
function wp_strip_custom_css_from_blocks( $content ) {
	if ( ! has_blocks( $content ) ) {
		return $content;
	}

	$unslashed = stripslashes( $content );

	$parser           = new WP_Block_Parser();
	$parser->document = $unslashed;
	$parser->offset   = 0;
	$end              = strlen( $unslashed );
	$replacements     = array();

	while ( $parser->offset < $end ) {
		$next_token = $parser->next_token();

		if ( 'no-more-tokens' === $next_token[0] ) {
			break;
		}

		list( $token_type, , $attrs, $start_offset, $token_length ) = $next_token;

		$parser->offset = $start_offset + $token_length;

		if ( 'block-opener' !== $token_type && 'void-block' !== $token_type ) {
			continue;
		}

		if ( ! isset( $attrs['style']['css'] ) ) {
			continue;
		}

		// Remove css and clean up empty style.
		unset( $attrs['style']['css'] );
		if ( empty( $attrs['style'] ) ) {
			unset( $attrs['style'] );
		}

		// Locate the JSON portion within the token.
		$token_string   = substr( $unslashed, $start_offset, $token_length );
		$json_rel_start = strcspn( $token_string, '{' );
		$json_rel_end   = strrpos( $token_string, '}' );

		$json_start  = $start_offset + $json_rel_start;
		$json_length = $json_rel_end - $json_rel_start + 1;

		// Re-encode attributes. If attrs is now empty, remove JSON and trailing space.
		if ( empty( $attrs ) ) {
			// Remove the trailing space after JSON.
			$replacements[] = array( $json_start, $json_length + 1, '' );
		} else {
			$replacements[] = array( $json_start, $json_length, serialize_block_attributes( $attrs ) );
		}
	}

	if ( empty( $replacements ) ) {
		return $content;
	}

	// Build the result by splicing replacements into the original string.
	$result = '';
	$was_at = 0;

	foreach ( $replacements as $replacement ) {
		list( $offset, $length, $new_json ) = $replacement;
		$result                            .= substr( $unslashed, $was_at, $offset - $was_at ) . $new_json;
		$was_at                             = $offset + $length;
	}

	if ( $was_at < $end ) {
		$result .= substr( $unslashed, $was_at );
	}

	return addslashes( $result );
}

/**
 * Adds the filters to strip custom CSS from block content on save.
 * Priority of 8 to run before wp_filter_global_styles_post (priority 9) and wp_filter_post_kses (priority 10).
 *
 * @since 7.0.0
 * @access private
 */
function wp_custom_css_kses_init_filters() {
	add_filter( 'content_save_pre', 'wp_strip_custom_css_from_blocks', 8 );
	add_filter( 'content_filtered_save_pre', 'wp_strip_custom_css_from_blocks', 8 );
}

/**
 * Removes the filters that strip custom CSS from block content on save.
 * Priority of 8 to run before wp_filter_global_styles_post (priority 9) and wp_filter_post_kses (priority 10).
 *
 * @since 7.0.0
 * @access private
 */
function wp_custom_css_remove_filters() {
	remove_filter( 'content_save_pre', 'wp_strip_custom_css_from_blocks', 8 );
	remove_filter( 'content_filtered_save_pre', 'wp_strip_custom_css_from_blocks', 8 );
}

/**
 * Registers the custom CSS content filters if the user does not have the edit_css capability.
 *
 * @since 7.0.0
 * @access private
 */
function wp_custom_css_kses_init() {
	wp_custom_css_remove_filters();
	if ( ! current_user_can( 'edit_css' ) ) {
		wp_custom_css_kses_init_filters();
	}
}

/**
 * Initializes custom CSS content filters when imported data should be filtered.
 *
 * Runs at priority 999 on {@see 'force_filtered_html_on_import'} to ensure it
 * fires after general KSES initialization, independently of user capabilities.
 * If the input of the filter is true it means we are in an import situation and should
 * enable the custom CSS filters, independently of the user capabilities.
 *
 * @since 7.0.0
 * @access private
 *
 * @param mixed $arg Input argument of the filter.
 * @return mixed Input argument of the filter.
 */
function wp_custom_css_force_filtered_html_on_import_filter( $arg ) {
	if ( $arg ) {
		wp_custom_css_kses_init_filters();
	}
	return $arg;
}

// Run before wp_filter_global_styles_post (priority 9) and wp_filter_post_kses (priority 10).
add_action( 'init', 'wp_custom_css_kses_init', 20 );
add_action( 'set_current_user', 'wp_custom_css_kses_init' );
add_filter( 'force_filtered_html_on_import', 'wp_custom_css_force_filtered_html_on_import_filter', 999 );

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'custom-css',
	array(
		'register_attribute' => 'wp_register_custom_css_support',
	)
);

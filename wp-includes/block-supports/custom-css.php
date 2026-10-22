<?php
/**
 * Custom CSS block support.
 *
 * @package WordPress
 */

/**
 * Checks whether a style node has a non-empty custom CSS string.
 *
 * @since 7.2.0
 * @access private
 *
 * @param mixed $style_node A style node, possibly containing a `css` key.
 * @return bool Whether the node has a non-empty custom CSS string.
 */
function wp_custom_css_has_value( $style_node ) {
	return is_array( $style_node )
		&& isset( $style_node['css'] )
		&& is_string( $style_node['css'] )
		&& '' !== trim( $style_node['css'] );
}

/**
 * Collects custom CSS defined for a block instance across its default,
 * pseudo-selector, and viewport states.
 *
 * @since 7.2.0
 * @access private
 *
 * @param array  $style                    The block's `style` attribute.
 * @param string $block_name               Block name.
 * @param array  $responsive_media_queries Media queries keyed by viewport state name.
 * @return array[] Custom CSS entries with `css`, `pseudo`, and `media_query` keys.
 */
function wp_get_custom_css_state_entries( $style, $block_name, $responsive_media_queries ) {
	$entries = array();
	if ( empty( $style ) || ! is_array( $style ) ) {
		return $entries;
	}

	$supported_pseudo_states = WP_Theme_JSON::VALID_BLOCK_PSEUDO_SELECTORS[ $block_name ] ?? array();

	if ( wp_custom_css_has_value( $style ) ) {
		$entries[] = array(
			'css'         => $style['css'],
			'pseudo'      => null,
			'media_query' => null,
		);
	}

	foreach ( $supported_pseudo_states as $pseudo_state ) {
		$pseudo_style = $style[ $pseudo_state ] ?? null;
		if ( wp_custom_css_has_value( $pseudo_style ) ) {
			$entries[] = array(
				'css'         => $pseudo_style['css'],
				'pseudo'      => $pseudo_state,
				'media_query' => null,
			);
		}
	}

	foreach ( $responsive_media_queries as $breakpoint => $media_query ) {
		$breakpoint_style = $style[ $breakpoint ] ?? null;
		if ( ! is_array( $breakpoint_style ) ) {
			continue;
		}

		if ( wp_custom_css_has_value( $breakpoint_style ) ) {
			$entries[] = array(
				'css'         => $breakpoint_style['css'],
				'pseudo'      => null,
				'media_query' => $media_query,
			);
		}

		foreach ( $supported_pseudo_states as $pseudo_state ) {
			$breakpoint_pseudo_style = $breakpoint_style[ $pseudo_state ] ?? null;
			if ( wp_custom_css_has_value( $breakpoint_pseudo_style ) ) {
				$entries[] = array(
					'css'         => $breakpoint_pseudo_style['css'],
					'pseudo'      => $pseudo_state,
					'media_query' => $media_query,
				);
			}
		}
	}

	return $entries;
}

/**
 * Validates and processes custom CSS for each block style state.
 *
 * @since 7.2.0
 * @access private
 *
 * @param array[] $state_entries Custom CSS entries.
 * @param string  $selector      Selector scoping the block instance.
 * @return string Processed CSS.
 */
function wp_process_custom_css_state_entries( $state_entries, $selector ) {
	$processed_css = '';

	foreach ( $state_entries as $entry ) {
		// Validate CSS doesn't contain HTML markup (same validation as global styles REST API).
		if ( preg_match( '#</?\w+#', $entry['css'] ) ) {
			continue;
		}

		$entry_selector = null !== $entry['pseudo'] ? $selector . $entry['pseudo'] : $selector;
		$entry_css      = WP_Theme_JSON::process_blocks_custom_css( $entry['css'], $entry_selector );

		if ( empty( $entry_css ) ) {
			continue;
		}

		$processed_css .= null !== $entry['media_query']
			? $entry['media_query'] . '{' . $entry_css . '}'
			: $entry_css;
	}

	return $processed_css;
}

/**
 * Generates the custom CSS class name for a block and registers its stylesheet.
 *
 * @since 7.2.0
 * @access private
 *
 * @param array $parsed_block The parsed block.
 * @return string|null The generated class name, or null if the block has no custom CSS to render.
 *
 * @phpstan-param array{
 *     blockName: string|null,
 *     attrs: array{
 *         className?: string,
 *         style?: array{
 *             css?: string,
 *             ...
 *         },
 *         ...
 *     },
 *     ...
 * } $parsed_block
 */
function wp_generate_custom_css_class_name( $parsed_block ) {
	$style = $parsed_block['attrs']['style'] ?? null;
	if ( empty( $style ) || ! is_array( $style ) ) {
		return null;
	}

	$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $parsed_block['blockName'] );
	if ( ! block_has_support( $block_type, 'customCSS', true ) ) {
		return null;
	}

	$viewport_settings        = wp_get_global_settings( array( 'viewport' ) );
	$responsive_media_queries = WP_Theme_JSON::get_viewport_media_queries( $viewport_settings );
	$state_entries            = wp_get_custom_css_state_entries( $style, $parsed_block['blockName'], $responsive_media_queries );
	if ( empty( $state_entries ) ) {
		return null;
	}

	// Generate a unique class name for this block instance.
	$class_name    = wp_unique_id_from_values( $parsed_block, 'wp-custom-css-' );
	$processed_css = wp_process_custom_css_state_entries( $state_entries, '.' . $class_name );
	if ( '' === $processed_css ) {
		return null;
	}

	/**
	 * Reuse one handle so identical custom CSS is enqueued only once via
	 * {@see wp_unique_id_from_values()}. Explicitly declare the `wp-block-library`
	 * dependency so `global-styles` is guaranteed to print after it, preventing
	 * block default styles from unintentionally overriding global styles.
	 */
	$handle = 'wp-block-custom-css';
	if ( ! wp_style_is( $handle, 'registered' ) ) {
		wp_register_style( $handle, false, array( 'wp-block-library', 'global-styles' ) );
	}
	$after_styles = wp_styles()->get_data( $handle, 'after' );
	if ( ! is_array( $after_styles ) ) {
		$after_styles = array();
	}
	if ( ! in_array( $processed_css, $after_styles, true ) ) {
		wp_add_inline_style( $handle, $processed_css );
	}

	return $class_name;
}

/**
 * Render the custom CSS stylesheet and add class name to block as required.
 *
 * @since 7.0.0
 *
 * @param array $parsed_block The parsed block.
 * @return array The same parsed block with custom CSS class name added if appropriate.
 *
 * @phpstan-param array{
 *     blockName: string|null,
 *     attrs: array{
 *         className?: string,
 *         style?: array{
 *             css?: string,
 *             ...
 *         },
 *         ...
 *     },
 *     ...
 * } $parsed_block
 * @phpstan-return array{
 *     blockName: string|null,
 *     attrs: array{
 *         className?: string,
 *         style?: array{
 *             css?: string,
 *             ...
 *         },
 *         ...
 *     },
 *     ...
 * }
 */
function wp_render_custom_css_support_styles( $parsed_block ) {
	$class_name = wp_generate_custom_css_class_name( $parsed_block );
	if ( null === $class_name ) {
		return $parsed_block;
	}

	$existing_class_name = $parsed_block['attrs']['className'] ?? null;
	$updated_class_name  = is_string( $existing_class_name )
		? "$existing_class_name $class_name"
		: $class_name;

	$parsed_block['attrs']['className'] = $updated_class_name;

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
 * Uses the class name that {@see wp_render_custom_css_support_styles()}
 * stored in the block attributes. Blocks that a parent parsed and rendered
 * itself never reach that filter, so the class name is generated here instead
 * via {@see wp_generate_custom_css_class_name()}.
 *
 * @since 7.0.0
 * @since 7.2.0 Generates the class name when the block attributes lack one.
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 * @return string Filtered block content.
 *
 * @phpstan-param array{
 *     blockName: string|null,
 *     attrs: array{
 *         className?: string,
 *         style?: array{
 *             css?: string,
 *             ...
 *         },
 *         ...
 *     },
 *     ...
 * } $block
 */
function wp_render_custom_css_class_name( $block_content, $block ) {
	$class_name_attr    = $block['attrs']['className'] ?? null;
	$class_name_prefix  = 'wp-custom-css-';
	$matched_class_name = null;

	// Parse out the 'wp-custom-css-*' class name added by wp_render_custom_css_support_styles().
	if ( is_string( $class_name_attr ) && str_contains( $class_name_attr, $class_name_prefix ) ) {
		$token_delimiter = " \t\f\r\n";
		$class_token     = strtok( $class_name_attr, $token_delimiter );
		while ( false !== $class_token ) {
			if ( str_starts_with( $class_token, $class_name_prefix ) ) {
				$matched_class_name = $class_token;
				break;
			}
			$class_token = strtok( $token_delimiter );
		}
	}

	// Direct WP_Block::render() calls skip render_block_data for the block itself.
	if ( null === $matched_class_name ) {
		$matched_class_name = wp_generate_custom_css_class_name( $block );
	}

	if ( null === $matched_class_name ) {
		return $block_content;
	}

	$tags = new WP_HTML_Tag_Processor( $block_content );
	if ( $tags->next_tag() ) {
		$tags->add_class( 'has-custom-css' );
		$tags->add_class( $matched_class_name );
	}

	return $tags->get_updated_html();
}

add_filter( 'render_block', 'wp_render_custom_css_class_name', 10, 2 );
add_filter( 'render_block_data', 'wp_render_custom_css_support_styles', 10, 1 );
add_action( 'wp_enqueue_scripts', 'wp_enqueue_block_custom_css', 1 );

/**
 * Registers the style block attribute for block types that support it.
 *
 * @since 7.0.0
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
 * Removes custom CSS from a style attribute and any nested style states.
 *
 * @since 7.2.0
 * @access private
 *
 * @param array $style The block's `style` attribute.
 * @return array The style attribute with all `css` keys removed.
 */
function wp_strip_custom_css_from_style_array( $style ) {
	if ( ! is_array( $style ) ) {
		return $style;
	}

	unset( $style['css'] );

	foreach ( $style as $key => $value ) {
		if ( is_string( $key ) && is_array( $value ) && ( str_starts_with( $key, ':' ) || str_starts_with( $key, '@' ) ) ) {
			$nested_style = wp_strip_custom_css_from_style_array( $value );
			if ( empty( $nested_style ) ) {
				unset( $style[ $key ] );
			} else {
				$style[ $key ] = $nested_style;
			}
		}
	}

	return $style;
}

/**
 * Strips custom CSS (`style.css` in attributes) from all blocks in post content.
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

		if ( ! isset( $attrs['style'] ) || ! is_array( $attrs['style'] ) ) {
			continue;
		}

		$stripped_style = wp_strip_custom_css_from_style_array( $attrs['style'] );
		if ( $stripped_style === $attrs['style'] ) {
			continue;
		}

		// Remove css and clean up empty style.
		if ( empty( $stripped_style ) ) {
			unset( $attrs['style'] );
		} else {
			$attrs['style'] = $stripped_style;
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

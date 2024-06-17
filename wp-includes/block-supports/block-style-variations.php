<?php
/**
 * Block support to enable per-section styling of block types via
 * block style variations.
 *
 * @package WordPress
 * @since 6.6.0
 */

/**
 * Generate block style variation instance name.
 *
 * @since 6.6.0
 * @access private
 *
 * @param array  $block     Block object.
 * @param string $variation Slug for the block style variation.
 *
 * @return string The unique variation name.
 */
function wp_create_block_style_variation_instance_name( $block, $variation ) {
	return $variation . '--' . md5( serialize( $block ) );
}

/**
 * Determines the block style variation names within a CSS class string.
 *
 * @since 6.6.0
 *
 * @param string $class_string CSS class string to look for a variation in.
 *
 * @return array|null The block style variation name if found.
 */
function wp_get_block_style_variation_name_from_class( $class_string ) {
	if ( ! is_string( $class_string ) ) {
		return null;
	}

	preg_match_all( '/\bis-style-(?!default)(\S+)\b/', $class_string, $matches );
	return $matches[1] ?? null;
}

/**
 * Render the block style variation's styles.
 *
 * In the case of nested blocks with variations applied, we want the parent
 * variation's styles to be rendered before their descendants. This solves the
 * issue of a block type being styled in both the parent and descendant: we want
 * the descendant style to take priority, and this is done by loading it after,
 * in the DOM order. This is why the variation stylesheet generation is in a
 * different filter.
 *
 * @since 6.6.0
 * @access private
 *
 * @param array $parsed_block The parsed block.
 *
 * @return array The parsed block with block style variation classname added.
 */
function wp_render_block_style_variation_support_styles( $parsed_block ) {
	$classes    = $parsed_block['attrs']['className'] ?? null;
	$variations = wp_get_block_style_variation_name_from_class( $classes );

	if ( ! $variations ) {
		return $parsed_block;
	}

	$tree       = WP_Theme_JSON_Resolver::get_merged_data();
	$theme_json = $tree->get_raw_data();

	// Only the first block style variation with data is supported.
	$variation_data = array();
	foreach ( $variations as $variation ) {
		$variation_data = $theme_json['styles']['blocks'][ $parsed_block['blockName'] ]['variations'][ $variation ] ?? array();

		if ( ! empty( $variation_data ) ) {
			break;
		}
	}

	if ( empty( $variation_data ) ) {
		return $parsed_block;
	}

	$variation_instance = wp_create_block_style_variation_instance_name( $parsed_block, $variation );
	$class_name         = "is-style-$variation_instance";
	$updated_class_name = $parsed_block['attrs']['className'] . " $class_name";

	/*
	 * Even though block style variations are effectively theme.json partials,
	 * they can't be processed completely as though they are.
	 *
	 * Block styles support custom selectors to direct specific types of styles
	 * to inner elements. For example, borders on Image block's get applied to
	 * the inner `img` element rather than the wrapping `figure`.
	 *
	 * The following relocates the "root" block style variation styles to
	 * under an appropriate blocks property to leverage the preexisting style
	 * generation for simple block style variations. This way they get the
	 * custom selectors they need.
	 *
	 * The inner elements and block styles for the variation itself are
	 * still included at the top level but scoped by the variation's selector
	 * when the stylesheet is generated.
	 */
	$elements_data = $variation_data['elements'] ?? array();
	$blocks_data   = $variation_data['blocks'] ?? array();
	unset( $variation_data['elements'] );
	unset( $variation_data['blocks'] );

	_wp_array_set(
		$blocks_data,
		array( $parsed_block['blockName'], 'variations', $variation_instance ),
		$variation_data
	);

	$config = array(
		'version' => WP_Theme_JSON::LATEST_SCHEMA,
		'styles'  => array(
			'elements' => $elements_data,
			'blocks'   => $blocks_data,
		),
	);

	// Turn off filter that excludes block nodes. They are needed here for the variation's inner block types.
	if ( ! is_admin() ) {
		remove_filter( 'wp_theme_json_get_style_nodes', 'wp_filter_out_block_nodes' );
	}

	// Temporarily prevent variation instance from being sanitized while processing theme.json.
	$styles_registry = WP_Block_Styles_Registry::get_instance();
	$styles_registry->register( $parsed_block['blockName'], array( 'name' => $variation_instance ) );

	$variation_theme_json = new WP_Theme_JSON( $config, 'blocks' );
	$variation_styles     = $variation_theme_json->get_stylesheet(
		array( 'styles' ),
		array( 'custom' ),
		array(
			'include_block_style_variations' => true,
			'skip_root_layout_styles'        => true,
			'scope'                          => ".$class_name",
		)
	);

	// Clean up temporary block style now instance styles have been processed.
	$styles_registry->unregister( $parsed_block['blockName'], $variation_instance );

	// Restore filter that excludes block nodes.
	if ( ! is_admin() ) {
		add_filter( 'wp_theme_json_get_style_nodes', 'wp_filter_out_block_nodes' );
	}

	if ( empty( $variation_styles ) ) {
		return $parsed_block;
	}

	wp_register_style( 'block-style-variation-styles', false, array( 'global-styles', 'wp-block-library' ) );
	wp_add_inline_style( 'block-style-variation-styles', $variation_styles );

	/*
	 * Add variation instance class name to block's className string so it can
	 * be enforced in the block markup via render_block filter.
	 */
	_wp_array_set( $parsed_block, array( 'attrs', 'className' ), $updated_class_name );

	return $parsed_block;
}

/**
 * Ensure the variation block support class name generated and added to
 * block attributes in the `render_block_data` filter gets applied to the
 * block's markup.
 *
 * @see wp_render_block_style_variation_support_styles
 *
 * @since 6.6.0
 * @access private
 *
 * @param  string $block_content Rendered block content.
 * @param  array  $block         Block object.
 *
 * @return string                Filtered block content.
 */
function wp_render_block_style_variation_class_name( $block_content, $block ) {
	if ( ! $block_content || empty( $block['attrs']['className'] ) ) {
		return $block_content;
	}

	/*
	 * Matches a class prefixed by `is-style`, followed by the
	 * variation slug, then `--`, and finally a hash.
	 *
	 * See `wp_create_block_style_variation_instance_name` for class generation.
	 */
	preg_match( '/\bis-style-(\S+?--\w+)\b/', $block['attrs']['className'], $matches );

	if ( empty( $matches ) ) {
		return $block_content;
	}

	$tags = new WP_HTML_Tag_Processor( $block_content );

	if ( $tags->next_tag() ) {
		/*
		 * Ensure the variation instance class name set in the
		 * `render_block_data` filter is applied in markup.
		 * See `wp_render_block_style_variation_support_styles`.
		 */
		$tags->add_class( $matches[0] );
	}

	return $tags->get_updated_html();
}

/**
 * Collects block style variation data for merging with theme.json data.
 *
 * @since 6.6.0
 * @access private
 *
 * @param array $variations Shared block style variations.
 *
 * @return array Block variations data to be merged under `styles.blocks`.
 */
function wp_resolve_block_style_variations( $variations ) {
	$variations_data = array();

	if ( empty( $variations ) ) {
		return $variations_data;
	}

	$have_named_variations = ! wp_is_numeric_array( $variations );

	foreach ( $variations as $key => $variation ) {
		$supported_blocks = $variation['blockTypes'] ?? array();

		/*
		 * Standalone theme.json partial files for block style variations
		 * will have their styles under a top-level property by the same name.
		 * Variations defined within an existing theme.json or theme style
		 * variation will themselves already be the required styles data.
		 */
		$variation_data = $variation['styles'] ?? $variation;

		if ( empty( $variation_data ) ) {
			continue;
		}

		/*
		 * Block style variations read in via standalone theme.json partials
		 * need to have their name set to the kebab case version of their title.
		 */
		$variation_name = $have_named_variations ? $key : ( $variation['slug'] ?? _wp_to_kebab_case( $variation['title'] ) );

		foreach ( $supported_blocks as $block_type ) {
			// Add block style variation data under current block type.
			$path = array( $block_type, 'variations', $variation_name );
			_wp_array_set( $variations_data, $path, $variation_data );
		}
	}

	return $variations_data;
}

/**
 * Merges variations data with existing theme.json data ensuring that the
 * current theme.json data values take precedence.
 *
 * @since 6.6.0
 * @access private
 *
 * @param array              $variations_data Block style variations data keyed by block type.
 * @param WP_Theme_JSON_Data $theme_json      Current theme.json data.
 * @param string             $origin          Origin for the theme.json data.
 *
 * @return WP_Theme_JSON The merged theme.json data.
 */
function wp_merge_block_style_variations_data( $variations_data, $theme_json, $origin = 'theme' ) {
	if ( empty( $variations_data ) ) {
		return $theme_json;
	}

	$variations_theme_json_data = array(
		'version' => WP_Theme_JSON::LATEST_SCHEMA,
		'styles'  => array( 'blocks' => $variations_data ),
	);

	$variations_theme_json = new WP_Theme_JSON_Data( $variations_theme_json_data, $origin );

	/*
	 * Merge the current theme.json data over shared variation data so that
	 * any explicit per block variation values take precedence.
	 */
	return $variations_theme_json->update_with( $theme_json->get_data() );
}

/**
 * Merges any shared block style variation definitions from a theme style
 * variation into their appropriate block type within theme json styles. Any
 * custom user selections already made will take precedence over the shared
 * style variation value.
 *
 * @since 6.6.0
 * @access private
 *
 * @param WP_Theme_JSON_Data $theme_json Current theme.json data.
 *
 * @return WP_Theme_JSON_Data
 */
function wp_resolve_block_style_variations_from_theme_style_variation( $theme_json ) {
	$theme_json_data   = $theme_json->get_data();
	$shared_variations = $theme_json_data['styles']['blocks']['variations'] ?? array();
	$variations_data   = wp_resolve_block_style_variations( $shared_variations );

	return wp_merge_block_style_variations_data( $variations_data, $theme_json, 'user' );
}

/**
 * Merges block style variation data sourced from standalone partial
 * theme.json files.
 *
 * @since 6.6.0
 * @access private
 *
 * @param WP_Theme_JSON_Data $theme_json Current theme.json data.
 *
 * @return WP_Theme_JSON_Data
 */
function wp_resolve_block_style_variations_from_theme_json_partials( $theme_json ) {
	$block_style_variations = WP_Theme_JSON_Resolver::get_style_variations( 'block' );
	$variations_data        = wp_resolve_block_style_variations( $block_style_variations );

	return wp_merge_block_style_variations_data( $variations_data, $theme_json );
}

/**
 * Merges shared block style variations registered within the
 * `styles.blocks.variations` property of the primary theme.json file.
 *
 * @since 6.6.0
 * @access private
 *
 * @param WP_Theme_JSON_Data $theme_json Current theme.json data.
 *
 * @return WP_Theme_JSON_Data
 */
function wp_resolve_block_style_variations_from_primary_theme_json( $theme_json ) {
	$theme_json_data        = $theme_json->get_data();
	$block_style_variations = $theme_json_data['styles']['blocks']['variations'] ?? array();
	$variations_data        = wp_resolve_block_style_variations( $block_style_variations );

	return wp_merge_block_style_variations_data( $variations_data, $theme_json );
}

/**
 * Merges block style variations registered via the block styles registry with a
 * style object, under their appropriate block types within theme.json styles.
 * Any variation values defined within the theme.json specific to a block type
 * will take precedence over these shared definitions.
 *
 * @since 6.6.0
 * @access private
 *
 * @param WP_Theme_JSON_Data $theme_json Current theme.json data.
 *
 * @return WP_Theme_JSON_Data
 */
function wp_resolve_block_style_variations_from_styles_registry( $theme_json ) {
	$registry        = WP_Block_Styles_Registry::get_instance();
	$styles          = $registry->get_all_registered();
	$variations_data = array();

	foreach ( $styles as $block_type => $variations ) {
		foreach ( $variations as $variation_name => $variation ) {
			if ( ! empty( $variation['style_data'] ) ) {
				$path = array( $block_type, 'variations', $variation_name );
				_wp_array_set( $variations_data, $path, $variation['style_data'] );
			}
		}
	}

	return wp_merge_block_style_variations_data( $variations_data, $theme_json );
}

/**
 * Enqueues styles for block style variations.
 *
 * @since 6.6.0
 * @access private
 */
function wp_enqueue_block_style_variation_styles() {
	wp_enqueue_style( 'block-style-variation-styles' );
}

// Register the block support.
WP_Block_Supports::get_instance()->register( 'block-style-variation', array() );

add_filter( 'render_block_data', 'wp_render_block_style_variation_support_styles', 10, 2 );
add_filter( 'render_block', 'wp_render_block_style_variation_class_name', 10, 2 );
add_action( 'wp_enqueue_scripts', 'wp_enqueue_block_style_variation_styles', 1 );

// Resolve block style variations from all their potential sources. The order here is deliberate.
add_filter( 'wp_theme_json_data_theme', 'wp_resolve_block_style_variations_from_primary_theme_json', 10, 1 );
add_filter( 'wp_theme_json_data_theme', 'wp_resolve_block_style_variations_from_theme_json_partials', 10, 1 );
add_filter( 'wp_theme_json_data_theme', 'wp_resolve_block_style_variations_from_styles_registry', 10, 1 );

add_filter( 'wp_theme_json_data_user', 'wp_resolve_block_style_variations_from_theme_style_variation', 10, 1 );

/**
 * Registers any block style variations contained within the provided
 * theme.json data.
 *
 * @since 6.6.0
 * @access private
 *
 * @param array $variations Shared block style variations.
 */
function wp_register_block_style_variations_from_theme_json_data( $variations ) {
	if ( empty( $variations ) ) {
		return $variations;
	}

	$registry              = WP_Block_Styles_Registry::get_instance();
	$have_named_variations = ! wp_is_numeric_array( $variations );

	foreach ( $variations as $key => $variation ) {
		$supported_blocks = $variation['blockTypes'] ?? array();

		/*
		 * Standalone theme.json partial files for block style variations
		 * will have their styles under a top-level property by the same name.
		 * Variations defined within an existing theme.json or theme style
		 * variation will themselves already be the required styles data.
		 */
		$variation_data = $variation['styles'] ?? $variation;

		if ( empty( $variation_data ) ) {
			continue;
		}

		/*
		 * Block style variations read in via standalone theme.json partials
		 * need to have their name set to the kebab case version of their title.
		 */
		$variation_name  = $have_named_variations ? $key : ( $variation['slug'] ?? _wp_to_kebab_case( $variation['title'] ) );
		$variation_label = $variation['title'] ?? $variation_name;

		foreach ( $supported_blocks as $block_type ) {
			$registered_styles = $registry->get_registered_styles_for_block( $block_type );

			// Register block style variation if it hasn't already been registered.
			if ( ! array_key_exists( $variation_name, $registered_styles ) ) {
				register_block_style(
					$block_type,
					array(
						'name'  => $variation_name,
						'label' => $variation_label,
					)
				);
			}
		}
	}
}

/**
 * Register shared block style variations defined by the theme.
 *
 * These can come in three forms:
 * - the theme's theme.json
 * - the theme's partials (standalone files in `/styles` that only define block style variations)
 * - the user's theme.json (for example, theme style variations the user selected)
 *
 * @since 6.6.0
 * @access private
 */
function wp_register_block_style_variations_from_theme() {
	/*
	 * Skip any registration of styles if no theme.json or variation partials are present.
	 *
	 * Given the possibility of hybrid themes, this check can't rely on if the theme
	 * is a block theme or not. Instead:
	 *   - If there is a primary theme.json, continue.
	 *   - If there is a partials directory, continue.
	 *   - The only variations to be registered from the global styles user origin,
	 *     are those that have been copied in from the selected theme style variation.
	 *     For a theme style variation to be selected it would have to have a partial
	 *     theme.json file covered by the previous check.
	 */
	$has_partials_directory = is_dir( get_stylesheet_directory() . '/styles' ) || is_dir( get_template_directory() . '/styles' );
	if ( ! wp_theme_has_theme_json() && ! $has_partials_directory ) {
		return;
	}

	// Partials from `/styles`.
	$variations_partials = WP_Theme_JSON_Resolver::get_style_variations( 'block' );
	wp_register_block_style_variations_from_theme_json_data( $variations_partials );

	/*
	 * Pull the data from the specific origin instead of the merged data.
	 * This is because, for 6.6, we only support registering block style variations
	 * for the 'theme' and 'custom' origins but not for 'default' (core theme.json)
	 * or 'custom' (theme.json in a block).
	 *
	 * When/If we add support for every origin, we should switch to using the public API
	 * instead, e.g.: wp_get_global_styles( array( 'blocks', 'variations' ) ).
	 */

	// theme.json of the theme.
	$theme_json_theme = WP_Theme_JSON_Resolver::get_theme_data();
	$variations_theme = $theme_json_theme->get_data()['styles']['blocks']['variations'] ?? array();
	wp_register_block_style_variations_from_theme_json_data( $variations_theme );

	// User data linked for this theme.
	$theme_json_user = WP_Theme_JSON_Resolver::get_user_data();
	$variations_user = $theme_json_user->get_data()['styles']['blocks']['variations'] ?? array();
	wp_register_block_style_variations_from_theme_json_data( $variations_user );
}
add_action( 'init', 'wp_register_block_style_variations_from_theme' );

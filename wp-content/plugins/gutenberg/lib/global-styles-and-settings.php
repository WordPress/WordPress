<?php
/**
 * API to interact with global settings & styles.
 *
 * @package gutenberg
 */

/**
 * Returns the stylesheet resulting of merging core, theme, and user data.
 *
 * @param array $types Types of styles to load. Optional.
 *                     See {@see 'WP_Theme_JSON::get_stylesheet'} for all valid types.
 *                     If empty, it'll load the following:
 *                     - for themes without theme.json: 'variables', 'presets', 'base-layout-styles'.
 *                     - for themes with theme.json: 'variables', 'presets', 'styles'.
 *
 * @return string Stylesheet.
 */
function gutenberg_get_global_stylesheet( $types = array() ) {
	// Ignore cache when `WP_DEBUG` is enabled, so it doesn't interfere with the theme developers workflow.
	$can_use_cached = empty( $types ) && ! WP_DEBUG;
	$cache_key      = 'gutenberg_get_global_stylesheet';
	$cache_group    = 'theme_json';
	if ( $can_use_cached ) {
		$cached = wp_cache_get( $cache_key, $cache_group );
		if ( $cached ) {
			return $cached;
		}
	}
	$tree = WP_Theme_JSON_Resolver_Gutenberg::get_merged_data();
	$tree = WP_Theme_JSON_Resolver_Gutenberg::resolve_theme_file_uris( $tree );

	$supports_theme_json = wp_theme_has_theme_json();
	if ( empty( $types ) && ! $supports_theme_json ) {
		$types = array( 'variables', 'presets', 'base-layout-styles' );
	} elseif ( empty( $types ) ) {
		$types = array( 'variables', 'presets', 'styles' );
	}

	/*
	 * If variables are part of the stylesheet,
	 * we add them.
	 *
	 * This is so themes without a theme.json still work as before 5.9:
	 * they can override the default presets.
	 * See https://core.trac.wordpress.org/ticket/54782
	 */
	$styles_variables = '';
	if ( in_array( 'variables', $types, true ) ) {
		/*
		 * We only use the default, theme, and custom origins.
		 * This is because styles for blocks origin are added
		 * at a later phase (render cycle) so we only render the ones in use.
		 * @see wp_add_global_styles_for_blocks
		 */
		$origins          = array( 'default', 'theme', 'custom' );
		$styles_variables = $tree->get_stylesheet( array( 'variables' ), $origins );
		$types            = array_diff( $types, array( 'variables' ) );
	}

	/*
	 * For the remaining types (presets, styles), we do consider origins:
	 *
	 * - themes without theme.json: only the classes for the presets defined by core
	 * - themes with theme.json: the presets and styles classes, both from core and the theme
	 */
	$styles_rest = '';
	if ( ! empty( $types ) ) {
		/*
		 * We only use the default, theme, and custom origins.
		 * This is because styles for blocks origin are added
		 * at a later phase (render cycle) so we only render the ones in use.
		 * @see wp_add_global_styles_for_blocks
		 */
		$origins = array( 'default', 'theme', 'custom' );

		/*
		* If the theme doesn't have theme.json but supports both appearance tools and color palette,
		* the 'theme' origin should be included so color palette presets are also output.
		*/
		if ( ! $supports_theme_json && ( current_theme_supports( 'appearance-tools' ) || current_theme_supports( 'border' ) ) && current_theme_supports( 'editor-color-palette' ) ) {
			$origins = array( 'default', 'theme' );
		} elseif ( ! $supports_theme_json ) {
			$origins = array( 'default' );
		}
		$styles_rest = $tree->get_stylesheet( $types, $origins );
	}
	$stylesheet = $styles_variables . $styles_rest;
	if ( $can_use_cached ) {
		wp_cache_set( $cache_key, $stylesheet, $cache_group );
	}
	return $stylesheet;
}

/**
 * Function to get the settings resulting of merging core, theme, and user data.
 *
 * @param array $path    Path to the specific setting to retrieve. Optional.
 *                       If empty, will return all settings.
 * @param array $context {
 *     Metadata to know where to retrieve the $path from. Optional.
 *
 *     @type string $block_name Which block to retrieve the settings from.
 *                              If empty, it'll return the settings for the global context.
 *     @type string $origin     Which origin to take data from.
 *                              Valid values are 'all' (core, theme, and user) or 'base' (core and theme).
 *                              If empty or unknown, 'all' is used.
 * }
 *
 * @return array The settings to retrieve.
 */
function gutenberg_get_global_settings( $path = array(), $context = array() ) {
	if ( ! empty( $context['block_name'] ) ) {
		$new_path = array( 'blocks', $context['block_name'] );
		foreach ( $path as $subpath ) {
			$new_path[] = $subpath;
		}
		$path = $new_path;
	}

	// This is the default value when no origin is provided or when it is 'all'.
	$origin = 'custom';
	if (
		! wp_theme_has_theme_json() ||
		( isset( $context['origin'] ) && 'base' === $context['origin'] )
	) {
		$origin = 'theme';
	}

	$cache_group = 'theme_json';
	$cache_key   = 'gutenberg_get_global_settings_' . $origin;
	$settings    = wp_cache_get( $cache_key, $cache_group );

	if ( false === $settings || WP_DEBUG ) {
		$settings = WP_Theme_JSON_Resolver_Gutenberg::get_merged_data( $origin )->get_settings();
		wp_cache_set( $cache_key, $settings, $cache_group );
	}

	return _wp_array_get( $settings, $path, $settings );
}

/**
 * Gets the global styles custom css from theme.json.
 *
 * @deprecated Gutenberg 18.6.0 Use {@see 'gutenberg_get_global_stylesheet'} instead for top-level custom CSS, or {@see 'WP_Theme_JSON_Gutenberg::get_styles_for_block'} for block-level custom CSS.
 *
 * @return string
 */
function gutenberg_get_global_styles_custom_css() {
	_deprecated_function( __FUNCTION__, 'Gutenberg 18.6.0', 'gutenberg_get_global_stylesheet' );
	// Ignore cache when `WP_DEBUG` is enabled, so it doesn't interfere with the theme developers workflow.
	$can_use_cached = ! WP_DEBUG;
	$cache_key      = 'gutenberg_get_global_custom_css';
	$cache_group    = 'theme_json';
	if ( $can_use_cached ) {
		$cached = wp_cache_get( $cache_key, $cache_group );
		if ( $cached ) {
			return $cached;
		}
	}

	if ( ! wp_theme_has_theme_json() ) {
		return '';
	}

	$tree       = WP_Theme_JSON_Resolver_Gutenberg::get_merged_data();
	$stylesheet = $tree->get_custom_css();

	if ( $can_use_cached ) {
		wp_cache_set( $cache_key, $stylesheet, $cache_group );
	}

	return $stylesheet;
}

/**
 * Gets the global styles base custom CSS from theme.json.
 *
 * @since 6.6.0
 *
 * @return string The global base custom CSS.
 */
function gutenberg_get_global_styles_base_custom_css() {
	_deprecated_function( __FUNCTION__, 'Gutenberg 18.6.0', 'gutenberg_get_global_stylesheet' );
	if ( ! wp_theme_has_theme_json() ) {
		return '';
	}

	$can_use_cached = ! WP_DEBUG;

	$cache_key   = 'gutenberg_get_global_styles_base_custom_css';
	$cache_group = 'theme_json';
	if ( $can_use_cached ) {
		$cached = wp_cache_get( $cache_key, $cache_group );
		if ( $cached ) {
			return $cached;
		}
	}

	$tree       = WP_Theme_JSON_Resolver_Gutenberg::get_merged_data();
	$stylesheet = $tree->get_base_custom_css();

	if ( $can_use_cached ) {
		wp_cache_set( $cache_key, $stylesheet, $cache_group );
	}

	return $stylesheet;
}

/**
 * Adds the global styles per-block custom CSS from theme.json
 * to the inline style for each block.
 *
 * @since 6.6.0
 *
 * @global WP_Styles $wp_styles
 */
function gutenberg_add_global_styles_block_custom_css() {
	_deprecated_function( __FUNCTION__, 'Gutenberg 18.6.0', 'gutenberg_add_global_styles_for_blocks' );
	global $wp_styles;

	if ( ! wp_theme_has_theme_json() || ! wp_should_load_separate_core_block_assets() ) {
		return;
	}

	$tree        = WP_Theme_JSON_Resolver_Gutenberg::get_merged_data();
	$block_nodes = $tree->get_block_custom_css_nodes();

	foreach ( $block_nodes as $metadata ) {
		$block_css = $tree->get_block_custom_css( $metadata['css'], $metadata['selector'] );

		$stylesheet_handle = 'global-styles';

		/*
		 * When `wp_should_load_separate_core_block_assets()` is true, follow a similar
		 * logic to the one in `gutenberg_add_global_styles_for_blocks` to add the custom
		 * css only when the block is rendered.
		 */
		if ( isset( $metadata['name'] ) ) {
			if ( str_starts_with( $metadata['name'], 'core/' ) ) {
				$block_name   = str_replace( 'core/', '', $metadata['name'] );
				$block_handle = 'wp-block-' . $block_name;
				if ( in_array( $block_handle, $wp_styles->queue, true ) ) {
					wp_add_inline_style( $stylesheet_handle, $block_css );
				}
			} else {
				wp_add_inline_style( $stylesheet_handle, $block_css );
			}
		}
	}
}


/**
 * Adds global style rules to the inline style for each block.
 *
 * @global WP_Styles $wp_styles
 *
 * @return void
 */
function gutenberg_add_global_styles_for_blocks() {
	global $wp_styles;
	$tree        = WP_Theme_JSON_Resolver_Gutenberg::get_merged_data();
	$tree        = WP_Theme_JSON_Resolver_Gutenberg::resolve_theme_file_uris( $tree );
	$block_nodes = $tree->get_styles_block_nodes();

	$can_use_cached = ! wp_is_development_mode( 'theme' );
	$update_cache   = false;

	if ( $can_use_cached ) {
		// Hash the merged WP_Theme_JSON data to bust cache on settings or styles change.
		$cache_hash = md5( wp_json_encode( $tree->get_raw_data() ) );
		$cache_key  = 'wp_styles_for_blocks';
		$cached     = get_transient( $cache_key );

		// Reset the cached data if there is no value or if the hash has changed.
		if ( ! is_array( $cached ) || $cached['hash'] !== $cache_hash ) {
			$cached = array(
				'hash'   => $cache_hash,
				'blocks' => array(),
			);

			// Update the cache if the hash has changed.
			$update_cache = true;
		}
	}

	foreach ( $block_nodes as $metadata ) {
		if ( $can_use_cached ) {
			// Use the block name as the key for cached CSS data. Otherwise, use a hash of the metadata.
			$cache_node_key = isset( $metadata['name'] ) ? $metadata['name'] : md5( wp_json_encode( $metadata ) );

			if ( isset( $cached['blocks'][ $cache_node_key ] ) ) {
				$block_css = $cached['blocks'][ $cache_node_key ];
			} else {
				$block_css                           = $tree->get_styles_for_block( $metadata );
				$cached['blocks'][ $cache_node_key ] = $block_css;

				// Update the cache if the cache contents have changed.
				$update_cache = true;
			}
		} else {
			$block_css = $tree->get_styles_for_block( $metadata );
		}

		if ( ! wp_should_load_separate_core_block_assets() ) {
			wp_add_inline_style( 'global-styles', $block_css );
			continue;
		}

		$stylesheet_handle = 'global-styles';

		/*
		 * When `wp_should_load_separate_core_block_assets()` is true, block styles are
		 * enqueued for each block on the page in class WP_Block's render function.
		 * This means there will be a handle in the styles queue for each of those blocks.
		 * Block-specific global styles should be attached to the global-styles handle, but
		 * only for blocks on the page, thus we check if the block's handle is in the queue
		 * before adding the inline style.
		 * This conditional loading only applies to core blocks.
		 */
		if ( isset( $metadata['name'] ) ) {
			if ( str_starts_with( $metadata['name'], 'core/' ) ) {
				$block_name   = str_replace( 'core/', '', $metadata['name'] );
				$block_handle = 'wp-block-' . $block_name;
				if ( in_array( $block_handle, $wp_styles->queue, true ) ) {
					wp_add_inline_style( $stylesheet_handle, $block_css );
				}
			} else {
				wp_add_inline_style( $stylesheet_handle, $block_css );
			}
		}

		// The likes of block element styles from theme.json do not have  $metadata['name'] set.
		if ( ! isset( $metadata['name'] ) && ! empty( $metadata['path'] ) ) {
			$block_name = wp_get_block_name_from_theme_json_path( $metadata['path'] );
			if ( $block_name ) {
				if ( str_starts_with( $block_name, 'core/' ) ) {
					$block_name   = str_replace( 'core/', '', $block_name );
					$block_handle = 'wp-block-' . $block_name;
					if ( in_array( $block_handle, $wp_styles->queue, true ) ) {
						wp_add_inline_style( $stylesheet_handle, $block_css );
					}
				} else {
					wp_add_inline_style( $stylesheet_handle, $block_css );
				}
			}
		}
	}

	if ( $update_cache ) {
		set_transient( $cache_key, $cached );
	}
}

/**
 * Private function to clean the caches used by gutenberg_get_global_settings method.
 *
 * @access private
 */
function _gutenberg_clean_theme_json_caches() {
	wp_cache_delete( 'wp_theme_has_theme_json', 'theme_json' );
	wp_cache_delete( 'gutenberg_get_global_stylesheet', 'theme_json' );
	wp_cache_delete( 'gutenberg_get_global_settings_custom', 'theme_json' );
	wp_cache_delete( 'gutenberg_get_global_settings_theme', 'theme_json' );
	wp_cache_delete( 'gutenberg_get_global_custom_css', 'theme_json' );
	wp_cache_delete( 'gutenberg_get_global_styles_base_custom_css', 'theme_json' );
	WP_Theme_JSON_Resolver_Gutenberg::clean_cached_data();
}
add_action( 'start_previewing_theme', '_gutenberg_clean_theme_json_caches' );
add_action( 'switch_theme', '_gutenberg_clean_theme_json_caches' );

/**
 * Gets the styles resulting of merging core, theme, and user data.
 *
 * @since 5.9.0
 * @since 6.3.0 the internal link format "var:preset|color|secondary" is resolved
 * to "var(--wp--preset--font-size--small)" so consumers don't have to.
 * @since 6.3.0 `transforms` is now usable in the `context` parameter. In case [`transforms`]['resolve_variables']
 * is defined, variables are resolved to their value in the styles.
 *
 * @param array $path    Path to the specific style to retrieve. Optional.
 *                       If empty, will return all styles.
 * @param array $context {
 *     Metadata to know where to retrieve the $path from. Optional.
 *
 *     @type string $block_name Which block to retrieve the styles from.
 *                              If empty, it'll return the styles for the global context.
 *     @type string $origin     Which origin to take data from.
 *                              Valid values are 'all' (core, theme, and user) or 'base' (core and theme).
 *                              If empty or unknown, 'all' is used.
 *     @type array $transforms Which transformation(s) to apply.
 *                              Valid value is array( 'resolve-variables' ).
 *                              If defined, variables are resolved to their value in the styles.
 * }
 * @return array The styles to retrieve.
 */
function gutenberg_get_global_styles( $path = array(), $context = array() ) {
	if ( ! empty( $context['block_name'] ) ) {
		$path = array_merge( array( 'blocks', $context['block_name'] ), $path );
	}

	$origin = 'custom';
	if ( isset( $context['origin'] ) && 'base' === $context['origin'] ) {
		$origin = 'theme';
	}

	$resolve_variables = isset( $context['transforms'] )
	&& is_array( $context['transforms'] )
	&& in_array( 'resolve-variables', $context['transforms'], true );

	$merged_data = WP_Theme_JSON_Resolver_Gutenberg::get_merged_data( $origin );
	if ( $resolve_variables ) {
		$merged_data = WP_Theme_JSON_Gutenberg::resolve_variables( $merged_data );
	}
	$styles = $merged_data->get_raw_data()['styles'];
	return _wp_array_get( $styles, $path, $styles );
}

/**
 * Returns the current theme's wanted patterns (slugs) to be
 * registered from Pattern Directory.
 *
 * @since 6.3.0
 *
 * @return string[]
 */
function gutenberg_get_theme_directory_pattern_slugs() {
	return WP_Theme_JSON_Resolver_Gutenberg::get_theme_data( array(), array( 'with_supports' => false ) )->get_patterns();
}

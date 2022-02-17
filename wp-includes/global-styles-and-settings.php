<?php
/**
 * APIs to interact with global settings & styles.
 *
 * @package WordPress
 */

/**
 * Function to get the settings resulting of merging core, theme, and user data.
 *
 * @since 5.9.0
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
function wp_get_global_settings( $path = array(), $context = array() ) {
	if ( ! empty( $context['block_name'] ) ) {
		$path = array_merge( array( 'blocks', $context['block_name'] ), $path );
	}

	$origin = 'custom';
	if ( isset( $context['origin'] ) && 'base' === $context['origin'] ) {
		$origin = 'theme';
	}

	$settings = WP_Theme_JSON_Resolver::get_merged_data( $origin )->get_settings();

	return _wp_array_get( $settings, $path, $settings );
}

/**
 * Function to get the styles resulting of merging core, theme, and user data.
 *
 * @since 5.9.0
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
 * }
 *
 * @return array The styles to retrieve.
 */
function wp_get_global_styles( $path = array(), $context = array() ) {
	if ( ! empty( $context['block_name'] ) ) {
		$path = array_merge( array( 'blocks', $context['block_name'] ), $path );
	}

	$origin = 'custom';
	if ( isset( $context['origin'] ) && 'base' === $context['origin'] ) {
		$origin = 'theme';
	}

	$styles = WP_Theme_JSON_Resolver::get_merged_data( $origin )->get_raw_data()['styles'];

	return _wp_array_get( $styles, $path, $styles );
}

/**
 * Returns the stylesheet resulting of merging core, theme, and user data.
 *
 * @since 5.9.0
 *
 * @param array $types Types of styles to load. Optional.
 *                     It accepts 'variables', 'styles', 'presets' as values.
 *                     If empty, it'll load all for themes with theme.json support
 *                     and only [ 'variables', 'presets' ] for themes without theme.json support.
 *
 * @return string Stylesheet.
 */
function wp_get_global_stylesheet( $types = array() ) {
	// Return cached value if it can be used and exists.
	// It's cached by theme to make sure that theme switching clears the cache.
	$can_use_cached = (
		( empty( $types ) ) &&
		( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) &&
		( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) &&
		( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) &&
		! is_admin()
	);
	$transient_name = 'global_styles_' . get_stylesheet();
	if ( $can_use_cached ) {
		$cached = get_transient( $transient_name );
		if ( $cached ) {
			return $cached;
		}
	}

	$tree = WP_Theme_JSON_Resolver::get_merged_data();

	$supports_theme_json = WP_Theme_JSON_Resolver::theme_has_support();
	if ( empty( $types ) && ! $supports_theme_json ) {
		$types = array( 'variables', 'presets' );
	} elseif ( empty( $types ) ) {
		$types = array( 'variables', 'styles', 'presets' );
	}

	/*
	 * If variables are part of the stylesheet,
	 * we add them for all origins (default, theme, user).
	 * This is so themes without a theme.json still work as before 5.9:
	 * they can override the default presets.
	 * See https://core.trac.wordpress.org/ticket/54782
	 */
	$styles_variables = '';
	if ( in_array( 'variables', $types, true ) ) {
		$styles_variables = $tree->get_stylesheet( array( 'variables' ) );
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
		$origins = array( 'default', 'theme', 'custom' );
		if ( ! $supports_theme_json ) {
			$origins = array( 'default' );
		}
		$styles_rest = $tree->get_stylesheet( $types, $origins );
	}

	$stylesheet = $styles_variables . $styles_rest;

	if ( $can_use_cached ) {
		// Cache for a minute.
		// This cache doesn't need to be any longer, we only want to avoid spikes on high-traffic sites.
		set_transient( $transient_name, $stylesheet, MINUTE_IN_SECONDS );
	}

	return $stylesheet;
}

/**
 * Returns a string containing the SVGs to be referenced as filters (duotone).
 *
 * @since 5.9.1
 *
 * @return string
 */
function wp_get_global_styles_svg_filters() {
	// Return cached value if it can be used and exists.
	// It's cached by theme to make sure that theme switching clears the cache.
	$can_use_cached = (
		( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) &&
		( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) &&
		( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) &&
		! is_admin()
	);
	$transient_name = 'global_styles_svg_filters_' . get_stylesheet();
	if ( $can_use_cached ) {
		$cached = get_transient( $transient_name );
		if ( $cached ) {
			return $cached;
		}
	}

	$supports_theme_json = WP_Theme_JSON_Resolver::theme_has_support();

	$origins = array( 'default', 'theme', 'custom' );
	if ( ! $supports_theme_json ) {
		$origins = array( 'default' );
	}

	$tree = WP_Theme_JSON_Resolver::get_merged_data();
	$svgs = $tree->get_svg_filters( $origins );

	if ( $can_use_cached ) {
		// Cache for a minute, same as wp_get_global_stylesheet.
		set_transient( $transient_name, $svgs, MINUTE_IN_SECONDS );
	}

	return $svgs;
}

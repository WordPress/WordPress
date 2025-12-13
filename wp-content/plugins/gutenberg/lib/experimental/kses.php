<?php
/**
 * Temporary compatibility shims for kses rules present in Gutenberg.
 *
 * The functions in this file should not be backported to core.
 *
 * @package gutenberg
 */

/**
 * Sanitizes global styles user content removing unsafe rules.
 *
 * This function is identical to the core version, but called the
 * Gutenberg version of the theme JSON class (`WP_Theme_JSON_Gutenberg`).
 *
 * This function should not be backported to core.
 *
 * @since 5.9.0
 *
 * @param string $data Post content to filter.
 * @return string Filtered post content with unsafe rules removed.
 */
function gutenberg_filter_global_styles_post( $data ) {
	$decoded_data        = json_decode( wp_unslash( $data ), true );
	$json_decoding_error = json_last_error();
	if (
		JSON_ERROR_NONE === $json_decoding_error &&
		is_array( $decoded_data ) &&
		isset( $decoded_data['isGlobalStylesUserThemeJSON'] ) &&
		$decoded_data['isGlobalStylesUserThemeJSON']
	) {
		unset( $decoded_data['isGlobalStylesUserThemeJSON'] );

		$data_to_encode = WP_Theme_JSON_Gutenberg::remove_insecure_properties( $decoded_data, 'custom' );

		$data_to_encode['isGlobalStylesUserThemeJSON'] = true;
		return wp_slash( wp_json_encode( $data_to_encode ) );
	}
	return $data;
}

/**
 * Override core's kses_init_filters hooks for global styles,
 * and use Gutenberg's version instead. This ensures that
 * Gutenberg's `remove_insecure_properties` function can be called.
 *
 * The hooks are only set if they are already added, which ensures
 * that global styles is only filtered for users without the `unfiltered_html`
 * capability.
 *
 * This function should not be backported to core.
 */
function gutenberg_override_core_kses_init_filters() {
	if ( has_filter( 'content_save_pre', 'wp_filter_global_styles_post' ) ) {
		remove_filter( 'content_save_pre', 'wp_filter_global_styles_post', 9 );
		add_filter( 'content_save_pre', 'gutenberg_filter_global_styles_post', 9 );
	}

	if ( has_filter( 'content_filtered_save_pre', 'wp_filter_global_styles_post' ) ) {
		remove_filter( 'content_filtered_save_pre', 'wp_filter_global_styles_post', 9 );
		add_filter( 'content_filtered_save_pre', 'gutenberg_filter_global_styles_post', 9 );
	}
}
// The 'kses_init_filters' is usually initialized with default priority. Use higher priority to override.
add_action( 'init', 'gutenberg_override_core_kses_init_filters', 20 );
add_action( 'set_current_user', 'gutenberg_override_core_kses_init_filters' );

if ( ! function_exists( 'allow_filter_in_styles' ) ) {
	/**
	 * See https://github.com/WordPress/wordpress-develop/pull/4108
	 *
	 * Mark CSS safe if it contains a "filter: url('#wp-duotone-...')" rule.
	 *
	 * This function should not be backported to core.
	 *
	 * @param bool   $allow_css Whether the CSS is allowed.
	 * @param string $css_test_string The CSS to test.
	 */
	function allow_filter_in_styles( $allow_css, $css_test_string ) {
		if ( preg_match(
			"/^filter:\s*url\((['\"]?)#wp-duotone-[-a-zA-Z0-9]+\\1\)(\s+!important)?$/",
			$css_test_string
		) ) {
			return true;
		}
		return $allow_css;
	}
}
add_filter( 'safecss_filter_attr_allow_css', 'allow_filter_in_styles', 10, 2 );

/**
 * Update allowed inline style attributes list.
 *
 * @param string[] $attrs Array of allowed CSS attributes.
 * @return string[] CSS attributes.
 */
function gutenberg_safe_grid_attrs( $attrs ) {
	$attrs[] = 'grid-column';
	$attrs[] = 'grid-row';
	$attrs[] = 'container-type';
	return $attrs;
}
add_filter( 'safe_style_css', 'gutenberg_safe_grid_attrs' );

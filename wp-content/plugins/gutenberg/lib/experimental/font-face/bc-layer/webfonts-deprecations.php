<?php
/**
 * Deprecated functions provided here to give extenders time to change
 * their plugins/themes before this API is introduced into Core.
 *
 * BACKPORT NOTE: Do not backport these deprecated functions to Core.
 *
 * @package    WordPress
 * @subpackage Fonts API
 * @since      X.X.X
 */

if ( ! function_exists( 'wp_webfonts' ) ) {
	/**
	 * Initialize $wp_webfonts if it has not been set.
	 *
	 * @since X.X.X
	 * @deprecated 15.1 Use wp_fonts() instead.
	 * @deprecated 16.3.0 No longer functional. Do not use.
	 *
	 * @global WP_Webfonts $wp_webfonts
	 *
	 * @return WP_Webfonts WP_Webfonts instance.
	 */
	function wp_webfonts() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 15.1' );

		global $wp_webfonts;

		if ( ! ( $wp_webfonts instanceof WP_Webfonts ) ) {
			$wp_webfonts = new WP_Webfonts();
		}

		return $wp_webfonts;
	}
}

if ( ! function_exists( 'wp_register_webfonts' ) ) {
	/**
	 * Registers one or more font-families and each of their variations.
	 *
	 * @since X.X.X
	 * @deprecated 15.1 Use wp_register_fonts() instead.
	 * @deprecated 16.3.0 Register is not supported.
	 *
	 * @return array Empty array.
	 */
	function wp_register_webfonts() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 15.1' );
		return array();
	}
}

if ( ! function_exists( 'wp_register_webfont' ) ) {
	/**
	 * Registers a single webfont.
	 *
	 * @since X.X.X
	 * @deprecated 14.9.1 Use wp_register_fonts().
	 * @deprecated 16.3.0 Register is not supported.
	 *
	 * @return bool False.
	 */
	function wp_register_webfont() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 14.9.1' );
		return false;
	}
}

if ( ! function_exists( 'wp_enqueue_webfonts' ) ) {
	/**
	 * Enqueues one or more font family and all of its variations.
	 *
	 * @since X.X.X
	 * @deprecated 15.1 Use wp_enqueue_fonts() instead.
	 * @deprecated 16.3.0 Enqueue is not supported.
	 */
	function wp_enqueue_webfonts() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 15.1' );
	}
}

if ( ! function_exists( 'wp_enqueue_webfont' ) ) {
	/**
	 * Enqueue a single font family that has been registered beforehand.
	 *
	 * @since X.X.X
	 * @deprecated 14.9.1 Use wp_enqueue_fonts() instead.
	 * @deprecated 16.3.0 Enqueue is not supported.
	 *
	 * @return bool False.
	 */
	function wp_enqueue_webfont() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 14.9.1' );
		return false;
	}
}

if ( ! function_exists( 'wp_enqueue_webfont_variations' ) ) {
	/**
	 * Enqueues a specific set of web font variations.
	 *
	 * @since X.X.X
	 * @deprecated 15.1 Use wp_enqueue_font_variations() instead.
	 * @deprecated 16.3.0 No longer functional. Do not use.
	 */
	function wp_enqueue_webfont_variations() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 15.1' );
	}
}

if ( ! function_exists( 'wp_deregister_webfont_variation' ) ) {
	/**
	 * Deregisters a font variation.
	 *
	 * @since 14.9.1
	 * @deprecated 15.1 Use wp_deregister_font_variation() instead.
	 * @deprecated 16.3.0 Deregister is not supported.
	 */
	function wp_deregister_webfont_variation() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 15.1' );
	}
}

if ( ! function_exists( 'wp_get_webfont_providers' ) ) {
	/**
	 * Gets all registered providers.
	 *
	 * @since X.X.X
	 * @deprecated 14.9.1 Use wp_fonts()->get_providers().
	 * @deprecated 16.3.0 Providers are not supported.
	 *
	 * @return array Empty array.
	 */
	function wp_get_webfont_providers() {
		_deprecated_function( __FUNCTION__, '14.9.1' );

		return array();
	}
}

if ( ! function_exists( 'wp_register_webfont_provider' ) ) {
	/**
	 * Registers a custom font service provider.
	 *
	 * @since X.X.X
	 * @deprecated 15.1 Use wp_register_font_provider() instead.
	 * @deprecated 16.3.0 Providers are not supported.
	 *
	 * @return bool False.
	 */
	function wp_register_webfont_provider() {
		_deprecated_function( __FUNCTION__, 'GB 15.1', 'wp_register_font_provider' );
		return false;
	}
}

if ( ! function_exists( 'wp_print_webfonts' ) ) {
	/**
	 * Invokes each provider to process and print its styles.
	 *
	 * @since 14.9.1
	 * @deprecated 15.1 Use wp_print_fonts() instead.
	 * @deprecated 16.3.0 Webfonts API is not supported.
	 *
	 * @return array Empty array.
	 */
	function wp_print_webfonts() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 15.1', 'wp_print_font_faces' );
		return array();
	}
}

if ( ! function_exists( 'wp_fonts' ) ) {
	/**
	 * Initialize $wp_fonts if it has not been set.
	 *
	 * @since X.X.X
	 * @deprecated 16.3.0 Use Font Library and Font Face. Fonts API is not supported.
	 *
	 * @global WP_Fonts $wp_fonts
	 *
	 * @return WP_Fonts WP_Fonts instance.
	 */
	function wp_fonts() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 16.3' );

		global $wp_fonts;

		if ( ! ( $wp_fonts instanceof WP_Fonts ) ) {
			$wp_fonts = new WP_Fonts();
		}

		return $wp_fonts;
	}
}

if ( ! function_exists( 'wp_register_fonts' ) ) {
	/**
	 * Registers one or more font-families and each of their variations.
	 *
	 * @since X.X.X
	 * @deprecated 16.3.0 Register is not supported.
	 *
	 * @return array Empty array.
	 */
	function wp_register_fonts() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 16.3' );
		return array();
	}
}

if ( ! function_exists( 'wp_enqueue_fonts' ) ) {
	/**
	 * Enqueues one or more font family and all of its variations.
	 *
	 * @since X.X.X
	 * @deprecated 16.3.0 Enqueue is not supported.
	 */
	function wp_enqueue_fonts() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 16.3' );
	}
}

if ( ! function_exists( 'wp_enqueue_font_variations' ) ) {
	/**
	 * Enqueues a specific set of font variations.
	 *
	 * @since X.X.X
	 * @deprecated 16.3.0 Enqueue is not supported.
	 */
	function wp_enqueue_font_variations() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 16.3' );
	}
}

if ( ! function_exists( 'wp_deregister_font_family' ) ) {
	/**
	 * Deregisters a font family and all of its registered variations.
	 *
	 * @since X.X.X
	 * @deprecated 16.3.0 Deregister is not supported.
	 */
	function wp_deregister_font_family() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 16.3' );
	}
}

if ( ! function_exists( 'wp_deregister_font_variation' ) ) {
	/**
	 * Deregisters a font variation.
	 *
	 * @since X.X.X
	 * @deprecated 16.3.0 Deregister is not supported.
	 */
	function wp_deregister_font_variation() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 16.3' );
	}
}

if ( ! function_exists( 'wp_register_font_provider' ) ) {
	/**
	 * Registers a custom font service provider.
	 *
	 * @since X.X.X
	 * @deprecated 16.3.0 Providers are not supported.
	 *
	 * @return bool False.
	 */
	function wp_register_font_provider() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 16.3' );
		return false;
	}
}

if ( ! function_exists( 'wp_print_fonts' ) ) {
	/**
	 * Invokes each provider to process and print its styles.
	 *
	 * @since X.X.X
	 * @deprecated 16.3.0 For classic themes, use wp_print_font_faces(). For all other sites,
	 *                    Font Face will automatically print all fonts in theme.json merged data layer,
	 *                    including in theme and user activated fonts from the Font Library.
	 *
	 * @return array Empty array.
	 */
	function wp_print_fonts() {
		_deprecated_function( __FUNCTION__, 'Gutenberg 16.3', 'wp_print_font_faces' );
		return array();
	}
}

<?php

/**
 * a fully static class to manage strings translations on admin side
 *
 * @since 1.6
 */
class PLL_Admin_Strings {
	static protected $strings = array(); // strings to translate
	static protected $default_strings; // default strings to register

	/**
	 * init: add filters
	 *
	 * @since 1.6
	 */
	static public function init() {
		// default strings translations sanitization
		add_filter( 'pll_sanitize_string_translation', array( __CLASS__, 'sanitize_string_translation' ), 10, 2 );
	}

	/**
	 * register strings for translation making sure it is not duplicate or empty
	 *
	 * @since 0.6
	 *
	 * @param string $name      A unique name for the string
	 * @param string $string    The string to register
	 * @param string $context   Optional, the group in which the string is registered, defaults to 'polylang'
	 * @param bool   $multiline Optional, whether the string table should display a multiline textarea or a single line input, defaults to single line
	 */
	static public function register_string( $name, $string, $context = 'Polylang', $multiline = false ) {
		// backward compatibility with Polylang older than 1.1
		if ( is_bool( $context ) ) {
			$multiline = $context;
			$context = 'Polylang';
		}

		if ( $string && is_scalar( $string ) ) {
			self::$strings[ md5( $string ) ] = compact( 'name', 'string', 'context', 'multiline' );
		}
	}

	/**
	 * get registered strings
	 *
	 * @since 0.6.1
	 *
	 * @return array list of all registered strings
	 */
	static public function &get_strings() {
		self::$default_strings = array(
			'options' => array(
				'blogname'        => __( 'Site Title' ),
				'blogdescription' => __( 'Tagline' ),
				'date_format'     => __( 'Date Format' ),
				'time_format'     => __( 'Time Format' ),
			),
			'widget_title' => __( 'Widget title', 'polylang' ),
			'widget_text'  => __( 'Widget text', 'polylang' ),
		);

		// WP strings
		foreach ( self::$default_strings['options'] as $option => $string ) {
			self::register_string( $string, get_option( $option ), 'WordPress' );
		}

		// widgets titles
		global $wp_registered_widgets;
		$sidebars = wp_get_sidebars_widgets();
		foreach ( $sidebars as $sidebar => $widgets ) {
			if ( 'wp_inactive_widgets' == $sidebar || empty( $widgets ) ) {
				continue;
			}

			foreach ( $widgets as $widget ) {
				// nothing can be done if the widget is created using pre WP2.8 API :(
				// there is no object, so we can't access it to get the widget options
				if ( ! isset( $wp_registered_widgets[ $widget ]['callback'][0] ) || ! is_object( $wp_registered_widgets[ $widget ]['callback'][0] ) || ! method_exists( $wp_registered_widgets[ $widget ]['callback'][0], 'get_settings' ) ) {
					continue;
				}

				$widget_settings = $wp_registered_widgets[ $widget ]['callback'][0]->get_settings();
				$number = $wp_registered_widgets[ $widget ]['params'][0]['number'];

				// don't enable widget translation if the widget is visible in only one language or if there is no title
				if ( empty( $widget_settings[ $number ]['pll_lang'] ) ) {
					if ( isset( $widget_settings[ $number ]['title'] ) && $title = $widget_settings[ $number ]['title'] ) {
						self::register_string( self::$default_strings['widget_title'], $title, 'Widget' );
					}

					if ( isset( $widget_settings[ $number ]['text'] ) && $text = $widget_settings[ $number ]['text'] ) {
						self::register_string( self::$default_strings['widget_text'], $text, 'Widget', true );
					}
				}
			}
		}

		/**
		 * Filter the list of strings registered for translation
		 * Mainly for use by our PLL_WPML_Compat class
		 *
		 * @since 1.0.2
		 *
		 * @param array $strings list of strings
		 */
		self::$strings = apply_filters( 'pll_get_strings', self::$strings );
		return self::$strings;
	}

	/**
	 * performs the sanitization ( before saving in DB ) of default strings translations
	 *
	 * @since 1.6
	 *
	 * @param string $translation translation to sanitize
	 * @param string $name        unique name for the string
	 * @return string
	 */
	static public function sanitize_string_translation( $translation, $name ) {
		$translation = wp_unslash( trim( $translation ) );

		if ( false !== ( $option = array_search( $name, self::$default_strings['options'], true ) ) ) {
			$translation = sanitize_option( $option, $translation );
		}

		if ( $name == self::$default_strings['widget_title'] ) {
			$translation = strip_tags( $translation );
		}

		if ( $name == self::$default_strings['widget_text'] && ! current_user_can( 'unfiltered_html' ) ) {
			$translation = wp_unslash( wp_filter_post_kses( addslashes( $translation ) ) ); // wp_filter_post_kses() expects slashed
		}

		return $translation;
	}
}

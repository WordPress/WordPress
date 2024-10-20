<?php
/**
 * WP_Font_Face class.
 *
 * @package    WordPress
 * @subpackage Fonts
 * @since      6.4.0
 */

/**
 * Font Face generates and prints `@font-face` styles for given fonts.
 *
 * @since 6.4.0
 */
class WP_Font_Face {

	/**
	 * The font-face property defaults.
	 *
	 * @since 6.4.0
	 *
	 * @var string[]
	 */
	private $font_face_property_defaults = array(
		'font-family'  => '',
		'font-style'   => 'normal',
		'font-weight'  => '400',
		'font-display' => 'fallback',
	);

	/**
	 * Valid font-face property names.
	 *
	 * @since 6.4.0
	 *
	 * @var string[]
	 */
	private $valid_font_face_properties = array(
		'ascent-override',
		'descent-override',
		'font-display',
		'font-family',
		'font-stretch',
		'font-style',
		'font-weight',
		'font-variant',
		'font-feature-settings',
		'font-variation-settings',
		'line-gap-override',
		'size-adjust',
		'src',
		'unicode-range',
	);

	/**
	 * Valid font-display values.
	 *
	 * @since 6.4.0
	 *
	 * @var string[]
	 */
	private $valid_font_display = array( 'auto', 'block', 'fallback', 'swap', 'optional' );

	/**
	 * Array of font-face style tag's attribute(s)
	 * where the key is the attribute name and the
	 * value is its value.
	 *
	 * @since 6.4.0
	 *
	 * @var string[]
	 */
	private $style_tag_attrs = array();

	/**
	 * Creates and initializes an instance of WP_Font_Face.
	 *
	 * @since 6.4.0
	 */
	public function __construct() {
		if (
			function_exists( 'is_admin' ) && ! is_admin()
			&&
			function_exists( 'current_theme_supports' ) && ! current_theme_supports( 'html5', 'style' )
		) {
			$this->style_tag_attrs = array( 'type' => 'text/css' );
		}
	}

	/**
	 * Generates and prints the `@font-face` styles for the given fonts.
	 *
	 * @since 6.4.0
	 *
	 * @param array[][] $fonts Optional. The font-families and their font variations.
	 *                         See {@see wp_print_font_faces()} for the supported fields.
	 *                         Default empty array.
	 */
	public function generate_and_print( array $fonts ) {
		$fonts = $this->validate_fonts( $fonts );

		// Bail out if there are no fonts are given to process.
		if ( empty( $fonts ) ) {
			return;
		}

		$css = $this->get_css( $fonts );

		/*
		 * The font-face CSS is contained within <style> tags and can only be interpreted
		 * as CSS in the browser. Using wp_strip_all_tags() is sufficient escaping
		 * to avoid malicious attempts to close </style> and open a <script>.
		 */
		$css = wp_strip_all_tags( $css );

		// Bail out if there is no CSS to print.
		if ( empty( $css ) ) {
			return;
		}

		printf( $this->get_style_element(), $css );
	}

	/**
	 * Validates each of the font-face properties.
	 *
	 * @since 6.4.0
	 *
	 * @param array $fonts The fonts to valid.
	 * @return array Prepared font-faces organized by provider and font-family.
	 */
	private function validate_fonts( array $fonts ) {
		$validated_fonts = array();

		foreach ( $fonts as $font_faces ) {
			foreach ( $font_faces as $font_face ) {
				$font_face = $this->validate_font_face_declarations( $font_face );
				// Skip if failed validation.
				if ( false === $font_face ) {
					continue;
				}

				$validated_fonts[] = $font_face;
			}
		}

		return $validated_fonts;
	}

	/**
	 * Validates each font-face declaration (property and value pairing).
	 *
	 * @since 6.4.0
	 *
	 * @param array $font_face Font face property and value pairings to validate.
	 * @return array|false Validated font-face on success, or false on failure.
	 */
	private function validate_font_face_declarations( array $font_face ) {
		$font_face = wp_parse_args( $font_face, $this->font_face_property_defaults );

		// Check the font-family.
		if ( empty( $font_face['font-family'] ) || ! is_string( $font_face['font-family'] ) ) {
			// @todo replace with `wp_trigger_error()`.
			_doing_it_wrong(
				__METHOD__,
				__( 'Font font-family must be a non-empty string.' ),
				'6.4.0'
			);
			return false;
		}

		// Make sure that local fonts have 'src' defined.
		if ( empty( $font_face['src'] ) || ( ! is_string( $font_face['src'] ) && ! is_array( $font_face['src'] ) ) ) {
			// @todo replace with `wp_trigger_error()`.
			_doing_it_wrong(
				__METHOD__,
				__( 'Font src must be a non-empty string or an array of strings.' ),
				'6.4.0'
			);
			return false;
		}

		// Validate the 'src' property.
		foreach ( (array) $font_face['src'] as $src ) {
			if ( empty( $src ) || ! is_string( $src ) ) {
				// @todo replace with `wp_trigger_error()`.
				_doing_it_wrong(
					__METHOD__,
					__( 'Each font src must be a non-empty string.' ),
					'6.4.0'
				);
				return false;
			}
		}

		// Check the font-weight.
		if ( ! is_string( $font_face['font-weight'] ) && ! is_int( $font_face['font-weight'] ) ) {
			// @todo replace with `wp_trigger_error()`.
			_doing_it_wrong(
				__METHOD__,
				__( 'Font font-weight must be a properly formatted string or integer.' ),
				'6.4.0'
			);
			return false;
		}

		// Check the font-display.
		if ( ! in_array( $font_face['font-display'], $this->valid_font_display, true ) ) {
			$font_face['font-display'] = $this->font_face_property_defaults['font-display'];
		}

		// Remove invalid properties.
		foreach ( $font_face as $property => $value ) {
			if ( ! in_array( $property, $this->valid_font_face_properties, true ) ) {
				unset( $font_face[ $property ] );
			}
		}

		return $font_face;
	}

	/**
	 * Gets the style element for wrapping the `@font-face` CSS.
	 *
	 * @since 6.4.0
	 *
	 * @return string The style element.
	 */
	private function get_style_element() {
		$attributes = $this->generate_style_element_attributes();

		return "<style class='wp-fonts-local'{$attributes}>\n%s\n</style>\n";
	}

	/**
	 * Gets the defined <style> element's attributes.
	 *
	 * @since 6.4.0
	 *
	 * @return string A string of attribute=value when defined, else, empty string.
	 */
	private function generate_style_element_attributes() {
		$attributes = '';
		foreach ( $this->style_tag_attrs as $name => $value ) {
			$attributes .= " {$name}='{$value}'";
		}
		return $attributes;
	}

	/**
	 * Gets the `@font-face` CSS styles for locally-hosted font files.
	 *
	 * This method does the following processing tasks:
	 *    1. Orchestrates an optimized `src` (with format) for browser support.
	 *    2. Generates the `@font-face` for all its fonts.
	 *
	 * @since 6.4.0
	 *
	 * @param array[] $font_faces The font-faces to generate @font-face CSS styles.
	 * @return string The `@font-face` CSS styles.
	 */
	private function get_css( $font_faces ) {
		$css = '';

		foreach ( $font_faces as $font_face ) {
				// Order the font's `src` items to optimize for browser support.
				$font_face = $this->order_src( $font_face );

				// Build the @font-face CSS for this font.
				$css .= '@font-face{' . $this->build_font_face_css( $font_face ) . '}' . "\n";
		}

		// Don't print the last newline character.
		return rtrim( $css, "\n" );
	}

	/**
	 * Orders `src` items to optimize for browser support.
	 *
	 * @since 6.4.0
	 *
	 * @param array $font_face Font face to process.
	 * @return array Font-face with ordered src items.
	 */
	private function order_src( array $font_face ) {
		if ( ! is_array( $font_face['src'] ) ) {
			$font_face['src'] = (array) $font_face['src'];
		}

		$src         = array();
		$src_ordered = array();

		foreach ( $font_face['src'] as $url ) {
			// Add data URIs first.
			if ( str_starts_with( trim( $url ), 'data:' ) ) {
				$src_ordered[] = array(
					'url'    => $url,
					'format' => 'data',
				);
				continue;
			}
			$format         = pathinfo( $url, PATHINFO_EXTENSION );
			$src[ $format ] = $url;
		}

		// Add woff2.
		if ( ! empty( $src['woff2'] ) ) {
			$src_ordered[] = array(
				'url'    => $src['woff2'],
				'format' => 'woff2',
			);
		}

		// Add woff.
		if ( ! empty( $src['woff'] ) ) {
			$src_ordered[] = array(
				'url'    => $src['woff'],
				'format' => 'woff',
			);
		}

		// Add ttf.
		if ( ! empty( $src['ttf'] ) ) {
			$src_ordered[] = array(
				'url'    => $src['ttf'],
				'format' => 'truetype',
			);
		}

		// Add eot.
		if ( ! empty( $src['eot'] ) ) {
			$src_ordered[] = array(
				'url'    => $src['eot'],
				'format' => 'embedded-opentype',
			);
		}

		// Add otf.
		if ( ! empty( $src['otf'] ) ) {
			$src_ordered[] = array(
				'url'    => $src['otf'],
				'format' => 'opentype',
			);
		}
		$font_face['src'] = $src_ordered;

		return $font_face;
	}

	/**
	 * Builds the font-family's CSS.
	 *
	 * @since 6.4.0
	 *
	 * @param array $font_face Font face to process.
	 * @return string This font-family's CSS.
	 */
	private function build_font_face_css( array $font_face ) {
		$css = '';

		/*
		 * Wrap font-family in quotes if it contains spaces
		 * and is not already wrapped in quotes.
		 */
		if (
			str_contains( $font_face['font-family'], ' ' ) &&
			! str_contains( $font_face['font-family'], '"' ) &&
			! str_contains( $font_face['font-family'], "'" )
		) {
			$font_face['font-family'] = '"' . $font_face['font-family'] . '"';
		}

		foreach ( $font_face as $key => $value ) {
			// Compile the "src" parameter.
			if ( 'src' === $key ) {
				$value = $this->compile_src( $value );
			}

			// If font-variation-settings is an array, convert it to a string.
			if ( 'font-variation-settings' === $key && is_array( $value ) ) {
				$value = $this->compile_variations( $value );
			}

			if ( ! empty( $value ) ) {
				$css .= "$key:$value;";
			}
		}

		return $css;
	}

	/**
	 * Compiles the `src` into valid CSS.
	 *
	 * @since 6.4.0
	 *
	 * @param array $value Value to process.
	 * @return string The CSS.
	 */
	private function compile_src( array $value ) {
		$src = '';

		foreach ( $value as $item ) {
			$src .= ( 'data' === $item['format'] )
				? ", url({$item['url']})"
				: ", url('{$item['url']}') format('{$item['format']}')";
		}

		$src = ltrim( $src, ', ' );
		return $src;
	}

	/**
	 * Compiles the font variation settings.
	 *
	 * @since 6.4.0
	 *
	 * @param array $font_variation_settings Array of font variation settings.
	 * @return string The CSS.
	 */
	private function compile_variations( array $font_variation_settings ) {
		$variations = '';

		foreach ( $font_variation_settings as $key => $value ) {
			$variations .= "$key $value";
		}

		return $variations;
	}
}

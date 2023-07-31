<?php
/**
 * WP_Duotone class
 *
 * Parts of this source were derived and modified from colord,
 * released under the MIT license.
 *
 * https://github.com/omgovich/colord
 *
 * Copyright (c) 2020 Vlad Shilov omgovich@ya.ru
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package WordPress
 * @since 6.3.0
 */

/**
 * Manages duotone block supports and global styles.
 *
 * @access private
 */
class WP_Duotone {
	/**
	 * Block names from global, theme, and custom styles that use duotone presets and the slug of
	 * the preset they are using.
	 *
	 * Example:
	 *  [
	 *      'core/featured-image' => 'blue-orange',
	 *       …
	 *  ]
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @var array
	 */
	private static $global_styles_block_names;

	/**
	 * An array of duotone filter data from global, theme, and custom presets.
	 *
	 * Example:
	 *  [
	 *      'wp-duotone-blue-orange' => [
	 *          'slug'  => 'blue-orange',
	 *          'colors' => [ '#0000ff', '#ffcc00' ],
	 *      ],
	 *      'wp-duotone-red-yellow' => [
	 *          'slug'   => 'red-yellow',
	 *          'colors' => [ '#cc0000', '#ffff33' ],
	 *      ],
	 *      …
	 *  ]
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @var array
	 */
	private static $global_styles_presets;

	/**
	 * All of the duotone filter data from presets for CSS custom properties on
	 * the page.
	 *
	 * Example:
	 *  [
	 *      'wp-duotone-blue-orange' => [
	 *          'slug'   => 'blue-orange',
	 *          'colors' => [ '#0000ff', '#ffcc00' ],
	 *      ],
	 *      …
	 *  ]
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @var array
	 */
	private static $used_global_styles_presets = array();

	/**
	 * All of the duotone filter data for SVGs on the page. Includes both
	 * presets and custom filters.
	 *
	 * Example:
	 *  [
	 *      'wp-duotone-blue-orange' => [
	 *          'slug'   => 'blue-orange',
	 *          'colors' => [ '#0000ff', '#ffcc00' ],
	 *      ],
	 *      'wp-duotone-000000-ffffff-2' => [
	 *          'slug'   => '000000-ffffff-2',
	 *          'colors' => [ '#000000', '#ffffff' ],
	 *      ],
	 *      …
	 *  ]
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @var array
	 */
	private static $used_svg_filter_data = array();

	/**
	 * All of the block CSS declarations for styles on the page.
	 *
	 * Example:
	 *  [
	 *      [
	 *          'selector'     => '.wp-duotone-000000-ffffff-2.wp-block-image img',
	 *          'declarations' => [
	 *              'filter' => 'url(#wp-duotone-000000-ffffff-2)',
	 *          ],
	 *      ],
	 *      …
	 *  ]
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @var array
	 */
	private static $block_css_declarations = array();

	/**
	 * Clamps a value between an upper and lower bound.
	 *
	 * Direct port of colord's clamp function.
	 *
	 * @link https://github.com/omgovich/colord/blob/3f859e03b0ca622eb15480f611371a0f15c9427f/src/helpers.ts#L23 Sourced from colord.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param float $number The number to clamp.
	 * @param float $min    The minimum value.
	 * @param float $max    The maximum value.
	 * @return float The clamped value.
	 */
	private static function colord_clamp( $number, $min = 0, $max = 1 ) {
		return $number > $max ? $max : ( $number > $min ? $number : $min );
	}

	/**
	 * Processes and clamps a degree (angle) value properly.
	 *
	 * Direct port of colord's clampHue function.
	 *
	 * @link https://github.com/omgovich/colord/blob/3f859e03b0ca622eb15480f611371a0f15c9427f/src/helpers.ts#L32 Sourced from colord.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param float $degrees The hue to clamp.
	 * @return float The clamped hue.
	 */
	private static function colord_clamp_hue( $degrees ) {
		$degrees = is_finite( $degrees ) ? $degrees % 360 : 0;
		return $degrees > 0 ? $degrees : $degrees + 360;
	}

	/**
	 * Converts a hue value to degrees from 0 to 360 inclusive.
	 *
	 * Direct port of colord's parseHue function.
	 *
	 * @link https://github.com/omgovich/colord/blob/3f859e03b0ca622eb15480f611371a0f15c9427f/src/helpers.ts#L40 Sourced from colord.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param float  $value The hue value to parse.
	 * @param string $unit  The unit of the hue value.
	 * @return float The parsed hue value.
	 */
	private static function colord_parse_hue( $value, $unit = 'deg' ) {
		$angle_units = array(
			'grad' => 360 / 400,
			'turn' => 360,
			'rad'  => 360 / ( M_PI * 2 ),
		);

		$factor = $angle_units[ $unit ];
		if ( ! $factor ) {
			$factor = 1;
		}

		return (float) $value * $factor;
	}

	/**
	 * Parses any valid Hex3, Hex4, Hex6 or Hex8 string and converts it to an RGBA object
	 *
	 * Direct port of colord's parseHex function.
	 *
	 * @link https://github.com/omgovich/colord/blob/3f859e03b0ca622eb15480f611371a0f15c9427f/src/colorModels/hex.ts#L8 Sourced from colord.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param string $hex The hex string to parse.
	 * @return array|null An array of RGBA values or null if the hex string is invalid.
	 */
	private static function colord_parse_hex( $hex ) {
		$is_match = preg_match(
			'/^#([0-9a-f]{3,8})$/i',
			$hex,
			$hex_match
		);

		if ( ! $is_match ) {
			return null;
		}

		$hex = $hex_match[1];

		if ( 4 >= strlen( $hex ) ) {
			return array(
				'r' => (int) base_convert( $hex[0] . $hex[0], 16, 10 ),
				'g' => (int) base_convert( $hex[1] . $hex[1], 16, 10 ),
				'b' => (int) base_convert( $hex[2] . $hex[2], 16, 10 ),
				'a' => 4 === strlen( $hex ) ? round( base_convert( $hex[3] . $hex[3], 16, 10 ) / 255, 2 ) : 1,
			);
		}

		if ( 6 === strlen( $hex ) || 8 === strlen( $hex ) ) {
			return array(
				'r' => (int) base_convert( substr( $hex, 0, 2 ), 16, 10 ),
				'g' => (int) base_convert( substr( $hex, 2, 2 ), 16, 10 ),
				'b' => (int) base_convert( substr( $hex, 4, 2 ), 16, 10 ),
				'a' => 8 === strlen( $hex ) ? round( (int) base_convert( substr( $hex, 6, 2 ), 16, 10 ) / 255, 2 ) : 1,
			);
		}

		return null;
	}

	/**
	 * Clamps an array of RGBA values.
	 *
	 * Direct port of colord's clampRgba function.
	 *
	 * @link https://github.com/omgovich/colord/blob/3f859e03b0ca622eb15480f611371a0f15c9427f/src/colorModels/rgb.ts#L5 Sourced from colord.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param array $rgba The RGBA array to clamp.
	 * @return array The clamped RGBA array.
	 */
	private static function colord_clamp_rgba( $rgba ) {
		$rgba['r'] = self::colord_clamp( $rgba['r'], 0, 255 );
		$rgba['g'] = self::colord_clamp( $rgba['g'], 0, 255 );
		$rgba['b'] = self::colord_clamp( $rgba['b'], 0, 255 );
		$rgba['a'] = self::colord_clamp( $rgba['a'] );

		return $rgba;
	}

	/**
	 * Parses a valid RGB[A] CSS color function/string
	 *
	 * Direct port of colord's parseRgbaString function.
	 *
	 * @link https://github.com/omgovich/colord/blob/3f859e03b0ca622eb15480f611371a0f15c9427f/src/colorModels/rgbString.ts#L18 Sourced from colord.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param string $input The RGBA string to parse.
	 * @return array|null An array of RGBA values or null if the RGB string is invalid.
	 */
	private static function colord_parse_rgba_string( $input ) {
		// Functional syntax.
		$is_match = preg_match(
			'/^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i',
			$input,
			$match
		);

		if ( ! $is_match ) {
			// Whitespace syntax.
			$is_match = preg_match(
				'/^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i',
				$input,
				$match
			);
		}

		if ( ! $is_match ) {
			return null;
		}

		/*
		 * For some reason, preg_match doesn't include empty matches at the end
		 * of the array, so we add them manually to make things easier later.
		 */
		for ( $i = 1; $i <= 8; $i++ ) {
			if ( ! isset( $match[ $i ] ) ) {
				$match[ $i ] = '';
			}
		}

		if ( $match[2] !== $match[4] || $match[4] !== $match[6] ) {
			return null;
		}

		return self::colord_clamp_rgba(
			array(
				'r' => (float) $match[1] / ( $match[2] ? 100 / 255 : 1 ),
				'g' => (float) $match[3] / ( $match[4] ? 100 / 255 : 1 ),
				'b' => (float) $match[5] / ( $match[6] ? 100 / 255 : 1 ),
				'a' => '' === $match[7] ? 1 : (float) $match[7] / ( $match[8] ? 100 : 1 ),
			)
		);
	}

	/**
	 * Clamps an array of HSLA values.
	 *
	 * Direct port of colord's clampHsla function.
	 *
	 * @link https://github.com/omgovich/colord/blob/3f859e03b0ca622eb15480f611371a0f15c9427f/src/colorModels/hsl.ts#L6 Sourced from colord.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param array $hsla The HSLA array to clamp.
	 * @return array The clamped HSLA array.
	 */
	private static function colord_clamp_hsla( $hsla ) {
		$hsla['h'] = self::colord_clamp_hue( $hsla['h'] );
		$hsla['s'] = self::colord_clamp( $hsla['s'], 0, 100 );
		$hsla['l'] = self::colord_clamp( $hsla['l'], 0, 100 );
		$hsla['a'] = self::colord_clamp( $hsla['a'] );

		return $hsla;
	}

	/**
	 * Converts an HSVA array to RGBA.
	 *
	 * Direct port of colord's hsvaToRgba function.
	 *
	 * @link https://github.com/omgovich/colord/blob/3f859e03b0ca622eb15480f611371a0f15c9427f/src/colorModels/hsv.ts#L52 Sourced from colord.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param array $hsva The HSVA array to convert.
	 * @return array The RGBA array.
	 */
	private static function colord_hsva_to_rgba( $hsva ) {
		$h = ( $hsva['h'] / 360 ) * 6;
		$s = $hsva['s'] / 100;
		$v = $hsva['v'] / 100;
		$a = $hsva['a'];

		$hh     = floor( $h );
		$b      = $v * ( 1 - $s );
		$c      = $v * ( 1 - ( $h - $hh ) * $s );
		$d      = $v * ( 1 - ( 1 - $h + $hh ) * $s );
		$module = $hh % 6;

		return array(
			'r' => array( $v, $c, $b, $b, $d, $v )[ $module ] * 255,
			'g' => array( $d, $v, $v, $c, $b, $b )[ $module ] * 255,
			'b' => array( $b, $b, $d, $v, $v, $c )[ $module ] * 255,
			'a' => $a,
		);
	}

	/**
	 * Converts an HSLA array to HSVA.
	 *
	 * Direct port of colord's hslaToHsva function.
	 *
	 * @link https://github.com/omgovich/colord/blob/3f859e03b0ca622eb15480f611371a0f15c9427f/src/colorModels/hsl.ts#L33 Sourced from colord.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param array $hsla The HSLA array to convert.
	 * @return array The HSVA array.
	 */
	private static function colord_hsla_to_hsva( $hsla ) {
		$h = $hsla['h'];
		$s = $hsla['s'];
		$l = $hsla['l'];
		$a = $hsla['a'];

		$s *= ( $l < 50 ? $l : 100 - $l ) / 100;

		return array(
			'h' => $h,
			's' => $s > 0 ? ( ( 2 * $s ) / ( $l + $s ) ) * 100 : 0,
			'v' => $l + $s,
			'a' => $a,
		);
	}

	/**
	 * Converts an HSLA array to RGBA.
	 *
	 * Direct port of colord's hslaToRgba function.
	 *
	 * @link https://github.com/omgovich/colord/blob/3f859e03b0ca622eb15480f611371a0f15c9427f/src/colorModels/hsl.ts#L55 Sourced from colord.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param array $hsla The HSLA array to convert.
	 * @return array The RGBA array.
	 */
	private static function colord_hsla_to_rgba( $hsla ) {
		return self::colord_hsva_to_rgba( self::colord_hsla_to_hsva( $hsla ) );
	}

	/**
	 * Parses a valid HSL[A] CSS color function/string.
	 *
	 * Direct port of colord's parseHslaString function.
	 *
	 * @link https://github.com/omgovich/colord/blob/3f859e03b0ca622eb15480f611371a0f15c9427f/src/colorModels/hslString.ts#L17 Sourced from colord.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param string $input The HSLA string to parse.
	 * @return array|null An array of RGBA values or null if the RGB string is invalid.
	 */
	private static function colord_parse_hsla_string( $input ) {
		// Functional syntax.
		$is_match = preg_match(
			'/^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s*,\s*([+-]?\d*\.?\d+)%\s*,\s*([+-]?\d*\.?\d+)%\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i',
			$input,
			$match
		);

		if ( ! $is_match ) {
			// Whitespace syntax.
			$is_match = preg_match(
				'/^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s+([+-]?\d*\.?\d+)%\s+([+-]?\d*\.?\d+)%\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i',
				$input,
				$match
			);
		}

		if ( ! $is_match ) {
			return null;
		}

		/*
		 * For some reason, preg_match doesn't include empty matches at the end
		 * of the array, so we add them manually to make things easier later.
		 */
		for ( $i = 1; $i <= 6; $i++ ) {
			if ( ! isset( $match[ $i ] ) ) {
				$match[ $i ] = '';
			}
		}

		$hsla = self::colord_clamp_hsla(
			array(
				'h' => self::colord_parse_hue( $match[1], $match[2] ),
				's' => (float) $match[3],
				'l' => (float) $match[4],
				'a' => '' === $match[5] ? 1 : (float) $match[5] / ( $match[6] ? 100 : 1 ),
			)
		);

		return self::colord_hsla_to_rgba( $hsla );
	}

	/**
	 * Tries to convert an incoming string into RGBA values.
	 *
	 * Direct port of colord's parse function simplified for our use case. This
	 * version only supports string parsing and only returns RGBA values.
	 *
	 * @link https://github.com/omgovich/colord/blob/3f859e03b0ca622eb15480f611371a0f15c9427f/src/parse.ts#L37 Sourced from colord.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param string $input The string to parse.
	 * @return array|null An array of RGBA values or null if the string is invalid.
	 */
	private static function colord_parse( $input ) {
		$result = self::colord_parse_hex( $input );

		if ( ! $result ) {
			$result = self::colord_parse_rgba_string( $input );
		}

		if ( ! $result ) {
			$result = self::colord_parse_hsla_string( $input );
		}

		return $result;
	}

	/**
	 * Takes the inline CSS duotone variable from a block and return the slug.
	 *
	 * Handles styles slugs like:
	 * var:preset|duotone|blue-orange
	 * var(--wp--preset--duotone--blue-orange)
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param string $duotone_attr The duotone attribute from a block.
	 * @return string The slug of the duotone preset or an empty string if no slug is found.
	 */
	private static function get_slug_from_attribute( $duotone_attr ) {
		// Uses Branch Reset Groups `(?|…)` to return one capture group.
		preg_match( '/(?|var:preset\|duotone\|(\S+)|var\(--wp--preset--duotone--(\S+)\))/', $duotone_attr, $matches );

		return ! empty( $matches[1] ) ? $matches[1] : '';
	}

	/**
	 * Checks if we have a valid duotone preset.
	 *
	 * Valid presets are defined in the $global_styles_presets array.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param string $duotone_attr The duotone attribute from a block.
	 * @return bool True if the duotone preset present and valid.
	 */
	private static function is_preset( $duotone_attr ) {
		$slug      = self::get_slug_from_attribute( $duotone_attr );
		$filter_id = self::get_filter_id( $slug );

		return array_key_exists( $filter_id, self::get_all_global_styles_presets() );
	}

	/**
	 * Gets the CSS variable name for a duotone preset.
	 *
	 * Example output:
	 *  --wp--preset--duotone--blue-orange
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param string $slug The slug of the duotone preset.
	 * @return string The CSS variable name.
	 */
	private static function get_css_custom_property_name( $slug ) {
		return "--wp--preset--duotone--$slug";
	}

	/**
	 * Get the ID of the duotone filter.
	 *
	 * Example output:
	 *  wp-duotone-blue-orange
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param string $slug The slug of the duotone preset.
	 * @return string The ID of the duotone filter.
	 */
	private static function get_filter_id( $slug ) {
		return "wp-duotone-$slug";
	}

	/**
	 * Get the CSS variable for a duotone preset.
	 *
	 * Example output:
	 *  var(--wp--preset--duotone--blue-orange)
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param string $slug The slug of the duotone preset.
	 * @return string The CSS variable.
	 */
	private static function get_css_var( $slug ) {
		$name = self::get_css_custom_property_name( $slug );
		return "var($name)";
	}

	/**
	 * Get the URL for a duotone filter.
	 *
	 * Example output:
	 *  url(#wp-duotone-blue-orange)
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param string $filter_id The ID of the filter.
	 * @return string The URL for the duotone filter.
	 */
	private static function get_filter_url( $filter_id ) {
		return "url(#$filter_id)";
	}

	/**
	 * Gets the SVG for the duotone filter definition.
	 *
	 * Whitespace is removed when SCRIPT_DEBUG is not enabled.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param string $filter_id The ID of the filter.
	 * @param array  $colors    An array of color strings.
	 * @return string An SVG with a duotone filter definition.
	 */
	private static function get_filter_svg( $filter_id, $colors ) {
		$duotone_values = array(
			'r' => array(),
			'g' => array(),
			'b' => array(),
			'a' => array(),
		);

		foreach ( $colors as $color_str ) {
			$color = self::colord_parse( $color_str );

			if ( null === $color ) {
				$error_message = sprintf(
					/* translators: %s: duotone colors */
					__( '"%s" in theme.json settings.color.duotone is not a hex or rgb string.' ),
					$color_str
				);
				_doing_it_wrong( __METHOD__, $error_message, '6.3.0' );
			} else {
				$duotone_values['r'][] = $color['r'] / 255;
				$duotone_values['g'][] = $color['g'] / 255;
				$duotone_values['b'][] = $color['b'] / 255;
				$duotone_values['a'][] = $color['a'];
			}
		}

		ob_start();

		?>

		<svg
			xmlns="http://www.w3.org/2000/svg"
			viewBox="0 0 0 0"
			width="0"
			height="0"
			focusable="false"
			role="none"
			style="visibility: hidden; position: absolute; left: -9999px; overflow: hidden;"
		>
			<defs>
				<filter id="<?php echo esc_attr( $filter_id ); ?>">
					<feColorMatrix
						color-interpolation-filters="sRGB"
						type="matrix"
						values="
							.299 .587 .114 0 0
							.299 .587 .114 0 0
							.299 .587 .114 0 0
							.299 .587 .114 0 0
						"
					/>
					<feComponentTransfer color-interpolation-filters="sRGB" >
						<feFuncR type="table" tableValues="<?php echo esc_attr( implode( ' ', $duotone_values['r'] ) ); ?>" />
						<feFuncG type="table" tableValues="<?php echo esc_attr( implode( ' ', $duotone_values['g'] ) ); ?>" />
						<feFuncB type="table" tableValues="<?php echo esc_attr( implode( ' ', $duotone_values['b'] ) ); ?>" />
						<feFuncA type="table" tableValues="<?php echo esc_attr( implode( ' ', $duotone_values['a'] ) ); ?>" />
					</feComponentTransfer>
					<feComposite in2="SourceGraphic" operator="in" />
				</filter>
			</defs>
		</svg>

		<?php

		$svg = ob_get_clean();

		if ( ! SCRIPT_DEBUG ) {
			// Clean up the whitespace.
			$svg = preg_replace( "/[\r\n\t ]+/", ' ', $svg );
			$svg = str_replace( '> <', '><', $svg );
			$svg = trim( $svg );
		}

		return $svg;
	}

	/**
	 * Returns the prefixed id for the duotone filter for use as a CSS id.
	 *
	 * Exported for the deprecated function wp_get_duotone_filter_id().
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 * @deprecated 6.3.0
	 *
	 * @param  array $preset Duotone preset value as seen in theme.json.
	 * @return string        Duotone filter CSS id.
	 */
	public static function get_filter_id_from_preset( $preset ) {
		_deprecated_function( __FUNCTION__, '6.3.0' );

		$filter_id = '';
		if ( isset( $preset['slug'] ) ) {
			$filter_id = self::get_filter_id( $preset['slug'] );
		}
		return $filter_id;
	}

	/**
	 * Gets the SVG for the duotone filter definition from a preset.
	 *
	 * Exported for the deprecated function wp_get_duotone_filter_property().
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 * @deprecated 6.3.0
	 *
	 * @param array $preset The duotone preset.
	 * @return string The SVG for the filter definition.
	 */
	public static function get_filter_svg_from_preset( $preset ) {
		_deprecated_function( __FUNCTION__, '6.3.0' );

		$filter_id = self::get_filter_id_from_preset( $preset );
		return self::get_filter_svg( $filter_id, $preset['colors'] );
	}

	/**
	 * Get the SVGs for the duotone filters.
	 *
	 * Example output:
	 *  <svg><defs><filter id="wp-duotone-blue-orange">…</filter></defs></svg><svg>…</svg>
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param array $sources The duotone presets.
	 * @return string The SVGs for the duotone filters.
	 */
	private static function get_svg_definitions( $sources ) {
		$svgs = '';
		foreach ( $sources as $filter_id => $filter_data ) {
			$colors = $filter_data['colors'];
			$svgs  .= self::get_filter_svg( $filter_id, $colors );
		}
		return $svgs;
	}

	/**
	 * Get the CSS for global styles.
	 *
	 * Example output:
	 *  body{--wp--preset--duotone--blue-orange:url('#wp-duotone-blue-orange');}
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param array $sources The duotone presets.
	 * @return string The CSS for global styles.
	 */
	private static function get_global_styles_presets( $sources ) {
		$css = 'body{';
		foreach ( $sources as $filter_id => $filter_data ) {
			$slug              = $filter_data['slug'];
			$colors            = $filter_data['colors'];
			$css_property_name = self::get_css_custom_property_name( $slug );
			$declaration_value = is_string( $colors ) ? $colors : self::get_filter_url( $filter_id );
			$css              .= "$css_property_name:$declaration_value;";
		}
		$css .= '}';
		return $css;
	}

	/**
	 * Enqueue a block CSS declaration for the page.
	 *
	 * This does not include any SVGs.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param string $filter_id        The filter ID. e.g. 'wp-duotone-000000-ffffff-2'.
	 * @param string $duotone_selector The block's duotone selector. e.g. '.wp-block-image img'.
	 * @param string $filter_value     The filter CSS value. e.g. 'url(#wp-duotone-000000-ffffff-2)' or 'unset'.
	 */
	private static function enqueue_block_css( $filter_id, $duotone_selector, $filter_value ) {
		// Build the CSS selectors to which the filter will be applied.
		$selectors = explode( ',', $duotone_selector );

		$selectors_scoped = array();
		foreach ( $selectors as $selector_part ) {
			/*
			 * Assuming the selector part is a subclass selector (not a tag name)
			 * so we can prepend the filter id class. If we want to support elements
			 * such as `img` or namespaces, we'll need to add a case for that here.
			 */
			$selectors_scoped[] = '.' . $filter_id . trim( $selector_part );
		}

		$selector = implode( ', ', $selectors_scoped );

		self::$block_css_declarations[] = array(
			'selector'     => $selector,
			'declarations' => array(
				'filter' => $filter_value,
			),
		);
	}

	/**
	 * Enqueue custom filter assets for the page.
	 *
	 * Includes an SVG filter and block CSS declaration.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param string $filter_id        The filter ID. e.g. 'wp-duotone-000000-ffffff-2'.
	 * @param string $duotone_selector The block's duotone selector. e.g. '.wp-block-image img'.
	 * @param string $filter_value     The filter CSS value. e.g. 'url(#wp-duotone-000000-ffffff-2)' or 'unset'.
	 * @param array  $filter_data      Duotone filter data with 'slug' and 'colors' keys.
	 */
	private static function enqueue_custom_filter( $filter_id, $duotone_selector, $filter_value, $filter_data ) {
		self::$used_svg_filter_data[ $filter_id ] = $filter_data;
		self::enqueue_block_css( $filter_id, $duotone_selector, $filter_value );
	}

	/**
	 * Enqueue preset assets for the page.
	 *
	 * Includes a CSS custom property, SVG filter, and block CSS declaration.
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 *
	 * @param string $filter_id        The filter ID. e.g. 'wp-duotone-blue-orange'.
	 * @param string $duotone_selector The block's duotone selector. e.g. '.wp-block-image img'.
	 * @param string $filter_value     The filter CSS value. e.g. 'url(#wp-duotone-blue-orange)' or 'unset'.
	 */
	private static function enqueue_global_styles_preset( $filter_id, $duotone_selector, $filter_value ) {
		$global_styles_presets = self::get_all_global_styles_presets();
		if ( ! array_key_exists( $filter_id, $global_styles_presets ) ) {
			$error_message = sprintf(
				/* translators: %s: duotone filter ID */
				__( 'The duotone id "%s" is not registered in theme.json settings' ),
				$filter_id
			);
			_doing_it_wrong( __METHOD__, $error_message, '6.3.0' );
			return;
		}
		self::$used_global_styles_presets[ $filter_id ] = $global_styles_presets[ $filter_id ];
		self::enqueue_custom_filter( $filter_id, $duotone_selector, $filter_value, $global_styles_presets[ $filter_id ] );
	}

	/**
	 * Registers the style and colors block attributes for block types that support it.
	 *
	 * Block support is added with `supports.filter.duotone` in block.json.
	 *
	 * @since 6.3.0
	 *
	 * @param WP_Block_Type $block_type Block Type.
	 */
	public static function register_duotone_support( $block_type ) {
		/*
		 * Previous `color.__experimentalDuotone` support flag is migrated
		 * to `filter.duotone` via `block_type_metadata_settings` filter.
		 */
		if ( block_has_support( $block_type, array( 'filter', 'duotone' ), null ) ) {
			if ( ! $block_type->attributes ) {
				$block_type->attributes = array();
			}

			if ( ! array_key_exists( 'style', $block_type->attributes ) ) {
				$block_type->attributes['style'] = array(
					'type' => 'object',
				);
			}
		}
	}

	/**
	 * Get the CSS selector for a block type.
	 *
	 * This handles selectors defined in `color.__experimentalDuotone` support
	 * if `filter.duotone` support is not defined.
	 *
	 * @internal
	 * @since 6.3.0
	 *
	 * @param WP_Block_Type $block_type Block type to check for support.
	 * @return string|null The CSS selector or null if there is no support.
	 */
	private static function get_selector( $block_type ) {
		if ( ! ( $block_type instanceof WP_Block_Type ) ) {
			return null;
		}

		/*
		 * Backward compatibility with `supports.color.__experimentalDuotone`
		 * is provided via the `block_type_metadata_settings` filter. If
		 * `supports.filter.duotone` has not been set and the experimental
		 * property has been, the experimental property value is copied into
		 * `supports.filter.duotone`.
		 */
		$duotone_support = block_has_support( $block_type, array( 'filter', 'duotone' ) );
		if ( ! $duotone_support ) {
			return null;
		}

		/*
		 * If the experimental duotone support was set, that value is to be
		 * treated as a selector and requires scoping.
		 */
		$experimental_duotone = _wp_array_get( $block_type->supports, array( 'color', '__experimentalDuotone' ), false );
		if ( $experimental_duotone ) {
			$root_selector = wp_get_block_css_selector( $block_type );
			return is_string( $experimental_duotone )
				? WP_Theme_JSON::scope_selector( $root_selector, $experimental_duotone )
				: $root_selector;
		}

		// Regular filter.duotone support uses filter.duotone selectors with fallbacks.
		return wp_get_block_css_selector( $block_type, array( 'filter', 'duotone' ), true );
	}

	/**
	 * Scrape all possible duotone presets from global and theme styles and
	 * store them in self::$global_styles_presets.
	 *
	 * Used in conjunction with self::render_duotone_support for blocks that
	 * use duotone preset filters.
	 *
	 * @since 6.3.0
	 *
	 * @return array An array of global styles presets, keyed on the filter ID.
	 */
	private static function get_all_global_styles_presets() {
		if ( isset( self::$global_styles_presets ) ) {
			return self::$global_styles_presets;
		}
		// Get the per block settings from the theme.json.
		$tree              = wp_get_global_settings();
		$presets_by_origin = _wp_array_get( $tree, array( 'color', 'duotone' ), array() );

		self::$global_styles_presets = array();
		foreach ( $presets_by_origin as $presets ) {
			foreach ( $presets as $preset ) {
				$filter_id = self::get_filter_id( _wp_to_kebab_case( $preset['slug'] ) );

				self::$global_styles_presets[ $filter_id ] = $preset;
			}
		}

		return self::$global_styles_presets;
	}

	/**
	 * Scrape all block names from global styles and store in self::$global_styles_block_names.
	 *
	 * Used in conjunction with self::render_duotone_support to output the
	 * duotone filters defined in the theme.json global styles.
	 *
	 * @since 6.3.0
	 *
	 * @return string[] An array of global style block slugs, keyed on the block name.
	 */
	private static function get_all_global_style_block_names() {
		if ( isset( self::$global_styles_block_names ) ) {
			return self::$global_styles_block_names;
		}
		// Get the per block settings from the theme.json.
		$tree        = WP_Theme_JSON_Resolver::get_merged_data();
		$block_nodes = $tree->get_styles_block_nodes();
		$theme_json  = $tree->get_raw_data();

		self::$global_styles_block_names = array();

		foreach ( $block_nodes as $block_node ) {
			// This block definition doesn't include any duotone settings. Skip it.
			if ( empty( $block_node['duotone'] ) ) {
				continue;
			}

			// Value looks like this: 'var(--wp--preset--duotone--blue-orange)' or 'var:preset|duotone|blue-orange'.
			$duotone_attr_path = array_merge( $block_node['path'], array( 'filter', 'duotone' ) );
			$duotone_attr      = _wp_array_get( $theme_json, $duotone_attr_path, array() );

			if ( empty( $duotone_attr ) ) {
				continue;
			}
			// If it has a duotone filter preset, save the block name and the preset slug.
			$slug = self::get_slug_from_attribute( $duotone_attr );

			if ( $slug && $slug !== $duotone_attr ) {
				self::$global_styles_block_names[ $block_node['name'] ] = $slug;
			}
		}
		return self::$global_styles_block_names;
	}

	/**
	 * Render out the duotone CSS styles and SVG.
	 *
	 * The hooks self::set_global_style_block_names and self::set_global_styles_presets
	 * must be called before this function.
	 *
	 * @since 6.3.0
	 *
	 * @param  string   $block_content Rendered block content.
	 * @param  array    $block         Block object.
	 * @param  WP_Block $wp_block      The block instance.
	 * @return string Filtered block content.
	 */
	public static function render_duotone_support( $block_content, $block, $wp_block ) {
		if ( empty( $block_content ) || ! $block['blockName'] ) {
			return $block_content;
		}
		$duotone_selector = self::get_selector( $wp_block->block_type );

		if ( ! $duotone_selector ) {
			return $block_content;
		}

		$global_styles_block_names = self::get_all_global_style_block_names();

		// The block should have a duotone attribute or have duotone defined in its theme.json to be processed.
		$has_duotone_attribute     = isset( $block['attrs']['style']['color']['duotone'] );
		$has_global_styles_duotone = array_key_exists( $block['blockName'], $global_styles_block_names );

		if ( ! $has_duotone_attribute && ! $has_global_styles_duotone ) {
			return $block_content;
		}

		// Generate the pieces needed for rendering a duotone to the page.
		if ( $has_duotone_attribute ) {

			/*
			 * Possible values for duotone attribute:
			 * 1. Array of colors - e.g. array('#000000', '#ffffff').
			 * 2. Variable for an existing Duotone preset - e.g. 'var:preset|duotone|blue-orange' or 'var(--wp--preset--duotone--blue-orange)''
			 * 3. A CSS string - e.g. 'unset' to remove globally applied duotone.
			 */

			$duotone_attr = $block['attrs']['style']['color']['duotone'];
			$is_preset    = is_string( $duotone_attr ) && self::is_preset( $duotone_attr );
			$is_css       = is_string( $duotone_attr ) && ! $is_preset;
			$is_custom    = is_array( $duotone_attr );

			if ( $is_preset ) {

				$slug         = self::get_slug_from_attribute( $duotone_attr ); // e.g. 'blue-orange'.
				$filter_id    = self::get_filter_id( $slug ); // e.g. 'wp-duotone-filter-blue-orange'.
				$filter_value = self::get_css_var( $slug ); // e.g. 'var(--wp--preset--duotone--blue-orange)'.

				// CSS custom property, SVG filter, and block CSS.
				self::enqueue_global_styles_preset( $filter_id, $duotone_selector, $filter_value );

			} elseif ( $is_css ) {
				$slug         = wp_unique_id( sanitize_key( $duotone_attr . '-' ) ); // e.g. 'unset-1'.
				$filter_id    = self::get_filter_id( $slug ); // e.g. 'wp-duotone-filter-unset-1'.
				$filter_value = $duotone_attr; // e.g. 'unset'.

				// Just block CSS.
				self::enqueue_block_css( $filter_id, $duotone_selector, $filter_value );
			} elseif ( $is_custom ) {
				$slug         = wp_unique_id( sanitize_key( implode( '-', $duotone_attr ) . '-' ) ); // e.g. '000000-ffffff-2'.
				$filter_id    = self::get_filter_id( $slug ); // e.g. 'wp-duotone-filter-000000-ffffff-2'.
				$filter_value = self::get_filter_url( $filter_id ); // e.g. 'url(#wp-duotone-filter-000000-ffffff-2)'.
				$filter_data  = array(
					'slug'   => $slug,
					'colors' => $duotone_attr,
				);

				// SVG filter and block CSS.
				self::enqueue_custom_filter( $filter_id, $duotone_selector, $filter_value, $filter_data );
			}
		} elseif ( $has_global_styles_duotone ) {
			$slug         = $global_styles_block_names[ $block['blockName'] ]; // e.g. 'blue-orange'.
			$filter_id    = self::get_filter_id( $slug ); // e.g. 'wp-duotone-filter-blue-orange'.
			$filter_value = self::get_css_var( $slug ); // e.g. 'var(--wp--preset--duotone--blue-orange)'.

			// CSS custom property, SVG filter, and block CSS.
			self::enqueue_global_styles_preset( $filter_id, $duotone_selector, $filter_value );
		}

		// Like the layout hook, this assumes the hook only applies to blocks with a single wrapper.
		$tags = new WP_HTML_Tag_Processor( $block_content );
		if ( $tags->next_tag() ) {
			$tags->add_class( $filter_id );
		}
		return $tags->get_updated_html();
	}

	/**
	 * Appends the used block duotone filter declarations to the inline block supports CSS.
	 *
	 * Uses the declarations saved in earlier calls to self::enqueue_block_css.
	 *
	 * @since 6.3.0
	 */
	public static function output_block_styles() {
		if ( ! empty( self::$block_css_declarations ) ) {
			wp_style_engine_get_stylesheet_from_css_rules(
				self::$block_css_declarations,
				array(
					'context' => 'block-supports',
				)
			);
		}
	}

	/**
	 * Appends the used global style duotone filter presets (CSS custom
	 * properties) to the inline global styles CSS.
	 *
	 * Uses the declarations saved in earlier calls to self::enqueue_global_styles_preset.
	 *
	 * @since 6.3.0
	 */
	public static function output_global_styles() {
		if ( ! empty( self::$used_global_styles_presets ) ) {
			wp_add_inline_style( 'global-styles', self::get_global_styles_presets( self::$used_global_styles_presets ) );
		}
	}

	/**
	 * Outputs all necessary SVG for duotone filters, CSS for classic themes.
	 *
	 * Uses the declarations saved in earlier calls to self::enqueue_global_styles_preset
	 * and self::enqueue_custom_filter.
	 *
	 * @since 6.3.0
	 */
	public static function output_footer_assets() {
		if ( ! empty( self::$used_svg_filter_data ) ) {
			echo self::get_svg_definitions( self::$used_svg_filter_data );
		}

		// In block themes, the CSS is added in the head via wp_add_inline_style in the wp_enqueue_scripts action.
		if ( ! wp_is_block_theme() ) {
			$style_tag_id = 'core-block-supports-duotone';
			wp_register_style( $style_tag_id, false );
			if ( ! empty( self::$used_global_styles_presets ) ) {
				wp_add_inline_style( $style_tag_id, self::get_global_styles_presets( self::$used_global_styles_presets ) );
			}
			if ( ! empty( self::$block_css_declarations ) ) {
				wp_add_inline_style( $style_tag_id, wp_style_engine_get_stylesheet_from_css_rules( self::$block_css_declarations ) );
			}
			wp_enqueue_style( $style_tag_id );
		}
	}

	/**
	 * Adds the duotone SVGs and CSS custom properties to the editor settings.
	 *
	 * This allows the properties to be pulled in by the EditorStyles component
	 * in JS and rendered in the post editor.
	 *
	 * @since 6.3.0
	 *
	 * @param array $settings The block editor settings from the `block_editor_settings_all` filter.
	 * @return array The editor settings with duotone SVGs and CSS custom properties.
	 */
	public static function add_editor_settings( $settings ) {
		$global_styles_presets = self::get_all_global_styles_presets();
		if ( ! empty( $global_styles_presets ) ) {
			if ( ! isset( $settings['styles'] ) ) {
				$settings['styles'] = array();
			}

			$settings['styles'][] = array(
				// For the editor we can add all of the presets by default.
				'assets'         => self::get_svg_definitions( $global_styles_presets ),
				// The 'svgs' type is new in 6.3 and requires the corresponding JS changes in the EditorStyles component to work.
				'__unstableType' => 'svgs',
				// These styles not generated by global styles, so this must be false or they will be stripped out in wp_get_block_editor_settings.
				'isGlobalStyles' => false,
			);

			$settings['styles'][] = array(
				// For the editor we can add all of the presets by default.
				'css'            => self::get_global_styles_presets( $global_styles_presets ),
				// This must be set and must be something other than 'theme' or they will be stripped out in the post editor <Editor> component.
				'__unstableType' => 'presets',
				// These styles are no longer generated by global styles, so this must be false or they will be stripped out in wp_get_block_editor_settings.
				'isGlobalStyles' => false,
			);
		}

		return $settings;
	}

	/**
	 * Migrates the experimental duotone support flag to the stabilized location.
	 *
	 * This moves `supports.color.__experimentalDuotone` to `supports.filter.duotone`.
	 *
	 * @since 6.3.0
	 *
	 * @param array $settings Current block type settings.
	 * @param array $metadata Block metadata as read in via block.json.
	 * @return array Filtered block type settings.
	 */
	public static function migrate_experimental_duotone_support_flag( $settings, $metadata ) {
		$duotone_support = _wp_array_get( $metadata, array( 'supports', 'color', '__experimentalDuotone' ), null );

		if ( ! isset( $settings['supports']['filter']['duotone'] ) && null !== $duotone_support ) {
			_wp_array_set( $settings, array( 'supports', 'filter', 'duotone' ), (bool) $duotone_support );
		}

		return $settings;
	}

	/**
	 * Gets the CSS filter property value from a preset.
	 *
	 * Exported for the deprecated function wp_get_duotone_filter_id().
	 *
	 * @internal
	 *
	 * @since 6.3.0
	 * @deprecated 6.3.0
	 *
	 * @param array $preset The duotone preset.
	 * @return string The CSS filter property value.
	 */
	public static function get_filter_css_property_value_from_preset( $preset ) {
		_deprecated_function( __FUNCTION__, '6.3.0' );

		if ( isset( $preset['colors'] ) && is_string( $preset['colors'] ) ) {
			return $preset['colors'];
		}

		$filter_id = self::get_filter_id_from_preset( $preset );

		return 'url(#' . $filter_id . ')';
	}
}

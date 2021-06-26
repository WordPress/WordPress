<?php
/**
 * Duotone block support flag.
 *
 * Parts of this source were derived and modified from TinyColor,
 * released under the MIT license.
 *
 * https://github.com/bgrins/TinyColor
 *
 * Copyright (c), Brian Grinstead, http://briangrinstead.com
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
 * @since 5.8.0
 */

/**
 * Takes input from [0, n] and returns it as [0, 1].
 *
 * Direct port of TinyColor's function, lightly simplified to maintain
 * consistency with TinyColor.
 *
 * @see https://github.com/bgrins/TinyColor
 *
 * @since 5.8.0
 * @access private
 *
 * @param mixed $n   Number of unknown type.
 * @param int   $max Upper value of the range to bound to.
 *
 * @return float Value in the range [0, 1].
 */
function wp_tinycolor_bound01( $n, $max ) {
	if ( 'string' === gettype( $n ) && false !== strpos( $n, '.' ) && 1 === (float) $n ) {
		$n = '100%';
	}

	$n = min( $max, max( 0, (float) $n ) );

	// Automatically convert percentage into number.
	if ( 'string' === gettype( $n ) && false !== strpos( $n, '%' ) ) {
		$n = (int) ( $n * $max ) / 100;
	}

	// Handle floating point rounding errors.
	if ( ( abs( $n - $max ) < 0.000001 ) ) {
		return 1.0;
	}

	// Convert into [0, 1] range if it isn't already.
	return ( $n % $max ) / (float) $max;
}

/**
 * Round and convert values of an RGB object.
 *
 * Direct port of TinyColor's function, lightly simplified to maintain
 * consistency with TinyColor.
 *
 * @see https://github.com/bgrins/TinyColor
 *
 * @since 5.8.0
 * @access private
 *
 * @param array $rgb_color RGB object.
 *
 * @return array Rounded and converted RGB object.
 */
function wp_tinycolor_rgb_to_rgb( $rgb_color ) {
	return array(
		'r' => wp_tinycolor_bound01( $rgb_color['r'], 255 ) * 255,
		'g' => wp_tinycolor_bound01( $rgb_color['g'], 255 ) * 255,
		'b' => wp_tinycolor_bound01( $rgb_color['b'], 255 ) * 255,
	);
}

/**
 * Helper function for hsl to rgb conversion.
 *
 * Direct port of TinyColor's function, lightly simplified to maintain
 * consistency with TinyColor.
 *
 * @see https://github.com/bgrins/TinyColor
 *
 * @since 5.8.0
 * @access private
 *
 * @param float $p first component.
 * @param float $q second component.
 * @param float $t third component.
 *
 * @return float R, G, or B component.
 */
function wp_tinycolor_hue_to_rgb( $p, $q, $t ) {
	if ( $t < 0 ) {
		$t += 1;
	}
	if ( $t > 1 ) {
		$t -= 1;
	}
	if ( $t < 1 / 6 ) {
		return $p + ( $q - $p ) * 6 * $t;
	}
	if ( $t < 1 / 2 ) {
		return $q;
	}
	if ( $t < 2 / 3 ) {
		return $p + ( $q - $p ) * ( 2 / 3 - $t ) * 6;
	}
	return $p;
}

/**
 * Convert an HSL object to an RGB object with converted and rounded values.
 *
 * Direct port of TinyColor's function, lightly simplified to maintain
 * consistency with TinyColor.
 *
 * @see https://github.com/bgrins/TinyColor
 *
 * @since 5.8.0
 * @access private
 *
 * @param array $hsl_color HSL object.
 *
 * @return array Rounded and converted RGB object.
 */
function wp_tinycolor_hsl_to_rgb( $hsl_color ) {
	$h = wp_tinycolor_bound01( $hsl_color['h'], 360 );
	$s = wp_tinycolor_bound01( $hsl_color['s'], 100 );
	$l = wp_tinycolor_bound01( $hsl_color['l'], 100 );

	if ( 0 === $s ) {
		// Achromatic.
		$r = $l;
		$g = $l;
		$b = $l;
	} else {
		$q = $l < 0.5 ? $l * ( 1 + $s ) : $l + $s - $l * $s;
		$p = 2 * $l - $q;
		$r = wp_tinycolor_hue_to_rgb( $p, $q, $h + 1 / 3 );
		$g = wp_tinycolor_hue_to_rgb( $p, $q, $h );
		$b = wp_tinycolor_hue_to_rgb( $p, $q, $h - 1 / 3 );
	}

	return array(
		'r' => $r * 255,
		'g' => $g * 255,
		'b' => $b * 255,
	);
}

/**
 * Parses hex, hsl, and rgb CSS strings using the same regex as TinyColor v1.4.2
 * used in the JavaScript. Only colors output from react-color are implemented
 * and the alpha value is ignored as it is not used in duotone.
 *
 * Direct port of TinyColor's function, lightly simplified to maintain
 * consistency with TinyColor.
 *
 * @see https://github.com/bgrins/TinyColor
 * @see https://github.com/casesandberg/react-color/
 *
 * @since 5.8.0
 * @access private
 *
 * @param string $color_str CSS color string.
 *
 * @return array RGB object.
 */
function wp_tinycolor_string_to_rgb( $color_str ) {
	$color_str = strtolower( trim( $color_str ) );

	$css_integer = '[-\\+]?\\d+%?';
	$css_number  = '[-\\+]?\\d*\\.\\d+%?';

	$css_unit = '(?:' . $css_number . ')|(?:' . $css_integer . ')';

	$permissive_match3 = '[\\s|\\(]+(' . $css_unit . ')[,|\\s]+(' . $css_unit . ')[,|\\s]+(' . $css_unit . ')\\s*\\)?';
	$permissive_match4 = '[\\s|\\(]+(' . $css_unit . ')[,|\\s]+(' . $css_unit . ')[,|\\s]+(' . $css_unit . ')[,|\\s]+(' . $css_unit . ')\\s*\\)?';

	$rgb_regexp = '/^rgb' . $permissive_match3 . '$/';
	if ( preg_match( $rgb_regexp, $color_str, $match ) ) {
		return wp_tinycolor_rgb_to_rgb(
			array(
				'r' => $match[1],
				'g' => $match[2],
				'b' => $match[3],
			)
		);
	}

	$rgba_regexp = '/^rgba' . $permissive_match4 . '$/';
	if ( preg_match( $rgba_regexp, $color_str, $match ) ) {
		return wp_tinycolor_rgb_to_rgb(
			array(
				'r' => $match[1],
				'g' => $match[2],
				'b' => $match[3],
			)
		);
	}

	$hsl_regexp = '/^hsl' . $permissive_match3 . '$/';
	if ( preg_match( $hsl_regexp, $color_str, $match ) ) {
		return wp_tinycolor_hsl_to_rgb(
			array(
				'h' => $match[1],
				's' => $match[2],
				'l' => $match[3],
			)
		);
	}

	$hsla_regexp = '/^hsla' . $permissive_match4 . '$/';
	if ( preg_match( $hsla_regexp, $color_str, $match ) ) {
		return wp_tinycolor_hsl_to_rgb(
			array(
				'h' => $match[1],
				's' => $match[2],
				'l' => $match[3],
			)
		);
	}

	$hex8_regexp = '/^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/';
	if ( preg_match( $hex8_regexp, $color_str, $match ) ) {
		return wp_tinycolor_rgb_to_rgb(
			array(
				'r' => base_convert( $match[1], 16, 10 ),
				'g' => base_convert( $match[2], 16, 10 ),
				'b' => base_convert( $match[3], 16, 10 ),
			)
		);
	}

	$hex6_regexp = '/^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/';
	if ( preg_match( $hex6_regexp, $color_str, $match ) ) {
		return wp_tinycolor_rgb_to_rgb(
			array(
				'r' => base_convert( $match[1], 16, 10 ),
				'g' => base_convert( $match[2], 16, 10 ),
				'b' => base_convert( $match[3], 16, 10 ),
			)
		);
	}

	$hex4_regexp = '/^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/';
	if ( preg_match( $hex4_regexp, $color_str, $match ) ) {
		return wp_tinycolor_rgb_to_rgb(
			array(
				'r' => base_convert( $match[1] . $match[1], 16, 10 ),
				'g' => base_convert( $match[2] . $match[2], 16, 10 ),
				'b' => base_convert( $match[3] . $match[3], 16, 10 ),
			)
		);
	}

	$hex3_regexp = '/^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/';
	if ( preg_match( $hex3_regexp, $color_str, $match ) ) {
		return wp_tinycolor_rgb_to_rgb(
			array(
				'r' => base_convert( $match[1] . $match[1], 16, 10 ),
				'g' => base_convert( $match[2] . $match[2], 16, 10 ),
				'b' => base_convert( $match[3] . $match[3], 16, 10 ),
			)
		);
	}
}


/**
 * Registers the style and colors block attributes for block types that support it.
 *
 * @since 5.8.0
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_duotone_support( $block_type ) {
	$has_duotone_support = false;
	if ( property_exists( $block_type, 'supports' ) ) {
		$has_duotone_support = _wp_array_get( $block_type->supports, array( 'color', '__experimentalDuotone' ), false );
	}

	if ( $has_duotone_support ) {
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
 * Render out the duotone stylesheet and SVG.
 *
 * @since 5.8.0
 * @access private
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 *
 * @return string Filtered block content.
 */
function wp_render_duotone_support( $block_content, $block ) {
	$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );

	$duotone_support = false;
	if ( $block_type && property_exists( $block_type, 'supports' ) ) {
		$duotone_support = _wp_array_get( $block_type->supports, array( 'color', '__experimentalDuotone' ), false );
	}

	$has_duotone_attribute = isset( $block['attrs']['style']['color']['duotone'] );

	if (
		! $duotone_support ||
		! $has_duotone_attribute
	) {
		return $block_content;
	}

	$duotone_colors = $block['attrs']['style']['color']['duotone'];

	$duotone_values = array(
		'r' => array(),
		'g' => array(),
		'b' => array(),
	);
	foreach ( $duotone_colors as $color_str ) {
		$color = wp_tinycolor_string_to_rgb( $color_str );

		$duotone_values['r'][] = $color['r'] / 255;
		$duotone_values['g'][] = $color['g'] / 255;
		$duotone_values['b'][] = $color['b'] / 255;
	}

	$duotone_id = 'wp-duotone-filter-' . uniqid();

	$selectors        = explode( ',', $duotone_support );
	$selectors_scoped = array_map(
		function ( $selector ) use ( $duotone_id ) {
			return '.' . $duotone_id . ' ' . trim( $selector );
		},
		$selectors
	);
	$selectors_group  = implode( ', ', $selectors_scoped );

	ob_start();

	?>

	<style>
		<?php echo $selectors_group; ?> {
			filter: url( <?php echo esc_url( '#' . $duotone_id ); ?> );
		}
	</style>

	<svg
		xmlns:xlink="http://www.w3.org/1999/xlink"
		viewBox="0 0 0 0"
		width="0"
		height="0"
		focusable="false"
		role="none"
		style="visibility: hidden; position: absolute; left: -9999px; overflow: hidden;"
	>
		<defs>
			<filter id="<?php echo esc_attr( $duotone_id ); ?>">
				<feColorMatrix
					type="matrix"
					<?php // phpcs:disable Generic.WhiteSpace.DisallowSpaceIndent ?>
					values=".299 .587 .114 0 0
							.299 .587 .114 0 0
							.299 .587 .114 0 0
							0 0 0 1 0"
					<?php // phpcs:enable Generic.WhiteSpace.DisallowSpaceIndent ?>
				/>
				<feComponentTransfer color-interpolation-filters="sRGB" >
					<feFuncR type="table" tableValues="<?php echo esc_attr( implode( ' ', $duotone_values['r'] ) ); ?>" />
					<feFuncG type="table" tableValues="<?php echo esc_attr( implode( ' ', $duotone_values['g'] ) ); ?>" />
					<feFuncB type="table" tableValues="<?php echo esc_attr( implode( ' ', $duotone_values['b'] ) ); ?>" />
				</feComponentTransfer>
			</filter>
		</defs>
	</svg>

	<?php

	$duotone = ob_get_clean();

	// Like the layout hook, this assumes the hook only applies to blocks with a single wrapper.
	$content = preg_replace(
		'/' . preg_quote( 'class="', '/' ) . '/',
		'class="' . $duotone_id . ' ',
		$block_content,
		1
	);

	return $content . $duotone;
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'duotone',
	array(
		'register_attribute' => 'wp_register_duotone_support',
	)
);
add_filter( 'render_block', 'wp_render_duotone_support', 10, 2 );

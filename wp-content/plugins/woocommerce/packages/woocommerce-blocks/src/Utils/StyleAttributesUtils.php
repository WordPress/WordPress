<?php
namespace Automattic\WooCommerce\Blocks\Utils;

/**
 * StyleAttributesUtils class used for getting class and style from attributes.
 */
class StyleAttributesUtils {

	/**
	 * If color value is in preset format, convert it to a CSS var. Else return same value
	 * For example:
	 * "var:preset|color|pale-pink" -> "var(--wp--preset--color--pale-pink)"
	 * "#98b66e" -> "#98b66e"
	 *
	 * @param string $color_value value to be processed.
	 *
	 * @return (string)
	 */
	public static function get_color_value( $color_value ) {
		if ( is_string( $color_value ) && str_contains( $color_value, 'var:preset|color|' ) ) {
			$color_value = str_replace( 'var:preset|color|', '', $color_value );
			return sprintf( 'var(--wp--preset--color--%s)', $color_value );
		}

		return $color_value;
	}

	/**
	 * Get CSS value for color preset.
	 *
	 * @param string $preset_name Preset name.
	 *
	 * @return string CSS value for color preset.
	 */
	public static function get_preset_value( $preset_name ) {
		return "var(--wp--preset--color--$preset_name)";
	}

	/**
	 * If spacing value is in preset format, convert it to a CSS var. Else return same value
	 * For example:
	 * "var:preset|spacing|50" -> "var(--wp--preset--spacing--50)"
	 * "50px" -> "50px"
	 *
	 * @param string $spacing_value value to be processed.
	 *
	 * @return (string)
	 */
	public static function get_spacing_value( $spacing_value ) {
		// Used following code as reference: https://github.com/WordPress/gutenberg/blob/cff6d70d6ff5a26e212958623dc3130569f95685/lib/block-supports/layout.php/#L219-L225.
		if ( is_string( $spacing_value ) && str_contains( $spacing_value, 'var:preset|spacing|' ) ) {
			$spacing_value = str_replace( 'var:preset|spacing|', '', $spacing_value );
			return sprintf( 'var(--wp--preset--spacing--%s)', $spacing_value );
		}

		return $spacing_value;
	}

	/**
	 * Get class and style for align from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_align_class_and_style( $attributes ) {

		$align_attribute = $attributes['align'] ?? null;

		if ( ! $align_attribute ) {
			return null;
		}

		if ( 'wide' === $align_attribute ) {
			return array(
				'class' => 'alignwide',
				'style' => null,
			);
		}

		if ( 'full' === $align_attribute ) {
			return array(
				'class' => 'alignfull',
				'style' => null,
			);
		}

		if ( 'left' === $align_attribute ) {
			return array(
				'class' => 'alignleft',
				'style' => null,
			);
		}

		if ( 'right' === $align_attribute ) {
			return array(
				'class' => 'alignright',
				'style' => null,
			);
		}

		if ( 'center' === $align_attribute ) {
			return array(
				'class' => 'aligncenter',
				'style' => null,
			);
		}

		return null;
	}

	/**
	 * Get class and style for background-color from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_background_color_class_and_style( $attributes ) {

		$background_color = $attributes['backgroundColor'] ?? '';

		$custom_background_color = $attributes['style']['color']['background'] ?? '';

		if ( ! $background_color && '' === $custom_background_color ) {
			return null;
		}

		if ( $background_color ) {
			return array(
				'class' => sprintf( 'has-background has-%s-background-color', $background_color ),
				'style' => null,
				'value' => self::get_preset_value( $background_color ),
			);
		} elseif ( '' !== $custom_background_color ) {
			return array(
				'class' => null,
				'style' => sprintf( 'background-color: %s;', $custom_background_color ),
				'value' => $custom_background_color,
			);
		}
		return null;
	}

	/**
	 * Get class and style for border-color from attributes.
	 *
	 * Data passed to this function is not always consistent. It can be:
	 * Linked - preset color: $attributes['borderColor'] => 'luminous-vivid-orange'.
	 * Linked - custom color: $attributes['style']['border']['color'] => '#681228'.
	 * Unlinked - preset color: $attributes['style']['border']['top']['color'] => 'var:preset|color|luminous-vivid-orange'
	 * Unlinked - custom color: $attributes['style']['border']['top']['color'] => '#681228'.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_border_color_class_and_style( $attributes ) {

		$border_color_linked_preset = $attributes['borderColor'] ?? '';
		$border_color_linked_custom = $attributes['style']['border']['color'] ?? '';
		$custom_border              = $attributes['style']['border'] ?? '';

		$border_color_class = '';
		$border_color_css   = '';

		if ( $border_color_linked_preset ) {
			// Linked preset color.
			$border_color_class = sprintf( 'has-border-color has-%s-border-color', $border_color_linked_preset );
		} elseif ( $border_color_linked_custom ) {
			// Linked custom color.
			$border_color_css .= 'border-color:' . $border_color_linked_custom . ';';
		} else {
			// Unlinked.
			if ( is_array( $custom_border ) ) {
				foreach ( $custom_border as $border_color_key => $border_color_value ) {
					if ( is_array( $border_color_value ) && array_key_exists( 'color', ( $border_color_value ) ) ) {
						$border_color_css .= 'border-' . $border_color_key . '-color:' . self::get_color_value( $border_color_value['color'] ) . ';';
					}
				}
			}
		}

		if ( ! $border_color_class && ! $border_color_css ) {
			return null;
		}

		return array(
			'class' => $border_color_class,
			'style' => $border_color_css,
		);
	}

	/**
	 * Get class and style for border-radius from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_border_radius_class_and_style( $attributes ) {

		$custom_border_radius = $attributes['style']['border']['radius'] ?? '';

		if ( '' === $custom_border_radius ) {
			return null;
		}

		$border_radius_css = '';

		if ( is_string( $custom_border_radius ) ) {
			// Linked sides.
			$border_radius_css = 'border-radius:' . $custom_border_radius . ';';
		} else {
			// Unlinked sides.
			$border_radius = array();

			$border_radius['border-top-left-radius']     = $custom_border_radius['topLeft'] ?? '';
			$border_radius['border-top-right-radius']    = $custom_border_radius['topRight'] ?? '';
			$border_radius['border-bottom-right-radius'] = $custom_border_radius['bottomRight'] ?? '';
			$border_radius['border-bottom-left-radius']  = $custom_border_radius['bottomLeft'] ?? '';

			foreach ( $border_radius as $border_radius_side => $border_radius_value ) {
				if ( '' !== $border_radius_value ) {
					$border_radius_css .= $border_radius_side . ':' . $border_radius_value . ';';
				}
			}
		}

		return array(
			'class' => null,
			'style' => $border_radius_css,
		);
	}

	/**
	 * Get class and style for border width from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_border_width_class_and_style( $attributes ) {

		$custom_border = $attributes['style']['border'] ?? '';

		if ( '' === $custom_border ) {
			return null;
		}

		$border_width_css = '';

		if ( array_key_exists( 'width', ( $custom_border ) ) && ! empty( $custom_border['width'] ) ) {
			// Linked sides.
			$border_width_css = 'border-width:' . $custom_border['width'] . ';';
		} else {
			// Unlinked sides.
			foreach ( $custom_border as $border_width_side => $border_width_value ) {
				if ( isset( $border_width_value['width'] ) ) {
					$border_width_css .= 'border-' . $border_width_side . '-width:' . $border_width_value['width'] . ';';
				}
			}
		}

		return array(
			'class' => null,
			'style' => $border_width_css,
		);
	}

	/**
	 * Get space-separated classes from block attributes.
	 *
	 * @param array $attributes Block attributes.
	 * @param array $properties Properties to get classes from.
	 *
	 * @return string Space-separated classes.
	 */
	public static function get_classes_by_attributes( $attributes, $properties = array() ) {
		$classes_and_styles = self::get_classes_and_styles_by_attributes( $attributes, $properties );

		return $classes_and_styles['classes'];
	}

	/**
	 * Get class and style for font-family from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_font_family_class_and_style( $attributes ) {

		$font_family = $attributes['fontFamily'] ?? '';

		if ( $font_family ) {
			return array(
				'class' => sprintf( 'has-%s-font-family', $font_family ),
				'style' => null,
			);
		}
		return null;
	}

	/**
	 * Get class and style for font-size from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_font_size_class_and_style( $attributes ) {

		$font_size = $attributes['fontSize'] ?? '';

		$custom_font_size = $attributes['style']['typography']['fontSize'] ?? '';

		if ( ! $font_size && '' === $custom_font_size ) {
			return null;
		}

		if ( $font_size ) {
			return array(
				'class' => sprintf( 'has-font-size has-%s-font-size', $font_size ),
				'style' => null,
			);
		} elseif ( '' !== $custom_font_size ) {
			return array(
				'class' => null,
				'style' => sprintf( 'font-size: %s;', $custom_font_size ),
			);
		}
		return null;
	}

	/**
	 * Get class and style for font-style from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_font_style_class_and_style( $attributes ) {

		$custom_font_style = $attributes['style']['typography']['fontStyle'] ?? '';

		if ( '' !== $custom_font_style ) {
			return array(
				'class' => null,
				'style' => sprintf( 'font-style: %s;', $custom_font_style ),
			);
		}
		return null;
	}

	/**
	 * Get class and style for font-weight from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_font_weight_class_and_style( $attributes ) {

		$custom_font_weight = $attributes['style']['typography']['fontWeight'] ?? '';

		if ( '' !== $custom_font_weight ) {
			return array(
				'class' => null,
				'style' => sprintf( 'font-weight: %s;', $custom_font_weight ),
			);
		}
		return null;
	}

	/**
	 * Get class and style for letter-spacing from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_letter_spacing_class_and_style( $attributes ) {

		$custom_letter_spacing = $attributes['style']['typography']['letterSpacing'] ?? '';

		if ( '' !== $custom_letter_spacing ) {
			return array(
				'class' => null,
				'style' => sprintf( 'letter-spacing: %s;', $custom_letter_spacing ),
			);
		}
		return null;
	}

	/**
	 * Get class and style for line height from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_line_height_class_and_style( $attributes ) {

		$line_height = $attributes['style']['typography']['lineHeight'] ?? '';

		if ( ! $line_height ) {
			return null;
		}

		return array(
			'class' => null,
			'style' => sprintf( 'line-height: %s;', $line_height ),
		);
	}

	/**
	 * Get class and style for link-color from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_link_color_class_and_style( $attributes ) {

		if ( ! isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
			return null;
		}

		$link_color = $attributes['style']['elements']['link']['color']['text'];

		// If the link color is selected from the theme color picker, the value of $link_color is var:preset|color|slug.
		// If the link color is selected from the core color picker, the value of $link_color is an hex value.
		// When the link color is a string var:preset|color|slug we parsed it for get the slug, otherwise we use the hex value.
		$index_named_link_color = strrpos( $link_color, '|' );

		if ( ! empty( $index_named_link_color ) ) {
			$parsed_named_link_color = substr( $link_color, $index_named_link_color + 1 );
			return array(
				'class' => null,
				'style' => sprintf( 'color: %s;', self::get_preset_value( $parsed_named_link_color ) ),
				'value' => self::get_preset_value( $parsed_named_link_color ),
			);
		} else {
			return array(
				'class' => null,
				'style' => sprintf( 'color: %s;', $link_color ),
				'value' => $link_color,
			);
		}
	}

	/**
	 * Get class and style for margin from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_margin_class_and_style( $attributes ) {
		$margin = $attributes['style']['spacing']['margin'] ?? null;

		if ( ! $margin ) {
			return null;
		}

		$spacing_values_css = '';

		foreach ( $margin as $margin_side => $margin_value ) {
			$spacing_values_css .= 'margin-' . $margin_side . ':' . self::get_spacing_value( $margin_value ) . ';';
		}

		return array(
			'class' => null,
			'style' => $spacing_values_css,
		);
	}

	/**
	 * Get class and style for padding from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_padding_class_and_style( $attributes ) {
		$padding = $attributes['style']['spacing']['padding'] ?? null;

		if ( ! $padding ) {
			return null;
		}

		$spacing_values_css = '';

		foreach ( $padding as $padding_side => $padding_value ) {
			$spacing_values_css .= 'padding-' . $padding_side . ':' . self::get_spacing_value( $padding_value ) . ';';
		}

		return array(
			'class' => null,
			'style' => $spacing_values_css,
		);
	}

	/**
	 * Get space-separated style rules from block attributes.
	 *
	 * @param array $attributes Block attributes.
	 * @param array $properties Properties to get styles from.
	 *
	 * @return string Space-separated style rules.
	 */
	public static function get_styles_by_attributes( $attributes, $properties = array() ) {
		$classes_and_styles = self::get_classes_and_styles_by_attributes( $attributes, $properties );

		return $classes_and_styles['styles'];
	}

	/**
	 * Get class and style for text align from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_text_align_class_and_style( $attributes ) {

		if ( isset( $attributes['textAlign'] ) ) {
			return array(
				'class' => 'has-text-align-' . $attributes['textAlign'],
				'style' => null,
			);
		}

		return null;
	}

	/**
	 * Get class and style for text-color from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_text_color_class_and_style( $attributes ) {

		$text_color = $attributes['textColor'] ?? '';

		$custom_text_color = $attributes['style']['color']['text'] ?? '';

		if ( ! $text_color && ! $custom_text_color ) {
			return null;
		}

		if ( $text_color ) {
			return array(
				'class' => sprintf( 'has-text-color has-%s-color', $text_color ),
				'style' => null,
				'value' => self::get_preset_value( $text_color ),
			);
		} elseif ( $custom_text_color ) {
			return array(
				'class' => null,
				'style' => sprintf( 'color: %s;', $custom_text_color ),
				'value' => $custom_text_color,
			);
		}
		return null;
	}

	/**
	 * Get class and style for text-decoration from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_text_decoration_class_and_style( $attributes ) {

		$custom_text_decoration = $attributes['style']['typography']['textDecoration'] ?? '';

		if ( '' !== $custom_text_decoration ) {
			return array(
				'class' => null,
				'style' => sprintf( 'text-decoration: %s;', $custom_text_decoration ),
			);
		}
		return null;
	}

	/**
	 * Get class and style for text-transform from attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return (array | null)
	 */
	public static function get_text_transform_class_and_style( $attributes ) {

		$custom_text_transform = $attributes['style']['typography']['textTransform'] ?? '';

		if ( '' !== $custom_text_transform ) {
			return array(
				'class' => null,
				'style' => sprintf( 'text-transform: %s;', $custom_text_transform ),
			);
		}
		return null;
	}

	/**
	 * Get classes and styles from attributes.
	 *
	 * @param array $attributes Block attributes.
	 * @param array $properties Properties to get classes/styles from.
	 *
	 * @return array
	 */
	public static function get_classes_and_styles_by_attributes( $attributes, $properties = array() ) {
		$classes_and_styles = array(
			'align'            => self::get_align_class_and_style( $attributes ),
			'background_color' => self::get_background_color_class_and_style( $attributes ),
			'border_color'     => self::get_border_color_class_and_style( $attributes ),
			'border_radius'    => self::get_border_radius_class_and_style( $attributes ),
			'border_width'     => self::get_border_width_class_and_style( $attributes ),
			'font_family'      => self::get_font_family_class_and_style( $attributes ),
			'font_size'        => self::get_font_size_class_and_style( $attributes ),
			'font_style'       => self::get_font_style_class_and_style( $attributes ),
			'font_weight'      => self::get_font_weight_class_and_style( $attributes ),
			'letter_spacing'   => self::get_letter_spacing_class_and_style( $attributes ),
			'line_height'      => self::get_line_height_class_and_style( $attributes ),
			'link_color'       => self::get_link_color_class_and_style( $attributes ),
			'margin'           => self::get_margin_class_and_style( $attributes ),
			'padding'          => self::get_padding_class_and_style( $attributes ),
			'text_align'       => self::get_text_align_class_and_style( $attributes ),
			'text_color'       => self::get_text_color_class_and_style( $attributes ),
			'text_decoration'  => self::get_text_decoration_class_and_style( $attributes ),
			'text_transform'   => self::get_text_transform_class_and_style( $attributes ),
		);

		if ( ! empty( $properties ) ) {
			foreach ( $classes_and_styles as $key => $value ) {
				if ( ! in_array( $key, $properties, true ) ) {
					unset( $classes_and_styles[ $key ] );
				}
			}
		}

		$classes_and_styles = array_filter( $classes_and_styles );

		$classes = array_map(
			function( $item ) {
				return $item['class'];
			},
			$classes_and_styles
		);

		$styles = array_map(
			function( $item ) {
				return $item['style'];
			},
			$classes_and_styles
		);

		$classes = array_filter( $classes );
		$styles  = array_filter( $styles );

		return array(
			'classes' => implode( ' ', $classes ),
			'styles'  => implode( ' ', $styles ),
		);
	}
}

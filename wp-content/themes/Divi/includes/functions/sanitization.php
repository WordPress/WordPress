<?php
/**
 * Sanitize float number
 * @param mixed
 * @return float
 */
function et_sanitize_float_number( $number ) {
	return floatval( $number );
}

/**
 * Sanitize integer number
 * @param mixed
 * @return int
 */
function et_sanitize_int_number( $number ) {
	return intval( $number );
}

/**
 * Sanitize font style
 * @param string
 * @param string
 */
function et_sanitize_font_style( $styles ) {
	// List of allowable style
	$allowed_styles = array_keys( et_divi_font_style_choices() );

	// Explodes styles into array
	$styles_array = explode( '|', $styles );

	// Get valid styles
	$valid_styles = array_intersect( $allowed_styles, $styles_array );

	// Return sanitized styles
	return implode( "|", $valid_styles );
}

/**
 * Sanitize choosen option based on options' key
 * @param string
 * @param array
 * @return string|bool
 */
function et_sanitize_key_based_option( $choosen, $options, $default = false ) {
	// Validate choosen option based on available options
	if ( ! isset( $options[ $choosen ] ) ) {
		return $default;
	}

	return $choosen;
}

/**
 * Sanitize font choice
 * @param string
 * @return string|bool
 */
function et_sanitize_font_choices( $choosen ) {
	return et_sanitize_key_based_option( $choosen, et_builder_get_fonts() );
}

/**
 * Sanitize color scheme
 * @param string
 * @return string|bool
 */
function et_sanitize_color_scheme( $choosen ) {
	return et_sanitize_key_based_option( $choosen, et_divi_color_scheme_choices() );
}

/**
 * Sanitize header style
 * @param string
 * @return string|bool
 */
function et_sanitize_header_style( $choosen ) {
	return et_sanitize_key_based_option( $choosen, et_divi_header_style_choices() );
}

/**
 * Sanitize dropdown animation
 * @param string
 * @return string|bool
 */
function et_sanitize_dropdown_animation( $choosen ) {
	return et_sanitize_key_based_option( $choosen, et_divi_dropdown_animation_choices() );
}

/**
 * Sanitize footer column
 * @param string
 * @return string|bool
 */
function et_sanitize_footer_column( $choosen ) {
	return et_sanitize_key_based_option( $choosen, et_divi_footer_column_choices() );
}

/**
 * Sanitize yes no choices
 * @param string
 * @return string|bool
 */
function et_sanitize_yes_no( $choosen ) {
	return et_sanitize_key_based_option( $choosen, et_divi_yes_no_choices() );
}

/**
 * Sanitize left or right choices
 * @param string
 * @return string|bool
 */
function et_sanitize_left_right( $choosen ) {
	return et_sanitize_key_based_option( $choosen, et_divi_left_right_choices() );
}

/**
 * Sanitize image animation choices
 * @param string
 * @return string|bool
 */
function et_sanitize_image_animation( $choosen ) {
	return et_sanitize_key_based_option( $choosen, et_divi_image_animation_choices() );
}

/**
 * Sanitize divider style choices
 * @param string
 * @return string|bool
 */
function et_sanitize_divider_style( $choosen ) {
	return et_sanitize_key_based_option( $choosen, et_divi_divider_style_choices() );
}

/**
 * Sanitize divider position choices
 * @param string
 * @return string|bool
 */
function et_sanitize_divider_position( $choosen ) {
	return et_sanitize_key_based_option( $choosen, et_divi_divider_position_choices() );
}

/**
 * Sanitize RGBA color
 * @param string
 * @return string|bool
 */
function et_sanitize_alpha_color( $color ) {
	// Trim unneeded whitespace
	$color = str_replace( ' ', '', $color );

	// If this is hex color, validate and return it
	if ( 1 === preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
		return $color;
	}

	// If this is rgb, validate and return it
	elseif ( 'rgb(' === substr( $color, 0, 4 ) ) {
		sscanf( $color, 'rgb(%d,%d,%d)', $red, $green, $blue );

		if ( ( $red >= 0 && $red <= 255 ) &&
			 ( $green >= 0 && $green <= 255 ) &&
			 ( $blue >= 0 && $blue <= 255 )
			) {
			return "rgb({$red},{$green},{$blue})";
		}
	}

	// If this is rgba, validate and return it
	elseif ( 'rgba(' === substr( $color, 0, 5 ) ) {
		sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );

		if ( ( $red >= 0 && $red <= 255 ) &&
			 ( $green >= 0 && $green <= 255 ) &&
			 ( $blue >= 0 && $blue <= 255 ) &&
			   $alpha >= 0 && $alpha <= 1
			) {
			return "rgba({$red},{$green},{$blue},{$alpha})";
		}
	}

	return false;
}

/**
 * Sanitize font icon
 * @param string
 * @param string
 * @return string
 */
function et_sanitize_font_icon( $font_icon, $symbols_function = 'default' ) {
	// Convert symbols into strings
	$font_icon = trim( $font_icon );
	$icon_symbols = is_callable( $symbols_function ) ? call_user_func( $symbols_function ) : et_pb_get_font_icon_symbols();
	$icon_symbols = array_map( 'et_sanitize_font_icon_convert_icon_to_string', $icon_symbols );

	// the exact font icon value is saved
	if ( 1 !== preg_match( "/^%%/", $font_icon ) ) {
		return in_array( $font_icon, $icon_symbols ) ? $font_icon : '';
	}

	// the font icon value is saved in the following format: %%index_number%%
	// strip the %'s to get to end result: index_number
	$icon_index = (int) str_replace( '%', '', $font_icon );
	return isset( $icon_symbols[ $icon_index ] ) ? $icon_symbols[ $icon_index ] : '';
}

/**
 * Convert font hex-code font icons into strings so it can be compared
 * @param string
 * @return string
 */
function et_sanitize_font_icon_convert_icon_to_string( $icon ) {
	// Replace &amp; with &. Otherwise, it'll incorrectly decoded
	$icon = str_replace( '&amp;', '&', $icon );

	// Decode
	return html_entity_decode( $icon );
}

/**
 * Array of allowed html tags on short block
 * @return array
 */
function et_allowed_html_tags_short_block() {
	$allowed_tags = array(
		'div' => array(
			'class' => array(),
			'id'    => array(),
		),
		'span' => array(
			'class' => array(),
			'id'    => array(),
		),
		'ol' => array(
			'class' => array(),
			'id'    => array(),
		),
		'ul' => array(
			'class' => array(),
			'id'    => array(),
		),
		'li' => array(
			'class' => array(),
			'id'    => array(),
		),
		'p' => array(
			'class' => array(),
			'id'    => array(),
		),
		'a' => array(
			'href'  => array(),
			'class' => array(),
			'id'    => array(),
			'rel'   => array(),
			'title'    => array(),
			'target'   => array(),
		),
		'br' => array(),
		'em' => array(),
		'strong' => array(),
	);

	return apply_filters( 'et_allowed_html_tags_short_block', $allowed_tags );
}

/**
 * Sanitize short block html input
 * @return string
 */
function et_sanitize_html_input_text( $string ) {
	return wp_kses( $string, et_allowed_html_tags_short_block() );
}

/**
 * Sanitize background repeat value
 * @return string
 */
function et_sanitize_background_repeat( $choosen ) {
	return et_sanitize_key_based_option(
		$choosen,
		et_divi_background_repeat_choices(),
		apply_filters( 'et_divi_background_repeat_default', 'repeat' )
	);
}

/**
 * Sanitize background attachment value
 * @return string
 */
function et_sanitize_background_attachment( $choosen ) {
	return et_sanitize_key_based_option(
		$choosen,
		et_divi_background_attachment_choices(),
		apply_filters( 'et_sanitize_background_attachment_default', 'scroll' )
	);
}

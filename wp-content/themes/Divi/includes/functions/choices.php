<?php
if ( ! function_exists( 'et_divi_font_style_choices' ) ) :
/**
 * Returns font style options
 * @return array
 */
function et_divi_font_style_choices() {
	return apply_filters( 'et_divi_font_style_choices', array(
		'bold'       => esc_html__( 'Bold', 'Divi' ),
		'italic'     => esc_html__( 'Italic', 'Divi' ),
		'uppercase'  => esc_html__( 'Uppercase', 'Divi' ),
		'underline'  => esc_html__( 'Underline', 'Divi' ),
	) );
}
endif;

if ( ! function_exists( 'et_divi_color_scheme_choices' ) ) :
/**
 * Returns list of color scheme used by Divi
 * @return array
 */
function et_divi_color_scheme_choices() {
	return apply_filters( 'et_divi_color_scheme_choices', array(
		'none'   => esc_html__( 'Default', 'Divi' ),
		'green'  => esc_html__( 'Green', 'Divi' ),
		'orange' => esc_html__( 'Orange', 'Divi' ),
		'pink'   => esc_html__( 'Pink', 'Divi' ),
		'red'    => esc_html__( 'Red', 'Divi' ),
	) );
}
endif;

if ( ! function_exists( 'et_divi_header_style_choices' ) ) :
/**
 * Returns list of header styles used by Divi
 * @return array
 */
function et_divi_header_style_choices() {
	return apply_filters( 'et_divi_header_style_choices', array(
		'left'       => esc_html__( 'Default', 'Divi' ),
		'centered'   => esc_html__( 'Centered', 'Divi' ),
		'split'	     => esc_html__( 'Centered Inline Logo', 'Divi' ),
		'slide'      => esc_html__( 'Slide In', 'Divi' ),
		'fullscreen' => esc_html__( 'Fullscreen', 'Divi' ),
	) );
}
endif;

if ( ! function_exists( 'et_divi_dropdown_animation_choices' ) ) :
/**
 * Returns list of dropdown animation
 * @return array
 */
function et_divi_dropdown_animation_choices() {
	return apply_filters( 'et_divi_dropdown_animation_choices', array(
		'fade'     => esc_html__( 'Fade', 'Divi' ),
		'expand'   => esc_html__( 'Expand', 'Divi' ),
		'slide'	   => esc_html__( 'Slide', 'Divi' ),
		'flip'	   => esc_html__( 'Flip', 'Divi' )
	) );
}
endif;

if ( ! function_exists( 'et_divi_footer_column_choices' ) ) :
/**
 * Returns list of footer column choices
 * @return array
 */
function et_divi_footer_column_choices() {
	return apply_filters( 'et_divi_footer_column_choices', array(
		'4'			=> sprintf( esc_html__( '%1$s Columns', 'Divi' ), '4' ),
		'3' 		=> sprintf( esc_html__( '%1$s Columns', 'Divi' ), '3' ),
		'2' 		=> sprintf( esc_html__( '%1$s Columns', 'Divi' ), '2' ),
		'1'  		=> esc_html__( '1 Column', 'Divi' ),
		'_1_4__3_4' => sprintf( esc_html__( '%1$s Columns', 'Divi' ), '1/4 + 3/4' ),
		'_3_4__1_4' => sprintf( esc_html__( '%1$s Columns', 'Divi' ), '3/4 + 1/4' ),
		'_1_3__2_3' => sprintf( esc_html__( '%1$s Columns', 'Divi' ), '1/3 + 2/3' ),
		'_2_3__1_3' => sprintf( esc_html__( '%1$s Columns', 'Divi' ), '2/3 + 1/3' ),
		'_1_4__1_2' => sprintf( esc_html__( '%1$s Columns', 'Divi' ), '1/4 + 1/4 + 1/2' ),
		'_1_2__1_4' => sprintf( esc_html__( '%1$s Columns', 'Divi' ), '1/2 + 1/4 + 1/4' ),
	) );
}
endif;

if ( ! function_exists( 'et_divi_yes_no_choices' ) ) :
/**
 * Returns yes no choices
 * @return array
 */
function et_divi_yes_no_choices() {
	return apply_filters( 'et_divi_yes_no_choices', array(
		'yes'  => esc_html__( 'Yes', 'Divi' ),
		'no'   => esc_html__( 'No', 'Divi' )
	) );
}
endif;

if ( ! function_exists( 'et_divi_left_right_choices' ) ) :
/**
 * Returns left or right choices
 * @return array
 */
function et_divi_left_right_choices() {
	return apply_filters( 'et_divi_left_right_choices', array(
		'right'  => esc_html__( 'Right', 'Divi' ),
		'left'   => esc_html__( 'Left', 'Divi' )
	) );
}
endif;

if ( ! function_exists( 'et_divi_image_animation_choices' ) ) :
/**
 * Returns image animation choices
 * @return array
 */
function et_divi_image_animation_choices() {
	return apply_filters( 'et_divi_image_animation_choices', array(
		'left' 		=> esc_html__( 'Left to Right', 'Divi' ),
		'right' 	=> esc_html__( 'Right to Left', 'Divi' ),
		'top' 		=> esc_html__( 'Top to Bottom', 'Divi' ),
		'bottom' 	=> esc_html__( 'Bottom to Top', 'Divi' ),
		'fade_in'	=> esc_html__( 'Fade In', 'Divi' ),
		'off' 		=> esc_html__( 'No Animation', 'Divi' ),
	) );
}
endif;

if ( ! function_exists( 'et_divi_divider_style_choices' ) ) :
/**
 * Returns divider style choices
 * @return array
 */
function et_divi_divider_style_choices() {
	return apply_filters( 'et_divi_divider_style_choices', array(
		'solid'		=> esc_html__( 'Solid', 'Divi' ),
		'dotted'	=> esc_html__( 'Dotted', 'Divi' ),
		'dashed'	=> esc_html__( 'Dashed', 'Divi' ),
		'double'	=> esc_html__( 'Double', 'Divi' ),
		'groove'	=> esc_html__( 'Groove', 'Divi' ),
		'ridge'		=> esc_html__( 'Ridge', 'Divi' ),
		'inset'		=> esc_html__( 'Inset', 'Divi' ),
		'outset'	=> esc_html__( 'Outset', 'Divi' ),
	) );
}
endif;

if ( ! function_exists( 'et_divi_divider_position_choices' ) ) :
/**
 * Returns divider position choices
 * @return array
 */
function et_divi_divider_position_choices() {
	return apply_filters( 'et_divi_divider_position_choices', array(
		'top'		=> esc_html__( 'Top', 'Divi' ),
		'center'	=> esc_html__( 'Vertically Centered', 'Divi' ),
		'bottom'	=> esc_html__( 'Bottom', 'Divi' ),
	) );
}
endif;

if ( ! function_exists( 'et_divi_background_repeat_choices' ) ) :
/**
 * Returns background repeat choices
 * @return array
 */
function et_divi_background_repeat_choices() {
	return apply_filters( 'et_divi_background_repeat_choices', array(
		'no-repeat'  => esc_html__( 'No Repeat', 'Divi' ),
		'repeat'     => esc_html__( 'Tile', 'Divi' ),
		'repeat-x'   => esc_html__( 'Tile Horizontally', 'Divi' ),
		'repeat-y'   => esc_html__( 'Tile Vertically', 'Divi' ),
	) );
}
endif;

if ( ! function_exists( 'et_divi_background_attachment_choices' ) ) :
/**
 * Returns background attachment choices
 * @return array
 */
function et_divi_background_attachment_choices() {
	return apply_filters( 'et_divi_background_attachment_choices', array(
		'scroll' => esc_html__( 'Scroll', 'Divi' ),
		'fixed'  => esc_html__( 'Fixed', 'Divi' ),
	) );
}
endif;

<?php

define( 'ET_BUILDER_THEME', true );
function et_setup_builder() {
	define( 'ET_BUILDER_DIR', get_template_directory() . '/includes/builder/' );
	define( 'ET_BUILDER_URI', get_template_directory_uri() . '/includes/builder' );
	define( 'ET_BUILDER_LAYOUT_POST_TYPE', 'et_pb_layout' );

	$theme_version = et_get_theme_version();
	define( 'ET_BUILDER_VERSION', $theme_version );

	load_theme_textdomain( 'et_builder', ET_BUILDER_DIR . 'languages' );
	require ET_BUILDER_DIR . 'framework.php';

	et_pb_register_posttypes();
}
add_action( 'init', 'et_setup_builder', 0 );

/**
 * Added custom data attribute to builder's section
 * @param array  initial custom data-* attributes for builder's section
 * @param array  section attributes
 * @param int    section order of appearances. zero based
 * @return array modified custom data-* attributes for builder's section
 */
function et_divi_section_data_attributes( $attributes, $atts, $num ) {
	$custom_padding        = isset( $atts['custom_padding'] ) ? $atts['custom_padding'] : '';
	$custom_padding_tablet = isset( $atts['custom_padding_tablet'] ) ? $atts['custom_padding_tablet'] : '';
	$custom_padding_phone  = isset( $atts['custom_padding_phone'] ) ? $atts['custom_padding_phone'] : '';
	$is_first_section      = 0 === $num;
	$is_transparent_nav    = et_divi_is_transparent_primary_nav();

	// Custom data-* attributes for transparent primary nav support.
	// Note: in customizer, the data-* attributes have to be printed for live preview purpose
	if ( $is_first_section && ( $is_transparent_nav || is_customize_preview() ) ) {
		if ( '' !== $custom_padding && 4 === count( explode( '|', $custom_padding ) ) ) {
			$attributes['padding'] = $custom_padding;
		}

		if ( '' !== $custom_padding_tablet && 4 === count( explode( '|', $custom_padding_tablet ) ) ) {
			$attributes['padding-tablet'] = $custom_padding_tablet;
		}

		if ( '' !== $custom_padding_phone && 4 === count( explode( '|', $custom_padding_phone ) ) ) {
			$attributes['padding-phone'] = $custom_padding_phone;
		}
	}

	return $attributes;
}
add_filter( 'et_pb_section_data_attributes', 'et_divi_section_data_attributes', 10, 3 );

/**
 * Switch the translation of Visual Builder interface to current user's language
 * @return void
 */
if ( ! function_exists( 'et_fb_set_builder_locale' ) ) :
function et_fb_set_builder_locale() {
	// apply translations inside VB only
	if ( empty( $_GET['et_fb'] ) ) {
		return;
	}

	// make sure switch_to_locale() funciton exists. It was introduced in WP 4.7
	if ( ! function_exists( 'switch_to_locale' ) ) {
		return;
	}

	// do not proceed if user language == website language
	if ( get_user_locale() === get_locale() ) {
		return;
	}

	// switch the translation to user language
	switch_to_locale( get_user_locale() );

	// manually restore the translation for all domains except for the 'et_builder' domain
	// otherwise entire page will be translated to user language, but we need to apply it to VB interface only.

	/* The below code adapted from WordPress

	  wp-includes/class-wp-locale-switcher.php:
	    * load_translations()

	  @copyright 2015 by the WordPress contributors.
	  This program is free software; you can redistribute it and/or modify
	  it under the terms of the GNU General Public License as published by
	  the Free Software Foundation; either version 2 of the License, or
	  (at your option) any later version.

	  This program is distributed in the hope that it will be useful,
	  but WITHOUT ANY WARRANTY; without even the implied warranty of
	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	  GNU General Public License for more details.

	  You should have received a copy of the GNU General Public License
	  along with this program; if not, write to the Free Software
	  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

	  This program incorporates work covered by the following copyright and
	  permission notices:

	  b2 is (c) 2001, 2002 Michel Valdrighi - m@tidakada.com - http://tidakada.com

	  b2 is released under the GPL

	  WordPress - Web publishing software

	  Copyright 2003-2010 by the contributors

	  WordPress is released under the GPL */

	global $l10n;

	$domains = $l10n ? array_keys( $l10n ) : array();

	load_default_textdomain( get_locale() );

	foreach ( $domains as $domain ) {
		if ( 'et_builder' === $domain ) {
			continue;
		}

		unload_textdomain( $domain );
		get_translations_for_domain( $domain );
	}
}
endif;
add_action( 'after_setup_theme', 'et_fb_set_builder_locale' );

/**
 * Added custom post class
 * @param array $classes array of post classes
 * @param array $class   array of additional post classes
 * @param int   $post_id post ID
 * @return array modified array of post classes
 */
function et_pb_post_class( $classes, $class, $post_id ) {
	global $post;

	// Added specific class name if curent post uses comment module. Use global $post->post_content
	// instead of get_the_content() to retrieve the post's unparsed shortcode content
	if ( is_single() && has_shortcode( $post->post_content, 'et_pb_comments' ) ) {
		$classes[] = 'et_pb_no_comments_section';
	}

	return $classes;
}
add_filter( 'post_class', 'et_pb_post_class', 10, 3 );
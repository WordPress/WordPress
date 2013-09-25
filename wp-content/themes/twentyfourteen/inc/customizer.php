<?php
/**
 * Twenty Fourteen Theme Customizer
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function twentyfourteen_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	$wp_customize->add_setting( 'accent_color', array(
		'default'           => '#24890d',
		'sanitize_callback' => 'twentyfourteen_generate_accent_colors',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'accent_color', array(
		'label'    => __( 'Accent Color', 'twentyfourteen' ),
		'section'  => 'colors',
		'settings' => 'accent_color',
	) ) );
}
add_action( 'customize_register', 'twentyfourteen_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function twentyfourteen_customize_preview_js() {
	wp_enqueue_script( 'twentyfourteen_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20120827', true );
}
add_action( 'customize_preview_init', 'twentyfourteen_customize_preview_js' );

/**
 * Generates two variants of the accent color, returns the original, and saves the others as theme mods.
 *
 * @param string $color The original color.
 * @return string $color The original color, sanitized.
 */
function twentyfourteen_generate_accent_colors( $color ) {
	$color = sanitize_hex_color( $color );

	set_theme_mod( 'accent_lighter', twentyfourteen_adjust_color( $color, 14 ) );
	set_theme_mod( 'accent_much_lighter', twentyfourteen_adjust_color( $color, 71 ) );

	return $color;
}

/**
 * Tweaks the brightness of a color by adjusting the RGB values by the given interval.
 *
 * Use positive values of $steps to brighten the color and negative values to darken the color.
 * All three RGB values are modified by the specified steps, within the range of 0-255. The hue
 * is generally maintained unless the number of steps causes one value to be capped at 0 or 255.
 *
 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
 * @param int $steps The number of steps to adjust the color by, in rgb units.
 * @return string $color The new color, in 6-digit hexadecimal form.
 */
function twentyfourteen_adjust_color( $color, $steps ) {
	// Convert shorthand to full hex.
	if ( strlen( $color ) == 3 ) {
		$color = str_repeat( substr( $color, 1, 1 ), 2 ) . str_repeat( substr( $color, 2, 1 ), 2 ) . str_repeat( substr( $color, 3, 1), 2 );
	}

	// Convert hex to rgb.
	$rgb = array( hexdec( substr( $color, 1, 2 ) ), hexdec( substr( $color, 3, 2 ) ), hexdec( substr( $color, 5, 2 ) ) );

	// Adjust color and switch back to hex.
	$hex = '#';
	foreach ( $rgb as $c ) {
		$c += $steps;
		if ( $c > 255 )
			$c = 255;
		elseif ( $c < 0 )
			$c = 0;
		$hex .= str_pad( dechex( $c ), 2, '0', STR_PAD_LEFT);
	}

	return $hex;
}

/**
 * Outputs the css for the Theme Customizer options.
 */
function twentyfourteen_customizer_styles() {
	$accent_color = get_theme_mod( 'accent_color' );

	// Don't do anything if the current color is the default.
	if ( '#24890d' === $accent_color )
		return;

	$accent_lighter = get_theme_mod( 'accent_lighter' );
	$accent_much_lighter = get_theme_mod( 'accent_much_lighter' );

	$css = '<style type="text/css" id="twentyfourteen-accent-color">
		/* Custom accent color. */
		h1 a:hover,
		h2 a:hover,
		h3 a:hover,
		h4 a:hover,
		h5 a:hover,
		h6 a:hover,
		a,
		.entry-title a:hover,
		.cat-links a:hover,
		.site-content .post-navigation a:hover,
		.site-content .image-navigation a:hover,
		.comment-author a:hover,
		.comment-metadata a:hover,
		.comment-list .trackback a:hover,
		.comment-list .pingback a:hover,
		.content-sidebar a:hover,
		.paging-navigation .page-numbers.current {
			color: ' . $accent_color . ';
		}

		button:hover,
		html input[type="button"]:hover,
		input[type="reset"]:hover,
		input[type="submit"]:hover,
		button:focus,
		html input[type="button"]:focus,
		input[type="reset"]:focus,
		input[type="submit"]:focus,
		.hentry .mejs-controls .mejs-time-rail .mejs-time-current,
		.header-extra,
		.search-toggle,
		.widget-area button,
		.widget-area html input[type="button"],
		.widget-area input[type="reset"],
		.widget-area input[type="submit"],
		.widget_calendar a,
		.content-sidebar button:hover,
		.content-sidebar html input[type="button"]:hover,
		.content-sidebar input[type="reset"]:hover,
		.content-sidebar input[type="submit"]:hover,
		.content-sidebar button:focus,
		.content-sidebar html input[type="button"]:focus,
		.content-sidebar input[type="reset"]:focus,
		.content-sidebar input[type="submit"]:focus,
		.page-links a:hover {
			background-color: ' . $accent_color . ';
		}

		::-moz-selection {
			background: ' . $accent_color . ';
		}

		::selection {
			background: ' . $accent_color . ';
		}

		.page-links a:hover,
		.paging-navigation .page-numbers.current {
			border-color: ' .  $accent_color . ';
		}

		/* Generated variant of custom accent color: slightly lighter. */
		.search-toggle:hover,
		.search-toggle.active,
		.search-box,
		.widget-area button:hover,
		.widget-area html input[type="button"]:hover,
		.widget-area input[type="reset"]:hover,
		.widget-area input[type="submit"]:hover,
		.widget-area button:focus,
		.widget-area html input[type="button"]:focus,
		.widget-area input[type="reset"]:focus,
		.widget-area input[type="submit"]:focus,
		.widget-area button:active,
		.widget-area html input[type="button"]:active,
		.widget-area input[type="reset"]:active,
		.widget-area input[type="submit"]:active,
		.widget_calendar a:hover {
			background-color: ' . $accent_lighter . ';
		}

		/* Generated variant of custom accent color: much lighter. */
		button:active,
		html input[type="button"]:active,
		input[type="reset"]:active,
		input[type="submit"]:active,
		.content-sidebar button:active,
		.content-sidebar html input[type="button"]:active,
		.content-sidebar input[type="reset"]:active,
		.content-sidebar input[type="submit"]:active {
			background-color: ' . $accent_much_lighter . ';
		}

		a:hover,
		a:focus,
		a:active,
		.primary-navigation li.current_page_item > a,
		.primary-navigation li.current-menu-item > a,
		.secondary-navigation a:hover,
		#secondary .current_page_item > a,
		#secondary .current-menu-item > a,
		#featured-content .entry-meta a:hover,
		#featured-content .entry-title a:hover,
		#featured-content .more-link,
		.widget-area a:hover {
			color: ' . $accent_much_lighter . ';
		}
		</style>';

	wp_add_inline_style( 'twentyfourteen-style', $css );
}
add_action( 'wp_enqueue_scripts', 'twentyfourteen_customizer_styles' );
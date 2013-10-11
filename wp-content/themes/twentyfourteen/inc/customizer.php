<?php
/**
 * Twenty Fourteen Theme Customizer support
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @since Twenty Fourteen 1.0
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
 * Bind JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since Twenty Fourteen 1.0
 */
function twentyfourteen_customize_preview_js() {
	wp_enqueue_script( 'twentyfourteen_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20120827', true );
}
add_action( 'customize_preview_init', 'twentyfourteen_customize_preview_js' );

/**
 * Generate two variants of the accent color, return the original, and
 * save the others as theme mods.
 *
 * @since Twenty Fourteen 1.0
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
 * Tweak the brightness of a color by adjusting the RGB values by the given interval.
 *
 * Use positive values of $steps to brighten the color and negative values to darken the color.
 * All three RGB values are modified by the specified steps, within the range of 0-255. The hue
 * is generally maintained unless the number of steps causes one value to be capped at 0 or 255.
 *
 * @since Twenty Fourteen 1.0
 *
 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
 * @param int $steps The number of steps to adjust the color by, in RGB units.
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
 * Output the CSS for the Theme Customizer options.
 *
 * @since Twenty Fourteen 1.0
 *
 * @return void
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
		.paging-navigation .page-numbers.current,
		.content-sidebar.widget-area a:hover,
		.content-sidebar .widget_twentyfourteen_ephemera .post-format-archive-link {
			color: ' . $accent_color . ';
		}

		button,
		html input[type="button"],
		input[type="reset"],
		input[type="submit"],
		.hentry .mejs-controls .mejs-time-rail .mejs-time-current,
		.header-extra,
		.search-toggle,
		.primary-navigation ul ul,
		.primary-navigation li:hover > a,
		.page-links a:hover,
		.widget_calendar tbody a {
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
		button:hover,
		html input[type="button"]:hover,
		input[type="reset"]:hover,
		input[type="submit"]:hover,
		button:focus,
		html input[type="button"]:focus,
		input[type="reset"]:focus,
		input[type="submit"]:focus,
		.widget_calendar tbody a:hover {
			background-color: ' . $accent_lighter . ';
		}

		/* Generated variant of custom accent color: much lighter. */
		button:active,
		html input[type="button"]:active,
		input[type="reset"]:active,
		input[type="submit"]:active {
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
		.featured-content a:hover,
		.featured-content .more-link,
		.widget-area a:hover {
			color: ' . $accent_much_lighter . ';
		}
		</style>';

	wp_add_inline_style( 'twentyfourteen-style', $css );
}
add_action( 'wp_enqueue_scripts', 'twentyfourteen_customizer_styles' );

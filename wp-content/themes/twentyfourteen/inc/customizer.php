<?php
/**
 * Twenty Fourteen Theme Customizer support
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

/**
 * Implement Theme Customizer additions and adjustments.
 *
 * @since Twenty Fourteen 1.0
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function twentyfourteen_customize_register( $wp_customize ) {
	// Add postMessage support for site title and description.
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	// Add custom description to Colors and Background sections.
 	$wp_customize->get_section( 'colors' )->description           = __( 'Background may only be visible on wide screens.', 'twentyfourteen' );
 	$wp_customize->get_section( 'background_image' )->description = __( 'Background may only be visible on wide screens.', 'twentyfourteen' );

	// Add the custom accent color setting and control.
	$wp_customize->add_setting( 'accent_color', array(
		'default'           => '#24890d',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'accent_color', array(
		'label'    => __( 'Accent Color', 'twentyfourteen' ),
		'section'  => 'colors',
	) ) );

	add_filter( 'theme_mod_accent_mid',  'twentyfourteen_accent_mid'  );
	add_filter( 'theme_mod_accent_light', 'twentyfourteen_accent_light' );

	// Add the featured content section in case it's not already there.
	$wp_customize->add_section( 'featured_content', array(
		'title'       => __( 'Featured Content', 'twentyfourteen' ),
		'description' => sprintf( __( 'Easily feature all posts with the <a href="%s">"featured" tag</a> or a tag of your choice; if no posts match the tag, <a href="%s">"sticky" posts</a> will be displayed instead.', 'twentyfourteen' ), admin_url( '/edit.php?tag=featured' ), admin_url( '/edit.php?show_sticky=1' ) ),
		'priority'    => 130,
	) );

	// Add the featured content layout setting and control.
	$wp_customize->add_setting( 'featured_content_layout', array(
		'default' => 'grid',
	) );

	$wp_customize->add_control( 'featured_content_layout', array(
		'label'   => __( 'Layout', 'twentyfourteen' ),
		'section' => 'featured_content',
		'type'    => 'select',
		'choices' => array(
			'grid'   => __( 'Grid',   'twentyfourteen' ),
			'slider' => __( 'Slider', 'twentyfourteen' ),
		),
	) );
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

	// Adjust color and switch back to 6-digit hex.
	$hex = '#';
	foreach ( $rgb as $value ) {
		$value += $steps;
		if ( $value > 255 )
			$value = 255;
		elseif ( $value < 0 )
			$value = 0;
		$hex .= str_pad( dechex( $value ), 2, '0', STR_PAD_LEFT);
	}

	return $hex;
}

 /**
 * Returns a slightly lighter color than what is set as the theme's
 * accent color.
 *
 * @since Twenty Fourteen 1.0
 *
 * @return string
 */
function twentyfourteen_accent_mid() {
	return twentyfourteen_adjust_color( get_theme_mod( 'accent_color' ), 29 );
}

/**
 * Returns a lighter color than what is set as the theme's accent color.
 *
 * @since Twenty Fourteen 1.0
 *
 * @return string
 */
function twentyfourteen_accent_light() {
	return twentyfourteen_adjust_color( get_theme_mod( 'accent_color' ), 49 );
}

/**
 * Caches the generated variants of the theme's accent color.
 *
 * @since Twenty Fourteen 1.0
 *
 * @return void
 */
function twentyfourteen_rebuild_accent_colors() {
	set_theme_mod( 'accent_mid',  twentyfourteen_accent_mid()  );
	set_theme_mod( 'accent_light', twentyfourteen_accent_light() );
}
add_action( 'update_option_theme_mods_twentyfourteen', 'twentyfourteen_rebuild_accent_colors' );

/**
 * Output the CSS for the Theme Customizer options.
 *
 * @since Twenty Fourteen 1.0
 *
 * @return void
 */
function twentyfourteen_customizer_styles() {
	$accent_color = get_theme_mod( 'accent_color', '#24890d' );

	// Don't do anything if the current color is the default.
	if ( '#24890d' === $accent_color )
		return;

	$accent_mid = get_theme_mod( 'accent_mid' );
	$accent_light = get_theme_mod( 'accent_light' );

	$css = '/* Custom accent color. */
		a,
		.content-sidebar .widget a {
			color: ' . $accent_color . ';
		}

		button,
		.contributor-posts-link,
		input[type="button"],
		input[type="reset"],
		input[type="submit"],
		.search-toggle,
		.hentry .mejs-controls .mejs-time-rail .mejs-time-current,
		.widget button,
		.widget input[type="button"],
		.widget input[type="reset"],
		.widget input[type="submit"],
		.widget_calendar tbody a,
		.content-sidebar .widget input[type="button"],
		.content-sidebar .widget input[type="reset"],
		.content-sidebar .widget input[type="submit"],
		.slider-control-paging .slider-active:before,
		.slider-control-paging .slider-active:hover:before,
		.slider-direction-nav a:hover {
			background-color: ' . $accent_color . ';
		}

		::-moz-selection {
			background: ' . $accent_color . ';
		}

		::selection {
			background: ' . $accent_color . ';
		}

		.paging-navigation .page-numbers.current {
			border-color: ' .  $accent_color . ';
		}

		@media screen and (min-width: 782px) {
			.primary-navigation li:hover > a,
			.primary-navigation li.focus > a,
			.primary-navigation ul ul {
				background-color: ' . $accent_color . ';
			}
		}

		@media screen and (min-width: 1008px) {
			.secondary-navigation li:hover > a,
			.secondary-navigation li.focus > a,
			.secondary-navigation ul ul {
				background-color: ' . $accent_color . ';
			}
		}

		/* Generated "mid" variant of custom accent color. */
		button:hover,
		button:focus,
		.contributor-posts-link:hover,
		input[type="button"]:hover,
		input[type="button"]:focus,
		input[type="reset"]:hover,
		input[type="reset"]:focus,
		input[type="submit"]:hover,
		input[type="submit"]:focus,
		.search-toggle:hover,
		.search-toggle.active,
		.search-box,
		.entry-meta .tag-links a:hover,
		.widget input[type="button"]:hover,
		.widget input[type="button"]:focus,
		.widget input[type="reset"]:hover,
		.widget input[type="reset"]:focus,
		.widget input[type="submit"]:hover,
		.widget input[type="submit"]:focus,
		.widget_calendar tbody a:hover,
		.content-sidebar .widget input[type="button"]:hover,
		.content-sidebar .widget input[type="button"]:focus,
		.content-sidebar .widget input[type="reset"]:hover,
		.content-sidebar .widget input[type="reset"]:focus,
		.content-sidebar .widget input[type="submit"]:hover,
		.content-sidebar .widget input[type="submit"]:focus,
		.slider-control-paging a:hover:before {
			background-color: ' . $accent_mid . ';
		}

		a:active,
		a:hover,
		.site-navigation a:hover,
		.entry-title a:hover,
		.entry-meta a:hover,
		.cat-links a:hover,
		.entry-content .edit-link a:hover,
		.page-links a:hover,
		.post-navigation a:hover,
		.image-navigation a:hover,
		.comment-author a:hover,
		.comment-list .pingback a:hover,
		.comment-list .trackback a:hover,
		.comment-metadata a:hover,
		.comment-reply-title small a:hover,
		.widget a:hover,
		.widget-title a:hover,
		.widget_twentyfourteen_ephemera .entry-meta a:hover,
		.content-sidebar .widget a:hover,
		.content-sidebar .widget .widget-title a:hover,
		.content-sidebar .widget_twentyfourteen_ephemera .entry-meta a:hover,
		.site-info a:hover,
		.featured-content a:hover {
			color: ' . $accent_mid . ';
		}

		.page-links a:hover,
		.paging-navigation a:hover {
			border-color: ' . $accent_mid . ';
		}

		.tag-links a:hover:before {
			border-right-color: ' . $accent_mid . ';
		}

		@media screen and (min-width: 782px) {
			.primary-navigation ul ul a:hover,
			.primary-navigation ul ul li.focus > a {
				background-color: ' . $accent_mid . ';
			}
		}

		@media screen and (min-width: 1008px) {
			.secondary-navigation ul ul a:hover,
			.secondary-navigation ul ul li.focus > a {
				background-color: ' . $accent_mid . ';
			}
		}

		/* Generated "light" variant of custom accent color. */
		button:active,
		.contributor-posts-link:active,
		input[type="button"]:active,
		input[type="reset"]:active,
		input[type="submit"]:active,
		.widget input[type="button"]:active,
		.widget input[type="reset"]:active,
		.widget input[type="submit"]:active,
		.content-sidebar .widget input[type="button"]:active,
		.content-sidebar .widget input[type="reset"]:active,
		.content-sidebar .widget input[type="submit"]:active {
			background-color: ' . $accent_light . ';
		}

		.site-navigation .current_page_item > a,
		.site-navigation .current_page_ancestor > a,
		.site-navigation .current-menu-item > a,
		.site-navigation .current-menu-ancestor > a {
			color: ' . $accent_light . ';
		}';


	wp_add_inline_style( 'twentyfourteen-style', $css );
}
add_action( 'wp_enqueue_scripts', 'twentyfourteen_customizer_styles' );

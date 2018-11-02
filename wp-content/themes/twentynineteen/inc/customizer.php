<?php
/**
 * Twenty Nineteen Theme Customizer
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function twentynineteen_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => 'twentynineteen_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => 'twentynineteen_customize_partial_blogdescription',
			)
		);
	}

	/**
	 * Custom colors.
	 */
	$wp_customize->add_setting(
		'colorscheme',
		array(
			'default'           => 'default',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'twentynineteen_sanitize_color_option',
		)
	);

	$wp_customize->add_control(
		'colorscheme',
		array(
			'type'     => 'radio',
			'label'    => __( 'Color Scheme', 'twentynineteen' ),
			'choices'  => array(
				'default'  => _x( 'Default', 'color scheme', 'twentynineteen' ),
				'custom' => _x( 'Custom', 'color scheme', 'twentynineteen' ),
			),
			'section'  => 'colors',
			'priority' => 5,
		)
	);

	// Add primary color setting and control.
	$wp_customize->add_setting(
		'colorscheme_primary_hue',
		array(
			'default'           => 199,
			'transport'         => 'postMessage',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'colorscheme_primary_hue',
			array(
				'label'       => __( 'Primary Color', 'twentynineteen' ),
				'description' => __( 'Changes the Color of the Featured Image overlay, Buttons, Links etc.', 'twentynineteen' ),
				'section'     => 'colors',
				'mode'        => 'hue',
			)
		)
	);

	$wp_customize->add_setting(
		'image_filter',
		array(
			'default'           => 'active',
			'sanitize_callback' => 'twentynineteen_sanitize_image_filter',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'image_filter',
		array(
			'label'       => __( 'Featured Image Color Filter', 'twentynineteen' ),
			'section'     => 'colors',
			'type'        => 'radio',
			'description' => __( "Twenty Nineteen adds a color filter to featured images using your site's primary color. If you disable this effect, the theme will use a black filter in individual posts to keep text readable when it appears on top of the featured image.", 'twentynineteen' ) . '<br/><span style="font-style: normal; display: block; margin-top: 16px;">' . __( 'On Featured Images, apply', 'twentynineteen' ) . '</span>',
			'choices'     => array(
				'active'   => __( 'A color filter', 'twentynineteen' ),
				'inactive' => __( 'A black filter', 'twentynineteen' ),
			),
		)
	);
}
add_action( 'customize_register', 'twentynineteen_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function twentynineteen_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function twentynineteen_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Bind JS handlers to instantly live-preview changes.
 */
function twentynineteen_customize_preview_js() {
	wp_enqueue_script( 'twentynineteen-customize-preview', get_theme_file_uri( '/js/customize-preview.js' ), array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'twentynineteen_customize_preview_js' );

/**
 * Load dynamic logic for the customizer controls area.
 */
function twentynineteen_panels_js() {
	wp_enqueue_script( 'twentynineteen-customize-controls', get_theme_file_uri( '/js/customize-controls.js' ), array(), '1.0', true );
}
add_action( 'customize_controls_enqueue_scripts', 'twentynineteen_panels_js' );

/**
 * Sanitize image filter choice.
 *
 * @param string $choice Whether image filter is active.
 *
 * @return string
 */
function twentynineteen_sanitize_color_option( $choice ) {
	$valid = array(
		'default',
		'custom',
	);

	if ( in_array( $choice, $valid, true ) ) {
		return $choice;
	}

	return 'default';
}
/**
 * Sanitize image filter choice.
 *
 * @param string $choice Whether image filter is active.
 *
 * @return string
 */
function twentynineteen_sanitize_image_filter( $choice ) {
	$valid = array(
		'active',
		'inactive',
	);

	if ( in_array( $choice, $valid, true ) ) {
		return $choice;
	}

	return 'active';
}

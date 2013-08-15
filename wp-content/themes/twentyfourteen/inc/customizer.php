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
 *
 */
function twentyfourteen_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	$wp_customize->add_section( 'twentyfourteen_theme_options', array(
		'title'         => __( 'Theme Options', 'twentyfourteen' ),
		'priority'      => 35,
	) );

	$wp_customize->add_setting( 'email_link', array(
		'default'       => '',
		'type'          => 'theme_mod',
		'capability'    => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'email_link', array(
		'label'         => __( 'Email Link', 'twentyfourteen' ),
		'section'       => 'twentyfourteen_theme_options',
		'settings'      => 'email_link',
		'type'          => 'text',
		'priority'      => 1,
	) );

	$wp_customize->add_setting( 'twitter_link', array(
		'default'       => '',
		'type'          => 'theme_mod',
		'capability'    => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'twitter_link', array(
		'label'         => __( 'Twitter Link', 'twentyfourteen' ),
		'section'       => 'twentyfourteen_theme_options',
		'settings'      => 'twitter_link',
		'type'          => 'text',
		'priority'      => 2,
	) );

	$wp_customize->add_setting( 'facebook_link', array(
		'default'       => '',
		'type'          => 'theme_mod',
		'capability'    => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'facebook_link', array(
		'label'         => __( 'Facebook Link', 'twentyfourteen' ),
		'section'       => 'twentyfourteen_theme_options',
		'settings'      => 'facebook_link',
		'type'          => 'text',
		'priority'      => 3,
	) );

	$wp_customize->add_setting( 'pinterest_link', array(
		'default'       => '',
		'type'          => 'theme_mod',
		'capability'    => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'pinterest_link', array(
		'label'         => __( 'Pinterest Link', 'twentyfourteen' ),
		'section'       => 'twentyfourteen_theme_options',
		'settings'      => 'pinterest_link',
		'type'          => 'text',
		'priority'      => 4,
	) );

	$wp_customize->add_setting( 'google_plus_link', array(
		'default'       => '',
		'type'          => 'theme_mod',
		'capability'    => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'google_plus_link', array(
		'label'         => __( 'Google+ Link', 'twentyfourteen' ),
		'section'       => 'twentyfourteen_theme_options',
		'settings'      => 'google_plus_link',
		'type'          => 'text',
		'priority'      => 5,
	) );

	$wp_customize->add_setting( 'linkedin_link', array(
		'default'       => '',
		'type'          => 'theme_mod',
		'capability'    => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'linkedin_link', array(
		'label'         => __( 'LinkedIn Link', 'twentyfourteen' ),
		'section'       => 'twentyfourteen_theme_options',
		'settings'      => 'linkedin_link',
		'type'          => 'text',
		'priority'      => 6,
	) );

	$wp_customize->add_setting( 'flickr_link', array(
		'default'       => '',
		'type'          => 'theme_mod',
		'capability'    => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'flickr_link', array(
		'label'         => __( 'Flickr Link', 'twentyfourteen' ),
		'section'       => 'twentyfourteen_theme_options',
		'settings'      => 'flickr_link',
		'type'          => 'text',
		'priority'      => 7,
	) );

	$wp_customize->add_setting( 'github_link', array(
		'default'       => '',
		'type'          => 'theme_mod',
		'capability'    => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'github_link', array(
		'label'         => __( 'Github Link', 'twentyfourteen' ),
		'section'       => 'twentyfourteen_theme_options',
		'settings'      => 'github_link',
		'type'          => 'text',
		'priority'      => 8,
	) );

	$wp_customize->add_setting( 'dribbble_link', array(
		'default'       => '',
		'type'          => 'theme_mod',
		'capability'    => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'dribbble_link', array(
		'label'         => __( 'Dribbble Link', 'twentyfourteen' ),
		'section'       => 'twentyfourteen_theme_options',
		'settings'      => 'dribbble_link',
		'type'          => 'text',
		'priority'      => 9,
	) );

	$wp_customize->add_setting( 'vimeo_link', array(
		'default'       => '',
		'type'          => 'theme_mod',
		'capability'    => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'vimeo_link', array(
		'label'         => __( 'Vimeo Link', 'twentyfourteen' ),
		'section'       => 'twentyfourteen_theme_options',
		'settings'      => 'vimeo_link',
		'type'          => 'text',
		'priority'      => 10,
	) );

	$wp_customize->add_setting( 'youtube_link', array(
		'default'       => '',
		'type'          => 'theme_mod',
		'capability'    => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'youtube_link', array(
		'label'         => __( 'YouTube Link', 'twentyfourteen' ),
		'section'       => 'twentyfourteen_theme_options',
		'settings'      => 'youtube_link',
		'type'          => 'text',
		'priority'      => 11,
	) );
}
add_action( 'customize_register', 'twentyfourteen_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 */
function twentyfourteen_customize_preview_js() {
	wp_enqueue_script( 'twentyfourteen_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20120827', true );
}
add_action( 'customize_preview_init', 'twentyfourteen_customize_preview_js' );

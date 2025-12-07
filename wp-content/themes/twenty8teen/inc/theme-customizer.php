<?php
/**
 * Twenty8teen Theme Customizer
 * @package Twenty8teen
 */

/**
 * Set up section, settings, controls for theme customization.
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function twenty8teen_customize_register( $wp_customize ) {
	// Add a section to the Customizer.
	$wp_customize->add_section( 'twenty8teen_design_section', array(
			'title' => __( 'Theme Design', 'twenty8teen' ),
			'description' => sprintf(
				/* translators: 1 is link to Colors section, 2 is link to Widgets, 3 is link to Header Image, 4 is link to Fonts */
				__( 'Other theme options can be found under <a href="%1$s">Colors</a>, <a href="%2$s">Widgets</a>, <a href="%3$s">Header Image</a>, <a href="%4$s">Fonts</a>. ',
				 'twenty8teen' ), "javascript:wp.customize.section( 'colors' ).focus();",
				 "javascript:wp.customize.panel( 'widgets' ).focus();",
				 "javascript:wp.customize.section( 'header_image' ).focus();",
				 "javascript:wp.customize.section( 'twenty8teen_fonts_section' ).focus();"
				),
			'priority' => 46,
			'capability' => 'edit_theme_options',
		) );

	// Add a section to the Customizer.
	$wp_customize->add_section( 'twenty8teen_fonts_section', array(
			'title' => __( 'Fonts', 'twenty8teen' ),
			'description' => sprintf(
				/* translators: 1 is link to Colors section, 2 is link to Widgets, 3 is link to Header Image, 4 is link to Design */
				__( 'Other theme options can be found under <a href="%1$s">Colors</a>, <a href="%2$s">Widgets</a>, <a href="%3$s">Header Image</a>, <a href="%4$s">Design</a>. ',
				 'twenty8teen' ), "javascript:wp.customize.section( 'colors' ).focus();",
				 "javascript:wp.customize.panel( 'widgets' ).focus();",
				 "javascript:wp.customize.section( 'header_image' ).focus();",
				 "javascript:wp.customize.section( 'twenty8teen_design_section' ).focus();"
				),
			'priority' => 45,
			'capability' => 'edit_theme_options',
		) );

	// Add a section to the Customizer.
	// For future options and child themes. It won't show until a control is added.
	$wp_customize->add_section( 'twenty8teen_advanced_section', array(
			'title' => __( 'Theme Advanced', 'twenty8teen' ),
			'description' => sprintf(
				/* translators: 1 is link to Colors section, 2 is link to Widgets, 3 is link to Header Image, 4 is link to Design */
				__( 'Other theme options can be found under <a href="%1$s">Colors</a>, <a href="%2$s">Widgets</a>, <a href="%3$s">Header Image</a>, <a href="%4$s">Design</a>. ',
				 'twenty8teen' ), "javascript:wp.customize.section( 'colors' ).focus();",
				 "javascript:wp.customize.panel( 'widgets' ).focus();",
				 "javascript:wp.customize.section( 'header_image' ).focus();",
				 "javascript:wp.customize.section( 'twenty8teen_design_section' ).focus();"
				),
			'priority' => 47,
			'capability' => 'edit_theme_options',
		) );

	require_once get_template_directory() . '/inc/class-column-control.php';
	require_once get_template_directory() . '/inc/class-preset-control.php';
	require_once get_template_directory() . '/inc/class-repeat-one-many-control.php';

	$default_colors = twenty8teen_default_colors();
	$default_booleans = twenty8teen_default_booleans();
	$default_sizes = twenty8teen_default_sizes();
	$default_identimages = twenty8teen_default_identimages();
	$default_fonts = twenty8teen_default_fonts();
	$default_area_classes = twenty8teen_default_area_classes();

	// Add settings to the Customizer.
	$wp_customize->add_setting( 'accent_color' , array(
			'default'   => $default_colors['accent_color'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_setting( 'body_textcolor' , array(
			'default'   => $default_colors['body_textcolor'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_setting( 'link_color' , array(
			'default'   => $default_colors['link_color'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_setting( 'excerpt_length' , array(
			'default'   => $default_sizes['excerpt_length'],
			'sanitize_callback' => 'twenty8teen_sanitize_excerpt_length',
	) );
	$wp_customize->add_setting( 'show_full_content', array(
			'default'        => $default_booleans['show_full_content'],
			'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_setting( 'start_in_tableview', array(
			'default'        => $default_booleans['start_in_tableview'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_setting( 'show_header', array(
			'default'        => $default_booleans['show_header'],
			'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_setting( 'show_vignette', array(
			'default'        => $default_booleans['show_vignette'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_setting( 'show_icons', array(
			'default'        => $default_booleans['show_icons'],
			'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_setting( 'show_as_cards', array(
			'default'        => $default_booleans['show_as_cards'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_setting( 'show_sidebar', array(
			'default'        => $default_booleans['show_sidebar'],
			'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_setting( 'switch_sidebar', array(
			'default'        => $default_booleans['switch_sidebar'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_setting( 'google_fonts[body]', array(
			'default'        => $default_fonts['body'],
			'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_setting( 'google_fonts[titles]', array(
			'default'        => $default_fonts['titles'],
			'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_setting( 'font_size_adjust' , array(
			'default'   => $default_sizes['font_size_adjust'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'twenty8teen_sanitize_float',
	) );
	$wp_customize->add_setting( 'show_header_imagebehind', array(
			'default'        => $default_booleans['show_header_imagebehind'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_setting( 'show_header_identimage', array(
			'default'        => $default_identimages['show_header_identimage'],
			'sanitize_callback' => 'twenty8teen_sanitize_select',
	) );
	$wp_customize->add_setting( 'show_entry_header_identimage', array(
			'default'        => $default_identimages['show_entry_header_identimage'],
			'sanitize_callback' => 'twenty8teen_sanitize_select',
	) );
	$wp_customize->add_setting( 'show_featured_identimage', array(
			'default'        => $default_identimages['show_featured_identimage'],
			'sanitize_callback' => 'twenty8teen_sanitize_select',
	) );
	$wp_customize->add_setting( 'featured_size_archives', array(
			'default'        => $default_sizes['featured_size_archives'],
			'sanitize_callback' => 'twenty8teen_sanitize_select',
	) );
	$wp_customize->add_setting( 'featured_size_single', array(
			'default'        => $default_sizes['featured_size_single'],
			'sanitize_callback' => 'twenty8teen_sanitize_select',
	) );
	$wp_customize->add_setting( 'identimage_alpha' , array(
			'default'   => $default_identimages['identimage_alpha'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'twenty8teen_sanitize_float',
	) );
	$wp_customize->add_setting( 'featured_image_classes' , array(
			'default'   => $default_identimages['featured_image_classes'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'twenty8teen_sanitize_column_classes',
	) );
	$wp_customize->add_setting( 'area_classes[header]' , array(
			'default'   => $default_area_classes['header'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'twenty8teen_sanitize_column_classes',
	) );
	$wp_customize->add_setting( 'area_classes[main]' , array(
			'default'   => $default_area_classes['main'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'twenty8teen_sanitize_column_classes',
	) );
	$wp_customize->add_setting( 'area_classes[content]' , array(
			'default'   => $default_area_classes['content'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'twenty8teen_sanitize_column_classes',
	) );
	$wp_customize->add_setting( 'area_classes[comments]' , array(
			'default'   => $default_area_classes['comments'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'twenty8teen_sanitize_column_classes',
	) );
	$wp_customize->add_setting( 'area_classes[sidebar]' , array(
			'default'   => $default_area_classes['sidebar'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'twenty8teen_sanitize_column_classes',
	) );
	$wp_customize->add_setting( 'area_classes[widgets]' , array(
			'default'   => $default_area_classes['widgets'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'twenty8teen_sanitize_column_classes',
	) );
	$wp_customize->add_setting( 'area_classes[footer]' , array(
			'default'   => $default_area_classes['footer'],
			'transport' => 'postMessage',
			'sanitize_callback' => 'twenty8teen_sanitize_column_classes',
	) );
	$wp_customize->add_setting( 'user_classes' , array(
			'default'   => '',
			'transport' => 'postMessage',
			'sanitize_callback' => 'twenty8teen_sanitize_user_classes',
	) );
	$wp_customize->add_setting( 'use_posttype_parts', array(
			'default'        => $default_booleans['use_posttype_parts'],
			'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_setting( 'page_conditional_presets' , array(
			'default'   => array(),
			'sanitize_callback' => 'twenty8teen_sanitize_page_conditional_presets',
	) );

	// Add controls for the above settings.
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize,
		'accent_color', array(
			'label'   => __( 'Accent Color', 'twenty8teen' ),
			'description' => __( 'Used for vignette, current menu item, link swap color, sticky post shadow', 'twenty8teen' ),
			'section' => 'colors',
	) ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize,
		'body_textcolor', array(
			'label'   => __( 'Text Color', 'twenty8teen' ),
			'section' => 'colors',
	) ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize,
		'link_color', array(
			'label'   => __( 'Link Color', 'twenty8teen' ),
			'section' => 'colors',
	) ) );
	$wp_customize->add_control( 'excerpt_length', array(
			'label'    => __( 'Generated excerpt length', 'twenty8teen' ),
			'description' => __( 'Number of words in a generated excerpt', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'type'     => 'number',
			'input_attrs' => array(
				'min'    => 0,
				'max'    => 100,
			),
			'active_callback' => 'twenty8teen_is_archives',
	) );
	$wp_customize->add_control( 'show_full_content', array(
			'label'    => __( 'Display full content on archive pages', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'type'     => 'checkbox',
			'active_callback' => 'twenty8teen_is_archives',
	) );
	$wp_customize->add_control( 'start_in_tableview', array(
			'label'    => __( 'Start archives in table view', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'type'     => 'checkbox',
	) );
	$wp_customize->add_control( 'show_header', array(
			'label'    => __( 'Show the header', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'type'     => 'checkbox',
	) );
	$wp_customize->add_control( 'show_vignette', array(
			'label'    => __( 'Display vignette', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'type'     => 'checkbox',
	) );
	$wp_customize->add_control( 'show_icons', array(
			'label'    => __( 'Display icons on links', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'type'     => 'checkbox',
	) );
	$wp_customize->add_control( 'show_as_cards', array(
			'label'    => __( 'Display posts as cards', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'type'     => 'checkbox',
	) );
	$wp_customize->add_control( 'show_sidebar', array(
			'label'    => __( 'Show the sidebar', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'type'     => 'checkbox',
	) );
	$wp_customize->add_control( 'switch_sidebar', array(
			'label'    => __( 'Switch sidebar to opposite side', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'type'     => 'checkbox',
	) );
	$wp_customize->add_control( 'google_fonts[body]', array(
			'label'    => __( 'Google font for content', 'twenty8teen' ),
			'description' => __( 'See <a href="https://fonts.google.com/" target="_blank">Google Fonts</a> for more.', 'twenty8teen' ) .
				twenty8teen_suggested_fonts_description( 'body' ),
			'section'  => 'twenty8teen_fonts_section',
			'type'     => 'text',
			'input_attrs' => array(
				'placeholder' => __( 'Enter any Google Font name', 'twenty8teen' ),
				'list'  => 'twenty8teen-suggested-fonts-list-body',
			),
	) );
	$wp_customize->add_control( 'google_fonts[titles]', array(
			'label'    => __( 'Google font for titles', 'twenty8teen' ),
			'description' => __( 'See <a href="https://fonts.google.com/" target="_blank">Google Fonts</a> for more.', 'twenty8teen' ) .
				twenty8teen_suggested_fonts_description( 'titles' ),
			'section'  => 'twenty8teen_fonts_section',
			'type'     => 'text',
			'input_attrs' => array(
				'placeholder' => __( 'Enter any Google Font name', 'twenty8teen' ),
				'list'  => 'twenty8teen-suggested-fonts-list-titles',
			),
	) );
	$wp_customize->add_control( 'font_size_adjust', array(
			'label'    => __( 'Title font size adjust', 'twenty8teen' ),
			'section'  => 'twenty8teen_fonts_section',
			'type'     => 'range',
			'input_attrs' => array(
				'min'    => 0.0,
				'max'    => 0.75,
				'step'   => 0.02,
			),
	) );
	$wp_customize->add_control( 'show_header_imagebehind', array(
			'label'    => __( 'Display header image behind text', 'twenty8teen' ),
			'description' => sprintf(
				/* translators: link to Widget section */
				__( 'Remember to add a Header Image <a href="%s">widget</a> where you want to see it. ',
					'twenty8teen' ), "javascript:wp.customize.panel( 'widgets' ).focus();"
				),
			'section'  => 'header_image',
			'type'     => 'checkbox',
	) );
	$wp_customize->add_control( 'show_header_identimage', array(
			'label'    => __( 'Type of gradient for default header image', 'twenty8teen' ),
			'section'  => 'header_image',
			'type'     => 'select',
			'choices'  => twenty8teen_identimage_choices(),
	) );
	$wp_customize->add_control( 'show_entry_header_identimage', array(
			'label'    => __( 'Type of gradient behind entry header', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'type'     => 'select',
			'choices'  => twenty8teen_identimage_choices(),
	) );
	$wp_customize->add_control( 'show_featured_identimage', array(
			'label'    => __( 'Type of gradient for default featured image', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'type'     => 'select',
			'choices'  => twenty8teen_identimage_choices(),
	) );
	$wp_customize->add_control( 'featured_size_archives', array(
			'label'    => __( 'Featured image size on archive pages', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'type'     => 'select',
			'choices'  => twenty8teen_image_size_choices(),
			'active_callback' => 'twenty8teen_is_archives',
	) );
	$wp_customize->add_control( 'featured_size_single', array(
			'label'    => __( 'Featured image size on single pages', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'type'     => 'select',
			'choices'  => twenty8teen_image_size_choices(),
			'active_callback' => 'twenty8teen_is_archives',
	) );
	$wp_customize->add_control( 'identimage_alpha', array(
			/* translators: identimage is a made up word, similar to identicon (prominent in the code) */
			'label'    => __( 'Brightness of identimage overlay', 'twenty8teen' ),
			'description' => __( 'Amount of opacity for overlay on gradient background', 'twenty8teen' ),
			'section'  => 'colors',
			'type'     => 'range',
			'input_attrs' => array(
				'min'    => 0.0,
				'max'    => 1.0,
				'step'   => 0.05,
			),
	) );
	$wp_customize->add_control( new Twenty8teen_Customize_Column_Control( $wp_customize,
		'featured_image_classes', array(
			'grid_label'  => __( 'Featured Image Styles', 'twenty8teen' ),
			'label'    => __( 'Styles for Featured Images', 'twenty8teen' ),
			'description' => __( 'Choose the styles for featured images', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'choices'  => twenty8teen_featured_image_class_choices(),
			'column'   => __( 'Images', 'twenty8teen' ),
	) ) );

	$wp_customize->add_control( new Twenty8teen_Customize_Column_Control( $wp_customize,
		'area_classes[header]', array(
			'grid_label'  => __( 'Area Styles', 'twenty8teen' ),
			'label'    => __( 'Styles for Header', 'twenty8teen' ),
			'description' => __( 'Choose the styles for each area', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'choices'  => twenty8teen_area_class_choices(),
			'column'   => __( 'Header', 'twenty8teen' ),
	) ) );
	$wp_customize->add_control( new Twenty8teen_Customize_Column_Control( $wp_customize,
		'area_classes[main]', array(
			'label'    => __( 'Styles for Main', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'choices'  => twenty8teen_area_class_choices(),
			'column'   => __( 'Main', 'twenty8teen' ),
	) ) );
	$wp_customize->add_control( new Twenty8teen_Customize_Column_Control( $wp_customize,
		'area_classes[content]', array(
			'label'    => __( 'Styles for Content', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'choices'  => twenty8teen_area_class_choices(),
			'column'   => __( 'Content', 'twenty8teen' ),
	) ) );
	$wp_customize->add_control( new Twenty8teen_Customize_Column_Control( $wp_customize,
		'area_classes[comments]', array(
			'label'    => __( 'Styles for Comments', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'choices'  => twenty8teen_area_class_choices(),
			'column'   => __( 'Comments', 'twenty8teen' ),
	) ) );
	$wp_customize->add_control( new Twenty8teen_Customize_Column_Control( $wp_customize,
		'area_classes[sidebar]', array(
			'label'    => __( 'Styles for Sidebar', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'choices'  => twenty8teen_area_class_choices(),
			'column'   => __( 'Sidebar', 'twenty8teen' ),
	) ) );
	$wp_customize->add_control( new Twenty8teen_Customize_Column_Control( $wp_customize,
		'area_classes[widgets]', array(
			'label'    => __( 'Styles for Widgets', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'choices'  => twenty8teen_area_class_choices(),
			'column'   => __( 'Widgets', 'twenty8teen' ),
	) ) );
	$wp_customize->add_control( new Twenty8teen_Customize_Column_Control( $wp_customize,
		'area_classes[footer]', array(
			'label'    => __( 'Styles for Footer', 'twenty8teen' ),
			'section'  => 'twenty8teen_design_section',
			'choices'  => twenty8teen_area_class_choices(),
			'column'   => __( 'Footer', 'twenty8teen' ),
	) ) );
	$wp_customize->add_control( 'user_classes', array(
			'label'    => __( 'User classes for Area Styles', 'twenty8teen' ),
			'description' => sprintf(
				/* translators: 1 is link to Area Styles control, 2 is link to Additional CSS */
				__( 'Add classes to the <a href="%1$s">Area Styles</a> option. The CSS can be in a plugin or in <a href="%2$s">Additional CSS</a>.<br /> Please save and reload the Customizer to see the changes.', 'twenty8teen' ),
				"javascript:wp.customize.control( 'area_classes[header]' ).focus();",
				"javascript:wp.customize.control( 'custom_css' ).focus();"
			),
			'section'  => 'twenty8teen_advanced_section',
			'type'     => 'text',
			'input_attrs' => array(
				'placeholder' => __( 'Enter class names', 'twenty8teen' ),
				),
	) );
	$wp_customize->add_control( 'use_posttype_parts', array(
			'label'    => __( 'Use post type template parts', 'twenty8teen' ),
			'section'  => 'twenty8teen_advanced_section',
			'type'     => 'checkbox',
			// Remove this callback if/when get_template_part pulls from the database.
			'active_callback' => 'is_child_theme',
	) );
	$wp_customize->add_control( new Twenty8teen_Customize_Preset_Control( $wp_customize,
		'option_presets', array(
			'label'    => __( 'Use preset theme options', 'twenty8teen' ),
			'description' => __( 'When applying a preset, theme options in the Customizer will be set to the preset values. When creating a preset, the Customizer values will be saved to the database.', 'twenty8teen' ),
			'section'  => 'twenty8teen_advanced_section',
			'settings' => array(),
			'capability' => 'edit_theme_options',
			'choices'  => twenty8teen_option_presets_choices(),
			'preset_values_callback' => 'twenty8teen_option_preset',
			'settings_choices' => twenty8teen_option_preset( 'option_presets', 'defaults' ),
	) ) );
	$wp_customize->add_control( new Twenty8teen_Customize_Repeat_One_Many_Control( $wp_customize,
		'page_conditional_presets', array(
			'label'    => __( 'Page conditional presets', 'twenty8teen' ),
			'description' => __( 'Select the body class that identifies the page, and the preset to apply when it is present. The static classes are shown, but dynamic ones can also be used, such as a specific category or post type.', 'twenty8teen' ),
			'section'  => 'twenty8teen_advanced_section',
			'choices'  => twenty8teen_page_conditional_presets_choices(),
			'value_choices' => twenty8teen_option_presets_choices(),
	) ) );

	if ( isset( $wp_customize->selective_refresh ) ) {
		// Add postMessage support for site title and description and header textcolor.
		$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
		$wp_customize->get_setting( 'background_color' )->transport = 'postMessage';
		$wp_customize->get_setting( 'header_image' )->transport = 'postMessage';
		$wp_customize->get_setting( 'show_header_identimage' )->transport = 'postMessage';

		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'twenty8teen_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'twenty8teen_customize_partial_blogdescription',
		) );
		$wp_customize->selective_refresh->add_partial( 'header_image', array(
			'selector'        => '.header-image',
			'settings'        => array(
				'header_image',
				'show_header_identimage',
				),
			'container_inclusive' => true,
			'render_callback' => 'twenty8teen_customize_partial_header_image',
		) );
	}

}
add_action( 'customize_register', 'twenty8teen_customize_register' );

/**
 * Add descriptions for existing options.
 */
function twenty8teen_extra_descriptions( $wp_customize ) {
	$widgets = $wp_customize->get_panel( 'widgets' );
	if ( $widgets ) {
		$widgets->description = sprintf(
			/* translators: link to Theme Design section */
			__( 'Each widget area can be arranged as desired. More options: <a href="%s">Theme Design</a>. ',
			 'twenty8teen' ),
			 "javascript:wp.customize.section( 'twenty8teen_design_section' ).focus();"
		);
		$widgets->description_hidden = false;
	}

	$custom_css = $wp_customize->get_control( 'custom_css' );
	if ( $custom_css ) {
		$selectors = array( '#masthead', '#content', '#sidebar', '#footer', '#comments',
			'.widget', '.header-image', '.site-title', '.site-navigation', '.entry',
			'.entry-header', '.entry-content', '.entry-summary', '.entry-footer',
			'.cards', '.image-behind' );
		$classes = array_keys( twenty8teen_area_class_choices() );
		$classes = explode( ' ', '.' . join( ' .', $classes ) );
		$selectors = array_merge( $selectors, $classes );
		$custom_css->description =
			'<p>' . __( 'Here are some IDs and class names to target.', 'twenty8teen' )
			. '</p> <p style="font-size:80%"><code>'
			. join( '</code>, <code>', apply_filters( 'twenty8teen_css_description',
				$selectors ) ) . '</code></p>' ;
	}

	$logo = $wp_customize->get_control( 'custom_logo' );
	if ( $logo ) {
		$logo->description = sprintf(
			/* translators: link to Widgets section */
			__( 'Remember to add a Custom Logo <a href="%s">widget</a> where you want to see it. ',
			 'twenty8teen' ), "javascript:wp.customize.panel( 'widgets' ).focus();"
		);
	}

}
add_action( 'customize_register', 'twenty8teen_extra_descriptions', 12 );


/**
 * Render the site title for the selective refresh partial.
 * @return void
 */
function twenty8teen_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 * @return void
 */
function twenty8teen_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Render the header image for the selective refresh partial.
 * @return void
 */
function twenty8teen_customize_partial_header_image() {
	get_template_part( 'template-parts/header-image' );
}

/**
 * Enqueue Javascript to make Customizer preview reflect changes without refresh.
 */
function twenty8teen_customize_preview_js() {
	wp_enqueue_script( 'twenty8teen-customizer-preview',
		get_template_directory_uri() . '/js/customizer-preview.js',
		array( 'customize-preview' ), '20210426', true );
	// This runs too soon to localize the script, so use an action hook.
	add_action( 'twenty8teen_found_conditional_presets',
		'twenty8teen_conditional_presets_json', 10, 2 );
}
add_action( 'customize_preview_init', 'twenty8teen_customize_preview_js', 11 );

/**
 * Output Javascript variables for controlling preview. Only hooked for
 * Customizer preview and if presets are applied.
 */
function twenty8teen_conditional_presets_json( $presets, $combined ) {
	wp_localize_script( 'twenty8teen-customizer-preview', 'twenty8teenPagePreset',
		array( 'vars' => array_keys( Twenty8teen_Customize_Preset_Control::flatten_array( $combined ) ) ) );
}

/**
 * Supply list of choices for identimage type.
 */
function twenty8teen_identimage_choices() {
	return array(
		'none' => __( 'None', 'twenty8teen' ),
		'linear' => __( 'Linear Gradient', 'twenty8teen' ),
		'radial' => __( 'Radial Gradient', 'twenty8teen' ),
		'conic' => __( 'Conic Gradient', 'twenty8teen' ),
		'repeating-linear' => __( 'Repeating Linear Gradient', 'twenty8teen' ),
		'repeating-radial' => __( 'Repeating Radial Gradient', 'twenty8teen' ),
		'repeating-conic' => __( 'Repeating Conic Gradient', 'twenty8teen' ),
	);
}

/**
 * Supply list of choices for image sizes.
 */
function twenty8teen_image_size_choices() {
	$sizes = get_intermediate_image_sizes();
	$choices = array(
		'none' => __( 'None', 'twenty8teen' ),
	);
	foreach ( $sizes as $size ) {
		$choices[$size] = ucfirst( $size );
	}
	$core = array(
		'thumbnail' => __( 'Thumbnail', 'twenty8teen' ),
		'medium'    => __( 'Medium', 'twenty8teen' ),
		'medium_large' => __( 'Medium Large', 'twenty8teen' ),
		'large'     => __( 'Large', 'twenty8teen' ),
	);
	return apply_filters( 'image_size_names_choose', array_merge( $choices, $core ) );
}

/**
 * Sanitize a choice from a select input.
 */
function twenty8teen_sanitize_select( $input, $setting ) {
	$control = $setting->manager->get_control( $setting->id );
	$valid = $control->choices;
	return array_key_exists( $input, $valid ) ? $input : $setting->default;
}

/**
 * Supply list of choices for featured image classes.
 */
function twenty8teen_featured_image_class_choices() {
	return apply_filters( 'twenty8teen_featured_image_class_choices', array(
		/* translators: Style adjectives */
		'border-outset' => __('Outset border', 'twenty8teen'),
		'round' => __( 'Rounded', 'twenty8teen' ),
		'shadow' => __( 'Shadow', 'twenty8teen' ),
		'sepia' => __( 'Sepia', 'twenty8teen' ),
		'active-shadow' => __( 'Active shadow', 'twenty8teen' ),
		'skew' => __( 'Skewed hover', 'twenty8teen' ),
		'scale-up' => __( 'Scale up hover', 'twenty8teen' ),
	) );
}

/**
 * Supply list of choices for area classes.
 */
function twenty8teen_area_class_choices() {
	return apply_filters( 'twenty8teen_area_class_choices', array(
		/* translators: Style adjectives; keep them short to fit in Customizer */
		'font-smaller' => __( 'Font smaller', 'twenty8teen' ),
		'font-larger' => __( 'Font larger', 'twenty8teen' ),
		'capitalize' => __( 'Capitalize', 'twenty8teen' ),
		'lowercase' => __( 'Lowercase', 'twenty8teen' ),
		'uppercase' => __( 'Uppercase', 'twenty8teen' ),
		'small-caps' => __( 'Small caps', 'twenty8teen' ),
		'letter-spacing-1' => __( 'Letter Spacing', 'twenty8teen' ),
		'side-padding' => __( 'Side padding', 'twenty8teen' ),
		'border-top' => __( 'Top border', 'twenty8teen' ),
		'border-right' => __( 'Right border', 'twenty8teen' ),
		'border-bottom' => __( 'Bottom border', 'twenty8teen' ),
		'border-left' => __( 'Left border', 'twenty8teen' ),
		'border-accent' => __( 'Accent border', 'twenty8teen' ),
		'box' => __( 'Boxed', 'twenty8teen' ),
		'slab' => __( 'Slab', 'twenty8teen' ),
		'round' => __( 'Rounded', 'twenty8teen' ),
		'semi-white' => __( 'Semi white', 'twenty8teen' ),
		'semi-black' => __( 'Semi black', 'twenty8teen' ),
		'semi-accent' => __( 'Semi accent', 'twenty8teen' ),
		'noise' => __( 'Noise', 'twenty8teen' ),
		'swap-color' => __( 'Swap colors', 'twenty8teen' ),
		'active-shadow' => __( 'Active shadow', 'twenty8teen' ),
		'glass' => __( 'Glass', 'twenty8teen' ),
		'frostedglass' => __( 'Frosted Glass', 'twenty8teen' ),
		'rays' => __( 'Rays', 'twenty8teen' ),
	) );
}

/**
 * Sanitize a column class input.
 */
function twenty8teen_sanitize_column_classes( $input ) {
	if ( is_string( $input ) ) {
		$delim = strpos( $input, ',' );
		$input = explode( ' ', substr( $input, ( $delim === false ? 0 : $delim +1 ) ) );
	}
	return join( ' ', array_map( 'sanitize_html_class', $input ) );
}

/**
 * Sanitize a user class input.
 */
function twenty8teen_sanitize_user_classes( $input ) {
	$input = str_replace( array( '.', ',', '  ' ), array( '', ' ', ' ' ), $input );
	$input = array_map( 'sanitize_html_class', explode( ' ', trim( $input ) ) );
	$current = array_keys( twenty8teen_add_user_classes( array() ) );
	$widget = array_keys( twenty8teen_widget_class_choices( '' ) );
	$already = array_merge( array(
		'site-header', 'site-main', 'content-area', 'widget-area', 'site-footer',
		'site-navigation', 'has-sidebar', 'header-behind', 'vignette', 'widget',
		'comment', 'header-image', 'image-behind', 'identimage', 'wp-post-image',
		'alignleft', 'aligncenter', 'alignright', 'entry', 'cards', 'table-view',
		// There are a lot of others, but these would be the most damaging to use.
		'body-font', 'titles-font',
		),
		array_diff( $widget, $current ) );
	return join( ' ', array_diff( $input, $already ) );
}

/**
 * Supply list of choices for option presets.
 */
function twenty8teen_option_presets_choices() {
	$saved = array_fill_keys( array_keys( get_theme_mod( 'option_presets', array() ) ), '' );
	return apply_filters( 'twenty8teen_option_presets_choices',
		// Double reverse so merge works, but with db presets shown after prefedined.
		array_reverse( array_merge( array_reverse( $saved, true ), array_reverse( array(
		'defaults' => __( 'Defaults', 'twenty8teen' ),
		'light' => __( 'Light', 'twenty8teen' ),
		'sunny' => __( 'Sunny', 'twenty8teen' ),
		'dark' => __( 'Dark', 'twenty8teen' ),
		'noise_background_image' => __( 'Noise for Background image', 'twenty8teen' ),
		'casual_fonts' => __( 'Casual Fonts', 'twenty8teen' ),
		'crisp_fonts' => __( 'Crisp Fonts', 'twenty8teen' ),
		'wide_round_fonts' => __( 'Wide Round Fonts', 'twenty8teen' ),
		'handwriting_fonts' => __( 'Handwriting Fonts', 'twenty8teen' ),
		'Happy Monkey, Baumans' => __( 'Happy Monkey, Baumans Fonts', 'twenty8teen' ),
		'old newspaper' => __( 'Old Newspaper', 'twenty8teen' ),
		'darkpurple' => __( 'Dark Purple', 'twenty8teen' ),
	), true ) ), true ) );
}

/**
 * Sanitize an option preset choice.
 */
function twenty8teen_sanitize_option_presets_choice( $input ) {
	$valid = twenty8teen_option_presets_choices(); // No setting, unlike others.
	return array_key_exists( $input, $valid ) ? $input : 'none';
}

/**
 * Supply list of choices for page conditions for presets.
 */
function twenty8teen_page_conditional_presets_choices() {
	return apply_filters( 'twenty8teen_page_conditional_presets_choices', array(
		'home'=> __( 'Home', 'twenty8teen' ),
		'blog'=> __( 'Blog', 'twenty8teen' ),
		'archive'=> __( 'Archive', 'twenty8teen' ),
		'category'=> __( 'Category', 'twenty8teen' ),
		'tag'=> __( 'Tag', 'twenty8teen' ),
		'post-type-archive'=> __( 'Post type archive', 'twenty8teen' ),
		'search'=> __( 'Search', 'twenty8teen' ),
		'author'=> __( 'Author', 'twenty8teen' ),
		'date'=> __( 'Date', 'twenty8teen' ),
		'attachment'=> __( 'Attachment', 'twenty8teen' ),
		'single'=> __( 'Single', 'twenty8teen' ),
		'page'=> __( 'Page', 'twenty8teen' ),
		'page-parent'=> __( 'Page parent', 'twenty8teen' ),
		'page-child'=> __( 'Page child', 'twenty8teen' ),
		'error404'=> __( 'Error 404', 'twenty8teen' ),
		'logged-in'=> __( 'Logged in', 'twenty8teen' ),
		'paged'=> __( 'Paged', 'twenty8teen' ),
	) );
}

/**
 * Sanitize the page condition option presets.
 */
function twenty8teen_sanitize_page_conditional_presets( $input ) {
	$clean = array();
	foreach ( $input as $class => $presets ) {
		$class = sanitize_html_class( $class );
		$presets = array_unique( $presets );
		$clean[$class] = $presets;
	}
	return $clean;
}

/**
 * Sanitize a float.
 */
function twenty8teen_sanitize_float( $input ) {
	if ( ! $input ) {
		 return '0';	// '0' vs. 0 or false
	}
	return round( floatval( $input ), 2 ) . '';
}

/**
 * Sanitize excerpt length.
 */
function twenty8teen_sanitize_excerpt_length( $input, $setting ) {
	if ( $input == '' ) {
		return $setting->default;
	}
	return min( absint( $input ), 100 );
}

/**
 * Used to set the Customizer transport method in javascript.
 */
function twenty8teen_is_archives() {
	return is_admin() || ! is_singular();
}

/**
 * Supply a list of suggested fonts (filtered).
 */
function twenty8teen_suggested_fonts() {
	return apply_filters( 'twenty8teen_suggested_fonts', array(
		'body'   => array(
			'Convergence', 'Prompt', 'Nobile', 'Poppins', 'El Messiri', 'Philosopher',
			'Alef', 'Laila', 'Rambla', 'Pangolin', 'Merienda', 'Marmelad'
		),
		'titles' => array(
			'Amarante', 'Nova Round', 'Salsa', 'Fondamento', 'Gabriela', 'Milonga',
			'Elsie', 'Lemon', 'Marko One', 'Limelight', 'Aclonica', 'Poller One'
		),
	 ) );
}

/**
 * Supply the suggested fonts for the Customizer pane, styled in the font.
 */
function twenty8teen_suggested_fonts_description( $which ) {
	$fonts = twenty8teen_suggested_fonts();
	$which = array_key_exists( $which, $fonts ) ? $which : 'body';
	$list = '<datalist id="twenty8teen-suggested-fonts-list-' . esc_attr( $which ) . '">';
	$out = '<ul style="font-style:normal; -moz-columns:2; -webkit-columns:2; columns:2">';
	foreach ( $fonts[$which] as $font ) {
		$list .= '<option value="' . esc_attr( $font ) . '">';
		$out .= '<li style="font-family: \'' . esc_attr( $font ) . '\'">' . esc_html( $font ) . '</li>';
	}
	return $out . '</ul>' . $list . '</datalist>';
}

/**
 * Enqueue styles and scripts for the Customizer pane.
 */
function twenty8teen_customize_pane_enqueue() {
	$fonts = twenty8teen_suggested_fonts();
	$fonts = array_merge( $fonts['body'], $fonts['titles'] );
	wp_enqueue_style( 'twenty8teen-suggested-fonts',
		twenty8teen_fonts_url( $fonts ), array(), null );
	wp_enqueue_script( 'twenty8teen-customizer-control',
		get_template_directory_uri() . '/js/customizer-control.js',
		array( 'customize-controls' ), '20190529', true );
}
add_action( 'customize_controls_enqueue_scripts', 'twenty8teen_customize_pane_enqueue' );

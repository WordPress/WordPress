<?php
/**
 * Twenty Minutes Theme Customizer
 *
 * @package Twenty Minutes
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function twenty_minutes_customize_register( $wp_customize ) {

	function twenty_minutes_sanitize_phone_number( $phone ) {
		return preg_replace( '/[^\d+]/', '', $phone );
	}

	function twenty_minutes_sanitize_checkbox( $checked ) {
		// Boolean check.
		return ( ( isset( $checked ) && true == $checked ) ? true : false );
	}

	wp_enqueue_style('twenty-minutes-customize-controls', trailingslashit(esc_url(get_template_directory_uri())).'/css/customize-controls.css');

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';

	//Logo
    $wp_customize->add_setting('twenty_minutes_logo_width',array(
		'default'=> '',
		'transport' => 'refresh',
		'sanitize_callback' => 'twenty_minutes_sanitize_integer'
	));
	$wp_customize->add_control(new Twenty_Minutes_Slider_Custom_Control( $wp_customize, 'twenty_minutes_logo_width',array(
		'label'	=> esc_html__('Logo Width','twenty-minutes'),
		'section'=> 'title_tagline',
		'settings'=>'twenty_minutes_logo_width',
		'input_attrs' => array(
            'step'             => 1,
			'min'              => 0,
			'max'              => 100,
        ),
	)));

	$wp_customize->add_setting('twenty_minutes_title_enable',array(
		'default' => false,
		'sanitize_callback' => 'twenty_minutes_sanitize_checkbox',
	));
	$wp_customize->add_control( 'twenty_minutes_title_enable', array(
	   'settings' => 'twenty_minutes_title_enable',
	   'section'   => 'title_tagline',
	   'label'     => __('Enable Site Title','twenty-minutes'),
	   'type'      => 'checkbox'
	));

	// twenty minutes Site Title color
	$wp_customize->add_setting('twenty_minutes_sitetitle',array(
		'default' => '',
		'sanitize_callback' => 'esc_html',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_sitetitle', array(
	   'settings' => 'twenty_minutes_sitetitle',
	   'section'   => 'title_tagline',
	   'label' => __('Site Title Color', 'twenty-minutes'),
	   'type'      => 'color'
	));

	$wp_customize->add_setting('twenty_minutes_tagline_enable',array(
		'default' => false,
		'sanitize_callback' => 'twenty_minutes_sanitize_checkbox',
	));
	$wp_customize->add_control( 'twenty_minutes_tagline_enable', array(
	   'settings' => 'twenty_minutes_tagline_enable',
	   'section'   => 'title_tagline',
	   'label'     => __('Enable Site Tagline','twenty-minutes'),
	   'type'      => 'checkbox'
	));

	// twenty minutes Site tagline color
	$wp_customize->add_setting('twenty_minutes_sitetagline',array(
		'default' => '',
		'sanitize_callback' => 'esc_html',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_sitetagline', array(
	   'settings' => 'twenty_minutes_sitetagline',
	   'section'   => 'title_tagline',
	   'label' => __('Site Tagline Color', 'twenty-minutes'),
	   'type'      => 'color'
	));

	// woocommerce section
	$wp_customize->add_section('twenty_minutes_woocommerce_page_settings', array(
		'title'    => __('WooCommerce Page Settings', 'twenty-minutes'),
		'priority' => null,
		'panel'    => 'woocommerce',
	));

	$wp_customize->add_setting('twenty_minutes_shop_page_sidebar',array(
		'default' => false,
		'sanitize_callback'	=> 'twenty_minutes_sanitize_checkbox'
	));
	$wp_customize->add_control('twenty_minutes_shop_page_sidebar',array(
		'type' => 'checkbox',
		'label' => __(' Check To Enable Shop page sidebar','twenty-minutes'),
		'section' => 'twenty_minutes_woocommerce_page_settings',
	));

    // shop page sidebar alignment
    $wp_customize->add_setting('twenty_minutes_shop_page_sidebar_position', array(
		'default'           => 'Right Sidebar',
		'sanitize_callback' => 'twenty_minutes_sanitize_choices',
	));
	$wp_customize->add_control('twenty_minutes_shop_page_sidebar_position',array(
		'type'           => 'radio',
		'label'          => __('Shop Page Sidebar', 'twenty-minutes'),
		'section'        => 'twenty_minutes_woocommerce_page_settings',
		'choices'        => array(
			'Left Sidebar'  => __('Left Sidebar', 'twenty-minutes'),
			'Right Sidebar' => __('Right Sidebar', 'twenty-minutes'),
		),
	));	 

	$wp_customize->add_setting('twenty_minutes_wooproducts_nav',array(
		'default' => 'Yes',
		'sanitize_callback'	=> 'twenty_minutes_sanitize_choices'
	));
	$wp_customize->add_control('twenty_minutes_wooproducts_nav',array(
		'type' => 'select',
		'label' => __('Shop Page Products Navigation','twenty-minutes'),
		'choices' => array(
			 'Yes' => __('Yes','twenty-minutes'),
			 'No' => __('No','twenty-minutes'),
		 ),
		'section' => 'twenty_minutes_woocommerce_page_settings',
	));

	 $wp_customize->add_setting( 'twenty_minutes_single_page_sidebar',array(
		'default' => false,
		'sanitize_callback'	=> 'twenty_minutes_sanitize_checkbox'
    ) );
    $wp_customize->add_control('twenty_minutes_single_page_sidebar',array(
    	'type' => 'checkbox',
       	'label' => __('Check To Enable Single Product Page Sidebar','twenty-minutes'),
		'section' => 'twenty_minutes_woocommerce_page_settings'
    ));

	// single product page sidebar alignment
    $wp_customize->add_setting('twenty_minutes_single_product_page_layout', array(
		'default'           => 'Right Sidebar',
		'sanitize_callback' => 'twenty_minutes_sanitize_choices',
	));
	$wp_customize->add_control('twenty_minutes_single_product_page_layout',array(
		'type'           => 'radio',
		'label'          => __('Single product Page Sidebar', 'twenty-minutes'),
		'section'        => 'twenty_minutes_woocommerce_page_settings',
		'choices'        => array(
			'Left Sidebar'  => __('Left Sidebar', 'twenty-minutes'),
			'Right Sidebar' => __('Right Sidebar', 'twenty-minutes'),
		),
	));

	$wp_customize->add_setting('twenty_minutes_related_product_enable',array(
		'default' => true,
		'sanitize_callback'	=> 'twenty_minutes_sanitize_checkbox'
	));
	$wp_customize->add_control('twenty_minutes_related_product_enable',array(
		'type' => 'checkbox',
		'label' => __('Check To Enable Related product','twenty-minutes'),
		'section' => 'twenty_minutes_woocommerce_page_settings',
	));	

	$wp_customize->add_setting( 'twenty_minutes_woo_product_img_border_radius', array(
        'default'              => '0',
        'transport'            => 'refresh',
        'sanitize_callback'    => 'twenty_minutes_sanitize_integer'
    ) );
    $wp_customize->add_control(new Twenty_Minutes_Slider_Custom_Control( $wp_customize, 'twenty_minutes_woo_product_img_border_radius',array(
		'label'	=> esc_html__('Product Img Border Radius','twenty-minutes'),
		'section'=> 'twenty_minutes_woocommerce_page_settings',
		'settings'=>'twenty_minutes_woo_product_img_border_radius',
		'input_attrs' => array(
            'step'             => 1,
			'min'              => 0,
			'max'              => 100,
        ),
	)));
    // Add a setting for number of products per row
    $wp_customize->add_setting('twenty_minutes_products_per_row', array(
	  'default'   => '4',
	  'transport' => 'refresh',
	  'sanitize_callback' => 'twenty_minutes_sanitize_integer'
    ));

    $wp_customize->add_control('twenty_minutes_products_per_row', array(
	  'label'    => __('Products Per Row', 'twenty-minutes'),
	  'section'  => 'twenty_minutes_woocommerce_page_settings',
	  'settings' => 'twenty_minutes_products_per_row',
	  'type'     => 'select',
	  'choices'  => array(
		  '2' => '2',
		  '3' => '3',
		  '4' => '4',
	  ),
    ));

    // Add a setting for the number of products per page
    $wp_customize->add_setting('twenty_minutes_products_per_page', array(
	  'default'   => '9',
	  'transport' => 'refresh',
	  'sanitize_callback' => 'twenty_minutes_sanitize_integer'
    ));
    $wp_customize->add_control('twenty_minutes_products_per_page', array(
	  'label'    => __('Products Per Page', 'twenty-minutes'),
	  'section'  => 'twenty_minutes_woocommerce_page_settings',
	  'settings' => 'twenty_minutes_products_per_page',
	  'type'     => 'number',
	  'input_attrs' => array(
		 'min'  => 1,
		 'step' => 1,
	  ),
    ));

	$wp_customize->add_setting('twenty_minutes_product_sale_position',array(
        'default' => 'Left',
        'sanitize_callback' => 'twenty_minutes_sanitize_choices'
	));
	$wp_customize->add_control('twenty_minutes_product_sale_position',array(
        'type' => 'radio',
        'label' => __('Product Sale Position','twenty-minutes'),
        'section' => 'twenty_minutes_woocommerce_page_settings',
        'choices' => array(
            'Left' => __('Left','twenty-minutes'),
            'Right' => __('Right','twenty-minutes'),
        ),
	) );  

	//Theme Options
	$wp_customize->add_panel( 'twenty_minutes_panel_area', array(
		'priority' => 10,
		'capability' => 'edit_theme_options',
		'title' => __( 'Theme Options Panel', 'twenty-minutes' ),
	) );

	//Site Layout Section
	$wp_customize->add_section('twenty_minutes_site_layoutsec',array(
		'title'	=> __('Manage Site Layout Section','twenty-minutes'),
		'description' => __('<p class="sec-title">Manage Site Layout Section</p>','twenty-minutes'),
		'priority'	=> 1,
		'panel' => 'twenty_minutes_panel_area',
	));

	$wp_customize->add_setting('twenty_minutes_preloader',array(
		'default' => false,
		'sanitize_callback' => 'twenty_minutes_sanitize_checkbox',
	));
	$wp_customize->add_control( 'twenty_minutes_preloader', array(
	   'section'   => 'twenty_minutes_site_layoutsec',
	   'label'	=> __('Check to Show preloader','twenty-minutes'),
	   'type'      => 'checkbox'
 	));

	$wp_customize->add_setting('twenty_minutes_box_layout',array(
		'default' => false,
		'sanitize_callback' => 'twenty_minutes_sanitize_checkbox',
	));
	$wp_customize->add_control( 'twenty_minutes_box_layout', array(
	   'section'   => 'twenty_minutes_site_layoutsec',
	   'label'	=> __('Check to Show Box Layout','twenty-minutes'),
	   'type'      => 'checkbox'
 	));

    // Add Settings and Controls for Page Layout
    $wp_customize->add_setting('twenty_minutes_sidebar_page_layout',array(
	  'default' => 'full',
	  'sanitize_callback' => 'twenty_minutes_sanitize_choices'
	));
	$wp_customize->add_control('twenty_minutes_sidebar_page_layout',array(
		'type' => 'radio',
		'label'     => __('Theme Page Sidebar Position', 'twenty-minutes'),
		'section' => 'twenty_minutes_site_layoutsec',
		'choices' => array(
			'left' => __('Left','twenty-minutes'),
			'right' => __('Right','twenty-minutes'),
			'full' => __('No Sidebar','twenty-minutes')
	),
	) );		

	$wp_customize->add_setting( 'twenty_minutes_layout_settings_upgraded_features',array(
		'sanitize_callback' => 'sanitize_text_field'
	));
	$wp_customize->add_control('twenty_minutes_layout_settings_upgraded_features', array(
		'type'=> 'hidden',
		'description' => "<span class='customizer-upgraded-features'>Unlock Premium Customization Features:
			<a target='_blank' href='". esc_url(TWENTY_MINUTES_PREMIUM_PAGE) ." '>Upgrade to Pro</a></span>",
		'section' => 'twenty_minutes_site_layoutsec'
	));  	

   	//Global Color
   	$wp_customize->add_section('twenty_minutes_global_color', array(
		'title'    => __('Manage Global Color Section', 'twenty-minutes'),
		'panel'    => 'twenty_minutes_panel_area',
	));

	$wp_customize->add_setting('twenty_minutes_first_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'twenty_minutes_first_color', array(
		'label'    => __('Theme Color', 'twenty-minutes'),
		'section'  => 'twenty_minutes_global_color',
		'settings' => 'twenty_minutes_first_color',
	)));	
	
	$wp_customize->add_setting( 'twenty_minutes_global_color_settings_upgraded_features',array(
		'sanitize_callback' => 'sanitize_text_field'
	));
	$wp_customize->add_control('twenty_minutes_global_color_settings_upgraded_features', array(
		'type'=> 'hidden',
		'description' => "<span class='customizer-upgraded-features'>Unlock Premium Customization Features:
			<a target='_blank' href='". esc_url(TWENTY_MINUTES_PREMIUM_PAGE) ." '>Upgrade to Pro</a></span>",
		'section' => 'twenty_minutes_global_color'
	));  		

 	// Header Section
	$wp_customize->add_section('twenty_minutes_header_section', array(
        'title' => __('Manage Header Section', 'twenty-minutes'),
		'description' => __('<p class="sec-title">Manage Header Section</p>','twenty-minutes'),
        'priority' => null,
		'panel' => 'twenty_minutes_panel_area',
 	));

 	$wp_customize->add_setting('twenty_minutes_topbar',array(
		'default' => false,
		'sanitize_callback' => 'twenty_minutes_sanitize_checkbox',
	));
	$wp_customize->add_control( 'twenty_minutes_topbar', array(
	   'section'   => 'twenty_minutes_header_section',
	   'label'	=> __('Check to show topbar','twenty-minutes'),
	   'type'      => 'checkbox'
 	));

 	$wp_customize->add_setting('twenty_minutes_stickyheader',array(
		'default' => false,
		'sanitize_callback' => 'twenty_minutes_sanitize_checkbox',
	));
	$wp_customize->add_control( 'twenty_minutes_stickyheader', array(
	   'section'   => 'twenty_minutes_header_section',
	   'label'	=> __('Check To Show Sticky Header','twenty-minutes'),
	   'type'      => 'checkbox'
 	));

	$wp_customize->add_setting('twenty_minutes_phone_number',array(
		'default' => '',
		'sanitize_callback' => 'twenty_minutes_sanitize_phone_number',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_phone_number', array(
	   'settings' => 'twenty_minutes_phone_number',
	   'section'   => 'twenty_minutes_header_section',
	   'label' => __('Add Phone Number', 'twenty-minutes'),
	   'type'      => 'text'
	));

	$wp_customize->add_setting('twenty_minutes_email_address',array(
		'default' => '',
		'sanitize_callback' => 'sanitize_email',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_email_address', array(
	   'settings' => 'twenty_minutes_email_address',
	   'section'   => 'twenty_minutes_header_section',
	   'label' => __('Add Email Address', 'twenty-minutes'),
	   'type'      => 'text'
	));

 	// twenty minutes header bg color
	$wp_customize->add_setting('twenty_minutes_headerbgcol',array(
		'default' => '',
		'sanitize_callback' => 'esc_html',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_headerbgcol', array(
	   'settings' => 'twenty_minutes_headerbgcol',
	   'section'   => 'twenty_minutes_header_section',
	   'label' => __('BG Color', 'twenty-minutes'),
	   'type'      => 'color'
	));

	// twenty minutes header icon color
	$wp_customize->add_setting('twenty_minutes_headericoncol',array(
		'default' => '',
		'sanitize_callback' => 'esc_html',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_headericoncol', array(
	   'settings' => 'twenty_minutes_headericoncol',
	   'section'   => 'twenty_minutes_header_section',
	   'label' => __('Icon Color', 'twenty-minutes'),
	   'type'      => 'color'
	));

	// twenty minutes header text color
	$wp_customize->add_setting('twenty_minutes_headertextcol',array(
		'default' => '',
		'sanitize_callback' => 'esc_html',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_headertextcol', array(
	   'settings' => 'twenty_minutes_headertextcol',
	   'section'   => 'twenty_minutes_header_section',
	   'label' => __('Text Color', 'twenty-minutes'),
	   'type'      => 'color'
	));

    $wp_customize->add_setting( 'twenty_minutes_header_settings_upgraded_features',array(
		'sanitize_callback' => 'sanitize_text_field'
	));
	$wp_customize->add_control('twenty_minutes_header_settings_upgraded_features', array(
		'type'=> 'hidden',
		'description' => "<span class='customizer-upgraded-features'>Unlock Premium Customization Features:
			<a target='_blank' href='". esc_url(TWENTY_MINUTES_PREMIUM_PAGE) ." '>Upgrade to Pro</a></span>",
		'section' => 'twenty_minutes_header_section'
	));  

	// Social media Section
	$wp_customize->add_section('twenty_minutes_social_media_section', array(
        'title' => __('Manage Social media Section', 'twenty-minutes'),
		'description' => __('<p class="sec-title">Manage Social media Section</p>','twenty-minutes'),
        'priority' => null,
		'panel' => 'twenty_minutes_panel_area',
 	));

	$wp_customize->add_setting('twenty_minutes_fb_link',array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_fb_link', array(
	   'settings' => 'twenty_minutes_fb_link',
	   'section'   => 'twenty_minutes_social_media_section',
	   'label' => __('Facebook Link', 'twenty-minutes'),
	   'type'      => 'url'
	));

	$wp_customize->add_setting('twenty_minutes_twitt_link',array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_twitt_link', array(
	   'settings' => 'twenty_minutes_twitt_link',
	   'section'   => 'twenty_minutes_social_media_section',
	   'label' => __('Twitter Link', 'twenty-minutes'),
	   'type'      => 'url'
	));

	$wp_customize->add_setting('twenty_minutes_linked_link',array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_linked_link', array(
	   'settings' => 'twenty_minutes_linked_link',
	   'section'   => 'twenty_minutes_social_media_section',
	   'label' => __('Linkdin Link', 'twenty-minutes'),
	   'type'      => 'url'
	));

	$wp_customize->add_setting('twenty_minutes_insta_link',array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_insta_link', array(
	   'settings' => 'twenty_minutes_insta_link',
	   'section'   => 'twenty_minutes_social_media_section',
	   'label' => __('Instagram Link', 'twenty-minutes'),
	   'type'      => 'url'
	));

	$wp_customize->add_setting('twenty_minutes_youtube_link',array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_youtube_link', array(
	   'settings' => 'twenty_minutes_youtube_link',
	   'section'   => 'twenty_minutes_social_media_section',
	   'label' => __('Youtube Link', 'twenty-minutes'),
	   'type'      => 'url'
	));

	// twenty minutes social icon color
	$wp_customize->add_setting('twenty_minutes_socialiconcol',array(
		'default' => '',
		'sanitize_callback' => 'esc_html',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_socialiconcol', array(
	   'settings' => 'twenty_minutes_socialiconcol',
	   'section'   => 'twenty_minutes_social_media_section',
	   'label' => __('Icon Color', 'twenty-minutes'),
	   'type'      => 'color'
	));

	// twenty minutes social bg color
	$wp_customize->add_setting('twenty_minutes_socialbgcol',array(
		'default' => '',
		'sanitize_callback' => 'esc_html',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_socialbgcol', array(
	   'settings' => 'twenty_minutes_socialbgcol',
	   'section'   => 'twenty_minutes_social_media_section',
	   'label' => __('BG Color', 'twenty-minutes'),
	   'type'      => 'color'
	));

    $wp_customize->add_setting( 'twenty_minutes_social_settings_upgraded_features',array(
		'sanitize_callback' => 'sanitize_text_field'
	));
	$wp_customize->add_control('twenty_minutes_social_settings_upgraded_features', array(
		'type'=> 'hidden',
		'description' => "<span class='customizer-upgraded-features'>Unlock Premium Customization Features:
			<a target='_blank' href='". esc_url(TWENTY_MINUTES_PREMIUM_PAGE) ." '>Upgrade to Pro</a></span>",
		'section' => 'twenty_minutes_social_media_section'
	));  	

	// Home Category Dropdown Section
	$wp_customize->add_section('twenty_minutes_one_cols_section',array(
		'title'	=> __('Manage Slider Section','twenty-minutes'),
		'description'	=> __('<p class="sec-title">Manage Slider Section</p> Select Category from the Dropdowns for slider, Also use the given image dimension (1200 x 450).','twenty-minutes'),
		'priority'	=> null,
		'panel' => 'twenty_minutes_panel_area'
	));

	//Hide Section
	$wp_customize->add_setting('twenty_minutes_hide_categorysec',array(
		'default' => false,
		'sanitize_callback' => 'twenty_minutes_sanitize_checkbox',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_hide_categorysec', array(
	   'settings' => 'twenty_minutes_hide_categorysec',
	   'section'   => 'twenty_minutes_one_cols_section',
	   'label'     => __('Check To Enable This Section','twenty-minutes'),
	   'type'      => 'checkbox'
	));
	
	// Add a category dropdown Slider Coloumn
	$wp_customize->add_setting( 'twenty_minutes_slidersection', array(
		'default'	=> '0',
		'sanitize_callback'	=> 'absint'
	) );
	$wp_customize->add_control( new Twenty_Minutes_Category_Dropdown_Custom_Control( $wp_customize, 'twenty_minutes_slidersection', array(
		'section' => 'twenty_minutes_one_cols_section',
	   'label' => __('Select Category to display Slider', 'twenty-minutes'),
		'settings'   => 'twenty_minutes_slidersection',
	) ) );

	$wp_customize->add_setting('twenty_minutes_button_text',array(
		'default' => '',
		'sanitize_callback' => 'sanitize_text_field',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_button_text', array(
	   'settings' => 'twenty_minutes_button_text',
	   'section'   => 'twenty_minutes_one_cols_section',
	   'label' => __('Add Button Text', 'twenty-minutes'),
	   'type'      => 'text'
	));

	$wp_customize->add_setting('twenty_minutes_button_link_slider',array(
        'default'=> '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    $wp_customize->add_control('twenty_minutes_button_link_slider',array(
        'label' => esc_html__('Add Button Link','twenty-minutes'),
        'section'=> 'twenty_minutes_one_cols_section',
        'type'=> 'url'
    ));

    $wp_customize->add_setting( 'twenty_minutes_slider_settings_upgraded_features',array(
	  'sanitize_callback' => 'sanitize_text_field'
	));
	$wp_customize->add_control('twenty_minutes_slider_settings_upgraded_features', array(
	    'type'=> 'hidden',
	    'description' => "<span class='customizer-upgraded-features'>Unlock Premium Customization Features:
	        <a target='_blank' href='". esc_url(TWENTY_MINUTES_PREMIUM_PAGE) ." '>Upgrade to Pro</a></span>",
	    'section' => 'twenty_minutes_one_cols_section'
	));

	// Service Section
	$wp_customize->add_section('twenty_minutes_two_cols_section',array(
		'title'	=> __('Manage Service Section','twenty-minutes'),
		'description'	=> __('<p class="sec-title">Manage Service Section</p> Select the post category to show services.','twenty-minutes'),
		'priority'	=> null,
		'panel' => 'twenty_minutes_panel_area'
	));

	//Hide Section
	$wp_customize->add_setting('twenty_minutes_show_serv_sec',array(
		'default' => false,
		'sanitize_callback' => 'twenty_minutes_sanitize_checkbox',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_show_serv_sec', array(
	   'settings' => 'twenty_minutes_show_serv_sec',
	   'section'   => 'twenty_minutes_two_cols_section',
	   'label'     => __('Check To Enable This Section','twenty-minutes'),
	   'type'      => 'checkbox'
	));

	$wp_customize->add_setting('twenty_minutes_section_text',array(
		'default' => '',
		'sanitize_callback' => 'sanitize_text_field',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_section_text', array(
	   'settings' => 'twenty_minutes_section_text',
	   'section'   => 'twenty_minutes_two_cols_section',
	   'label'     => __('Add Section Text','twenty-minutes'),
	   'type'      => 'text'
	));

	$wp_customize->add_setting('twenty_minutes_section_title',array(
		'default' => '',
		'sanitize_callback' => 'sanitize_text_field',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_section_title', array(
	   'settings' => 'twenty_minutes_section_title',
	   'section'   => 'twenty_minutes_two_cols_section',
	   'label'     => __('Add Section Title','twenty-minutes'),
	   'type'      => 'text'
	));

	// Add a category dropdown Slider Coloumn
	$wp_customize->add_setting( 'twenty_minutes_services_section', array(
		'default'	=> '0',
		'sanitize_callback'	=> 'absint'
	) );
	$wp_customize->add_control( new Twenty_Minutes_Category_Dropdown_Custom_Control( $wp_customize, 'twenty_minutes_services_section', array(
		'section' => 'twenty_minutes_two_cols_section',
	   'label' => __('Select Category to display Services', 'twenty-minutes'),
		'settings'   => 'twenty_minutes_services_section',
	) ) );

	$wp_customize->add_setting( 'twenty_minutes_secondsec_settings_upgraded_features',array(
	  'sanitize_callback' => 'sanitize_text_field'
	));
	$wp_customize->add_control('twenty_minutes_secondsec_settings_upgraded_features', array(
	  'type'=> 'hidden',
	  'description' => "<span class='customizer-upgraded-features'>Unlock Premium Customization Features:
	      <a target='_blank' href='". esc_url(TWENTY_MINUTES_PREMIUM_PAGE) ." '>Upgrade to Pro</a></span>",
	  'section' => 'twenty_minutes_two_cols_section'
	));

	//Blog post
	$wp_customize->add_section('twenty_minutes_blog_post_settings',array(
        'title' => __('Manage Post Section', 'twenty-minutes'),
        'priority' => null,
        'panel' => 'twenty_minutes_panel_area'
    ) );

	$wp_customize->add_setting('twenty_minutes_metafields_date', array(
	    'default' => true,
	    'sanitize_callback' => 'twenty_minutes_sanitize_checkbox',
	));
	$wp_customize->add_control('twenty_minutes_metafields_date', array(
	    'settings' => 'twenty_minutes_metafields_date', 
	    'section'   => 'twenty_minutes_blog_post_settings',
	    'label'     => __('Check to Enable Date', 'twenty-minutes'),
	    'type'      => 'checkbox',
	));

	$wp_customize->add_setting('twenty_minutes_metafields_comments', array(
		'default' => true,
		'sanitize_callback' => 'twenty_minutes_sanitize_checkbox',
	));	
	$wp_customize->add_control('twenty_minutes_metafields_comments', array(
		'settings' => 'twenty_minutes_metafields_comments',
		'section'  => 'twenty_minutes_blog_post_settings',
		'label'    => __('Check to Enable Comments', 'twenty-minutes'),
		'type'     => 'checkbox',
	));

	$wp_customize->add_setting('twenty_minutes_metafields_author', array(
		'default' => true,
		'sanitize_callback' => 'twenty_minutes_sanitize_checkbox',
	));
	$wp_customize->add_control('twenty_minutes_metafields_author', array(
		'settings' => 'twenty_minutes_metafields_author',
		'section'  => 'twenty_minutes_blog_post_settings',
		'label'    => __('Check to Enable Author', 'twenty-minutes'),
		'type'     => 'checkbox',
	));		

	$wp_customize->add_setting('twenty_minutes_metafields_time', array(
		'default' => true,
		'sanitize_callback' => 'twenty_minutes_sanitize_checkbox',
	));
	$wp_customize->add_control('twenty_minutes_metafields_time', array(
		'settings' => 'twenty_minutes_metafields_time',
		'section'  => 'twenty_minutes_blog_post_settings',
		'label'    => __('Check to Enable Time', 'twenty-minutes'),
		'type'     => 'checkbox',
	));	

	$wp_customize->add_setting('twenty_minutes_metabox_seperator',array(
		'default' => '|',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('twenty_minutes_metabox_seperator',array(
		'type' => 'text',
		'label' => __('Metabox Seperator','twenty-minutes'),
		'description' => __('Ex: "/", "|", "-", ...','twenty-minutes'),
		'section' => 'twenty_minutes_blog_post_settings'
	)); 

   // Add Settings and Controls for Post Layout
	$wp_customize->add_setting('twenty_minutes_sidebar_post_layout',array(
		'default' => 'right',
		'sanitize_callback' => 'twenty_minutes_sanitize_choices'
	));
	$wp_customize->add_control('twenty_minutes_sidebar_post_layout',array(
		'type' => 'radio',
		'label'     => __('Theme Post Sidebar Position', 'twenty-minutes'),
		'description'   => __('This option work for blog page, archive page and search page.', 'twenty-minutes'),
		'section' => 'twenty_minutes_blog_post_settings',
		'choices' => array(
			'left' => __('Left','twenty-minutes'),
			'right' => __('Right','twenty-minutes'),
			'three-column' => __('Three Columns','twenty-minutes'),
			'four-column' => __('Four Columns','twenty-minutes'),
			'grid' => __('Grid Layout','twenty-minutes'),
			'full' => __('No Sidebar','twenty-minutes')
     ),
	) );

	$wp_customize->add_setting('twenty_minutes_blog_post_description_option',array(
    	'default'   => 'Full Content', 
        'sanitize_callback' => 'twenty_minutes_sanitize_choices'
	));
	$wp_customize->add_control('twenty_minutes_blog_post_description_option',array(
        'type' => 'radio',
        'label' => __('Post Description Length','twenty-minutes'),
        'section' => 'twenty_minutes_blog_post_settings',
        'choices' => array(
            'No Content' => __('No Content','twenty-minutes'),
            'Excerpt Content' => __('Excerpt Content','twenty-minutes'),
            'Full Content' => __('Full Content','twenty-minutes'),
        ),
	) );

	$wp_customize->add_setting( 'twenty_minutes_post_settings_upgraded_features',array(
		'sanitize_callback' => 'sanitize_text_field'
	));
	$wp_customize->add_control('twenty_minutes_post_settings_upgraded_features', array(
		'type'=> 'hidden',
		'description' => "<span class='customizer-upgraded-features'>Unlock Premium Customization Features:
			<a target='_blank' href='". esc_url(TWENTY_MINUTES_PREMIUM_PAGE) ." '>Upgrade to Pro</a></span>",
		'section' => 'twenty_minutes_blog_post_settings'
	));  		

//Single Post Settings
	$wp_customize->add_section('twenty_minutes_single_post_settings',array(
		'title' => __('Manage Single Post Section', 'twenty-minutes'),
		'priority' => null,
		'panel' => 'twenty_minutes_panel_area'
	));

	$wp_customize->add_setting( 'twenty_minutes_single_page_breadcrumb',array(
		'default' => true,
        'sanitize_callback'	=> 'twenty_minutes_sanitize_checkbox',
	));
	$wp_customize->add_control('twenty_minutes_single_page_breadcrumb',array(
       'section' => 'twenty_minutes_single_post_settings',
	   'label' => __( 'Check To Enable Breadcrumb','twenty-minutes' ),
	   'type' => 'checkbox'
    ));	

	$wp_customize->add_setting('twenty_minutes_single_post_date',array(
		'default' => true,
		'sanitize_callback'	=> 'twenty_minutes_sanitize_checkbox'
	));
	$wp_customize->add_control('twenty_minutes_single_post_date',array(
		'type' => 'checkbox',
		'label' => __('Enable / Disable Date ','twenty-minutes'),
		'section' => 'twenty_minutes_single_post_settings'
	));	

	$wp_customize->add_setting('twenty_minutes_single_post_author',array(
		'default' => true,
		'sanitize_callback'	=> 'twenty_minutes_sanitize_checkbox'
	));
	$wp_customize->add_control('twenty_minutes_single_post_author',array(
		'type' => 'checkbox',
		'label' => __('Enable / Disable Author','twenty-minutes'),
		'section' => 'twenty_minutes_single_post_settings'
	));

	$wp_customize->add_setting('twenty_minutes_single_post_comment',array(
		'default' => true,
		'sanitize_callback'	=> 'twenty_minutes_sanitize_checkbox'
	));
	$wp_customize->add_control('twenty_minutes_single_post_comment',array(
		'type' => 'checkbox',
		'label' => __('Enable / Disable Comments','twenty-minutes'),
		'section' => 'twenty_minutes_single_post_settings'
	));	

	$wp_customize->add_setting('twenty_minutes_single_post_time',array(
		'default' => true,
		'sanitize_callback'	=> 'twenty_minutes_sanitize_checkbox'
	));
	$wp_customize->add_control('twenty_minutes_single_post_time',array(
		'type' => 'checkbox',
		'label' => __('Enable / Disable Time','twenty-minutes'),
		'section' => 'twenty_minutes_single_post_settings'
	));	

	$wp_customize->add_setting('twenty_minutes_single_post_metabox_seperator',array(
		'default' => '|',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('twenty_minutes_single_post_metabox_seperator',array(
		'type' => 'text',
		'label' => __('Metabox Seperator','twenty-minutes'),
		'description' => __('Ex: "/", "|", "-", ...','twenty-minutes'),
		'section' => 'twenty_minutes_single_post_settings'
	)); 

	$wp_customize->add_setting('twenty_minutes_sidebar_single_post_layout',array(
    	'default' => 'right',
    	 'sanitize_callback' => 'twenty_minutes_sanitize_choices'
	));
	$wp_customize->add_control('twenty_minutes_sidebar_single_post_layout',array(
   		'type' => 'radio',
    	'label'     => __('Single post sidebar layout', 'twenty-minutes'),
     	'section' => 'twenty_minutes_single_post_settings',
     	'choices' => array(
			'left' => __('Left','twenty-minutes'),
			'right' => __('Right','twenty-minutes'),
			'full' => __('No Sidebar','twenty-minutes')
     ),
	));

	$wp_customize->add_setting( 'twenty_minutes_single_post_settings_upgraded_features',array(
		'sanitize_callback' => 'sanitize_text_field'
	));
	$wp_customize->add_control('twenty_minutes_single_post_settings_upgraded_features', array(
		'type'=> 'hidden',
		'description' => "<span class='customizer-upgraded-features'>Unlock Premium Customization Features:
		   <a target='_blank' href='". esc_url(TWENTY_MINUTES_PREMIUM_PAGE) ." '>Upgrade to Pro</a></span>",
		'section' => 'twenty_minutes_single_post_settings'
	)); 

	//Page Settings
	$wp_customize->add_section('twenty_minutes_page_settings',array(
		'title' => __('Manage Page Section', 'twenty-minutes'),
		'priority' => null,
		'panel' => 'twenty_minutes_panel_area'
	));

	// Add Settings and Controls for Page Layout
	$wp_customize->add_setting('twenty_minutes_sidebar_page_layout',array(
		'default' => 'full',
			'sanitize_callback' => 'twenty_minutes_sanitize_choices'
	));
	$wp_customize->add_control('twenty_minutes_sidebar_page_layout',array(
		'type' => 'radio',
		'label'     => __('Theme Page Sidebar Position', 'twenty-minutes'),
		'section' => 'twenty_minutes_page_settings',
		'choices' => array(
			'left' => __('Left','twenty-minutes'),
			'right' => __('Right','twenty-minutes'),
			'full' => __('No Sidebar','twenty-minutes')
	),
	));	

	$wp_customize->add_setting( 'twenty_minutes_page_settings_upgraded_features',array(
		'sanitize_callback' => 'sanitize_text_field'
	));
	$wp_customize->add_control('twenty_minutes_page_settings_upgraded_features', array(
		'type'=> 'hidden',
		'description' => "<span class='customizer-upgraded-features'>Unlock Premium Customization Features:
		<a target='_blank' href='". esc_url(TWENTY_MINUTES_PREMIUM_PAGE) ." '>Upgrade to Pro</a></span>",
		'section' => 'twenty_minutes_page_settings'
	));

	// 404 Page Settings
	$wp_customize->add_section('twenty_minutes_page_not_found', array(
		'title'	=> __('Manage 404 Page Section','twenty-minutes'),
		'priority'	=> null,
		'panel' => 'twenty_minutes_panel_area',
	));

	$wp_customize->add_setting('twenty_minutes_page_not_found_heading',array(
		'default'=> __('404 Not Found','twenty-minutes'),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('twenty_minutes_page_not_found_heading',array(
		'label'	=> __('404 Heading','twenty-minutes'),
		'section'=> 'twenty_minutes_page_not_found',
		'type'=> 'text'
	));

	$wp_customize->add_setting('twenty_minutes_page_not_found_content',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));

	$wp_customize->add_control('twenty_minutes_page_not_found_content',array(
		'label'	=> __('404 Text','twenty-minutes'),
		'input_attrs' => array(
			'placeholder' => __( 'Looks like you have taken a wrong turn.....Don\'t worry... it happens to the best of us.', 'twenty-minutes' ),
		),
		'section'=> 'twenty_minutes_page_not_found',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'twenty_minutes_page_not_found_settings_upgraded_features',array(
		'sanitize_callback' => 'sanitize_text_field'
	));
	$wp_customize->add_control('twenty_minutes_page_not_found_settings_upgraded_features', array(
		'type'=> 'hidden',
		'description' => "<span class='customizer-upgraded-features'>Unlock Premium Customization Features:
			<a target='_blank' href='". esc_url(TWENTY_MINUTES_PREMIUM_PAGE) ." '>Upgrade to Pro</a></span>",
		'section' => 'twenty_minutes_page_not_found'
	));

	// Footer Section
	$wp_customize->add_section('twenty_minutes_footer', array(
		'title'	=> __('Manage Footer Section','twenty-minutes'),
		'description' => __('<p class="sec-title">Manage Footer Section</p>','twenty-minutes'),
		'priority'	=> null,
		'panel' => 'twenty_minutes_panel_area',
	));

	$wp_customize->add_setting('twenty_minutes_footer_widget', array(
	    'default' => true,
	    'sanitize_callback' => 'twenty_minutes_sanitize_checkbox',
	));
	$wp_customize->add_control('twenty_minutes_footer_widget', array(
	    'settings' => 'twenty_minutes_footer_widget', 
	    'section'   => 'twenty_minutes_footer',
	    'label'     => __('Check to Enable Footer Widget', 'twenty-minutes'),
	    'type'      => 'checkbox',
	));

	$wp_customize->add_setting('twenty_minutes_copyright_line',array(
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control( 'twenty_minutes_copyright_line', array(
	   'section' 	=> 'twenty_minutes_footer',
	   'label'	 	=> __('Copyright Line','twenty-minutes'),
	   'type'    	=> 'text',
	   'priority' 	=> null,
    ));

	$wp_customize->add_setting('twenty_minutes_copyright_link',array(
    	'default' => '',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control( 'twenty_minutes_copyright_link', array(
	   'section' 	=> 'twenty_minutes_footer',
	   'label'	 	=> __('Copyright Link','twenty-minutes'),
	   'type'    	=> 'text',
	   'priority' 	=> null,
    ));

	// footer bg col
	$wp_customize->add_setting('twenty_minutes_footer_bg_col',array(
		'default' => '',
		'sanitize_callback' => 'esc_html',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_footer_bg_col', array(
		'settings' => 'twenty_minutes_footer_bg_col',
		'section'   => 'twenty_minutes_footer',
		'label' => __('BG Color', 'twenty-minutes'),
		'type'      => 'color'
	));

    // footer coypright col
	$wp_customize->add_setting('twenty_minutes_footer_coypright_col',array(
		'default' => '',
		'sanitize_callback' => 'esc_html',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_footer_coypright_col', array(
	   'settings' => 'twenty_minutes_footer_coypright_col',
	   'section'   => 'twenty_minutes_footer',
	   'label' => __('Copyright Color', 'twenty-minutes'),
	   'type'      => 'color'
	));

	// footer coyprighthover col
	$wp_customize->add_setting('twenty_minutes_footer_coyprighthover_col',array(
		'default' => '',
		'sanitize_callback' => 'esc_html',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_footer_coyprighthover_col', array(
	   'settings' => 'twenty_minutes_footer_coyprighthover_col',
	   'section'   => 'twenty_minutes_footer',
	   'label' => __('Copyright Hover Color', 'twenty-minutes'),
	   'type'      => 'color'
	));

	// footer coyprightbg col
	$wp_customize->add_setting('twenty_minutes_footer_coyprightbg_col',array(
		'default' => '',
		'sanitize_callback' => 'esc_html',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_footer_coyprightbg_col', array(
	   'settings' => 'twenty_minutes_footer_coyprightbg_col',
	   'section'   => 'twenty_minutes_footer',
	   'label' => __('Copyright BG Color', 'twenty-minutes'),
	   'type'      => 'color'
	));

	// footer heading col
	$wp_customize->add_setting('twenty_minutes_footer_heading_col',array(
		'default' => '',
		'sanitize_callback' => 'esc_html',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_footer_heading_col', array(
	   'settings' => 'twenty_minutes_footer_heading_col',
	   'section'   => 'twenty_minutes_footer',
	   'label' => __('Heading Color', 'twenty-minutes'),
	   'type'      => 'color'
	));

	// footer text col
	$wp_customize->add_setting('twenty_minutes_footer_text_col',array(
		'default' => '',
		'sanitize_callback' => 'esc_html',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_footer_text_col', array(
	   'settings' => 'twenty_minutes_footer_text_col',
	   'section'   => 'twenty_minutes_footer',
	   'label' => __('Text Color', 'twenty-minutes'),
	   'type'      => 'color'
	));

	// footer list col
	$wp_customize->add_setting('twenty_minutes_footer_list_col',array(
		'default' => '',
		'sanitize_callback' => 'esc_html',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_footer_list_col', array(
	   'settings' => 'twenty_minutes_footer_list_col',
	   'section'   => 'twenty_minutes_footer',
	   'label' => __('List Color', 'twenty-minutes'),
	   'type'      => 'color'
	));

	// footer listhover col
	$wp_customize->add_setting('twenty_minutes_footer_listhover_col',array(
		'default' => '',
		'sanitize_callback' => 'esc_html',
		'capability' => 'edit_theme_options',
	));
	$wp_customize->add_control( 'twenty_minutes_footer_listhover_col', array(
	   'settings' => 'twenty_minutes_footer_listhover_col',
	   'section'   => 'twenty_minutes_footer',
	   'label' => __('List Hover Color', 'twenty-minutes'),
	   'type'      => 'color'
	));

    $wp_customize->add_setting('twenty_minutes_scroll_hide', array(
        'default' => true,
        'sanitize_callback' => 'twenty_minutes_sanitize_checkbox'
    ));
    $wp_customize->add_control( new WP_Customize_Control($wp_customize,'twenty_minutes_scroll_hide',array(
        'label'          => __( 'Check To Show Scroll To Top', 'twenty-minutes' ),
        'section'        => 'twenty_minutes_footer',
        'settings'       => 'twenty_minutes_scroll_hide',
        'type'           => 'checkbox',
    )));

	$wp_customize->add_setting('twenty_minutes_scroll_position',array(
        'default' => 'Right',
        'sanitize_callback' => 'twenty_minutes_sanitize_choices'
    ));
    $wp_customize->add_control('twenty_minutes_scroll_position',array(
        'type' => 'radio',
        'section' => 'twenty_minutes_footer',
        'label'	 	=> __('Scroll To Top Positions','twenty-minutes'),
        'choices' => array(
            'Right' => __('Right','twenty-minutes'),
            'Left' => __('Left','twenty-minutes'),
            'Center' => __('Center','twenty-minutes')
        ),
    ) );

	$wp_customize->add_setting('twenty_minutes_scroll_text',array(
		'default'	=> __('TOP','twenty-minutes'),
		'sanitize_callback'	=> 'sanitize_text_field',
	));	
	$wp_customize->add_control('twenty_minutes_scroll_text',array(
		'label'	=> __('Scroll To Top Button Text','twenty-minutes'),
		'section'	=> 'twenty_minutes_footer',
		'type'		=> 'text'
	));

	$wp_customize->add_setting( 'twenty_minutes_scroll_top_shape', array(
		'default'           => 'circle',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control( 'twenty_minutes_scroll_top_shape', array(
		'label'    => __( 'Scroll to Top Button Shape', 'twenty-minutes' ),
		'section'  => 'twenty_minutes_footer',
		'settings' => 'twenty_minutes_scroll_top_shape',
		'type'     => 'radio',
		'choices'  => array(
			'box'        => __( 'Box', 'twenty-minutes' ),
			'curved' => __( 'Curved', 'twenty-minutes'),
			'circle'     => __( 'Circle', 'twenty-minutes' ),
		),
	));

	$wp_customize->add_setting('twenty_minutes_footer_widget_areas',array(
		'default'           => 4,
		'sanitize_callback' => 'twenty_minutes_sanitize_choices',
	));
	$wp_customize->add_control('twenty_minutes_footer_widget_areas',array(
		'type'        => 'radio',
		'section' => 'twenty_minutes_footer',
		'label'       => __('Footer widget area', 'twenty-minutes'),
		'choices' => array(
		   '1'     => __('One', 'twenty-minutes'),
		   '2'     => __('Two', 'twenty-minutes'),
		   '3'     => __('Three', 'twenty-minutes'),
		   '4'     => __('Four', 'twenty-minutes')
		),
	));

    $wp_customize->add_setting( 'twenty_minutes_footer_settings_upgraded_features',array(
	  'sanitize_callback' => 'sanitize_text_field'
	));
	$wp_customize->add_control('twenty_minutes_footer_settings_upgraded_features', array(
	    'type'=> 'hidden',
	    'description' => "<span class='customizer-upgraded-features'>Unlock Premium Customization Features:
	        <a target='_blank' href='". esc_url(TWENTY_MINUTES_PREMIUM_PAGE) ." '>Upgrade to Pro</a></span>",
	    'section' => 'twenty_minutes_footer'
	));

    // Google Fonts
    $wp_customize->add_section( 'twenty_minutes_google_fonts_section', array(
		'title'       => __( 'Google Fonts', 'twenty-minutes' ),
		'priority'    => 24,
	) );

	$font_choices = array(
		'' => 'No Fonts',
		'Kaushan Script:' => 'Kaushan Script',
		'Emilys Candy:' => 'Emilys Candy',
		'Poppins:0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900' => 'Poppins',
		'Source Sans Pro:400,700,400italic,700italic' => 'Source Sans Pro',
		'Open Sans:400italic,700italic,400,700' => 'Open Sans',
		'Oswald:400,700' => 'Oswald',
		'Playfair Display:400,700,400italic' => 'Playfair Display',
		'Montserrat:400,700' => 'Montserrat',
		'Raleway:400,700' => 'Raleway',
		'Droid Sans:400,700' => 'Droid Sans',
		'Lato:400,700,400italic,700italic' => 'Lato',
		'Arvo:400,700,400italic,700italic' => 'Arvo',
		'Lora:400,700,400italic,700italic' => 'Lora',
		'Merriweather:400,300italic,300,400italic,700,700italic' => 'Merriweather',
		'Oxygen:400,300,700' => 'Oxygen',
		'PT Serif:400,700' => 'PT Serif',
		'PT Sans:400,700,400italic,700italic' => 'PT Sans',
		'PT Sans Narrow:400,700' => 'PT Sans Narrow',
		'Cabin:400,700,400italic' => 'Cabin',
		'Fjalla One:400' => 'Fjalla One',
		'Francois One:400' => 'Francois One',
		'Josefin Sans:400,300,600,700' => 'Josefin Sans',
		'Libre Baskerville:400,400italic,700' => 'Libre Baskerville',
		'Arimo:400,700,400italic,700italic' => 'Arimo',
		'Ubuntu:400,700,400italic,700italic' => 'Ubuntu',
		'Bitter:400,700,400italic' => 'Bitter',
		'Droid Serif:400,700,400italic,700italic' => 'Droid Serif',
		'Roboto:400,400italic,700,700italic' => 'Roboto',
		'Open Sans Condensed:700,300italic,300' => 'Open Sans Condensed',
		'Roboto Condensed:400italic,700italic,400,700' => 'Roboto Condensed',
		'Roboto Slab:400,700' => 'Roboto Slab',
		'Yanone Kaffeesatz:400,700' => 'Yanone Kaffeesatz',
		'Rokkitt:400' => 'Rokkitt',
	);

	$wp_customize->add_setting( 'twenty_minutes_headings_fonts', array(
		'sanitize_callback' => 'twenty_minutes_sanitize_fonts',
	));
	$wp_customize->add_control( 'twenty_minutes_headings_fonts', array(
		'type' => 'select',
		'description' => __('Select your desired font for the headings.', 'twenty-minutes'),
		'section' => 'twenty_minutes_google_fonts_section',
		'choices' => $font_choices
	));

	$wp_customize->add_setting( 'twenty_minutes_body_fonts', array(
		'sanitize_callback' => 'twenty_minutes_sanitize_fonts'
	));
	$wp_customize->add_control( 'twenty_minutes_body_fonts', array(
		'type' => 'select',
		'description' => __( 'Select your desired font for the body.', 'twenty-minutes' ),
		'section' => 'twenty_minutes_google_fonts_section',
		'choices' => $font_choices
	));
}
add_action( 'customize_register', 'twenty_minutes_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function twenty_minutes_customize_preview_js() {
	wp_enqueue_script( 'twenty_minutes_customizer', esc_url(get_template_directory_uri()) . '/js/customize-preview.js', array( 'customize-preview' ), '20161510', true );
}
add_action( 'customize_preview_init', 'twenty_minutes_customize_preview_js' );

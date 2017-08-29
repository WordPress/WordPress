<?php
/**
 * Lighthouse Theme Customizer
 *
 * Please browse readme.txt for credits and forking information
 *
 * @package Lighthouse
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function lighthouse_customize_register( $wp_customize ) {

	//get the current color value for accent color
	$color_scheme = lighthouse_get_color_scheme();
	//get the default color for current color scheme
	$current_color_scheme = lighthouse_current_color_scheme_default_color();

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
 $wp_customize->remove_control('display_header_text');

	//Header Background Color setting
	$wp_customize->add_setting( 'header_bg_color', array(
		'default'           => '#1b1b1b',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
		) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_bg_color', array(
		'label'       => __( 'Header Background Color', 'lighthouse' ),
		'description' => __( 'Applied to header background.', 'lighthouse' ),
		'section'     => 'colors',
		'settings'    => 'header_bg_color',
		) ) );




	$wp_customize->add_section(
		'lighthouse_new',
		array(
			'title' => __('Lighthouse Pro', 'lighthouse'),
			'priority' => 999,
			'description' => __('<a href="https://lighthouseseooptimization.github.io/wordpress/lighthouse/" target="_blank"><img src="https://lighthouseseooptimization.github.io/wordpress/lighthouse/img/lighthosueimg.png"></a><p><strong>Got questions or need help?</strong></p><a href="https://lighthouseseooptimization.github.io/wordpress/lighthouse/#contact" target="_blank">Email us here</a> or write to us directly at: Beseenseo@gmail.com <br><br> Upgrade to Lighthouse Pro to get 30+ more features! ', 'lighthouse') . '<a href="https://lighthouseseooptimization.github.io/wordpress/lighthouse/" target="_blank">Upgrade now!</a> <br> 

			<p><strong>Extra features:</strong></p>
			<ul>
			<li>Improved Meta Tags</li>
			<li>The Best SEO Plugins</li>
			<li>Better SEO</li>
			<li>Custom Header Height</li>
			<li>Full Width Layout</li>
			<li>Only Show Header Image on Front Page</li>
			<li>Only Show Top Widgets On Front Page</li>
			<li>Only Show Bottom Widgets On Front Page</li>
			<li>Remove Transparent Background From Header Menu</li>
			<li>Hide Header Section (Image & Text) Completely</li>
			<li>Show/Hide Post Author Byline</li>
			<li>Custom Header Text</li>
			<li>Scroll To Top Button</li>
			<li>Scroll To Top Button Color</li>
			<li>Custom Footer Section Copyright Text</li>
			<li>Custom Post/Page Link Color</li>
			<li>Custom Navigation Background</li>
			<li>Custom Navigation Link Color</li>
			<li>Custom Navigation Logo Text Color</li>
			<li>Custom Bottom Widgets Background Color</li>
			<li>Custom Bottom Widgets Text Color</li>
			<li>Custom Bottom Widgets Title Color</li>
			<li>Custom Footer Widget Background</li>
			<li>Custom Footer Widget Headline Color</li>
			<li>Custom Footer Copyright Background Color</li>
			<li>Custom Footer Copyright Text Color</li>
			<li>24 Hour Support</li>
			<li>Free Updates Forever</li>
			</ul>
			<a class="button button-primary" href="https://lighthouseseooptimization.github.io/wordpress/lighthouse/" target="_blank">Upgrade now for $32</a>
			',
			)
		);  
	$wp_customize->add_setting('lighthouse_options[info]', array(
		'sanitize_callback' => 'lighthouse_no_sanitize',
		'type' => 'info_control',
		'capability' => 'edit_theme_options',
		)
	);
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'pro_section', array(
		'section' => 'lighthouse_new',
		'settings' => 'lighthouse_options[info]',
		'type' => 'textarea',
		'priority' => 109
		) )
	);   

	$wp_customize->add_section(
		'lighthouse_contact',
		array(
			'title' => __('Help and Support', 'lighthouse'),
			'priority' => 995,
			'description' => __('Have questions or need help? ', 'lighthouse') . '<a href="https://lighthouseseooptimization.github.io/wordpress/lighthouse/#contact" target="_blank">Email us here</a> or write to us directly at: Beseenseo@gmail.com <br><br> <p><strong>Theme documentation</strong><br> Documentation can be found in the admin sidebar under Appearance > Lighthouse</p><br><a href="https://lighthouseseooptimization.github.io/wordpress/lighthouse/" target="_blank"><img src="https://lighthouseseooptimization.github.io/wordpress/lighthouse/img/lighthosueimg.png"></a>',
			)
		);  
	$wp_customize->add_setting('lighthouse_contact[info]', array(
		'sanitize_callback' => 'lighthouse_no_sanitize',
		'type' => 'info_control',
		'capability' => 'edit_theme_options',
		)
	);
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'contact_section', array(
		'section' => 'lighthouse_contact',
		'settings' => 'lighthouse_contact[info]',
		'type' => 'textarea',
		'priority' => 105
		) )
	);   



		$wp_customize->add_setting( 'toggle_header_text', array(
        'default' => 0,
        'priority' => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ) );
   	
	$wp_customize->add_control( 'toggle_header_text', array(
	    'label'    => __( 'Hide header text', 'lighthouse' ),
	    'section'  => 'title_tagline',
	    'settings' => 'toggle_header_text',
	    'priority' => 1,
	    'type'     => 'checkbox',
) );


// Post and page Section
	$wp_customize->add_section(
		'post_page_options',
		array(
			'title'     => __('Post & Page','lighthouse'),
			'priority'  => 1
			)
		);
	$wp_customize->add_setting( 'headline_color', array(
		'default'           => '#212121',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
		) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'headline_color', array(
		'label'       => __( 'Post & Page Headline Color', 'lighthouse' ),
		'description' => __( 'Choose a color for the post & page headline.', 'lighthouse' ),
		'section'     => 'colors',
		'settings'    => 'headline_color',
		) ) );
	$wp_customize->add_setting( 'post_content_color', array(
		'default'           => '#424242',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
		) );

		$wp_customize->add_setting( 'author_line_color', array(
		'default'           => '#989898',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'author_line_color', array(
		'label'       => __( 'Author Byline Color', 'lighthouse' ),
		'description' => __( 'Choose a color for the author byline in the top of posts.', 'lighthouse' ),
		'section'     => 'colors',
		'settings'    => 'author_line_color',
	) ) );


	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'post_content_color', array(
		'label'       => __( 'Post & Page Paragraph Color', 'lighthouse' ),
		'description' => __( 'Choose a color for the post & page paragraphs.', 'lighthouse' ),
		'section'     => 'colors',
		'settings'    => 'post_content_color',
		) ) );


	$wp_customize->add_setting( 'author_line_color', array(
		'default'           => '#989898',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
		) );


// Post and page Section end

	$wp_customize->add_setting( 'top_widget_background_color', array(
		'default'           => '#fff',
		'sanitize_callback' => 'sanitize_hex_color',
		'priority'     => '99',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'top_widget_background_color', array(
		'label'       => __( 'Top Widgets Background Color', 'lighthouse' ),
		'description' => __( 'Choose a background color for the three top widgets.', 'lighthouse' ),
		'section'     => 'colors',
		'priority'     => '99',
		'settings'    => 'top_widget_background_color',
	) ) );

	$wp_customize->add_setting( 'top_widget_title_color', array(
		'default'           => '#212121',
		'priority'     => '99',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'top_widget_title_color', array(
		'label'       => __( 'Top Widgets Title Color', 'lighthouse' ),
		'description' => __( 'Choose a color for the three top widgets titles.', 'lighthouse' ),
		'section'     => 'colors',
		'priority'     => '99',
		'settings'    => 'top_widget_title_color',
	) ) );	


	$wp_customize->add_setting( 'top_widget_text_color', array(
		'default'           => '#424242',
		'priority'     => '99',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'top_widget_text_color', array(
		'label'       => __( 'Top Widgets Text Color', 'lighthouse' ),
		'description' => __( 'Choose a color for the three top widgets text.', 'lighthouse' ),
		'section'     => 'colors',
		'priority'     => '99',
		'settings'    => 'top_widget_text_color',
	) ) );	

	$wp_customize->add_setting( 'header_color_text_colorino', array(
		'default'           => '#fff',
		'priority'     => '1',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_color_text_colorino', array(
		'label'       => __( 'Header Text Colors', 'lighthouse' ),
		'description' => __( 'Choose a color for the header text.', 'lighthouse' ),
		'section'     => 'colors',
		'priority'     => '1',
		'settings'    => 'header_color_text_colorino',
	) ) );	


// Top Widget Section
// Bottom Widget Section

	$wp_customize->add_setting( 'bottom_widget_background_color', array(
		'default'           => '#fff',
		'priority'     => '99',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bottom_widget_background_color', array(
		'label'       => __( 'Bottom Widgets Background Color', 'lighthouse' ),
		'description' => __( 'Choose a background color for the three bottom widgets.', 'lighthouse' ),
		'section'     => 'colors',
		'priority'     => '99',
		'settings'    => 'bottom_widget_background_color',
	) ) );

	$wp_customize->add_setting( 'bottom_widget_title_color', array(
		'default'           => '#212121',
		'priority'     => '99',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_textcolor', array(
		'label'       => __( 'Header Text Color', 'lighthouse' ),
		'description' => __( 'Choose a color for the header text.', 'lighthouse' ),
		'section'     => 'postpage',
		'priority' => 1,
		'settings'    => 'header_textcolor',
	) ) );

	$wp_customize->add_setting( 'header_textcolor', array(
		'default'           => '#fff',
		'priority' => 1,
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bottom_widget_title_color', array(
		'label'       => __( 'Bottom Widgets Title Color', 'lighthouse' ),
		'description' => __( 'Choose a color for the three bottom widgets titles.', 'lighthouse' ),
		'section'     => 'colors',
		'priority'     => '99',
		'settings'    => 'bottom_widget_title_color',
	) ) );	


	$wp_customize->add_setting( 'bottom_widget_text_color', array(
		'default'           => '#424242',
		'priority'     => '99',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bottom_widget_text_color', array(
		'label'       => __( 'Bottom Widgets Text Color', 'lighthouse' ),
		'description' => __( 'Choose a color for the three bottom widgets text.', 'lighthouse' ),
		'section'     => 'colors',
		'priority'     => '99',
		'settings'    => 'bottom_widget_text_color',
	) ) );	

// bottom Widget Section


// Sidebar Section
	$wp_customize->add_section(
		'sidebar_options',
		array(
			'title'     => __('Sidebar','lighthouse'),
			'priority'  => 1
			)
		);

	$wp_customize->add_setting( 'sidebar_headline_colors', array(
		'default'           => '#212121',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_headline_colors', array(
		'label'       => __( 'Sidebar Headline Color', 'lighthouse' ),
		'description' => __( 'Choose the color of the sidebar titles and headlines', 'lighthouse' ),
		'section'     => 'colors',
		'settings'    => 'sidebar_headline_colors',
	) ) );

	$wp_customize->add_setting( 'sidebar_background_color', array(
		'default'           => '#fff',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
		) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_background_color', array(
		'label'       => __( 'Sidebar Background Color', 'lighthouse' ),
		'description' => __( 'Choose the color of the sidebar background', 'lighthouse' ),
		'section'     => 'colors',
		'settings'    => 'sidebar_background_color',
		) ) );


	$wp_customize->add_setting( 'sidebar_headline_colors', array(
		'default'           => '#212121',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
		) );



	$wp_customize->add_setting( 'sidebar_link_color', array(
		'default'           => '#727272',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
		) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_link_color', array(
		'label'       => __( 'Sidebar Link Color', 'lighthouse' ),
		'description' => __( 'Choose the color of the sidebar links', 'lighthouse' ),
		'section'     => 'colors',
		'settings'    => 'sidebar_link_color',
		) ) );

	$wp_customize->add_setting( 'sidebar_link_border_color', array(
		'default'           => '#ddd',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
		) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_link_border_color', array(
		'label'       => __( 'Sidebar Link Border Color', 'lighthouse' ),
		'description' => __( 'Choose the color of the sidebar link borders', 'lighthouse' ),
		'section'     => 'colors',
		'settings'    => 'sidebar_link_border_color',
		) ) );
// Sidebar section end


	$wp_customize->add_section(
		'accent_color_option',
		array(
			'title'     => __('Global Theme Color','lighthouse'),
			'priority'  => 3
			)
		);

	// Add color scheme setting and control.
	$wp_customize->add_setting( 'color_scheme', array(
		'default'           => 'default',
		'sanitize_callback' => 'lighthouse_sanitize_color_scheme',
		'transport'         => 'postMessage',
		) );

	$wp_customize->add_control( 'color_scheme', array(
		'label'    => __( 'Theme Color Name', 'lighthouse' ),
		'section'  => 'colors',
		'type'     => 'select',
		'choices'  => lighthouse_get_color_scheme_choices(),
		'priority' => 3,
		) );

	// Add custom accent color.

	$wp_customize->add_setting( 'accent_color', array(
		'default'           => $current_color_scheme[0],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
		) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'accent_color', array(
		'label'       => __( 'Theme Color', 'lighthouse' ),
		'description' => __( 'Applied to highlight elements.', 'lighthouse' ),
		'section'     => 'colors',
		'priority'  => 1,
		'settings'    => 'accent_color',
		) ) );

	//Add section for post option
	$wp_customize->add_section(
		'post_options',
		array(
			'title'     => __('Post Options','lighthouse'),
			'priority'  => 300
			)
		);

	$wp_customize->add_setting('post_display_option', array(
		'default'        => 'post-excerpt',
		'sanitize_callback' => 'lighthouse_sanitize_post_display_option',
		'transport'         => 'refresh'
		));

	$wp_customize->add_control('post_display_types', array(
		'label'      => __('How would you like to dipaly a post on post listing page?', 'lighthouse'),
		'section'    => 'post_options',
		'settings'   => 'post_display_option',
		'type'       => 'radio',
		'choices'    => array(
			'post-excerpt' => __('Post excerpt','lighthouse'),
			'full-post' => __('Full post','lighthouse'),            
			),
		));
	
}
add_action( 'customize_register', 'lighthouse_customize_register' );

/**
 * Register color schemes for lighthouse.
 *
 * @return array An associative array of color scheme options.
 */
function lighthouse_get_color_schemes() {
	return apply_filters( 'lighthouse_color_schemes', array(
		'default' => array(
			'label'  => __( 'Default', 'lighthouse' ),
			'colors' => array(
				'#fab526',			
				),
			),
		'pink'    => array(
			'label'  => __( 'Pink', 'lighthouse' ),
			'colors' => array(
				'#FF4081',				
				),
			),
		'orange'  => array(
			'label'  => __( 'Orange', 'lighthouse' ),
			'colors' => array(
				'#FF5722',
				),
			),
		'green'    => array(
			'label'  => __( 'Green', 'lighthouse' ),
			'colors' => array(
				'#8BC34A',
				),
			),
		'red'    => array(
			'label'  => __( 'Red', 'lighthouse' ),
			'colors' => array(
				'#FF5252',
				),
			),
		'yellow'    => array(
			'label'  => __( 'yellow', 'lighthouse' ),
			'colors' => array(
				'#FFC107',
				),
			),
		'blue'   => array(
			'label'  => __( 'Blue', 'lighthouse' ),
			'colors' => array(
				'#03A9F4',
				),
			),
		) );
}

if(!function_exists('lighthouse_current_color_scheme_default_color')):
/**
 * Get the default hex color value for current color scheme
 *
 *
 * @return array An associative array of current color scheme hex values.
 */
function lighthouse_current_color_scheme_default_color(){
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );
	
	$color_schemes       = lighthouse_get_color_schemes();

	if ( array_key_exists( $color_scheme_option, $color_schemes ) ) {
		return $color_schemes[ $color_scheme_option ]['colors'];
	}

	return $color_schemes['default']['colors'];
}
endif; //lighthouse_current_color_scheme_default_color

if ( ! function_exists( 'lighthouse_get_color_scheme' ) ) :
/**
 * Get the current lighthouse color scheme.
 *
 *
 * @return array An associative array of currently set color hex values.
 */
function lighthouse_get_color_scheme() {
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );
	$accent_color = get_theme_mod('accent_color','#fab526');
	$color_schemes       = lighthouse_get_color_schemes();

	if ( array_key_exists( $color_scheme_option, $color_schemes ) ) {
		$color_schemes[ $color_scheme_option ]['colors'] = array($accent_color);
		return $color_schemes[ $color_scheme_option ]['colors'];
	}

	return $color_schemes['default']['colors'];
}
endif; // lighthouse_get_color_scheme

if ( ! function_exists( 'lighthouse_get_color_scheme_choices' ) ) :
/**
 * Returns an array of color scheme choices registered for lighthouse.
 *
 *
 * @return array Array of color schemes.
 */
function lighthouse_get_color_scheme_choices() {
	$color_schemes                = lighthouse_get_color_schemes();
	$color_scheme_control_options = array();

	foreach ( $color_schemes as $color_scheme => $value ) {
		$color_scheme_control_options[ $color_scheme ] = $value['label'];
	}

	return $color_scheme_control_options;
}
endif; // lighthouse_get_color_scheme_choices

if ( ! function_exists( 'lighthouse_sanitize_color_scheme' ) ) :
/**
 * Sanitization callback for color schemes.
 *
 *
 * @param string $value Color scheme name value.
 * @return string Color scheme name.
 */
function lighthouse_sanitize_color_scheme( $value ) {
	$color_schemes = lighthouse_get_color_scheme_choices();

	if ( ! array_key_exists( $value, $color_schemes ) ) {
		$value = 'default';
	}

	return $value;
}
endif; // lighthouse_sanitize_color_scheme

if ( ! function_exists( 'lighthouse_sanitize_post_display_option' ) ) :
/**
 * Sanitization callback for post display option.
 *
 *
 * @param string $value post display style.
 * @return string post display style.
 */

function lighthouse_sanitize_post_display_option( $value ) {
	if ( ! in_array( $value, array( 'post-excerpt', 'full-post' ) ) )
		$value = 'post-excerpt';

	return $value;
}
endif; // lighthouse_sanitize_post_display_option
/**
 * Enqueues front-end CSS for color scheme.
 *
 *
 * @see wp_add_inline_style()
 */
function lighthouse_color_scheme_css() {
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );
	
	$color_scheme = lighthouse_get_color_scheme();

	$color = array(
		'accent_color'            => $color_scheme[0],
		);

	$color_scheme_css = lighthouse_get_color_scheme_css( $color);

	wp_add_inline_style( 'lighthouse-style', $color_scheme_css );
}
add_action( 'wp_enqueue_scripts', 'lighthouse_color_scheme_css' );

/**
 * Returns CSS for the color schemes.
 *
 * @param array $colors Color scheme colors.
 * @return string Color scheme CSS.
 */
function lighthouse_get_color_scheme_css( $colors ) {
	$colors = wp_parse_args( $colors, array(
		'accent_color'            => '',
		) );

	$css = <<<CSS
	/* Color Scheme */

	/* Accent Color */

	a:active,
	a:hover,
	a:focus {
		color: {$colors['accent_color']};
	}

	.navbar-default .navbar-nav > li > a:hover, .navbar-default .navbar-nav > li > a:focus {
		color: {$colors['accent_color']};
	}

	.navbar-default .navbar-toggle:hover, .navbar-default .navbar-toggle:focus {
		background-color: {$colors['accent_color']};
		background: {$colors['accent_color']};
		border-color:{$colors['accent_color']};
	}

	.navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > .active > a:hover, .navbar-default .navbar-nav > .active > a:focus {
		color: {$colors['accent_color']} !important;			
	}

	.dropdown-menu > .active > a, .dropdown-menu > .active > a:hover, .dropdown-menu > .active > a:focus {	    
		background-color: {$colors['accent_color']};
	}
	.btn, .btn-default:visited, .btn-default:active:hover, .btn-default.active:hover, .btn-default:active:focus, .btn-default.active:focus, .btn-default:active.focus, .btn-default.active.focus {
		background: {$colors['accent_color']};
	}

	.navbar-default .navbar-nav > .open > a, .navbar-default .navbar-nav > .open > a:hover, .navbar-default .navbar-nav > .open > a:focus {
		color: {$colors['accent_color']};
	}
	.cat-links a, .tags-links a {
		color: {$colors['accent_color']};
	}
	.navbar-default .navbar-nav > li > .dropdown-menu > li > a:hover,
	.navbar-default .navbar-nav > li > .dropdown-menu > li > a:focus {
		color: #fff;
		background-color: {$colors['accent_color']};
	}
	h5.entry-date a:hover {
		color: {$colors['accent_color']};
	}

	 #respond input#submit {
	background-color: {$colors['accent_color']};
	background: {$colors['accent_color']};
}
.navbar-default .navbar-nav .open .dropdown-menu > .active > a, .navbar-default .navbar-nav .open .dropdown-menu > .active > a:hover, .navbar-default .navbar-nav .open .dropdown-menu > .active > a:focus {
	background-color: #fff;

}
.top-widgets h3:after {
	display: block;
	max-width: 60px;
	background:  {$colors['accent_color']};
	height: 3px;
	content: ' ';
	margin: 0 auto;
	margin-top: 10px;
}
.bottom-widgets h3:after {
	display: block;
	max-width: 60px;
	background:  {$colors['accent_color']};
	height: 3px;
	content: ' ';
	margin: 0 auto;
	margin-top: 10px;
}
button:hover, button, button:active, button:focus {
	border: 1px solid {$colors['accent_color']};
	background-color:{$colors['accent_color']};
	background:{$colors['accent_color']};
}
.dropdown-menu .current-menu-item.current_page_item a, .dropdown-menu .current-menu-item.current_page_item a:hover, .dropdown-menu .current-menu-item.current_page_item a:active, .dropdown-menu .current-menu-item.current_page_item a:focus {
	background: {$colors['accent_color']} !important;
	color:#fff !important
}
@media (max-width: 767px) {
	.navbar-default .navbar-nav .open .dropdown-menu > li > a:hover {
		background-color: {$colors['accent_color']};
		color: #fff;
	}
}
blockquote {
	border-left: 5px solid {$colors['accent_color']};
}
.sticky-post{
	background: {$colors['accent_color']};
	color:white;
}

.entry-title a:hover,
.entry-title a:focus{
	color: {$colors['accent_color']};
}

.entry-header .entry-meta::after{
	background: {$colors['accent_color']};
}

.post-password-form input[type="submit"], .post-password-form input[type="submit"]:hover, .post-password-form input[type="submit"]:focus, .post-password-form input[type="submit"]:active {
	background-color: {$colors['accent_color']};

}

.fa {
	color: {$colors['accent_color']};
}

.btn-default{
	border-bottom: 1px solid {$colors['accent_color']};
}

.btn-default:hover, .btn-default:focus{
	border-bottom: 1px solid {$colors['accent_color']};
	background-color: {$colors['accent_color']};
}

.nav-previous:hover, .nav-next:hover{
	border: 1px solid {$colors['accent_color']};
	background-color: {$colors['accent_color']};
}

.next-post a:hover,.prev-post a:hover{
	color: {$colors['accent_color']};
}

.posts-navigation .next-post a:hover .fa, .posts-navigation .prev-post a:hover .fa{
	color: {$colors['accent_color']};
}


#secondary .widget-title {
border-left: 3px solid {$colors['accent_color']};
}

	#secondary .widget a:hover,
	#secondary .widget a:focus{
color: {$colors['accent_color']};
}

	#secondary .widget_calendar tbody a {
background-color: {$colors['accent_color']};
color: #fff;
padding: 0.2em;
}

	#secondary .widget_calendar tbody a:hover{
background-color: {$colors['accent_color']};
color: #fff;
padding: 0.2em;
}	

CSS;

return $css;
}

if(! function_exists('lighthouse_header_bg_color_css' ) ):
/**
* Set the header background color 
*/
function lighthouse_header_bg_color_css(){

	?>

	<style type="text/css">
	.site-header { background: <?php echo esc_attr(get_theme_mod( 'header_bg_color')); ?>; }
	.footer-widgets h3 { color: <?php echo esc_attr(get_theme_mod( 'footer_widget_title_colors')); ?>; }
	.site-footer { background: <?php echo esc_attr(get_theme_mod( 'footer_copyright_background_color')); ?>; }
	.footer-widget-wrapper { background: <?php echo esc_attr(get_theme_mod( 'footer_colors')); ?>; }
	.row.site-info { color: <?php echo esc_attr(get_theme_mod( 'footer_copyright_text_color')); ?>; }
	#secondary h3.widget-title, #secondary h4.widget-title { color: <?php echo esc_attr(get_theme_mod( 'sidebar_headline_colors')); ?>; }
	#secondary .widget { background: <?php echo esc_attr(get_theme_mod( 'sidebar_background_color')); ?>; }
	#secondary .widget a { color: <?php echo esc_attr(get_theme_mod( 'sidebar_link_color')); ?>; }
	#secondary .widget li { border-color: <?php echo esc_attr(get_theme_mod( 'sidebar_link_border_color')); ?>; }
   	.site-description, .site-title { color: <?php echo esc_attr(get_theme_mod( 'header_textcolor')); ?>; }
	.site-title::after{ background-color: <?php echo esc_attr(get_theme_mod( 'header_textcolor')); ?>; }
   	.site-description, .site-title { color: <?php echo esc_attr(get_theme_mod( 'header_color_text_colorino')); ?>; }
	.site-title::after{ background-color: <?php echo esc_attr(get_theme_mod( 'header_color_text_colorino')); ?>; }	
	.navbar-default { background-color: <?php echo esc_attr(get_theme_mod( 'navigation_background_color')); ?>; }
	.navbar-default .navbar-nav>li>a { color: <?php echo esc_attr(get_theme_mod( 'navigation_text_color')); ?>; }
	.navbar-default .navbar-brand { color: <?php echo esc_attr(get_theme_mod( 'navigation_logo_color')); ?>; }
	h1.entry-title, .entry-header .entry-title a { color: <?php echo esc_attr(get_theme_mod( 'headline_color')); ?>; }
	.entry-content, .entry-summary { color: <?php echo esc_attr(get_theme_mod( 'post_content_color')); ?>; }
	h5.entry-date, h5.entry-date a { color: <?php echo esc_attr(get_theme_mod( 'author_line_color')); ?>; }
	.top-widgets { background: <?php echo esc_attr(get_theme_mod( 'top_widget_background_color')); ?>; }
	.top-widgets h3 { color: <?php echo esc_attr(get_theme_mod( 'top_widget_title_color')); ?>; }
	.top-widgets, .top-widgets p { color: <?php echo esc_attr(get_theme_mod( 'top_widget_text_color')); ?>; }
	.bottom-widgets { background: <?php echo esc_attr(get_theme_mod( 'bottom_widget_background_color')); ?>; }
	.bottom-widgets h3 { color: <?php echo esc_attr(get_theme_mod( 'bottom_widget_title_color')); ?>; }
	.bottom-widgets, .bottom-widgets p { color: <?php echo esc_attr(get_theme_mod( 'bottom_widget_text_color')); ?>; }

	</style>
	<?php }
	add_action( 'wp_head', 'lighthouse_header_bg_color_css' );
	endif;

/**
 * Binds JS listener to make Customizer color_scheme control.
 *
 * Passes color scheme data as colorScheme global.
 *
 */
function lighthouse_customize_control_js() {
	wp_enqueue_script( 'lighthouse-color-scheme-control', get_template_directory_uri() . '/js/color-scheme-control.js', array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), '20141216', true );
	wp_localize_script( 'lighthouse-color-scheme-control', 'colorScheme', lighthouse_get_color_schemes() );
}
add_action( 'customize_controls_enqueue_scripts', 'lighthouse_customize_control_js' );


function lighthouse_customizer_stylesheet() {
	
	wp_enqueue_style( 'lighthouse-customizer-css', get_template_directory_uri().'/css/customize-css.css', NULL, NULL, 'all' );
	
}


/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function lighthouse_customize_preview_js() {
	wp_enqueue_script( 'lighthouse_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'lighthouse_customize_preview_js' );

/**
 * Output an Underscore template for generating CSS for the color scheme.
 *
 * The template generates the css dynamically for instant display in the Customizer
 * preview.
 *
 */
function lighthouse_color_scheme_css_template() {
	$colors = array(
		'accent_color'            => '{{ data.accent_color }}',
		);
		?>
		<script type="text/html" id="tmpl-lighthouse-color-scheme">
		<?php echo lighthouse_get_color_scheme_css( $colors ); ?>
		</script>
		<?php
	}
	add_action( 'customize_controls_print_footer_scripts', 'lighthouse_color_scheme_css_template' );

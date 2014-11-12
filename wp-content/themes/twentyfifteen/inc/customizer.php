<?php
/**
 * Twenty Fifteen Customizer.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

/**
 * Add postMessage support for site title and description for the Customizer.
 *
 * @since Twenty Fifteen 1.0
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function twentyfifteen_customize_register( $wp_customize ) {
	$color_scheme = twentyfifteen_get_color_scheme();

	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	// Add color scheme setting and control.
	$wp_customize->add_setting( 'color_scheme', array(
		'default'           => 'default',
		'sanitize_callback' => 'twentyfifteen_sanitize_color_scheme',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_setting( 'color_scheme_css', array(
		'default'           => '',
		'sanitize_callback' => 'esc_html',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( 'color_scheme', array(
		'label'    => esc_html__( 'Base Color Scheme', 'twentyfifteen' ),
		'section'  => 'colors',
		'type'     => 'select',
		'choices'  => twentyfifteen_get_color_scheme_choices(),
		'priority' => 1,
	) );

	// Add custom header and sidebar text color setting and control.
	$wp_customize->add_setting( 'sidebar_textcolor', array(
		'default'           => $color_scheme[4],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_textcolor', array(
		'label'       => esc_html__( 'Header and Sidebar Text Color', 'twentyfifteen' ),
		'description' => esc_html__( 'Only applied to the sidebar on wider screens. On small screens the sidebar will become the header.', 'twentyfifteen' ),
		'section'     => 'colors',
	) ) );

	// Remove the core header textcolor control, as it shares the sidebar text color.
	$wp_customize->remove_control( 'header_textcolor' );

	// Add custom header and sidebar background color setting and control.
	$wp_customize->add_setting( 'header_background_color', array(
		'default'           => $color_scheme[1],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_background_color', array(
		'label'       => esc_html__( 'Header and Sidebar Background Color', 'twentyfifteen' ),
		'description' => esc_html__( 'Only applied to the sidebar on wider screens. On small screens the sidebar will become the header.', 'twentyfifteen' ),
		'section'     => 'colors',
	) ) );

	// Add an additional description to the header image section.
	$wp_customize->get_section( 'header_image' )->description = esc_html__( 'Only applied to the sidebar on wide screens. On small screens it will be applied to the header.', 'twentyfifteen' );
}
add_action( 'customize_register', 'twentyfifteen_customize_register', 11 );

/**
 * Register color schemes for Twenty Fifteen.
 * Can be filtered with twentyfifteen_color_schemes.
 *
 * The order of colors in a colors array:
 * 1. Main Background Color.
 * 2. Sidebar Background Color.
 * 3. Box Background Bolor.
 * 4. Main Text and Link Color.
 * 5. Sidebar Text and Link Color.
 * 6. Meta Box Background Color.
 *
 * @since Twenty Fifteen 1.0
 *
 * @return array An associative array of color scheme options.
 */
function twentyfifteen_get_color_schemes() {
	return apply_filters( 'twentyfifteen_color_schemes', array(
		'default' => array(
			'label'  => esc_html__( 'Default', 'twentyfifteen' ),
			'colors' => array(
				'#f1f1f1',
				'#ffffff',
				'#ffffff',
				'#333333',
				'#333333',
				'#f7f7f7',
			),
		),
		'dark'    => array(
			'label'  => esc_html__( 'Dark', 'twentyfifteen' ),
			'colors' => array(
				'#111111',
				'#202020',
				'#202020',
				'#bebebe',
				'#bebebe',
				'#1b1b1b',
			),
		),
		'yellow'  => array(
			'label'  => esc_html__( 'Yellow', 'twentyfifteen' ),
			'colors' => array(
				'#f4ca16',
				'#ffdf00',
				'#ffffff',
				'#111111',
				'#111111',
				'#f1f1f1',
			),
		),
		'pink'    => array(
			'label'  => esc_html__( 'Pink', 'twentyfifteen' ),
			'colors' => array(
				'#ffe5d1',
				'#e53b51',
				'#ffffff',
				'#352712',
				'#ffffff',
				'#f1f1f1',
			),
		),
		'purple'  => array(
			'label'  => esc_html__( 'Purple', 'twentyfifteen' ),
			'colors' => array(
				'#674970',
				'#2e2256',
				'#ffffff',
				'#2e2256',
				'#ffffff',
				'#f1f1f1',
			),
		),
		'blue'   => array(
			'label'  => esc_html__( 'Blue', 'twentyfifteen' ),
			'colors' => array(
				'#e9f2f9',
				'#55c3dc',
				'#ffffff',
				'#22313f',
				'#ffffff',
				'#f1f1f1',
			),
		),
	) );
}

if ( ! function_exists( 'twentyfifteen_get_color_scheme' ) ) :
/**
 * Returns an array of either the current or default color scheme hex values.
 *
 * @since Twenty Fifteen 1.0
 *
 * @return array
 */
function twentyfifteen_get_color_scheme() {
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );
	$color_schemes       = twentyfifteen_get_color_schemes();

	if ( array_key_exists( $color_scheme_option, $color_schemes ) ) {
		return $color_schemes[ $color_scheme_option ]['colors'];
	}

	return $color_schemes['default']['colors'];
}
endif; // twentyfifteen_get_color_scheme

if ( ! function_exists( 'twentyfifteen_get_color_scheme_control_options' ) ) :
/**
 * Returns an array of color scheme choices registered for Twenty Fifteen.
 *
 * @since Twenty Fifteen 1.0
 *
 * @return array
 */
function twentyfifteen_get_color_scheme_choices() {
	$color_schemes                = twentyfifteen_get_color_schemes();
	$color_scheme_control_options = array();

	foreach ( $color_schemes as $color_scheme => $value ) {
		$color_scheme_control_options[ $color_scheme ] = $value['label'];
	}

	return $color_scheme_control_options;
}
endif; // twentyfifteen_get_color_scheme_control_options

if ( ! function_exists( 'twentyfifteen_sanitize_color_scheme' ) ) :
/**
 * Sanitization callback for color schemes.
 *
 * @since Twenty Fifteen 1.0
 *
 * @param string $value Color scheme name value.
 *
 * @return string Color scheme name.
 */
function twentyfifteen_sanitize_color_scheme( $value ) {
	$color_schemes = twentyfifteen_get_color_scheme_choices();

	if ( ! array_key_exists( $value, $color_schemes ) ) {
		$value = 'default';
	}

	return $value;
}
endif; // twentyfifteen_sanitize_color_scheme

/**
 * Enqueues front-end CSS for color scheme.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_color_scheme_css() {
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );
	$color_scheme_css    = get_theme_mod( 'color_scheme_css', '' );

	// Don't do anything if the default color scheme is selected.
	if ( 'default' === $color_scheme_option || empty( $color_scheme_css ) ) {
		return;
	}

	wp_add_inline_style( 'twentyfifteen-style', $color_scheme_css );
}
add_action( 'wp_enqueue_scripts', 'twentyfifteen_color_scheme_css' );

/**
 * Binds JS listener to make Customizer color_scheme control.
 * Passes color scheme data as colorScheme global.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_customize_control_js() {
	wp_enqueue_script( 'color-scheme-control', get_template_directory_uri() . '/js/color-scheme-control.js', array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), '', true  );
	wp_localize_script( 'color-scheme-control', 'colorScheme', twentyfifteen_get_color_schemes() );
}
add_action( 'customize_controls_enqueue_scripts', 'twentyfifteen_customize_control_js' );

/**
 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_customize_preview_js() {
	wp_enqueue_script( 'twentyfifteen-customize-preview', get_template_directory_uri() . '/js/customize-preview.js', array( 'customize-preview' ), '20141029', true );
}
add_action( 'customize_preview_init', 'twentyfifteen_customize_preview_js' );

/**
 * Output an Underscore template for generating CSS for the color scheme.
 *
 * The template generates the css dynamically for instant display in the Customizer preview,
 * and to be saved in a `theme_mod` for display on the front-end.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_color_scheme_css_template() {
	?>
	<script type="text/html" id="tmpl-twentyfifteen-color-scheme">
	/* Color Scheme */

	/* Background Color */
	body {
		background-color: {{ data.background_color }};
	}

	/* Sidebar Background Color */
	body:before,
	.site-header {
		background-color: {{ data.header_background_color }};
	}

	/* Box Background Color */
	.post-navigation,
	.pagination,
	.secondary,
	.site-footer,
	.hentry,
	.page-header,
	.page-content,
	.comments-area {
		background-color: {{ data.box_background_color }};
	}

	/* Box Background Color */
	button,
	input[type="button"],
	input[type="reset"],
	input[type="submit"],
	.pagination .prev,
	.pagination .next,
	.pagination .prev:before,
	.pagination .next:before,
	.widget_calendar tbody a,
	.widget_calendar tbody a:hover,
	.widget_calendar tbody a:focus,
	.entry-content .page-links a,
	.entry-content .page-links a:hover,
	.entry-content .page-links a:focus,
	.sticky-post {
		color: {{ data.box_background_color }};
	}

	/* Main Text Color */
	button,
	input[type="button"],
	input[type="reset"],
	input[type="submit"],
	.pagination .prev,
	.pagination .next,
	.widget_calendar tbody a,
	.page-links a,
	.sticky-post {
		background-color: {{ data.textcolor }};
	}

	/* Main Text Color */
	body,
	blockquote cite,
	blockquote small,
	a,
	.dropdown-toggle:after,
	.image-navigation a:hover,
	.image-navigation a:focus,
	.comment-navigation a:hover,
	.comment-navigation a:focus,
	.widget-title,
	.entry-footer a:hover,
	.entry-footer a:focus,
	.comment-metadata a:hover,
	.comment-metadata a:focus,
	.pingback .edit-link a:hover,
	.pingback .edit-link a:focus,
	.comment-list .reply a:hover,
	.comment-list .reply a:focus,
	.site-info a:hover,
	.site-info a:focus {
		color: {{ data.textcolor }};
	}

	/* Main Text Color */
	.entry-content a,
	.entry-summary a,
	.page-content a,
	.comment-content a,
	.pingback .comment-body > a,
	.author-description a,
	.taxonomy-description a,
	.textwidget a,
	.entry-footer a:hover,
	.comment-metadata a:hover,
	.pingback .edit-link a:hover,
	.comment-list .reply a:hover,
	.site-info a:hover {
		border-color: {{ data.textcolor }};
	}

	/* Secondary Text Color */
	button:hover,
	button:focus,
	input[type="button"]:hover,
	input[type="button"]:focus,
	input[type="reset"]:hover,
	input[type="reset"]:focus,
	input[type="submit"]:hover,
	input[type="submit"]:focus,
	.pagination .prev:hover,
	.pagination .prev:focus,
	.pagination .next:hover,
	.pagination .next:focus,
	.widget_calendar tbody a:hover,
	.widget_calendar tbody a:focus,
	.page-links a:hover,
	.page-links a:focus {
		background-color: {{ data.textcolor }}; /* Fallback for IE7 and IE8 */
		background-color: {{ data.secondary_textcolor }};
	}

	/* Secondary Text Color */
	blockquote,
	a:hover,
	a:focus,
	.main-navigation .menu-item-description,
	.post-navigation .meta-nav,
	.post-navigation a:hover .post-title,
	.post-navigation a:focus .post-title,
	.image-navigation,
	.image-navigation a,
	.comment-navigation,
	.comment-navigation a,
	.widget,
	.author-heading,
	.entry-footer,
	.entry-footer a,
	.taxonomy-description,
	.page-links > .page-links-title,
	.entry-caption,
	.comment-author,
	.comment-metadata,
	.comment-metadata a,
	.pingback .edit-link,
	.pingback .edit-link a,
	.post-password-form label,
	.comment-form label,
	.comment-notes,
	.comment-awaiting-moderation,
	.logged-in-as,
	.form-allowed-tags,
	.no-comments,
	.site-info,
	.site-info a,
	.wp-caption-text,
	.gallery-caption,
	.comment-list .reply a {
		color: {{ data.textcolor }}; /* Fallback for IE7 and IE8 */
		color: {{ data.secondary_textcolor }};
	}

	/* Secondary Text Color */
	blockquote,
	.logged-in-as a:hover,
	.comment-author a:hover {
		border-color: {{ data.textcolor }}; /* Fallback for IE7 and IE8 */
		border-color: {{ data.secondary_textcolor }};
	}

	/* Border Color */
	hr,
	.dropdown-toggle:hover,
	.dropdown-toggle:focus {
		background-color: {{ data.textcolor }}; /* Fallback for IE7 and IE8 */
		background-color: {{ data.border_color }};
	}

	/* Border Color */
	pre,
	abbr[title],
	table,
	th,
	td,
	input,
	textarea,
	.main-navigation ul,
	.main-navigation li,
	.post-navigation,
	.pagination,
	.comment-navigation,
	.widget li,
	.widget_categories .children,
	.widget_nav_menu .sub-menu,
	.widget_pages .children,
	.site-header,
	.site-footer,
	.hentry .hentry,
	.author-info,
	.entry-content .page-links a,
	.page-links > span,
	.page-header,
	.comments-area,
	.comment-list .comment-respond,
	.comment-list article,
	.comment-list .pingback,
	.comment-list .trackback,
	.comment-list .reply a,
	.no-comments {
		border-color: {{ data.textcolor }}; /* Fallback for IE7 and IE8 */
		border-color: {{ data.border_color }};
	}

	.post-navigation .nav-previous:not(.has-post-thumbnail) .nav-next:not(.has-post-thumbnail) {
		border-color: {{ data.border_color }};
	}

	/* Border Focus Color */
	a:focus,
	button:focus,
	input:focus {
		outline-color: {{ data.textcolor }}; /* Fallback for IE7 and IE8 */
		outline-color: {{ data.border_focus_color }};
	}

	input:focus,
	textarea:focus {
		border-color: {{ data.textcolor }}; /* Fallback for IE7 and IE8 */
		border-color: {{ data.border_focus_color }};
	}

	/* Sidebar Link Color */
	.secondary-toggle:before {
		color: {{ data.sidebar_textcolor }};
	}

	.site-title a,
	.site-description {
		color: {{ data.sidebar_textcolor }};
	}

	/* Sidebar Text Color */
	.site-title a:hover,
	.site-title a:focus {
		color: {{ data.secondary_sidebar_textcolor }};
	}

	/* Sidebar Border Color */
	.secondary-toggle {
		border-color: {{ data.sidebar_textcolor }}; /* Fallback for IE7 and IE8 */
		border-color: {{ data.sidebar_border_color }};
	}

	/* Sidebar Border Focus Color */
	.secondary-toggle:hover,
	.secondary-toggle:focus {
		border-color: {{ data.sidebar_textcolor }}; /* Fallback for IE7 and IE8 */
		border-color: {{ data.sidebar_border_focus_color }};
	}

	.site-title a {
		outline-color: {{ data.sidebar_textcolor }}; /* Fallback for IE7 and IE8 */
		outline-color: {{ data.sidebar_border_focus_color }};
	}

	/* Meta Background Color */
	.entry-footer {
		background-color: {{ data.meta_box_background_color }};
	}

	@media screen and (min-width: 38.75em) {
		/* Main Text Color */
		.page-header {
			border-color: {{ data.textcolor }};
		}
	}

	@media screen and (min-width: 59.6875em) {
		/* Make sure its transparent on desktop */
		.site-header,
		.secondary {
			background-color: transparent;
		}

		/* Sidebar Background Color */
		.widget button,
		.widget input[type="button"],
		.widget input[type="reset"],
		.widget input[type="submit"],
		.widget_calendar tbody a,
		.widget_calendar tbody a:hover,
		.widget_calendar tbody a:focus {
			color: {{ data.header_background_color }};
		}

		/* Sidebar Link Color */
		.secondary a,
		.dropdown-toggle:after,
		.widget-title,
		.widget blockquote cite,
		.widget blockquote small {
			color: {{ data.sidebar_textcolor }};
		}

		.widget button,
		.widget input[type="button"],
		.widget input[type="reset"],
		.widget input[type="submit"],
		.widget_calendar tbody a {
			background-color: {{ data.sidebar_textcolor }};
		}

		.textwidget a {
			border-color: {{ data.sidebar_textcolor }};
		}

		/* Sidebar Text Color */
		.secondary a:hover,
		.secondary a:focus,
		.main-navigation .menu-item-description,
		.widget,
		.widget blockquote,
		.widget .wp-caption-text,
		.widget .gallery-caption {
			color: {{ data.secondary_sidebar_textcolor }};
		}

		.widget button:hover,
		.widget button:focus,
		.widget input[type="button"]:hover,
		.widget input[type="button"]:focus,
		.widget input[type="reset"]:hover,
		.widget input[type="reset"]:focus,
		.widget input[type="submit"]:hover,
		.widget input[type="submit"]:focus,
		.widget_calendar tbody a:hover,
		.widget_calendar tbody a:focus {
			background-color: {{ data.secondary_sidebar_textcolor }};
		}

		.widget blockquote {
			border-color: {{ data.secondary_sidebar_textcolor }};
		}

		/* Sidebar Border Color */
		.main-navigation ul,
		.main-navigation li,
		.widget input,
		.widget textarea,
		.widget table,
		.widget th,
		.widget td,
		.widget pre,
		.widget li,
		.widget_categories .children,
		.widget_nav_menu .sub-menu,
		.widget_pages .children,
		.widget abbr[title] {
			border-color: {{ data.sidebar_border_color }};
		}

		.dropdown-toggle:hover,
		.dropdown-toggle:focus,
		.widget hr {
			background-color: {{ data.sidebar_border_color }};
		}

		.widget input:focus,
		.widget textarea:focus {
			border-color: {{ data.sidebar_border_focus_color }};
		}

		.sidebar a:focus,
		.dropdown-toggle:focus {
			outline-color: {{ data.sidebar_border_focus_color }};
		}
	}
	</script>
	<?php
}
add_action( 'customize_controls_print_footer_scripts', 'twentyfifteen_color_scheme_css_template' );

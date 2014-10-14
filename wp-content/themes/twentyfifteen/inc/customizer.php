<?php
/**
 * Twenty Fifteen Theme Customizer.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @since Twenty Fifteen 1.0
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function twentyfifteen_customize_register( $wp_customize ) {
	$color_scheme = twentyfifteen_get_color_scheme();

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'background_color' )->transport = 'refresh';

	// Add color scheme setting and control.
	$wp_customize->add_setting( 'color_scheme', array(
		'default'           => 'default',
		'sanitize_callback' => 'twentyfifteen_sanitize_color_scheme',
	) );

	$wp_customize->add_control( new Twentyfifteen_Customize_Color_Scheme_Control( $wp_customize, 'color_scheme', array(
		'label'    => esc_html__( 'Color Scheme', 'twentyfifteen' ),
		'section'  => 'colors',
		'choices'  => twentyfifteen_get_color_scheme_choices(),
		'priority' => 1,
	) ) );

	// Add custom sidebar text color setting and control.
	$wp_customize->add_setting( 'sidebar_textcolor', array(
		'default'           => $color_scheme[4],
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_textcolor', array(
		'label'   => __( 'Sidebar Text Color', 'twentyfifteen' ),
		'section' => 'colors',
	) ) );

	// Add custom header background color setting and control.
	$wp_customize->add_setting( 'header_background_color', array(
		'default'           => $color_scheme[1],
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_background_color', array(
		'label'   => esc_html__( 'Header & Sidebar Background Color', 'twentyfifteen' ),
		'section' => 'colors',
	) ) );
}
add_action( 'customize_register', 'twentyfifteen_customize_register', 11 );

/**
 * Custom control for Color Schemes
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_customize_color_scheme_control() {
	class Twentyfifteen_Customize_Color_Scheme_Control extends WP_Customize_Control {
		public $type = 'colorScheme';

		function enqueue() {
	 		wp_enqueue_script( 'color-scheme-control', get_template_directory_uri() . '/js/color-scheme-control.js', array( 'customize-controls' ), '', true  );
	 		wp_localize_script( 'color-scheme-control', 'colorScheme', twentyfifteen_get_color_schemes() );
	 	}

		public function render_content() {
			if ( empty( $this->choices ) )
				return;

			?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php endif; ?>

				<select <?php $this->link(); ?>>
					<?php
					foreach ( $this->choices as $value => $label )
						echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>';
					?>
				</select>
			</label>
			<?php
		}
	}
}
add_action( 'customize_register', 'twentyfifteen_customize_color_scheme_control', 10 );

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
 * Returns an array of either the current or default color scheme hex values
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

	return $color_schemes[ 'default' ]['colors'];
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

	// Don't do anything if the default color scheme is selected.
	if ( 'default' === $color_scheme_option ) {
		return;
	}

	// If we get this far, we have custom styles. Let's do this.
	$color_scheme = twentyfifteen_get_color_scheme();

	// Convert main and sidebar text hex color to rgba.
	$color_main_text_rgb        = twentyfifteen_hex2rgb( $color_scheme[3] );
	$color_sidebar_link_rgb     = twentyfifteen_hex2rgb( $color_scheme[4] );

	$color_background           = $color_scheme[0];
	$color_sidebar_background   = $color_scheme[1];
	$color_box_background       = $color_scheme[2];
	$color_main_text            = $color_scheme[3];
	$color_secondary_text       = vsprintf( 'rgba( %1$s, %2$s, %3$s, 0.7)', $color_main_text_rgb );
	$color_border               = vsprintf( 'rgba( %1$s, %2$s, %3$s, 0.1)', $color_main_text_rgb );
	$color_border_focus         = vsprintf( 'rgba( %1$s, %2$s, %3$s, 0.3)', $color_main_text_rgb );
	$color_sidebar_link         = $color_scheme[4];
	$color_sidebar_text         = vsprintf( 'rgba( %1$s, %2$s, %3$s, 0.7)', $color_sidebar_link_rgb );
	$color_sidebar_border       = vsprintf( 'rgba( %1$s, %2$s, %3$s, 0.1)', $color_sidebar_link_rgb );
	$color_sidebar_border_focus = vsprintf( 'rgba( %1$s, %2$s, %3$s, 0.3)', $color_sidebar_link_rgb );
	$color_meta_box             = $color_scheme[5];

	$css = '
		/* Color Scheme */

		/* Background Color */
		body {
			background-color: %1$s;
		}

		/* Sidebar Background Color */
		body:before,
		.site-header {
			background-color: %2$s;
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
			background-color: %3$s;
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
		.entry-content .page-links a,
		.entry-content .page-links a:hover,
		.entry-content .page-links a:focus,
		.sticky-post {
			color: %3$s;
		}

		/* Main Text Color */
		button,
		input[type="button"],
		input[type="reset"],
		input[type="submit"],
		.pagination .prev,
		.pagination .next,
		.page-links a,
		.sticky-post {
			background-color: %4$s;
		}

		/* Main Text Color */
		body,
		blockquote cite,
		blockquote small,
		a,
		.image-navigation a:hover,
		.image-navigation a:focus,
		.comment-navigation a:hover,
		.comment-navigation a:focus,
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
			color: %4$s;
		}

		/* Main Text Color */
		.entry-content a,
		.entry-summary a,
		.page-content a,
		.comment-content a,
		.author-description a,
		.comment-list .reply a:hover,
		.comment-list .reply a:focus {
			border-color: %4$s;
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
		.page-links a:hover,
		.page-links a:focus {
			background-color: %4$s; /* Fallback for IE7 and IE8 */
			background-color: %5$s;
		}

		/* Secondary Text Color */
		blockquote,
		input[type="text"],
		input[type="email"],
		input[type="url"],
		input[type="password"],
		input[type="search"],
		textarea,
		a:hover,
		a:focus,
		.post-navigation .meta-nav,
		.post-navigation a:hover .post-title,
		.post-navigation a:focus .post-title,
		.image-navigation,
		.image-navigation a,
		.comment-navigation,
		.comment-navigation a,
		.author-heading,
		.entry-footer,
		.entry-footer a,
		.taxonomy-description,
		.page-links > .page-links-title,
		.entry-caption,
		.comment-author,
		.comment-metadata,
		.comment-metadata a,
		.pingback .comment-edit-link,
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
		.gallery-caption {
			color: %4$s; /* Fallback for IE7 and IE8 */
			color: %5$s;
		}

		/* Secondary Text Color */
		blockquote,
		.entry-content a:hover,
		.entry-content a:focus,
		.entry-summary a:hover,
		.entry-summary a:focus,
		.page-content a:hover,
		.page-content a:focus,
		.comment-content a:hover,
		.comment-content a:focus,
		.author-description a:hover,
		.author-description a:focus {
			border-color: %4$s; /* Fallback for IE7 and IE8 */
			border-color: %5$s;
		}

		/* Border Color */
		hr {
			background-color: %4$s; /* Fallback for IE7 and IE8 */
			background-color: %6$s;
		}

		/* Border Color */
		pre,
		abbr[title],
		table,
		th,
		td,
		input,
		textarea,
		.post-navigation,
		.post-navigation .nav-previous:not(.has-post-thumbnail) + .nav-next:not(.has-post-thumbnail),
		.pagination,
		.comment-navigation,
		.site-header,
		.site-footer,
		.hentry + .hentry,
		.author-info,
		.entry-content .page-links a,
		.page-links > span,
		.page-header,
		.comments-area,
		.comment-list + .comment-respond,
		.comment-list article,
		.comment-list .pingback,
		.comment-list .trackback,
		.comment-list .reply a,
		.no-comments {
			border-color: %4$s; /* Fallback for IE7 and IE8 */
			border-color: %6$s;
		}

		/* Border Focus Color */
		input:focus,
		textarea:focus {
			border-color: %4$s; /* Fallback for IE7 and IE8 */
			border-color: %7$s;
		}

		/* Sidebar Link Color */
		.secondary-toggle:hover {
			border-color: %8$s;
		}

		.secondary-toggle:before {
			color: %8$s;
		}

		.secondary-toggle:focus {
			outline-color: %8$s;
		}

		.site-title a,
		.site-description {
			color: %8$s;
		}

		/* Sidebar Text Color */
		.site-title a:hover,
		.site-title a:focus {
			color: %9$s;
		}

		/* Sidebar Border Color */
		.secondary-toggle {
			border-color: %8$s; /* Fallback for IE7 and IE8 */
			border-color: %10$s;
		}

		/* Meta Background Color */
		.entry-footer {
			background-color: %12$s;
		}

		@media screen and (min-width: 38.75em) {
			/* Main Text Color */
			.page-header {
				border-color: %4$s;
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
			.widget_calendar tbody a:focus,
			.widget mark,
			.widget ins {
				color: %2$s;
			}

			/* Sidebar Link Color */
			.widget button,
			.widget input[type="button"],
			.widget input[type="reset"],
			.widget input[type="submit"],
			.widget_calendar tbody a,
			.widget mark,
			.widget ins {
				background-color: %8$s;
			}

			.secondary a,
			.dropdown-toggle:after,
			.widget-title,
			.widget blockquote cite,
			.widget blockquote small {
				color: %8$s;
			}

			.dropdown-toggle:focus {
				outline-color: %8$s;
			}

			/* Sidebar Text Color */
			.secondary a:hover,
			.secondary a:focus,
			.widget,
			.main-navigation .menu-item-description,
			.widget blockquote,
			.widget .wp-caption-text,
			.widget .gallery-caption {
				color: %9$s;
			}

			.dropdown-toggle:hover,
			.dropdown-toggle:focus,
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
				background-color: %9$s;
			}

			.widget blockquote {
				border-color: %9$s;
			}

			/* Sidebar Border Color */
			.main-navigation ul,
			.main-navigation li,
			.widget input,
			.widget textarea,
			.widget table,
			.widget th,
			.widget td,
			.widget input,
			.widget textarea,
			.widget pre,
			.widget li,
			.widget_categories .children,
			.widget_nav_menu .sub-menu,
			.widget_pages .children,
			.widget abbr[title] {
				border-color: %10$s;
			}

			.widget hr {
				background-color: %10$s;
			}

			/* Sidebar Border Focus Color */
			.widget input:focus,
			.widget textarea:focus {
				border-color: %11$s;
			}
		}
	';

	wp_add_inline_style( 'twentyfifteen-style', sprintf( $css,
		$color_background,
		$color_sidebar_background,
		$color_box_background,
		$color_main_text,
		$color_secondary_text,
		$color_border,
		$color_border_focus,
		$color_sidebar_link,
		$color_sidebar_text,
		$color_sidebar_border,
		$color_sidebar_border_focus,
		$color_meta_box
	) );
}
add_action( 'wp_enqueue_scripts', 'twentyfifteen_color_scheme_css' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_customize_preview_js() {
	wp_enqueue_script( 'twentyfifteen-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20141005', true );
}
add_action( 'customize_preview_init', 'twentyfifteen_customize_preview_js' );
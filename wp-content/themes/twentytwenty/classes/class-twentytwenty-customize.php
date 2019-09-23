<?php
/**
 * Customizer settings for this theme.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0.0
 */

if ( ! class_exists( 'TwentyTwenty_Customize' ) ) {
	/**
	 * CUSTOMIZER SETTINGS
	 */
	class TwentyTwenty_Customize {

		/**
		 * Register customizer options.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		public static function register( $wp_customize ) {

			/**
			 * Site Title & Description.
			 * */
			$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
			$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

			$wp_customize->selective_refresh->add_partial(
				'blogname',
				array(
					'selector'        => '.site-title a',
					'render_callback' => 'twentytwenty_customize_partial_blogname',
				)
			);

			$wp_customize->selective_refresh->add_partial(
				'blogdescription',
				array(
					'selector'        => '.site-description',
					'render_callback' => 'twentytwenty_customize_partial_blogdescription',
				)
			);

			$wp_customize->selective_refresh->add_partial(
				'custom_logo',
				array(
					'selector'        => '.header-titles [class*=site-]:not(.site-description)',
					'render_callback' => 'twentytwenty_customize_partial_site_logo',
				)
			);

			/**
			 * Site Identity
			 */

			/* 2X Header Logo ---------------- */
			$wp_customize->add_setting(
				'retina_logo',
				array(
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => array( __CLASS__, 'sanitize_checkbox' ),
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				'retina_logo',
				array(
					'type'        => 'checkbox',
					'section'     => 'title_tagline',
					'priority'    => 10,
					'label'       => __( 'Retina logo', 'twentytwenty' ),
					'description' => __( 'Scales the logo to half its uploaded size, making it sharp on high-res screens.', 'twentytwenty' ),
				)
			);

			// Header & Footer Background Color.
			$wp_customize->add_setting(
				'header_footer_background_color',
				array(
					'default'           => '#ffffff',
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'header_footer_background_color',
					array(
						'label'   => esc_html__( 'Header & Footer Background Color', 'twentytwenty' ),
						'section' => 'colors',
					)
				)
			);

			/**
			 * Implementation for the accent color.
			 * This is different to all other color options because of the accessibility enhancements.
			 * The control is a hue-only colorpicker, and there is a separate setting that holds values
			 * for other colors calculated based on the selected hue and various background-colors on the page.
			 *
			 * @since 1.0.0
			 */

			// Add the setting for the hue colorpicker.
			$wp_customize->add_setting(
				'accent_hue',
				array(
					'default'           => 344,
					'type'              => 'theme_mod',
					'sanitize_callback' => 'absint',
					'transport'         => 'postMessage',
				)
			);

			// Add setting to hold colors derived from the accent hue.
			$wp_customize->add_setting(
				'accent_accessible_colors',
				array(
					'default'           => array(
						'content'       => array(
							'text'      => '#000000',
							'accent'    => '#cd2653',
							'secondary' => '#6d6d6d',
							'borders'   => '#dcd7ca',
						),
						'header-footer' => array(
							'text'      => '#000000',
							'accent'    => '#cd2653',
							'secondary' => '#6d6d6d',
							'borders'   => '#dcd7ca',
						),
					),
					'type'              => 'theme_mod',
					'transport'         => 'postMessage',
					'sanitize_callback' => array( 'TwentyTwenty_Customize', 'sanitize_accent_accessible_colors' ),
				)
			);

			// Add the hue-only colorpicker for the accent color.
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'accent_hue',
					array(
						'label'    => esc_html__( 'Accent Color Hue', 'twentytwenty' ),
						'section'  => 'colors',
						'settings' => 'accent_hue',
						'mode'     => 'hue',
					)
				)
			);

			/**
			 * Custom Accent Colors.
			*/
			$accent_color_options = self::get_color_options();

			// Loop over the color options and add them to the customizer.
			foreach ( $accent_color_options as $color_option_name => $color_option ) {

				$wp_customize->add_setting(
					$color_option_name,
					array(
						'default'           => $color_option['default'],
						'sanitize_callback' => 'sanitize_hex_color',
					)
				);

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
						$wp_customize,
						$color_option_name,
						array(
							'label'    => $color_option['label'],
							'section'  => 'colors',
							'priority' => 10,
						)
					)
				);

			}

			// Update background color with postMessage, so inline CSS output is updated as well.
			$wp_customize->get_setting( 'background_color' )->transport = 'postMessage';

			/**
			 * Theme Options
			 */

			$wp_customize->add_section(
				'options',
				array(
					'title'       => __( 'Theme Options', 'twentytwenty' ),
					'priority'    => 40,
					'capability'  => 'edit_theme_options',
					'description' => __( 'Settings for this theme.', 'twentytwenty' ),
				)
			);

			/* Enable Header Search --------- */

			$wp_customize->add_setting(
				'enable_header_search',
				array(
					'capability'        => 'edit_theme_options',
					'default'           => false,
					'sanitize_callback' => array( __CLASS__, 'sanitize_checkbox' ),
				)
			);

			$wp_customize->add_control(
				'enable_header_search',
				array(
					'type'        => 'checkbox',
					'section'     => 'options',
					'priority'    => 10,
					'label'       => __( 'Show search in header', 'twentytwenty' ),
					'description' => __( 'Uncheck to hide the search in the header.', 'twentytwenty' ),
				)
			);

			/* Display full content or excerpts on the blog and archives --------- */

			$wp_customize->add_setting(
				'blog_content',
				array(
					'capability'        => 'edit_theme_options',
					'default'           => 'full',
					'sanitize_callback' => array( __CLASS__, 'sanitize_select' ),
				)
			);

			$wp_customize->add_control(
				'blog_content',
				array(
					'type'        => 'radio',
					'section'     => 'options',
					'priority'    => 10,
					'label'       => __( 'On archive pages, posts show:', 'twentytwenty' ),
					'description' => __( 'Search results always show the summary.', 'twentytwenty' ),
					'choices'     => array(
						'full'    => __( 'Full text', 'twentytwenty' ),
						'summary' => __( 'Summary', 'twentytwenty' ),
					),
				)
			);

			/**
			 * Template: Cover Template.
			 */
			$wp_customize->add_section(
				'cover_template_options',
				array(
					'title'       => __( 'Cover Template', 'twentytwenty' ),
					'capability'  => 'edit_theme_options',
					'description' => __( 'Settings for the "Cover Template" page template.', 'twentytwenty' ),
					'priority'    => 42,
				)
			);

			/* Overlay Fixed Background ------ */

			$wp_customize->add_setting(
				'cover_template_fixed_background',
				array(
					'capability'        => 'edit_theme_options',
					'default'           => true,
					'sanitize_callback' => array( __CLASS__, 'sanitize_checkbox' ),
				)
			);

			$wp_customize->add_control(
				'cover_template_fixed_background',
				array(
					'type'        => 'checkbox',
					'section'     => 'cover_template_options',
					'label'       => __( 'Fixed Background Image', 'twentytwenty' ),
					'description' => __( 'Creates a parallax effect when the visitor scrolls.', 'twentytwenty' ),
				)
			);

			/* Separator --------------------- */

			$wp_customize->add_setting(
				'cover_template_separator_1',
				array(
					'sanitize_callback' => 'wp_filter_nohtml_kses',
				)
			);

			$wp_customize->add_control(
				new TwentyTwenty_Separator_Control(
					$wp_customize,
					'cover_template_separator_1',
					array(
						'section' => 'cover_template_options',
					)
				)
			);

			/* Overlay Background Color ------ */

			$wp_customize->add_setting(
				'cover_template_overlay_background_color',
				array(
					'default'           => twentytwenty_get_color_for_area( 'content', 'accent' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'cover_template_overlay_background_color',
					array(
						'label'       => __( 'Image Overlay Background Color', 'twentytwenty' ),
						'description' => __( 'The color used for the featured image overlay. Defaults to the accent color.', 'twentytwenty' ),
						'section'     => 'cover_template_options',
					)
				)
			);

			/* Overlay Text Color ------------ */

			$wp_customize->add_setting(
				'cover_template_overlay_text_color',
				array(
					'default'           => '#ffffff',
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'cover_template_overlay_text_color',
					array(
						'label'       => __( 'Image Overlay Text Color', 'twentytwenty' ),
						'description' => __( 'The color used for the text in the featured image overlay.', 'twentytwenty' ),
						'section'     => 'cover_template_options',
					)
				)
			);

			/* Overlay Blend Mode ------------ */

			$wp_customize->add_setting(
				'cover_template_overlay_blend_mode',
				array(
					'default'           => 'multiply',
					'sanitize_callback' => array( __CLASS__, 'sanitize_select' ),
				)
			);

			$wp_customize->add_control(
				'cover_template_overlay_blend_mode',
				array(
					'label'       => __( 'Image Overlay Blend Mode', 'twentytwenty' ),
					'description' => __( 'How the overlay color will blend with the image. Some browsers, like Internet Explorer and Edge, only support the "Normal" mode.', 'twentytwenty' ),
					'section'     => 'cover_template_options',
					'type'        => 'select',
					'choices'     => array(
						'normal'      => __( 'Normal', 'twentytwenty' ),
						'multiply'    => __( 'Multiply', 'twentytwenty' ),
						'screen'      => __( 'Screen', 'twentytwenty' ),
						'overlay'     => __( 'Overlay', 'twentytwenty' ),
						'darken'      => __( 'Darken', 'twentytwenty' ),
						'lighten'     => __( 'Lighten', 'twentytwenty' ),
						'color-dodge' => __( 'Color Dodge', 'twentytwenty' ),
						'color-burn'  => __( 'Color Burn', 'twentytwenty' ),
						'hard-light'  => __( 'Hard Light', 'twentytwenty' ),
						'soft-light'  => __( 'Soft Light', 'twentytwenty' ),
						'difference'  => __( 'Difference', 'twentytwenty' ),
						'exclusion'   => __( 'Exclusion', 'twentytwenty' ),
						'hue'         => __( 'Hue', 'twentytwenty' ),
						'saturation'  => __( 'Saturation', 'twentytwenty' ),
						'color'       => __( 'Color', 'twentytwenty' ),
						'luminosity'  => __( 'Luminosity', 'twentytwenty' ),
					),
				)
			);

			/* Overlay Color Opacity --------- */

			$wp_customize->add_setting(
				'cover_template_overlay_opacity',
				array(
					'default'           => '80',
					'sanitize_callback' => array( __CLASS__, 'sanitize_select' ),
				)
			);

			$wp_customize->add_control(
				'cover_template_overlay_opacity',
				array(
					'label'       => __( 'Image Overlay Opacity', 'twentytwenty' ),
					'description' => __( 'Make sure that the value is high enough that the text is readable.', 'twentytwenty' ),
					'section'     => 'cover_template_options',
					'type'        => 'select',
					'choices'     => array(
						'0'   => __( '0%', 'twentytwenty' ),
						'10'  => __( '10%', 'twentytwenty' ),
						'20'  => __( '20%', 'twentytwenty' ),
						'30'  => __( '30%', 'twentytwenty' ),
						'40'  => __( '40%', 'twentytwenty' ),
						'50'  => __( '50%', 'twentytwenty' ),
						'60'  => __( '60%', 'twentytwenty' ),
						'70'  => __( '70%', 'twentytwenty' ),
						'80'  => __( '80%', 'twentytwenty' ),
						'90'  => __( '90%', 'twentytwenty' ),
						'100' => __( '100%', 'twentytwenty' ),
					),
				)
			);

		}

		/**
		 * Sanitization callback for the "accent_accessible_colors" setting.
		 *
		 * @static
		 * @access public
		 * @since 1.0.0
		 * @param array $value The value we want to sanitize.
		 * @return array       Returns sanitized value. Each item in the array gets sanitized separately.
		 */
		public static function sanitize_accent_accessible_colors( $value ) {

			// Make sure the value is an array. Do not typecast, use empty array as fallback.
			$value = is_array( $value ) ? $value : array();

			// Loop values.
			foreach ( $value as $area => $values ) {
				foreach ( $values as $context => $color_val ) {
					$value[ $area ][ $context ] = sanitize_hex_color( $color_val );
				}
			}

			return $value;
		}

		/**
		 * Return the sitewide color options included.
		 * Note: These values are shared between the block editor styles and the customizer,
		 * and abstracted to this function.
		 */
		public static function get_color_options() {
			return apply_filters( 'twentytwenty_accent_color_options', array() );
		}

		/**
		 * Sanitize select.
		 *
		 * @param string $input The input from the setting.
		 * @param object $setting The selected setting.
		 */
		public static function sanitize_select( $input, $setting ) {
			$input   = sanitize_key( $input );
			$choices = $setting->manager->get_control( $setting->id )->choices;
			return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
		}

		/**
		 * Sanitize boolean for checkbox.
		 *
		 * @param bool $checked Wethere or not a blox is checked.
		 */
		public static function sanitize_checkbox( $checked ) {
			return ( ( isset( $checked ) && true === $checked ) ? true : false );
		}

	}

	// Setup the Theme Customizer settings and controls.
	add_action( 'customize_register', array( 'TwentyTwenty_Customize', 'register' ) );

}

/**
 * PARTIAL REFRESH FUNCTIONS
 * */
if ( ! function_exists( 'twentytwenty_customize_partial_blogname' ) ) {
	/**
	 * Render the site title for the selective refresh partial.
	 */
	function twentytwenty_customize_partial_blogname() {
		bloginfo( 'name' );
	}
}

if ( ! function_exists( 'twentytwenty_customize_partial_blogdescription' ) ) {
	/**
	 * Render the site description for the selective refresh partial.
	 */
	function twentytwenty_customize_partial_blogdescription() {
		bloginfo( 'description' );
	}
}

if ( ! function_exists( 'twentytwenty_customize_partial_site_logo' ) ) {
	/**
	 * Render the site logo for the selective refresh partial.
	 *
	 * Doing it this way so we don't have issues with `render_callback`'s arguments.
	 */
	function twentytwenty_customize_partial_site_logo() {
		twentytwenty_site_logo();
	}
}

<?php
/**
 * Customizer settings for this theme.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since 1.0.0
 */

if ( ! class_exists( 'Twenty_Twenty_One_Customize' ) ) {
	/**
	 * Customizer Settings.
	 *
	 * @since 1.0.0
	 */
	class Twenty_Twenty_One_Customize {

		/**
		 * Constructor. Instantiate the object.
		 *
		 * @access public
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'customize_register', array( $this, 'register' ) );
		}

		/**
		 * Register customizer options.
		 *
		 * @access public
		 *
		 * @since 1.0.0
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 *
		 * @return void
		 */
		public function register( $wp_customize ) {

			// Change site-title & description to postMessage.
			$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage'; // @phpstan-ignore-line. Assume that this setting exists.
			$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage'; // @phpstan-ignore-line. Assume that this setting exists.

			// Add partial for blogname.
			$wp_customize->selective_refresh->add_partial(
				'blogname',
				array(
					'selector'        => '.site-title',
					'render_callback' => array( $this, 'partial_blogname' ),
				)
			);

			// Add partial for blogdescription.
			$wp_customize->selective_refresh->add_partial(
				'blogdescription',
				array(
					'selector'        => '.site-description',
					'render_callback' => array( $this, 'partial_blogdescription' ),
				)
			);

			// Add "display_title_and_tagline" setting for displaying the site-title & tagline.
			$wp_customize->add_setting(
				'display_title_and_tagline',
				array(
					'capability'        => 'edit_theme_options',
					'default'           => true,
					'sanitize_callback' => array( __CLASS__, 'sanitize_checkbox' ),
				)
			);

			// Add control for the "display_title_and_tagline" setting.
			$wp_customize->add_control(
				'display_title_and_tagline',
				array(
					'type'    => 'checkbox',
					'section' => 'title_tagline',
					'label'   => esc_html__( 'Display Site Title & Tagline', 'twentytwentyone' ),
				)
			);

			/**
			 * Add excerpt or full text selector to customizer
			 */
			$wp_customize->add_section(
				'excerpt_settings',
				array(
					'title'    => esc_html__( 'Excerpt Settings', 'twentytwentyone' ),
					'priority' => 120,
				)
			);

			$wp_customize->add_setting(
				'display_excerpt_or_full_post',
				array(
					'capability'        => 'edit_theme_options',
					'default'           => 'excerpt',
					'sanitize_callback' => function( $value ) {
						return 'excerpt' === $value || 'full' === $value ? $value : 'excerpt';
					},
				)
			);

			$wp_customize->add_control(
				'display_excerpt_or_full_post',
				array(
					'type'    => 'radio',
					'section' => 'excerpt_settings',
					'label'   => esc_html__( 'On archive pages, posts show:', 'twentytwentyone' ),
					'choices' => array(
						'excerpt' => esc_html__( 'Summary', 'twentytwentyone' ),
						'full'    => esc_html__( 'Full text', 'twentytwentyone' ),
					),
				)
			);

			// Background color.
			// Include the custom control class.
			include_once get_theme_file_path( 'classes/class-twenty-twenty-one-customize-color-control.php' ); // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

			// Register the custom control.
			$wp_customize->register_control_type( 'Twenty_Twenty_One_Customize_Color_Control' );

			// Get the palette from theme-supports.
			$palette = get_theme_support( 'editor-color-palette' );

			// Build the colors array from theme-support.
			$colors = array();
			if ( isset( $palette[0] ) && is_array( $palette[0] ) ) {
				foreach ( $palette[0] as $palette_color ) {
					$colors[] = $palette_color['color'];
				}
			}

			// Add the control. Overrides the default background-color control.
			$wp_customize->add_control(
				new Twenty_Twenty_One_Customize_Color_Control(
					$wp_customize,
					'background_color',
					array(
						'label'   => esc_html__( 'Background Control', 'twentytwentyone' ),
						'section' => 'colors',
						'palette' => $colors,
					)
				)
			);
		}

		/**
		 * Sanitize boolean for checkbox.
		 *
		 * @access public
		 *
		 * @since 1.0.0
		 *
		 * @param bool $checked Whether or not a box is checked.
		 *
		 * @return bool
		 */
		public static function sanitize_checkbox( $checked = null ) {
			return (bool) isset( $checked ) && true === $checked;
		}

		/**
		 * Render the site title for the selective refresh partial.
		 *
		 * @access public
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function partial_blogname() {
			bloginfo( 'name' );
		}

		/**
		 * Render the site tagline for the selective refresh partial.
		 *
		 * @access public
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function partial_blogdescription() {
			bloginfo( 'description' );
		}
	}
}

<?php
/**
 * Twenty Twelve Theme Options
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

class Twenty_Twelve_Options {
	/**
	 * The option value in the database will be based on get_stylesheet()
	 * so child themes don't share the parent theme's option value.
	 *
	 * @access public
	 * @var string
	 */
	public $option_key = 'twentytwelve_theme_options';

	/**
	 * Holds our options.
	 *
	 * @access public
	 * @var array
	 */
	public $options = array();

	/**
	 * Constructor.
	 *
	 * @access public
	 *
	 * @return Twenty_Twelve_Options
	 */
	public function __construct() {
		// Set option key based on get_stylesheet()
		if ( 'twentytwelve' != get_stylesheet() )
			$this->option_key = get_stylesheet() . '_theme_options';

		add_action( 'admin_init',             array( $this, 'options_init'         ) );
		add_action( 'admin_menu',             array( $this, 'add_page'             ) );
		add_action( 'customize_register',     array( $this, 'customize_register'   ) );
		add_action( 'customize_preview_init', array( $this, 'customize_preview_js' ) );
	}

	/**
	 * Registers the form setting for our options array.
	 *
	 * This function is attached to the admin_init action hook.
	 *
	 * This call to register_setting() registers a validation callback, validate(),
	 * which is used when the option is saved, to ensure that our option values are properly
	 * formatted, and safe.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function options_init() {
		// Load our options for use in any method.
		$this->options = $this->get_theme_options();

		// Register our option group.
		register_setting(
			'twentytwelve_options',    // Options group, see settings_fields() call in render_page()
			$this->option_key,         // Database option, see get_theme_options()
			array( $this, 'validate' ) // The sanitization callback, see validate()
		);

		// Register our settings field group.
		add_settings_section(
			'general',        // Unique identifier for the settings section
			'',               // Section title (we don't want one)
			'__return_false', // Section callback (we don't want anything)
			'theme_options'   // Menu slug, used to uniquely identify the page; see add_page()
		);

		// Register our individual settings fields.
		add_settings_field(
			'enable_fonts',                                // Unique identifier for the field for this section
			__( 'Enable Web Fonts', 'twentytwelve' ),      // Setting field label
			array( $this, 'settings_field_enable_fonts' ), // Function that renders the settings field
			'theme_options',                               // Menu slug, used to uniquely identify the page; see add_page()
			'general'                                      // Settings section. Same as the first argument in the add_settings_section() above
		);
	}

	/**
	 * Adds our theme options page to the admin menu.
	 *
	 * This function is attached to the admin_menu action hook.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function add_page() {
		$theme_page = add_theme_page(
			__( 'Theme Options', 'twentytwelve' ), // Name of page
			__( 'Theme Options', 'twentytwelve' ), // Label in menu
			'edit_theme_options',                  // Capability required
			'theme_options',                       // Menu slug, used to uniquely identify the page
			array( $this, 'render_page' )          // Function that renders the options page
		);
	}

	/**
	 * Returns the default options.
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function get_default_theme_options() {
		$default_theme_options = array(
			'enable_fonts' => false,
		);

		return apply_filters( 'twentytwelve_default_theme_options', $default_theme_options );
	}

	/**
	 * Returns the options array.
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function get_theme_options() {
		return get_option( $this->option_key, $this->get_default_theme_options() );
	}

	/**
	 * Renders the enable fonts checkbox setting field.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function settings_field_enable_fonts() {
		$options = $this->options;
		?>
		<label for="enable-fonts">
			<input type="checkbox" name="<?php echo $this->option_key; ?>[enable_fonts]" id="enable-fonts" <?php checked( $options['enable_fonts'] ); ?> />
			<?php _e( 'Enable the Open Sans typeface.', 'twentytwelve' );  ?>
		</label>
		<?php
	}

	/**
	 * Displays the theme options page.
	 *
	 * @uses get_current_theme() for back compat, fallback for < 3.4
	 * @access public
	 *
	 * @return void
	 */
	public function render_page() {
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<?php $theme_name = function_exists( 'wp_get_theme' ) ? wp_get_theme() : get_current_theme(); ?>
			<h2><?php printf( __( '%s Theme Options', 'twentytwelve' ), $theme_name ); ?></h2>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'twentytwelve_options' );
					do_settings_sections( 'theme_options' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Sanitizes and validates form input.
	 *
	 * @see options_init()
	 * @access public
	 * @param array $input
	 *
	 * @return array The validated data.
	 */
	public function validate( $input ) {
		$output = $defaults = $this->get_default_theme_options();

		// The enable fonts checkbox should boolean true or false
		if ( ! isset( $input['enable_fonts'] ) )
			$input['enable_fonts'] = false;
		$output['enable_fonts'] = ( false != $input['enable_fonts'] ? true : false );

		return apply_filters( 'twentytwelve_options_validate', $output, $input, $defaults );
	}

	/**
	 * Implements Twenty Twelve theme options into Theme Customizer.
	 *
	 * @since Twenty Twelve 1.0
	 * @access public
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 *
	 * @return void
	 */
	public function customize_register( $wp_customize ) {

		// Add postMessage support for site title and tagline
		$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

		// Enable Web Fonts
		$wp_customize->add_section( $this->option_key . '_enable_fonts', array(
			'title'    => __( 'Fonts', 'twentytwelve' ),
			'priority' => 35,
		) );

		$defaults = $this->get_default_theme_options();

		$wp_customize->add_setting( $this->option_key . '[enable_fonts]', array(
			'default'    => $defaults['enable_fonts'],
			'type'       => 'option',
			'capability' => 'edit_theme_options',
			'transport'  => 'postMessage',
		) );

		$wp_customize->add_control( $this->option_key . '_enable_fonts', array(
			'label'    => __( 'Enable the Open Sans typeface.', 'twentytwelve' ),
			'section'  => $this->option_key . '_enable_fonts',
			'settings' => $this->option_key . '[enable_fonts]',
			'type'     => 'checkbox',
		) );
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 *
	 * @since Twenty Twelve 1.0
	 * @access public
	 *
	 * @return void
	 */
	public function customize_preview_js() {
		wp_enqueue_script( 'twentytwelve-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), '20120802', true );
		wp_localize_script( 'twentytwelve-customizer', 'twentytwelve_customizer', array(
			'option_key' => $this->option_key,
			'link'       => $this->custom_fonts_url(),
		) );
	}

	/**
	 * Creates path to load fonts CSS file with correct protocol.
	 *
	 * @since Twenty Twelve 1.0
	 * @access public
	 *
	 * @return string Path to load fonts CSS.
	 */
	public function custom_fonts_url() {
		$protocol = is_ssl() ? 'https' : 'http';
		return $protocol . '://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700';
	}
}
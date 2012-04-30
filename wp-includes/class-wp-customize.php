<?php
/**
 * Customize
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */

final class WP_Customize {
	protected $theme;
	protected $original_stylesheet;

	protected $previewing = false;

	protected $settings = array();
	protected $sections = array();
	protected $controls = array();

	protected $customized;

	private $_post_values;

	/**
	 * Constructor.
	 *
	 * @since 3.4.0
	 */
	public function __construct() {
		require( ABSPATH . WPINC . '/class-wp-customize-setting.php' );
		require( ABSPATH . WPINC . '/class-wp-customize-section.php' );
		require( ABSPATH . WPINC . '/class-wp-customize-control.php' );

		add_action( 'setup_theme',  array( $this, 'setup_theme' ) );
		add_action( 'admin_init',   array( $this, 'admin_init' ) );
		add_action( 'wp_loaded',    array( $this, 'wp_loaded' ) );

		add_action( 'wp_ajax_customize_save', array( $this, 'save' ) );

		add_action( 'customize_register',                 array( $this, 'register_controls' ) );
		add_action( 'customize_controls_init',            array( $this, 'prepare_controls' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ) );
	}

	/**
	 * Update theme modifications for the current theme.
	 * Note: Candidate core function.
	 * http://core.trac.wordpress.org/ticket/20091
	 *
	 * @since 3.4.0
	 *
	 * @param array $mods Theme modifications.
	 */
	public function set_theme_mods( $mods ) {
		$current = get_theme_mods();

		$mods = wp_parse_args( $mods, $current );

		$theme = get_stylesheet();
		update_option( "theme_mods_$theme", $mods );
	}

	/**
	 * Start preview and customize theme.
	 *
	 * Check if customize query variable exist. Init filters to filter the current theme.
	 *
	 * @since 3.4.0
	 */
	public function setup_theme() {
		if ( ! isset( $_REQUEST['customize'] ) || 'on' != $_REQUEST['customize'] )
			return;

		$this->start_previewing_theme();
		show_admin_bar( false );
	}

	/**
	 * Start previewing the selected theme.
	 *
	 * Adds filters to change the current theme.
	 *
	 * @since 3.4.0
	 */
	public function start_previewing_theme() {
		if ( $this->is_preview() || false === $this->theme || ( $this->theme && ! $this->theme->exists() ) )
			return;

		// Initialize $theme and $original_stylesheet if they do not yet exist.
		if ( ! isset( $this->theme ) ) {
			$this->theme = wp_get_theme( $_REQUEST['theme'] );
			if ( ! $this->theme->exists() ) {
				$this->theme = false;
				return;
			}
		}

		$this->original_stylesheet = get_stylesheet();

		$this->previewing = true;

		add_filter( 'template', array( $this, 'get_template' ) );
		add_filter( 'stylesheet', array( $this, 'get_stylesheet' ) );
		add_filter( 'pre_option_current_theme', array( $this, 'current_theme' ) );

		// @link: http://core.trac.wordpress.org/ticket/20027
		add_filter( 'pre_option_stylesheet', array( $this, 'get_stylesheet' ) );
		add_filter( 'pre_option_template', array( $this, 'get_template' ) );

		// Handle custom theme roots.
		add_filter( 'pre_option_stylesheet_root', array( $this, 'get_stylesheet_root' ) );
		add_filter( 'pre_option_template_root', array( $this, 'get_template_root' ) );

		do_action( 'start_previewing_theme' );
	}

	/**
	 * Stop previewing the selected theme.
	 *
	 * Removes filters to change the current theme.
	 *
	 * @since 3.4.0
	 */
	public function stop_previewing_theme() {
		if ( ! $this->is_preview() )
			return;

		$this->previewing = false;

		remove_filter( 'template', array( $this, 'get_template' ) );
		remove_filter( 'stylesheet', array( $this, 'get_stylesheet' ) );
		remove_filter( 'pre_option_current_theme', array( $this, 'current_theme' ) );

		// @link: http://core.trac.wordpress.org/ticket/20027
		remove_filter( 'pre_option_stylesheet', array( $this, 'get_stylesheet' ) );
		remove_filter( 'pre_option_template', array( $this, 'get_template' ) );

		// Handle custom theme roots.
		remove_filter( 'pre_option_stylesheet_root', array( $this, 'get_stylesheet_root' ) );
		remove_filter( 'pre_option_template_root', array( $this, 'get_template_root' ) );

		do_action( 'stop_previewing_theme' );
	}

	/**
	 * Register styles/scripts and initialize the preview of each setting
	 *
	 * @since 3.4.0
	 */
	public function wp_loaded() {
		do_action( 'customize_register' );

		if ( $this->is_preview() && ! is_admin() )
			$this->customize_preview_init();
	}

	/**
	 * Decode the $_POST attribute used to override the WP_Customize_Setting values.
	 *
	 * @since 3.4.0
	 */
	public function post_value( $setting ) {
		if ( ! isset( $this->_post_values ) ) {
			if ( isset( $_POST['customized'] ) )
				$this->_post_values = json_decode( stripslashes( $_POST['customized'] ), true );
			else
				$this->_post_values = false;
		}

		if ( isset( $this->_post_values[ $setting->id ] ) )
			return $setting->sanitize( $this->_post_values[ $setting->id ] );
	}


	/**
	 * Print javascript settings.
	 *
	 * @since 3.4.0
	 */
	public function customize_preview_init() {
		$this->prepare_controls();

		wp_enqueue_script( 'customize-preview' );
		add_action( 'wp_footer', array( $this, 'customize_preview_settings' ), 20 );

		foreach ( $this->settings as $setting ) {
			$setting->preview();
		}

		do_action( 'customize_preview_init' );
	}


	/**
	 * Print javascript settings.
	 *
	 * @since 3.4.0
	 */
	public function customize_preview_settings() {
		$settings = array(
			// @todo: Perhaps grab the URL via $_POST?
			'parent' => esc_url( admin_url( 'themes.php' ) ),
			'values' => array(),
		);

		foreach ( $this->settings as $id => $setting ) {
			$settings['values'][ $id ] = $setting->value();
		}

		?>
		<script type="text/javascript">
			(function() {
				if ( typeof wp === 'undefined' || ! wp.customize )
					return;

				wp.customize.settings = <?php echo json_encode( $settings ); ?>;
			})();
		</script>
		<?php
	}

	/**
	 * Is it a theme preview?
	 *
	 * @since 3.4.0
	 *
	 * @return bool True if it's a preview, false if not.
	 */
	public function is_preview() {
		return (bool) $this->previewing;
	}

	/**
	 * Retrieve the template name of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return string Template name.
	 */
	public function get_template() {
		return $this->theme->get_template();
	}

	/**
	 * Retrieve the stylesheet name of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return string Stylesheet name.
	 */
	public function get_stylesheet() {
		return $this->theme->get_stylesheet();
	}

	/**
	 * Retrieve the template root of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return string Theme root.
	 */
	public function get_template_root() {
		return get_raw_theme_root( $this->get_template(), true );
	}

	/**
	 * Retrieve the stylesheet root of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return string Theme root.
	 */
	public function get_stylesheet_root() {
		return get_raw_theme_root( $this->get_stylesheet(), true );
	}

	/**
	 * Filter the current theme and return the name of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return string Theme name.
	 */
	public function current_theme( $current_theme ) {
		return $this->theme->display('Name');
	}

	/**
	 * Trigger save action and load customize controls.
	 *
	 * @since 3.4.0
	 */
	public function admin_init() {
		if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) )
			return;

		if ( ! isset( $_GET['customize'] ) || 'on' != $_GET['customize'] )
			return;

		if ( empty( $_GET['theme'] ) )
			return;

		if ( ! $this->is_preview() )
			return;

		if ( ! current_user_can( 'edit_theme_options' ) )
			return;

		include( ABSPATH . WPINC . '/customize-controls.php' );

		die;
	}

	/**
	 * Switch the theme and trigger the save action of each setting.
	 *
	 * @since 3.4.0
	 */
	public function save() {
		if ( ! $this->is_preview() )
			die;

		check_ajax_referer( 'customize_controls', 'nonce' );

		// Do we have to switch themes?
		if ( $this->get_stylesheet() != $this->original_stylesheet ) {
			if ( ! current_user_can( 'switch_themes' ) )
				die;

			// Temporarily stop previewing the theme to allow switch_themes()
			// to operate properly.
			$this->stop_previewing_theme();
			switch_theme( $this->get_template(), $this->get_stylesheet() );
			$this->start_previewing_theme();
		}

		do_action( 'customize_save' );

		foreach ( $this->settings as $setting ) {
			$setting->save();
		}

		add_action( 'admin_notices', array( $this, '_save_feedback' ) );

		die;
	}

	/**
	 * Show an admin notice after settings are saved.
	 *
	 * @since 3.4.0
	 */
	public function _save_feedback() {
		?>
		<div class="updated"><p><?php printf( __( 'Settings saved and theme activated. <a href="%s">Visit site</a>.' ), home_url( '/' ) ); ?></p></div>
		<?php
	}

	/**
	 * Add a customize setting.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id A specific ID of the setting. Can be a
	 *                   theme mod or option name.
	 * @param array $args Setting arguments.
	 */
	public function add_setting( $id, $args = array() ) {
		if ( is_a( $id, 'WP_Customize_Setting' ) )
			$setting = $id;
		else
			$setting = new WP_Customize_Setting( $this, $id, $args );

		$this->settings[ $setting->id ] = $setting;
	}

	/**
	 * Retrieve a customize setting.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id A specific ID of the setting.
	 * @return object The settings object.
	 */
	public function get_setting( $id ) {
		if ( isset( $this->settings[ $id ] ) )
			return $this->settings[ $id ];
	}

	/**
	 * Remove a customize setting.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id A specific ID of the setting.
	 */
	public function remove_setting( $id ) {
		unset( $this->settings[ $id ] );
	}

	/**
	 * Add a customize section.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id A specific ID of the section.
	 * @param array $args Section arguments.
	 */
	public function add_section( $id, $args = array() ) {
		if ( is_a( $id, 'WP_Customize_Section' ) )
			$section = $id;
		else
			$section = new WP_Customize_Section( $this, $id, $args );

		$this->sections[ $section->id ] = $section;
	}

	/**
	 * Retrieve a customize section.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id A specific ID of the section.
	 * @return object The section object.
	 */
	public function get_section( $id ) {
		if ( isset( $this->sections[ $id ] ) )
			return $this->sections[ $id ];
	}

	/**
	 * Remove a customize section.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id A specific ID of the section.
	 */
	public function remove_section( $id ) {
		unset( $this->sections[ $id ] );
	}

	/**
	 * Add a customize control.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id A specific ID of the control.
	 * @param array $args Setting arguments.
	 */
	public function add_control( $id, $args = array() ) {
		if ( is_a( $id, 'WP_Customize_Control' ) )
			$control = $id;
		else
			$control = new WP_Customize_Control( $this, $id, $args );

		$this->controls[ $control->id ] = $control;
	}

	/**
	 * Retrieve a customize control.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id A specific ID of the control.
	 * @return object The settings object.
	 */
	public function get_control( $id ) {
		if ( isset( $this->controls[ $id ] ) )
			return $this->controls[ $id ];
	}

	/**
	 * Remove a customize setting.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id A specific ID of the control.
	 */
	public function remove_control( $id ) {
		unset( $this->controls[ $id ] );
	}

	/**
	 * Helper function to compare two objects by priority.
	 *
	 * @since 3.4.0
	 *
	 * @param object $a Object A.
	 * @param object $b Object B.
	 */
	protected final function _cmp_priority( $a, $b ) {
		$ap = $a->priority;
		$bp = $b->priority;

		if ( $ap == $bp )
			return 0;
		return ( $ap > $bp ) ? 1 : -1;
	}

	/**
	 * Prepare settings and sections.
	 *
	 * @since 3.4.0
	 */
	public function prepare_controls() {
		// Prepare controls
		// Reversing makes uasort sort by time added when conflicts occur.

		$this->controls = array_reverse( $this->controls );
		$controls = array();

		foreach ( $this->controls as $id => $control ) {
			if ( ! isset( $this->sections[ $control->section ] ) || ! $control->check_capabilities() )
				continue;

			$this->sections[ $control->section ]->controls[] = $control;
			$controls[ $id ] = $control;
		}
		$this->controls = $controls;

		// Prepare sections
		$this->sections = array_reverse( $this->sections );
		uasort( $this->sections, array( $this, '_cmp_priority' ) );
		$sections = array();

		foreach ( $this->sections as $section ) {
			if ( ! $section->check_capabilities() || ! $section->controls )
				continue;

			usort( $section->controls, array( $this, '_cmp_priority' ) );
			$sections[] = $section;
		}
		$this->sections = $sections;
	}

	/**
	 * Enqueue scripts for customize controls.
	 *
	 * @since 3.4.0
	 */
	public function enqueue_control_scripts() {
		foreach ( $this->controls as $control ) {
			$control->enqueue();
		}
	}

	/**
	 * Register some default controls.
	 *
	 * @since 3.4.0
	 */
	public function register_controls() {

		/* Custom Header */

		$this->add_section( 'header', array(
			'title'          => __( 'Header' ),
			'theme_supports' => 'custom-header',
			'priority'       => 20,
		) );

		$this->add_setting( 'header_textcolor', array(
			// @todo: replace with a new accept() setting method
			// 'sanitize_callback' => 'sanitize_hexcolor',
			'theme_supports' => array( 'custom-header', 'header-text' ),
			'default'        => get_theme_support( 'custom-header', 'default-text-color' ),
		) );

		$this->add_control( 'display_header_text', array(
			'settings' => 'header_textcolor',
			'label'    => __( 'Display Header Text' ),
			'section'  => 'header',
			'type'     => 'checkbox',
		) );

		$this->add_control( new WP_Customize_Color_Control( $this, 'header_textcolor', array(
			'label'   => __( 'Text Color' ),
			'section' => 'header',
		) ) );

		// Input type: checkbox
		// With custom value
		$this->add_setting( 'header_image', array(
			'default'        => get_theme_support( 'custom-header', 'default-image' ),
			'theme_supports' => 'custom-header',
		) );

		$this->add_control( new WP_Customize_Header_Image_Control( $this ) );

		/* Custom Background */

		$this->add_section( 'background', array(
			'title'          => __( 'Background' ),
			'theme_supports' => 'custom-background',
			'priority'       => 30,
		) );

		// Input type: Color
		// With sanitize_callback
		$this->add_setting( 'background_color', array(
			'default'           => get_theme_support( 'custom-background', 'default-color' ),
			'sanitize_callback' => 'sanitize_hexcolor',
			'theme_supports'    => 'custom-background',
			'transport'         => 'postMessage',
		) );

		$this->add_control( new WP_Customize_Color_Control( $this, 'background_color', array(
			'label'   => __( 'Background Color' ),
			'section' => 'background',
		) ) );

		$this->add_setting( 'background_image', array(
			'default'        => get_theme_support( 'custom-background', 'default-image' ),
			'theme_supports' => 'custom-background',
		) );

		$this->add_control( new WP_Customize_Image_Control( $this, 'background_image', array(
			'label'          => __( 'Background Image' ),
			'section'        => 'background',
			'context'        => 'custom-background',
		) ) );

		$this->add_setting( 'background_repeat', array(
			'default'        => 'repeat',
			'theme_supports' => 'custom-background',
		) );

		$this->add_control( 'background_repeat', array(
			'label'      => __( 'Background Repeat' ),
			'section'    => 'background',
			'type'       => 'radio',
			'choices'    => array(
				'no-repeat'  => __('No Repeat'),
				'repeat'     => __('Tile'),
				'repeat-x'   => __('Tile Horizontally'),
				'repeat-y'   => __('Tile Vertically'),
			),
		) );

		$this->add_setting( 'background_position_x', array(
			'default'        => 'left',
			'theme_supports' => 'custom-background',
		) );

		$this->add_control( 'background_position_x', array(
			'label'      => __( 'Background Position' ),
			'section'    => 'background',
			'type'       => 'radio',
			'choices'    => array(
				'left'       => __('Left'),
				'center'     => __('Center'),
				'right'      => __('Right'),
			),
		) );

		$this->add_setting( 'background_attachment', array(
			'default'        => 'fixed',
			'theme_supports' => 'custom-background',
		) );

		$this->add_control( 'background_attachment', array(
			'label'      => __( 'Background Attachment' ),
			'section'    => 'background',
			'type'       => 'radio',
			'choices'    => array(
				'fixed'      => __('Fixed'),
				'scroll'     => __('Scroll'),
			),
		) );

		/* Nav Menus */

		$locations      = get_registered_nav_menus();
		$menus          = wp_get_nav_menus();
		$menu_locations = get_nav_menu_locations();
		$num_locations  = count( array_keys( $locations ) );

		$this->add_section( 'nav', array(
			'title'          => __( 'Navigation' ),
			'theme_supports' => 'menus',
			'priority'       => 40,
			'description'    => sprintf( _n('Your theme supports %s menu. Select which menu you would like to use.', 'Your theme supports %s menus. Select which menu appears in each location.', $num_locations ), number_format_i18n( $num_locations ) ) . "\n\n" . __('You can edit your menu content on the Menus screen in the Appearance section.'),
		) );

		if ( $menus ) {
			$choices = array( 0 => __( '&mdash; Select &mdash;' ) );
			foreach ( $menus as $menu ) {
				$truncated_name = wp_html_excerpt( $menu->name, 40 );
				$truncated_name = ( $truncated_name == $menu->name ) ? $menu->name : trim( $truncated_name ) . '&hellip;';
				$choices[ $menu->term_id ] = $truncated_name;
			}

			foreach ( $locations as $location => $description ) {
				$menu_setting_id = "nav_menu_locations[{$location}]";

				$this->add_setting( $menu_setting_id, array(
					'sanitize_callback' => 'absint',
					'theme_supports'    => 'menus',
				) );

				$this->add_control( $menu_setting_id, array(
					'label'   => $description,
					'section' => 'nav',
					'type'    => 'select',
					'choices' => $choices,
				) );
			}
		}

		/* Static Front Page */
		// #WP19627

		$this->add_section( 'static_front_page', array(
			'title'          => __( 'Static Front Page' ),
		//	'theme_supports' => 'static-front-page',
			'priority'       => 50,
			'description'    => __( 'Your theme supports a static front page.' ),
		) );

		$this->add_setting( 'show_on_front', array(
			'default'        => get_option( 'show_on_front' ),
			'capability'     => 'manage_options',
			'type'           => 'option',
		//	'theme_supports' => 'static-front-page',
		) );

		$this->add_control( 'show_on_front', array(
			'label'   => __( 'Front page displays' ),
			'section' => 'static_front_page',
			'type'    => 'radio',
			'choices' => array(
				'posts' => __( 'Your latest posts' ),
				'page'  => __( 'A static page' ),
			),
		) );

		$this->add_setting( 'page_on_front', array(
			'type'       => 'option',
			'capability' => 'manage_options',
		//	'theme_supports' => 'static-front-page',
		) );

		$this->add_control( 'page_on_front', array(
			'label'      => __( 'Front page' ),
			'section'    => 'static_front_page',
			'type'       => 'dropdown-pages',
		) );

		$this->add_setting( 'page_for_posts', array(
			'type'           => 'option',
			'capability'     => 'manage_options',
		//	'theme_supports' => 'static-front-page',
		) );

		$this->add_control( 'page_for_posts', array(
			'label'      => __( 'Posts page' ),
			'section'    => 'static_front_page',
			'type'       => 'dropdown-pages',
		) );

		/* Site Title & Tagline */

		$this->add_section( 'strings', array(
			'title'    => __( 'Site Title & Tagline' ),
			'priority' => 5,
		) );

		$this->add_setting( 'blogname', array(
			'default'    => get_option( 'blogname' ),
			'type'       => 'option',
			'capability' => 'manage_options',
		) );

		$this->add_control( 'blogname', array(
			'label'      => __( 'Site Title' ),
			'section'    => 'strings',
		) );

		$this->add_setting( 'blogdescription', array(
			'default'    => get_option( 'blogdescription' ),
			'type'       => 'option',
			'capability' => 'manage_options',
		) );

		$this->add_control( 'blogdescription', array(
			'label'      => __( 'Tagline' ),
			'section'    => 'strings',
		) );
	}
};

// Callback function for sanitizing a hex color
function sanitize_hexcolor( $color ) {
	$color = preg_replace( '/[^0-9a-fA-F]/', '', $color );

	if ( preg_match('|[A-Fa-f0-9]{3,6}|', $color ) )
		return $color;

	return $color;
}
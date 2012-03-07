<?php
/**
 * Customize
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */

final class WP_Customize {
	protected $template;
	protected $stylesheet;
	protected $previewing = false;

	protected $settings = array();
	protected $sections = array();

	/**
	 * Constructor.
	 *
	 * @since 3.4.0
	 */
	public function __construct() {
		require( ABSPATH . WPINC . '/class-wp-customize-setting.php' );
		require( ABSPATH . WPINC . '/class-wp-customize-section.php' );

		add_action( 'setup_theme',  array( $this, 'setup_theme' ) );
		add_action( 'admin_init',   array( $this, 'admin_init' ) );
		add_action( 'wp_loaded',    array( $this, 'wp_loaded' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );

		add_action( 'customize_previewing',    array( $this, 'customize_previewing' ) );
		add_action( 'customize_register',      array( $this, 'register_controls' ) );
		add_action( 'customize_controls_init', array( $this, 'prepare_controls' ) );
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
	 * Check if customize query variable exist.
	 *
	 * @since 3.4.0
	 */
	public function setup_theme() {
		if ( ! isset( $_REQUEST['customize'] ) || 'on' != $_REQUEST['customize'] )
			return;

		if ( ! $this->set_stylesheet() || isset( $_REQUEST['save'] ) )
			return;

		$this->previewing = true;
		do_action( 'customize_previewing' );
	}

	/**
	 * Init filters to filter theme options.
	 *
	 * @since 3.4.0
	 */
	public function customize_previewing() {
		global $wp_theme_directories;

		show_admin_bar( false );

		add_filter( 'template', array( $this, 'get_template' ) );
		add_filter( 'stylesheet', array( $this, 'get_stylesheet' ) );
		add_filter( 'pre_option_current_theme', array( $this, 'current_theme' ) );

		// @link: http://core.trac.wordpress.org/ticket/20027
		add_filter( 'pre_option_stylesheet', array( $this, 'get_stylesheet' ) );
		add_filter( 'pre_option_template', array( $this, 'get_template' ) );

		// Handle custom theme roots.
		if ( count( $wp_theme_directories ) > 1 ) {
			add_filter( 'pre_option_stylesheet_root', array( $this, 'get_stylesheet_root' ) );
			add_filter( 'pre_option_template_root', array( $this, 'get_template_root' ) );
		}
	}

	/**
	 * Register styles/scripts and initialize the preview of each setting
	 *
	 * @since 3.4.0
	 */
	public function wp_loaded() {
		do_action( 'customize_register' );

		if ( ! $this->is_preview() )
			return;

		wp_enqueue_script( 'customize-preview' );
		add_action( 'wp_footer', array( $this, 'customize_preview_settings' ), 20 );

		foreach ( $this->settings as $setting ) {
			$setting->preview();
		}
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
	 * Set the template name of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return bool|string Template name.
	 */
	public function set_template() {
		if ( ! empty( $this->template ) )
			return $this->template;

		$template = preg_replace('|[^a-z0-9_./-]|i', '', $_REQUEST['template'] );
		if ( validate_file( $template ) )
			return false;

		return $this->template = $template;
	}

	/**
	 * Set the stylesheet name of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return bool|string Stylesheet name.
	 */
	public function set_stylesheet() {
		if ( ! empty( $this->stylesheet ) )
			return $this->stylesheet;

		$this->set_template();
		if ( empty( $this->template ) )
			return false;

		if ( empty( $_REQUEST['stylesheet'] ) ) {
			$stylesheet = $this->template;
		} else {
			$stylesheet = preg_replace( '|[^a-z0-9_./-]|i', '', $_REQUEST['stylesheet'] );
			if ( $stylesheet != $this->template && validate_file( $stylesheet ) )
				return false;
		}
		return $this->stylesheet = $stylesheet;

	}

	/**
	 * Retrieve the template name of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return string Template name.
	 */
	public function get_template() {
		return $this->template;
	}

	/**
	 * Retrieve the stylesheet name of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return string Stylesheet name.
	 */
	public function get_stylesheet() {
		return $this->stylesheet;
	}

	/**
	 * Retrieve the template root of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return string Theme root.
	 */
	public function get_template_root() {
		return get_raw_theme_root( $this->template, true );
	}

	/**
	 * Retrieve the stylesheet root of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return string Theme root.
	 */
	public function get_stylesheet_root() {
		return get_raw_theme_root( $this->stylesheet, true );
	}

	/**
	 * Filter the current theme and return the name of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return string Theme name.
	 */
	public function current_theme( $current_theme ) {
		return wp_get_theme( $this->stylesheet )->get('Name');
	}

	/**
	 * Trigger save action and load customize controls.
	 *
	 * @since 3.4.0
	 */
	public function admin_init() {
		if ( isset( $_REQUEST['save'] ) )
			$this->save();

		wp_enqueue_script( 'customize-loader' );

		if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) )
			return;

		if ( ! isset( $_GET['customize'] ) || 'on' != $_GET['customize'] )
			return;

		if ( ! $this->is_preview() )
			return;

		if ( ! current_user_can( 'edit_theme_options' ) )
			return;

		include( ABSPATH . WPINC . '/customize-controls.php' );

		die;
	}

	/**
	 * Print the customize template.
	 *
	 * @since 3.4.0
	 */
	public function admin_footer() {
		?>
		<div id="customize-container" class="wp-full-overlay">
			<input type="hidden" class="admin-url" value="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" />
			<a href="#" class="close-full-overlay"><?php printf( __( '&larr; Return to %s' ), get_admin_page_title() ); ?></a>
			<a href="#" class="collapse-sidebar button-secondary" title="<?php esc_attr_e('Collapse Sidebar'); ?>"><span></span></a>
		</div>
		<?php
	}

	/**
	 * Switch the theme and trigger the save action of each setting.
	 *
	 * @since 3.4.0
	 */
	public function save() {
		if ( $this->is_preview() )
			return;

		check_admin_referer( 'customize_controls' );

		if ( ! $this->set_stylesheet() )
			return;

		$active_template   = get_template();
		$active_stylesheet = get_stylesheet();

		// Do we have to switch themes?
		if ( $this->get_template() != $active_template || $this->get_stylesheet() != $active_stylesheet ) {
			if ( ! current_user_can( 'switch_themes' ) )
				return;

			switch_theme( $this->get_template(), $this->get_stylesheet() );
		}

		do_action( 'customize_save' );

		foreach ( $this->settings as $setting ) {
			$setting->save();
		}

		add_action( 'admin_notices', array( $this, '_save_feedback' ) );
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
	 * @param string $id An specific ID of the setting. Can be a
	 *                   theme mod or option name.
	 * @param array $args Setting arguments.
	 */
	public function add_setting( $id, $args = array() ) {
		$setting = new WP_Customize_Setting( $id, $args );

		$this->settings[ $setting->id ] = $setting;
	}

	/**
	 * Retrieve a customize setting.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id An specific ID of the setting.
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
	 * @param string $id An specific ID of the setting.
	 */
	public function remove_setting( $id ) {
		unset( $this->settings[ $id ] );
	}

	/**
	 * Add a customize section.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id An specific ID of the section.
	 * @param array $args Section arguments.
	 */
	public function add_section( $id, $args = array() ) {
		$section = new WP_Customize_Section( $id, $args );

		$this->sections[ $section->id ] = $section;
	}

	/**
	 * Retrieve a customize section.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id An specific ID of the section.
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
	 * @param string $id An specific ID of the section.
	 */
	public function remove_section( $id ) {
		unset( $this->sections[ $id ] );
	}

	/**
	 * Helper function to compare two objects by priority.
	 *
	 * @since 3.4.0
	 *
	 * @param object $a Object A.
	 * @param object $b Object B.
	 */
	protected function _cmp_priority( $a, $b ) {
		$ap = $a->priority;
		$bp = $b->priority;

		if ( $ap == $bp )
			return 0;
		return ( $ap > $bp ) ? 1 : -1;
	}

	/**
	 * Prepare settings and sections. Also enqueue needed scripts/styles.
	 *
	 * @since 3.4.0
	 */
	public function prepare_controls() {
		// Reversing makes uasort sort by time added when conflicts occur.

		$this->sections = array_reverse( $this->sections );
		uasort( $this->sections, array( $this, '_cmp_priority' ) );

		$this->settings = array_reverse( $this->settings );
		foreach ( $this->settings as $setting ) {
			if ( ! isset( $this->sections[ $setting->section ] ) )
				continue;

			$this->sections[ $setting->section ]->settings[] = $setting;

			if ( $setting->check_capabilities() )
				$setting->enqueue();
		}

		foreach ( $this->sections as $section ) {
			usort( $section->settings, array( $this, '_cmp_priority' ) );
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
		) );

		$this->add_setting( 'header_textcolor', array(
			'label'             => 'Text Color',
			'section'           => 'header',
			'sanitize_callback' => 'sanitize_hexcolor',
			'control'           => 'color',
			'default'           => defined( 'HEADER_TEXTCOLOR' ) ? HEADER_TEXTCOLOR : ''
		) );

		/*
		$this->add_setting( 'display_header', array(
			'label'   => 'Display Text',
			'section' => 'header',
			'type'    => 'radio',
			'choices' => array(
				'show'  => 'Yes',
				'hide'  => 'No'
			),
			// Showing header text is actually done by setting header_textcolor to 'blank'.
			// @todo: Do some JS magic to make this work (since we'll be hiding the textcolor input).
			'theme_mod' => false,
		) );
		*/

		// Input type: checkbox
		// With custom value
		$this->add_setting( 'header_image', array(
			'label'   => 'Random Image',
			'section' => 'header',
			'control' => 'checkbox',
			 // @todo
			 // not the default, it's the value.
			 // value is saved in get_theme_support( 'custom-header' )[0][ 'random-default' ]
			'default' => 'random-default-image'
		) );

		/* Custom Background */

		$this->add_section( 'background', array(
			'title'          => __( 'Background' ),
			'theme_supports' => 'custom-background',
		) );

		// Input type: Color
		// With sanitize_callback
		$this->add_setting( 'background_color', array(
			'label'             => 'Background Color',
			'section'           => 'background',
			'control'           => 'color',
			'default'           => defined( 'BACKGROUND_COLOR' ) ? BACKGROUND_COLOR : '',
			'sanitize_callback' => 'sanitize_hexcolor'
		) );

		/* Nav Menus */

		$locations      = get_registered_nav_menus();
		$menus          = wp_get_nav_menus();
		$menu_locations = get_nav_menu_locations();
		$num_locations  = count( array_keys( $locations ) );

		$this->add_section( 'nav', array(
			'title'          => __( 'Navigation' ),
			'theme_supports' => 'menus',
			'description'    => sprintf( _n('Your theme supports %s menu. Select which menu you would like to use.', 'Your theme supports %s menus. Select which menu appears in each location.', $num_locations ), number_format_i18n( $num_locations ) ),
		) );

		foreach ( $locations as $location => $description ) {
			$choices = array( 0 => '' );
			foreach ( $menus as $menu ) {
				$truncated_name = wp_html_excerpt( $menu->name, 40 );
				$truncated_name == $menu->name ? $menu->name : trim( $truncated_name ) . '&hellip;';
				$choices[ $menu->term_id ] = $truncated_name;
			}

			$this->add_setting( "nav_menu_locations[{$location}]", array(
				'label'             => $description,
				'theme_supports'    => 'menus', // Todo: Needs also widgets -- array( 'menus', 'widgets' )
				'section'           => 'nav',
				'control'           => 'select',
				'choices'           => $choices,
				'sanitize_callback' => 'absint',
			) );
		}

		/* Static Front Page */
		// #WP19627

		$this->add_section( 'static_front_page', array(
			'title'          => __( 'Static Front Page' ),
		//	'theme_supports' => 'static-front-page',
			'description'    => __( 'Your theme supports a static front page.' ),
		) );

		$choices = array();
		$choices['posts'] = __( 'Your latest posts' );
		$choices['page'] = __( 'A static page (select below)' );

		$this->add_setting( 'show_on_front', array(
			'label'          => __( 'Front page displays' ),
		//	'theme_supports' => 'static-front-page',
			'section'        => 'static_front_page',
			'control'        => 'radio',
			'choices'        => $choices,
			'default'        => get_option( 'show_on_front' ),
			'type'           => 'option',
			'capability'     => 'manage_options'
		) );

		$this->add_setting( 'page_on_front', array(
			'label'          => __( 'Front page:' ),
		//	'theme_supports' => 'static-front-page',
			'section'        => 'static_front_page',
			'control'        => 'dropdown-pages',
			'type'           => 'option',
			'capability'     => 'manage_options'
		) );

		$this->add_setting( 'page_for_posts', array(
			'label'          => __( 'Posts page:' ),
		//	'theme_supports' => 'static-front-page',
			'section'        => 'static_front_page',
			'control'        => 'dropdown-pages',
			'type'           => 'option',
			'capability'     => 'manage_options'
		) );

		/* Site Title & Tagline */

		$this->add_section( 'strings', array(
			'title'          => __( 'Site Title & Tagline' ),
			'description'    => __( 'Customize some strings.' ),
		) );

		$this->add_setting( 'blogname', array(
			'label'          => __( 'Site Title' ),
			'section'        => 'strings',
			'default'        => get_option( 'blogname' ),
			'type'           => 'option',
			'capability'     => 'manage_options'
		) );

		$this->add_setting( 'blogdescription', array(
			'label'          => __( 'Tagline' ),
			'section'        => 'strings',
			'default'        => get_option( 'blogdescription' ),
			'type'           => 'option',
			'capability'     => 'manage_options'
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

// Custome render type for a dropdown menu.
function customize_control_dropdown_pages( $setting ) {
	printf(
		__( '<label>%s %s</label>' ),
		$setting->label,
		wp_dropdown_pages(
			array(
				'name'              => $setting->get_name(),
				'echo'              => 0,
				'show_option_none'  => __( '&mdash; Select &mdash;' ),
				'option_none_value' => '0',
				'selected'          => get_option( $setting->id )
			)
		)
	);
}
add_action( 'customize_render_control-dropdown-pages', 'customize_control_dropdown_pages' );

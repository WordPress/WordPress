<?php
/**
 * WordPress Customize Manager classes
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */

/**
 * Customize Manager class.
 *
 * Bootstraps the Customize experience on the server-side.
 *
 * Sets up the theme-switching process if a theme other than the active one is
 * being previewed and customized.
 *
 * Serves as a factory for Customize Controls and Settings, and
 * instantiates default Customize Controls and Settings.
 *
 * @since 3.4.0
 */
final class WP_Customize_Manager {
	/**
	 * An instance of the theme being previewed.
	 *
	 * @since 3.4.0
	 * @access protected
	 * @var WP_Theme
	 */
	protected $theme;

	/**
	 * The directory name of the previously active theme (within the theme_root).
	 *
	 * @since 3.4.0
	 * @access protected
	 * @var string
	 */
	protected $original_stylesheet;

	/**
	 * Whether this is a Customizer pageload.
	 *
	 * @since 3.4.0
	 * @access protected
	 * @var bool
	 */
	protected $previewing = false;

	/**
	 * Methods and properties dealing with managing widgets in the Customizer.
	 *
	 * @since 3.9.0
	 * @access public
	 * @var WP_Customize_Widgets
	 */
	public $widgets;

	/**
	 * Methods and properties dealing with managing nav menus in the Customizer.
	 *
	 * @since 4.3.0
	 * @access public
	 * @var WP_Customize_Nav_Menus
	 */
	public $nav_menus;

	/**
	 * Registered instances of WP_Customize_Setting.
	 *
	 * @since 3.4.0
	 * @access protected
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Sorted top-level instances of WP_Customize_Panel and WP_Customize_Section.
	 *
	 * @since 4.0.0
	 * @access protected
	 * @var array
	 */
	protected $containers = array();

	/**
	 * Registered instances of WP_Customize_Panel.
	 *
	 * @since 4.0.0
	 * @access protected
	 * @var array
	 */
	protected $panels = array();

	/**
	 * Registered instances of WP_Customize_Section.
	 *
	 * @since 3.4.0
	 * @access protected
	 * @var array
	 */
	protected $sections = array();

	/**
	 * Registered instances of WP_Customize_Control.
	 *
	 * @since 3.4.0
	 * @access protected
	 * @var array
	 */
	protected $controls = array();

	/**
	 * Return value of check_ajax_referer() in customize_preview_init() method.
	 *
	 * @since 3.5.0
	 * @access protected
	 * @var false|int
	 */
	protected $nonce_tick;

	/**
	 * Panel types that may be rendered from JS templates.
	 *
	 * @since 4.3.0
	 * @access protected
	 * @var array
	 */
	protected $registered_panel_types = array();

	/**
	 * Section types that may be rendered from JS templates.
	 *
	 * @since 4.3.0
	 * @access protected
	 * @var array
	 */
	protected $registered_section_types = array();

	/**
	 * Control types that may be rendered from JS templates.
	 *
	 * @since 4.1.0
	 * @access protected
	 * @var array
	 */
	protected $registered_control_types = array();

	/**
	 * Initial URL being previewed.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var string
	 */
	protected $preview_url;

	/**
	 * URL to link the user to when closing the Customizer.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var string
	 */
	protected $return_url;

	/**
	 * Mapping of 'panel', 'section', 'control' to the ID which should be autofocused.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var array
	 */
	protected $autofocus = array();

	/**
	 * Unsanitized values for Customize Settings parsed from $_POST['customized'].
	 *
	 * @var array
	 */
	private $_post_values;

	/**
	 * Constructor.
	 *
	 * @since 3.4.0
	 */
	public function __construct() {
		require_once( ABSPATH . WPINC . '/class-wp-customize-setting.php' );
		require_once( ABSPATH . WPINC . '/class-wp-customize-panel.php' );
		require_once( ABSPATH . WPINC . '/class-wp-customize-section.php' );
		require_once( ABSPATH . WPINC . '/class-wp-customize-control.php' );

		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-color-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-media-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-upload-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-image-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-background-image-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-cropped-image-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-site-icon-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-header-image-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-theme-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-widget-area-customize-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-widget-form-customize-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-item-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-location-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-name-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-auto-add-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-new-menu-control.php' );

		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menus-panel.php' );

		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-themes-section.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-sidebar-section.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-section.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-new-menu-section.php' );

		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-filter-setting.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-header-image-setting.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-background-image-setting.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-item-setting.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-setting.php' );

		/**
		 * Filter the core Customizer components to load.
		 *
		 * This allows Core components to be excluded from being instantiated by
		 * filtering them out of the array. Note that this filter generally runs
		 * during the <code>plugins_loaded</code> action, so it cannot be added
		 * in a theme.
		 *
		 * @since 4.4.0
		 *
		 * @see WP_Customize_Manager::__construct()
		 *
		 * @param array                $components List of core components to load.
		 * @param WP_Customize_Manager $this       WP_Customize_Manager instance.
		 */
		$components = apply_filters( 'customize_loaded_components', array( 'widgets', 'nav_menus' ), $this );

		if ( in_array( 'widgets', $components ) ) {
			require_once( ABSPATH . WPINC . '/class-wp-customize-widgets.php' );
			$this->widgets = new WP_Customize_Widgets( $this );
		}
		if ( in_array( 'nav_menus', $components ) ) {
			require_once( ABSPATH . WPINC . '/class-wp-customize-nav-menus.php' );
			$this->nav_menus = new WP_Customize_Nav_Menus( $this );
		}

		add_filter( 'wp_die_handler', array( $this, 'wp_die_handler' ) );

		add_action( 'setup_theme', array( $this, 'setup_theme' ) );
		add_action( 'wp_loaded',   array( $this, 'wp_loaded' ) );

		// Run wp_redirect_status late to make sure we override the status last.
		add_action( 'wp_redirect_status', array( $this, 'wp_redirect_status' ), 1000 );

		// Do not spawn cron (especially the alternate cron) while running the Customizer.
		remove_action( 'init', 'wp_cron' );

		// Do not run update checks when rendering the controls.
		remove_action( 'admin_init', '_maybe_update_core' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_themes' );

		add_action( 'wp_ajax_customize_save',           array( $this, 'save' ) );
		add_action( 'wp_ajax_customize_refresh_nonces', array( $this, 'refresh_nonces' ) );

		add_action( 'customize_register',                 array( $this, 'register_controls' ) );
		add_action( 'customize_register',                 array( $this, 'register_dynamic_settings' ), 11 ); // allow code to create settings first
		add_action( 'customize_controls_init',            array( $this, 'prepare_controls' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ) );

		// Render Panel, Section, and Control templates.
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'render_panel_templates' ), 1 );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'render_section_templates' ), 1 );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'render_control_templates' ), 1 );

		// Export the settings to JS via the _wpCustomizeSettings variable.
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'customize_pane_settings' ), 1000 );
	}

	/**
	 * Return true if it's an AJAX request.
	 *
	 * @since 3.4.0
	 * @since 4.2.0 Added `$action` param.
	 * @access public
	 *
	 * @param string|null $action Whether the supplied AJAX action is being run.
	 * @return bool True if it's an AJAX request, false otherwise.
	 */
	public function doing_ajax( $action = null ) {
		$doing_ajax = ( defined( 'DOING_AJAX' ) && DOING_AJAX );
		if ( ! $doing_ajax ) {
			return false;
		}

		if ( ! $action ) {
			return true;
		} else {
			/*
			 * Note: we can't just use doing_action( "wp_ajax_{$action}" ) because we need
			 * to check before admin-ajax.php gets to that point.
			 */
			return isset( $_REQUEST['action'] ) && wp_unslash( $_REQUEST['action'] ) === $action;
		}
	}

	/**
	 * Custom wp_die wrapper. Returns either the standard message for UI
	 * or the AJAX message.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $ajax_message AJAX return
	 * @param mixed $message UI message
	 */
	protected function wp_die( $ajax_message, $message = null ) {
		if ( $this->doing_ajax() || isset( $_POST['customized'] ) ) {
			wp_die( $ajax_message );
		}

		if ( ! $message ) {
			$message = __( 'Cheatin&#8217; uh?' );
		}

		wp_die( $message );
	}

	/**
	 * Return the AJAX wp_die() handler if it's a customized request.
	 *
	 * @since 3.4.0
	 *
	 * @return string
	 */
	public function wp_die_handler() {
		if ( $this->doing_ajax() || isset( $_POST['customized'] ) ) {
			return '_ajax_wp_die_handler';
		}

		return '_default_wp_die_handler';
	}

	/**
	 * Start preview and customize theme.
	 *
	 * Check if customize query variable exist. Init filters to filter the current theme.
	 *
	 * @since 3.4.0
	 */
	public function setup_theme() {
		send_origin_headers();

		$doing_ajax_or_is_customized = ( $this->doing_ajax() || isset( $_POST['customized'] ) );
		if ( is_admin() && ! $doing_ajax_or_is_customized ) {
			auth_redirect();
		} elseif ( $doing_ajax_or_is_customized && ! is_user_logged_in() ) {
			$this->wp_die( 0, __( 'You must be logged in to complete this action.' ) );
		}

		show_admin_bar( false );

		if ( ! current_user_can( 'customize' ) ) {
			$this->wp_die( -1, __( 'You are not allowed to customize the appearance of this site.' ) );
		}

		$this->original_stylesheet = get_stylesheet();

		$this->theme = wp_get_theme( isset( $_REQUEST['theme'] ) ? $_REQUEST['theme'] : null );

		if ( $this->is_theme_active() ) {
			// Once the theme is loaded, we'll validate it.
			add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
		} else {
			// If the requested theme is not the active theme and the user doesn't have the
			// switch_themes cap, bail.
			if ( ! current_user_can( 'switch_themes' ) ) {
				$this->wp_die( -1, __( 'You are not allowed to edit theme options on this site.' ) );
			}

			// If the theme has errors while loading, bail.
			if ( $this->theme()->errors() ) {
				$this->wp_die( -1, $this->theme()->errors()->get_error_message() );
			}

			// If the theme isn't allowed per multisite settings, bail.
			if ( ! $this->theme()->is_allowed() ) {
				$this->wp_die( -1, __( 'The requested theme does not exist.' ) );
			}
		}

		$this->start_previewing_theme();
	}

	/**
	 * Callback to validate a theme once it is loaded
	 *
	 * @since 3.4.0
	 */
	public function after_setup_theme() {
		$doing_ajax_or_is_customized = ( $this->doing_ajax() || isset( $_SERVER['customized'] ) );
		if ( ! $doing_ajax_or_is_customized && ! validate_current_theme() ) {
			wp_redirect( 'themes.php?broken=true' );
			exit;
		}
	}

	/**
	 * If the theme to be previewed isn't the active theme, add filter callbacks
	 * to swap it out at runtime.
	 *
	 * @since 3.4.0
	 */
	public function start_previewing_theme() {
		// Bail if we're already previewing.
		if ( $this->is_preview() ) {
			return;
		}

		$this->previewing = true;

		if ( ! $this->is_theme_active() ) {
			add_filter( 'template', array( $this, 'get_template' ) );
			add_filter( 'stylesheet', array( $this, 'get_stylesheet' ) );
			add_filter( 'pre_option_current_theme', array( $this, 'current_theme' ) );

			// @link: https://core.trac.wordpress.org/ticket/20027
			add_filter( 'pre_option_stylesheet', array( $this, 'get_stylesheet' ) );
			add_filter( 'pre_option_template', array( $this, 'get_template' ) );

			// Handle custom theme roots.
			add_filter( 'pre_option_stylesheet_root', array( $this, 'get_stylesheet_root' ) );
			add_filter( 'pre_option_template_root', array( $this, 'get_template_root' ) );
		}

		/**
		 * Fires once the Customizer theme preview has started.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Manager $this WP_Customize_Manager instance.
		 */
		do_action( 'start_previewing_theme', $this );
	}

	/**
	 * Stop previewing the selected theme.
	 *
	 * Removes filters to change the current theme.
	 *
	 * @since 3.4.0
	 */
	public function stop_previewing_theme() {
		if ( ! $this->is_preview() ) {
			return;
		}

		$this->previewing = false;

		if ( ! $this->is_theme_active() ) {
			remove_filter( 'template', array( $this, 'get_template' ) );
			remove_filter( 'stylesheet', array( $this, 'get_stylesheet' ) );
			remove_filter( 'pre_option_current_theme', array( $this, 'current_theme' ) );

			// @link: https://core.trac.wordpress.org/ticket/20027
			remove_filter( 'pre_option_stylesheet', array( $this, 'get_stylesheet' ) );
			remove_filter( 'pre_option_template', array( $this, 'get_template' ) );

			// Handle custom theme roots.
			remove_filter( 'pre_option_stylesheet_root', array( $this, 'get_stylesheet_root' ) );
			remove_filter( 'pre_option_template_root', array( $this, 'get_template_root' ) );
		}

		/**
		 * Fires once the Customizer theme preview has stopped.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Manager $this WP_Customize_Manager instance.
		 */
		do_action( 'stop_previewing_theme', $this );
	}

	/**
	 * Get the theme being customized.
	 *
	 * @since 3.4.0
	 *
	 * @return WP_Theme
	 */
	public function theme() {
		if ( ! $this->theme ) {
			$this->theme = wp_get_theme();
		}
		return $this->theme;
	}

	/**
	 * Get the registered settings.
	 *
	 * @since 3.4.0
	 *
	 * @return array
	 */
	public function settings() {
		return $this->settings;
	}

	/**
	 * Get the registered controls.
	 *
	 * @since 3.4.0
	 *
	 * @return array
	 */
	public function controls() {
		return $this->controls;
	}

	/**
	 * Get the registered containers.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function containers() {
		return $this->containers;
	}

	/**
	 * Get the registered sections.
	 *
	 * @since 3.4.0
	 *
	 * @return array
	 */
	public function sections() {
		return $this->sections;
	}

	/**
	 * Get the registered panels.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @return array Panels.
	 */
	public function panels() {
		return $this->panels;
	}

	/**
	 * Checks if the current theme is active.
	 *
	 * @since 3.4.0
	 *
	 * @return bool
	 */
	public function is_theme_active() {
		return $this->get_stylesheet() == $this->original_stylesheet;
	}

	/**
	 * Register styles/scripts and initialize the preview of each setting
	 *
	 * @since 3.4.0
	 */
	public function wp_loaded() {

		/**
		 * Fires once WordPress has loaded, allowing scripts and styles to be initialized.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Manager $this WP_Customize_Manager instance.
		 */
		do_action( 'customize_register', $this );

		if ( $this->is_preview() && ! is_admin() )
			$this->customize_preview_init();
	}

	/**
	 * Prevents AJAX requests from following redirects when previewing a theme
	 * by issuing a 200 response instead of a 30x.
	 *
	 * Instead, the JS will sniff out the location header.
	 *
	 * @since 3.4.0
	 *
	 * @param $status
	 * @return int
	 */
	public function wp_redirect_status( $status ) {
		if ( $this->is_preview() && ! is_admin() )
			return 200;

		return $status;
	}

	/**
	 * Parse the incoming $_POST['customized'] JSON data and store the unsanitized
	 * settings for subsequent post_value() lookups.
	 *
	 * @since 4.1.1
	 *
	 * @return array
	 */
	public function unsanitized_post_values() {
		if ( ! isset( $this->_post_values ) ) {
			if ( isset( $_POST['customized'] ) ) {
				$this->_post_values = json_decode( wp_unslash( $_POST['customized'] ), true );
			}
			if ( empty( $this->_post_values ) ) { // if not isset or if JSON error
				$this->_post_values = array();
			}
		}
		if ( empty( $this->_post_values ) ) {
			return array();
		} else {
			return $this->_post_values;
		}
	}

	/**
	 * Return the sanitized value for a given setting from the request's POST data.
	 *
	 * @since 3.4.0
	 * @since 4.1.1 Introduced 'default' parameter.
	 *
	 * @param WP_Customize_Setting $setting A WP_Customize_Setting derived object
	 * @param mixed $default value returned $setting has no post value (added in 4.2.0).
	 * @return string|mixed $post_value Sanitized value or the $default provided
	 */
	public function post_value( $setting, $default = null ) {
		$post_values = $this->unsanitized_post_values();
		if ( array_key_exists( $setting->id, $post_values ) ) {
			return $setting->sanitize( $post_values[ $setting->id ] );
		} else {
			return $default;
		}
	}

	/**
	 * Override a setting's (unsanitized) value as found in any incoming $_POST['customized'].
	 *
	 * @since 4.2.0
	 * @access public
	 *
	 * @param string $setting_id ID for the WP_Customize_Setting instance.
	 * @param mixed  $value      Post value.
	 */
	public function set_post_value( $setting_id, $value ) {
		$this->unsanitized_post_values();
		$this->_post_values[ $setting_id ] = $value;

		/**
		 * Announce when a specific setting's unsanitized post value has been set.
		 *
		 * Fires when the {@see WP_Customize_Manager::set_post_value()} method is called.
		 *
		 * The dynamic portion of the hook name, `$setting_id`, refers to the setting ID.
		 *
		 * @since 4.4.0
		 *
		 * @param mixed                $value Unsanitized setting post value.
		 * @param WP_Customize_Manager $this  WP_Customize_Manager instance.
		 */
		do_action( "customize_post_value_set_{$setting_id}", $value, $this );

		/**
		 * Announce when any setting's unsanitized post value has been set.
		 *
		 * Fires when the {@see WP_Customize_Manager::set_post_value()} method is called.
		 *
		 * This is useful for <code>WP_Customize_Setting</code> instances to watch
		 * in order to update a cached previewed value.
		 *
		 * @since 4.4.0
		 *
		 * @param string               $setting_id Setting ID.
		 * @param mixed                $value      Unsanitized setting post value.
		 * @param WP_Customize_Manager $this       WP_Customize_Manager instance.
		 */
		do_action( 'customize_post_value_set', $setting_id, $value, $this );
	}

	/**
	 * Print JavaScript settings.
	 *
	 * @since 3.4.0
	 */
	public function customize_preview_init() {
		$this->nonce_tick = check_ajax_referer( 'preview-customize_' . $this->get_stylesheet(), 'nonce' );

		$this->prepare_controls();

		wp_enqueue_script( 'customize-preview' );
		add_action( 'wp', array( $this, 'customize_preview_override_404_status' ) );
		add_action( 'wp_head', array( $this, 'customize_preview_base' ) );
		add_action( 'wp_head', array( $this, 'customize_preview_html5' ) );
		add_action( 'wp_head', array( $this, 'customize_preview_loading_style' ) );
		add_action( 'wp_footer', array( $this, 'customize_preview_settings' ), 20 );
		add_action( 'shutdown', array( $this, 'customize_preview_signature' ), 1000 );
		add_filter( 'wp_die_handler', array( $this, 'remove_preview_signature' ) );

		foreach ( $this->settings as $setting ) {
			$setting->preview();
		}

		/**
		 * Fires once the Customizer preview has initialized and JavaScript
		 * settings have been printed.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Manager $this WP_Customize_Manager instance.
		 */
		do_action( 'customize_preview_init', $this );
	}

	/**
	 * Prevent sending a 404 status when returning the response for the customize
	 * preview, since it causes the jQuery AJAX to fail. Send 200 instead.
	 *
	 * @since 4.0.0
	 * @access public
	 */
	public function customize_preview_override_404_status() {
		if ( is_404() ) {
			status_header( 200 );
		}
	}

	/**
	 * Print base element for preview frame.
	 *
	 * @since 3.4.0
	 */
	public function customize_preview_base() {
		?><base href="<?php echo home_url( '/' ); ?>" /><?php
	}

	/**
	 * Print a workaround to handle HTML5 tags in IE < 9.
	 *
	 * @since 3.4.0
	 */
	public function customize_preview_html5() { ?>
		<!--[if lt IE 9]>
		<script type="text/javascript">
			var e = [ 'abbr', 'article', 'aside', 'audio', 'canvas', 'datalist', 'details',
				'figure', 'footer', 'header', 'hgroup', 'mark', 'menu', 'meter', 'nav',
				'output', 'progress', 'section', 'time', 'video' ];
			for ( var i = 0; i < e.length; i++ ) {
				document.createElement( e[i] );
			}
		</script>
		<![endif]--><?php
	}

	/**
	 * Print CSS for loading indicators for the Customizer preview.
	 *
	 * @since 4.2.0
	 * @access public
	 */
	public function customize_preview_loading_style() {
		?><style>
			body.wp-customizer-unloading {
				opacity: 0.25;
				cursor: progress !important;
				-webkit-transition: opacity 0.5s;
				transition: opacity 0.5s;
			}
			body.wp-customizer-unloading * {
				pointer-events: none !important;
			}
		</style><?php
	}

	/**
	 * Print JavaScript settings for preview frame.
	 *
	 * @since 3.4.0
	 */
	public function customize_preview_settings() {
		$settings = array(
			'channel' => wp_unslash( $_POST['customize_messenger_channel'] ),
			'activePanels' => array(),
			'activeSections' => array(),
			'activeControls' => array(),
		);

		if ( 2 == $this->nonce_tick ) {
			$settings['nonce'] = array(
				'save' => wp_create_nonce( 'save-customize_' . $this->get_stylesheet() ),
				'preview' => wp_create_nonce( 'preview-customize_' . $this->get_stylesheet() )
			);
		}

		foreach ( $this->panels as $panel_id => $panel ) {
			if ( $panel->check_capabilities() ) {
				$settings['activePanels'][ $panel_id ] = $panel->active();
				foreach ( $panel->sections as $section_id => $section ) {
					if ( $section->check_capabilities() ) {
						$settings['activeSections'][ $section_id ] = $section->active();
					}
				}
			}
		}
		foreach ( $this->sections as $id => $section ) {
			if ( $section->check_capabilities() ) {
				$settings['activeSections'][ $id ] = $section->active();
			}
		}
		foreach ( $this->controls as $id => $control ) {
			if ( $control->check_capabilities() ) {
				$settings['activeControls'][ $id ] = $control->active();
			}
		}

		?>
		<script type="text/javascript">
			var _wpCustomizeSettings = <?php echo wp_json_encode( $settings ); ?>;
			_wpCustomizeSettings.values = {};
			(function( v ) {
				<?php
				/*
				 * Serialize settings separately from the initial _wpCustomizeSettings
				 * serialization in order to avoid a peak memory usage spike.
				 * @todo We may not even need to export the values at all since the pane syncs them anyway.
				 */
				foreach ( $this->settings as $id => $setting ) {
					if ( $setting->check_capabilities() ) {
						printf(
							"v[%s] = %s;\n",
							wp_json_encode( $id ),
							wp_json_encode( $setting->js_value() )
						);
					}
				}
				?>
			})( _wpCustomizeSettings.values );
		</script>
		<?php
	}

	/**
	 * Prints a signature so we can ensure the Customizer was properly executed.
	 *
	 * @since 3.4.0
	 */
	public function customize_preview_signature() {
		echo 'WP_CUSTOMIZER_SIGNATURE';
	}

	/**
	 * Removes the signature in case we experience a case where the Customizer was not properly executed.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $return Value passed through for wp_die_handler filter.
	 * @return mixed Value passed through for wp_die_handler filter.
	 */
	public function remove_preview_signature( $return = null ) {
		remove_action( 'shutdown', array( $this, 'customize_preview_signature' ), 1000 );

		return $return;
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
		return $this->theme()->get_template();
	}

	/**
	 * Retrieve the stylesheet name of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @return string Stylesheet name.
	 */
	public function get_stylesheet() {
		return $this->theme()->get_stylesheet();
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
	 * @param $current_theme {@internal Parameter is not used}
	 * @return string Theme name.
	 */
	public function current_theme( $current_theme ) {
		return $this->theme()->display('Name');
	}

	/**
	 * Switch the theme and trigger the save() method on each setting.
	 *
	 * @since 3.4.0
	 */
	public function save() {
		if ( ! $this->is_preview() ) {
			wp_send_json_error( 'not_preview' );
		}

		$action = 'save-customize_' . $this->get_stylesheet();
		if ( ! check_ajax_referer( $action, 'nonce', false ) ) {
			wp_send_json_error( 'invalid_nonce' );
		}

		// Do we have to switch themes?
		if ( ! $this->is_theme_active() ) {
			// Temporarily stop previewing the theme to allow switch_themes()
			// to operate properly.
			$this->stop_previewing_theme();
			switch_theme( $this->get_stylesheet() );
			update_option( 'theme_switched_via_customizer', true );
			$this->start_previewing_theme();
		}

		/**
		 * Fires once the theme has switched in the Customizer, but before settings
		 * have been saved.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Manager $this WP_Customize_Manager instance.
		 */
		do_action( 'customize_save', $this );

		foreach ( $this->settings as $setting ) {
			$setting->save();
		}

		/**
		 * Fires after Customize settings have been saved.
		 *
		 * @since 3.6.0
		 *
		 * @param WP_Customize_Manager $this WP_Customize_Manager instance.
		 */
		do_action( 'customize_save_after', $this );

		/**
		 * Filter response data for a successful customize_save AJAX request.
		 *
		 * This filter does not apply if there was a nonce or authentication failure.
		 *
		 * @since 4.2.0
		 *
		 * @param array                $data Additional information passed back to the 'saved'
		 *                                   event on `wp.customize`.
		 * @param WP_Customize_Manager $this WP_Customize_Manager instance.
		 */
		$response = apply_filters( 'customize_save_response', array(), $this );
		wp_send_json_success( $response );
	}

	/**
	 * Refresh nonces for the current preview.
	 *
	 * @since 4.2.0
	 */
	public function refresh_nonces() {
		if ( ! $this->is_preview() ) {
			wp_send_json_error( 'not_preview' );
		}

		$nonces = array(
			'save'    => wp_create_nonce( 'save-customize_' . $this->get_stylesheet() ),
			'preview' => wp_create_nonce( 'preview-customize_' . $this->get_stylesheet() ),
		);

		/**
		 * Filter nonces for a customize_refresh_nonces AJAX request.
		 *
		 * @since 4.2.0
		 *
		 * @param array                $nonces Array of refreshed nonces for save and
		 *                                     preview actions.
		 * @param WP_Customize_Manager $this   WP_Customize_Manager instance.
		 */
		$nonces = apply_filters( 'customize_refresh_nonces', $nonces, $this );
		wp_send_json_success( $nonces );
	}

	/**
	 * Add a customize setting.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_Customize_Setting|string $id Customize Setting object, or ID.
	 * @param array $args                     Setting arguments; passed to WP_Customize_Setting
	 *                                        constructor.
	 */
	public function add_setting( $id, $args = array() ) {
		if ( $id instanceof WP_Customize_Setting ) {
			$setting = $id;
		} else {
			$setting = new WP_Customize_Setting( $this, $id, $args );
		}
		$this->settings[ $setting->id ] = $setting;
	}

	/**
	 * Register any dynamically-created settings, such as those from $_POST['customized']
	 * that have no corresponding setting created.
	 *
	 * This is a mechanism to "wake up" settings that have been dynamically created
	 * on the frontend and have been sent to WordPress in `$_POST['customized']`. When WP
	 * loads, the dynamically-created settings then will get created and previewed
	 * even though they are not directly created statically with code.
	 *
	 * @since 4.2.0
	 *
	 * @param array $setting_ids The setting IDs to add.
	 * @return array The WP_Customize_Setting objects added.
	 */
	public function add_dynamic_settings( $setting_ids ) {
		$new_settings = array();
		foreach ( $setting_ids as $setting_id ) {
			// Skip settings already created
			if ( $this->get_setting( $setting_id ) ) {
				continue;
			}

			$setting_args = false;
			$setting_class = 'WP_Customize_Setting';

			/**
			 * Filter a dynamic setting's constructor args.
			 *
			 * For a dynamic setting to be registered, this filter must be employed
			 * to override the default false value with an array of args to pass to
			 * the WP_Customize_Setting constructor.
			 *
			 * @since 4.2.0
			 *
			 * @param false|array $setting_args The arguments to the WP_Customize_Setting constructor.
			 * @param string      $setting_id   ID for dynamic setting, usually coming from `$_POST['customized']`.
			 */
			$setting_args = apply_filters( 'customize_dynamic_setting_args', $setting_args, $setting_id );
			if ( false === $setting_args ) {
				continue;
			}

			/**
			 * Allow non-statically created settings to be constructed with custom WP_Customize_Setting subclass.
			 *
			 * @since 4.2.0
			 *
			 * @param string $setting_class WP_Customize_Setting or a subclass.
			 * @param string $setting_id    ID for dynamic setting, usually coming from `$_POST['customized']`.
			 * @param array  $setting_args  WP_Customize_Setting or a subclass.
			 */
			$setting_class = apply_filters( 'customize_dynamic_setting_class', $setting_class, $setting_id, $setting_args );

			$setting = new $setting_class( $this, $setting_id, $setting_args );

			$this->add_setting( $setting );
			$new_settings[] = $setting;
		}
		return $new_settings;
	}

	/**
	 * Retrieve a customize setting.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id Customize Setting ID.
	 * @return WP_Customize_Setting|void The setting, if set.
	 */
	public function get_setting( $id ) {
		if ( isset( $this->settings[ $id ] ) ) {
			return $this->settings[ $id ];
		}
	}

	/**
	 * Remove a customize setting.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id Customize Setting ID.
	 */
	public function remove_setting( $id ) {
		unset( $this->settings[ $id ] );
	}

	/**
	 * Add a customize panel.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param WP_Customize_Panel|string $id   Customize Panel object, or Panel ID.
	 * @param array                     $args Optional. Panel arguments. Default empty array.
	 */
	public function add_panel( $id, $args = array() ) {
		if ( $id instanceof WP_Customize_Panel ) {
			$panel = $id;
		} else {
			$panel = new WP_Customize_Panel( $this, $id, $args );
		}

		$this->panels[ $panel->id ] = $panel;
	}

	/**
	 * Retrieve a customize panel.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $id Panel ID to get.
	 * @return WP_Customize_Panel|void Requested panel instance, if set.
	 */
	public function get_panel( $id ) {
		if ( isset( $this->panels[ $id ] ) ) {
			return $this->panels[ $id ];
		}
	}

	/**
	 * Remove a customize panel.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $id Panel ID to remove.
	 */
	public function remove_panel( $id ) {
		unset( $this->panels[ $id ] );
	}

	/**
	 * Register a customize panel type.
	 *
	 * Registered types are eligible to be rendered via JS and created dynamically.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @see WP_Customize_Panel
	 *
	 * @param string $panel Name of a custom panel which is a subclass of WP_Customize_Panel.
	 */
	public function register_panel_type( $panel ) {
		$this->registered_panel_types[] = $panel;
	}

	/**
	 * Render JS templates for all registered panel types.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function render_panel_templates() {
		foreach ( $this->registered_panel_types as $panel_type ) {
			$panel = new $panel_type( $this, 'temp', array() );
			$panel->print_template();
		}
	}

	/**
	 * Add a customize section.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_Customize_Section|string $id   Customize Section object, or Section ID.
	 * @param array                       $args Section arguments.
	 */
	public function add_section( $id, $args = array() ) {
		if ( $id instanceof WP_Customize_Section ) {
			$section = $id;
		} else {
			$section = new WP_Customize_Section( $this, $id, $args );
		}
		$this->sections[ $section->id ] = $section;
	}

	/**
	 * Retrieve a customize section.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id Section ID.
	 * @return WP_Customize_Section|void The section, if set.
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
	 * @param string $id Section ID.
	 */
	public function remove_section( $id ) {
		unset( $this->sections[ $id ] );
	}

	/**
	 * Register a customize section type.
	 *
	 * Registered types are eligible to be rendered via JS and created dynamically.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @see WP_Customize_Section
	 *
	 * @param string $section Name of a custom section which is a subclass of WP_Customize_Section.
	 */
	public function register_section_type( $section ) {
		$this->registered_section_types[] = $section;
	}

	/**
	 * Render JS templates for all registered section types.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function render_section_templates() {
		foreach ( $this->registered_section_types as $section_type ) {
			$section = new $section_type( $this, 'temp', array() );
			$section->print_template();
		}
	}

	/**
	 * Add a customize control.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_Customize_Control|string $id   Customize Control object, or ID.
	 * @param array                       $args Control arguments; passed to WP_Customize_Control
	 *                                          constructor.
	 */
	public function add_control( $id, $args = array() ) {
		if ( $id instanceof WP_Customize_Control ) {
			$control = $id;
		} else {
			$control = new WP_Customize_Control( $this, $id, $args );
		}
		$this->controls[ $control->id ] = $control;
	}

	/**
	 * Retrieve a customize control.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id ID of the control.
	 * @return WP_Customize_Control|void The control object, if set.
	 */
	public function get_control( $id ) {
		if ( isset( $this->controls[ $id ] ) )
			return $this->controls[ $id ];
	}

	/**
	 * Remove a customize control.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id ID of the control.
	 */
	public function remove_control( $id ) {
		unset( $this->controls[ $id ] );
	}

	/**
	 * Register a customize control type.
	 *
	 * Registered types are eligible to be rendered via JS and created dynamically.
	 *
	 * @since 4.1.0
	 * @access public
	 *
	 * @param string $control Name of a custom control which is a subclass of
	 *                        {@see WP_Customize_Control}.
	 */
	public function register_control_type( $control ) {
		$this->registered_control_types[] = $control;
	}

	/**
	 * Render JS templates for all registered control types.
	 *
	 * @since 4.1.0
	 * @access public
	 */
	public function render_control_templates() {
		foreach ( $this->registered_control_types as $control_type ) {
			$control = new $control_type( $this, 'temp', array() );
			$control->print_template();
		}
	}

	/**
	 * Helper function to compare two objects by priority, ensuring sort stability via instance_number.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_Customize_Panel|WP_Customize_Section|WP_Customize_Control $a Object A.
	 * @param WP_Customize_Panel|WP_Customize_Section|WP_Customize_Control $b Object B.
	 * @return int
	 */
	protected function _cmp_priority( $a, $b ) {
		if ( $a->priority === $b->priority ) {
			return $a->instance_number - $b->instance_number;
		} else {
			return $a->priority - $b->priority;
		}
	}

	/**
	 * Prepare panels, sections, and controls.
	 *
	 * For each, check if required related components exist,
	 * whether the user has the necessary capabilities,
	 * and sort by priority.
	 *
	 * @since 3.4.0
	 */
	public function prepare_controls() {

		$controls = array();
		uasort( $this->controls, array( $this, '_cmp_priority' ) );

		foreach ( $this->controls as $id => $control ) {
			if ( ! isset( $this->sections[ $control->section ] ) || ! $control->check_capabilities() ) {
				continue;
			}

			$this->sections[ $control->section ]->controls[] = $control;
			$controls[ $id ] = $control;
		}
		$this->controls = $controls;

		// Prepare sections.
		uasort( $this->sections, array( $this, '_cmp_priority' ) );
		$sections = array();

		foreach ( $this->sections as $section ) {
			if ( ! $section->check_capabilities() ) {
				continue;
			}

			usort( $section->controls, array( $this, '_cmp_priority' ) );

			if ( ! $section->panel ) {
				// Top-level section.
				$sections[ $section->id ] = $section;
			} else {
				// This section belongs to a panel.
				if ( isset( $this->panels [ $section->panel ] ) ) {
					$this->panels[ $section->panel ]->sections[ $section->id ] = $section;
				}
			}
		}
		$this->sections = $sections;

		// Prepare panels.
		uasort( $this->panels, array( $this, '_cmp_priority' ) );
		$panels = array();

		foreach ( $this->panels as $panel ) {
			if ( ! $panel->check_capabilities() ) {
				continue;
			}

			uasort( $panel->sections, array( $this, '_cmp_priority' ) );
			$panels[ $panel->id ] = $panel;
		}
		$this->panels = $panels;

		// Sort panels and top-level sections together.
		$this->containers = array_merge( $this->panels, $this->sections );
		uasort( $this->containers, array( $this, '_cmp_priority' ) );
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
	 * Determine whether the user agent is iOS.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return bool Whether the user agent is iOS.
	 */
	public function is_ios() {
		return wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] );
	}

	/**
	 * Get the template string for the Customizer pane document title.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return string The template string for the document title.
	 */
	public function get_document_title_template() {
		if ( $this->is_theme_active() ) {
			/* translators: %s: document title from the preview */
			$document_title_tmpl = __( 'Customize: %s' );
		} else {
			/* translators: %s: document title from the preview */
			$document_title_tmpl = __( 'Live Preview: %s' );
		}
		$document_title_tmpl = html_entity_decode( $document_title_tmpl, ENT_QUOTES, 'UTF-8' ); // Because exported to JS and assigned to document.title.
		return $document_title_tmpl;
	}

	/**
	 * Set the initial URL to be previewed.
	 *
	 * URL is validated.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $preview_url URL to be previewed.
	 */
	public function set_preview_url( $preview_url ) {
		$this->preview_url = wp_validate_redirect( $preview_url, home_url( '/' ) );
	}

	/**
	 * Get the initial URL to be previewed.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return string URL being previewed.
	 */
	public function get_preview_url() {
		if ( empty( $this->preview_url ) ) {
			$preview_url = home_url( '/' );
		} else {
			$preview_url = $this->preview_url;
		}
		return $preview_url;
	}

	/**
	 * Set URL to link the user to when closing the Customizer.
	 *
	 * URL is validated.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $return_url URL for return link.
	 */
	public function set_return_url( $return_url ) {
		$return_url = remove_query_arg( wp_removable_query_args(), $return_url );
		$return_url = wp_validate_redirect( $return_url );
		$this->return_url = $return_url;
	}

	/**
	 * Get URL to link the user to when closing the Customizer.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return string URL for link to close Customizer.
	 */
	public function get_return_url() {
		$referer = wp_get_referer();
		$excluded_referer_basenames = array( 'customize.php', 'wp-login.php' );

		if ( $this->return_url ) {
			$return_url = $this->return_url;
		} else if ( $referer && ! in_array( basename( parse_url( $referer, PHP_URL_PATH ) ), $excluded_referer_basenames, true ) ) {
			$return_url = $referer;
		} else if ( $this->preview_url ) {
			$return_url = $this->preview_url;
		} else {
			$return_url = home_url( '/' );
		}
		return $return_url;
	}

	/**
	 * Set the autofocused constructs.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param array $autofocus {
	 *     Mapping of 'panel', 'section', 'control' to the ID which should be autofocused.
	 *
	 *     @type string [$control]  ID for control to be autofocused.
	 *     @type string [$section]  ID for section to be autofocused.
	 *     @type string [$panel]    ID for panel to be autofocused.
	 * }
	 */
	public function set_autofocus( $autofocus ) {
		$this->autofocus = array_filter( wp_array_slice_assoc( $autofocus, array( 'panel', 'section', 'control' ) ), 'is_string' );
	}

	/**
	 * Get the autofocused constructs.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array {
	 *     Mapping of 'panel', 'section', 'control' to the ID which should be autofocused.
	 *
	 *     @type string [$control]  ID for control to be autofocused.
	 *     @type string [$section]  ID for section to be autofocused.
	 *     @type string [$panel]    ID for panel to be autofocused.
	 * }
	 */
	public function get_autofocus() {
		return $this->autofocus;
	}

	/**
	 * Print JavaScript settings for parent window.
	 *
	 * @since 4.4.0
	 */
	public function customize_pane_settings() {
		/*
		 * If the frontend and the admin are served from the same domain, load the
		 * preview over ssl if the Customizer is being loaded over ssl. This avoids
		 * insecure content warnings. This is not attempted if the admin and frontend
		 * are on different domains to avoid the case where the frontend doesn't have
		 * ssl certs. Domain mapping plugins can allow other urls in these conditions
		 * using the customize_allowed_urls filter.
		 */

		$allowed_urls = array( home_url( '/' ) );
		$admin_origin = parse_url( admin_url() );
		$home_origin  = parse_url( home_url() );
		$cross_domain = ( strtolower( $admin_origin['host'] ) !== strtolower( $home_origin['host'] ) );

		if ( is_ssl() && ! $cross_domain ) {
			$allowed_urls[] = home_url( '/', 'https' );
		}

		/**
		 * Filter the list of URLs allowed to be clicked and followed in the Customizer preview.
		 *
		 * @since 3.4.0
		 *
		 * @param array $allowed_urls An array of allowed URLs.
		 */
		$allowed_urls = array_unique( apply_filters( 'customize_allowed_urls', $allowed_urls ) );

		$login_url = add_query_arg( array(
			'interim-login' => 1,
			'customize-login' => 1,
		), wp_login_url() );

		// Prepare Customizer settings to pass to JavaScript.
		$settings = array(
			'theme'    => array(
				'stylesheet' => $this->get_stylesheet(),
				'active'     => $this->is_theme_active(),
			),
			'url'      => array(
				'preview'       => esc_url_raw( $this->get_preview_url() ),
				'parent'        => esc_url_raw( admin_url() ),
				'activated'     => esc_url_raw( home_url( '/' ) ),
				'ajax'          => esc_url_raw( admin_url( 'admin-ajax.php', 'relative' ) ),
				'allowed'       => array_map( 'esc_url_raw', $allowed_urls ),
				'isCrossDomain' => $cross_domain,
				'home'          => esc_url_raw( home_url( '/' ) ),
				'login'         => esc_url_raw( $login_url ),
			),
			'browser'  => array(
				'mobile' => wp_is_mobile(),
				'ios'    => $this->is_ios(),
			),
			'panels'   => array(),
			'sections' => array(),
			'nonce'    => array(
				'save'    => wp_create_nonce( 'save-customize_' . $this->get_stylesheet() ),
				'preview' => wp_create_nonce( 'preview-customize_' . $this->get_stylesheet() ),
			),
			'autofocus' => array(),
			'documentTitleTmpl' => $this->get_document_title_template(),
		);

		// Prepare Customize Section objects to pass to JavaScript.
		foreach ( $this->sections() as $id => $section ) {
			if ( $section->check_capabilities() ) {
				$settings['sections'][ $id ] = $section->json();
			}
		}

		// Prepare Customize Panel objects to pass to JavaScript.
		foreach ( $this->panels() as $panel_id => $panel ) {
			if ( $panel->check_capabilities() ) {
				$settings['panels'][ $panel_id ] = $panel->json();
				foreach ( $panel->sections as $section_id => $section ) {
					if ( $section->check_capabilities() ) {
						$settings['sections'][ $section_id ] = $section->json();
					}
				}
			}
		}

		// Pass to frontend the Customizer construct being deeplinked.
		foreach ( $this->get_autofocus() as $type => $id ) {
			$can_autofocus = (
				( 'control' === $type && $this->get_control( $id ) && $this->get_control( $id )->check_capabilities() )
				||
				( 'section' === $type && isset( $settings['sections'][ $id ] ) )
				||
				( 'panel' === $type && isset( $settings['panels'][ $id ] ) )
			);
			if ( $can_autofocus ) {
				$settings['autofocus'][ $type ] = $id;
			}
		}

		?>
		<script type="text/javascript">
			var _wpCustomizeSettings = <?php echo wp_json_encode( $settings ); ?>;
			_wpCustomizeSettings.controls = {};
			_wpCustomizeSettings.settings = {};
			<?php

			// Serialize settings one by one to improve memory usage.
			echo "(function ( s ){\n";
			foreach ( $this->settings() as $setting ) {
				if ( $setting->check_capabilities() ) {
					printf(
						"s[%s] = %s;\n",
						wp_json_encode( $setting->id ),
						wp_json_encode( array(
							'value'     => $setting->js_value(),
							'transport' => $setting->transport,
							'dirty'     => $setting->dirty,
						) )
					);
				}
			}
			echo "})( _wpCustomizeSettings.settings );\n";

			// Serialize controls one by one to improve memory usage.
			echo "(function ( c ){\n";
			foreach ( $this->controls() as $control ) {
				if ( $control->check_capabilities() ) {
					printf(
						"c[%s] = %s;\n",
						wp_json_encode( $control->id ),
						wp_json_encode( $control->json() )
					);
				}
			}
			echo "})( _wpCustomizeSettings.controls );\n";
		?>
		</script>
		<?php
	}

	/**
	 * Register some default controls.
	 *
	 * @since 3.4.0
	 */
	public function register_controls() {

		/* Panel, Section, and Control Types */
		$this->register_panel_type( 'WP_Customize_Panel' );
		$this->register_section_type( 'WP_Customize_Section' );
		$this->register_section_type( 'WP_Customize_Sidebar_Section' );
		$this->register_control_type( 'WP_Customize_Color_Control' );
		$this->register_control_type( 'WP_Customize_Media_Control' );
		$this->register_control_type( 'WP_Customize_Upload_Control' );
		$this->register_control_type( 'WP_Customize_Image_Control' );
		$this->register_control_type( 'WP_Customize_Background_Image_Control' );
		$this->register_control_type( 'WP_Customize_Cropped_Image_Control' );
		$this->register_control_type( 'WP_Customize_Site_Icon_Control' );
		$this->register_control_type( 'WP_Customize_Theme_Control' );

		/* Themes */

		$this->add_section( new WP_Customize_Themes_Section( $this, 'themes', array(
			'title'      => $this->theme()->display( 'Name' ),
			'capability' => 'switch_themes',
			'priority'   => 0,
		) ) );

		// Themes Setting (unused - the theme is considerably more fundamental to the Customizer experience).
		$this->add_setting( new WP_Customize_Filter_Setting( $this, 'active_theme', array(
			'capability' => 'switch_themes',
		) ) );

		require_once( ABSPATH . 'wp-admin/includes/theme.php' );

		// Theme Controls.

		// Add a control for the active/original theme.
		if ( ! $this->is_theme_active() ) {
			$themes = wp_prepare_themes_for_js( array( wp_get_theme( $this->original_stylesheet ) ) );
			$active_theme = current( $themes );
			$active_theme['isActiveTheme'] = true;
			$this->add_control( new WP_Customize_Theme_Control( $this, $active_theme['id'], array(
				'theme'    => $active_theme,
				'section'  => 'themes',
				'settings' => 'active_theme',
			) ) );
		}

		$themes = wp_prepare_themes_for_js();
		foreach ( $themes as $theme ) {
			if ( $theme['active'] || $theme['id'] === $this->original_stylesheet ) {
				continue;
			}

			$theme_id = 'theme_' . $theme['id'];
			$theme['isActiveTheme'] = false;
			$this->add_control( new WP_Customize_Theme_Control( $this, $theme_id, array(
				'theme'    => $theme,
				'section'  => 'themes',
				'settings' => 'active_theme',
			) ) );
		}

		/* Site Identity */

		$this->add_section( 'title_tagline', array(
			'title'    => __( 'Site Identity' ),
			'priority' => 20,
		) );

		$this->add_setting( 'blogname', array(
			'default'    => get_option( 'blogname' ),
			'type'       => 'option',
			'capability' => 'manage_options',
		) );

		$this->add_control( 'blogname', array(
			'label'      => __( 'Site Title' ),
			'section'    => 'title_tagline',
		) );

		$this->add_setting( 'blogdescription', array(
			'default'    => get_option( 'blogdescription' ),
			'type'       => 'option',
			'capability' => 'manage_options',
		) );

		$this->add_control( 'blogdescription', array(
			'label'      => __( 'Tagline' ),
			'section'    => 'title_tagline',
		) );

		$this->add_setting( 'site_icon', array(
			'type'       => 'option',
			'capability' => 'manage_options',
			'transport'  => 'postMessage', // Previewed with JS in the Customizer controls window.
		) );

		$this->add_control( new WP_Customize_Site_Icon_Control( $this, 'site_icon', array(
			'label'       => __( 'Site Icon' ),
			'description' => __( 'The Site Icon is used as a browser and app icon for your site. Icons must be square, and at least 512px wide and tall.' ),
			'section'     => 'title_tagline',
			'priority'    => 60,
			'height'      => 512,
			'width'       => 512,
		) ) );

		/* Colors */

		$this->add_section( 'colors', array(
			'title'          => __( 'Colors' ),
			'priority'       => 40,
		) );

		$this->add_setting( 'header_textcolor', array(
			'theme_supports' => array( 'custom-header', 'header-text' ),
			'default'        => get_theme_support( 'custom-header', 'default-text-color' ),

			'sanitize_callback'    => array( $this, '_sanitize_header_textcolor' ),
			'sanitize_js_callback' => 'maybe_hash_hex_color',
		) );

		// Input type: checkbox
		// With custom value
		$this->add_control( 'display_header_text', array(
			'settings' => 'header_textcolor',
			'label'    => __( 'Display Header Text' ),
			'section'  => 'title_tagline',
			'type'     => 'checkbox',
			'priority' => 40,
		) );

		$this->add_control( new WP_Customize_Color_Control( $this, 'header_textcolor', array(
			'label'   => __( 'Header Text Color' ),
			'section' => 'colors',
		) ) );

		// Input type: Color
		// With sanitize_callback
		$this->add_setting( 'background_color', array(
			'default'        => get_theme_support( 'custom-background', 'default-color' ),
			'theme_supports' => 'custom-background',

			'sanitize_callback'    => 'sanitize_hex_color_no_hash',
			'sanitize_js_callback' => 'maybe_hash_hex_color',
		) );

		$this->add_control( new WP_Customize_Color_Control( $this, 'background_color', array(
			'label'   => __( 'Background Color' ),
			'section' => 'colors',
		) ) );


		/* Custom Header */

		$this->add_section( 'header_image', array(
			'title'          => __( 'Header Image' ),
			'theme_supports' => 'custom-header',
			'priority'       => 60,
		) );

		$this->add_setting( new WP_Customize_Filter_Setting( $this, 'header_image', array(
			'default'        => get_theme_support( 'custom-header', 'default-image' ),
			'theme_supports' => 'custom-header',
		) ) );

		$this->add_setting( new WP_Customize_Header_Image_Setting( $this, 'header_image_data', array(
			// 'default'        => get_theme_support( 'custom-header', 'default-image' ),
			'theme_supports' => 'custom-header',
		) ) );

		$this->add_control( new WP_Customize_Header_Image_Control( $this ) );

		/* Custom Background */

		$this->add_section( 'background_image', array(
			'title'          => __( 'Background Image' ),
			'theme_supports' => 'custom-background',
			'priority'       => 80,
		) );

		$this->add_setting( 'background_image', array(
			'default'        => get_theme_support( 'custom-background', 'default-image' ),
			'theme_supports' => 'custom-background',
		) );

		$this->add_setting( new WP_Customize_Background_Image_Setting( $this, 'background_image_thumb', array(
			'theme_supports' => 'custom-background',
		) ) );

		$this->add_control( new WP_Customize_Background_Image_Control( $this ) );

		$this->add_setting( 'background_repeat', array(
			'default'        => get_theme_support( 'custom-background', 'default-repeat' ),
			'theme_supports' => 'custom-background',
		) );

		$this->add_control( 'background_repeat', array(
			'label'      => __( 'Background Repeat' ),
			'section'    => 'background_image',
			'type'       => 'radio',
			'choices'    => array(
				'no-repeat'  => __('No Repeat'),
				'repeat'     => __('Tile'),
				'repeat-x'   => __('Tile Horizontally'),
				'repeat-y'   => __('Tile Vertically'),
			),
		) );

		$this->add_setting( 'background_position_x', array(
			'default'        => get_theme_support( 'custom-background', 'default-position-x' ),
			'theme_supports' => 'custom-background',
		) );

		$this->add_control( 'background_position_x', array(
			'label'      => __( 'Background Position' ),
			'section'    => 'background_image',
			'type'       => 'radio',
			'choices'    => array(
				'left'       => __('Left'),
				'center'     => __('Center'),
				'right'      => __('Right'),
			),
		) );

		$this->add_setting( 'background_attachment', array(
			'default'        => get_theme_support( 'custom-background', 'default-attachment' ),
			'theme_supports' => 'custom-background',
		) );

		$this->add_control( 'background_attachment', array(
			'label'      => __( 'Background Attachment' ),
			'section'    => 'background_image',
			'type'       => 'radio',
			'choices'    => array(
				'scroll'     => __('Scroll'),
				'fixed'      => __('Fixed'),
			),
		) );

		// If the theme is using the default background callback, we can update
		// the background CSS using postMessage.
		if ( get_theme_support( 'custom-background', 'wp-head-callback' ) === '_custom_background_cb' ) {
			foreach ( array( 'color', 'image', 'position_x', 'repeat', 'attachment' ) as $prop ) {
				$this->get_setting( 'background_' . $prop )->transport = 'postMessage';
			}
		}

		/* Static Front Page */
		// #WP19627

		// Replicate behavior from options-reading.php and hide front page options if there are no pages
		if ( get_pages() ) {
			$this->add_section( 'static_front_page', array(
				'title'          => __( 'Static Front Page' ),
			//	'theme_supports' => 'static-front-page',
				'priority'       => 120,
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
		}
	}

	/**
	 * Add settings from the POST data that were not added with code, e.g. dynamically-created settings for Widgets
	 *
	 * @since 4.2.0
	 * @access public
	 *
	 * @see add_dynamic_settings()
	 */
	public function register_dynamic_settings() {
		$this->add_dynamic_settings( array_keys( $this->unsanitized_post_values() ) );
	}

	/**
	 * Callback for validating the header_textcolor value.
	 *
	 * Accepts 'blank', and otherwise uses sanitize_hex_color_no_hash().
	 * Returns default text color if hex color is empty.
	 *
	 * @since 3.4.0
	 *
	 * @param string $color
	 * @return mixed
	 */
	public function _sanitize_header_textcolor( $color ) {
		if ( 'blank' === $color )
			return 'blank';

		$color = sanitize_hex_color_no_hash( $color );
		if ( empty( $color ) )
			$color = get_theme_support( 'custom-header', 'default-text-color' );

		return $color;
	}
}

/**
 * Sanitizes a hex color.
 *
 * Returns either '', a 3 or 6 digit hex color (with #), or nothing.
 * For sanitizing values without a #, see sanitize_hex_color_no_hash().
 *
 * @since 3.4.0
 *
 * @param string $color
 * @return string|void
 */
function sanitize_hex_color( $color ) {
	if ( '' === $color )
		return '';

	// 3 or 6 hex digits, or the empty string.
	if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
		return $color;
}

/**
 * Sanitizes a hex color without a hash. Use sanitize_hex_color() when possible.
 *
 * Saving hex colors without a hash puts the burden of adding the hash on the
 * UI, which makes it difficult to use or upgrade to other color types such as
 * rgba, hsl, rgb, and html color names.
 *
 * Returns either '', a 3 or 6 digit hex color (without a #), or null.
 *
 * @since 3.4.0
 *
 * @param string $color
 * @return string|null
 */
function sanitize_hex_color_no_hash( $color ) {
	$color = ltrim( $color, '#' );

	if ( '' === $color )
		return '';

	return sanitize_hex_color( '#' . $color ) ? $color : null;
}

/**
 * Ensures that any hex color is properly hashed.
 * Otherwise, returns value untouched.
 *
 * This method should only be necessary if using sanitize_hex_color_no_hash().
 *
 * @since 3.4.0
 *
 * @param string $color
 * @return string
 */
function maybe_hash_hex_color( $color ) {
	if ( $unhashed = sanitize_hex_color_no_hash( $color ) )
		return '#' . $unhashed;

	return $color;
}

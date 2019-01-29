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
	 * @var WP_Theme
	 */
	protected $theme;

	/**
	 * The directory name of the previously active theme (within the theme_root).
	 *
	 * @since 3.4.0
	 * @var string
	 */
	protected $original_stylesheet;

	/**
	 * Whether this is a Customizer pageload.
	 *
	 * @since 3.4.0
	 * @var bool
	 */
	protected $previewing = false;

	/**
	 * Methods and properties dealing with managing widgets in the Customizer.
	 *
	 * @since 3.9.0
	 * @var WP_Customize_Widgets
	 */
	public $widgets;

	/**
	 * Methods and properties dealing with managing nav menus in the Customizer.
	 *
	 * @since 4.3.0
	 * @var WP_Customize_Nav_Menus
	 */
	public $nav_menus;

	/**
	 * Methods and properties dealing with selective refresh in the Customizer preview.
	 *
	 * @since 4.5.0
	 * @var WP_Customize_Selective_Refresh
	 */
	public $selective_refresh;

	/**
	 * Registered instances of WP_Customize_Setting.
	 *
	 * @since 3.4.0
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Sorted top-level instances of WP_Customize_Panel and WP_Customize_Section.
	 *
	 * @since 4.0.0
	 * @var array
	 */
	protected $containers = array();

	/**
	 * Registered instances of WP_Customize_Panel.
	 *
	 * @since 4.0.0
	 * @var array
	 */
	protected $panels = array();

	/**
	 * List of core components.
	 *
	 * @since 4.5.0
	 * @var array
	 */
	protected $components = array( 'widgets', 'nav_menus' );

	/**
	 * Registered instances of WP_Customize_Section.
	 *
	 * @since 3.4.0
	 * @var array
	 */
	protected $sections = array();

	/**
	 * Registered instances of WP_Customize_Control.
	 *
	 * @since 3.4.0
	 * @var array
	 */
	protected $controls = array();

	/**
	 * Panel types that may be rendered from JS templates.
	 *
	 * @since 4.3.0
	 * @var array
	 */
	protected $registered_panel_types = array();

	/**
	 * Section types that may be rendered from JS templates.
	 *
	 * @since 4.3.0
	 * @var array
	 */
	protected $registered_section_types = array();

	/**
	 * Control types that may be rendered from JS templates.
	 *
	 * @since 4.1.0
	 * @var array
	 */
	protected $registered_control_types = array();

	/**
	 * Initial URL being previewed.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	protected $preview_url;

	/**
	 * URL to link the user to when closing the Customizer.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	protected $return_url;

	/**
	 * Mapping of 'panel', 'section', 'control' to the ID which should be autofocused.
	 *
	 * @since 4.4.0
	 * @var array
	 */
	protected $autofocus = array();

	/**
	 * Messenger channel.
	 *
	 * @since 4.7.0
	 * @var string
	 */
	protected $messenger_channel;

	/**
	 * Whether the autosave revision of the changeset should be loaded.
	 *
	 * @since 4.9.0
	 * @var bool
	 */
	protected $autosaved = false;

	/**
	 * Whether the changeset branching is allowed.
	 *
	 * @since 4.9.0
	 * @var bool
	 */
	protected $branching = true;

	/**
	 * Whether settings should be previewed.
	 *
	 * @since 4.9.0
	 * @var bool
	 */
	protected $settings_previewed = true;

	/**
	 * Whether a starter content changeset was saved.
	 *
	 * @since 4.9.0
	 * @var bool
	 */
	protected $saved_starter_content_changeset = false;

	/**
	 * Unsanitized values for Customize Settings parsed from $_POST['customized'].
	 *
	 * @var array
	 */
	private $_post_values;

	/**
	 * Changeset UUID.
	 *
	 * @since 4.7.0
	 * @var string
	 */
	private $_changeset_uuid;

	/**
	 * Changeset post ID.
	 *
	 * @since 4.7.0
	 * @var int|false
	 */
	private $_changeset_post_id;

	/**
	 * Changeset data loaded from a customize_changeset post.
	 *
	 * @since 4.7.0
	 * @var array
	 */
	private $_changeset_data;

	/**
	 * Constructor.
	 *
	 * @since 3.4.0
	 * @since 4.7.0 Added `$args` parameter.
	 *
	 * @param array $args {
	 *     Args.
	 *
	 *     @type null|string|false $changeset_uuid     Changeset UUID, the `post_name` for the customize_changeset post containing the customized state.
	 *                                                 Defaults to `null` resulting in a UUID to be immediately generated. If `false` is provided, then
	 *                                                 then the changeset UUID will be determined during `after_setup_theme`: when the
	 *                                                 `customize_changeset_branching` filter returns false, then the default UUID will be that
	 *                                                 of the most recent `customize_changeset` post that has a status other than 'auto-draft',
	 *                                                 'publish', or 'trash'. Otherwise, if changeset branching is enabled, then a random UUID will be used.
	 *     @type string            $theme              Theme to be previewed (for theme switch). Defaults to customize_theme or theme query params.
	 *     @type string            $messenger_channel  Messenger channel. Defaults to customize_messenger_channel query param.
	 *     @type bool              $settings_previewed If settings should be previewed. Defaults to true.
	 *     @type bool              $branching          If changeset branching is allowed; otherwise, changesets are linear. Defaults to true.
	 *     @type bool              $autosaved          If data from a changeset's autosaved revision should be loaded if it exists. Defaults to false.
	 * }
	 */
	public function __construct( $args = array() ) {

		$args = array_merge(
			array_fill_keys( array( 'changeset_uuid', 'theme', 'messenger_channel', 'settings_previewed', 'autosaved', 'branching' ), null ),
			$args
		);

		// Note that the UUID format will be validated in the setup_theme() method.
		if ( ! isset( $args['changeset_uuid'] ) ) {
			$args['changeset_uuid'] = wp_generate_uuid4();
		}

		// The theme and messenger_channel should be supplied via $args, but they are also looked at in the $_REQUEST global here for back-compat.
		if ( ! isset( $args['theme'] ) ) {
			if ( isset( $_REQUEST['customize_theme'] ) ) {
				$args['theme'] = wp_unslash( $_REQUEST['customize_theme'] );
			} elseif ( isset( $_REQUEST['theme'] ) ) { // Deprecated.
				$args['theme'] = wp_unslash( $_REQUEST['theme'] );
			}
		}
		if ( ! isset( $args['messenger_channel'] ) && isset( $_REQUEST['customize_messenger_channel'] ) ) {
			$args['messenger_channel'] = sanitize_key( wp_unslash( $_REQUEST['customize_messenger_channel'] ) );
		}

		$this->original_stylesheet = get_stylesheet();
		$this->theme               = wp_get_theme( 0 === validate_file( $args['theme'] ) ? $args['theme'] : null );
		$this->messenger_channel   = $args['messenger_channel'];
		$this->_changeset_uuid     = $args['changeset_uuid'];

		foreach ( array( 'settings_previewed', 'autosaved', 'branching' ) as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = (bool) $args[ $key ];
			}
		}

		require_once( ABSPATH . WPINC . '/class-wp-customize-setting.php' );
		require_once( ABSPATH . WPINC . '/class-wp-customize-panel.php' );
		require_once( ABSPATH . WPINC . '/class-wp-customize-section.php' );
		require_once( ABSPATH . WPINC . '/class-wp-customize-control.php' );

		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-color-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-media-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-upload-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-image-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-background-image-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-background-position-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-cropped-image-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-site-icon-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-header-image-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-theme-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-code-editor-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-widget-area-customize-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-widget-form-customize-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-item-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-location-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-name-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-locations-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-auto-add-control.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-new-menu-control.php' ); // @todo Remove in a future release. See #42364.

		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menus-panel.php' );

		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-themes-panel.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-themes-section.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-sidebar-section.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-section.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-new-menu-section.php' ); // @todo Remove in a future release. See #42364.

		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-custom-css-setting.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-filter-setting.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-header-image-setting.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-background-image-setting.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-item-setting.php' );
		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-setting.php' );

		/**
		 * Filters the core Customizer components to load.
		 *
		 * This allows Core components to be excluded from being instantiated by
		 * filtering them out of the array. Note that this filter generally runs
		 * during the {@see 'plugins_loaded'} action, so it cannot be added
		 * in a theme.
		 *
		 * @since 4.4.0
		 *
		 * @see WP_Customize_Manager::__construct()
		 *
		 * @param string[]             $components Array of core components to load.
		 * @param WP_Customize_Manager $this       WP_Customize_Manager instance.
		 */
		$components = apply_filters( 'customize_loaded_components', $this->components, $this );

		require_once( ABSPATH . WPINC . '/customize/class-wp-customize-selective-refresh.php' );
		$this->selective_refresh = new WP_Customize_Selective_Refresh( $this );

		if ( in_array( 'widgets', $components, true ) ) {
			require_once( ABSPATH . WPINC . '/class-wp-customize-widgets.php' );
			$this->widgets = new WP_Customize_Widgets( $this );
		}

		if ( in_array( 'nav_menus', $components, true ) ) {
			require_once( ABSPATH . WPINC . '/class-wp-customize-nav-menus.php' );
			$this->nav_menus = new WP_Customize_Nav_Menus( $this );
		}

		add_action( 'setup_theme', array( $this, 'setup_theme' ) );
		add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );

		// Do not spawn cron (especially the alternate cron) while running the Customizer.
		remove_action( 'init', 'wp_cron' );

		// Do not run update checks when rendering the controls.
		remove_action( 'admin_init', '_maybe_update_core' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_themes' );

		add_action( 'wp_ajax_customize_save', array( $this, 'save' ) );
		add_action( 'wp_ajax_customize_trash', array( $this, 'handle_changeset_trash_request' ) );
		add_action( 'wp_ajax_customize_refresh_nonces', array( $this, 'refresh_nonces' ) );
		add_action( 'wp_ajax_customize_load_themes', array( $this, 'handle_load_themes_request' ) );
		add_filter( 'heartbeat_settings', array( $this, 'add_customize_screen_to_heartbeat_settings' ) );
		add_filter( 'heartbeat_received', array( $this, 'check_changeset_lock_with_heartbeat' ), 10, 3 );
		add_action( 'wp_ajax_customize_override_changeset_lock', array( $this, 'handle_override_changeset_lock_request' ) );
		add_action( 'wp_ajax_customize_dismiss_autosave_or_lock', array( $this, 'handle_dismiss_autosave_or_lock_request' ) );

		add_action( 'customize_register', array( $this, 'register_controls' ) );
		add_action( 'customize_register', array( $this, 'register_dynamic_settings' ), 11 ); // allow code to create settings first
		add_action( 'customize_controls_init', array( $this, 'prepare_controls' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ) );

		// Render Common, Panel, Section, and Control templates.
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'render_panel_templates' ), 1 );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'render_section_templates' ), 1 );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'render_control_templates' ), 1 );

		// Export header video settings with the partial response.
		add_filter( 'customize_render_partials_response', array( $this, 'export_header_video_settings' ), 10, 3 );

		// Export the settings to JS via the _wpCustomizeSettings variable.
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'customize_pane_settings' ), 1000 );

		// Add theme update notices.
		if ( current_user_can( 'install_themes' ) || current_user_can( 'update_themes' ) ) {
			require_once ABSPATH . '/wp-admin/includes/update.php';
			add_action( 'customize_controls_print_footer_scripts', 'wp_print_admin_notice_templates' );
		}
	}

	/**
	 * Return true if it's an Ajax request.
	 *
	 * @since 3.4.0
	 * @since 4.2.0 Added `$action` param.
	 *
	 * @param string|null $action Whether the supplied Ajax action is being run.
	 * @return bool True if it's an Ajax request, false otherwise.
	 */
	public function doing_ajax( $action = null ) {
		if ( ! wp_doing_ajax() ) {
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
	 * or the Ajax message.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $ajax_message Ajax return
	 * @param mixed $message UI message
	 */
	protected function wp_die( $ajax_message, $message = null ) {
		if ( $this->doing_ajax() ) {
			wp_die( $ajax_message );
		}

		if ( ! $message ) {
			$message = __( 'Something went wrong.' );
		}

		if ( $this->messenger_channel ) {
			ob_start();
			wp_enqueue_scripts();
			wp_print_scripts( array( 'customize-base' ) );

			$settings = array(
				'messengerArgs' => array(
					'channel' => $this->messenger_channel,
					'url'     => wp_customize_url(),
				),
				'error'         => $ajax_message,
			);
			?>
			<script>
			( function( api, settings ) {
				var preview = new api.Messenger( settings.messengerArgs );
				preview.send( 'iframe-loading-error', settings.error );
			} )( wp.customize, <?php echo wp_json_encode( $settings ); ?> );
			</script>
			<?php
			$message .= ob_get_clean();
		}

		wp_die( $message );
	}

	/**
	 * Return the Ajax wp_die() handler if it's a customized request.
	 *
	 * @since 3.4.0
	 * @deprecated 4.7.0
	 *
	 * @return callable Die handler.
	 */
	public function wp_die_handler() {
		_deprecated_function( __METHOD__, '4.7.0' );

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
	 *
	 * @global string $pagenow
	 */
	public function setup_theme() {
		global $pagenow;

		// Check permissions for customize.php access since this method is called before customize.php can run any code,
		if ( 'customize.php' === $pagenow && ! current_user_can( 'customize' ) ) {
			if ( ! is_user_logged_in() ) {
				auth_redirect();
			} else {
				wp_die(
					'<h1>' . __( 'You need a higher level of permission.' ) . '</h1>' .
					'<p>' . __( 'Sorry, you are not allowed to customize this site.' ) . '</p>',
					403
				);
			}
			return;
		}

		// If a changeset was provided is invalid.
		if ( isset( $this->_changeset_uuid ) && false !== $this->_changeset_uuid && ! wp_is_uuid( $this->_changeset_uuid ) ) {
			$this->wp_die( -1, __( 'Invalid changeset UUID' ) );
		}

		/*
		 * Clear incoming post data if the user lacks a CSRF token (nonce). Note that the customizer
		 * application will inject the customize_preview_nonce query parameter into all Ajax requests.
		 * For similar behavior elsewhere in WordPress, see rest_cookie_check_errors() which logs out
		 * a user when a valid nonce isn't present.
		 */
		$has_post_data_nonce = (
			check_ajax_referer( 'preview-customize_' . $this->get_stylesheet(), 'nonce', false )
			||
			check_ajax_referer( 'save-customize_' . $this->get_stylesheet(), 'nonce', false )
			||
			check_ajax_referer( 'preview-customize_' . $this->get_stylesheet(), 'customize_preview_nonce', false )
		);
		if ( ! current_user_can( 'customize' ) || ! $has_post_data_nonce ) {
			unset( $_POST['customized'] );
			unset( $_REQUEST['customized'] );
		}

		/*
		 * If unauthenticated then require a valid changeset UUID to load the preview.
		 * In this way, the UUID serves as a secret key. If the messenger channel is present,
		 * then send unauthenticated code to prompt re-auth.
		 */
		if ( ! current_user_can( 'customize' ) && ! $this->changeset_post_id() ) {
			$this->wp_die( $this->messenger_channel ? 0 : -1, __( 'Non-existent changeset UUID.' ) );
		}

		if ( ! headers_sent() ) {
			send_origin_headers();
		}

		// Hide the admin bar if we're embedded in the customizer iframe.
		if ( $this->messenger_channel ) {
			show_admin_bar( false );
		}

		if ( $this->is_theme_active() ) {
			// Once the theme is loaded, we'll validate it.
			add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
		} else {
			// If the requested theme is not the active theme and the user doesn't have the
			// switch_themes cap, bail.
			if ( ! current_user_can( 'switch_themes' ) ) {
				$this->wp_die( -1, __( 'Sorry, you are not allowed to edit theme options on this site.' ) );
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

		// Make sure changeset UUID is established immediately after the theme is loaded.
		add_action( 'after_setup_theme', array( $this, 'establish_loaded_changeset' ), 5 );

		/*
		 * Import theme starter content for fresh installations when landing in the customizer.
		 * Import starter content at after_setup_theme:100 so that any
		 * add_theme_support( 'starter-content' ) calls will have been made.
		 */
		if ( get_option( 'fresh_site' ) && 'customize.php' === $pagenow ) {
			add_action( 'after_setup_theme', array( $this, 'import_theme_starter_content' ), 100 );
		}

		$this->start_previewing_theme();
	}

	/**
	 * Establish the loaded changeset.
	 *
	 * This method runs right at after_setup_theme and applies the 'customize_changeset_branching' filter to determine
	 * whether concurrent changesets are allowed. Then if the Customizer is not initialized with a `changeset_uuid` param,
	 * this method will determine which UUID should be used. If changeset branching is disabled, then the most saved
	 * changeset will be loaded by default. Otherwise, if there are no existing saved changesets or if changeset branching is
	 * enabled, then a new UUID will be generated.
	 *
	 * @since 4.9.0
	 * @global string $pagenow
	 */
	public function establish_loaded_changeset() {
		global $pagenow;

		if ( empty( $this->_changeset_uuid ) ) {
			$changeset_uuid = null;

			if ( ! $this->branching() && $this->is_theme_active() ) {
				$unpublished_changeset_posts = $this->get_changeset_posts(
					array(
						'post_status'               => array_diff( get_post_stati(), array( 'auto-draft', 'publish', 'trash', 'inherit', 'private' ) ),
						'exclude_restore_dismissed' => false,
						'author'                    => 'any',
						'posts_per_page'            => 1,
						'order'                     => 'DESC',
						'orderby'                   => 'date',
					)
				);
				$unpublished_changeset_post  = array_shift( $unpublished_changeset_posts );
				if ( ! empty( $unpublished_changeset_post ) && wp_is_uuid( $unpublished_changeset_post->post_name ) ) {
					$changeset_uuid = $unpublished_changeset_post->post_name;
				}
			}

			// If no changeset UUID has been set yet, then generate a new one.
			if ( empty( $changeset_uuid ) ) {
				$changeset_uuid = wp_generate_uuid4();
			}

			$this->_changeset_uuid = $changeset_uuid;
		}

		if ( is_admin() && 'customize.php' === $pagenow ) {
			$this->set_changeset_lock( $this->changeset_post_id() );
		}
	}

	/**
	 * Callback to validate a theme once it is loaded
	 *
	 * @since 3.4.0
	 */
	public function after_setup_theme() {
		$doing_ajax_or_is_customized = ( $this->doing_ajax() || isset( $_POST['customized'] ) );
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
	 * Gets whether settings are or will be previewed.
	 *
	 * @since 4.9.0
	 * @see WP_Customize_Setting::preview()
	 *
	 * @return bool
	 */
	public function settings_previewed() {
		return $this->settings_previewed;
	}

	/**
	 * Gets whether data from a changeset's autosaved revision should be loaded if it exists.
	 *
	 * @since 4.9.0
	 * @see WP_Customize_Manager::changeset_data()
	 *
	 * @return bool Is using autosaved changeset revision.
	 */
	public function autosaved() {
		return $this->autosaved;
	}

	/**
	 * Whether the changeset branching is allowed.
	 *
	 * @since 4.9.0
	 * @see WP_Customize_Manager::establish_loaded_changeset()
	 *
	 * @return bool Is changeset branching.
	 */
	public function branching() {

		/**
		 * Filters whether or not changeset branching is allowed.
		 *
		 * By default in core, when changeset branching is not allowed, changesets will operate
		 * linearly in that only one saved changeset will exist at a time (with a 'draft' or
		 * 'future' status). This makes the Customizer operate in a way that is similar to going to
		 * "edit" to one existing post: all users will be making changes to the same post, and autosave
		 * revisions will be made for that post.
		 *
		 * By contrast, when changeset branching is allowed, then the model is like users going
		 * to "add new" for a page and each user makes changes independently of each other since
		 * they are all operating on their own separate pages, each getting their own separate
		 * initial auto-drafts and then once initially saved, autosave revisions on top of that
		 * user's specific post.
		 *
		 * Since linear changesets are deemed to be more suitable for the majority of WordPress users,
		 * they are the default. For WordPress sites that have heavy site management in the Customizer
		 * by multiple users then branching changesets should be enabled by means of this filter.
		 *
		 * @since 4.9.0
		 *
		 * @param bool                 $allow_branching Whether branching is allowed. If `false`, the default,
		 *                                              then only one saved changeset exists at a time.
		 * @param WP_Customize_Manager $wp_customize    Manager instance.
		 */
		$this->branching = apply_filters( 'customize_changeset_branching', $this->branching, $this );

		return $this->branching;
	}

	/**
	 * Get the changeset UUID.
	 *
	 * @since 4.7.0
	 * @see WP_Customize_Manager::establish_loaded_changeset()
	 *
	 * @return string UUID.
	 */
	public function changeset_uuid() {
		if ( empty( $this->_changeset_uuid ) ) {
			$this->establish_loaded_changeset();
		}
		return $this->_changeset_uuid;
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

		// Unconditionally register core types for panels, sections, and controls in case plugin unhooks all customize_register actions.
		$this->register_panel_type( 'WP_Customize_Panel' );
		$this->register_panel_type( 'WP_Customize_Themes_Panel' );
		$this->register_section_type( 'WP_Customize_Section' );
		$this->register_section_type( 'WP_Customize_Sidebar_Section' );
		$this->register_section_type( 'WP_Customize_Themes_Section' );
		$this->register_control_type( 'WP_Customize_Color_Control' );
		$this->register_control_type( 'WP_Customize_Media_Control' );
		$this->register_control_type( 'WP_Customize_Upload_Control' );
		$this->register_control_type( 'WP_Customize_Image_Control' );
		$this->register_control_type( 'WP_Customize_Background_Image_Control' );
		$this->register_control_type( 'WP_Customize_Background_Position_Control' );
		$this->register_control_type( 'WP_Customize_Cropped_Image_Control' );
		$this->register_control_type( 'WP_Customize_Site_Icon_Control' );
		$this->register_control_type( 'WP_Customize_Theme_Control' );
		$this->register_control_type( 'WP_Customize_Code_Editor_Control' );
		$this->register_control_type( 'WP_Customize_Date_Time_Control' );

		/**
		 * Fires once WordPress has loaded, allowing scripts and styles to be initialized.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Manager $this WP_Customize_Manager instance.
		 */
		do_action( 'customize_register', $this );

		if ( $this->settings_previewed() ) {
			foreach ( $this->settings as $setting ) {
				$setting->preview();
			}
		}

		if ( $this->is_preview() && ! is_admin() ) {
			$this->customize_preview_init();
		}
	}

	/**
	 * Prevents Ajax requests from following redirects when previewing a theme
	 * by issuing a 200 response instead of a 30x.
	 *
	 * Instead, the JS will sniff out the location header.
	 *
	 * @since 3.4.0
	 * @deprecated 4.7.0
	 *
	 * @param int $status Status.
	 * @return int
	 */
	public function wp_redirect_status( $status ) {
		_deprecated_function( __FUNCTION__, '4.7.0' );

		if ( $this->is_preview() && ! is_admin() ) {
			return 200;
		}

		return $status;
	}

	/**
	 * Find the changeset post ID for a given changeset UUID.
	 *
	 * @since 4.7.0
	 *
	 * @param string $uuid Changeset UUID.
	 * @return int|null Returns post ID on success and null on failure.
	 */
	public function find_changeset_post_id( $uuid ) {
		$cache_group       = 'customize_changeset_post';
		$changeset_post_id = wp_cache_get( $uuid, $cache_group );
		if ( $changeset_post_id && 'customize_changeset' === get_post_type( $changeset_post_id ) ) {
			return $changeset_post_id;
		}

		$changeset_post_query = new WP_Query(
			array(
				'post_type'              => 'customize_changeset',
				'post_status'            => get_post_stati(),
				'name'                   => $uuid,
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'cache_results'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'lazy_load_term_meta'    => false,
			)
		);
		if ( ! empty( $changeset_post_query->posts ) ) {
			// Note: 'fields'=>'ids' is not being used in order to cache the post object as it will be needed.
			$changeset_post_id = $changeset_post_query->posts[0]->ID;
			wp_cache_set( $uuid, $changeset_post_id, $cache_group );
			return $changeset_post_id;
		}

		return null;
	}

	/**
	 * Get changeset posts.
	 *
	 * @since 4.9.0
	 *
	 * @param array $args {
	 *     Args to pass into `get_posts()` to query changesets.
	 *
	 *     @type int    $posts_per_page             Number of posts to return. Defaults to -1 (all posts).
	 *     @type int    $author                     Post author. Defaults to current user.
	 *     @type string $post_status                Status of changeset. Defaults to 'auto-draft'.
	 *     @type bool   $exclude_restore_dismissed  Whether to exclude changeset auto-drafts that have been dismissed. Defaults to true.
	 * }
	 * @return WP_Post[] Auto-draft changesets.
	 */
	protected function get_changeset_posts( $args = array() ) {
		$default_args = array(
			'exclude_restore_dismissed' => true,
			'posts_per_page'            => -1,
			'post_type'                 => 'customize_changeset',
			'post_status'               => 'auto-draft',
			'order'                     => 'DESC',
			'orderby'                   => 'date',
			'no_found_rows'             => true,
			'cache_results'             => true,
			'update_post_meta_cache'    => false,
			'update_post_term_cache'    => false,
			'lazy_load_term_meta'       => false,
		);
		if ( get_current_user_id() ) {
			$default_args['author'] = get_current_user_id();
		}
		$args = array_merge( $default_args, $args );

		if ( ! empty( $args['exclude_restore_dismissed'] ) ) {
			unset( $args['exclude_restore_dismissed'] );
			$args['meta_query'] = array(
				array(
					'key'     => '_customize_restore_dismissed',
					'compare' => 'NOT EXISTS',
				),
			);
		}

		return get_posts( $args );
	}

	/**
	 * Dismiss all of the current user's auto-drafts (other than the present one).
	 *
	 * @since 4.9.0
	 * @return int The number of auto-drafts that were dismissed.
	 */
	protected function dismiss_user_auto_draft_changesets() {
		$changeset_autodraft_posts = $this->get_changeset_posts(
			array(
				'post_status'               => 'auto-draft',
				'exclude_restore_dismissed' => true,
				'posts_per_page'            => -1,
			)
		);
		$dismissed                 = 0;
		foreach ( $changeset_autodraft_posts as $autosave_autodraft_post ) {
			if ( $autosave_autodraft_post->ID === $this->changeset_post_id() ) {
				continue;
			}
			if ( update_post_meta( $autosave_autodraft_post->ID, '_customize_restore_dismissed', true ) ) {
				$dismissed++;
			}
		}
		return $dismissed;
	}

	/**
	 * Get the changeset post id for the loaded changeset.
	 *
	 * @since 4.7.0
	 *
	 * @return int|null Post ID on success or null if there is no post yet saved.
	 */
	public function changeset_post_id() {
		if ( ! isset( $this->_changeset_post_id ) ) {
			$post_id = $this->find_changeset_post_id( $this->changeset_uuid() );
			if ( ! $post_id ) {
				$post_id = false;
			}
			$this->_changeset_post_id = $post_id;
		}
		if ( false === $this->_changeset_post_id ) {
			return null;
		}
		return $this->_changeset_post_id;
	}

	/**
	 * Get the data stored in a changeset post.
	 *
	 * @since 4.7.0
	 *
	 * @param int $post_id Changeset post ID.
	 * @return array|WP_Error Changeset data or WP_Error on error.
	 */
	protected function get_changeset_post_data( $post_id ) {
		if ( ! $post_id ) {
			return new WP_Error( 'empty_post_id' );
		}
		$changeset_post = get_post( $post_id );
		if ( ! $changeset_post ) {
			return new WP_Error( 'missing_post' );
		}
		if ( 'revision' === $changeset_post->post_type ) {
			if ( 'customize_changeset' !== get_post_type( $changeset_post->post_parent ) ) {
				return new WP_Error( 'wrong_post_type' );
			}
		} elseif ( 'customize_changeset' !== $changeset_post->post_type ) {
			return new WP_Error( 'wrong_post_type' );
		}
		$changeset_data = json_decode( $changeset_post->post_content, true );
		if ( function_exists( 'json_last_error' ) && json_last_error() ) {
			return new WP_Error( 'json_parse_error', '', json_last_error() );
		}
		if ( ! is_array( $changeset_data ) ) {
			return new WP_Error( 'expected_array' );
		}
		return $changeset_data;
	}

	/**
	 * Get changeset data.
	 *
	 * @since 4.7.0
	 * @since 4.9.0 This will return the changeset's data with a user's autosave revision merged on top, if one exists and $autosaved is true.
	 *
	 * @return array Changeset data.
	 */
	public function changeset_data() {
		if ( isset( $this->_changeset_data ) ) {
			return $this->_changeset_data;
		}
		$changeset_post_id = $this->changeset_post_id();
		if ( ! $changeset_post_id ) {
			$this->_changeset_data = array();
		} else {
			if ( $this->autosaved() && is_user_logged_in() ) {
				$autosave_post = wp_get_post_autosave( $changeset_post_id, get_current_user_id() );
				if ( $autosave_post ) {
					$data = $this->get_changeset_post_data( $autosave_post->ID );
					if ( ! is_wp_error( $data ) ) {
						$this->_changeset_data = $data;
					}
				}
			}

			// Load data from the changeset if it was not loaded from an autosave.
			if ( ! isset( $this->_changeset_data ) ) {
				$data = $this->get_changeset_post_data( $changeset_post_id );
				if ( ! is_wp_error( $data ) ) {
					$this->_changeset_data = $data;
				} else {
					$this->_changeset_data = array();
				}
			}
		}
		return $this->_changeset_data;
	}

	/**
	 * Starter content setting IDs.
	 *
	 * @since 4.7.0
	 * @var array
	 */
	protected $pending_starter_content_settings_ids = array();

	/**
	 * Import theme starter content into the customized state.
	 *
	 * @since 4.7.0
	 *
	 * @param array $starter_content Starter content. Defaults to `get_theme_starter_content()`.
	 */
	function import_theme_starter_content( $starter_content = array() ) {
		if ( empty( $starter_content ) ) {
			$starter_content = get_theme_starter_content();
		}

		$changeset_data = array();
		if ( $this->changeset_post_id() ) {
			/*
			 * Don't re-import starter content into a changeset saved persistently.
			 * This will need to be revisited in the future once theme switching
			 * is allowed with drafted/scheduled changesets, since switching to
			 * another theme could result in more starter content being applied.
			 * However, when doing an explicit save it is currently possible for
			 * nav menus and nav menu items specifically to lose their starter_content
			 * flags, thus resulting in duplicates being created since they fail
			 * to get re-used. See #40146.
			 */
			if ( 'auto-draft' !== get_post_status( $this->changeset_post_id() ) ) {
				return;
			}

			$changeset_data = $this->get_changeset_post_data( $this->changeset_post_id() );
		}

		$sidebars_widgets = isset( $starter_content['widgets'] ) && ! empty( $this->widgets ) ? $starter_content['widgets'] : array();
		$attachments      = isset( $starter_content['attachments'] ) && ! empty( $this->nav_menus ) ? $starter_content['attachments'] : array();
		$posts            = isset( $starter_content['posts'] ) && ! empty( $this->nav_menus ) ? $starter_content['posts'] : array();
		$options          = isset( $starter_content['options'] ) ? $starter_content['options'] : array();
		$nav_menus        = isset( $starter_content['nav_menus'] ) && ! empty( $this->nav_menus ) ? $starter_content['nav_menus'] : array();
		$theme_mods       = isset( $starter_content['theme_mods'] ) ? $starter_content['theme_mods'] : array();

		// Widgets.
		$max_widget_numbers = array();
		foreach ( $sidebars_widgets as $sidebar_id => $widgets ) {
			$sidebar_widget_ids = array();
			foreach ( $widgets as $widget ) {
				list( $id_base, $instance ) = $widget;

				if ( ! isset( $max_widget_numbers[ $id_base ] ) ) {

					// When $settings is an array-like object, get an intrinsic array for use with array_keys().
					$settings = get_option( "widget_{$id_base}", array() );
					if ( $settings instanceof ArrayObject || $settings instanceof ArrayIterator ) {
						$settings = $settings->getArrayCopy();
					}

					// Find the max widget number for this type.
					$widget_numbers = array_keys( $settings );
					if ( count( $widget_numbers ) > 0 ) {
						$widget_numbers[]               = 1;
						$max_widget_numbers[ $id_base ] = call_user_func_array( 'max', $widget_numbers );
					} else {
						$max_widget_numbers[ $id_base ] = 1;
					}
				}
				$max_widget_numbers[ $id_base ] += 1;

				$widget_id  = sprintf( '%s-%d', $id_base, $max_widget_numbers[ $id_base ] );
				$setting_id = sprintf( 'widget_%s[%d]', $id_base, $max_widget_numbers[ $id_base ] );

				$setting_value = $this->widgets->sanitize_widget_js_instance( $instance );
				if ( empty( $changeset_data[ $setting_id ] ) || ! empty( $changeset_data[ $setting_id ]['starter_content'] ) ) {
					$this->set_post_value( $setting_id, $setting_value );
					$this->pending_starter_content_settings_ids[] = $setting_id;
				}
				$sidebar_widget_ids[] = $widget_id;
			}

			$setting_id = sprintf( 'sidebars_widgets[%s]', $sidebar_id );
			if ( empty( $changeset_data[ $setting_id ] ) || ! empty( $changeset_data[ $setting_id ]['starter_content'] ) ) {
				$this->set_post_value( $setting_id, $sidebar_widget_ids );
				$this->pending_starter_content_settings_ids[] = $setting_id;
			}
		}

		$starter_content_auto_draft_post_ids = array();
		if ( ! empty( $changeset_data['nav_menus_created_posts']['value'] ) ) {
			$starter_content_auto_draft_post_ids = array_merge( $starter_content_auto_draft_post_ids, $changeset_data['nav_menus_created_posts']['value'] );
		}

		// Make an index of all the posts needed and what their slugs are.
		$needed_posts = array();
		$attachments  = $this->prepare_starter_content_attachments( $attachments );
		foreach ( $attachments as $attachment ) {
			$key                  = 'attachment:' . $attachment['post_name'];
			$needed_posts[ $key ] = true;
		}
		foreach ( array_keys( $posts ) as $post_symbol ) {
			if ( empty( $posts[ $post_symbol ]['post_name'] ) && empty( $posts[ $post_symbol ]['post_title'] ) ) {
				unset( $posts[ $post_symbol ] );
				continue;
			}
			if ( empty( $posts[ $post_symbol ]['post_name'] ) ) {
				$posts[ $post_symbol ]['post_name'] = sanitize_title( $posts[ $post_symbol ]['post_title'] );
			}
			if ( empty( $posts[ $post_symbol ]['post_type'] ) ) {
				$posts[ $post_symbol ]['post_type'] = 'post';
			}
			$needed_posts[ $posts[ $post_symbol ]['post_type'] . ':' . $posts[ $post_symbol ]['post_name'] ] = true;
		}
		$all_post_slugs = array_merge(
			wp_list_pluck( $attachments, 'post_name' ),
			wp_list_pluck( $posts, 'post_name' )
		);

		/*
		 * Obtain all post types referenced in starter content to use in query.
		 * This is needed because 'any' will not account for post types not yet registered.
		 */
		$post_types = array_filter( array_merge( array( 'attachment' ), wp_list_pluck( $posts, 'post_type' ) ) );

		// Re-use auto-draft starter content posts referenced in the current customized state.
		$existing_starter_content_posts = array();
		if ( ! empty( $starter_content_auto_draft_post_ids ) ) {
			$existing_posts_query = new WP_Query(
				array(
					'post__in'       => $starter_content_auto_draft_post_ids,
					'post_status'    => 'auto-draft',
					'post_type'      => $post_types,
					'posts_per_page' => -1,
				)
			);
			foreach ( $existing_posts_query->posts as $existing_post ) {
				$post_name = $existing_post->post_name;
				if ( empty( $post_name ) ) {
					$post_name = get_post_meta( $existing_post->ID, '_customize_draft_post_name', true );
				}
				$existing_starter_content_posts[ $existing_post->post_type . ':' . $post_name ] = $existing_post;
			}
		}

		// Re-use non-auto-draft posts.
		if ( ! empty( $all_post_slugs ) ) {
			$existing_posts_query = new WP_Query(
				array(
					'post_name__in'  => $all_post_slugs,
					'post_status'    => array_diff( get_post_stati(), array( 'auto-draft' ) ),
					'post_type'      => 'any',
					'posts_per_page' => -1,
				)
			);
			foreach ( $existing_posts_query->posts as $existing_post ) {
				$key = $existing_post->post_type . ':' . $existing_post->post_name;
				if ( isset( $needed_posts[ $key ] ) && ! isset( $existing_starter_content_posts[ $key ] ) ) {
					$existing_starter_content_posts[ $key ] = $existing_post;
				}
			}
		}

		// Attachments are technically posts but handled differently.
		if ( ! empty( $attachments ) ) {

			$attachment_ids = array();

			foreach ( $attachments as $symbol => $attachment ) {
				$file_array    = array(
					'name' => $attachment['file_name'],
				);
				$file_path     = $attachment['file_path'];
				$attachment_id = null;
				$attached_file = null;
				if ( isset( $existing_starter_content_posts[ 'attachment:' . $attachment['post_name'] ] ) ) {
					$attachment_post = $existing_starter_content_posts[ 'attachment:' . $attachment['post_name'] ];
					$attachment_id   = $attachment_post->ID;
					$attached_file   = get_attached_file( $attachment_id );
					if ( empty( $attached_file ) || ! file_exists( $attached_file ) ) {
						$attachment_id = null;
						$attached_file = null;
					} elseif ( $this->get_stylesheet() !== get_post_meta( $attachment_post->ID, '_starter_content_theme', true ) ) {

						// Re-generate attachment metadata since it was previously generated for a different theme.
						$metadata = wp_generate_attachment_metadata( $attachment_post->ID, $attached_file );
						wp_update_attachment_metadata( $attachment_id, $metadata );
						update_post_meta( $attachment_id, '_starter_content_theme', $this->get_stylesheet() );
					}
				}

				// Insert the attachment auto-draft because it doesn't yet exist or the attached file is gone.
				if ( ! $attachment_id ) {

					// Copy file to temp location so that original file won't get deleted from theme after sideloading.
					$temp_file_name = wp_tempnam( basename( $file_path ) );
					if ( $temp_file_name && copy( $file_path, $temp_file_name ) ) {
						$file_array['tmp_name'] = $temp_file_name;
					}
					if ( empty( $file_array['tmp_name'] ) ) {
						continue;
					}

					$attachment_post_data = array_merge(
						wp_array_slice_assoc( $attachment, array( 'post_title', 'post_content', 'post_excerpt' ) ),
						array(
							'post_status' => 'auto-draft', // So attachment will be garbage collected in a week if changeset is never published.
						)
					);

					// In PHP < 5.6 filesize() returns 0 for the temp files unless we clear the file status cache.
					// Technically, PHP < 5.6.0 || < 5.5.13 || < 5.4.29 but no need to be so targeted.
					// See https://bugs.php.net/bug.php?id=65701
					if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
						clearstatcache();
					}

					$attachment_id = media_handle_sideload( $file_array, 0, null, $attachment_post_data );
					if ( is_wp_error( $attachment_id ) ) {
						continue;
					}
					update_post_meta( $attachment_id, '_starter_content_theme', $this->get_stylesheet() );
					update_post_meta( $attachment_id, '_customize_draft_post_name', $attachment['post_name'] );
				}

				$attachment_ids[ $symbol ] = $attachment_id;
			}
			$starter_content_auto_draft_post_ids = array_merge( $starter_content_auto_draft_post_ids, array_values( $attachment_ids ) );
		}

		// Posts & pages.
		if ( ! empty( $posts ) ) {
			foreach ( array_keys( $posts ) as $post_symbol ) {
				if ( empty( $posts[ $post_symbol ]['post_type'] ) || empty( $posts[ $post_symbol ]['post_name'] ) ) {
					continue;
				}
				$post_type = $posts[ $post_symbol ]['post_type'];
				if ( ! empty( $posts[ $post_symbol ]['post_name'] ) ) {
					$post_name = $posts[ $post_symbol ]['post_name'];
				} elseif ( ! empty( $posts[ $post_symbol ]['post_title'] ) ) {
					$post_name = sanitize_title( $posts[ $post_symbol ]['post_title'] );
				} else {
					continue;
				}

				// Use existing auto-draft post if one already exists with the same type and name.
				if ( isset( $existing_starter_content_posts[ $post_type . ':' . $post_name ] ) ) {
					$posts[ $post_symbol ]['ID'] = $existing_starter_content_posts[ $post_type . ':' . $post_name ]->ID;
					continue;
				}

				// Translate the featured image symbol.
				if ( ! empty( $posts[ $post_symbol ]['thumbnail'] )
					&& preg_match( '/^{{(?P<symbol>.+)}}$/', $posts[ $post_symbol ]['thumbnail'], $matches )
					&& isset( $attachment_ids[ $matches['symbol'] ] ) ) {
					$posts[ $post_symbol ]['meta_input']['_thumbnail_id'] = $attachment_ids[ $matches['symbol'] ];
				}

				if ( ! empty( $posts[ $post_symbol ]['template'] ) ) {
					$posts[ $post_symbol ]['meta_input']['_wp_page_template'] = $posts[ $post_symbol ]['template'];
				}

				$r = $this->nav_menus->insert_auto_draft_post( $posts[ $post_symbol ] );
				if ( $r instanceof WP_Post ) {
					$posts[ $post_symbol ]['ID'] = $r->ID;
				}
			}

			$starter_content_auto_draft_post_ids = array_merge( $starter_content_auto_draft_post_ids, wp_list_pluck( $posts, 'ID' ) );
		}

		// The nav_menus_created_posts setting is why nav_menus component is dependency for adding posts.
		if ( ! empty( $this->nav_menus ) && ! empty( $starter_content_auto_draft_post_ids ) ) {
			$setting_id = 'nav_menus_created_posts';
			$this->set_post_value( $setting_id, array_unique( array_values( $starter_content_auto_draft_post_ids ) ) );
			$this->pending_starter_content_settings_ids[] = $setting_id;
		}

		// Nav menus.
		$placeholder_id              = -1;
		$reused_nav_menu_setting_ids = array();
		foreach ( $nav_menus as $nav_menu_location => $nav_menu ) {

			$nav_menu_term_id    = null;
			$nav_menu_setting_id = null;
			$matches             = array();

			// Look for an existing placeholder menu with starter content to re-use.
			foreach ( $changeset_data as $setting_id => $setting_params ) {
				$can_reuse = (
					! empty( $setting_params['starter_content'] )
					&&
					! in_array( $setting_id, $reused_nav_menu_setting_ids, true )
					&&
					preg_match( '#^nav_menu\[(?P<nav_menu_id>-?\d+)\]$#', $setting_id, $matches )
				);
				if ( $can_reuse ) {
					$nav_menu_term_id              = intval( $matches['nav_menu_id'] );
					$nav_menu_setting_id           = $setting_id;
					$reused_nav_menu_setting_ids[] = $setting_id;
					break;
				}
			}

			if ( ! $nav_menu_term_id ) {
				while ( isset( $changeset_data[ sprintf( 'nav_menu[%d]', $placeholder_id ) ] ) ) {
					$placeholder_id--;
				}
				$nav_menu_term_id    = $placeholder_id;
				$nav_menu_setting_id = sprintf( 'nav_menu[%d]', $placeholder_id );
			}

			$this->set_post_value(
				$nav_menu_setting_id,
				array(
					'name' => isset( $nav_menu['name'] ) ? $nav_menu['name'] : $nav_menu_location,
				)
			);
			$this->pending_starter_content_settings_ids[] = $nav_menu_setting_id;

			// @todo Add support for menu_item_parent.
			$position = 0;
			foreach ( $nav_menu['items'] as $nav_menu_item ) {
				$nav_menu_item_setting_id = sprintf( 'nav_menu_item[%d]', $placeholder_id-- );
				if ( ! isset( $nav_menu_item['position'] ) ) {
					$nav_menu_item['position'] = $position++;
				}
				$nav_menu_item['nav_menu_term_id'] = $nav_menu_term_id;

				if ( isset( $nav_menu_item['object_id'] ) ) {
					if ( 'post_type' === $nav_menu_item['type'] && preg_match( '/^{{(?P<symbol>.+)}}$/', $nav_menu_item['object_id'], $matches ) && isset( $posts[ $matches['symbol'] ] ) ) {
						$nav_menu_item['object_id'] = $posts[ $matches['symbol'] ]['ID'];
						if ( empty( $nav_menu_item['title'] ) ) {
							$original_object        = get_post( $nav_menu_item['object_id'] );
							$nav_menu_item['title'] = $original_object->post_title;
						}
					} else {
						continue;
					}
				} else {
					$nav_menu_item['object_id'] = 0;
				}

				if ( empty( $changeset_data[ $nav_menu_item_setting_id ] ) || ! empty( $changeset_data[ $nav_menu_item_setting_id ]['starter_content'] ) ) {
					$this->set_post_value( $nav_menu_item_setting_id, $nav_menu_item );
					$this->pending_starter_content_settings_ids[] = $nav_menu_item_setting_id;
				}
			}

			$setting_id = sprintf( 'nav_menu_locations[%s]', $nav_menu_location );
			if ( empty( $changeset_data[ $setting_id ] ) || ! empty( $changeset_data[ $setting_id ]['starter_content'] ) ) {
				$this->set_post_value( $setting_id, $nav_menu_term_id );
				$this->pending_starter_content_settings_ids[] = $setting_id;
			}
		}

		// Options.
		foreach ( $options as $name => $value ) {
			if ( preg_match( '/^{{(?P<symbol>.+)}}$/', $value, $matches ) ) {
				if ( isset( $posts[ $matches['symbol'] ] ) ) {
					$value = $posts[ $matches['symbol'] ]['ID'];
				} elseif ( isset( $attachment_ids[ $matches['symbol'] ] ) ) {
					$value = $attachment_ids[ $matches['symbol'] ];
				} else {
					continue;
				}
			}

			if ( empty( $changeset_data[ $name ] ) || ! empty( $changeset_data[ $name ]['starter_content'] ) ) {
				$this->set_post_value( $name, $value );
				$this->pending_starter_content_settings_ids[] = $name;
			}
		}

		// Theme mods.
		foreach ( $theme_mods as $name => $value ) {
			if ( preg_match( '/^{{(?P<symbol>.+)}}$/', $value, $matches ) ) {
				if ( isset( $posts[ $matches['symbol'] ] ) ) {
					$value = $posts[ $matches['symbol'] ]['ID'];
				} elseif ( isset( $attachment_ids[ $matches['symbol'] ] ) ) {
					$value = $attachment_ids[ $matches['symbol'] ];
				} else {
					continue;
				}
			}

			// Handle header image as special case since setting has a legacy format.
			if ( 'header_image' === $name ) {
				$name     = 'header_image_data';
				$metadata = wp_get_attachment_metadata( $value );
				if ( empty( $metadata ) ) {
					continue;
				}
				$value = array(
					'attachment_id' => $value,
					'url'           => wp_get_attachment_url( $value ),
					'height'        => $metadata['height'],
					'width'         => $metadata['width'],
				);
			} elseif ( 'background_image' === $name ) {
				$value = wp_get_attachment_url( $value );
			}

			if ( empty( $changeset_data[ $name ] ) || ! empty( $changeset_data[ $name ]['starter_content'] ) ) {
				$this->set_post_value( $name, $value );
				$this->pending_starter_content_settings_ids[] = $name;
			}
		}

		if ( ! empty( $this->pending_starter_content_settings_ids ) ) {
			if ( did_action( 'customize_register' ) ) {
				$this->_save_starter_content_changeset();
			} else {
				add_action( 'customize_register', array( $this, '_save_starter_content_changeset' ), 1000 );
			}
		}
	}

	/**
	 * Prepare starter content attachments.
	 *
	 * Ensure that the attachments are valid and that they have slugs and file name/path.
	 *
	 * @since 4.7.0
	 *
	 * @param array $attachments Attachments.
	 * @return array Prepared attachments.
	 */
	protected function prepare_starter_content_attachments( $attachments ) {
		$prepared_attachments = array();
		if ( empty( $attachments ) ) {
			return $prepared_attachments;
		}

		// Such is The WordPress Way.
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		foreach ( $attachments as $symbol => $attachment ) {

			// A file is required and URLs to files are not currently allowed.
			if ( empty( $attachment['file'] ) || preg_match( '#^https?://$#', $attachment['file'] ) ) {
				continue;
			}

			$file_path = null;
			if ( file_exists( $attachment['file'] ) ) {
				$file_path = $attachment['file']; // Could be absolute path to file in plugin.
			} elseif ( is_child_theme() && file_exists( get_stylesheet_directory() . '/' . $attachment['file'] ) ) {
				$file_path = get_stylesheet_directory() . '/' . $attachment['file'];
			} elseif ( file_exists( get_template_directory() . '/' . $attachment['file'] ) ) {
				$file_path = get_template_directory() . '/' . $attachment['file'];
			} else {
				continue;
			}
			$file_name = basename( $attachment['file'] );

			// Skip file types that are not recognized.
			$checked_filetype = wp_check_filetype( $file_name );
			if ( empty( $checked_filetype['type'] ) ) {
				continue;
			}

			// Ensure post_name is set since not automatically derived from post_title for new auto-draft posts.
			if ( empty( $attachment['post_name'] ) ) {
				if ( ! empty( $attachment['post_title'] ) ) {
					$attachment['post_name'] = sanitize_title( $attachment['post_title'] );
				} else {
					$attachment['post_name'] = sanitize_title( preg_replace( '/\.\w+$/', '', $file_name ) );
				}
			}

			$attachment['file_name']         = $file_name;
			$attachment['file_path']         = $file_path;
			$prepared_attachments[ $symbol ] = $attachment;
		}
		return $prepared_attachments;
	}

	/**
	 * Save starter content changeset.
	 *
	 * @since 4.7.0
	 */
	public function _save_starter_content_changeset() {

		if ( empty( $this->pending_starter_content_settings_ids ) ) {
			return;
		}

		$this->save_changeset_post(
			array(
				'data'            => array_fill_keys( $this->pending_starter_content_settings_ids, array( 'starter_content' => true ) ),
				'starter_content' => true,
			)
		);
		$this->saved_starter_content_changeset = true;

		$this->pending_starter_content_settings_ids = array();
	}

	/**
	 * Get dirty pre-sanitized setting values in the current customized state.
	 *
	 * The returned array consists of a merge of three sources:
	 * 1. If the theme is not currently active, then the base array is any stashed
	 *    theme mods that were modified previously but never published.
	 * 2. The values from the current changeset, if it exists.
	 * 3. If the user can customize, the values parsed from the incoming
	 *    `$_POST['customized']` JSON data.
	 * 4. Any programmatically-set post values via `WP_Customize_Manager::set_post_value()`.
	 *
	 * The name "unsanitized_post_values" is a carry-over from when the customized
	 * state was exclusively sourced from `$_POST['customized']`. Nevertheless,
	 * the value returned will come from the current changeset post and from the
	 * incoming post data.
	 *
	 * @since 4.1.1
	 * @since 4.7.0 Added `$args` parameter and merging with changeset values and stashed theme mods.
	 *
	 * @param array $args {
	 *     Args.
	 *
	 *     @type bool $exclude_changeset Whether the changeset values should also be excluded. Defaults to false.
	 *     @type bool $exclude_post_data Whether the post input values should also be excluded. Defaults to false when lacking the customize capability.
	 * }
	 * @return array
	 */
	public function unsanitized_post_values( $args = array() ) {
		$args = array_merge(
			array(
				'exclude_changeset' => false,
				'exclude_post_data' => ! current_user_can( 'customize' ),
			),
			$args
		);

		$values = array();

		// Let default values be from the stashed theme mods if doing a theme switch and if no changeset is present.
		if ( ! $this->is_theme_active() ) {
			$stashed_theme_mods = get_option( 'customize_stashed_theme_mods' );
			$stylesheet         = $this->get_stylesheet();
			if ( isset( $stashed_theme_mods[ $stylesheet ] ) ) {
				$values = array_merge( $values, wp_list_pluck( $stashed_theme_mods[ $stylesheet ], 'value' ) );
			}
		}

		if ( ! $args['exclude_changeset'] ) {
			foreach ( $this->changeset_data() as $setting_id => $setting_params ) {
				if ( ! array_key_exists( 'value', $setting_params ) ) {
					continue;
				}
				if ( isset( $setting_params['type'] ) && 'theme_mod' === $setting_params['type'] ) {

					// Ensure that theme mods values are only used if they were saved under the current theme.
					$namespace_pattern = '/^(?P<stylesheet>.+?)::(?P<setting_id>.+)$/';
					if ( preg_match( $namespace_pattern, $setting_id, $matches ) && $this->get_stylesheet() === $matches['stylesheet'] ) {
						$values[ $matches['setting_id'] ] = $setting_params['value'];
					}
				} else {
					$values[ $setting_id ] = $setting_params['value'];
				}
			}
		}

		if ( ! $args['exclude_post_data'] ) {
			if ( ! isset( $this->_post_values ) ) {
				if ( isset( $_POST['customized'] ) ) {
					$post_values = json_decode( wp_unslash( $_POST['customized'] ), true );
				} else {
					$post_values = array();
				}
				if ( is_array( $post_values ) ) {
					$this->_post_values = $post_values;
				} else {
					$this->_post_values = array();
				}
			}
			$values = array_merge( $values, $this->_post_values );
		}
		return $values;
	}

	/**
	 * Returns the sanitized value for a given setting from the current customized state.
	 *
	 * The name "post_value" is a carry-over from when the customized state was exclusively
	 * sourced from `$_POST['customized']`. Nevertheless, the value returned will come
	 * from the current changeset post and from the incoming post data.
	 *
	 * @since 3.4.0
	 * @since 4.1.1 Introduced the `$default` parameter.
	 * @since 4.6.0 `$default` is now returned early when the setting post value is invalid.
	 *
	 * @see WP_REST_Server::dispatch()
	 * @see WP_REST_Request::sanitize_params()
	 * @see WP_REST_Request::has_valid_params()
	 *
	 * @param WP_Customize_Setting $setting A WP_Customize_Setting derived object.
	 * @param mixed                $default Value returned $setting has no post value (added in 4.2.0)
	 *                                      or the post value is invalid (added in 4.6.0).
	 * @return string|mixed $post_value Sanitized value or the $default provided.
	 */
	public function post_value( $setting, $default = null ) {
		$post_values = $this->unsanitized_post_values();
		if ( ! array_key_exists( $setting->id, $post_values ) ) {
			return $default;
		}
		$value = $post_values[ $setting->id ];
		$valid = $setting->validate( $value );
		if ( is_wp_error( $valid ) ) {
			return $default;
		}
		$value = $setting->sanitize( $value );
		if ( is_null( $value ) || is_wp_error( $value ) ) {
			return $default;
		}
		return $value;
	}

	/**
	 * Override a setting's value in the current customized state.
	 *
	 * The name "post_value" is a carry-over from when the customized state was
	 * exclusively sourced from `$_POST['customized']`.
	 *
	 * @since 4.2.0
	 *
	 * @param string $setting_id ID for the WP_Customize_Setting instance.
	 * @param mixed  $value      Post value.
	 */
	public function set_post_value( $setting_id, $value ) {
		$this->unsanitized_post_values(); // Populate _post_values from $_POST['customized'].
		$this->_post_values[ $setting_id ] = $value;

		/**
		 * Announce when a specific setting's unsanitized post value has been set.
		 *
		 * Fires when the WP_Customize_Manager::set_post_value() method is called.
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
		 * Fires when the WP_Customize_Manager::set_post_value() method is called.
		 *
		 * This is useful for `WP_Customize_Setting` instances to watch
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

		/*
		 * Now that Customizer previews are loaded into iframes via GET requests
		 * and natural URLs with transaction UUIDs added, we need to ensure that
		 * the responses are never cached by proxies. In practice, this will not
		 * be needed if the user is logged-in anyway. But if anonymous access is
		 * allowed then the auth cookies would not be sent and WordPress would
		 * not send no-cache headers by default.
		 */
		if ( ! headers_sent() ) {
			nocache_headers();
			header( 'X-Robots: noindex, nofollow, noarchive' );
		}
		add_action( 'wp_head', 'wp_no_robots' );
		add_filter( 'wp_headers', array( $this, 'filter_iframe_security_headers' ) );

		/*
		 * If preview is being served inside the customizer preview iframe, and
		 * if the user doesn't have customize capability, then it is assumed
		 * that the user's session has expired and they need to re-authenticate.
		 */
		if ( $this->messenger_channel && ! current_user_can( 'customize' ) ) {
			$this->wp_die( -1, __( 'Unauthorized. You may remove the customize_messenger_channel param to preview as frontend.' ) );
			return;
		}

		$this->prepare_controls();

		add_filter( 'wp_redirect', array( $this, 'add_state_query_params' ) );

		wp_enqueue_script( 'customize-preview' );
		wp_enqueue_style( 'customize-preview' );
		add_action( 'wp_head', array( $this, 'customize_preview_loading_style' ) );
		add_action( 'wp_head', array( $this, 'remove_frameless_preview_messenger_channel' ) );
		add_action( 'wp_footer', array( $this, 'customize_preview_settings' ), 20 );
		add_filter( 'get_edit_post_link', '__return_empty_string' );

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
	 * Filter the X-Frame-Options and Content-Security-Policy headers to ensure frontend can load in customizer.
	 *
	 * @since 4.7.0
	 *
	 * @param array $headers Headers.
	 * @return array Headers.
	 */
	public function filter_iframe_security_headers( $headers ) {
		$headers['X-Frame-Options']         = 'SAMEORIGIN';
		$headers['Content-Security-Policy'] = "frame-ancestors 'self'";
		return $headers;
	}

	/**
	 * Add customize state query params to a given URL if preview is allowed.
	 *
	 * @since 4.7.0
	 * @see wp_redirect()
	 * @see WP_Customize_Manager::get_allowed_url()
	 *
	 * @param string $url URL.
	 * @return string URL.
	 */
	public function add_state_query_params( $url ) {
		$parsed_original_url = wp_parse_url( $url );
		$is_allowed          = false;
		foreach ( $this->get_allowed_urls() as $allowed_url ) {
			$parsed_allowed_url = wp_parse_url( $allowed_url );
			$is_allowed         = (
				$parsed_allowed_url['scheme'] === $parsed_original_url['scheme']
				&&
				$parsed_allowed_url['host'] === $parsed_original_url['host']
				&&
				0 === strpos( $parsed_original_url['path'], $parsed_allowed_url['path'] )
			);
			if ( $is_allowed ) {
				break;
			}
		}

		if ( $is_allowed ) {
			$query_params = array(
				'customize_changeset_uuid' => $this->changeset_uuid(),
			);
			if ( ! $this->is_theme_active() ) {
				$query_params['customize_theme'] = $this->get_stylesheet();
			}
			if ( $this->messenger_channel ) {
				$query_params['customize_messenger_channel'] = $this->messenger_channel;
			}
			$url = add_query_arg( $query_params, $url );
		}

		return $url;
	}

	/**
	 * Prevent sending a 404 status when returning the response for the customize
	 * preview, since it causes the jQuery Ajax to fail. Send 200 instead.
	 *
	 * @since 4.0.0
	 * @deprecated 4.7.0
	 */
	public function customize_preview_override_404_status() {
		_deprecated_function( __METHOD__, '4.7.0' );
	}

	/**
	 * Print base element for preview frame.
	 *
	 * @since 3.4.0
	 * @deprecated 4.7.0
	 */
	public function customize_preview_base() {
		_deprecated_function( __METHOD__, '4.7.0' );
	}

	/**
	 * Print a workaround to handle HTML5 tags in IE < 9.
	 *
	 * @since 3.4.0
	 * @deprecated 4.7.0 Customizer no longer supports IE8, so all supported browsers recognize HTML5.
	 */
	public function customize_preview_html5() {
		_deprecated_function( __FUNCTION__, '4.7.0' );
	}

	/**
	 * Print CSS for loading indicators for the Customizer preview.
	 *
	 * @since 4.2.0
	 */
	public function customize_preview_loading_style() {
		?>
		<style>
			body.wp-customizer-unloading {
				opacity: 0.25;
				cursor: progress !important;
				-webkit-transition: opacity 0.5s;
				transition: opacity 0.5s;
			}
			body.wp-customizer-unloading * {
				pointer-events: none !important;
			}
			form.customize-unpreviewable,
			form.customize-unpreviewable input,
			form.customize-unpreviewable select,
			form.customize-unpreviewable button,
			a.customize-unpreviewable,
			area.customize-unpreviewable {
				cursor: not-allowed !important;
			}
		</style>
		<?php
	}

	/**
	 * Remove customize_messenger_channel query parameter from the preview window when it is not in an iframe.
	 *
	 * This ensures that the admin bar will be shown. It also ensures that link navigation will
	 * work as expected since the parent frame is not being sent the URL to navigate to.
	 *
	 * @since 4.7.0
	 */
	public function remove_frameless_preview_messenger_channel() {
		if ( ! $this->messenger_channel ) {
			return;
		}
		?>
		<script>
		( function() {
			var urlParser, oldQueryParams, newQueryParams, i;
			if ( parent !== window ) {
				return;
			}
			urlParser = document.createElement( 'a' );
			urlParser.href = location.href;
			oldQueryParams = urlParser.search.substr( 1 ).split( /&/ );
			newQueryParams = [];
			for ( i = 0; i < oldQueryParams.length; i += 1 ) {
				if ( ! /^customize_messenger_channel=/.test( oldQueryParams[ i ] ) ) {
					newQueryParams.push( oldQueryParams[ i ] );
				}
			}
			urlParser.search = newQueryParams.join( '&' );
			if ( urlParser.search !== location.search ) {
				location.replace( urlParser.href );
			}
		} )();
		</script>
		<?php
	}

	/**
	 * Print JavaScript settings for preview frame.
	 *
	 * @since 3.4.0
	 */
	public function customize_preview_settings() {
		$post_values                 = $this->unsanitized_post_values( array( 'exclude_changeset' => true ) );
		$setting_validities          = $this->validate_setting_values( $post_values );
		$exported_setting_validities = array_map( array( $this, 'prepare_setting_validity_for_js' ), $setting_validities );

		// Note that the REQUEST_URI is not passed into home_url() since this breaks subdirectory installations.
		$self_url           = empty( $_SERVER['REQUEST_URI'] ) ? home_url( '/' ) : esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		$state_query_params = array(
			'customize_theme',
			'customize_changeset_uuid',
			'customize_messenger_channel',
		);
		$self_url           = remove_query_arg( $state_query_params, $self_url );

		$allowed_urls  = $this->get_allowed_urls();
		$allowed_hosts = array();
		foreach ( $allowed_urls as $allowed_url ) {
			$parsed = wp_parse_url( $allowed_url );
			if ( empty( $parsed['host'] ) ) {
				continue;
			}
			$host = $parsed['host'];
			if ( ! empty( $parsed['port'] ) ) {
				$host .= ':' . $parsed['port'];
			}
			$allowed_hosts[] = $host;
		}

		$switched_locale = switch_to_locale( get_user_locale() );
		$l10n            = array(
			'shiftClickToEdit'  => __( 'Shift-click to edit this element.' ),
			'linkUnpreviewable' => __( 'This link is not live-previewable.' ),
			'formUnpreviewable' => __( 'This form is not live-previewable.' ),
		);
		if ( $switched_locale ) {
			restore_previous_locale();
		}

		$settings = array(
			'changeset'         => array(
				'uuid'      => $this->changeset_uuid(),
				'autosaved' => $this->autosaved(),
			),
			'timeouts'          => array(
				'selectiveRefresh' => 250,
				'keepAliveSend'    => 1000,
			),
			'theme'             => array(
				'stylesheet' => $this->get_stylesheet(),
				'active'     => $this->is_theme_active(),
			),
			'url'               => array(
				'self'          => $self_url,
				'allowed'       => array_map( 'esc_url_raw', $this->get_allowed_urls() ),
				'allowedHosts'  => array_unique( $allowed_hosts ),
				'isCrossDomain' => $this->is_cross_domain(),
			),
			'channel'           => $this->messenger_channel,
			'activePanels'      => array(),
			'activeSections'    => array(),
			'activeControls'    => array(),
			'settingValidities' => $exported_setting_validities,
			'nonce'             => current_user_can( 'customize' ) ? $this->get_nonces() : array(),
			'l10n'              => $l10n,
			'_dirty'            => array_keys( $post_values ),
		);

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
	 * @deprecated 4.7.0
	 */
	public function customize_preview_signature() {
		_deprecated_function( __METHOD__, '4.7.0' );
	}

	/**
	 * Removes the signature in case we experience a case where the Customizer was not properly executed.
	 *
	 * @since 3.4.0
	 * @deprecated 4.7.0
	 *
	 * @param mixed $return Value passed through for {@see 'wp_die_handler'} filter.
	 * @return mixed Value passed through for {@see 'wp_die_handler'} filter.
	 */
	public function remove_preview_signature( $return = null ) {
		_deprecated_function( __METHOD__, '4.7.0' );

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
	 * Filters the current theme and return the name of the previewed theme.
	 *
	 * @since 3.4.0
	 *
	 * @param $current_theme {@internal Parameter is not used}
	 * @return string Theme name.
	 */
	public function current_theme( $current_theme ) {
		return $this->theme()->display( 'Name' );
	}

	/**
	 * Validates setting values.
	 *
	 * Validation is skipped for unregistered settings or for values that are
	 * already null since they will be skipped anyway. Sanitization is applied
	 * to values that pass validation, and values that become null or `WP_Error`
	 * after sanitizing are marked invalid.
	 *
	 * @since 4.6.0
	 *
	 * @see WP_REST_Request::has_valid_params()
	 * @see WP_Customize_Setting::validate()
	 *
	 * @param array $setting_values Mapping of setting IDs to values to validate and sanitize.
	 * @param array $options {
	 *     Options.
	 *
	 *     @type bool $validate_existence  Whether a setting's existence will be checked.
	 *     @type bool $validate_capability Whether the setting capability will be checked.
	 * }
	 * @return array Mapping of setting IDs to return value of validate method calls, either `true` or `WP_Error`.
	 */
	public function validate_setting_values( $setting_values, $options = array() ) {
		$options = wp_parse_args(
			$options,
			array(
				'validate_capability' => false,
				'validate_existence'  => false,
			)
		);

		$validities = array();
		foreach ( $setting_values as $setting_id => $unsanitized_value ) {
			$setting = $this->get_setting( $setting_id );
			if ( ! $setting ) {
				if ( $options['validate_existence'] ) {
					$validities[ $setting_id ] = new WP_Error( 'unrecognized', __( 'Setting does not exist or is unrecognized.' ) );
				}
				continue;
			}
			if ( $options['validate_capability'] && ! current_user_can( $setting->capability ) ) {
				$validity = new WP_Error( 'unauthorized', __( 'Unauthorized to modify setting due to capability.' ) );
			} else {
				if ( is_null( $unsanitized_value ) ) {
					continue;
				}
				$validity = $setting->validate( $unsanitized_value );
			}
			if ( ! is_wp_error( $validity ) ) {
				/** This filter is documented in wp-includes/class-wp-customize-setting.php */
				$late_validity = apply_filters( "customize_validate_{$setting->id}", new WP_Error(), $unsanitized_value, $setting );
				if ( is_wp_error( $late_validity ) && $late_validity->has_errors() ) {
					$validity = $late_validity;
				}
			}
			if ( ! is_wp_error( $validity ) ) {
				$value = $setting->sanitize( $unsanitized_value );
				if ( is_null( $value ) ) {
					$validity = false;
				} elseif ( is_wp_error( $value ) ) {
					$validity = $value;
				}
			}
			if ( false === $validity ) {
				$validity = new WP_Error( 'invalid_value', __( 'Invalid value.' ) );
			}
			$validities[ $setting_id ] = $validity;
		}
		return $validities;
	}

	/**
	 * Prepares setting validity for exporting to the client (JS).
	 *
	 * Converts `WP_Error` instance into array suitable for passing into the
	 * `wp.customize.Notification` JS model.
	 *
	 * @since 4.6.0
	 *
	 * @param true|WP_Error $validity Setting validity.
	 * @return true|array If `$validity` was a WP_Error, the error codes will be array-mapped
	 *                    to their respective `message` and `data` to pass into the
	 *                    `wp.customize.Notification` JS model.
	 */
	public function prepare_setting_validity_for_js( $validity ) {
		if ( is_wp_error( $validity ) ) {
			$notification = array();
			foreach ( $validity->errors as $error_code => $error_messages ) {
				$notification[ $error_code ] = array(
					'message' => join( ' ', $error_messages ),
					'data'    => $validity->get_error_data( $error_code ),
				);
			}
			return $notification;
		} else {
			return true;
		}
	}

	/**
	 * Handle customize_save WP Ajax request to save/update a changeset.
	 *
	 * @since 3.4.0
	 * @since 4.7.0 The semantics of this method have changed to update a changeset, optionally to also change the status and other attributes.
	 */
	public function save() {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'unauthenticated' );
		}

		if ( ! $this->is_preview() ) {
			wp_send_json_error( 'not_preview' );
		}

		$action = 'save-customize_' . $this->get_stylesheet();
		if ( ! check_ajax_referer( $action, 'nonce', false ) ) {
			wp_send_json_error( 'invalid_nonce' );
		}

		$changeset_post_id = $this->changeset_post_id();
		$is_new_changeset  = empty( $changeset_post_id );
		if ( $is_new_changeset ) {
			if ( ! current_user_can( get_post_type_object( 'customize_changeset' )->cap->create_posts ) ) {
				wp_send_json_error( 'cannot_create_changeset_post' );
			}
		} else {
			if ( ! current_user_can( get_post_type_object( 'customize_changeset' )->cap->edit_post, $changeset_post_id ) ) {
				wp_send_json_error( 'cannot_edit_changeset_post' );
			}
		}

		if ( ! empty( $_POST['customize_changeset_data'] ) ) {
			$input_changeset_data = json_decode( wp_unslash( $_POST['customize_changeset_data'] ), true );
			if ( ! is_array( $input_changeset_data ) ) {
				wp_send_json_error( 'invalid_customize_changeset_data' );
			}
		} else {
			$input_changeset_data = array();
		}

		// Validate title.
		$changeset_title = null;
		if ( isset( $_POST['customize_changeset_title'] ) ) {
			$changeset_title = sanitize_text_field( wp_unslash( $_POST['customize_changeset_title'] ) );
		}

		// Validate changeset status param.
		$is_publish       = null;
		$changeset_status = null;
		if ( isset( $_POST['customize_changeset_status'] ) ) {
			$changeset_status = wp_unslash( $_POST['customize_changeset_status'] );
			if ( ! get_post_status_object( $changeset_status ) || ! in_array( $changeset_status, array( 'draft', 'pending', 'publish', 'future' ), true ) ) {
				wp_send_json_error( 'bad_customize_changeset_status', 400 );
			}
			$is_publish = ( 'publish' === $changeset_status || 'future' === $changeset_status );
			if ( $is_publish && ! current_user_can( get_post_type_object( 'customize_changeset' )->cap->publish_posts ) ) {
				wp_send_json_error( 'changeset_publish_unauthorized', 403 );
			}
		}

		/*
		 * Validate changeset date param. Date is assumed to be in local time for
		 * the WP if in MySQL format (YYYY-MM-DD HH:MM:SS). Otherwise, the date
		 * is parsed with strtotime() so that ISO date format may be supplied
		 * or a string like "+10 minutes".
		 */
		$changeset_date_gmt = null;
		if ( isset( $_POST['customize_changeset_date'] ) ) {
			$changeset_date = wp_unslash( $_POST['customize_changeset_date'] );
			if ( preg_match( '/^\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d$/', $changeset_date ) ) {
				$mm         = substr( $changeset_date, 5, 2 );
				$jj         = substr( $changeset_date, 8, 2 );
				$aa         = substr( $changeset_date, 0, 4 );
				$valid_date = wp_checkdate( $mm, $jj, $aa, $changeset_date );
				if ( ! $valid_date ) {
					wp_send_json_error( 'bad_customize_changeset_date', 400 );
				}
				$changeset_date_gmt = get_gmt_from_date( $changeset_date );
			} else {
				$timestamp = strtotime( $changeset_date );
				if ( ! $timestamp ) {
					wp_send_json_error( 'bad_customize_changeset_date', 400 );
				}
				$changeset_date_gmt = gmdate( 'Y-m-d H:i:s', $timestamp );
			}
		}

		$lock_user_id = null;
		$autosave     = ! empty( $_POST['customize_changeset_autosave'] );
		if ( ! $is_new_changeset ) {
			$lock_user_id = wp_check_post_lock( $this->changeset_post_id() );
		}

		// Force request to autosave when changeset is locked.
		if ( $lock_user_id && ! $autosave ) {
			$autosave           = true;
			$changeset_status   = null;
			$changeset_date_gmt = null;
		}

		if ( $autosave && ! defined( 'DOING_AUTOSAVE' ) ) { // Back-compat.
			define( 'DOING_AUTOSAVE', true );
		}

		$autosaved = false;
		$r         = $this->save_changeset_post(
			array(
				'status'   => $changeset_status,
				'title'    => $changeset_title,
				'date_gmt' => $changeset_date_gmt,
				'data'     => $input_changeset_data,
				'autosave' => $autosave,
			)
		);
		if ( $autosave && ! is_wp_error( $r ) ) {
			$autosaved = true;
		}

		// If the changeset was locked and an autosave request wasn't itself an error, then now explicitly return with a failure.
		if ( $lock_user_id && ! is_wp_error( $r ) ) {
			$r = new WP_Error(
				'changeset_locked',
				__( 'Changeset is being edited by other user.' ),
				array(
					'lock_user' => $this->get_lock_user_data( $lock_user_id ),
				)
			);
		}

		if ( is_wp_error( $r ) ) {
			$response = array(
				'message' => $r->get_error_message(),
				'code'    => $r->get_error_code(),
			);
			if ( is_array( $r->get_error_data() ) ) {
				$response = array_merge( $response, $r->get_error_data() );
			} else {
				$response['data'] = $r->get_error_data();
			}
		} else {
			$response       = $r;
			$changeset_post = get_post( $this->changeset_post_id() );

			// Dismiss all other auto-draft changeset posts for this user (they serve like autosave revisions), as there should only be one.
			if ( $is_new_changeset ) {
				$this->dismiss_user_auto_draft_changesets();
			}

			// Note that if the changeset status was publish, then it will get set to trash if revisions are not supported.
			$response['changeset_status'] = $changeset_post->post_status;
			if ( $is_publish && 'trash' === $response['changeset_status'] ) {
				$response['changeset_status'] = 'publish';
			}

			if ( 'publish' !== $response['changeset_status'] ) {
				$this->set_changeset_lock( $changeset_post->ID );
			}

			if ( 'future' === $response['changeset_status'] ) {
				$response['changeset_date'] = $changeset_post->post_date;
			}

			if ( 'publish' === $response['changeset_status'] || 'trash' === $response['changeset_status'] ) {
				$response['next_changeset_uuid'] = wp_generate_uuid4();
			}
		}

		if ( $autosave ) {
			$response['autosaved'] = $autosaved;
		}

		if ( isset( $response['setting_validities'] ) ) {
			$response['setting_validities'] = array_map( array( $this, 'prepare_setting_validity_for_js' ), $response['setting_validities'] );
		}

		/**
		 * Filters response data for a successful customize_save Ajax request.
		 *
		 * This filter does not apply if there was a nonce or authentication failure.
		 *
		 * @since 4.2.0
		 *
		 * @param array                $response Additional information passed back to the 'saved'
		 *                                       event on `wp.customize`.
		 * @param WP_Customize_Manager $this     WP_Customize_Manager instance.
		 */
		$response = apply_filters( 'customize_save_response', $response, $this );

		if ( is_wp_error( $r ) ) {
			wp_send_json_error( $response );
		} else {
			wp_send_json_success( $response );
		}
	}

	/**
	 * Save the post for the loaded changeset.
	 *
	 * @since 4.7.0
	 *
	 * @param array $args {
	 *     Args for changeset post.
	 *
	 *     @type array  $data            Optional additional changeset data. Values will be merged on top of any existing post values.
	 *     @type string $status          Post status. Optional. If supplied, the save will be transactional and a post revision will be allowed.
	 *     @type string $title           Post title. Optional.
	 *     @type string $date_gmt        Date in GMT. Optional.
	 *     @type int    $user_id         ID for user who is saving the changeset. Optional, defaults to the current user ID.
	 *     @type bool   $starter_content Whether the data is starter content. If false (default), then $starter_content will be cleared for any $data being saved.
	 *     @type bool   $autosave        Whether this is a request to create an autosave revision.
	 * }
	 *
	 * @return array|WP_Error Returns array on success and WP_Error with array data on error.
	 */
	function save_changeset_post( $args = array() ) {

		$args = array_merge(
			array(
				'status'          => null,
				'title'           => null,
				'data'            => array(),
				'date_gmt'        => null,
				'user_id'         => get_current_user_id(),
				'starter_content' => false,
				'autosave'        => false,
			),
			$args
		);

		$changeset_post_id       = $this->changeset_post_id();
		$existing_changeset_data = array();
		if ( $changeset_post_id ) {
			$existing_status = get_post_status( $changeset_post_id );
			if ( 'publish' === $existing_status || 'trash' === $existing_status ) {
				return new WP_Error(
					'changeset_already_published',
					__( 'The previous set of changes has already been published. Please try saving your current set of changes again.' ),
					array(
						'next_changeset_uuid' => wp_generate_uuid4(),
					)
				);
			}

			$existing_changeset_data = $this->get_changeset_post_data( $changeset_post_id );
			if ( is_wp_error( $existing_changeset_data ) ) {
				return $existing_changeset_data;
			}
		}

		// Fail if attempting to publish but publish hook is missing.
		if ( 'publish' === $args['status'] && false === has_action( 'transition_post_status', '_wp_customize_publish_changeset' ) ) {
			return new WP_Error( 'missing_publish_callback' );
		}

		// Validate date.
		$now = gmdate( 'Y-m-d H:i:59' );
		if ( $args['date_gmt'] ) {
			$is_future_dated = ( mysql2date( 'U', $args['date_gmt'], false ) > mysql2date( 'U', $now, false ) );
			if ( ! $is_future_dated ) {
				return new WP_Error( 'not_future_date', __( 'You must supply a future date to schedule.' ) ); // Only future dates are allowed.
			}

			if ( ! $this->is_theme_active() && ( 'future' === $args['status'] || $is_future_dated ) ) {
				return new WP_Error( 'cannot_schedule_theme_switches' ); // This should be allowed in the future, when theme is a regular setting.
			}
			$will_remain_auto_draft = ( ! $args['status'] && ( ! $changeset_post_id || 'auto-draft' === get_post_status( $changeset_post_id ) ) );
			if ( $will_remain_auto_draft ) {
				return new WP_Error( 'cannot_supply_date_for_auto_draft_changeset' );
			}
		} elseif ( $changeset_post_id && 'future' === $args['status'] ) {

			// Fail if the new status is future but the existing post's date is not in the future.
			$changeset_post = get_post( $changeset_post_id );
			if ( mysql2date( 'U', $changeset_post->post_date_gmt, false ) <= mysql2date( 'U', $now, false ) ) {
				return new WP_Error( 'not_future_date', __( 'You must supply a future date to schedule.' ) );
			}
		}

		if ( ! empty( $is_future_dated ) && 'publish' === $args['status'] ) {
			$args['status'] = 'future';
		}

		// Validate autosave param. See _wp_post_revision_fields() for why these fields are disallowed.
		if ( $args['autosave'] ) {
			if ( $args['date_gmt'] ) {
				return new WP_Error( 'illegal_autosave_with_date_gmt' );
			} elseif ( $args['status'] ) {
				return new WP_Error( 'illegal_autosave_with_status' );
			} elseif ( $args['user_id'] && get_current_user_id() !== $args['user_id'] ) {
				return new WP_Error( 'illegal_autosave_with_non_current_user' );
			}
		}

		// The request was made via wp.customize.previewer.save().
		$update_transactionally = (bool) $args['status'];
		$allow_revision         = (bool) $args['status'];

		// Amend post values with any supplied data.
		foreach ( $args['data'] as $setting_id => $setting_params ) {
			if ( is_array( $setting_params ) && array_key_exists( 'value', $setting_params ) ) {
				$this->set_post_value( $setting_id, $setting_params['value'] ); // Add to post values so that they can be validated and sanitized.
			}
		}

		// Note that in addition to post data, this will include any stashed theme mods.
		$post_values = $this->unsanitized_post_values(
			array(
				'exclude_changeset' => true,
				'exclude_post_data' => false,
			)
		);
		$this->add_dynamic_settings( array_keys( $post_values ) ); // Ensure settings get created even if they lack an input value.

		/*
		 * Get list of IDs for settings that have values different from what is currently
		 * saved in the changeset. By skipping any values that are already the same, the
		 * subset of changed settings can be passed into validate_setting_values to prevent
		 * an underprivileged modifying a single setting for which they have the capability
		 * from being blocked from saving. This also prevents a user from touching of the
		 * previous saved settings and overriding the associated user_id if they made no change.
		 */
		$changed_setting_ids = array();
		foreach ( $post_values as $setting_id => $setting_value ) {
			$setting = $this->get_setting( $setting_id );

			if ( $setting && 'theme_mod' === $setting->type ) {
				$prefixed_setting_id = $this->get_stylesheet() . '::' . $setting->id;
			} else {
				$prefixed_setting_id = $setting_id;
			}

			$is_value_changed = (
				! isset( $existing_changeset_data[ $prefixed_setting_id ] )
				||
				! array_key_exists( 'value', $existing_changeset_data[ $prefixed_setting_id ] )
				||
				$existing_changeset_data[ $prefixed_setting_id ]['value'] !== $setting_value
			);
			if ( $is_value_changed ) {
				$changed_setting_ids[] = $setting_id;
			}
		}

		/**
		 * Fires before save validation happens.
		 *
		 * Plugins can add just-in-time {@see 'customize_validate_{$this->ID}'} filters
		 * at this point to catch any settings registered after `customize_register`.
		 * The dynamic portion of the hook name, `$this->ID` refers to the setting ID.
		 *
		 * @since 4.6.0
		 *
		 * @param WP_Customize_Manager $this WP_Customize_Manager instance.
		 */
		do_action( 'customize_save_validation_before', $this );

		// Validate settings.
		$validated_values      = array_merge(
			array_fill_keys( array_keys( $args['data'] ), null ), // Make sure existence/capability checks are done on value-less setting updates.
			$post_values
		);
		$setting_validities    = $this->validate_setting_values(
			$validated_values,
			array(
				'validate_capability' => true,
				'validate_existence'  => true,
			)
		);
		$invalid_setting_count = count( array_filter( $setting_validities, 'is_wp_error' ) );

		/*
		 * Short-circuit if there are invalid settings the update is transactional.
		 * A changeset update is transactional when a status is supplied in the request.
		 */
		if ( $update_transactionally && $invalid_setting_count > 0 ) {
			$response = array(
				'setting_validities' => $setting_validities,
				/* translators: %s: number of invalid settings */
				'message'            => sprintf( _n( 'Unable to save due to %s invalid setting.', 'Unable to save due to %s invalid settings.', $invalid_setting_count ), number_format_i18n( $invalid_setting_count ) ),
			);
			return new WP_Error( 'transaction_fail', '', $response );
		}

		// Obtain/merge data for changeset.
		$original_changeset_data = $this->get_changeset_post_data( $changeset_post_id );
		$data                    = $original_changeset_data;
		if ( is_wp_error( $data ) ) {
			$data = array();
		}

		// Ensure that all post values are included in the changeset data.
		foreach ( $post_values as $setting_id => $post_value ) {
			if ( ! isset( $args['data'][ $setting_id ] ) ) {
				$args['data'][ $setting_id ] = array();
			}
			if ( ! isset( $args['data'][ $setting_id ]['value'] ) ) {
				$args['data'][ $setting_id ]['value'] = $post_value;
			}
		}

		foreach ( $args['data'] as $setting_id => $setting_params ) {
			$setting = $this->get_setting( $setting_id );
			if ( ! $setting || ! $setting->check_capabilities() ) {
				continue;
			}

			// Skip updating changeset for invalid setting values.
			if ( isset( $setting_validities[ $setting_id ] ) && is_wp_error( $setting_validities[ $setting_id ] ) ) {
				continue;
			}

			$changeset_setting_id = $setting_id;
			if ( 'theme_mod' === $setting->type ) {
				$changeset_setting_id = sprintf( '%s::%s', $this->get_stylesheet(), $setting_id );
			}

			if ( null === $setting_params ) {
				// Remove setting from changeset entirely.
				unset( $data[ $changeset_setting_id ] );
			} else {

				if ( ! isset( $data[ $changeset_setting_id ] ) ) {
					$data[ $changeset_setting_id ] = array();
				}

				// Merge any additional setting params that have been supplied with the existing params.
				$merged_setting_params = array_merge( $data[ $changeset_setting_id ], $setting_params );

				// Skip updating setting params if unchanged (ensuring the user_id is not overwritten).
				if ( $data[ $changeset_setting_id ] === $merged_setting_params ) {
					continue;
				}

				$data[ $changeset_setting_id ] = array_merge(
					$merged_setting_params,
					array(
						'type'              => $setting->type,
						'user_id'           => $args['user_id'],
						'date_modified_gmt' => current_time( 'mysql', true ),
					)
				);

				// Clear starter_content flag in data if changeset is not explicitly being updated for starter content.
				if ( empty( $args['starter_content'] ) ) {
					unset( $data[ $changeset_setting_id ]['starter_content'] );
				}
			}
		}

		$filter_context = array(
			'uuid'          => $this->changeset_uuid(),
			'title'         => $args['title'],
			'status'        => $args['status'],
			'date_gmt'      => $args['date_gmt'],
			'post_id'       => $changeset_post_id,
			'previous_data' => is_wp_error( $original_changeset_data ) ? array() : $original_changeset_data,
			'manager'       => $this,
		);

		/**
		 * Filters the settings' data that will be persisted into the changeset.
		 *
		 * Plugins may amend additional data (such as additional meta for settings) into the changeset with this filter.
		 *
		 * @since 4.7.0
		 *
		 * @param array $data Updated changeset data, mapping setting IDs to arrays containing a $value item and optionally other metadata.
		 * @param array $context {
		 *     Filter context.
		 *
		 *     @type string               $uuid          Changeset UUID.
		 *     @type string               $title         Requested title for the changeset post.
		 *     @type string               $status        Requested status for the changeset post.
		 *     @type string               $date_gmt      Requested date for the changeset post in MySQL format and GMT timezone.
		 *     @type int|false            $post_id       Post ID for the changeset, or false if it doesn't exist yet.
		 *     @type array                $previous_data Previous data contained in the changeset.
		 *     @type WP_Customize_Manager $manager       Manager instance.
		 * }
		 */
		$data = apply_filters( 'customize_changeset_save_data', $data, $filter_context );

		// Switch theme if publishing changes now.
		if ( 'publish' === $args['status'] && ! $this->is_theme_active() ) {
			// Temporarily stop previewing the theme to allow switch_themes() to operate properly.
			$this->stop_previewing_theme();
			switch_theme( $this->get_stylesheet() );
			update_option( 'theme_switched_via_customizer', true );
			$this->start_previewing_theme();
		}

		// Gather the data for wp_insert_post()/wp_update_post().
		$json_options = 0;
		if ( defined( 'JSON_UNESCAPED_SLASHES' ) ) {
			$json_options |= JSON_UNESCAPED_SLASHES; // Introduced in PHP 5.4. This is only to improve readability as slashes needn't be escaped in storage.
		}
		$json_options |= JSON_PRETTY_PRINT; // Also introduced in PHP 5.4, but WP defines constant for back compat. See WP Trac #30139.
		$post_array    = array(
			'post_content' => wp_json_encode( $data, $json_options ),
		);
		if ( $args['title'] ) {
			$post_array['post_title'] = $args['title'];
		}
		if ( $changeset_post_id ) {
			$post_array['ID'] = $changeset_post_id;
		} else {
			$post_array['post_type']   = 'customize_changeset';
			$post_array['post_name']   = $this->changeset_uuid();
			$post_array['post_status'] = 'auto-draft';
		}
		if ( $args['status'] ) {
			$post_array['post_status'] = $args['status'];
		}

		// Reset post date to now if we are publishing, otherwise pass post_date_gmt and translate for post_date.
		if ( 'publish' === $args['status'] ) {
			$post_array['post_date_gmt'] = '0000-00-00 00:00:00';
			$post_array['post_date']     = '0000-00-00 00:00:00';
		} elseif ( $args['date_gmt'] ) {
			$post_array['post_date_gmt'] = $args['date_gmt'];
			$post_array['post_date']     = get_date_from_gmt( $args['date_gmt'] );
		} elseif ( $changeset_post_id && 'auto-draft' === get_post_status( $changeset_post_id ) ) {
			/*
			 * Keep bumping the date for the auto-draft whenever it is modified;
			 * this extends its life, preserving it from garbage-collection via
			 * wp_delete_auto_drafts().
			 */
			$post_array['post_date']     = current_time( 'mysql' );
			$post_array['post_date_gmt'] = '';
		}

		$this->store_changeset_revision = $allow_revision;
		add_filter( 'wp_save_post_revision_post_has_changed', array( $this, '_filter_revision_post_has_changed' ), 5, 3 );

		/*
		 * Update the changeset post. The publish_customize_changeset action
		 * will cause the settings in the changeset to be saved via
		 * WP_Customize_Setting::save().
		 */

		// Prevent content filters from corrupting JSON in post_content.
		$has_kses = ( false !== has_filter( 'content_save_pre', 'wp_filter_post_kses' ) );
		if ( $has_kses ) {
			kses_remove_filters();
		}
		$has_targeted_link_rel_filters = ( false !== has_filter( 'content_save_pre', 'wp_targeted_link_rel' ) );
		if ( $has_targeted_link_rel_filters ) {
			wp_remove_targeted_link_rel_filters();
		}

		// Note that updating a post with publish status will trigger WP_Customize_Manager::publish_changeset_values().
		if ( $changeset_post_id ) {
			if ( $args['autosave'] && 'auto-draft' !== get_post_status( $changeset_post_id ) ) {
				// See _wp_translate_postdata() for why this is required as it will use the edit_post meta capability.
				add_filter( 'map_meta_cap', array( $this, 'grant_edit_post_capability_for_changeset' ), 10, 4 );
				$post_array['post_ID']   = $post_array['ID'];
				$post_array['post_type'] = 'customize_changeset';
				$r                       = wp_create_post_autosave( wp_slash( $post_array ) );
				remove_filter( 'map_meta_cap', array( $this, 'grant_edit_post_capability_for_changeset' ), 10 );
			} else {
				$post_array['edit_date'] = true; // Prevent date clearing.
				$r                       = wp_update_post( wp_slash( $post_array ), true );

				// Delete autosave revision for user when the changeset is updated.
				if ( ! empty( $args['user_id'] ) ) {
					$autosave_draft = wp_get_post_autosave( $changeset_post_id, $args['user_id'] );
					if ( $autosave_draft ) {
						wp_delete_post( $autosave_draft->ID, true );
					}
				}
			}
		} else {
			$r = wp_insert_post( wp_slash( $post_array ), true );
			if ( ! is_wp_error( $r ) ) {
				$this->_changeset_post_id = $r; // Update cached post ID for the loaded changeset.
			}
		}

		// Restore removed content filters.
		if ( $has_kses ) {
			kses_init_filters();
		}
		if ( $has_targeted_link_rel_filters ) {
			wp_init_targeted_link_rel_filters();
		}

		$this->_changeset_data = null; // Reset so WP_Customize_Manager::changeset_data() will re-populate with updated contents.

		remove_filter( 'wp_save_post_revision_post_has_changed', array( $this, '_filter_revision_post_has_changed' ) );

		$response = array(
			'setting_validities' => $setting_validities,
		);

		if ( is_wp_error( $r ) ) {
			$response['changeset_post_save_failure'] = $r->get_error_code();
			return new WP_Error( 'changeset_post_save_failure', '', $response );
		}

		return $response;
	}

	/**
	 * Trash or delete a changeset post.
	 *
	 * The following re-formulates the logic from `wp_trash_post()` as done in
	 * `wp_publish_post()`. The reason for bypassing `wp_trash_post()` is that it
	 * will mutate the the `post_content` and the `post_name` when they should be
	 * untouched.
	 *
	 * @since 4.9.0
	 * @global wpdb $wpdb WordPress database abstraction object.
	 * @see wp_trash_post()
	 *
	 * @param int|WP_Post $post The changeset post.
	 * @return mixed A WP_Post object for the trashed post or an empty value on failure.
	 */
	public function trash_changeset_post( $post ) {
		global $wpdb;

		$post = get_post( $post );

		if ( ! ( $post instanceof WP_Post ) ) {
			return $post;
		}
		$post_id = $post->ID;

		if ( ! EMPTY_TRASH_DAYS ) {
			return wp_delete_post( $post_id, true );
		}

		if ( 'trash' === get_post_status( $post ) ) {
			return false;
		}

		/** This filter is documented in wp-includes/post.php */
		$check = apply_filters( 'pre_trash_post', null, $post );
		if ( null !== $check ) {
			return $check;
		}

		/** This action is documented in wp-includes/post.php */
		do_action( 'wp_trash_post', $post_id );

		add_post_meta( $post_id, '_wp_trash_meta_status', $post->post_status );
		add_post_meta( $post_id, '_wp_trash_meta_time', time() );

		$old_status = $post->post_status;
		$new_status = 'trash';
		$wpdb->update( $wpdb->posts, array( 'post_status' => $new_status ), array( 'ID' => $post->ID ) );
		clean_post_cache( $post->ID );

		$post->post_status = $new_status;
		wp_transition_post_status( $new_status, $old_status, $post );

		/** This action is documented in wp-includes/post.php */
		do_action( "edit_post_{$post->post_type}", $post->ID, $post );

		/** This action is documented in wp-includes/post.php */
		do_action( 'edit_post', $post->ID, $post );

		/** This action is documented in wp-includes/post.php */
		do_action( "save_post_{$post->post_type}", $post->ID, $post, true );

		/** This action is documented in wp-includes/post.php */
		do_action( 'save_post', $post->ID, $post, true );

		/** This action is documented in wp-includes/post.php */
		do_action( 'wp_insert_post', $post->ID, $post, true );

		wp_trash_post_comments( $post_id );

		/** This action is documented in wp-includes/post.php */
		do_action( 'trashed_post', $post_id );

		return $post;
	}

	/**
	 * Handle request to trash a changeset.
	 *
	 * @since 4.9.0
	 */
	public function handle_changeset_trash_request() {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'unauthenticated' );
		}

		if ( ! $this->is_preview() ) {
			wp_send_json_error( 'not_preview' );
		}

		if ( ! check_ajax_referer( 'trash_customize_changeset', 'nonce', false ) ) {
			wp_send_json_error(
				array(
					'code'    => 'invalid_nonce',
					'message' => __( 'There was an authentication problem. Please reload and try again.' ),
				)
			);
		}

		$changeset_post_id = $this->changeset_post_id();

		if ( ! $changeset_post_id ) {
			wp_send_json_error(
				array(
					'message' => __( 'No changes saved yet, so there is nothing to trash.' ),
					'code'    => 'non_existent_changeset',
				)
			);
			return;
		}

		if ( $changeset_post_id && ! current_user_can( get_post_type_object( 'customize_changeset' )->cap->delete_post, $changeset_post_id ) ) {
			wp_send_json_error(
				array(
					'code'    => 'changeset_trash_unauthorized',
					'message' => __( 'Unable to trash changes.' ),
				)
			);
		}

		if ( 'trash' === get_post_status( $changeset_post_id ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Changes have already been trashed.' ),
					'code'    => 'changeset_already_trashed',
				)
			);
			return;
		}

		$r = $this->trash_changeset_post( $changeset_post_id );
		if ( ! ( $r instanceof WP_Post ) ) {
			wp_send_json_error(
				array(
					'code'    => 'changeset_trash_failure',
					'message' => __( 'Unable to trash changes.' ),
				)
			);
		}

		wp_send_json_success(
			array(
				'message' => __( 'Changes trashed successfully.' ),
			)
		);
	}

	/**
	 * Re-map 'edit_post' meta cap for a customize_changeset post to be the same as 'customize' maps.
	 *
	 * There is essentially a "meta meta" cap in play here, where 'edit_post' meta cap maps to
	 * the 'customize' meta cap which then maps to 'edit_theme_options'. This is currently
	 * required in core for `wp_create_post_autosave()` because it will call
	 * `_wp_translate_postdata()` which in turn will check if a user can 'edit_post', but the
	 * the caps for the customize_changeset post type are all mapping to the meta capability.
	 * This should be able to be removed once #40922 is addressed in core.
	 *
	 * @since 4.9.0
	 * @link https://core.trac.wordpress.org/ticket/40922
	 * @see WP_Customize_Manager::save_changeset_post()
	 * @see _wp_translate_postdata()
	 *
	 * @param string[] $caps    Array of the user's capabilities.
	 * @param string   $cap     Capability name.
	 * @param int      $user_id The user ID.
	 * @param array    $args    Adds the context to the cap. Typically the object ID.
	 * @return array   Capabilities.
	 */
	public function grant_edit_post_capability_for_changeset( $caps, $cap, $user_id, $args ) {
		if ( 'edit_post' === $cap && ! empty( $args[0] ) && 'customize_changeset' === get_post_type( $args[0] ) ) {
			$post_type_obj = get_post_type_object( 'customize_changeset' );
			$caps          = map_meta_cap( $post_type_obj->cap->$cap, $user_id );
		}
		return $caps;
	}

	/**
	 * Marks the changeset post as being currently edited by the current user.
	 *
	 * @since 4.9.0
	 *
	 * @param int  $changeset_post_id Changeset post id.
	 * @param bool $take_over Take over the changeset, default is false.
	 */
	public function set_changeset_lock( $changeset_post_id, $take_over = false ) {
		if ( $changeset_post_id ) {
			$can_override = ! (bool) get_post_meta( $changeset_post_id, '_edit_lock', true );

			if ( $take_over ) {
				$can_override = true;
			}

			if ( $can_override ) {
				$lock = sprintf( '%s:%s', time(), get_current_user_id() );
				update_post_meta( $changeset_post_id, '_edit_lock', $lock );
			} else {
				$this->refresh_changeset_lock( $changeset_post_id );
			}
		}
	}

	/**
	 * Refreshes changeset lock with the current time if current user edited the changeset before.
	 *
	 * @since 4.9.0
	 *
	 * @param int $changeset_post_id Changeset post id.
	 */
	public function refresh_changeset_lock( $changeset_post_id ) {
		if ( ! $changeset_post_id ) {
			return;
		}
		$lock = get_post_meta( $changeset_post_id, '_edit_lock', true );
		$lock = explode( ':', $lock );

		if ( $lock && ! empty( $lock[1] ) ) {
			$user_id         = intval( $lock[1] );
			$current_user_id = get_current_user_id();
			if ( $user_id === $current_user_id ) {
				$lock = sprintf( '%s:%s', time(), $user_id );
				update_post_meta( $changeset_post_id, '_edit_lock', $lock );
			}
		}
	}

	/**
	 * Filter heartbeat settings for the Customizer.
	 *
	 * @since 4.9.0
	 * @param array $settings Current settings to filter.
	 * @return array Heartbeat settings.
	 */
	public function add_customize_screen_to_heartbeat_settings( $settings ) {
		global $pagenow;
		if ( 'customize.php' === $pagenow ) {
			$settings['screenId'] = 'customize';
		}
		return $settings;
	}

	/**
	 * Get lock user data.
	 *
	 * @since 4.9.0
	 *
	 * @param int $user_id User ID.
	 * @return array|null User data formatted for client.
	 */
	protected function get_lock_user_data( $user_id ) {
		if ( ! $user_id ) {
			return null;
		}
		$lock_user = get_userdata( $user_id );
		if ( ! $lock_user ) {
			return null;
		}
		return array(
			'id'     => $lock_user->ID,
			'name'   => $lock_user->display_name,
			'avatar' => get_avatar_url( $lock_user->ID, array( 'size' => 128 ) ),
		);
	}

	/**
	 * Check locked changeset with heartbeat API.
	 *
	 * @since 4.9.0
	 *
	 * @param array  $response  The Heartbeat response.
	 * @param array  $data      The $_POST data sent.
	 * @param string $screen_id The screen id.
	 * @return array The Heartbeat response.
	 */
	public function check_changeset_lock_with_heartbeat( $response, $data, $screen_id ) {
		if ( isset( $data['changeset_uuid'] ) ) {
			$changeset_post_id = $this->find_changeset_post_id( $data['changeset_uuid'] );
		} else {
			$changeset_post_id = $this->changeset_post_id();
		}

		if (
			array_key_exists( 'check_changeset_lock', $data )
			&& 'customize' === $screen_id
			&& $changeset_post_id
			&& current_user_can( get_post_type_object( 'customize_changeset' )->cap->edit_post, $changeset_post_id )
		) {
			$lock_user_id = wp_check_post_lock( $changeset_post_id );

			if ( $lock_user_id ) {
				$response['customize_changeset_lock_user'] = $this->get_lock_user_data( $lock_user_id );
			} else {

				// Refreshing time will ensure that the user is sitting on customizer and has not closed the customizer tab.
				$this->refresh_changeset_lock( $changeset_post_id );
			}
		}

		return $response;
	}

	/**
	 * Removes changeset lock when take over request is sent via Ajax.
	 *
	 * @since 4.9.0
	 */
	public function handle_override_changeset_lock_request() {
		if ( ! $this->is_preview() ) {
			wp_send_json_error( 'not_preview', 400 );
		}

		if ( ! check_ajax_referer( 'customize_override_changeset_lock', 'nonce', false ) ) {
			wp_send_json_error(
				array(
					'code'    => 'invalid_nonce',
					'message' => __( 'Security check failed.' ),
				)
			);
		}

		$changeset_post_id = $this->changeset_post_id();

		if ( empty( $changeset_post_id ) ) {
			wp_send_json_error(
				array(
					'code'    => 'no_changeset_found_to_take_over',
					'message' => __( 'No changeset found to take over' ),
				)
			);
		}

		if ( ! current_user_can( get_post_type_object( 'customize_changeset' )->cap->edit_post, $changeset_post_id ) ) {
			wp_send_json_error(
				array(
					'code'    => 'cannot_remove_changeset_lock',
					'message' => __( 'Sorry, you are not allowed to take over.' ),
				)
			);
		}

		$this->set_changeset_lock( $changeset_post_id, true );

		wp_send_json_success( 'changeset_taken_over' );
	}

	/**
	 * Whether a changeset revision should be made.
	 *
	 * @since 4.7.0
	 * @var bool
	 */
	protected $store_changeset_revision;

	/**
	 * Filters whether a changeset has changed to create a new revision.
	 *
	 * Note that this will not be called while a changeset post remains in auto-draft status.
	 *
	 * @since 4.7.0
	 *
	 * @param bool    $post_has_changed Whether the post has changed.
	 * @param WP_Post $last_revision    The last revision post object.
	 * @param WP_Post $post             The post object.
	 *
	 * @return bool Whether a revision should be made.
	 */
	public function _filter_revision_post_has_changed( $post_has_changed, $last_revision, $post ) {
		unset( $last_revision );
		if ( 'customize_changeset' === $post->post_type ) {
			$post_has_changed = $this->store_changeset_revision;
		}
		return $post_has_changed;
	}

	/**
	 * Publish changeset values.
	 *
	 * This will the values contained in a changeset, even changesets that do not
	 * correspond to current manager instance. This is called by
	 * `_wp_customize_publish_changeset()` when a customize_changeset post is
	 * transitioned to the `publish` status. As such, this method should not be
	 * called directly and instead `wp_publish_post()` should be used.
	 *
	 * Please note that if the settings in the changeset are for a non-activated
	 * theme, the theme must first be switched to (via `switch_theme()`) before
	 * invoking this method.
	 *
	 * @since 4.7.0
	 * @see _wp_customize_publish_changeset()
	 * @global wpdb $wpdb
	 *
	 * @param int $changeset_post_id ID for customize_changeset post. Defaults to the changeset for the current manager instance.
	 * @return true|WP_Error True or error info.
	 */
	public function _publish_changeset_values( $changeset_post_id ) {
		global $wpdb;

		$publishing_changeset_data = $this->get_changeset_post_data( $changeset_post_id );
		if ( is_wp_error( $publishing_changeset_data ) ) {
			return $publishing_changeset_data;
		}

		$changeset_post = get_post( $changeset_post_id );

		/*
		 * Temporarily override the changeset context so that it will be read
		 * in calls to unsanitized_post_values() and so that it will be available
		 * on the $wp_customize object passed to hooks during the save logic.
		 */
		$previous_changeset_post_id = $this->_changeset_post_id;
		$this->_changeset_post_id   = $changeset_post_id;
		$previous_changeset_uuid    = $this->_changeset_uuid;
		$this->_changeset_uuid      = $changeset_post->post_name;
		$previous_changeset_data    = $this->_changeset_data;
		$this->_changeset_data      = $publishing_changeset_data;

		// Parse changeset data to identify theme mod settings and user IDs associated with settings to be saved.
		$setting_user_ids   = array();
		$theme_mod_settings = array();
		$namespace_pattern  = '/^(?P<stylesheet>.+?)::(?P<setting_id>.+)$/';
		$matches            = array();
		foreach ( $this->_changeset_data as $raw_setting_id => $setting_params ) {
			$actual_setting_id    = null;
			$is_theme_mod_setting = (
				isset( $setting_params['value'] )
				&&
				isset( $setting_params['type'] )
				&&
				'theme_mod' === $setting_params['type']
				&&
				preg_match( $namespace_pattern, $raw_setting_id, $matches )
			);
			if ( $is_theme_mod_setting ) {
				if ( ! isset( $theme_mod_settings[ $matches['stylesheet'] ] ) ) {
					$theme_mod_settings[ $matches['stylesheet'] ] = array();
				}
				$theme_mod_settings[ $matches['stylesheet'] ][ $matches['setting_id'] ] = $setting_params;

				if ( $this->get_stylesheet() === $matches['stylesheet'] ) {
					$actual_setting_id = $matches['setting_id'];
				}
			} else {
				$actual_setting_id = $raw_setting_id;
			}

			// Keep track of the user IDs for settings actually for this theme.
			if ( $actual_setting_id && isset( $setting_params['user_id'] ) ) {
				$setting_user_ids[ $actual_setting_id ] = $setting_params['user_id'];
			}
		}

		$changeset_setting_values = $this->unsanitized_post_values(
			array(
				'exclude_post_data' => true,
				'exclude_changeset' => false,
			)
		);
		$changeset_setting_ids    = array_keys( $changeset_setting_values );
		$this->add_dynamic_settings( $changeset_setting_ids );

		/**
		 * Fires once the theme has switched in the Customizer, but before settings
		 * have been saved.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Manager $manager WP_Customize_Manager instance.
		 */
		do_action( 'customize_save', $this );

		/*
		 * Ensure that all settings will allow themselves to be saved. Note that
		 * this is safe because the setting would have checked the capability
		 * when the setting value was written into the changeset. So this is why
		 * an additional capability check is not required here.
		 */
		$original_setting_capabilities = array();
		foreach ( $changeset_setting_ids as $setting_id ) {
			$setting = $this->get_setting( $setting_id );
			if ( $setting && ! isset( $setting_user_ids[ $setting_id ] ) ) {
				$original_setting_capabilities[ $setting->id ] = $setting->capability;
				$setting->capability                           = 'exist';
			}
		}

		$original_user_id = get_current_user_id();
		foreach ( $changeset_setting_ids as $setting_id ) {
			$setting = $this->get_setting( $setting_id );
			if ( $setting ) {
				/*
				 * Set the current user to match the user who saved the value into
				 * the changeset so that any filters that apply during the save
				 * process will respect the original user's capabilities. This
				 * will ensure, for example, that KSES won't strip unsafe HTML
				 * when a scheduled changeset publishes via WP Cron.
				 */
				if ( isset( $setting_user_ids[ $setting_id ] ) ) {
					wp_set_current_user( $setting_user_ids[ $setting_id ] );
				} else {
					wp_set_current_user( $original_user_id );
				}

				$setting->save();
			}
		}
		wp_set_current_user( $original_user_id );

		// Update the stashed theme mod settings, removing the active theme's stashed settings, if activated.
		if ( did_action( 'switch_theme' ) ) {
			$other_theme_mod_settings = $theme_mod_settings;
			unset( $other_theme_mod_settings[ $this->get_stylesheet() ] );
			$this->update_stashed_theme_mod_settings( $other_theme_mod_settings );
		}

		/**
		 * Fires after Customize settings have been saved.
		 *
		 * @since 3.6.0
		 *
		 * @param WP_Customize_Manager $manager WP_Customize_Manager instance.
		 */
		do_action( 'customize_save_after', $this );

		// Restore original capabilities.
		foreach ( $original_setting_capabilities as $setting_id => $capability ) {
			$setting = $this->get_setting( $setting_id );
			if ( $setting ) {
				$setting->capability = $capability;
			}
		}

		// Restore original changeset data.
		$this->_changeset_data    = $previous_changeset_data;
		$this->_changeset_post_id = $previous_changeset_post_id;
		$this->_changeset_uuid    = $previous_changeset_uuid;

		/*
		 * Convert all autosave revisions into their own auto-drafts so that users can be prompted to
		 * restore them when a changeset is published, but they had been locked out from including
		 * their changes in the changeset.
		 */
		$revisions = wp_get_post_revisions( $changeset_post_id, array( 'check_enabled' => false ) );
		foreach ( $revisions as $revision ) {
			if ( false !== strpos( $revision->post_name, "{$changeset_post_id}-autosave" ) ) {
				$wpdb->update(
					$wpdb->posts,
					array(
						'post_status' => 'auto-draft',
						'post_type'   => 'customize_changeset',
						'post_name'   => wp_generate_uuid4(),
						'post_parent' => 0,
					),
					array(
						'ID' => $revision->ID,
					)
				);
				clean_post_cache( $revision->ID );
			}
		}

		return true;
	}

	/**
	 * Update stashed theme mod settings.
	 *
	 * @since 4.7.0
	 *
	 * @param array $inactive_theme_mod_settings Mapping of stylesheet to arrays of theme mod settings.
	 * @return array|false Returns array of updated stashed theme mods or false if the update failed or there were no changes.
	 */
	protected function update_stashed_theme_mod_settings( $inactive_theme_mod_settings ) {
		$stashed_theme_mod_settings = get_option( 'customize_stashed_theme_mods' );
		if ( empty( $stashed_theme_mod_settings ) ) {
			$stashed_theme_mod_settings = array();
		}

		// Delete any stashed theme mods for the active theme since they would have been loaded and saved upon activation.
		unset( $stashed_theme_mod_settings[ $this->get_stylesheet() ] );

		// Merge inactive theme mods with the stashed theme mod settings.
		foreach ( $inactive_theme_mod_settings as $stylesheet => $theme_mod_settings ) {
			if ( ! isset( $stashed_theme_mod_settings[ $stylesheet ] ) ) {
				$stashed_theme_mod_settings[ $stylesheet ] = array();
			}

			$stashed_theme_mod_settings[ $stylesheet ] = array_merge(
				$stashed_theme_mod_settings[ $stylesheet ],
				$theme_mod_settings
			);
		}

		$autoload = false;
		$result   = update_option( 'customize_stashed_theme_mods', $stashed_theme_mod_settings, $autoload );
		if ( ! $result ) {
			return false;
		}
		return $stashed_theme_mod_settings;
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

		wp_send_json_success( $this->get_nonces() );
	}

	/**
	 * Delete a given auto-draft changeset or the autosave revision for a given changeset or delete changeset lock.
	 *
	 * @since 4.9.0
	 */
	public function handle_dismiss_autosave_or_lock_request() {
		// Calls to dismiss_user_auto_draft_changesets() and wp_get_post_autosave() require non-zero get_current_user_id().
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'unauthenticated', 401 );
		}

		if ( ! $this->is_preview() ) {
			wp_send_json_error( 'not_preview', 400 );
		}

		if ( ! check_ajax_referer( 'customize_dismiss_autosave_or_lock', 'nonce', false ) ) {
			wp_send_json_error( 'invalid_nonce', 403 );
		}

		$changeset_post_id = $this->changeset_post_id();
		$dismiss_lock      = ! empty( $_POST['dismiss_lock'] );
		$dismiss_autosave  = ! empty( $_POST['dismiss_autosave'] );

		if ( $dismiss_lock ) {
			if ( empty( $changeset_post_id ) && ! $dismiss_autosave ) {
				wp_send_json_error( 'no_changeset_to_dismiss_lock', 404 );
			}
			if ( ! current_user_can( get_post_type_object( 'customize_changeset' )->cap->edit_post, $changeset_post_id ) && ! $dismiss_autosave ) {
				wp_send_json_error( 'cannot_remove_changeset_lock', 403 );
			}

			delete_post_meta( $changeset_post_id, '_edit_lock' );

			if ( ! $dismiss_autosave ) {
				wp_send_json_success( 'changeset_lock_dismissed' );
			}
		}

		if ( $dismiss_autosave ) {
			if ( empty( $changeset_post_id ) || 'auto-draft' === get_post_status( $changeset_post_id ) ) {
				$dismissed = $this->dismiss_user_auto_draft_changesets();
				if ( $dismissed > 0 ) {
					wp_send_json_success( 'auto_draft_dismissed' );
				} else {
					wp_send_json_error( 'no_auto_draft_to_delete', 404 );
				}
			} else {
				$revision = wp_get_post_autosave( $changeset_post_id, get_current_user_id() );

				if ( $revision ) {
					if ( ! current_user_can( get_post_type_object( 'customize_changeset' )->cap->delete_post, $changeset_post_id ) ) {
						wp_send_json_error( 'cannot_delete_autosave_revision', 403 );
					}

					if ( ! wp_delete_post( $revision->ID, true ) ) {
						wp_send_json_error( 'autosave_revision_deletion_failure', 500 );
					} else {
						wp_send_json_success( 'autosave_revision_deleted' );
					}
				} else {
					wp_send_json_error( 'no_autosave_revision_to_delete', 404 );
				}
			}
		}

		wp_send_json_error( 'unknown_error', 500 );
	}

	/**
	 * Add a customize setting.
	 *
	 * @since 3.4.0
	 * @since 4.5.0 Return added WP_Customize_Setting instance.
	 *
	 * @param WP_Customize_Setting|string $id   Customize Setting object, or ID.
	 * @param array                       $args {
	 *  Optional. Array of properties for the new WP_Customize_Setting. Default empty array.
	 *
	 *  @type string       $type                  Type of the setting. Default 'theme_mod'.
	 *  @type string       $capability            Capability required for the setting. Default 'edit_theme_options'
	 *  @type string|array $theme_supports        Theme features required to support the panel. Default is none.
	 *  @type string       $default               Default value for the setting. Default is empty string.
	 *  @type string       $transport             Options for rendering the live preview of changes in Theme Customizer.
	 *                                            Using 'refresh' makes the change visible by reloading the whole preview.
	 *                                            Using 'postMessage' allows a custom JavaScript to handle live changes.
	 * @link https://developer.wordpress.org/themes/customize-api
	 *                                            Default is 'refresh'
	 *  @type callable     $validate_callback     Server-side validation callback for the setting's value.
	 *  @type callable     $sanitize_callback     Callback to filter a Customize setting value in un-slashed form.
	 *  @type callable     $sanitize_js_callback  Callback to convert a Customize PHP setting value to a value that is
	 *                                            JSON serializable.
	 *  @type bool         $dirty                 Whether or not the setting is initially dirty when created.
	 * }
	 * @return WP_Customize_Setting             The instance of the setting that was added.
	 */
	public function add_setting( $id, $args = array() ) {
		if ( $id instanceof WP_Customize_Setting ) {
			$setting = $id;
		} else {
			$class = 'WP_Customize_Setting';

			/** This filter is documented in wp-includes/class-wp-customize-manager.php */
			$args = apply_filters( 'customize_dynamic_setting_args', $args, $id );

			/** This filter is documented in wp-includes/class-wp-customize-manager.php */
			$class = apply_filters( 'customize_dynamic_setting_class', $class, $id, $args );

			$setting = new $class( $this, $id, $args );
		}

		$this->settings[ $setting->id ] = $setting;
		return $setting;
	}

	/**
	 * Register any dynamically-created settings, such as those from $_POST['customized']
	 * that have no corresponding setting created.
	 *
	 * This is a mechanism to "wake up" settings that have been dynamically created
	 * on the front end and have been sent to WordPress in `$_POST['customized']`. When WP
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

			$setting_args  = false;
			$setting_class = 'WP_Customize_Setting';

			/**
			 * Filters a dynamic setting's constructor args.
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
	 * @since 4.5.0 Return added WP_Customize_Panel instance.
	 *
	 * @param WP_Customize_Panel|string $id   Customize Panel object, or Panel ID.
	 * @param array                     $args {
	 *  Optional. Array of properties for the new Panel object. Default empty array.
	 *  @type int          $priority              Priority of the panel, defining the display order of panels and sections.
	 *                                            Default 160.
	 *  @type string       $capability            Capability required for the panel. Default `edit_theme_options`
	 *  @type string|array $theme_supports        Theme features required to support the panel.
	 *  @type string       $title                 Title of the panel to show in UI.
	 *  @type string       $description           Description to show in the UI.
	 *  @type string       $type                  Type of the panel.
	 *  @type callable     $active_callback       Active callback.
	 * }
	 * @return WP_Customize_Panel             The instance of the panel that was added.
	 */
	public function add_panel( $id, $args = array() ) {
		if ( $id instanceof WP_Customize_Panel ) {
			$panel = $id;
		} else {
			$panel = new WP_Customize_Panel( $this, $id, $args );
		}

		$this->panels[ $panel->id ] = $panel;
		return $panel;
	}

	/**
	 * Retrieve a customize panel.
	 *
	 * @since 4.0.0
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
	 *
	 * @param string $id Panel ID to remove.
	 */
	public function remove_panel( $id ) {
		// Removing core components this way is _doing_it_wrong().
		if ( in_array( $id, $this->components, true ) ) {
			/* translators: 1: panel id, 2: link to 'customize_loaded_components' filter reference */
			$message = sprintf(
				__( 'Removing %1$s manually will cause PHP warnings. Use the %2$s filter instead.' ),
				$id,
				'<a href="' . esc_url( 'https://developer.wordpress.org/reference/hooks/customize_loaded_components/' ) . '"><code>customize_loaded_components</code></a>'
			);

			_doing_it_wrong( __METHOD__, $message, '4.5.0' );
		}
		unset( $this->panels[ $id ] );
	}

	/**
	 * Register a customize panel type.
	 *
	 * Registered types are eligible to be rendered via JS and created dynamically.
	 *
	 * @since 4.3.0
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
	 * @since 4.5.0 Return added WP_Customize_Section instance.
	 *
	 * @param WP_Customize_Section|string $id   Customize Section object, or Section ID.
	 * @param array                     $args {
	 *  Optional. Array of properties for the new Section object. Default empty array.
	 *  @type int          $priority              Priority of the section, defining the display order of panels and sections.
	 *                                            Default 160.
	 *  @type string       $panel                 The panel this section belongs to (if any). Default empty.
	 *  @type string       $capability            Capability required for the section. Default 'edit_theme_options'
	 *  @type string|array $theme_supports        Theme features required to support the section.
	 *  @type string       $title                 Title of the section to show in UI.
	 *  @type string       $description           Description to show in the UI.
	 *  @type string       $type                  Type of the section.
	 *  @type callable     $active_callback       Active callback.
	 *  @type bool         $description_hidden    Hide the description behind a help icon, instead of inline above the first control. Default false.
	 * }
	 * @return WP_Customize_Section             The instance of the section that was added.
	 */
	public function add_section( $id, $args = array() ) {
		if ( $id instanceof WP_Customize_Section ) {
			$section = $id;
		} else {
			$section = new WP_Customize_Section( $this, $id, $args );
		}

		$this->sections[ $section->id ] = $section;
		return $section;
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
		if ( isset( $this->sections[ $id ] ) ) {
			return $this->sections[ $id ];
		}
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
	 * @since 4.5.0 Return added WP_Customize_Control instance.
	 *
	 * @param WP_Customize_Control|string $id   Customize Control object, or ID.
	 * @param array                       $args {
	 *  Optional. Array of properties for the new Control object. Default empty array.
	 *
	 *  @type array        $settings              All settings tied to the control. If undefined, defaults to `$setting`.
	 *                                            IDs in the array correspond to the ID of a registered `WP_Customize_Setting`.
	 *  @type string       $setting               The primary setting for the control (if there is one). Default is 'default'.
	 *  @type string       $capability            Capability required to use this control. Normally derived from `$settings`.
	 *  @type int          $priority              Order priority to load the control. Default 10.
	 *  @type string       $section               The section this control belongs to. Default empty.
	 *  @type string       $label                 Label for the control. Default empty.
	 *  @type string       $description           Description for the control. Default empty.
	 *  @type array        $choices               List of choices for 'radio' or 'select' type controls, where values
	 *                                            are the keys, and labels are the values. Default empty array.
	 *  @type array        $input_attrs           List of custom input attributes for control output, where attribute
	 *                                            names are the keys and values are the values. Default empty array.
	 *  @type bool         $allow_addition        Show UI for adding new content, currently only used for the
	 *                                            dropdown-pages control. Default false.
	 *  @type string       $type                  The type of the control. Default 'text'.
	 *  @type callback     $active_callback       Active callback.
	 * }
	 * @return WP_Customize_Control             The instance of the control that was added.
	 */
	public function add_control( $id, $args = array() ) {
		if ( $id instanceof WP_Customize_Control ) {
			$control = $id;
		} else {
			$control = new WP_Customize_Control( $this, $id, $args );
		}

		$this->controls[ $control->id ] = $control;
		return $control;
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
		if ( isset( $this->controls[ $id ] ) ) {
			return $this->controls[ $id ];
		}
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
	 *
	 * @param string $control Name of a custom control which is a subclass of
	 *                        WP_Customize_Control.
	 */
	public function register_control_type( $control ) {
		$this->registered_control_types[] = $control;
	}

	/**
	 * Render JS templates for all registered control types.
	 *
	 * @since 4.1.0
	 */
	public function render_control_templates() {
		if ( $this->branching() ) {
			$l10n = array(
				/* translators: %s: User who is customizing the changeset in customizer. */
				'locked'                => __( '%s is already customizing this changeset. Please wait until they are done to try customizing. Your latest changes have been autosaved.' ),
				/* translators: %s: User who is customizing the changeset in customizer. */
				'locked_allow_override' => __( '%s is already customizing this changeset. Do you want to take over?' ),
			);
		} else {
			$l10n = array(
				/* translators: %s: User who is customizing the changeset in customizer. */
				'locked'                => __( '%s is already customizing this site. Please wait until they are done to try customizing. Your latest changes have been autosaved.' ),
				/* translators: %s: User who is customizing the changeset in customizer. */
				'locked_allow_override' => __( '%s is already customizing this site. Do you want to take over?' ),
			);
		}

		foreach ( $this->registered_control_types as $control_type ) {
			$control = new $control_type(
				$this,
				'temp',
				array(
					'settings' => array(),
				)
			);
			$control->print_template();
		}
		?>

		<script type="text/html" id="tmpl-customize-control-default-content">
			<#
			var inputId = _.uniqueId( 'customize-control-default-input-' );
			var descriptionId = _.uniqueId( 'customize-control-default-description-' );
			var describedByAttr = data.description ? ' aria-describedby="' + descriptionId + '" ' : '';
			#>
			<# switch ( data.type ) {
				case 'checkbox': #>
					<span class="customize-inside-control-row">
						<input
							id="{{ inputId }}"
							{{{ describedByAttr }}}
							type="checkbox"
							value="{{ data.value }}"
							data-customize-setting-key-link="default"
						>
						<label for="{{ inputId }}">
							{{ data.label }}
						</label>
						<# if ( data.description ) { #>
							<span id="{{ descriptionId }}" class="description customize-control-description">{{{ data.description }}}</span>
						<# } #>
					</span>
					<#
					break;
				case 'radio':
					if ( ! data.choices ) {
						return;
					}
					#>
					<# if ( data.label ) { #>
						<label for="{{ inputId }}" class="customize-control-title">
							{{ data.label }}
						</label>
					<# } #>
					<# if ( data.description ) { #>
						<span id="{{ descriptionId }}" class="description customize-control-description">{{{ data.description }}}</span>
					<# } #>
					<# _.each( data.choices, function( val, key ) { #>
						<span class="customize-inside-control-row">
							<#
							var value, text;
							if ( _.isObject( val ) ) {
								value = val.value;
								text = val.text;
							} else {
								value = key;
								text = val;
							}
							#>
							<input
								id="{{ inputId + '-' + value }}"
								type="radio"
								value="{{ value }}"
								name="{{ inputId }}"
								data-customize-setting-key-link="default"
								{{{ describedByAttr }}}
							>
							<label for="{{ inputId + '-' + value }}">{{ text }}</label>
						</span>
					<# } ); #>
					<#
					break;
				default:
					#>
					<# if ( data.label ) { #>
						<label for="{{ inputId }}" class="customize-control-title">
							{{ data.label }}
						</label>
					<# } #>
					<# if ( data.description ) { #>
						<span id="{{ descriptionId }}" class="description customize-control-description">{{{ data.description }}}</span>
					<# } #>

					<#
					var inputAttrs = {
						id: inputId,
						'data-customize-setting-key-link': 'default'
					};
					if ( 'textarea' === data.type ) {
						inputAttrs.rows = '5';
					} else if ( 'button' === data.type ) {
						inputAttrs['class'] = 'button button-secondary';
						inputAttrs.type = 'button';
					} else {
						inputAttrs.type = data.type;
					}
					if ( data.description ) {
						inputAttrs['aria-describedby'] = descriptionId;
					}
					_.extend( inputAttrs, data.input_attrs );
					#>

					<# if ( 'button' === data.type ) { #>
						<button
							<# _.each( _.extend( inputAttrs ), function( value, key ) { #>
								{{{ key }}}="{{ value }}"
							<# } ); #>
						>{{ inputAttrs.value }}</button>
					<# } else if ( 'textarea' === data.type ) { #>
						<textarea
							<# _.each( _.extend( inputAttrs ), function( value, key ) { #>
								{{{ key }}}="{{ value }}"
							<# }); #>
						>{{ inputAttrs.value }}</textarea>
					<# } else if ( 'select' === data.type ) { #>
						<# delete inputAttrs.type; #>
						<select
							<# _.each( _.extend( inputAttrs ), function( value, key ) { #>
								{{{ key }}}="{{ value }}"
							<# }); #>
							>
							<# _.each( data.choices, function( val, key ) { #>
								<#
								var value, text;
								if ( _.isObject( val ) ) {
									value = val.value;
									text = val.text;
								} else {
									value = key;
									text = val;
								}
								#>
								<option value="{{ value }}">{{ text }}</option>
							<# } ); #>
						</select>
					<# } else { #>
						<input
							<# _.each( _.extend( inputAttrs ), function( value, key ) { #>
								{{{ key }}}="{{ value }}"
							<# }); #>
							>
					<# } #>
			<# } #>
		</script>

		<script type="text/html" id="tmpl-customize-notification">
			<li class="notice notice-{{ data.type || 'info' }} {{ data.alt ? 'notice-alt' : '' }} {{ data.dismissible ? 'is-dismissible' : '' }} {{ data.containerClasses || '' }}" data-code="{{ data.code }}" data-type="{{ data.type }}">
				<div class="notification-message">{{{ data.message || data.code }}}</div>
				<# if ( data.dismissible ) { #>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss' ); ?></span></button>
				<# } #>
			</li>
		</script>

		<script type="text/html" id="tmpl-customize-changeset-locked-notification">
			<li class="notice notice-{{ data.type || 'info' }} {{ data.containerClasses || '' }}" data-code="{{ data.code }}" data-type="{{ data.type }}">
				<div class="notification-message customize-changeset-locked-message">
					<img class="customize-changeset-locked-avatar" src="{{ data.lockUser.avatar }}" alt="{{ data.lockUser.name }}">
					<p class="currently-editing">
						<# if ( data.message ) { #>
							{{{ data.message }}}
						<# } else if ( data.allowOverride ) { #>
							<?php
							echo esc_html( sprintf( $l10n['locked_allow_override'], '{{ data.lockUser.name }}' ) );
							?>
						<# } else { #>
							<?php
							echo esc_html( sprintf( $l10n['locked'], '{{ data.lockUser.name }}' ) );
							?>
						<# } #>
					</p>
					<p class="notice notice-error notice-alt" hidden></p>
					<p class="action-buttons">
						<# if ( data.returnUrl !== data.previewUrl ) { #>
							<a class="button customize-notice-go-back-button" href="{{ data.returnUrl }}"><?php _e( 'Go back' ); ?></a>
						<# } #>
						<a class="button customize-notice-preview-button" href="{{ data.frontendPreviewUrl }}"><?php _e( 'Preview' ); ?></a>
						<# if ( data.allowOverride ) { #>
							<button class="button button-primary wp-tab-last customize-notice-take-over-button"><?php _e( 'Take over' ); ?></button>
						<# } #>
					</p>
				</div>
			</li>
		</script>

		<script type="text/html" id="tmpl-customize-code-editor-lint-error-notification">
			<li class="notice notice-{{ data.type || 'info' }} {{ data.alt ? 'notice-alt' : '' }} {{ data.dismissible ? 'is-dismissible' : '' }} {{ data.containerClasses || '' }}" data-code="{{ data.code }}" data-type="{{ data.type }}">
				<div class="notification-message">{{{ data.message || data.code }}}</div>

				<p>
					<# var elementId = 'el-' + String( Math.random() ); #>
					<input id="{{ elementId }}" type="checkbox">
					<label for="{{ elementId }}"><?php _e( 'Update anyway, even though it might break your site?' ); ?></label>
				</p>
			</li>
		</script>

		<?php
		/* The following template is obsolete in core but retained for plugins. */
		?>
		<script type="text/html" id="tmpl-customize-control-notifications">
			<ul>
				<# _.each( data.notifications, function( notification ) { #>
					<li class="notice notice-{{ notification.type || 'info' }} {{ data.altNotice ? 'notice-alt' : '' }}" data-code="{{ notification.code }}" data-type="{{ notification.type }}">{{{ notification.message || notification.code }}}</li>
				<# } ); #>
			</ul>
		</script>

		<script type="text/html" id="tmpl-customize-preview-link-control" >
			<# var elementPrefix = _.uniqueId( 'el' ) + '-' #>
			<p class="customize-control-title">
				<?php esc_html_e( 'Share Preview Link' ); ?>
			</p>
			<p class="description customize-control-description"><?php esc_html_e( 'See how changes would look live on your website, and share the preview with people who can\'t access the Customizer.' ); ?></p>
			<div class="customize-control-notifications-container"></div>
			<div class="preview-link-wrapper">
				<label for="{{ elementPrefix }}customize-preview-link-input" class="screen-reader-text"><?php esc_html_e( 'Preview Link' ); ?></label>
				<a href="" target="">
					<span class="preview-control-element" data-component="url"></span>
					<span class="screen-reader-text"><?php _e( '(opens in a new tab)' ); ?></span>
				</a>
				<input id="{{ elementPrefix }}customize-preview-link-input" readonly tabindex="-1" class="preview-control-element" data-component="input">
				<button class="customize-copy-preview-link preview-control-element button button-secondary" data-component="button" data-copy-text="<?php esc_attr_e( 'Copy' ); ?>" data-copied-text="<?php esc_attr_e( 'Copied' ); ?>" ><?php esc_html_e( 'Copy' ); ?></button>
			</div>
		</script>
		<script type="text/html" id="tmpl-customize-selected-changeset-status-control">
			<# var inputId = _.uniqueId( 'customize-selected-changeset-status-control-input-' ); #>
			<# var descriptionId = _.uniqueId( 'customize-selected-changeset-status-control-description-' ); #>
			<# if ( data.label ) { #>
				<label for="{{ inputId }}" class="customize-control-title">{{ data.label }}</label>
			<# } #>
			<# if ( data.description ) { #>
				<span id="{{ descriptionId }}" class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>
			<# _.each( data.choices, function( choice ) { #>
				<# var choiceId = inputId + '-' + choice.status; #>
				<span class="customize-inside-control-row">
					<input id="{{ choiceId }}" type="radio" value="{{ choice.status }}" name="{{ inputId }}" data-customize-setting-key-link="default">
					<label for="{{ choiceId }}">{{ choice.label }}</label>
				</span>
			<# } ); #>
		</script>
		<?php
	}

	/**
	 * Helper function to compare two objects by priority, ensuring sort stability via instance_number.
	 *
	 * @since 3.4.0
	 * @deprecated 4.7.0 Use wp_list_sort()
	 *
	 * @param WP_Customize_Panel|WP_Customize_Section|WP_Customize_Control $a Object A.
	 * @param WP_Customize_Panel|WP_Customize_Section|WP_Customize_Control $b Object B.
	 * @return int
	 */
	protected function _cmp_priority( $a, $b ) {
		_deprecated_function( __METHOD__, '4.7.0', 'wp_list_sort' );

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

		$controls       = array();
		$this->controls = wp_list_sort(
			$this->controls,
			array(
				'priority'        => 'ASC',
				'instance_number' => 'ASC',
			),
			'ASC',
			true
		);

		foreach ( $this->controls as $id => $control ) {
			if ( ! isset( $this->sections[ $control->section ] ) || ! $control->check_capabilities() ) {
				continue;
			}

			$this->sections[ $control->section ]->controls[] = $control;
			$controls[ $id ]                                 = $control;
		}
		$this->controls = $controls;

		// Prepare sections.
		$this->sections = wp_list_sort(
			$this->sections,
			array(
				'priority'        => 'ASC',
				'instance_number' => 'ASC',
			),
			'ASC',
			true
		);
		$sections       = array();

		foreach ( $this->sections as $section ) {
			if ( ! $section->check_capabilities() ) {
				continue;
			}

			$section->controls = wp_list_sort(
				$section->controls,
				array(
					'priority'        => 'ASC',
					'instance_number' => 'ASC',
				)
			);

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
		$this->panels = wp_list_sort(
			$this->panels,
			array(
				'priority'        => 'ASC',
				'instance_number' => 'ASC',
			),
			'ASC',
			true
		);
		$panels       = array();

		foreach ( $this->panels as $panel ) {
			if ( ! $panel->check_capabilities() ) {
				continue;
			}

			$panel->sections      = wp_list_sort(
				$panel->sections,
				array(
					'priority'        => 'ASC',
					'instance_number' => 'ASC',
				),
				'ASC',
				true
			);
			$panels[ $panel->id ] = $panel;
		}
		$this->panels = $panels;

		// Sort panels and top-level sections together.
		$this->containers = array_merge( $this->panels, $this->sections );
		$this->containers = wp_list_sort(
			$this->containers,
			array(
				'priority'        => 'ASC',
				'instance_number' => 'ASC',
			),
			'ASC',
			true
		);
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

		if ( ! is_multisite() && ( current_user_can( 'install_themes' ) || current_user_can( 'update_themes' ) || current_user_can( 'delete_themes' ) ) ) {
			wp_enqueue_script( 'updates' );
			wp_localize_script(
				'updates',
				'_wpUpdatesItemCounts',
				array(
					'totals' => wp_get_update_data(),
				)
			);
		}
	}

	/**
	 * Determine whether the user agent is iOS.
	 *
	 * @since 4.4.0
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
	 *
	 * @param string $preview_url URL to be previewed.
	 */
	public function set_preview_url( $preview_url ) {
		$preview_url       = esc_url_raw( $preview_url );
		$this->preview_url = wp_validate_redirect( $preview_url, home_url( '/' ) );
	}

	/**
	 * Get the initial URL to be previewed.
	 *
	 * @since 4.4.0
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
	 * Determines whether the admin and the frontend are on different domains.
	 *
	 * @since 4.7.0
	 *
	 * @return bool Whether cross-domain.
	 */
	public function is_cross_domain() {
		$admin_origin = wp_parse_url( admin_url() );
		$home_origin  = wp_parse_url( home_url() );
		$cross_domain = ( strtolower( $admin_origin['host'] ) !== strtolower( $home_origin['host'] ) );
		return $cross_domain;
	}

	/**
	 * Get URLs allowed to be previewed.
	 *
	 * If the front end and the admin are served from the same domain, load the
	 * preview over ssl if the Customizer is being loaded over ssl. This avoids
	 * insecure content warnings. This is not attempted if the admin and front end
	 * are on different domains to avoid the case where the front end doesn't have
	 * ssl certs. Domain mapping plugins can allow other urls in these conditions
	 * using the customize_allowed_urls filter.
	 *
	 * @since 4.7.0
	 *
	 * @returns array Allowed URLs.
	 */
	public function get_allowed_urls() {
		$allowed_urls = array( home_url( '/' ) );

		if ( is_ssl() && ! $this->is_cross_domain() ) {
			$allowed_urls[] = home_url( '/', 'https' );
		}

		/**
		 * Filters the list of URLs allowed to be clicked and followed in the Customizer preview.
		 *
		 * @since 3.4.0
		 *
		 * @param string[] $allowed_urls An array of allowed URLs.
		 */
		$allowed_urls = array_unique( apply_filters( 'customize_allowed_urls', $allowed_urls ) );

		return $allowed_urls;
	}

	/**
	 * Get messenger channel.
	 *
	 * @since 4.7.0
	 *
	 * @return string Messenger channel.
	 */
	public function get_messenger_channel() {
		return $this->messenger_channel;
	}

	/**
	 * Set URL to link the user to when closing the Customizer.
	 *
	 * URL is validated.
	 *
	 * @since 4.4.0
	 *
	 * @param string $return_url URL for return link.
	 */
	public function set_return_url( $return_url ) {
		$return_url       = esc_url_raw( $return_url );
		$return_url       = remove_query_arg( wp_removable_query_args(), $return_url );
		$return_url       = wp_validate_redirect( $return_url );
		$this->return_url = $return_url;
	}

	/**
	 * Get URL to link the user to when closing the Customizer.
	 *
	 * @since 4.4.0
	 *
	 * @return string URL for link to close Customizer.
	 */
	public function get_return_url() {
		$referer                    = wp_get_referer();
		$excluded_referer_basenames = array( 'customize.php', 'wp-login.php' );

		if ( $this->return_url ) {
			$return_url = $this->return_url;
		} elseif ( $referer && ! in_array( basename( parse_url( $referer, PHP_URL_PATH ) ), $excluded_referer_basenames, true ) ) {
			$return_url = $referer;
		} elseif ( $this->preview_url ) {
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
	 * Get nonces for the Customizer.
	 *
	 * @since 4.5.0
	 *
	 * @return array Nonces.
	 */
	public function get_nonces() {
		$nonces = array(
			'save'                     => wp_create_nonce( 'save-customize_' . $this->get_stylesheet() ),
			'preview'                  => wp_create_nonce( 'preview-customize_' . $this->get_stylesheet() ),
			'switch_themes'            => wp_create_nonce( 'switch_themes' ),
			'dismiss_autosave_or_lock' => wp_create_nonce( 'customize_dismiss_autosave_or_lock' ),
			'override_lock'            => wp_create_nonce( 'customize_override_changeset_lock' ),
			'trash'                    => wp_create_nonce( 'trash_customize_changeset' ),
		);

		/**
		 * Filters nonces for Customizer.
		 *
		 * @since 4.2.0
		 *
		 * @param string[]             $nonces Array of refreshed nonces for save and
		 *                                     preview actions.
		 * @param WP_Customize_Manager $this   WP_Customize_Manager instance.
		 */
		$nonces = apply_filters( 'customize_refresh_nonces', $nonces, $this );

		return $nonces;
	}

	/**
	 * Print JavaScript settings for parent window.
	 *
	 * @since 4.4.0
	 */
	public function customize_pane_settings() {

		$login_url = add_query_arg(
			array(
				'interim-login'   => 1,
				'customize-login' => 1,
			),
			wp_login_url()
		);

		// Ensure dirty flags are set for modified settings.
		foreach ( array_keys( $this->unsanitized_post_values() ) as $setting_id ) {
			$setting = $this->get_setting( $setting_id );
			if ( $setting ) {
				$setting->dirty = true;
			}
		}

		$autosave_revision_post  = null;
		$autosave_autodraft_post = null;
		$changeset_post_id       = $this->changeset_post_id();
		if ( ! $this->saved_starter_content_changeset && ! $this->autosaved() ) {
			if ( $changeset_post_id ) {
				if ( is_user_logged_in() ) {
					$autosave_revision_post = wp_get_post_autosave( $changeset_post_id, get_current_user_id() );
				}
			} else {
				$autosave_autodraft_posts = $this->get_changeset_posts(
					array(
						'posts_per_page'            => 1,
						'post_status'               => 'auto-draft',
						'exclude_restore_dismissed' => true,
					)
				);
				if ( ! empty( $autosave_autodraft_posts ) ) {
					$autosave_autodraft_post = array_shift( $autosave_autodraft_posts );
				}
			}
		}

		$current_user_can_publish = current_user_can( get_post_type_object( 'customize_changeset' )->cap->publish_posts );

		// @todo Include all of the status labels here from script-loader.php, and then allow it to be filtered.
		$status_choices = array();
		if ( $current_user_can_publish ) {
			$status_choices[] = array(
				'status' => 'publish',
				'label'  => __( 'Publish' ),
			);
		}
		$status_choices[] = array(
			'status' => 'draft',
			'label'  => __( 'Save Draft' ),
		);
		if ( $current_user_can_publish ) {
			$status_choices[] = array(
				'status' => 'future',
				'label'  => _x( 'Schedule', 'customizer changeset action/button label' ),
			);
		}

		// Prepare Customizer settings to pass to JavaScript.
		$changeset_post = null;
		if ( $changeset_post_id ) {
			$changeset_post = get_post( $changeset_post_id );
		}

		// Determine initial date to be at present or future, not past.
		$current_time = current_time( 'mysql', false );
		$initial_date = $current_time;
		if ( $changeset_post ) {
			$initial_date = get_the_time( 'Y-m-d H:i:s', $changeset_post->ID );
			if ( $initial_date < $current_time ) {
				$initial_date = $current_time;
			}
		}

		$lock_user_id = false;
		if ( $this->changeset_post_id() ) {
			$lock_user_id = wp_check_post_lock( $this->changeset_post_id() );
		}

		$settings = array(
			'changeset'              => array(
				'uuid'                  => $this->changeset_uuid(),
				'branching'             => $this->branching(),
				'autosaved'             => $this->autosaved(),
				'hasAutosaveRevision'   => ! empty( $autosave_revision_post ),
				'latestAutoDraftUuid'   => $autosave_autodraft_post ? $autosave_autodraft_post->post_name : null,
				'status'                => $changeset_post ? $changeset_post->post_status : '',
				'currentUserCanPublish' => $current_user_can_publish,
				'publishDate'           => $initial_date,
				'statusChoices'         => $status_choices,
				'lockUser'              => $lock_user_id ? $this->get_lock_user_data( $lock_user_id ) : null,
			),
			'initialServerDate'      => $current_time,
			'dateFormat'             => get_option( 'date_format' ),
			'timeFormat'             => get_option( 'time_format' ),
			'initialServerTimestamp' => floor( microtime( true ) * 1000 ),
			'initialClientTimestamp' => -1, // To be set with JS below.
			'timeouts'               => array(
				'windowRefresh'           => 250,
				'changesetAutoSave'       => AUTOSAVE_INTERVAL * 1000,
				'keepAliveCheck'          => 2500,
				'reflowPaneContents'      => 100,
				'previewFrameSensitivity' => 2000,
			),
			'theme'                  => array(
				'stylesheet'  => $this->get_stylesheet(),
				'active'      => $this->is_theme_active(),
				'_canInstall' => current_user_can( 'install_themes' ),
			),
			'url'                    => array(
				'preview'       => esc_url_raw( $this->get_preview_url() ),
				'return'        => esc_url_raw( $this->get_return_url() ),
				'parent'        => esc_url_raw( admin_url() ),
				'activated'     => esc_url_raw( home_url( '/' ) ),
				'ajax'          => esc_url_raw( admin_url( 'admin-ajax.php', 'relative' ) ),
				'allowed'       => array_map( 'esc_url_raw', $this->get_allowed_urls() ),
				'isCrossDomain' => $this->is_cross_domain(),
				'home'          => esc_url_raw( home_url( '/' ) ),
				'login'         => esc_url_raw( $login_url ),
			),
			'browser'                => array(
				'mobile' => wp_is_mobile(),
				'ios'    => $this->is_ios(),
			),
			'panels'                 => array(),
			'sections'               => array(),
			'nonce'                  => $this->get_nonces(),
			'autofocus'              => $this->get_autofocus(),
			'documentTitleTmpl'      => $this->get_document_title_template(),
			'previewableDevices'     => $this->get_previewable_devices(),
			'l10n'                   => array(
				'confirmDeleteTheme'   => __( 'Are you sure you want to delete this theme?' ),
				/* translators: %d: number of theme search results, which cannot currently consider singular vs. plural forms */
				'themeSearchResults'   => __( '%d themes found' ),
				/* translators: %d: number of themes being displayed, which cannot currently consider singular vs. plural forms */
				'announceThemeCount'   => __( 'Displaying %d themes' ),
				/* translators: %s: theme name */
				'announceThemeDetails' => __( 'Showing details for theme: %s' ),
			),
		);

		// Temporarily disable installation in Customizer. See #42184.
		$filesystem_method = get_filesystem_method();
		ob_start();
		$filesystem_credentials_are_stored = request_filesystem_credentials( self_admin_url() );
		ob_end_clean();
		if ( 'direct' !== $filesystem_method && ! $filesystem_credentials_are_stored ) {
			$settings['theme']['_filesystemCredentialsNeeded'] = true;
		}

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

		?>
		<script type="text/javascript">
			var _wpCustomizeSettings = <?php echo wp_json_encode( $settings ); ?>;
			_wpCustomizeSettings.initialClientTimestamp = _.now();
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
						wp_json_encode( $setting->json() )
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
	 * Returns a list of devices to allow previewing.
	 *
	 * @since 4.5.0
	 *
	 * @return array List of devices with labels and default setting.
	 */
	public function get_previewable_devices() {
		$devices = array(
			'desktop' => array(
				'label'   => __( 'Enter desktop preview mode' ),
				'default' => true,
			),
			'tablet'  => array(
				'label' => __( 'Enter tablet preview mode' ),
			),
			'mobile'  => array(
				'label' => __( 'Enter mobile preview mode' ),
			),
		);

		/**
		 * Filters the available devices to allow previewing in the Customizer.
		 *
		 * @since 4.5.0
		 *
		 * @see WP_Customize_Manager::get_previewable_devices()
		 *
		 * @param array $devices List of devices with labels and default setting.
		 */
		$devices = apply_filters( 'customize_previewable_devices', $devices );

		return $devices;
	}

	/**
	 * Register some default controls.
	 *
	 * @since 3.4.0
	 */
	public function register_controls() {

		/* Themes (controls are loaded via ajax) */

		$this->add_panel(
			new WP_Customize_Themes_Panel(
				$this,
				'themes',
				array(
					'title'       => $this->theme()->display( 'Name' ),
					'description' => (
					'<p>' . __( 'Looking for a theme? You can search or browse the WordPress.org theme directory, install and preview themes, then activate them right here.' ) . '</p>' .
					'<p>' . __( 'While previewing a new theme, you can continue to tailor things like widgets and menus, and explore theme-specific options.' ) . '</p>'
					),
					'capability'  => 'switch_themes',
					'priority'    => 0,
				)
			)
		);

		$this->add_section(
			new WP_Customize_Themes_Section(
				$this,
				'installed_themes',
				array(
					'title'      => __( 'Installed themes' ),
					'action'     => 'installed',
					'capability' => 'switch_themes',
					'panel'      => 'themes',
					'priority'   => 0,
				)
			)
		);

		if ( ! is_multisite() ) {
			$this->add_section(
				new WP_Customize_Themes_Section(
					$this,
					'wporg_themes',
					array(
						'title'       => __( 'WordPress.org themes' ),
						'action'      => 'wporg',
						'filter_type' => 'remote',
						'capability'  => 'install_themes',
						'panel'       => 'themes',
						'priority'    => 5,
					)
				)
			);
		}

		// Themes Setting (unused - the theme is considerably more fundamental to the Customizer experience).
		$this->add_setting(
			new WP_Customize_Filter_Setting(
				$this,
				'active_theme',
				array(
					'capability' => 'switch_themes',
				)
			)
		);

		/* Site Identity */

		$this->add_section(
			'title_tagline',
			array(
				'title'    => __( 'Site Identity' ),
				'priority' => 20,
			)
		);

		$this->add_setting(
			'blogname',
			array(
				'default'    => get_option( 'blogname' ),
				'type'       => 'option',
				'capability' => 'manage_options',
			)
		);

		$this->add_control(
			'blogname',
			array(
				'label'   => __( 'Site Title' ),
				'section' => 'title_tagline',
			)
		);

		$this->add_setting(
			'blogdescription',
			array(
				'default'    => get_option( 'blogdescription' ),
				'type'       => 'option',
				'capability' => 'manage_options',
			)
		);

		$this->add_control(
			'blogdescription',
			array(
				'label'   => __( 'Tagline' ),
				'section' => 'title_tagline',
			)
		);

		// Add a setting to hide header text if the theme doesn't support custom headers.
		if ( ! current_theme_supports( 'custom-header', 'header-text' ) ) {
			$this->add_setting(
				'header_text',
				array(
					'theme_supports'    => array( 'custom-logo', 'header-text' ),
					'default'           => 1,
					'sanitize_callback' => 'absint',
				)
			);

			$this->add_control(
				'header_text',
				array(
					'label'    => __( 'Display Site Title and Tagline' ),
					'section'  => 'title_tagline',
					'settings' => 'header_text',
					'type'     => 'checkbox',
				)
			);
		}

		$this->add_setting(
			'site_icon',
			array(
				'type'       => 'option',
				'capability' => 'manage_options',
				'transport'  => 'postMessage', // Previewed with JS in the Customizer controls window.
			)
		);

		$this->add_control(
			new WP_Customize_Site_Icon_Control(
				$this,
				'site_icon',
				array(
					'label'       => __( 'Site Icon' ),
					'description' => sprintf(
						'<p>' . __( 'Site Icons are what you see in browser tabs, bookmark bars, and within the WordPress mobile apps. Upload one here!' ) . '</p>' .
						/* translators: %s: site icon size in pixels */
						'<p>' . __( 'Site Icons should be square and at least %s pixels.' ) . '</p>',
						'<strong>512 &times; 512</strong>'
					),
					'section'     => 'title_tagline',
					'priority'    => 60,
					'height'      => 512,
					'width'       => 512,
				)
			)
		);

		$this->add_setting(
			'custom_logo',
			array(
				'theme_supports' => array( 'custom-logo' ),
				'transport'      => 'postMessage',
			)
		);

		$custom_logo_args = get_theme_support( 'custom-logo' );
		$this->add_control(
			new WP_Customize_Cropped_Image_Control(
				$this,
				'custom_logo',
				array(
					'label'         => __( 'Logo' ),
					'section'       => 'title_tagline',
					'priority'      => 8,
					'height'        => $custom_logo_args[0]['height'],
					'width'         => $custom_logo_args[0]['width'],
					'flex_height'   => $custom_logo_args[0]['flex-height'],
					'flex_width'    => $custom_logo_args[0]['flex-width'],
					'button_labels' => array(
						'select'       => __( 'Select logo' ),
						'change'       => __( 'Change logo' ),
						'remove'       => __( 'Remove' ),
						'default'      => __( 'Default' ),
						'placeholder'  => __( 'No logo selected' ),
						'frame_title'  => __( 'Select logo' ),
						'frame_button' => __( 'Choose logo' ),
					),
				)
			)
		);

		$this->selective_refresh->add_partial(
			'custom_logo',
			array(
				'settings'            => array( 'custom_logo' ),
				'selector'            => '.custom-logo-link',
				'render_callback'     => array( $this, '_render_custom_logo_partial' ),
				'container_inclusive' => true,
			)
		);

		/* Colors */

		$this->add_section(
			'colors',
			array(
				'title'    => __( 'Colors' ),
				'priority' => 40,
			)
		);

		$this->add_setting(
			'header_textcolor',
			array(
				'theme_supports'       => array( 'custom-header', 'header-text' ),
				'default'              => get_theme_support( 'custom-header', 'default-text-color' ),

				'sanitize_callback'    => array( $this, '_sanitize_header_textcolor' ),
				'sanitize_js_callback' => 'maybe_hash_hex_color',
			)
		);

		// Input type: checkbox
		// With custom value
		$this->add_control(
			'display_header_text',
			array(
				'settings' => 'header_textcolor',
				'label'    => __( 'Display Site Title and Tagline' ),
				'section'  => 'title_tagline',
				'type'     => 'checkbox',
				'priority' => 40,
			)
		);

		$this->add_control(
			new WP_Customize_Color_Control(
				$this,
				'header_textcolor',
				array(
					'label'   => __( 'Header Text Color' ),
					'section' => 'colors',
				)
			)
		);

		// Input type: Color
		// With sanitize_callback
		$this->add_setting(
			'background_color',
			array(
				'default'              => get_theme_support( 'custom-background', 'default-color' ),
				'theme_supports'       => 'custom-background',

				'sanitize_callback'    => 'sanitize_hex_color_no_hash',
				'sanitize_js_callback' => 'maybe_hash_hex_color',
			)
		);

		$this->add_control(
			new WP_Customize_Color_Control(
				$this,
				'background_color',
				array(
					'label'   => __( 'Background Color' ),
					'section' => 'colors',
				)
			)
		);

		/* Custom Header */

		if ( current_theme_supports( 'custom-header', 'video' ) ) {
			$title       = __( 'Header Media' );
			$description = '<p>' . __( 'If you add a video, the image will be used as a fallback while the video loads.' ) . '</p>';

			$width  = absint( get_theme_support( 'custom-header', 'width' ) );
			$height = absint( get_theme_support( 'custom-header', 'height' ) );
			if ( $width && $height ) {
				$control_description = sprintf(
					/* translators: 1: .mp4, 2: header size in pixels */
					__( 'Upload your video in %1$s format and minimize its file size for best results. Your theme recommends dimensions of %2$s pixels.' ),
					'<code>.mp4</code>',
					sprintf( '<strong>%s &times; %s</strong>', $width, $height )
				);
			} elseif ( $width ) {
				$control_description = sprintf(
					/* translators: 1: .mp4, 2: header width in pixels */
					__( 'Upload your video in %1$s format and minimize its file size for best results. Your theme recommends a width of %2$s pixels.' ),
					'<code>.mp4</code>',
					sprintf( '<strong>%s</strong>', $width )
				);
			} else {
				$control_description = sprintf(
					/* translators: 1: .mp4, 2: header height in pixels */
					__( 'Upload your video in %1$s format and minimize its file size for best results. Your theme recommends a height of %2$s pixels.' ),
					'<code>.mp4</code>',
					sprintf( '<strong>%s</strong>', $height )
				);
			}
		} else {
			$title               = __( 'Header Image' );
			$description         = '';
			$control_description = '';
		}

		$this->add_section(
			'header_image',
			array(
				'title'          => $title,
				'description'    => $description,
				'theme_supports' => 'custom-header',
				'priority'       => 60,
			)
		);

		$this->add_setting(
			'header_video',
			array(
				'theme_supports'    => array( 'custom-header', 'video' ),
				'transport'         => 'postMessage',
				'sanitize_callback' => 'absint',
				'validate_callback' => array( $this, '_validate_header_video' ),
			)
		);

		$this->add_setting(
			'external_header_video',
			array(
				'theme_supports'    => array( 'custom-header', 'video' ),
				'transport'         => 'postMessage',
				'sanitize_callback' => array( $this, '_sanitize_external_header_video' ),
				'validate_callback' => array( $this, '_validate_external_header_video' ),
			)
		);

		$this->add_setting(
			new WP_Customize_Filter_Setting(
				$this,
				'header_image',
				array(
					'default'        => sprintf( get_theme_support( 'custom-header', 'default-image' ), get_template_directory_uri(), get_stylesheet_directory_uri() ),
					'theme_supports' => 'custom-header',
				)
			)
		);

		$this->add_setting(
			new WP_Customize_Header_Image_Setting(
				$this,
				'header_image_data',
				array(
					'theme_supports' => 'custom-header',
				)
			)
		);

		/*
		 * Switch image settings to postMessage when video support is enabled since
		 * it entails that the_custom_header_markup() will be used, and thus selective
		 * refresh can be utilized.
		 */
		if ( current_theme_supports( 'custom-header', 'video' ) ) {
			$this->get_setting( 'header_image' )->transport      = 'postMessage';
			$this->get_setting( 'header_image_data' )->transport = 'postMessage';
		}

		$this->add_control(
			new WP_Customize_Media_Control(
				$this,
				'header_video',
				array(
					'theme_supports'  => array( 'custom-header', 'video' ),
					'label'           => __( 'Header Video' ),
					'description'     => $control_description,
					'section'         => 'header_image',
					'mime_type'       => 'video',
					'active_callback' => 'is_header_video_active',
				)
			)
		);

		$this->add_control(
			'external_header_video',
			array(
				'theme_supports'  => array( 'custom-header', 'video' ),
				'type'            => 'url',
				'description'     => __( 'Or, enter a YouTube URL:' ),
				'section'         => 'header_image',
				'active_callback' => 'is_header_video_active',
			)
		);

		$this->add_control( new WP_Customize_Header_Image_Control( $this ) );

		$this->selective_refresh->add_partial(
			'custom_header',
			array(
				'selector'            => '#wp-custom-header',
				'render_callback'     => 'the_custom_header_markup',
				'settings'            => array( 'header_video', 'external_header_video', 'header_image' ), // The image is used as a video fallback here.
				'container_inclusive' => true,
			)
		);

		/* Custom Background */

		$this->add_section(
			'background_image',
			array(
				'title'          => __( 'Background Image' ),
				'theme_supports' => 'custom-background',
				'priority'       => 80,
			)
		);

		$this->add_setting(
			'background_image',
			array(
				'default'           => get_theme_support( 'custom-background', 'default-image' ),
				'theme_supports'    => 'custom-background',
				'sanitize_callback' => array( $this, '_sanitize_background_setting' ),
			)
		);

		$this->add_setting(
			new WP_Customize_Background_Image_Setting(
				$this,
				'background_image_thumb',
				array(
					'theme_supports'    => 'custom-background',
					'sanitize_callback' => array( $this, '_sanitize_background_setting' ),
				)
			)
		);

		$this->add_control( new WP_Customize_Background_Image_Control( $this ) );

		$this->add_setting(
			'background_preset',
			array(
				'default'           => get_theme_support( 'custom-background', 'default-preset' ),
				'theme_supports'    => 'custom-background',
				'sanitize_callback' => array( $this, '_sanitize_background_setting' ),
			)
		);

		$this->add_control(
			'background_preset',
			array(
				'label'   => _x( 'Preset', 'Background Preset' ),
				'section' => 'background_image',
				'type'    => 'select',
				'choices' => array(
					'default' => _x( 'Default', 'Default Preset' ),
					'fill'    => __( 'Fill Screen' ),
					'fit'     => __( 'Fit to Screen' ),
					'repeat'  => _x( 'Repeat', 'Repeat Image' ),
					'custom'  => _x( 'Custom', 'Custom Preset' ),
				),
			)
		);

		$this->add_setting(
			'background_position_x',
			array(
				'default'           => get_theme_support( 'custom-background', 'default-position-x' ),
				'theme_supports'    => 'custom-background',
				'sanitize_callback' => array( $this, '_sanitize_background_setting' ),
			)
		);

		$this->add_setting(
			'background_position_y',
			array(
				'default'           => get_theme_support( 'custom-background', 'default-position-y' ),
				'theme_supports'    => 'custom-background',
				'sanitize_callback' => array( $this, '_sanitize_background_setting' ),
			)
		);

		$this->add_control(
			new WP_Customize_Background_Position_Control(
				$this,
				'background_position',
				array(
					'label'    => __( 'Image Position' ),
					'section'  => 'background_image',
					'settings' => array(
						'x' => 'background_position_x',
						'y' => 'background_position_y',
					),
				)
			)
		);

		$this->add_setting(
			'background_size',
			array(
				'default'           => get_theme_support( 'custom-background', 'default-size' ),
				'theme_supports'    => 'custom-background',
				'sanitize_callback' => array( $this, '_sanitize_background_setting' ),
			)
		);

		$this->add_control(
			'background_size',
			array(
				'label'   => __( 'Image Size' ),
				'section' => 'background_image',
				'type'    => 'select',
				'choices' => array(
					'auto'    => __( 'Original' ),
					'contain' => __( 'Fit to Screen' ),
					'cover'   => __( 'Fill Screen' ),
				),
			)
		);

		$this->add_setting(
			'background_repeat',
			array(
				'default'           => get_theme_support( 'custom-background', 'default-repeat' ),
				'sanitize_callback' => array( $this, '_sanitize_background_setting' ),
				'theme_supports'    => 'custom-background',
			)
		);

		$this->add_control(
			'background_repeat',
			array(
				'label'   => __( 'Repeat Background Image' ),
				'section' => 'background_image',
				'type'    => 'checkbox',
			)
		);

		$this->add_setting(
			'background_attachment',
			array(
				'default'           => get_theme_support( 'custom-background', 'default-attachment' ),
				'sanitize_callback' => array( $this, '_sanitize_background_setting' ),
				'theme_supports'    => 'custom-background',
			)
		);

		$this->add_control(
			'background_attachment',
			array(
				'label'   => __( 'Scroll with Page' ),
				'section' => 'background_image',
				'type'    => 'checkbox',
			)
		);

		// If the theme is using the default background callback, we can update
		// the background CSS using postMessage.
		if ( get_theme_support( 'custom-background', 'wp-head-callback' ) === '_custom_background_cb' ) {
			foreach ( array( 'color', 'image', 'preset', 'position_x', 'position_y', 'size', 'repeat', 'attachment' ) as $prop ) {
				$this->get_setting( 'background_' . $prop )->transport = 'postMessage';
			}
		}

		/*
		 * Static Front Page
		 * See also https://core.trac.wordpress.org/ticket/19627 which introduces the static-front-page theme_support.
		 * The following replicates behavior from options-reading.php.
		 */

		$this->add_section(
			'static_front_page',
			array(
				'title'           => __( 'Homepage Settings' ),
				'priority'        => 120,
				'description'     => __( 'You can choose what&#8217;s displayed on the homepage of your site. It can be posts in reverse chronological order (classic blog), or a fixed/static page. To set a static homepage, you first need to create two Pages. One will become the homepage, and the other will be where your posts are displayed.' ),
				'active_callback' => array( $this, 'has_published_pages' ),
			)
		);

		$this->add_setting(
			'show_on_front',
			array(
				'default'    => get_option( 'show_on_front' ),
				'capability' => 'manage_options',
				'type'       => 'option',
			)
		);

		$this->add_control(
			'show_on_front',
			array(
				'label'   => __( 'Your homepage displays' ),
				'section' => 'static_front_page',
				'type'    => 'radio',
				'choices' => array(
					'posts' => __( 'Your latest posts' ),
					'page'  => __( 'A static page' ),
				),
			)
		);

		$this->add_setting(
			'page_on_front',
			array(
				'type'       => 'option',
				'capability' => 'manage_options',
			)
		);

		$this->add_control(
			'page_on_front',
			array(
				'label'          => __( 'Homepage' ),
				'section'        => 'static_front_page',
				'type'           => 'dropdown-pages',
				'allow_addition' => true,
			)
		);

		$this->add_setting(
			'page_for_posts',
			array(
				'type'       => 'option',
				'capability' => 'manage_options',
			)
		);

		$this->add_control(
			'page_for_posts',
			array(
				'label'          => __( 'Posts page' ),
				'section'        => 'static_front_page',
				'type'           => 'dropdown-pages',
				'allow_addition' => true,
			)
		);

		/* Custom CSS */
		$section_description  = '<p>';
		$section_description .= __( 'Add your own CSS code here to customize the appearance and layout of your site.' );
		$section_description .= sprintf(
			' <a href="%1$s" class="external-link" target="_blank">%2$s<span class="screen-reader-text"> %3$s</span></a>',
			esc_url( __( 'https://codex.wordpress.org/CSS' ) ),
			__( 'Learn more about CSS' ),
			/* translators: accessibility text */
			__( '(opens in a new tab)' )
		);
		$section_description .= '</p>';

		$section_description .= '<p id="editor-keyboard-trap-help-1">' . __( 'When using a keyboard to navigate:' ) . '</p>';
		$section_description .= '<ul>';
		$section_description .= '<li id="editor-keyboard-trap-help-2">' . __( 'In the editing area, the Tab key enters a tab character.' ) . '</li>';
		$section_description .= '<li id="editor-keyboard-trap-help-3">' . __( 'To move away from this area, press the Esc key followed by the Tab key.' ) . '</li>';
		$section_description .= '<li id="editor-keyboard-trap-help-4">' . __( 'Screen reader users: when in forms mode, you may need to press the escape key twice.' ) . '</li>';
		$section_description .= '</ul>';

		if ( 'false' !== wp_get_current_user()->syntax_highlighting ) {
			$section_description .= '<p>';
			$section_description .= sprintf(
				/* translators: 1: link to user profile, 2: additional link attributes, 3: accessibility text */
				__( 'The edit field automatically highlights code syntax. You can disable this in your <a href="%1$s" %2$s>user profile%3$s</a> to work in plain text mode.' ),
				esc_url( get_edit_profile_url() ),
				'class="external-link" target="_blank"',
				sprintf(
					'<span class="screen-reader-text"> %s</span>',
					/* translators: accessibility text */
					__( '(opens in a new tab)' )
				)
			);
			$section_description .= '</p>';
		}

		$section_description .= '<p class="section-description-buttons">';
		$section_description .= '<button type="button" class="button-link section-description-close">' . __( 'Close' ) . '</button>';
		$section_description .= '</p>';

		$this->add_section(
			'custom_css',
			array(
				'title'              => __( 'Additional CSS' ),
				'priority'           => 200,
				'description_hidden' => true,
				'description'        => $section_description,
			)
		);

		$custom_css_setting = new WP_Customize_Custom_CSS_Setting(
			$this,
			sprintf( 'custom_css[%s]', get_stylesheet() ),
			array(
				'capability' => 'edit_css',
				'default'    => '',
			)
		);
		$this->add_setting( $custom_css_setting );

		$this->add_control(
			new WP_Customize_Code_Editor_Control(
				$this,
				'custom_css',
				array(
					'label'       => __( 'CSS code' ),
					'section'     => 'custom_css',
					'settings'    => array( 'default' => $custom_css_setting->id ),
					'code_type'   => 'text/css',
					'input_attrs' => array(
						'aria-describedby' => 'editor-keyboard-trap-help-1 editor-keyboard-trap-help-2 editor-keyboard-trap-help-3 editor-keyboard-trap-help-4',
					),
				)
			)
		);
	}

	/**
	 * Return whether there are published pages.
	 *
	 * Used as active callback for static front page section and controls.
	 *
	 * @since 4.7.0
	 *
	 * @returns bool Whether there are published (or to be published) pages.
	 */
	public function has_published_pages() {

		$setting = $this->get_setting( 'nav_menus_created_posts' );
		if ( $setting ) {
			foreach ( $setting->value() as $post_id ) {
				if ( 'page' === get_post_type( $post_id ) ) {
					return true;
				}
			}
		}
		return 0 !== count( get_pages() );
	}

	/**
	 * Add settings from the POST data that were not added with code, e.g. dynamically-created settings for Widgets
	 *
	 * @since 4.2.0
	 *
	 * @see add_dynamic_settings()
	 */
	public function register_dynamic_settings() {
		$setting_ids = array_keys( $this->unsanitized_post_values() );
		$this->add_dynamic_settings( $setting_ids );
	}

	/**
	 * Load themes into the theme browsing/installation UI.
	 *
	 * @since 4.9.0
	 */
	public function handle_load_themes_request() {
		check_ajax_referer( 'switch_themes', 'nonce' );

		if ( ! current_user_can( 'switch_themes' ) ) {
			wp_die( -1 );
		}

		if ( empty( $_POST['theme_action'] ) ) {
			wp_send_json_error( 'missing_theme_action' );
		}
		$theme_action = sanitize_key( $_POST['theme_action'] );
		$themes       = array();
		$args         = array();

		// Define query filters based on user input.
		if ( ! array_key_exists( 'search', $_POST ) ) {
			$args['search'] = '';
		} else {
			$args['search'] = sanitize_text_field( wp_unslash( $_POST['search'] ) );
		}

		if ( ! array_key_exists( 'tags', $_POST ) ) {
			$args['tag'] = '';
		} else {
			$args['tag'] = array_map( 'sanitize_text_field', wp_unslash( (array) $_POST['tags'] ) );
		}

		if ( ! array_key_exists( 'page', $_POST ) ) {
			$args['page'] = 1;
		} else {
			$args['page'] = absint( $_POST['page'] );
		}

		require_once ABSPATH . 'wp-admin/includes/theme.php';

		if ( 'installed' === $theme_action ) {

			// Load all installed themes from wp_prepare_themes_for_js().
			$themes = array( 'themes' => wp_prepare_themes_for_js() );
			foreach ( $themes['themes'] as &$theme ) {
				$theme['type']   = 'installed';
				$theme['active'] = ( isset( $_POST['customized_theme'] ) && $_POST['customized_theme'] === $theme['id'] );
			}
		} elseif ( 'wporg' === $theme_action ) {

			// Load WordPress.org themes from the .org API and normalize data to match installed theme objects.
			if ( ! current_user_can( 'install_themes' ) ) {
				wp_die( -1 );
			}

			// Arguments for all queries.
			$wporg_args = array(
				'per_page' => 100,
			);

			$args = array_merge( $wporg_args, $args );

			if ( '' === $args['search'] && '' === $args['tag'] ) {
				$args['browse'] = 'new'; // Sort by latest themes by default.
			}

			// Load themes from the .org API.
			$themes = themes_api( 'query_themes', $args );
			if ( is_wp_error( $themes ) ) {
				wp_send_json_error();
			}

			// This list matches the allowed tags in wp-admin/includes/theme-install.php.
			$themes_allowedtags                     = array_fill_keys(
				array( 'a', 'abbr', 'acronym', 'code', 'pre', 'em', 'strong', 'div', 'p', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'img' ),
				array()
			);
			$themes_allowedtags['a']                = array_fill_keys( array( 'href', 'title', 'target' ), true );
			$themes_allowedtags['acronym']['title'] = true;
			$themes_allowedtags['abbr']['title']    = true;
			$themes_allowedtags['img']              = array_fill_keys( array( 'src', 'class', 'alt' ), true );

			// Prepare a list of installed themes to check against before the loop.
			$installed_themes = array();
			$wp_themes        = wp_get_themes();
			foreach ( $wp_themes as $theme ) {
				$installed_themes[] = $theme->get_stylesheet();
			}
			$update_php = network_admin_url( 'update.php?action=install-theme' );

			// Set up properties for themes available on WordPress.org.
			foreach ( $themes->themes as &$theme ) {
				$theme->install_url = add_query_arg(
					array(
						'theme'    => $theme->slug,
						'_wpnonce' => wp_create_nonce( 'install-theme_' . $theme->slug ),
					),
					$update_php
				);

				$theme->name        = wp_kses( $theme->name, $themes_allowedtags );
				$theme->version     = wp_kses( $theme->version, $themes_allowedtags );
				$theme->description = wp_kses( $theme->description, $themes_allowedtags );
				$theme->stars       = wp_star_rating(
					array(
						'rating' => $theme->rating,
						'type'   => 'percent',
						'number' => $theme->num_ratings,
						'echo'   => false,
					)
				);
				$theme->num_ratings = number_format_i18n( $theme->num_ratings );
				$theme->preview_url = set_url_scheme( $theme->preview_url );

				// Handle themes that are already installed as installed themes.
				if ( in_array( $theme->slug, $installed_themes, true ) ) {
					$theme->type = 'installed';
				} else {
					$theme->type = $theme_action;
				}

				// Set active based on customized theme.
				$theme->active = ( isset( $_POST['customized_theme'] ) && $_POST['customized_theme'] === $theme->slug );

				// Map available theme properties to installed theme properties.
				$theme->id           = $theme->slug;
				$theme->screenshot   = array( $theme->screenshot_url );
				$theme->authorAndUri = wp_kses( $theme->author['display_name'], $themes_allowedtags );

				if ( isset( $theme->parent ) ) {
					$theme->parent = $theme->parent['slug'];
				} else {
					$theme->parent = false;
				}
				unset( $theme->slug );
				unset( $theme->screenshot_url );
				unset( $theme->author );
			} // End foreach().
		} // End if().

		/**
		 * Filters the theme data loaded in the customizer.
		 *
		 * This allows theme data to be loading from an external source,
		 * or modification of data loaded from `wp_prepare_themes_for_js()`
		 * or WordPress.org via `themes_api()`.
		 *
		 * @since 4.9.0
		 *
		 * @see wp_prepare_themes_for_js()
		 * @see themes_api()
		 * @see WP_Customize_Manager::__construct()
		 *
		 * @param array                $themes  Nested array of theme data.
		 * @param array                $args    List of arguments, such as page, search term, and tags to query for.
		 * @param WP_Customize_Manager $manager Instance of Customize manager.
		 */
		$themes = apply_filters( 'customize_load_themes', $themes, $args, $this );

		wp_send_json_success( $themes );
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
		if ( 'blank' === $color ) {
			return 'blank';
		}

		$color = sanitize_hex_color_no_hash( $color );
		if ( empty( $color ) ) {
			$color = get_theme_support( 'custom-header', 'default-text-color' );
		}

		return $color;
	}

	/**
	 * Callback for validating a background setting value.
	 *
	 * @since 4.7.0
	 *
	 * @param string $value Repeat value.
	 * @param WP_Customize_Setting $setting Setting.
	 * @return string|WP_Error Background value or validation error.
	 */
	public function _sanitize_background_setting( $value, $setting ) {
		if ( 'background_repeat' === $setting->id ) {
			if ( ! in_array( $value, array( 'repeat-x', 'repeat-y', 'repeat', 'no-repeat' ) ) ) {
				return new WP_Error( 'invalid_value', __( 'Invalid value for background repeat.' ) );
			}
		} elseif ( 'background_attachment' === $setting->id ) {
			if ( ! in_array( $value, array( 'fixed', 'scroll' ) ) ) {
				return new WP_Error( 'invalid_value', __( 'Invalid value for background attachment.' ) );
			}
		} elseif ( 'background_position_x' === $setting->id ) {
			if ( ! in_array( $value, array( 'left', 'center', 'right' ), true ) ) {
				return new WP_Error( 'invalid_value', __( 'Invalid value for background position X.' ) );
			}
		} elseif ( 'background_position_y' === $setting->id ) {
			if ( ! in_array( $value, array( 'top', 'center', 'bottom' ), true ) ) {
				return new WP_Error( 'invalid_value', __( 'Invalid value for background position Y.' ) );
			}
		} elseif ( 'background_size' === $setting->id ) {
			if ( ! in_array( $value, array( 'auto', 'contain', 'cover' ), true ) ) {
				return new WP_Error( 'invalid_value', __( 'Invalid value for background size.' ) );
			}
		} elseif ( 'background_preset' === $setting->id ) {
			if ( ! in_array( $value, array( 'default', 'fill', 'fit', 'repeat', 'custom' ), true ) ) {
				return new WP_Error( 'invalid_value', __( 'Invalid value for background size.' ) );
			}
		} elseif ( 'background_image' === $setting->id || 'background_image_thumb' === $setting->id ) {
			$value = empty( $value ) ? '' : esc_url_raw( $value );
		} else {
			return new WP_Error( 'unrecognized_setting', __( 'Unrecognized background setting.' ) );
		}
		return $value;
	}

	/**
	 * Export header video settings to facilitate selective refresh.
	 *
	 * @since 4.7.0
	 *
	 * @param array $response Response.
	 * @param WP_Customize_Selective_Refresh $selective_refresh Selective refresh component.
	 * @param array $partials Array of partials.
	 * @return array
	 */
	public function export_header_video_settings( $response, $selective_refresh, $partials ) {
		if ( isset( $partials['custom_header'] ) ) {
			$response['custom_header_settings'] = get_header_video_settings();
		}

		return $response;
	}

	/**
	 * Callback for validating the header_video value.
	 *
	 * Ensures that the selected video is less than 8MB and provides an error message.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_Error $validity
	 * @param mixed $value
	 * @return mixed
	 */
	public function _validate_header_video( $validity, $value ) {
		$video = get_attached_file( absint( $value ) );
		if ( $video ) {
			$size = filesize( $video );
			if ( 8 < $size / pow( 1024, 2 ) ) { // Check whether the size is larger than 8MB.
				$validity->add(
					'size_too_large',
					__( 'This video file is too large to use as a header video. Try a shorter video or optimize the compression settings and re-upload a file that is less than 8MB. Or, upload your video to YouTube and link it with the option below.' )
				);
			}
			if ( '.mp4' !== substr( $video, -4 ) && '.mov' !== substr( $video, -4 ) ) { // Check for .mp4 or .mov format, which (assuming h.264 encoding) are the only cross-browser-supported formats.
				$validity->add(
					'invalid_file_type',
					sprintf(
						/* translators: 1: .mp4, 2: .mov */
						__( 'Only %1$s or %2$s files may be used for header video. Please convert your video file and try again, or, upload your video to YouTube and link it with the option below.' ),
						'<code>.mp4</code>',
						'<code>.mov</code>'
					)
				);
			}
		}
		return $validity;
	}

	/**
	 * Callback for validating the external_header_video value.
	 *
	 * Ensures that the provided URL is supported.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_Error $validity
	 * @param mixed $value
	 * @return mixed
	 */
	public function _validate_external_header_video( $validity, $value ) {
		$video = esc_url_raw( $value );
		if ( $video ) {
			if ( ! preg_match( '#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#', $video ) ) {
				$validity->add( 'invalid_url', __( 'Please enter a valid YouTube URL.' ) );
			}
		}
		return $validity;
	}

	/**
	 * Callback for sanitizing the external_header_video value.
	 *
	 * @since 4.7.1
	 *
	 * @param string $value URL.
	 * @return string Sanitized URL.
	 */
	public function _sanitize_external_header_video( $value ) {
		return esc_url_raw( trim( $value ) );
	}

	/**
	 * Callback for rendering the custom logo, used in the custom_logo partial.
	 *
	 * This method exists because the partial object and context data are passed
	 * into a partial's render_callback so we cannot use get_custom_logo() as
	 * the render_callback directly since it expects a blog ID as the first
	 * argument. When WP no longer supports PHP 5.3, this method can be removed
	 * in favor of an anonymous function.
	 *
	 * @see WP_Customize_Manager::register_controls()
	 *
	 * @since 4.5.0
	 *
	 * @return string Custom logo.
	 */
	public function _render_custom_logo_partial() {
		return get_custom_logo();
	}
}

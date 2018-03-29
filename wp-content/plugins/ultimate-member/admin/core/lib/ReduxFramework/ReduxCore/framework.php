<?php
    /**
     * Redux Framework is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 3 of the License, or
     * any later version.
     * Redux Framework is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
     * GNU General Public License for more details.
     * You should have received a copy of the GNU General Public License
     * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
     *
     * @package     Redux_Framework
     * @subpackage  Core
     * @author      Redux Framework Team
     */
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'ReduxFrameworkInstances' ) ) {
        // Instance Container
        require_once dirname( __FILE__ ) . '/inc/class.redux_instances.php';
        require_once dirname( __FILE__ ) . '/inc/lib.redux_instances.php';
    }

    if ( class_exists( 'ReduxFrameworkInstances' ) ) {
        add_action( 'redux/init', 'ReduxFrameworkInstances::get_instance' );
    }

    // Don't duplicate me!
    if ( ! class_exists( 'ReduxFramework' ) ) {

        // Redux CDN class
        require_once dirname( __FILE__ ) . '/inc/class.redux_cdn.php';

        // Redux API class  :)
        require_once dirname( __FILE__ ) . '/inc/class.redux_api.php';

        // General helper functions
        require_once dirname( __FILE__ ) . '/inc/class.redux_helpers.php';

        // General functions
        require_once dirname( __FILE__ ) . '/inc/class.redux_functions.php';
        require_once dirname( __FILE__ ) . '/inc/class.p.php';

        require_once dirname( __FILE__ ) . '/inc/class.thirdparty.fixes.php';

        require_once dirname( __FILE__ ) . '/inc/class.redux_filesystem.php';

        require_once dirname( __FILE__ ) . '/inc/class.redux_admin_notices.php';

        // ThemeCheck checks
        require_once dirname( __FILE__ ) . '/inc/themecheck/class.redux_themecheck.php';

        // Welcome
        require_once dirname( __FILE__ ) . '/inc/welcome/welcome.php';

        /**
         * Main ReduxFramework class
         *
         * @since       1.0.0
         */
        class ReduxFramework {

            // ATTENTION DEVS
            // Please update the build number with each push, no matter how small.
            // This will make for easier support when we ask users what version they are using.

            public static $_version = '3.6.2';
            public static $_dir;
            public static $_url;
            public static $_upload_dir;
            public static $_upload_url;
            public static $wp_content_url;
            public static $base_wp_content_url;
            public static $_is_plugin = true;
            public static $_as_plugin = false;

            public static function init() {
                $dir = Redux_Helpers::cleanFilePath( dirname( __FILE__ ) );

                // Windows-proof constants: replace backward by forward slashes. Thanks to: @peterbouwmeester
                self::$_dir           = trailingslashit( $dir );
                self::$wp_content_url = trailingslashit( Redux_Helpers::cleanFilePath( ( is_ssl() ? str_replace( 'http://', 'https://', WP_CONTENT_URL ) : WP_CONTENT_URL ) ) );

                // See if Redux is a plugin or not
                if ( strpos( Redux_Helpers::cleanFilePath( __FILE__ ), Redux_Helpers::cleanFilePath( get_stylesheet_directory() ) ) !== false || strpos( Redux_Helpers::cleanFilePath( __FILE__ ), Redux_Helpers::cleanFilePath( get_template_directory_uri() ) ) !== false || strpos( Redux_Helpers::cleanFilePath( __FILE__ ), Redux_Helpers::cleanFilePath( WP_CONTENT_DIR . '/themes/' ) ) !== false ) {
                    self::$_is_plugin = false;
                } else {
                    // Check if plugin is a symbolic link, see if it's a plugin. If embedded, we can't do a thing.
                    if ( strpos( self::$_dir, ABSPATH ) === false ) {
                        if ( ! function_exists( 'get_plugins' ) ) {
                            require_once ABSPATH . 'wp-admin/includes/plugin.php';
                        }

                        $is_plugin = false;
                        foreach ( get_plugins() as $key => $value ) {
                            if ( is_plugin_active( $key ) && strpos( $key, 'redux-framework.php' ) !== false ) {
                                self::$_dir = trailingslashit( Redux_Helpers::cleanFilePath( WP_CONTENT_DIR . '/plugins/' . plugin_dir_path( $key ) . 'ReduxCore/' ) );
                                $is_plugin  = true;
                            }
                        }
                        if ( ! $is_plugin ) {
                            self::$_is_plugin = false;
                        }
                    }
                }

                if ( self::$_is_plugin == true || self::$_as_plugin == true ) {
                    self::$_url = plugin_dir_url( __FILE__ );
                } else {
                    if ( strpos( Redux_Helpers::cleanFilePath( __FILE__ ), Redux_Helpers::cleanFilePath( get_template_directory() ) ) !== false ) {
                        $relative_url = str_replace( Redux_Helpers::cleanFilePath( get_template_directory() ), '', self::$_dir );
                        self::$_url   = trailingslashit( get_template_directory_uri() . $relative_url );
                    } else if ( strpos( Redux_Helpers::cleanFilePath( __FILE__ ), Redux_Helpers::cleanFilePath( get_stylesheet_directory() ) ) !== false ) {
                        $relative_url = str_replace( Redux_Helpers::cleanFilePath( get_stylesheet_directory() ), '', self::$_dir );
                        self::$_url   = trailingslashit( get_stylesheet_directory_uri() . $relative_url );
                    } else {
                        $wp_content_dir = trailingslashit( Redux_Helpers::cleanFilePath( WP_CONTENT_DIR ) );
                        $wp_content_dir = trailingslashit( str_replace( '//', '/', $wp_content_dir ) );
                        $relative_url   = str_replace( $wp_content_dir, '', self::$_dir );
                        self::$_url     = trailingslashit( self::$wp_content_url . $relative_url );
                    }
                }

                self::$_url       = apply_filters( "redux/_url", self::$_url );
                self::$_dir       = apply_filters( "redux/_dir", self::$_dir );
                self::$_is_plugin = apply_filters( "redux/_is_plugin", self::$_is_plugin );
            }

            // ::init()

            public $framework_url = 'http://www.reduxframework.com/';
            public static $instance = null;
            public $admin_notices = array();
            public $page = '';
            public $saved = false;
            public $fields = array(); // Fields by type used in the panel
            public $field_sections = array(); // Section id's by field type, then field ID
            public $current_tab = ''; // Current section to display, cookies
            public $extensions = array(); // Extensions by type used in the panel
            public $sections = array(); // Sections and fields
            public $errors = array(); // Errors
            public $warnings = array(); // Warnings
            public $options = array(); // Option values
            public $options_defaults = null; // Option defaults
            public $notices = array(); // Option defaults
            public $compiler_fields = array(); // Fields that trigger the compiler hook
            public $required = array(); // Information that needs to be localized
            public $required_child = array(); // Information that needs to be localized
            public $localize_data = array(); // Information that needs to be localized
            public $fonts = array(); // Information that needs to be localized
            public $folds = array(); // The itms that need to fold.
            public $path = '';
            public $changed_values = array(); // Values that have been changed on save. Orig values.
            public $output = array(); // Fields with CSS output selectors
            public $outputCSS = null; // CSS that get auto-appended to the header
            public $compilerCSS = null; // CSS that get sent to the compiler hook
            public $customizerCSS = null; // CSS that goes to the customizer
            public $fieldsValues = array(); //all fields values in an id=>value array so we can check dependencies
            public $fieldsHidden = array(); //all fields that didn't pass the dependency test and are hidden
            public $toHide = array(); // Values to hide on page load
            public $typography = null; //values to generate google font CSS
            public $import_export = null;
            public $no_panel = array(); // Fields that are not visible in the panel
            private $show_hints = false;
            public $hidden_perm_fields = array(); //  Hidden fields specified by 'permissions' arg.
            public $hidden_perm_sections = array(); //  Hidden sections specified by 'permissions' arg.
            public $typography_preview = array();
            public $args = array();
            public $filesystem = null;
            public $font_groups = array();
            public $lang = "";
            public $dev_mode_forced = false;
            public $reload_fields = array();
            public $omit_share_icons = false;
            public $omit_admin_items = false;

            /**
             * Class Constructor. Defines the args for the theme options class
             *
             * @since       1.0.0
             *
             * @param       array $sections   Panel sections.
             * @param       array $args       Class constructor arguments.
             * @param       array $extra_tabs Extra panel tabs. // REMOVE
             *
             * @return \ReduxFramework
             */
            public function __construct( $sections = array(), $args = array(), $extra_tabs = array() ) {
                // Disregard WP AJAX 'heartbeat'call.  Why waste resources?
                if ( isset ( $_POST ) && isset ( $_POST['action'] ) && $_POST['action'] == 'heartbeat' ) {

                    // Hook, for purists.
                    if ( ! has_action( 'redux/ajax/heartbeat' ) ) {
                        do_action( 'redux/ajax/heartbeat', $this );
                    }

                    // Buh bye!
                    return;
                }

                // Pass parent pointer to function helper.
                Redux_Functions::$_parent     = $this;
                Redux_CDN::$_parent           = $this;
                Redux_Admin_Notices::$_parent = $this;

                // Set values
                $this->set_default_args();
                $this->args = wp_parse_args( $args, $this->args );

                if ( empty ( $this->args['transient_time'] ) ) {
                    $this->args['transient_time'] = 60 * MINUTE_IN_SECONDS;
                }

                if ( empty ( $this->args['footer_credit'] ) ) {
                    $this->args['footer_credit'] = '<span id="footer-thankyou">' . sprintf( __( 'Options panel created using %1$s', 'redux-framework' ), '<a href="' . esc_url( $this->framework_url ) . '" target="_blank">' . __( 'Redux Framework', 'redux-framework' ) . '</a> v' . self::$_version ) . '</span>';
                }

                if ( empty ( $this->args['menu_title'] ) ) {
                    $this->args['menu_title'] = __( 'Options', 'redux-framework' );
                }

                if ( empty ( $this->args['page_title'] ) ) {
                    $this->args['page_title'] = __( 'Options', 'redux-framework' );
                }

                $this->old_opt_name = $this->args['opt_name'];

                /**
                 * filter 'redux/args/{opt_name}'
                 *
                 * @param  array $args ReduxFramework configuration
                 */
                $this->args = apply_filters( "redux/args/{$this->args['opt_name']}", $this->args );

                /**
                 * filter 'redux/options/{opt_name}/args'
                 *
                 * @param  array $args ReduxFramework configuration
                 */
                $this->args = apply_filters( "redux/options/{$this->args['opt_name']}/args", $this->args );

                if ( $this->args['opt_name'] == $this->old_opt_name ) {
                    unset( $this->old_opt_name );
                }

                // Do not save the defaults if we're on a live preview!
                if ( $GLOBALS['pagenow'] == "customize" && isset( $_GET['theme'] ) && ! empty( $_GET['theme'] ) ) {
                    $this->args['save_defaults'] = false;
                }

                $this->change_demo_defaults();

                if ( ! empty ( $this->args['opt_name'] ) ) {
                    /**
                     * SHIM SECTION
                     * Old variables and ways of doing things that need correcting.  ;)
                     * */
                    // Variable name change
                    if ( ! empty ( $this->args['page_cap'] ) ) {
                        $this->args['page_permissions'] = $this->args['page_cap'];
                        unset ( $this->args['page_cap'] );
                    }

                    if ( ! empty ( $this->args['page_position'] ) ) {
                        $this->args['page_priority'] = $this->args['page_position'];
                        unset ( $this->args['page_position'] );
                    }

                    if ( ! empty ( $this->args['page_type'] ) ) {
                        $this->args['menu_type'] = $this->args['page_type'];
                        unset ( $this->args['page_type'] );
                    }

                    // Get rid of extra_tabs! Not needed.
                    if ( is_array( $extra_tabs ) && ! empty ( $extra_tabs ) ) {
                        foreach ( $extra_tabs as $tab ) {
                            array_push( $this->sections, $tab );
                        }
                    }

                    // Move to the first loop area!
                    /**
                     * filter 'redux-sections'
                     *
                     * @deprecated
                     *
                     * @param  array $sections field option sections
                     */
                    $this->sections = apply_filters( 'redux-sections', $sections ); // REMOVE LATER
                    /**
                     * filter 'redux-sections-{opt_name}'
                     *
                     * @deprecated
                     *
                     * @param  array $sections field option sections
                     */
                    $this->sections = apply_filters( "redux-sections-{$this->args['opt_name']}", $this->sections ); // REMOVE LATER
                    /**
                     * filter 'redux/options/{opt_name}/sections'
                     *
                     * @param  array $sections field option sections
                     */
                    $this->sections = apply_filters( "redux/options/{$this->args['opt_name']}/sections", $this->sections );

                    /**
                     * Construct hook
                     * action 'redux/construct'
                     *
                     * @param object $this ReduxFramework
                     */
                    do_action( 'redux/construct', $this );

                    // Set the default values
                    $this->_default_cleanup();

                    // Internataionalization
                    $this->_internationalization();

                    $this->filesystem = Redux_Filesystem::get_instance( $this );

                    //set redux upload folder
                    $this->set_redux_content();

                    // Register extra extensions
                    $this->_register_extensions();

                    // Grab database values
                    $this->get_options();

                    // Tracking
                    if ( isset( $this->args['allow_tracking'] ) && $this->args['allow_tracking'] && Redux_Helpers::isTheme( __FILE__ ) ) {
                        $this->_tracking();
                    }

                    // Options page
                    add_action( 'admin_menu', array( $this, '_options_page' ) );

                    // Add a network menu
                    if ( $this->args['database'] == "network" && $this->args['network_admin'] ) {
                        add_action( 'network_admin_menu', array( $this, '_options_page' ) );
                    }

                    // Admin Bar menu
                    add_action( 'admin_bar_menu', array(
                        $this,
                        '_admin_bar_menu'
                    ), $this->args['admin_bar_priority'] );

                    // Register setting
                    add_action( 'admin_init', array( $this, '_register_settings' ) );

                    // Display admin notices in dev_mode
                    if ( true == $this->args['dev_mode'] ) {
                        if ( true == $this->args['update_notice'] ) {
                            add_action( 'admin_init', array( $this, '_update_check' ) );
                        }
                    }

                    // Display admin notices
                    add_action( 'admin_notices', array( $this, '_admin_notices' ), 99 );

                    // Check for dismissed admin notices.
                    add_action( 'admin_init', array( $this, '_dismiss_admin_notice' ), 9 );

                    // Enqueue the admin page CSS and JS
                    if ( isset ( $_GET['page'] ) && $_GET['page'] == $this->args['page_slug'] ) {
                        add_action( 'admin_enqueue_scripts', array( $this, '_enqueue' ), 1 );
                    }

                    // Output dynamic CSS
                    // Frontend: Maybe enqueue dynamic CSS and Google fonts
                    if ( empty ( $this->args['output_location'] ) || in_array( 'frontend', $this->args['output_location'] ) ) {
                        add_action( 'wp_head', array( &$this, '_output_css' ), 150 );
                        add_action( 'wp_enqueue_scripts', array( &$this, '_enqueue_output' ), 150 );
                    }

                    // Login page: Maybe enqueue dynamic CSS and Google fonts
                    if ( in_array( 'login', $this->args['output_location'] ) ) {
                        add_action( 'login_head', array( &$this, '_output_css' ), 150 );
                        add_action( 'login_enqueue_scripts', array( &$this, '_enqueue_output' ), 150 );
                    }

                    // Admin area: Maybe enqueue dynamic CSS and Google fonts
                    if ( in_array( 'admin', $this->args['output_location'] ) ) {
                        add_action( 'admin_head', array( &$this, '_output_css' ), 150 );
                        add_action( 'admin_enqueue_scripts', array( &$this, '_enqueue_output' ), 150 );
                    }


                    add_action( 'wp_print_scripts', array( $this, 'vc_fixes' ), 100 );
                    add_action( 'admin_enqueue_scripts', array( $this, 'vc_fixes' ), 100 );


                    if ( $this->args['database'] == "network" && $this->args['network_admin'] ) {
                        add_action( 'network_admin_edit_redux_' . $this->args['opt_name'], array(
                            $this,
                            'save_network_page'
                        ), 10, 0 );
                        add_action( 'admin_bar_menu', array( $this, 'network_admin_bar' ), 999 );
                    }
                    // Ajax saving!!!
                    add_action( "wp_ajax_" . $this->args['opt_name'] . '_ajax_save', array( $this, "ajax_save" ) );

                    if ( $this->args['dev_mode'] == true || Redux_Helpers::isLocalHost() == true ) {
                        require_once 'core/dashboard.php';
                        new reduxDashboardWidget( $this );

                        if ( ! isset ( $GLOBALS['redux_notice_check'] ) ) {
                            require_once 'core/newsflash.php';

                            $params = array(
                                'dir_name'    => 'notice',
                                'server_file' => 'http://reduxframework.com/wp-content/uploads/redux/redux_notice.json',
                                'interval'    => 3,
                                'cookie_id'   => 'redux_blast',
                            );

                            new reduxNewsflash( $this, $params );
                            $GLOBALS['redux_notice_check'] = 1;
                        }
                    }
                }

                /**
                 * Loaded hook
                 * action 'redux/loaded'
                 *
                 * @param  object $this ReduxFramework
                 */
                do_action( 'redux/loaded', $this );
            }

            // __construct()

            private function set_redux_content() {
                $upload_dir        = wp_upload_dir();
                self::$_upload_dir = $upload_dir['basedir'] . '/redux/';
                self::$_upload_url = str_replace( array(
                    'https://',
                    'http://'
                ), '//', $upload_dir['baseurl'] . '/redux/' );
            }

            private function set_default_args() {
                $this->args = array(
                    'opt_name'                  => '',
                    // Must be defined by theme/plugin
                    'google_api_key'            => '',
                    // Must be defined to update the google fonts cache for the typography module
                    'google_update_weekly'      => false,
                    // Set to keep your google fonts updated weekly
                    'last_tab'                  => '',
                    // force a specific tab to always show on reload
                    'menu_icon'                 => '',
                    // menu icon
                    'menu_title'                => '',
                    // menu title/text
                    'page_title'                => '',
                    // option page title
                    'page_slug'                 => '',
                    'page_permissions'          => 'manage_options',
                    'menu_type'                 => 'menu',
                    // ('menu'|'submenu')
                    'page_parent'               => 'themes.php',
                    // requires menu_type = 'submenu
                    'page_priority'             => null,
                    'allow_sub_menu'            => true,
                    // allow submenus to be added if menu_type == menu
                    'save_defaults'             => true,
                    // Save defaults to the DB on it if empty
                    'footer_credit'             => '',
                    'async_typography'          => false,
                    'disable_google_fonts_link' => false,
                    'class'                     => '',
                    // Class that gets appended to all redux-containers
                    'admin_bar'                 => true,
                    'admin_bar_priority'        => 999,
                    // Show the panel pages on the admin bar
                    'admin_bar_icon'            => '',
                    // admin bar icon
                    'help_tabs'                 => array(),
                    'help_sidebar'              => '',
                    'database'                  => '',
                    // possible: options, theme_mods, theme_mods_expanded, transient, network
                    'customizer'                => false,
                    // setting to true forces get_theme_mod_expanded
                    'global_variable'           => '',
                    // Changes global variable from $GLOBALS['YOUR_OPT_NAME'] to whatever you set here. false disables the global variable
                    'output'                    => true,
                    // Dynamically generate CSS
                    'compiler'                  => true,
                    // Initiate the compiler hook
                    'output_tag'                => true,
                    // Print Output Tag
                    'output_location'           => array( 'frontend' ),
                    // Where  the dynamic CSS will be added. Can be any combination from: 'frontend', 'login', 'admin'
                    'transient_time'            => '',
                    'default_show'              => false,
                    // If true, it shows the default value
                    'default_mark'              => '',
                    // What to print by the field's title if the value shown is default
                    'update_notice'             => true,
                    // Recieve an update notice of new commits when in dev mode
                    'disable_save_warn'         => false,
                    // Disable the save warn
                    'open_expanded'             => false,
                    'hide_expand'               => false,
                    // Start the panel fully expanded to start with
                    'network_admin'             => false,
                    // Enable network admin when using network database mode
                    'network_sites'             => true,
                    // Enable sites as well as admin when using network database mode
                    'hide_reset'                => false,
                    'hide_save'                 => false,
                    'hints'                     => array(
                        'icon'          => 'el el-question-sign',
                        'icon_position' => 'right',
                        'icon_color'    => 'lightgray',
                        'icon_size'     => 'normal',
                        'tip_style'     => array(
                            'color'   => 'light',
                            'shadow'  => true,
                            'rounded' => false,
                            'style'   => '',
                        ),
                        'tip_position'  => array(
                            'my' => 'top_left',
                            'at' => 'bottom_right',
                        ),
                        'tip_effect'    => array(
                            'show' => array(
                                'effect'   => 'slide',
                                'duration' => '500',
                                'event'    => 'mouseover',
                            ),
                            'hide' => array(
                                'effect'   => 'fade',
                                'duration' => '500',
                                'event'    => 'click mouseleave',
                            ),
                        ),
                    ),
                    'show_import_export'        => true,
                    'show_options_object'       => true,
                    'dev_mode'                  => true,
                    'templates_path'            => '',
                    // Path to the templates file for various Redux elements
                    'ajax_save'                 => true,
                    // Disable the use of ajax saving for the panel
                    'use_cdn'                   => true,
                    'cdn_check_time'            => 1440,
                    'options_api'               => true,
                );
            }

            // Fix conflicts with Visual Composer.
            public function vc_fixes() {
                if ( redux_helpers::isFieldInUse( $this, 'ace_editor' ) ) {
                    wp_dequeue_script( 'wpb_ace' );
                    wp_deregister_script( 'wpb_ace' );
                }
            }

            public function network_admin_bar( $wp_admin_bar ) {

                $args = array(
                    'id'     => $this->args['opt_name'] . '_network_admin',
                    'title'  => $this->args['menu_title'],
                    'parent' => 'network-admin',
                    'href'   => network_admin_url( 'settings.php' ) . '?page=' . $this->args['page_slug'],
                    'meta'   => array( 'class' => 'redux-network-admin' )
                );
                $wp_admin_bar->add_node( $args );
            }

            public function save_network_page() {

                $data = $this->_validate_options( $_POST[ $this->args['opt_name'] ] );

                if ( ! empty ( $data ) ) {
                    $this->set_options( $data );
                }

                wp_redirect( add_query_arg( array(
                    'page'    => $this->args['page_slug'],
                    'updated' => 'true'
                ), network_admin_url( 'settings.php' ) ) );
                exit ();
            }

            public function _update_check() {
                // Only one notice per instance please
                if ( ! isset ( $GLOBALS['redux_update_check'] ) ) {
                    Redux_Functions::updateCheck( self::$_version );
                    $GLOBALS['redux_update_check'] = 1;
                }
            }

            public function _admin_notices() {
                Redux_Admin_Notices::adminNotices( $this->admin_notices );
            }

            public function _dismiss_admin_notice() {
                Redux_Admin_Notices::dismissAdminNotice();
            }

            /**
             * Load the plugin text domain for translation.
             *
             * @since    3.0.5
             */
            private function _internationalization() {

                /**
                 * Locale for text domain
                 * filter 'redux/textdomain/{opt_name}'
                 *
                 * @param string     The locale of the blog or from the 'locale' hook
                 * @param string     'redux-framework'  text domain
                 */
                //                $locale = apply_filters( "redux/textdomain/{$this->args['opt_name']}", get_locale(), 'redux-framework' );
                //
                //                if ( strpos( $locale, '_' ) === false ) {
                //                    if ( file_exists( self::$_dir . 'languages/' . strtolower( $locale ) . '_' . strtoupper( $locale ) . '.mo' ) ) {
                //                        $locale = strtolower( $locale ) . '_' . strtoupper( $locale );
                //                    }
                //                }

                $basename = basename( __FILE__ );
                $basepath = plugin_basename( __FILE__ );
                $basepath = str_replace( $basename, '', $basepath );

                $basepath = apply_filters( "redux/textdomain/basepath/{$this->args['opt_name']}", $basepath );

                load_plugin_textdomain( 'redux-framework', false, $basepath . 'languages' );
            }
            // _internationalization()

            /**
             * @return ReduxFramework
             */
            public function get_instance() {
                //self::$_instance = $this;
                return self::$instance;
            }

            // get_instance()

            private function _tracking() {
                if ( file_exists( dirname( __FILE__ ) . '/inc/tracking.php' ) ) {
                    require_once dirname( __FILE__ ) . '/inc/tracking.php';
                    $tracking = Redux_Tracking::get_instance();
                    $tracking->load( $this );
                }
            }
            // _tracking()

            /**
             * ->_get_default(); This is used to return the default value if default_show is set
             *
             * @since       1.0.1
             * @access      public
             *
             * @param       string $opt_name The option name to return
             * @param       mixed  $default  (null)  The value to return if default not set
             *
             * @return      mixed $default
             */
            public function _get_default( $opt_name, $default = null ) {
                if ( $this->args['default_show'] == true ) {

                    if ( empty ( $this->options_defaults ) ) {
                        $this->_default_values(); // fill cache
                    }

                    $default = array_key_exists( $opt_name, $this->options_defaults ) ? $this->options_defaults[ $opt_name ] : $default;
                }

                return $default;
            }
            // _get_default()

            /**
             * ->get(); This is used to return and option value from the options array
             *
             * @since       1.0.0
             * @access      public
             *
             * @param       string $opt_name The option name to return
             * @param       mixed  $default  (null) The value to return if option not set
             *
             * @return      mixed
             */
            public function get( $opt_name, $default = null ) {
                return ( ! empty ( $this->options[ $opt_name ] ) ) ? $this->options[ $opt_name ] : $this->_get_default( $opt_name, $default );
            }
            // get()

            /**
             * ->set(); This is used to set an arbitrary option in the options array
             *
             * @since       1.0.0
             * @access      public
             *
             * @param       string $opt_name The name of the option being added
             * @param       mixed  $value    The value of the option being added
             *
             * @return      void
             */
            public function set( $opt_name = '', $value = '' ) {
                if ( $opt_name != '' ) {
                    $this->options[ $opt_name ] = $value;
                    $this->set_options( $this->options );
                }
            }
            // set()

            /**
             * Set a global variable by the global_variable argument
             *
             * @since   3.1.5
             * @return  bool          (global was set)
             */
            private function set_global_variable() {
                if ( $this->args['global_variable'] ) {
                    $option_global = $this->args['global_variable'];
                    /**
                     * filter 'redux/options/{opt_name}/global_variable'
                     *
                     * @param array $value option value to set global_variable with
                     */
                    $GLOBALS[ $this->args['global_variable'] ] = apply_filters( "redux/options/{$this->args['opt_name']}/global_variable", $this->options );
                    if ( isset ( $this->transients['last_save'] ) ) {
                        // Deprecated
                        $GLOBALS[ $this->args['global_variable'] ]['REDUX_last_saved'] = $this->transients['last_save'];
                        // Last save key
                        $GLOBALS[ $this->args['global_variable'] ]['REDUX_LAST_SAVE'] = $this->transients['last_save'];
                    }
                    if ( isset ( $this->transients['last_compiler'] ) ) {
                        // Deprecated
                        $GLOBALS[ $this->args['global_variable'] ]['REDUX_COMPILER'] = $this->transients['last_compiler'];
                        // Last compiler hook key
                        $GLOBALS[ $this->args['global_variable'] ]['REDUX_LAST_COMPILER'] = $this->transients['last_compiler'];
                    }

                    return true;
                }

                return false;
            }
            // set_global_variable()

            /**
             * ->set_options(); This is used to set an arbitrary option in the options array
             *
             * @since ReduxFramework 3.0.0
             *
             * @param mixed $value the value of the option being added
             */
            public function set_options( $value = '' ) {

                $this->transients['last_save'] = time();

                if ( ! empty ( $value ) ) {

                    $this->options = $value;

                    if ( $this->args['database'] === 'transient' ) {
                        set_transient( $this->args['opt_name'] . '-transient', $value, $this->args['transient_time'] );
                    } else if ( $this->args['database'] === 'theme_mods' ) {
                        set_theme_mod( $this->args['opt_name'] . '-mods', $value );
                    } else if ( $this->args['database'] === 'theme_mods_expanded' ) {
                        foreach ( $value as $k => $v ) {
                            set_theme_mod( $k, $v );
                        }
                    } else if ( $this->args['database'] === 'network' ) {
                        // Strip those slashes!
                        $value = json_decode( stripslashes( json_encode( $value ) ), true );
                        update_site_option( $this->args['opt_name'], $value );
                    } else {
                        update_option( $this->args['opt_name'], $value );
                    }

                    // Store the changed values in the transient
                    if ( $value != $this->options ) {
                        foreach ( $value as $k => $v ) {
                            if ( ! isset ( $this->options[ $k ] ) ) {
                                $this->options[ $k ] = "";
                            } else if ( $v == $this->options[ $k ] ) {
                                unset ( $this->options[ $k ] );
                            }
                        }
                        $this->transients['changed_values'] = $this->options;
                    }

                    $this->options = $value;

                    // Set a global variable by the global_variable argument.
                    $this->set_global_variable();

                    // Saving the transient values
                    $this->set_transients();

                    //do_action( "redux-saved-{$this->args['opt_name']}", $value ); // REMOVE
                    //do_action( "redux/options/{$this->args['opt_name']}/saved", $value, $this->transients['changed_values'] );
                }
            }
            // set_options()

            /**
             * ->get_options(); This is used to get options from the database
             *
             * @since ReduxFramework 3.0.0
             */
            public function get_options() {
                $defaults = false;

                if ( ! empty ( $this->defaults ) ) {
                    $defaults = $this->defaults;
                }

                if ( $this->args['database'] === "transient" ) {
                    $result = get_transient( $this->args['opt_name'] . '-transient' );
                } else if ( $this->args['database'] === "theme_mods" ) {
                    $result = get_theme_mod( $this->args['opt_name'] . '-mods' );
                } else if ( $this->args['database'] === 'theme_mods_expanded' ) {
                    $result = get_theme_mods();
                } else if ( $this->args['database'] === 'network' ) {
                    $result = get_site_option( $this->args['opt_name'], array() );
                    $result = json_decode( stripslashes( json_encode( $result ) ), true );
                } else {
                    $result = get_option( $this->args['opt_name'], array() );
                }

                if ( empty ( $result ) && ! empty ( $defaults ) ) {
                    $results = $defaults;
                    $this->set_options( $results );
                } else {
                    $this->options = $result;
                }

                /**
                 * action 'redux/options/{opt_name}/options'
                 *
                 * @param mixed $value option values
                 */
                $this->options = apply_filters( "redux/options/{$this->args['opt_name']}/options", $this->options );

                // Get transient values
                $this->get_transients();

                // Set a global variable by the global_variable argument.
                $this->set_global_variable();
            }
            // get_options()

            /**
             * ->get_wordpress_date() - Get Wordpress specific data from the DB and return in a usable array
             *
             * @since ReduxFramework 3.0.0
             */
            public function get_wordpress_data( $type = false, $args = array() ) {
                $data = "";
                //return $data;
                /**
                 * filter 'redux/options/{opt_name}/wordpress_data/{type}/'
                 *
                 * @deprecated
                 *
                 * @param string $data
                 */
                $data = apply_filters( "redux/options/{$this->args['opt_name']}/wordpress_data/$type/", $data ); // REMOVE LATER

                /**
                 * filter 'redux/options/{opt_name}/data/{type}'
                 *
                 * @param string $data
                 */
                $data = apply_filters( "redux/options/{$this->args['opt_name']}/data/$type", $data );

                $argsKey = "";
                foreach ( $args as $key => $value ) {
                    if ( ! is_array( $value ) ) {
                        $argsKey .= $value . "-";
                    } else {
                        $argsKey .= implode( "-", $value );
                    }
                }

                if ( empty ( $data ) && isset ( $this->wp_data[ $type . $argsKey ] ) ) {
                    $data = $this->wp_data[ $type . $argsKey ];
                }

                if ( empty ( $data ) && ! empty ( $type ) ) {

                    /**
                     * Use data from Wordpress to populate options array
                     * */
                    if ( ! empty ( $type ) && empty ( $data ) ) {
                        if ( empty ( $args ) ) {
                            $args = array();
                        }

                        $data = array();
                        $args = wp_parse_args( $args, array() );

                        if ( $type == "categories" || $type == "category" ) {
                            $cats = get_categories( $args );
                            if ( ! empty ( $cats ) ) {
                                foreach ( $cats as $cat ) {
                                    $data[ $cat->term_id ] = $cat->name;
                                }
                                //foreach
                            } // If
                        } else if ( $type == "menus" || $type == "menu" ) {
                            $menus = wp_get_nav_menus( $args );
                            if ( ! empty ( $menus ) ) {
                                foreach ( $menus as $item ) {
                                    $data[ $item->term_id ] = $item->name;
                                }
                                //foreach
                            }
                            //if
                        } else if ( $type == "pages" || $type == "page" ) {
                            if ( ! isset ( $args['posts_per_page'] ) ) {
                                $args['posts_per_page'] = 20;
                            }
                            $pages = get_pages( $args );
                            if ( ! empty ( $pages ) ) {
                                foreach ( $pages as $page ) {
                                    $data[ $page->ID ] = $page->post_title;
                                }
                                //foreach
                            }
                            //if
                        } else if ( $type == "terms" || $type == "term" ) {
                            $taxonomies = $args['taxonomies'];
                            unset ( $args['taxonomies'] );
                            $terms = get_terms( $taxonomies, $args ); // this will get nothing
                            if ( ! empty ( $terms ) && ! is_a( $terms, 'WP_Error' ) ) {
                                foreach ( $terms as $term ) {
                                    $data[ $term->term_id ] = $term->name;
                                }
                                //foreach
                            } // If
                        } else if ( $type == "taxonomy" || $type == "taxonomies" ) {
                            $taxonomies = get_taxonomies( $args );
                            if ( ! empty ( $taxonomies ) ) {
                                foreach ( $taxonomies as $key => $taxonomy ) {
                                    $data[ $key ] = $taxonomy;
                                }
                                //foreach
                            } // If
                        } else if ( $type == "posts" || $type == "post" ) {
                            $posts = get_posts( $args );
                            if ( ! empty ( $posts ) ) {
                                foreach ( $posts as $post ) {
                                    $data[ $post->ID ] = $post->post_title;
                                }
                                //foreach
                            }
                            //if
                        } else if ( $type == "post_type" || $type == "post_types" ) {
                            global $wp_post_types;

                            $defaults   = array(
                                'public'              => true,
                                'exclude_from_search' => false,
                            );
                            $args       = wp_parse_args( $args, $defaults );
                            $output     = 'names';
                            $operator   = 'and';
                            $post_types = get_post_types( $args, $output, $operator );

                            ksort( $post_types );

                            foreach ( $post_types as $name => $title ) {
                                if ( isset ( $wp_post_types[ $name ]->labels->menu_name ) ) {
                                    $data[ $name ] = $wp_post_types[ $name ]->labels->menu_name;
                                } else {
                                    $data[ $name ] = ucfirst( $name );
                                }
                            }
                        } else if ( $type == "tags" || $type == "tag" ) { // NOT WORKING!
                            $tags = get_tags( $args );
                            if ( ! empty ( $tags ) ) {
                                foreach ( $tags as $tag ) {
                                    $data[ $tag->term_id ] = $tag->name;
                                }
                                //foreach
                            }
                            //if
                        } else if ( $type == "menu_location" || $type == "menu_locations" ) {
                            global $_wp_registered_nav_menus;

                            foreach ( $_wp_registered_nav_menus as $k => $v ) {
                                $data[ $k ] = $v;
                            }
                        } else if ( $type == "image_size" || $type == "image_sizes" ) {
                            global $_wp_additional_image_sizes;

                            foreach ( $_wp_additional_image_sizes as $size_name => $size_attrs ) {
                                $data[ $size_name ] = $size_name . ' - ' . $size_attrs['width'] . ' x ' . $size_attrs['height'];
                            }
                        } else if ( $type == "elusive-icons" || $type == "elusive-icon" || $type == "elusive" ||
                                    $type == "font-icon" || $type == "font-icons" || $type == "icons"
                        ) {

                            /**
                             * filter 'redux-font-icons'
                             *
                             * @deprecated
                             *
                             * @param array $font_icons array of elusive icon classes
                             */
                            $font_icons = apply_filters( 'redux-font-icons', array() ); // REMOVE LATER

                            /**
                             * filter 'redux/font-icons'
                             *
                             * @deprecated
                             *
                             * @param array $font_icons array of elusive icon classes
                             */
                            $font_icons = apply_filters( 'redux/font-icons', $font_icons );

                            /**
                             * filter 'redux/{opt_name}/field/font/icons'
                             *
                             * @deprecated
                             *
                             * @param array $font_icons array of elusive icon classes
                             */
                            $font_icons = apply_filters( "redux/{$this->args['opt_name']}/field/font/icons", $font_icons );

                            foreach ( $font_icons as $k ) {
                                $data[ $k ] = $k;
                            }
                        } else if ( $type == "roles" ) {
                            /** @global WP_Roles $wp_roles */
                            global $wp_roles;

                            $data = $wp_roles->get_names();
                        } else if ( $type == "sidebars" || $type == "sidebar" ) {
                            /** @global array $wp_registered_sidebars */
                            global $wp_registered_sidebars;

                            foreach ( $wp_registered_sidebars as $key => $value ) {
                                $data[ $key ] = $value['name'];
                            }
                        } else if ( $type == "capabilities" ) {
                            /** @global WP_Roles $wp_roles */
                            global $wp_roles;

                            foreach ( $wp_roles->roles as $role ) {
                                foreach ( $role['capabilities'] as $key => $cap ) {
                                    $data[ $key ] = ucwords( str_replace( '_', ' ', $key ) );
                                }
                            }
                        } else if ( $type == "callback" ) {
                            if ( ! is_array( $args ) ) {
                                $args = array( $args );
                            }
                            $data = call_user_func( $args[0] );
                        } else if ( $type == "users" || $type == "users" ) {
                            $users = get_users( $args );
                            if ( ! empty ( $users ) ) {
                                foreach ( $users as $user ) {
                                    $data[ $user->ID ] = $user->display_name;
                                }
                                //foreach
                            }
                            //if
                        }
                        //if
                    }
                    //if

                    $this->wp_data[ $type . $argsKey ] = $data;
                }

                //if

                return $data;
            }
            // get_wordpress_data()

            /**
             * ->show(); This is used to echo and option value from the options array
             *
             * @since       1.0.0
             * @access      public
             *
             * @param       string $opt_name The name of the option being shown
             * @param       mixed  $default  The value to show if $opt_name isn't set
             *
             * @return      void
             */
            public function show( $opt_name, $default = '' ) {
                $option = $this->get( $opt_name );
                if ( ! is_array( $option ) && $option != '' ) {
                    echo $option;
                } elseif ( $default != '' ) {
                    echo $this->_get_default( $opt_name, $default );
                }
            }
            // show()

            /**
             * Get the default value for an option
             *
             * @since  3.3.6
             * @access public
             *
             * @param string $key       The option's ID
             * @param string $array_key The key of the default's array
             *
             * @return mixed
             */
            public function get_default_value( $key, $array_key = false ) {
                if ( empty ( $this->options_defaults ) ) {
                    $this->options_defaults = $this->_default_values();
                }

                $defaults = $this->options_defaults;
                $value    = '';

                if ( isset ( $defaults[ $key ] ) ) {
                    if ( $array_key !== false && isset ( $defaults[ $key ][ $array_key ] ) ) {
                        $value = $defaults[ $key ][ $array_key ];
                    } else {
                        $value = $defaults[ $key ];
                    }
                }

                return $value;
            }

            public function field_default_values( $field ) {
                // Detect what field types are being used
                if ( ! isset ( $this->fields[ $field['type'] ][ $field['id'] ] ) ) {
                    $this->fields[ $field['type'] ][ $field['id'] ] = 1;
                } else {
                    $this->fields[ $field['type'] ] = array( $field['id'] => 1 );
                }
                if ( isset ( $field['default'] ) ) {
                    $this->options_defaults[ $field['id'] ] = apply_filters( "redux/{$this->args['opt_name']}/field/{$field['type']}/defaults", $field['default'], $field );
                } elseif ( ( $field['type'] != "ace_editor" ) ) {
                    // Sorter data filter

                    if ( isset( $field['data'] ) && ! empty( $field['data'] ) ) {
                        if ( ! isset( $field['args'] ) ) {
                            $field['args'] = array();
                        }
                        if ( is_array( $field['data'] ) && ! empty( $field['data'] ) ) {
                            foreach ( $field['data'] as $key => $data ) {
                                if ( ! empty( $data ) ) {
                                    if ( ! isset ( $field['args'][ $key ] ) ) {
                                        $field['args'][ $key ] = array();
                                    }
                                    $field['options'][ $key ] = $this->get_wordpress_data( $data, $field['args'][ $key ] );
                                }
                            }
                        } else {
                            $field['options'] = $this->get_wordpress_data( $field['data'], $field['args'] );
                        }
                    }

                    if ( $field['type'] == "sorter" && isset ( $field['data'] ) && ! empty ( $field['data'] ) && is_array( $field['data'] ) ) {
                        if ( ! isset ( $field['args'] ) ) {
                            $field['args'] = array();
                        }
                        foreach ( $field['data'] as $key => $data ) {
                            if ( ! isset ( $field['args'][ $key ] ) ) {
                                $field['args'][ $key ] = array();
                            }
                            $field['options'][ $key ] = $this->get_wordpress_data( $data, $field['args'][ $key ] );
                        }
                    }

                    if ( isset ( $field['options'] ) ) {
                        if ( $field['type'] == "sortable" ) {
                            $this->options_defaults[ $field['id'] ] = array();
                        } elseif ( $field['type'] == "image_select" ) {
                            $this->options_defaults[ $field['id'] ] = '';
                        } elseif ( $field['type'] == "select" ) {
                            $this->options_defaults[ $field['id'] ] = '';
                        } else {
                            $this->options_defaults[ $field['id'] ] = $field['options'];
                        }
                    }
                }
            }

            /**
             * Get default options into an array suitable for the settings API
             *
             * @since       1.0.0
             * @access      public
             * @return      array $this->options_defaults
             */
            public function _default_values() {
                if ( ! is_null( $this->sections ) && is_null( $this->options_defaults ) ) {

                    // fill the cache
                    foreach ( $this->sections as $sk => $section ) {
                        if ( ! isset ( $section['id'] ) ) {
                            if ( ! is_numeric( $sk ) || ! isset ( $section['title'] ) ) {
                                $section['id'] = $sk;
                            } else {
                                $section['id'] = sanitize_title( $section['title'], $sk );
                            }
                            $this->sections[ $sk ] = $section;
                        }
                        if ( isset ( $section['fields'] ) ) {
                            foreach ( $section['fields'] as $k => $field ) {
                                if ( empty ( $field['id'] ) && empty ( $field['type'] ) ) {
                                    continue;
                                }

                                if ( in_array( $field['type'], array( 'ace_editor' ) ) && isset ( $field['options'] ) ) {
                                    $this->sections[ $sk ]['fields'][ $k ]['args'] = $field['options'];
                                    unset ( $this->sections[ $sk ]['fields'][ $k ]['options'] );
                                }

                                if ( $field['type'] == "section" && isset ( $field['indent'] ) && $field['indent'] == "true" ) {
                                    $field['class'] = isset ( $field['class'] ) ? $field['class'] : '';
                                    $field['class'] .= " redux-section-indent-start";
                                    $this->sections[ $sk ]['fields'][ $k ] = $field;
                                }
                                $this->field_default_values( $field );
                            }
                        }
                    }
                }

                /**
                 * filter 'redux/options/{opt_name}/defaults'
                 *
                 * @param array $defaults option default values
                 */
                $this->transients['changed_values'] = isset ( $this->transients['changed_values'] ) ? $this->transients['changed_values'] : array();
                $this->options_defaults             = apply_filters( "redux/options/{$this->args['opt_name']}/defaults", $this->options_defaults, $this->transients['changed_values'] );

                return $this->options_defaults;
            }

            /**
             * Set default options on admin_init if option doesn't exist
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            private function _default_cleanup() {

                // Fix the global variable name
                if ( $this->args['global_variable'] == "" && $this->args['global_variable'] !== false ) {
                    $this->args['global_variable'] = str_replace( '-', '_', $this->args['opt_name'] );
                }

                // Force dev_mode on WP_DEBUG = true and if it's a local server
                if ( Redux_Helpers::isLocalHost() || ( Redux_Helpers::isWpDebug() ) ) {
                    if ( $this->args['dev_mode'] != true ) {
                        $this->args['update_notice'] = false;
                    }
                    $this->dev_mode_forced  = true;
                    $this->args['dev_mode'] = true;
                    if ( isset( $this->args['forced_dev_mode_off'] ) && $this->args['forced_dev_mode_off'] == true ) {
                        $this->dev_mode_forced  = false;
                        $this->args['dev_mode'] = false;
                    }
                }

                // Auto create the page_slug appropriately
                if ( empty( $this->args['page_slug'] ) ) {
                    if ( ! empty( $this->args['display_name'] ) ) {
                        $this->args['page_slug'] = sanitize_html_class( $this->args['display_name'] );
                    } else if ( ! empty( $this->args['page_title'] ) ) {
                        $this->args['page_slug'] = sanitize_html_class( $this->args['page_title'] );
                    } else if ( ! empty( $this->args['menu_title'] ) ) {
                        $this->args['page_slug'] = sanitize_html_class( $this->args['menu_title'] );
                    } else {
                        $this->args['page_slug'] = str_replace( '-', '_', $this->args['opt_name'] );
                    }
                }

                if ( isset( $this->args['customizer_only'] ) && $this->args['customizer_only'] == true ) {
                    $this->args['menu_type']      = 'hidden';
                    $this->args['customizer']     = true;
                    $this->args['admin_bar']      = false;
                    $this->args['allow_sub_menu'] = false;
                }

                // Check if the Airplane Mode plugin is installed
                if ( class_exists( 'Airplane_Mode_Core' ) ) {
                    $airplane = Airplane_Mode_Core::getInstance();
                    if ( method_exists( $airplane, 'enabled' ) ) {
                        if ( $airplane->enabled() ) {
                            $this->args['use_cdn'] = false;
                        }
                    } else if ( $airplane->check_status() == 'on' ) {
                        $this->args['use_cdn'] = false;
                    }
                }
            }

            /**
             * Class Add Sub Menu Function, creates options submenu in Wordpress admin area.
             *
             * @since       3.1.9
             * @access      private
             * @return      void
             */
            private function add_submenu( $page_parent, $page_title, $menu_title, $page_permissions, $page_slug ) {
                global $submenu;

                // Just in case. One never knows.
                $page_parent = strtolower( $page_parent );

                $test = array(
                    'index.php'               => 'dashboard',
                    'edit.php'                => 'posts',
                    'upload.php'              => 'media',
                    'link-manager.php'        => 'links',
                    'edit.php?post_type=page' => 'pages',
                    'edit-comments.php'       => 'comments',
                    'themes.php'              => 'theme',
                    'plugins.php'             => 'plugins',
                    'users.php'               => 'users',
                    'tools.php'               => 'management',
                    'options-general.php'     => 'options',
                );

                if ( isset ( $test[ $page_parent ] ) ) {
                    $function   = 'add_' . $test[ $page_parent ] . '_page';
                    $this->page = $function (
                        $page_title, $menu_title, $page_permissions, $page_slug, array( $this, 'generate_panel' )
                    );
                } else {
                    // Network settings and Post type menus. These do not have
                    // wrappers and need to be appened to using add_submenu_page.
                    // Okay, since we've left the post type menu appending
                    // as default, we need to validate it, so anything that
                    // isn't post_type=<post_type> doesn't get through and mess
                    // things up.
                    $addMenu = false;
                    if ( 'settings.php' != $page_parent ) {
                        // Establish the needle
                        $needle = '?post_type=';

                        // Check if it exists in the page_parent (how I miss instr)
                        $needlePos = strrpos( $page_parent, $needle );

                        // It's there, so...
                        if ( $needlePos > 0 ) {

                            // Get the post type.
                            $postType = substr( $page_parent, $needlePos + strlen( $needle ) );

                            // Ensure it exists.
                            if ( post_type_exists( $postType ) ) {
                                // Set flag to add the menu page
                                $addMenu = true;
                            }
                            // custom menu
                        } elseif ( isset ( $submenu[ $this->args['page_parent'] ] ) ) {
                            $addMenu = true;
                        } else {
                            global $menu;

                            foreach ( $menu as $menupriority => $menuitem ) {
                                $needle_menu_slug = isset ( $menuitem ) ? $menuitem[2] : false;
                                if ( $needle_menu_slug != false ) {

                                    // check if the current needle menu equals page_parent
                                    if ( strcasecmp( $needle_menu_slug, $page_parent ) == 0 ) {

                                        // found an empty parent menu
                                        $addMenu = true;
                                    }
                                }
                            }
                        }
                    } else {
                        // The page_parent was settings.php, so set menu add
                        // flag to true.
                        $addMenu = true;
                    }
                    // Add the submenu if it's permitted.
                    if ( true == $addMenu ) {
                        // ONLY for non-wp.org themes OR plugins. Theme-Check alert shown if used and IS theme.
                        $this->page = call_user_func( 'add_submenu_page', $page_parent, $page_title, $menu_title, $page_permissions, $page_slug, array(
                            &$this,
                            'generate_panel'
                        ) );
                    }
                }
            }

            /**
             * Class Options Page Function, creates main options page.
             *
             * @since       1.0.0
             * @access      public
             * @return void
             */
            public function _options_page() {

                if ( $this->args['menu_type'] == 'hidden' ) {

                    // No menu to add!
                } else if ( $this->args['menu_type'] == 'submenu' ) {
                    $this->add_submenu(
                        $this->args['page_parent'], $this->args['page_title'], $this->args['menu_title'], $this->args['page_permissions'], $this->args['page_slug']
                    );
                } else {
                    // Theme-Check notice is displayed for WP.org theme devs, informing them to NOT use this.
                    $this->page = call_user_func( 'add_menu_page', $this->args['page_title'], $this->args['menu_title'], $this->args['page_permissions'], $this->args['page_slug'], array(
                        &$this,
                        'generate_panel'
                    ), $this->args['menu_icon'], $this->args['page_priority']
                    );

                    if ( true === $this->args['allow_sub_menu'] ) {
                        if ( ! isset ( $section['type'] ) || $section['type'] != 'divide' ) {
                            foreach ( $this->sections as $k => $section ) {
                                $canBeSubSection = ( $k > 0 && ( ! isset ( $this->sections[ ( $k ) ]['type'] ) || $this->sections[ ( $k ) ]['type'] != "divide" ) ) ? true : false;

                                if ( ! isset ( $section['title'] ) || ( $canBeSubSection && ( isset ( $section['subsection'] ) && $section['subsection'] == true ) ) ) {
                                    continue;
                                }

                                if ( isset ( $section['submenu'] ) && $section['submenu'] == false ) {
                                    continue;
                                }

                                if ( isset ( $section['customizer_only'] ) && $section['customizer_only'] == true ) {
                                    continue;
                                }

                                if ( isset ( $section['hidden'] ) && $section['hidden'] == true ) {
                                    continue;
                                }

                                if ( isset( $section['permissions'] ) && ! current_user_can( $section['permissions'] ) ) {
                                    continue;
                                }

                                // ONLY for non-wp.org themes OR plugins. Theme-Check alert shown if used and IS theme.
                                call_user_func( 'add_submenu_page', $this->args['page_slug'], $section['title'], $section['title'], $this->args['page_permissions'], $this->args['page_slug'] . '&tab=' . $k,
                                    //create_function( '$a', "return null;" )
                                    '__return_null' );
                            }

                            // Remove parent submenu item instead of adding null item.
                            remove_submenu_page( $this->args['page_slug'], $this->args['page_slug'] );
                        }
                    }
                }

                add_action( "load-{$this->page}", array( &$this, '_load_page' ) );
            }
            // _options_page()

            /**
             * Add admin bar menu
             *
             * @since       3.1.5.16
             * @access      public
             * @global      $menu , $submenu, $wp_admin_bar
             * @return      void
             */
            public function _admin_bar_menu() {
                global $menu, $submenu, $wp_admin_bar;

                $ct         = wp_get_theme();
                $theme_data = $ct;

                if ( ! is_super_admin() || ! is_admin_bar_showing() || ! $this->args['admin_bar'] || $this->args['menu_type'] == 'hidden' ) {
                    return;
                }

                if ( $menu ) {
                    foreach ( $menu as $menu_item ) {
                        if ( isset ( $menu_item[2] ) && $menu_item[2] === $this->args["page_slug"] ) {

                            // Fetch the title
                            $title = empty ( $this->args['admin_bar_icon'] ) ? $menu_item[0] : '<span class="ab-icon ' . $this->args['admin_bar_icon'] . '"></span>' . $menu_item[0];

                            $nodeargs = array(
                                'id'    => $menu_item[2],
                                'title' => $title,
                                'href'  => admin_url( 'admin.php?page=' . $menu_item[2] ),
                                'meta'  => array()
                            );
                            $wp_admin_bar->add_node( $nodeargs );

                            break;
                        }
                    }

                    if ( isset ( $submenu[ $this->args["page_slug"] ] ) && is_array( $submenu[ $this->args["page_slug"] ] ) ) {
                        foreach ( $submenu[ $this->args["page_slug"] ] as $index => $redux_options_submenu ) {
                            $subnodeargs = array(
                                'id'     => $this->args["page_slug"] . '_' . $index,
                                'title'  => $redux_options_submenu[0],
                                'parent' => $this->args["page_slug"],
                                'href'   => admin_url( 'admin.php?page=' . $redux_options_submenu[2] ),
                            );

                            $wp_admin_bar->add_node( $subnodeargs );
                        }
                    }

                    // Let's deal with external links
                    if ( isset ( $this->args['admin_bar_links'] ) ) {

                        if ( ! $this->args['dev_mode'] && $this->omit_admin_items ) {
                            return;
                        }

                        // Group for Main Root Menu (External Group)
                        $wp_admin_bar->add_node( array(
                            'id'     => $this->args["page_slug"] . '-external',
                            'parent' => $this->args["page_slug"],
                            'group'  => true,
                            'meta'   => array( 'class' => 'ab-sub-secondary' )
                        ) );

                        // Add Child Menus to External Group Menu
                        foreach ( $this->args['admin_bar_links'] as $link ) {
                            if ( ! isset ( $link['id'] ) ) {
                                $link['id'] = $this->args["page_slug"] . '-sub-' . sanitize_html_class( $link['title'] );
                            }
                            $externalnodeargs = array(
                                'id'     => $link['id'],
                                'title'  => $link['title'],
                                'parent' => $this->args["page_slug"] . '-external',
                                'href'   => $link['href'],
                                'meta'   => array( 'target' => '_blank' )
                            );

                            $wp_admin_bar->add_node( $externalnodeargs );
                        }
                    }
                } else {
                    // Fetch the title
                    $title = empty ( $this->args['admin_bar_icon'] ) ? $this->args['menu_title'] : '<span class="ab-icon ' . $this->args['admin_bar_icon'] . '"></span>' . $this->args['menu_title'];

                    $nodeargs = array(
                        'id'    => $this->args["page_slug"],
                        'title' => $title,
                        // $theme_data->get( 'Name' ) . " " . __( 'Options', 'redux-framework-demo' ),
                        'href'  => admin_url( 'admin.php?page=' . $this->args["page_slug"] ),
                        'meta'  => array()
                    );

                    $wp_admin_bar->add_node( $nodeargs );
                }
            }
            // _admin_bar_menu()

            /**
             * Output dynamic CSS at bottom of HEAD
             *
             * @since       3.2.8
             * @access      public
             * @return      void
             */
            public function _output_css() {
                if ( $this->args['output'] == false && $this->args['compiler'] == false ) {
                    return;
                }

                if ( isset ( $this->no_output ) ) {
                    return;
                }

                if ( ! empty ( $this->outputCSS ) && ( $this->args['output_tag'] == true || ( isset ( $_POST['customized'] ) ) ) ) {
                    echo '<style type="text/css" title="dynamic-css" class="options-output">' . $this->outputCSS . '</style>';
                }
            }

            /**
             * Enqueue CSS and Google fonts for front end
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            public function _enqueue_output() {
                if ( $this->args['output'] == false && $this->args['compiler'] == false ) {
                    return;
                }

                /** @noinspection PhpUnusedLocalVariableInspection */
                foreach ( $this->sections as $k => $section ) {
                    if ( isset ( $section['type'] ) && ( $section['type'] == 'divide' ) ) {
                        continue;
                    }

                    if ( isset ( $section['fields'] ) ) {
                        /** @noinspection PhpUnusedLocalVariableInspection */
                        foreach ( $section['fields'] as $fieldk => $field ) {
                            if ( isset ( $field['type'] ) && $field['type'] != "callback" ) {
                                $field_class = "ReduxFramework_{$field['type']}";
                                if ( ! class_exists( $field_class ) ) {

                                    if ( ! isset ( $field['compiler'] ) ) {
                                        $field['compiler'] = "";
                                    }

                                    /**
                                     * Field class file
                                     * filter 'redux/{opt_name}/field/class/{field.type}
                                     *
                                     * @param       string        field class file
                                     * @param array $field        field config data
                                     */
                                    $class_file = apply_filters( "redux/{$this->args['opt_name']}/field/class/{$field['type']}", self::$_dir . "inc/fields/{$field['type']}/field_{$field['type']}.php", $field );

                                    if ( $class_file && file_exists( $class_file ) && ! class_exists( $field_class ) ) {
                                        /** @noinspection PhpIncludeInspection */
                                        require_once $class_file;
                                    }
                                }

                                if ( ! empty ( $this->options[ $field['id'] ] ) && class_exists( $field_class ) && method_exists( $field_class, 'output' ) && $this->_can_output_css( $field ) ) {
                                    $field = apply_filters( "redux/field/{$this->args['opt_name']}/output_css", $field );

                                    if ( ! empty ( $field['output'] ) && ! is_array( $field['output'] ) ) {
                                        $field['output'] = array( $field['output'] );
                                    }

                                    $value   = isset ( $this->options[ $field['id'] ] ) ? $this->options[ $field['id'] ] : '';
                                    $enqueue = new $field_class ( $field, $value, $this );

                                    if ( ( ( isset ( $field['output'] ) && ! empty ( $field['output'] ) ) || ( isset ( $field['compiler'] ) && ! empty ( $field['compiler'] ) ) || $field['type'] == "typography" || $field['type'] == "icon_select" ) ) {
                                        $enqueue->output();
                                    }
                                }
                            }
                        }
                    }
                }

                // For use like in the customizer. Stops the output, but passes the CSS in the variable for the compiler
                if ( isset ( $this->no_output ) ) {
                    return;
                }

                if ( ! empty ( $this->typography ) && ! empty ( $this->typography ) && filter_var( $this->args['output'], FILTER_VALIDATE_BOOLEAN ) ) {
                    $version    = ! empty ( $this->transients['last_save'] ) ? $this->transients['last_save'] : '';
                    $typography = new ReduxFramework_typography ( null, null, $this );

                    if ( $this->args['async_typography'] && ! empty ( $this->typography ) ) {
                        $families = array();
                        foreach ( $this->typography as $key => $value ) {
                            $families[] = $key;
                        }
                        ?>
                        <script>
                            /* You can add more configuration options to webfontloader by previously defining the WebFontConfig with your options */
                            if ( typeof WebFontConfig === "undefined" ) {
                                WebFontConfig = new Object();
                            }
                            WebFontConfig['google'] = {families: [<?php echo $typography->makeGoogleWebfontString ( $this->typography ) ?>]};

                            (function() {
                                var wf = document.createElement( 'script' );
                                wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1.5.3/webfont.js';
                                wf.type = 'text/javascript';
                                wf.async = 'true';
                                var s = document.getElementsByTagName( 'script' )[0];
                                s.parentNode.insertBefore( wf, s );
                            })();
                        </script>
                        <?php
                    } elseif ( ! $this->args['disable_google_fonts_link'] ) {
                        $protocol = ( ! empty ( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ) ? "https:" : "http:";

                        //echo '<link rel="stylesheet" id="options-google-fonts" title="" href="'.$protocol.$typography->makeGoogleWebfontLink( $this->typography ).'&amp;v='.$version.'" type="text/css" media="all" />';
                        wp_register_style( 'redux-google-fonts-' . $this->args['opt_name'], $protocol . $typography->makeGoogleWebfontLink( $this->typography ), '', $version );
                        wp_enqueue_style( 'redux-google-fonts-' . $this->args['opt_name'] );
                    }
                }
            }
            // _enqueue_output()

            /**
             * Enqueue CSS/JS for options page
             *
             * @since       1.0.0
             * @access      public
             * @global      $wp_styles
             * @return      void
             */
            public function _enqueue() {
                require_once 'core/enqueue.php';
                $enqueue = new reduxCoreEnqueue ( $this );
                $enqueue->init();
            }
            // _enqueue()

            /**
             * Show page help
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            public function _load_page() {

                // Do admin head action for this page
                add_action( 'admin_head', array( &$this, 'admin_head' ) );

                // Do admin footer text hook
                add_filter( 'admin_footer_text', array( &$this, 'admin_footer_text' ) );

                $screen = get_current_screen();

                if ( is_array( $this->args['help_tabs'] ) ) {
                    foreach ( $this->args['help_tabs'] as $tab ) {
                        $screen->add_help_tab( $tab );
                    }
                }

                // If hint argument is set, display hint tab
                if ( true == $this->show_hints ) {
                    global $current_user;

                    // Users enable/disable hint choice
                    $hint_status = get_user_meta( $current_user->ID, 'ignore_hints' ) ? get_user_meta( $current_user->ID, 'ignore_hints', true ) : 'true';

                    // current page parameters
                    $curPage = esc_attr( $_GET['page'] );

                    $curTab = '0';
                    if ( isset ( $_GET['tab'] ) ) {
                        $curTab = esc_attr( $_GET['tab'] );
                    }

                    // Default url values for enabling hints.
                    $dismiss = 'true';
                    $s       = __( 'Enable', 'redux-framework' );

                    // Values for disabling hints.
                    if ( 'true' == $hint_status ) {
                        $dismiss = 'false';
                        $s       = __( 'Disable', 'redux-framework' );
                    }

                    // Make URL
                    $url = '<a class="redux_hint_status" href="?dismiss=' . $dismiss . '&amp;id=hints&amp;page=' . $curPage . '&amp;tab=' . $curTab . '">' . $s . ' hints</a>';

                    $event = __( 'moving the mouse over', 'redux-framework' );
                    if ( 'click' == $this->args['hints']['tip_effect']['show']['event'] ) {
                        $event = __( 'clicking', 'redux-framework' );
                    }

                    // Construct message
                    $msg = sprintf( __( 'Hints are tooltips that popup when %d the hint icon, offering addition information about the field in which they appear.  They can be %d d by using the link below.', 'redux-framework' ), $event, strtolower( $s ) ) . '<br/><br/>' . $url;

                    // Construct hint tab
                    $tab = array(
                        'id'      => 'redux-hint-tab',
                        'title'   => __( 'Hints', 'redux-framework' ),
                        'content' => '<p>' . $msg . '</p>'
                    );

                    $screen->add_help_tab( $tab );
                }

                // Sidebar text
                if ( $this->args['help_sidebar'] != '' ) {

                    // Specify users text from arguments
                    $screen->set_help_sidebar( $this->args['help_sidebar'] );
                } else {

                    // If sidebar text is empty and hints are active, display text
                    // about hints.
                    if ( true == $this->show_hints ) {
                        $screen->set_help_sidebar( '<p><strong>Redux Framework</strong><br/><br/>Hint Tooltip Preferences</p>' );
                    }
                }

                /**
                 * action 'redux-load-page-{opt_name}'
                 *
                 * @deprecated
                 *
                 * @param object $screen WP_Screen
                 */
                do_action( "redux-load-page-{$this->args['opt_name']}", $screen ); // REMOVE

                /**
                 * action 'redux/page/{opt_name}/load'
                 *
                 * @param object $screen WP_Screen
                 */
                do_action( "redux/page/{$this->args['opt_name']}/load", $screen );
            }
            // _load_page()

            /**
             * Do action redux-admin-head for options page
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            public function admin_head() {
                /**
                 * action 'redux-admin-head-{opt_name}'
                 *
                 * @deprecated
                 *
                 * @param  object $this ReduxFramework
                 */
                do_action( "redux-admin-head-{$this->args['opt_name']}", $this ); // REMOVE

                /**
                 * action 'redux/page/{opt_name}/header'
                 *
                 * @param  object $this ReduxFramework
                 */
                do_action( "redux/page/{$this->args['opt_name']}/header", $this );
            }
            // admin_head()

            /**
             * Return footer text
             *
             * @since       2.0.0
             * @access      public
             * @return      string $this->args['footer_credit']
             */
            public function admin_footer_text() {
                return $this->args['footer_credit'];
            }
            // admin_footer_text()

            /**
             * Return default output string for use in panel
             *
             * @since       3.1.5
             * @access      public
             * @return      string default_output
             */
            private function get_default_output_string( $field ) {
                $default_output = "";

                if ( ! isset ( $field['default'] ) ) {
                    $field['default'] = "";
                }

                if ( ! is_array( $field['default'] ) ) {
                    if ( ! empty ( $field['options'][ $field['default'] ] ) ) {
                        if ( ! empty ( $field['options'][ $field['default'] ]['alt'] ) ) {
                            $default_output .= $field['options'][ $field['default'] ]['alt'] . ', ';
                        } else {
                            // TODO: This serialize fix may not be the best solution. Look into it. PHP 5.4 error without serialize
                            if ( ! is_array( $field['options'][ $field['default'] ] ) ) {
                                $default_output .= $field['options'][ $field['default'] ] . ", ";
                            } else {
                                $default_output .= serialize( $field['options'][ $field['default'] ] ) . ", ";
                            }
                        }
                    } else if ( ! empty ( $field['options'][ $field['default'] ] ) ) {
                        $default_output .= $field['options'][ $field['default'] ] . ", ";
                    } else if ( ! empty ( $field['default'] ) ) {
                        if ( $field['type'] == 'switch' && isset ( $field['on'] ) && isset ( $field['off'] ) ) {
                            $default_output .= ( $field['default'] == 1 ? $field['on'] : $field['off'] ) . ', ';
                        } else {
                            $default_output .= $field['default'] . ', ';
                        }
                    }
                } else {
                    foreach ( $field['default'] as $defaultk => $defaultv ) {
                        if ( ! empty ( $field['options'][ $defaultv ]['alt'] ) ) {
                            $default_output .= $field['options'][ $defaultv ]['alt'] . ', ';
                        } else if ( ! empty ( $field['options'][ $defaultv ] ) ) {
                            $default_output .= $field['options'][ $defaultv ] . ", ";
                        } else if ( ! empty ( $field['options'][ $defaultk ] ) ) {
                            $default_output .= $field['options'][ $defaultk ] . ", ";
                        } else if ( ! empty ( $defaultv ) ) {
                            $default_output .= $defaultv . ', ';
                        }
                    }
                }

                if ( ! empty ( $default_output ) ) {
                    $default_output = __( 'Default', 'redux-framework' ) . ": " . substr( $default_output, 0, - 2 );
                }

                if ( ! empty ( $default_output ) ) {
                    $default_output = '<span class="showDefaults">' . $default_output . '</span><br class="default_br" />';
                }

                return $default_output;
            }

            // get_default_output_string()

            public function get_header_html( $field ) {
                global $current_user;

                // Set to empty string to avoid wanrings.
                $hint = '';
                $th   = "";

                if ( isset ( $field['title'] ) && isset ( $field['type'] ) && $field['type'] !== "info" && $field['type'] !== "section" ) {
                    $default_mark = ( ! empty ( $field['default'] ) && isset ( $this->options[ $field['id'] ] ) && $this->options[ $field['id'] ] == $field['default'] && ! empty ( $this->args['default_mark'] ) && isset ( $field['default'] ) ) ? $this->args['default_mark'] : '';

                    // If a hint is specified in the field, process it.
                    if ( isset ( $field['hint'] ) && ! '' == $field['hint'] ) {

                        // Set show_hints flag to true, so helptab will be displayed.
                        $this->show_hints = true;

                        $hint = apply_filters( 'redux/hints/html', $hint, $field, $this->args );

                        // Get user pref for displaying hints.
                        $metaVal = get_user_meta( $current_user->ID, 'ignore_hints', true );
                        if ( 'true' == $metaVal || empty ( $metaVal ) && empty( $hint ) ) {

                            // Set hand cursor for clickable hints
                            $pointer = '';
                            if ( isset ( $this->args['hints']['tip_effect']['show']['event'] ) && 'click' == $this->args['hints']['tip_effect']['show']['event'] ) {
                                $pointer = 'pointer';
                            }

                            $size = '16px';
                            if ( 'large' == $this->args['hints']['icon_size'] ) {
                                $size = '18px';
                            }

                            // In case docs are ignored.
                            $titleParam   = isset ( $field['hint']['title'] ) ? $field['hint']['title'] : '';
                            $contentParam = isset ( $field['hint']['content'] ) ? $field['hint']['content'] : '';

                            $hint_color = isset ( $this->args['hints']['icon_color'] ) ? $this->args['hints']['icon_color'] : '#d3d3d3';

                            // Set hint html with appropriate position css
                            $hint = '<div class="redux-hint-qtip" style="float:' . $this->args['hints']['icon_position'] . '; font-size: ' . $size . '; color:' . $hint_color . '; cursor: ' . $pointer . ';" qtip-title="' . $titleParam . '" qtip-content="' . $contentParam . '">&nbsp;<i class="' . ( isset( $this->args['hints']['icon'] ) ? $this->args['hints']['icon'] : '' ) . '"></i></div>';
                        }
                    }

                    if ( ! empty ( $field['title'] ) ) {
                        if ( 'left' == $this->args['hints']['icon_position'] ) {
                            $th = $hint . $field['title'] . $default_mark . "";
                        } else {
                            $th = $field['title'] . $default_mark . "" . $hint;
                        }
                    }

                    if ( isset ( $field['subtitle'] ) ) {
                        $th .= '<span class="description">' . $field['subtitle'] . '</span>';
                    }
                }

                if ( ! empty ( $th ) ) {
                    $th = '<div class="redux_field_th">' . $th . '</div>';
                }

                $filter_arr = array(
                    'editor',
                    'ace_editor',
                    'info',
                    'section',
                    'repeater',
                    'color_scheme',
                    'social_profiles',
                    'css_layout'
                );

                if ( $this->args['default_show'] == true && isset ( $field['default'] ) && isset ( $this->options[ $field['id'] ] ) && $this->options[ $field['id'] ] != $field['default'] && ! in_array( $field['type'], $filter_arr ) ) {
                    $th .= $this->get_default_output_string( $field );
                }

                return $th;
            }

            /**
             * Register Option for use
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            public function _register_settings() {

                // TODO - REMOVE
                // Not used by new sample-config, but in here for legacy builds
                // This is bad and can break things. Hehe.
                if ( ! function_exists( 'wp_get_current_user' ) ) {
                    require_once ABSPATH . "wp-includes/pluggable.php";
                }

                if ( $this->args['options_api'] == true ) {
                    register_setting( $this->args['opt_name'] . '_group', $this->args['opt_name'], array(
                        $this,
                        '_validate_options'
                    ) );
                }


                if ( is_null( $this->sections ) ) {
                    return;
                }

                if ( empty( $this->options_defaults ) ) {
                    $this->options_defaults = $this->_default_values();
                }

                $runUpdate = false;

                foreach ( $this->sections as $k => $section ) {
                    if ( isset ( $section['type'] ) && $section['type'] == 'divide' ) {
                        continue;
                    }

                    $display = true;

                    if ( isset ( $_GET['page'] ) && $_GET['page'] == $this->args['page_slug'] ) {
                        if ( isset ( $section['panel'] ) && $section['panel'] == false ) {
                            $display = false;
                        }
                    }


                    // DOVY! Replace $k with $section['id'] when ready
                    /**
                     * filter 'redux-section-{index}-modifier-{opt_name}'
                     *
                     * @param array $section section configuration
                     */
                    $section = apply_filters( "redux-section-{$k}-modifier-{$this->args['opt_name']}", $section );

                    /**
                     * filter 'redux/options/{opt_name}/section/{section.id}'
                     *
                     * @param array $section section configuration
                     */
                    if ( isset ( $section['id'] ) ) {
                        $section = apply_filters( "redux/options/{$this->args['opt_name']}/section/{$section['id']}", $section );
                    }

                    if ( empty ( $section ) ) {
                        unset ( $this->sections[ $k ] );
                        continue;
                    }

                    if ( ! isset ( $section['title'] ) ) {
                        $section['title'] = "";
                    }

                    if ( isset ( $section['customizer_only'] ) && $section['customizer_only'] == true ) {
                        $section['panel']     = false;
                        $this->sections[ $k ] = $section;
                    }

                    $heading = isset ( $section['heading'] ) ? $section['heading'] : $section['title'];

                    if ( isset ( $section['permissions'] ) ) {
                        if ( ! current_user_can( $section['permissions'] ) ) {
                            $this->hidden_perm_sections[] = $section['title'];

                            foreach ( $section['fields'] as $num => $field_data ) {
                                $field_type = $field_data['type'];

                                if ( $field_type != 'section' || $field_type != 'divide' || $field_type != 'info' || $field_type != 'raw' ) {
                                    $field_id = $field_data['id'];
                                    $default  = isset ( $this->options_defaults[ $field_id ] ) ? $this->options_defaults[ $field_id ] : '';
                                    $data     = isset ( $this->options[ $field_id ] ) ? $this->options[ $field_id ] : $default;

                                    $this->hidden_perm_fields[ $field_id ] = $data;
                                }
                            }

                            continue;
                        }
                    }

                    if ( ! $display || ! function_exists( 'add_settings_section' ) ) {
                        $this->no_panel_section[ $k ] = $section;
                    } else {
                        add_settings_section( $this->args['opt_name'] . $k . '_section', $heading, array(
                            &$this,
                            '_section_desc'
                        ), $this->args['opt_name'] . $k . '_section_group' );
                    }

                    $sectionIndent = false;
                    if ( isset ( $section['fields'] ) ) {
                        foreach ( $section['fields'] as $fieldk => $field ) {
                            if ( ! isset ( $field['type'] ) ) {
                                continue; // You need a type!
                            }

                            if ( $field['type'] == "info" && isset( $field['raw_html'] ) && $field['raw_html'] == true ) {
                                $field['type']                             = "raw";
                                $field['content']                          = $field['desc'];
                                $field['desc']                             = "";
                                $this->sections[ $k ]['fields'][ $fieldk ] = $field;
                            } else if ( $field['type'] == "info" ) {
                                if ( ! isset( $field['full_width'] ) ) {
                                    $field['full_width']                       = true;
                                    $this->sections[ $k ]['fields'][ $fieldk ] = $field;
                                }
                            }

                            if ( $field['type'] == "raw" ) {
                                if ( isset( $field['align'] ) ) {
                                    $field['full_width'] = $field['align'] ? false : true;
                                    unset( $field['align'] );
                                } else if ( ! isset( $field['full_width'] ) ) {
                                    $field['full_width'] = true;
                                }
                                $this->sections[ $k ]['fields'][ $fieldk ] = $field;
                            }


                            /**
                             * filter 'redux/options/{opt_name}/field/{field.id}'
                             *
                             * @param array $field field config
                             */
                            $field = apply_filters( "redux/options/{$this->args['opt_name']}/field/{$field['id']}/register", $field );


                            $this->field_types[ $field['type'] ] = isset ( $this->field_types[ $field['type'] ] ) ? $this->field_types[ $field['type'] ] : array();

                            $this->field_sections[ $field['type'] ][ $field['id'] ] = $k;

                            $display = true;

                            if ( isset ( $_GET['page'] ) && $_GET['page'] == $this->args['page_slug'] ) {
                                if ( isset ( $field['panel'] ) && $field['panel'] == false ) {
                                    $display = false;
                                }
                            }
                            if ( isset ( $field['customizer_only'] ) && $field['customizer_only'] == true ) {
                                $display = false;
                            }

                            if ( isset ( $section['customizer'] ) ) {
                                $field['customizer']                       = $section['customizer'];
                                $this->sections[ $k ]['fields'][ $fieldk ] = $field;
                            }

                            if ( isset ( $field['permissions'] ) ) {

                                if ( ! current_user_can( $field['permissions'] ) ) {
                                    $data = isset ( $this->options[ $field['id'] ] ) ? $this->options[ $field['id'] ] : $this->options_defaults[ $field['id'] ];

                                    $this->hidden_perm_fields[ $field['id'] ] = $data;

                                    continue;
                                }
                            }

                            if ( ! isset ( $field['id'] ) ) {
                                echo '<br /><h3>No field ID is set.</h3><pre>';
                                print_r( $field );
                                echo "</pre><br />";
                                continue;
                            }

                            if ( isset ( $field['type'] ) && $field['type'] == "section" ) {
                                if ( isset ( $field['indent'] ) && $field['indent'] == true ) {
                                    $sectionIndent = true;
                                } else {
                                    $sectionIndent = false;
                                }
                            }

                            if ( isset ( $field['type'] ) && $field['type'] == "info" && $sectionIndent ) {
                                $field['indent'] = $sectionIndent;
                            }

                            $th = $this->get_header_html( $field );

                            $field['name'] = $this->args['opt_name'] . '[' . $field['id'] . ']';

                            // Set the default value if present
                            $this->options_defaults[ $field['id'] ] = isset ( $this->options_defaults[ $field['id'] ] ) ? $this->options_defaults[ $field['id'] ] : '';

                            // Set the defaults to the value if not present
                            $doUpdate = false;

                            // Check fields for values in the default parameter
                            if ( ! isset ( $this->options[ $field['id'] ] ) && isset ( $field['default'] ) ) {
                                $this->options_defaults[ $field['id'] ] = $this->options[ $field['id'] ] = $field['default'];
                                $doUpdate                               = true;

                                // Check fields that hae no default value, but an options value with settings to
                                // be saved by default
                            } elseif ( ! isset ( $this->options[ $field['id'] ] ) && isset ( $field['options'] ) ) {

                                // If sorter field, check for options as save them as defaults
                                if ( $field['type'] == 'sorter' || $field['type'] == 'sortable' ) {
                                    $this->options_defaults[ $field['id'] ] = $this->options[ $field['id'] ] = $field['options'];
                                    $doUpdate                               = true;
                                }
                            }

                            // CORRECT URLS if media URLs are wrong, but attachment IDs are present.
                            if ( $field['type'] == "media" ) {
                                if ( isset ( $this->options[ $field['id'] ]['id'] ) && isset ( $this->options[ $field['id'] ]['url'] ) && ! empty ( $this->options[ $field['id'] ]['url'] ) && strpos( $this->options[ $field['id'] ]['url'], str_replace( 'http://', '', WP_CONTENT_URL ) ) === false ) {
                                    $data = wp_get_attachment_url( $this->options[ $field['id'] ]['id'] );

                                    if ( isset ( $data ) && ! empty ( $data ) ) {
                                        $this->options[ $field['id'] ]['url']       = $data;
                                        $data                                       = wp_get_attachment_image_src( $this->options[ $field['id'] ]['id'], array(
                                            150,
                                            150
                                        ) );
                                        $this->options[ $field['id'] ]['thumbnail'] = $data[0];
                                        $doUpdate                                   = true;
                                    }
                                }
                            }

                            if ( $field['type'] == "background" ) {
                                if ( isset ( $this->options[ $field['id'] ]['media']['id'] ) && isset ( $this->options[ $field['id'] ]['background-image'] ) && ! empty ( $this->options[ $field['id'] ]['background-image'] ) && strpos( $this->options[ $field['id'] ]['background-image'], str_replace( 'http://', '', WP_CONTENT_URL ) ) === false ) {
                                    $data = wp_get_attachment_url( $this->options[ $field['id'] ]['media']['id'] );

                                    if ( isset ( $data ) && ! empty ( $data ) ) {
                                        $this->options[ $field['id'] ]['background-image']   = $data;
                                        $data                                                = wp_get_attachment_image_src( $this->options[ $field['id'] ]['media']['id'], array(
                                            150,
                                            150
                                        ) );
                                        $this->options[ $field['id'] ]['media']['thumbnail'] = $data[0];
                                        $doUpdate                                            = true;
                                    }
                                }
                            }

                            if ( $field['type'] == "slides" ) {
                                if ( isset ( $this->options[ $field['id'] ] ) && is_array( $this->options[ $field['id'] ] ) && isset ( $this->options[ $field['id'] ][0]['attachment_id'] ) && isset ( $this->options[ $field['id'] ][0]['image'] ) && ! empty ( $this->options[ $field['id'] ][0]['image'] ) && strpos( $this->options[ $field['id'] ][0]['image'], str_replace( 'http://', '', WP_CONTENT_URL ) ) === false ) {
                                    foreach ( $this->options[ $field['id'] ] as $key => $val ) {
                                        $data = wp_get_attachment_url( $val['attachment_id'] );

                                        if ( isset ( $data ) && ! empty ( $data ) ) {
                                            $this->options[ $field['id'] ][ $key ]['image'] = $data;
                                            $data                                           = wp_get_attachment_image_src( $val['attachment_id'], array(
                                                150,
                                                150
                                            ) );
                                            $this->options[ $field['id'] ][ $key ]['thumb'] = $data[0];
                                            $doUpdate                                       = true;
                                        }
                                    }
                                }
                            }
                            // END -> CORRECT URLS if media URLs are wrong, but attachment IDs are present.

                            if ( true == $doUpdate && ! isset ( $this->never_save_to_db ) ) {
                                if ( $this->args['save_defaults'] ) { // Only save that to the DB if allowed to
                                    $runUpdate = true;
                                }
                                // elseif($this->saved != '' && $this->saved != false) {
                                // $runUpdate = true;
                                //}
                            }

                            if ( ! isset ( $field['class'] ) ) { // No errors please
                                $field['class'] = "";
                            }
                            $id = $field['id'];

                            /**
                             * filter 'redux-field-{field.id}modifier-{opt_name}'
                             *
                             * @deprecated
                             *
                             * @param array $field field config
                             */
                            $field = apply_filters( "redux-field-{$field['id']}modifier-{$this->args['opt_name']}", $field ); // REMOVE LATER

                            /**
                             * filter 'redux/options/{opt_name}/field/{field.id}'
                             *
                             * @param array $field field config
                             */
                            $field = apply_filters( "redux/options/{$this->args['opt_name']}/field/{$field['id']}", $field );

                            if ( empty ( $field ) || ! $field || $field == false ) {
                                unset ( $this->sections[ $k ]['fields'][ $fieldk ] );
                                continue;
                            }

                            if ( ! empty ( $this->folds[ $field['id'] ]['parent'] ) ) { // This has some fold items, hide it by default
                                $field['class'] .= " fold";
                            }

                            if ( ! empty ( $this->folds[ $field['id'] ]['children'] ) ) { // Sets the values you shoe fold children on
                                $field['class'] .= " foldParent";
                            }

                            if ( ! empty ( $field['compiler'] ) ) {
                                $field['class'] .= " compiler";
                                $this->compiler_fields[ $field['id'] ] = 1;
                            }

                            if ( isset ( $field['unit'] ) && ! isset ( $field['units'] ) ) {
                                $field['units'] = $field['unit'];
                                unset ( $field['unit'] );
                            }

                            $this->sections[ $k ]['fields'][ $fieldk ] = $field;

                            if ( isset ( $this->args['display_source'] ) ) {
                                $th .= '<div id="' . $field['id'] . '-settings" style="display:none;"><pre>' . var_export( $this->sections[ $k ]['fields'][ $fieldk ], true ) . '</pre></div>';
                                $th .= '<br /><a href="#TB_inline?width=600&height=800&inlineId=' . $field['id'] . '-settings" class="thickbox"><small>View Source</small></a>';
                            }

                            /**
                             * action 'redux/options/{opt_name}/field/field.type}/register'
                             */
                            do_action( "redux/options/{$this->args['opt_name']}/field/{$field['type']}/register", $field );

                            $this->check_dependencies( $field );
                            $this->field_head[ $field['id'] ] = $th;

                            if ( ! $display || isset ( $this->no_panel_section[ $k ] ) ) {
                                $this->no_panel[] = $field['id'];
                            } else {
                                if ( isset ( $field['hidden'] ) && $field['hidden'] ) {
                                    $field['label_for'] = 'redux_hide_field';
                                }
                                if ( $this->args['options_api'] == true ) {
                                    add_settings_field(
                                        "{$fieldk}_field", $th, array(
                                        &$this,
                                        '_field_input'
                                    ), "{$this->args['opt_name']}{$k}_section_group", "{$this->args['opt_name']}{$k}_section", $field
                                    );
                                }
                            }
                        }
                    }
                }

                /**
                 * action 'redux-register-settings-{opt_name}'
                 *
                 * @deprecated
                 */
                do_action( "redux-register-settings-{$this->args['opt_name']}" ); // REMOVE

                /**
                 * action 'redux/options/{opt_name}/register'
                 *
                 * @param array option sections
                 */
                do_action( "redux/options/{$this->args['opt_name']}/register", $this->sections );

                if ( $runUpdate && ! isset ( $this->never_save_to_db ) ) { // Always update the DB with new fields
                    $this->set_options( $this->options );
                }

                if ( isset ( $this->transients['run_compiler'] ) && $this->transients['run_compiler'] ) {

                    $this->no_output = true;
                    $this->_enqueue_output();


                    /**
                     * action 'redux-compiler-{opt_name}'
                     *
                     * @deprecated
                     *
                     * @param array  options
                     * @param string CSS that get sent to the compiler hook
                     */
                    do_action( "redux-compiler-{$this->args['opt_name']}", $this->options, $this->compilerCSS, $this->transients['changed_values'] ); // REMOVE

                    /**
                     * action 'redux/options/{opt_name}a'
                     *
                     * @param array  options
                     * @param string CSS that get sent to the compiler hook
                     */
                    do_action( "redux/options/{$this->args['opt_name']}/compiler", $this->options, $this->compilerCSS, $this->transients['changed_values'] );

                    /**
                     * action 'redux/options/{opt_name}/compiler/advanced'
                     *
                     * @param array  options
                     * @param string CSS that get sent to the compiler hook, which sends the full Redux object
                     */
                    do_action( "redux/options/{$this->args['opt_name']}/compiler/advanced", $this );

                    unset ( $this->transients['run_compiler'] );
                    $this->set_transients();
                }
            }
            // _register_settings()

            /**
             * Register Extensions for use
             *
             * @since       3.0.0
             * @access      public
             * @return      void
             */
            private function _register_extensions() {
                $path    = dirname( __FILE__ ) . '/inc/extensions/';
                $folders = scandir( $path, 1 );

                /**
                 * action 'redux/extensions/before'
                 *
                 * @param object $this ReduxFramework
                 */
                do_action( "redux/extensions/before", $this );

                /**
                 * action 'redux/extensions/{opt_name}/before'
                 *
                 * @param object $this ReduxFramework
                 */
                do_action( "redux/extensions/{$this->args['opt_name']}/before", $this );

                if ( isset( $this->old_opt_name ) ) {
                    do_action( "redux/extensions/{$this->old_opt_name}/before", $this );
                }

                foreach ( $folders as $folder ) {
                    if ( $folder === '.' || $folder === '..' || ! is_dir( $path . $folder ) || substr( $folder, 0, 1 ) === '.' || substr( $folder, 0, 1 ) === '@' || substr( $folder, 0, 4 ) === '_vti' ) {
                        continue;
                    }

                    $extension_class = 'ReduxFramework_Extension_' . $folder;

                    /**
                     * filter 'redux-extensionclass-load'
                     *
                     * @deprecated
                     *
                     * @param        string                    extension class file path
                     * @param string $extension_class          extension class name
                     */
                    $class_file = apply_filters( "redux-extensionclass-load", "$path/$folder/extension_{$folder}.php", $extension_class ); // REMOVE LATER

                    /**
                     * filter 'redux/extension/{opt_name}/{folder}'
                     *
                     * @param        string                    extension class file path
                     * @param string $extension_class          extension class name
                     */
                    $class_file = apply_filters( "redux/extension/{$this->args['opt_name']}/$folder", "$path/$folder/extension_{$folder}.php", $class_file );

                    if ( $class_file ) {

                        if ( file_exists( $class_file ) ) {
                            require_once $class_file;

                            $this->extensions[ $folder ] = new $extension_class ( $this );
                        }
                    }
                }

                /**
                 * action 'redux-register-extensions-{opt_name}'
                 *
                 * @deprecated
                 *
                 * @param object $this ReduxFramework
                 */
                do_action( "redux-register-extensions-{$this->args['opt_name']}", $this ); // REMOVE

                /**
                 * action 'redux/extensions/{opt_name}'
                 *
                 * @param object $this ReduxFramework
                 */
                do_action( "redux/extensions/{$this->args['opt_name']}", $this );

                if ( isset( $this->old_opt_name ) && ! empty( $this->old_opt_name ) ) {
                    do_action( "redux/extensions/{$this->old_opt_name}", $this );
                }
            }

            private function get_transients() {
                if ( ! isset ( $this->transients ) ) {
                    $this->transients       = get_option( $this->args['opt_name'] . '-transients', array() );
                    $this->transients_check = $this->transients;
                }
            }

            public function set_transients() {
                if ( ! isset ( $this->transients ) || ! isset ( $this->transients_check ) || $this->transients != $this->transients_check ) {
                    update_option( $this->args['opt_name'] . '-transients', $this->transients );
                    $this->transients_check = $this->transients;
                }
            }

            /**
             * Validate the Options options before insertion
             *
             * @since       3.0.0
             * @access      public
             *
             * @param       array $plugin_options The options array
             *
             * @return array|mixed|string|void
             */
            public function _validate_options( $plugin_options ) {
                //print_r($plugin_options);
                //              exit();
                if ( isset ( $this->validation_ran ) ) {
                    return $plugin_options;
                }
                $this->validation_ran = 1;

                // Save the values not in the panel
                if ( isset ( $plugin_options['redux-no_panel'] ) ) {
                    $keys = explode( '|', $plugin_options['redux-no_panel'] );
                    foreach ( $keys as $key ) {
                        $plugin_options[ $key ] = $this->options[ $key ];
                    }
                    if ( isset ( $plugin_options['redux-no_panel'] ) ) {
                        unset ( $plugin_options['redux-no_panel'] );
                    }
                }

                if ( ! empty ( $this->hidden_perm_fields ) && is_array( $this->hidden_perm_fields ) ) {
                    foreach ( $this->hidden_perm_fields as $id => $data ) {
                        $plugin_options[ $id ] = $data;
                    }
                }

                if ( $plugin_options == $this->options ) {
                    return $plugin_options;
                }

                $time = time();

                // Sets last saved time
                $this->transients['last_save'] = $time;

                // Import
                if ( ( isset( $plugin_options['import_code'] ) && ! empty( $plugin_options['import_code'] ) ) || ( isset( $plugin_options['import_link'] ) && ! empty( $plugin_options['import_link'] ) ) ) {
                    $this->transients['last_save_mode'] = "import"; // Last save mode
                    $this->transients['last_compiler']  = $time;
                    $this->transients['last_import']    = $time;
                    $this->transients['run_compiler']   = 1;

                    if ( $plugin_options['import_code'] != '' ) {
                        $import = $plugin_options['import_code'];
                    } elseif ( $plugin_options['import_link'] != '' ) {
                        $import = wp_remote_retrieve_body( wp_remote_get( $plugin_options['import_link'] ) );
                    }

                    if ( ! empty ( $import ) ) {
                        $imported_options = json_decode( $import, true );
                    }

                    if ( ! empty ( $imported_options ) && is_array( $imported_options ) && isset ( $imported_options['redux-backup'] ) && $imported_options['redux-backup'] == '1' ) {

                        $this->transients['changed_values'] = array();
                        foreach ( $plugin_options as $key => $value ) {
                            if ( isset ( $imported_options[ $key ] ) && $imported_options[ $key ] != $value ) {
                                $this->transients['changed_values'][ $key ] = $value;
                                $plugin_options[ $key ]                     = $value;
                            }
                        }

                        /**
                         * action 'redux/options/{opt_name}/import'
                         *
                         * @param  &array [&$plugin_options, redux_options]
                         */
                        do_action_ref_array( "redux/options/{$this->args['opt_name']}/import", array(
                            &$plugin_options,
                            $imported_options,
                            $this->transients['changed_values']
                        ) );

                        setcookie( 'redux_current_tab', '', 1, '/', $time + 1000, "/" );
                        $_COOKIE['redux_current_tab'] = 1;

                        unset ( $plugin_options['defaults'], $plugin_options['compiler'], $plugin_options['import'], $plugin_options['import_code'] );
                        if ( $this->args['database'] == 'transient' || $this->args['database'] == 'theme_mods' || $this->args['database'] == 'theme_mods_expanded' || $this->args['database'] == 'network' ) {
                            $this->set_options( $plugin_options );

                            return;
                        }

                        $plugin_options = wp_parse_args( $imported_options, $plugin_options );

                        $this->set_transients(); // Update the transients

                        return $plugin_options;
                    }
                }

                // Reset all to defaults
                if ( ! empty ( $plugin_options['defaults'] ) ) {
                    if ( empty ( $this->options_defaults ) ) {
                        $this->options_defaults = $this->_default_values();
                    }

                    /**
                     * apply_filters 'redux/validate/{opt_name}/defaults'
                     *
                     * @param  &array [ $this->options_defaults, $plugin_options]
                     */
                    $plugin_options = apply_filters( "redux/validate/{$this->args['opt_name']}/defaults", $this->options_defaults );

                    $this->transients['changed_values'] = array();

                    if ( empty ( $this->options ) ) {
                        $this->options = $this->options_defaults;
                    }

                    foreach ( $this->options as $key => $value ) {
                        if ( isset ( $plugin_options[ $key ] ) && $value != $plugin_options[ $key ] ) {
                            $this->transients['changed_values'][ $key ] = $value;
                        }
                    }

                    $this->transients['run_compiler']   = 1;
                    $this->transients['last_save_mode'] = "defaults"; // Last save mode
                    //setcookie('redux-compiler-' . $this->args['opt_name'], 1, time() + 1000, "/");
                    //setcookie("redux-saved-{$this->args['opt_name']}", 'defaults', time() + 1000, "/");

                    $this->set_transients(); // Update the transients

                    return $plugin_options;
                }

                // Section reset to defaults
                if ( ! empty ( $plugin_options['defaults-section'] ) ) {
                    if ( isset ( $plugin_options['redux-section'] ) && isset ( $this->sections[ $plugin_options['redux-section'] ]['fields'] ) ) {
                        /**
                         * apply_filters 'redux/validate/{opt_name}/defaults_section'
                         *
                         * @param  &array [ $this->options_defaults, $plugin_options]
                         */
                        foreach ( $this->sections[ $plugin_options['redux-section'] ]['fields'] as $field ) {
                            if ( isset ( $this->options_defaults[ $field['id'] ] ) ) {
                                $plugin_options[ $field['id'] ] = $this->options_defaults[ $field['id'] ];
                            } else {
                                $plugin_options[ $field['id'] ] = "";
                            }

                            if ( isset ( $field['compiler'] ) ) {
                                $compiler = true;
                            }
                        }

                        $plugin_options = apply_filters( "redux/validate/{$this->args['opt_name']}/defaults_section", $plugin_options );
                    }

                    $this->transients['changed_values'] = array();
                    foreach ( $this->options as $key => $value ) {
                        if ( isset ( $plugin_options[ $key ] ) && $value != $plugin_options[ $key ] ) {
                            $this->transients['changed_values'][ $key ] = $value;
                        }
                    }

                    if ( isset ( $compiler ) ) {
                        //$this->run_compiler = true;
                        //setcookie('redux-compiler-' . $this->args['opt_name'], 1, time()+1000, '/');
                        //$plugin_options['REDUX_COMPILER'] = time();
                        $this->transients['last_compiler'] = $time;
                        $this->transients['run_compiler']  = 1;
                    }

                    $this->transients['last_save_mode'] = "defaults_section"; // Last save mode
                    //setcookie("redux-saved-{$this->args['opt_name']}", 'defaults_section', time() + 1000, "/");
                    unset ( $plugin_options['defaults'], $plugin_options['defaults_section'], $plugin_options['import'], $plugin_options['import_code'], $plugin_options['import_link'], $plugin_options['compiler'], $plugin_options['redux-section'] );

                    $this->set_transients();

                    return $plugin_options;
                }

                //                if ($this->transients['last_save_mode'] != 'remove') {
                $this->transients['last_save_mode'] = "normal"; // Last save mode
                //               } else {
                //                    $this->transients['last_save_mode'] = '';
                //                }

                /**
                 * apply_filters 'redux/validate/{opt_name}/before_validation'
                 *
                 * @param  &array [&$plugin_options, redux_options]
                 */
                $plugin_options = apply_filters( "redux/validate/{$this->args['opt_name']}/before_validation", $plugin_options, $this->options );

                // Validate fields (if needed)
                $plugin_options = $this->_validate_values( $plugin_options, $this->options, $this->sections );

                if ( ! empty ( $this->errors ) || ! empty ( $this->warnings ) ) {
                    $this->transients['notices'] = array( 'errors' => $this->errors, 'warnings' => $this->warnings );
                }

                /**
                 * action 'redux-validate-{opt_name}'
                 *
                 * @deprecated
                 *
                 * @param  &array [&$plugin_options, redux_options]
                 */
                do_action_ref_array( "redux-validate-{$this->args['opt_name']}", array(
                    &$plugin_options,
                    $this->options
                ) ); // REMOVE

                if ( ! isset ( $this->transients['changed_values'] ) ) {
                    $this->transients['changed_values'] = array();
                }

                /**
                 * action 'redux/options/{opt_name}/validate'
                 *
                 * @param  &array [&$plugin_options, redux_options]
                 */
                do_action_ref_array( "redux/options/{$this->args['opt_name']}/validate", array(
                    &$plugin_options,
                    $this->options,
                    $this->transients['changed_values']
                ) );

                if ( ! empty ( $plugin_options['compiler'] ) ) {
                    unset ( $plugin_options['compiler'] );

                    $this->transients['last_compiler'] = $time;
                    $this->transients['run_compiler']  = 1;
                }

                $this->transients['changed_values'] = array(); // Changed values since last save
                foreach ( $this->options as $key => $value ) {
                    if ( isset ( $plugin_options[ $key ] ) && $value != $plugin_options[ $key ] ) {
                        $this->transients['changed_values'][ $key ] = $value;
                    }
                }

                unset ( $plugin_options['defaults'], $plugin_options['defaults_section'], $plugin_options['import'], $plugin_options['import_code'], $plugin_options['import_link'], $plugin_options['compiler'], $plugin_options['redux-section'] );
                if ( $this->args['database'] == 'transient' || $this->args['database'] == 'theme_mods' || $this->args['database'] == 'theme_mods_expanded' ) {
                    $this->set_options( $plugin_options );

                    return;
                }

                if ( defined( 'WP_CACHE' ) && WP_CACHE && class_exists( 'W3_ObjectCache' ) && function_exists( 'w3_instance' ) ) {
                    //echo "here";
                    $w3_inst = w3_instance( 'W3_ObjectCache' );
                    $w3      = $w3_inst->instance();
                    $key     = $w3->_get_cache_key( $this->args['opt_name'] . '-transients', 'transient' );
                    //echo $key;
                    $w3->delete( $key, 'transient', true );
                    //set_transient($this->args['opt_name'].'-transients', $this->transients);
                    //exit();
                }

                $this->set_transients( $this->transients );

                return $plugin_options;
            }

            public function ajax_save() {
                if ( ! wp_verify_nonce( $_REQUEST['nonce'], "redux_ajax_nonce" . $this->args['opt_name'] ) ) {
                    echo json_encode( array(
                        'status' => __( 'Invalid security credential.  Please reload the page and try again.', 'redux-framework' ),
                        'action' => ''
                    ) );

                    die();
                }

                if ( ! current_user_can( $this->args['page_permissions'] ) ) {
                    echo json_encode( array(
                        'status' => __( 'Invalid user capability.  Please reload the page and try again.', 'redux-framework' ),
                        'action' => ''
                    ) );

                    die();
                }

                $redux = ReduxFrameworkInstances::get_instance( $_POST['opt_name'] );

                if ( ! empty ( $_POST['data'] ) && ! empty ( $redux->args['opt_name'] ) ) {

                    $values = array();
                    //if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
                    //    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
                    //    while (list($key, $val) = each($process)) {
                    //        foreach ($val as $k => $v) {
                    //            unset($process[$key][$k]);
                    //            if (is_array($v)) {
                    //                $process[$key][stripslashes($k)] = $v;
                    //                $process[] = &$process[$key][stripslashes($k)];
                    //            } else {
                    //                $process[$key][stripslashes($k)] = stripslashes($v);
                    //            }
                    //        }
                    //    }
                    //    unset($process);
                    //}
                    $_POST['data'] = stripslashes( $_POST['data'] );

                    // Old method of saving, in case we need to go back! - kp
                    //parse_str( $_POST['data'], $values );

                    // New method to avoid input_var nonesense.  Thanks @harunbasic
                    $values = $this->redux_parse_str( $_POST['data'] );

                    $values = $values[ $redux->args['opt_name'] ];

                    if ( function_exists( 'get_magic_quotes_gpc' ) && get_magic_quotes_gpc() ) {
                        $values = array_map( 'stripslashes_deep', $values );
                    }

                    if ( ! empty ( $values ) ) {

                        try {
                            if ( isset ( $redux->validation_ran ) ) {
                                unset ( $redux->validation_ran );
                            }
                            $redux->set_options( $redux->_validate_options( $values ) );

                            $do_reload = false;
                            if ( isset( $this->reload_fields ) && ! empty( $this->reload_fields ) ) {
                                if ( ! empty( $this->transients['changed_values'] ) ) {
                                    foreach ( $this->reload_fields as $idx => $val ) {
                                        if ( array_key_exists( $val, $this->transients['changed_values'] ) ) {
                                            $do_reload = true;
                                        }
                                    }
                                }
                            }

                            if ( $do_reload || ( isset ( $values['defaults'] ) && ! empty ( $values['defaults'] ) ) || ( isset ( $values['defaults-section'] ) && ! empty ( $values['defaults-section'] ) ) ) {
                                echo json_encode( array( 'status' => 'success', 'action' => 'reload' ) );
                                die ();
                            }

                            require_once 'core/enqueue.php';
                            $enqueue = new reduxCoreEnqueue ( $redux );
                            $enqueue->get_warnings_and_errors_array();

                            $return_array = array(
                                'status'   => 'success',
                                'options'  => $redux->options,
                                'errors'   => isset ( $redux->localize_data['errors'] ) ? $redux->localize_data['errors'] : null,
                                'warnings' => isset ( $redux->localize_data['warnings'] ) ? $redux->localize_data['warnings'] : null,
                            );

                        } catch ( Exception $e ) {
                            $return_array = array( 'status' => $e->getMessage() );
                        }
                    } else {
                        echo json_encode( array( 'status' => __( 'Your panel has no fields. Nothing to save.', 'redux-framework' ) ) );
                    }
                }
                if ( isset ( $this->transients['run_compiler'] ) && $this->transients['run_compiler'] ) {

                    $this->no_output = true;
                    $this->_enqueue_output();

                    try {
                        /**
                         * action 'redux-compiler-{opt_name}'
                         *
                         * @deprecated
                         *
                         * @param array  options
                         * @param string CSS that get sent to the compiler hook
                         */
                        do_action( "redux-compiler-{$this->args['opt_name']}", $this->options, $this->compilerCSS, $this->transients['changed_values'] ); // REMOVE

                        /**
                         * action 'redux/options/{opt_name}/compiler'
                         *
                         * @param array  options
                         * @param string CSS that get sent to the compiler hook
                         */
                        do_action( "redux/options/{$this->args['opt_name']}/compiler", $this->options, $this->compilerCSS, $this->transients['changed_values'] );

                        /**
                         * action 'redux/options/{opt_name}/compiler/advanced'
                         *
                         * @param array  options
                         * @param string CSS that get sent to the compiler hook, which sends the full Redux object
                         */
                        do_action( "redux/options/{$this->args['opt_name']}/compiler/advanced", $this );
                    } catch ( Exception $e ) {
                        $return_array = array( 'status' => $e->getMessage() );
                    }

                    unset ( $this->transients['run_compiler'] );
                    $this->set_transients();
                }
                if ( isset( $return_array ) ) {
                    if ( $return_array['status'] == "success" ) {
                        require_once 'core/panel.php';
                        $panel = new reduxCorePanel ( $redux );
                        ob_start();
                        $panel->notification_bar();
                        $notification_bar = ob_get_contents();
                        ob_end_clean();
                        $return_array['notification_bar'] = $notification_bar;
                    }

                    echo json_encode( apply_filters( "redux/options/{$this->args['opt_name']}/ajax_save/response", $return_array ) );
                }

                die ();

            }

            /**
             * Validate values from options form (used in settings api validate function)
             * calls the custom validation class for the field so authors can override with custom classes
             *
             * @since       1.0.0
             * @access      public
             *
             * @param       array $plugin_options
             * @param       array $options
             *
             * @return      array $plugin_options
             */
            public function _validate_values( $plugin_options, $options, $sections ) {
                foreach ( $sections as $k => $section ) {
                    if ( isset ( $section['fields'] ) ) {
                        foreach ( $section['fields'] as $fkey => $field ) {

                            if ( is_array( $field ) ) {
                                $field['section_id'] = $k;
                            }

                            if ( isset ( $field['type'] ) && ( $field['type'] == 'checkbox' || $field['type'] == 'checkbox_hide_below' || $field['type'] == 'checkbox_hide_all' ) ) {
                                if ( ! isset ( $plugin_options[ $field['id'] ] ) ) {
                                    $plugin_options[ $field['id'] ] = 0;
                                }
                            }

//                            if ( isset ( $field['type'] ) && $field['type'] == 'typography' ) {
//                                if ( ! is_array( $plugin_options[ $field['id'] ] ) && ! empty( $plugin_options[ $field['id'] ] ) ) {
//                                    $plugin_options[ $field['id'] ] = json_decode( $plugin_options[ $field['id'] ], true );
//                                }
//                            }

                            if ( isset( $this->extensions[ $field['type'] ] ) && method_exists( $this->extensions[ $field['type'] ], '_validate_values' ) ) {
                                $plugin_options = $this->extensions[ $field['type'] ]->_validate_values( $plugin_options, $field );
                            }

                            // Default 'not_empty 'flag to false.
                            $isNotEmpty = false;

                            // Make sure 'validate' field is set.
                            if ( isset ( $field['validate'] ) ) {

                                // Make sure 'validate field' is set to 'not_empty' or 'email_not_empty'
                                //if ( $field['validate'] == 'not_empty' || $field['validate'] == 'email_not_empty' || $field['validate'] == 'numeric_not_empty' ) {
                                if ( strtolower( substr( $field['validate'], - 9 ) ) == 'not_empty' ) {

                                    // Set the flag.
                                    $isNotEmpty = true;
                                }
                            }

                            // Check for empty id value

                            if ( ! isset ( $field['id'] ) || ! isset ( $plugin_options[ $field['id'] ] ) || ( isset ( $plugin_options[ $field['id'] ] ) && $plugin_options[ $field['id'] ] == '' ) ) {

                                // If we are looking for an empty value, in the case of 'not_empty'
                                // then we need to keep processing.
                                if ( ! $isNotEmpty ) {

                                    // Empty id and not checking for 'not_empty.  Bail out...
                                    if (!isset($field['validate_callback'])) {
                                        continue;
                                    }
                                    //continue;
                                }
                            }

                            // Force validate of custom field types
                            if ( isset ( $field['type'] ) && ! isset ( $field['validate'] ) && ! isset( $field['validate_callback'] ) ) {
                                if ( $field['type'] == 'color' || $field['type'] == 'color_gradient' ) {
                                    $field['validate'] = 'color';
                                } elseif ( $field['type'] == 'date' ) {
                                    $field['validate'] = 'date';
                                }
                            }

                            if ( isset ( $field['validate'] ) ) {
                                $validate = 'Redux_Validation_' . $field['validate'];

                                if ( ! class_exists( $validate ) ) {
                                    /**
                                     * filter 'redux-validateclass-load'
                                     *
                                     * @deprecated
                                     *
                                     * @param        string             validation class file path
                                     * @param string $validate          validation class name
                                     */
                                    $class_file = apply_filters( "redux-validateclass-load", self::$_dir . "inc/validation/{$field['validate']}/validation_{$field['validate']}.php", $validate ); // REMOVE LATER

                                    /**
                                     * filter 'redux/validate/{opt_name}/class/{field.validate}'
                                     *
                                     * @param        string                validation class file path
                                     * @param string $class_file           validation class file path
                                     */
                                    $class_file = apply_filters( "redux/validate/{$this->args['opt_name']}/class/{$field['validate']}", self::$_dir . "inc/validation/{$field['validate']}/validation_{$field['validate']}.php", $class_file );

                                    if ( $class_file ) {
                                        if ( file_exists( $class_file ) ) {
                                            require_once $class_file;
                                        }
                                    }
                                }

                                if ( class_exists( $validate ) ) {

                                    //!DOVY - DB saving stuff. Is this right?
                                    if ( empty ( $options[ $field['id'] ] ) ) {
                                        $options[ $field['id'] ] = '';
                                    }

                                    if ( isset ( $plugin_options[ $field['id'] ] ) && is_array( $plugin_options[ $field['id'] ] ) && ! empty ( $plugin_options[ $field['id'] ] ) ) {
                                        foreach ( $plugin_options[ $field['id'] ] as $key => $value ) {
                                            $before = $after = null;
                                            if ( isset ( $plugin_options[ $field['id'] ][ $key ] ) && ( ! empty ( $plugin_options[ $field['id'] ][ $key ] ) || $plugin_options[ $field['id'] ][ $key ] == '0' ) ) {
                                                if ( is_array( $plugin_options[ $field['id'] ][ $key ] ) ) {
                                                    $before = $plugin_options[ $field['id'] ][ $key ];
                                                } else {
                                                    $before = trim( $plugin_options[ $field['id'] ][ $key ] );
                                                }
                                            }

                                            if ( isset ( $options[ $field['id'] ][ $key ] ) && ( ! empty ( $plugin_options[ $field['id'] ][ $key ] ) || $plugin_options[ $field['id'] ][ $key ] == '0' ) ) {
                                                $after = $options[ $field['id'] ][ $key ];
                                            }

                                            $validation = new $validate ( $this, $field, $before, $after );
                                            if ( ! empty ( $validation->value ) || $validation->value == '0' ) {
                                                $plugin_options[ $field['id'] ][ $key ] = $validation->value;
                                            } else {
                                                unset ( $plugin_options[ $field['id'] ][ $key ] );
                                            }

                                            if ( isset ( $validation->error ) ) {
                                                $this->errors[] = $validation->error;
                                            }

                                            if ( isset ( $validation->warning ) ) {
                                                $this->warnings[] = $validation->warning;
                                            }
                                        }
                                    } else {
                                        if ( isset( $plugin_options[ $field['id'] ] ) ) {
                                            if ( is_array( $plugin_options[ $field['id'] ] ) ) {
                                                $pofi = $plugin_options[ $field['id'] ];
                                            } else {
                                                $pofi = trim( $plugin_options[ $field['id'] ] );
                                            }
                                        } else {
                                            $pofi = null;
                                        }

                                        $validation                     = new $validate ( $this, $field, $pofi, $options[ $field['id'] ] );
                                        $plugin_options[ $field['id'] ] = $validation->value;

                                        if ( isset ( $validation->error ) ) {
                                            $this->errors[] = $validation->error;
                                        }

                                        if ( isset ( $validation->warning ) ) {
                                            $this->warnings[] = $validation->warning;
                                        }
                                    }

                                    continue;
                                }
                            }
                            if ( isset ( $field['validate_callback'] ) && ( is_callable( $field['validate_callback'] ) || ( is_string( $field['validate_callback'] ) && function_exists( $field['validate_callback'] ) ) ) ) {
                                $callback = $field['validate_callback'];
                                unset ( $field['validate_callback'] );

                                $callbackvalues                 = call_user_func( $callback, $field, $plugin_options[ $field['id'] ], $options[ $field['id'] ] );
                                $plugin_options[ $field['id'] ] = $callbackvalues['value'];

                                if ( isset ( $callbackvalues['error'] ) ) {
                                    $this->errors[] = $callbackvalues['error'];
                                }
                                // TODO - This warning message is failing. Hmm.
                                // No it isn't.  Problem was in the sample-config - kp
                                if ( isset ( $callbackvalues['warning'] ) ) {
                                    $this->warnings[] = $callbackvalues['warning'];
                                }
                            }
                        }
                    }
                }

                return $plugin_options;
            }

            /**
             * Return Section Menu HTML
             *
             * @since       3.1.5
             * @access      public
             * @return      void
             */
            public function section_menu( $k, $section, $suffix = "", $sections = array() ) {
                $display = true;

                $section['class'] = isset ( $section['class'] ) ? ' ' . $section['class'] : '';

                if ( isset ( $_GET['page'] ) && $_GET['page'] == $this->args['page_slug'] ) {
                    if ( isset ( $section['panel'] ) && $section['panel'] == false ) {
                        $display = false;
                    }
                }

                if ( ! $display ) {
                    return "";
                }

                if ( empty ( $sections ) ) {
                    $sections = $this->sections;
                }

                $string = "";
                if ( ( ( isset ( $this->args['icon_type'] ) && $this->args['icon_type'] == 'image' ) || ( isset ( $section['icon_type'] ) && $section['icon_type'] == 'image' ) ) || ( isset( $section['icon'] ) && strpos( $section['icon'], '/' ) !== false ) ) {
                    //if( !empty( $this->args['icon_type'] ) && $this->args['icon_type'] == 'image' ) {
                    $icon = ( ! isset ( $section['icon'] ) ) ? '' : '<img class="image_icon_type" src="' . esc_url( $section['icon'] ) . '" /> ';
                } else {
                    if ( ! empty ( $section['icon_class'] ) ) {
                        $icon_class = ' ' . $section['icon_class'];
                    } elseif ( ! empty ( $this->args['default_icon_class'] ) ) {
                        $icon_class = ' ' . $this->args['default_icon_class'];
                    } else {
                        $icon_class = '';
                    }
                    $icon = ( ! isset ( $section['icon'] ) ) ? '<i class="el el-cog' . esc_attr( $icon_class ) . '"></i> ' : '<i class="' . esc_attr( $section['icon'] ) . esc_attr( $icon_class ) . '"></i> ';
                }
                if ( strpos( $icon, 'el-icon-' ) !== false ) {
                    $icon = str_replace( 'el-icon-', 'el el-', $icon );
                }

                $hide_section = '';
                if ( isset ( $section['hidden'] ) ) {
                    $hide_section = ( $section['hidden'] == true ) ? ' hidden ' : '';
                }

                $canBeSubSection = ( $k > 0 && ( ! isset ( $sections[ ( $k ) ]['type'] ) || $sections[ ( $k ) ]['type'] != "divide" ) ) ? true : false;

                if ( ! $canBeSubSection && isset ( $section['subsection'] ) && $section['subsection'] == true ) {
                    unset ( $section['subsection'] );
                }

                if ( isset ( $section['type'] ) && $section['type'] == "divide" ) {
                    $string .= '<li class="divide' . esc_attr( $section['class'] ) . '">&nbsp;</li>';
                } else if ( ! isset ( $section['subsection'] ) || $section['subsection'] != true ) {

                    // DOVY! REPLACE $k with $section['ID'] when used properly.
                    //$active = ( ( is_numeric($this->current_tab) && $this->current_tab == $k ) || ( !is_numeric($this->current_tab) && $this->current_tab === $k )  ) ? ' active' : '';
                    $subsections      = ( isset ( $sections[ ( $k + 1 ) ] ) && isset ( $sections[ ( $k + 1 ) ]['subsection'] ) && $sections[ ( $k + 1 ) ]['subsection'] == true ) ? true : false;
                    $subsectionsClass = $subsections ? ' hasSubSections' : '';
                    $subsectionsClass .= ( ! isset ( $section['fields'] ) || empty ( $section['fields'] ) ) ? ' empty_section' : '';
                    $extra_icon = $subsections ? '<span class="extraIconSubsections"><i class="el el-chevron-down">&nbsp;</i></span>' : '';
                    $string .= '<li id="' . esc_attr( $k . $suffix ) . '_section_group_li" class="redux-group-tab-link-li' . esc_attr( $hide_section ) . esc_attr( $section['class'] ) . esc_attr( $subsectionsClass ) . '">';
                    $string .= '<a href="javascript:void(0);" id="' . esc_attr( $k . $suffix ) . '_section_group_li_a" class="redux-group-tab-link-a" data-key="' . esc_attr( $k ) . '" data-rel="' . esc_attr( $k . $suffix ) . '">' . $extra_icon . $icon . '<span class="group_title">' . wp_kses_post( $section['title'] ) . '</span></a>';

                    $nextK = $k;

                    // Make sure you can make this a subsection
                    if ( $subsections ) {
                        $string .= '<ul id="' . esc_attr( $nextK . $suffix ) . '_section_group_li_subsections" class="subsection">';
                        $doLoop = true;

                        while ( $doLoop ) {
                            $nextK += 1;
                            $display = true;

                            if ( isset ( $_GET['page'] ) && $_GET['page'] == $this->args['page_slug'] ) {
                                if ( isset ( $sections[ $nextK ]['panel'] ) && $sections[ $nextK ]['panel'] == false ) {
                                    $display = false;
                                }
                            }

                            if ( count( $sections ) < $nextK || ! isset ( $sections[ $nextK ] ) || ! isset ( $sections[ $nextK ]['subsection'] ) || $sections[ $nextK ]['subsection'] != true ) {
                                $doLoop = false;
                            } else {
                                if ( ! $display ) {
                                    continue;
                                }

                                $hide_sub = '';
                                if ( isset ( $sections[ $nextK ]['hidden'] ) ) {
                                    $hide_sub = ( $sections[ $nextK ]['hidden'] == true ) ? ' hidden ' : '';
                                }

                                if ( ( isset ( $this->args['icon_type'] ) && $this->args['icon_type'] == 'image' ) || ( isset ( $sections[ $nextK ]['icon_type'] ) && $sections[ $nextK ]['icon_type'] == 'image' ) ) {
                                    //if( !empty( $this->args['icon_type'] ) && $this->args['icon_type'] == 'image' ) {
                                    $icon = ( ! isset ( $sections[ $nextK ]['icon'] ) ) ? '' : '<img class="image_icon_type" src="' . esc_url( $sections[ $nextK ]['icon'] ) . '" /> ';
                                } else {
                                    if ( ! empty ( $sections[ $nextK ]['icon_class'] ) ) {
                                        $icon_class = ' ' . $sections[ $nextK ]['icon_class'];
                                    } elseif ( ! empty ( $this->args['default_icon_class'] ) ) {
                                        $icon_class = ' ' . $this->args['default_icon_class'];
                                    } else {
                                        $icon_class = '';
                                    }
                                    $icon = ( ! isset ( $sections[ $nextK ]['icon'] ) ) ? '' : '<i class="' . esc_attr( $sections[ $nextK ]['icon'] ) . esc_attr( $icon_class ) . '"></i> ';
                                }
                                if ( strpos( $icon, 'el-icon-' ) !== false ) {
                                    $icon = str_replace( 'el-icon-', 'el el-', $icon );
                                }

                                $sections[ $nextK ]['class'] = isset($sections[ $nextK ]['class']) ? $sections[ $nextK ]['class'] : '';
                                $section[ $nextK ]['class'] = isset ( $section[ $nextK ]['class'] ) ? $section[ $nextK ]['class'] : $sections[ $nextK ]['class'];
                                $string .= '<li id="' . esc_attr( $nextK . $suffix ) . '_section_group_li" class="redux-group-tab-link-li ' . esc_attr( $hide_sub ) . esc_attr( $section[ $nextK ]['class'] ) . ( $icon ? ' hasIcon' : '' ) . '">';
                                $string .= '<a href="javascript:void(0);" id="' . esc_attr( $nextK . $suffix ) . '_section_group_li_a" class="redux-group-tab-link-a" data-key="' . esc_attr( $nextK ) . '" data-rel="' . esc_attr( $nextK . $suffix ) . '">' . $icon . '<span class="group_title">' . wp_kses_post( $sections[ $nextK ]['title'] ) . '</span></a>';
                                $string .= '</li>';
                            }
                        }

                        $string .= '</ul>';
                    }

                    $string .= '</li>';
                }

                return $string;
            }
            // section_menu()

            /**
             * HTML OUTPUT.
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            public function generate_panel() {
                require_once 'core/panel.php';
                $panel = new reduxCorePanel ( $this );
                $panel->init();
                $this->set_transients();
            }

            /**
             * Section HTML OUTPUT.
             *
             * @since       1.0.0
             * @access      public
             *
             * @param       array $section
             *
             * @return      void
             */
            public function _section_desc( $section ) {
                $id = rtrim( $section['id'], '_section' );
                $id = str_replace($this->args['opt_name'], '', $id);

                if ( isset ( $this->sections[ $id ]['desc'] ) && ! empty ( $this->sections[ $id ]['desc'] ) ) {
                    echo '<div class="redux-section-desc">' . $this->sections[ $id ]['desc'] . '</div>';
                }
            }

            /**
             * Field HTML OUTPUT.
             * Gets option from options array, then calls the specific field type class - allows extending by other devs
             *
             * @since       1.0.0
             *
             * @param array  $field
             * @param string $v
             *
             * @return      void
             */
            public function _field_input( $field, $v = null ) {

                if ( isset ( $field['callback'] ) && ( is_callable( $field['callback'] ) || ( is_string( $field['callback'] ) && function_exists( $field['callback'] ) ) ) ) {

                    $value = ( isset ( $this->options[ $field['id'] ] ) ) ? $this->options[ $field['id'] ] : '';

                    /**
                     * action 'redux-before-field-{opt_name}'
                     *
                     * @deprecated
                     *
                     * @param array  $field field data
                     * @param string $value field.id
                     */
                    do_action( "redux-before-field-{$this->args['opt_name']}", $field, $value ); // REMOVE

                    /**
                     * action 'redux/field/{opt_name}/{field.type}/callback/before'
                     *
                     * @param array  $field field data
                     * @param string $value field.id
                     */
                    do_action_ref_array( "redux/field/{$this->args['opt_name']}/{$field['type']}/callback/before", array(
                        &$field,
                        &$value
                    ) );

                    /**
                     * action 'redux/field/{opt_name}/callback/before'
                     *
                     * @param array  $field field data
                     * @param string $value field.id
                     */
                    do_action_ref_array( "redux/field/{$this->args['opt_name']}/callback/before", array(
                        &$field,
                        &$value
                    ) );

                    call_user_func( $field['callback'], $field, $value );


                    /**
                     * action 'redux-after-field-{opt_name}'
                     *
                     * @deprecated
                     *
                     * @param array  $field field data
                     * @param string $value field.id
                     */
                    do_action( "redux-after-field-{$this->args['opt_name']}", $field, $value ); // REMOVE

                    /**
                     * action 'redux/field/{opt_name}/{field.type}/callback/after'
                     *
                     * @param array  $field field data
                     * @param string $value field.id
                     */
                    do_action_ref_array( "redux/field/{$this->args['opt_name']}/{$field['type']}/callback/after", array(
                        &$field,
                        &$value
                    ) );

                    /**
                     * action 'redux/field/{opt_name}/callback/after'
                     *
                     * @param array  $field field data
                     * @param string $value field.id
                     */
                    do_action_ref_array( "redux/field/{$this->args['opt_name']}/callback/after", array(
                        &$field,
                        &$value
                    ) );


                    return;
                }

                if ( isset ( $field['type'] ) ) {

                    // If the field is set not to display in the panel
                    $display = true;
                    if ( isset ( $_GET['page'] ) && $_GET['page'] == $this->args['page_slug'] ) {
                        if ( isset ( $field['panel'] ) && $field['panel'] == false ) {
                            $display = false;
                        }
                    }

                    if ( ! $display ) {
                        return;
                    }

                    $field_class = "ReduxFramework_{$field['type']}";

                    if ( ! class_exists( $field_class ) ) {
                        //                    $class_file = apply_filters( 'redux/field/class/'.$field['type'], self::$_dir . 'inc/fields/' . $field['type'] . '/field_' . $field['type'] . '.php', $field ); // REMOVE
                        /**
                         * filter 'redux/{opt_name}/field/class/{field.type}'
                         *
                         * @param       string        field class file path
                         * @param array $field        field data
                         */
                        $class_file = apply_filters( "redux/{$this->args['opt_name']}/field/class/{$field['type']}", self::$_dir . "inc/fields/{$field['type']}/field_{$field['type']}.php", $field );

                        if ( $class_file ) {
                            if ( file_exists( $class_file ) ) {
                                require_once $class_file;
                            }
                        }
                    }

                    if ( class_exists( $field_class ) ) {
                        $value = isset ( $this->options[ $field['id'] ] ) ? $this->options[ $field['id'] ] : '';

                        if ( $v !== null ) {
                            $value = $v;
                        }

                        /**
                         * action 'redux-before-field-{opt_name}'
                         *
                         * @deprecated
                         *
                         * @param array  $field field data
                         * @param string $value field id
                         */
                        do_action( "redux-before-field-{$this->args['opt_name']}", $field, $value ); // REMOVE

                        /**
                         * action 'redux/field/{opt_name}/{field.type}/render/before'
                         *
                         * @param array  $field field data
                         * @param string $value field id
                         */
                        do_action_ref_array( "redux/field/{$this->args['opt_name']}/{$field['type']}/render/before", array(
                            &$field,
                            &$value
                        ) );

                        /**
                         * action 'redux/field/{$this->args['opt_name']}/render/before'
                         *
                         * @param array  $field field data
                         * @param string $value field id
                         */
                        do_action_ref_array( "redux/field/{$this->args['opt_name']}/render/before", array(
                            &$field,
                            &$value
                        ) );

                        if ( ! isset ( $field['name_suffix'] ) ) {
                            $field['name_suffix'] = "";
                        }

                        $render = new $field_class ( $field, $value, $this );
                        ob_start();

                        $render->render();

                        /*

                      echo "<pre>";
                      print_r($value);
                      echo "</pre>";
                     */

                        /**
                         * filter 'redux-field-{opt_name}'
                         *
                         * @deprecated
                         *
                         * @param       string        rendered field markup
                         * @param array $field        field data
                         */
                        $_render = apply_filters( "redux-field-{$this->args['opt_name']}", ob_get_contents(), $field ); // REMOVE

                        /**
                         * filter 'redux/field/{opt_name}/{field.type}/render/after'
                         *
                         * @param       string        rendered field markup
                         * @param array $field        field data
                         */
                        $_render = apply_filters( "redux/field/{$this->args['opt_name']}/{$field['type']}/render/after", $_render, $field );

                        /**
                         * filter 'redux/field/{opt_name}/render/after'
                         *
                         * @param       string        rendered field markup
                         * @param array $field        field data
                         */
                        $_render = apply_filters( "redux/field/{$this->args['opt_name']}/render/after", $_render, $field );

                        ob_end_clean();

                        //save the values into a unique array in case we need it for dependencies
                        $this->fieldsValues[ $field['id'] ] = ( isset ( $value['url'] ) && is_array( $value ) ) ? $value['url'] : $value;

                        //create default data und class string and checks the dependencies of an object
                        $class_string = '';
                        $data_string  = '';

                        $this->check_dependencies( $field );

                        /**
                         * action 'redux/field/{opt_name}/{field.type}/fieldset/before/{opt_name}'
                         *
                         * @param array  $field field data
                         * @param string $value field id
                         */
                        do_action_ref_array( "redux/field/{$this->args['opt_name']}/{$field['type']}/fieldset/before/{$this->args['opt_name']}", array(
                            &$field,
                            &$value
                        ) );

                        /**
                         * action 'redux/field/{opt_name}/fieldset/before/{opt_name}'
                         *
                         * @param array  $field field data
                         * @param string $value field id
                         */
                        do_action_ref_array( "redux/field/{$this->args['opt_name']}/fieldset/before/{$this->args['opt_name']}", array(
                            &$field,
                            &$value
                        ) );

                        //if ( ! isset( $field['fields'] ) || empty( $field['fields'] ) ) {
                        $hidden = '';
                        if ( isset ( $field['hidden'] ) && $field['hidden'] ) {
                            $hidden = 'hidden ';
                        }

                        if ( isset( $field['full_width'] ) && $field['full_width'] == true ) {
                            $class_string .= "redux_remove_th";
                        }

                        if ( isset ( $field['fieldset_class'] ) && ! empty( $field['fieldset_class'] ) ) {
                            $class_string .= ' ' . $field['fieldset_class'];
                        }

                        echo '<fieldset id="' . $this->args['opt_name'] . '-' . $field['id'] . '" class="' . $hidden . 'redux-field-container redux-field redux-field-init redux-container-' . $field['type'] . ' ' . $class_string . '" data-id="' . $field['id'] . '" ' . $data_string . ' data-type="' . $field['type'] . '">';
                        //}

                        echo $_render;

                        if ( ! empty ( $field['desc'] ) ) {
                            $field['description'] = $field['desc'];
                        }

                        echo ( isset ( $field['description'] ) && $field['type'] != "info" && $field['type'] !== "section" && ! empty ( $field['description'] ) ) ? '<div class="description field-desc">' . $field['description'] . '</div>' : '';

                        //if ( ! isset( $field['fields'] ) || empty( $field['fields'] ) ) {
                        echo '</fieldset>';
                        //}

                        /**
                         * action 'redux-after-field-{opt_name}'
                         *
                         * @deprecated
                         *
                         * @param array  $field field data
                         * @param string $value field id
                         */
                        do_action( "redux-after-field-{$this->args['opt_name']}", $field, $value ); // REMOVE

                        /**
                         * action 'redux/field/{opt_name}/{field.type}/fieldset/after/{opt_name}'
                         *
                         * @param array  $field field data
                         * @param string $value field id
                         */
                        do_action_ref_array( "redux/field/{$this->args['opt_name']}/{$field['type']}/fieldset/after/{$this->args['opt_name']}", array(
                            &$field,
                            &$value
                        ) );

                        /**
                         * action 'redux/field/{opt_name}/fieldset/after/{opt_name}'
                         *
                         * @param array  $field field data
                         * @param string $value field id
                         */
                        do_action_ref_array( "redux/field/{$this->args['opt_name']}/fieldset/after/{$this->args['opt_name']}", array(
                            &$field,
                            &$value
                        ) );
                    }
                }
            }
            // _field_input()

            /**
             * Can Output CSS
             * Check if a field meets its requirements before outputting to CSS
             *
             * @param $field
             *
             * @return bool
             */
            public function _can_output_css( $field ) {
                $return = true;

                $field = apply_filters( "redux/field/{$this->args['opt_name']}/_can_output_css", $field );
                if ( isset ( $field['force_output'] ) && $field['force_output'] == true ) {
                    return $return;
                }

                if ( ! empty ( $field['required'] ) ) {
                    if ( isset ( $field['required'][0] ) ) {
                        if ( ! is_array( $field['required'][0] ) && count( $field['required'] ) == 3 ) {
                            $parentValue = $GLOBALS[ $this->args['global_variable'] ][ $field['required'][0] ];
                            $checkValue  = $field['required'][2];
                            $operation   = $field['required'][1];
                            $return      = $this->compareValueDependencies( $parentValue, $checkValue, $operation );
                        } else if ( is_array( $field['required'][0] ) ) {
                            foreach ( $field['required'] as $required ) {
                                if ( ! is_array( $required[0] ) && count( $required ) == 3 ) {
                                    $parentValue = $GLOBALS[ $this->args['global_variable'] ][ $required[0] ];
                                    $checkValue  = $required[2];
                                    $operation   = $required[1];
                                    $return      = $this->compareValueDependencies( $parentValue, $checkValue, $operation );
                                }
                                if ( ! $return ) {
                                    return $return;
                                }
                            }
                        }
                    }
                }

                return $return;
            }
            // _can_output_css

            /**
             * Checks dependencies between objects based on the $field['required'] array
             * If the array is set it needs to have exactly 3 entries.
             * The first entry describes which field should be monitored by the current field. eg: "content"
             * The second entry describes the comparison parameter. eg: "equals, not, is_larger, is_smaller ,contains"
             * The third entry describes the value that we are comparing against.
             * Example: if the required array is set to array('content','equals','Hello World'); then the current
             * field will only be displayed if the field with id "content" has exactly the value "Hello World"
             *
             * @param array $field
             *
             * @return array $params
             */
            public function check_dependencies( $field ) {
                //$params = array('data_string' => "", 'class_string' => "");
                if ( isset( $field['ajax_save'] ) && $field['ajax_save'] == false ) {
                    $this->reload_fields[] = $field['id'];
                }

                if ( ! empty ( $field['required'] ) ) {
                    if ( ! isset ( $this->required_child[ $field['id'] ] ) ) {
                        $this->required_child[ $field['id'] ] = array();
                    }

                    if ( ! isset ( $this->required[ $field['id'] ] ) ) {
                        $this->required[ $field['id'] ] = array();
                    }

                    if ( is_array( $field['required'][0] ) ) {

                        foreach ( $field['required'] as $value ) {
                            if ( is_array( $value ) && count( $value ) == 3 ) {
                                $data               = array();
                                $data['parent']     = $value[0];
                                $data['operation']  = $value[1];
                                $data['checkValue'] = $value[2];

                                $this->required[ $data['parent'] ][ $field['id'] ][] = $data;

                                if ( ! in_array( $data['parent'], $this->required_child[ $field['id'] ] ) ) {
                                    $this->required_child[ $field['id'] ][] = $data;
                                }

                                $this->checkRequiredDependencies( $field, $data );
                            }
                        }
                    } else {
                        $data               = array();
                        $data['parent']     = $field['required'][0];
                        $data['operation']  = $field['required'][1];
                        $data['checkValue'] = $field['required'][2];

                        $this->required[ $data['parent'] ][ $field['id'] ][] = $data;

                        if ( ! in_array( $data['parent'], $this->required_child[ $field['id'] ] ) ) {
                            $this->required_child[ $field['id'] ][] = $data;
                        }

                        $this->checkRequiredDependencies( $field, $data );
                    }
                }
                //return $params;
            }

            // Compare data for required field
            private function compareValueDependencies( $parentValue, $checkValue, $operation ) {
                $return = false;
                switch ( $operation ) {
                    case '=':
                    case 'equals':
                        $data['operation'] = "=";

                        if ( is_array( $parentValue ) ) {
                            foreach ( $parentValue as $idx => $val ) {
                                if ( is_array( $checkValue ) ) {
                                    foreach ( $checkValue as $i => $v ) {
                                        if ( $val == $v ) {
                                            $return = true;
                                        }
                                    }
                                } else {
                                    if ( $val == $checkValue ) {
                                        $return = true;
                                    }
                                }
                            }
                        } else {
                            if ( is_array( $checkValue ) ) {
                                foreach ( $checkValue as $i => $v ) {
                                    if ( $parentValue == $v ) {
                                        $return = true;
                                    }
                                }
                            } else {
                                if ( $parentValue == $checkValue ) {
                                    $return = true;
                                }
                            }
                        }
                        break;

                    case '!=':
                    case 'not':
                        $data['operation'] = "!==";
                        if ( is_array( $parentValue ) ) {
                            foreach ( $parentValue as $idx => $val ) {
                                if ( is_array( $checkValue ) ) {
                                    foreach ( $checkValue as $i => $v ) {
                                        if ( $val != $v ) {
                                            $return = true;
                                        }
                                    }
                                } else {
                                    if ( $val != $checkValue ) {
                                        $return = true;
                                    }
                                }
                            }
                        } else {
                            if ( is_array( $checkValue ) ) {
                                foreach ( $checkValue as $i => $v ) {
                                    if ( $parentValue != $v ) {
                                        $return = true;
                                    }
                                }
                            } else {
                                if ( $parentValue != $checkValue ) {
                                    $return = true;
                                }
                            }
                        }

                        //                        if ( is_array( $checkValue ) ) {
                        //                            if ( ! in_array( $parentValue, $checkValue ) ) {
                        //                                $return = true;
                        //                            }
                        //                        } else {
                        //                            if ( $parentValue != $checkValue ) {
                        //                                $return = true;
                        //                            } else if ( is_array( $parentValue ) ) {
                        //                                if ( ! in_array( $checkValue, $parentValue ) ) {
                        //                                    $return = true;
                        //                                }
                        //                            }
                        //                        }
                        break;
                    case '>':
                    case 'greater':
                    case 'is_larger':
                        $data['operation'] = ">";
                        if ( $parentValue > $checkValue ) {
                            $return = true;
                        }
                        break;
                    case '>=':
                    case 'greater_equal':
                    case 'is_larger_equal':
                        $data['operation'] = ">=";
                        if ( $parentValue >= $checkValue ) {
                            $return = true;
                        }
                        break;
                    case '<':
                    case 'less':
                    case 'is_smaller':
                        $data['operation'] = "<";
                        if ( $parentValue < $checkValue ) {
                            $return = true;
                        }
                        break;
                    case '<=':
                    case 'less_equal':
                    case 'is_smaller_equal':
                        $data['operation'] = "<=";
                        if ( $parentValue <= $checkValue ) {
                            $return = true;
                        }
                        break;
                    case 'contains':
                        if ( is_array( $parentValue ) ) {
                            $parentValue = implode( ',', $parentValue );
                        }

                        if ( is_array( $checkValue ) ) {
                            foreach ( $checkValue as $idx => $opt ) {
                                if ( strpos( $parentValue, (string) $opt ) !== false ) {
                                    $return = true;
                                }
                            }
                        } else {
                            if ( strpos( $parentValue, (string) $checkValue ) !== false ) {
                                $return = true;
                            }
                        }

                        break;
                    case 'doesnt_contain':
                    case 'not_contain':
                        if ( is_array( $parentValue ) ) {
                            $parentValue = implode( ',', $parentValue );
                        }

                        if ( is_array( $checkValue ) ) {
                            foreach ( $checkValue as $idx => $opt ) {
                                if ( strpos( $parentValue, (string) $opt ) === false ) {
                                    $return = true;
                                }
                            }
                        } else {
                            if ( strpos( $parentValue, (string) $checkValue ) === false ) {
                                $return = true;
                            }
                        }

                        break;
                    case 'is_empty_or':
                        if ( empty ( $parentValue ) || $parentValue == $checkValue ) {
                            $return = true;
                        }
                        break;
                    case 'not_empty_and':
                        if ( ! empty ( $parentValue ) && $parentValue != $checkValue ) {
                            $return = true;
                        }
                        break;
                    case 'is_empty':
                    case 'empty':
                    case '!isset':
                        if ( empty ( $parentValue ) || $parentValue == "" || $parentValue == null ) {
                            $return = true;
                        }
                        break;
                    case 'not_empty':
                    case '!empty':
                    case 'isset':
                        if ( ! empty ( $parentValue ) && $parentValue != "" && $parentValue != null ) {
                            $return = true;
                        }
                        break;
                }

                return $return;
            }

            private function checkRequiredDependencies( $field, $data ) {
                //required field must not be hidden. otherwise hide this one by default

                if ( ! in_array( $data['parent'], $this->fieldsHidden ) && ( ! isset ( $this->folds[ $field['id'] ] ) || $this->folds[ $field['id'] ] != "hide" ) ) {
                    if ( isset ( $this->options[ $data['parent'] ] ) ) {
                        $return = $this->compareValueDependencies( $this->options[ $data['parent'] ], $data['checkValue'], $data['operation'] );
                        //$return = $this->compareValueDependencies( $data['parent'], $data['checkValue'], $data['operation'] );
                    }
                }

                if ( ( isset ( $return ) && $return ) && ( ! isset ( $this->folds[ $field['id'] ] ) || $this->folds[ $field['id'] ] != "hide" ) ) {
                    $this->folds[ $field['id'] ] = "show";
                } else {
                    $this->folds[ $field['id'] ] = "hide";
                    if ( ! in_array( $field['id'], $this->fieldsHidden ) ) {
                        $this->fieldsHidden[] = $field['id'];
                    }
                }
            }

            /**
             * converts an array into a html data string
             *
             * @param array $data example input: array('id'=>'true')
             *
             * @return string $data_string example output: data-id='true'
             */
            public function create_data_string( $data = array() ) {
                $data_string = "";

                foreach ( $data as $key => $value ) {
                    if ( is_array( $value ) ) {
                        $value = implode( "|", $value );
                    }
                    $data_string .= " data-$key='$value' ";
                }

                return $data_string;
            }

            /**
             * Parses the string into variables without the max_input_vars limitation.
             *
             * @since   3.5.7.11
             * @author  harunbasic
             * @access  public
             *
             * @param   string $string
             *
             * @return  array $result
             */
            function redux_parse_str( $string ) {
                if ( '' == $string ) {
                    return false;
                }

                $result = array();
                $pairs  = explode( '&', $string );

                foreach ( $pairs as $key => $pair ) {
                    // use the original parse_str() on each element
                    parse_str( $pair, $params );

                    $k = key( $params );

                    if ( ! isset( $result[ $k ] ) ) {
                        $result += $params;
                    } else {
                        $result[ $k ] = $this->redux_array_merge_recursive_distinct( $result[ $k ], $params[ $k ] );
                    }
                }

                return $result;
            }


            /**
             * Merge arrays without converting values with duplicate keys to arrays as array_merge_recursive does.
             * As seen here http://php.net/manual/en/function.array-merge-recursive.php#92195
             *
             * @since   3.5.7.11
             * @author  harunbasic
             * @access  public
             *
             * @param   array $array1
             * @param   array $array2
             *
             * @return  array $merged
             */
            function redux_array_merge_recursive_distinct( array $array1, array $array2 ) {
                $merged = $array1;

                foreach ( $array2 as $key => $value ) {
                    if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
                        $merged[ $key ] = $this->redux_array_merge_recursive_distinct( $merged[ $key ], $value );
                    } else if ( is_numeric( $key ) && isset( $merged[ $key ] ) ) {
                        $merged[] = $value;
                    } else {
                        $merged[ $key ] = $value;
                    }
                }

                return $merged;
            }

            private function change_demo_defaults() {
                if ( $this->args['dev_mode'] == true || Redux_Helpers::isLocalHost() == true ) {
                    if ( ! empty( $this->args['admin_bar_links'] ) ) {
                        foreach ( $this->args['admin_bar_links'] as $idx => $arr ) {
                            if ( is_array( $arr ) && ! empty( $arr ) ) {
                                foreach ( $arr as $x => $y ) {
                                    if ( strpos( strtolower( $y ), 'redux' ) !== false ) {
                                        $msg = __( '<strong>Redux Framework Notice: </strong>There are references to the Redux Framework support site in your config\'s <code>admin_bar_links</code> argument.  This is sample data.  Please change or remove this data before shipping your product.', 'redux-framework' );
                                        $this->display_arg_change_notice( 'admin', $msg );
                                        $this->omit_admin_items = true;
                                        continue;
                                    }
                                }
                            }
                        }
                    }

                    if ( ! empty( $this->args['share_icons'] ) ) {
                        foreach ( $this->args['share_icons'] as $idx => $arr ) {
                            if ( is_array( $arr ) && ! empty( $arr ) ) {
                                foreach ( $arr as $x => $y ) {
                                    if ( strpos( strtolower( $y ), 'redux' ) !== false ) {
                                        $msg = __( '<strong>Redux Framework Notice: </strong>There are references to the Redux Framework support site in your config\'s <code>share_icons</code> argument.  This is sample data.  Please change or remove this data before shipping your product.', 'redux-framework' );
                                        $this->display_arg_change_notice( 'share', $msg );
                                        $this->omit_share_icons = true;
                                    }
                                }
                            }
                        }
                    }

                }
            }

            private function display_arg_change_notice( $mode, $msg = '' ) {
                if ( $mode == 'admin' ) {
                    if ( ! $this->omit_admin_items ) {
                        $this->admin_notices[] = array(
                            'type'    => 'error',
                            'msg'     => $msg,
                            'id'      => 'admin_config',
                            'dismiss' => true,
                        );
                    }
                }

                if ( $mode == 'share' ) {
                    if ( ! $this->omit_share_icons ) {
                        $this->admin_notices[] = array(
                            'type'    => 'error',
                            'msg'     => $msg,
                            'id'      => 'share_config',
                            'dismiss' => true,
                        );
                    }
                }
            }
        }

        // ReduxFramework

        /**
         * action 'redux/init'
         *
         * @param null
         */
        do_action( 'redux/init', ReduxFramework::init() );
    } // class_exists('ReduxFramework')

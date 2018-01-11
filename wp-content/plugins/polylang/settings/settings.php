<?php

/**
 * A class for the Polylang settings pages
 * accessible in $polylang global object
 *
 * properties:
 * options          => inherited, reference to Polylang options array
 * model            => inherited, reference to PLL_Model object
 * links_model      => inherited, reference to PLL_Links_Model object
 * links            => inherited, reference to PLL_Admin_Links object
 * static_pages     => inherited, reference to PLL_Admin_Static_Pages object
 * filters_links    => inherited, reference to PLL_Filters_Links object
 * curlang          => inherited, optional, current language used to filter admin content
 * pref_lang        => inherited, preferred language used as default when saving posts or terms
 *
 * @since 1.2
 */
class PLL_Settings extends PLL_Admin_Base {
	protected $active_tab, $modules;

	/**
	 * Constructor
	 *
	 * @since 1.2
	 *
	 * @param object $links_model
	 */
	public function __construct( &$links_model ) {
		parent::__construct( $links_model );

		if ( isset( $_GET['page'] ) ) {
			$this->active_tab = 'mlang' === $_GET['page'] ? 'lang' : substr( $_GET['page'], 6 );
		}

		PLL_Admin_Strings::init();

		// FIXME put this as late as possible
		add_action( 'admin_init', array( $this, 'register_settings_modules' ) );

		// adds screen options and the about box in the languages admin panel
		add_action( 'load-toplevel_page_mlang', array( $this, 'load_page' ) );
		add_action( 'load-languages_page_mlang_strings', array( $this, 'load_page_strings' ) );

		// saves per-page value in screen option
		add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
	}

	/**
	 * Initializes the modules
	 *
	 * @since 1.8
	 */
	public function register_settings_modules() {
		$modules = array(
			'PLL_Settings_Tools',
			'PLL_Settings_Licenses',
		);

		if ( $this->model->get_languages_list() ) {
			$modules = array_merge( array(
				'PLL_Settings_Url',
				'PLL_Settings_Browser',
				'PLL_Settings_Media',
				'PLL_Settings_CPT',
				'PLL_Settings_Sync',
				'PLL_Settings_WPML',
				'PLL_Settings_Share_Slug',
				'PLL_Settings_Translate_Slugs',
			), $modules );
		}

		/**
		 * Filter the list of setting modules
		 *
		 * @since 1.8
		 *
		 * @param array $modules the list of module classes
		 */
		$modules = apply_filters( 'pll_settings_modules', $modules );

		foreach ( $modules as $key => $class ) {
			$key = is_numeric( $key ) ? strtolower( str_replace( 'PLL_Settings_', '', $class ) ) : $key;
			$this->modules[ $key ] = new $class( $this );
		}
	}

	/**
	 * Loads the about metabox
	 *
	 * @since 0.8
	 */
	public function metabox_about() {
		include PLL_SETTINGS_INC . '/view-about.php';
	}

	/**
	 * Adds screen options and the about box in the languages admin panel
	 *
	 * @since 0.9.5
	 */
	public function load_page() {
		if ( ! defined( 'PLL_DISPLAY_ABOUT' ) || PLL_DISPLAY_ABOUT ) {
			add_meta_box(
				'pll-about-box',
				__( 'About Polylang', 'polylang' ),
				array( $this, 'metabox_about' ),
				'settings_page_mlang', // FIXME not shown in screen options
				'normal'
			);
		}

		add_screen_option( 'per_page', array(
			'label'   => __( 'Languages', 'polylang' ),
			'default' => 10,
			'option'  => 'pll_lang_per_page',
		) );

		add_action( 'admin_notices', array( $this, 'notice_objects_with_no_lang' ) );
	}

	/**
	 * Adds screen options in the strings translations admin panel
	 *
	 * @since 2.1
	 */
	public function load_page_strings() {
		add_screen_option( 'per_page', array(
			'label'   => __( 'Strings translations', 'polylang' ),
			'default' => 10,
			'option'  => 'pll_strings_per_page',
		) );
	}

	/**
	 * Save the "Views/Uploads per page" option set by this user
	 *
	 * @since 0.9.5
	 *
	 * @param mixed  $status false or value returned by previous filter
	 * @param string $option Name of the option being changed
	 * @param string $value  Value of the option
	 *
	 * @return string New value if this is our option, otherwise nothing
	 */
	public function set_screen_option( $status, $option, $value ) {
		return 'pll_lang_per_page' === $option || 'pll_strings_per_page' === $option ? $value : $status;
	}

	/**
	 * Manages the user input for the languages pages
	 *
	 * @since 1.9
	 *
	 * @param string $action
	 */
	public function handle_actions( $action ) {
		switch ( $action ) {
			case 'add':
				check_admin_referer( 'add-lang', '_wpnonce_add-lang' );

				if ( $this->model->add_language( $_POST ) && 'en_US' !== $_POST['locale'] ) {
					// attempts to install the language pack
					require_once ABSPATH . 'wp-admin/includes/translation-install.php';
					if ( ! wp_download_language_pack( $_POST['locale'] ) ) {
						add_settings_error( 'general', 'pll_download_mo', __( 'The language was created, but the WordPress language file was not downloaded. Please install it manually.', 'polylang' ) );
					}

					// force checking for themes and plugins translations updates
					wp_clean_themes_cache();
					wp_clean_plugins_cache();
				}
				self::redirect(); // to refresh the page ( possible thanks to the $_GET['noheader']=true )
				break;

			case 'delete':
				check_admin_referer( 'delete-lang' );

				if ( ! empty( $_GET['lang'] ) ) {
					$this->model->delete_language( (int) $_GET['lang'] );
				}

				self::redirect(); // to refresh the page ( possible thanks to the $_GET['noheader']=true )
				break;

			case 'update':
				check_admin_referer( 'add-lang', '_wpnonce_add-lang' );
				$error = $this->model->update_language( $_POST );
				self::redirect(); // to refresh the page ( possible thanks to the $_GET['noheader']=true )
				break;

			case 'default-lang':
				check_admin_referer( 'default-lang' );

				if ( $lang = $this->model->get_language( (int) $_GET['lang'] ) ) {
					$this->model->update_default_lang( $lang->slug );
				}

				self::redirect(); // to refresh the page ( possible thanks to the $_GET['noheader']=true )
				break;

			case 'content-default-lang':
				check_admin_referer( 'content-default-lang' );

				if ( $nolang = $this->model->get_objects_with_no_lang() ) {
					if ( ! empty( $nolang['posts'] ) ) {
						$this->model->set_language_in_mass( 'post', $nolang['posts'], $this->options['default_lang'] );
					}
					if ( ! empty( $nolang['terms'] ) ) {
						$this->model->set_language_in_mass( 'term', $nolang['terms'], $this->options['default_lang'] );
					}
				}

				self::redirect(); // to refresh the page ( possible thanks to the $_GET['noheader']=true )
				break;

			case 'activate':
				check_admin_referer( 'pll_activate' );
				$this->modules[ $_GET['module'] ]->activate();
				self::redirect();
				break;

			case 'deactivate':
				check_admin_referer( 'pll_deactivate' );
				$this->modules[ $_GET['module'] ]->deactivate();
				self::redirect();
				break;

			default:
				/**
				 * Fires when a non default action has been sent to Polylang settings
				 *
				 * @since 1.8
				 */
				do_action( "mlang_action_$action" );
				break;
		}
	}

	/**
	 * Displays the 3 tabs pages: languages, strings translations, settings
	 * also manages user input for these pages
	 *
	 * @since 0.1
	 */
	public function languages_page() {
		switch ( $this->active_tab ) {
			case 'lang':
				// prepare the list table of languages
				$list_table = new PLL_Table_Languages();
				$list_table->prepare_items( $this->model->get_languages_list() );
				break;

			case 'strings':
				$string_table = new PLL_Table_String( $this->model->get_languages_list() );
				$string_table->prepare_items();
				break;
		}

		// handle user input
		$action = isset( $_REQUEST['pll_action'] ) ? $_REQUEST['pll_action'] : '';
		if ( 'edit' === $action && ! empty( $_GET['lang'] ) ) {
			$edit_lang = $this->model->get_language( (int) $_GET['lang'] );
		} else {
			$this->handle_actions( $action );
		}

		// displays the page
		include PLL_SETTINGS_INC . '/view-languages.php';
	}

	/**
	 * Enqueues scripts and styles
	 */
	public function admin_enqueue_scripts() {
		parent::admin_enqueue_scripts();

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'pll_admin', plugins_url( '/js/admin' . $suffix . '.js', POLYLANG_FILE ), array( 'jquery', 'wp-ajax-response', 'postbox', 'jquery-ui-selectmenu' ), POLYLANG_VERSION );
		wp_localize_script( 'pll_admin', 'pll_flag_base_url', plugins_url( '/flags/', POLYLANG_FILE ) );

		wp_enqueue_style( 'pll_selectmenu', plugins_url( '/css/selectmenu' . $suffix . '.css', POLYLANG_FILE ), array(), POLYLANG_VERSION );
	}

	/**
	 * Displays a notice when there are objects with no language assigned
	 *
	 * @since 1.8
	 */
	public function notice_objects_with_no_lang() {
		if ( ! empty( $this->options['default_lang'] ) && $this->model->get_objects_with_no_lang( 1 ) ) {
			printf(
				'<div class="error"><p>%s <a href="%s">%s</a></p></div>',
				esc_html__( 'There are posts, pages, categories or tags without language.', 'polylang' ),
				wp_nonce_url( '?page=mlang&amp;pll_action=content-default-lang&amp;noheader=true', 'content-default-lang' ),
				esc_html__( 'You can set them all to the default language.', 'polylang' )
			);
		}
	}

	/**
	 * Redirects to language page ( current active tab )
	 * saves error messages in a transient for reuse in redirected page
	 *
	 * @since 1.5
	 *
	 * @param array $args query arguments to add to the url
	 */
	static public function redirect( $args = array() ) {
		if ( $errors = get_settings_errors() ) {
			set_transient( 'settings_errors', $errors, 30 );
			$args['settings-updated'] = 1;
		}
		// remove possible 'pll_action' and 'lang' query args from the referer before redirecting
		wp_safe_redirect( add_query_arg( $args, remove_query_arg( array( 'pll_action', 'lang' ), wp_get_referer() ) ) );
		exit;
	}
}

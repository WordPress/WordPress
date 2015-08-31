<?php
/**
 * @package WPSEO\Admin
 */

/**
 * Class that holds most of the admin functionality for Yoast SEO.
 */
class WPSEO_Admin {

	/**
	 * @var array
	 */
	private $options;

	/**
	 * @var Yoast_Dashboard_Widget
	 */
	public $dashboard_widget;

	/**
	 * @var WPSEO_GSC
	 */
	private $page_gsc;

	/**
	 * Class constructor
	 */
	function __construct() {
		$this->options = WPSEO_Options::get_all();

		if ( is_multisite() ) {
			WPSEO_Options::maybe_set_multisite_defaults( false );
		}

		if ( $this->options['stripcategorybase'] === true ) {
			add_action( 'created_category', array( $this, 'schedule_rewrite_flush' ) );
			add_action( 'edited_category', array( $this, 'schedule_rewrite_flush' ) );
			add_action( 'delete_category', array( $this, 'schedule_rewrite_flush' ) );
		}

		$this->page_gsc = new WPSEO_GSC();
		$this->dashboard_widget = new Yoast_Dashboard_Widget();

		// Needs the lower than default priority so other plugins can hook underneath it without issue.
		add_action( 'admin_menu', array( $this, 'register_settings_page' ), 5 );
		add_action( 'network_admin_menu', array( $this, 'register_network_settings_page' ) );

		add_filter( 'plugin_action_links_' . WPSEO_BASENAME, array( $this, 'add_action_link' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'config_page_scripts' ) );

		if ( '0' == get_option( 'blog_public' ) ) {
			add_action( 'admin_footer', array( $this, 'blog_public_warning' ) );
		}

		if ( ( ( isset( $this->options['theme_has_description'] ) && $this->options['theme_has_description'] === true ) ||
				$this->options['theme_description_found'] !== '' ) && $this->options['ignore_meta_description_warning'] !== true
		) {
			add_action( 'admin_footer', array( $this, 'meta_description_warning' ) );
		}

		if ( $this->options['cleanslugs'] === true ) {
			add_filter( 'name_save_pre', array( $this, 'remove_stopwords_from_slug' ), 0 );
		}

		add_filter( 'user_contactmethods', array( $this, 'update_contactmethods' ), 10, 1 );

		add_action( 'after_switch_theme', array( $this, 'switch_theme' ) );
		add_action( 'switch_theme', array( $this, 'switch_theme' ) );

		add_filter( 'set-screen-option', array( $this, 'save_bulk_edit_options' ), 10, 3 );

		add_action( 'activated_plugin', array( 'WPSEO_Plugin_Conflict', 'hook_check_for_plugin_conflicts' ), 10, 1 );

		WPSEO_Utils::register_cache_clear_option( 'wpseo',  '' );
	}

	/**
	 * Schedules a rewrite flush to happen at shutdown
	 */
	function schedule_rewrite_flush() {
		add_action( 'shutdown', 'flush_rewrite_rules' );
	}

	/**
	 * Register the menu item and its sub menu's.
	 *
	 * @global array $submenu used to change the label on the first item.
	 */
	function register_settings_page() {
		if ( WPSEO_Utils::grant_access() !== true ) {
			return;
		}

		// Base 64 encoded SVG image.
		$icon_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCIgWw0KCTwhRU5USVRZIG5zX2Zsb3dzICJodHRwOi8vbnMuYWRvYmUuY29tL0Zsb3dzLzEuMC8iPg0KCTwhRU5USVRZIG5zX2V4dGVuZCAiaHR0cDovL25zLmFkb2JlLmNvbS9FeHRlbnNpYmlsaXR5LzEuMC8iPg0KCTwhRU5USVRZIG5zX2FpICJodHRwOi8vbnMuYWRvYmUuY29tL0Fkb2JlSWxsdXN0cmF0b3IvMTAuMC8iPg0KCTwhRU5USVRZIG5zX2dyYXBocyAiaHR0cDovL25zLmFkb2JlLmNvbS9HcmFwaHMvMS4wLyI+DQpdPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMYWFnXzEiIHhtbG5zOng9IiZuc19leHRlbmQ7IiB4bWxuczppPSImbnNfYWk7IiB4bWxuczpncmFwaD0iJm5zX2dyYXBoczsiDQoJIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbG5zOmE9Imh0dHA6Ly9ucy5hZG9iZS5jb20vQWRvYmVTVkdWaWV3ZXJFeHRlbnNpb25zLzMuMC8iDQoJIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgNDAgMzEuODkiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDQwIDMxLjg5IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxnPg0KPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTQwLDEyLjUyNEM0MCw1LjYwOCwzMS40NjksMCwyMCwwQzguNTMsMCwwLDUuNjA4LDAsMTIuNTI0YzAsNS41Niw1LjI0MywxMC4yNzIsMTMuNTU3LDExLjkwN3YtNC4wNjUNCgljMCwwLDAuMDQtMS0wLjI4LTEuOTJjLTAuMzItMC45MjEtMS43Ni0zLjAwMS0xLjc2LTUuMTIxYzAtMi4xMjEsMi41NjEtOS41NjMsNS4xMjItMTAuNDQ0Yy0wLjQsMS4yMDEtMC4zMiw3LjY4My0wLjMyLDcuNjgzDQoJczEuNCwyLjcyLDQuNjQxLDIuNzJjMy4yNDIsMCw0LjUxMS0xLjc2LDQuNzE1LTIuMmMwLjIwNi0wLjQ0LDAuODQ2LTguNzIzLDAuODQ2LTguNzIzczQuMDgyLDQuNDAyLDMuNjgyLDkuMzYzDQoJYy0wLjQwMSw0Ljk2Mi00LjQ4Miw3LjIwMy02LjEyMiw5LjEyM2MtMS4yODYsMS41MDUtMi4yMjQsMy4xMy0yLjYyOSw0LjE2OGMwLjgwMS0wLjAzNCwxLjU4Ny0wLjA5OCwyLjM2MS0wLjE4NGw5LjE1MSw3LjA1OQ0KCWwtNC44ODQtNy44M0MzNS41MzUsMjIuMTYxLDQwLDE3LjcxMyw0MCwxMi41MjR6Ii8+DQo8L2c+DQo8L3N2Zz4=';

		// Add main page.
		$admin_page = add_menu_page( 'Yoast SEO: ' . __( 'General Settings', 'wordpress-seo' ), __( 'SEO', 'wordpress-seo' ), 'manage_options', 'wpseo_dashboard', array(
			$this,
			'load_page',
		), $icon_svg, '99.31337' );

		/**
		 * Filter: 'wpseo_manage_options_capability' - Allow changing the capability users need to view the settings pages
		 *
		 * @api string unsigned The capability
		 */
		$manage_options_cap = apply_filters( 'wpseo_manage_options_capability', 'manage_options' );

		// Sub menu pages.
		$submenu_pages = array(
			array(
				'wpseo_dashboard',
				'',
				__( 'Titles &amp; Metas', 'wordpress-seo' ),
				$manage_options_cap,
				'wpseo_titles',
				array( $this, 'load_page' ),
				array( array( $this, 'title_metas_help_tab' ) ),
			),
			array(
				'wpseo_dashboard',
				'',
				__( 'Social', 'wordpress-seo' ),
				$manage_options_cap,
				'wpseo_social',
				array( $this, 'load_page' ),
				null,
			),
			array(
				'wpseo_dashboard',
				'',
				__( 'XML Sitemaps', 'wordpress-seo' ),
				$manage_options_cap,
				'wpseo_xml',
				array( $this, 'load_page' ),
				null,
			),
			array(
				'wpseo_dashboard',
				'',
				__( 'Advanced', 'wordpress-seo' ),
				$manage_options_cap,
				'wpseo_advanced',
				array( $this, 'load_page' ),
				null,
			),
			array(
				'wpseo_dashboard',
				'',
				__( 'Tools', 'wordpress-seo' ),
				$manage_options_cap,
				'wpseo_tools',
				array( $this, 'load_page' ),
				null,
			),
			array(
				'wpseo_dashboard',
				'',
				__( 'Search Console', 'wordpress-seo' ),
				$manage_options_cap,
				'wpseo_search_console',
				array( $this->page_gsc, 'display' ),
				array( array( $this->page_gsc, 'set_help' ) ),
			),
		);

		// Add Extension submenu page.
		$submenu_pages[] = array(
			'wpseo_dashboard',
			'',
			'<span style="color:#f18500">' . __( 'Extensions', 'wordpress-seo' ) . '</span>',
			$manage_options_cap,
			'wpseo_licenses',
			array( $this, 'load_page' ),
			null,
		);

		// Allow submenu pages manipulation.
		$submenu_pages = apply_filters( 'wpseo_submenu_pages', $submenu_pages );

		// Loop through submenu pages and add them.
		if ( count( $submenu_pages ) ) {
			foreach ( $submenu_pages as $submenu_page ) {

				// Add submenu page.
				$admin_page = add_submenu_page( $submenu_page[0], $submenu_page[2] . ' - Yoast SEO', $submenu_page[2], $submenu_page[3], $submenu_page[4], $submenu_page[5] );

				// Check if we need to hook.
				if ( isset( $submenu_page[6] ) && ( is_array( $submenu_page[6] ) && $submenu_page[6] !== array() ) ) {
					foreach ( $submenu_page[6] as $submenu_page_action ) {
						add_action( 'load-' . $admin_page, $submenu_page_action );
					}
				}
			}
		}

		global $submenu;
		if ( isset( $submenu['wpseo_dashboard'] ) && current_user_can( $manage_options_cap ) ) {
			$submenu['wpseo_dashboard'][0][0] = __( 'General', 'wordpress-seo' );
		}
	}

	/**
	 * Adds contextual help to the titles & metas page.
	 */
	function title_metas_help_tab() {
		$screen = get_current_screen();

		$screen->set_help_sidebar( '
			<p><strong>' . __( 'For more information:', 'wordpress-seo' ) . '</strong></p>
			<p><a target="_blank" href="https://yoast.com/articles/wordpress-seo/#titles">' . __( 'Title optimization', 'wordpress-seo' ) . '</a></p>
			<p><a target="_blank" href="https://yoast.com/google-page-title/">' . __( 'Why Google won\'t display the right page title', 'wordpress-seo' ) . '</a></p>'
		);

		$screen->add_help_tab(
			array(
				'id'      => 'basic-help',
				'title'   => __( 'Template explanation', 'wordpress-seo' ),
				/* translators: %1$s expands to Yoast SEO */
				'content' => '<p>' . sprintf( __( 'The title &amp; metas settings for %1$s are made up of variables that are replaced by specific values from the page when the page is displayed. The tabs on the left explain the available variables.', 'wordpress-seo' ), 'Yoast SEO' ) . '</p>' . '<p>' . __( 'Note that not all variables can be used in every template.', 'wordpress-seo' ) . '</p>',
			)
		);

		$screen->add_help_tab(
			array(
				'id'      => 'title-vars',
				'title'   => __( 'Basic Variables', 'wordpress-seo' ),
				'content' => "\n\t\t<h2>" . __( 'Basic Variables', 'wordpress-seo' ) . "</h2>\n\t\t" . WPSEO_Replace_Vars::get_basic_help_texts(),
			)
		);

		$screen->add_help_tab(
			array(
				'id'      => 'title-vars-advanced',
				'title'   => __( 'Advanced Variables', 'wordpress-seo' ),
				'content' => "\n\t\t<h2>" . __( 'Advanced Variables', 'wordpress-seo' ) . "</h2>\n\t\t" . WPSEO_Replace_Vars::get_advanced_help_texts(),
			)
		);
	}

	/**
	 * Register the settings page for the Network settings.
	 */
	function register_network_settings_page() {
		if ( WPSEO_Utils::grant_access() ) {
			// Base 64 encoded SVG image.
			$icon_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCIgWw0KCTwhRU5USVRZIG5zX2Zsb3dzICJodHRwOi8vbnMuYWRvYmUuY29tL0Zsb3dzLzEuMC8iPg0KCTwhRU5USVRZIG5zX2V4dGVuZCAiaHR0cDovL25zLmFkb2JlLmNvbS9FeHRlbnNpYmlsaXR5LzEuMC8iPg0KCTwhRU5USVRZIG5zX2FpICJodHRwOi8vbnMuYWRvYmUuY29tL0Fkb2JlSWxsdXN0cmF0b3IvMTAuMC8iPg0KCTwhRU5USVRZIG5zX2dyYXBocyAiaHR0cDovL25zLmFkb2JlLmNvbS9HcmFwaHMvMS4wLyI+DQpdPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMYWFnXzEiIHhtbG5zOng9IiZuc19leHRlbmQ7IiB4bWxuczppPSImbnNfYWk7IiB4bWxuczpncmFwaD0iJm5zX2dyYXBoczsiDQoJIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbG5zOmE9Imh0dHA6Ly9ucy5hZG9iZS5jb20vQWRvYmVTVkdWaWV3ZXJFeHRlbnNpb25zLzMuMC8iDQoJIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgNDAgMzEuODkiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDQwIDMxLjg5IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxnPg0KPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTQwLDEyLjUyNEM0MCw1LjYwOCwzMS40NjksMCwyMCwwQzguNTMsMCwwLDUuNjA4LDAsMTIuNTI0YzAsNS41Niw1LjI0MywxMC4yNzIsMTMuNTU3LDExLjkwN3YtNC4wNjUNCgljMCwwLDAuMDQtMS0wLjI4LTEuOTJjLTAuMzItMC45MjEtMS43Ni0zLjAwMS0xLjc2LTUuMTIxYzAtMi4xMjEsMi41NjEtOS41NjMsNS4xMjItMTAuNDQ0Yy0wLjQsMS4yMDEtMC4zMiw3LjY4My0wLjMyLDcuNjgzDQoJczEuNCwyLjcyLDQuNjQxLDIuNzJjMy4yNDIsMCw0LjUxMS0xLjc2LDQuNzE1LTIuMmMwLjIwNi0wLjQ0LDAuODQ2LTguNzIzLDAuODQ2LTguNzIzczQuMDgyLDQuNDAyLDMuNjgyLDkuMzYzDQoJYy0wLjQwMSw0Ljk2Mi00LjQ4Miw3LjIwMy02LjEyMiw5LjEyM2MtMS4yODYsMS41MDUtMi4yMjQsMy4xMy0yLjYyOSw0LjE2OGMwLjgwMS0wLjAzNCwxLjU4Ny0wLjA5OCwyLjM2MS0wLjE4NGw5LjE1MSw3LjA1OQ0KCWwtNC44ODQtNy44M0MzNS41MzUsMjIuMTYxLDQwLDE3LjcxMyw0MCwxMi41MjR6Ii8+DQo8L2c+DQo8L3N2Zz4=';
			add_menu_page( 'Yoast SEO: ' . __( 'MultiSite Settings', 'wordpress-seo' ), __( 'SEO', 'wordpress-seo' ), 'delete_users', 'wpseo_dashboard', array(
				$this,
				'network_config_page',
			), $icon_svg );

			if ( WPSEO_Utils::allow_system_file_edit() === true ) {
				add_submenu_page( 'wpseo_dashboard', 'Yoast SEO: ' . __( 'Edit Files', 'wordpress-seo' ), __( 'Edit Files', 'wordpress-seo' ), 'delete_users', 'wpseo_files', array(
					$this,
					'load_page',
				) );
			}

			// Add Extension submenu page.
			add_submenu_page( 'wpseo_dashboard', 'Yoast SEO: ' . __( 'Extensions', 'wordpress-seo' ), __( 'Extensions', 'wordpress-seo' ), 'delete_users', 'wpseo_licenses', array(
				$this,
				'load_page',
			) );
		}
	}


	/**
	 * Load the form for a WPSEO admin page
	 */
	function load_page() {
		$page = filter_input( INPUT_GET, 'page' );

		switch ( $page ) {
			case 'wpseo_advanced':
				require_once( WPSEO_PATH . 'admin/pages/advanced.php' );
				break;

			case 'wpseo_tools':
				require_once( WPSEO_PATH . 'admin/pages/tools.php' );
				break;

			case 'wpseo_titles':
				require_once( WPSEO_PATH . 'admin/pages/metas.php' );
				break;

			case 'wpseo_social':
				require_once( WPSEO_PATH . 'admin/pages/social.php' );
				break;

			case 'wpseo_xml':
				require_once( WPSEO_PATH . 'admin/pages/xml-sitemaps.php' );
				break;

			case 'wpseo_licenses':
				require_once( WPSEO_PATH . 'admin/pages/licenses.php' );
				break;

			case 'wpseo_files':
				require_once( WPSEO_PATH . 'admin/views/tool-file-editor.php' );
				break;

			case 'wpseo_dashboard':
			default:
				require_once( WPSEO_PATH . 'admin/pages/dashboard.php' );
				break;
		}
	}


	/**
	 * Loads the form for the network configuration page.
	 */
	function network_config_page() {
		require_once( WPSEO_PATH . 'admin/pages/network.php' );
	}


	/**
	 * Adds the ability to choose how many posts are displayed per page
	 * on the bulk edit pages.
	 */
	function bulk_edit_options() {
		$option = 'per_page';
		$args   = array(
			'label'   => __( 'Posts', 'wordpress-seo' ),
			'default' => 10,
			'option'  => 'wpseo_posts_per_page',
		);
		add_screen_option( $option, $args );
	}

	/**
	 * Saves the posts per page limit for bulk edit pages.
	 *
	 * @param int    $status
	 * @param string $option
	 * @param int    $value
	 *
	 * @return int
	 */
	function save_bulk_edit_options( $status, $option, $value ) {
		if ( 'wpseo_posts_per_page' === $option && ( $value > 0 && $value < 1000 ) ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Display an error message when the blog is set to private.
	 */
	function blog_public_warning() {
		if ( ( function_exists( 'is_network_admin' ) && is_network_admin() ) || WPSEO_Utils::grant_access() !== true ) {
			return;
		}

		if ( $this->options['ignore_blog_public_warning'] === true ) {
			return;
		}
		printf( '
			<div id="robotsmessage" class="error">
				<p>
					<strong>%1$s</strong>
					%2$s
					<a href="javascript:wpseoSetIgnore(\'blog_public_warning\',\'robotsmessage\',\'%3$s\');" class="button">%4$s</a>
				</p>
			</div>',
			__( 'Huge SEO Issue: You\'re blocking access to robots.', 'wordpress-seo' ),
			sprintf( __( 'You must %sgo to your Reading Settings%s and uncheck the box for Search Engine Visibility.', 'wordpress-seo' ), sprintf( '<a href="%s">', esc_url( admin_url( 'options-reading.php' ) ) ), '</a>' ),
			esc_js( wp_create_nonce( 'wpseo-ignore' ) ),
			__( 'I know, don\'t bug me.', 'wordpress-seo' )
		);
	}

	/**
	 * Display an error message when the theme contains a meta description tag.
	 *
	 * @since 1.4.14
	 */
	function meta_description_warning() {
		if ( ( function_exists( 'is_network_admin' ) && is_network_admin() ) || WPSEO_Utils::grant_access() !== true ) {
			return;
		}

		// No need to double display it on the dashboard.
		if ( 'wpseo_dashboard' === filter_input( INPUT_GET, 'page' ) ) {
			return;
		}

		if ( true === $this->options['ignore_meta_description_warning'] ) {
			return;
		}

		printf( '
			<div id="metamessage" class="error">
				<p>
					<strong>%1$s</strong>
					%2$s
					<a href="javascript:wpseoSetIgnore(\'meta_description_warning\',\'metamessage\',\'%3$s\');" class="button">%4$s</a>
				</p>
			</div>',
			__( 'SEO Issue:', 'wordpress-seo' ),
			/* translators: %1$s expands to Yoast SEO, %2$s to opening anchor and %3$s the anchor closing tag */
			sprintf( __( 'Your theme contains a meta description, which blocks %1$s from working properly. Please visit the %2$sSEO Dashboard%3$s to fix this.', 'wordpress-seo' ), 'Yoast SEO', sprintf( '<a href="%s">', esc_url( admin_url( 'admin.php?page=wpseo_dashboard' ) ) ), '</a>' ),
			esc_js( wp_create_nonce( 'wpseo-ignore' ) ),
			__( 'I know, don\'t bug me.', 'wordpress-seo' )
		);
	}

	/**
	 * Add a link to the settings page to the plugins list
	 *
	 * @staticvar string $this_plugin holds the directory & filename for the plugin
	 *
	 * @param array  $links array of links for the plugins, adapted when the current plugin is found.
	 * @param string $file  the filename for the current plugin, which the filter loops through.
	 *
	 * @return array $links
	 */
	function add_action_link( $links, $file ) {
		if ( WPSEO_BASENAME === $file && WPSEO_Utils::grant_access() ) {
			$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=wpseo_dashboard' ) ) . '">' . __( 'Settings', 'wordpress-seo' ) . '</a>';
			array_unshift( $links, $settings_link );
		}

		if ( class_exists( 'Yoast_Product_WPSEO_Premium' ) ) {
			$license_manager = new Yoast_Plugin_License_Manager( new Yoast_Product_WPSEO_Premium() );
			if ( $license_manager->license_is_valid() ) {
				return $links;
			}
		}

		// Add link to premium support landing page.
		$premium_link = '<a href="https://yoast.com/wordpress/plugins/seo-premium/support/#utm_source=wordpress-seo-settings-link&amp;utm_medium=textlink&amp;utm_campaign=support-link">' . __( 'Premium Support', 'wordpress-seo' ) . '</a>';
		array_unshift( $links, $premium_link );

		// Add link to docs.
		$faq_link = '<a href="https://yoast.com/wordpress/plugins/seo/faq/">' . __( 'FAQ', 'wordpress-seo' ) . '</a>';
		array_unshift( $links, $faq_link );

		return $links;
	}

	/**
	 * Enqueues the (tiny) global JS needed for the plugin.
	 */
	function config_page_scripts() {
		if ( WPSEO_Utils::grant_access() ) {
			wp_enqueue_script( 'wpseo-admin-global-script', plugins_url( 'js/wp-seo-admin-global' . WPSEO_CSSJS_SUFFIX . '.js', WPSEO_FILE ), array( 'jquery' ), WPSEO_VERSION, true );
		}
	}

	/**
	 * Filter the $contactmethods array and add Facebook, Google+ and Twitter.
	 *
	 * These are used with the Facebook author, rel="author" and Twitter cards implementation.
	 *
	 * @param array $contactmethods currently set contactmethods.
	 *
	 * @return array $contactmethods with added contactmethods.
	 */
	public function update_contactmethods( $contactmethods ) {
		// Add Google+.
		$contactmethods['googleplus'] = __( 'Google+', 'wordpress-seo' );
		// Add Twitter.
		$contactmethods['twitter'] = __( 'Twitter username (without @)', 'wordpress-seo' );
		// Add Facebook.
		$contactmethods['facebook'] = __( 'Facebook profile URL', 'wordpress-seo' );

		return $contactmethods;
	}

	/**
	 * Cleans stopwords out of the slug, if the slug hasn't been set yet.
	 *
	 * @since 1.1.7
	 *
	 * @param string $slug if this isn't empty, the function will return an unaltered slug.
	 *
	 * @return string $clean_slug cleaned slug
	 */
	function remove_stopwords_from_slug( $slug ) {
		// Don't change an existing slug.
		if ( isset( $slug ) && $slug !== '' ) {
			return $slug;
		}

		if ( ! filter_input( INPUT_POST, 'post_title' ) ) {
			return $slug;
		}

		// Don't change slug if the post is a draft, this conflicts with polylang.
		if ( 'draft' == filter_input( INPUT_POST, 'post_status' ) ) {
			return $slug;
		}

		// Lowercase the slug and strip slashes.
		$clean_slug = sanitize_title( stripslashes( filter_input( INPUT_POST, 'post_title' ) ) );

		// Turn it to an array and strip stopwords by comparing against an array of stopwords.
		$clean_slug_array = array_diff( explode( '-', $clean_slug ), $this->stopwords() );

		// Don't change the slug if there are less than 3 words left.
		if ( count( $clean_slug_array ) < 3 ) {
			return $clean_slug;
		}

		// Turn the sanitized array into a string.
		$clean_slug = join( '-', $clean_slug_array );

		return $clean_slug;
	}

	/**
	 * Returns the stopwords for the current language
	 *
	 * @since 1.1.7
	 *
	 * @return array $stopwords array of stop words to check and / or remove from slug
	 */
	function stopwords() {
		/* translators: this should be an array of stopwords for your language, separated by comma's. */
		$stopwords = explode( ',', __( "a,about,above,after,again,against,all,am,an,and,any,are,as,at,be,because,been,before,being,below,between,both,but,by,could,did,do,does,doing,down,during,each,few,for,from,further,had,has,have,having,he,he'd,he'll,he's,her,here,here's,hers,herself,him,himself,his,how,how's,i,i'd,i'll,i'm,i've,if,in,into,is,it,it's,its,itself,let's,me,more,most,my,myself,nor,of,on,once,only,or,other,ought,our,ours,ourselves,out,over,own,same,she,she'd,she'll,she's,should,so,some,such,than,that,that's,the,their,theirs,them,themselves,then,there,there's,these,they,they'd,they'll,they're,they've,this,those,through,to,too,under,until,up,very,was,we,we'd,we'll,we're,we've,were,what,what's,when,when's,where,where's,which,while,who,who's,whom,why,why's,with,would,you,you'd,you'll,you're,you've,your,yours,yourself,yourselves", 'wordpress-seo' ) );

		/**
		 * Allows filtering of the stop words list
		 * Especially useful for users on a language in which WPSEO is not available yet
		 * and/or users who want to turn off stop word filtering
		 * @api  array  $stopwords  Array of all lowercase stopwords to check and/or remove from slug
		 */
		$stopwords = apply_filters( 'wpseo_stopwords', $stopwords );

		return $stopwords;
	}


	/**
	 * Check whether the stopword appears in the string
	 *
	 * @param string $haystack    The string to be checked for the stopword.
	 * @param bool   $checkingUrl Whether or not we're checking a URL.
	 *
	 * @return bool|mixed
	 */
	function stopwords_check( $haystack, $checkingUrl = false ) {
		$stopWords = $this->stopwords();

		if ( is_array( $stopWords ) && $stopWords !== array() ) {
			foreach ( $stopWords as $stopWord ) {
				// If checking a URL remove the single quotes.
				if ( $checkingUrl ) {
					$stopWord = str_replace( "'", '', $stopWord );
				}

				// Check whether the stopword appears as a whole word.
				// @todo [JRF => whomever] check whether the use of \b (=word boundary) would be more efficient ;-).
				$res = preg_match( "`(^|[ \n\r\t\.,'\(\)\"\+;!?:])" . preg_quote( $stopWord, '`' ) . "($|[ \n\r\t\.,'\(\)\"\+;!?:])`iu", $haystack );
				if ( $res > 0 ) {
					return $stopWord;
				}
			}
		}

		return false;
	}

	/**
	 * Log the updated timestamp for user profiles when theme is changed
	 */
	function switch_theme() {
		$users = get_users( array( 'who' => 'authors' ) );
		if ( is_array( $users ) && $users !== array() ) {
			foreach ( $users as $user ) {
				update_user_meta( $user->ID, '_yoast_wpseo_profile_updated', time() );
			}
		}
	}

	/********************** DEPRECATED METHODS **********************/

	/**
	 * Check whether the current user is allowed to access the configuration.
	 *
	 * @deprecated 1.5.0
	 * @deprecated use WPSEO_Utils::grant_access()
	 * @see        WPSEO_Utils::grant_access()
	 *
	 * @return boolean
	 */
	function grant_access() {
		_deprecated_function( __METHOD__, 'WPSEO 1.5.0', 'WPSEO_Utils::grant_access()' );

		return WPSEO_Utils::grant_access();
	}

	/**
	 * Check whether the current user is allowed to access the configuration.
	 *
	 * @deprecated 1.5.0
	 * @deprecated use wpseo_do_upgrade()
	 * @see        WPSEO_Upgrade
	 */
	function maybe_upgrade() {
		_deprecated_function( __METHOD__, 'WPSEO 1.5.0', 'wpseo_do_upgrade' );
		new WPSEO_Upgrade();
	}

	/**
	 * Clears the cache
	 *
	 * @deprecated 1.5.0
	 * @deprecated use WPSEO_Utils::clear_cache()
	 * @see        WPSEO_Utils::clear_cache()
	 */
	function clear_cache() {
		_deprecated_function( __METHOD__, 'WPSEO 1.5.0', 'WPSEO_Utils::clear_cache()' );
		WPSEO_Utils::clear_cache();
	}

	/**
	 * Clear rewrites
	 *
	 * @deprecated 1.5.0
	 * @deprecated use WPSEO_Utils::clear_rewrites()
	 * @see        WPSEO_Utils::clear_rewrites()
	 */
	function clear_rewrites() {
		_deprecated_function( __METHOD__, 'WPSEO 1.5.0', 'WPSEO_Utils::clear_rewrites()' );
		WPSEO_Utils::clear_rewrites();
	}

	/**
	 * Register all the options needed for the configuration pages.
	 *
	 * @deprecated 1.5.0
	 * @deprecated use WPSEO_Option::register_setting() on each individual option
	 * @see        WPSEO_Option::register_setting()
	 */
	function options_init() {
		_deprecated_function( __METHOD__, 'WPSEO 1.5.0', 'WPSEO_Option::register_setting()' );
	}



} /* End of class */

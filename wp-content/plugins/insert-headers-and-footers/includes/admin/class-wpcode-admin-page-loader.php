<?php
/**
 * Class used to load admin pages allowing child classes
 *  to replace or add pages by changing the classes used.
 *
 * @package WPCode
 */

/**
 * Class WPCode admin page loader.
 */
class WPCode_Admin_Page_Loader {

	/**
	 * Array of admin pages to load.
	 *
	 * @var array
	 */
	public $pages = array();

	/**
	 * Slugs of pages that should not be visible in the submenu.
	 *
	 * @var array
	 */
	public $hidden_pages = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->require_files();

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 9 );
		add_filter( 'plugin_action_links_' . WPCODE_PLUGIN_BASENAME, array( $this, 'add_plugin_action_links' ) );

		// Hide submenus.
		add_filter( 'parent_file', array( $this, 'hide_menus' ), 1020 );
	}

	/**
	 * Load required files for the admin pages.
	 *
	 * @return void
	 */
	public function require_files() {
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-headers-footers.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-code-snippets.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-snippet-manager.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-library.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-generator.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-tools.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-settings.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-click.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-pixel.php';
	}

	/**
	 * Load the pages classes allowing child classes to replace.
	 *
	 * @return void
	 */
	public function prepare_pages() {

		if ( $this->is_headers_footers_mode() ) {
			$this->pages['headers_footers'] = 'WPCode_Admin_Page_Headers_Footers';

			return;
		}

		$this->pages['code_snippets']   = 'WPCode_Admin_Page_Code_Snippets';
		$this->pages['snippet_manager'] = 'WPCode_Admin_Page_Snippet_Manager';
		$this->pages['headers_footers'] = 'WPCode_Admin_Page_Headers_Footers';
		$this->pages['pixel']           = 'WPCode_Admin_Page_Pixel';
		$this->pages['library']         = 'WPCode_Admin_Page_Library';
		$this->pages['generator']       = 'WPCode_Admin_Page_Generator';
		$this->pages['tools']           = 'WPCode_Admin_Page_Tools';
		$this->pages['settings']        = 'WPCode_Admin_Page_Settings';
		$this->pages['click']           = 'WPCode_Admin_Page_Click';
	}

	/**
	 * Load the pages using their specific classes.
	 *
	 * @return void
	 */
	public function load_pages() {

		$this->prepare_pages();

		do_action( 'wpcode_before_admin_pages_loaded', $this->pages );

		foreach ( $this->pages as $page_class ) {
			if ( ! class_exists( $page_class ) ) {
				continue;
			}
			/**
			 * @var WPCode_Admin_Page $new_page
			 */
			$new_page = new $page_class();
			if ( $new_page->hide_menu ) {
				$this->hidden_pages[] = $new_page->page_slug;
			}
		}
	}

	/**
	 * Add the main menu item used for all the other admin pages.
	 *
	 * @return void
	 */
	public function add_main_menu_item() {
		$svg         = get_wpcode_icon( 'logo', 36, 34, '-10 -6 80 80' );
		$wpcode_icon = 'data:image/svg+xml;base64,' . base64_encode( $svg ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		add_menu_page(
			__( 'Code Snippets', 'insert-headers-and-footers' ),
			__( 'Code Snippets', 'insert-headers-and-footers' ),
			'wpcode_edit_snippets',
			'wpcode',
			array(
				$this,
				'admin_menu_page',
			),
			$wpcode_icon,
			'81.45687234432916'
		);
	}

	/**
	 * Handler for registering the admin menu & loading pages.
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		if ( ! $this->is_headers_footers_mode() ) {
			$this->add_main_menu_item();
		}

		$this->load_pages();
	}

	/**
	 * Check if the site is in headers & footers only mode.
	 *
	 * @return mixed
	 */
	public function is_headers_footers_mode() {
		return wpcode()->settings->get_option( 'headers_footers_mode' );
	}

	/**
	 * Generic handler for the wpcode pages.
	 *
	 * @return void
	 */
	public function admin_menu_page() {
		do_action( 'wpcode_admin_page' );
	}

	/**
	 * Add a link to the code snippets list in the plugins list view.
	 *
	 * @param array $links The links specific to our plugin.
	 *
	 * @return array
	 */
	public function add_plugin_action_links( $links ) {
		$url  = add_query_arg(
			array(
				'page' => 'wpcode',
			),
			admin_url( 'admin.php' )
		);
		$text = esc_html__( 'Code Snippets', 'insert-headers-and-footers' );
		if ( wpcode()->settings->get_option( 'headers_footers_mode' ) ) {
			$url  = add_query_arg(
				array(
					'page' => 'wpcode-headers-footers',
				),
				admin_url( 'options-general.php' )
			);
			$text = esc_html__( 'Settings', 'insert-headers-and-footers' );
		}
		$custom = array();

		$custom['pro'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank" rel="noopener noreferrer" 
				style="color: #00a32a; font-weight: 700;" 
				onmouseover="this.style.color=\'#008a20\';" 
				onmouseout="this.style.color=\'#00a32a\';"
				>%3$s</a>',
			wpcode_utm_url(
				'https://wpcode.com/lite/',
				'all-plugins',
				'get-wpcode-pro'
			),
			esc_attr__( 'Upgrade to WPCode Pro', 'insert-headers-and-footers' ),
			esc_html__( 'Get WPCode Pro', 'insert-headers-and-footers' )
		);

		$custom['settings'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			$url,
			$text
		);

		return array_merge( $custom, $links );
	}

	/**
	 * Hide menu items for pages that should be hidden.
	 * We're using the parent_file filter to improve compatibility with admin-menu-editor.
	 *
	 * @param string $parent_file The parent file.
	 *
	 * @return string
	 */
	public function hide_menus( $parent_file ) {

		foreach ( $this->hidden_pages as $page ) {
			remove_submenu_page( 'wpcode', $page );
		}

		return $parent_file;
	}
}

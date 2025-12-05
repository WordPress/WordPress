<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Menu
 */

/**
 * Registers the regular admin menu and network admin menu implementations.
 */
class WPSEO_Menu implements WPSEO_WordPress_Integration {

	/**
	 * The page identifier used in WordPress to register the admin page.
	 *
	 * !DO NOT CHANGE THIS!
	 *
	 * @var string
	 */
	public const PAGE_IDENTIFIER = 'wpseo_dashboard';

	/**
	 * List of classes that add admin functionality.
	 *
	 * @var array
	 */
	protected $admin_features;

	/**
	 * Registers all hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		$admin_menu = new WPSEO_Admin_Menu( $this );
		$admin_menu->register_hooks();

		if ( WPSEO_Utils::is_plugin_network_active() ) {
			$network_admin_menu = new WPSEO_Network_Admin_Menu( $this );
			$network_admin_menu->register_hooks();
		}

		$capability_normalizer = new WPSEO_Submenu_Capability_Normalize();
		$capability_normalizer->register_hooks();
	}

	/**
	 * Returns the main menu page identifier.
	 *
	 * @return string Page identifier to use.
	 */
	public function get_page_identifier() {
		return self::PAGE_IDENTIFIER;
	}

	/**
	 * Loads the requested admin settings page.
	 *
	 * @return void
	 */
	public function load_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['page'] ) && is_string( $_GET['page'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			$page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
			$this->show_page( $page );
		}
	}

	/**
	 * Shows an admin settings page.
	 *
	 * @param string $page Page to display.
	 *
	 * @return void
	 */
	protected function show_page( $page ) {
		switch ( $page ) {
			case 'wpseo_tools':
				require_once WPSEO_PATH . 'admin/pages/tools.php';
				break;

			case 'wpseo_files':
				require_once WPSEO_PATH . 'admin/views/tool-file-editor.php';
				break;

			default:
				break;
		}
	}
}

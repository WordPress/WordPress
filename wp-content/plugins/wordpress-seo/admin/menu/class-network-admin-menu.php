<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Menu
 */

/**
 * Network Admin Menu handler.
 */
class WPSEO_Network_Admin_Menu extends WPSEO_Base_Menu {

	/**
	 * Registers all hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		// Needs the lower than default priority so other plugins can hook underneath it without issue.
		add_action( 'network_admin_menu', [ $this, 'register_settings_page' ], 5 );
	}

	/**
	 * Register the settings page for the Network settings.
	 *
	 * @return void
	 */
	public function register_settings_page() {
		if ( ! $this->check_manage_capability() ) {
			return;
		}

		add_menu_page(
			__( 'Network Settings', 'wordpress-seo' ) . ' - Yoast SEO',
			'Yoast SEO',
			$this->get_manage_capability(),
			$this->get_page_identifier(),
			[ $this, 'network_config_page' ],
			$this->get_icon_svg()
		);

		$submenu_pages = $this->get_submenu_pages();
		$this->register_submenu_pages( $submenu_pages );
	}

	/**
	 * Returns the list of registered submenu pages.
	 *
	 * @return array List of registered submenu pages.
	 */
	public function get_submenu_pages() {

		// Submenu pages.
		$submenu_pages = [
			$this->get_submenu_page(
				__( 'General', 'wordpress-seo' ),
				$this->get_page_identifier(),
				[ $this, 'network_config_page' ]
			),
		];

		if ( WPSEO_Utils::allow_system_file_edit() === true ) {
			$submenu_pages[] = $this->get_submenu_page( __( 'Edit Files', 'wordpress-seo' ), 'wpseo_files' );
		}

		/**
		 * Filter: 'wpseo_network_submenu_pages' - Collects all network submenus that need to be shown.
		 *
		 * @internal For internal Yoast SEO use only.
		 *
		 * @param array $submenu_pages List with all submenu pages.
		 */
		return (array) apply_filters( 'wpseo_network_submenu_pages', $submenu_pages );
	}

	/**
	 * Loads the form for the network configuration page.
	 *
	 * @return void
	 */
	public function network_config_page() {
		require_once WPSEO_PATH . 'admin/pages/network.php';
	}

	/**
	 * Checks whether the current user has capabilities to manage all options.
	 *
	 * @return bool True if capabilities are sufficient, false otherwise.
	 */
	protected function check_manage_capability() {
		return current_user_can( $this->get_manage_capability() );
	}

	/**
	 * Returns the capability that is required to manage all options.
	 *
	 * @return string Capability to check against.
	 */
	protected function get_manage_capability() {
		return 'wpseo_manage_network_options';
	}
}

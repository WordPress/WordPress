<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Menu
 */

/**
 * Registers the admin menu on the left of the admin area.
 */
class WPSEO_Admin_Menu extends WPSEO_Base_Menu {

	/**
	 * Registers all hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		// Needs the lower than default priority so other plugins can hook underneath it without issue.
		add_action( 'admin_menu', [ $this, 'register_settings_page' ], 5 );
	}

	/**
	 * Registers the menu item submenus.
	 *
	 * @return void
	 */
	public function register_settings_page() {
		$manage_capability   = $this->get_manage_capability();
		$page_identifier     = $this->get_page_identifier();
		$admin_page_callback = $this->get_admin_page_callback();

		// Get all submenu pages.
		$submenu_pages = $this->get_submenu_pages();

		foreach ( $submenu_pages as $submenu_page ) {
			if ( WPSEO_Capability_Utils::current_user_can( $submenu_page[3] ) ) {
				$manage_capability   = $submenu_page[3];
				$page_identifier     = $submenu_page[4];
				$admin_page_callback = $submenu_page[5];
				break;
			}
		}

		foreach ( $submenu_pages as $index => $submenu_page ) {
			$submenu_pages[ $index ][0] = $page_identifier;
		}

		/*
		 * The current user has the capability to control anything.
		 * This means that all submenus and dashboard can be shown.
		 */
		global $admin_page_hooks;

		add_menu_page(
			'Yoast SEO: ' . __( 'Dashboard', 'wordpress-seo' ),
			'Yoast SEO ' . $this->get_notification_counter(),
			$manage_capability,
			$page_identifier,
			$admin_page_callback,
			$this->get_icon_svg(),
			99
		);

		// Wipe notification bits from hooks.
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride -- This is a deliberate action.
		$admin_page_hooks[ $page_identifier ] = 'seo';

		// Add submenu items to the main menu if possible.
		$this->register_submenu_pages( $submenu_pages );
	}

	/**
	 * Returns the list of registered submenu pages.
	 *
	 * @return array List of registered submenu pages.
	 */
	public function get_submenu_pages() {
		global $wpseo_admin;

		$search_console_callback = null;

		// Account for when the available submenu pages are requested from outside the admin.
		if ( isset( $wpseo_admin ) ) {
			$google_search_console   = new WPSEO_GSC();
			$search_console_callback = [ $google_search_console, 'display' ];
		}

		// Submenu pages.
		$submenu_pages = [
			$this->get_submenu_page(
				__( 'Search Console', 'wordpress-seo' ),
				'wpseo_search_console',
				$search_console_callback
			),
			$this->get_submenu_page( __( 'Tools', 'wordpress-seo' ), 'wpseo_tools' ),
		];

		/**
		 * Filter: 'wpseo_submenu_pages' - Collects all submenus that need to be shown.
		 *
		 * @param array $submenu_pages List with all submenu pages.
		 */
		return (array) apply_filters( 'wpseo_submenu_pages', $submenu_pages );
	}

	/**
	 * Returns the notification count in HTML format.
	 *
	 * @return string The notification count in HTML format.
	 */
	protected function get_notification_counter() {
		$notification_center = Yoast_Notification_Center::get();
		$notification_count  = $notification_center->get_notification_count();

		// Add main page.
		/* translators: Hidden accessibility text; %s: number of notifications. */
		$notifications = sprintf( _n( '%s notification', '%s notifications', $notification_count, 'wordpress-seo' ), number_format_i18n( $notification_count ) );

		return sprintf( '<span class="update-plugins count-%1$d"><span class="plugin-count" aria-hidden="true">%1$d</span><span class="screen-reader-text">%2$s</span></span>', $notification_count, $notifications );
	}

	/**
	 * Returns the capability that is required to manage all options.
	 *
	 * @return string Capability to check against.
	 */
	protected function get_manage_capability() {
		return 'wpseo_manage_options';
	}
}

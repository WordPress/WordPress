<?php
/**
 * Lite-specific admin page loader.
 * Extends the default pages with lite-specific items.
 *
 * @package WPCode
 */

/**
 * Class WPCode_Admin_Page_Loader_Lite.
 */
class WPCode_Admin_Page_Loader_Lite extends WPCode_Admin_Page_Loader {

	/**
	 * Load menu items from parent class and add class-specific ones.
	 *
	 * @return void
	 */
	public function hooks() {
		parent::hooks();

		add_action( 'admin_menu', array( $this, 'add_upgrade_menu_item' ), 40 );
		add_action( 'admin_head', array( $this, 'adjust_pro_menu_item_class' ) );
		add_action( 'admin_head', array( $this, 'admin_menu_styles' ), 11 );
	}

	/**
	 * Add lite-specific upgrade to pro menu item.
	 *
	 * @return void
	 */
	public function add_upgrade_menu_item() {
		add_submenu_page(
			'wpcode',
			esc_html__( 'Upgrade to Pro', 'insert-headers-and-footers' ),
			esc_html__( 'Upgrade to Pro', 'insert-headers-and-footers' ),
			'wpcode_edit_snippets',
			esc_url( wpcode_utm_url( 'https://wpcode.com/lite/', 'wpcode-admin', 'admin-side-menu' ) )
		);
	}

	/**
	 * Add the PRO badge to left sidebar menu item.
	 *
	 * @since 1.7.8
	 */
	public function adjust_pro_menu_item_class() {

		global $submenu;

		// Bail if plugin menu is not registered.
		if ( ! isset( $submenu['wpcode'] ) ) {
			return;
		}

		$upgrade_link_position = key(
			array_filter(
				$submenu['wpcode'],
				static function ( $item ) {
					return strpos( $item[2], 'https://wpcode.com/lite' ) !== false;
				}
			)
		);

		// Bail if "Upgrade to Pro" menu item is not registered.
		if ( is_null( $upgrade_link_position ) ) {
			return;
		}

		$screen = get_current_screen();
		// Let's make sure we have an ID and the link is set in the menu.
		if ( isset( $screen->id ) && isset( $submenu['wpcode'][ $upgrade_link_position ][2] ) ) {
			// Let's clean up the screen id a bit.
			$screen_id = str_replace(
				array(
					'code-snippets_page_',
					'toplevel_page_',
				),
				'',
				$screen->id
			);

			$submenu['wpcode'][ $upgrade_link_position ][2] = str_replace( 'wpcode-admin', $screen_id, $submenu['wpcode'][ $upgrade_link_position ][2] );
		}

		// Prepare a HTML class.
		// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
		if ( isset( $submenu['wpcode'][ $upgrade_link_position ][4] ) ) {
			$submenu['wpcode'][ $upgrade_link_position ][4] .= ' wpcode-sidebar-upgrade-pro';
		} else {
			$submenu['wpcode'][ $upgrade_link_position ][] = 'wpcode-sidebar-upgrade-pro';
		}
		// phpcs:enable WordPress.WP.GlobalVariablesOverride.Prohibited
	}


	/**
	 * Output inline styles for the admin menu.
	 */
	public function admin_menu_styles() {
		$styles = 'a.wpcode-sidebar-upgrade-pro { background-color: #59A56D !important; color: #fff !important; font-weight: 600 !important; }';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		printf( '<style>%s</style>', $styles );
	}
}

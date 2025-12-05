<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Menu
 */

/**
 * Normalize submenu capabilities to `wpseo_manage_options`.
 */
class WPSEO_Submenu_Capability_Normalize implements WPSEO_WordPress_Integration {

	/**
	 * Registers all hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'wpseo_submenu_pages', [ $this, 'normalize_submenus_capability' ] );
	}

	/**
	 * Normalizes any `manage_options` to `wpseo_manage_options`.
	 *
	 * This is needed as the module plugins are not updated with the new capabilities directly,
	 * but they should not be shown as main menu items.
	 *
	 * @param array $submenu_pages List of subpages to convert.
	 *
	 * @return array Converted subpages.
	 */
	public function normalize_submenus_capability( $submenu_pages ) {
		foreach ( $submenu_pages as $index => $submenu_page ) {
			if ( $submenu_page[3] === 'manage_options' ) {
				$submenu_pages[ $index ][3] = 'wpseo_manage_options';
			}
		}

		return $submenu_pages;
	}
}

<?php
/**
 * Upgrader API: Bulk_Plugin_Upgrader_Skin class
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 4.6.0
 */

/**
 * Bulk Theme Upgrader Skin for WordPress Theme Upgrades.
 *
 * @since 3.0.0
 * @since 4.6.0 Moved to its own file from wp-admin/includes/class-wp-upgrader-skins.php.
 *
 * @see Bulk_Upgrader_Skin
 */
class Bulk_Theme_Upgrader_Skin extends Bulk_Upgrader_Skin {
	public $theme_info = array(); // Theme_Upgrader::bulk_upgrade() will fill this in.

	public function add_strings() {
		parent::add_strings();
		$this->upgrader->strings['skin_before_update_header'] = __( 'Updating Theme %1$s (%2$d/%3$d)' );
	}

	/**
	 * @param string $title
	 */
	public function before( $title = '' ) {
		parent::before( $this->theme_info->display( 'Name' ) );
	}

	/**
	 * @param string $title
	 */
	public function after( $title = '' ) {
		parent::after( $this->theme_info->display( 'Name' ) );
		$this->decrement_update_count( 'theme' );
	}

	/**
	 */
	public function bulk_footer() {
		parent::bulk_footer();
		$update_actions = array(
			'themes_page'  => '<a href="' . self_admin_url( 'themes.php' ) . '" target="_parent">' . __( 'Return to Themes page' ) . '</a>',
			'updates_page' => '<a href="' . self_admin_url( 'update-core.php' ) . '" target="_parent">' . __( 'Return to WordPress Updates page' ) . '</a>',
		);
		if ( ! current_user_can( 'switch_themes' ) && ! current_user_can( 'edit_theme_options' ) ) {
			unset( $update_actions['themes_page'] );
		}

		/**
		 * Filters the list of action links available following bulk theme updates.
		 *
		 * @since 3.0.0
		 *
		 * @param array    $update_actions Array of theme action links.
		 * @param WP_Theme $theme_info     Theme object for the last-updated theme.
		 */
		$update_actions = apply_filters( 'update_bulk_theme_complete_actions', $update_actions, $this->theme_info );

		if ( ! empty( $update_actions ) ) {
			$this->feedback( implode( ' | ', (array) $update_actions ) );
		}
	}
}

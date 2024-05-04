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

	/**
	 * Theme info.
	 *
	 * The Theme_Upgrader::bulk_upgrade() method will fill this in
	 * with info retrieved from the Theme_Upgrader::theme_info() method,
	 * which in turn calls the wp_get_theme() function.
	 *
	 * @since 3.0.0
	 * @var WP_Theme|false The theme's info object, or false.
	 */
	public $theme_info = false;

	/**
	 * Sets up the strings used in the update process.
	 *
	 * @since 3.0.0
	 */
	public function add_strings() {
		parent::add_strings();
		/* translators: 1: Theme name, 2: Number of the theme, 3: Total number of themes being updated. */
		$this->upgrader->strings['skin_before_update_header'] = __( 'Updating Theme %1$s (%2$d/%3$d)' );
	}

	/**
	 * Performs an action before a bulk theme update.
	 *
	 * @since 3.0.0
	 *
	 * @param string $title
	 */
	public function before( $title = '' ) {
		parent::before( $this->theme_info->display( 'Name' ) );
	}

	/**
	 * Performs an action following a bulk theme update.
	 *
	 * @since 3.0.0
	 *
	 * @param string $title
	 */
	public function after( $title = '' ) {
		parent::after( $this->theme_info->display( 'Name' ) );
		$this->decrement_update_count( 'theme' );
	}

	/**
	 * Displays the footer following the bulk update process.
	 *
	 * @since 3.0.0
	 */
	public function bulk_footer() {
		parent::bulk_footer();

		$update_actions = array(
			'themes_page'  => sprintf(
				'<a href="%s" target="_parent">%s</a>',
				self_admin_url( 'themes.php' ),
				__( 'Go to Themes page' )
			),
			'updates_page' => sprintf(
				'<a href="%s" target="_parent">%s</a>',
				self_admin_url( 'update-core.php' ),
				__( 'Go to WordPress Updates page' )
			),
		);

		if ( ! current_user_can( 'switch_themes' ) && ! current_user_can( 'edit_theme_options' ) ) {
			unset( $update_actions['themes_page'] );
		}

		/**
		 * Filters the list of action links available following bulk theme updates.
		 *
		 * @since 3.0.0
		 *
		 * @param string[] $update_actions Array of theme action links.
		 * @param WP_Theme $theme_info     Theme object for the last-updated theme.
		 */
		$update_actions = apply_filters( 'update_bulk_theme_complete_actions', $update_actions, $this->theme_info );

		if ( ! empty( $update_actions ) ) {
			$this->feedback( implode( ' | ', (array) $update_actions ) );
		}
	}
}

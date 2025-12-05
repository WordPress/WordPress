<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * A WordPress integration that listens for whether the SEO changes have been saved successfully.
 */
class WPSEO_Admin_Settings_Changed_Listener implements WPSEO_WordPress_Integration {

	/**
	 * Have the Yoast SEO settings been saved.
	 *
	 * @var bool
	 */
	private static $settings_saved = false;

	/**
	 * Registers all hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_init', [ $this, 'intercept_save_update_notification' ] );
	}

	/**
	 * Checks and overwrites the wp_settings_errors global to determine whether the Yoast SEO settings have been saved.
	 *
	 * @return void
	 */
	public function intercept_save_update_notification() {
		global $pagenow;

		if ( $pagenow !== 'admin.php' || ! YoastSEO()->helpers->current_page->is_yoast_seo_page() ) {
			return;
		}

		// Variable name is the same as the global that is set by get_settings_errors.
		$wp_settings_errors = get_settings_errors();

		foreach ( $wp_settings_errors as $key => $wp_settings_error ) {
			if ( ! $this->is_settings_updated_notification( $wp_settings_error ) ) {
				continue;
			}

			self::$settings_saved = true;
			unset( $wp_settings_errors[ $key ] );
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride -- Overwrite the global with the list excluding the Changed saved message.
			$GLOBALS['wp_settings_errors'] = $wp_settings_errors;
			break;
		}
	}

	/**
	 * Checks whether the settings notification is a settings_updated notification.
	 *
	 * @param array $wp_settings_error The settings object.
	 *
	 * @return bool Whether this is a settings updated settings notification.
	 */
	public function is_settings_updated_notification( $wp_settings_error ) {
		return ! empty( $wp_settings_error['code'] ) && $wp_settings_error['code'] === 'settings_updated';
	}

	/**
	 * Get whether the settings have successfully been saved
	 *
	 * @return bool Whether the settings have successfully been saved.
	 */
	public function have_settings_been_saved() {
		return self::$settings_saved;
	}

	/**
	 * Renders a success message if the Yoast SEO settings have been saved.
	 *
	 * @return void
	 */
	public function show_success_message() {
		if ( $this->have_settings_been_saved() ) {
			echo '<p class="wpseo-message"><span class="dashicons dashicons-yes"></span>',
				esc_html__( 'Settings saved.', 'wordpress-seo' ),
				'</p>';
		}
	}
}

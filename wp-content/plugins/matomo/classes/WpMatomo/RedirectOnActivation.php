<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo;

use WpMatomo\Admin\TrackingSettings;

class RedirectOnActivation {
	/**
	 * @var Settings
	 */
	public static $settings;

	public function __construct() {
		self::$settings = new Settings();
	}

	public function register_hooks() {
		register_activation_hook( MATOMO_ANALYTICS_FILE, [ $this, 'matomo_activate' ] );
		add_action( 'admin_init', [ $this, 'matomo_plugin_redirect' ] );
	}

	public function matomo_activate() {
		add_option( 'matomo_plugin_do_activation_redirect', true );
	}

	public function matomo_plugin_redirect() {
		if ( get_option( 'matomo_plugin_do_activation_redirect', false ) ) {
			delete_option( 'matomo_plugin_do_activation_redirect' );
			$this->redirect_to_getting_started();
		}
	}

	/**
	 * We don't test the result of the wp_redirect method and we silent this method
	 * as this method will not work during unit tests.
	 * We just return if yes or no we should redirect
	 *
	 * @see https://github.com/matomo-org/matomo-for-wordpress/issues/434
	 * @return boolean
	 */
	public function redirect_to_getting_started() {
		$redirect = false;
		if ( ! isset( $_GET['activate-multi'] ) ) {
			if (
				( self::$settings->get_global_option( Settings::SHOW_GET_STARTED_PAGE ) === 1 ) &&
				( self::$settings->get_global_option( 'track_mode' ) === TrackingSettings::TRACK_MODE_DISABLED )
			) {
				$redirect = true;
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				@wp_safe_redirect( admin_url( 'admin.php?page=matomo-get-started' ) );
			}
		}

		return $redirect;
	}
}

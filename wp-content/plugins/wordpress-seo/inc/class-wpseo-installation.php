<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internals
 * @since   3.6
 */

/**
 * This class checks if the wpseo option doesn't exists. In the case it doesn't it will set a property that is
 * accessible via a method to check if the installation is fresh.
 */
class WPSEO_Installation {

	/**
	 * Checks if Yoast SEO is installed for the first time.
	 */
	public function __construct() {
		$is_first_install = $this->is_first_install();

		if ( $is_first_install && WPSEO_Utils::is_api_available() ) {
			add_action( 'wpseo_activate', [ $this, 'set_first_install_options' ] );
		}
	}

	/**
	 * When the option doesn't exist, it should be a new install.
	 *
	 * @return bool
	 */
	private function is_first_install() {
		return ( get_option( 'wpseo' ) === false );
	}

	/**
	 * Sets the options on first install for showing the installation notice and disabling of the settings pages.
	 *
	 * @return void
	 */
	public function set_first_install_options() {
		$options = get_option( 'wpseo' );

		$options['show_onboarding_notice'] = true;
		$options['first_activated_on']     = time();
		$options['first_activated_by']     = get_current_user_id();

		update_option( 'wpseo', $options );
	}
}

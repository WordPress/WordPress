<?php
/**
 * A compatibility layer for some of the most popular plugins.
 *
 * @package Two_Factor
 */

/**
 * A compatibility layer for some of the most popular plugins.
 *
 * Should be used with care because ideally we wouldn't need
 * any integration specific code for this plugin. Everything should
 * be handled through clever use of hooks and best practices.
 */
class Two_Factor_Compat {
	/**
	 * Initialize all the custom hooks as necessary.
	 *
	 * @return void
	 */
	public function init() {
		/**
		 * Jetpack
		 *
		 * @see https://wordpress.org/plugins/jetpack/
		 */
		add_filter( 'two_factor_rememberme', array( $this, 'jetpack_rememberme' ) );
	}

	/**
	 * Jetpack single sign-on wants long-lived sessions for users.
	 *
	 * @param boolean $rememberme Current state of the "remember me" toggle.
	 *
	 * @return boolean
	 */
	public function jetpack_rememberme( $rememberme ) {
		$action = filter_input( INPUT_GET, 'action', FILTER_CALLBACK, array( 'options' => 'sanitize_key' ) );

		if ( 'jetpack-sso' === $action && $this->jetpack_is_sso_active() ) {
			return true;
		}

		return $rememberme;
	}

	/**
	 * Helper to detect the presence of the active SSO module.
	 *
	 * @return boolean
	 */
	public function jetpack_is_sso_active() {
		return ( class_exists( 'Jetpack' ) && method_exists( 'Jetpack', 'is_module_active' ) && Jetpack::is_module_active( 'sso' ) );
	}
}

<?php
/**
 * Proxy connection interface
 *
 * @package Requests
 * @subpackage Proxy
 * @since 1.6
 */

/**
 * Proxy connection interface
 *
 * Implement this interface to handle proxy settings and authentication
 *
 * Parameters should be passed via the constructor where possible, as this
 * makes it much easier for users to use your provider.
 *
 * @see Requests_Hooks
 * @package Requests
 * @subpackage Proxy
 * @since 1.6
 */
interface Requests_Proxy {
	/**
	 * Register hooks as needed
	 *
	 * This method is called in {@see Requests::request} when the user has set
	 * an instance as the 'auth' option. Use this callback to register all the
	 * hooks you'll need.
	 *
	 * @see Requests_Hooks::register
	 * @param Requests_Hooks $hooks Hook system
	 */
	public function register(Requests_Hooks &$hooks);
}
<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Listeners
 */

/**
 * Dictates the required methods for a Listener implementation.
 */
interface WPSEO_Listener {

	/**
	 * Listens to an argument in the request URL and triggers an action.
	 *
	 * @return void
	 */
	public function listen();
}

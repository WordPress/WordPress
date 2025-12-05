<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\admin\google_search_console
 */

/**
 * Class WPSEO_GSC.
 */
class WPSEO_GSC {

	/**
	 * The option where data will be stored.
	 *
	 * @var string
	 */
	public const OPTION_WPSEO_GSC = 'wpseo-gsc';

	/**
	 * Outputs the HTML for the redirect page.
	 *
	 * @return void
	 */
	public function display() {
		require_once WPSEO_PATH . 'admin/google_search_console/views/gsc-display.php';
	}
}

<?php

namespace Yoast\WP\SEO\Integrations;

use Yoast\WP\SEO\Conditionals\No_Conditionals;

/**
 * Class to manage the integration with the WP uninstall flow.
 */
class Uninstall_Integration implements Integration_Interface {

	use No_Conditionals;

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'uninstall_' . \WPSEO_BASENAME, [ $this, 'wpseo_uninstall' ] );
	}

	/**
	 * Performs all necessary actions that should happen upon plugin uninstall.
	 *
	 * @return void
	 */
	public function wpseo_uninstall() {
		$this->clear_import_statuses();
	}

	/**
	 * Clears the persistent import statuses.
	 *
	 * @return void
	 */
	public function clear_import_statuses() {
		$yoast_options = \get_site_option( 'wpseo' );

		if ( isset( $yoast_options['importing_completed'] ) ) {
			$yoast_options['importing_completed'] = [];

			\update_site_option( 'wpseo', $yoast_options );
		}
	}
}

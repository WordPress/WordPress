<?php

namespace Yoast\WP\SEO\Helpers;

/**
 * A helper object for site environment.
 */
class Environment_Helper {

	/**
	 * Determines if the site is running on production.
	 *
	 * @return bool True if WordPress is currently running on production, false for all other environments.
	 */
	public function is_production_mode() {
		// phpcs:ignore Yoast.NamingConventions.ValidHookName.WrongPrefix -- existing hook.
		$override = \apply_filters( 'yoast_seo_development_mode', false );
		if ( $override ) {
			return true;
		}

		return $this->get_wp_environment() === 'production';
	}

	/**
	 * Determines on which environment WordPress is running.
	 *
	 * @return string The current WordPress environment.
	 */
	public function get_wp_environment() {
		return \wp_get_environment_type();
	}
}

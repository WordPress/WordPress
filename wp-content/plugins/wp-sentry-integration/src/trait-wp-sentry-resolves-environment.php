<?php

/**
 * @internal This class is not part of the public API and may be removed or changed at any time.
 */
trait WP_Sentry_Resolve_Environment {
	/**
	 * Retrieve the current environment name from user config or WordPress API.
	 *
	 * @return string
	 */
	protected function get_environment(): string {
		$environment = defined( 'WP_SENTRY_ENV' ) ? WP_SENTRY_ENV : null;

		if ( $environment === null && function_exists( 'wp_get_environment_type' ) ) {
			$environment = wp_get_environment_type();
		} elseif ( $environment === null && defined( 'WP_ENVIRONMENT_TYPE' ) ) {
			$wp_environments = [
				'local',
				'development',
				'staging',
				'production',
			];

			// Make sure the environment is an allowed one, and not accidentally set to an invalid value.
			// This mimicks the behavior of wp_get_environment_type().
			if ( in_array( WP_ENVIRONMENT_TYPE, $wp_environments, true ) ) {
				$environment = WP_ENVIRONMENT_TYPE;
			}
		}

		return $environment ?? 'unspecified';
	}
}

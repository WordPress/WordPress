<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Services\Health_Check\Health_Check;

/**
 * Integrates health checks with WordPress' Site Health.
 */
class Health_Check_Integration implements Integration_Interface {

	/**
	 * Contains all the health check implementations.
	 *
	 * @var Health_Check[]
	 */
	private $health_checks = [];

	/**
	 * Uses the dependency injection container to obtain all available implementations of the Health_Check interface.
	 *
	 * @param  Health_Check ...$health_checks The available health checks implementations.
	 */
	public function __construct( Health_Check ...$health_checks ) {
		$this->health_checks = $health_checks;
	}

	/**
	 * Hooks the health checks into WordPress' site status tests.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'site_status_tests', [ $this, 'add_health_checks' ] );
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * In this case: only when on an admin page.
	 *
	 * @return array The conditionals.
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Checks if the input is a WordPress site status tests array, and adds Yoast's health checks if it is.
	 *
	 * @param  string[] $tests Array containing WordPress site status tests.
	 * @return string[] Array containing WordPress site status tests with Yoast's health checks.
	 */
	public function add_health_checks( $tests ) {
		if ( ! $this->is_valid_site_status_tests_array( $tests ) ) {
			return $tests;
		}

		return $this->add_health_checks_to_site_status_tests( $tests );
	}

	/**
	 * Checks if the input array is a WordPress site status tests array.
	 *
	 * @param  mixed $tests Array to check.
	 * @return bool Returns true if the input array is a WordPress site status tests array.
	 */
	private function is_valid_site_status_tests_array( $tests ) {
		if ( ! \is_array( $tests ) ) {
			return false;
		}

		if ( ! \array_key_exists( 'direct', $tests ) ) {
			return false;
		}

		if ( ! \is_array( $tests['direct'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Adds the health checks to WordPress' site status tests.
	 *
	 * @param  string[] $tests Array containing WordPress site status tests.
	 * @return string[] Array containing WordPress site status tests with Yoast's health checks.
	 */
	private function add_health_checks_to_site_status_tests( $tests ) {
		foreach ( $this->health_checks as $health_check ) {
			if ( $health_check->is_excluded() ) {
				continue;
			}

			$tests['direct'][ $health_check->get_test_identifier() ] = [
				'test' => [ $health_check, 'run_and_get_result' ],
			];
		}

		return $tests;
	}
}

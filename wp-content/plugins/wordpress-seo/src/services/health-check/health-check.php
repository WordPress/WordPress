<?php

namespace Yoast\WP\SEO\Services\Health_Check;

/**
 * Abstract class for all health checks. Provides a uniform interface for the Health_Check_Integration.
 */
abstract class Health_Check {

	/**
	 * The prefix to add to the test identifier. Used to differentiate between Yoast's health checks, and other health checks.
	 */
	public const TEST_IDENTIFIER_PREFIX = 'yoast-';

	/**
	 * The object that runs the actual health check.
	 *
	 * @var Runner_Interface
	 */
	private $runner;

	/**
	 * The health check implementation sets the runner so this class can start a health check.
	 *
	 * @param  Runner_Interface $runner The health check runner.
	 * @return void
	 */
	protected function set_runner( $runner ) {
		$this->runner = $runner;
	}

	/**
	 * Returns the identifier of health check implementation. WordPress needs this to manage the health check (https://developer.wordpress.org/reference/hooks/site_status_tests/).
	 *
	 * @return string The identifier that WordPress requires.
	 */
	public function get_test_identifier() {
		$full_class_name            = static::class;
		$class_name_backslash_index = \strrpos( $full_class_name, '\\' );

		$class_name = $full_class_name;
		if ( $class_name_backslash_index ) {
			$class_name_index = ( $class_name_backslash_index + 1 );
			$class_name       = \substr( $full_class_name, $class_name_index );
		}

		$lowercase            = \strtolower( $class_name );
		$whitespace_as_dashes = \str_replace( '_', '-', $lowercase );
		$with_prefix          = self::TEST_IDENTIFIER_PREFIX . $whitespace_as_dashes;
		return $with_prefix;
	}

	/**
	 * Runs the health check, and returns its result in the format that WordPress requires to show the results to the user (https://developer.wordpress.org/reference/hooks/site_status_test_result/).
	 *
	 * @return string[] The array containing a WordPress site status report.
	 */
	public function run_and_get_result() {
		$this->runner->run();
		return $this->get_result();
	}

	/**
	 * Gets the result from the health check implementation.
	 *
	 * @return string[] The array containing a WordPress site status report.
	 */
	abstract protected function get_result();

	/**
	 * Returns whether the health check should be excluded from the results.
	 *
	 * @return bool Whether the check should be excluded.
	 */
	abstract public function is_excluded();
}

<?php

namespace Yoast\WP\SEO\Services\Health_Check;

/**
 * Interface for the health check runner. The abstract Health_Check uses this to run a health check.
 */
interface Runner_Interface {

	/**
	 * Runs the health check.
	 *
	 * @return void
	 */
	public function run();
}

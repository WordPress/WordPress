<?php

namespace Yoast\WP\SEO\Services\Health_Check;

/**
 * Creates Report_Builder instances.
 */
class Report_Builder_Factory {

	/**
	 * Creates a new Report_Builder instance.
	 *
	 * @param string $test_identifier The test identifier as a string.
	 * @return Report_Builder The new Report_Builder instance.
	 */
	public function create( $test_identifier ) {
		$instance = new Report_Builder();
		return $instance->set_test_identifier( $test_identifier );
	}
}

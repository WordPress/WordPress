<?php

namespace Yoast\WP\SEO\Services\Health_Check;

/**
 * Used by classes that use a health check Report_Builder.
 */
trait Reports_Trait {

	/**
	 * The factory for the builder object that generates WordPress-friendly test results.
	 *
	 * @var Report_Builder_Factory
	 */
	private $report_builder_factory;

	/**
	 * The test identifier that's set on the Report_Builder.
	 *
	 * @var string
	 */
	private $test_identifier = '';

	/**
	 * Sets the name that WordPress uses to identify this health check.
	 *
	 * @param  string $test_identifier The identifier.
	 * @return void
	 */
	public function set_test_identifier( $test_identifier ) {
		$this->test_identifier = $test_identifier;
	}

	/**
	 * Returns a new Report_Builder instance using the set test identifier.
	 *
	 * @return Report_Builder
	 */
	private function get_report_builder() {
		return $this->report_builder_factory->create( $this->test_identifier );
	}
}

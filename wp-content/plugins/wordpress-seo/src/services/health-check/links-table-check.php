<?php

namespace Yoast\WP\SEO\Services\Health_Check;

use Yoast\WP\SEO\Conditionals\Should_Index_Links_Conditional;

/**
 * Passes when the links table is accessible.
 */
class Links_Table_Check extends Health_Check {

	/**
	 * Runs the health check.
	 *
	 * @var Links_Table_Runner
	 */
	private $runner;

	/**
	 * Generates WordPress-friendly health check results.
	 *
	 * @var Links_Table_Reports
	 */
	private $reports;

	/**
	 * The conditional that checks if the links table should be indexed.
	 *
	 * @var Should_Index_Links_Conditional
	 */
	private $should_index_links_conditional;

	/**
	 * Constructor.
	 *
	 * @param Links_Table_Runner             $runner                         The object that implements the actual health check.
	 * @param Links_Table_Reports            $reports                        The object that generates WordPress-friendly results.
	 * @param Should_Index_Links_Conditional $should_index_links_conditional The conditional that checks if the links table should be indexed.
	 */
	public function __construct(
		Links_Table_Runner $runner,
		Links_Table_Reports $reports,
		Should_Index_Links_Conditional $should_index_links_conditional
	) {
		$this->runner                         = $runner;
		$this->reports                        = $reports;
		$this->should_index_links_conditional = $should_index_links_conditional;

		$this->reports->set_test_identifier( $this->get_test_identifier() );
		$this->set_runner( $this->runner );
	}

	/**
	 * Returns the WordPress-friendly health check result.
	 *
	 * @return string[] The WordPress-friendly health check result.
	 */
	protected function get_result() {
		if ( $this->runner->is_successful() ) {
			return $this->reports->get_success_result();
		}

		return $this->reports->get_links_table_not_accessible_result();
	}

	/**
	 * Returns whether the health check should be excluded from the results.
	 *
	 * @return bool false, because it's not excluded.
	 */
	public function is_excluded() {
		return ! $this->should_index_links_conditional->is_met();
	}
}

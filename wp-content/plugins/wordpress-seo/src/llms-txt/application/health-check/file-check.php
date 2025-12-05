<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Application\Health_Check;

use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Llms_Txt\User_Interface\Health_Check\File_Reports;
use Yoast\WP\SEO\Services\Health_Check\Health_Check;

/**
 * Fails when the llms.txt file fails to be generated.
 */
class File_Check extends Health_Check {

	/**
	 * Runs the health check.
	 *
	 * @var File_Runner
	 */
	private $runner;

	/**
	 * Generates WordPress-friendly health check results.
	 *
	 * @var File_Reports
	 */
	private $reports;

	/**
	 * The Options_Helper instance.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Constructor.
	 *
	 * @param  File_Runner    $runner         The object that implements the actual health check.
	 * @param  File_Reports   $reports        The object that generates WordPress-friendly results.
	 * @param  Options_Helper $options_helper The options helper.
	 */
	public function __construct(
		File_Runner $runner,
		File_Reports $reports,
		Options_Helper $options_helper
	) {
		$this->runner         = $runner;
		$this->reports        = $reports;
		$this->options_helper = $options_helper;

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

		return $this->reports->get_generation_failure_result( $this->runner->get_generation_failure_reason() );
	}

	/**
	 * Returns true when the llms.txt feature is disabled.
	 *
	 * @return bool Whether the health check should be excluded from the results.
	 */
	public function is_excluded() {
		return $this->options_helper->get( 'enable_llms_txt', false ) !== true;
	}
}

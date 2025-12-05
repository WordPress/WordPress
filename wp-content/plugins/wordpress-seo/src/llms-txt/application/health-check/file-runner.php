<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Application\Health_Check;

use Yoast\WP\SEO\Llms_Txt\Application\File\Commands\Populate_File_Command_Handler;
use Yoast\WP\SEO\Services\Health_Check\Runner_Interface;

/**
 * Runs the File_Generation health check.
 */
class File_Runner implements Runner_Interface {

	/**
	 * Is set to non-empty string when the llms.txt file failed to (re-)generate.
	 *
	 * @var bool
	 */
	private $generation_failure_reason = '';

	/**
	 * Runs the health check.
	 *
	 * @return void
	 */
	public function run() {
		$this->generation_failure_reason = \get_option( Populate_File_Command_Handler::GENERATION_FAILURE_OPTION, '' );
	}

	/**
	 * Returns true if there is no generation failure reason.
	 *
	 * @return bool The boolean indicating if the health check was succesful.
	 */
	public function is_successful() {
		return $this->generation_failure_reason === '';
	}

	/**
	 * Returns the generation failure reason.
	 *
	 * @return string The boolean indicating if the health check was succesful.
	 */
	public function get_generation_failure_reason(): string {
		return $this->generation_failure_reason;
	}
}

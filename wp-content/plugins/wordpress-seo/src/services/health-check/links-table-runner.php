<?php

namespace Yoast\WP\SEO\Services\Health_Check;

use Yoast\WP\SEO\Config\Migration_Status;
use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Runs the Links_Table health check.
 */
class Links_Table_Runner implements Runner_Interface {

	/**
	 * Is set to true when the links table is accessible.
	 *
	 * @var bool
	 */
	private $links_table_accessible = false;

	/**
	 * The Migration_Status object used to determine whether the links table is accessible.
	 *
	 * @var Migration_Status
	 */
	private $migration_status;

	/**
	 * The Options_Helper object used to determine whether the health check should run or not.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Constructor.
	 *
	 * @param Migration_Status $migration_status Object used to determine whether the links table is accessible.
	 * @param Options_Helper   $options_helper   Object used to determine whether the health check should run.
	 */
	public function __construct( Migration_Status $migration_status, Options_Helper $options_helper ) {
		$this->migration_status = $migration_status;
		$this->options_helper   = $options_helper;
	}

	/**
	 * Runs the health check. Checks if the tagline is set to WordPress' default tagline, or to its set translation.
	 *
	 * @return void
	 */
	public function run() {
		$this->links_table_accessible = $this->migration_status->is_version( 'free', \WPSEO_VERSION );
	}

	/**
	 * Returns true if the links table is accessible
	 *
	 * @return bool The boolean indicating if the health check was succesful.
	 */
	public function is_successful() {
		return $this->links_table_accessible;
	}
}

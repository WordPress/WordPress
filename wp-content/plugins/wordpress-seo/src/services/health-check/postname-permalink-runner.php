<?php

namespace Yoast\WP\SEO\Services\Health_Check;

/**
 * Runs the Postname_Permalink health check.
 */
class Postname_Permalink_Runner implements Runner_Interface {

	/**
	 * Is set to true when permalinks are set to contain the post name
	 *
	 * @var bool
	 */
	private $permalinks_contain_postname;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->permalinks_contain_postname = false;
	}

	/**
	 * Runs the health check. Checks if permalinks are set to contain the post name.
	 *
	 * @return void
	 */
	public function run() {
		$this->permalinks_contain_postname = ( \strpos( \get_option( 'permalink_structure' ), '%postname%' ) !== false );
	}

	/**
	 * Returns true if permalinks are set to contain the post name.
	 *
	 * @return bool True if permalinks are set to contain the post name.
	 */
	public function is_successful() {
		return $this->permalinks_contain_postname;
	}
}

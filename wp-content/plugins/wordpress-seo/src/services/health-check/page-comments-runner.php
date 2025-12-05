<?php

namespace Yoast\WP\SEO\Services\Health_Check;

/**
 * Runs the Page_Comments health check.
 */
class Page_Comments_Runner implements Runner_Interface {

	/**
	 * Is set to true when comments are set to display on a single page.
	 *
	 * @var bool
	 */
	private $comments_on_single_page;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->comments_on_single_page = false;
	}

	/**
	 * Runs the health check. Checks if comments are displayed on a single page.
	 *
	 * @return void
	 */
	public function run() {
		$this->comments_on_single_page = \get_option( 'page_comments' ) !== '1';
	}

	/**
	 * Returns true if comments are displayed on a single page.
	 *
	 * @return bool True if comments are displayed on a single page.
	 */
	public function is_successful() {
		return $this->comments_on_single_page;
	}
}

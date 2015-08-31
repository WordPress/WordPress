<?php
/**
 * @package WPSEO\Admin|Google_Search_Console
 */

/**
 * Class WPSEO_GSC_Issues
 */
class WPSEO_GSC_Issues {

	/**
	 * @var string
	 */
	private $option_name = '';

	/**
	 * List of all current issues to compare with received issues
	 *
	 * @var array
	 */
	private $current_issues = array();

	/**
	 * Holder for all the issues
	 *
	 * @var array
	 */
	private $issues = array();

	/**
	 * Setting up the properties and fetching the current issues
	 *
	 * @param string     $platform
	 * @param string     $category
	 * @param array|bool $fetched_issues
	 */
	public function __construct( $platform, $category, $fetched_issues = false ) {
		$this->option_name = strtolower( 'wpseo-gsc-issues-' . $platform . '-' . $category );
		$this->issues      = $this->get_issues();

		if ( ! empty( $fetched_issues ) && is_array( $fetched_issues ) ) {
			$this->save_fetched_issues( $fetched_issues );
		}
	}
	/**
	 * Getting the issues from the options.
	 *
	 * @return array
	 */
	public function get_issues() {
		return get_option( $this->option_name, array() );
	}

	/**
	 * Deleting the issue from the issues
	 *
	 * @param string $url
	 *
	 * @return bool
	 */
	public function delete_issue( $url ) {
		$target_issue = $this->get_issue_by_url( $url );
		if ( $target_issue !== false ) {
			unset( $this->issues[ $target_issue ] );

			$this->save_issues( $this->issues );

			return true;
		}

		return false;
	}

	/**
	 * Fetching the issues for current category and compare them with the already existing issues.
	 *
	 * @param array $fetched_issues
	 */
	private function save_fetched_issues( array $fetched_issues ) {
		$this->set_current_issues();

		$crawl_issues = $this->get_issues();

		// Walk through the issues to do the comparison.
		foreach ( $fetched_issues as $issue ) {
			$this->issue_compare( $crawl_issues, $issue );
		}

		$this->save_issues( $crawl_issues );

		// Refresh the value of $this->issues.
		$this->issues = $this->get_issues();
	}

	/**
	 * Comparing the issue with the list of current existing issues
	 *
	 * @param array    $crawl_issues
	 * @param stdClass $issue
	 */
	private function issue_compare( &$crawl_issues, $issue ) {
		$issue->pageUrl = WPSEO_Utils::format_url( (string) $issue->pageUrl );

		if ( ! in_array( $issue->pageUrl, $this->current_issues ) ) {
			array_push(
				$crawl_issues,
				$this->get_issue( $this->create_issue( $issue ) )
			);
		}
	}

	/**
	 * The fetched issue from the API will be parsed as an WPSEO_Crawl_Issue object. After initializing the issue as an
	 * object, the object will be returned
	 *
	 * @param stdClass $issue
	 *
	 * @return WPSEO_GSC_Issue
	 */
	private function create_issue( $issue ) {
		return new WPSEO_GSC_Issue(
			$issue->pageUrl,
			new DateTime( (string) $issue->first_detected ),
			new DateTime( (string) $issue->last_crawled ),
			(string) ( ! empty( $issue->responseCode ) ) ? $issue->responseCode : null
		);
	}

	/**
	 * Returns the crawl issue as an array.
	 *
	 * @param WPSEO_GSC_Issue $crawl_issue
	 *
	 * @return array()
	 */
	private function get_issue( WPSEO_GSC_Issue $crawl_issue ) {
		return $crawl_issue->to_array();
	}

	/**
	 * Saving the issues to the options. The target option is base on current platform and category.
	 *
	 * @param array $issues
	 */
	private function save_issues( array $issues ) {
		update_option( $this->option_name, $issues, false );
	}

	/**
	 * Getting the issues from the options and get only the URL out of it. This is because there will be a comparison
	 * with the issues from the API.
	 */
	private function set_current_issues() {
		if ( ! empty( $this->issues ) ) {
			$this->current_issues = wp_list_pluck( $this->issues, 'url' );
		}
	}

	/**
	 * Search in the issues for the given $url
	 *
	 * @param string $url
	 *
	 * @return int|string
	 */
	private function get_issue_by_url( $url ) {
		foreach ( $this->issues as $key => $issue ) {
			if ( $url === $issue['url'] ) {
				return $key;
			}
		}

		return false;
	}

}

<?php
/**
 * @package WPSEO\Admin|Google_Search_Console
 */

/**
 * Class WPSEO_GSC_Count
 */
class WPSEO_GSC_Count {

	// The last checked timestamp.
	const OPTION_CI_LAST_FETCH = 'wpseo_gsc_last_fetch';

	// The option name where the issues counts are saved.
	const OPTION_CI_COUNTS     = 'wpseo_gsc_issues_counts';

	/**
	 * @var WPSEO_GSC_Service
	 */
	private $service;

	/**
	 * Holder for the fetched issues from GSC
	 *
	 * @var array
	 */
	private $issues = array();

	/**
	 * Fetching the counts
	 *
	 * @param WPSEO_GSC_Service $service
	 */
	public function __construct( WPSEO_GSC_Service $service ) {
		$this->service = $service;

		$this->fetch_counts();
	}

	/**
	 * Getting the counts for given platform and return them as an array
	 *
	 * @param string $platform
	 *
	 * @return array
	 */
	public function get_platform_counts( $platform ) {
		$counts = $this->get_counts();
		if ( array_key_exists( $platform, $counts ) ) {
			return $counts[ $platform ];
		}

		return array();
	}

	/**
	 * Return the fetched issues
	 *
	 * @return array
	 */
	public function get_issues() {
		return $this->issues;
	}

	/**
	 * Listing the issues an gives them back as fetched issues
	 *
	 * @param string $platform
	 * @param string $category
	 */
	public function list_issues( $platform, $category ) {
		$counts = $this->get_counts();

		if ( array_key_exists( $platform, $counts ) ) {
			$counts[ $platform ] = $this->list_category_issues( $counts[ $platform ], $platform, $category );

			// Write the new counts value.
			$this->set_counts( $counts );
		}
	}

	/**
	 * Fetching the counts from the GSC API
	 */
	private function fetch_counts() {
		if ( WPSEO_GSC_Settings::get_profile() && $this->get_last_fetch() <= strtotime( '-12 hours' ) ) {
			// Remove the timestamp.
			$this->remove_last_fetch();

			// Getting the counts and parse them.
			$counts = $this->parse_counts( $this->service->get_crawl_issue_counts() );

			// Fetching the counts by setting an option.
			$this->set_counts( $counts );

			// Saving the current timestamp.
			$this->save_last_fetch();
		}
	}

	/**
	 * Parsing the received counts from the API and map the keys to plugin friendly values
	 *
	 * @param array $fetched_counts
	 *
	 * @return array
	 */
	private function parse_counts( array $fetched_counts ) {
		$counts = array();
		foreach ( $fetched_counts as $platform_name => $categories ) {
			$new_platform = WPSEO_GSC_Mapper::platform_from_api( $platform_name );

			foreach ( $categories as $category_name => $category ) {
				$new_category = WPSEO_GSC_Mapper::category_from_api( $category_name );
				$counts[ $new_platform ][ $new_category ] = $category;
			}
		}

		return $counts;
	}

	/**
	 * Listing the issues for current category.
	 *
	 * @param array  $counts
	 * @param string $platform
	 * @param string $category
	 *
	 * @return array
	 */
	private function list_category_issues( array $counts, $platform, $category ) {
		// When the issues have to be fetched.
		if ( array_key_exists( $category, $counts ) && $counts[ $category ]['count'] > 0 && $counts[ $category ]['last_fetch'] <= strtotime( '-12 hours' ) ) {
			if ( $issues = $this->service->fetch_category_issues( WPSEO_GSC_Mapper::platform_to_api( $platform ), WPSEO_GSC_Mapper::category_to_api( $category ) ) ) {
				$this->issues = $issues;
			}

			// Set last fetch.
			$counts[ $category ]['last_fetch'] = time();
		}

		return $counts;
	}

	/**
	 * Getting the counts from the options
	 *
	 * @return array
	 */
	private function get_counts() {
		return get_option( self::OPTION_CI_COUNTS, array() );
	}

	/**
	 * Fetching the counts from the service and store them in an option
	 *
	 * @param array $counts
	 */
	private function set_counts( array $counts ) {
		update_option( self::OPTION_CI_COUNTS, $counts );
	}

	/**
	 * Store the timestamp of when crawl errors were saved the last time.
	 */
	private function save_last_fetch() {
		add_option( self::OPTION_CI_LAST_FETCH, time(), '', 'no' );
	}

	/**
	 * Remove the last checked option
	 */
	private function remove_last_fetch() {
		delete_option( self::OPTION_CI_LAST_FETCH );
	}

	/**
	 * Get the timestamp of when the crawl errors were last saved
	 *
	 * @return int
	 */
	private function get_last_fetch() {
		return get_option( self::OPTION_CI_LAST_FETCH, 0 );
	}

}

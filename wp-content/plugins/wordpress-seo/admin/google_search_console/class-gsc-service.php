<?php
/**
 * @package WPSEO\Admin|Google_Search_Console
 */

/**
 * Class WPSEO_GSC_Service
 */
class WPSEO_GSC_Service {

	/**
	 * @var Yoast_Api_Google_Client
	 */
	private $client;

	/**
	 * @var string
	 */
	private $profile;

	/**
	 * Constructor
	 *
	 * @param string $profile
	 */
	public function __construct( $profile = '' ) {
		$this->profile = $profile;

		$this->set_client();
	}

	/**
	 * Returns the client
	 *
	 * @return Yoast_Api_Google_Client
	 */
	public function get_client() {
		return $this->client;
	}

	/**
	 * Removes the option and calls the clients clear_data method to clear that one as well
	 */
	public function clear_data() {
		// Clear client data.
		$this->client->clear_data();
	}

	/**
	 * Get all sites that are registered in the GSC panel
	 *
	 * @return array
	 */
	public function get_sites() {
		$sites = array();

		$response_json = $this->client->do_request( 'sites', true );

		// Do list sites request.
		if ( ! empty( $response_json->siteEntry ) ) {
			foreach ( $response_json->siteEntry as $entry ) {
				$sites[ str_ireplace( 'sites/', '', (string) $entry->siteUrl ) ] = (string) $entry->siteUrl;
			}

			// Sorting the retrieved sites.
			asort( $sites );
		}

		return $sites;
	}

	/**
	 * Get crawl issues
	 *
	 * @return array
	 */
	public function get_crawl_issue_counts() {
		// Setup crawl error list.
		$crawl_error_counts = $this->get_crawl_error_counts( $this->profile );

		$return = array();
		if ( ! empty( $crawl_error_counts->countPerTypes ) ) {
			foreach ( $crawl_error_counts->countPerTypes as $category ) {
				$return[ $category->platform ][ $category->category ] = array(
					'count'      => $category->entries[0]->count,
					'last_fetch' => null,
				);
			}
		}

		return $return;
	}

	/**
	 * Sending request to mark issue as fixed
	 *
	 * @param string $url
	 * @param string $platform
	 * @param string $category
	 *
	 * @return bool
	 */
	public function mark_as_fixed( $url, $platform, $category ) {
		$response = $this->client->do_request( 'sites/' .  urlencode( $this->profile ) .  '/urlCrawlErrorsSamples/' . urlencode( ltrim( $url, '/' ) ) . '?category=' . WPSEO_GSC_Mapper::category_to_api( $category ) . '&platform=' . WPSEO_GSC_Mapper::platform_to_api( $platform ) . '', false, 'DELETE' );
		return ( $response->getResponseHttpCode() === 204 );
	}

	/**
	 * Fetching the issues from the GSC API
	 *
	 * @param string $platform
	 * @param string $category
	 *
	 * @return mixed
	 */
	public function fetch_category_issues( $platform, $category ) {
		$issues = $this->client->do_request(
			'sites/' . urlencode( $this->profile ) . '/urlCrawlErrorsSamples?category=' . $category . '&platform=' . $platform,
			true
		);

		if ( ! empty( $issues->urlCrawlErrorSample ) ) {
			return $issues->urlCrawlErrorSample;
		}
	}

	/**
	 * Setting the GSC client
	 */
	private function set_client() {
		try {
			new Yoast_Api_Libs( '2.0' );
		}
		catch ( Exception $exception ) {
			if ( $exception->getMessage() === 'required_version' ) {
				$this->incompatible_api_libs(
					__( 'Yoast plugins share some code between them to make your site faster. As a result of that, we need all Yoast plugins to be up to date. We\'ve detected this isn\'t the case, so please update the Yoast plugins that aren\'t up to date yet.', 'wordpress-seo' )
				);
			}
		}

		if ( class_exists( 'Yoast_Api_Google_Client' ) === false ) {
			$this->incompatible_api_libs(
				/* translators: %1$s expands to Yoast SEO, %2$s expands to Google Analytics by Yoast */
				sprintf(
					__(
						'%1$s detected you’re using a version of %2$s which is not compatible with %1$s. Please update %2$s to the latest version to use this feature.',
						'wordpress-seo'
					),
					'Yoast SEO',
					'Google Analytics by Yoast'
				)
			);

			wp_redirect( admin_url( 'admin.php?page=wpseo_dashboard' ) );
			exit;
		}

		$this->client = new Yoast_Api_Google_Client( WPSEO_GSC_Config::$gsc, 'wpseo-gsc', 'https://www.googleapis.com/webmasters/v3/' );
	}

	/**
	 * Adding notice that the api libs has the wrong version
	 *
	 * @param string $notice
	 */
	private function incompatible_api_libs( $notice ) {
		Yoast_Notification_Center::get()->add_notification(
			new Yoast_Notification( $notice, array( 'type' => 'error' ) )
		);
	}

	/**
	 * Getting the crawl error counts
	 *
	 * @param string $profile
	 *
	 * @return object|bool
	 */
	private function get_crawl_error_counts( $profile ) {
		$crawl_error_counts = $this->client->do_request(
			'sites/' . urlencode( $profile ) . '/urlCrawlErrorsCounts/query',
			true
		);

		if ( ! empty( $crawl_error_counts ) ) {
			return $crawl_error_counts;
		}

		return false;
	}

}

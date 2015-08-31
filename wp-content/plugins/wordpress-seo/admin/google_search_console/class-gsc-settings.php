<?php
/**
 * @package WPSEO\Admin|Google_Search_Console
 */

/**
 * Class WPSEO_GSC_Settings
 */
class WPSEO_GSC_Settings {

	/**
	 * Clear all data from the database
	 *
	 * @param WPSEO_GSC_Service $service
	 */
	public static function clear_data( WPSEO_GSC_Service $service ) {
		// Remove issue and issue counts.
		self::remove();

		// Removes the GSC options.
		self::remove_gsc_option();

		// Clear the service data.
		$service->clear_data();
	}

	/**
	 * Reloading all the issues
	 */
	public static function reload_issues( ) {
		// Remove issue and issue counts.
		self::remove();
	}

	/**
	 * When authorization is successful return true, otherwise false
	 *
	 * @param string                  $authorization_code
	 * @param Yoast_Api_Google_Client $client
	 *
	 * @return bool
	 */
	public static function validate_authorization( $authorization_code, Yoast_Api_Google_Client $client ) {
		return ( $authorization_code !== '' && $client->authenticate_client( $authorization_code ) );
	}

	/**
	 * Get the GSC profile
	 *
	 * @return string
	 */
	public static function get_profile() {
		// Get option.
		$option = get_option( WPSEO_GSC::OPTION_WPSEO_GSC, array( 'profile' => '' ) );

		// Set the profile.
		$profile = '';
		if ( ! empty( $option['profile'] ) ) {
			$profile = $option['profile'];
		}

		// Return the profile.
		return trim( $profile, '/' );
	}

	/**
	 * Removes the issue counts and all the issues from the options
	 */
	private static function remove() {
		// Remove the issue counts from the options.
		self::remove_issue_counts();

		// Removing all issues from the database.
		self::remove_issues();
	}

	/**
	 * Remove the issue counts
	 */
	private static function remove_issue_counts() {
		// Remove the options which are holding the counts.
		delete_option( WPSEO_GSC_Count::OPTION_CI_COUNTS );
		delete_option( WPSEO_GSC_Count::OPTION_CI_LAST_FETCH );
	}

	/**
	 * Delete the issues and their meta data from the database
	 */
	private static function remove_issues() {
		global $wpdb;

		// Remove local crawl issues by running a delete query.
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wpseo-gsc-issues-%'" );
	}

	/**
	 * Removes the options for GSC
	 */
	private static function remove_gsc_option() {
		delete_option( WPSEO_GSC::OPTION_WPSEO_GSC );
	}

}

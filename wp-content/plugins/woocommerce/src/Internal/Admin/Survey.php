<?php
/**
 * Survey helper methods.
 */

namespace Automattic\WooCommerce\Internal\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Survey Class.
 */
class Survey {
	/**
	 * Survey URL.
	 */
	const SURVEY_URL = 'https://automattic.survey.fm';

	/**
	 * Get a survey's URL from a path.
	 *
	 * @param  string $path Path of the survey.
	 * @param  array  $query Query arguments as key value pairs.
	 * @return string Full URL to survey.
	 */
	public static function get_url( $path, $query = array() ) {
		$url = self::SURVEY_URL . $path;

		$query_args = apply_filters( 'woocommerce_admin_survey_query', $query );

		if ( ! empty( $query_args ) ) {
			$query_string = http_build_query( $query_args );
			$url          = $url . '?' . $query_string;
		}

		return $url;
	}
}

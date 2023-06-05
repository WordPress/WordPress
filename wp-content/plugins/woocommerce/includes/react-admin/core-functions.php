<?php
/**
 * Core Functions
 *
 * Holds core functions for wc-admin.
 *
 * @package WooCommerce\Admin\Functions
 */

use Automattic\WooCommerce\Internal\Admin\Settings;

/**
 * Format a number using the decimal and thousands separator settings in WooCommerce.
 *
 * @param mixed $number Number to be formatted.
 * @return string
 */
function wc_admin_number_format( $number ) {
	$currency_settings = Settings::get_currency_settings();
	return number_format(
		$number,
		0,
		$currency_settings['decimalSeparator'],
		$currency_settings['thousandSeparator']
	);
}

/**
 * Retrieves a URL to relative path inside WooCommerce admin with
 * the provided query parameters.
 *
 * @param  string $path Relative path of the desired page.
 * @param  array  $query Query parameters to append to the path.
 *
 * @return string       Fully qualified URL pointing to the desired path.
 */
function wc_admin_url( $path = null, $query = array() ) {
	if ( ! empty( $query ) ) {
		$query_string = http_build_query( $query );
		$path         = $path ? '&path=' . $path . '&' . $query_string : '';
	}

	return admin_url( 'admin.php?page=wc-admin' . $path, dirname( __FILE__ ) );
}

/**
 * Record an event using Tracks.
 *
 * @internal WooCommerce core only includes Tracks in admin, not the REST API, so we need to include it.
 * @param string $event_name Event name for tracks.
 * @param array  $properties Properties to pass along with event.
 */
function wc_admin_record_tracks_event( $event_name, $properties = array() ) {
	// WC post types must be registered first for WC_Tracks to work.
	if ( ! post_type_exists( 'product' ) ) {
		return;
	}

	if ( ! class_exists( 'WC_Tracks' ) ) {
		if ( ! defined( 'WC_ABSPATH' ) || ! file_exists( WC_ABSPATH . 'includes/tracks/class-wc-tracks.php' ) ) {
			return;
		}

		include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks.php';
		include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks-event.php';
		include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks-client.php';
		include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks-footer-pixel.php';
		include_once WC_ABSPATH . 'includes/tracks/class-wc-site-tracking.php';
	}

	WC_Tracks::record_event( $event_name, $properties );
}

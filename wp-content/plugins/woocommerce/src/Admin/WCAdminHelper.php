<?php
/**
 * WCAdminHelper
 *
 * Helper class for generic WCAdmin functions.
 */

namespace Automattic\WooCommerce\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class WCAdminHelper
 */
class WCAdminHelper {
	/**
	 * WC Admin timestamp option name.
	 */
	const WC_ADMIN_TIMESTAMP_OPTION = 'woocommerce_admin_install_timestamp';

	const WC_ADMIN_STORE_AGE_RANGES = array(
		'week-1'    => array(
			'start' => 0,
			'end'   => WEEK_IN_SECONDS,
		),
		'week-1-4'  => array(
			'start' => WEEK_IN_SECONDS,
			'end'   => WEEK_IN_SECONDS * 4,
		),
		'month-1-3' => array(
			'start' => MONTH_IN_SECONDS,
			'end'   => MONTH_IN_SECONDS * 3,
		),
		'month-3-6' => array(
			'start' => MONTH_IN_SECONDS * 3,
			'end'   => MONTH_IN_SECONDS * 6,
		),
		'month-6+'  => array(
			'start' => MONTH_IN_SECONDS * 6,
		),
	);

	/**
	 * Get the number of seconds that the store has been active.
	 *
	 * @return number Number of seconds.
	 */
	public static function get_wcadmin_active_for_in_seconds() {
		$install_timestamp = get_option( self::WC_ADMIN_TIMESTAMP_OPTION );

		if ( ! is_numeric( $install_timestamp ) ) {
			$install_timestamp = time();
			update_option( self::WC_ADMIN_TIMESTAMP_OPTION, $install_timestamp );
		}

		return time() - $install_timestamp;
	}


	/**
	 * Test how long WooCommerce Admin has been active.
	 *
	 * @param int $seconds Time in seconds to check.
	 * @return bool Whether or not WooCommerce admin has been active for $seconds.
	 */
	public static function is_wc_admin_active_for( $seconds ) {
		$wc_admin_active_for = self::get_wcadmin_active_for_in_seconds();

		return ( $wc_admin_active_for >= $seconds );
	}

	/**
	 * Test if WooCommerce Admin has been active within a pre-defined range.
	 *
	 * @param string $range range available in WC_ADMIN_STORE_AGE_RANGES.
	 * @param int    $custom_start custom start in range.
	 * @throws \InvalidArgumentException Throws exception when invalid $range is passed in.
	 * @return bool Whether or not WooCommerce admin has been active within the range.
	 */
	public static function is_wc_admin_active_in_date_range( $range, $custom_start = null ) {
		if ( ! array_key_exists( $range, self::WC_ADMIN_STORE_AGE_RANGES ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					'"%s" range is not supported, use one of: %s',
					$range,
					implode( ', ', array_keys( self::WC_ADMIN_STORE_AGE_RANGES ) )
				)
			);
		}
		$wc_admin_active_for = self::get_wcadmin_active_for_in_seconds();

		$range_data = self::WC_ADMIN_STORE_AGE_RANGES[ $range ];
		$start      = null !== $custom_start ? $custom_start : $range_data['start'];
		if ( $range_data && $wc_admin_active_for >= $start ) {
			return isset( $range_data['end'] ) ? $wc_admin_active_for < $range_data['end'] : true;
		}
		return false;
	}
}

<?php
/**
 * Date helper class.
 *
 * @package WPSEO\Internals
 */

/**
 * Class WPSEO_Date_Helper
 *
 * Note: Move this class with namespace to the src/helpers directory and add a class_alias for BC.
 */
class WPSEO_Date_Helper {

	/**
	 * Formats a given date in UTC TimeZone format.
	 *
	 * @param string $date   String representing the date / time.
	 * @param string $format The format that the passed date should be in.
	 *
	 * @return string The formatted date.
	 */
	public function format( $date, $format = DATE_W3C ) {
		return YoastSEO()->helpers->date->format( $date, $format );
	}

	/**
	 * Formats the given timestamp to the needed format.
	 *
	 * @param int    $timestamp The timestamp to use for the formatting.
	 * @param string $format    The format that the passed date should be in.
	 *
	 * @return string The formatted date.
	 */
	public function format_timestamp( $timestamp, $format = DATE_W3C ) {
		return YoastSEO()->helpers->date->format_timestamp( $timestamp, $format );
	}

	/**
	 * Formats a given date in UTC TimeZone format and translate it to the set language.
	 *
	 * @param string $date   String representing the date / time.
	 * @param string $format The format that the passed date should be in.
	 *
	 * @return string The formatted and translated date.
	 */
	public function format_translated( $date, $format = DATE_W3C ) {
		return YoastSEO()->helpers->date->format_translated( $date, $format );
	}

	/**
	 * Check if a string is a valid datetime.
	 *
	 * @param string $datetime String input to check as valid input for DateTime class.
	 *
	 * @return bool True when datatime is valid.
	 */
	public function is_valid_datetime( $datetime ) {
		return YoastSEO()->helpers->date->is_valid_datetime( $datetime );
	}
}

<?php

namespace Yoast\WP\SEO\Helpers;

use DateTime;
use DateTimeZone;
use Exception;

/**
 * A helper object for dates.
 */
class Date_Helper {

	/**
	 * Convert given date string to the W3C format.
	 *
	 * If $translate is true then the given date and format string will
	 * be passed to date_i18n() for translation.
	 *
	 * @param string $date      Date string to convert.
	 * @param bool   $translate Whether the return date should be translated. Default false.
	 *
	 * @return string Formatted date string.
	 */
	public function mysql_date_to_w3c_format( $date, $translate = false ) {
		return \mysql2date( \DATE_W3C, $date, $translate );
	}

	/**
	 * Formats a given date in UTC TimeZone format.
	 *
	 * @param string $date   String representing the date / time.
	 * @param string $format The format that the passed date should be in.
	 *
	 * @return string The formatted date.
	 */
	public function format( $date, $format = \DATE_W3C ) {
		if ( ! \is_string( $date ) ) {
			return $date;
		}

		$immutable_date = \date_create_immutable_from_format( 'Y-m-d H:i:s', $date, new DateTimeZone( 'UTC' ) );

		if ( ! $immutable_date ) {
			return $date;
		}

		return $immutable_date->format( $format );
	}

	/**
	 * Formats the given timestamp to the needed format.
	 *
	 * @param int    $timestamp The timestamp to use for the formatting.
	 * @param string $format    The format that the passed date should be in.
	 *
	 * @return string The formatted date.
	 */
	public function format_timestamp( $timestamp, $format = \DATE_W3C ) {
		if ( ! \is_string( $timestamp ) && ! \is_int( $timestamp ) ) {
			return $timestamp;
		}

		$immutable_date = \date_create_immutable_from_format( 'U', $timestamp, new DateTimeZone( 'UTC' ) );

		if ( ! $immutable_date ) {
			return $timestamp;
		}

		return $immutable_date->format( $format );
	}

	/**
	 * Formats a given date in UTC TimeZone format and translate it to the set language.
	 *
	 * @param string $date   String representing the date / time.
	 * @param string $format The format that the passed date should be in.
	 *
	 * @return string The formatted and translated date.
	 */
	public function format_translated( $date, $format = \DATE_W3C ) {
		return \date_i18n( $format, $this->format( $date, 'U' ) );
	}

	/**
	 * Returns the current time measured in the number of seconds since the Unix Epoch (January 1 1970 00:00:00 GMT).
	 *
	 * @return int The current time measured in the number of seconds since the Unix Epoch (January 1 1970 00:00:00 GMT).
	 */
	public function current_time() {
		return \time();
	}

	/**
	 * Check if a string is a valid datetime.
	 *
	 * @param string $datetime String input to check as valid input for DateTime class.
	 *
	 * @return bool True when datetime is valid.
	 */
	public function is_valid_datetime( $datetime ) {
		if ( $datetime === null ) {
			/*
			 * While not "officially" supported, `null` will be handled as `"now"` until PHP 9.0.
			 * @link https://3v4l.org/tYp2k
			 */
			return true;
		}

		if ( \is_string( $datetime ) && \substr( $datetime, 0, 1 ) === '-' ) {
			return false;
		}

		try {
			return new DateTime( $datetime ) !== false;
		} catch ( Exception $exception ) {
			return false;
		}
	}
}

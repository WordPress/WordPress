<?php
/**
 * Logger Interface
 *
 * @version 3.0.0
 * @package WooCommerce\Interface
 */

/**
 * WC Logger Interface
 *
 * Functions that must be defined to correctly fulfill logger API.
 *
 * @version 3.0.0
 */
interface WC_Logger_Interface {

	/**
	 * Add a log entry.
	 *
	 * This is not the preferred method for adding log messages. Please use log() or any one of
	 * the level methods (debug(), info(), etc.). This method may be deprecated in the future.
	 *
	 * @param string $handle File handle.
	 * @param string $message Log message.
	 * @param string $level Log level.
	 *
	 * @return bool True if log was added, otherwise false.
	 */
	public function add( $handle, $message, $level = WC_Log_Levels::NOTICE );

	/**
	 * Add a log entry.
	 *
	 * @param string $level One of the following:
	 *     'emergency': System is unusable.
	 *     'alert': Action must be taken immediately.
	 *     'critical': Critical conditions.
	 *     'error': Error conditions.
	 *     'warning': Warning conditions.
	 *     'notice': Normal but significant condition.
	 *     'info': Informational messages.
	 *     'debug': Debug-level messages.
	 * @param string $message Log message.
	 * @param array  $context Optional. Additional information for log handlers.
	 */
	public function log( $level, $message, $context = array() );

	/**
	 * Adds an emergency level message.
	 *
	 * System is unusable.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional. Additional information for log handlers.
	 */
	public function emergency( $message, $context = array() );

	/**
	 * Adds an alert level message.
	 *
	 * Action must be taken immediately.
	 * Example: Entire website down, database unavailable, etc.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional. Additional information for log handlers.
	 */
	public function alert( $message, $context = array() );

	/**
	 * Adds a critical level message.
	 *
	 * Critical conditions.
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional. Additional information for log handlers.
	 */
	public function critical( $message, $context = array() );

	/**
	 * Adds an error level message.
	 *
	 * Runtime errors that do not require immediate action but should typically be logged
	 * and monitored.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional. Additional information for log handlers.
	 */
	public function error( $message, $context = array() );

	/**
	 * Adds a warning level message.
	 *
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things that are not
	 * necessarily wrong.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional. Additional information for log handlers.
	 */
	public function warning( $message, $context = array() );

	/**
	 * Adds a notice level message.
	 *
	 * Normal but significant events.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional. Additional information for log handlers.
	 */
	public function notice( $message, $context = array() );

	/**
	 * Adds a info level message.
	 *
	 * Interesting events.
	 * Example: User logs in, SQL logs.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional. Additional information for log handlers.
	 */
	public function info( $message, $context = array() );

	/**
	 * Adds a debug level message.
	 *
	 * Detailed debug information.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional. Additional information for log handlers.
	 */
	public function debug( $message, $context = array() );
}

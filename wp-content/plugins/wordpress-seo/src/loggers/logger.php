<?php

namespace Yoast\WP\SEO\Loggers;

use YoastSEO_Vendor\Psr\Log\LoggerInterface;
use YoastSEO_Vendor\Psr\Log\LoggerTrait;
use YoastSEO_Vendor\Psr\Log\NullLogger;

/**
 * Our logger class.
 */
class Logger implements LoggerInterface {

	use LoggerTrait;

	/**
	 * The logger object.
	 *
	 * @var LoggerInterface
	 */
	protected $wrapped_logger;

	/**
	 * Logger constructor.
	 */
	public function __construct() {
		$this->wrapped_logger = new NullLogger();

		/**
		 * Gives the possibility to set override the logger interface.
		 *
		 * @param LoggerInterface $logger Instance of NullLogger.
		 *
		 * @return LoggerInterface The logger object.
		 */
		$this->wrapped_logger = \apply_filters( 'wpseo_logger', $this->wrapped_logger );
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed  $level   The log level.
	 * @param string $message The log message.
	 * @param array  $context The log context.
	 *
	 * @return void
	 */
	public function log( $level, $message, array $context = [] ) {
		$this->wrapped_logger->log( $level, $message, $context );
	}
}

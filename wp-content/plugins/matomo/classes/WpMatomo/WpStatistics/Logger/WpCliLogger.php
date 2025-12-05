<?php

namespace WpMatomo\WpStatistics\Logger;

use WP_CLI;
use Psr\Log\LoggerInterface;

/**
 * WP_CLi logger
 *
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class WpCliLogger implements LoggerInterface {

	public function debug( $message, array $context = array() ) {
		WP_CLI::log(
			'[debug] ' . str_replace(
				array_map(
					[
						$this,
						'getContext',
					],
					array_keys( $context )
				),
				array_values( $context ),
				$message
			)
		);
	}

	public function error( $message, array $context = array() ) {
		WP_CLI::log(
			'[error] ' . str_replace(
				array_map(
					[
						$this,
						'getContext',
					],
					array_keys( $context )
				),
				array_values( $context ),
				$message
			)
		);
	}

	public function critical( $message, array $context = array() ) {
		WP_CLI::log(
			'[critical] ' . str_replace(
				array_map(
					[
						$this,
						'getContext',
					],
					array_keys( $context )
				),
				array_values( $context ),
				$message
			)
		);
	}

	public function warning( $message, array $context = array() ) {
		WP_CLI::log(
			'[warning] ' . str_replace(
				array_map(
					[
						$this,
						'getContext',
					],
					array_keys( $context )
				),
				array_values( $context ),
				$message
			)
		);
	}

	public function info( $message, array $context = array() ) {
		WP_CLI::log(
			'[info] ' . str_replace(
				array_map(
					[
						$this,
						'getContext',
					],
					array_keys( $context )
				),
				array_values( $context ),
				$message
			)
		);
	}

	public function log( $level, $message, array $context = array() ) {
		WP_CLI::log(
			'[' . $level . '] ' . str_replace(
				array_map(
					[
						$this,
						'getContext',
					],
					array_keys( $context )
				),
				array_values( $context ),
				$message
			)
		);
	}

	public function notice( $message, array $context = array() ) {
		WP_CLI::log(
			'[notice] ' . str_replace(
				array_map(
					[
						$this,
						'getContext',
					],
					array_keys( $context )
				),
				array_values( $context ),
				$message
			)
		);
	}

	public function alert( $message, array $context = array() ) {
		WP_CLI::log(
			'[alert] ' . str_replace(
				array_map(
					[
						$this,
						'getContext',
					],
					array_keys( $context )
				),
				array_values( $context ),
				$message
			)
		);
	}

	public function emergency( $message, array $context = array() ) {
		WP_CLI::log(
			'[emergency] ' . str_replace(
				array_map(
					[
						$this,
						'getContext',
					],
					array_keys( $context )
				),
				array_values( $context ),
				$message
			)
		);
	}

	public function getContext( $context ) {
		return '{' . $context . '}';
	}
}

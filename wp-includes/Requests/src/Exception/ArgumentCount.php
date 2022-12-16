<?php

namespace WpOrg\Requests\Exception;

use WpOrg\Requests\Exception;

/**
 * Exception for when an incorrect number of arguments are passed to a method.
 *
 * Typically, this exception is used when all arguments for a method are optional,
 * but certain arguments need to be passed together, i.e. a method which can be called
 * with no arguments or with two arguments, but not with one argument.
 *
 * Along the same lines, this exception is also used if a method expects an array
 * with a certain number of elements and the provided number of elements does not comply.
 *
 * @package Requests\Exceptions
 * @since   2.0.0
 */
final class ArgumentCount extends Exception {

	/**
	 * Create a new argument count exception with a standardized text.
	 *
	 * @param string $expected The argument count expected as a phrase.
	 *                         For example: `at least 2 arguments` or `exactly 1 argument`.
	 * @param int    $received The actual argument count received.
	 * @param string $type     Exception type.
	 *
	 * @return \WpOrg\Requests\Exception\ArgumentCount
	 */
	public static function create($expected, $received, $type) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace
		$stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

		return new self(
			sprintf(
				'%s::%s() expects %s, %d given',
				$stack[1]['class'],
				$stack[1]['function'],
				$expected,
				$received
			),
			$type
		);
	}
}

<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

namespace WpOrg\Requests\Utility;

/**
 * Poor person's (backed) enum with the PHP native trim `$characters` defaults to choose from.
 *
 * In PHP 8.6, the default value for the `$characters` parameter of the `[rl]trim()` function
 * changed to include the form feed character.
 *
 * This "enum" allows calls to `[rl]trim()` throughout the code to document and make it explicit
 * which characters should be trimmed from the text string in question.
 *
 * @link https://wiki.php.net/rfc/trim_form_feed
 *
 * ---------------------------------------------------------------------------------------------
 * This class is only intended for internal use by Requests and is not part of the public API.
 * This also means that it has no promise of backward compatibility. Use at your own risk.
 * ---------------------------------------------------------------------------------------------
 *
 * @internal
 * @package Requests\Utilities
 * @since   2.1.0
 */
final class Trim {

	/**
	 * The ASCII whitespace characters and the NUL byte, excluding the form feed character.
	 *
	 * This is the PHP native default in PHP < 8.6.
	 *
	 * @var string
	 */
	const WHITESPACE_CHARS_NO_FF = " \n\r\t\v\x00";

	/**
	 * The ASCII whitespace characters, including the form feed character, and the NUL byte.
	 *
	 * This is the PHP native default in PHP >= 8.6.
	 *
	 * @var string
	 */
	const WHITESPACE_CHARS = " \f\n\r\t\v\x00";
}

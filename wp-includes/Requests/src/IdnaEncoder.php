<?php

namespace WpOrg\Requests;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Utility\InputValidator;

/**
 * IDNA URL encoder
 *
 * Note: Not fully compliant, as nameprep does nothing yet.
 *
 * @package Requests\Utilities
 *
 * @link https://tools.ietf.org/html/rfc3490 IDNA specification
 * @link https://tools.ietf.org/html/rfc3492 Punycode/Bootstrap specification
 */
class IdnaEncoder {
	/**
	 * ACE prefix used for IDNA
	 *
	 * @link https://tools.ietf.org/html/rfc3490#section-5
	 * @var string
	 */
	const ACE_PREFIX = 'xn--';

	/**
	 * Maximum length of a IDNA URL in ASCII.
	 *
	 * @see \WpOrg\Requests\IdnaEncoder::to_ascii()
	 *
	 * @since 2.0.0
	 *
	 * @var int
	 */
	const MAX_LENGTH = 64;

	/**#@+
	 * Bootstrap constant for Punycode
	 *
	 * @link https://tools.ietf.org/html/rfc3492#section-5
	 * @var int
	 */
	const BOOTSTRAP_BASE         = 36;
	const BOOTSTRAP_TMIN         = 1;
	const BOOTSTRAP_TMAX         = 26;
	const BOOTSTRAP_SKEW         = 38;
	const BOOTSTRAP_DAMP         = 700;
	const BOOTSTRAP_INITIAL_BIAS = 72;
	const BOOTSTRAP_INITIAL_N    = 128;
	/**#@-*/

	/**
	 * Encode a hostname using Punycode
	 *
	 * @param string|Stringable $hostname Hostname
	 * @return string Punycode-encoded hostname
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed argument is not a string or a stringable object.
	 */
	public static function encode($hostname) {
		if (InputValidator::is_string_or_stringable($hostname) === false) {
			throw InvalidArgument::create(1, '$hostname', 'string|Stringable', gettype($hostname));
		}

		$parts = explode('.', $hostname);
		foreach ($parts as &$part) {
			$part = self::to_ascii($part);
		}

		return implode('.', $parts);
	}

	/**
	 * Convert a UTF-8 text string to an ASCII string using Punycode
	 *
	 * @param string $text ASCII or UTF-8 string (max length 64 characters)
	 * @return string ASCII string
	 *
	 * @throws \WpOrg\Requests\Exception Provided string longer than 64 ASCII characters (`idna.provided_too_long`)
	 * @throws \WpOrg\Requests\Exception Prepared string longer than 64 ASCII characters (`idna.prepared_too_long`)
	 * @throws \WpOrg\Requests\Exception Provided string already begins with xn-- (`idna.provided_is_prefixed`)
	 * @throws \WpOrg\Requests\Exception Encoded string longer than 64 ASCII characters (`idna.encoded_too_long`)
	 */
	public static function to_ascii($text) {
		// Step 1: Check if the text is already ASCII
		if (self::is_ascii($text)) {
			// Skip to step 7
			if (strlen($text) < self::MAX_LENGTH) {
				return $text;
			}

			throw new Exception('Provided string is too long', 'idna.provided_too_long', $text);
		}

		// Step 2: nameprep
		$text = self::nameprep($text);

		// Step 3: UseSTD3ASCIIRules is false, continue
		// Step 4: Check if it's ASCII now
		if (self::is_ascii($text)) {
			// Skip to step 7
			/*
			 * As the `nameprep()` method returns the original string, this code will never be reached until
			 * that method is properly implemented.
			 */
			// @codeCoverageIgnoreStart
			if (strlen($text) < self::MAX_LENGTH) {
				return $text;
			}

			throw new Exception('Prepared string is too long', 'idna.prepared_too_long', $text);
			// @codeCoverageIgnoreEnd
		}

		// Step 5: Check ACE prefix
		if (strpos($text, self::ACE_PREFIX) === 0) {
			throw new Exception('Provided string begins with ACE prefix', 'idna.provided_is_prefixed', $text);
		}

		// Step 6: Encode with Punycode
		$text = self::punycode_encode($text);

		// Step 7: Prepend ACE prefix
		$text = self::ACE_PREFIX . $text;

		// Step 8: Check size
		if (strlen($text) < self::MAX_LENGTH) {
			return $text;
		}

		throw new Exception('Encoded string is too long', 'idna.encoded_too_long', $text);
	}

	/**
	 * Check whether a given text string contains only ASCII characters
	 *
	 * @internal (Testing found regex was the fastest implementation)
	 *
	 * @param string $text Text to examine.
	 * @return bool Is the text string ASCII-only?
	 */
	protected static function is_ascii($text) {
		return (preg_match('/(?:[^\x00-\x7F])/', $text) !== 1);
	}

	/**
	 * Prepare a text string for use as an IDNA name
	 *
	 * @todo Implement this based on RFC 3491 and the newer 5891
	 * @param string $text Text to prepare.
	 * @return string Prepared string
	 */
	protected static function nameprep($text) {
		return $text;
	}

	/**
	 * Convert a UTF-8 string to a UCS-4 codepoint array
	 *
	 * Based on \WpOrg\Requests\Iri::replace_invalid_with_pct_encoding()
	 *
	 * @param string $input Text to convert.
	 * @return array Unicode code points
	 *
	 * @throws \WpOrg\Requests\Exception Invalid UTF-8 codepoint (`idna.invalidcodepoint`)
	 */
	protected static function utf8_to_codepoints($input) {
		$codepoints = [];

		// Get number of bytes
		$strlen = strlen($input);

		// phpcs:ignore Generic.CodeAnalysis.JumbledIncrementer -- This is a deliberate choice.
		for ($position = 0; $position < $strlen; $position++) {
			$value = ord($input[$position]);

			if ((~$value & 0x80) === 0x80) {            // One byte sequence:
				$character = $value;
				$length    = 1;
				$remaining = 0;
			} elseif (($value & 0xE0) === 0xC0) {       // Two byte sequence:
				$character = ($value & 0x1F) << 6;
				$length    = 2;
				$remaining = 1;
			} elseif (($value & 0xF0) === 0xE0) {       // Three byte sequence:
				$character = ($value & 0x0F) << 12;
				$length    = 3;
				$remaining = 2;
			} elseif (($value & 0xF8) === 0xF0) {       // Four byte sequence:
				$character = ($value & 0x07) << 18;
				$length    = 4;
				$remaining = 3;
			} else {                                    // Invalid byte:
				throw new Exception('Invalid Unicode codepoint', 'idna.invalidcodepoint', $value);
			}

			if ($remaining > 0) {
				if ($position + $length > $strlen) {
					throw new Exception('Invalid Unicode codepoint', 'idna.invalidcodepoint', $character);
				}

				for ($position++; $remaining > 0; $position++) {
					$value = ord($input[$position]);

					// If it is invalid, count the sequence as invalid and reprocess the current byte:
					if (($value & 0xC0) !== 0x80) {
						throw new Exception('Invalid Unicode codepoint', 'idna.invalidcodepoint', $character);
					}

					--$remaining;
					$character |= ($value & 0x3F) << ($remaining * 6);
				}

				$position--;
			}

			if (// Non-shortest form sequences are invalid
				$length > 1 && $character <= 0x7F
				|| $length > 2 && $character <= 0x7FF
				|| $length > 3 && $character <= 0xFFFF
				// Outside of range of ucschar codepoints
				// Noncharacters
				|| ($character & 0xFFFE) === 0xFFFE
				|| $character >= 0xFDD0 && $character <= 0xFDEF
				|| (
					// Everything else not in ucschar
					$character > 0xD7FF && $character < 0xF900
					|| $character < 0x20
					|| $character > 0x7E && $character < 0xA0
					|| $character > 0xEFFFD
				)
			) {
				throw new Exception('Invalid Unicode codepoint', 'idna.invalidcodepoint', $character);
			}

			$codepoints[] = $character;
		}

		return $codepoints;
	}

	/**
	 * RFC3492-compliant encoder
	 *
	 * @internal Pseudo-code from Section 6.3 is commented with "#" next to relevant code
	 *
	 * @param string $input UTF-8 encoded string to encode
	 * @return string Punycode-encoded string
	 *
	 * @throws \WpOrg\Requests\Exception On character outside of the domain (never happens with Punycode) (`idna.character_outside_domain`)
	 */
	public static function punycode_encode($input) {
		$output = '';
		// let n = initial_n
		$n = self::BOOTSTRAP_INITIAL_N;
		// let delta = 0
		$delta = 0;
		// let bias = initial_bias
		$bias = self::BOOTSTRAP_INITIAL_BIAS;
		// let h = b = the number of basic code points in the input
		$h = 0;
		$b = 0; // see loop
		// copy them to the output in order
		$codepoints = self::utf8_to_codepoints($input);
		$extended   = [];

		foreach ($codepoints as $char) {
			if ($char < 128) {
				// Character is valid ASCII
				// TODO: this should also check if it's valid for a URL
				$output .= chr($char);
				$h++;

				// Check if the character is non-ASCII, but below initial n
				// This never occurs for Punycode, so ignore in coverage
				// @codeCoverageIgnoreStart
			} elseif ($char < $n) {
				throw new Exception('Invalid character', 'idna.character_outside_domain', $char);
				// @codeCoverageIgnoreEnd
			} else {
				$extended[$char] = true;
			}
		}

		$extended = array_keys($extended);
		sort($extended);
		$b = $h;
		// [copy them] followed by a delimiter if b > 0
		if (strlen($output) > 0) {
			$output .= '-';
		}

		// {if the input contains a non-basic code point < n then fail}
		// while h < length(input) do begin
		$codepointcount = count($codepoints);
		while ($h < $codepointcount) {
			// let m = the minimum code point >= n in the input
			$m = array_shift($extended);
			//printf('next code point to insert is %s' . PHP_EOL, dechex($m));
			// let delta = delta + (m - n) * (h + 1), fail on overflow
			$delta += ($m - $n) * ($h + 1);
			// let n = m
			$n = $m;
			// for each code point c in the input (in order) do begin
			for ($num = 0; $num < $codepointcount; $num++) {
				$c = $codepoints[$num];
				// if c < n then increment delta, fail on overflow
				if ($c < $n) {
					$delta++;
				} elseif ($c === $n) { // if c == n then begin
					// let q = delta
					$q = $delta;
					// for k = base to infinity in steps of base do begin
					for ($k = self::BOOTSTRAP_BASE; ; $k += self::BOOTSTRAP_BASE) {
						// let t = tmin if k <= bias {+ tmin}, or
						//     tmax if k >= bias + tmax, or k - bias otherwise
						if ($k <= ($bias + self::BOOTSTRAP_TMIN)) {
							$t = self::BOOTSTRAP_TMIN;
						} elseif ($k >= ($bias + self::BOOTSTRAP_TMAX)) {
							$t = self::BOOTSTRAP_TMAX;
						} else {
							$t = $k - $bias;
						}

						// if q < t then break
						if ($q < $t) {
							break;
						}

						// output the code point for digit t + ((q - t) mod (base - t))
						$digit   = (int) ($t + (($q - $t) % (self::BOOTSTRAP_BASE - $t)));
						$output .= self::digit_to_char($digit);
						// let q = (q - t) div (base - t)
						$q = (int) floor(($q - $t) / (self::BOOTSTRAP_BASE - $t));
					} // end
					// output the code point for digit q
					$output .= self::digit_to_char($q);
					// let bias = adapt(delta, h + 1, test h equals b?)
					$bias = self::adapt($delta, $h + 1, $h === $b);
					// let delta = 0
					$delta = 0;
					// increment h
					$h++;
				} // end
			} // end
			// increment delta and n
			$delta++;
			$n++;
		} // end

		return $output;
	}

	/**
	 * Convert a digit to its respective character
	 *
	 * @link https://tools.ietf.org/html/rfc3492#section-5
	 *
	 * @param int $digit Digit in the range 0-35
	 * @return string Single character corresponding to digit
	 *
	 * @throws \WpOrg\Requests\Exception On invalid digit (`idna.invalid_digit`)
	 */
	protected static function digit_to_char($digit) {
		// @codeCoverageIgnoreStart
		// As far as I know, this never happens, but still good to be sure.
		if ($digit < 0 || $digit > 35) {
			throw new Exception(sprintf('Invalid digit %d', $digit), 'idna.invalid_digit', $digit);
		}

		// @codeCoverageIgnoreEnd
		$digits = 'abcdefghijklmnopqrstuvwxyz0123456789';
		return substr($digits, $digit, 1);
	}

	/**
	 * Adapt the bias
	 *
	 * @link https://tools.ietf.org/html/rfc3492#section-6.1
	 * @param int $delta
	 * @param int $numpoints
	 * @param bool $firsttime
	 * @return int|float New bias
	 *
	 * function adapt(delta,numpoints,firsttime):
	 */
	protected static function adapt($delta, $numpoints, $firsttime) {
		// if firsttime then let delta = delta div damp
		if ($firsttime) {
			$delta = floor($delta / self::BOOTSTRAP_DAMP);
		} else {
			// else let delta = delta div 2
			$delta = floor($delta / 2);
		}

		// let delta = delta + (delta div numpoints)
		$delta += floor($delta / $numpoints);
		// let k = 0
		$k = 0;
		// while delta > ((base - tmin) * tmax) div 2 do begin
		$max = floor(((self::BOOTSTRAP_BASE - self::BOOTSTRAP_TMIN) * self::BOOTSTRAP_TMAX) / 2);
		while ($delta > $max) {
			// let delta = delta div (base - tmin)
			$delta = floor($delta / (self::BOOTSTRAP_BASE - self::BOOTSTRAP_TMIN));
			// let k = k + base
			$k += self::BOOTSTRAP_BASE;
		} // end
		// return k + (((base - tmin + 1) * delta) div (delta + skew))
		return $k + floor(((self::BOOTSTRAP_BASE - self::BOOTSTRAP_TMIN + 1) * $delta) / ($delta + self::BOOTSTRAP_SKEW));
	}
}

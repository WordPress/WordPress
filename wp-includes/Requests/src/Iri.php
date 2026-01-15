<?php
/**
 * IRI parser/serialiser/normaliser
 *
 * @package Requests\Utilities
 */

namespace WpOrg\Requests;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Ipv6;
use WpOrg\Requests\Port;
use WpOrg\Requests\Utility\InputValidator;

/**
 * IRI parser/serialiser/normaliser
 *
 * Copyright (c) 2007-2010, Geoffrey Sneddon and Steve Minutillo.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *
 *  * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 *
 *  * Neither the name of the SimplePie Team nor the names of its contributors
 *       may be used to endorse or promote products derived from this software
 *       without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package Requests\Utilities
 * @author Geoffrey Sneddon
 * @author Steve Minutillo
 * @copyright 2007-2009 Geoffrey Sneddon and Steve Minutillo
 * @license https://opensource.org/licenses/bsd-license.php
 * @link http://hg.gsnedders.com/iri/
 *
 * @property string $iri IRI we're working with
 * @property-read string $uri IRI in URI form, {@see \WpOrg\Requests\Iri::to_uri()}
 * @property string $scheme Scheme part of the IRI
 * @property string $authority Authority part, formatted for a URI (userinfo + host + port)
 * @property string $iauthority Authority part of the IRI (userinfo + host + port)
 * @property string $userinfo Userinfo part, formatted for a URI (after '://' and before '@')
 * @property string $iuserinfo Userinfo part of the IRI (after '://' and before '@')
 * @property string $host Host part, formatted for a URI
 * @property string $ihost Host part of the IRI
 * @property string $port Port part of the IRI (after ':')
 * @property string $path Path part, formatted for a URI (after first '/')
 * @property string $ipath Path part of the IRI (after first '/')
 * @property string $query Query part, formatted for a URI (after '?')
 * @property string $iquery Query part of the IRI (after '?')
 * @property string $fragment Fragment, formatted for a URI (after '#')
 * @property string $ifragment Fragment part of the IRI (after '#')
 */
class Iri {
	/**
	 * Scheme
	 *
	 * @var string|null
	 */
	protected $scheme = null;

	/**
	 * User Information
	 *
	 * @var string|null
	 */
	protected $iuserinfo = null;

	/**
	 * ihost
	 *
	 * @var string|null
	 */
	protected $ihost = null;

	/**
	 * Port
	 *
	 * @var string|null
	 */
	protected $port = null;

	/**
	 * ipath
	 *
	 * @var string
	 */
	protected $ipath = '';

	/**
	 * iquery
	 *
	 * @var string|null
	 */
	protected $iquery = null;

	/**
	 * ifragment|null
	 *
	 * @var string
	 */
	protected $ifragment = null;

	/**
	 * Normalization database
	 *
	 * Each key is the scheme, each value is an array with each key as the IRI
	 * part and value as the default value for that part.
	 *
	 * @var array
	 */
	protected $normalization = array(
		'acap' => array(
			'port' => Port::ACAP,
		),
		'dict' => array(
			'port' => Port::DICT,
		),
		'file' => array(
			'ihost' => 'localhost',
		),
		'http' => array(
			'port' => Port::HTTP,
		),
		'https' => array(
			'port' => Port::HTTPS,
		),
	);

	/**
	 * Return the entire IRI when you try and read the object as a string
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->get_iri();
	}

	/**
	 * Overload __set() to provide access via properties
	 *
	 * @param string $name Property name
	 * @param mixed $value Property value
	 */
	public function __set($name, $value) {
		if (method_exists($this, 'set_' . $name)) {
			call_user_func(array($this, 'set_' . $name), $value);
		}
		elseif (
			   $name === 'iauthority'
			|| $name === 'iuserinfo'
			|| $name === 'ihost'
			|| $name === 'ipath'
			|| $name === 'iquery'
			|| $name === 'ifragment'
		) {
			call_user_func(array($this, 'set_' . substr($name, 1)), $value);
		}
	}

	/**
	 * Overload __get() to provide access via properties
	 *
	 * @param string $name Property name
	 * @return mixed
	 */
	public function __get($name) {
		// isset() returns false for null, we don't want to do that
		// Also why we use array_key_exists below instead of isset()
		$props = get_object_vars($this);

		if (
			$name === 'iri' ||
			$name === 'uri' ||
			$name === 'iauthority' ||
			$name === 'authority'
		) {
			$method = 'get_' . $name;
			$return = $this->$method();
		}
		elseif (array_key_exists($name, $props)) {
			$return = $this->$name;
		}
		// host -> ihost
		elseif (($prop = 'i' . $name) && array_key_exists($prop, $props)) {
			$name = $prop;
			$return = $this->$prop;
		}
		// ischeme -> scheme
		elseif (($prop = substr($name, 1)) && array_key_exists($prop, $props)) {
			$name = $prop;
			$return = $this->$prop;
		}
		else {
			trigger_error('Undefined property: ' . get_class($this) . '::' . $name, E_USER_NOTICE);
			$return = null;
		}

		if ($return === null && isset($this->normalization[$this->scheme][$name])) {
			return $this->normalization[$this->scheme][$name];
		}
		else {
			return $return;
		}
	}

	/**
	 * Overload __isset() to provide access via properties
	 *
	 * @param string $name Property name
	 * @return bool
	 */
	public function __isset($name) {
		return (method_exists($this, 'get_' . $name) || isset($this->$name));
	}

	/**
	 * Overload __unset() to provide access via properties
	 *
	 * @param string $name Property name
	 */
	public function __unset($name) {
		if (method_exists($this, 'set_' . $name)) {
			call_user_func(array($this, 'set_' . $name), '');
		}
	}

	/**
	 * Create a new IRI object, from a specified string
	 *
	 * @param string|Stringable|null $iri
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $iri argument is not a string, Stringable or null.
	 */
	public function __construct($iri = null) {
		if ($iri !== null && InputValidator::is_string_or_stringable($iri) === false) {
			throw InvalidArgument::create(1, '$iri', 'string|Stringable|null', gettype($iri));
		}

		$this->set_iri($iri);
	}

	/**
	 * Create a new IRI object by resolving a relative IRI
	 *
	 * Returns false if $base is not absolute, otherwise an IRI.
	 *
	 * @param \WpOrg\Requests\Iri|string $base (Absolute) Base IRI
	 * @param \WpOrg\Requests\Iri|string $relative Relative IRI
	 * @return \WpOrg\Requests\Iri|false
	 */
	public static function absolutize($base, $relative) {
		if (!($relative instanceof self)) {
			$relative = new self($relative);
		}
		if (!$relative->is_valid()) {
			return false;
		}
		elseif ($relative->scheme !== null) {
			return clone $relative;
		}

		if (!($base instanceof self)) {
			$base = new self($base);
		}
		if ($base->scheme === null || !$base->is_valid()) {
			return false;
		}

		if ($relative->get_iri() !== '') {
			if ($relative->iuserinfo !== null || $relative->ihost !== null || $relative->port !== null) {
				$target = clone $relative;
				$target->scheme = $base->scheme;
			}
			else {
				$target = new self;
				$target->scheme = $base->scheme;
				$target->iuserinfo = $base->iuserinfo;
				$target->ihost = $base->ihost;
				$target->port = $base->port;
				if ($relative->ipath !== '') {
					if ($relative->ipath[0] === '/') {
						$target->ipath = $relative->ipath;
					}
					elseif (($base->iuserinfo !== null || $base->ihost !== null || $base->port !== null) && $base->ipath === '') {
						$target->ipath = '/' . $relative->ipath;
					}
					elseif (($last_segment = strrpos($base->ipath, '/')) !== false) {
						$target->ipath = substr($base->ipath, 0, $last_segment + 1) . $relative->ipath;
					}
					else {
						$target->ipath = $relative->ipath;
					}
					$target->ipath = $target->remove_dot_segments($target->ipath);
					$target->iquery = $relative->iquery;
				}
				else {
					$target->ipath = $base->ipath;
					if ($relative->iquery !== null) {
						$target->iquery = $relative->iquery;
					}
					elseif ($base->iquery !== null) {
						$target->iquery = $base->iquery;
					}
				}
				$target->ifragment = $relative->ifragment;
			}
		}
		else {
			$target = clone $base;
			$target->ifragment = null;
		}
		$target->scheme_normalization();
		return $target;
	}

	/**
	 * Parse an IRI into scheme/authority/path/query/fragment segments
	 *
	 * @param string $iri
	 * @return array
	 */
	protected function parse_iri($iri) {
		$iri = trim($iri, "\x20\x09\x0A\x0C\x0D");
		$has_match = preg_match('/^((?P<scheme>[^:\/?#]+):)?(\/\/(?P<authority>[^\/?#]*))?(?P<path>[^?#]*)(\?(?P<query>[^#]*))?(#(?P<fragment>.*))?$/', $iri, $match);
		if (!$has_match) {
			throw new Exception('Cannot parse supplied IRI', 'iri.cannot_parse', $iri);
		}

		if ($match[1] === '') {
			$match['scheme'] = null;
		}
		if (!isset($match[3]) || $match[3] === '') {
			$match['authority'] = null;
		}
		if (!isset($match[5])) {
			$match['path'] = '';
		}
		if (!isset($match[6]) || $match[6] === '') {
			$match['query'] = null;
		}
		if (!isset($match[8]) || $match[8] === '') {
			$match['fragment'] = null;
		}
		return $match;
	}

	/**
	 * Remove dot segments from a path
	 *
	 * @param string $input
	 * @return string
	 */
	protected function remove_dot_segments($input) {
		$output = '';
		while (strpos($input, './') !== false || strpos($input, '/.') !== false || $input === '.' || $input === '..') {
			// A: If the input buffer begins with a prefix of "../" or "./",
			// then remove that prefix from the input buffer; otherwise,
			if (strpos($input, '../') === 0) {
				$input = substr($input, 3);
			}
			elseif (strpos($input, './') === 0) {
				$input = substr($input, 2);
			}
			// B: if the input buffer begins with a prefix of "/./" or "/.",
			// where "." is a complete path segment, then replace that prefix
			// with "/" in the input buffer; otherwise,
			elseif (strpos($input, '/./') === 0) {
				$input = substr($input, 2);
			}
			elseif ($input === '/.') {
				$input = '/';
			}
			// C: if the input buffer begins with a prefix of "/../" or "/..",
			// where ".." is a complete path segment, then replace that prefix
			// with "/" in the input buffer and remove the last segment and its
			// preceding "/" (if any) from the output buffer; otherwise,
			elseif (strpos($input, '/../') === 0) {
				$input = substr($input, 3);
				$output = substr_replace($output, '', (strrpos($output, '/') ?: 0));
			}
			elseif ($input === '/..') {
				$input = '/';
				$output = substr_replace($output, '', (strrpos($output, '/') ?: 0));
			}
			// D: if the input buffer consists only of "." or "..", then remove
			// that from the input buffer; otherwise,
			elseif ($input === '.' || $input === '..') {
				$input = '';
			}
			// E: move the first path segment in the input buffer to the end of
			// the output buffer, including the initial "/" character (if any)
			// and any subsequent characters up to, but not including, the next
			// "/" character or the end of the input buffer
			elseif (($pos = strpos($input, '/', 1)) !== false) {
				$output .= substr($input, 0, $pos);
				$input = substr_replace($input, '', 0, $pos);
			}
			else {
				$output .= $input;
				$input = '';
			}
		}
		return $output . $input;
	}

	/**
	 * Replace invalid character with percent encoding
	 *
	 * @param string $text Input string
	 * @param string $extra_chars Valid characters not in iunreserved or
	 *                            iprivate (this is ASCII-only)
	 * @param bool $iprivate Allow iprivate
	 * @return string
	 */
	protected function replace_invalid_with_pct_encoding($text, $extra_chars, $iprivate = false) {
		// Normalize as many pct-encoded sections as possible
		$text = preg_replace_callback('/(?:%[A-Fa-f0-9]{2})+/', array($this, 'remove_iunreserved_percent_encoded'), $text);

		// Replace invalid percent characters
		$text = preg_replace('/%(?![A-Fa-f0-9]{2})/', '%25', $text);

		// Add unreserved and % to $extra_chars (the latter is safe because all
		// pct-encoded sections are now valid).
		$extra_chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~%';

		// Now replace any bytes that aren't allowed with their pct-encoded versions
		$position = 0;
		$strlen = strlen($text);
		while (($position += strspn($text, $extra_chars, $position)) < $strlen) {
			$value = ord($text[$position]);

			// Start position
			$start = $position;

			// By default we are valid
			$valid = true;

			// No one byte sequences are valid due to the while.
			// Two byte sequence:
			if (($value & 0xE0) === 0xC0) {
				$character = ($value & 0x1F) << 6;
				$length = 2;
				$remaining = 1;
			}
			// Three byte sequence:
			elseif (($value & 0xF0) === 0xE0) {
				$character = ($value & 0x0F) << 12;
				$length = 3;
				$remaining = 2;
			}
			// Four byte sequence:
			elseif (($value & 0xF8) === 0xF0) {
				$character = ($value & 0x07) << 18;
				$length = 4;
				$remaining = 3;
			}
			// Invalid byte:
			else {
				$valid = false;
				$length = 1;
				$remaining = 0;
			}

			if ($remaining) {
				if ($position + $length <= $strlen) {
					for ($position++; $remaining; $position++) {
						$value = ord($text[$position]);

						// Check that the byte is valid, then add it to the character:
						if (($value & 0xC0) === 0x80) {
							$character |= ($value & 0x3F) << (--$remaining * 6);
						}
						// If it is invalid, count the sequence as invalid and reprocess the current byte:
						else {
							$valid = false;
							$position--;
							break;
						}
					}
				}
				else {
					$position = $strlen - 1;
					$valid = false;
				}
			}

			// Percent encode anything invalid or not in ucschar
			if (
				// Invalid sequences
				!$valid
				// Non-shortest form sequences are invalid
				|| $length > 1 && $character <= 0x7F
				|| $length > 2 && $character <= 0x7FF
				|| $length > 3 && $character <= 0xFFFF
				// Outside of range of ucschar codepoints
				// Noncharacters
				|| ($character & 0xFFFE) === 0xFFFE
				|| $character >= 0xFDD0 && $character <= 0xFDEF
				|| (
					// Everything else not in ucschar
					   $character > 0xD7FF && $character < 0xF900
					|| $character < 0xA0
					|| $character > 0xEFFFD
				)
				&& (
					// Everything not in iprivate, if it applies
					   !$iprivate
					|| $character < 0xE000
					|| $character > 0x10FFFD
				)
			) {
				// If we were a character, pretend we weren't, but rather an error.
				if ($valid) {
					$position--;
				}

				for ($j = $start; $j <= $position; $j++) {
					$text = substr_replace($text, sprintf('%%%02X', ord($text[$j])), $j, 1);
					$j += 2;
					$position += 2;
					$strlen += 2;
				}
			}
		}

		return $text;
	}

	/**
	 * Callback function for preg_replace_callback.
	 *
	 * Removes sequences of percent encoded bytes that represent UTF-8
	 * encoded characters in iunreserved
	 *
	 * @param array $regex_match PCRE match
	 * @return string Replacement
	 */
	protected function remove_iunreserved_percent_encoded($regex_match) {
		// As we just have valid percent encoded sequences we can just explode
		// and ignore the first member of the returned array (an empty string).
		$bytes = explode('%', $regex_match[0]);

		// Initialize the new string (this is what will be returned) and that
		// there are no bytes remaining in the current sequence (unsurprising
		// at the first byte!).
		$string = '';
		$remaining = 0;

		// Loop over each and every byte, and set $value to its value
		for ($i = 1, $len = count($bytes); $i < $len; $i++) {
			$value = hexdec($bytes[$i]);

			// If we're the first byte of sequence:
			if (!$remaining) {
				// Start position
				$start = $i;

				// By default we are valid
				$valid = true;

				// One byte sequence:
				if ($value <= 0x7F) {
					$character = $value;
					$length = 1;
				}
				// Two byte sequence:
				elseif (($value & 0xE0) === 0xC0) {
					$character = ($value & 0x1F) << 6;
					$length = 2;
					$remaining = 1;
				}
				// Three byte sequence:
				elseif (($value & 0xF0) === 0xE0) {
					$character = ($value & 0x0F) << 12;
					$length = 3;
					$remaining = 2;
				}
				// Four byte sequence:
				elseif (($value & 0xF8) === 0xF0) {
					$character = ($value & 0x07) << 18;
					$length = 4;
					$remaining = 3;
				}
				// Invalid byte:
				else {
					$valid = false;
					$remaining = 0;
				}
			}
			// Continuation byte:
			else {
				// Check that the byte is valid, then add it to the character:
				if (($value & 0xC0) === 0x80) {
					$remaining--;
					$character |= ($value & 0x3F) << ($remaining * 6);
				}
				// If it is invalid, count the sequence as invalid and reprocess the current byte as the start of a sequence:
				else {
					$valid = false;
					$remaining = 0;
					$i--;
				}
			}

			// If we've reached the end of the current byte sequence, append it to Unicode::$data
			if (!$remaining) {
				// Percent encode anything invalid or not in iunreserved
				if (
					// Invalid sequences
					!$valid
					// Non-shortest form sequences are invalid
					|| $length > 1 && $character <= 0x7F
					|| $length > 2 && $character <= 0x7FF
					|| $length > 3 && $character <= 0xFFFF
					// Outside of range of iunreserved codepoints
					|| $character < 0x2D
					|| $character > 0xEFFFD
					// Noncharacters
					|| ($character & 0xFFFE) === 0xFFFE
					|| $character >= 0xFDD0 && $character <= 0xFDEF
					// Everything else not in iunreserved (this is all BMP)
					|| $character === 0x2F
					|| $character > 0x39 && $character < 0x41
					|| $character > 0x5A && $character < 0x61
					|| $character > 0x7A && $character < 0x7E
					|| $character > 0x7E && $character < 0xA0
					|| $character > 0xD7FF && $character < 0xF900
				) {
					for ($j = $start; $j <= $i; $j++) {
						$string .= '%' . strtoupper($bytes[$j]);
					}
				}
				else {
					for ($j = $start; $j <= $i; $j++) {
						$string .= chr(hexdec($bytes[$j]));
					}
				}
			}
		}

		// If we have any bytes left over they are invalid (i.e., we are
		// mid-way through a multi-byte sequence)
		if ($remaining) {
			for ($j = $start; $j < $len; $j++) {
				$string .= '%' . strtoupper($bytes[$j]);
			}
		}

		return $string;
	}

	protected function scheme_normalization() {
		if (isset($this->normalization[$this->scheme]['iuserinfo']) && $this->iuserinfo === $this->normalization[$this->scheme]['iuserinfo']) {
			$this->iuserinfo = null;
		}
		if (isset($this->normalization[$this->scheme]['ihost']) && $this->ihost === $this->normalization[$this->scheme]['ihost']) {
			$this->ihost = null;
		}
		if (isset($this->normalization[$this->scheme]['port']) && $this->port === $this->normalization[$this->scheme]['port']) {
			$this->port = null;
		}
		if (isset($this->normalization[$this->scheme]['ipath']) && $this->ipath === $this->normalization[$this->scheme]['ipath']) {
			$this->ipath = '';
		}
		if (isset($this->ihost) && empty($this->ipath)) {
			$this->ipath = '/';
		}
		if (isset($this->normalization[$this->scheme]['iquery']) && $this->iquery === $this->normalization[$this->scheme]['iquery']) {
			$this->iquery = null;
		}
		if (isset($this->normalization[$this->scheme]['ifragment']) && $this->ifragment === $this->normalization[$this->scheme]['ifragment']) {
			$this->ifragment = null;
		}
	}

	/**
	 * Check if the object represents a valid IRI. This needs to be done on each
	 * call as some things change depending on another part of the IRI.
	 *
	 * @return bool
	 */
	public function is_valid() {
		$isauthority = $this->iuserinfo !== null || $this->ihost !== null || $this->port !== null;
		if ($this->ipath !== '' &&
			(
				$isauthority && $this->ipath[0] !== '/' ||
				(
					$this->scheme === null &&
					!$isauthority &&
					strpos($this->ipath, ':') !== false &&
					(strpos($this->ipath, '/') === false ? true : strpos($this->ipath, ':') < strpos($this->ipath, '/'))
				)
			)
		) {
			return false;
		}

		return true;
	}

	public function __wakeup() {
		$class_props = get_class_vars( __CLASS__ );
		$string_props = array( 'scheme', 'iuserinfo', 'ihost', 'port', 'ipath', 'iquery', 'ifragment' );
		$array_props = array( 'normalization' );
		foreach ( $class_props as $prop => $default_value ) {
			if ( in_array( $prop, $string_props, true ) && ! is_string( $this->$prop ) ) {
				throw new UnexpectedValueException();
			} elseif ( in_array( $prop, $array_props, true ) && ! is_array( $this->$prop ) ) {
				throw new UnexpectedValueException();
			}
			$this->$prop = null;
		}
	}

	/**
	 * Set the entire IRI. Returns true on success, false on failure (if there
	 * are any invalid characters).
	 *
	 * @param string $iri
	 * @return bool
	 */
	protected function set_iri($iri) {
		static $cache;
		if (!$cache) {
			$cache = array();
		}

		if ($iri === null) {
			return true;
		}

		$iri = (string) $iri;

		if (isset($cache[$iri])) {
			list($this->scheme,
				 $this->iuserinfo,
				 $this->ihost,
				 $this->port,
				 $this->ipath,
				 $this->iquery,
				 $this->ifragment,
				 $return) = $cache[$iri];
			return $return;
		}

		$parsed = $this->parse_iri($iri);

		$return = $this->set_scheme($parsed['scheme'])
			&& $this->set_authority($parsed['authority'])
			&& $this->set_path($parsed['path'])
			&& $this->set_query($parsed['query'])
			&& $this->set_fragment($parsed['fragment']);

		$cache[$iri] = array($this->scheme,
							 $this->iuserinfo,
							 $this->ihost,
							 $this->port,
							 $this->ipath,
							 $this->iquery,
							 $this->ifragment,
							 $return);
		return $return;
	}

	/**
	 * Set the scheme. Returns true on success, false on failure (if there are
	 * any invalid characters).
	 *
	 * @param string $scheme
	 * @return bool
	 */
	protected function set_scheme($scheme) {
		if ($scheme === null) {
			$this->scheme = null;
		}
		elseif (!preg_match('/^[A-Za-z][0-9A-Za-z+\-.]*$/', $scheme)) {
			$this->scheme = null;
			return false;
		}
		else {
			$this->scheme = strtolower($scheme);
		}
		return true;
	}

	/**
	 * Set the authority. Returns true on success, false on failure (if there are
	 * any invalid characters).
	 *
	 * @param string $authority
	 * @return bool
	 */
	protected function set_authority($authority) {
		static $cache;
		if (!$cache) {
			$cache = array();
		}

		if ($authority === null) {
			$this->iuserinfo = null;
			$this->ihost = null;
			$this->port = null;
			return true;
		}
		if (isset($cache[$authority])) {
			list($this->iuserinfo,
				 $this->ihost,
				 $this->port,
				 $return) = $cache[$authority];

			return $return;
		}

		$remaining = $authority;
		if (($iuserinfo_end = strrpos($remaining, '@')) !== false) {
			$iuserinfo = substr($remaining, 0, $iuserinfo_end);
			$remaining = substr($remaining, $iuserinfo_end + 1);
		}
		else {
			$iuserinfo = null;
		}

		if (($port_start = strpos($remaining, ':', (strpos($remaining, ']') ?: 0))) !== false) {
			$port = substr($remaining, $port_start + 1);
			if ($port === false || $port === '') {
				$port = null;
			}
			$remaining = substr($remaining, 0, $port_start);
		}
		else {
			$port = null;
		}

		$return = $this->set_userinfo($iuserinfo) &&
				  $this->set_host($remaining) &&
				  $this->set_port($port);

		$cache[$authority] = array($this->iuserinfo,
								   $this->ihost,
								   $this->port,
								   $return);

		return $return;
	}

	/**
	 * Set the iuserinfo.
	 *
	 * @param string $iuserinfo
	 * @return bool
	 */
	protected function set_userinfo($iuserinfo) {
		if ($iuserinfo === null) {
			$this->iuserinfo = null;
		}
		else {
			$this->iuserinfo = $this->replace_invalid_with_pct_encoding($iuserinfo, '!$&\'()*+,;=:');
			$this->scheme_normalization();
		}

		return true;
	}

	/**
	 * Set the ihost. Returns true on success, false on failure (if there are
	 * any invalid characters).
	 *
	 * @param string $ihost
	 * @return bool
	 */
	protected function set_host($ihost) {
		if ($ihost === null) {
			$this->ihost = null;
			return true;
		}
		if (substr($ihost, 0, 1) === '[' && substr($ihost, -1) === ']') {
			if (Ipv6::check_ipv6(substr($ihost, 1, -1))) {
				$this->ihost = '[' . Ipv6::compress(substr($ihost, 1, -1)) . ']';
			}
			else {
				$this->ihost = null;
				return false;
			}
		}
		else {
			$ihost = $this->replace_invalid_with_pct_encoding($ihost, '!$&\'()*+,;=');

			// Lowercase, but ignore pct-encoded sections (as they should
			// remain uppercase). This must be done after the previous step
			// as that can add unescaped characters.
			$position = 0;
			$strlen = strlen($ihost);
			while (($position += strcspn($ihost, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ%', $position)) < $strlen) {
				if ($ihost[$position] === '%') {
					$position += 3;
				}
				else {
					$ihost[$position] = strtolower($ihost[$position]);
					$position++;
				}
			}

			$this->ihost = $ihost;
		}

		$this->scheme_normalization();

		return true;
	}

	/**
	 * Set the port. Returns true on success, false on failure (if there are
	 * any invalid characters).
	 *
	 * @param string $port
	 * @return bool
	 */
	protected function set_port($port) {
		if ($port === null) {
			$this->port = null;
			return true;
		}

		if (strspn($port, '0123456789') === strlen($port)) {
			$this->port = (int) $port;
			$this->scheme_normalization();
			return true;
		}

		$this->port = null;
		return false;
	}

	/**
	 * Set the ipath.
	 *
	 * @param string $ipath
	 * @return bool
	 */
	protected function set_path($ipath) {
		static $cache;
		if (!$cache) {
			$cache = array();
		}

		$ipath = (string) $ipath;

		if (isset($cache[$ipath])) {
			$this->ipath = $cache[$ipath][(int) ($this->scheme !== null)];
		}
		else {
			$valid = $this->replace_invalid_with_pct_encoding($ipath, '!$&\'()*+,;=@:/');
			$removed = $this->remove_dot_segments($valid);

			$cache[$ipath] = array($valid, $removed);
			$this->ipath = ($this->scheme !== null) ? $removed : $valid;
		}
		$this->scheme_normalization();
		return true;
	}

	/**
	 * Set the iquery.
	 *
	 * @param string $iquery
	 * @return bool
	 */
	protected function set_query($iquery) {
		if ($iquery === null) {
			$this->iquery = null;
		}
		else {
			$this->iquery = $this->replace_invalid_with_pct_encoding($iquery, '!$&\'()*+,;=:@/?', true);
			$this->scheme_normalization();
		}
		return true;
	}

	/**
	 * Set the ifragment.
	 *
	 * @param string $ifragment
	 * @return bool
	 */
	protected function set_fragment($ifragment) {
		if ($ifragment === null) {
			$this->ifragment = null;
		}
		else {
			$this->ifragment = $this->replace_invalid_with_pct_encoding($ifragment, '!$&\'()*+,;=:@/?');
			$this->scheme_normalization();
		}
		return true;
	}

	/**
	 * Convert an IRI to a URI (or parts thereof)
	 *
	 * @param string|bool $iri IRI to convert (or false from {@see \WpOrg\Requests\Iri::get_iri()})
	 * @return string|false URI if IRI is valid, false otherwise.
	 */
	protected function to_uri($iri) {
		if (!is_string($iri)) {
			return false;
		}

		static $non_ascii;
		if (!$non_ascii) {
			$non_ascii = implode('', range("\x80", "\xFF"));
		}

		$position = 0;
		$strlen = strlen($iri);
		while (($position += strcspn($iri, $non_ascii, $position)) < $strlen) {
			$iri = substr_replace($iri, sprintf('%%%02X', ord($iri[$position])), $position, 1);
			$position += 3;
			$strlen += 2;
		}

		return $iri;
	}

	/**
	 * Get the complete IRI
	 *
	 * @return string|false
	 */
	protected function get_iri() {
		if (!$this->is_valid()) {
			return false;
		}

		$iri = '';
		if ($this->scheme !== null) {
			$iri .= $this->scheme . ':';
		}
		if (($iauthority = $this->get_iauthority()) !== null) {
			$iri .= '//' . $iauthority;
		}
		$iri .= $this->ipath;
		if ($this->iquery !== null) {
			$iri .= '?' . $this->iquery;
		}
		if ($this->ifragment !== null) {
			$iri .= '#' . $this->ifragment;
		}

		return $iri;
	}

	/**
	 * Get the complete URI
	 *
	 * @return string
	 */
	protected function get_uri() {
		return $this->to_uri($this->get_iri());
	}

	/**
	 * Get the complete iauthority
	 *
	 * @return string|null
	 */
	protected function get_iauthority() {
		if ($this->iuserinfo === null && $this->ihost === null && $this->port === null) {
			return null;
		}

		$iauthority = '';
		if ($this->iuserinfo !== null) {
			$iauthority .= $this->iuserinfo . '@';
		}
		if ($this->ihost !== null) {
			$iauthority .= $this->ihost;
		}
		if ($this->port !== null) {
			$iauthority .= ':' . $this->port;
		}
		return $iauthority;
	}

	/**
	 * Get the complete authority
	 *
	 * @return string
	 */
	protected function get_authority() {
		$iauthority = $this->get_iauthority();
		if (is_string($iauthority)) {
			return $this->to_uri($iauthority);
		}
		else {
			return $iauthority;
		}
	}
}

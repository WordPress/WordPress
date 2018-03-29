<?php

class wfWAFUtils {

	/**
	 * Return dot or colon notation of IPv4 or IPv6 address.
	 *
	 * @param string $ip
	 * @return string|bool
	 */
	public static function inet_ntop($ip) {
		// trim this to the IPv4 equiv if it's in the mapped range
		if (wfWAFUtils::strlen($ip) == 16 && wfWAFUtils::substr($ip, 0, 12) == "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff") {
			$ip = wfWAFUtils::substr($ip, 12, 4);
		}
		return self::hasIPv6Support() ? @inet_ntop($ip) : self::_inet_ntop($ip);
	}

	/**
	 * Return the packed binary string of an IPv4 or IPv6 address.
	 *
	 * @param string $ip
	 * @return string
	 */
	public static function inet_pton($ip) {
		// convert the 4 char IPv4 to IPv6 mapped version.
		$pton = str_pad(self::hasIPv6Support() ? @inet_pton($ip) : self::_inet_pton($ip), 16,
			"\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff\x00\x00\x00\x00", STR_PAD_LEFT);
		return $pton;
	}

	/**
	 * Added compatibility for hosts that do not have inet_pton.
	 *
	 * @param $ip
	 * @return bool|string
	 */
	public static function _inet_pton($ip) {
		// IPv4
		if (preg_match('/^(?:\d{1,3}(?:\.|$)){4}/', $ip)) {
			$octets = explode('.', $ip);
			$bin = chr($octets[0]) . chr($octets[1]) . chr($octets[2]) . chr($octets[3]);
			return $bin;
		}

		// IPv6
		if (preg_match('/^((?:[\da-f]{1,4}(?::|)){0,8})(::)?((?:[\da-f]{1,4}(?::|)){0,8})$/i', $ip)) {
			if ($ip === '::') {
				return "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
			}
			$colon_count = wfWAFUtils::substr_count($ip, ':');
			$dbl_colon_pos = wfWAFUtils::strpos($ip, '::');
			if ($dbl_colon_pos !== false) {
				$ip = str_replace('::', str_repeat(':0000',
						(($dbl_colon_pos === 0 || $dbl_colon_pos === wfWAFUtils::strlen($ip) - 2) ? 9 : 8) - $colon_count) . ':', $ip);
				$ip = trim($ip, ':');
			}

			$ip_groups = explode(':', $ip);
			$ipv6_bin = '';
			foreach ($ip_groups as $ip_group) {
				$ipv6_bin .= pack('H*', str_pad($ip_group, 4, '0', STR_PAD_LEFT));
			}

			return wfWAFUtils::strlen($ipv6_bin) === 16 ? $ipv6_bin : false;
		}

		// IPv4 mapped IPv6
		if (preg_match('/^((?:0{1,4}(?::|)){0,5})(::)?ffff:((?:\d{1,3}(?:\.|$)){4})$/i', $ip, $matches)) {
			$octets = explode('.', $matches[3]);
			return "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff" . chr($octets[0]) . chr($octets[1]) . chr($octets[2]) . chr($octets[3]);
		}

		return false;
	}

	/**
	 * Added compatibility for hosts that do not have inet_ntop.
	 *
	 * @param $ip
	 * @return bool|string
	 */
	public static function _inet_ntop($ip) {
		// IPv4
		if (wfWAFUtils::strlen($ip) === 4) {
			return ord($ip[0]) . '.' . ord($ip[1]) . '.' . ord($ip[2]) . '.' . ord($ip[3]);
		}

		// IPv6
		if (wfWAFUtils::strlen($ip) === 16) {

			// IPv4 mapped IPv6
			if (wfWAFUtils::substr($ip, 0, 12) == "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff") {
				return "::ffff:" . ord($ip[12]) . '.' . ord($ip[13]) . '.' . ord($ip[14]) . '.' . ord($ip[15]);
			}

			$hex = bin2hex($ip);
			$groups = str_split($hex, 4);
			$collapse = false;
			$done_collapse = false;
			foreach ($groups as $index => $group) {
				if ($group == '0000' && !$done_collapse) {
					if (!$collapse) {
						$groups[$index] = ':';
					} else {
						$groups[$index] = '';
					}
					$collapse = true;
				} else if ($collapse) {
					$done_collapse = true;
					$collapse = false;
				}
				$groups[$index] = ltrim($groups[$index], '0');
			}
			$ip = join(':', array_filter($groups));
			$ip = str_replace(':::', '::', $ip);
			return $ip == ':' ? '::' : $ip;
		}

		return false;
	}

	/**
	 * Verify PHP was compiled with IPv6 support.
	 *
	 * Some hosts appear to not have inet_ntop, and others appear to have inet_ntop but are unable to process IPv6 addresses.
	 *
	 * @return bool
	 */
	public static function hasIPv6Support() {
		return defined('AF_INET6');
	}

	/**
	 * Expand a compressed printable representation of an IPv6 address.
	 *
	 * @param string $ip
	 * @return string
	 */
	public static function expandIPv6Address($ip) {
		$hex = bin2hex(self::inet_pton($ip));
		$ip = wfWAFUtils::substr(preg_replace("/([a-f0-9]{4})/i", "$1:", $hex), 0, -1);
		return $ip;
	}

	protected static $servicesJSON;

	public static function json_encode($string) {
		if (function_exists('json_encode')) {
			return json_encode($string);
		} else {
			if (!self::$servicesJSON) {
				require_once WFWAF_LIB_PATH . 'json.php';
				self::$servicesJSON = new wfServices_JSON();
			}
			return self::$servicesJSON->encodeUnsafe($string);
		}
	}

	public static function json_decode($string, $assoc_array = false) {
		if (function_exists('json_decode')) {
			return json_decode($string, $assoc_array);
		} else {
			if (!self::$servicesJSON) {
				require_once WFWAF_LIB_PATH . 'json.php';
				self::$servicesJSON = new wfServices_JSON();
			}
			$res = self::$servicesJSON->decode($string);
			if ($assoc_array)
				$res = self::_json_decode_object_helper($res);
			return $res;

		}
	}

	/**
	 * @param object $data
	 * @return array
	 */
	protected static function _json_decode_object_helper($data) {
		if (is_object($data))
			$data = get_object_vars($data);
		return is_array($data) ? array_map('wfWAFUtils::_json_decode_object_helper', $data) : $data;
	}

	/**
	 * Compare two strings in constant time. It can leak the length of a string.
	 *
	 * @param string $a Expected string.
	 * @param string $b Actual string.
	 * @return bool Whether strings are equal.
	 */
	public static function hash_equals($a, $b) {
		$a_length = wfWAFUtils::strlen($a);
		if ($a_length !== wfWAFUtils::strlen($b)) {
			return false;
		}
		$result = 0;

		// Do not attempt to "optimize" this.
		for ($i = 0; $i < $a_length; $i++) {
			$result |= ord($a[$i]) ^ ord($b[$i]);
		}

		return $result === 0;
	}

	/**
	 * @param $algo
	 * @param $data
	 * @param $key
	 * @param bool|false $raw_output
	 * @return bool|string
	 */
	public static function hash_hmac($algo, $data, $key, $raw_output = false) {
		if (function_exists('hash_hmac')) {
			return hash_hmac($algo, $data, $key, $raw_output);
		}
		return self::_hash_hmac($algo, $data, $key, $raw_output);
	}

	/**
	 * @param $algo
	 * @param $data
	 * @param $key
	 * @param bool|false $raw_output
	 * @return bool|string
	 */
	private static function _hash_hmac($algo, $data, $key, $raw_output = false) {
		$packs = array('md5' => 'H32', 'sha1' => 'H40');

		if (!isset($packs[$algo]))
			return false;

		$pack = $packs[$algo];

		if (wfWAFUtils::strlen($key) > 64)
			$key = pack($pack, $algo($key));

		$key = str_pad($key, 64, chr(0));

		$ipad = (wfWAFUtils::substr($key, 0, 64) ^ str_repeat(chr(0x36), 64));
		$opad = (wfWAFUtils::substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64));

		$hmac = $algo($opad . pack($pack, $algo($ipad . $data)));

		if ($raw_output)
			return pack($pack, $hmac);
		return $hmac;
	}

	/**
	 * @param int $length
	 * @param string $chars
	 * @return string
	 */
	public static function getRandomString($length = 16, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|') {
		// This is faster than calling self::random_int for $length
		$bytes = self::random_bytes($length);
		$return = '';
		$maxIndex = wfWAFUtils::strlen($chars) - 1;
		for ($i = 0; $i < $length; $i++) {
			$fp = (float) ord($bytes[$i]) / 255.0; // convert to [0,1]
			$index = (int) (round($fp * $maxIndex));
			$return .= $chars[$index];
		}
		return $return;
	}

	/**
	 * Polyfill for random_bytes.
	 *
	 * @param int $bytes
	 * @return string
	 */
	public static function random_bytes($bytes) {
		$bytes = (int) $bytes;
		if (function_exists('random_bytes')) {
			try {
				$rand = random_bytes($bytes);
				if (is_string($rand) && wfWAFUtils::strlen($rand) === $bytes) {
					return $rand;
				}
			} catch (Exception $e) {
				// Fall through
			} catch (TypeError $e) {
				// Fall through
			} catch (Error $e) {
				// Fall through
			}
		}
		if (function_exists('mcrypt_create_iv')) {
			$rand = @mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM);
			if (is_string($rand) && wfWAFUtils::strlen($rand) === $bytes) {
				return $rand;
			}
		}
		if (function_exists('openssl_random_pseudo_bytes')) {
			$rand = @openssl_random_pseudo_bytes($bytes, $strong);
			if (is_string($rand) && wfWAFUtils::strlen($rand) === $bytes) {
				return $rand;
			}
		}
		// Last resort is insecure
		$return = '';
		for ($i = 0; $i < $bytes; $i++) {
			$return .= chr(mt_rand(0, 255));
		}
		return $return;
	}

	/**
	 * Polyfill for random_int.
	 *
	 * @param int $min
	 * @param int $max
	 * @return int
	 */
	public static function random_int($min = 0, $max = 0x7FFFFFFF) {
		if (function_exists('random_int')) {
			try {
				return random_int($min, $max);
			} catch (Exception $e) {
				// Fall through
			} catch (TypeError $e) {
				// Fall through
			} catch (Error $e) {
				// Fall through
			}
		}
		$diff = $max - $min;
		$bytes = self::random_bytes(4);
		if ($bytes === false || wfWAFUtils::strlen($bytes) != 4) {
			throw new RuntimeException("Unable to get 4 bytes");
		}
		$val = @unpack("Nint", $bytes);
		$val = $val['int'] & 0x7FFFFFFF;
		$fp = (float) $val / 2147483647.0; // convert to [0,1]
		return (int) (round($fp * $diff) + $min);
	}

	/**
	 * @param mixed $subject
	 * @return array|string
	 */
	public static function stripMagicQuotes($subject) {
		$sybase = ini_get('magic_quotes_sybase');
		$sybaseEnabled = ((is_numeric($sybase) && $sybase) ||
			(is_string($sybase) && $sybase && !in_array(wfWAFUtils::strtolower($sybase), array(
					'off',
					'false'
				))));
		if ((function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) || $sybaseEnabled) {
			return self::stripslashes_deep($subject);
		}
		return $subject;
	}

	/**
	 * @param mixed $subject
	 * @return array|string
	 */
	public static function stripslashes_deep($subject) {
		if (is_array($subject)) {
			return array_map(array(
				'self', 'stripslashes_deep',
			), $subject);
		} else if (is_string($subject)) {
			return stripslashes($subject);
		}
		return $subject;
	}


	/**
	 * Set the mbstring internal encoding to a binary safe encoding when func_overload
	 * is enabled.
	 *
	 * When mbstring.func_overload is in use for multi-byte encodings, the results from
	 * strlen() and similar functions respect the utf8 characters, causing binary data
	 * to return incorrect lengths.
	 *
	 * This function overrides the mbstring encoding to a binary-safe encoding, and
	 * resets it to the users expected encoding afterwards through the
	 * `reset_mbstring_encoding` function.
	 *
	 * It is safe to recursively call this function, however each
	 * `mbstring_binary_safe_encoding()` call must be followed up with an equal number
	 * of `reset_mbstring_encoding()` calls.
	 *
	 * @see wfWAFUtils::reset_mbstring_encoding
	 *
	 * @staticvar array $encodings
	 * @staticvar bool  $overloaded
	 *
	 * @param bool $reset Optional. Whether to reset the encoding back to a previously-set encoding.
	 *                    Default false.
	 */
	public static function mbstring_binary_safe_encoding($reset = false) {
		static $encodings = array();
		static $overloaded = null;

		if (is_null($overloaded))
			$overloaded = function_exists('mb_internal_encoding') && (ini_get('mbstring.func_overload') & 2);

		if (false === $overloaded)
			return;

		if (!$reset) {
			$encoding = mb_internal_encoding();
			array_push($encodings, $encoding);
			mb_internal_encoding('ISO-8859-1');
		}

		if ($reset && $encodings) {
			$encoding = array_pop($encodings);
			mb_internal_encoding($encoding);
		}
	}

	/**
	 * Reset the mbstring internal encoding to a users previously set encoding.
	 *
	 * @see wfWAFUtils::mbstring_binary_safe_encoding
	 */
	public static function reset_mbstring_encoding() {
		self::mbstring_binary_safe_encoding(true);
	}

	/**
	 * @param callable $function
	 * @param array $args
	 * @return mixed
	 */
	protected static function callMBSafeStrFunction($function, $args) {
		self::mbstring_binary_safe_encoding();
		$return = call_user_func_array($function, $args);
		self::reset_mbstring_encoding();
		return $return;
	}

	/**
	 * Multibyte safe strlen.
	 *
	 * @param $binary
	 * @return int
	 */
	public static function strlen($binary) {
		$args = func_get_args();
		return self::callMBSafeStrFunction('strlen', $args);
	}

	/**
	 * @param $haystack
	 * @param $needle
	 * @param int $offset
	 * @return int
	 */
	public static function stripos($haystack, $needle, $offset = 0) {
		$args = func_get_args();
		return self::callMBSafeStrFunction('stripos', $args);
	}

	/**
	 * @param $string
	 * @return mixed
	 */
	public static function strtolower($string) {
		$args = func_get_args();
		return self::callMBSafeStrFunction('strtolower', $args);
	}

	/**
	 * @param $string
	 * @param $start
	 * @param $length
	 * @return mixed
	 */
	public static function substr($string, $start, $length = null) {
		if ($length === null) { $length = self::strlen($string); }
		return self::callMBSafeStrFunction('substr', array(
			$string, $start, $length
		));
	}

	/**
	 * @param $haystack
	 * @param $needle
	 * @param int $offset
	 * @return mixed
	 */
	public static function strpos($haystack, $needle, $offset = 0) {
		$args = func_get_args();
		return self::callMBSafeStrFunction('strpos', $args);
	}

	/**
	 * @param string $haystack
	 * @param string $needle
	 * @param int $offset
	 * @param int $length
	 * @return mixed
	 */
	public static function substr_count($haystack, $needle, $offset = 0, $length = null) {
		if ($length === null) { $length = self::strlen($haystack); }
		return self::callMBSafeStrFunction('substr_count', array(
			$haystack, $needle, $offset, $length
		));
	}

	/**
	 * @param $string
	 * @return mixed
	 */
	public static function strtoupper($string) {
		$args = func_get_args();
		return self::callMBSafeStrFunction('strtoupper', $args);
	}

	/**
	 * @param string $haystack
	 * @param string $needle
	 * @param int $offset
	 * @return mixed
	 */
	public static function strrpos($haystack, $needle, $offset = 0) {
		$args = func_get_args();
		return self::callMBSafeStrFunction('strrpos', $args);
	}
	
	/**
	 * @param string $val An ini byte size value (e.g., 20M)
	 * @return int
	 */
	public static function iniSizeToBytes($val) {
		$val = trim($val);
		if (preg_match('/^\d+$/', $val)) {
			return (int) $val;
		}
		
		$last = strtolower(substr($val, -1));
		$val = (int) substr($val, 0, -1);
		switch ($last) {
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
		
		return $val;
	}
	
	public static function reverseLookup($IP) {
		$IPn = self::inet_pton($IP);
		// This function works for IPv4 or IPv6
		if (function_exists('gethostbyaddr')) {
			$host = @gethostbyaddr($IP);
		}
		if (!$host) {
			$ptr = false;
			if (filter_var($IP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
				$ptr = implode(".", array_reverse(explode(".", $IP))) . ".in-addr.arpa";
			} else if (filter_var($IP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
				$ptr = implode(".", array_reverse(str_split(bin2hex($IPn)))) . ".ip6.arpa";
			}
			
			if ($ptr && function_exists('dns_get_record')) {
				$host = @dns_get_record($ptr, DNS_PTR);
				if ($host) {
					$host = $host[0]['target'];
				}
			}
		}
		if (!$host) {
			return '';
		}
		return $host;
	}
	
	public static function patternToRegex($pattern, $mod = 'i', $sep = '/') {
		$pattern = preg_quote(trim($pattern), $sep);
		$pattern = str_replace(' ', '\s', $pattern);
		return $sep . '^' . str_replace('\*', '.*', $pattern) . '$' . $sep . $mod;
	}
	
	public static function isUABlocked($uaPattern, $ua) { // takes a pattern using asterisks as wildcards, turns it into regex and checks it against the visitor UA returning true if blocked
		return fnmatch($uaPattern, $ua, FNM_CASEFOLD);
	}
	
	public static function isRefererBlocked($refPattern, $referer) {
		return fnmatch($refPattern, $referer, FNM_CASEFOLD);
	}
	
	public static function extractBareURI($URL) {
		$URL = preg_replace('/^https?:\/\/[^\/]+/i', '', $URL); //strip of method and host
		$URL = preg_replace('/\#.*$/', '', $URL); //strip off fragment
		$URL = preg_replace('/\?.*$/', '', $URL); //strip off query string
		return $URL;
	}
	
	public static function extractHostname($str) {
		if (preg_match('/https?:\/\/([a-zA-Z0-9\.\-]+)(?:\/|$)/i', $str, $matches)) {
			return strtolower($matches[1]);
		}
		else {
			return false;
		}
	}
	
	public static function redirect($location, $status = 302) {
		$is_apache = (strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false || strpos($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false);
		$is_IIS = !$is_apache && (strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false || strpos($_SERVER['SERVER_SOFTWARE'], 'ExpressionDevServer') !== false);
		
		self::doNotCache();
		
		if (!$is_IIS && PHP_SAPI != 'cgi-fcgi') {
			self::statusHeader($status); // This causes problems on IIS and some FastCGI setups
		}
		
		header("Location: {$location}", true, $status);
		exit;
	}
	
	public static function statusHeader($code) {
		$code = abs(intval($code));
		
		$statusCodes = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			226 => 'IM Used',
			
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Reserved',
			307 => 'Temporary Redirect',
			308 => 'Permanent Redirect',
			
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			418 => 'I\'m a teapot',
			421 => 'Misdirected Request',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			426 => 'Upgrade Required',
			428 => 'Precondition Required',
			429 => 'Too Many Requests',
			431 => 'Request Header Fields Too Large',
			451 => 'Unavailable For Legal Reasons',
			
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			510 => 'Not Extended',
			511 => 'Network Authentication Required',
		);
			
		$description = (isset($statusCodes[$code]) ? $statusCodes[$code] : '');
		
		$protocol = $_SERVER['SERVER_PROTOCOL'];
		if (!in_array($protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0'))) {
			$protocol = 'HTTP/1.0';
		}
		
		$header = "{$protocol} {$code} {$description}";
		@header($header, true, $code);
	}
	
	public static function doNotCache() {
		header("Pragma: no-cache");
		header("Cache-Control: no-cache, must-revalidate, private");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); //In the past
		if (!defined('DONOTCACHEPAGE')) { define('DONOTCACHEPAGE', true); }
		if (!defined('DONOTCACHEDB')) { define('DONOTCACHEDB', true); }
		if (!defined('DONOTCDN')) { define('DONOTCDN', true); }
		if (!defined('DONOTCACHEOBJECT')) { define('DONOTCACHEOBJECT', true); }
	}
	
	/**
	 * Check if an IP address is in a network block
	 *
	 * @param string	$subnet	Single IP or subnet in CIDR notation (e.g. '192.168.100.0' or '192.168.100.0/22')
	 * @param string	$ip		IPv4 or IPv6 address in dot or colon notation
	 * @return boolean
	 */
	public static function subnetContainsIP($subnet, $ip) {
		list($network, $prefix) = array_pad(explode('/', $subnet, 2), 2, null);
		
		if (filter_var($network, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			// If no prefix was supplied, 32 is implied for IPv4
			if ($prefix === null) {
				$prefix = 32;
			}
			
			// Validate the IPv4 network prefix
			if ($prefix < 0 || $prefix > 32) {
				return false;
			}
			
			// Increase the IPv4 network prefix to work in the IPv6 address space
			$prefix += 96;
		} else {
			// If no prefix was supplied, 128 is implied for IPv6
			if ($prefix === null) {
				$prefix = 128;
			}
			
			// Validate the IPv6 network prefix
			if ($prefix < 1 || $prefix > 128) {
				return false;
			}
		}
		
		$bin_network = wfWAFUtils::substr(self::inet_pton($network), 0, ceil($prefix / 8));
		$bin_ip = wfWAFUtils::substr(self::inet_pton($ip), 0, ceil($prefix / 8));
		if ($prefix % 8 != 0) { //Adjust the last relevant character to fit the mask length since the character's bits are split over it
			$pos = intval($prefix / 8);
			$adjustment = chr(((0xff << (8 - ($prefix % 8))) & 0xff));
			$bin_network[$pos] = ($bin_network[$pos] & $adjustment);
			$bin_ip[$pos] = ($bin_ip[$pos] & $adjustment);
		}
		
		return ($bin_network === $bin_ip);
	}
	
	/**
	 * Behaves exactly like PHP's parse_url but uses WP's compatibility fixes for early PHP 5 versions.
	 * 
	 * @param string $url
	 * @param int $component
	 * @return mixed
	 */
	public static function parse_url($url, $component = -1) {
		$to_unset = array();
		$url = strval($url);
		
		if (substr($url, 0, 2) === '//') {
			$to_unset[] = 'scheme';
			$url = 'placeholder:' . $url;
		}
		elseif (substr($url, 0, 1) === '/') {
			$to_unset[] = 'scheme';
			$to_unset[] = 'host';
			$url = 'placeholder://placeholder' . $url;
		}
		
		$parts = @parse_url($url);
		
		if ($parts === false) { // Parsing failure
			return $parts;
		}
		
		// Remove the placeholder values
		foreach ($to_unset as $key) {
			unset($parts[$key]);
		}
		
		if ($component === -1) {
			return $parts;
		}
		
		$translation = array(
			PHP_URL_SCHEME   => 'scheme',
			PHP_URL_HOST     => 'host',
			PHP_URL_PORT     => 'port',
			PHP_URL_USER     => 'user',
			PHP_URL_PASS     => 'pass',
			PHP_URL_PATH     => 'path',
			PHP_URL_QUERY    => 'query',
			PHP_URL_FRAGMENT => 'fragment',
		);
		
		$key = false;
		if (isset($translation[$component])) {
			$key = $translation[$component];
		}
		
		if ($key !== false && is_array($parts) && isset($parts[$key])) {
			return $parts[$key];
		}
		
		return null;
	}
	
	/**
	 * Validates the URL, supporting both scheme-relative and path-relative formats.
	 * 
	 * @param $url
	 * @return mixed
	 */
	public static function validate_url($url) {
		$url = strval($url);
		
		if (substr($url, 0, 2) === '//') {
			$url = 'placeholder:' . $url;
		}
		elseif (substr($url, 0, 1) === '/') {
			$url = 'placeholder://placeholder' . $url;
		}
		
		return filter_var($url, FILTER_VALIDATE_URL);
	}
	
	public static function rawPOSTBody() {
		global $HTTP_RAW_POST_DATA;
		if (empty($HTTP_RAW_POST_DATA)) { //Defined if always_populate_raw_post_data is on, PHP < 7, and the encoding type is not multipart/form-data
			$data = file_get_contents('php://input'); //Available if the encoding type is not multipart/form-data; it can only be read once prior to PHP 5.6 so we save it in $HTTP_RAW_POST_DATA for WP Core and others
			
			//For our purposes, we don't currently need the raw POST body if it's multipart/form-data since the data will be in $_POST/$_FILES. If we did, we could reconstruct the body here.
			
			$HTTP_RAW_POST_DATA = $data;
		}
		else {
			$data =& $HTTP_RAW_POST_DATA;
		}
		return $data;
	}
	
	/**
	 * Returns the current timestamp, adjusted as needed to get close to what we consider a true timestamp. We use this
	 * because a significant number of servers are using a drastically incorrect time.
	 * 
	 * @return int
	 */
	public static function normalizedTime() {
		$offset = 0;
		try {
			$offset = wfWAF::getInstance()->getStorageEngine()->getConfig('timeoffset_ntp', false);
			if ($offset === false) {
				$offset = wfWAF::getInstance()->getStorageEngine()->getConfig('timeoffset_wf', false);
				if ($offset === false) { $offset = 0; }
			}
		}
		catch (Exception $e) {
			//Ignore
		}
		return time() + $offset;
	}
}

<?php
require_once('wfConfig.php');
require_once('wfCountryMap.php');
class wfUtils {
	private static $isWindows = false;
	public static $scanLockFH = false;
	private static $lastErrorReporting = false;
	private static $lastDisplayErrors = false;
	public static function patternToRegex($pattern, $mod = 'i', $sep = '/') {
		$pattern = preg_quote(trim($pattern), $sep);
		$pattern = str_replace(' ', '\s', $pattern);
		return $sep . '^' . str_replace('\*', '.*', $pattern) . '$' . $sep . $mod;
	}
	public static function versionedAsset($subpath) {
		$version = WORDFENCE_BUILD_NUMBER;
		if ($version != 'WORDFENCE_BUILD_NUMBER' && preg_match('/^(.+?)(\.[^\.]+)$/', $subpath, $matches)) {
			$prefix = $matches[1];
			$suffix = $matches[2];
			return $prefix . '.' . $version . $suffix;
		}
		
		return $subpath;
	}
	public static function makeTimeAgo($secs, $noSeconds = false) {
		if($secs < 1){
			return "a moment";
		}
		
		if (function_exists('date_diff')) {
			$now = new DateTime();
			$utc = new DateTimeZone('UTC');
			$dtStr = gmdate("c", (int) ($now->getTimestamp() + $secs)); //Have to do it this way because of PHP 5.2
			$then = new DateTime($dtStr, $utc);
			
			$diff = $then->diff($now);
			$years = $diff->y;
			$months = $diff->m;
			$days = $diff->d;
			$hours = $diff->h;
			$minutes = $diff->i;
		}
		else {
			$years = 0;
			$months = floor($secs / (86400 * 30));
			$days = floor($secs / 86400);
			$hours = floor($secs / 3600);
			$minutes = floor($secs / 60);
			
			if ($months) {
				$days -= $months * 30;
			}
			else if ($days) {
				$hours -= $days * 24;
			}
			else if ($hours) {
				$minutes -= $hours * 60;
			}
		}
		
		if ($years) {
			return self::pluralize($years, 'year', $months, 'month');
		}
		else if ($months) {
			return self::pluralize($months, 'month', $days, 'day');
		}
		else if ($days) {
			return self::pluralize($days, 'day', $hours, 'hour');
		}
		else if ($hours) {
			return self::pluralize($hours, 'hour', $minutes, 'min');
		}
		else if ($minutes) {
			return self::pluralize($minutes, 'min');
		}
		else {
			if($noSeconds){
				return "less than a minute";
			} else {
				return floor($secs) . " secs";
			}
		}
	}
	public static function makeDuration($secs, $createExact = false) {
		$components = array();
		
		$months = floor($secs / (86400 * 30)); $secs -= $months * 86400 * 30;
		$days = floor($secs / 86400); $secs -= $days * 86400;
		$hours = floor($secs / 3600); $secs -= $hours * 3600;
		$minutes = floor($secs / 60); $secs -= $minutes * 60;
		
		if ($months) {
			$components[] = self::pluralize($months, 'month');
			if (!$createExact) {
				$hours = $minutes = $secs = 0;
			}
		}
		if ($days) {
			$components[] = self::pluralize($days, 'day');
			if (!$createExact) {
				$minutes = $secs = 0;
			}
		}
		if ($hours) {
			$components[] = self::pluralize($hours, 'hour');
			if (!$createExact) {
				$secs = 0;
			}
		}
		if ($minutes) {
			$components[] = self::pluralize($minutes, 'minute');
		}
		if ($secs) {
			$components[] = self::pluralize($secs, 'second');
		}
		
		if (empty($components)) {
			$components[] = 'less than 1 second';
		}
		
		return implode(' ', $components);
	}
	public static function pluralize($m1, $t1, $m2 = false, $t2 = false) {
		if($m1 != 1) {
			$t1 = $t1 . 's';
		}
		if($m2 != 1) {
			$t2 = $t2 . 's';
		}
		if($m1 && $m2){
			return "$m1 $t1 $m2 $t2";
		} else {
			return "$m1 $t1";
		}
	}
	public static function formatBytes($bytes, $precision = 2) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		// Uncomment one of the following alternatives
		$bytes /= pow(1024, $pow);
		// $bytes /= (1 << (10 * $pow)); 

		return round($bytes, $precision) . ' ' . $units[$pow];
	}
	
	/**
	 * Returns the PHP version formatted for display, stripping off the build information when present.
	 * 
	 * @return string
	 */
	public static function cleanPHPVersion() {
		$version = phpversion();
		if (preg_match('/^(\d+\.\d+\.\d+)/', $version, $matches)) {
			return $matches[1];
		}
		return $version;
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
		
		$bin_network = wfUtils::substr(self::inet_pton($network), 0, ceil($prefix / 8));
		$bin_ip = wfUtils::substr(self::inet_pton($ip), 0, ceil($prefix / 8));
		if ($prefix % 8 != 0) { //Adjust the last relevant character to fit the mask length since the character's bits are split over it
			$pos = intval($prefix / 8);
			$adjustment = chr(((0xff << (8 - ($prefix % 8))) & 0xff));
			$bin_network[$pos] = ($bin_network[$pos] & $adjustment);
			$bin_ip[$pos] = ($bin_ip[$pos] & $adjustment);
		}
		
		return ($bin_network === $bin_ip);
	}

	/**
	 * Convert CIDR notation to a wfUserIPRange object
	 *
	 * @param string $cidr
	 * @return wfUserIPRange
	 */
	public static function CIDR2wfUserIPRange($cidr) {
		list($network, $prefix) = array_pad(explode('/', $cidr, 2), 2, null);
		$ip_range = new wfUserIPRange();

		if (filter_var($network, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			// If no prefix was supplied, 32 is implied for IPv4
			if ($prefix === null) {
				$prefix = 32;
			}

			// Validate the IPv4 network prefix
			if ($prefix < 0 || $prefix > 32) {
				return $ip_range;
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
				return $ip_range;
			}
		}

		// Convert human readable address to 128 bit (IPv6) binary string
		// Note: self::inet_pton converts IPv4 addresses to IPv6 compatible versions
		$binary_network = self::inet_pton($network);
		$binary_mask = wfHelperBin::str2bin(str_pad(str_repeat('1', $prefix), 128, '0', STR_PAD_RIGHT));

		// Calculate first and last address
		$binary_first = $binary_network & $binary_mask;
		$binary_last = $binary_network | ~ $binary_mask;

		// Convert binary addresses back to human readable strings
		$first = self::inet_ntop($binary_first);
		$last = self::inet_ntop($binary_last);

		if (filter_var($network, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			$first = self::expandIPv6Address($first);
			$last = self::expandIPv6Address($last);
		}

		// Split addresses into segments
		$first_array = preg_split('/[\.\:]/', $first);
		$last_array = preg_split('/[\.\:]/', $last);

		// Make sure arrays are the same size. IPv6 '::' could cause problems otherwise.
		// The strlen filter should leave zeros in place
		$first_array = array_pad(array_filter($first_array, 'strlen'), count($last_array), '0');

		$range_segments = array();

		foreach ($first_array as $index => $segment) {
			if ($segment === $last_array[$index]) {
				$range_segments[] = str_pad(ltrim($segment, '0'), 1, '0');
			} else if ($segment === '' || $last_array[$index] === '') {
				$range_segments[] = '';
			} else {
				$range_segments[] = "[". str_pad(ltrim($segment, '0'), 1, '0') . "-" .
					str_pad(ltrim($last_array[$index], '0'), 1, '0') . "]";
			}
		}

		$delimiter = filter_var($network, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? '.' : ':';

		$ip_range->setIPString(implode($delimiter, $range_segments));

		return $ip_range;
	}

	/**
	 * Return dot notation of IPv4 address.
	 *
	 * @param int $ip
	 * @return string|bool
	 */
	public static function inet_ntoa($ip) {
		$long = 4294967295 - ($ip - 1);
		return long2ip(-$long);
	}

	/**
	 * Return string representation of 32 bit int of the IP address.
	 *
	 * @param string $ip
	 * @return string
	 */
	public static function inet_aton($ip) {
		$ip = preg_replace('/(?<=^|\.)0+([1-9])/', '$1', $ip);
		return sprintf("%u", ip2long($ip));
	}

	/**
	 * Return dot or colon notation of IPv4 or IPv6 address.
	 *
	 * @param string $ip
	 * @return string|bool
	 */
	public static function inet_ntop($ip) {
		// trim this to the IPv4 equiv if it's in the mapped range
		if (strlen($ip) == 16 && substr($ip, 0, 12) == "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff") {
			$ip = substr($ip, 12, 4);
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
			$colon_count = substr_count($ip, ':');
			$dbl_colon_pos = strpos($ip, '::');
			if ($dbl_colon_pos !== false) {
				$ip = str_replace('::', str_repeat(':0000',
						(($dbl_colon_pos === 0 || $dbl_colon_pos === strlen($ip) - 2) ? 9 : 8) - $colon_count) . ':', $ip);
				$ip = trim($ip, ':');
			}

			$ip_groups = explode(':', $ip);
			$ipv6_bin = '';
			foreach ($ip_groups as $ip_group) {
				$ipv6_bin .= pack('H*', str_pad($ip_group, 4, '0', STR_PAD_LEFT));
			}

			return strlen($ipv6_bin) === 16 ? $ipv6_bin : false;
		}

		// IPv4 mapped IPv6
		if (preg_match('/^(?:\:(?:\:0{1,4}){0,4}\:|(?:0{1,4}\:){5})ffff\:(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})$/i', $ip, $matches)) {
			$octets = explode('.', $matches[1]);
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
		if (strlen($ip) === 4) {
			return ord($ip[0]) . '.' . ord($ip[1]) . '.' . ord($ip[2]) . '.' . ord($ip[3]);
		}

		// IPv6
		if (strlen($ip) === 16) {

			// IPv4 mapped IPv6
			if (substr($ip, 0, 12) == "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff") {
				return "::ffff:" . ord($ip[12]) . '.' . ord($ip[13]) . '.' . ord($ip[14]) . '.' . ord($ip[15]);
			}

			$hex = bin2hex($ip);
			$groups = str_split($hex, 4);
			$in_collapse = false;
			$done_collapse = false;
			foreach ($groups as $index => $group) {
				if ($group == '0000' && !$done_collapse) {
					if ($in_collapse) {
						$groups[$index] = '';
						continue;
					}
					$groups[$index] = ':';
					$in_collapse = true;
					continue;
				}
				if ($in_collapse) {
					$done_collapse = true;
				}
				$groups[$index] = ltrim($groups[$index], '0');
				if (strlen($groups[$index]) === 0) {
					$groups[$index] = '0';
				}
			}
			$ip = join(':', array_filter($groups, 'strlen'));
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

	public static function hasLoginCookie(){
		if(isset($_COOKIE)){
			if(is_array($_COOKIE)){
				foreach($_COOKIE as $key => $val){
					if(strpos($key, 'wordpress_logged_in') === 0){
						return true;
					}
				}
			}
		}
		return false;
	}
	public static function getBaseURL(){
		return plugins_url('', WORDFENCE_FCPATH) . '/';
	}
	public static function getPluginBaseDir(){
		if(function_exists('wp_normalize_path')){ //Older WP versions don't have this func and we had many complaints before this check.
			if(defined('WP_PLUGIN_DIR')) {
				return wp_normalize_path(WP_PLUGIN_DIR . '/');
			}
			return wp_normalize_path(WP_CONTENT_DIR . '/plugins/');
		} else {
			if(defined('WP_PLUGIN_DIR')) {
				return WP_PLUGIN_DIR . '/';
			}
			return WP_CONTENT_DIR . '/plugins/';
		}
	}
	public static function makeRandomIP(){
		return rand(11,230) . '.' . rand(0,255) . '.' . rand(0,255) . '.' . rand(0,255);
	}
	
	/**
	 * Converts a truthy value to a boolean, checking in this order:
	 * - already a boolean
	 * - numeric (0 => false, otherwise true)
	 * - 'false', 'f', 'no', 'n', or 'off' => false
	 * - 'true', 't', 'yes', 'y', or 'on' => true
	 * - empty value => false, otherwise true
	 * 
	 * @param $value
	 * @return bool
	 */
	public static function truthyToBoolean($value) {
		if ($value === true || $value === false) {
			return $value;
		}
		
		if (is_numeric($value)) {
			return !!$value;
		}
		
		if (preg_match('/^(?:f(?:alse)?|no?|off)$/i', $value)) {
			return false;
		}
		else if (preg_match('/^(?:t(?:rue)?|y(?:es)?|on)$/i', $value)) {
			return true;
		}
		
		return !empty($value);
	}
	
	/**
	 * Converts a truthy value to 1 or 0.
	 * 
	 * @see wfUtils::truthyToBoolean
	 * 
	 * @param $value
	 * @return int
	 */
	public static function truthyToInt($value) {
		return self::truthyToBoolean($value) ? 1 : 0;
	}

	/**
	 * Get the list of whitelisted IPs and networks
	 *
	 * Results may include wfUserIPRange objects for now. Ideally everything would be in CIDR notation.
	 *
	 * @param string	$filter	Group name to filter whitelist by
	 * @return array
	 */
	public static function getIPWhitelist($filter = null) {
		static $wfIPWhitelist;

		if (!isset($wfIPWhitelist)) {
			include('wfIPWhitelist.php');

			// Memoize user defined whitelist IPs and ranges
			// TODO: Convert everything to CIDR
			$wfIPWhitelist['user'] = array();

			foreach (array_filter(explode(',', wfConfig::get('whitelisted'))) as $ip) {
				$wfIPWhitelist['user'][] = new wfUserIPRange($ip);
			}
		}

		$whitelist = array();

		foreach ($wfIPWhitelist as $group => $values) {
			if ($filter === null || $group === $filter) {
				$whitelist = array_merge($whitelist, $values);
			}
		}

		return $whitelist;
	}

	/**
	 * @param string $addr Should be in dot or colon notation (127.0.0.1 or ::1)
	 * @return bool
	 */
	public static function isPrivateAddress($addr) {
		// Run this through the preset list for IPv4 addresses.
		if (filter_var($addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
			foreach (self::getIPWhitelist('private') as $a) {
				if (self::subnetContainsIP($a, $addr)) {
					return true;
				}
			}
		}

		return filter_var($addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) !== false
			&& filter_var($addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
	}

	/**
	 * Expects an array of items. The items are either IP's or IP's separated by comma, space or tab. Or an array of IP's.
	 * We then examine all IP's looking for a public IP and storing private IP's in an array. If we find no public IPs we return the first private addr we found.
	 *
	 * @param array $arr
	 * @return bool|mixed
	 */
	private static function getCleanIP($arr){
		$privates = array(); //Store private addrs until end as last resort.
		for($i = 0; $i < count($arr); $i++){
			$item = $arr[$i];
			if(is_array($item)){
				foreach($item as $j){
					// try verifying the IP is valid before stripping the port off
					if (!self::isValidIP($j)) {
						$j = preg_replace('/:\d+$/', '', $j); //Strip off port
					}
					if (self::isValidIP($j)) {
						if (self::isPrivateAddress($j)) {
							$privates[] = $j;
						} else {
							return $j;
						}
					}
				}
				continue; //This was an array so we can skip to the next item
			}
			$skipToNext = false;
			foreach(array(',', ' ', "\t") as $char){
				if(strpos($item, $char) !== false){
					$sp = explode($char, $item);
					$sp = array_reverse($sp);
					foreach($sp as $j){
						$j = trim($j);
						if (!self::isValidIP($j)) {
							$j = preg_replace('/:\d+$/', '', $j); //Strip off port
						}
						if(self::isValidIP($j)){
							if(self::isPrivateAddress($j)){
								$privates[] = $j;
							} else {
								return $j;
							}
						}
					}
					$skipToNext = true;
					break;
				}
			}
			if($skipToNext){ continue; } //Skip to next item because this one had a comma, space or tab so was delimited and we didn't find anything.

			if (!self::isValidIP($item)) {
				$item = preg_replace('/:\d+$/', '', $item); //Strip off port
			}
			if(self::isValidIP($item)){
				if(self::isPrivateAddress($item)){
					$privates[] = $item;
				} else {
					return $item;
				}
			}
		}
		if(sizeof($privates) > 0){
			return $privates[0]; //Return the first private we found so that we respect the order the IP's were passed to this function.
		} else {
			return false;
		}
	}

	/**
	 * Expects an array of items. The items are either IP's or IP's separated by comma, space or tab. Or an array of IP's.
	 * We then examine all IP's looking for a public IP and storing private IP's in an array. If we find no public IPs we return the first private addr we found.
	 *
	 * @param array $arr
	 * @return bool|mixed
	 */
	private static function getCleanIPAndServerVar($arr, $trustedProxies = null) {
		$privates = array(); //Store private addrs until end as last resort.
		for($i = 0; $i < count($arr); $i++){
			list($item, $var) = $arr[$i];
			if(is_array($item)){
				foreach($item as $j){
					// try verifying the IP is valid before stripping the port off
					if (!self::isValidIP($j)) {
						$j = preg_replace('/:\d+$/', '', $j); //Strip off port
					}
					if (self::isValidIP($j)) {
						if (self::isIPv6MappedIPv4($j)) {
							$j = self::inet_ntop(self::inet_pton($j));
						}

						if (self::isPrivateAddress($j)) {
							$privates[] = array($j, $var);
						} else {
							return array($j, $var);
						}
					}
				}
				continue; //This was an array so we can skip to the next item
			}
			$skipToNext = false;
			if ($trustedProxies === null) {
				$trustedProxies = explode("\n", wfConfig::get('howGetIPs_trusted_proxies', ''));
			}
			foreach(array(',', ' ', "\t") as $char){
				if(strpos($item, $char) !== false){
					$sp = explode($char, $item);
					$sp = array_reverse($sp);
					foreach($sp as $index => $j){
						$j = trim($j);
						if (!self::isValidIP($j)) {
							$j = preg_replace('/:\d+$/', '', $j); //Strip off port
						}
						if(self::isValidIP($j)){
							if (self::isIPv6MappedIPv4($j)) {
								$j = self::inet_ntop(self::inet_pton($j));
							}
							
							foreach ($trustedProxies as $proxy) {
								if (!empty($proxy)) {
									if (self::subnetContainsIP($proxy, $j) && $index < count($sp) - 1) {
										continue 2;
									}
								}
							}

							if(self::isPrivateAddress($j)){
								$privates[] = array($j, $var);
							} else {
								return array($j, $var);
							}
						}
					}
					$skipToNext = true;
					break;
				}
			}
			if($skipToNext){ continue; } //Skip to next item because this one had a comma, space or tab so was delimited and we didn't find anything.

			if (!self::isValidIP($item)) {
				$item = preg_replace('/:\d+$/', '', $item); //Strip off port
			}
			if(self::isValidIP($item)){
				if (self::isIPv6MappedIPv4($item)) {
					$item = self::inet_ntop(self::inet_pton($item));
				}

				if(self::isPrivateAddress($item)){
					$privates[] = array($item, $var);
				} else {
					return array($item, $var);
				}
			}
		}
		if(sizeof($privates) > 0){
			return $privates[0]; //Return the first private we found so that we respect the order the IP's were passed to this function.
		} else {
			return false;
		}
	}

	/**
	 * @param string $ip
	 * @return bool
	 */
	public static function isIPv6MappedIPv4($ip) {
		return preg_match('/^(?:\:(?:\:0{1,4}){0,4}\:|(?:0{1,4}\:){5})ffff\:\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/i', $ip) > 0;
	}

	public static function extractHostname($str){
		if(preg_match('/https?:\/\/([a-zA-Z0-9\.\-]+)(?:\/|$)/i', $str, $matches)){
			return strtolower($matches[1]);
		} else {
			return false;
		}
	}
	public static function getIP($refreshCache = false) {
		static $theIP = null;
		if (isset($theIP) && !$refreshCache) {
			return $theIP;
		}
		//For debugging. 
		//return '54.232.205.132';
		//return self::makeRandomIP();

		// if no REMOTE_ADDR, it's probably running from the command line
		$ip = self::getIPAndServerVariable();
		if (is_array($ip)) {
			list($IP, $variable) = $ip;
			$theIP = $IP;
			return $IP;
		}
		return false;
	}
	
	public static function getIPForField($field, $trustedProxies = null) {
		$ip = self::getIPAndServerVariable($field, $trustedProxies);
		if (is_array($ip)) {
			list($IP, $variable) = $ip;
			return $IP;
		}
		return false;
	}

	public static function getIPAndServerVariable($howGet = null, $trustedProxies = null) {
		$connectionIP = array_key_exists('REMOTE_ADDR', $_SERVER) ? array($_SERVER['REMOTE_ADDR'], 'REMOTE_ADDR') : array('127.0.0.1', 'REMOTE_ADDR');

		if ($howGet === null) {
			$howGet = wfConfig::get('howGetIPs', false);
		}
		
		if($howGet){
			if($howGet == 'REMOTE_ADDR'){
				return self::getCleanIPAndServerVar(array($connectionIP), $trustedProxies);
			} else {
				$ipsToCheck = array(
					array((isset($_SERVER[$howGet]) ? $_SERVER[$howGet] : ''), $howGet),
					$connectionIP,
				);
				return self::getCleanIPAndServerVar($ipsToCheck, $trustedProxies);
			}
		} else {
			$ipsToCheck = array();
			
			$recommendedField = wfConfig::get('detectProxyRecommendation', ''); //Prioritize the result from our proxy check if done
			if (!empty($recommendedField) && $recommendedField != 'UNKNOWN' && $recommendedField != 'DEFERRED') {
				if (isset($_SERVER[$recommendedField])) {
					$ipsToCheck[] = array($_SERVER[$recommendedField], $recommendedField);
				}
			}
			$ipsToCheck[] = $connectionIP;
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ipsToCheck[] = array($_SERVER['HTTP_X_FORWARDED_FOR'], 'HTTP_X_FORWARDED_FOR');
			}
			if (isset($_SERVER['HTTP_X_REAL_IP'])) {
				$ipsToCheck[] = array($_SERVER['HTTP_X_REAL_IP'], 'HTTP_X_REAL_IP');
			}
			return self::getCleanIPAndServerVar($ipsToCheck, $trustedProxies);
		}
		return false; //Returns an array with a valid IP and the server variable, or false.
	}
	public static function getIPPreview($howGet = null, $trustedProxies = null) {
		$ip = self::getIPAndServerVariable($howGet, $trustedProxies);
		if (is_array($ip)) {
			list($IP, $variable) = $ip;
			if (isset($_SERVER[$variable]) && strpos($_SERVER[$variable], ',') !== false) {
				$items = preg_replace('/[\s,]/', '', explode(',', $_SERVER[$variable]));
				$output = '';
				foreach ($items as $i) {
					if ($IP == $i) {
						$output .= ', <strong>' . esc_html($i) . '</strong>';
					}
					else {
						$output .= ', ' . esc_html($i); 
					}
				}
				
				return substr($output, 2);
			}
			return '<strong>' . esc_html($IP) . '</strong>';
		}
		return false;
	}
	public static function isValidIP($IP){
		return filter_var($IP, FILTER_VALIDATE_IP) !== false;
	}
	public static function isValidCIDRRange($range) {
		$components = explode('/', $range);
		if (count($components) != 2) { return false; }
		
		list($ip, $prefix) = $components;
		if (!self::isValidIP($ip)) { return false; }
		
		if (!preg_match('/^\d+$/', $prefix)) { return false; }
		
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			if ($prefix < 0 || $prefix > 32) { return false; }
		}
		else {
			if ($prefix < 1 || $prefix > 128) { return false; }
		}
		
		return true;
	}
	public static function isValidEmail($email, $strict = false) {
		//We don't default to strict, full validation because poorly-configured servers can crash due to the regex PHP uses in filter_var($email, FILTER_VALIDATE_EMAIL)
		if ($strict) {
			return filter_var($email, FILTER_VALIDATE_EMAIL !== false);
		}
		
		return preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/i', $email) === 1;
	}
	public static function getRequestedURL() {
		if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) {
			$host = $_SERVER['HTTP_HOST'];
		} else if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']) {
			$host = $_SERVER['SERVER_NAME'];
		}
		else {
			return null;
		}
		$prefix = 'http';
		if (is_ssl()) {
			$prefix = 'https';
		}
		return $prefix . '://' . $host . $_SERVER['REQUEST_URI'];
	}

	public static function editUserLink($userID){
		return get_admin_url() . 'user-edit.php?user_id=' . $userID;
	}
	public static function tmpl($file, $data){
		extract($data);
		ob_start();
		include $file;
		return ob_get_contents() . (ob_end_clean() ? "" : "");
	}
	public static function bigRandomHex(){
		return dechex(rand(0, 2147483647)) . dechex(rand(0, 2147483647)) . dechex(rand(0, 2147483647));
	}
	public static function encrypt($str){
		$key = wfConfig::get('encKey');
		if(! $key){
			wordfence::status(1, 'error', "Wordfence error: No encryption key found!");
			return false;
		}
		$db = new wfDB();
		return $db->querySingle("select HEX(AES_ENCRYPT('%s', '%s')) as val", $str, $key);
	}
	public static function decrypt($str){
		$key = wfConfig::get('encKey');
		if(! $key){
			wordfence::status(1, 'error', "Wordfence error: No encryption key found!");
			return false;
		}
		$db = new wfDB();
		return $db->querySingle("select AES_DECRYPT(UNHEX('%s'), '%s') as val", $str, $key);
	}
	public static function lcmem(){
		$trace=debug_backtrace();
		$caller=array_shift($trace);
		$mem = memory_get_usage(true);
		error_log("$mem at " . $caller['file'] . " line " . $caller['line']);
	}
	public static function logCaller(){
		$trace=debug_backtrace();
		$caller=array_shift($trace);
		$c2 = array_shift($trace);
		error_log("Caller for " . $caller['file'] . " line " . $caller['line'] . " is " . $c2['file'] . ' line ' . $c2['line']);
	}
	public static function getWPVersion($forceRecheck = false){
		if ($forceRecheck) {
			require(ABSPATH . 'wp-includes/version.php'); //defines $wp_version
			return $wp_version;
		}
		
		if(wordfence::$wordfence_wp_version){
			return wordfence::$wordfence_wp_version;
		} else {
			global $wp_version;
			return $wp_version;
		}
	}
	public static function isAdminPageMU(){
		if(preg_match('/^[\/a-zA-Z0-9\-\_\s\+\~\!\^\.]*\/wp-admin\/network\//', $_SERVER['REQUEST_URI'])){
			return true;
		}
		return false;
	}
	public static function getSiteBaseURL(){
		return rtrim(site_url(), '/') . '/';
	}
	public static function longestLine($data){
		$lines = preg_split('/[\r\n]+/', $data);
		$max = 0;
		foreach($lines as $line){
			$len = strlen($line);
			if($len > $max){
				$max = $len;
			}
		}
		return $max;
	}
	public static function longestNospace($data){
		$lines = preg_split('/[\r\n\s\t]+/', $data);
		$max = 0;
		foreach($lines as $line){
			$len = strlen($line);
			if($len > $max){
				$max = $len;
			}
		}
		return $max;
	}
	public static function requestMaxMemory(){
		if(wfConfig::get('maxMem', false) && (int) wfConfig::get('maxMem') > 0){
			$maxMem = (int) wfConfig::get('maxMem');
		} else {
			$maxMem = 256;
		}
		if( function_exists('memory_get_usage') && ( (int) @ini_get('memory_limit') < $maxMem ) ){
			self::iniSet('memory_limit', $maxMem . 'M');
		}
	}
	public static function isAdmin($user = false){
		if($user){
			if(is_multisite()){
				if(user_can($user, 'manage_network')){
					return true;
				}
			} else {
				if(user_can($user, 'manage_options')){
					return true;
				}
			}
		} else {
			if(is_multisite()){
				if(current_user_can('manage_network')){
					return true;
				}
			} else {
				if(current_user_can('manage_options')){
					return true;
				}
			}
		}
		return false;
	}
	public static function hasTwoFactorEnabled($user = false) {
		if (!$user) {
			$user = get_user_by('ID', get_current_user_id());
		}
		
		if (!$user) {
			return false;
		}
		
		$twoFactorUsers = wfConfig::get_ser('twoFactorUsers', array());
		$hasActivatedTwoFactorUser = false;
		foreach ($twoFactorUsers as &$t) {
			if ($t[3] == 'activated') {
				$userID = $t[0];
				if ($userID == $user->ID && wfUtils::isAdmin($user)) {
					$hasActivatedTwoFactorUser = true;
				}
			}
		}
		
		return $hasActivatedTwoFactorUser;
	}
	public static function isWindows(){
		if(! self::$isWindows){
			if(preg_match('/^win/i', PHP_OS)){
				self::$isWindows = 'yes';
			} else {
				self::$isWindows = 'no';
			}
		}
		return self::$isWindows == 'yes' ? true : false;
	}
	public static function cleanupOneEntryPerLine($string) {
		$string = str_replace(",", "\n", $string); // fix old format
		return implode("\n", array_unique(array_filter(array_map('trim', explode("\n", $string)))));
	}
	public static function getScanFileError() {
		$fileTime = wfConfig::get('scanFileProcessing');
		if (! $fileTime) {
			return;
		}
		list($file, $time) =  unserialize($fileTime);
		if ($time+10 < time()) {
			$files = wfConfig::get('scan_exclude') . "\n" . $file;
			wfConfig::set('scan_exclude', self::cleanupOneEntryPerLine($files));
			self::endProcessingFile();
		}
	}

	public static function beginProcessingFile($file) {
		wfConfig::set('scanFileProcessing', serialize(array($file, time())));
	}

	public static function endProcessingFile() {
		wfConfig::set('scanFileProcessing', null);
		if (wfScanner::shared()->useLowResourceScanning()) {
			usleep(10000); //10 ms
		}
	}

	public static function getScanLock(){
		//Windows does not support non-blocking flock, so we use time.
		$scanRunning = wfConfig::get('wf_scanRunning');
		if($scanRunning && time() - $scanRunning < WORDFENCE_MAX_SCAN_LOCK_TIME){
			return false;
		}
		wfConfig::set('wf_scanRunning', time());
		return true;
	}
	public static function clearScanLock(){
		global $wpdb;
		$wfdb = new wfDB();
		$wfdb->truncate(wfDB::networkTable('wfHoover'));

		wfConfig::set('wf_scanRunning', '');
		wfIssues::updateScanStillRunning(false);
	}
	public static function getIPGeo($IP){ //Works with int or dotted

		$locs = self::getIPsGeo(array($IP));
		if(isset($locs[$IP])){
			return $locs[$IP];
		} else {
			return false;
		}
	}
	public static function getIPsGeo($IPs){ //works with int or dotted. Outputs same format it receives.
		$IPs = array_unique($IPs);
		$toResolve = array();
		$db = new wfDB();
		$locsTable = wfDB::networkTable('wfLocs');
		$IPLocs = array();
		foreach($IPs as $IP){
			$isBinaryIP = !self::isValidIP($IP);
			if ($isBinaryIP) {
				$ip_printable = wfUtils::inet_ntop($IP);
				$ip_bin = $IP;
			} else {
				$ip_printable = $IP;
				$ip_bin = wfUtils::inet_pton($IP);
			}

			$row = $db->querySingleRec("select IP, ctime, failed, city, region, countryName, countryCode, lat, lon, unix_timestamp() - ctime as age from " . $locsTable . " where IP=%s", $ip_bin);
			if($row){
				if($row['age'] > WORDFENCE_MAX_IPLOC_AGE){
					$db->queryWrite("delete from " . $locsTable . " where IP=%s", $row['IP']);
				} else {
					if($row['failed'] == 1){
						$IPLocs[$ip_printable] = false;
					} else {
						$row['IP'] = self::inet_ntop($row['IP']);
						$IPLocs[$ip_printable] = $row;
					}
				}
			}
			if(! isset($IPLocs[$ip_printable])){
				$toResolve[] = $ip_printable;
			}
		}
		if(sizeof($toResolve) > 0){
			$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
			try {
				$freshIPs = $api->call('resolve_ips', array(), array(
					'ips' => implode(',', $toResolve)
					));
				if(is_array($freshIPs)){
					foreach($freshIPs as $IP => $value){
						$IP_bin = wfUtils::inet_pton($IP);
						if($value == 'failed'){
							$db->queryWrite("insert IGNORE into " . $locsTable . " (IP, ctime, failed) values (%s, unix_timestamp(), 1)", $IP_bin);
							$IPLocs[$IP] = false;
						} else if(is_array($value)){
							for($i = 0; $i <= 5; $i++){
								//Prevent warnings in debug mode about uninitialized values
								if(! isset($value[$i])){ $value[$i] = ''; }
							}
							$db->queryWrite("insert IGNORE into " . $locsTable . " (IP, ctime, failed, city, region, countryName, countryCode, lat, lon) values (%s, unix_timestamp(), 0, '%s', '%s', '%s', '%s', %s, %s)",
								$IP_bin,
								$value[3], //city
								$value[2], //region
								$value[1], //countryName
								$value[0],//countryCode
								$value[4],//lat
								$value[5]//lon
								);
							$IPLocs[$IP] = array(
								'IP' => $IP,
								'city' => $value[3],
								'region' => $value[2],
								'countryName' => $value[1],
								'countryCode' => $value[0],
								'lat' => $value[4],
								'lon' => $value[5]
								);
						}
					}
				}
			} catch(Exception $e){
				wordfence::status(2, 'error', "Call to Wordfence API to resolve IPs failed: " . $e->getMessage());
				return array();
			}
		}
		return $IPLocs;
	}

	public static function reverseLookup($IP) {
		static $_memoryCache = array();
		if (isset($_memoryCache[$IP])) {
			return $_memoryCache[$IP];
		}
		
		$db = new wfDB();
		$reverseTable = wfDB::networkTable('wfReverseCache');
		$IPn = wfUtils::inet_pton($IP);
		$host = $db->querySingle("select host from " . $reverseTable . " where IP=%s and unix_timestamp() - lastUpdate < %d", $IPn, WORDFENCE_REVERSE_LOOKUP_CACHE_TIME);
		if (!$host) {
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
			$_memoryCache[$IP] = $host;
			if (!$host) {
				$host = 'NONE';
			}
			$db->queryWrite("insert into " . $reverseTable . " (IP, host, lastUpdate) values (%s, '%s', unix_timestamp()) ON DUPLICATE KEY UPDATE host='%s', lastUpdate=unix_timestamp()", $IPn, $host, $host);
		}
		if ($host == 'NONE') {
			$_memoryCache[$IP] = '';
			return '';
		} else {
			$_memoryCache[$IP] = $host;
			return $host;
		}
	}
	public static function errorsOff(){
		self::$lastErrorReporting = @ini_get('error_reporting');
		@error_reporting(0);
		self::$lastDisplayErrors = @ini_get('display_errors');
		self::iniSet('display_errors', 0);
		if(class_exists('wfScan')){ wfScan::$errorHandlingOn = false; }
	}
	public static function errorsOn(){
		@error_reporting(self::$lastErrorReporting);
		self::iniSet('display_errors', self::$lastDisplayErrors);
		if(class_exists('wfScan')){ wfScan::$errorHandlingOn = true; }
	}
	//Note this function may report files that are too big which actually are not too big but are unseekable and throw an error on fseek(). But that's intentional
	public static function fileTooBig($file){ //Deals with files > 2 gigs on 32 bit systems which are reported with the wrong size due to integer overflow
		wfUtils::errorsOff();
		$fh = @fopen($file, 'r');
		wfUtils::errorsOn();
		if(! $fh){ return false; }
		$offset = WORDFENCE_MAX_FILE_SIZE_TO_PROCESS + 1;
		$tooBig = false;
		try {
			if(@fseek($fh, $offset, SEEK_SET) === 0){
				if(strlen(fread($fh, 1)) === 1){
					$tooBig = true;
				}
			} //Otherwise we couldn't seek there so it must be smaller
			fclose($fh);
			return $tooBig;
		} catch(Exception $e){ return true; } //If we get an error don't scan this file, report it's too big.
	}
	public static function fileOver2Gigs($file){ //Surround calls to this func with try/catch because fseek may throw error.
		$fh = @fopen($file, 'r');
		if(! $fh){ return false; }
		$offset = 2147483647;
		$tooBig = false;
		//My throw an error so surround calls to this func with try/catch
		if(@fseek($fh, $offset, SEEK_SET) === 0){
			if(strlen(fread($fh, 1)) === 1){
				$tooBig = true;
			}
		} //Otherwise we couldn't seek there so it must be smaller
		@fclose($fh);
		return $tooBig;
	}
	public static function countryCode2Name($code){
		if(isset(wfCountryMap::$map[$code])){
			return wfCountryMap::$map[$code];
		} else {
			return '';
		}
	}
	public static function extractBareURI($URL){
		$URL = preg_replace('/^https?:\/\/[^\/]+/i', '', $URL); //strip of method and host
		$URL = preg_replace('/\#.*$/', '', $URL); //strip off fragment
		$URL = preg_replace('/\?.*$/', '', $URL); //strip off query string
		return $URL;
	}
	public static function IP2Country($IP){
		require_once('wfGeoIP.php');
		
		if (filter_var($IP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
			$gi = geoip_open(dirname(__FILE__) . "/GeoIPv6.dat", WF_GEOIP_STANDARD);
			$country = geoip_country_code_by_addr_v6($gi, $IP);
		} else {
			$gi = geoip_open(dirname(__FILE__) . "/GeoIP.dat", WF_GEOIP_STANDARD);
			$country = geoip_country_code_by_addr($gi, $IP);
		}
		geoip_close($gi);
		return $country ? $country : '';
	}
	public static function geoIPVersion() {
		require_once('wfGeoIP.php');
		$version = array();
		
		$gi = geoip_open(dirname(__FILE__) . "/GeoIP.dat", WF_GEOIP_STANDARD);
		$v = @geoip_database_info($gi);
		if ($v !== null) {
			$version[] = $v;
		}
		geoip_close($gi);
		
		if (self::hasIPv6Support()) {
			$gi = geoip_open(dirname(__FILE__) . "/GeoIPv6.dat", WF_GEOIP_STANDARD);
			$v = @geoip_database_info($gi);
			if ($v !== null) {
				$version[] = $v;
			}
			geoip_close($gi);
		}
		
		return $version;
	}
	public static function siteURLRelative(){
		if(is_multisite()){
			$URL = network_site_url();
		} else {
			$URL = site_url();
		}
		$URL = preg_replace('/^https?:\/\/[^\/]+/i', '', $URL);
		$URL = rtrim($URL, '/') . '/';
		return $URL;
	}
	public static function localHumanDate(){
		return date('l jS \of F Y \a\t h:i:s A', time() + (3600 * get_option('gmt_offset')));
	}
	public static function localHumanDateShort(){
		return date('D jS F \@ h:i:sA', time() + (3600 * get_option('gmt_offset')));
	}
	public static function funcEnabled($func){
		if(! function_exists($func)){ return false; }
		$disabled = explode(',', ini_get('disable_functions'));
		foreach($disabled as $f){
			if($func == $f){ return false; }
		}
		return true;
	}
	public static function iniSet($key, $val){
		if(self::funcEnabled('ini_set')){
			@ini_set($key, $val);
		}
	}
	public static function doNotCache(){
		header("Pragma: no-cache");
		header("Cache-Control: no-cache, must-revalidate, private");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); //In the past
		if(! defined('DONOTCACHEPAGE')){ define('DONOTCACHEPAGE', true); }
		if(! defined('DONOTCACHEDB')){ define('DONOTCACHEDB', true); }
		if(! defined('DONOTCDN')){ define('DONOTCDN', true); }
		if(! defined('DONOTCACHEOBJECT')){ define('DONOTCACHEOBJECT', true); }
		wfCache::doNotCache();
	}
	public static function isUABlocked($uaPattern){ // takes a pattern using asterisks as wildcards, turns it into regex and checks it against the visitor UA returning true if blocked
		return fnmatch($uaPattern, !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '', FNM_CASEFOLD);
	}
	public static function isRefererBlocked($refPattern){
		return fnmatch($refPattern, !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', FNM_CASEFOLD);
	}

	/**
	 * @param $startIP
	 * @param $endIP
	 * @return array
	 */
	public static function rangeToCIDRs($startIP, $endIP){
		$start_ip_printable = wfUtils::inet_ntop($startIP);
		if (filter_var($start_ip_printable, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			return self::rangeToCIDRsIPv4(current(unpack('N', substr($startIP, 12, 4))), current(unpack('N', substr($endIP, 12, 4))));
		}
		$startIPBin = str_pad(wfHelperBin::bin2str($startIP), 128, '0', STR_PAD_LEFT);
		$endIPBin = str_pad(wfHelperBin::bin2str($endIP), 128, '0', STR_PAD_LEFT);
		$IPIncBin = $startIPBin;
		$CIDRs = array();
		while (strcmp($IPIncBin, $endIPBin) <= 0) {
			$longNetwork = 128;
			$IPNetBin = $IPIncBin;
			while (($IPIncBin[$longNetwork - 1] == '0') && (strcmp(substr_replace($IPNetBin, '1', $longNetwork - 1, 1), $endIPBin) <= 0)) {
				$IPNetBin[$longNetwork - 1] = '1';
				$longNetwork--;
			}
			$CIDRs[] = self::inet_ntop(str_pad(wfHelperBin::str2bin($IPIncBin), 16, "\x00", STR_PAD_LEFT)) . ($longNetwork < 128 ? '/' . $longNetwork : '');
			$IPIncBin = str_pad(wfHelperBin::bin2str(wfHelperBin::addbin2bin(chr(1), wfHelperBin::str2bin($IPNetBin))), 128, '0', STR_PAD_LEFT);
		}
		return $CIDRs;
	}

	public static function rangeToCIDRsIPv4($startIP, $endIP){
		$startIPBin = sprintf('%032b', $startIP);
		$endIPBin = sprintf('%032b', $endIP);
		$IPIncBin = $startIPBin;
		$CIDRs = array();
		while(strcmp($IPIncBin, $endIPBin) <= 0){
			$longNetwork = 32;
			$IPNetBin = $IPIncBin;
			while(($IPIncBin[$longNetwork - 1] == '0') && (strcmp(substr_replace($IPNetBin, '1', $longNetwork - 1, 1), $endIPBin) <= 0)){
				$IPNetBin[$longNetwork - 1] = '1';
				$longNetwork--;
			}
			$CIDRs[] = long2ip(bindec($IPIncBin)) . ($longNetwork < 32 ? '/' . $longNetwork : '');
			$IPIncBin = sprintf('%032b', bindec($IPNetBin) + 1);
		}
		return $CIDRs;
	}

	public static function setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly){
		if(version_compare(PHP_VERSION, '5.2.0') >= 0){
			@setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
		} else {
			@setcookie($name, $value, $expire, $path);
		}
	}
	public static function isNginx(){
		$sapi = php_sapi_name();
		$serverSoft = $_SERVER['SERVER_SOFTWARE'];
		if($sapi == 'fpm-fcgi' && stripos($serverSoft, 'nginx') !== false){
			return true;
		}
	}
	public static function getLastError(){
		$err = error_get_last();
		if(is_array($err)){
			return $err['message'];
		}
		return '';
	}
	public static function hostNotExcludedFromProxy($url){
		if(! defined('WP_PROXY_BYPASS_HOSTS')){
			return true; //No hosts are excluded
		}
		$hosts = explode(',', WP_PROXY_BYPASS_HOSTS);
		$url = preg_replace('/^https?:\/\//i', '', $url);
		$url = preg_replace('/\/.*$/', '', $url);
		$url = strtolower($url);
		foreach($hosts as $h){
			if(strtolower(trim($h)) == $url){
				return false;
			}
		}
		return true;
	}
	public static function hasXSS($URL){
		if(! preg_match('/^https?:\/\/[a-z0-9\.\-]+\/[^\':<>\"\\\]*$/i', $URL)){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param string $host
	 * @return array
	 */
	public static function resolveDomainName($host) {
		// Fallback if this function is not available
		if (!function_exists('dns_get_record')) {
			return gethostbynamel($host);
		}

		$ips = array_merge((array) @dns_get_record($host, DNS_AAAA), (array) @dns_get_record($host, DNS_A));
		$return = array();

		foreach ($ips as $record) {
			if ($record['type'] === 'A') {
				$return[] = $record['ip'];
			}
			if ($record['type'] === 'AAAA') {
				$return[] = $record['ipv6'];
			}
		}
		return $return;
	}

	/**
	 * Expand a compressed printable representation of an IPv6 address.
	 *
	 * @param string $ip
	 * @return string
	 */
	public static function expandIPv6Address($ip) {
		$hex = bin2hex(self::inet_pton($ip));
		$ip = substr(preg_replace("/([a-f0-9]{4})/i", "$1:", $hex), 0, -1);
		return $ip;
	}

	public static function set_html_content_type() {
		return 'text/html';
	}

	public static function htmlEmail($to, $subject, $body) {
		add_filter( 'wp_mail_content_type', 'wfUtils::set_html_content_type' );
		$result = wp_mail($to, $subject, $body);
		remove_filter( 'wp_mail_content_type', 'wfUtils::set_html_content_type' );
		return $result;
	}

	/**
	 * @param string $readmePath
	 * @return bool
	 */
	public static function hideReadme($readmePath = null) {
		if ($readmePath === null) {
			$readmePath = ABSPATH . 'readme.html';
		}

		if (file_exists($readmePath)) {
			$readmePathInfo = pathinfo($readmePath);
			require_once ABSPATH . WPINC . '/pluggable.php';
			$hiddenReadmeFile = $readmePathInfo['filename'] . '.' . wp_hash('readme') . '.' . $readmePathInfo['extension'];
			return @rename($readmePath, $readmePathInfo['dirname'] . '/' . $hiddenReadmeFile);
		}
		return false;
	}

	/**
	 * @param string $readmePath
	 * @return bool
	 */
	public static function showReadme($readmePath = null) {
		if ($readmePath === null) {
			$readmePath = ABSPATH . 'readme.html';
		}
		$readmePathInfo = pathinfo($readmePath);
		require_once ABSPATH . WPINC . '/pluggable.php';
		$hiddenReadmeFile = $readmePathInfo['dirname'] . '/' . $readmePathInfo['filename'] . '.' . wp_hash('readme') . '.' . $readmePathInfo['extension'];
		if (file_exists($hiddenReadmeFile)) {
			return @rename($hiddenReadmeFile, $readmePath);
		}
		return false;
	}

	public static function htaccessAppend($code)
	{
		$htaccess = wfCache::getHtaccessPath();
		$content  = self::htaccess();
		if (wfUtils::isNginx() || !is_writable($htaccess)) {
			return false;
		}

		if (strpos($content, $code) === false) {
			// make sure we write this once
			file_put_contents($htaccess, $content . "\n" . trim($code), LOCK_EX);
		}

		return true;
	}
	
	public static function htaccessPrepend($code)
	{
		$htaccess = wfCache::getHtaccessPath();
		$content  = self::htaccess();
		if (wfUtils::isNginx() || !is_writable($htaccess)) {
			return false;
		}
		
		if (strpos($content, $code) === false) {
			// make sure we write this once
			file_put_contents($htaccess, trim($code) . "\n" . $content, LOCK_EX);
		}
		
		return true;
	}

	public static function htaccess() {
		$htaccess = wfCache::getHtaccessPath();
		if (is_readable($htaccess) && !wfUtils::isNginx()) {
			return file_get_contents($htaccess);
		}
		return "";
	}

	/**
	 * @param array $array
	 * @param mixed $oldKey
	 * @param mixed $newKey
	 * @return array
	 * @throws Exception
	 */
	public static function arrayReplaceKey($array, $oldKey, $newKey) {
		$keys = array_keys($array);
		if (($index = array_search($oldKey, $keys)) === false) {
			throw new Exception(sprintf('Key "%s" does not exist', $oldKey));
		}
		$keys[$index] = $newKey;
		return array_combine($keys, array_values($array));
	}
	
	/**
	 * Takes a string that may have characters that will be interpreted as invalid UTF-8 byte sequences and translates them into a string of the equivalent hex sequence.
	 * 
	 * @param $string
	 * @param bool $inline
	 * @return string
	 */
	public static function potentialBinaryStringToHTML($string, $inline = false, $allowmb4 = false) {
		$output = '';
		
		if (!defined('ENT_SUBSTITUTE')) {
			define('ENT_SUBSTITUTE', 0);
		}
		
		$span = '<span class="wf-hex-sequence">';
		if ($inline) {
			$span = '<span style="color:#587ECB">';
		}
		
		for ($i = 0; $i < wfUtils::strlen($string); $i++) {
			$c = $string[$i];
			$b = ord($c);
			if ($b < 0x20) {
				$output .= $span . '\x' . str_pad(dechex($b), 2, '0', STR_PAD_LEFT) . '</span>';
			}
			else if ($b < 0x80) {
				$output .= htmlspecialchars($c, ENT_QUOTES, 'ISO-8859-1');
			}
			else { //Assume multi-byte UTF-8
				$bytes = 0;
				$test = $b;
				
				while (($test & 0x80) > 0) {
					$bytes++;
					$test = (($test << 1) & 0xff);
				}
				
				$brokenUTF8 = ($i + $bytes > wfUtils::strlen($string) || $bytes == 1);
				if (!$brokenUTF8) { //Make sure we have all the bytes
					for ($n = 1; $n < $bytes; $n++) {
						$c2 = $string[$i + $n];
						$b2 = ord($c2);
						if (($b2 & 0xc0) != 0x80) {
							$brokenUTF8 = true;
							$bytes = $n;
							break;
						}
					}
				}
				
				if (!$brokenUTF8) { //Ensure the byte sequences are within the accepted ranges: https://tools.ietf.org/html/rfc3629
					/*
					 * UTF8-octets = *( UTF8-char )
   					 * UTF8-char   = UTF8-1 / UTF8-2 / UTF8-3 / UTF8-4
   					 * UTF8-1      = %x00-7F
   					 * UTF8-2      = %xC2-DF UTF8-tail
   					 * UTF8-3      = %xE0 %xA0-BF UTF8-tail / %xE1-EC 2( UTF8-tail ) /
   					 *               %xED %x80-9F UTF8-tail / %xEE-EF 2( UTF8-tail )
   					 * UTF8-4      = %xF0 %x90-BF 2( UTF8-tail ) / %xF1-F3 3( UTF8-tail ) /
   					 *               %xF4 %x80-8F 2( UTF8-tail )
   					 * UTF8-tail   = %x80-BF
					 */
					
					$testString = wfUtils::substr($string, $i, $bytes);
					$regex = '/^(?:' .
						'[\xc2-\xdf][\x80-\xbf]' . //UTF8-2
						'|' . '\xe0[\xa0-\xbf][\x80-\xbf]' . //UTF8-3
						'|' . '[\xe1-\xec][\x80-\xbf]{2}' .
						'|' . '\xed[\x80-\x9f][\x80-\xbf]' .
						'|' . '[\xee-\xef][\x80-\xbf]{2}';
					if ($allowmb4) {
						$regex .= '|' . '\xf0[\x90-\xbf][\x80-\xbf]{2}' . //UTF8-4
							'|' . '[\xf1-\xf3][\x80-\xbf]{3}' .
							'|' . '\xf4[\x80-\x8f][\x80-\xbf]{2}';
					}
					$regex  .= ')$/';
					if (!preg_match($regex, $testString)) {
						$brokenUTF8 = true;
					}
				}
				
				if ($brokenUTF8) {
					$bytes = min($bytes, strlen($string) - $i);
					for ($n = 0; $n < $bytes; $n++) {
						$c2 = $string[$i + $n];
						$b2 = ord($c2);
						$output .= $span . '\x' . str_pad(dechex($b2), 2, '0', STR_PAD_LEFT) . '</span>';
					}
					$i += ($bytes - 1);
				}
				else {
					$output .= htmlspecialchars(wfUtils::substr($string, $i, $bytes), ENT_QUOTES | ENT_SUBSTITUTE, 'ISO-8859-1');
					$i += ($bytes - 1);
				}
			}
		}
		return $output;
	}
	
	public static function requestDetectProxyCallback($timeout = 0.01, $blocking = false, $forceCheck = false) {
		$currentRecommendation = wfConfig::get('detectProxyRecommendation', '');
		if (!$forceCheck) {
			$detectProxyNextCheck = wfConfig::get('detectProxyNextCheck', false);
			if ($detectProxyNextCheck !== false && time() < $detectProxyNextCheck) {
				if (empty($currentRecommendation)) {
					wfConfig::set('detectProxyRecommendation', 'DEFERRED', wfConfig::DONT_AUTOLOAD);
				}
				return; //Let it pull the currently-stored value
			}
		}

		try {
			$waf = wfWAF::getInstance();
			if ($waf->getStorageEngine()->getConfig('attackDataKey', false) === false) {
				$waf->getStorageEngine()->setConfig('attackDataKey', mt_rand(0, 0xfff));
			}
			$response = wp_remote_get(sprintf(WFWAF_API_URL_SEC . "proxy-check/%d.txt", $waf->getStorageEngine()->getConfig('attackDataKey')), array('headers' => array('Referer' => false)));
			
			if (!is_wp_error($response)) {
				$okToSendBody = wp_remote_retrieve_body($response);
				if (preg_match('/^(ok|wait),\s*(\d+)$/i', $okToSendBody, $matches)) {
					$command = $matches[1];
					$ttl = $matches[2];
					if ($command == 'wait') {
						wfConfig::set('detectProxyNextCheck', time() + $ttl, wfConfig::DONT_AUTOLOAD);
						if (empty($currentRecommendation) || $currentRecommendation == 'UNKNOWN') {
							wfConfig::set('detectProxyRecommendation', 'DEFERRED', wfConfig::DONT_AUTOLOAD);
						}
						return;
					}
					
					wfConfig::set('detectProxyNextCheck', time() + $ttl, wfConfig::DONT_AUTOLOAD);
				}
				else { //Unknown response
					wfConfig::set('detectProxyNextCheck', false, wfConfig::DONT_AUTOLOAD);
					if (empty($currentRecommendation) || $currentRecommendation == 'UNKNOWN') {
						wfConfig::set('detectProxyRecommendation', 'DEFERRED', wfConfig::DONT_AUTOLOAD);
					}
					return;
				}
			}
		}
		catch (Exception $e) {
			return;
		}
		
		$nonce = bin2hex(wfWAFUtils::random_bytes(32));
		$callback = self::getSiteBaseURL() . '?_wfsf=detectProxy';
		
		wfConfig::set('detectProxyNonce', $nonce, wfConfig::DONT_AUTOLOAD);
		wfConfig::set('detectProxyRecommendation', '', wfConfig::DONT_AUTOLOAD);
		
		$payload = array(
			'nonce' => $nonce,
			'callback' => $callback,
		);
		
		$homeurl = wfUtils::wpHomeURL();
		$siteurl = wfUtils::wpSiteURL();
		
		wp_remote_post(WFWAF_API_URL_SEC . "?" . http_build_query(array(
				'action' => 'detect_proxy',
				'k'      => wfConfig::get('apiKey'),
				's'      => $siteurl,
				'h'		 => $homeurl,
				't'		 => microtime(true),
			), null, '&'),
			array(
				'body'    => json_encode($payload),
				'headers' => array(
					'Content-Type' => 'application/json',
					'Referer' => false,
				),
				'timeout' => $timeout,
				'blocking' => $blocking,
			));
		
		//Asynchronous so we don't care about a response at this point.
	}
	
	/**
	 * @return bool Returns false if the payload is invalid, true if it processed the callback (even if the IP wasn't found).
	 */
	public static function processDetectProxyCallback() {
		$nonce = wfConfig::get('detectProxyNonce', '');
		$testNonce = (isset($_POST['nonce']) ? $_POST['nonce'] : '');
		if (empty($nonce) || empty($testNonce)) {
			return false;
		}
		
		if (!hash_equals($nonce, $testNonce)) {
			return false;
		}
		
		$ips = (isset($_POST['ips']) ? $_POST['ips'] : array());
		if (empty($ips)) {
			return false;
		}
		
		$expandedIPs = array();
		foreach ($ips as $ip) {
			$expandedIPs[] = self::inet_pton($ip);
		}
		
		$checks = array('HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP', 'REMOTE_ADDR', 'HTTP_X_FORWARDED_FOR');
		foreach ($checks as $key) {
			if (!isset($_SERVER[$key])) {
				continue;
			}
			
			$testIP = self::getCleanIPAndServerVar(array(array($_SERVER[$key], $key)));
			if ($testIP === false) {
				continue;
			}
			
			$testIP = self::inet_pton($testIP[0]);
			if (in_array($testIP, $expandedIPs)) {
				wfConfig::set('detectProxyRecommendation', $key, wfConfig::DONT_AUTOLOAD);
				wfConfig::set('detectProxyNonce', '', wfConfig::DONT_AUTOLOAD);
				return true;
			}
		}
		
		wfConfig::set('detectProxyRecommendation', 'UNKNOWN', wfConfig::DONT_AUTOLOAD);
		wfConfig::set('detectProxyNonce', '', wfConfig::DONT_AUTOLOAD);
		return true;
	}
	
	/**
	 * Returns a v4 UUID.
	 * 
	 * @return string
	 */
	public static function uuid() {
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			wfWAFUtils::random_int(0, 0xffff), wfWAFUtils::random_int(0, 0xffff),
			
			// 16 bits for "time_mid"
			wfWAFUtils::random_int(0, 0xffff),
			
			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			wfWAFUtils::random_int(0, 0x0fff) | 0x4000,
			
			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			wfWAFUtils::random_int(0, 0x3fff) | 0x8000,
			
			// 48 bits for "node"
			wfWAFUtils::random_int(0, 0xffff), wfWAFUtils::random_int(0, 0xffff), wfWAFUtils::random_int(0, 0xffff)
		);
	}
	
	public static function base32_encode($rawString, $rightPadFinalBits = false, $padFinalGroup = false, $padCharacter = '=') //Adapted from https://github.com/ademarre/binary-to-text-php
	{
		// Unpack string into an array of bytes
		$bytes = unpack('C*', $rawString);
		$byteCount = count($bytes);
		
		$encodedString = '';
		$byte = array_shift($bytes);
		$bitsRead = 0;
		$oldBits = 0;
		
		$chars             = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
		$bitsPerCharacter  = 5;
		
		$charsPerByte = 8 / $bitsPerCharacter;
		$encodedLength = $byteCount * $charsPerByte;
		
		// Generate encoded output; each loop produces one encoded character
		for ($c = 0; $c < $encodedLength; $c++) {
			
			// Get the bits needed for this encoded character
			if ($bitsRead + $bitsPerCharacter > 8) {
				// Not enough bits remain in this byte for the current character
				// Save the remaining bits before getting the next byte
				$oldBitCount = 8 - $bitsRead;
				$oldBits = $byte ^ ($byte >> $oldBitCount << $oldBitCount);
				$newBitCount = $bitsPerCharacter - $oldBitCount;
				
				if (!$bytes) {
					// Last bits; match final character and exit loop
					if ($rightPadFinalBits) $oldBits <<= $newBitCount;
					$encodedString .= $chars[$oldBits];
					
					if ($padFinalGroup) {
						// Array of the lowest common multiples of $bitsPerCharacter and 8, divided by 8
						$lcmMap = array(1 => 1, 2 => 1, 3 => 3, 4 => 1, 5 => 5, 6 => 3, 7 => 7, 8 => 1);
						$bytesPerGroup = $lcmMap[$bitsPerCharacter];
						$pads = $bytesPerGroup * $charsPerByte - ceil((strlen($rawString) % $bytesPerGroup) * $charsPerByte);
						$encodedString .= str_repeat($padCharacter, $pads);
					}
					
					break;
				}
				
				// Get next byte
				$byte = array_shift($bytes);
				$bitsRead = 0;
				
			} else {
				$oldBitCount = 0;
				$newBitCount = $bitsPerCharacter;
			}
			
			// Read only the needed bits from this byte
			$bits = $byte >> 8 - ($bitsRead + ($newBitCount));
			$bits ^= $bits >> $newBitCount << $newBitCount;
			$bitsRead += $newBitCount;
			
			if ($oldBitCount) {
				// Bits come from seperate bytes, add $oldBits to $bits
				$bits = ($oldBits << $newBitCount) | $bits;
			}
			
			$encodedString .= $chars[$bits];
		}
		
		return $encodedString;
	}
	
	private static function _home_url_nofilter($path = '', $scheme = null) { //A version of the native get_home_url and get_option without the filter calls
		global $pagenow, $wpdb, $blog_id;
		
		static $cached_url = null;
		if ($cached_url !== null) {
			return $cached_url;
		}
		
		if (defined('WP_HOME') && WORDFENCE_PREFER_WP_HOME_FOR_WPML) {
			$cached_url = WP_HOME;
			return $cached_url;
		}
		
		if ( empty( $blog_id ) || !is_multisite() ) {
			$url = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'home' LIMIT 1");
			if (empty($url)) { //get_option uses siteurl instead if home is empty
				$url = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'siteurl' LIMIT 1");
			}
		}
		else if (is_multisite()) {
			$current_network = get_network();
			if ( 'relative' == $scheme )
				$url = $current_network->path;
			else
				$url = 'http://' . $current_network->domain . $current_network->path;
		}
		
		if ( ! in_array( $scheme, array( 'http', 'https', 'relative' ) ) ) {
			if ( is_ssl() && ! is_admin() && 'wp-login.php' !== $pagenow )
				$scheme = 'https';
			else
				$scheme = parse_url( $url, PHP_URL_SCHEME );
		}
		
		$url = set_url_scheme( $url, $scheme );
		
		if ( $path && is_string( $path ) )
			$url .= '/' . ltrim( $path, '/' );
		
		$cached_url = $url;
		return $url;
	}
	
	public static function refreshCachedHomeURL() {
		$pullDirectly = class_exists('WPML_URL_Filters');
		$homeurl = '';
		if ($pullDirectly) {
			//A version of the native get_home_url without the filter call
			$homeurl = self::_home_url_nofilter();
		}
		
		if (function_exists('get_bloginfo') && empty($homeurl)) {
			if (is_multisite()) {
				$homeurl = network_home_url();
				$homeurl = rtrim($homeurl, '/'); //Because previously we used get_bloginfo and it returns http://example.com without a '/' char.
			}
			else {
				$homeurl = home_url();
			}
		}
		
		if (wfConfig::get('wp_home_url') !== $homeurl) {
			wfConfig::set('wp_home_url', $homeurl);
		}
	}
	
	public static function wpHomeURL($path = '', $scheme = null) {
		$homeurl = wfConfig::get('wp_home_url', '');
		if (function_exists('get_bloginfo') && empty($homeurl)) {
			if (is_multisite()) {
				$homeurl = network_home_url($path, $scheme);
				$homeurl = rtrim($homeurl, '/'); //Because previously we used get_bloginfo and it returns http://example.com without a '/' char.
			}
			else {
				$homeurl = home_url($path, $scheme);
			}
		}
		return $homeurl;
	}
	
	private static function _site_url_nofilter($path = '', $scheme = null) { //A version of the native get_site_url and get_option without the filter calls
		global $pagenow, $wpdb, $blog_id;
		
		static $cached_url = null;
		if ($cached_url !== null) {
			return $cached_url;
		}
		
		if (defined('WP_SITEURL') && WORDFENCE_PREFER_WP_HOME_FOR_WPML) {
			$cached_url = WP_SITEURL;
			return $cached_url;
		}
		
		if ( empty( $blog_id ) || !is_multisite() ) {
			$url = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'siteurl' LIMIT 1");
		}
		else if (is_multisite()) {
			$current_network = get_network();
			if ( 'relative' == $scheme )
				$url = $current_network->path;
			else
				$url = 'http://' . $current_network->domain . $current_network->path;
		}
		
		if ( ! in_array( $scheme, array( 'http', 'https', 'relative' ) ) ) {
			if ( is_ssl() && ! is_admin() && 'wp-login.php' !== $pagenow )
				$scheme = 'https';
			else
				$scheme = parse_url( $url, PHP_URL_SCHEME );
		}
		
		$url = set_url_scheme( $url, $scheme );
		
		if ( $path && is_string( $path ) )
			$url .= '/' . ltrim( $path, '/' );
		
		$cached_url = $url;
		return $url;
	}
	
	public static function refreshCachedSiteURL() {
		$pullDirectly = class_exists('WPML_URL_Filters');
		$siteurl = '';
		if ($pullDirectly) {
			//A version of the native get_home_url without the filter call
			$siteurl = self::_site_url_nofilter();
		}
		
		if (function_exists('get_bloginfo') && empty($siteurl)) {
			if (is_multisite()) {
				$siteurl = network_site_url();
				$siteurl = rtrim($siteurl, '/'); //Because previously we used get_bloginfo and it returns http://example.com without a '/' char.
			}
			else {
				$siteurl = site_url();
			}
		}
		
		if (wfConfig::get('wp_site_url') !== $siteurl) {
			wfConfig::set('wp_site_url', $siteurl);
		}
	}
	
	public static function wpSiteURL($path = '', $scheme = null) {
		$siteurl = wfConfig::get('wp_site_url', '');
		if (function_exists('get_bloginfo') && empty($siteurl)) {
			if (is_multisite()) {
				$siteurl = network_site_url($path, $scheme);
			}
			else {
				$siteurl = site_url($path, $scheme);
			}
		}
		return $siteurl;
	}
	
	public static function wafInstallationType() {
		try {
			$status = (defined('WFWAF_ENABLED') && !WFWAF_ENABLED) ? 'disabled' : wfWaf::getInstance()->getStorageEngine()->getConfig('wafStatus');
			if (defined('WFWAF_ENABLED') && !WFWAF_ENABLED) {
				return "{$status}|const";
			}
			else if (defined('WFWAF_SUBDIRECTORY_INSTALL') && WFWAF_SUBDIRECTORY_INSTALL) {
				return "{$status}|subdir";
			}
			else if (defined('WFWAF_AUTO_PREPEND') && WFWAF_AUTO_PREPEND) {
				return "{$status}|extended";
			}
			
			return "{$status}|basic";
		}
		catch (Exception $e) {
			//Do nothing
		}
		
		return 'unknown';
	}
	
	public static function hex2bin($string) { //Polyfill for PHP < 5.4
		if (!is_string($string)) { return false; }
		if (strlen($string) % 2 == 1) { return false; }
		return pack('H*', $string);
	}
	
	/**
	 * Returns whether or not the site should be treated as if it's full-time SSL.
	 * 
	 * @return bool
	 */
	public static function isFullSSL() {
		return is_ssl() && parse_url(self::wpHomeURL(), PHP_URL_SCHEME) === 'https'; //It's possible for only wp-admin to be SSL so we check the home URL too
	}

	/**
	 * Identical to the same functions in wfWAFUtils.
	 * 
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
	
	public static function sets_equal($a1, $a2) {
		if (!is_array($a1) || !is_array($a2)) {
			return false;
		}
		
		if (count($a1) != count($a2)) {
			return false;
		}
		
		sort($a1, SORT_NUMERIC);
		sort($a2, SORT_NUMERIC);
		return $a1 == $a2;
	}
	
	public static function array_first($array) {
		if (empty($array)) {
			return null;
		}
		
		$values = array_values($array);
		return $values[0];
	}
	
	public static function array_last($array) {
		if (empty($array)) {
			return null;
		}
		
		$values = array_values($array);
		return $values[count($values) - 1];
	}
	
	/**
	 * Returns the current timestamp, adjusted as needed to get close to what we consider a true timestamp. We use this
	 * because a significant number of servers are using a drastically incorrect time.
	 *
	 * @return int
	 */
	public static function normalizedTime($base = false) {
		if ($base === false) {
			$base = time();
		}
		
		$offset = wfConfig::get('timeoffset_ntp', false);
		if ($offset === false) {
			$offset = wfConfig::get('timeoffset_wf', false);
			if ($offset === false) { $offset = 0; }
		}
		return $base + $offset;
	}
	
	/**
	 * Returns what we consider a true timestamp, adjusted as needed to match the local server's drift. We use this
	 * because a significant number of servers are using a drastically incorrect time.
	 *
	 * @return int
	 */
	public static function denormalizedTime($base) {
		$offset = wfConfig::get('timeoffset_ntp', false);
		if ($offset === false) {
			$offset = wfConfig::get('timeoffset_wf', false);
			if ($offset === false) { $offset = 0; }
		}
		return $base - $offset;
	}
	
	/**
	 * Returns the number of minutes for the time zone offset from UTC. If $timestamp and using a named time zone, 
	 * it will be adjusted automatically to match whether or not the server's time zone is in Daylight Savings Time.
	 * 
	 * @param bool|int $timestamp Assumed to be in UTC. If false, defaults to the current timestamp.
	 * @return int
	 */
	public static function timeZoneMinutes($timestamp = false) {
		if ($timestamp === false) {
			$timestamp = time();
		}
		
		$tz = get_option('timezone_string');
		if (!empty($tz)) {
			$timezone = new DateTimeZone($tz);
			$dtStr = gmdate("c", (int) $timestamp); //Have to do it this way because of PHP 5.2
			$dt = new DateTime($dtStr, $timezone);
			return (int) ($timezone->getOffset($dt) / 60);
		}
		else {
			$gmt = get_option('gmt_offset');
			if (!empty($gmt)) {
				return (int) ($gmt * 60);
			}
		}
		
		return 0;
	}
	
	/**
	 * Formats and returns the given timestamp using the time zone set for the WordPress installation.
	 * 
	 * @param string $format See the PHP docs on DateTime for the format options. 
	 * @param int|bool $timestamp Assumed to be in UTC. If false, defaults to the current timestamp.
	 * @return string
	 */
	public static function formatLocalTime($format, $timestamp = false) {
		if ($timestamp === false) {
			$timestamp = time();
		}
		
		$utc = new DateTimeZone('UTC');
		$dtStr = gmdate("c", (int) $timestamp); //Have to do it this way because of PHP 5.2
		$dt = new DateTime($dtStr, $utc);
		$tz = get_option('timezone_string');
		if (!empty($tz)) {
			$dt->setTimezone(new DateTimeZone($tz));
		}
		else {
			$gmt = get_option('gmt_offset');
			if (!empty($gmt)) {
				if (PHP_VERSION_ID < 50510) {
					$dtStr = gmdate("c", (int) ($timestamp + $gmt * 3600)); //Have to do it this way because of < PHP 5.5.10
					$dt = new DateTime($dtStr, $utc);
				}
				else {
					$direction = ($gmt > 0 ? '+' : '-');
					$gmt = abs($gmt);
					$h = (int) $gmt;
					$m = ($gmt - $h) * 60;
					$dt->setTimezone(new DateTimeZone($direction . str_pad($h, 2, '0', STR_PAD_LEFT) . str_pad($m, 2, '0', STR_PAD_LEFT)));
				}
			}
		}
		return $dt->format($format);
	}
	
	/**
	 * Parses the given time string and returns its DateTime with the server's configured time zone.
	 * 
	 * @param string $timestring
	 * @return DateTime
	 */
	public static function parseLocalTime($timestring) {
		$utc = new DateTimeZone('UTC');
		$tz = get_option('timezone_string');
		if (!empty($tz)) {
			$tz = new DateTimeZone($tz);
			return new DateTime($timestring, $tz);
		}
		else {
			$gmt = get_option('gmt_offset');
			if (!empty($gmt)) {
				if (PHP_VERSION_ID < 50510) {
					$timestamp = strtotime($timestring);
					$dtStr = gmdate("c", (int) ($timestamp + $gmt * 3600)); //Have to do it this way because of < PHP 5.5.10
					return new DateTime($dtStr, $utc);
				}
				else {
					$direction = ($gmt > 0 ? '+' : '-');
					$gmt = abs($gmt);
					$h = (int) $gmt;
					$m = ($gmt - $h) * 60;
					$tz = new DateTimeZone($direction . str_pad($h, 2, '0', STR_PAD_LEFT) . str_pad($m, 2, '0', STR_PAD_LEFT));
					return new DateTime($timestring, $tz);
				}
			}
		}
		return new DateTime($timestring);
	} 
}

// GeoIP lib uses these as well
if (!function_exists('inet_ntop')) {
	function inet_ntop($ip) {
		return wfUtils::_inet_ntop($ip);
	}
}
if (!function_exists('inet_pton')) {
	function inet_pton($ip) {
		return wfUtils::_inet_pton($ip);
	}
}


class wfWebServerInfo {

	const APACHE = 1;
	const NGINX = 2;
	const LITESPEED = 4;
	const IIS = 8;

	private $handler;
	private $software;
	private $softwareName;

	/**
	 *
	 */
	public static function createFromEnvironment() {
		$serverInfo = new self;
		$sapi = php_sapi_name();
		if (stripos($_SERVER['SERVER_SOFTWARE'], 'apache') !== false) {
			$serverInfo->setSoftware(self::APACHE);
			$serverInfo->setSoftwareName('apache');
		}
		if (stripos($_SERVER['SERVER_SOFTWARE'], 'litespeed') !== false || $sapi == 'litespeed') {
			$serverInfo->setSoftware(self::LITESPEED);
			$serverInfo->setSoftwareName('litespeed');
		}
		if (strpos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false) {
			$serverInfo->setSoftware(self::NGINX);
			$serverInfo->setSoftwareName('nginx');
		}
		if (strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false || strpos($_SERVER['SERVER_SOFTWARE'], 'ExpressionDevServer') !== false) {
			$serverInfo->setSoftware(self::IIS);
			$serverInfo->setSoftwareName('iis');
		}

		$serverInfo->setHandler($sapi);

		return $serverInfo;
	}

	/**
	 * @return bool
	 */
	public function isApache() {
		return $this->getSoftware() === self::APACHE;
	}

	/**
	 * @return bool
	 */
	public function isNGINX() {
		return $this->getSoftware() === self::NGINX;
	}

	/**
	 * @return bool
	 */
	public function isLiteSpeed() {
		return $this->getSoftware() === self::LITESPEED;
	}

	/**
	 * @return bool
	 */
	public function isIIS() {
		return $this->getSoftware() === self::IIS;
	}

	/**
	 * @return bool
	 */
	public function isApacheModPHP() {
		return $this->isApache() && function_exists('apache_get_modules');
	}

	/**
	 * Not sure if this can be implemented at the PHP level.
	 * @return bool
	 */
	public function isApacheSuPHP() {
		return $this->isApache() && $this->isCGI() &&
			function_exists('posix_getuid') &&
			getmyuid() === posix_getuid();
	}

	/**
	 * @return bool
	 */
	public function isCGI() {
		return !$this->isFastCGI() && stripos($this->getHandler(), 'cgi') !== false;
	}

	/**
	 * @return bool
	 */
	public function isFastCGI() {
		return stripos($this->getHandler(), 'fastcgi') !== false || stripos($this->getHandler(), 'fpm-fcgi') !== false;
	}

	/**
	 * @return mixed
	 */
	public function getHandler() {
		return $this->handler;
	}

	/**
	 * @param mixed $handler
	 */
	public function setHandler($handler) {
		$this->handler = $handler;
	}

	/**
	 * @return mixed
	 */
	public function getSoftware() {
		return $this->software;
	}

	/**
	 * @param mixed $software
	 */
	public function setSoftware($software) {
		$this->software = $software;
	}

	/**
	 * @return mixed
	 */
	public function getSoftwareName() {
		return $this->softwareName;
	}

	/**
	 * @param mixed $softwareName
	 */
	public function setSoftwareName($softwareName) {
		$this->softwareName = $softwareName;
	}
}

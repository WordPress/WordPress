<?php

/**
 *
 */
class wfWAFUserIPRange {

	/**
	 * @var string|null
	 */
	private $ip_string;

	/**
	 * @param string|null $ip_string
	 */
	public function __construct($ip_string = null) {
		$this->setIPString($ip_string);
	}
	
	public function isIPInRange($ip) {
		$ip_string = $this->getIPString();
		
		if (strpos($ip_string, '/') !== false) { //CIDR range -- 127.0.0.1/24
			return wfWAFUtils::subnetContainsIP($ip_string, $ip);
		}
		else if (strpos($ip_string, '[') !== false) //Bracketed range -- 127.0.0.[1-100]
		{
			// IPv4 range
			if (strpos($ip_string, '.') !== false && strpos($ip, '.') !== false) {
				// IPv4-mapped-IPv6
				if (preg_match('/:ffff:([^:]+)$/i', $ip_string, $matches)) {
					$ip_string = $matches[1];
				}
				if (preg_match('/:ffff:([^:]+)$/i', $ip, $matches)) {
					$ip = $matches[1];
				}
				
				// Range check
				if (preg_match('/\[\d+\-\d+\]/', $ip_string)) {
					$IPparts = explode('.', $ip);
					$whiteParts = explode('.', $ip_string);
					$mismatch = false;
					if (count($whiteParts) != 4 || count($IPparts) != 4) {
						return false;
					}
					
					for ($i = 0; $i <= 3; $i++) {
						if (preg_match('/^\[(\d+)\-(\d+)\]$/', $whiteParts[$i], $m)) {
							if ($IPparts[$i] < $m[1] || $IPparts[$i] > $m[2]) {
								$mismatch = true;
							}
						}
						else if ($whiteParts[$i] != $IPparts[$i]) {
							$mismatch = true;
						}
					}
					if ($mismatch === false) {
						return true; // Is whitelisted because we did not get a mismatch
					}
				}
				else if ($ip_string == $ip) {
					return true;
				}
				
				// IPv6 range
			}
			else if (strpos($ip_string, ':') !== false && strpos($ip, ':') !== false) {
				$ip = strtolower(wfWAFUtils::expandIPv6Address($ip));
				$ip_string = strtolower(self::expandIPv6Range($ip_string));
				if (preg_match('/\[[a-f0-9]+\-[a-f0-9]+\]/i', $ip_string)) {
					$IPparts = explode(':', $ip);
					$whiteParts = explode(':', $ip_string);
					$mismatch = false;
					if (count($whiteParts) != 8 || count($IPparts) != 8) {
						return false;
					}
					
					for ($i = 0; $i <= 7; $i++) {
						if (preg_match('/^\[([a-f0-9]+)\-([a-f0-9]+)\]$/i', $whiteParts[$i], $m)) {
							$ip_group = hexdec($IPparts[$i]);
							$range_group_from = hexdec($m[1]);
							$range_group_to = hexdec($m[2]);
							if ($ip_group < $range_group_from || $ip_group > $range_group_to) {
								$mismatch = true;
								break;
							}
						}
						else if ($whiteParts[$i] != $IPparts[$i]) {
							$mismatch = true;
							break;
						}
					}
					if ($mismatch === false) {
						return true; // Is whitelisted because we did not get a mismatch
					}
				}
				else if ($ip_string == $ip) {
					return true;
				}
			}
		}
		else if (strpos($ip_string, '-') !== false) { //Linear range -- 127.0.0.1 - 127.0.1.100
			list($ip1, $ip2) = explode('-', $ip_string);
			$ip1N = wfWAFUtils::inet_pton($ip1);
			$ip2N = wfWAFUtils::inet_pton($ip2);
			$ipN = wfWAFUtils::inet_pton($ip);
			return (strcmp($ip1N, $ipN) <= 0 && strcmp($ip2N, $ipN) >= 0);
		}
		else { //Treat as a literal IP
			$ip1 = @wfWAFUtils::inet_pton($ip_string);
			$ip2 = @wfWAFUtils::inet_pton($ip);
			if ($ip1 !== false && $ip1 == $ip2) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Expand a compressed printable range representation of an IPv6 address.
	 *
	 * @todo Hook up exceptions for better error handling.
	 * @todo Allow IPv4 mapped IPv6 addresses (::ffff:192.168.1.1).
	 * @param string $ip_range
	 * @return string
	 */
	public static function expandIPv6Range($ip_range) {
		$colon_count = substr_count($ip_range, ':');
		$dbl_colon_count = substr_count($ip_range, '::');
		if ($dbl_colon_count > 1) {
			return false;
		}
		$dbl_colon_pos = strpos($ip_range, '::');
		if ($dbl_colon_pos !== false) {
			$ip_range = str_replace('::', str_repeat(':0000',
					(($dbl_colon_pos === 0 || $dbl_colon_pos === strlen($ip_range) - 2) ? 9 : 8) - $colon_count) . ':', $ip_range);
			$ip_range = trim($ip_range, ':');
		}
		$colon_count = substr_count($ip_range, ':');
		if ($colon_count != 7) {
			return false;
		}

		$groups = explode(':', $ip_range);
		$expanded = '';
		foreach ($groups as $group) {
			if (preg_match('/\[([a-f0-9]{1,4})\-([a-f0-9]{1,4})\]/i', $group, $matches)) {
				$expanded .= sprintf('[%s-%s]', str_pad(strtolower($matches[1]), 4, '0', STR_PAD_LEFT), str_pad(strtolower($matches[2]), 4, '0', STR_PAD_LEFT)) . ':';
			} else if (preg_match('/[a-f0-9]{1,4}/i', $group)) {
				$expanded .= str_pad(strtolower($group), 4, '0', STR_PAD_LEFT) . ':';
			} else {
				return false;
			}
		}
		return trim($expanded, ':');
	}

	/**
	 * @return bool
	 */
	public function isValidRange() {
		return $this->isValidCIDRRange() || $this->isValidBracketedRange() || $this->isValidLinearRange() || filter_var($this->getIPString(), FILTER_VALIDATE_IP) !== false;
	}
	
	public function isValidCIDRRange() { //e.g., 192.0.2.1/24
		$ip_string = $this->getIPString();
		if (preg_match('/[^0-9a-f:\/\.]/i', $ip_string)) { return false; }
		$components = explode('/', $ip_string);
		if (count($components) != 2) { return false; }
		
		list($ip, $prefix) = $components;
		if (filter_var($ip, FILTER_VALIDATE_IP) === false) { return false; }
		
		if (!preg_match('/^\d+$/', $prefix)) { return false; }
		
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			if ($prefix < 0 || $prefix > 32) { return false; }
		}
		else {
			if ($prefix < 1 || $prefix > 128) { return false; }
		}
		
		return true;
	}
	
	public function isValidBracketedRange() { //e.g., 192.0.2.[1-10]
		$ip_string = $this->getIPString();
		if (preg_match('/[^0-9a-f:\.\[\]\-]/i', $ip_string)) { return false; }
		if (strpos($ip_string, '.') !== false) { //IPv4
			if (preg_match_all('/(\d+)/', $ip_string, $matches) > 0) {
				foreach ($matches[1] as $match) {
					$group = (int) $match;
					if ($group > 255 || $group < 0) {
						return false;
					}
				}
			}
			
			$group_regex = '([0-9]{1,3}|\[[0-9]{1,3}\-[0-9]{1,3}\])';
			return preg_match('/^' . str_repeat("{$group_regex}\\.", 3) . $group_regex . '$/i', $ip_string) > 0;
		}
		
		//IPv6
		if (strpos($ip_string, '::') !== false) {
			$ip_string = self::expandIPv6Range($ip_string);
		}
		if (!$ip_string) {
			return false;
		}
		$group_regex = '([a-f0-9]{1,4}|\[[a-f0-9]{1,4}\-[a-f0-9]{1,4}\])';
		return preg_match('/^' . str_repeat("$group_regex:", 7) . $group_regex . '$/i', $ip_string) > 0;
	}
	
	public function isValidLinearRange() { //e.g., 192.0.2.1-192.0.2.100
		$ip_string = $this->getIPString();
		if (preg_match('/[^0-9a-f:\.\-]/i', $ip_string)) { return false; }
		list($ip1, $ip2) = explode("-", $ip_string);
		$ip1N = @wfWAFUtils::inet_pton($ip1);
		$ip2N = @wfWAFUtils::inet_pton($ip2);
		
		if ($ip1N === false || filter_var($ip1, FILTER_VALIDATE_IP) === false || $ip2N === false || filter_var($ip2, FILTER_VALIDATE_IP) === false) {
			return false;
		}
		
		return strcmp($ip1N, $ip2N) <= 0;
	}
	
	protected function _sanitizeIPRange($ip_string) {
		$ip_string = preg_replace('/\s/', '', $ip_string); //Strip whitespace
		$ip_string = preg_replace('/[\\x{2013}-\\x{2015}]/u', '-', $ip_string); //Non-hyphen dashes to hyphen
		$ip_string = strtolower($ip_string);
		
		if (preg_match('/^\d+-\d+$/', $ip_string)) { //v5 32 bit int style format
			list($start, $end) = explode('-', $ip_string);
			$start = long2ip($start);
			$end = long2ip($end);
			$ip_string = "{$start}-{$end}";
		}
		
		return $ip_string;
	}


	/**
	 * @return string|null
	 */
	public function getIPString() {
		return $this->ip_string;
	}

	/**
	 * @param string|null $ip_string
	 */
	public function setIPString($ip_string) {
		$this->ip_string = $this->_sanitizeIPRange($ip_string);
	}
}

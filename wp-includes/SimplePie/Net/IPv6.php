<?php
/**
 * SimplePie
 *
 * A PHP-Based RSS and Atom Feed Framework.
 * Takes the hard work out of managing a complete RSS/Atom solution.
 *
 * Copyright (c) 2004-2012, Ryan Parman, Geoffrey Sneddon, Ryan McCue, and contributors
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 *
 * 	* Redistributions of source code must retain the above copyright notice, this list of
 * 	  conditions and the following disclaimer.
 *
 * 	* Redistributions in binary form must reproduce the above copyright notice, this list
 * 	  of conditions and the following disclaimer in the documentation and/or other materials
 * 	  provided with the distribution.
 *
 * 	* Neither the name of the SimplePie Team nor the names of its contributors may be used
 * 	  to endorse or promote products derived from this software without specific prior
 * 	  written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS
 * OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 * AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS
 * AND CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package SimplePie
 * @version 1.3
 * @copyright 2004-2012 Ryan Parman, Geoffrey Sneddon, Ryan McCue
 * @author Ryan Parman
 * @author Geoffrey Sneddon
 * @author Ryan McCue
 * @link http://simplepie.org/ SimplePie
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */


/**
 * Class to validate and to work with IPv6 addresses.
 *
 * @package SimplePie
 * @subpackage HTTP
 * @copyright 2003-2005 The PHP Group
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @link http://pear.php.net/package/Net_IPv6
 * @author Alexander Merz <alexander.merz@web.de>
 * @author elfrink at introweb dot nl
 * @author Josh Peck <jmp at joshpeck dot org>
 * @author Geoffrey Sneddon <geoffers@gmail.com>
 */
class SimplePie_Net_IPv6
{
	/**
	 * Uncompresses an IPv6 address
	 *
	 * RFC 4291 allows you to compress concecutive zero pieces in an address to
	 * '::'. This method expects a valid IPv6 address and expands the '::' to
	 * the required number of zero pieces.
	 *
	 * Example:  FF01::101   ->  FF01:0:0:0:0:0:0:101
	 *           ::1         ->  0:0:0:0:0:0:0:1
	 *
	 * @author Alexander Merz <alexander.merz@web.de>
	 * @author elfrink at introweb dot nl
	 * @author Josh Peck <jmp at joshpeck dot org>
	 * @copyright 2003-2005 The PHP Group
	 * @license http://www.opensource.org/licenses/bsd-license.php
	 * @param string $ip An IPv6 address
	 * @return string The uncompressed IPv6 address
	 */
	public static function uncompress($ip)
	{
		$c1 = -1;
		$c2 = -1;
		if (substr_count($ip, '::') === 1)
		{
			list($ip1, $ip2) = explode('::', $ip);
			if ($ip1 === '')
			{
				$c1 = -1;
			}
			else
			{
				$c1 = substr_count($ip1, ':');
			}
			if ($ip2 === '')
			{
				$c2 = -1;
			}
			else
			{
				$c2 = substr_count($ip2, ':');
			}
			if (strpos($ip2, '.') !== false)
			{
				$c2++;
			}
			// ::
			if ($c1 === -1 && $c2 === -1)
			{
				$ip = '0:0:0:0:0:0:0:0';
			}
			// ::xxx
			else if ($c1 === -1)
			{
				$fill = str_repeat('0:', 7 - $c2);
				$ip = str_replace('::', $fill, $ip);
			}
			// xxx::
			else if ($c2 === -1)
			{
				$fill = str_repeat(':0', 7 - $c1);
				$ip = str_replace('::', $fill, $ip);
			}
			// xxx::xxx
			else
			{
				$fill = ':' . str_repeat('0:', 6 - $c2 - $c1);
				$ip = str_replace('::', $fill, $ip);
			}
		}
		return $ip;
	}

	/**
	 * Compresses an IPv6 address
	 *
	 * RFC 4291 allows you to compress concecutive zero pieces in an address to
	 * '::'. This method expects a valid IPv6 address and compresses consecutive
	 * zero pieces to '::'.
	 *
	 * Example:  FF01:0:0:0:0:0:0:101   ->  FF01::101
	 *           0:0:0:0:0:0:0:1        ->  ::1
	 *
	 * @see uncompress()
	 * @param string $ip An IPv6 address
	 * @return string The compressed IPv6 address
	 */
	public static function compress($ip)
	{
		// Prepare the IP to be compressed
		$ip = self::uncompress($ip);
		$ip_parts = self::split_v6_v4($ip);

		// Replace all leading zeros
		$ip_parts[0] = preg_replace('/(^|:)0+([0-9])/', '\1\2', $ip_parts[0]);

		// Find bunches of zeros
		if (preg_match_all('/(?:^|:)(?:0(?::|$))+/', $ip_parts[0], $matches, PREG_OFFSET_CAPTURE))
		{
			$max = 0;
			$pos = null;
			foreach ($matches[0] as $match)
			{
				if (strlen($match[0]) > $max)
				{
					$max = strlen($match[0]);
					$pos = $match[1];
				}
			}

			$ip_parts[0] = substr_replace($ip_parts[0], '::', $pos, $max);
		}

		if ($ip_parts[1] !== '')
		{
			return implode(':', $ip_parts);
		}
		else
		{
			return $ip_parts[0];
		}
	}

	/**
	 * Splits an IPv6 address into the IPv6 and IPv4 representation parts
	 *
	 * RFC 4291 allows you to represent the last two parts of an IPv6 address
	 * using the standard IPv4 representation
	 *
	 * Example:  0:0:0:0:0:0:13.1.68.3
	 *           0:0:0:0:0:FFFF:129.144.52.38
	 *
	 * @param string $ip An IPv6 address
	 * @return array [0] contains the IPv6 represented part, and [1] the IPv4 represented part
	 */
	private static function split_v6_v4($ip)
	{
		if (strpos($ip, '.') !== false)
		{
			$pos = strrpos($ip, ':');
			$ipv6_part = substr($ip, 0, $pos);
			$ipv4_part = substr($ip, $pos + 1);
			return array($ipv6_part, $ipv4_part);
		}
		else
		{
			return array($ip, '');
		}
	}

	/**
	 * Checks an IPv6 address
	 *
	 * Checks if the given IP is a valid IPv6 address
	 *
	 * @param string $ip An IPv6 address
	 * @return bool true if $ip is a valid IPv6 address
	 */
	public static function check_ipv6($ip)
	{
		$ip = self::uncompress($ip);
		list($ipv6, $ipv4) = self::split_v6_v4($ip);
		$ipv6 = explode(':', $ipv6);
		$ipv4 = explode('.', $ipv4);
		if (count($ipv6) === 8 && count($ipv4) === 1 || count($ipv6) === 6 && count($ipv4) === 4)
		{
			foreach ($ipv6 as $ipv6_part)
			{
				// The section can't be empty
				if ($ipv6_part === '')
					return false;

				// Nor can it be over four characters
				if (strlen($ipv6_part) > 4)
					return false;

				// Remove leading zeros (this is safe because of the above)
				$ipv6_part = ltrim($ipv6_part, '0');
				if ($ipv6_part === '')
					$ipv6_part = '0';

				// Check the value is valid
				$value = hexdec($ipv6_part);
				if (dechex($value) !== strtolower($ipv6_part) || $value < 0 || $value > 0xFFFF)
					return false;
			}
			if (count($ipv4) === 4)
			{
				foreach ($ipv4 as $ipv4_part)
				{
					$value = (int) $ipv4_part;
					if ((string) $value !== $ipv4_part || $value < 0 || $value > 0xFF)
						return false;
				}
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Checks if the given IP is a valid IPv6 address
	 *
	 * @codeCoverageIgnore
	 * @deprecated Use {@see SimplePie_Net_IPv6::check_ipv6()} instead
	 * @see check_ipv6
	 * @param string $ip An IPv6 address
	 * @return bool true if $ip is a valid IPv6 address
	 */
	public static function checkIPv6($ip)
	{
		return self::check_ipv6($ip);
	}
}

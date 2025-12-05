<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL v3 or later
 */
namespace Matomo\Network;

/**
 * IP address utilities (for both IPv4 and IPv6).
 *
 * As a matter of naming convention, we use `$ip` for the binary format (network address format)
 * and `$ipString` for the string/presentation format (i.e., human-readable form).
 */
class IPUtils
{
    /**
     * Removes the port and the last portion of a CIDR IP address.
     *
     * @param string $ipString The IP address to sanitize.
     * @return string
     */
    public static function sanitizeIp($ipString)
    {
        $ipString = trim($ipString);
        // CIDR notation, A.B.C.D/E
        $posSlash = strrpos($ipString, '/');
        if ($posSlash !== \false) {
            $ipString = substr($ipString, 0, $posSlash);
        }
        $posColon = strrpos($ipString, ':');
        $posDot = strrpos($ipString, '.');
        if ($posColon !== \false) {
            // IPv6 address with port, [A:B:C:D:E:F:G:H]:EEEE
            $posRBrac = strrpos($ipString, ']');
            if ($posRBrac !== \false && $ipString[0] == '[') {
                $ipString = substr($ipString, 1, $posRBrac - 1);
            }
            if ($posDot !== \false) {
                // IPv4 address with port, A.B.C.D:EEEE
                if ($posColon > $posDot) {
                    $ipString = substr($ipString, 0, $posColon);
                }
                // else: Dotted quad IPv6 address, A:B:C:D:E:F:G.H.I.J
            } else {
                if (strpos($ipString, ':') === $posColon) {
                    $ipString = substr($ipString, 0, $posColon);
                }
            }
            // else: IPv6 address, A:B:C:D:E:F:G:H
        }
        // else: IPv4 address, A.B.C.D
        return $ipString;
    }
    /**
     * Sanitize human-readable (user-supplied) IP address range.
     *
     * Accepts the following formats for $ipRange:
     * - single IPv4 address, e.g., 127.0.0.1
     * - single IPv6 address, e.g., ::1/128
     * - IPv4 block using CIDR notation, e.g., 192.168.0.0/22 represents the IPv4 addresses from 192.168.0.0 to 192.168.3.255
     * - IPv6 block using CIDR notation, e.g., 2001:DB8::/48 represents the IPv6 addresses from 2001:DB8:0:0:0:0:0:0 to 2001:DB8:0:FFFF:FFFF:FFFF:FFFF:FFFF
     * - wildcards, e.g., 192.168.0.* or 2001:DB8:*:*:*:*:*:*
     *
     * @param string $ipRangeString IP address range
     * @return string|null  IP address range in CIDR notation OR null on failure
     */
    public static function sanitizeIpRange($ipRangeString)
    {
        $ipRangeString = trim($ipRangeString);
        if (empty($ipRangeString)) {
            return null;
        }
        // IP address with wildcards '*'
        if (strpos($ipRangeString, '*') !== \false) {
            // Disallow prefixed wildcards and anything other than wildcards
            // and separators (including IPv6 zero groups) after first wildcard
            if (preg_match('/[^.:]\\*|\\*.*([^.:*]|::)/', $ipRangeString)) {
                return null;
            }
            $numWildcards = substr_count($ipRangeString, '*');
            $ipRangeString = str_replace('*', '0', $ipRangeString);
            // CIDR
        } elseif (($pos = strpos($ipRangeString, '/')) !== \false) {
            $bits = substr($ipRangeString, $pos + 1);
            $ipRangeString = substr($ipRangeString, 0, $pos);
            if (!is_numeric($bits)) {
                return null;
            }
        }
        // single IP
        if (($ip = @inet_pton($ipRangeString)) === \false) {
            return null;
        }
        $maxbits = strlen($ip) * 8;
        if (!isset($bits)) {
            $bits = $maxbits;
            if (isset($numWildcards)) {
                $bits -= ($maxbits === 32 ? 8 : 16) * $numWildcards;
            }
        }
        if ($bits < 0 || $bits > $maxbits) {
            return null;
        }
        return "{$ipRangeString}/{$bits}";
    }
    /**
     * Converts an IP address in string/presentation format to binary/network address format.
     *
     * @param string $ipString IP address, either IPv4 or IPv6, e.g. `'127.0.0.1'`.
     * @return string Binary-safe string, e.g. `"\x7F\x00\x00\x01"`.
     */
    public static function stringToBinaryIP($ipString)
    {
        // use @inet_pton() because it throws an exception and E_WARNING on invalid input
        $ip = @inet_pton($ipString);
        return $ip === \false ? "\x00\x00\x00\x00" : $ip;
    }
    /**
     * Convert binary/network address format to string/presentation format.
     *
     * @param string $ip IP address in binary/network address format, e.g. `"\x7F\x00\x00\x01"`.
     * @return string IP address in string format, e.g. `'127.0.0.1'`.
     */
    public static function binaryToStringIP($ip)
    {
        // use @inet_ntop() because it throws an exception and E_WARNING on invalid input
        $ipStr = @inet_ntop($ip);
        return $ipStr === \false ? '0.0.0.0' : $ipStr;
    }
    /**
     * Get low and high IP addresses for a specified IP range.
     *
     * @param string $ipRange An IP address range in string format, e.g. `'192.168.1.1/24'`.
     * @return array|null Array `array($lowIp, $highIp)` in binary format, or null on failure.
     */
    public static function getIPRangeBounds($ipRange)
    {
        $ipRange = self::sanitizeIpRange($ipRange);
        if ($ipRange === null || ($pos = strpos($ipRange, '/')) === \false || $pos + 1 === strlen($ipRange)) {
            return null;
        }
        $range = substr($ipRange, 0, $pos);
        $high = $low = @inet_pton($range);
        if ($low === \false) {
            return null;
        }
        $addrLen = strlen($low);
        $bits = (int) substr($ipRange, $pos + 1);
        if ($bits < 0 || $bits > $addrLen * 8) {
            return null;
        }
        $octet = (int) (($bits + 7) / 8);
        for ($i = $octet; $i < $addrLen; $i++) {
            $low[$i] = chr(0);
            $high[$i] = chr(255);
        }
        if ($n = $bits % 8) {
            $mask = (1 << 8 - $n) - 1;
            $value = ord($low[--$octet]) & ~$mask;
            $low[$octet] = chr($value);
            $high[$octet] = chr($value | $mask);
        }
        return array($low, $high);
    }
}

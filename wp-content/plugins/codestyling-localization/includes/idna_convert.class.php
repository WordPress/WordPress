<?php
// {{{ license

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */
//
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU Lesser General Public License as       |
// | published by the Free Software Foundation; either version 2.1 of the |
// | License, or (at your option) any later version.                      |
// |                                                                      |
// | This library is distributed in the hope that it will be useful, but  |
// | WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// |                                                                      |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 |
// | USA.                                                                 |
// +----------------------------------------------------------------------+
//

// }}}

/**
 * Encode/decode Internationalized Domain Names.
 *
 * The class allows to convert internationalized domain names
 * (see RFC 3490 for details) as they can be used with various registries worldwide
 * to be translated between their original (localized) form and their encoded form
 * as it will be used in the DNS (Domain Name System).
 *
 * The class provides two public methods, encode() and decode(), which do exactly
 * what you would expect them to do. You are allowed to use complete domain names,
 * simple strings and complete email addresses as well. That means, that you might
 * use any of the following notations:
 *
 * - www.nörgler.com
 * - xn--nrgler-wxa
 * - xn--brse-5qa.xn--knrz-1ra.info
 *
 * Unicode input might be given as either UTF-8 string, UCS-4 string or UCS-4 array.
 * Unicode output is available in the same formats.
 * You can select your preferred format via {@link set_paramter()}.
 *
 * ACE input and output is always expected to be ASCII.
 *
 * @author  Matthias Sommerfeld <mso@phlylabs.de>
 * @copyright 2004-2011 phlyLabs Berlin, http://phlylabs.de
 * @version 0.8.0 2011-03-11
 */
class idna_convert
{
    // NP See below

    // Internal settings, do not mess with them
    protected $_punycode_prefix = 'xn--';
    protected $_invalid_ucs = 0x80000000;
    protected $_max_ucs = 0x10FFFF;
    protected $_base = 36;
    protected $_tmin = 1;
    protected $_tmax = 26;
    protected $_skew = 38;
    protected $_damp = 700;
    protected $_initial_bias = 72;
    protected $_initial_n = 0x80;
    protected $_sbase = 0xAC00;
    protected $_lbase = 0x1100;
    protected $_vbase = 0x1161;
    protected $_tbase = 0x11A7;
    protected $_lcount = 19;
    protected $_vcount = 21;
    protected $_tcount = 28;
    protected $_ncount = 588;   // _vcount * _tcount
    protected $_scount = 11172; // _lcount * _tcount * _vcount
    protected $_error = false;

    protected static $_mb_string_overload = null;

    // See {@link set_paramter()} for details of how to change the following
    // settings from within your script / application
    protected $_api_encoding = 'utf8';   // Default input charset is UTF-8
    protected $_allow_overlong = false;  // Overlong UTF-8 encodings are forbidden
    protected $_strict_mode = false;     // Behave strict or not
    protected $_idn_version = 2003;      // Can be either 2003 (old, default) or 2008

    /**
     * the constructor
     *
     * @param array $options
     * @return boolean
     * @since 0.5.2
     */
    public function __construct($options = false)
    {
        $this->slast = $this->_sbase + $this->_lcount * $this->_vcount * $this->_tcount;
        // If parameters are given, pass these to the respective method
        if (is_array($options)) {
            $this->set_parameter($options);
        }

        // populate mbstring overloading cache if not set
        if (self::$_mb_string_overload === null) {
            self::$_mb_string_overload = (extension_loaded('mbstring')
                && (ini_get('mbstring.func_overload') & 0x02) === 0x02);
        }
    }

    /**
     * Sets a new option value. Available options and values:
     * [encoding - Use either UTF-8, UCS4 as array or UCS4 as string as input ('utf8' for UTF-8,
     *         'ucs4_string' and 'ucs4_array' respectively for UCS4); The output is always UTF-8]
     * [overlong - Unicode does not allow unnecessarily long encodings of chars,
     *             to allow this, set this parameter to true, else to false;
     *             default is false.]
     * [strict - true: strict mode, good for registration purposes - Causes errors
     *           on failures; false: loose mode, ideal for "wildlife" applications
     *           by silently ignoring errors and returning the original input instead
     *
     * @param    mixed     Parameter to set (string: single parameter; array of Parameter => Value pairs)
     * @param    string    Value to use (if parameter 1 is a string)
     * @return   boolean   true on success, false otherwise
     */
    public function set_parameter($option, $value = false)
    {
        if (!is_array($option)) {
            $option = array($option => $value);
        }
        foreach ($option as $k => $v) {
            switch ($k) {
            case 'encoding':
                switch ($v) {
                case 'utf8':
                case 'ucs4_string':
                case 'ucs4_array':
                    $this->_api_encoding = $v;
                    break;
                default:
                    $this->_error('Set Parameter: Unknown parameter '.$v.' for option '.$k);
                    return false;
                }
                break;
            case 'overlong':
                $this->_allow_overlong = ($v) ? true : false;
                break;
            case 'strict':
                $this->_strict_mode = ($v) ? true : false;
                break;
            case 'idn_version':
                if (in_array($v, array('2003', '2008'))) {
                    $this->_idn_version = $v;
                } else {
                    $this->_error('Set Parameter: Unknown parameter '.$v.' for option '.$k);
                }
                break;
            case 'encode_german_sz': // Deprecated
                if (!$v) {
                    self::$NP['replacemaps'][0xDF] = array(0x73, 0x73);
                } else {
                    unset(self::$NP['replacemaps'][0xDF]);
                }
                break;
            default:
                $this->_error('Set Parameter: Unknown option '.$k);
                return false;
            }
        }
        return true;
    }

    /**
     * Decode a given ACE domain name
     * @param    string   Domain name (ACE string)
     * [@param    string   Desired output encoding, see {@link set_parameter}]
     * @return   string   Decoded Domain name (UTF-8 or UCS-4)
     */
    public function decode($input, $one_time_encoding = false)
    {
        // Optionally set
        if ($one_time_encoding) {
            switch ($one_time_encoding) {
            case 'utf8':
            case 'ucs4_string':
            case 'ucs4_array':
                break;
            default:
                $this->_error('Unknown encoding '.$one_time_encoding);
                return false;
            }
        }
        // Make sure to drop any newline characters around
        $input = trim($input);

        // Negotiate input and try to determine, whether it is a plain string,
        // an email address or something like a complete URL
        if (strpos($input, '@')) { // Maybe it is an email address
            // No no in strict mode
            if ($this->_strict_mode) {
                $this->_error('Only simple domain name parts can be handled in strict mode');
                return false;
            }
            list ($email_pref, $input) = explode('@', $input, 2);
            $arr = explode('.', $input);
            foreach ($arr as $k => $v) {
                if (preg_match('!^'.preg_quote($this->_punycode_prefix, '!').'!', $v)) {
                    $conv = $this->_decode($v);
                    if ($conv) $arr[$k] = $conv;
                }
            }
            $input = join('.', $arr);
            $arr = explode('.', $email_pref);
            foreach ($arr as $k => $v) {
                if (preg_match('!^'.preg_quote($this->_punycode_prefix, '!').'!', $v)) {
                    $conv = $this->_decode($v);
                    if ($conv) $arr[$k] = $conv;
                }
            }
            $email_pref = join('.', $arr);
            $return = $email_pref . '@' . $input;
        } elseif (preg_match('![:\./]!', $input)) { // Or a complete domain name (with or without paths / parameters)
            // No no in strict mode
            if ($this->_strict_mode) {
                $this->_error('Only simple domain name parts can be handled in strict mode');
                return false;
            }
            $parsed = parse_url($input);
            if (isset($parsed['host'])) {
                $arr = explode('.', $parsed['host']);
                foreach ($arr as $k => $v) {
                    $conv = $this->_decode($v);
                    if ($conv) $arr[$k] = $conv;
                }
                $parsed['host'] = join('.', $arr);
                $return =
                        (empty($parsed['scheme']) ? '' : $parsed['scheme'].(strtolower($parsed['scheme']) == 'mailto' ? ':' : '://'))
                        .(empty($parsed['user']) ? '' : $parsed['user'].(empty($parsed['pass']) ? '' : ':'.$parsed['pass']).'@')
                        .$parsed['host']
                        .(empty($parsed['port']) ? '' : ':'.$parsed['port'])
                        .(empty($parsed['path']) ? '' : $parsed['path'])
                        .(empty($parsed['query']) ? '' : '?'.$parsed['query'])
                        .(empty($parsed['fragment']) ? '' : '#'.$parsed['fragment']);
            } else { // parse_url seems to have failed, try without it
                $arr = explode('.', $input);
                foreach ($arr as $k => $v) {
                    $conv = $this->_decode($v);
                    $arr[$k] = ($conv) ? $conv : $v;
                }
                $return = join('.', $arr);
            }
        } else { // Otherwise we consider it being a pure domain name string
            $return = $this->_decode($input);
            if (!$return) $return = $input;
        }
        // The output is UTF-8 by default, other output formats need conversion here
        // If one time encoding is given, use this, else the objects property
        switch (($one_time_encoding) ? $one_time_encoding : $this->_api_encoding) {
        case 'utf8':
            return $return;
            break;
        case 'ucs4_string':
           return $this->_ucs4_to_ucs4_string($this->_utf8_to_ucs4($return));
           break;
        case 'ucs4_array':
            return $this->_utf8_to_ucs4($return);
            break;
        default:
            $this->_error('Unsupported output format');
            return false;
        }
    }

    /**
     * Encode a given UTF-8 domain name
     * @param    string   Domain name (UTF-8 or UCS-4)
     * [@param    string   Desired input encoding, see {@link set_parameter}]
     * @return   string   Encoded Domain name (ACE string)
     */
    public function encode($decoded, $one_time_encoding = false)
    {
        // Forcing conversion of input to UCS4 array
        // If one time encoding is given, use this, else the objects property
        switch ($one_time_encoding ? $one_time_encoding : $this->_api_encoding) {
        case 'utf8':
            $decoded = $this->_utf8_to_ucs4($decoded);
            break;
        case 'ucs4_string':
           $decoded = $this->_ucs4_string_to_ucs4($decoded);
        case 'ucs4_array':
           break;
        default:
            $this->_error('Unsupported input format: '.($one_time_encoding ? $one_time_encoding : $this->_api_encoding));
            return false;
        }

        // No input, no output, what else did you expect?
        if (empty($decoded)) return '';

        // Anchors for iteration
        $last_begin = 0;
        // Output string
        $output = '';
        foreach ($decoded as $k => $v) {
            // Make sure to use just the plain dot
            switch($v) {
            case 0x3002:
            case 0xFF0E:
            case 0xFF61:
                $decoded[$k] = 0x2E;
                // Right, no break here, the above are converted to dots anyway
            // Stumbling across an anchoring character
            case 0x2E:
            case 0x2F:
            case 0x3A:
            case 0x3F:
            case 0x40:
                // Neither email addresses nor URLs allowed in strict mode
                if ($this->_strict_mode) {
                   $this->_error('Neither email addresses nor URLs are allowed in strict mode.');
                   return false;
                } else {
                    // Skip first char
                    if ($k) {
                        $encoded = '';
                        $encoded = $this->_encode(array_slice($decoded, $last_begin, (($k)-$last_begin)));
                        if ($encoded) {
                            $output .= $encoded;
                        } else {
                            $output .= $this->_ucs4_to_utf8(array_slice($decoded, $last_begin, (($k)-$last_begin)));
                        }
                        $output .= chr($decoded[$k]);
                    }
                    $last_begin = $k + 1;
                }
            }
        }
        // Catch the rest of the string
        if ($last_begin) {
            $inp_len = sizeof($decoded);
            $encoded = '';
            $encoded = $this->_encode(array_slice($decoded, $last_begin, (($inp_len)-$last_begin)));
            if ($encoded) {
                $output .= $encoded;
            } else {
                $output .= $this->_ucs4_to_utf8(array_slice($decoded, $last_begin, (($inp_len)-$last_begin)));
            }
            return $output;
        } else {
            if ($output = $this->_encode($decoded)) {
                return $output;
            } else {
                return $this->_ucs4_to_utf8($decoded);
            }
        }
    }

    /**
     * Removes a weakness of encode(), which cannot properly handle URIs but instead encodes their
     * path or query components, too.
     * @param string  $uri  Expects the URI as a UTF-8 (or ASCII) string
     * @return  string  The URI encoded to Punycode, everything but the host component is left alone
     * @since 0.6.4
     */
    public function encode_uri($uri)
    {
        $parsed = parse_url($uri);
        if (!isset($parsed['host'])) {
            $this->_error('The given string does not look like a URI');
            return false;
        }
        $arr = explode('.', $parsed['host']);
        foreach ($arr as $k => $v) {
            $conv = $this->encode($v, 'utf8');
            if ($conv) $arr[$k] = $conv;
        }
        $parsed['host'] = join('.', $arr);
        $return =
                (empty($parsed['scheme']) ? '' : $parsed['scheme'].(strtolower($parsed['scheme']) == 'mailto' ? ':' : '://'))
                .(empty($parsed['user']) ? '' : $parsed['user'].(empty($parsed['pass']) ? '' : ':'.$parsed['pass']).'@')
                .$parsed['host']
                .(empty($parsed['port']) ? '' : ':'.$parsed['port'])
                .(empty($parsed['path']) ? '' : $parsed['path'])
                .(empty($parsed['query']) ? '' : '?'.$parsed['query'])
                .(empty($parsed['fragment']) ? '' : '#'.$parsed['fragment']);
        return $return;
    }

    /**
     * Use this method to get the last error ocurred
     * @param    void
     * @return   string   The last error, that occured
     */
    public function get_last_error()
    {
        return $this->_error;
    }

    /**
     * The actual decoding algorithm
     * @param string
     * @return mixed
     */
    protected function _decode($encoded)
    {
        $decoded = array();
        // find the Punycode prefix
        if (!preg_match('!^'.preg_quote($this->_punycode_prefix, '!').'!', $encoded)) {
            $this->_error('This is not a punycode string');
            return false;
        }
        $encode_test = preg_replace('!^'.preg_quote($this->_punycode_prefix, '!').'!', '', $encoded);
        // If nothing left after removing the prefix, it is hopeless
        if (!$encode_test) {
            $this->_error('The given encoded string was empty');
            return false;
        }
        // Find last occurence of the delimiter
        $delim_pos = strrpos($encoded, '-');
        if ($delim_pos > self::byteLength($this->_punycode_prefix)) {
            for ($k = self::byteLength($this->_punycode_prefix); $k < $delim_pos; ++$k) {
                $decoded[] = ord($encoded{$k});
            }
        }
        $deco_len = count($decoded);
        $enco_len = self::byteLength($encoded);

        // Wandering through the strings; init
        $is_first = true;
        $bias = $this->_initial_bias;
        $idx = 0;
        $char = $this->_initial_n;

        for ($enco_idx = ($delim_pos) ? ($delim_pos + 1) : 0; $enco_idx < $enco_len; ++$deco_len) {
            for ($old_idx = $idx, $w = 1, $k = $this->_base; 1 ; $k += $this->_base) {
                $digit = $this->_decode_digit($encoded{$enco_idx++});
                $idx += $digit * $w;
                $t = ($k <= $bias) ? $this->_tmin :
                        (($k >= $bias + $this->_tmax) ? $this->_tmax : ($k - $bias));
                if ($digit < $t) break;
                $w = (int) ($w * ($this->_base - $t));
            }
            $bias = $this->_adapt($idx - $old_idx, $deco_len + 1, $is_first);
            $is_first = false;
            $char += (int) ($idx / ($deco_len + 1));
            $idx %= ($deco_len + 1);
            if ($deco_len > 0) {
                // Make room for the decoded char
                for ($i = $deco_len; $i > $idx; $i--) $decoded[$i] = $decoded[($i - 1)];
            }
            $decoded[$idx++] = $char;
        }
        return $this->_ucs4_to_utf8($decoded);
    }

    /**
     * The actual encoding algorithm
     * @param  string
     * @return mixed
     */
    protected function _encode($decoded)
    {
        // We cannot encode a domain name containing the Punycode prefix
        $extract = self::byteLength($this->_punycode_prefix);
        $check_pref = $this->_utf8_to_ucs4($this->_punycode_prefix);
        $check_deco = array_slice($decoded, 0, $extract);

        if ($check_pref == $check_deco) {
            $this->_error('This is already a punycode string');
            return false;
        }
        // We will not try to encode strings consisting of basic code points only
        $encodable = false;
        foreach ($decoded as $k => $v) {
            if ($v > 0x7a) {
                $encodable = true;
                break;
            }
        }
        if (!$encodable) {
            $this->_error('The given string does not contain encodable chars');
            return false;
        }
        // Do NAMEPREP
        $decoded = $this->_nameprep($decoded);
        if (!$decoded || !is_array($decoded)) return false; // NAMEPREP failed
        $deco_len  = count($decoded);
        if (!$deco_len) return false; // Empty array
        $codecount = 0; // How many chars have been consumed
        $encoded = '';
        // Copy all basic code points to output
        for ($i = 0; $i < $deco_len; ++$i) {
            $test = $decoded[$i];
            // Will match [-0-9a-zA-Z]
            if ((0x2F < $test && $test < 0x40) || (0x40 < $test && $test < 0x5B)
                    || (0x60 < $test && $test <= 0x7B) || (0x2D == $test)) {
                $encoded .= chr($decoded[$i]);
                $codecount++;
            }
        }
        if ($codecount == $deco_len) return $encoded; // All codepoints were basic ones

        // Start with the prefix; copy it to output
        $encoded = $this->_punycode_prefix.$encoded;
        // If we have basic code points in output, add an hyphen to the end
        if ($codecount) $encoded .= '-';
        // Now find and encode all non-basic code points
        $is_first = true;
        $cur_code = $this->_initial_n;
        $bias = $this->_initial_bias;
        $delta = 0;
        while ($codecount < $deco_len) {
            // Find the smallest code point >= the current code point and
            // remember the last ouccrence of it in the input
            for ($i = 0, $next_code = $this->_max_ucs; $i < $deco_len; $i++) {
                if ($decoded[$i] >= $cur_code && $decoded[$i] <= $next_code) {
                    $next_code = $decoded[$i];
                }
            }
            $delta += ($next_code - $cur_code) * ($codecount + 1);
            $cur_code = $next_code;

            // Scan input again and encode all characters whose code point is $cur_code
            for ($i = 0; $i < $deco_len; $i++) {
                if ($decoded[$i] < $cur_code) {
                    $delta++;
                } elseif ($decoded[$i] == $cur_code) {
                    for ($q = $delta, $k = $this->_base; 1; $k += $this->_base) {
                        $t = ($k <= $bias) ? $this->_tmin :
                                (($k >= $bias + $this->_tmax) ? $this->_tmax : $k - $bias);
                        if ($q < $t) break;
                        $encoded .= $this->_encode_digit(intval($t + (($q - $t) % ($this->_base - $t)))); //v0.4.5 Changed from ceil() to intval()
                        $q = (int) (($q - $t) / ($this->_base - $t));
                    }
                    $encoded .= $this->_encode_digit($q);
                    $bias = $this->_adapt($delta, $codecount+1, $is_first);
                    $codecount++;
                    $delta = 0;
                    $is_first = false;
                }
            }
            $delta++;
            $cur_code++;
        }
        return $encoded;
    }

    /**
     * Adapt the bias according to the current code point and position
     * @param int $delta
     * @param int $npoints
     * @param int $is_first
     * @return int
     */
    protected function _adapt($delta, $npoints, $is_first)
    {
        $delta = intval($is_first ? ($delta / $this->_damp) : ($delta / 2));
        $delta += intval($delta / $npoints);
        for ($k = 0; $delta > (($this->_base - $this->_tmin) * $this->_tmax) / 2; $k += $this->_base) {
            $delta = intval($delta / ($this->_base - $this->_tmin));
        }
        return intval($k + ($this->_base - $this->_tmin + 1) * $delta / ($delta + $this->_skew));
    }

    /**
     * Encoding a certain digit
     * @param    int $d
     * @return string
     */
    protected function _encode_digit($d)
    {
        return chr($d + 22 + 75 * ($d < 26));
    }

    /**
     * Decode a certain digit
     * @param    int $cp
     * @return int
     */
    protected function _decode_digit($cp)
    {
        $cp = ord($cp);
        return ($cp - 48 < 10) ? $cp - 22 : (($cp - 65 < 26) ? $cp - 65 : (($cp - 97 < 26) ? $cp - 97 : $this->_base));
    }

    /**
     * Internal error handling method
     * @param  string $error
     */
    protected function _error($error = '')
    {
        $this->_error = $error;
    }

    /**
     * Do Nameprep according to RFC3491 and RFC3454
     * @param    array    Unicode Characters
     * @return   string   Unicode Characters, Nameprep'd
     */
    protected function _nameprep($input)
    {
        $output = array();
        $error = false;
        //
        // Mapping
        // Walking through the input array, performing the required steps on each of
        // the input chars and putting the result into the output array
        // While mapping required chars we apply the cannonical ordering
        foreach ($input as $v) {
            // Map to nothing == skip that code point
            if (in_array($v, self::$NP['map_nothing'])) continue;
            // Try to find prohibited input
            if (in_array($v, self::$NP['prohibit']) || in_array($v, self::$NP['general_prohibited'])) {
                $this->_error('NAMEPREP: Prohibited input U+'.sprintf('%08X', $v));
                return false;
            }
            foreach (self::$NP['prohibit_ranges'] as $range) {
                if ($range[0] <= $v && $v <= $range[1]) {
                    $this->_error('NAMEPREP: Prohibited input U+'.sprintf('%08X', $v));
                    return false;
                }
            }

            if (0xAC00 <= $v && $v <= 0xD7AF) {
                // Hangul syllable decomposition
                foreach ($this->_hangul_decompose($v) as $out) {
                    $output[] = (int) $out;
                }
            } elseif (($this->_idn_version == '2003') && isset(self::$NP['replacemaps'][$v])) {
                // There's a decomposition mapping for that code point
                // Decompositions only in version 2003 (original) of IDNA
                foreach ($this->_apply_cannonical_ordering(self::$NP['replacemaps'][$v]) as $out) {
                    $output[] = (int) $out;
                }
            } else {
                $output[] = (int) $v;
            }
        }
        // Before applying any Combining, try to rearrange any Hangul syllables
        $output = $this->_hangul_compose($output);
        //
        // Combine code points
        //
        $last_class = 0;
        $last_starter = 0;
        $out_len = count($output);
        for ($i = 0; $i < $out_len; ++$i) {
            $class = $this->_get_combining_class($output[$i]);
            if ((!$last_class || $last_class > $class) && $class) {
                // Try to match
                $seq_len = $i - $last_starter;
                $out = $this->_combine(array_slice($output, $last_starter, $seq_len));
                // On match: Replace the last starter with the composed character and remove
                // the now redundant non-starter(s)
                if ($out) {
                    $output[$last_starter] = $out;
                    if (count($out) != $seq_len) {
                        for ($j = $i+1; $j < $out_len; ++$j) $output[$j-1] = $output[$j];
                        unset($output[$out_len]);
                    }
                    // Rewind the for loop by one, since there can be more possible compositions
                    $i--;
                    $out_len--;
                    $last_class = ($i == $last_starter) ? 0 : $this->_get_combining_class($output[$i-1]);
                    continue;
                }
            }
            // The current class is 0
            if (!$class) $last_starter = $i;
            $last_class = $class;
        }
        return $output;
    }

    /**
     * Decomposes a Hangul syllable
     * (see http://www.unicode.org/unicode/reports/tr15/#Hangul
     * @param    integer  32bit UCS4 code point
     * @return   array    Either Hangul Syllable decomposed or original 32bit value as one value array
     */
    protected function _hangul_decompose($char)
    {
        $sindex = (int) $char - $this->_sbase;
        if ($sindex < 0 || $sindex >= $this->_scount) return array($char);
        $result = array();
        $result[] = (int) $this->_lbase + $sindex / $this->_ncount;
        $result[] = (int) $this->_vbase + ($sindex % $this->_ncount) / $this->_tcount;
        $T = intval($this->_tbase + $sindex % $this->_tcount);
        if ($T != $this->_tbase) $result[] = $T;
        return $result;
    }
    /**
     * Ccomposes a Hangul syllable
     * (see http://www.unicode.org/unicode/reports/tr15/#Hangul
     * @param    array    Decomposed UCS4 sequence
     * @return   array    UCS4 sequence with syllables composed
     */
    protected function _hangul_compose($input)
    {
        $inp_len = count($input);
        if (!$inp_len) return array();
        $result = array();
        $last = (int) $input[0];
        $result[] = $last; // copy first char from input to output

        for ($i = 1; $i < $inp_len; ++$i) {
            $char = (int) $input[$i];
            $sindex = $last - $this->_sbase;
            $lindex = $last - $this->_lbase;
            $vindex = $char - $this->_vbase;
            $tindex = $char - $this->_tbase;
            // Find out, whether two current characters are LV and T
            if (0 <= $sindex && $sindex < $this->_scount && ($sindex % $this->_tcount == 0)
                    && 0 <= $tindex && $tindex <= $this->_tcount) {
                // create syllable of form LVT
                $last += $tindex;
                $result[(count($result) - 1)] = $last; // reset last
                continue; // discard char
            }
            // Find out, whether two current characters form L and V
            if (0 <= $lindex && $lindex < $this->_lcount && 0 <= $vindex && $vindex < $this->_vcount) {
                // create syllable of form LV
                $last = (int) $this->_sbase + ($lindex * $this->_vcount + $vindex) * $this->_tcount;
                $result[(count($result) - 1)] = $last; // reset last
                continue; // discard char
            }
            // if neither case was true, just add the character
            $last = $char;
            $result[] = $char;
        }
        return $result;
    }

    /**
     * Returns the combining class of a certain wide char
     * @param    integer    Wide char to check (32bit integer)
     * @return   integer    Combining class if found, else 0
     */
    protected function _get_combining_class($char)
    {
        return isset(self::$NP['norm_combcls'][$char]) ? self::$NP['norm_combcls'][$char] : 0;
    }

    /**
     * Applies the cannonical ordering of a decomposed UCS4 sequence
     * @param    array      Decomposed UCS4 sequence
     * @return   array      Ordered USC4 sequence
     */
    protected function _apply_cannonical_ordering($input)
    {
        $swap = true;
        $size = count($input);
        while ($swap) {
            $swap = false;
            $last = $this->_get_combining_class(intval($input[0]));
            for ($i = 0; $i < $size-1; ++$i) {
                $next = $this->_get_combining_class(intval($input[$i+1]));
                if ($next != 0 && $last > $next) {
                    // Move item leftward until it fits
                    for ($j = $i + 1; $j > 0; --$j) {
                        if ($this->_get_combining_class(intval($input[$j-1])) <= $next) break;
                        $t = intval($input[$j]);
                        $input[$j] = intval($input[$j-1]);
                        $input[$j-1] = $t;
                        $swap = true;
                    }
                    // Reentering the loop looking at the old character again
                    $next = $last;
                }
                $last = $next;
            }
        }
        return $input;
    }

    /**
     * Do composition of a sequence of starter and non-starter
     * @param    array      UCS4 Decomposed sequence
     * @return   array      Ordered USC4 sequence
     */
    protected function _combine($input)
    {
        $inp_len = count($input);
        foreach (self::$NP['replacemaps'] as $np_src => $np_target) {
            if ($np_target[0] != $input[0]) continue;
            if (count($np_target) != $inp_len) continue;
            $hit = false;
            foreach ($input as $k2 => $v2) {
                if ($v2 == $np_target[$k2]) {
                    $hit = true;
                } else {
                    $hit = false;
                    break;
                }
            }
            if ($hit) return $np_src;
        }
        return false;
    }

    /**
     * This converts an UTF-8 encoded string to its UCS-4 representation
     * By talking about UCS-4 "strings" we mean arrays of 32bit integers representing
     * each of the "chars". This is due to PHP not being able to handle strings with
     * bit depth different from 8. This apllies to the reverse method _ucs4_to_utf8(), too.
     * The following UTF-8 encodings are supported:
     * bytes bits  representation
     * 1        7  0xxxxxxx
     * 2       11  110xxxxx 10xxxxxx
     * 3       16  1110xxxx 10xxxxxx 10xxxxxx
     * 4       21  11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
     * 5       26  111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
     * 6       31  1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
     * Each x represents a bit that can be used to store character data.
     * The five and six byte sequences are part of Annex D of ISO/IEC 10646-1:2000
     * @param string $input
     * @return string
     */
    protected function _utf8_to_ucs4($input)
    {
        $output = array();
        $out_len = 0;
        $inp_len = self::byteLength($input);
        $mode = 'next';
        $test = 'none';
        for ($k = 0; $k < $inp_len; ++$k) {
            $v = ord($input{$k}); // Extract byte from input string
            if ($v < 128) { // We found an ASCII char - put into stirng as is
                $output[$out_len] = $v;
                ++$out_len;
                if ('add' == $mode) {
                    $this->_error('Conversion from UTF-8 to UCS-4 failed: malformed input at byte '.$k);
                    return false;
                }
                continue;
            }
            if ('next' == $mode) { // Try to find the next start byte; determine the width of the Unicode char
                $start_byte = $v;
                $mode = 'add';
                $test = 'range';
                if ($v >> 5 == 6) { // &110xxxxx 10xxxxx
                    $next_byte = 0; // Tells, how many times subsequent bitmasks must rotate 6bits to the left
                    $v = ($v - 192) << 6;
                } elseif ($v >> 4 == 14) { // &1110xxxx 10xxxxxx 10xxxxxx
                    $next_byte = 1;
                    $v = ($v - 224) << 12;
                } elseif ($v >> 3 == 30) { // &11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
                    $next_byte = 2;
                    $v = ($v - 240) << 18;
                } elseif ($v >> 2 == 62) { // &111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
                    $next_byte = 3;
                    $v = ($v - 248) << 24;
                } elseif ($v >> 1 == 126) { // &1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
                    $next_byte = 4;
                    $v = ($v - 252) << 30;
                } else {
                    $this->_error('This might be UTF-8, but I don\'t understand it at byte '.$k);
                    return false;
                }
                if ('add' == $mode) {
                    $output[$out_len] = (int) $v;
                    ++$out_len;
                    continue;
                }
            }
            if ('add' == $mode) {
                if (!$this->_allow_overlong && $test == 'range') {
                    $test = 'none';
                    if (($v < 0xA0 && $start_byte == 0xE0) || ($v < 0x90 && $start_byte == 0xF0) || ($v > 0x8F && $start_byte == 0xF4)) {
                        $this->_error('Bogus UTF-8 character detected (out of legal range) at byte '.$k);
                        return false;
                    }
                }
                if ($v >> 6 == 2) { // Bit mask must be 10xxxxxx
                    $v = ($v - 128) << ($next_byte * 6);
                    $output[($out_len - 1)] += $v;
                    --$next_byte;
                } else {
                    $this->_error('Conversion from UTF-8 to UCS-4 failed: malformed input at byte '.$k);
                    return false;
                }
                if ($next_byte < 0) {
                    $mode = 'next';
                }
            }
        } // for
        return $output;
    }

    /**
     * Convert UCS-4 string into UTF-8 string
     * See _utf8_to_ucs4() for details
     * @param string  $input
     * @return string
     */
    protected function _ucs4_to_utf8($input)
    {
        $output = '';
        foreach ($input as $k => $v) {
            if ($v < 128) { // 7bit are transferred literally
                $output .= chr($v);
            } elseif ($v < (1 << 11)) { // 2 bytes
                $output .= chr(192+($v >> 6)).chr(128+($v & 63));
            } elseif ($v < (1 << 16)) { // 3 bytes
                $output .= chr(224+($v >> 12)).chr(128+(($v >> 6) & 63)).chr(128+($v & 63));
            } elseif ($v < (1 << 21)) { // 4 bytes
                $output .= chr(240+($v >> 18)).chr(128+(($v >> 12) & 63)).chr(128+(($v >> 6) & 63)).chr(128+($v & 63));
            } elseif (self::$safe_mode) {
                $output .= self::$safe_char;
            } else {
                $this->_error('Conversion from UCS-4 to UTF-8 failed: malformed input at byte '.$k);
                return false;
            }
        }
        return $output;
    }

    /**
     * Convert UCS-4 array into UCS-4 string
     *
     * @param array $input
     * @return string
     */
    protected function _ucs4_to_ucs4_string($input)
    {
        $output = '';
        // Take array values and split output to 4 bytes per value
        // The bit mask is 255, which reads &11111111
        foreach ($input as $v) {
            $output .= chr(($v >> 24) & 255).chr(($v >> 16) & 255).chr(($v >> 8) & 255).chr($v & 255);
        }
        return $output;
    }

    /**
     * Convert UCS-4 strin into UCS-4 garray
     *
     * @param  string $input
     * @return array
     */
    protected function _ucs4_string_to_ucs4($input)
    {
        $output = array();
        $inp_len = self::byteLength($input);
        // Input length must be dividable by 4
        if ($inp_len % 4) {
            $this->_error('Input UCS4 string is broken');
            return false;
        }
        // Empty input - return empty output
        if (!$inp_len) return $output;
        for ($i = 0, $out_len = -1; $i < $inp_len; ++$i) {
            // Increment output position every 4 input bytes
            if (!($i % 4)) {
                $out_len++;
                $output[$out_len] = 0;
            }
            $output[$out_len] += ord($input{$i}) << (8 * (3 - ($i % 4) ) );
        }
        return $output;
    }

    /**
     * Gets the length of a string in bytes even if mbstring function
     * overloading is turned on
     *
     * @param string $string the string for which to get the length.
     * @return integer the length of the string in bytes.
     */
    protected static function byteLength($string)
    {
        if (self::$_mb_string_overload) {
            return mb_strlen($string, '8bit');
        }
        return strlen((binary) $string);
    }

    /**
     * Attempts to return a concrete IDNA instance.
     *
     * @param array $params Set of paramaters
     * @return idna_convert
     * @access public
     */
    public function getInstance($params = array())
    {
        return new idna_convert($params);
    }

    /**
     * Attempts to return a concrete IDNA instance for either php4 or php5,
     * only creating a new instance if no IDNA instance with the same
     * parameters currently exists.
     *
     * @param array $params Set of paramaters
     *
     * @return object idna_convert
     * @access public
     */
    public function singleton($params = array())
    {
        static $instances;
        if (!isset($instances)) {
            $instances = array();
        }
        $signature = serialize($params);
        if (!isset($instances[$signature])) {
            $instances[$signature] = idna_convert::getInstance($params);
        }
        return $instances[$signature];
    }

    /**
     * Holds all relevant mapping tables
     * See RFC3454 for details
     *
     * @private array
     * @since 0.5.2
     */
    protected static $NP = array
            ('map_nothing' => array(0xAD, 0x34F, 0x1806, 0x180B, 0x180C, 0x180D, 0x200B, 0x200C
                    ,0x200D, 0x2060, 0xFE00, 0xFE01, 0xFE02, 0xFE03, 0xFE04, 0xFE05, 0xFE06, 0xFE07
                    ,0xFE08, 0xFE09, 0xFE0A, 0xFE0B, 0xFE0C, 0xFE0D, 0xFE0E, 0xFE0F, 0xFEFF
                    )
            ,'general_prohibited' => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19
                    ,20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32 ,33, 34, 35, 36, 37, 38, 39, 40, 41, 42
                    ,43, 44, 47, 59, 60, 61, 62, 63, 64, 91, 92, 93, 94, 95, 96, 123, 124, 125, 126, 127, 0x3002
                    )
            ,'prohibit' => array(0xA0, 0x340, 0x341, 0x6DD, 0x70F, 0x1680, 0x180E, 0x2000, 0x2001, 0x2002, 0x2003
                    ,0x2004, 0x2005, 0x2006, 0x2007, 0x2008, 0x2009, 0x200A, 0x200B, 0x200C, 0x200D, 0x200E, 0x200F
                    ,0x2028, 0x2029, 0x202A, 0x202B, 0x202C, 0x202D, 0x202E, 0x202F, 0x205F, 0x206A, 0x206B, 0x206C
                    ,0x206D, 0x206E, 0x206F, 0x3000, 0xFEFF, 0xFFF9, 0xFFFA, 0xFFFB, 0xFFFC, 0xFFFD, 0xFFFE, 0xFFFF
                    ,0x1FFFE, 0x1FFFF, 0x2FFFE, 0x2FFFF, 0x3FFFE, 0x3FFFF, 0x4FFFE, 0x4FFFF, 0x5FFFE, 0x5FFFF, 0x6FFFE
                    ,0x6FFFF, 0x7FFFE, 0x7FFFF, 0x8FFFE, 0x8FFFF, 0x9FFFE, 0x9FFFF, 0xAFFFE, 0xAFFFF, 0xBFFFE, 0xBFFFF
                    ,0xCFFFE, 0xCFFFF, 0xDFFFE, 0xDFFFF, 0xE0001, 0xEFFFE, 0xEFFFF, 0xFFFFE, 0xFFFFF, 0x10FFFE, 0x10FFFF
                    )
            ,'prohibit_ranges' => array(array(0x80, 0x9F), array(0x2060, 0x206F), array(0x1D173, 0x1D17A)
                    ,array(0xE000, 0xF8FF) ,array(0xF0000, 0xFFFFD), array(0x100000, 0x10FFFD)
                    ,array(0xFDD0, 0xFDEF), array(0xD800, 0xDFFF), array(0x2FF0, 0x2FFB), array(0xE0020, 0xE007F)
                    )
            ,'replacemaps' => array(0x41 => array(0x61), 0x42 => array(0x62), 0x43 => array(0x63)
                    ,0x44 => array(0x64), 0x45 => array(0x65), 0x46 => array(0x66), 0x47 => array(0x67)
                    ,0x48 => array(0x68), 0x49 => array(0x69), 0x4A => array(0x6A), 0x4B => array(0x6B)
                    ,0x4C => array(0x6C), 0x4D => array(0x6D), 0x4E => array(0x6E), 0x4F => array(0x6F)
                    ,0x50 => array(0x70), 0x51 => array(0x71), 0x52 => array(0x72), 0x53 => array(0x73)
                    ,0x54 => array(0x74), 0x55 => array(0x75), 0x56 => array(0x76), 0x57 => array(0x77)
                    ,0x58 => array(0x78), 0x59 => array(0x79), 0x5A => array(0x7A), 0xB5 => array(0x3BC)
                    ,0xC0 => array(0xE0), 0xC1 => array(0xE1), 0xC2 => array(0xE2), 0xC3 => array(0xE3)
                    ,0xC4 => array(0xE4), 0xC5 => array(0xE5), 0xC6 => array(0xE6), 0xC7 => array(0xE7)
                    ,0xC8 => array(0xE8), 0xC9 => array(0xE9), 0xCA => array(0xEA), 0xCB => array(0xEB)
                    ,0xCC => array(0xEC), 0xCD => array(0xED), 0xCE => array(0xEE), 0xCF => array(0xEF)
                    ,0xD0 => array(0xF0), 0xD1 => array(0xF1), 0xD2 => array(0xF2), 0xD3 => array(0xF3)
                    ,0xD4 => array(0xF4), 0xD5 => array(0xF5), 0xD6 => array(0xF6), 0xD8 => array(0xF8)
                    ,0xD9 => array(0xF9), 0xDA => array(0xFA), 0xDB => array(0xFB), 0xDC => array(0xFC)
                    ,0xDD => array(0xFD), 0xDE => array(0xFE), 0xDF => array(0x73, 0x73)
                    ,0x100 => array(0x101), 0x102 => array(0x103), 0x104 => array(0x105)
                    ,0x106 => array(0x107), 0x108 => array(0x109), 0x10A => array(0x10B)
                    ,0x10C => array(0x10D), 0x10E => array(0x10F), 0x110 => array(0x111)
                    ,0x112 => array(0x113), 0x114 => array(0x115), 0x116 => array(0x117)
                    ,0x118 => array(0x119), 0x11A => array(0x11B), 0x11C => array(0x11D)
                    ,0x11E => array(0x11F), 0x120 => array(0x121), 0x122 => array(0x123)
                    ,0x124 => array(0x125), 0x126 => array(0x127), 0x128 => array(0x129)
                    ,0x12A => array(0x12B), 0x12C => array(0x12D), 0x12E => array(0x12F)
                    ,0x130 => array(0x69, 0x307), 0x132 => array(0x133), 0x134 => array(0x135)
                    ,0x136 => array(0x137), 0x139 => array(0x13A), 0x13B => array(0x13C)
                    ,0x13D => array(0x13E), 0x13F => array(0x140), 0x141 => array(0x142)
                    ,0x143 => array(0x144), 0x145 => array(0x146), 0x147 => array(0x148)
                    ,0x149 => array(0x2BC, 0x6E), 0x14A => array(0x14B), 0x14C => array(0x14D)
                    ,0x14E => array(0x14F), 0x150 => array(0x151), 0x152 => array(0x153)
                    ,0x154 => array(0x155), 0x156 => array(0x157), 0x158 => array(0x159)
                    ,0x15A => array(0x15B), 0x15C => array(0x15D), 0x15E => array(0x15F)
                    ,0x160 => array(0x161), 0x162 => array(0x163), 0x164 => array(0x165)
                    ,0x166 => array(0x167), 0x168 => array(0x169), 0x16A => array(0x16B)
                    ,0x16C => array(0x16D), 0x16E => array(0x16F), 0x170 => array(0x171)
                    ,0x172 => array(0x173), 0x174 => array(0x175), 0x176 => array(0x177)
                    ,0x178 => array(0xFF), 0x179 => array(0x17A), 0x17B => array(0x17C)
                    ,0x17D => array(0x17E), 0x17F => array(0x73), 0x181 => array(0x253)
                    ,0x182 => array(0x183), 0x184 => array(0x185), 0x186 => array(0x254)
                    ,0x187 => array(0x188), 0x189 => array(0x256), 0x18A => array(0x257)
                    ,0x18B => array(0x18C), 0x18E => array(0x1DD), 0x18F => array(0x259)
                    ,0x190 => array(0x25B), 0x191 => array(0x192), 0x193 => array(0x260)
                    ,0x194 => array(0x263), 0x196 => array(0x269), 0x197 => array(0x268)
                    ,0x198 => array(0x199), 0x19C => array(0x26F), 0x19D => array(0x272)
                    ,0x19F => array(0x275), 0x1A0 => array(0x1A1), 0x1A2 => array(0x1A3)
                    ,0x1A4 => array(0x1A5), 0x1A6 => array(0x280), 0x1A7 => array(0x1A8)
                    ,0x1A9 => array(0x283), 0x1AC => array(0x1AD), 0x1AE => array(0x288)
                    ,0x1AF => array(0x1B0), 0x1B1 => array(0x28A), 0x1B2 => array(0x28B)
                    ,0x1B3 => array(0x1B4), 0x1B5 => array(0x1B6), 0x1B7 => array(0x292)
                    ,0x1B8 => array(0x1B9), 0x1BC => array(0x1BD), 0x1C4 => array(0x1C6)
                    ,0x1C5 => array(0x1C6), 0x1C7 => array(0x1C9), 0x1C8 => array(0x1C9)
                    ,0x1CA => array(0x1CC), 0x1CB => array(0x1CC), 0x1CD => array(0x1CE)
                    ,0x1CF => array(0x1D0), 0x1D1   => array(0x1D2), 0x1D3   => array(0x1D4)
                    ,0x1D5   => array(0x1D6), 0x1D7   => array(0x1D8), 0x1D9   => array(0x1DA)
                    ,0x1DB   => array(0x1DC), 0x1DE   => array(0x1DF), 0x1E0   => array(0x1E1)
                    ,0x1E2   => array(0x1E3), 0x1E4   => array(0x1E5), 0x1E6   => array(0x1E7)
                    ,0x1E8   => array(0x1E9), 0x1EA   => array(0x1EB), 0x1EC   => array(0x1ED)
                    ,0x1EE   => array(0x1EF), 0x1F0   => array(0x6A, 0x30C), 0x1F1   => array(0x1F3)
                    ,0x1F2   => array(0x1F3), 0x1F4   => array(0x1F5), 0x1F6   => array(0x195)
                    ,0x1F7   => array(0x1BF), 0x1F8   => array(0x1F9), 0x1FA   => array(0x1FB)
                    ,0x1FC   => array(0x1FD), 0x1FE   => array(0x1FF), 0x200   => array(0x201)
                    ,0x202   => array(0x203), 0x204   => array(0x205), 0x206   => array(0x207)
                    ,0x208   => array(0x209), 0x20A   => array(0x20B), 0x20C   => array(0x20D)
                    ,0x20E   => array(0x20F), 0x210   => array(0x211), 0x212   => array(0x213)
                    ,0x214   => array(0x215), 0x216   => array(0x217), 0x218   => array(0x219)
                    ,0x21A   => array(0x21B), 0x21C   => array(0x21D), 0x21E   => array(0x21F)
                    ,0x220   => array(0x19E), 0x222   => array(0x223), 0x224   => array(0x225)
                    ,0x226   => array(0x227), 0x228   => array(0x229), 0x22A   => array(0x22B)
                    ,0x22C   => array(0x22D), 0x22E   => array(0x22F), 0x230   => array(0x231)
                    ,0x232   => array(0x233), 0x345   => array(0x3B9), 0x37A   => array(0x20, 0x3B9)
                    ,0x386   => array(0x3AC), 0x388   => array(0x3AD), 0x389   => array(0x3AE)
                    ,0x38A   => array(0x3AF), 0x38C   => array(0x3CC), 0x38E   => array(0x3CD)
                    ,0x38F   => array(0x3CE), 0x390   => array(0x3B9, 0x308, 0x301)
                    ,0x391   => array(0x3B1), 0x392   => array(0x3B2), 0x393   => array(0x3B3)
                    ,0x394   => array(0x3B4), 0x395   => array(0x3B5), 0x396   => array(0x3B6)
                    ,0x397   => array(0x3B7), 0x398   => array(0x3B8), 0x399   => array(0x3B9)
                    ,0x39A   => array(0x3BA), 0x39B   => array(0x3BB), 0x39C   => array(0x3BC)
                    ,0x39D   => array(0x3BD), 0x39E   => array(0x3BE), 0x39F   => array(0x3BF)
                    ,0x3A0   => array(0x3C0), 0x3A1   => array(0x3C1), 0x3A3   => array(0x3C3)
                    ,0x3A4   => array(0x3C4), 0x3A5   => array(0x3C5), 0x3A6   => array(0x3C6)
                    ,0x3A7   => array(0x3C7), 0x3A8   => array(0x3C8), 0x3A9   => array(0x3C9)
                    ,0x3AA   => array(0x3CA), 0x3AB   => array(0x3CB), 0x3B0   => array(0x3C5, 0x308, 0x301)
                    ,0x3C2   => array(0x3C3), 0x3D0   => array(0x3B2), 0x3D1   => array(0x3B8)
                    ,0x3D2   => array(0x3C5), 0x3D3   => array(0x3CD), 0x3D4   => array(0x3CB)
                    ,0x3D5   => array(0x3C6), 0x3D6   => array(0x3C0), 0x3D8   => array(0x3D9)
                    ,0x3DA   => array(0x3DB), 0x3DC   => array(0x3DD), 0x3DE   => array(0x3DF)
                    ,0x3E0   => array(0x3E1), 0x3E2   => array(0x3E3), 0x3E4   => array(0x3E5)
                    ,0x3E6   => array(0x3E7), 0x3E8   => array(0x3E9), 0x3EA   => array(0x3EB)
                    ,0x3EC   => array(0x3ED), 0x3EE   => array(0x3EF), 0x3F0   => array(0x3BA)
                    ,0x3F1   => array(0x3C1), 0x3F2   => array(0x3C3), 0x3F4   => array(0x3B8)
                    ,0x3F5   => array(0x3B5), 0x400   => array(0x450), 0x401   => array(0x451)
                    ,0x402   => array(0x452), 0x403   => array(0x453), 0x404   => array(0x454)
                    ,0x405   => array(0x455), 0x406   => array(0x456), 0x407   => array(0x457)
                    ,0x408   => array(0x458), 0x409   => array(0x459), 0x40A   => array(0x45A)
                    ,0x40B   => array(0x45B), 0x40C   => array(0x45C), 0x40D   => array(0x45D)
                    ,0x40E   => array(0x45E), 0x40F   => array(0x45F), 0x410   => array(0x430)
                    ,0x411   => array(0x431), 0x412   => array(0x432), 0x413   => array(0x433)
                    ,0x414   => array(0x434), 0x415   => array(0x435), 0x416   => array(0x436)
                    ,0x417   => array(0x437), 0x418   => array(0x438), 0x419   => array(0x439)
                    ,0x41A   => array(0x43A), 0x41B   => array(0x43B), 0x41C   => array(0x43C)
                    ,0x41D   => array(0x43D), 0x41E   => array(0x43E), 0x41F   => array(0x43F)
                    ,0x420   => array(0x440), 0x421   => array(0x441), 0x422   => array(0x442)
                    ,0x423   => array(0x443), 0x424   => array(0x444), 0x425   => array(0x445)
                    ,0x426   => array(0x446), 0x427   => array(0x447), 0x428   => array(0x448)
                    ,0x429   => array(0x449), 0x42A   => array(0x44A), 0x42B   => array(0x44B)
                    ,0x42C   => array(0x44C), 0x42D   => array(0x44D), 0x42E   => array(0x44E)
                    ,0x42F   => array(0x44F), 0x460   => array(0x461), 0x462   => array(0x463)
                    ,0x464   => array(0x465), 0x466   => array(0x467), 0x468   => array(0x469)
                    ,0x46A   => array(0x46B), 0x46C   => array(0x46D), 0x46E   => array(0x46F)
                    ,0x470   => array(0x471), 0x472   => array(0x473), 0x474   => array(0x475)
                    ,0x476   => array(0x477), 0x478   => array(0x479), 0x47A   => array(0x47B)
                    ,0x47C   => array(0x47D), 0x47E   => array(0x47F), 0x480   => array(0x481)
                    ,0x48A   => array(0x48B), 0x48C   => array(0x48D), 0x48E   => array(0x48F)
                    ,0x490   => array(0x491), 0x492   => array(0x493), 0x494   => array(0x495)
                    ,0x496   => array(0x497), 0x498   => array(0x499), 0x49A   => array(0x49B)
                    ,0x49C   => array(0x49D), 0x49E   => array(0x49F), 0x4A0   => array(0x4A1)
                    ,0x4A2   => array(0x4A3), 0x4A4   => array(0x4A5), 0x4A6   => array(0x4A7)
                    ,0x4A8   => array(0x4A9), 0x4AA   => array(0x4AB), 0x4AC   => array(0x4AD)
                    ,0x4AE   => array(0x4AF), 0x4B0   => array(0x4B1), 0x4B2   => array(0x4B3)
                    ,0x4B4   => array(0x4B5), 0x4B6   => array(0x4B7), 0x4B8   => array(0x4B9)
                    ,0x4BA   => array(0x4BB), 0x4BC   => array(0x4BD), 0x4BE   => array(0x4BF)
                    ,0x4C1   => array(0x4C2), 0x4C3   => array(0x4C4), 0x4C5   => array(0x4C6)
                    ,0x4C7   => array(0x4C8), 0x4C9   => array(0x4CA), 0x4CB   => array(0x4CC)
                    ,0x4CD   => array(0x4CE), 0x4D0   => array(0x4D1), 0x4D2   => array(0x4D3)
                    ,0x4D4   => array(0x4D5), 0x4D6   => array(0x4D7), 0x4D8   => array(0x4D9)
                    ,0x4DA   => array(0x4DB), 0x4DC   => array(0x4DD), 0x4DE   => array(0x4DF)
                    ,0x4E0   => array(0x4E1), 0x4E2   => array(0x4E3), 0x4E4   => array(0x4E5)
                    ,0x4E6   => array(0x4E7), 0x4E8   => array(0x4E9), 0x4EA   => array(0x4EB)
                    ,0x4EC   => array(0x4ED), 0x4EE   => array(0x4EF), 0x4F0   => array(0x4F1)
                    ,0x4F2   => array(0x4F3), 0x4F4   => array(0x4F5), 0x4F8   => array(0x4F9)
                    ,0x500   => array(0x501), 0x502   => array(0x503), 0x504   => array(0x505)
                    ,0x506   => array(0x507), 0x508   => array(0x509), 0x50A   => array(0x50B)
                    ,0x50C   => array(0x50D), 0x50E   => array(0x50F), 0x531   => array(0x561)
                    ,0x532   => array(0x562), 0x533   => array(0x563), 0x534   => array(0x564)
                    ,0x535   => array(0x565), 0x536   => array(0x566), 0x537   => array(0x567)
                    ,0x538   => array(0x568), 0x539   => array(0x569), 0x53A   => array(0x56A)
                    ,0x53B   => array(0x56B), 0x53C   => array(0x56C), 0x53D   => array(0x56D)
                    ,0x53E   => array(0x56E), 0x53F   => array(0x56F), 0x540   => array(0x570)
                    ,0x541   => array(0x571), 0x542   => array(0x572), 0x543   => array(0x573)
                    ,0x544   => array(0x574), 0x545   => array(0x575), 0x546   => array(0x576)
                    ,0x547   => array(0x577), 0x548   => array(0x578), 0x549   => array(0x579)
                    ,0x54A   => array(0x57A), 0x54B   => array(0x57B), 0x54C   => array(0x57C)
                    ,0x54D   => array(0x57D), 0x54E   => array(0x57E), 0x54F   => array(0x57F)
                    ,0x550   => array(0x580), 0x551   => array(0x581), 0x552   => array(0x582)
                    ,0x553   => array(0x583), 0x554   => array(0x584), 0x555   => array(0x585)
                    ,0x556 => array(0x586), 0x587 => array(0x565, 0x582), 0xE33 => array(0xE4D, 0xE32)
                    ,0x1E00  => array(0x1E01), 0x1E02  => array(0x1E03), 0x1E04  => array(0x1E05)
                    ,0x1E06  => array(0x1E07), 0x1E08  => array(0x1E09), 0x1E0A  => array(0x1E0B)
                    ,0x1E0C  => array(0x1E0D), 0x1E0E  => array(0x1E0F), 0x1E10  => array(0x1E11)
                    ,0x1E12  => array(0x1E13), 0x1E14  => array(0x1E15), 0x1E16  => array(0x1E17)
                    ,0x1E18  => array(0x1E19), 0x1E1A  => array(0x1E1B), 0x1E1C  => array(0x1E1D)
                    ,0x1E1E  => array(0x1E1F), 0x1E20  => array(0x1E21), 0x1E22  => array(0x1E23)
                    ,0x1E24  => array(0x1E25), 0x1E26  => array(0x1E27), 0x1E28  => array(0x1E29)
                    ,0x1E2A  => array(0x1E2B), 0x1E2C  => array(0x1E2D), 0x1E2E  => array(0x1E2F)
                    ,0x1E30  => array(0x1E31), 0x1E32  => array(0x1E33), 0x1E34  => array(0x1E35)
                    ,0x1E36  => array(0x1E37), 0x1E38  => array(0x1E39), 0x1E3A  => array(0x1E3B)
                    ,0x1E3C  => array(0x1E3D), 0x1E3E  => array(0x1E3F), 0x1E40  => array(0x1E41)
                    ,0x1E42  => array(0x1E43), 0x1E44  => array(0x1E45), 0x1E46  => array(0x1E47)
                    ,0x1E48  => array(0x1E49), 0x1E4A  => array(0x1E4B), 0x1E4C  => array(0x1E4D)
                    ,0x1E4E  => array(0x1E4F), 0x1E50  => array(0x1E51), 0x1E52  => array(0x1E53)
                    ,0x1E54  => array(0x1E55), 0x1E56  => array(0x1E57), 0x1E58  => array(0x1E59)
                    ,0x1E5A  => array(0x1E5B), 0x1E5C  => array(0x1E5D), 0x1E5E  => array(0x1E5F)
                    ,0x1E60  => array(0x1E61), 0x1E62  => array(0x1E63), 0x1E64  => array(0x1E65)
                    ,0x1E66  => array(0x1E67), 0x1E68  => array(0x1E69), 0x1E6A  => array(0x1E6B)
                    ,0x1E6C  => array(0x1E6D), 0x1E6E  => array(0x1E6F), 0x1E70  => array(0x1E71)
                    ,0x1E72  => array(0x1E73), 0x1E74  => array(0x1E75), 0x1E76  => array(0x1E77)
                    ,0x1E78  => array(0x1E79), 0x1E7A  => array(0x1E7B), 0x1E7C  => array(0x1E7D)
                    ,0x1E7E  => array(0x1E7F), 0x1E80  => array(0x1E81), 0x1E82  => array(0x1E83)
                    ,0x1E84  => array(0x1E85), 0x1E86  => array(0x1E87), 0x1E88  => array(0x1E89)
                    ,0x1E8A  => array(0x1E8B), 0x1E8C  => array(0x1E8D), 0x1E8E  => array(0x1E8F)
                    ,0x1E90  => array(0x1E91), 0x1E92  => array(0x1E93), 0x1E94  => array(0x1E95)
                    ,0x1E96  => array(0x68, 0x331), 0x1E97  => array(0x74, 0x308), 0x1E98  => array(0x77, 0x30A)
                    ,0x1E99  => array(0x79, 0x30A), 0x1E9A  => array(0x61, 0x2BE), 0x1E9B  => array(0x1E61)
                    ,0x1EA0  => array(0x1EA1), 0x1EA2  => array(0x1EA3), 0x1EA4  => array(0x1EA5)
                    ,0x1EA6  => array(0x1EA7), 0x1EA8  => array(0x1EA9), 0x1EAA  => array(0x1EAB)
                    ,0x1EAC  => array(0x1EAD), 0x1EAE  => array(0x1EAF), 0x1EB0  => array(0x1EB1)
                    ,0x1EB2  => array(0x1EB3), 0x1EB4  => array(0x1EB5), 0x1EB6  => array(0x1EB7)
                    ,0x1EB8  => array(0x1EB9), 0x1EBA  => array(0x1EBB), 0x1EBC  => array(0x1EBD)
                    ,0x1EBE  => array(0x1EBF), 0x1EC0  => array(0x1EC1), 0x1EC2  => array(0x1EC3)
                    ,0x1EC4  => array(0x1EC5), 0x1EC6  => array(0x1EC7), 0x1EC8  => array(0x1EC9)
                    ,0x1ECA  => array(0x1ECB), 0x1ECC  => array(0x1ECD), 0x1ECE  => array(0x1ECF)
                    ,0x1ED0  => array(0x1ED1), 0x1ED2  => array(0x1ED3), 0x1ED4  => array(0x1ED5)
                    ,0x1ED6  => array(0x1ED7), 0x1ED8  => array(0x1ED9), 0x1EDA  => array(0x1EDB)
                    ,0x1EDC  => array(0x1EDD), 0x1EDE  => array(0x1EDF), 0x1EE0  => array(0x1EE1)
                    ,0x1EE2  => array(0x1EE3), 0x1EE4  => array(0x1EE5), 0x1EE6  => array(0x1EE7)
                    ,0x1EE8  => array(0x1EE9), 0x1EEA  => array(0x1EEB), 0x1EEC  => array(0x1EED)
                    ,0x1EEE  => array(0x1EEF), 0x1EF0  => array(0x1EF1), 0x1EF2  => array(0x1EF3)
                    ,0x1EF4  => array(0x1EF5), 0x1EF6  => array(0x1EF7), 0x1EF8  => array(0x1EF9)
                    ,0x1F08  => array(0x1F00), 0x1F09  => array(0x1F01), 0x1F0A  => array(0x1F02)
                    ,0x1F0B  => array(0x1F03), 0x1F0C  => array(0x1F04), 0x1F0D  => array(0x1F05)
                    ,0x1F0E  => array(0x1F06), 0x1F0F  => array(0x1F07), 0x1F18  => array(0x1F10)
                    ,0x1F19  => array(0x1F11), 0x1F1A  => array(0x1F12), 0x1F1B  => array(0x1F13)
                    ,0x1F1C  => array(0x1F14), 0x1F1D  => array(0x1F15), 0x1F28  => array(0x1F20)
                    ,0x1F29  => array(0x1F21), 0x1F2A  => array(0x1F22), 0x1F2B  => array(0x1F23)
                    ,0x1F2C  => array(0x1F24), 0x1F2D  => array(0x1F25), 0x1F2E  => array(0x1F26)
                    ,0x1F2F  => array(0x1F27), 0x1F38  => array(0x1F30), 0x1F39  => array(0x1F31)
                    ,0x1F3A  => array(0x1F32), 0x1F3B  => array(0x1F33), 0x1F3C  => array(0x1F34)
                    ,0x1F3D  => array(0x1F35), 0x1F3E  => array(0x1F36), 0x1F3F  => array(0x1F37)
                    ,0x1F48  => array(0x1F40), 0x1F49  => array(0x1F41), 0x1F4A  => array(0x1F42)
                    ,0x1F4B  => array(0x1F43), 0x1F4C  => array(0x1F44), 0x1F4D  => array(0x1F45)
                    ,0x1F50  => array(0x3C5, 0x313), 0x1F52  => array(0x3C5, 0x313, 0x300)
                    ,0x1F54  => array(0x3C5, 0x313, 0x301), 0x1F56  => array(0x3C5, 0x313, 0x342)
                    ,0x1F59  => array(0x1F51), 0x1F5B  => array(0x1F53), 0x1F5D  => array(0x1F55)
                    ,0x1F5F  => array(0x1F57), 0x1F68  => array(0x1F60), 0x1F69  => array(0x1F61)
                    ,0x1F6A  => array(0x1F62), 0x1F6B  => array(0x1F63), 0x1F6C  => array(0x1F64)
                    ,0x1F6D  => array(0x1F65), 0x1F6E  => array(0x1F66), 0x1F6F  => array(0x1F67)
                    ,0x1F80  => array(0x1F00, 0x3B9), 0x1F81  => array(0x1F01, 0x3B9)
                    ,0x1F82  => array(0x1F02, 0x3B9), 0x1F83  => array(0x1F03, 0x3B9)
                    ,0x1F84  => array(0x1F04, 0x3B9), 0x1F85  => array(0x1F05, 0x3B9)
                    ,0x1F86  => array(0x1F06, 0x3B9), 0x1F87  => array(0x1F07, 0x3B9)
                    ,0x1F88  => array(0x1F00, 0x3B9), 0x1F89  => array(0x1F01, 0x3B9)
                    ,0x1F8A  => array(0x1F02, 0x3B9), 0x1F8B  => array(0x1F03, 0x3B9)
                    ,0x1F8C  => array(0x1F04, 0x3B9), 0x1F8D  => array(0x1F05, 0x3B9)
                    ,0x1F8E  => array(0x1F06, 0x3B9), 0x1F8F  => array(0x1F07, 0x3B9)
                    ,0x1F90  => array(0x1F20, 0x3B9), 0x1F91  => array(0x1F21, 0x3B9)
                    ,0x1F92  => array(0x1F22, 0x3B9), 0x1F93  => array(0x1F23, 0x3B9)
                    ,0x1F94  => array(0x1F24, 0x3B9), 0x1F95  => array(0x1F25, 0x3B9)
                    ,0x1F96  => array(0x1F26, 0x3B9), 0x1F97  => array(0x1F27, 0x3B9)
                    ,0x1F98  => array(0x1F20, 0x3B9), 0x1F99  => array(0x1F21, 0x3B9)
                    ,0x1F9A  => array(0x1F22, 0x3B9), 0x1F9B  => array(0x1F23, 0x3B9)
                    ,0x1F9C  => array(0x1F24, 0x3B9), 0x1F9D  => array(0x1F25, 0x3B9)
                    ,0x1F9E  => array(0x1F26, 0x3B9), 0x1F9F  => array(0x1F27, 0x3B9)
                    ,0x1FA0  => array(0x1F60, 0x3B9), 0x1FA1  => array(0x1F61, 0x3B9)
                    ,0x1FA2  => array(0x1F62, 0x3B9), 0x1FA3  => array(0x1F63, 0x3B9)
                    ,0x1FA4  => array(0x1F64, 0x3B9), 0x1FA5  => array(0x1F65, 0x3B9)
                    ,0x1FA6  => array(0x1F66, 0x3B9), 0x1FA7  => array(0x1F67, 0x3B9)
                    ,0x1FA8  => array(0x1F60, 0x3B9), 0x1FA9  => array(0x1F61, 0x3B9)
                    ,0x1FAA  => array(0x1F62, 0x3B9), 0x1FAB  => array(0x1F63, 0x3B9)
                    ,0x1FAC  => array(0x1F64, 0x3B9), 0x1FAD  => array(0x1F65, 0x3B9)
                    ,0x1FAE  => array(0x1F66, 0x3B9), 0x1FAF  => array(0x1F67, 0x3B9)
                    ,0x1FB2  => array(0x1F70, 0x3B9), 0x1FB3  => array(0x3B1, 0x3B9)
                    ,0x1FB4  => array(0x3AC, 0x3B9), 0x1FB6  => array(0x3B1, 0x342)
                    ,0x1FB7  => array(0x3B1, 0x342, 0x3B9), 0x1FB8  => array(0x1FB0)
                    ,0x1FB9  => array(0x1FB1), 0x1FBA  => array(0x1F70), 0x1FBB  => array(0x1F71)
                    ,0x1FBC  => array(0x3B1, 0x3B9), 0x1FBE  => array(0x3B9)
                    ,0x1FC2  => array(0x1F74, 0x3B9), 0x1FC3  => array(0x3B7, 0x3B9)
                    ,0x1FC4  => array(0x3AE, 0x3B9), 0x1FC6  => array(0x3B7, 0x342)
                    ,0x1FC7  => array(0x3B7, 0x342, 0x3B9), 0x1FC8  => array(0x1F72)
                    ,0x1FC9  => array(0x1F73), 0x1FCA  => array(0x1F74), 0x1FCB  => array(0x1F75)
                    ,0x1FCC  => array(0x3B7, 0x3B9), 0x1FD2  => array(0x3B9, 0x308, 0x300)
                    ,0x1FD3  => array(0x3B9, 0x308, 0x301), 0x1FD6  => array(0x3B9, 0x342)
                    ,0x1FD7  => array(0x3B9, 0x308, 0x342), 0x1FD8  => array(0x1FD0)
                    ,0x1FD9  => array(0x1FD1), 0x1FDA  => array(0x1F76)
                    ,0x1FDB  => array(0x1F77), 0x1FE2  => array(0x3C5, 0x308, 0x300)
                    ,0x1FE3  => array(0x3C5, 0x308, 0x301), 0x1FE4  => array(0x3C1, 0x313)
                    ,0x1FE6  => array(0x3C5, 0x342), 0x1FE7  => array(0x3C5, 0x308, 0x342)
                    ,0x1FE8  => array(0x1FE0), 0x1FE9  => array(0x1FE1)
                    ,0x1FEA  => array(0x1F7A), 0x1FEB  => array(0x1F7B)
                    ,0x1FEC  => array(0x1FE5), 0x1FF2  => array(0x1F7C, 0x3B9)
                    ,0x1FF3  => array(0x3C9, 0x3B9), 0x1FF4  => array(0x3CE, 0x3B9)
                    ,0x1FF6  => array(0x3C9, 0x342), 0x1FF7  => array(0x3C9, 0x342, 0x3B9)
                    ,0x1FF8  => array(0x1F78), 0x1FF9  => array(0x1F79), 0x1FFA  => array(0x1F7C)
                    ,0x1FFB  => array(0x1F7D), 0x1FFC  => array(0x3C9, 0x3B9)
                    ,0x20A8  => array(0x72, 0x73), 0x2102  => array(0x63), 0x2103  => array(0xB0, 0x63)
                    ,0x2107  => array(0x25B), 0x2109  => array(0xB0, 0x66), 0x210B  => array(0x68)
                    ,0x210C  => array(0x68), 0x210D  => array(0x68), 0x2110  => array(0x69)
                    ,0x2111  => array(0x69), 0x2112  => array(0x6C), 0x2115  => array(0x6E)
                    ,0x2116  => array(0x6E, 0x6F), 0x2119  => array(0x70), 0x211A  => array(0x71)
                    ,0x211B  => array(0x72), 0x211C  => array(0x72), 0x211D  => array(0x72)
                    ,0x2120  => array(0x73, 0x6D), 0x2121  => array(0x74, 0x65, 0x6C)
                    ,0x2122  => array(0x74, 0x6D), 0x2124  => array(0x7A), 0x2126  => array(0x3C9)
                    ,0x2128  => array(0x7A), 0x212A  => array(0x6B), 0x212B  => array(0xE5)
                    ,0x212C  => array(0x62), 0x212D  => array(0x63), 0x2130  => array(0x65)
                    ,0x2131  => array(0x66), 0x2133  => array(0x6D), 0x213E  => array(0x3B3)
                    ,0x213F  => array(0x3C0), 0x2145  => array(0x64) ,0x2160  => array(0x2170)
                    ,0x2161  => array(0x2171), 0x2162  => array(0x2172), 0x2163  => array(0x2173)
                    ,0x2164  => array(0x2174), 0x2165  => array(0x2175), 0x2166  => array(0x2176)
                    ,0x2167  => array(0x2177), 0x2168  => array(0x2178), 0x2169  => array(0x2179)
                    ,0x216A  => array(0x217A), 0x216B  => array(0x217B), 0x216C  => array(0x217C)
                    ,0x216D  => array(0x217D), 0x216E  => array(0x217E), 0x216F  => array(0x217F)
                    ,0x24B6  => array(0x24D0), 0x24B7  => array(0x24D1), 0x24B8  => array(0x24D2)
                    ,0x24B9  => array(0x24D3), 0x24BA  => array(0x24D4), 0x24BB  => array(0x24D5)
                    ,0x24BC  => array(0x24D6), 0x24BD  => array(0x24D7), 0x24BE  => array(0x24D8)
                    ,0x24BF  => array(0x24D9), 0x24C0  => array(0x24DA), 0x24C1  => array(0x24DB)
                    ,0x24C2  => array(0x24DC), 0x24C3  => array(0x24DD), 0x24C4  => array(0x24DE)
                    ,0x24C5  => array(0x24DF), 0x24C6  => array(0x24E0), 0x24C7  => array(0x24E1)
                    ,0x24C8  => array(0x24E2), 0x24C9  => array(0x24E3), 0x24CA  => array(0x24E4)
                    ,0x24CB  => array(0x24E5), 0x24CC  => array(0x24E6), 0x24CD  => array(0x24E7)
                    ,0x24CE  => array(0x24E8), 0x24CF  => array(0x24E9), 0x3371  => array(0x68, 0x70, 0x61)
                    ,0x3373  => array(0x61, 0x75), 0x3375  => array(0x6F, 0x76)
                    ,0x3380  => array(0x70, 0x61), 0x3381  => array(0x6E, 0x61)
                    ,0x3382  => array(0x3BC, 0x61), 0x3383  => array(0x6D, 0x61)
                    ,0x3384  => array(0x6B, 0x61), 0x3385  => array(0x6B, 0x62)
                    ,0x3386  => array(0x6D, 0x62), 0x3387  => array(0x67, 0x62)
                    ,0x338A  => array(0x70, 0x66), 0x338B  => array(0x6E, 0x66)
                    ,0x338C  => array(0x3BC, 0x66), 0x3390  => array(0x68, 0x7A)
                    ,0x3391  => array(0x6B, 0x68, 0x7A), 0x3392  => array(0x6D, 0x68, 0x7A)
                    ,0x3393  => array(0x67, 0x68, 0x7A), 0x3394  => array(0x74, 0x68, 0x7A)
                    ,0x33A9  => array(0x70, 0x61), 0x33AA  => array(0x6B, 0x70, 0x61)
                    ,0x33AB  => array(0x6D, 0x70, 0x61), 0x33AC  => array(0x67, 0x70, 0x61)
                    ,0x33B4  => array(0x70, 0x76), 0x33B5  => array(0x6E, 0x76)
                    ,0x33B6  => array(0x3BC, 0x76), 0x33B7  => array(0x6D, 0x76)
                    ,0x33B8  => array(0x6B, 0x76), 0x33B9  => array(0x6D, 0x76)
                    ,0x33BA  => array(0x70, 0x77), 0x33BB  => array(0x6E, 0x77)
                    ,0x33BC  => array(0x3BC, 0x77), 0x33BD  => array(0x6D, 0x77)
                    ,0x33BE  => array(0x6B, 0x77), 0x33BF  => array(0x6D, 0x77)
                    ,0x33C0  => array(0x6B, 0x3C9), 0x33C1  => array(0x6D, 0x3C9) /*
                    ,0x33C2  => array(0x61, 0x2E, 0x6D, 0x2E) */
                    ,0x33C3  => array(0x62, 0x71), 0x33C6  => array(0x63, 0x2215, 0x6B, 0x67)
                    ,0x33C7  => array(0x63, 0x6F, 0x2E), 0x33C8  => array(0x64, 0x62)
                    ,0x33C9  => array(0x67, 0x79), 0x33CB  => array(0x68, 0x70)
                    ,0x33CD  => array(0x6B, 0x6B), 0x33CE  => array(0x6B, 0x6D)
                    ,0x33D7  => array(0x70, 0x68), 0x33D9  => array(0x70, 0x70, 0x6D)
                    ,0x33DA  => array(0x70, 0x72), 0x33DC  => array(0x73, 0x76)
                    ,0x33DD  => array(0x77, 0x62), 0xFB00  => array(0x66, 0x66)
                    ,0xFB01  => array(0x66, 0x69), 0xFB02  => array(0x66, 0x6C)
                    ,0xFB03  => array(0x66, 0x66, 0x69), 0xFB04  => array(0x66, 0x66, 0x6C)
                    ,0xFB05  => array(0x73, 0x74), 0xFB06  => array(0x73, 0x74)
                    ,0xFB13  => array(0x574, 0x576), 0xFB14  => array(0x574, 0x565)
                    ,0xFB15  => array(0x574, 0x56B), 0xFB16  => array(0x57E, 0x576)
                    ,0xFB17  => array(0x574, 0x56D), 0xFF21  => array(0xFF41)
                    ,0xFF22  => array(0xFF42), 0xFF23  => array(0xFF43), 0xFF24  => array(0xFF44)
                    ,0xFF25  => array(0xFF45), 0xFF26  => array(0xFF46), 0xFF27  => array(0xFF47)
                    ,0xFF28  => array(0xFF48), 0xFF29  => array(0xFF49), 0xFF2A  => array(0xFF4A)
                    ,0xFF2B  => array(0xFF4B), 0xFF2C  => array(0xFF4C), 0xFF2D  => array(0xFF4D)
                    ,0xFF2E  => array(0xFF4E), 0xFF2F  => array(0xFF4F), 0xFF30  => array(0xFF50)
                    ,0xFF31  => array(0xFF51), 0xFF32  => array(0xFF52), 0xFF33  => array(0xFF53)
                    ,0xFF34  => array(0xFF54), 0xFF35  => array(0xFF55), 0xFF36  => array(0xFF56)
                    ,0xFF37  => array(0xFF57), 0xFF38  => array(0xFF58), 0xFF39  => array(0xFF59)
                    ,0xFF3A  => array(0xFF5A), 0x10400 => array(0x10428), 0x10401 => array(0x10429)
                    ,0x10402 => array(0x1042A), 0x10403 => array(0x1042B), 0x10404 => array(0x1042C)
                    ,0x10405 => array(0x1042D), 0x10406 => array(0x1042E), 0x10407 => array(0x1042F)
                    ,0x10408 => array(0x10430), 0x10409 => array(0x10431), 0x1040A => array(0x10432)
                    ,0x1040B => array(0x10433), 0x1040C => array(0x10434), 0x1040D => array(0x10435)
                    ,0x1040E => array(0x10436), 0x1040F => array(0x10437), 0x10410 => array(0x10438)
                    ,0x10411 => array(0x10439), 0x10412 => array(0x1043A), 0x10413 => array(0x1043B)
                    ,0x10414 => array(0x1043C), 0x10415 => array(0x1043D), 0x10416 => array(0x1043E)
                    ,0x10417 => array(0x1043F), 0x10418 => array(0x10440), 0x10419 => array(0x10441)
                    ,0x1041A => array(0x10442), 0x1041B => array(0x10443), 0x1041C => array(0x10444)
                    ,0x1041D => array(0x10445), 0x1041E => array(0x10446), 0x1041F => array(0x10447)
                    ,0x10420 => array(0x10448), 0x10421 => array(0x10449), 0x10422 => array(0x1044A)
                    ,0x10423 => array(0x1044B), 0x10424 => array(0x1044C), 0x10425 => array(0x1044D)
                    ,0x1D400 => array(0x61), 0x1D401 => array(0x62), 0x1D402 => array(0x63)
                    ,0x1D403 => array(0x64), 0x1D404 => array(0x65), 0x1D405 => array(0x66)
                    ,0x1D406 => array(0x67), 0x1D407 => array(0x68), 0x1D408 => array(0x69)
                    ,0x1D409 => array(0x6A), 0x1D40A => array(0x6B), 0x1D40B => array(0x6C)
                    ,0x1D40C => array(0x6D), 0x1D40D => array(0x6E), 0x1D40E => array(0x6F)
                    ,0x1D40F => array(0x70), 0x1D410 => array(0x71), 0x1D411 => array(0x72)
                    ,0x1D412 => array(0x73), 0x1D413 => array(0x74), 0x1D414 => array(0x75)
                    ,0x1D415 => array(0x76), 0x1D416 => array(0x77), 0x1D417 => array(0x78)
                    ,0x1D418 => array(0x79), 0x1D419 => array(0x7A), 0x1D434 => array(0x61)
                    ,0x1D435 => array(0x62), 0x1D436 => array(0x63), 0x1D437 => array(0x64)
                    ,0x1D438 => array(0x65), 0x1D439 => array(0x66), 0x1D43A => array(0x67)
                    ,0x1D43B => array(0x68), 0x1D43C => array(0x69), 0x1D43D => array(0x6A)
                    ,0x1D43E => array(0x6B), 0x1D43F => array(0x6C), 0x1D440 => array(0x6D)
                    ,0x1D441 => array(0x6E), 0x1D442 => array(0x6F), 0x1D443 => array(0x70)
                    ,0x1D444 => array(0x71), 0x1D445 => array(0x72), 0x1D446 => array(0x73)
                    ,0x1D447 => array(0x74), 0x1D448 => array(0x75), 0x1D449 => array(0x76)
                    ,0x1D44A => array(0x77), 0x1D44B => array(0x78), 0x1D44C => array(0x79)
                    ,0x1D44D => array(0x7A), 0x1D468 => array(0x61), 0x1D469 => array(0x62)
                    ,0x1D46A => array(0x63), 0x1D46B => array(0x64), 0x1D46C => array(0x65)
                    ,0x1D46D => array(0x66), 0x1D46E => array(0x67), 0x1D46F => array(0x68)
                    ,0x1D470 => array(0x69), 0x1D471 => array(0x6A), 0x1D472 => array(0x6B)
                    ,0x1D473 => array(0x6C), 0x1D474 => array(0x6D), 0x1D475 => array(0x6E)
                    ,0x1D476 => array(0x6F), 0x1D477 => array(0x70), 0x1D478 => array(0x71)
                    ,0x1D479 => array(0x72), 0x1D47A => array(0x73), 0x1D47B => array(0x74)
                    ,0x1D47C => array(0x75), 0x1D47D => array(0x76), 0x1D47E => array(0x77)
                    ,0x1D47F => array(0x78), 0x1D480 => array(0x79), 0x1D481 => array(0x7A)
                    ,0x1D49C => array(0x61), 0x1D49E => array(0x63), 0x1D49F => array(0x64)
                    ,0x1D4A2 => array(0x67), 0x1D4A5 => array(0x6A), 0x1D4A6 => array(0x6B)
                    ,0x1D4A9 => array(0x6E), 0x1D4AA => array(0x6F), 0x1D4AB => array(0x70)
                    ,0x1D4AC => array(0x71), 0x1D4AE => array(0x73), 0x1D4AF => array(0x74)
                    ,0x1D4B0 => array(0x75), 0x1D4B1 => array(0x76), 0x1D4B2 => array(0x77)
                    ,0x1D4B3 => array(0x78), 0x1D4B4 => array(0x79), 0x1D4B5 => array(0x7A)
                    ,0x1D4D0 => array(0x61), 0x1D4D1 => array(0x62), 0x1D4D2 => array(0x63)
                    ,0x1D4D3 => array(0x64), 0x1D4D4 => array(0x65), 0x1D4D5 => array(0x66)
                    ,0x1D4D6 => array(0x67), 0x1D4D7 => array(0x68), 0x1D4D8 => array(0x69)
                    ,0x1D4D9 => array(0x6A), 0x1D4DA => array(0x6B), 0x1D4DB => array(0x6C)
                    ,0x1D4DC => array(0x6D), 0x1D4DD => array(0x6E), 0x1D4DE => array(0x6F)
                    ,0x1D4DF => array(0x70), 0x1D4E0 => array(0x71), 0x1D4E1 => array(0x72)
                    ,0x1D4E2 => array(0x73), 0x1D4E3 => array(0x74), 0x1D4E4 => array(0x75)
                    ,0x1D4E5 => array(0x76), 0x1D4E6 => array(0x77), 0x1D4E7 => array(0x78)
                    ,0x1D4E8 => array(0x79), 0x1D4E9 => array(0x7A), 0x1D504 => array(0x61)
                    ,0x1D505 => array(0x62), 0x1D507 => array(0x64), 0x1D508 => array(0x65)
                    ,0x1D509 => array(0x66), 0x1D50A => array(0x67), 0x1D50D => array(0x6A)
                    ,0x1D50E => array(0x6B), 0x1D50F => array(0x6C), 0x1D510 => array(0x6D)
                    ,0x1D511 => array(0x6E), 0x1D512 => array(0x6F), 0x1D513 => array(0x70)
                    ,0x1D514 => array(0x71), 0x1D516 => array(0x73), 0x1D517 => array(0x74)
                    ,0x1D518 => array(0x75), 0x1D519 => array(0x76), 0x1D51A => array(0x77)
                    ,0x1D51B => array(0x78), 0x1D51C => array(0x79), 0x1D538 => array(0x61)
                    ,0x1D539 => array(0x62), 0x1D53B => array(0x64), 0x1D53C => array(0x65)
                    ,0x1D53D => array(0x66), 0x1D53E => array(0x67), 0x1D540 => array(0x69)
                    ,0x1D541 => array(0x6A), 0x1D542 => array(0x6B), 0x1D543 => array(0x6C)
                    ,0x1D544 => array(0x6D), 0x1D546 => array(0x6F), 0x1D54A => array(0x73)
                    ,0x1D54B => array(0x74), 0x1D54C => array(0x75), 0x1D54D => array(0x76)
                    ,0x1D54E => array(0x77), 0x1D54F => array(0x78), 0x1D550 => array(0x79)
                    ,0x1D56C => array(0x61), 0x1D56D => array(0x62), 0x1D56E => array(0x63)
                    ,0x1D56F => array(0x64), 0x1D570 => array(0x65), 0x1D571 => array(0x66)
                    ,0x1D572 => array(0x67), 0x1D573 => array(0x68), 0x1D574 => array(0x69)
                    ,0x1D575 => array(0x6A), 0x1D576 => array(0x6B), 0x1D577 => array(0x6C)
                    ,0x1D578 => array(0x6D), 0x1D579 => array(0x6E), 0x1D57A => array(0x6F)
                    ,0x1D57B => array(0x70), 0x1D57C => array(0x71), 0x1D57D => array(0x72)
                    ,0x1D57E => array(0x73), 0x1D57F => array(0x74), 0x1D580 => array(0x75)
                    ,0x1D581 => array(0x76), 0x1D582 => array(0x77), 0x1D583 => array(0x78)
                    ,0x1D584 => array(0x79), 0x1D585 => array(0x7A), 0x1D5A0 => array(0x61)
                    ,0x1D5A1 => array(0x62), 0x1D5A2 => array(0x63), 0x1D5A3 => array(0x64)
                    ,0x1D5A4 => array(0x65), 0x1D5A5 => array(0x66), 0x1D5A6 => array(0x67)
                    ,0x1D5A7 => array(0x68), 0x1D5A8 => array(0x69), 0x1D5A9 => array(0x6A)
                    ,0x1D5AA => array(0x6B), 0x1D5AB => array(0x6C), 0x1D5AC => array(0x6D)
                    ,0x1D5AD => array(0x6E), 0x1D5AE => array(0x6F), 0x1D5AF => array(0x70)
                    ,0x1D5B0 => array(0x71), 0x1D5B1 => array(0x72), 0x1D5B2 => array(0x73)
                    ,0x1D5B3 => array(0x74), 0x1D5B4 => array(0x75), 0x1D5B5 => array(0x76)
                    ,0x1D5B6 => array(0x77), 0x1D5B7 => array(0x78), 0x1D5B8 => array(0x79)
                    ,0x1D5B9 => array(0x7A), 0x1D5D4 => array(0x61), 0x1D5D5 => array(0x62)
                    ,0x1D5D6 => array(0x63), 0x1D5D7 => array(0x64), 0x1D5D8 => array(0x65)
                    ,0x1D5D9 => array(0x66), 0x1D5DA => array(0x67), 0x1D5DB => array(0x68)
                    ,0x1D5DC => array(0x69), 0x1D5DD => array(0x6A), 0x1D5DE => array(0x6B)
                    ,0x1D5DF => array(0x6C), 0x1D5E0 => array(0x6D), 0x1D5E1 => array(0x6E)
                    ,0x1D5E2 => array(0x6F), 0x1D5E3 => array(0x70), 0x1D5E4 => array(0x71)
                    ,0x1D5E5 => array(0x72), 0x1D5E6 => array(0x73), 0x1D5E7 => array(0x74)
                    ,0x1D5E8 => array(0x75), 0x1D5E9 => array(0x76), 0x1D5EA => array(0x77)
                    ,0x1D5EB => array(0x78), 0x1D5EC => array(0x79), 0x1D5ED => array(0x7A)
                    ,0x1D608 => array(0x61), 0x1D609 => array(0x62) ,0x1D60A => array(0x63)
                    ,0x1D60B => array(0x64), 0x1D60C => array(0x65), 0x1D60D => array(0x66)
                    ,0x1D60E => array(0x67), 0x1D60F => array(0x68), 0x1D610 => array(0x69)
                    ,0x1D611 => array(0x6A), 0x1D612 => array(0x6B), 0x1D613 => array(0x6C)
                    ,0x1D614 => array(0x6D), 0x1D615 => array(0x6E), 0x1D616 => array(0x6F)
                    ,0x1D617 => array(0x70), 0x1D618 => array(0x71), 0x1D619 => array(0x72)
                    ,0x1D61A => array(0x73), 0x1D61B => array(0x74), 0x1D61C => array(0x75)
                    ,0x1D61D => array(0x76), 0x1D61E => array(0x77), 0x1D61F => array(0x78)
                    ,0x1D620 => array(0x79), 0x1D621 => array(0x7A), 0x1D63C => array(0x61)
                    ,0x1D63D => array(0x62), 0x1D63E => array(0x63), 0x1D63F => array(0x64)
                    ,0x1D640 => array(0x65), 0x1D641 => array(0x66), 0x1D642 => array(0x67)
                    ,0x1D643 => array(0x68), 0x1D644 => array(0x69), 0x1D645 => array(0x6A)
                    ,0x1D646 => array(0x6B), 0x1D647 => array(0x6C), 0x1D648 => array(0x6D)
                    ,0x1D649 => array(0x6E), 0x1D64A => array(0x6F), 0x1D64B => array(0x70)
                    ,0x1D64C => array(0x71), 0x1D64D => array(0x72), 0x1D64E => array(0x73)
                    ,0x1D64F => array(0x74), 0x1D650 => array(0x75), 0x1D651 => array(0x76)
                    ,0x1D652 => array(0x77), 0x1D653 => array(0x78), 0x1D654 => array(0x79)
                    ,0x1D655 => array(0x7A), 0x1D670 => array(0x61), 0x1D671 => array(0x62)
                    ,0x1D672 => array(0x63), 0x1D673 => array(0x64), 0x1D674 => array(0x65)
                    ,0x1D675 => array(0x66), 0x1D676 => array(0x67), 0x1D677 => array(0x68)
                    ,0x1D678 => array(0x69), 0x1D679 => array(0x6A), 0x1D67A => array(0x6B)
                    ,0x1D67B => array(0x6C), 0x1D67C => array(0x6D), 0x1D67D => array(0x6E)
                    ,0x1D67E => array(0x6F), 0x1D67F => array(0x70), 0x1D680 => array(0x71)
                    ,0x1D681 => array(0x72), 0x1D682 => array(0x73), 0x1D683 => array(0x74)
                    ,0x1D684 => array(0x75), 0x1D685 => array(0x76), 0x1D686 => array(0x77)
                    ,0x1D687 => array(0x78), 0x1D688 => array(0x79), 0x1D689 => array(0x7A)
                    ,0x1D6A8 => array(0x3B1), 0x1D6A9 => array(0x3B2), 0x1D6AA => array(0x3B3)
                    ,0x1D6AB => array(0x3B4), 0x1D6AC => array(0x3B5), 0x1D6AD => array(0x3B6)
                    ,0x1D6AE => array(0x3B7), 0x1D6AF => array(0x3B8), 0x1D6B0 => array(0x3B9)
                    ,0x1D6B1 => array(0x3BA), 0x1D6B2 => array(0x3BB), 0x1D6B3 => array(0x3BC)
                    ,0x1D6B4 => array(0x3BD), 0x1D6B5 => array(0x3BE), 0x1D6B6 => array(0x3BF)
                    ,0x1D6B7 => array(0x3C0), 0x1D6B8 => array(0x3C1), 0x1D6B9 => array(0x3B8)
                    ,0x1D6BA => array(0x3C3), 0x1D6BB => array(0x3C4), 0x1D6BC => array(0x3C5)
                    ,0x1D6BD => array(0x3C6), 0x1D6BE => array(0x3C7), 0x1D6BF => array(0x3C8)
                    ,0x1D6C0 => array(0x3C9), 0x1D6D3 => array(0x3C3), 0x1D6E2 => array(0x3B1)
                    ,0x1D6E3 => array(0x3B2), 0x1D6E4 => array(0x3B3), 0x1D6E5 => array(0x3B4)
                    ,0x1D6E6 => array(0x3B5), 0x1D6E7 => array(0x3B6), 0x1D6E8 => array(0x3B7)
                    ,0x1D6E9 => array(0x3B8), 0x1D6EA => array(0x3B9), 0x1D6EB => array(0x3BA)
                    ,0x1D6EC => array(0x3BB), 0x1D6ED => array(0x3BC), 0x1D6EE => array(0x3BD)
                    ,0x1D6EF => array(0x3BE), 0x1D6F0 => array(0x3BF), 0x1D6F1 => array(0x3C0)
                    ,0x1D6F2 => array(0x3C1), 0x1D6F3 => array(0x3B8) ,0x1D6F4 => array(0x3C3)
                    ,0x1D6F5 => array(0x3C4), 0x1D6F6 => array(0x3C5), 0x1D6F7 => array(0x3C6)
                    ,0x1D6F8 => array(0x3C7), 0x1D6F9 => array(0x3C8) ,0x1D6FA => array(0x3C9)
                    ,0x1D70D => array(0x3C3), 0x1D71C => array(0x3B1), 0x1D71D => array(0x3B2)
                    ,0x1D71E => array(0x3B3), 0x1D71F => array(0x3B4), 0x1D720 => array(0x3B5)
                    ,0x1D721 => array(0x3B6), 0x1D722 => array(0x3B7), 0x1D723 => array(0x3B8)
                    ,0x1D724 => array(0x3B9), 0x1D725 => array(0x3BA), 0x1D726 => array(0x3BB)
                    ,0x1D727 => array(0x3BC), 0x1D728 => array(0x3BD), 0x1D729 => array(0x3BE)
                    ,0x1D72A => array(0x3BF), 0x1D72B => array(0x3C0), 0x1D72C => array(0x3C1)
                    ,0x1D72D => array(0x3B8), 0x1D72E => array(0x3C3), 0x1D72F => array(0x3C4)
                    ,0x1D730 => array(0x3C5), 0x1D731 => array(0x3C6), 0x1D732 => array(0x3C7)
                    ,0x1D733 => array(0x3C8), 0x1D734 => array(0x3C9), 0x1D747 => array(0x3C3)
                    ,0x1D756 => array(0x3B1), 0x1D757 => array(0x3B2), 0x1D758 => array(0x3B3)
                    ,0x1D759 => array(0x3B4), 0x1D75A => array(0x3B5), 0x1D75B => array(0x3B6)
                    ,0x1D75C => array(0x3B7), 0x1D75D => array(0x3B8), 0x1D75E => array(0x3B9)
                    ,0x1D75F => array(0x3BA), 0x1D760 => array(0x3BB), 0x1D761 => array(0x3BC)
                    ,0x1D762 => array(0x3BD), 0x1D763 => array(0x3BE), 0x1D764 => array(0x3BF)
                    ,0x1D765 => array(0x3C0), 0x1D766 => array(0x3C1), 0x1D767 => array(0x3B8)
                    ,0x1D768 => array(0x3C3), 0x1D769 => array(0x3C4), 0x1D76A => array(0x3C5)
                    ,0x1D76B => array(0x3C6), 0x1D76C => array(0x3C7), 0x1D76D => array(0x3C8)
                    ,0x1D76E => array(0x3C9), 0x1D781 => array(0x3C3), 0x1D790 => array(0x3B1)
                    ,0x1D791 => array(0x3B2), 0x1D792 => array(0x3B3), 0x1D793 => array(0x3B4)
                    ,0x1D794 => array(0x3B5), 0x1D795 => array(0x3B6), 0x1D796 => array(0x3B7)
                    ,0x1D797 => array(0x3B8), 0x1D798 => array(0x3B9), 0x1D799 => array(0x3BA)
                    ,0x1D79A => array(0x3BB), 0x1D79B => array(0x3BC), 0x1D79C => array(0x3BD)
                    ,0x1D79D => array(0x3BE), 0x1D79E => array(0x3BF), 0x1D79F => array(0x3C0)
                    ,0x1D7A0 => array(0x3C1), 0x1D7A1 => array(0x3B8), 0x1D7A2 => array(0x3C3)
                    ,0x1D7A3 => array(0x3C4), 0x1D7A4 => array(0x3C5), 0x1D7A5 => array(0x3C6)
                    ,0x1D7A6 => array(0x3C7), 0x1D7A7 => array(0x3C8), 0x1D7A8 => array(0x3C9)
                    ,0x1D7BB => array(0x3C3), 0x3F9   => array(0x3C3), 0x1D2C  => array(0x61)
                    ,0x1D2D  => array(0xE6), 0x1D2E  => array(0x62), 0x1D30  => array(0x64)
                    ,0x1D31  => array(0x65), 0x1D32  => array(0x1DD), 0x1D33  => array(0x67)
                    ,0x1D34  => array(0x68), 0x1D35  => array(0x69), 0x1D36  => array(0x6A)
                    ,0x1D37  => array(0x6B), 0x1D38  => array(0x6C), 0x1D39  => array(0x6D)
                    ,0x1D3A  => array(0x6E), 0x1D3C  => array(0x6F), 0x1D3D  => array(0x223)
                    ,0x1D3E  => array(0x70), 0x1D3F  => array(0x72), 0x1D40  => array(0x74)
                    ,0x1D41  => array(0x75), 0x1D42  => array(0x77), 0x213B  => array(0x66, 0x61, 0x78)
                    ,0x3250  => array(0x70, 0x74, 0x65), 0x32CC  => array(0x68, 0x67)
                    ,0x32CE  => array(0x65, 0x76), 0x32CF  => array(0x6C, 0x74, 0x64)
                    ,0x337A  => array(0x69, 0x75), 0x33DE  => array(0x76, 0x2215, 0x6D)
                    ,0x33DF  => array(0x61, 0x2215, 0x6D)
                    )
            ,'norm_combcls' => array(0x334   => 1,   0x335   => 1,   0x336   => 1,   0x337   => 1
                    ,0x338   => 1,   0x93C   => 7,   0x9BC   => 7,   0xA3C   => 7,   0xABC   => 7
                    ,0xB3C   => 7,   0xCBC   => 7,   0x1037  => 7,   0x3099  => 8,   0x309A  => 8
                    ,0x94D   => 9,   0x9CD   => 9,   0xA4D   => 9,   0xACD   => 9,   0xB4D   => 9
                    ,0xBCD   => 9,   0xC4D   => 9,   0xCCD   => 9,   0xD4D   => 9,   0xDCA   => 9
                    ,0xE3A   => 9,   0xF84   => 9,   0x1039  => 9,   0x1714  => 9,   0x1734  => 9
                    ,0x17D2  => 9,   0x5B0   => 10,  0x5B1   => 11,  0x5B2   => 12,  0x5B3   => 13
                    ,0x5B4   => 14,  0x5B5   => 15,  0x5B6   => 16,  0x5B7   => 17,  0x5B8   => 18
                    ,0x5B9   => 19,  0x5BB   => 20,  0x5Bc   => 21,  0x5BD   => 22,  0x5BF   => 23
                    ,0x5C1   => 24,  0x5C2   => 25,  0xFB1E  => 26,  0x64B   => 27,  0x64C   => 28
                    ,0x64D   => 29,  0x64E   => 30,  0x64F   => 31,  0x650   => 32,  0x651   => 33
                    ,0x652   => 34,  0x670   => 35,  0x711   => 36,  0xC55   => 84,  0xC56   => 91
                    ,0xE38   => 103, 0xE39   => 103, 0xE48   => 107, 0xE49   => 107, 0xE4A   => 107
                    ,0xE4B   => 107, 0xEB8   => 118, 0xEB9   => 118, 0xEC8   => 122, 0xEC9   => 122
                    ,0xECA   => 122, 0xECB   => 122, 0xF71   => 129, 0xF72   => 130, 0xF7A   => 130
                    ,0xF7B   => 130, 0xF7C   => 130, 0xF7D   => 130, 0xF80   => 130, 0xF74   => 132
                    ,0x321   => 202, 0x322   => 202, 0x327   => 202, 0x328   => 202, 0x31B   => 216
                    ,0xF39   => 216, 0x1D165 => 216, 0x1D166 => 216, 0x1D16E => 216, 0x1D16F => 216
                    ,0x1D170 => 216, 0x1D171 => 216, 0x1D172 => 216, 0x302A  => 218, 0x316   => 220
                    ,0x317   => 220, 0x318   => 220, 0x319   => 220, 0x31C   => 220, 0x31D   => 220
                    ,0x31E   => 220, 0x31F   => 220, 0x320   => 220, 0x323   => 220, 0x324   => 220
                    ,0x325   => 220, 0x326   => 220, 0x329   => 220, 0x32A   => 220, 0x32B   => 220
                    ,0x32C   => 220, 0x32D   => 220, 0x32E   => 220, 0x32F   => 220, 0x330   => 220
                    ,0x331   => 220, 0x332   => 220, 0x333   => 220, 0x339   => 220, 0x33A   => 220
                    ,0x33B   => 220, 0x33C   => 220, 0x347   => 220, 0x348   => 220, 0x349   => 220
                    ,0x34D   => 220, 0x34E   => 220, 0x353   => 220, 0x354   => 220, 0x355   => 220
                    ,0x356   => 220, 0x591   => 220, 0x596   => 220, 0x59B   => 220, 0x5A3   => 220
                    ,0x5A4   => 220, 0x5A5   => 220, 0x5A6   => 220, 0x5A7   => 220, 0x5AA   => 220
                    ,0x655   => 220, 0x656   => 220, 0x6E3   => 220, 0x6EA   => 220, 0x6ED   => 220
                    ,0x731   => 220, 0x734   => 220, 0x737   => 220, 0x738   => 220, 0x739   => 220
                    ,0x73B   => 220, 0x73C   => 220, 0x73E   => 220, 0x742   => 220, 0x744   => 220
                    ,0x746   => 220, 0x748   => 220, 0x952   => 220, 0xF18   => 220, 0xF19   => 220
                    ,0xF35   => 220, 0xF37   => 220, 0xFC6   => 220, 0x193B  => 220, 0x20E8  => 220
                    ,0x1D17B => 220, 0x1D17C => 220, 0x1D17D => 220, 0x1D17E => 220, 0x1D17F => 220
                    ,0x1D180 => 220, 0x1D181 => 220, 0x1D182 => 220, 0x1D18A => 220, 0x1D18B => 220
                    ,0x59A   => 222, 0x5AD   => 222, 0x1929  => 222, 0x302D  => 222, 0x302E  => 224
                    ,0x302F  => 224, 0x1D16D => 226, 0x5AE   => 228, 0x18A9  => 228, 0x302B  => 228
                    ,0x300   => 230, 0x301   => 230, 0x302   => 230, 0x303   => 230, 0x304   => 230
                    ,0x305   => 230, 0x306   => 230, 0x307   => 230, 0x308   => 230, 0x309   => 230
                    ,0x30A   => 230, 0x30B   => 230, 0x30C   => 230, 0x30D   => 230, 0x30E   => 230
                    ,0x30F   => 230, 0x310   => 230, 0x311   => 230, 0x312   => 230, 0x313   => 230
                    ,0x314   => 230, 0x33D   => 230, 0x33E   => 230, 0x33F   => 230, 0x340   => 230
                    ,0x341   => 230, 0x342   => 230, 0x343   => 230, 0x344   => 230, 0x346   => 230
                    ,0x34A   => 230, 0x34B   => 230, 0x34C   => 230, 0x350   => 230, 0x351   => 230
                    ,0x352   => 230, 0x357   => 230, 0x363   => 230, 0x364   => 230, 0x365   => 230
                    ,0x366   => 230, 0x367   => 230, 0x368   => 230, 0x369   => 230, 0x36A   => 230
                    ,0x36B   => 230, 0x36C   => 230, 0x36D   => 230, 0x36E   => 230, 0x36F   => 230
                    ,0x483   => 230, 0x484   => 230, 0x485   => 230, 0x486   => 230, 0x592   => 230
                    ,0x593   => 230, 0x594   => 230, 0x595   => 230, 0x597   => 230, 0x598   => 230
                    ,0x599   => 230, 0x59C   => 230, 0x59D   => 230, 0x59E   => 230, 0x59F   => 230
                    ,0x5A0   => 230, 0x5A1   => 230, 0x5A8   => 230, 0x5A9   => 230, 0x5AB   => 230
                    ,0x5AC   => 230, 0x5AF   => 230, 0x5C4   => 230, 0x610   => 230, 0x611   => 230
                    ,0x612   => 230, 0x613   => 230, 0x614   => 230, 0x615   => 230, 0x653   => 230
                    ,0x654   => 230, 0x657   => 230, 0x658   => 230, 0x6D6   => 230, 0x6D7   => 230
                    ,0x6D8   => 230, 0x6D9   => 230, 0x6DA   => 230, 0x6DB   => 230, 0x6DC   => 230
                    ,0x6DF   => 230, 0x6E0   => 230, 0x6E1   => 230, 0x6E2   => 230, 0x6E4   => 230
                    ,0x6E7   => 230, 0x6E8   => 230, 0x6EB   => 230, 0x6EC   => 230, 0x730   => 230
                    ,0x732   => 230, 0x733   => 230, 0x735   => 230, 0x736   => 230, 0x73A   => 230
                    ,0x73D   => 230, 0x73F   => 230, 0x740   => 230, 0x741   => 230, 0x743   => 230
                    ,0x745   => 230, 0x747   => 230, 0x749   => 230, 0x74A   => 230, 0x951   => 230
                    ,0x953   => 230, 0x954   => 230, 0xF82   => 230, 0xF83   => 230, 0xF86   => 230
                    ,0xF87   => 230, 0x170D  => 230, 0x193A  => 230, 0x20D0  => 230, 0x20D1  => 230
                    ,0x20D4  => 230, 0x20D5  => 230, 0x20D6  => 230, 0x20D7  => 230, 0x20DB  => 230
                    ,0x20DC  => 230, 0x20E1  => 230, 0x20E7  => 230, 0x20E9  => 230, 0xFE20  => 230
                    ,0xFE21  => 230, 0xFE22  => 230, 0xFE23  => 230, 0x1D185 => 230, 0x1D186 => 230
                    ,0x1D187 => 230, 0x1D189 => 230, 0x1D188 => 230, 0x1D1AA => 230, 0x1D1AB => 230
                    ,0x1D1AC => 230, 0x1D1AD => 230, 0x315   => 232, 0x31A   => 232, 0x302C  => 232
                    ,0x35F   => 233, 0x362   => 233, 0x35D   => 234, 0x35E   => 234, 0x360   => 234
                    ,0x361   => 234, 0x345   => 240
                    )
            );
}
?>
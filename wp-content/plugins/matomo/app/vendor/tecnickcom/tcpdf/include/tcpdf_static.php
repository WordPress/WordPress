<?php

namespace {
    //============================================================+
    // File name   : tcpdf_static.php
    // Version     : 1.1.5
    // Begin       : 2002-08-03
    // Last Update : 2024-12-23
    // Author      : Nicola Asuni - Tecnick.com LTD - www.tecnick.com - info@tecnick.com
    // License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
    // -------------------------------------------------------------------
    // Copyright (C) 2002-2025 Nicola Asuni - Tecnick.com LTD
    //
    // This file is part of TCPDF software library.
    //
    // TCPDF is free software: you can redistribute it and/or modify it
    // under the terms of the GNU Lesser General Public License as
    // published by the Free Software Foundation, either version 3 of the
    // License, or (at your option) any later version.
    //
    // TCPDF is distributed in the hope that it will be useful, but
    // WITHOUT ANY WARRANTY; without even the implied warranty of
    // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    // See the GNU Lesser General Public License for more details.
    //
    // You should have received a copy of the License
    // along with TCPDF. If not, see
    // <http://www.tecnick.com/pagefiles/tcpdf/LICENSE.TXT>.
    //
    // See LICENSE.TXT file for more information.
    // -------------------------------------------------------------------
    //
    // Description :
    //   Static methods used by the TCPDF class.
    //
    //============================================================+
    /**
     * @file
     * This is a PHP class that contains static methods for the TCPDF class.<br>
     * @package com.tecnick.tcpdf
     * @author Nicola Asuni
     * @version 1.1.5
     */
    /**
     * @class TCPDF_STATIC
     * Static methods used by the TCPDF class.
     * @package com.tecnick.tcpdf
     * @brief PHP class for generating PDF documents without requiring external extensions.
     * @version 1.1.5
     * @author Nicola Asuni - info@tecnick.com
     */
    class TCPDF_STATIC
    {
        /**
         * Current TCPDF version.
         * @private static
         */
        private static $tcpdf_version = '6.8.2';
        /**
         * String alias for total number of pages.
         * @public static
         */
        public static $alias_tot_pages = '{:ptp:}';
        /**
         * String alias for page number.
         * @public static
         */
        public static $alias_num_page = '{:pnp:}';
        /**
         * String alias for total number of pages in a single group.
         * @public static
         */
        public static $alias_group_tot_pages = '{:ptg:}';
        /**
         * String alias for group page number.
         * @public static
         */
        public static $alias_group_num_page = '{:png:}';
        /**
         * String alias for right shift compensation used to correctly align page numbers on the right.
         * @public static
         */
        public static $alias_right_shift = '{rsc:';
        /**
         * Encryption padding string.
         * @public static
         */
        public static $enc_padding = "(\xbfN^Nu\x8aAd\x00NV\xff\xfa\x01\x08..\x00\xb6\xd0h>\x80/\f\xa9\xfedSiz";
        /**
         * ByteRange placemark used during digital signature process.
         * @since 4.6.028 (2009-08-25)
         * @public static
         */
        public static $byterange_string = '/ByteRange[0 ********** ********** **********]';
        /**
         * Array page boxes names
         * @public static
         */
        public static $pageboxes = array('MediaBox', 'CropBox', 'BleedBox', 'TrimBox', 'ArtBox');
        /**
         * Array of default cURL options for curl_setopt_array.
         *
         * @var array<int, bool|int|string> cURL options.
         */
        protected const CURLOPT_DEFAULT = [\CURLOPT_CONNECTTIMEOUT => 5, \CURLOPT_MAXREDIRS => 5, \CURLOPT_PROTOCOLS => \CURLPROTO_HTTPS | \CURLPROTO_HTTP | \CURLPROTO_FTP | \CURLPROTO_FTPS, \CURLOPT_SSL_VERIFYHOST => 2, \CURLOPT_SSL_VERIFYPEER => \true, \CURLOPT_TIMEOUT => 30, \CURLOPT_USERAGENT => 'tcpdf'];
        /**
         * Array of fixed cURL options for curl_setopt_array.
         *
         * @var array<int, bool|int|string> cURL options.
         */
        protected const CURLOPT_FIXED = [\CURLOPT_FAILONERROR => \true, \CURLOPT_RETURNTRANSFER => \true];
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        /**
         * Return the current TCPDF version.
         * @return string TCPDF version string
         * @since 5.9.012 (2010-11-10)
         * @public static
         */
        public static function getTCPDFVersion()
        {
            return self::$tcpdf_version;
        }
        /**
         * Return the current TCPDF producer.
         * @return string TCPDF producer string
         * @since 6.0.000 (2013-03-16)
         * @public static
         */
        public static function getTCPDFProducer()
        {
            return "TCPDF " . self::getTCPDFVersion() . " (http://www.tcpdf.org)";
        }
        /**
         * Check if the URL exist.
         * @param string $url URL to check.
         * @return boolean true if the URl exist, false otherwise.
         * @since 5.9.204 (2013-01-28)
         * @public static
         */
        public static function isValidURL($url)
        {
            $headers = @\get_headers($url);
            if ($headers === \false) {
                return \false;
            }
            return \strpos($headers[0], '200') !== \false;
        }
        /**
         * Removes SHY characters from text.
         * Unicode Data:<ul>
         * <li>Name : SOFT HYPHEN, commonly abbreviated as SHY</li>
         * <li>HTML Entity (decimal): "&amp;#173;"</li>
         * <li>HTML Entity (hex): "&amp;#xad;"</li>
         * <li>HTML Entity (named): "&amp;shy;"</li>
         * <li>How to type in Microsoft Windows: [Alt +00AD] or [Alt 0173]</li>
         * <li>UTF-8 (hex): 0xC2 0xAD (c2ad)</li>
         * <li>UTF-8 character: chr(194).chr(173)</li>
         * </ul>
         * @param string $txt input string
         * @param boolean $unicode True if we are in unicode mode, false otherwise.
         * @return string without SHY characters.
         * @since (4.5.019) 2009-02-28
         * @public static
         */
        public static function removeSHY($txt = '', $unicode = \true)
        {
            $txt = \preg_replace('/([\\xc2]{1}[\\xad]{1})/', '', $txt);
            if (!$unicode) {
                $txt = \preg_replace('/([\\xad]{1})/', '', $txt);
            }
            return $txt;
        }
        /**
         * Get the border mode accounting for multicell position (opens bottom side of multicell crossing pages)
         * @param string|array|int $brd Indicates if borders must be drawn around the cell block. The value can be a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul>or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul> or an array of line styles for each border group: array('LTRB' => array('width' => 2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)))
         * @param string $position multicell position: 'start', 'middle', 'end'
         * @param boolean $opencell True when the cell is left open at the page bottom, false otherwise.
         * @return array border mode array
         * @since 4.4.002 (2008-12-09)
         * @public static
         */
        public static function getBorderMode($brd, $position = 'start', $opencell = \true)
        {
            if (!$opencell or empty($brd)) {
                return $brd;
            }
            if ($brd == 1) {
                $brd = 'LTRB';
            }
            if (\is_string($brd)) {
                // convert string to array
                $slen = \strlen($brd);
                $newbrd = array();
                for ($i = 0; $i < $slen; ++$i) {
                    $newbrd[$brd[$i]] = array('cap' => 'square', 'join' => 'miter');
                }
                $brd = $newbrd;
            }
            foreach ($brd as $border => $style) {
                switch ($position) {
                    case 'start':
                        if (\strpos($border, 'B') !== \false) {
                            // remove bottom line
                            $newkey = \str_replace('B', '', $border);
                            if (\strlen($newkey) > 0) {
                                $brd[$newkey] = $style;
                            }
                            unset($brd[$border]);
                        }
                        break;
                    case 'middle':
                        if (\strpos($border, 'B') !== \false) {
                            // remove bottom line
                            $newkey = \str_replace('B', '', $border);
                            if (\strlen($newkey) > 0) {
                                $brd[$newkey] = $style;
                            }
                            unset($brd[$border]);
                            $border = $newkey;
                        }
                        if (\strpos($border, 'T') !== \false) {
                            // remove bottom line
                            $newkey = \str_replace('T', '', $border);
                            if (\strlen($newkey) > 0) {
                                $brd[$newkey] = $style;
                            }
                            unset($brd[$border]);
                        }
                        break;
                    case 'end':
                        if (\strpos($border, 'T') !== \false) {
                            // remove bottom line
                            $newkey = \str_replace('T', '', $border);
                            if (\strlen($newkey) > 0) {
                                $brd[$newkey] = $style;
                            }
                            unset($brd[$border]);
                        }
                        break;
                }
            }
            return $brd;
        }
        /**
         * Determine whether a string is empty.
         * @param string $str string to be checked
         * @return bool true if string is empty
         * @since 4.5.044 (2009-04-16)
         * @public static
         */
        public static function empty_string($str)
        {
            return \is_null($str) or \is_string($str) and \strlen($str) == 0;
        }
        /**
         * Returns a temporary filename for caching object on filesystem.
         * @param string $type Type of file (name of the subdir on the tcpdf cache folder).
         * @param string $file_id TCPDF file_id.
         * @return string filename.
         * @since 4.5.000 (2008-12-31)
         * @public static
         */
        public static function getObjFilename($type = 'tmp', $file_id = '')
        {
            return \tempnam(\K_PATH_CACHE, '__tcpdf_' . $file_id . '_' . $type . '_' . \md5(\TCPDF_STATIC::getRandomSeed()) . '_');
        }
        /**
         * Add "\" before "\", "(" and ")"
         * @param string $s string to escape.
         * @return string escaped string.
         * @public static
         */
        public static function _escape($s)
        {
            // the chr(13) substitution fixes the Bugs item #1421290.
            return \strtr($s, array(')' => '\\)', '(' => '\\(', '\\' => '\\\\', \chr(13) => '\\r'));
        }
        /**
         * Escape some special characters (&lt; &gt; &amp;) for XML output.
         * @param string $str Input string to convert.
         * @return string converted string
         * @since 5.9.121 (2011-09-28)
         * @public static
         */
        public static function _escapeXML($str)
        {
            $replaceTable = array("\x00" => '', '&' => '&amp;', '<' => '&lt;', '>' => '&gt;');
            $str = \strtr($str === null ? '' : $str, $replaceTable);
            return $str;
        }
        /**
         * Creates a copy of a class object
         * @param object $object class object to be cloned
         * @return object cloned object
         * @since 4.5.029 (2009-03-19)
         * @public static
         */
        public static function objclone($object)
        {
            if ($object instanceof \Imagick and \version_compare(\phpversion('imagick'), '3.0.1') !== 1) {
                // on the versions after 3.0.1 the clone() method was deprecated in favour of clone keyword
                return @$object->clone();
            }
            return @clone $object;
        }
        /**
         * Output input data and compress it if possible.
         * @param string $data Data to output.
         * @param int $length Data length in bytes.
         * @since 5.9.086
         * @public static
         */
        public static function sendOutputData($data, $length)
        {
            if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) or empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
                // the content length may vary if the server is using compression
                \header('Content-Length: ' . $length);
            }
            echo $data;
        }
        /**
         * Replace page number aliases with number.
         * @param string $page Page content.
         * @param array $replace Array of replacements (array keys are replacement strings, values are alias arrays).
         * @param int $diff If passed, this will be set to the total char number difference between alias and replacements.
         * @return array replaced page content and updated $diff parameter as array.
         * @public static
         */
        public static function replacePageNumAliases($page, $replace, $diff = 0)
        {
            foreach ($replace as $rep) {
                foreach ($rep[3] as $a) {
                    if (\strpos($page, $a) !== \false) {
                        $page = \str_replace($a, $rep[0], $page);
                        $diff += $rep[2] - $rep[1];
                    }
                }
            }
            return array($page, $diff);
        }
        /**
         * Returns timestamp in seconds from formatted date-time.
         * @param string $date Formatted date-time.
         * @return int seconds.
         * @since 5.9.152 (2012-03-23)
         * @public static
         */
        public static function getTimestamp($date)
        {
            if ($date[0] == 'D' and $date[1] == ':') {
                // remove date prefix if present
                $date = \substr($date, 2);
            }
            return \strtotime($date);
        }
        /**
         * Returns a formatted date-time.
         * @param int $time Time in seconds.
         * @return string escaped date string.
         * @since 5.9.152 (2012-03-23)
         * @public static
         */
        public static function getFormattedDate($time)
        {
            return \substr_replace(\date('YmdHisO', \intval($time)), '\'', 0 - 2, 0) . '\'';
        }
        /**
         * Returns a string containing random data to be used as a seed for encryption methods.
         * @param string $seed starting seed value
         * @return string containing random data
         * @author Nicola Asuni
         * @since 5.9.006 (2010-10-19)
         * @public static
         */
        public static function getRandomSeed($seed = '')
        {
            $rnd = \uniqid(\rand() . \microtime(\true), \true);
            if (\function_exists('posix_getpid')) {
                $rnd .= \posix_getpid();
            }
            if (\function_exists('random_bytes')) {
                $rnd .= \random_bytes(512);
            } elseif (\function_exists('openssl_random_pseudo_bytes') and \strtoupper(\substr(\PHP_OS, 0, 3)) !== 'WIN') {
                // this is not used on windows systems because it is very slow for a know bug
                $rnd .= \openssl_random_pseudo_bytes(512);
            } else {
                for ($i = 0; $i < 23; ++$i) {
                    $rnd .= \uniqid('', \true);
                }
            }
            return $rnd . $seed . __FILE__ . \microtime(\true);
        }
        /**
         * Encrypts a string using MD5 and returns it's value as a binary string.
         * @param string $str input string
         * @return string MD5 encrypted binary string
         * @since 2.0.000 (2008-01-02)
         * @public static
         */
        public static function _md5_16($str)
        {
            return \pack('H*', \md5($str));
        }
        /**
         * Returns the input text encrypted using AES algorithm and the specified key.
         * This method requires openssl or mcrypt. Text is padded to 16bytes blocks
         * @param string $key encryption key
         * @param string $text input text to be encrypted
         * @return string encrypted text
         * @author Nicola Asuni
         * @since 5.0.005 (2010-05-11)
         * @public static
         */
        public static function _AES($key, $text)
        {
            // padding (RFC 2898, PKCS #5: Password-Based Cryptography Specification Version 2.0)
            $padding = 16 - \strlen($text) % 16;
            $text .= \str_repeat(\chr($padding), $padding);
            if (\extension_loaded('openssl')) {
                $algo = 'aes-256-cbc';
                if (\strlen($key) == 16) {
                    $algo = 'aes-128-cbc';
                }
                $iv = \openssl_random_pseudo_bytes(\openssl_cipher_iv_length($algo));
                $text = \openssl_encrypt($text, $algo, $key, \OPENSSL_RAW_DATA, $iv);
                return $iv . \substr($text, 0, -16);
            }
            $iv = \mcrypt_create_iv(\mcrypt_get_iv_size(\MCRYPT_RIJNDAEL_128, \MCRYPT_MODE_CBC), \MCRYPT_RAND);
            $text = \mcrypt_encrypt(\MCRYPT_RIJNDAEL_128, $key, $text, \MCRYPT_MODE_CBC, $iv);
            $text = $iv . $text;
            return $text;
        }
        /**
         * Returns the input text encrypted using AES algorithm and the specified key.
         * This method requires openssl or mcrypt. Text is not padded
         * @param string $key encryption key
         * @param string $text input text to be encrypted
         * @return string encrypted text
         * @author Nicola Asuni
         * @since TODO
         * @public static
         */
        public static function _AESnopad($key, $text)
        {
            if (\extension_loaded('openssl')) {
                $algo = 'aes-256-cbc';
                if (\strlen($key) == 16) {
                    $algo = 'aes-128-cbc';
                }
                $iv = \str_repeat("\x00", \openssl_cipher_iv_length($algo));
                $text = \openssl_encrypt($text, $algo, $key, \OPENSSL_RAW_DATA, $iv);
                return \substr($text, 0, -16);
            }
            $iv = \str_repeat("\x00", \mcrypt_get_iv_size(\MCRYPT_RIJNDAEL_128, \MCRYPT_MODE_CBC));
            $text = \mcrypt_encrypt(\MCRYPT_RIJNDAEL_128, $key, $text, \MCRYPT_MODE_CBC, $iv);
            return $text;
        }
        /**
         * Returns the input text encrypted using RC4 algorithm and the specified key.
         * RC4 is the standard encryption algorithm used in PDF format
         * @param string $key Encryption key.
         * @param string $text Input text to be encrypted.
         * @param string $last_enc_key Reference to last RC4 key encrypted.
         * @param string $last_enc_key_c Reference to last RC4 computed key.
         * @return string encrypted text
         * @since 2.0.000 (2008-01-02)
         * @author Klemen Vodopivec, Nicola Asuni
         * @public static
         */
        public static function _RC4($key, $text, &$last_enc_key, &$last_enc_key_c)
        {
            if (\function_exists('mcrypt_encrypt') and $out = @\mcrypt_encrypt(\MCRYPT_ARCFOUR, $key, $text, \MCRYPT_MODE_STREAM, '')) {
                // try to use mcrypt function if exist
                return $out;
            }
            if ($last_enc_key != $key) {
                $k = \str_repeat($key, (int) (256 / \strlen($key) + 1));
                $rc4 = \range(0, 255);
                $j = 0;
                for ($i = 0; $i < 256; ++$i) {
                    $t = $rc4[$i];
                    $j = ($j + $t + \ord($k[$i])) % 256;
                    $rc4[$i] = $rc4[$j];
                    $rc4[$j] = $t;
                }
                $last_enc_key = $key;
                $last_enc_key_c = $rc4;
            } else {
                $rc4 = $last_enc_key_c;
            }
            $len = \strlen($text);
            $a = 0;
            $b = 0;
            $out = '';
            for ($i = 0; $i < $len; ++$i) {
                $a = ($a + 1) % 256;
                $t = $rc4[$a];
                $b = ($b + $t) % 256;
                $rc4[$a] = $rc4[$b];
                $rc4[$b] = $t;
                $k = $rc4[($rc4[$a] + $rc4[$b]) % 256];
                $out .= \chr(\ord($text[$i]) ^ $k);
            }
            return $out;
        }
        /**
         * Return the permission code used on encryption (P value).
         * @param array $permissions the set of permissions (specify the ones you want to block).
         * @param int $mode encryption strength: 0 = RC4 40 bit; 1 = RC4 128 bit; 2 = AES 128 bit; 3 = AES 256 bit.
         * @since 5.0.005 (2010-05-12)
         * @author Nicola Asuni
         * @public static
         */
        public static function getUserPermissionCode($permissions, $mode = 0)
        {
            $options = array(
                'owner' => 2,
                // bit 2 -- inverted logic: cleared by default
                'print' => 4,
                // bit 3
                'modify' => 8,
                // bit 4
                'copy' => 16,
                // bit 5
                'annot-forms' => 32,
                // bit 6
                'fill-forms' => 256,
                // bit 9
                'extract' => 512,
                // bit 10
                'assemble' => 1024,
                // bit 11
                'print-high' => 2048,
            );
            $protection = 2147422012;
            // 32 bit: (01111111 11111111 00001111 00111100)
            foreach ($permissions as $permission) {
                if (isset($options[$permission])) {
                    if ($mode > 0 or $options[$permission] <= 32) {
                        // set only valid permissions
                        if ($options[$permission] == 2) {
                            // the logic for bit 2 is inverted (cleared by default)
                            $protection += $options[$permission];
                        } else {
                            $protection -= $options[$permission];
                        }
                    }
                }
            }
            return $protection;
        }
        /**
         * Convert hexadecimal string to string
         * @param string $bs byte-string to convert
         * @return string
         * @since 5.0.005 (2010-05-12)
         * @author Nicola Asuni
         * @public static
         */
        public static function convertHexStringToString($bs)
        {
            $string = '';
            // string to be returned
            $bslength = \strlen($bs);
            if ($bslength % 2 != 0) {
                // padding
                $bs .= '0';
                ++$bslength;
            }
            for ($i = 0; $i < $bslength; $i += 2) {
                $string .= \chr(\hexdec($bs[$i] . $bs[$i + 1]));
            }
            return $string;
        }
        /**
         * Convert string to hexadecimal string (byte string)
         * @param string $s string to convert
         * @return string byte string
         * @since 5.0.010 (2010-05-17)
         * @author Nicola Asuni
         * @public static
         */
        public static function convertStringToHexString($s)
        {
            $bs = '';
            $chars = \preg_split('//', $s, -1, \PREG_SPLIT_NO_EMPTY);
            foreach ($chars as $c) {
                $bs .= \sprintf('%02s', \dechex(\ord($c)));
            }
            return $bs;
        }
        /**
         * Convert encryption P value to a string of bytes, low-order byte first.
         * @param string $protection 32bit encryption permission value (P value)
         * @return string
         * @since 5.0.005 (2010-05-12)
         * @author Nicola Asuni
         * @public static
         */
        public static function getEncPermissionsString($protection)
        {
            $binprot = \sprintf('%032b', $protection);
            $str = \chr(\bindec(\substr($binprot, 24, 8)));
            $str .= \chr(\bindec(\substr($binprot, 16, 8)));
            $str .= \chr(\bindec(\substr($binprot, 8, 8)));
            $str .= \chr(\bindec(\substr($binprot, 0, 8)));
            return $str;
        }
        /**
         * Encode a name object.
         * @param string $name Name object to encode.
         * @return string Encoded name object.
         * @author Nicola Asuni
         * @since 5.9.097 (2011-06-23)
         * @public static
         */
        public static function encodeNameObject($name)
        {
            $escname = '';
            $length = \strlen($name);
            for ($i = 0; $i < $length; ++$i) {
                $chr = $name[$i];
                if (\preg_match('/[0-9a-zA-Z#_=-]/', $chr) == 1) {
                    $escname .= $chr;
                } else {
                    $escname .= \sprintf('#%02X', \ord($chr));
                }
            }
            return $escname;
        }
        /**
         * Convert JavaScript form fields properties array to Annotation Properties array.
         * @param array $prop javascript field properties. Possible values are described on official Javascript for Acrobat API reference.
         * @param array $spot_colors Reference to spot colors array.
         * @param boolean $rtl True if in Right-To-Left text direction mode, false otherwise.
         * @return array of annotation properties
         * @author Nicola Asuni
         * @since 4.8.000 (2009-09-06)
         * @public static
         */
        public static function getAnnotOptFromJSProp($prop, &$spot_colors, $rtl = \false)
        {
            if (isset($prop['aopt']) and \is_array($prop['aopt'])) {
                // the annotation options are already defined
                return $prop['aopt'];
            }
            $opt = array();
            // value to be returned
            // alignment: Controls how the text is laid out within the text field.
            if (isset($prop['alignment'])) {
                switch ($prop['alignment']) {
                    case 'left':
                        $opt['q'] = 0;
                        break;
                    case 'center':
                        $opt['q'] = 1;
                        break;
                    case 'right':
                        $opt['q'] = 2;
                        break;
                    default:
                        $opt['q'] = $rtl ? 2 : 0;
                        break;
                }
            }
            // lineWidth: Specifies the thickness of the border when stroking the perimeter of a field's rectangle.
            if (isset($prop['lineWidth'])) {
                $linewidth = \intval($prop['lineWidth']);
            } else {
                $linewidth = 1;
            }
            // borderStyle: The border style for a field.
            if (isset($prop['borderStyle'])) {
                switch ($prop['borderStyle']) {
                    case 'border.d':
                    case 'dashed':
                        $opt['border'] = array(0, 0, $linewidth, array(3, 2));
                        $opt['bs'] = array('w' => $linewidth, 's' => 'D', 'd' => array(3, 2));
                        break;
                    case 'border.b':
                    case 'beveled':
                        $opt['border'] = array(0, 0, $linewidth);
                        $opt['bs'] = array('w' => $linewidth, 's' => 'B');
                        break;
                    case 'border.i':
                    case 'inset':
                        $opt['border'] = array(0, 0, $linewidth);
                        $opt['bs'] = array('w' => $linewidth, 's' => 'I');
                        break;
                    case 'border.u':
                    case 'underline':
                        $opt['border'] = array(0, 0, $linewidth);
                        $opt['bs'] = array('w' => $linewidth, 's' => 'U');
                        break;
                    case 'border.s':
                    case 'solid':
                        $opt['border'] = array(0, 0, $linewidth);
                        $opt['bs'] = array('w' => $linewidth, 's' => 'S');
                        break;
                    default:
                        break;
                }
            }
            if (isset($prop['border']) and \is_array($prop['border'])) {
                $opt['border'] = $prop['border'];
            }
            if (!isset($opt['mk'])) {
                $opt['mk'] = array();
            }
            if (!isset($opt['mk']['if'])) {
                $opt['mk']['if'] = array();
            }
            $opt['mk']['if']['a'] = array(0.5, 0.5);
            // buttonAlignX: Controls how space is distributed from the left of the button face with respect to the icon.
            if (isset($prop['buttonAlignX'])) {
                $opt['mk']['if']['a'][0] = $prop['buttonAlignX'];
            }
            // buttonAlignY: Controls how unused space is distributed from the bottom of the button face with respect to the icon.
            if (isset($prop['buttonAlignY'])) {
                $opt['mk']['if']['a'][1] = $prop['buttonAlignY'];
            }
            // buttonFitBounds: If true, the extent to which the icon may be scaled is set to the bounds of the button field.
            if (isset($prop['buttonFitBounds']) and $prop['buttonFitBounds'] == 'true') {
                $opt['mk']['if']['fb'] = \true;
            }
            // buttonScaleHow: Controls how the icon is scaled (if necessary) to fit inside the button face.
            if (isset($prop['buttonScaleHow'])) {
                switch ($prop['buttonScaleHow']) {
                    case 'scaleHow.proportional':
                        $opt['mk']['if']['s'] = 'P';
                        break;
                    case 'scaleHow.anamorphic':
                        $opt['mk']['if']['s'] = 'A';
                        break;
                }
            }
            // buttonScaleWhen: Controls when an icon is scaled to fit inside the button face.
            if (isset($prop['buttonScaleWhen'])) {
                switch ($prop['buttonScaleWhen']) {
                    case 'scaleWhen.always':
                        $opt['mk']['if']['sw'] = 'A';
                        break;
                    case 'scaleWhen.never':
                        $opt['mk']['if']['sw'] = 'N';
                        break;
                    case 'scaleWhen.tooBig':
                        $opt['mk']['if']['sw'] = 'B';
                        break;
                    case 'scaleWhen.tooSmall':
                        $opt['mk']['if']['sw'] = 'S';
                        break;
                }
            }
            // buttonPosition: Controls how the text and the icon of the button are positioned with respect to each other within the button face.
            if (isset($prop['buttonPosition'])) {
                switch ($prop['buttonPosition']) {
                    case 0:
                    case 'position.textOnly':
                        $opt['mk']['tp'] = 0;
                        break;
                    case 1:
                    case 'position.iconOnly':
                        $opt['mk']['tp'] = 1;
                        break;
                    case 2:
                    case 'position.iconTextV':
                        $opt['mk']['tp'] = 2;
                        break;
                    case 3:
                    case 'position.textIconV':
                        $opt['mk']['tp'] = 3;
                        break;
                    case 4:
                    case 'position.iconTextH':
                        $opt['mk']['tp'] = 4;
                        break;
                    case 5:
                    case 'position.textIconH':
                        $opt['mk']['tp'] = 5;
                        break;
                    case 6:
                    case 'position.overlay':
                        $opt['mk']['tp'] = 6;
                        break;
                }
            }
            // fillColor: Specifies the background color for a field.
            if (isset($prop['fillColor'])) {
                if (\is_array($prop['fillColor'])) {
                    $opt['mk']['bg'] = $prop['fillColor'];
                } else {
                    $opt['mk']['bg'] = \TCPDF_COLORS::convertHTMLColorToDec($prop['fillColor'], $spot_colors);
                }
            }
            // strokeColor: Specifies the stroke color for a field that is used to stroke the rectangle of the field with a line as large as the line width.
            if (isset($prop['strokeColor'])) {
                if (\is_array($prop['strokeColor'])) {
                    $opt['mk']['bc'] = $prop['strokeColor'];
                } else {
                    $opt['mk']['bc'] = \TCPDF_COLORS::convertHTMLColorToDec($prop['strokeColor'], $spot_colors);
                }
            }
            // rotation: The rotation of a widget in counterclockwise increments.
            if (isset($prop['rotation'])) {
                $opt['mk']['r'] = $prop['rotation'];
            }
            // charLimit: Limits the number of characters that a user can type into a text field.
            if (isset($prop['charLimit'])) {
                $opt['maxlen'] = \intval($prop['charLimit']);
            }
            $ff = 0;
            // readonly: The read-only characteristic of a field. If a field is read-only, the user can see the field but cannot change it.
            if (isset($prop['readonly']) and $prop['readonly'] == 'true') {
                $ff += 1 << 0;
            }
            // required: Specifies whether a field requires a value.
            if (isset($prop['required']) and $prop['required'] == 'true') {
                $ff += 1 << 1;
            }
            // multiline: Controls how text is wrapped within the field.
            if (isset($prop['multiline']) and $prop['multiline'] == 'true') {
                $ff += 1 << 12;
            }
            // password: Specifies whether the field should display asterisks when data is entered in the field.
            if (isset($prop['password']) and $prop['password'] == 'true') {
                $ff += 1 << 13;
            }
            // NoToggleToOff: If set, exactly one radio button shall be selected at all times; selecting the currently selected button has no effect.
            if (isset($prop['NoToggleToOff']) and $prop['NoToggleToOff'] == 'true') {
                $ff += 1 << 14;
            }
            // Radio: If set, the field is a set of radio buttons.
            if (isset($prop['Radio']) and $prop['Radio'] == 'true') {
                $ff += 1 << 15;
            }
            // Pushbutton: If set, the field is a pushbutton that does not retain a permanent value.
            if (isset($prop['Pushbutton']) and $prop['Pushbutton'] == 'true') {
                $ff += 1 << 16;
            }
            // Combo: If set, the field is a combo box; if clear, the field is a list box.
            if (isset($prop['Combo']) and $prop['Combo'] == 'true') {
                $ff += 1 << 17;
            }
            // editable: Controls whether a combo box is editable.
            if (isset($prop['editable']) and $prop['editable'] == 'true') {
                $ff += 1 << 18;
            }
            // Sort: If set, the field's option items shall be sorted alphabetically.
            if (isset($prop['Sort']) and $prop['Sort'] == 'true') {
                $ff += 1 << 19;
            }
            // fileSelect: If true, sets the file-select flag in the Options tab of the text field (Field is Used for File Selection).
            if (isset($prop['fileSelect']) and $prop['fileSelect'] == 'true') {
                $ff += 1 << 20;
            }
            // multipleSelection: If true, indicates that a list box allows a multiple selection of items.
            if (isset($prop['multipleSelection']) and $prop['multipleSelection'] == 'true') {
                $ff += 1 << 21;
            }
            // doNotSpellCheck: If true, spell checking is not performed on this editable text field.
            if (isset($prop['doNotSpellCheck']) and $prop['doNotSpellCheck'] == 'true') {
                $ff += 1 << 22;
            }
            // doNotScroll: If true, the text field does not scroll and the user, therefore, is limited by the rectangular region designed for the field.
            if (isset($prop['doNotScroll']) and $prop['doNotScroll'] == 'true') {
                $ff += 1 << 23;
            }
            // comb: If set to true, the field background is drawn as series of boxes (one for each character in the value of the field) and each character of the content is drawn within those boxes. The number of boxes drawn is determined from the charLimit property. It applies only to text fields. The setter will also raise if any of the following field properties are also set multiline, password, and fileSelect. A side-effect of setting this property is that the doNotScroll property is also set.
            if (isset($prop['comb']) and $prop['comb'] == 'true') {
                $ff += 1 << 24;
            }
            // radiosInUnison: If false, even if a group of radio buttons have the same name and export value, they behave in a mutually exclusive fashion, like HTML radio buttons.
            if (isset($prop['radiosInUnison']) and $prop['radiosInUnison'] == 'true') {
                $ff += 1 << 25;
            }
            // richText: If true, the field allows rich text formatting.
            if (isset($prop['richText']) and $prop['richText'] == 'true') {
                $ff += 1 << 25;
            }
            // commitOnSelChange: Controls whether a field value is committed after a selection change.
            if (isset($prop['commitOnSelChange']) and $prop['commitOnSelChange'] == 'true') {
                $ff += 1 << 26;
            }
            $opt['ff'] = $ff;
            // defaultValue: The default value of a field - that is, the value that the field is set to when the form is reset.
            if (isset($prop['defaultValue'])) {
                $opt['dv'] = $prop['defaultValue'];
            }
            $f = 4;
            // default value for annotation flags
            // readonly: The read-only characteristic of a field. If a field is read-only, the user can see the field but cannot change it.
            if (isset($prop['readonly']) and $prop['readonly'] == 'true') {
                $f += 1 << 6;
            }
            // display: Controls whether the field is hidden or visible on screen and in print.
            if (isset($prop['display'])) {
                if ($prop['display'] == 'display.visible') {
                    //
                } elseif ($prop['display'] == 'display.hidden') {
                    $f += 1 << 1;
                } elseif ($prop['display'] == 'display.noPrint') {
                    $f -= 1 << 2;
                } elseif ($prop['display'] == 'display.noView') {
                    $f += 1 << 5;
                }
            }
            $opt['f'] = $f;
            // currentValueIndices: Reads and writes single or multiple values of a list box or combo box.
            if (isset($prop['currentValueIndices']) and \is_array($prop['currentValueIndices'])) {
                $opt['i'] = $prop['currentValueIndices'];
            }
            // value: The value of the field data that the user has entered.
            if (isset($prop['value'])) {
                if (\is_array($prop['value'])) {
                    $opt['opt'] = array();
                    foreach ($prop['value'] as $key => $optval) {
                        // exportValues: An array of strings representing the export values for the field.
                        if (isset($prop['exportValues'][$key])) {
                            $opt['opt'][$key] = array($prop['exportValues'][$key], $prop['value'][$key]);
                        } else {
                            $opt['opt'][$key] = $prop['value'][$key];
                        }
                    }
                } else {
                    $opt['v'] = $prop['value'];
                }
            }
            // richValue: This property specifies the text contents and formatting of a rich text field.
            if (isset($prop['richValue'])) {
                $opt['rv'] = $prop['richValue'];
            }
            // submitName: If nonempty, used during form submission instead of name. Only applicable if submitting in HTML format (that is, URL-encoded).
            if (isset($prop['submitName'])) {
                $opt['tm'] = $prop['submitName'];
            }
            // name: Fully qualified field name.
            if (isset($prop['name'])) {
                $opt['t'] = $prop['name'];
            }
            // userName: The user name (short description string) of the field.
            if (isset($prop['userName'])) {
                $opt['tu'] = $prop['userName'];
            }
            // highlight: Defines how a button reacts when a user clicks it.
            if (isset($prop['highlight'])) {
                switch ($prop['highlight']) {
                    case 'none':
                    case 'highlight.n':
                        $opt['h'] = 'N';
                        break;
                    case 'invert':
                    case 'highlight.i':
                        $opt['h'] = 'i';
                        break;
                    case 'push':
                    case 'highlight.p':
                        $opt['h'] = 'P';
                        break;
                    case 'outline':
                    case 'highlight.o':
                        $opt['h'] = 'O';
                        break;
                }
            }
            // Unsupported options:
            // - calcOrderIndex: Changes the calculation order of fields in the document.
            // - delay: Delays the redrawing of a field's appearance.
            // - defaultStyle: This property defines the default style attributes for the form field.
            // - style: Allows the user to set the glyph style of a check box or radio button.
            // - textColor, textFont, textSize
            return $opt;
        }
        /**
         * Format the page numbers.
         * This method can be overridden for custom formats.
         * @param int $num page number
         * @return string
         * @since 4.2.005 (2008-11-06)
         * @public static
         */
        public static function formatPageNumber($num)
        {
            return \number_format((float) $num, 0, '', '.');
        }
        /**
         * Format the page numbers on the Table Of Content.
         * This method can be overridden for custom formats.
         * @param int $num page number
         * @return string
         * @since 4.5.001 (2009-01-04)
         * @see addTOC(), addHTMLTOC()
         * @public static
         */
        public static function formatTOCPageNumber($num)
        {
            return \number_format((float) $num, 0, '', '.');
        }
        /**
         * Extracts the CSS properties from a CSS string.
         * @param string $cssdata string containing CSS definitions.
         * @return array An array where the keys are the CSS selectors and the values are the CSS properties.
         * @author Nicola Asuni
         * @since 5.1.000 (2010-05-25)
         * @public static
         */
        public static function extractCSSproperties($cssdata)
        {
            if (empty($cssdata)) {
                return array();
            }
            // remove comments
            $cssdata = \preg_replace('/\\/\\*[^\\*]*\\*\\//', '', $cssdata);
            // remove newlines and multiple spaces
            $cssdata = \preg_replace('/[\\s]+/', ' ', $cssdata);
            // remove some spaces
            $cssdata = \preg_replace('/[\\s]*([;:\\{\\}]{1})[\\s]*/', '\\1', $cssdata);
            // remove empty blocks
            $cssdata = \preg_replace('/([^\\}\\{]+)\\{\\}/', '', $cssdata);
            // replace media type parenthesis
            $cssdata = \preg_replace('/@media[\\s]+([^\\{]*)\\{/i', '@media \\1§', $cssdata);
            $cssdata = \preg_replace('/\\}\\}/si', '}§', $cssdata);
            // trim string
            $cssdata = \trim($cssdata);
            // find media blocks (all, braille, embossed, handheld, print, projection, screen, speech, tty, tv)
            $cssblocks = array();
            $matches = array();
            if (\preg_match_all('/@media[\\s]+([^\\§]*)§([^§]*)§/i', $cssdata, $matches) > 0) {
                foreach ($matches[1] as $key => $type) {
                    $cssblocks[$type] = $matches[2][$key];
                }
                // remove media blocks
                $cssdata = \preg_replace('/@media[\\s]+([^\\§]*)§([^§]*)§/i', '', $cssdata);
            }
            // keep 'all' and 'print' media, other media types are discarded
            if (isset($cssblocks['all']) and !empty($cssblocks['all'])) {
                $cssdata .= $cssblocks['all'];
            }
            if (isset($cssblocks['print']) and !empty($cssblocks['print'])) {
                $cssdata .= $cssblocks['print'];
            }
            // reset css blocks array
            $cssblocks = array();
            $matches = array();
            // explode css data string into array
            if (\substr($cssdata, -1) == '}') {
                // remove last parethesis
                $cssdata = \substr($cssdata, 0, -1);
            }
            $matches = \explode('}', $cssdata);
            foreach ($matches as $key => $block) {
                // index 0 contains the CSS selector, index 1 contains CSS properties
                $cssblocks[$key] = \explode('{', $block);
                if (!isset($cssblocks[$key][1])) {
                    // remove empty definitions
                    unset($cssblocks[$key]);
                }
            }
            // split groups of selectors (comma-separated list of selectors)
            foreach ($cssblocks as $key => $block) {
                if (\strpos($block[0], ',') > 0) {
                    $selectors = \explode(',', $block[0]);
                    foreach ($selectors as $sel) {
                        $cssblocks[] = array(0 => \trim($sel), 1 => $block[1]);
                    }
                    unset($cssblocks[$key]);
                }
            }
            // covert array to selector => properties
            $cssdata = array();
            foreach ($cssblocks as $block) {
                $selector = $block[0];
                // calculate selector's specificity
                $matches = array();
                $a = 0;
                // the declaration is not from is a 'style' attribute
                $b = \intval(\preg_match_all('/[\\#]/', $selector, $matches));
                // number of ID attributes
                $c = \intval(\preg_match_all('/[\\[\\.]/', $selector, $matches));
                // number of other attributes
                $c += \intval(\preg_match_all('/[\\:]link|visited|hover|active|focus|target|lang|enabled|disabled|checked|indeterminate|root|nth|first|last|only|empty|contains|not/i', $selector, $matches));
                // number of pseudo-classes
                $d = \intval(\preg_match_all('/[\\>\\+\\~\\s]{1}[a-zA-Z0-9]+/', ' ' . $selector, $matches));
                // number of element names
                $d += \intval(\preg_match_all('/[\\:][\\:]/', $selector, $matches));
                // number of pseudo-elements
                $specificity = $a . $b . $c . $d;
                // add specificity to the beginning of the selector
                $cssdata[$specificity . ' ' . $selector] = $block[1];
            }
            // sort selectors alphabetically to account for specificity
            \ksort($cssdata, \SORT_STRING);
            // return array
            return $cssdata;
        }
        /**
         * Cleanup HTML code (requires HTML Tidy library).
         * @param string $html htmlcode to fix
         * @param string $default_css CSS commands to add
         * @param array|null $tagvs parameters for setHtmlVSpace method
         * @param array|null $tidy_options options for tidy_parse_string function
         * @param array $tagvspaces Array of vertical spaces for tags.
         * @return string XHTML code cleaned up
         * @author Nicola Asuni
         * @since 5.9.017 (2010-11-16)
         * @see setHtmlVSpace()
         * @public static
         */
        public static function fixHTMLCode($html, $default_css, $tagvs, $tidy_options, &$tagvspaces)
        {
            // configure parameters for HTML Tidy
            if (\TCPDF_STATIC::empty_string($tidy_options)) {
                $tidy_options = array('clean' => 1, 'drop-empty-paras' => 0, 'drop-proprietary-attributes' => 1, 'fix-backslash' => 1, 'hide-comments' => 1, 'join-styles' => 1, 'lower-literals' => 1, 'merge-divs' => 1, 'merge-spans' => 1, 'output-xhtml' => 1, 'word-2000' => 1, 'wrap' => 0, 'output-bom' => 0);
            }
            // clean up the HTML code
            $tidy = \tidy_parse_string($html, $tidy_options);
            // fix the HTML
            $tidy->cleanRepair();
            // get the CSS part
            $tidy_head = \tidy_get_head($tidy);
            $css = $tidy_head->value;
            $css = \preg_replace('/<style([^>]+)>/ims', '<style>', $css);
            $css = \preg_replace('/<\\/style>(.*)<style>/ims', "\n", $css);
            $css = \str_replace('/*<![CDATA[*/', '', $css);
            $css = \str_replace('/*]]>*/', '', $css);
            \preg_match('/<style>(.*)<\\/style>/ims', $css, $matches);
            if (isset($matches[1])) {
                $css = \strtolower($matches[1]);
            } else {
                $css = '';
            }
            // include default css
            $css = '<style>' . $default_css . $css . '</style>';
            // get the body part
            $tidy_body = \tidy_get_body($tidy);
            $html = $tidy_body->value;
            // fix some self-closing tags
            $html = \str_replace('<br>', '<br />', $html);
            // remove some empty tag blocks
            $html = \preg_replace('/<div([^\\>]*)><\\/div>/', '', $html);
            $html = \preg_replace('/<p([^\\>]*)><\\/p>/', '', $html);
            if (!\TCPDF_STATIC::empty_string($tagvs)) {
                // set vertical space for some XHTML tags
                $tagvspaces = $tagvs;
            }
            // return the cleaned XHTML code + CSS
            return $css . $html;
        }
        /**
         * Returns true if the CSS selector is valid for the selected HTML tag
         * @param array $dom array of HTML tags and properties
         * @param int $key key of the current HTML tag
         * @param string $selector CSS selector string
         * @return true if the selector is valid, false otherwise
         * @since 5.1.000 (2010-05-25)
         * @public static
         */
        public static function isValidCSSSelectorForTag($dom, $key, $selector)
        {
            $valid = \false;
            // value to be returned
            $tag = $dom[$key]['value'];
            $class = array();
            if (isset($dom[$key]['attribute']['class']) and !empty($dom[$key]['attribute']['class'])) {
                $class = \explode(' ', \strtolower($dom[$key]['attribute']['class']));
            }
            $id = '';
            if (isset($dom[$key]['attribute']['id']) and !empty($dom[$key]['attribute']['id'])) {
                $id = \strtolower($dom[$key]['attribute']['id']);
            }
            $selector = \preg_replace('/([\\>\\+\\~\\s]{1})([\\.]{1})([^\\>\\+\\~\\s]*)/si', '\\1*.\\3', $selector);
            $matches = array();
            if (\preg_match_all('/([\\>\\+\\~\\s]{1})([a-zA-Z0-9\\*]+)([^\\>\\+\\~\\s]*)/si', $selector, $matches, \PREG_PATTERN_ORDER | \PREG_OFFSET_CAPTURE) > 0) {
                $parentop = \array_pop($matches[1]);
                $operator = $parentop[0];
                $offset = $parentop[1];
                $lasttag = \array_pop($matches[2]);
                $lasttag = \strtolower(\trim($lasttag[0]));
                if ($lasttag == '*' or $lasttag == $tag) {
                    // the last element on selector is our tag or 'any tag'
                    $attrib = \array_pop($matches[3]);
                    $attrib = \strtolower(\trim($attrib[0]));
                    if (!empty($attrib)) {
                        // check if matches class, id, attribute, pseudo-class or pseudo-element
                        switch ($attrib[0]) {
                            case '.':
                                // class
                                if (\in_array(\substr($attrib, 1), $class)) {
                                    $valid = \true;
                                }
                                break;
                            case '#':
                                // ID
                                if (\substr($attrib, 1) == $id) {
                                    $valid = \true;
                                }
                                break;
                            case '[':
                                // attribute
                                $attrmatch = array();
                                if (\preg_match('/\\[([a-zA-Z0-9]*)[\\s]*([\\~\\^\\$\\*\\|\\=]*)[\\s]*["]?([^"\\]]*)["]?\\]/i', $attrib, $attrmatch) > 0) {
                                    $att = \strtolower($attrmatch[1]);
                                    $val = $attrmatch[3];
                                    if (isset($dom[$key]['attribute'][$att])) {
                                        switch ($attrmatch[2]) {
                                            case '=':
                                                if ($dom[$key]['attribute'][$att] == $val) {
                                                    $valid = \true;
                                                }
                                                break;
                                            case '~=':
                                                if (\in_array($val, \explode(' ', $dom[$key]['attribute'][$att]))) {
                                                    $valid = \true;
                                                }
                                                break;
                                            case '^=':
                                                if ($val == \substr($dom[$key]['attribute'][$att], 0, \strlen($val))) {
                                                    $valid = \true;
                                                }
                                                break;
                                            case '$=':
                                                if ($val == \substr($dom[$key]['attribute'][$att], -\strlen($val))) {
                                                    $valid = \true;
                                                }
                                                break;
                                            case '*=':
                                                if (\strpos($dom[$key]['attribute'][$att], $val) !== \false) {
                                                    $valid = \true;
                                                }
                                                break;
                                            case '|=':
                                                if ($dom[$key]['attribute'][$att] == $val) {
                                                    $valid = \true;
                                                } elseif (\preg_match('/' . $val . '[\\-]{1}/i', $dom[$key]['attribute'][$att]) > 0) {
                                                    $valid = \true;
                                                }
                                                break;
                                            default:
                                                $valid = \true;
                                        }
                                    }
                                }
                                break;
                            case ':':
                                // pseudo-class or pseudo-element
                                if ($attrib[1] == ':') {
                                    // pseudo-element
                                    // pseudo-elements are not supported!
                                    // (::first-line, ::first-letter, ::before, ::after)
                                } else {
                                    // pseudo-class
                                    // pseudo-classes are not supported!
                                    // (:root, :nth-child(n), :nth-last-child(n), :nth-of-type(n), :nth-last-of-type(n), :first-child, :last-child, :first-of-type, :last-of-type, :only-child, :only-of-type, :empty, :link, :visited, :active, :hover, :focus, :target, :lang(fr), :enabled, :disabled, :checked)
                                }
                                break;
                        }
                        // end of switch
                    } else {
                        $valid = \true;
                    }
                    if ($valid and $offset > 0) {
                        $valid = \false;
                        // check remaining selector part
                        $selector = \substr($selector, 0, $offset);
                        switch ($operator) {
                            case ' ':
                                // descendant of an element
                                while ($dom[$key]['parent'] > 0) {
                                    if (self::isValidCSSSelectorForTag($dom, $dom[$key]['parent'], $selector)) {
                                        $valid = \true;
                                        break;
                                    } else {
                                        $key = $dom[$key]['parent'];
                                    }
                                }
                                break;
                            case '>':
                                // child of an element
                                $valid = self::isValidCSSSelectorForTag($dom, $dom[$key]['parent'], $selector);
                                break;
                            case '+':
                                // immediately preceded by an element
                                for ($i = $key - 1; $i > $dom[$key]['parent']; --$i) {
                                    if ($dom[$i]['tag'] and $dom[$i]['opening']) {
                                        $valid = self::isValidCSSSelectorForTag($dom, $i, $selector);
                                        break;
                                    }
                                }
                                break;
                            case '~':
                                // preceded by an element
                                for ($i = $key - 1; $i > $dom[$key]['parent']; --$i) {
                                    if ($dom[$i]['tag'] and $dom[$i]['opening']) {
                                        if (self::isValidCSSSelectorForTag($dom, $i, $selector)) {
                                            break;
                                        }
                                    }
                                }
                                break;
                        }
                    }
                }
            }
            return $valid;
        }
        /**
         * Returns the styles array that apply for the selected HTML tag.
         * @param array $dom array of HTML tags and properties
         * @param int $key key of the current HTML tag
         * @param array $css array of CSS properties
         * @return array containing CSS properties
         * @since 5.1.000 (2010-05-25)
         * @public static
         */
        public static function getCSSdataArray($dom, $key, $css)
        {
            $cssarray = array();
            // style to be returned
            // get parent CSS selectors
            $selectors = array();
            if (isset($dom[$dom[$key]['parent']]['csssel'])) {
                $selectors = $dom[$dom[$key]['parent']]['csssel'];
            }
            // get all styles that apply
            foreach ($css as $selector => $style) {
                $pos = \strpos($selector, ' ');
                // get specificity
                $specificity = \substr($selector, 0, $pos);
                // remove specificity
                $selector = \substr($selector, $pos);
                // check if this selector apply to current tag
                if (self::isValidCSSSelectorForTag($dom, $key, $selector)) {
                    if (!\in_array($selector, $selectors)) {
                        // add style if not already added on parent selector
                        $cssarray[] = array('k' => $selector, 's' => $specificity, 'c' => $style);
                        $selectors[] = $selector;
                    }
                }
            }
            if (isset($dom[$key]['attribute']['style'])) {
                // attach inline style (latest properties have high priority)
                $cssarray[] = array('k' => '', 's' => '1000', 'c' => $dom[$key]['attribute']['style']);
            }
            // order the css array to account for specificity
            $cssordered = array();
            foreach ($cssarray as $key => $val) {
                $skey = \sprintf('%04d', $key);
                $cssordered[$val['s'] . '_' . $skey] = $val;
            }
            // sort selectors alphabetically to account for specificity
            \ksort($cssordered, \SORT_STRING);
            return array($selectors, $cssordered);
        }
        /**
         * Compact CSS data array into single string.
         * @param array $css array of CSS properties
         * @return string containing merged CSS properties
         * @since 5.9.070 (2011-04-19)
         * @public static
         */
        public static function getTagStyleFromCSSarray($css)
        {
            $tagstyle = '';
            // value to be returned
            foreach ($css as $style) {
                // split single css commands
                $csscmds = \explode(';', $style['c']);
                foreach ($csscmds as $cmd) {
                    if (!empty($cmd)) {
                        $pos = \strpos($cmd, ':');
                        if ($pos !== \false) {
                            $cmd = \substr($cmd, 0, $pos + 1);
                            if (\strpos($tagstyle, $cmd) !== \false) {
                                // remove duplicate commands (last commands have high priority)
                                $tagstyle = \preg_replace('/' . $cmd . '[^;]+/i', '', $tagstyle);
                            }
                        }
                    }
                }
                $tagstyle .= ';' . $style['c'];
            }
            // remove multiple semicolons
            $tagstyle = \preg_replace('/[;]+/', ';', $tagstyle);
            return $tagstyle;
        }
        /**
         * Returns the Roman representation of an integer number
         * @param int $number number to convert
         * @return string roman representation of the specified number
         * @since 4.4.004 (2008-12-10)
         * @public static
         */
        public static function intToRoman($number)
        {
            $roman = '';
            if ($number >= 4000) {
                // do not represent numbers above 4000 in Roman numerals
                return \strval($number);
            }
            while ($number >= 1000) {
                $roman .= 'M';
                $number -= 1000;
            }
            while ($number >= 900) {
                $roman .= 'CM';
                $number -= 900;
            }
            while ($number >= 500) {
                $roman .= 'D';
                $number -= 500;
            }
            while ($number >= 400) {
                $roman .= 'CD';
                $number -= 400;
            }
            while ($number >= 100) {
                $roman .= 'C';
                $number -= 100;
            }
            while ($number >= 90) {
                $roman .= 'XC';
                $number -= 90;
            }
            while ($number >= 50) {
                $roman .= 'L';
                $number -= 50;
            }
            while ($number >= 40) {
                $roman .= 'XL';
                $number -= 40;
            }
            while ($number >= 10) {
                $roman .= 'X';
                $number -= 10;
            }
            while ($number >= 9) {
                $roman .= 'IX';
                $number -= 9;
            }
            while ($number >= 5) {
                $roman .= 'V';
                $number -= 5;
            }
            while ($number >= 4) {
                $roman .= 'IV';
                $number -= 4;
            }
            while ($number >= 1) {
                $roman .= 'I';
                --$number;
            }
            return $roman;
        }
        /**
         * Find position of last occurrence of a substring in a string
         * @param string $haystack The string to search in.
         * @param string $needle substring to search.
         * @param int $offset May be specified to begin searching an arbitrary number of characters into the string.
         * @return int|false Returns the position where the needle exists. Returns FALSE if the needle was not found.
         * @since 4.8.038 (2010-03-13)
         * @public static
         */
        public static function revstrpos($haystack, $needle, $offset = 0)
        {
            $length = \strlen($haystack);
            $offset = $offset > 0 ? $length - $offset : \abs($offset);
            $pos = \strpos(\strrev($haystack), \strrev($needle), $offset);
            return $pos === \false ? \false : $length - $pos - \strlen($needle);
        }
        /**
         * Returns an array of hyphenation patterns.
         * @param string $file TEX file containing hypenation patterns. TEX patterns can be downloaded from http://www.ctan.org/tex-archive/language/hyph-utf8/tex/generic/hyph-utf8/patterns/
         * @return array of hyphenation patterns
         * @author Nicola Asuni
         * @since 4.9.012 (2010-04-12)
         * @public static
         */
        public static function getHyphenPatternsFromTEX($file)
        {
            // TEX patterns are available at:
            // http://www.ctan.org/tex-archive/language/hyph-utf8/tex/generic/hyph-utf8/patterns/
            $data = \file_get_contents($file);
            $patterns = array();
            // remove comments
            $data = \preg_replace('/\\%[^\\n]*/', '', $data);
            // extract the patterns part
            \preg_match('/\\\\patterns\\{([^\\}]*)\\}/i', $data, $matches);
            $data = \trim(\substr($matches[0], 10, -1));
            // extract each pattern
            $patterns_array = \preg_split('/[\\s]+/', $data);
            // create new language array of patterns
            $patterns = array();
            foreach ($patterns_array as $val) {
                if (!\TCPDF_STATIC::empty_string($val)) {
                    $val = \trim($val);
                    $val = \str_replace('\'', '\\\'', $val);
                    $key = \preg_replace('/[0-9]+/', '', $val);
                    $patterns[$key] = $val;
                }
            }
            return $patterns;
        }
        /**
         * Get the Path-Painting Operators.
         * @param string $style Style of rendering. Possible values are:
         * <ul>
         *   <li>S or D: Stroke the path.</li>
         *   <li>s or d: Close and stroke the path.</li>
         *   <li>f or F: Fill the path, using the nonzero winding number rule to determine the region to fill.</li>
         *   <li>f* or F*: Fill the path, using the even-odd rule to determine the region to fill.</li>
         *   <li>B or FD or DF: Fill and then stroke the path, using the nonzero winding number rule to determine the region to fill.</li>
         *   <li>B* or F*D or DF*: Fill and then stroke the path, using the even-odd rule to determine the region to fill.</li>
         *   <li>b or fd or df: Close, fill, and then stroke the path, using the nonzero winding number rule to determine the region to fill.</li>
         *   <li>b or f*d or df*: Close, fill, and then stroke the path, using the even-odd rule to determine the region to fill.</li>
         *   <li>CNZ: Clipping mode using the even-odd rule to determine which regions lie inside the clipping path.</li>
         *   <li>CEO: Clipping mode using the nonzero winding number rule to determine which regions lie inside the clipping path</li>
         *   <li>n: End the path object without filling or stroking it.</li>
         * </ul>
         * @param string $default default style
         * @return string
         * @author Nicola Asuni
         * @since 5.0.000 (2010-04-30)
         * @public static
         */
        public static function getPathPaintOperator($style, $default = 'S')
        {
            $op = '';
            switch ($style) {
                case 'S':
                case 'D':
                    $op = 'S';
                    break;
                case 's':
                case 'd':
                    $op = 's';
                    break;
                case 'f':
                case 'F':
                    $op = 'f';
                    break;
                case 'f*':
                case 'F*':
                    $op = 'f*';
                    break;
                case 'B':
                case 'FD':
                case 'DF':
                    $op = 'B';
                    break;
                case 'B*':
                case 'F*D':
                case 'DF*':
                    $op = 'B*';
                    break;
                case 'b':
                case 'fd':
                case 'df':
                    $op = 'b';
                    break;
                case 'b*':
                case 'f*d':
                case 'df*':
                    $op = 'b*';
                    break;
                case 'CNZ':
                    $op = 'W n';
                    break;
                case 'CEO':
                    $op = 'W* n';
                    break;
                case 'n':
                    $op = 'n';
                    break;
                default:
                    if (!empty($default)) {
                        $op = self::getPathPaintOperator($default, '');
                    } else {
                        $op = '';
                    }
            }
            return $op;
        }
        /**
         * Get the product of two SVG tranformation matrices
         * @param array $ta first SVG tranformation matrix
         * @param array $tb second SVG tranformation matrix
         * @return array transformation array
         * @author Nicola Asuni
         * @since 5.0.000 (2010-05-02)
         * @public static
         */
        public static function getTransformationMatrixProduct($ta, $tb)
        {
            $tm = array();
            $tm[0] = $ta[0] * $tb[0] + $ta[2] * $tb[1];
            $tm[1] = $ta[1] * $tb[0] + $ta[3] * $tb[1];
            $tm[2] = $ta[0] * $tb[2] + $ta[2] * $tb[3];
            $tm[3] = $ta[1] * $tb[2] + $ta[3] * $tb[3];
            $tm[4] = $ta[0] * $tb[4] + $ta[2] * $tb[5] + $ta[4];
            $tm[5] = $ta[1] * $tb[4] + $ta[3] * $tb[5] + $ta[5];
            return $tm;
        }
        /**
         * Get the tranformation matrix from SVG transform attribute
         * @param string $attribute transformation
         * @return array of transformations
         * @author Nicola Asuni
         * @since 5.0.000 (2010-05-02)
         * @public static
         */
        public static function getSVGTransformMatrix($attribute)
        {
            // identity matrix
            $tm = array(1, 0, 0, 1, 0, 0);
            $transform = array();
            if (\preg_match_all('/(matrix|translate|scale|rotate|skewX|skewY)[\\s]*\\(([^\\)]+)\\)/si', $attribute, $transform, \PREG_SET_ORDER) > 0) {
                foreach ($transform as $key => $data) {
                    if (!empty($data[2])) {
                        $a = 1;
                        $b = 0;
                        $c = 0;
                        $d = 1;
                        $e = 0;
                        $f = 0;
                        $regs = array();
                        switch ($data[1]) {
                            case 'matrix':
                                if (\preg_match('/([a-z0-9\\-\\.]+)[\\,\\s]+([a-z0-9\\-\\.]+)[\\,\\s]+([a-z0-9\\-\\.]+)[\\,\\s]+([a-z0-9\\-\\.]+)[\\,\\s]+([a-z0-9\\-\\.]+)[\\,\\s]+([a-z0-9\\-\\.]+)/si', $data[2], $regs)) {
                                    $a = $regs[1];
                                    $b = $regs[2];
                                    $c = $regs[3];
                                    $d = $regs[4];
                                    $e = $regs[5];
                                    $f = $regs[6];
                                }
                                break;
                            case 'translate':
                                if (\preg_match('/([a-z0-9\\-\\.]+)[\\,\\s]+([a-z0-9\\-\\.]+)/si', $data[2], $regs)) {
                                    $e = $regs[1];
                                    $f = $regs[2];
                                } elseif (\preg_match('/([a-z0-9\\-\\.]+)/si', $data[2], $regs)) {
                                    $e = $regs[1];
                                }
                                break;
                            case 'scale':
                                if (\preg_match('/([a-z0-9\\-\\.]+)[\\,\\s]+([a-z0-9\\-\\.]+)/si', $data[2], $regs)) {
                                    $a = $regs[1];
                                    $d = $regs[2];
                                } elseif (\preg_match('/([a-z0-9\\-\\.]+)/si', $data[2], $regs)) {
                                    $a = $regs[1];
                                    $d = $a;
                                }
                                break;
                            case 'rotate':
                                if (\preg_match('/([0-9\\-\\.]+)[\\,\\s]+([a-z0-9\\-\\.]+)[\\,\\s]+([a-z0-9\\-\\.]+)/si', $data[2], $regs)) {
                                    $ang = \deg2rad($regs[1]);
                                    $x = $regs[2];
                                    $y = $regs[3];
                                    $a = \cos($ang);
                                    $b = \sin($ang);
                                    $c = -$b;
                                    $d = $a;
                                    $e = $x * (1 - $a) - $y * $c;
                                    $f = $y * (1 - $d) - $x * $b;
                                } elseif (\preg_match('/([0-9\\-\\.]+)/si', $data[2], $regs)) {
                                    $ang = \deg2rad($regs[1]);
                                    $a = \cos($ang);
                                    $b = \sin($ang);
                                    $c = -$b;
                                    $d = $a;
                                    $e = 0;
                                    $f = 0;
                                }
                                break;
                            case 'skewX':
                                if (\preg_match('/([0-9\\-\\.]+)/si', $data[2], $regs)) {
                                    $c = \tan(\deg2rad($regs[1]));
                                }
                                break;
                            case 'skewY':
                                if (\preg_match('/([0-9\\-\\.]+)/si', $data[2], $regs)) {
                                    $b = \tan(\deg2rad($regs[1]));
                                }
                                break;
                        }
                        $tm = self::getTransformationMatrixProduct($tm, array($a, $b, $c, $d, $e, $f));
                    }
                }
            }
            return $tm;
        }
        /**
         * Returns the angle in radiants between two vectors
         * @param int $x1 X coordinate of first vector point
         * @param int $y1 Y coordinate of first vector point
         * @param int $x2 X coordinate of second vector point
         * @param int $y2 Y coordinate of second vector point
         * @author Nicola Asuni
         * @since 5.0.000 (2010-05-04)
         * @public static
         */
        public static function getVectorsAngle($x1, $y1, $x2, $y2)
        {
            $dprod = $x1 * $x2 + $y1 * $y2;
            $dist1 = \sqrt($x1 * $x1 + $y1 * $y1);
            $dist2 = \sqrt($x2 * $x2 + $y2 * $y2);
            $angle = \acos($dprod / ($dist1 * $dist2));
            if (\is_nan($angle)) {
                $angle = \M_PI;
            }
            if ($x1 * $y2 - $x2 * $y1 < 0) {
                $angle *= -1;
            }
            return $angle;
        }
        /**
         * Split string by a regular expression.
         * This is a wrapper for the preg_split function to avoid the bug: https://bugs.php.net/bug.php?id=45850
         * @param string $pattern The regular expression pattern to search for without the modifiers, as a string.
         * @param string $modifiers The modifiers part of the pattern,
         * @param string $subject The input string.
         * @param int $limit If specified, then only substrings up to limit are returned with the rest of the string being placed in the last substring. A limit of -1, 0 or NULL means "no limit" and, as is standard across PHP, you can use NULL to skip to the flags parameter.
         * @param int $flags The flags as specified on the preg_split PHP function.
         * @return array Returns an array containing substrings of subject split along boundaries matched by pattern.modifier
         * @author Nicola Asuni
         * @since 6.0.023
         * @public static
         */
        public static function pregSplit($pattern, $modifiers, $subject, $limit = NULL, $flags = NULL)
        {
            // PHP 8.1 deprecates nulls for $limit and $flags
            $limit = $limit === null ? -1 : $limit;
            $flags = $flags === null ? 0 : $flags;
            // the bug only happens on PHP 5.2 when using the u modifier
            if (\strpos($modifiers, 'u') === \FALSE or \count(\preg_split('//u', "\n\t", -1, \PREG_SPLIT_NO_EMPTY)) == 2) {
                $ret = \preg_split($pattern . $modifiers, $subject, $limit, $flags);
                if ($ret === \false) {
                    return array();
                }
                return \is_array($ret) ? $ret : array();
            }
            // preg_split is bugged - try alternative solution
            $ret = array();
            while (($nl = \strpos($subject, "\n")) !== \FALSE) {
                $ret = \array_merge($ret, \preg_split($pattern . $modifiers, \substr($subject, 0, $nl), $limit, $flags));
                $ret[] = "\n";
                $subject = \substr($subject, $nl + 1);
            }
            if (\strlen($subject) > 0) {
                $ret = \array_merge($ret, \preg_split($pattern . $modifiers, $subject, $limit, $flags));
            }
            return $ret;
        }
        /**
         * Wrapper to use fopen only with local files
         * @param string $filename Name of the file to open
         * @param string $mode
         * @return resource|false Returns a file pointer resource on success, or FALSE on error.
         * @public static
         */
        public static function fopenLocal($filename, $mode)
        {
            if (\strpos($filename, '://') === \false) {
                $filename = 'file://' . $filename;
            } elseif (\stream_is_local($filename) !== \true) {
                return \false;
            }
            return \fopen($filename, $mode);
        }
        /**
         * Check if the URL exist.
         * @param string $url URL to check.
         * @return bool Returns TRUE if the URL exists; FALSE otherwise.
         * @public static
         * @since 6.2.25
         */
        public static function url_exists($url)
        {
            $crs = \curl_init();
            $curlopts = [];
            if (\ini_get('open_basedir') == '' && (\ini_get('safe_mode') === '' || \ini_get('safe_mode') === \false)) {
                $curlopts[\CURLOPT_FOLLOWLOCATION] = \true;
            }
            $curlopts = \array_replace($curlopts, self::CURLOPT_DEFAULT);
            $curlopts = \array_replace($curlopts, \K_CURLOPTS);
            $curlopts = \array_replace($curlopts, self::CURLOPT_FIXED);
            $curlopts[\CURLOPT_URL] = $url;
            \curl_setopt_array($crs, $curlopts);
            \curl_exec($crs);
            $code = \curl_getinfo($crs, \CURLINFO_HTTP_CODE);
            \curl_close($crs);
            return $code == 200;
        }
        /**
         * Encode query params in URL
         *
         * @param string $url
         * @return string
         * @since 6.3.3 (2019-11-01)
         * @public static
         */
        public static function encodeUrlQuery($url)
        {
            $urlData = \parse_url($url);
            if (isset($urlData['query']) && $urlData['query']) {
                $urlQueryData = array();
                \parse_str(\urldecode($urlData['query']), $urlQueryData);
                $port = isset($urlData['port']) ? ':' . $urlData['port'] : '';
                $updatedUrl = $urlData['scheme'] . '://' . $urlData['host'] . $port . $urlData['path'] . '?' . \http_build_query($urlQueryData);
            } else {
                $updatedUrl = $url;
            }
            return $updatedUrl;
        }
        /**
         * Wrapper for file_exists.
         * Checks whether a file or directory exists.
         * Only allows some protocols and local files.
         * @param string $filename Path to the file or directory.
         * @return bool Returns TRUE if the file or directory specified by filename exists; FALSE otherwise.
         * @public static
         */
        public static function file_exists($filename)
        {
            if (\preg_match('|^https?://|', $filename) == 1) {
                return self::url_exists($filename);
            }
            if (\strpos($filename, '://')) {
                return \false;
                // only support http and https wrappers for security reasons
            }
            return @\file_exists($filename);
        }
        /**
         * Reads entire file into a string.
         * The file can be also an URL.
         * @param string $file Name of the file or URL to read.
         * @return string|false The function returns the read data or FALSE on failure.
         * @author Nicola Asuni
         * @since 6.0.025
         * @public static
         */
        public static function fileGetContents($file)
        {
            $alt = array($file);
            //
            if (\strlen($file) > 1 && $file[0] === '/' && $file[1] !== '/' && !empty($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] !== '/') {
                $findroot = \strpos($file, $_SERVER['DOCUMENT_ROOT']);
                if ($findroot === \false || $findroot > 1) {
                    $alt[] = \htmlspecialchars_decode(\urldecode($_SERVER['DOCUMENT_ROOT'] . $file));
                }
            }
            //
            $protocol = 'http';
            if (!empty($_SERVER['HTTPS']) && \strtolower($_SERVER['HTTPS']) != 'off') {
                $protocol .= 's';
            }
            //
            $url = $file;
            if (\preg_match('%^//%', $url) && !empty($_SERVER['HTTP_HOST'])) {
                $url = $protocol . ':' . \str_replace(' ', '%20', $url);
            }
            $url = \htmlspecialchars_decode($url);
            $alt[] = $url;
            //
            if (\preg_match('%^(https?)://%', $url) && empty($_SERVER['HTTP_HOST']) && empty($_SERVER['DOCUMENT_ROOT'])) {
                $urldata = \parse_url($url);
                if (empty($urldata['query'])) {
                    $host = $protocol . '://' . $_SERVER['HTTP_HOST'];
                    if (\strpos($url, $host) === 0) {
                        // convert URL to full server path
                        $tmp = \str_replace($host, $_SERVER['DOCUMENT_ROOT'], $url);
                        $alt[] = \htmlspecialchars_decode(\urldecode($tmp));
                    }
                }
            }
            //
            if (isset($_SERVER['SCRIPT_URI']) && !\preg_match('%^(https?|ftp)://%', $file) && !\preg_match('%^//%', $file)) {
                $urldata = @\parse_url($_SERVER['SCRIPT_URI']);
                $alt[] = $urldata['scheme'] . '://' . $urldata['host'] . ($file[0] == '/' ? '' : '/') . $file;
            }
            //
            $alt = \array_unique($alt);
            foreach ($alt as $path) {
                if (!self::file_exists($path)) {
                    continue;
                }
                $ret = @\file_get_contents($path);
                if ($ret != \false) {
                    return $ret;
                }
                // try to use CURL for URLs
                if (!\ini_get('allow_url_fopen') && \function_exists('curl_init') && \preg_match('%^(https?|ftp)://%', $path)) {
                    // try to get remote file data using cURL
                    $crs = \curl_init();
                    $curlopts = [];
                    if (\ini_get('open_basedir') == '' && (\ini_get('safe_mode') === '' || \ini_get('safe_mode') === \false)) {
                        $curlopts[\CURLOPT_FOLLOWLOCATION] = \true;
                    }
                    $curlopts = \array_replace($curlopts, self::CURLOPT_DEFAULT);
                    $curlopts = \array_replace($curlopts, \K_CURLOPTS);
                    $curlopts = \array_replace($curlopts, self::CURLOPT_FIXED);
                    $curlopts[\CURLOPT_URL] = $url;
                    \curl_setopt_array($crs, $curlopts);
                    $ret = \curl_exec($crs);
                    \curl_close($crs);
                    if ($ret !== \false) {
                        return $ret;
                    }
                }
            }
            return \false;
        }
        /**
         * Get ULONG from string (Big Endian 32-bit unsigned integer).
         * @param string $str string from where to extract value
         * @param int $offset point from where to read the data
         * @return int 32 bit value
         * @author Nicola Asuni
         * @since 5.2.000 (2010-06-02)
         * @public static
         */
        public static function _getULONG($str, $offset)
        {
            $v = \unpack('Ni', \substr($str, $offset, 4));
            return $v['i'];
        }
        /**
         * Get USHORT from string (Big Endian 16-bit unsigned integer).
         * @param string $str string from where to extract value
         * @param int $offset point from where to read the data
         * @return int 16 bit value
         * @author Nicola Asuni
         * @since 5.2.000 (2010-06-02)
         * @public static
         */
        public static function _getUSHORT($str, $offset)
        {
            $v = \unpack('ni', \substr($str, $offset, 2));
            return $v['i'];
        }
        /**
         * Get SHORT from string (Big Endian 16-bit signed integer).
         * @param string $str String from where to extract value.
         * @param int $offset Point from where to read the data.
         * @return int 16 bit value
         * @author Nicola Asuni
         * @since 5.2.000 (2010-06-02)
         * @public static
         */
        public static function _getSHORT($str, $offset)
        {
            $v = \unpack('si', \substr($str, $offset, 2));
            return $v['i'];
        }
        /**
         * Get FWORD from string (Big Endian 16-bit signed integer).
         * @param string $str String from where to extract value.
         * @param int $offset Point from where to read the data.
         * @return int 16 bit value
         * @author Nicola Asuni
         * @since 5.9.123 (2011-09-30)
         * @public static
         */
        public static function _getFWORD($str, $offset)
        {
            $v = self::_getUSHORT($str, $offset);
            if ($v > 0x7fff) {
                $v -= 0x10000;
            }
            return $v;
        }
        /**
         * Get UFWORD from string (Big Endian 16-bit unsigned integer).
         * @param string $str string from where to extract value
         * @param int $offset point from where to read the data
         * @return int 16 bit value
         * @author Nicola Asuni
         * @since 5.9.123 (2011-09-30)
         * @public static
         */
        public static function _getUFWORD($str, $offset)
        {
            $v = self::_getUSHORT($str, $offset);
            return $v;
        }
        /**
         * Get FIXED from string (32-bit signed fixed-point number (16.16).
         * @param string $str string from where to extract value
         * @param int $offset point from where to read the data
         * @return int 16 bit value
         * @author Nicola Asuni
         * @since 5.9.123 (2011-09-30)
         * @public static
         */
        public static function _getFIXED($str, $offset)
        {
            // mantissa
            $m = self::_getFWORD($str, $offset);
            // fraction
            $f = self::_getUSHORT($str, $offset + 2);
            $v = \floatval('' . $m . '.' . $f . '');
            return $v;
        }
        /**
         * Get BYTE from string (8-bit unsigned integer).
         * @param string $str String from where to extract value.
         * @param int $offset Point from where to read the data.
         * @return int 8 bit value
         * @author Nicola Asuni
         * @since 5.2.000 (2010-06-02)
         * @public static
         */
        public static function _getBYTE($str, $offset)
        {
            $v = \unpack('Ci', \substr($str, $offset, 1));
            return $v['i'];
        }
        /**
         * Binary-safe and URL-safe file read.
         * Reads up to length bytes from the file pointer referenced by handle. Reading stops as soon as one of the following conditions is met: length bytes have been read; EOF (end of file) is reached.
         * @param resource $handle
         * @param int $length
         * @return string|false Returns the read string or FALSE in case of error.
         * @author Nicola Asuni
         * @since 4.5.027 (2009-03-16)
         * @public static
         */
        public static function rfread($handle, $length)
        {
            $data = \fread($handle, $length);
            if ($data === \false) {
                return \false;
            }
            $rest = $length - \strlen($data);
            if ($rest > 0 && !\feof($handle)) {
                $data .= self::rfread($handle, $rest);
            }
            return $data;
        }
        /**
         * Read a 4-byte (32 bit) integer from file.
         * @param resource $f file resource.
         * @return int 4-byte integer
         * @public static
         */
        public static function _freadint($f)
        {
            $a = \unpack('Ni', \fread($f, 4));
            return $a['i'];
        }
        /**
         * Array of page formats
         * measures are calculated in this way: (inches * 72) or (millimeters * 72 / 25.4)
         * @public static
         *
         * @var array<string,float[]>
         */
        public static $page_formats = array(
            // ISO 216 A Series + 2 SIS 014711 extensions
            'A0' => array(2383.937, 3370.394),
            // = (  841 x 1189 ) mm  = ( 33.11 x 46.81 ) in
            'A1' => array(1683.78, 2383.937),
            // = (  594 x 841  ) mm  = ( 23.39 x 33.11 ) in
            'A2' => array(1190.551, 1683.78),
            // = (  420 x 594  ) mm  = ( 16.54 x 23.39 ) in
            'A3' => array(841.89, 1190.551),
            // = (  297 x 420  ) mm  = ( 11.69 x 16.54 ) in
            'A4' => array(595.276, 841.89),
            // = (  210 x 297  ) mm  = (  8.27 x 11.69 ) in
            'A5' => array(419.528, 595.276),
            // = (  148 x 210  ) mm  = (  5.83 x 8.27  ) in
            'A6' => array(297.638, 419.528),
            // = (  105 x 148  ) mm  = (  4.13 x 5.83  ) in
            'A7' => array(209.764, 297.638),
            // = (   74 x 105  ) mm  = (  2.91 x 4.13  ) in
            'A8' => array(147.402, 209.764),
            // = (   52 x 74   ) mm  = (  2.05 x 2.91  ) in
            'A9' => array(104.882, 147.402),
            // = (   37 x 52   ) mm  = (  1.46 x 2.05  ) in
            'A10' => array(73.70099999999999, 104.882),
            // = (   26 x 37   ) mm  = (  1.02 x 1.46  ) in
            'A11' => array(51.024, 73.70099999999999),
            // = (   18 x 26   ) mm  = (  0.71 x 1.02  ) in
            'A12' => array(36.85, 51.024),
            // = (   13 x 18   ) mm  = (  0.51 x 0.71  ) in
            // ISO 216 B Series + 2 SIS 014711 extensions
            'B0' => array(2834.646, 4008.189),
            // = ( 1000 x 1414 ) mm  = ( 39.37 x 55.67 ) in
            'B1' => array(2004.094, 2834.646),
            // = (  707 x 1000 ) mm  = ( 27.83 x 39.37 ) in
            'B2' => array(1417.323, 2004.094),
            // = (  500 x 707  ) mm  = ( 19.69 x 27.83 ) in
            'B3' => array(1000.63, 1417.323),
            // = (  353 x 500  ) mm  = ( 13.90 x 19.69 ) in
            'B4' => array(708.6609999999999, 1000.63),
            // = (  250 x 353  ) mm  = (  9.84 x 13.90 ) in
            'B5' => array(498.898, 708.6609999999999),
            // = (  176 x 250  ) mm  = (  6.93 x 9.84  ) in
            'B6' => array(354.331, 498.898),
            // = (  125 x 176  ) mm  = (  4.92 x 6.93  ) in
            'B7' => array(249.449, 354.331),
            // = (   88 x 125  ) mm  = (  3.46 x 4.92  ) in
            'B8' => array(175.748, 249.449),
            // = (   62 x 88   ) mm  = (  2.44 x 3.46  ) in
            'B9' => array(124.724, 175.748),
            // = (   44 x 62   ) mm  = (  1.73 x 2.44  ) in
            'B10' => array(87.874, 124.724),
            // = (   31 x 44   ) mm  = (  1.22 x 1.73  ) in
            'B11' => array(62.362, 87.874),
            // = (   22 x 31   ) mm  = (  0.87 x 1.22  ) in
            'B12' => array(42.52, 62.362),
            // = (   15 x 22   ) mm  = (  0.59 x 0.87  ) in
            // ISO 216 C Series + 2 SIS 014711 extensions + 5 EXTENSION
            'C0' => array(2599.37, 3676.535),
            // = (  917 x 1297 ) mm  = ( 36.10 x 51.06 ) in
            'C1' => array(1836.85, 2599.37),
            // = (  648 x 917  ) mm  = ( 25.51 x 36.10 ) in
            'C2' => array(1298.268, 1836.85),
            // = (  458 x 648  ) mm  = ( 18.03 x 25.51 ) in
            'C3' => array(918.425, 1298.268),
            // = (  324 x 458  ) mm  = ( 12.76 x 18.03 ) in
            'C4' => array(649.134, 918.425),
            // = (  229 x 324  ) mm  = (  9.02 x 12.76 ) in
            'C5' => array(459.213, 649.134),
            // = (  162 x 229  ) mm  = (  6.38 x 9.02  ) in
            'C6' => array(323.15, 459.213),
            // = (  114 x 162  ) mm  = (  4.49 x 6.38  ) in
            'C7' => array(229.606, 323.15),
            // = (   81 x 114  ) mm  = (  3.19 x 4.49  ) in
            'C8' => array(161.575, 229.606),
            // = (   57 x 81   ) mm  = (  2.24 x 3.19  ) in
            'C9' => array(113.386, 161.575),
            // = (   40 x 57   ) mm  = (  1.57 x 2.24  ) in
            'C10' => array(79.37, 113.386),
            // = (   28 x 40   ) mm  = (  1.10 x 1.57  ) in
            'C11' => array(56.693, 79.37),
            // = (   20 x 28   ) mm  = (  0.79 x 1.10  ) in
            'C12' => array(39.685, 56.693),
            // = (   14 x 20   ) mm  = (  0.55 x 0.79  ) in
            'C76' => array(229.606, 459.213),
            // = (   81 x 162  ) mm  = (  3.19 x 6.38  ) in
            'DL' => array(311.811, 623.622),
            // = (  110 x 220  ) mm  = (  4.33 x 8.66  ) in
            'DLE' => array(323.15, 637.795),
            // = (  114 x 225  ) mm  = (  4.49 x 8.86  ) in
            'DLX' => array(340.158, 666.1420000000001),
            // = (  120 x 235  ) mm  = (  4.72 x 9.25  ) in
            'DLP' => array(280.63, 595.276),
            // = (   99 x 210  ) mm  = (  3.90 x 8.27  ) in (1/3 A4)
            // SIS 014711 E Series
            'E0' => array(2491.654, 3517.795),
            // = (  879 x 1241 ) mm  = ( 34.61 x 48.86 ) in
            'E1' => array(1757.48, 2491.654),
            // = (  620 x 879  ) mm  = ( 24.41 x 34.61 ) in
            'E2' => array(1247.244, 1757.48),
            // = (  440 x 620  ) mm  = ( 17.32 x 24.41 ) in
            'E3' => array(878.74, 1247.244),
            // = (  310 x 440  ) mm  = ( 12.20 x 17.32 ) in
            'E4' => array(623.622, 878.74),
            // = (  220 x 310  ) mm  = (  8.66 x 12.20 ) in
            'E5' => array(439.37, 623.622),
            // = (  155 x 220  ) mm  = (  6.10 x 8.66  ) in
            'E6' => array(311.811, 439.37),
            // = (  110 x 155  ) mm  = (  4.33 x 6.10  ) in
            'E7' => array(221.102, 311.811),
            // = (   78 x 110  ) mm  = (  3.07 x 4.33  ) in
            'E8' => array(155.906, 221.102),
            // = (   55 x 78   ) mm  = (  2.17 x 3.07  ) in
            'E9' => array(110.551, 155.906),
            // = (   39 x 55   ) mm  = (  1.54 x 2.17  ) in
            'E10' => array(76.535, 110.551),
            // = (   27 x 39   ) mm  = (  1.06 x 1.54  ) in
            'E11' => array(53.858, 76.535),
            // = (   19 x 27   ) mm  = (  0.75 x 1.06  ) in
            'E12' => array(36.85, 53.858),
            // = (   13 x 19   ) mm  = (  0.51 x 0.75  ) in
            // SIS 014711 G Series
            'G0' => array(2715.591, 3838.11),
            // = (  958 x 1354 ) mm  = ( 37.72 x 53.31 ) in
            'G1' => array(1919.055, 2715.591),
            // = (  677 x 958  ) mm  = ( 26.65 x 37.72 ) in
            'G2' => array(1357.795, 1919.055),
            // = (  479 x 677  ) mm  = ( 18.86 x 26.65 ) in
            'G3' => array(958.11, 1357.795),
            // = (  338 x 479  ) mm  = ( 13.31 x 18.86 ) in
            'G4' => array(677.48, 958.11),
            // = (  239 x 338  ) mm  = (  9.41 x 13.31 ) in
            'G5' => array(479.055, 677.48),
            // = (  169 x 239  ) mm  = (  6.65 x 9.41  ) in
            'G6' => array(337.323, 479.055),
            // = (  119 x 169  ) mm  = (  4.69 x 6.65  ) in
            'G7' => array(238.11, 337.323),
            // = (   84 x 119  ) mm  = (  3.31 x 4.69  ) in
            'G8' => array(167.244, 238.11),
            // = (   59 x 84   ) mm  = (  2.32 x 3.31  ) in
            'G9' => array(119.055, 167.244),
            // = (   42 x 59   ) mm  = (  1.65 x 2.32  ) in
            'G10' => array(82.205, 119.055),
            // = (   29 x 42   ) mm  = (  1.14 x 1.65  ) in
            'G11' => array(59.528, 82.205),
            // = (   21 x 29   ) mm  = (  0.83 x 1.14  ) in
            'G12' => array(39.685, 59.528),
            // = (   14 x 21   ) mm  = (  0.55 x 0.83  ) in
            // ISO Press
            'RA0' => array(2437.795, 3458.268),
            // = (  860 x 1220 ) mm  = ( 33.86 x 48.03 ) in
            'RA1' => array(1729.134, 2437.795),
            // = (  610 x 860  ) mm  = ( 24.02 x 33.86 ) in
            'RA2' => array(1218.898, 1729.134),
            // = (  430 x 610  ) mm  = ( 16.93 x 24.02 ) in
            'RA3' => array(864.567, 1218.898),
            // = (  305 x 430  ) mm  = ( 12.01 x 16.93 ) in
            'RA4' => array(609.449, 864.567),
            // = (  215 x 305  ) mm  = (  8.46 x 12.01 ) in
            'SRA0' => array(2551.181, 3628.346),
            // = (  900 x 1280 ) mm  = ( 35.43 x 50.39 ) in
            'SRA1' => array(1814.173, 2551.181),
            // = (  640 x 900  ) mm  = ( 25.20 x 35.43 ) in
            'SRA2' => array(1275.591, 1814.173),
            // = (  450 x 640  ) mm  = ( 17.72 x 25.20 ) in
            'SRA3' => array(907.087, 1275.591),
            // = (  320 x 450  ) mm  = ( 12.60 x 17.72 ) in
            'SRA4' => array(637.795, 907.087),
            // = (  225 x 320  ) mm  = (  8.86 x 12.60 ) in
            // German DIN 476
            '4A0' => array(4767.874, 6740.787),
            // = ( 1682 x 2378 ) mm  = ( 66.22 x 93.62 ) in
            '2A0' => array(3370.394, 4767.874),
            // = ( 1189 x 1682 ) mm  = ( 46.81 x 66.22 ) in
            // Variations on the ISO Standard
            'A2_EXTRA' => array(1261.417, 1754.646),
            // = (  445 x 619  ) mm  = ( 17.52 x 24.37 ) in
            'A3+' => array(932.598, 1369.134),
            // = (  329 x 483  ) mm  = ( 12.95 x 19.02 ) in
            'A3_EXTRA' => array(912.756, 1261.417),
            // = (  322 x 445  ) mm  = ( 12.68 x 17.52 ) in
            'A3_SUPER' => array(864.567, 1440.0),
            // = (  305 x 508  ) mm  = ( 12.01 x 20.00 ) in
            'SUPER_A3' => array(864.567, 1380.472),
            // = (  305 x 487  ) mm  = ( 12.01 x 19.17 ) in
            'A4_EXTRA' => array(666.1420000000001, 912.756),
            // = (  235 x 322  ) mm  = (  9.25 x 12.68 ) in
            'A4_SUPER' => array(649.134, 912.756),
            // = (  229 x 322  ) mm  = (  9.02 x 12.68 ) in
            'SUPER_A4' => array(643.465, 1009.134),
            // = (  227 x 356  ) mm  = (  8.94 x 14.02 ) in
            'A4_LONG' => array(595.276, 986.457),
            // = (  210 x 348  ) mm  = (  8.27 x 13.70 ) in
            'F4' => array(595.276, 935.433),
            // = (  210 x 330  ) mm  = (  8.27 x 12.99 ) in
            'SO_B5_EXTRA' => array(572.598, 782.362),
            // = (  202 x 276  ) mm  = (  7.95 x 10.87 ) in
            'A5_EXTRA' => array(490.394, 666.1420000000001),
            // = (  173 x 235  ) mm  = (  6.81 x 9.25  ) in
            // ANSI Series
            'ANSI_E' => array(2448.0, 3168.0),
            // = (  864 x 1118 ) mm  = ( 34.00 x 44.00 ) in
            'ANSI_D' => array(1584.0, 2448.0),
            // = (  559 x 864  ) mm  = ( 22.00 x 34.00 ) in
            'ANSI_C' => array(1224.0, 1584.0),
            // = (  432 x 559  ) mm  = ( 17.00 x 22.00 ) in
            'ANSI_B' => array(792.0, 1224.0),
            // = (  279 x 432  ) mm  = ( 11.00 x 17.00 ) in
            'ANSI_A' => array(612.0, 792.0),
            // = (  216 x 279  ) mm  = (  8.50 x 11.00 ) in
            // Traditional 'Loose' North American Paper Sizes
            'USLEDGER' => array(1224.0, 792.0),
            // = (  432 x 279  ) mm  = ( 17.00 x 11.00 ) in
            'LEDGER' => array(1224.0, 792.0),
            // = (  432 x 279  ) mm  = ( 17.00 x 11.00 ) in
            'ORGANIZERK' => array(792.0, 1224.0),
            // = (  279 x 432  ) mm  = ( 11.00 x 17.00 ) in
            'BIBLE' => array(792.0, 1224.0),
            // = (  279 x 432  ) mm  = ( 11.00 x 17.00 ) in
            'USTABLOID' => array(792.0, 1224.0),
            // = (  279 x 432  ) mm  = ( 11.00 x 17.00 ) in
            'TABLOID' => array(792.0, 1224.0),
            // = (  279 x 432  ) mm  = ( 11.00 x 17.00 ) in
            'ORGANIZERM' => array(612.0, 792.0),
            // = (  216 x 279  ) mm  = (  8.50 x 11.00 ) in
            'USLETTER' => array(612.0, 792.0),
            // = (  216 x 279  ) mm  = (  8.50 x 11.00 ) in
            'LETTER' => array(612.0, 792.0),
            // = (  216 x 279  ) mm  = (  8.50 x 11.00 ) in
            'USLEGAL' => array(612.0, 1008.0),
            // = (  216 x 356  ) mm  = (  8.50 x 14.00 ) in
            'LEGAL' => array(612.0, 1008.0),
            // = (  216 x 356  ) mm  = (  8.50 x 14.00 ) in
            'GOVERNMENTLETTER' => array(576.0, 756.0),
            // = (  203 x 267  ) mm  = (  8.00 x 10.50 ) in
            'GLETTER' => array(576.0, 756.0),
            // = (  203 x 267  ) mm  = (  8.00 x 10.50 ) in
            'JUNIORLEGAL' => array(576.0, 360.0),
            // = (  203 x 127  ) mm  = (  8.00 x 5.00  ) in
            'JLEGAL' => array(576.0, 360.0),
            // = (  203 x 127  ) mm  = (  8.00 x 5.00  ) in
            // Other North American Paper Sizes
            'QUADDEMY' => array(2520.0, 3240.0),
            // = (  889 x 1143 ) mm  = ( 35.00 x 45.00 ) in
            'SUPER_B' => array(936.0, 1368.0),
            // = (  330 x 483  ) mm  = ( 13.00 x 19.00 ) in
            'QUARTO' => array(648.0, 792.0),
            // = (  229 x 279  ) mm  = (  9.00 x 11.00 ) in
            'GOVERNMENTLEGAL' => array(612.0, 936.0),
            // = (  216 x 330  ) mm  = (  8.50 x 13.00 ) in
            'FOLIO' => array(612.0, 936.0),
            // = (  216 x 330  ) mm  = (  8.50 x 13.00 ) in
            'MONARCH' => array(522.0, 756.0),
            // = (  184 x 267  ) mm  = (  7.25 x 10.50 ) in
            'EXECUTIVE' => array(522.0, 756.0),
            // = (  184 x 267  ) mm  = (  7.25 x 10.50 ) in
            'ORGANIZERL' => array(396.0, 612.0),
            // = (  140 x 216  ) mm  = (  5.50 x 8.50  ) in
            'STATEMENT' => array(396.0, 612.0),
            // = (  140 x 216  ) mm  = (  5.50 x 8.50  ) in
            'MEMO' => array(396.0, 612.0),
            // = (  140 x 216  ) mm  = (  5.50 x 8.50  ) in
            'FOOLSCAP' => array(595.4400000000001, 936.0),
            // = (  210 x 330  ) mm  = (  8.27 x 13.00 ) in
            'COMPACT' => array(306.0, 486.0),
            // = (  108 x 171  ) mm  = (  4.25 x 6.75  ) in
            'ORGANIZERJ' => array(198.0, 360.0),
            // = (   70 x 127  ) mm  = (  2.75 x 5.00  ) in
            // Canadian standard CAN 2-9.60M
            'P1' => array(1587.402, 2437.795),
            // = (  560 x 860  ) mm  = ( 22.05 x 33.86 ) in
            'P2' => array(1218.898, 1587.402),
            // = (  430 x 560  ) mm  = ( 16.93 x 22.05 ) in
            'P3' => array(793.701, 1218.898),
            // = (  280 x 430  ) mm  = ( 11.02 x 16.93 ) in
            'P4' => array(609.449, 793.701),
            // = (  215 x 280  ) mm  = (  8.46 x 11.02 ) in
            'P5' => array(396.85, 609.449),
            // = (  140 x 215  ) mm  = (  5.51 x 8.46  ) in
            'P6' => array(303.307, 396.85),
            // = (  107 x 140  ) mm  = (  4.21 x 5.51  ) in
            // North American Architectural Sizes
            'ARCH_E' => array(2592.0, 3456.0),
            // = (  914 x 1219 ) mm  = ( 36.00 x 48.00 ) in
            'ARCH_E1' => array(2160.0, 3024.0),
            // = (  762 x 1067 ) mm  = ( 30.00 x 42.00 ) in
            'ARCH_D' => array(1728.0, 2592.0),
            // = (  610 x 914  ) mm  = ( 24.00 x 36.00 ) in
            'BROADSHEET' => array(1296.0, 1728.0),
            // = (  457 x 610  ) mm  = ( 18.00 x 24.00 ) in
            'ARCH_C' => array(1296.0, 1728.0),
            // = (  457 x 610  ) mm  = ( 18.00 x 24.00 ) in
            'ARCH_B' => array(864.0, 1296.0),
            // = (  305 x 457  ) mm  = ( 12.00 x 18.00 ) in
            'ARCH_A' => array(648.0, 864.0),
            // = (  229 x 305  ) mm  = (  9.00 x 12.00 ) in
            // -- North American Envelope Sizes
            // - Announcement Envelopes
            'ANNENV_A2' => array(314.64, 414.0),
            // = (  111 x 146  ) mm  = (  4.37 x 5.75  ) in
            'ANNENV_A6' => array(342.0, 468.0),
            // = (  121 x 165  ) mm  = (  4.75 x 6.50  ) in
            'ANNENV_A7' => array(378.0, 522.0),
            // = (  133 x 184  ) mm  = (  5.25 x 7.25  ) in
            'ANNENV_A8' => array(396.0, 584.64),
            // = (  140 x 206  ) mm  = (  5.50 x 8.12  ) in
            'ANNENV_A10' => array(450.0, 692.64),
            // = (  159 x 244  ) mm  = (  6.25 x 9.62  ) in
            'ANNENV_SLIM' => array(278.64, 638.64),
            // = (   98 x 225  ) mm  = (  3.87 x 8.87  ) in
            // - Commercial Envelopes
            'COMMENV_N6_1/4' => array(252.0, 432.0),
            // = (   89 x 152  ) mm  = (  3.50 x 6.00  ) in
            'COMMENV_N6_3/4' => array(260.64, 468.0),
            // = (   92 x 165  ) mm  = (  3.62 x 6.50  ) in
            'COMMENV_N8' => array(278.64, 540.0),
            // = (   98 x 191  ) mm  = (  3.87 x 7.50  ) in
            'COMMENV_N9' => array(278.64, 638.64),
            // = (   98 x 225  ) mm  = (  3.87 x 8.87  ) in
            'COMMENV_N10' => array(296.64, 684.0),
            // = (  105 x 241  ) mm  = (  4.12 x 9.50  ) in
            'COMMENV_N11' => array(324.0, 746.64),
            // = (  114 x 263  ) mm  = (  4.50 x 10.37 ) in
            'COMMENV_N12' => array(342.0, 792.0),
            // = (  121 x 279  ) mm  = (  4.75 x 11.00 ) in
            'COMMENV_N14' => array(360.0, 828.0),
            // = (  127 x 292  ) mm  = (  5.00 x 11.50 ) in
            // - Catalogue Envelopes
            'CATENV_N1' => array(432.0, 648.0),
            // = (  152 x 229  ) mm  = (  6.00 x 9.00  ) in
            'CATENV_N1_3/4' => array(468.0, 684.0),
            // = (  165 x 241  ) mm  = (  6.50 x 9.50  ) in
            'CATENV_N2' => array(468.0, 720.0),
            // = (  165 x 254  ) mm  = (  6.50 x 10.00 ) in
            'CATENV_N3' => array(504.0, 720.0),
            // = (  178 x 254  ) mm  = (  7.00 x 10.00 ) in
            'CATENV_N6' => array(540.0, 756.0),
            // = (  191 x 267  ) mm  = (  7.50 x 10.50 ) in
            'CATENV_N7' => array(576.0, 792.0),
            // = (  203 x 279  ) mm  = (  8.00 x 11.00 ) in
            'CATENV_N8' => array(594.0, 810.0),
            // = (  210 x 286  ) mm  = (  8.25 x 11.25 ) in
            'CATENV_N9_1/2' => array(612.0, 756.0),
            // = (  216 x 267  ) mm  = (  8.50 x 10.50 ) in
            'CATENV_N9_3/4' => array(630.0, 810.0),
            // = (  222 x 286  ) mm  = (  8.75 x 11.25 ) in
            'CATENV_N10_1/2' => array(648.0, 864.0),
            // = (  229 x 305  ) mm  = (  9.00 x 12.00 ) in
            'CATENV_N12_1/2' => array(684.0, 900.0),
            // = (  241 x 318  ) mm  = (  9.50 x 12.50 ) in
            'CATENV_N13_1/2' => array(720.0, 936.0),
            // = (  254 x 330  ) mm  = ( 10.00 x 13.00 ) in
            'CATENV_N14_1/4' => array(810.0, 882.0),
            // = (  286 x 311  ) mm  = ( 11.25 x 12.25 ) in
            'CATENV_N14_1/2' => array(828.0, 1044.0),
            // = (  292 x 368  ) mm  = ( 11.50 x 14.50 ) in
            // Japanese (JIS P 0138-61) Standard B-Series
            'JIS_B0' => array(2919.685, 4127.244),
            // = ( 1030 x 1456 ) mm  = ( 40.55 x 57.32 ) in
            'JIS_B1' => array(2063.622, 2919.685),
            // = (  728 x 1030 ) mm  = ( 28.66 x 40.55 ) in
            'JIS_B2' => array(1459.843, 2063.622),
            // = (  515 x 728  ) mm  = ( 20.28 x 28.66 ) in
            'JIS_B3' => array(1031.811, 1459.843),
            // = (  364 x 515  ) mm  = ( 14.33 x 20.28 ) in
            'JIS_B4' => array(728.504, 1031.811),
            // = (  257 x 364  ) mm  = ( 10.12 x 14.33 ) in
            'JIS_B5' => array(515.9059999999999, 728.504),
            // = (  182 x 257  ) mm  = (  7.17 x 10.12 ) in
            'JIS_B6' => array(362.835, 515.9059999999999),
            // = (  128 x 182  ) mm  = (  5.04 x 7.17  ) in
            'JIS_B7' => array(257.953, 362.835),
            // = (   91 x 128  ) mm  = (  3.58 x 5.04  ) in
            'JIS_B8' => array(181.417, 257.953),
            // = (   64 x 91   ) mm  = (  2.52 x 3.58  ) in
            'JIS_B9' => array(127.559, 181.417),
            // = (   45 x 64   ) mm  = (  1.77 x 2.52  ) in
            'JIS_B10' => array(90.709, 127.559),
            // = (   32 x 45   ) mm  = (  1.26 x 1.77  ) in
            'JIS_B11' => array(62.362, 90.709),
            // = (   22 x 32   ) mm  = (  0.87 x 1.26  ) in
            'JIS_B12' => array(45.354, 62.362),
            // = (   16 x 22   ) mm  = (  0.63 x 0.87  ) in
            // PA Series
            'PA0' => array(2381.102, 3174.803),
            // = (  840 x 1120 ) mm  = ( 33.07 x 44.09 ) in
            'PA1' => array(1587.402, 2381.102),
            // = (  560 x 840  ) mm  = ( 22.05 x 33.07 ) in
            'PA2' => array(1190.551, 1587.402),
            // = (  420 x 560  ) mm  = ( 16.54 x 22.05 ) in
            'PA3' => array(793.701, 1190.551),
            // = (  280 x 420  ) mm  = ( 11.02 x 16.54 ) in
            'PA4' => array(595.276, 793.701),
            // = (  210 x 280  ) mm  = (  8.27 x 11.02 ) in
            'PA5' => array(396.85, 595.276),
            // = (  140 x 210  ) mm  = (  5.51 x 8.27  ) in
            'PA6' => array(297.638, 396.85),
            // = (  105 x 140  ) mm  = (  4.13 x 5.51  ) in
            'PA7' => array(198.425, 297.638),
            // = (   70 x 105  ) mm  = (  2.76 x 4.13  ) in
            'PA8' => array(147.402, 198.425),
            // = (   52 x 70   ) mm  = (  2.05 x 2.76  ) in
            'PA9' => array(99.21299999999999, 147.402),
            // = (   35 x 52   ) mm  = (  1.38 x 2.05  ) in
            'PA10' => array(73.70099999999999, 99.21299999999999),
            // = (   26 x 35   ) mm  = (  1.02 x 1.38  ) in
            // Standard Photographic Print Sizes
            'PASSPORT_PHOTO' => array(99.21299999999999, 127.559),
            // = (   35 x 45   ) mm  = (  1.38 x 1.77  ) in
            'E' => array(233.858, 340.157),
            // = (   82 x 120  ) mm  = (  3.25 x 4.72  ) in
            'L' => array(252.283, 360.0),
            // = (   89 x 127  ) mm  = (  3.50 x 5.00  ) in
            '3R' => array(252.283, 360.0),
            // = (   89 x 127  ) mm  = (  3.50 x 5.00  ) in
            'KG' => array(289.134, 430.866),
            // = (  102 x 152  ) mm  = (  4.02 x 5.98  ) in
            '4R' => array(289.134, 430.866),
            // = (  102 x 152  ) mm  = (  4.02 x 5.98  ) in
            '4D' => array(340.157, 430.866),
            // = (  120 x 152  ) mm  = (  4.72 x 5.98  ) in
            '2L' => array(360.0, 504.567),
            // = (  127 x 178  ) mm  = (  5.00 x 7.01  ) in
            '5R' => array(360.0, 504.567),
            // = (  127 x 178  ) mm  = (  5.00 x 7.01  ) in
            '8P' => array(430.866, 575.433),
            // = (  152 x 203  ) mm  = (  5.98 x 7.99  ) in
            '6R' => array(430.866, 575.433),
            // = (  152 x 203  ) mm  = (  5.98 x 7.99  ) in
            '6P' => array(575.433, 720.0),
            // = (  203 x 254  ) mm  = (  7.99 x 10.00 ) in
            '8R' => array(575.433, 720.0),
            // = (  203 x 254  ) mm  = (  7.99 x 10.00 ) in
            '6PW' => array(575.433, 864.567),
            // = (  203 x 305  ) mm  = (  7.99 x 12.01 ) in
            'S8R' => array(575.433, 864.567),
            // = (  203 x 305  ) mm  = (  7.99 x 12.01 ) in
            '4P' => array(720.0, 864.567),
            // = (  254 x 305  ) mm  = ( 10.00 x 12.01 ) in
            '10R' => array(720.0, 864.567),
            // = (  254 x 305  ) mm  = ( 10.00 x 12.01 ) in
            '4PW' => array(720.0, 1080.0),
            // = (  254 x 381  ) mm  = ( 10.00 x 15.00 ) in
            'S10R' => array(720.0, 1080.0),
            // = (  254 x 381  ) mm  = ( 10.00 x 15.00 ) in
            '11R' => array(790.866, 1009.134),
            // = (  279 x 356  ) mm  = ( 10.98 x 14.02 ) in
            'S11R' => array(790.866, 1224.567),
            // = (  279 x 432  ) mm  = ( 10.98 x 17.01 ) in
            '12R' => array(864.567, 1080.0),
            // = (  305 x 381  ) mm  = ( 12.01 x 15.00 ) in
            'S12R' => array(864.567, 1292.598),
            // = (  305 x 456  ) mm  = ( 12.01 x 17.95 ) in
            // Common Newspaper Sizes
            'NEWSPAPER_BROADSHEET' => array(2125.984, 1700.787),
            // = (  750 x 600  ) mm  = ( 29.53 x 23.62 ) in
            'NEWSPAPER_BERLINER' => array(1332.283, 892.913),
            // = (  470 x 315  ) mm  = ( 18.50 x 12.40 ) in
            'NEWSPAPER_TABLOID' => array(1218.898, 793.701),
            // = (  430 x 280  ) mm  = ( 16.93 x 11.02 ) in
            'NEWSPAPER_COMPACT' => array(1218.898, 793.701),
            // = (  430 x 280  ) mm  = ( 16.93 x 11.02 ) in
            // Business Cards
            'CREDIT_CARD' => array(153.014, 242.646),
            // = (   54 x 86   ) mm  = (  2.13 x 3.37  ) in
            'BUSINESS_CARD' => array(153.014, 242.646),
            // = (   54 x 86   ) mm  = (  2.13 x 3.37  ) in
            'BUSINESS_CARD_ISO7810' => array(153.014, 242.646),
            // = (   54 x 86   ) mm  = (  2.13 x 3.37  ) in
            'BUSINESS_CARD_ISO216' => array(147.402, 209.764),
            // = (   52 x 74   ) mm  = (  2.05 x 2.91  ) in
            'BUSINESS_CARD_IT' => array(155.906, 240.945),
            // = (   55 x 85   ) mm  = (  2.17 x 3.35  ) in
            'BUSINESS_CARD_UK' => array(155.906, 240.945),
            // = (   55 x 85   ) mm  = (  2.17 x 3.35  ) in
            'BUSINESS_CARD_FR' => array(155.906, 240.945),
            // = (   55 x 85   ) mm  = (  2.17 x 3.35  ) in
            'BUSINESS_CARD_DE' => array(155.906, 240.945),
            // = (   55 x 85   ) mm  = (  2.17 x 3.35  ) in
            'BUSINESS_CARD_ES' => array(155.906, 240.945),
            // = (   55 x 85   ) mm  = (  2.17 x 3.35  ) in
            'BUSINESS_CARD_CA' => array(144.567, 252.283),
            // = (   51 x 89   ) mm  = (  2.01 x 3.50  ) in
            'BUSINESS_CARD_US' => array(144.567, 252.283),
            // = (   51 x 89   ) mm  = (  2.01 x 3.50  ) in
            'BUSINESS_CARD_JP' => array(155.906, 257.953),
            // = (   55 x 91   ) mm  = (  2.17 x 3.58  ) in
            'BUSINESS_CARD_HK' => array(153.071, 255.118),
            // = (   54 x 90   ) mm  = (  2.13 x 3.54  ) in
            'BUSINESS_CARD_AU' => array(155.906, 255.118),
            // = (   55 x 90   ) mm  = (  2.17 x 3.54  ) in
            'BUSINESS_CARD_DK' => array(155.906, 255.118),
            // = (   55 x 90   ) mm  = (  2.17 x 3.54  ) in
            'BUSINESS_CARD_SE' => array(155.906, 255.118),
            // = (   55 x 90   ) mm  = (  2.17 x 3.54  ) in
            'BUSINESS_CARD_RU' => array(141.732, 255.118),
            // = (   50 x 90   ) mm  = (  1.97 x 3.54  ) in
            'BUSINESS_CARD_CZ' => array(141.732, 255.118),
            // = (   50 x 90   ) mm  = (  1.97 x 3.54  ) in
            'BUSINESS_CARD_FI' => array(141.732, 255.118),
            // = (   50 x 90   ) mm  = (  1.97 x 3.54  ) in
            'BUSINESS_CARD_HU' => array(141.732, 255.118),
            // = (   50 x 90   ) mm  = (  1.97 x 3.54  ) in
            'BUSINESS_CARD_IL' => array(141.732, 255.118),
            // = (   50 x 90   ) mm  = (  1.97 x 3.54  ) in
            // Billboards
            '4SHEET' => array(2880.0, 4320.0),
            // = ( 1016 x 1524 ) mm  = ( 40.00 x 60.00 ) in
            '6SHEET' => array(3401.575, 5102.362),
            // = ( 1200 x 1800 ) mm  = ( 47.24 x 70.87 ) in
            '12SHEET' => array(8640.0, 4320.0),
            // = ( 3048 x 1524 ) mm  = (120.00 x 60.00 ) in
            '16SHEET' => array(5760.0, 8640.0),
            // = ( 2032 x 3048 ) mm  = ( 80.00 x 120.00) in
            '32SHEET' => array(11520.0, 8640.0),
            // = ( 4064 x 3048 ) mm  = (160.00 x 120.00) in
            '48SHEET' => array(17280.0, 8640.0),
            // = ( 6096 x 3048 ) mm  = (240.00 x 120.00) in
            '64SHEET' => array(23040.0, 8640.0),
            // = ( 8128 x 3048 ) mm  = (320.00 x 120.00) in
            '96SHEET' => array(34560.0, 8640.0),
            // = (12192 x 3048 ) mm  = (480.00 x 120.00) in
            // -- Old European Sizes
            // - Old Imperial English Sizes
            'EN_EMPEROR' => array(3456.0, 5184.0),
            // = ( 1219 x 1829 ) mm  = ( 48.00 x 72.00 ) in
            'EN_ANTIQUARIAN' => array(2232.0, 3816.0),
            // = (  787 x 1346 ) mm  = ( 31.00 x 53.00 ) in
            'EN_GRAND_EAGLE' => array(2070.0, 3024.0),
            // = (  730 x 1067 ) mm  = ( 28.75 x 42.00 ) in
            'EN_DOUBLE_ELEPHANT' => array(1926.0, 2880.0),
            // = (  679 x 1016 ) mm  = ( 26.75 x 40.00 ) in
            'EN_ATLAS' => array(1872.0, 2448.0),
            // = (  660 x 864  ) mm  = ( 26.00 x 34.00 ) in
            'EN_COLOMBIER' => array(1692.0, 2484.0),
            // = (  597 x 876  ) mm  = ( 23.50 x 34.50 ) in
            'EN_ELEPHANT' => array(1656.0, 2016.0),
            // = (  584 x 711  ) mm  = ( 23.00 x 28.00 ) in
            'EN_DOUBLE_DEMY' => array(1620.0, 2556.0),
            // = (  572 x 902  ) mm  = ( 22.50 x 35.50 ) in
            'EN_IMPERIAL' => array(1584.0, 2160.0),
            // = (  559 x 762  ) mm  = ( 22.00 x 30.00 ) in
            'EN_PRINCESS' => array(1548.0, 2016.0),
            // = (  546 x 711  ) mm  = ( 21.50 x 28.00 ) in
            'EN_CARTRIDGE' => array(1512.0, 1872.0),
            // = (  533 x 660  ) mm  = ( 21.00 x 26.00 ) in
            'EN_DOUBLE_LARGE_POST' => array(1512.0, 2376.0),
            // = (  533 x 838  ) mm  = ( 21.00 x 33.00 ) in
            'EN_ROYAL' => array(1440.0, 1800.0),
            // = (  508 x 635  ) mm  = ( 20.00 x 25.00 ) in
            'EN_SHEET' => array(1404.0, 1692.0),
            // = (  495 x 597  ) mm  = ( 19.50 x 23.50 ) in
            'EN_HALF_POST' => array(1404.0, 1692.0),
            // = (  495 x 597  ) mm  = ( 19.50 x 23.50 ) in
            'EN_SUPER_ROYAL' => array(1368.0, 1944.0),
            // = (  483 x 686  ) mm  = ( 19.00 x 27.00 ) in
            'EN_DOUBLE_POST' => array(1368.0, 2196.0),
            // = (  483 x 775  ) mm  = ( 19.00 x 30.50 ) in
            'EN_MEDIUM' => array(1260.0, 1656.0),
            // = (  445 x 584  ) mm  = ( 17.50 x 23.00 ) in
            'EN_DEMY' => array(1260.0, 1620.0),
            // = (  445 x 572  ) mm  = ( 17.50 x 22.50 ) in
            'EN_LARGE_POST' => array(1188.0, 1512.0),
            // = (  419 x 533  ) mm  = ( 16.50 x 21.00 ) in
            'EN_COPY_DRAUGHT' => array(1152.0, 1440.0),
            // = (  406 x 508  ) mm  = ( 16.00 x 20.00 ) in
            'EN_POST' => array(1116.0, 1386.0),
            // = (  394 x 489  ) mm  = ( 15.50 x 19.25 ) in
            'EN_CROWN' => array(1080.0, 1440.0),
            // = (  381 x 508  ) mm  = ( 15.00 x 20.00 ) in
            'EN_PINCHED_POST' => array(1062.0, 1332.0),
            // = (  375 x 470  ) mm  = ( 14.75 x 18.50 ) in
            'EN_BRIEF' => array(972.0, 1152.0),
            // = (  343 x 406  ) mm  = ( 13.50 x 16.00 ) in
            'EN_FOOLSCAP' => array(972.0, 1224.0),
            // = (  343 x 432  ) mm  = ( 13.50 x 17.00 ) in
            'EN_SMALL_FOOLSCAP' => array(954.0, 1188.0),
            // = (  337 x 419  ) mm  = ( 13.25 x 16.50 ) in
            'EN_POTT' => array(900.0, 1080.0),
            // = (  318 x 381  ) mm  = ( 12.50 x 15.00 ) in
            // - Old Imperial Belgian Sizes
            'BE_GRAND_AIGLE' => array(1984.252, 2948.031),
            // = (  700 x 1040 ) mm  = ( 27.56 x 40.94 ) in
            'BE_COLOMBIER' => array(1757.48, 2409.449),
            // = (  620 x 850  ) mm  = ( 24.41 x 33.46 ) in
            'BE_DOUBLE_CARRE' => array(1757.48, 2607.874),
            // = (  620 x 920  ) mm  = ( 24.41 x 36.22 ) in
            'BE_ELEPHANT' => array(1746.142, 2182.677),
            // = (  616 x 770  ) mm  = ( 24.25 x 30.31 ) in
            'BE_PETIT_AIGLE' => array(1700.787, 2381.102),
            // = (  600 x 840  ) mm  = ( 23.62 x 33.07 ) in
            'BE_GRAND_JESUS' => array(1559.055, 2069.291),
            // = (  550 x 730  ) mm  = ( 21.65 x 28.74 ) in
            'BE_JESUS' => array(1530.709, 2069.291),
            // = (  540 x 730  ) mm  = ( 21.26 x 28.74 ) in
            'BE_RAISIN' => array(1417.323, 1842.52),
            // = (  500 x 650  ) mm  = ( 19.69 x 25.59 ) in
            'BE_GRAND_MEDIAN' => array(1303.937, 1714.961),
            // = (  460 x 605  ) mm  = ( 18.11 x 23.82 ) in
            'BE_DOUBLE_POSTE' => array(1233.071, 1601.575),
            // = (  435 x 565  ) mm  = ( 17.13 x 22.24 ) in
            'BE_COQUILLE' => array(1218.898, 1587.402),
            // = (  430 x 560  ) mm  = ( 16.93 x 22.05 ) in
            'BE_PETIT_MEDIAN' => array(1176.378, 1502.362),
            // = (  415 x 530  ) mm  = ( 16.34 x 20.87 ) in
            'BE_RUCHE' => array(1020.472, 1303.937),
            // = (  360 x 460  ) mm  = ( 14.17 x 18.11 ) in
            'BE_PROPATRIA' => array(977.953, 1218.898),
            // = (  345 x 430  ) mm  = ( 13.58 x 16.93 ) in
            'BE_LYS' => array(898.583, 1125.354),
            // = (  317 x 397  ) mm  = ( 12.48 x 15.63 ) in
            'BE_POT' => array(870.236, 1088.504),
            // = (  307 x 384  ) mm  = ( 12.09 x 15.12 ) in
            'BE_ROSETTE' => array(765.354, 983.622),
            // = (  270 x 347  ) mm  = ( 10.63 x 13.66 ) in
            // - Old Imperial French Sizes
            'FR_UNIVERS' => array(2834.646, 3685.039),
            // = ( 1000 x 1300 ) mm  = ( 39.37 x 51.18 ) in
            'FR_DOUBLE_COLOMBIER' => array(2551.181, 3571.654),
            // = (  900 x 1260 ) mm  = ( 35.43 x 49.61 ) in
            'FR_GRANDE_MONDE' => array(2551.181, 3571.654),
            // = (  900 x 1260 ) mm  = ( 35.43 x 49.61 ) in
            'FR_DOUBLE_SOLEIL' => array(2267.717, 3401.575),
            // = (  800 x 1200 ) mm  = ( 31.50 x 47.24 ) in
            'FR_DOUBLE_JESUS' => array(2154.331, 3174.803),
            // = (  760 x 1120 ) mm  = ( 29.92 x 44.09 ) in
            'FR_GRAND_AIGLE' => array(2125.984, 3004.724),
            // = (  750 x 1060 ) mm  = ( 29.53 x 41.73 ) in
            'FR_PETIT_AIGLE' => array(1984.252, 2664.567),
            // = (  700 x 940  ) mm  = ( 27.56 x 37.01 ) in
            'FR_DOUBLE_RAISIN' => array(1842.52, 2834.646),
            // = (  650 x 1000 ) mm  = ( 25.59 x 39.37 ) in
            'FR_JOURNAL' => array(1842.52, 2664.567),
            // = (  650 x 940  ) mm  = ( 25.59 x 37.01 ) in
            'FR_COLOMBIER_AFFICHE' => array(1785.827, 2551.181),
            // = (  630 x 900  ) mm  = ( 24.80 x 35.43 ) in
            'FR_DOUBLE_CAVALIER' => array(1757.48, 2607.874),
            // = (  620 x 920  ) mm  = ( 24.41 x 36.22 ) in
            'FR_CLOCHE' => array(1700.787, 2267.717),
            // = (  600 x 800  ) mm  = ( 23.62 x 31.50 ) in
            'FR_SOLEIL' => array(1700.787, 2267.717),
            // = (  600 x 800  ) mm  = ( 23.62 x 31.50 ) in
            'FR_DOUBLE_CARRE' => array(1587.402, 2551.181),
            // = (  560 x 900  ) mm  = ( 22.05 x 35.43 ) in
            'FR_DOUBLE_COQUILLE' => array(1587.402, 2494.488),
            // = (  560 x 880  ) mm  = ( 22.05 x 34.65 ) in
            'FR_JESUS' => array(1587.402, 2154.331),
            // = (  560 x 760  ) mm  = ( 22.05 x 29.92 ) in
            'FR_RAISIN' => array(1417.323, 1842.52),
            // = (  500 x 650  ) mm  = ( 19.69 x 25.59 ) in
            'FR_CAVALIER' => array(1303.937, 1757.48),
            // = (  460 x 620  ) mm  = ( 18.11 x 24.41 ) in
            'FR_DOUBLE_COURONNE' => array(1303.937, 2040.945),
            // = (  460 x 720  ) mm  = ( 18.11 x 28.35 ) in
            'FR_CARRE' => array(1275.591, 1587.402),
            // = (  450 x 560  ) mm  = ( 17.72 x 22.05 ) in
            'FR_COQUILLE' => array(1247.244, 1587.402),
            // = (  440 x 560  ) mm  = ( 17.32 x 22.05 ) in
            'FR_DOUBLE_TELLIERE' => array(1247.244, 1927.559),
            // = (  440 x 680  ) mm  = ( 17.32 x 26.77 ) in
            'FR_DOUBLE_CLOCHE' => array(1133.858, 1700.787),
            // = (  400 x 600  ) mm  = ( 15.75 x 23.62 ) in
            'FR_DOUBLE_POT' => array(1133.858, 1757.48),
            // = (  400 x 620  ) mm  = ( 15.75 x 24.41 ) in
            'FR_ECU' => array(1133.858, 1474.016),
            // = (  400 x 520  ) mm  = ( 15.75 x 20.47 ) in
            'FR_COURONNE' => array(1020.472, 1303.937),
            // = (  360 x 460  ) mm  = ( 14.17 x 18.11 ) in
            'FR_TELLIERE' => array(963.78, 1247.244),
            // = (  340 x 440  ) mm  = ( 13.39 x 17.32 ) in
            'FR_POT' => array(878.74, 1133.858),
        );
        /**
         * Get page dimensions from format name.
         * @param mixed $format The format name @see self::$page_format<ul>
         * @return array containing page width and height in points
         * @since 5.0.010 (2010-05-17)
         * @public static
         */
        public static function getPageSizeFromFormat($format)
        {
            if (isset(self::$page_formats[$format])) {
                return self::$page_formats[$format];
            }
            return self::$page_formats['A4'];
        }
        /**
         * Set page boundaries.
         * @param int $page page number
         * @param string $type valid values are: <ul><li>'MediaBox' : the boundaries of the physical medium on which the page shall be displayed or printed;</li><li>'CropBox' : the visible region of default user space;</li><li>'BleedBox' : the region to which the contents of the page shall be clipped when output in a production environment;</li><li>'TrimBox' : the intended dimensions of the finished page after trimming;</li><li>'ArtBox' : the page's meaningful content (including potential white space).</li></ul>
         * @param float $llx lower-left x coordinate in user units.
         * @param float $lly lower-left y coordinate in user units.
         * @param float $urx upper-right x coordinate in user units.
         * @param float $ury upper-right y coordinate in user units.
         * @param boolean $points If true uses user units as unit of measure, otherwise uses PDF points.
         * @param float $k Scale factor (number of points in user unit).
         * @param array $pagedim Array of page dimensions.
         * @return array pagedim array of page dimensions.
         * @since 5.0.010 (2010-05-17)
         * @public static
         */
        public static function setPageBoxes($page, $type, $llx, $lly, $urx, $ury, $points, $k, $pagedim = array())
        {
            if (!isset($pagedim[$page])) {
                // initialize array
                $pagedim[$page] = array();
            }
            if (!\in_array($type, self::$pageboxes)) {
                return;
            }
            if ($points) {
                $k = 1;
            }
            $pagedim[$page][$type]['llx'] = $llx * $k;
            $pagedim[$page][$type]['lly'] = $lly * $k;
            $pagedim[$page][$type]['urx'] = $urx * $k;
            $pagedim[$page][$type]['ury'] = $ury * $k;
            return $pagedim;
        }
        /**
         * Swap X and Y coordinates of page boxes (change page boxes orientation).
         * @param int $page page number
         * @param array $pagedim Array of page dimensions.
         * @return array pagedim array of page dimensions.
         * @since 5.0.010 (2010-05-17)
         * @public static
         */
        public static function swapPageBoxCoordinates($page, $pagedim)
        {
            foreach (self::$pageboxes as $type) {
                // swap X and Y coordinates
                if (isset($pagedim[$page][$type])) {
                    $tmp = $pagedim[$page][$type]['llx'];
                    $pagedim[$page][$type]['llx'] = $pagedim[$page][$type]['lly'];
                    $pagedim[$page][$type]['lly'] = $tmp;
                    $tmp = $pagedim[$page][$type]['urx'];
                    $pagedim[$page][$type]['urx'] = $pagedim[$page][$type]['ury'];
                    $pagedim[$page][$type]['ury'] = $tmp;
                }
            }
            return $pagedim;
        }
        /**
         * Get the canonical page layout mode.
         * @param string $layout The page layout. Possible values are:<ul><li>SinglePage Display one page at a time</li><li>OneColumn Display the pages in one column</li><li>TwoColumnLeft Display the pages in two columns, with odd-numbered pages on the left</li><li>TwoColumnRight Display the pages in two columns, with odd-numbered pages on the right</li><li>TwoPageLeft (PDF 1.5) Display the pages two at a time, with odd-numbered pages on the left</li><li>TwoPageRight (PDF 1.5) Display the pages two at a time, with odd-numbered pages on the right</li></ul>
         * @return string Canonical page layout name.
         * @public static
         */
        public static function getPageLayoutMode($layout = 'SinglePage')
        {
            switch ($layout) {
                case 'default':
                case 'single':
                case 'SinglePage':
                    $layout_mode = 'SinglePage';
                    break;
                case 'continuous':
                case 'OneColumn':
                    $layout_mode = 'OneColumn';
                    break;
                case 'two':
                case 'TwoColumnLeft':
                    $layout_mode = 'TwoColumnLeft';
                    break;
                case 'TwoColumnRight':
                    $layout_mode = 'TwoColumnRight';
                    break;
                case 'TwoPageLeft':
                    $layout_mode = 'TwoPageLeft';
                    break;
                case 'TwoPageRight':
                    $layout_mode = 'TwoPageRight';
                    break;
                default:
                    $layout_mode = 'SinglePage';
            }
            return $layout_mode;
        }
        /**
         * Get the canonical page layout mode.
         * @param string $mode A name object specifying how the document should be displayed when opened:<ul><li>UseNone Neither document outline nor thumbnail images visible</li><li>UseOutlines Document outline visible</li><li>UseThumbs Thumbnail images visible</li><li>FullScreen Full-screen mode, with no menu bar, window controls, or any other window visible</li><li>UseOC (PDF 1.5) Optional content group panel visible</li><li>UseAttachments (PDF 1.6) Attachments panel visible</li></ul>
         * @return string Canonical page mode name.
         * @public static
         */
        public static function getPageMode($mode = 'UseNone')
        {
            switch ($mode) {
                case 'UseNone':
                    $page_mode = 'UseNone';
                    break;
                case 'UseOutlines':
                    $page_mode = 'UseOutlines';
                    break;
                case 'UseThumbs':
                    $page_mode = 'UseThumbs';
                    break;
                case 'FullScreen':
                    $page_mode = 'FullScreen';
                    break;
                case 'UseOC':
                    $page_mode = 'UseOC';
                    break;
                case '':
                    $page_mode = 'UseAttachments';
                    break;
                default:
                    $page_mode = 'UseNone';
            }
            return $page_mode;
        }
    }
    // END OF TCPDF_STATIC CLASS
    //============================================================+
    // END OF FILE
    //============================================================+
}

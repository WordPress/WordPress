<?php
/**
 * Random_* Compatibility Library 
 * for using the new PHP 7 random_* API in PHP 5 projects
 * 
 * The MIT License (MIT)
 * 
 * Copyright (c) 2015 Paragon Initiative Enterprises
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

if (!defined('PHP_VERSION_ID')) {
    // This constant was introduced in PHP 5.2.7
    $RandomCompatversion = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($RandomCompatversion[0] * 10000 + $RandomCompatversion[1] * 100 + $RandomCompatversion[2]));
    $RandomCompatversion = null;
}
if (PHP_VERSION_ID < 70000) {
    if (!defined('RANDOM_COMPAT_READ_BUFFER')) {
        define('RANDOM_COMPAT_READ_BUFFER', 8);
    }
    $RandomCompatDIR = dirname(__FILE__);
    require_once $RandomCompatDIR.'/byte_safe_strings.php';
    require_once $RandomCompatDIR.'/cast_to_int.php';
    require_once $RandomCompatDIR.'/error_polyfill.php';
    if (!function_exists('random_bytes')) {
        /**
         * PHP 5.2.0 - 5.6.x way to implement random_bytes()
         * 
         * We use conditional statements here to define the function in accordance
         * to the operating environment. It's a micro-optimization.
         * 
         * In order of preference:
         *   1. Use libsodium if available.
         *   2. fread() /dev/urandom if available (never on Windows)
         *   3. mcrypt_create_iv($bytes, MCRYPT_CREATE_IV)
         *   4. COM('CAPICOM.Utilities.1')->GetRandom()
         *   5. openssl_random_pseudo_bytes() (absolute last resort)
         * 
         * See ERRATA.md for our reasoning behind this particular order
         */
        if (PHP_VERSION_ID >= 50300 && extension_loaded('libsodium') && function_exists('\\Sodium\\randombytes_buf')) {
            // See random_bytes_libsodium.php
            require_once $RandomCompatDIR.'/random_bytes_libsodium.php';
        }
        if (
            !function_exists('random_bytes') && 
            DIRECTORY_SEPARATOR === '/' &&
            @is_readable('/dev/urandom')
        ) {
            // DIRECTORY_SEPARATOR === '/' on Unix-like OSes -- this is a fast
            // way to exclude Windows.
            // 
            // Error suppression on is_readable() in case of an open_basedir or 
            // safe_mode failure. All we care about is whether or not we can 
            // read it at this point. If the PHP environment is going to panic 
            // over trying to see if the file can be read in the first place,
            // that is not helpful to us here.
            
            // See random_bytes_dev_urandom.php
            require_once $RandomCompatDIR.'/random_bytes_dev_urandom.php';
        }
        if (
            !function_exists('random_bytes') &&
            PHP_VERSION_ID >= 50307 &&
            extension_loaded('mcrypt')
        ) {
            // See random_bytes_mcrypt.php
            require_once $RandomCompatDIR.'/random_bytes_mcrypt.php';
        }
        if (
            !function_exists('random_bytes') && 
            extension_loaded('com_dotnet') &&
            class_exists('COM')
        ) {
            $RandomCompat_disabled_classes = preg_split(
                '#\s*,\s*#', 
                strtolower(ini_get('disable_classes'))
            );
            
            if (!in_array('com', $RandomCompat_disabled_classes)) {
                try {
                    $RandomCompatCOMtest = new COM('CAPICOM.Utilities.1');
                    if (method_exists($RandomCompatCOMtest, 'GetRandom')) {
                        // See random_bytes_com_dotnet.php
                        require_once $RandomCompatDIR.'/random_bytes_com_dotnet.php';
                    }
                } catch (com_exception $e) {
                    // Don't try to use it.
                }
            }
            $RandomCompat_disabled_classes = null;
            $RandomCompatCOMtest = null;
        }
        if (
            !function_exists('random_bytes') && 
            extension_loaded('openssl') &&
            (
                // Unix-like with PHP >= 5.3.0 or
                (
                    DIRECTORY_SEPARATOR === '/' &&
                    PHP_VERSION_ID >= 50300
                ) ||
                // Windows with PHP >= 5.4.1
                PHP_VERSION_ID >= 50401
            )
        ) {
            // See random_bytes_openssl.php
            require_once $RandomCompatDIR.'/random_bytes_openssl.php';
        }
        if (!function_exists('random_bytes')) {
            /**
             * We don't have any more options, so let's throw an exception right now
             * and hope the developer won't let it fail silently.
             */
            function random_bytes()
            {
                throw new Exception(
                    'There is no suitable CSPRNG installed on your system'
                );
            }
        }
    }
    if (!function_exists('random_int')) {
        require_once $RandomCompatDIR.'/random_int.php';
    }
    $RandomCompatDIR = null;
}

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
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
if (PHP_VERSION_ID < 70000) {
    if (!defined('RANDOM_COMPAT_READ_BUFFER')) {
        define('RANDOM_COMPAT_READ_BUFFER', 8);
    }
    require_once "byte_safe_strings.php";
    require_once "error_polyfill.php";
    if (!function_exists('random_bytes')) {
        /**
         * PHP 5.2.0 - 5.6.x way to implement random_bytes()
         * 
         * We use conditional statements here to define the function in accordance
         * to the operating environment. It's a micro-optimization.
         * 
         * In order of preference:
         *   1. fread() /dev/urandom if available
         *   2. mcrypt_create_iv($bytes, MCRYPT_CREATE_IV)
         *   3. COM('CAPICOM.Utilities.1')->GetRandom()
         *   4. openssl_random_pseudo_bytes()
         * 
         * See ERRATA.md for our reasoning behind this particular order
         */
        if (!ini_get('open_basedir') && is_readable('/dev/urandom')) {
            // See random_bytes_dev_urandom.php
            require_once "random_bytes_dev_urandom.php";
        } elseif (PHP_VERSION_ID >= 50307 && function_exists('mcrypt_create_iv')) {
            // See random_bytes_mcrypt.php
            require_once "random_bytes_mcrypt.php";
        } elseif (extension_loaded('com_dotnet')) {
            // See random_bytes_com_dotnet.php
            require_once "random_bytes_com_dotnet.php";
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            // See random_bytes_openssl.php
            require_once "random_bytes_openssl.php";
        } else {
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
        require_once "random_int.php";
    }
}

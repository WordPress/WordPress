<?php

/*
 * This file is part of composer/ca-bundle.
 *
 * (c) Composer <https://github.com/composer>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Composer\CaBundle;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\PhpProcess;

/**
 * @author Chris Smith <chris@cs278.org>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class CaBundle
{
    /** @var string|null */
    private static $caPath;
    /** @var array<string, bool> */
    private static $caFileValidity = array();

    /**
     * Returns the system CA bundle path, or a path to the bundled one
     *
     * This method was adapted from Sslurp.
     * https://github.com/EvanDotPro/Sslurp
     *
     * (c) Evan Coury <me@evancoury.com>
     *
     * For the full copyright and license information, please see below:
     *
     * Copyright (c) 2013, Evan Coury
     * All rights reserved.
     *
     * Redistribution and use in source and binary forms, with or without modification,
     * are permitted provided that the following conditions are met:
     *
     *     * Redistributions of source code must retain the above copyright notice,
     *       this list of conditions and the following disclaimer.
     *
     *     * Redistributions in binary form must reproduce the above copyright notice,
     *       this list of conditions and the following disclaimer in the documentation
     *       and/or other materials provided with the distribution.
     *
     * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
     * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
     * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
     * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
     * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
     * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
     * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
     * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
     * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
     * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
     *
     * @param  LoggerInterface $logger optional logger for information about which CA files were loaded
     * @return string          path to a CA bundle file or directory
     */
    public static function getSystemCaRootBundlePath(?LoggerInterface $logger = null)
    {
        if (self::$caPath !== null) {
            return self::$caPath;
        }
        $caBundlePaths = array();

        // If SSL_CERT_FILE env variable points to a valid certificate/bundle, use that.
        // This mimics how OpenSSL uses the SSL_CERT_FILE env variable.
        $caBundlePaths[] = self::getEnvVariable('SSL_CERT_FILE');

        // If SSL_CERT_DIR env variable points to a valid certificate/bundle, use that.
        // This mimics how OpenSSL uses the SSL_CERT_FILE env variable.
        $caBundlePaths[] = self::getEnvVariable('SSL_CERT_DIR');

        $caBundlePaths[] = ini_get('openssl.cafile');
        $caBundlePaths[] = ini_get('openssl.capath');

        $otherLocations = array(
            '/etc/pki/tls/certs/ca-bundle.crt', // Fedora, RHEL, CentOS (ca-certificates package)
            '/etc/ssl/certs/ca-certificates.crt', // Debian, Ubuntu, Gentoo, Arch Linux (ca-certificates package)
            '/etc/ssl/ca-bundle.pem', // SUSE, openSUSE (ca-certificates package)
            '/usr/ssl/certs/ca-bundle.crt', // Cygwin
            '/opt/local/share/curl/curl-ca-bundle.crt', // OS X macports, curl-ca-bundle package
            '/usr/local/share/curl/curl-ca-bundle.crt', // Default cURL CA bunde path (without --with-ca-bundle option)
            '/usr/share/ssl/certs/ca-bundle.crt', // Really old RedHat?
            '/etc/ssl/cert.pem', // OpenBSD
            '/usr/local/etc/openssl/cert.pem', // OS X homebrew, openssl package
            '/usr/local/etc/openssl@1.1/cert.pem', // OS X homebrew, openssl@1.1 package
            '/opt/homebrew/etc/openssl@3/cert.pem', // macOS silicon homebrew, openssl@3 package
            '/opt/homebrew/etc/openssl@1.1/cert.pem', // macOS silicon homebrew, openssl@1.1 package
            '/etc/pki/tls/certs',
            '/etc/ssl/certs', // FreeBSD
        );

        $caBundlePaths = array_merge($caBundlePaths, $otherLocations);

        foreach ($caBundlePaths as $caBundle) {
            if ($caBundle && self::caFileUsable($caBundle, $logger)) {
                return self::$caPath = $caBundle;
            }

            if ($caBundle && self::caDirUsable($caBundle, $logger)) {
                return self::$caPath = $caBundle;
            }
        }

        return self::$caPath = static::getBundledCaBundlePath(); // Bundled CA file, last resort
    }

    /**
     * Returns the path to the bundled CA file
     *
     * In case you don't want to trust the user or the system, you can use this directly
     *
     * @return string path to a CA bundle file
     */
    public static function getBundledCaBundlePath()
    {
        $caBundleFile = __DIR__.'/../res/cacert.pem';

        // cURL does not understand 'phar://' paths
        // see https://github.com/composer/ca-bundle/issues/10
        if (0 === strpos($caBundleFile, 'phar://')) {
            $tempCaBundleFile = tempnam(sys_get_temp_dir(), 'openssl-ca-bundle-');
            if (false === $tempCaBundleFile) {
                throw new \RuntimeException('Could not create a temporary file to store the bundled CA file');
            }

            file_put_contents(
                $tempCaBundleFile,
                file_get_contents($caBundleFile)
            );

            register_shutdown_function(function() use ($tempCaBundleFile) {
                @unlink($tempCaBundleFile);
            });

            $caBundleFile = $tempCaBundleFile;
        }

        return $caBundleFile;
    }

    /**
     * Validates a CA file using opensl_x509_parse only if it is safe to use
     *
     * @param string          $filename
     * @param LoggerInterface $logger   optional logger for information about which CA files were loaded
     *
     * @return bool
     */
    public static function validateCaFile($filename, ?LoggerInterface $logger = null)
    {
        static $warned = false;

        if (isset(self::$caFileValidity[$filename])) {
            return self::$caFileValidity[$filename];
        }

        $contents = file_get_contents($filename);

        if (is_string($contents) && strlen($contents) > 0) {
            $contents = preg_replace("/^(\\-+(?:BEGIN|END))\\s+TRUSTED\\s+(CERTIFICATE\\-+)\$/m", '$1 $2', $contents);
            if (null === $contents) {
                // regex extraction failed
                $isValid = false;
            } else {
                $isValid = (bool) openssl_x509_parse($contents);
            }
        } else {
            $isValid = false;
        }

        if ($logger) {
            $logger->debug('Checked CA file '.realpath($filename).': '.($isValid ? 'valid' : 'invalid'));
        }

        return self::$caFileValidity[$filename] = $isValid;
    }

    /**
     * Test if it is safe to use the PHP function openssl_x509_parse().
     *
     * This checks if OpenSSL extensions is vulnerable to remote code execution
     * via the exploit documented as CVE-2013-6420.
     *
     * @return bool
     */
    public static function isOpensslParseSafe()
    {
        return true;
    }

    /**
     * Resets the static caches
     * @return void
     */
    public static function reset()
    {
        self::$caFileValidity = array();
        self::$caPath = null;
    }

    /**
     * @param  string $name
     * @return string|false
     */
    private static function getEnvVariable($name)
    {
        if (isset($_SERVER[$name])) {
            return (string) $_SERVER[$name];
        }

        if (PHP_SAPI === 'cli' && ($value = getenv($name)) !== false && $value !== null) {
            return (string) $value;
        }

        return false;
    }

    /**
     * @param  string|false $certFile
     * @param  LoggerInterface|null $logger
     * @return bool
     */
    private static function caFileUsable($certFile, ?LoggerInterface $logger = null)
    {
        return $certFile
            && self::isFile($certFile, $logger)
            && self::isReadable($certFile, $logger)
            && self::validateCaFile($certFile, $logger);
    }

    /**
     * @param  string|false $certDir
     * @param  LoggerInterface|null $logger
     * @return bool
     */
    private static function caDirUsable($certDir, ?LoggerInterface $logger = null)
    {
        return $certDir
            && self::isDir($certDir, $logger)
            && self::isReadable($certDir, $logger)
            && self::glob($certDir . '/*', $logger);
    }

    /**
     * @param  string $certFile
     * @param  LoggerInterface|null $logger
     * @return bool
     */
    private static function isFile($certFile, ?LoggerInterface $logger = null)
    {
        $isFile = @is_file($certFile);
        if (!$isFile && $logger) {
            $logger->debug(sprintf('Checked CA file %s does not exist or it is not a file.', $certFile));
        }

        return $isFile;
    }

    /**
     * @param  string $certDir
     * @param  LoggerInterface|null $logger
     * @return bool
     */
    private static function isDir($certDir, ?LoggerInterface $logger = null)
    {
        $isDir = @is_dir($certDir);
        if (!$isDir && $logger) {
            $logger->debug(sprintf('Checked directory %s does not exist or it is not a directory.', $certDir));
        }

        return $isDir;
    }

    /**
     * @param  string $certFileOrDir
     * @param  LoggerInterface|null $logger
     * @return bool
     */
    private static function isReadable($certFileOrDir, ?LoggerInterface $logger = null)
    {
        $isReadable = @is_readable($certFileOrDir);
        if (!$isReadable && $logger) {
            $logger->debug(sprintf('Checked file or directory %s is not readable.', $certFileOrDir));
        }

        return $isReadable;
    }

    /**
     * @param  string $pattern
     * @param  LoggerInterface|null $logger
     * @return bool
     */
    private static function glob($pattern, ?LoggerInterface $logger = null)
    {
        $certs = glob($pattern);
        if ($certs === false) {
            if ($logger) {
                $logger->debug(sprintf("An error occurred while trying to find certificates for pattern: %s", $pattern));
            }
            return false;
        }

        if (count($certs) === 0) {
            if ($logger) {
                $logger->debug(sprintf("No CA files found for pattern: %s", $pattern));
            }
            return false;
        }

        return true;
    }
}

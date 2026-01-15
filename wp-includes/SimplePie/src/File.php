<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie;

use SimplePie\HTTP\Response;

/**
 * Used for fetching remote files and reading local files
 *
 * Supports HTTP 1.0 via cURL or fsockopen, with spotty HTTP 1.1 support
 *
 * This class can be overloaded with {@see \SimplePie\SimplePie::set_file_class()}
 *
 * @todo Move to properly supporting RFC2616 (HTTP/1.1)
 */
class File implements Response
{
    /**
     * @var string The final URL after following all redirects
     * @deprecated Use `get_final_requested_uri()` method.
     */
    public $url;

    /**
     * @var ?string User agent to use in requests
     * @deprecated Set the user agent in constructor.
     */
    public $useragent;

    /** @var bool */
    public $success = true;

    /** @var array<string, non-empty-array<string>> Canonical representation of headers */
    private $parsed_headers = [];
    /** @var array<string, string> Last known value of $headers property (used to detect external modification) */
    private $last_headers = [];
    /**
     * @var array<string, string> Headers as string for BC
     * @deprecated Use `get_headers()` method.
     */
    public $headers = [];

    /**
     * @var ?string Body of the HTTP response
     * @deprecated Use `get_body_content()` method.
     */
    public $body;

    /**
     * @var int Status code of the HTTP response
     * @deprecated Use `get_status_code()` method.
     */
    public $status_code = 0;

    /** @var non-negative-int Number of redirect that were already performed during this request sequence. */
    public $redirects = 0;

    /** @var ?string */
    public $error;

    /**
     * @var int-mask-of<SimplePie::FILE_SOURCE_*> Bit mask representing the method used to fetch the file and whether it is a local file or remote file obtained over HTTP.
     * @deprecated Backend is implementation detail which you should not care about; to see if the file was retrieved over HTTP, check if `get_final_requested_uri()` with `Misc::is_remote_uri()`.
     */
    public $method = \SimplePie\SimplePie::FILE_SOURCE_NONE;

    /**
     * @var string The permanent URL or the resource (first URL after the prefix of (only) permanent redirects)
     * @deprecated Use `get_permanent_uri()` method.
     */
    public $permanent_url;
    /** @var bool Whether the permanent URL is still writeable (prefix of permanent redirects has not ended) */
    private $permanentUrlMutable = true;

    /**
     * @param string $url
     * @param int $timeout
     * @param int $redirects
     * @param ?array<string, string> $headers
     * @param ?string $useragent
     * @param bool $force_fsockopen
     * @param array<int, mixed> $curl_options
     */
    public function __construct(string $url, int $timeout = 10, int $redirects = 5, ?array $headers = null, ?string $useragent = null, bool $force_fsockopen = false, array $curl_options = [])
    {
        if (function_exists('idn_to_ascii')) {
            $parsed = \SimplePie\Misc::parse_url($url);
            if ($parsed['authority'] !== '' && !ctype_print($parsed['authority'])) {
                $authority = (string) \idn_to_ascii($parsed['authority'], \IDNA_NONTRANSITIONAL_TO_ASCII, \INTL_IDNA_VARIANT_UTS46);
                $url = \SimplePie\Misc::compress_parse_url($parsed['scheme'], $authority, $parsed['path'], $parsed['query'], null);
            }
        }
        $this->url = $url;
        if ($this->permanentUrlMutable) {
            $this->permanent_url = $url;
        }
        $this->useragent = $useragent;
        if (preg_match('/^http(s)?:\/\//i', $url)) {
            if ($useragent === null) {
                $useragent = (string) ini_get('user_agent');
                $this->useragent = $useragent;
            }
            if (!is_array($headers)) {
                $headers = [];
            }
            if (!$force_fsockopen && function_exists('curl_exec')) {
                $this->method = \SimplePie\SimplePie::FILE_SOURCE_REMOTE | \SimplePie\SimplePie::FILE_SOURCE_CURL;
                $fp = curl_init();
                $headers2 = [];
                foreach ($headers as $key => $value) {
                    $headers2[] = "$key: $value";
                }
                if (isset($curl_options[CURLOPT_HTTPHEADER])) {
                    if (is_array($curl_options[CURLOPT_HTTPHEADER])) {
                        $headers2 = array_merge($headers2, $curl_options[CURLOPT_HTTPHEADER]);
                    }
                    unset($curl_options[CURLOPT_HTTPHEADER]);
                }
                if (version_compare(\SimplePie\Misc::get_curl_version(), '7.10.5', '>=')) {
                    curl_setopt($fp, CURLOPT_ENCODING, '');
                }
                curl_setopt($fp, CURLOPT_URL, $url);
                curl_setopt($fp, CURLOPT_HEADER, 1);
                curl_setopt($fp, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($fp, CURLOPT_FAILONERROR, 1);
                curl_setopt($fp, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($fp, CURLOPT_CONNECTTIMEOUT, $timeout);
                curl_setopt($fp, CURLOPT_REFERER, \SimplePie\Misc::url_remove_credentials($url));
                curl_setopt($fp, CURLOPT_USERAGENT, $useragent);
                curl_setopt($fp, CURLOPT_HTTPHEADER, $headers2);
                foreach ($curl_options as $curl_param => $curl_value) {
                    curl_setopt($fp, $curl_param, $curl_value);
                }

                $responseHeaders = curl_exec($fp);
                if (curl_errno($fp) === CURLE_WRITE_ERROR || curl_errno($fp) === CURLE_BAD_CONTENT_ENCODING) {
                    curl_setopt($fp, CURLOPT_ENCODING, 'none');
                    $responseHeaders = curl_exec($fp);
                }
                $this->status_code = curl_getinfo($fp, CURLINFO_HTTP_CODE);
                if (curl_errno($fp)) {
                    $this->error = 'cURL error ' . curl_errno($fp) . ': ' . curl_error($fp);
                    $this->success = false;
                } else {
                    // Use the updated url provided by curl_getinfo after any redirects.
                    if ($info = curl_getinfo($fp)) {
                        $this->url = $info['url'];
                    }
                    // For PHPStan: We already checked that error did not occur.
                    assert(is_array($info) && $info['redirect_count'] >= 0);
                    if (\PHP_VERSION_ID < 80000) {
                        curl_close($fp);
                    }
                    $responseHeaders = \SimplePie\HTTP\Parser::prepareHeaders((string) $responseHeaders, $info['redirect_count'] + 1);
                    $parser = new \SimplePie\HTTP\Parser($responseHeaders, true);
                    if ($parser->parse()) {
                        $this->set_headers($parser->headers);
                        $this->body = $parser->body;
                        $this->status_code = $parser->status_code;
                        if ((in_array($this->status_code, [300, 301, 302, 303, 307]) || $this->status_code > 307 && $this->status_code < 400) && ($locationHeader = $this->get_header_line('location')) !== '' && $this->redirects < $redirects) {
                            $this->redirects++;
                            $location = \SimplePie\Misc::absolutize_url($locationHeader, $url);
                            if ($location === false) {
                                $this->error = "Invalid redirect location, trying to base “{$locationHeader}” onto “{$url}”";
                                $this->success = false;
                                return;
                            }
                            $this->permanentUrlMutable = $this->permanentUrlMutable && ($this->status_code == 301 || $this->status_code == 308);
                            $this->__construct($location, $timeout, $redirects, $headers, $useragent, $force_fsockopen, $curl_options);
                            return;
                        }
                    }
                }
            } else {
                $this->method = \SimplePie\SimplePie::FILE_SOURCE_REMOTE | \SimplePie\SimplePie::FILE_SOURCE_FSOCKOPEN;
                if (($url_parts = parse_url($url)) === false) {
                    throw new \InvalidArgumentException('Malformed URL: ' . $url);
                }
                if (!isset($url_parts['host'])) {
                    throw new \InvalidArgumentException('Missing hostname: ' . $url);
                }
                $socket_host = $url_parts['host'];
                if (isset($url_parts['scheme']) && strtolower($url_parts['scheme']) === 'https') {
                    $socket_host = 'ssl://' . $socket_host;
                    $url_parts['port'] = 443;
                }
                if (!isset($url_parts['port'])) {
                    $url_parts['port'] = 80;
                }
                $fp = @fsockopen($socket_host, $url_parts['port'], $errno, $errstr, $timeout);
                if (!$fp) {
                    $this->error = 'fsockopen error: ' . $errstr;
                    $this->success = false;
                } else {
                    stream_set_timeout($fp, $timeout);
                    if (isset($url_parts['path'])) {
                        if (isset($url_parts['query'])) {
                            $get = "$url_parts[path]?$url_parts[query]";
                        } else {
                            $get = $url_parts['path'];
                        }
                    } else {
                        $get = '/';
                    }
                    $out = "GET $get HTTP/1.1\r\n";
                    $out .= "Host: $url_parts[host]\r\n";
                    $out .= "User-Agent: $useragent\r\n";
                    if (extension_loaded('zlib')) {
                        $out .= "Accept-Encoding: x-gzip,gzip,deflate\r\n";
                    }

                    if (isset($url_parts['user']) && isset($url_parts['pass'])) {
                        $out .= "Authorization: Basic " . base64_encode("$url_parts[user]:$url_parts[pass]") . "\r\n";
                    }
                    foreach ($headers as $key => $value) {
                        $out .= "$key: $value\r\n";
                    }
                    $out .= "Connection: Close\r\n\r\n";
                    fwrite($fp, $out);

                    $info = stream_get_meta_data($fp);

                    $responseHeaders = '';
                    while (!$info['eof'] && !$info['timed_out']) {
                        $responseHeaders .= fread($fp, 1160);
                        $info = stream_get_meta_data($fp);
                    }
                    if (!$info['timed_out']) {
                        $parser = new \SimplePie\HTTP\Parser($responseHeaders, true);
                        if ($parser->parse()) {
                            $this->set_headers($parser->headers);
                            $this->body = $parser->body;
                            $this->status_code = $parser->status_code;
                            if ((in_array($this->status_code, [300, 301, 302, 303, 307]) || $this->status_code > 307 && $this->status_code < 400) && ($locationHeader = $this->get_header_line('location')) !== '' && $this->redirects < $redirects) {
                                $this->redirects++;
                                $location = \SimplePie\Misc::absolutize_url($locationHeader, $url);
                                $this->permanentUrlMutable = $this->permanentUrlMutable && ($this->status_code == 301 || $this->status_code == 308);
                                if ($location === false) {
                                    $this->error = "Invalid redirect location, trying to base “{$locationHeader}” onto “{$url}”";
                                    $this->success = false;
                                    return;
                                }
                                $this->__construct($location, $timeout, $redirects, $headers, $useragent, $force_fsockopen, $curl_options);
                                return;
                            }
                            if (($contentEncodingHeader = $this->get_header_line('content-encoding')) !== '') {
                                // Hey, we act dumb elsewhere, so let's do that here too
                                switch (strtolower(trim($contentEncodingHeader, "\x09\x0A\x0D\x20"))) {
                                    case 'gzip':
                                    case 'x-gzip':
                                        if (($decompressed = gzdecode($this->body)) === false) {
                                            $this->error = 'Unable to decode HTTP "gzip" stream';
                                            $this->success = false;
                                        } else {
                                            $this->body = $decompressed;
                                        }
                                        break;

                                    case 'deflate':
                                        if (($decompressed = gzinflate($this->body)) !== false) {
                                            $this->body = $decompressed;
                                        } elseif (($decompressed = gzuncompress($this->body)) !== false) {
                                            $this->body = $decompressed;
                                        } elseif (($decompressed = gzdecode($this->body)) !== false) {
                                            $this->body = $decompressed;
                                        } else {
                                            $this->error = 'Unable to decode HTTP "deflate" stream';
                                            $this->success = false;
                                        }
                                        break;

                                    default:
                                        $this->error = 'Unknown content coding';
                                        $this->success = false;
                                }
                            }
                        }
                    } else {
                        $this->error = 'fsocket timed out';
                        $this->success = false;
                    }
                    fclose($fp);
                }
            }
        } else {
            $this->method = \SimplePie\SimplePie::FILE_SOURCE_LOCAL | \SimplePie\SimplePie::FILE_SOURCE_FILE_GET_CONTENTS;
            if (empty($url) || !is_readable($url) ||  false === $filebody = file_get_contents($url)) {
                $this->body = '';
                $this->error = sprintf('file "%s" is not readable', $url);
                $this->success = false;
            } else {
                $this->body = $filebody;
                $this->status_code = 200;
            }
        }
        if ($this->success) {
            assert($this->body !== null); // For PHPStan
            // Leading whitespace may cause XML parsing errors (XML declaration cannot be preceded by anything other than BOM) so we trim it.
            // Note that unlike built-in `trim` function’s default settings, we do not trim `\x00` to avoid breaking characters in UTF-16 or UTF-32 encoded strings.
            // We also only do that when the whitespace is followed by `<`, so that we do not break e.g. UTF-16LE encoded whitespace like `\n\x00` in half.
            $this->body = preg_replace('/^[ \n\r\t\v]+</', '<', $this->body);
        }
    }

    public function get_permanent_uri(): string
    {
        return (string) $this->permanent_url;
    }

    public function get_final_requested_uri(): string
    {
        return (string) $this->url;
    }

    public function get_status_code(): int
    {
        return (int) $this->status_code;
    }

    public function get_headers(): array
    {
        $this->maybe_update_headers();
        return $this->parsed_headers;
    }

    public function has_header(string $name): bool
    {
        $this->maybe_update_headers();
        return $this->get_header($name) !== [];
    }

    public function get_header(string $name): array
    {
        $this->maybe_update_headers();
        return $this->parsed_headers[strtolower($name)] ?? [];
    }

    public function with_header(string $name, $value)
    {
        $this->maybe_update_headers();
        $new = clone $this;

        $newHeader = [
            strtolower($name) => (array) $value,
        ];
        $new->set_headers($newHeader + $this->get_headers());

        return $new;
    }

    public function get_header_line(string $name): string
    {
        $this->maybe_update_headers();
        return implode(', ', $this->get_header($name));
    }

    public function get_body_content(): string
    {
        return (string) $this->body;
    }

    /**
     * Check if the $headers property was changed and update the internal state accordingly.
     */
    private function maybe_update_headers(): void
    {
        if ($this->headers !== $this->last_headers) {
            $this->parsed_headers = array_map(
                function (string $header_line): array {
                    if (strpos($header_line, ',') === false) {
                        return [$header_line];
                    } else {
                        return array_map('trim', explode(',', $header_line));
                    }
                },
                $this->headers
            );
        }
        $this->last_headers = $this->headers;
    }

    /**
     * Sets headers internally.
     *
     * @param array<string, non-empty-array<string>> $headers
     */
    private function set_headers(array $headers): void
    {
        $this->parsed_headers = $headers;
        $this->headers = self::flatten_headers($headers);
        $this->last_headers = $this->headers;
    }

    /**
     * Converts PSR-7 compatible headers into a legacy format.
     *
     * @param array<string, non-empty-array<string>> $headers
     *
     * @return array<string, string>
     */
    private function flatten_headers(array $headers): array
    {
        return array_map(function (array $values): string {
            return implode(',', $values);
        }, $headers);
    }

    /**
     * Create a File instance from another Response
     *
     * For BC reasons in some places there MUST be a `File` instance
     * instead of a `Response` implementation
     *
     * @see Locator::__construct()
     * @internal
     */
    final public static function fromResponse(Response $response): self
    {
        $headers = [];

        foreach ($response->get_headers() as $name => $header) {
            $headers[$name] = implode(', ', $header);
        }

        /** @var File */
        $file = (new \ReflectionClass(File::class))->newInstanceWithoutConstructor();

        $file->url = $response->get_final_requested_uri();
        $file->useragent = null;
        $file->headers = $headers;
        $file->body = $response->get_body_content();
        $file->status_code = $response->get_status_code();
        $file->permanent_url = $response->get_permanent_uri();

        return $file;
    }
}

class_alias('SimplePie\File', 'SimplePie_File');

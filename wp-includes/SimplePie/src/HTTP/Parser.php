<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie\HTTP;

/**
 * HTTP Response Parser
 * @template Psr7Compatible of bool
 */
class Parser
{
    /**
     * HTTP Version
     *
     * @var float
     */
    public $http_version = 0.0;

    /**
     * Status code
     *
     * @var int
     */
    public $status_code = 0;

    /**
     * Reason phrase
     *
     * @var string
     */
    public $reason = '';

    /**
     * @var Psr7Compatible whether headers are compatible with PSR-7 format.
     */
    private $psr7Compatible;

    /**
     * Key/value pairs of the headers
     *
     * @var (Psr7Compatible is true ? array<string, non-empty-array<string>> : array<string, string>)
     */
    public $headers = [];

    /**
     * Body of the response
     *
     * @var string
     */
    public $body = '';

    private const STATE_HTTP_VERSION = 'http_version';

    private const STATE_STATUS = 'status';

    private const STATE_REASON = 'reason';

    private const STATE_NEW_LINE = 'new_line';

    private const STATE_BODY = 'body';

    private const STATE_NAME = 'name';

    private const STATE_VALUE = 'value';

    private const STATE_VALUE_CHAR = 'value_char';

    private const STATE_QUOTE = 'quote';

    private const STATE_QUOTE_ESCAPED = 'quote_escaped';

    private const STATE_QUOTE_CHAR = 'quote_char';

    private const STATE_CHUNKED = 'chunked';

    private const STATE_EMIT = 'emit';

    private const STATE_ERROR = false;

    /**
     * Current state of the state machine
     *
     * @var self::STATE_*
     */
    protected $state = self::STATE_HTTP_VERSION;

    /**
     * Input data
     *
     * @var string
     */
    protected $data = '';

    /**
     * Input data length (to avoid calling strlen() everytime this is needed)
     *
     * @var int
     */
    protected $data_length = 0;

    /**
     * Current position of the pointer
     *
     * @var int
     */
    protected $position = 0;

    /**
     * Name of the header currently being parsed
     *
     * @var string
     */
    protected $name = '';

    /**
     * Value of the header currently being parsed
     *
     * @var string
     */
    protected $value = '';

    /**
     * Create an instance of the class with the input data
     *
     * @param string $data Input data
     * @param Psr7Compatible $psr7Compatible Whether the data types are in format compatible with PSR-7.
     */
    public function __construct(string $data, bool $psr7Compatible = false)
    {
        $this->data = $data;
        $this->data_length = strlen($this->data);
        $this->psr7Compatible = $psr7Compatible;
    }

    /**
     * Parse the input data
     *
     * @return bool true on success, false on failure
     */
    public function parse()
    {
        while ($this->state && $this->state !== self::STATE_EMIT && $this->has_data()) {
            $state = $this->state;
            $this->$state();
        }
        $this->data = '';
        if ($this->state === self::STATE_EMIT || $this->state === self::STATE_BODY) {
            return true;
        }

        // Reset the parser state.
        $this->http_version = 0.0;
        $this->status_code = 0;
        $this->reason = '';
        $this->headers = [];
        $this->body = '';
        return false;
    }

    /**
     * Check whether there is data beyond the pointer
     *
     * @return bool true if there is further data, false if not
     */
    protected function has_data()
    {
        return (bool) ($this->position < $this->data_length);
    }

    /**
     * See if the next character is LWS
     *
     * @return bool true if the next character is LWS, false if not
     */
    protected function is_linear_whitespace()
    {
        return (bool) ($this->data[$this->position] === "\x09"
            || $this->data[$this->position] === "\x20"
            || ($this->data[$this->position] === "\x0A"
                && isset($this->data[$this->position + 1])
                && ($this->data[$this->position + 1] === "\x09" || $this->data[$this->position + 1] === "\x20")));
    }

    /**
     * Parse the HTTP version
     * @return void
     */
    protected function http_version()
    {
        if (strpos($this->data, "\x0A") !== false && strtoupper(substr($this->data, 0, 5)) === 'HTTP/') {
            $len = strspn($this->data, '0123456789.', 5);
            $http_version = substr($this->data, 5, $len);
            $this->position += 5 + $len;
            if (substr_count($http_version, '.') <= 1) {
                $this->http_version = (float) $http_version;
                $this->position += strspn($this->data, "\x09\x20", $this->position);
                $this->state = self::STATE_STATUS;
            } else {
                $this->state = self::STATE_ERROR;
            }
        } else {
            $this->state = self::STATE_ERROR;
        }
    }

    /**
     * Parse the status code
     * @return void
     */
    protected function status()
    {
        if ($len = strspn($this->data, '0123456789', $this->position)) {
            $this->status_code = (int) substr($this->data, $this->position, $len);
            $this->position += $len;
            $this->state = self::STATE_REASON;
        } else {
            $this->state = self::STATE_ERROR;
        }
    }

    /**
     * Parse the reason phrase
     * @return void
     */
    protected function reason()
    {
        $len = strcspn($this->data, "\x0A", $this->position);
        $this->reason = trim(substr($this->data, $this->position, $len), "\x09\x0D\x20");
        $this->position += $len + 1;
        $this->state = self::STATE_NEW_LINE;
    }

    private function add_header(string $name, string $value): void
    {
        if ($this->psr7Compatible) {
            // For PHPStan: should be enforced by template parameter but PHPStan is not smart enough.
            /** @var array<string, non-empty-array<string>> */
            $headers = &$this->headers;
            $headers[$name][] = $value;
        } else {
            // For PHPStan: should be enforced by template parameter but PHPStan is not smart enough.
            /** @var array<string, string>) */
            $headers = &$this->headers;
            $headers[$name] .= ', ' . $value;
        }
    }

    private function replace_header(string $name, string $value): void
    {
        if ($this->psr7Compatible) {
            // For PHPStan: should be enforced by template parameter but PHPStan is not smart enough.
            /** @var array<string, non-empty-array<string>> */
            $headers = &$this->headers;
            $headers[$name] = [$value];
        } else {
            // For PHPStan: should be enforced by template parameter but PHPStan is not smart enough.
            /** @var array<string, string>) */
            $headers = &$this->headers;
            $headers[$name] = $value;
        }
    }

    /**
     * Deal with a new line, shifting data around as needed
     * @return void
     */
    protected function new_line()
    {
        $this->value = trim($this->value, "\x0D\x20");
        if ($this->name !== '' && $this->value !== '') {
            $this->name = strtolower($this->name);
            // We should only use the last Content-Type header. c.f. issue #1
            if (isset($this->headers[$this->name]) && $this->name !== 'content-type') {
                $this->add_header($this->name, $this->value);
            } else {
                $this->replace_header($this->name, $this->value);
            }
        }
        $this->name = '';
        $this->value = '';
        if (substr($this->data[$this->position], 0, 2) === "\x0D\x0A") {
            $this->position += 2;
            $this->state = self::STATE_BODY;
        } elseif ($this->data[$this->position] === "\x0A") {
            $this->position++;
            $this->state = self::STATE_BODY;
        } else {
            $this->state = self::STATE_NAME;
        }
    }

    /**
     * Parse a header name
     * @return void
     */
    protected function name()
    {
        $len = strcspn($this->data, "\x0A:", $this->position);
        if (isset($this->data[$this->position + $len])) {
            if ($this->data[$this->position + $len] === "\x0A") {
                $this->position += $len;
                $this->state = self::STATE_NEW_LINE;
            } else {
                $this->name = substr($this->data, $this->position, $len);
                $this->position += $len + 1;
                $this->state = self::STATE_VALUE;
            }
        } else {
            $this->state = self::STATE_ERROR;
        }
    }

    /**
     * Parse LWS, replacing consecutive LWS characters with a single space
     * @return void
     */
    protected function linear_whitespace()
    {
        do {
            if (substr($this->data, $this->position, 2) === "\x0D\x0A") {
                $this->position += 2;
            } elseif ($this->data[$this->position] === "\x0A") {
                $this->position++;
            }
            $this->position += strspn($this->data, "\x09\x20", $this->position);
        } while ($this->has_data() && $this->is_linear_whitespace());
        $this->value .= "\x20";
    }

    /**
     * See what state to move to while within non-quoted header values
     * @return void
     */
    protected function value()
    {
        if ($this->is_linear_whitespace()) {
            $this->linear_whitespace();
        } else {
            switch ($this->data[$this->position]) {
                case '"':
                    // Workaround for ETags: we have to include the quotes as
                    // part of the tag.
                    if (strtolower($this->name) === 'etag') {
                        $this->value .= '"';
                        $this->position++;
                        $this->state = self::STATE_VALUE_CHAR;
                        break;
                    }
                    $this->position++;
                    $this->state = self::STATE_QUOTE;
                    break;

                case "\x0A":
                    $this->position++;
                    $this->state = self::STATE_NEW_LINE;
                    break;

                default:
                    $this->state = self::STATE_VALUE_CHAR;
                    break;
            }
        }
    }

    /**
     * Parse a header value while outside quotes
     * @return void
     */
    protected function value_char()
    {
        $len = strcspn($this->data, "\x09\x20\x0A\"", $this->position);
        $this->value .= substr($this->data, $this->position, $len);
        $this->position += $len;
        $this->state = self::STATE_VALUE;
    }

    /**
     * See what state to move to while within quoted header values
     * @return void
     */
    protected function quote()
    {
        if ($this->is_linear_whitespace()) {
            $this->linear_whitespace();
        } else {
            switch ($this->data[$this->position]) {
                case '"':
                    $this->position++;
                    $this->state = self::STATE_VALUE;
                    break;

                case "\x0A":
                    $this->position++;
                    $this->state = self::STATE_NEW_LINE;
                    break;

                case '\\':
                    $this->position++;
                    $this->state = self::STATE_QUOTE_ESCAPED;
                    break;

                default:
                    $this->state = self::STATE_QUOTE_CHAR;
                    break;
            }
        }
    }

    /**
     * Parse a header value while within quotes
     * @return void
     */
    protected function quote_char()
    {
        $len = strcspn($this->data, "\x09\x20\x0A\"\\", $this->position);
        $this->value .= substr($this->data, $this->position, $len);
        $this->position += $len;
        $this->state = self::STATE_VALUE;
    }

    /**
     * Parse an escaped character within quotes
     * @return void
     */
    protected function quote_escaped()
    {
        $this->value .= $this->data[$this->position];
        $this->position++;
        $this->state = self::STATE_QUOTE;
    }

    /**
     * Parse the body
     * @return void
     */
    protected function body()
    {
        $this->body = substr($this->data, $this->position);
        if (!empty($this->headers['transfer-encoding'])) {
            unset($this->headers['transfer-encoding']);
            $this->state = self::STATE_CHUNKED;
        } else {
            $this->state = self::STATE_EMIT;
        }
    }

    /**
     * Parsed a "Transfer-Encoding: chunked" body
     * @return void
     */
    protected function chunked()
    {
        if (!preg_match('/^([0-9a-f]+)[^\r\n]*\r\n/i', trim($this->body))) {
            $this->state = self::STATE_EMIT;
            return;
        }

        $decoded = '';
        $encoded = $this->body;

        while (true) {
            $is_chunked = (bool) preg_match('/^([0-9a-f]+)[^\r\n]*\r\n/i', $encoded, $matches);
            if (!$is_chunked) {
                // Looks like it's not chunked after all
                $this->state = self::STATE_EMIT;
                return;
            }

            $length = hexdec(trim($matches[1]));
            // For PHPStan: this will only be float when larger than PHP_INT_MAX.
            // But even on 32-bit systems, it would mean 2GiB chunk, which sounds unlikely.
            \assert(\is_int($length), "Length needs to be shorter than PHP_INT_MAX");
            if ($length === 0) {
                // Ignore trailer headers
                $this->state = self::STATE_EMIT;
                $this->body = $decoded;
                return;
            }

            $chunk_length = strlen($matches[0]);
            $decoded .= substr($encoded, $chunk_length, $length);
            $encoded = substr($encoded, $chunk_length + $length + 2);

            // BC for PHP < 8.0: substr() can return bool instead of string
            $encoded = ($encoded === false) ? '' : $encoded;

            if (trim($encoded) === '0' || empty($encoded)) {
                $this->state = self::STATE_EMIT;
                $this->body = $decoded;
                return;
            }
        }
    }

    /**
     * Prepare headers (take care of proxies headers)
     *
     * @param string  $headers Raw headers
     * @param non-negative-int $count Redirection count. Default to 1.
     *
     * @return string
     */
    public static function prepareHeaders(string $headers, int $count = 1)
    {
        $data = explode("\r\n\r\n", $headers, $count);
        $data = array_pop($data);
        if (false !== stripos($data, "HTTP/1.0 200 Connection established\r\n")) {
            $exploded = explode("\r\n\r\n", $data, 2);
            $data = end($exploded);
        }
        if (false !== stripos($data, "HTTP/1.1 200 Connection established\r\n")) {
            $exploded = explode("\r\n\r\n", $data, 2);
            $data = end($exploded);
        }
        return $data;
    }
}

class_alias('SimplePie\HTTP\Parser', 'SimplePie_HTTP_Parser');

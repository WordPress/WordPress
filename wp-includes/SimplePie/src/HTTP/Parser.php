<?php

/**
 * SimplePie
 *
 * A PHP-Based RSS and Atom Feed Framework.
 * Takes the hard work out of managing a complete RSS/Atom solution.
 *
 * Copyright (c) 2004-2022, Ryan Parman, Sam Sneddon, Ryan McCue, and contributors
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
 * @copyright 2004-2016 Ryan Parman, Sam Sneddon, Ryan McCue
 * @author Ryan Parman
 * @author Sam Sneddon
 * @author Ryan McCue
 * @link http://simplepie.org/ SimplePie
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace SimplePie\HTTP;

/**
 * HTTP Response Parser
 *
 * @package SimplePie
 * @subpackage HTTP
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
     * Key/value pairs of the headers
     *
     * @var array
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
     * Name of the hedaer currently being parsed
     *
     * @var string
     */
    protected $name = '';

    /**
     * Value of the hedaer currently being parsed
     *
     * @var string
     */
    protected $value = '';

    /**
     * Create an instance of the class with the input data
     *
     * @param string $data Input data
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->data_length = strlen($this->data);
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

        $this->http_version = '';
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
     */
    protected function http_version()
    {
        if (strpos($this->data, "\x0A") !== false && strtoupper(substr($this->data, 0, 5)) === 'HTTP/') {
            $len = strspn($this->data, '0123456789.', 5);
            $this->http_version = substr($this->data, 5, $len);
            $this->position += 5 + $len;
            if (substr_count($this->http_version, '.') <= 1) {
                $this->http_version = (float) $this->http_version;
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
     */
    protected function reason()
    {
        $len = strcspn($this->data, "\x0A", $this->position);
        $this->reason = trim(substr($this->data, $this->position, $len), "\x09\x0D\x20");
        $this->position += $len + 1;
        $this->state = self::STATE_NEW_LINE;
    }

    /**
     * Deal with a new line, shifting data around as needed
     */
    protected function new_line()
    {
        $this->value = trim($this->value, "\x0D\x20");
        if ($this->name !== '' && $this->value !== '') {
            $this->name = strtolower($this->name);
            // We should only use the last Content-Type header. c.f. issue #1
            if (isset($this->headers[$this->name]) && $this->name !== 'content-type') {
                $this->headers[$this->name] .= ', ' . $this->value;
            } else {
                $this->headers[$this->name] = $this->value;
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
     */
    protected function quote_escaped()
    {
        $this->value .= $this->data[$this->position];
        $this->position++;
        $this->state = self::STATE_QUOTE;
    }

    /**
     * Parse the body
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
     * @param integer $count   Redirection count. Default to 1.
     *
     * @return string
     */
    public static function prepareHeaders($headers, $count = 1)
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

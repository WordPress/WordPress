<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie\XML\Declaration;

/**
 * Parses the XML Declaration
 */
class Parser
{
    /**
     * XML Version
     *
     * @access public
     * @var string
     */
    public $version = '1.0';

    /**
     * Encoding
     *
     * @access public
     * @var string
     */
    public $encoding = 'UTF-8';

    /**
     * Standalone
     *
     * @access public
     * @var bool
     */
    public $standalone = false;

    private const STATE_BEFORE_VERSION_NAME = 'before_version_name';

    private const STATE_VERSION_NAME = 'version_name';

    private const STATE_VERSION_EQUALS = 'version_equals';

    private const STATE_VERSION_VALUE = 'version_value';

    private const STATE_ENCODING_NAME = 'encoding_name';

    private const STATE_EMIT = 'emit';

    private const STATE_ENCODING_EQUALS = 'encoding_equals';

    private const STATE_STANDALONE_NAME = 'standalone_name';

    private const STATE_ENCODING_VALUE = 'encoding_value';

    private const STATE_STANDALONE_EQUALS = 'standalone_equals';

    private const STATE_STANDALONE_VALUE = 'standalone_value';

    private const STATE_ERROR = false;

    /**
     * Current state of the state machine
     *
     * @access private
     * @var self::STATE_*
     */
    public $state = self::STATE_BEFORE_VERSION_NAME;

    /**
     * Input data
     *
     * @access private
     * @var string
     */
    public $data = '';

    /**
     * Input data length (to avoid calling strlen() everytime this is needed)
     *
     * @access private
     * @var int
     */
    public $data_length = 0;

    /**
     * Current position of the pointer
     *
     * @var int
     * @access private
     */
    public $position = 0;

    /**
     * Create an instance of the class with the input data
     *
     * @access public
     * @param string $data Input data
     */
    public function __construct(string $data)
    {
        $this->data = $data;
        $this->data_length = strlen($this->data);
    }

    /**
     * Parse the input data
     *
     * @access public
     * @return bool true on success, false on failure
     */
    public function parse(): bool
    {
        while ($this->state && $this->state !== self::STATE_EMIT && $this->has_data()) {
            $state = $this->state;
            $this->$state();
        }
        $this->data = '';
        if ($this->state === self::STATE_EMIT) {
            return true;
        }

        // Reset the parser state.
        $this->version = '1.0';
        $this->encoding = 'UTF-8';
        $this->standalone = false;
        return false;
    }

    /**
     * Check whether there is data beyond the pointer
     *
     * @access private
     * @return bool true if there is further data, false if not
     */
    public function has_data(): bool
    {
        return (bool) ($this->position < $this->data_length);
    }

    /**
     * Advance past any whitespace
     *
     * @return int Number of whitespace characters passed
     */
    public function skip_whitespace()
    {
        $whitespace = strspn($this->data, "\x09\x0A\x0D\x20", $this->position);
        $this->position += $whitespace;
        return $whitespace;
    }

    /**
     * Read value
     *
     * @return string|false
     */
    public function get_value()
    {
        $quote = substr($this->data, $this->position, 1);
        if ($quote === '"' || $quote === "'") {
            $this->position++;
            $len = strcspn($this->data, $quote, $this->position);
            if ($this->has_data()) {
                $value = substr($this->data, $this->position, $len);
                $this->position += $len + 1;
                return $value;
            }
        }
        return false;
    }

    public function before_version_name(): void
    {
        if ($this->skip_whitespace()) {
            $this->state = self::STATE_VERSION_NAME;
        } else {
            $this->state = self::STATE_ERROR;
        }
    }

    public function version_name(): void
    {
        if (substr($this->data, $this->position, 7) === 'version') {
            $this->position += 7;
            $this->skip_whitespace();
            $this->state = self::STATE_VERSION_EQUALS;
        } else {
            $this->state = self::STATE_ERROR;
        }
    }

    public function version_equals(): void
    {
        if (substr($this->data, $this->position, 1) === '=') {
            $this->position++;
            $this->skip_whitespace();
            $this->state = self::STATE_VERSION_VALUE;
        } else {
            $this->state = self::STATE_ERROR;
        }
    }

    public function version_value(): void
    {
        if ($version = $this->get_value()) {
            $this->version = $version;
            $this->skip_whitespace();
            if ($this->has_data()) {
                $this->state = self::STATE_ENCODING_NAME;
            } else {
                $this->state = self::STATE_EMIT;
            }
        } else {
            $this->state = self::STATE_ERROR;
        }
    }

    public function encoding_name(): void
    {
        if (substr($this->data, $this->position, 8) === 'encoding') {
            $this->position += 8;
            $this->skip_whitespace();
            $this->state = self::STATE_ENCODING_EQUALS;
        } else {
            $this->state = self::STATE_STANDALONE_NAME;
        }
    }

    public function encoding_equals(): void
    {
        if (substr($this->data, $this->position, 1) === '=') {
            $this->position++;
            $this->skip_whitespace();
            $this->state = self::STATE_ENCODING_VALUE;
        } else {
            $this->state = self::STATE_ERROR;
        }
    }

    public function encoding_value(): void
    {
        if ($encoding = $this->get_value()) {
            $this->encoding = $encoding;
            $this->skip_whitespace();
            if ($this->has_data()) {
                $this->state = self::STATE_STANDALONE_NAME;
            } else {
                $this->state = self::STATE_EMIT;
            }
        } else {
            $this->state = self::STATE_ERROR;
        }
    }

    public function standalone_name(): void
    {
        if (substr($this->data, $this->position, 10) === 'standalone') {
            $this->position += 10;
            $this->skip_whitespace();
            $this->state = self::STATE_STANDALONE_EQUALS;
        } else {
            $this->state = self::STATE_ERROR;
        }
    }

    public function standalone_equals(): void
    {
        if (substr($this->data, $this->position, 1) === '=') {
            $this->position++;
            $this->skip_whitespace();
            $this->state = self::STATE_STANDALONE_VALUE;
        } else {
            $this->state = self::STATE_ERROR;
        }
    }

    public function standalone_value(): void
    {
        if ($standalone = $this->get_value()) {
            switch ($standalone) {
                case 'yes':
                    $this->standalone = true;
                    break;

                case 'no':
                    $this->standalone = false;
                    break;

                default:
                    $this->state = self::STATE_ERROR;
                    return;
            }

            $this->skip_whitespace();
            if ($this->has_data()) {
                $this->state = self::STATE_ERROR;
            } else {
                $this->state = self::STATE_EMIT;
            }
        } else {
            $this->state = self::STATE_ERROR;
        }
    }
}

class_alias('SimplePie\XML\Declaration\Parser', 'SimplePie_XML_Declaration_Parser');

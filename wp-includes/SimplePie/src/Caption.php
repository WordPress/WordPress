<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie;

/**
 * Handles `<media:text>` captions as defined in Media RSS.
 *
 * Used by {@see \SimplePie\Enclosure::get_caption()} and {@see \SimplePie\Enclosure::get_captions()}
 *
 * This class can be overloaded with {@see \SimplePie\SimplePie::set_caption_class()}
 */
class Caption
{
    /**
     * Content type
     *
     * @var ?string
     * @see get_type()
     */
    public $type;

    /**
     * Language
     *
     * @var ?string
     * @see get_language()
     */
    public $lang;

    /**
     * Start time
     *
     * @var ?string
     * @see get_starttime()
     */
    public $startTime;

    /**
     * End time
     *
     * @var ?string
     * @see get_endtime()
     */
    public $endTime;

    /**
     * Caption text
     *
     * @var ?string
     * @see get_text()
     */
    public $text;

    /**
     * Constructor, used to input the data
     *
     * For documentation on all the parameters, see the corresponding
     * properties and their accessors
     */
    public function __construct(
        ?string $type = null,
        ?string $lang = null,
        ?string $startTime = null,
        ?string $endTime = null,
        ?string $text = null
    ) {
        $this->type = $type;
        $this->lang = $lang;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->text = $text;
    }

    /**
     * String-ified version
     *
     * @return string
     */
    public function __toString()
    {
        // There is no $this->data here
        return md5(serialize($this));
    }

    /**
     * Get the end time
     *
     * @return string|null Time in the format 'hh:mm:ss.SSS'
     */
    public function get_endtime()
    {
        if ($this->endTime !== null) {
            return $this->endTime;
        }

        return null;
    }

    /**
     * Get the language
     *
     * @link http://tools.ietf.org/html/rfc3066
     * @return string|null Language code as per RFC 3066
     */
    public function get_language()
    {
        if ($this->lang !== null) {
            return $this->lang;
        }

        return null;
    }

    /**
     * Get the start time
     *
     * @return string|null Time in the format 'hh:mm:ss.SSS'
     */
    public function get_starttime()
    {
        if ($this->startTime !== null) {
            return $this->startTime;
        }

        return null;
    }

    /**
     * Get the text of the caption
     *
     * @return string|null
     */
    public function get_text()
    {
        if ($this->text !== null) {
            return $this->text;
        }

        return null;
    }

    /**
     * Get the content type (not MIME type)
     *
     * @return string|null Either 'text' or 'html'
     */
    public function get_type()
    {
        if ($this->type !== null) {
            return $this->type;
        }

        return null;
    }
}

class_alias('SimplePie\Caption', 'SimplePie_Caption');

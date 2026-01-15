<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie;

/**
 * Manages `<media:copyright>` copyright tags as defined in Media RSS
 *
 * Used by {@see \SimplePie\Enclosure::get_copyright()}
 *
 * This class can be overloaded with {@see \SimplePie\SimplePie::set_copyright_class()}
 */
class Copyright
{
    /**
     * Copyright URL
     *
     * @var ?string
     * @see get_url()
     */
    public $url;

    /**
     * Attribution
     *
     * @var ?string
     * @see get_attribution()
     */
    public $label;

    /**
     * Constructor, used to input the data
     *
     * For documentation on all the parameters, see the corresponding
     * properties and their accessors
     */
    public function __construct(
        ?string $url = null,
        ?string $label = null
    ) {
        $this->url = $url;
        $this->label = $label;
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
     * Get the copyright URL
     *
     * @return string|null URL to copyright information
     */
    public function get_url()
    {
        if ($this->url !== null) {
            return $this->url;
        }

        return null;
    }

    /**
     * Get the attribution text
     *
     * @return string|null
     */
    public function get_attribution()
    {
        if ($this->label !== null) {
            return $this->label;
        }

        return null;
    }
}

class_alias('SimplePie\Copyright', 'SimplePie_Copyright');

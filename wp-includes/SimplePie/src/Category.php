<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie;

/**
 * Manages all category-related data
 *
 * Used by {@see \SimplePie\Item::get_category()} and {@see \SimplePie\Item::get_categories()}
 *
 * This class can be overloaded with {@see \SimplePie\SimplePie::set_category_class()}
 */
class Category
{
    /**
     * Category identifier
     *
     * @var string|null
     * @see get_term
     */
    public $term;

    /**
     * Categorization scheme identifier
     *
     * @var string|null
     * @see get_scheme()
     */
    public $scheme;

    /**
     * Human readable label
     *
     * @var string|null
     * @see get_label()
     */
    public $label;

    /**
     * Category type
     *
     * category for <category>
     * subject for <dc:subject>
     *
     * @var string|null
     * @see get_type()
     */
    public $type;

    /**
     * Constructor, used to input the data
     *
     * @param string|null $term
     * @param string|null $scheme
     * @param string|null $label
     * @param string|null $type
     */
    public function __construct(?string $term = null, ?string $scheme = null, ?string $label = null, ?string $type = null)
    {
        $this->term = $term;
        $this->scheme = $scheme;
        $this->label = $label;
        $this->type = $type;
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
     * Get the category identifier
     *
     * @return string|null
     */
    public function get_term()
    {
        return $this->term;
    }

    /**
     * Get the categorization scheme identifier
     *
     * @return string|null
     */
    public function get_scheme()
    {
        return $this->scheme;
    }

    /**
     * Get the human readable label
     *
     * @param bool $strict
     * @return string|null
     */
    public function get_label(bool $strict = false)
    {
        if ($this->label === null && $strict !== true) {
            return $this->get_term();
        }
        return $this->label;
    }

    /**
     * Get the category type
     *
     * @return string|null
     */
    public function get_type()
    {
        return $this->type;
    }
}

class_alias('SimplePie\Category', 'SimplePie_Category');

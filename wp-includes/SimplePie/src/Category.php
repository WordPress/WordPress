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

namespace SimplePie;

/**
 * Manages all category-related data
 *
 * Used by {@see \SimplePie\Item::get_category()} and {@see \SimplePie\Item::get_categories()}
 *
 * This class can be overloaded with {@see \SimplePie\SimplePie::set_category_class()}
 *
 * @package SimplePie
 * @subpackage API
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
    public function __construct($term = null, $scheme = null, $label = null, $type = null)
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
    public function get_label($strict = false)
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

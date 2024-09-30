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
 * Manages all author-related data
 *
 * Used by {@see Item::get_author()} and {@see SimplePie::get_authors()}
 *
 * This class can be overloaded with {@see SimplePie::set_author_class()}
 *
 * @package SimplePie
 * @subpackage API
 */
class Author
{
    /**
     * Author's name
     *
     * @var string
     * @see get_name()
     */
    public $name;

    /**
     * Author's link
     *
     * @var string
     * @see get_link()
     */
    public $link;

    /**
     * Author's email address
     *
     * @var string
     * @see get_email()
     */
    public $email;

    /**
     * Constructor, used to input the data
     *
     * @param string $name
     * @param string $link
     * @param string $email
     */
    public function __construct($name = null, $link = null, $email = null)
    {
        $this->name = $name;
        $this->link = $link;
        $this->email = $email;
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
     * Author's name
     *
     * @return string|null
     */
    public function get_name()
    {
        if ($this->name !== null) {
            return $this->name;
        }

        return null;
    }

    /**
     * Author's link
     *
     * @return string|null
     */
    public function get_link()
    {
        if ($this->link !== null) {
            return $this->link;
        }

        return null;
    }

    /**
     * Author's email address
     *
     * @return string|null
     */
    public function get_email()
    {
        if ($this->email !== null) {
            return $this->email;
        }

        return null;
    }
}

class_alias('SimplePie\Author', 'SimplePie_Author');

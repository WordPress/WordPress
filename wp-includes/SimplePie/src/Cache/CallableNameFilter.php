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
 * @copyright 2004-2022 Ryan Parman, Sam Sneddon, Ryan McCue
 * @author Ryan Parman
 * @author Sam Sneddon
 * @author Ryan McCue
 * @link http://simplepie.org/ SimplePie
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace SimplePie\Cache;

/**
 * Creating a cache filename with callables
 *
 * @package SimplePie
 * @subpackage Caching
 */
final class CallableNameFilter implements NameFilter
{
    /**
     * @var callable
     */
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * Method to create cache filename with.
     *
     * The returning name MUST follow the rules for keys in PSR-16.
     *
     * @link https://www.php-fig.org/psr/psr-16/
     *
     * The returning name MUST be a string of at least one character
     * that uniquely identifies a cached item, MUST only contain the
     * characters A-Z, a-z, 0-9, _, and . in any order in UTF-8 encoding
     * and MUST not longer then 64 characters. The following characters
     * are reserved for future extensions and MUST NOT be used: {}()/\@:
     *
     * A provided implementing library MAY support additional characters
     * and encodings or longer lengths, but MUST support at least that
     * minimum.
     *
     * @param string $name The name for the cache will be most likly an url with query string
     *
     * @return string the new cache name
     */
    public function filter(string $name): string
    {
        return call_user_func($this->callable, $name);
    }
}

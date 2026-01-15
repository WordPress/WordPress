<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie\Cache;

/**
 * Creating a cache filename with callables
 */
final class CallableNameFilter implements NameFilter
{
    /**
     * @var callable(string): string
     */
    private $callable;

    /**
     * @param callable(string): string $callable
     */
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
     * @param string $name The name for the cache will be most likely an url with query string
     *
     * @return string the new cache name
     */
    public function filter(string $name): string
    {
        return call_user_func($this->callable, $name);
    }
}

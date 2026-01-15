<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\Exception as SimplePieException;

class_exists('SimplePie\Exception');

// @trigger_error(sprintf('Using the "SimplePie_Exception" class is deprecated since SimplePie 1.7.0, use "SimplePie\Exception" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\Exception" instead */
    class SimplePie_Exception extends SimplePieException
    {
    }
}

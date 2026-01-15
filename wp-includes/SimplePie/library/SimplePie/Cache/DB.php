<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\Cache\DB;

class_exists('SimplePie\Cache\DB');

// @trigger_error(sprintf('Using the "SimplePie_Cache_DB" class is deprecated since SimplePie 1.7.0, use "SimplePie\Cache\DB" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\Cache\DB" instead */
    abstract class SimplePie_Cache_DB extends DB
    {
    }
}

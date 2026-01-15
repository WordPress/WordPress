<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\Cache\File;

class_exists('SimplePie\Cache\File');

// @trigger_error(sprintf('Using the "SimplePie_Cache_File" class is deprecated since SimplePie 1.7.0, use "SimplePie\Cache\File" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\Cache\File" instead */
    class SimplePie_Cache_File extends File
    {
    }
}

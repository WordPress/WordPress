<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\Cache\Base;

interface_exists('SimplePie\Cache\Base');

// @trigger_error(sprintf('Using the "SimplePie_Cache_Base" class is deprecated since SimplePie 1.7.0, use "SimplePie\Cache\Base" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\Cache\Base" instead */
    interface SimplePie_Cache_Base extends Base
    {
    }
}

<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\Gzdecode;

class_exists('SimplePie\Gzdecode');

// @trigger_error(sprintf('Using the "SimplePie_gzdecode" class is deprecated since SimplePie 1.7.0, use "SimplePie\Gzdecode" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\Gzdecode" instead */
    class SimplePie_gzdecode extends Gzdecode
    {
    }
}

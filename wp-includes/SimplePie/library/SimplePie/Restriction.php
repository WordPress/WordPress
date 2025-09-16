<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\Restriction;

class_exists('SimplePie\Restriction');

// @trigger_error(sprintf('Using the "SimplePie_Restriction" class is deprecated since SimplePie 1.7.0, use "SimplePie\Restriction" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\Restriction" instead */
    class SimplePie_Restriction extends Restriction
    {
    }
}

<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\Rating;

class_exists('SimplePie\Rating');

// @trigger_error(sprintf('Using the "SimplePie_Rating" class is deprecated since SimplePie 1.7.0, use "SimplePie\Rating" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\Rating" instead */
    class SimplePie_Rating extends Rating
    {
    }
}

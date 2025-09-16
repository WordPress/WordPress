<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\Caption;

class_exists('SimplePie\Caption');

// @trigger_error(sprintf('Using the "SimplePie_Caption" class is deprecated since SimplePie 1.7.0, use "SimplePie\Caption" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\Caption" instead */
    class SimplePie_Caption extends Caption
    {
    }
}

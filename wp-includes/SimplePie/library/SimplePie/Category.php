<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\Category;

class_exists('SimplePie\Category');

// @trigger_error(sprintf('Using the "SimplePie_Category" class is deprecated since SimplePie 1.7.0, use "SimplePie\Category" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\Category" instead */
    class SimplePie_Category extends Category
    {
    }
}

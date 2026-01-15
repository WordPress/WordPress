<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\Item;

class_exists('SimplePie\Item');

// @trigger_error(sprintf('Using the "SimplePie_Item" class is deprecated since SimplePie 1.7.0, use "SimplePie\Item" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\Item" instead */
    class SimplePie_Item extends Item
    {
    }
}

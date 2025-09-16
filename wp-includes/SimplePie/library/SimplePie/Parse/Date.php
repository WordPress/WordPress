<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\Parse\Date;

class_exists('SimplePie\Parse\Date');

// @trigger_error(sprintf('Using the "SimplePie_Parse_Date" class is deprecated since SimplePie 1.7.0, use "SimplePie\Parse\Date" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\Parse\Date" instead */
    class SimplePie_Parse_Date extends Date
    {
    }
}

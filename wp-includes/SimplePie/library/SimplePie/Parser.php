<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\Parser;

class_exists('SimplePie\Parser');

// @trigger_error(sprintf('Using the "SimplePie_Parser" class is deprecated since SimplePie 1.7.0, use "SimplePie\Parser" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\Parser" instead */
    class SimplePie_Parser extends Parser
    {
    }
}

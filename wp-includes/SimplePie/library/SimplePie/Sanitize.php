<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\Sanitize;

class_exists('SimplePie\Sanitize');

// @trigger_error(sprintf('Using the "SimplePie_Sanitize" class is deprecated since SimplePie 1.7.0, use "SimplePie\Sanitize" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\Sanitize" instead */
    class SimplePie_Sanitize extends Sanitize
    {
    }
}

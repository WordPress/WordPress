<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\Net\IPv6;

class_exists('SimplePie\Net\IPv6');

// @trigger_error(sprintf('Using the "SimplePie_Net_IPv6" class is deprecated since SimplePie 1.7.0, use "SimplePie\Net\IPv6" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\Net\IPv6" instead */
    class SimplePie_Net_IPv6 extends IPv6
    {
    }
}

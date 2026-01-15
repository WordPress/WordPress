<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\XML\Declaration\Parser;

class_exists('SimplePie\XML\Declaration\Parser');

// @trigger_error(sprintf('Using the "SimplePie_XML_Declaration_Parser" class is deprecated since SimplePie 1.7.0, use "SimplePie\XML\Declaration\Parser" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\XML\Declaration\Parser" instead */
    class SimplePie_XML_Declaration_Parser extends Parser
    {
    }
}

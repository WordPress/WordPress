<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie;

use Exception as NativeException;

/**
 * General SimplePie exception class
 */
class Exception extends NativeException
{
}

class_alias('SimplePie\Exception', 'SimplePie_Exception');

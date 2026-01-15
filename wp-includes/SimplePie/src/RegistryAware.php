<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie;

/**
 * Handles the injection of Registry into other class
 *
 * {@see \SimplePie\SimplePie::get_registry()}
 */
interface RegistryAware
{
    /**
     * Set the Registry into the class
     *
     * @return void
     */
    public function set_registry(Registry $registry);
}

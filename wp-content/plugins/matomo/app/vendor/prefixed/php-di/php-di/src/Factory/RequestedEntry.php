<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Factory;

/**
 * Represents the container entry that was requested.
 *
 * Implementations of this interface can be injected in factory parameters in order
 * to know what was the name of the requested entry.
 *
 * @api
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface RequestedEntry
{
    /**
     * Returns the name of the entry that was requested by the container.
     */
    public function getName() : string;
}

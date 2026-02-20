<?php

declare (strict_types=1);
namespace WordPress\AiClient\Common\Contracts;

/**
 * Interface for objects that cache data.
 *
 * @since 0.4.0
 */
interface CachesDataInterface
{
    /**
     * Invalidates all caches managed by this object.
     *
     * @since 0.4.0
     *
     * @return void
     */
    public function invalidateCaches(): void;
}

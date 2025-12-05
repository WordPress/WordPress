<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
declare (strict_types=1);
namespace DeviceDetector\Cache;

use Psr\SimpleCache\CacheInterface as PsrCacheInterface;
class PSR16Bridge implements \DeviceDetector\Cache\CacheInterface
{
    /**
     * @var PsrCacheInterface
     */
    private $cache;
    /**
     * PSR16Bridge constructor.
     * @param PsrCacheInterface $cache
     */
    public function __construct(PsrCacheInterface $cache)
    {
        $this->cache = $cache;
    }
    /**
     * @inheritDoc
     */
    public function fetch(string $id)
    {
        return $this->cache->get($id, \false);
    }
    /**
     * @inheritDoc
     */
    public function contains(string $id) : bool
    {
        return $this->cache->has($id);
    }
    /**
     * @inheritDoc
     */
    public function save(string $id, $data, int $lifeTime = 0) : bool
    {
        return $this->cache->set($id, $data, \func_num_args() < 3 ? null : $lifeTime);
    }
    /**
     * @inheritDoc
     */
    public function delete(string $id) : bool
    {
        return $this->cache->delete($id);
    }
    /**
     * @inheritDoc
     */
    public function flushAll() : bool
    {
        return $this->cache->clear();
    }
}

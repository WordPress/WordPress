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

use Doctrine\Common\Cache\CacheProvider;
class DoctrineBridge implements \DeviceDetector\Cache\CacheInterface
{
    /**
     * @var CacheProvider
     */
    private $cache;
    /**
     * @param CacheProvider $cache
     */
    public function __construct(CacheProvider $cache)
    {
        $this->cache = $cache;
    }
    /**
     * @inheritDoc
     */
    public function fetch(string $id)
    {
        return $this->cache->fetch($id);
    }
    /**
     * @inheritDoc
     */
    public function contains(string $id) : bool
    {
        return $this->cache->contains($id);
    }
    /**
     * @inheritDoc
     */
    public function save(string $id, $data, int $lifeTime = 0) : bool
    {
        return $this->cache->save($id, $data, $lifeTime);
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
        return $this->cache->flushAll();
    }
}

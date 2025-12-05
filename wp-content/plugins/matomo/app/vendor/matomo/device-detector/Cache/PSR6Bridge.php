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

use Psr\Cache\CacheItemPoolInterface;
class PSR6Bridge implements \DeviceDetector\Cache\CacheInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private $pool;
    /**
     * PSR6Bridge constructor.
     * @param CacheItemPoolInterface $pool
     */
    public function __construct(CacheItemPoolInterface $pool)
    {
        $this->pool = $pool;
    }
    /**
     * @inheritDoc
     */
    public function fetch(string $id)
    {
        $item = $this->pool->getItem($id);
        return $item->isHit() ? $item->get() : \false;
    }
    /**
     * @inheritDoc
     */
    public function contains(string $id) : bool
    {
        return $this->pool->hasItem($id);
    }
    /**
     * @inheritDoc
     */
    public function save(string $id, $data, int $lifeTime = 0) : bool
    {
        $item = $this->pool->getItem($id);
        $item->set($data);
        if (\func_num_args() > 2) {
            $item->expiresAfter($lifeTime);
        }
        return $this->pool->save($item);
    }
    /**
     * @inheritDoc
     */
    public function delete(string $id) : bool
    {
        return $this->pool->deleteItem($id);
    }
    /**
     * @inheritDoc
     */
    public function flushAll() : bool
    {
        return $this->pool->clear();
    }
}

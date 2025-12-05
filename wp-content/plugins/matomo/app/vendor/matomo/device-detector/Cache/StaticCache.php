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

/**
 * Class StaticCache
 *
 * Simple Cache that caches in a static property
 * (Speeds up multiple detections in one request)
 */
class StaticCache implements \DeviceDetector\Cache\CacheInterface
{
    /**
     * Holds the static cache data
     * @var array
     */
    protected static $staticCache = [];
    /**
     * @inheritdoc
     */
    public function fetch(string $id)
    {
        return $this->contains($id) ? self::$staticCache[$id] : \false;
    }
    /**
     * @inheritdoc
     */
    public function contains(string $id) : bool
    {
        return isset(self::$staticCache[$id]) || \array_key_exists($id, self::$staticCache);
    }
    /**
     * @inheritdoc
     */
    public function save(string $id, $data, int $lifeTime = 0) : bool
    {
        self::$staticCache[$id] = $data;
        return \true;
    }
    /**
     * @inheritdoc
     */
    public function delete(string $id) : bool
    {
        unset(self::$staticCache[$id]);
        return \true;
    }
    /**
     * @inheritdoc
     */
    public function flushAll() : bool
    {
        self::$staticCache = [];
        return \true;
    }
}

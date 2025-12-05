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

use Illuminate\Support\Facades\Cache;
class LaravelCache implements \DeviceDetector\Cache\CacheInterface
{
    /**
     * @inheritDoc
     */
    public function fetch(string $id)
    {
        return Cache::get($id);
    }
    /**
     * @inheritDoc
     */
    public function contains(string $id) : bool
    {
        return Cache::has($id);
    }
    /**
     * @inheritDoc
     */
    public function save(string $id, $data, int $lifeTime = 0) : bool
    {
        return Cache::put($id, $data, \func_num_args() < 3 ? null : $lifeTime);
    }
    /**
     * @inheritDoc
     */
    public function delete(string $id) : bool
    {
        return Cache::forget($id);
    }
    /**
     * @inheritDoc
     */
    public function flushAll() : bool
    {
        return Cache::flush();
    }
}

<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL v3 or later
 *
 */
namespace Matomo\Cache;

use Matomo\Cache\Backend;
/**
 * This cache uses one "cache" entry for all cache entries it contains.
 *
 * This comes handy for things that you need very often, nearly in every request. Instead of having to read eg.
 * a hundred caches from file we only load one file which contains the hundred cache ids. Should be used only for things
 * that you need very often and only for cache entries that are not too large to keep loading and parsing the single
 * cache entry fast.
 *
 * $cache = new Eager($backend, $storageId = 'eagercache');
 * // $cache->fetch('my'id')
 * // $cache->save('myid', 'test');
 *
 * // ... at some point or at the end of the request
 * $cache->persistCacheIfNeeded($lifeTime = 43200);
 */
class Eager implements \Matomo\Cache\Cache
{
    /**
     * @var Backend
     */
    private $storage;
    private $storageId;
    private $content = array();
    private $isDirty = \false;
    /**
     * Loads the cache entries from the given backend using the given storageId.
     *
     * @param Backend $storage
     * @param $storageId
     */
    public function __construct(Backend $storage, $storageId)
    {
        $this->storage = $storage;
        $this->storageId = $storageId;
        $content = $storage->doFetch($storageId);
        if (is_array($content)) {
            $this->content = $content;
        }
    }
    /**
     * Fetches an entry from the cache.
     *
     * Make sure to call the method {@link contains()} to verify whether there is actually any content saved under
     * this cache id.
     *
     * @param string $id The cache id.
     * @return int|float|string|boolean|array
     */
    public function fetch($id)
    {
        return $this->content[$id];
    }
    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return array_key_exists($id, $this->content);
    }
    /**
     * Puts data into the cache.
     *
     * @param string $id The cache id.
     * @param int|float|string|boolean|array $content
     * @param int $lifeTime Setting a lifetime is not supported by this cache and the parameter will be ignored.
     * @return boolean
     */
    public function save($id, $content, $lifeTime = 0)
    {
        if (is_object($content)) {
            throw new \InvalidArgumentException('You cannot use this cache to cache an object, only arrays, strings and numbers. Have a look at Transient cache.');
            // for performance reasons we do currently not recursively search whether any array contains an object.
        }
        $this->content[$id] = $content;
        $this->isDirty = \true;
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        if ($this->contains($id)) {
            $this->isDirty = \true;
            unset($this->content[$id]);
            return \true;
        }
        return \false;
    }
    /**
     * {@inheritdoc}
     */
    public function flushAll()
    {
        $this->storage->doDelete($this->storageId);
        $this->content = array();
        $this->isDirty = \false;
        return \true;
    }
    /**
     * Will persist all previously made changes if there were any.
     *
     * @param int $lifeTime  The cache lifetime in seconds.
     *                       If != 0, sets a specific lifetime for this cache entry (0 => infinite lifeTime).
     */
    public function persistCacheIfNeeded($lifeTime)
    {
        if ($this->isDirty) {
            $this->storage->doSave($this->storageId, $this->content, $lifeTime);
        }
    }
}

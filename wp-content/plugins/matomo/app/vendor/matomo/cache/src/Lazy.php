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
class Lazy implements \Matomo\Cache\Cache
{
    private $backend;
    /**
     * Initializes the cache.
     *
     * @param Backend $backend Any backend that should be used to store / hold the cache entries.
     */
    public function __construct(Backend $backend)
    {
        $this->backend = $backend;
    }
    /**
     * Fetches an entry from the cache.
     *
     * @param string $id The cache id.
     * @return mixed The cached data or FALSE, if no cache entry exists for the given id.
     */
    public function fetch($id)
    {
        $id = $this->getCompletedCacheIdIfValid($id);
        return $this->backend->doFetch($id);
    }
    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        $id = $this->getCompletedCacheIdIfValid($id);
        return $this->backend->doContains($id);
    }
    /**
     * Puts data into the cache.
     *
     * @param string $id       The cache id.
     * @param mixed  $data     The cache entry/data.
     * @param int    $lifeTime The cache lifetime in seconds.
     *                         If != 0, sets a specific lifetime for this cache entry (0 => infinite lifeTime).
     *
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    public function save($id, $data, $lifeTime = 0)
    {
        $id = $this->getCompletedCacheIdIfValid($id);
        if (is_object($data)) {
            throw new \InvalidArgumentException('You cannot use this cache to cache an object, only arrays, strings and numbers. Have a look at Transient cache.');
            // for performance reasons we do currently not recursively search whether any array contains an object.
        }
        return $this->backend->doSave($id, $data, $lifeTime);
    }
    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $id = $this->getCompletedCacheIdIfValid($id);
        return $this->backend->doDelete($id);
    }
    /**
     * {@inheritdoc}
     */
    public function flushAll()
    {
        return $this->backend->doFlush();
    }
    private function getCompletedCacheIdIfValid($id)
    {
        $this->checkId($id);
        return 'matomocache_' . $id;
    }
    private function checkId($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Empty cache id given');
        }
        if (!$this->isValidId($id)) {
            throw new \InvalidArgumentException("Invalid cache id request {$id}");
        }
    }
    /**
     * Returns true if the string is a valid id.
     *
     * Id that start with a-Z or 0-9 and contain a-Z, 0-9, underscore(_), dash(-), and dot(.) will be accepted.
     * Id beginning with anything but a-Z or 0-9 will be rejected (including .htaccess for example).
     * Id containing anything other than above mentioned will also be rejected (file names with spaces won't be accepted).
     *
     * @param string $id
     * @return bool
     */
    private function isValidId($id)
    {
        return 0 !== preg_match('/(^[a-zA-Z0-9]+([a-zA-Z_0-9.-]*))$/D', $id);
    }
}

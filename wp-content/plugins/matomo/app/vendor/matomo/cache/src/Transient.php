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
 * This class is used to cache data during one request.
 *
 * Compared to the lazy cache it does not support setting any lifetime. To be a fast cache it does
 * not validate any cache id etc.
 */
class Transient implements \Matomo\Cache\Cache
{
    /**
     * @var array $data
     */
    private $data = array();
    /**
     * Fetches an entry from the cache.
     *
     * Make sure to call the method {@link has()} to verify whether there is actually any content set under this
     * cache id.
     *
     * @param string $id The cache id.
     * @return mixed
     */
    public function fetch($id)
    {
        if ($this->contains($id)) {
            return $this->data[$id];
        }
        return \false;
    }
    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return isset($this->data[$id]) || array_key_exists($id, $this->data);
    }
    /**
     * Puts data into the cache.
     *
     * @param string $id The cache id.
     * @param mixed $content
     * @param int $lifeTime Setting a lifetime is not supported by this cache and the parameter will be ignored.
     * @return boolean
     */
    public function save($id, $content, $lifeTime = 0)
    {
        $this->data[$id] = $content;
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        if (!$this->contains($id)) {
            return \false;
        }
        unset($this->data[$id]);
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function flushAll()
    {
        $this->data = array();
        return \true;
    }
}

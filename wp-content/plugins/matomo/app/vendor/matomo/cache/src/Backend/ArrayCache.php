<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL v3 or later
 *
 */
namespace Matomo\Cache\Backend;

use Matomo\Cache\Backend;
class ArrayCache implements Backend
{
    private $data = array();
    /**
     * {@inheritdoc}
     */
    public function doFetch($id)
    {
        return $this->doContains($id) ? $this->data[$id] : \false;
    }
    /**
     * {@inheritdoc}
     */
    public function doContains($id)
    {
        // isset() is required for performance optimizations, to avoid unnecessary function calls to array_key_exists.
        return isset($this->data[$id]) || array_key_exists($id, $this->data);
    }
    /**
     * {@inheritdoc}
     */
    public function doSave($id, $data, $lifeTime = 0)
    {
        $this->data[$id] = $data;
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function doDelete($id)
    {
        unset($this->data[$id]);
        return \true;
    }
    public function doFlush()
    {
        $this->data = array();
        return \true;
    }
}

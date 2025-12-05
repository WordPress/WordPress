<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL v3 or later
 *
 */
namespace Matomo\Cache\Backend;

use Doctrine\Common\Cache\RedisCache;
use Matomo\Cache\Backend;
class Redis extends RedisCache implements Backend
{
    public function doFetch($id)
    {
        return parent::doFetch($id);
    }
    public function doContains($id)
    {
        return parent::doContains($id);
    }
    public function doSave($id, $data, $lifeTime = 0)
    {
        return parent::doSave($id, $data, $lifeTime);
    }
    public function doDelete($id)
    {
        return parent::doDelete($id);
    }
    public function doFlush()
    {
        return parent::doFlush();
    }
}

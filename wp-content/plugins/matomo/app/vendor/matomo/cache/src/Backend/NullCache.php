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
/**
 * Can be used in development to prevent caching. Does not cache anything.
 */
class NullCache implements Backend
{
    public function doFetch($id)
    {
        return \false;
    }
    public function doContains($id)
    {
        return \false;
    }
    public function doSave($id, $data, $lifeTime = 0)
    {
        return \true;
    }
    public function doDelete($id)
    {
        return \true;
    }
    public function doFlush()
    {
        return \true;
    }
}

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
class BaseDecorator implements Backend
{
    /**
     * @var Backend
     */
    protected $decorated;
    /**
     * Constructor.
     *
     * @param Backend   $decorated Wrapped backend to apply TTL to.
     * @param array     $options includes default TTL to be used.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($decorated)
    {
        $this->decorated = $decorated;
    }
    public function doFetch($id)
    {
        return $this->decorated->doFetch($id);
    }
    public function doContains($id)
    {
        return $this->decorated->doContains($id);
    }
    public function doSave($id, $data, $lifeTime = 0)
    {
        return $this->decorated->doSave($id, $data, $lifeTime);
    }
    public function doDelete($id)
    {
        return $this->decorated->doDelete($id);
    }
    public function doFlush()
    {
        return $this->decorated->doFlush();
    }
    public function getBackend()
    {
        return $this->decorated;
    }
}

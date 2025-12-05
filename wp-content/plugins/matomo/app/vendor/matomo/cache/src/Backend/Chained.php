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
 * TODO: extend Doctrine ChainCache as soon as available
 */
class Chained implements Backend
{
    /**
     * @var Backend[]
     */
    private $backends = array();
    /**
     * Initializes the chained backend.
     *
     * @param Backend[] $backends An array of backends to use. They should be ordered from fastest to slowest.
     */
    public function __construct($backends = array())
    {
        $this->backends = array_values($backends);
    }
    public function getBackends()
    {
        return $this->backends;
    }
    public function doFetch($id)
    {
        foreach ($this->backends as $key => $backend) {
            $value = $backend->doFetch($id);
            if ($value !== \false) {
                // EG If chain is ARRAY => REDIS => DB and we find result in DB we will update REDIS and ARRAY
                for ($subKey = $key - 1; $subKey >= 0; $subKey--) {
                    $this->backends[$subKey]->doSave($id, $value, 300);
                    // TODO we should use the actual TTL here
                }
                return $value;
            }
        }
        return \false;
    }
    public function doContains($id)
    {
        foreach ($this->backends as $backend) {
            if ($backend->doContains($id)) {
                return \true;
            }
        }
        return \false;
    }
    public function doSave($id, $data, $lifeTime = 0)
    {
        $stored = \true;
        foreach ($this->backends as $backend) {
            $stored = $backend->doSave($id, $data, $lifeTime) && $stored;
        }
        return $stored;
    }
    // returns true even when file does not exist
    public function doDelete($id)
    {
        foreach ($this->backends as $backend) {
            if ($backend->doContains($id)) {
                $backend->doDelete($id);
            }
        }
        return \true;
    }
    public function doFlush()
    {
        $flushed = \true;
        foreach ($this->backends as $backend) {
            $flushed = $backend->doFlush() && $flushed;
        }
        return $flushed;
    }
}

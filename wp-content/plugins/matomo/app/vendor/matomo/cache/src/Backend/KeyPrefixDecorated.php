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
class KeyPrefixDecorated extends \Matomo\Cache\Backend\BaseDecorator
{
    /**
     * @var string
     */
    private $keyPrefix;
    /**
     * Constructor.
     *
     * @param Backend   $decorated Wrapped backend to apply TTL to.
     * @param array     $options includes the string to prefix the key with
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($decorated, $options)
    {
        if (!isset($options['keyPrefix']) || !is_string($options['keyPrefix'])) {
            throw new \InvalidArgumentException("The keyPrefix option is required and must be a string");
        }
        $this->keyPrefix = $options['keyPrefix'];
        parent::__construct($decorated);
    }
    public function doFetch($id)
    {
        return $this->decorated->doFetch($this->keyPrefix . $id);
    }
    public function doContains($id)
    {
        return $this->decorated->doContains($this->keyPrefix . $id);
    }
    public function doSave($id, $data, $lifeTime = 0)
    {
        return $this->decorated->doSave($this->keyPrefix . $id, $data, $lifeTime);
    }
    public function doDelete($id)
    {
        return $this->decorated->doDelete($this->keyPrefix . $id);
    }
}

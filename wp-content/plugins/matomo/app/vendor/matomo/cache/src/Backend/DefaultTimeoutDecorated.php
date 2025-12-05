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
class DefaultTimeoutDecorated extends \Matomo\Cache\Backend\BaseDecorator
{
    /**
     * @var integer
     */
    private $defaultTTL;
    /**
     * Constructor.
     *
     * @param Backend   $decorated Wrapped backend to apply TTL to.
     * @param array     $options includes default TTL to be used.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($decorated, $options)
    {
        if (!isset($options['defaultTimeout']) || !is_int($options['defaultTimeout'])) {
            throw new \InvalidArgumentException("The defaultTimeout option is required and must be an integer");
        }
        $this->defaultTTL = $options['defaultTimeout'];
        parent::__construct($decorated);
    }
    public function doSave($id, $data, $lifeTime = 0)
    {
        return $this->decorated->doSave($id, $data, $lifeTime ?: $this->defaultTTL);
    }
}

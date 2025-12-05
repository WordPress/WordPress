<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Model\Container;

/**
 * @ignore for tests only
 */
class FixedIdGenerator implements \Piwik\Plugins\TagManager\Model\Container\ContainerIdGenerator
{
    /**
     * @var string
     */
    private $startId = 0;
    public function generateId()
    {
        $this->startId++;
        return 'aaatest' . $this->startId;
    }
}

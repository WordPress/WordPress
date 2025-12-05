<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Model\Container;

use Piwik\Common;
use Piwik\Plugins\TagManager\Dao\ContainersDao;
class RandomContainerIdGenerator implements \Piwik\Plugins\TagManager\Model\Container\ContainerIdGenerator
{
    /**
     * @var ContainersDao
     */
    private $dao;
    public function __construct(ContainersDao $containersDao)
    {
        $this->dao = $containersDao;
    }
    public function generateId()
    {
        $numTries = 0;
        do {
            $numTries++;
            // we do not use "0" to avoid any potential problems with a starting zero eg in preview mode detection when
            // ignoring a preview we check for starts with zero
            $idContainer = Common::getRandomString($len = 1, $alphabet = "abcdefghijklmnoprstuvwxyzABCDEFGHIJKLMNOPRSTUVWXYZ123456789");
            $idContainer .= Common::getRandomString($len = 7, $alphabet = "abcdefghijklmnoprstuvwxyzABCDEFGHIJKLMNOPRSTUVWXYZ0123456789");
        } while ($this->dao->hasContainer($idContainer) && $numTries < 100);
        return $idContainer;
    }
}

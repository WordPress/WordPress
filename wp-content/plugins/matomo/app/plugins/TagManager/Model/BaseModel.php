<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Model;

use Piwik\Container\StaticContainer;
use Piwik\Date;
use Piwik\Piwik;
use Piwik\Site;
class BaseModel
{
    private $now;
    /**
     * @ignore tests only
     * @param string $now
     */
    public function setCurrentDateTime($now)
    {
        $this->now = $now;
    }
    protected function getCurrentDateTime()
    {
        if (isset($this->now)) {
            return $this->now;
        }
        return Date::now()->getDatetime();
    }
    protected function formatDate($date, $idSite)
    {
        $timezone = Site::getTimezoneFor($idSite);
        return Date::factory($date, $timezone)->getLocalized(Date::DATETIME_FORMAT_SHORT);
    }
    protected function getDraftContainerVersion(int $idSite, string $idContainer) : int
    {
        $container = StaticContainer::get(\Piwik\Plugins\TagManager\Model\Container::class)->getContainer($idSite, $idContainer);
        if (empty($container)) {
            throw new \Exception(Piwik::translate('TagManager_ErrorContainerDoesNotExist', [$idContainer]));
        }
        // Copy the new trigger to the draft version of the destination container
        $idContainerVersion = $container['draft']['idcontainerversion'] ?? null;
        // Make sure that the version is set and is an integer value
        if (empty($idContainerVersion) || !(is_int($idContainerVersion) || is_string($idContainerVersion) && ctype_digit($idContainerVersion))) {
            throw new \Exception(Piwik::translate('TagManager_ErrorContainerVersionDoesNotExist'));
        }
        // Make sure that the type is int
        return intval($idContainerVersion);
    }
}

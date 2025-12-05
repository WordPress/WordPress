<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Activity;

class VersionDeleted extends \Piwik\Plugins\TagManager\Activity\VersionBaseActivity
{
    protected $eventName = 'TagManager.deleteContainerVersion.end';
    public function extractParams($eventData)
    {
        if (empty($eventData[0]) || !is_array($eventData[0])) {
            return \false;
        }
        $info = $eventData[0];
        return $this->formatActivityData($info['idSite'], $info['idContainer'], $info['idContainerVersion'], 0);
    }
    public function getTranslatedDescription($activityData, $performingUser)
    {
        $siteName = $this->getSiteNameFromActivityData($activityData);
        $entityName = $this->getEntityNameFromActivityData($activityData);
        $containerName = $this->getContainerNameFromActivityData($activityData);
        $desc = sprintf('deleted the version "%1$s" from container "%2$s" for site "%3$s"', $entityName, $containerName, $siteName);
        return $desc;
    }
}

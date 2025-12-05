<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Activity;

class ContainerDeleted extends \Piwik\Plugins\TagManager\Activity\BaseActivity
{
    protected $eventName = 'TagManager.deleteContainer.end';
    public function extractParams($eventData)
    {
        if (empty($eventData[0]) || !is_array($eventData[0])) {
            return \false;
        }
        $info = $eventData[0];
        return $this->formatActivityData($info['idSite'], $info['idContainer'], 0, 0);
    }
    public function getTranslatedDescription($activityData, $performingUser)
    {
        $siteName = $this->getSiteNameFromActivityData($activityData);
        $containerName = $this->getContainerNameFromActivityData($activityData);
        $desc = sprintf('deleted the container "%1$s" for site "%2$s"', $containerName, $siteName);
        return $desc;
    }
}

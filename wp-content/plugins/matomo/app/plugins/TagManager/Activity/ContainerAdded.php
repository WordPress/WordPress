<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Activity;

class ContainerAdded extends \Piwik\Plugins\TagManager\Activity\TagBaseActivity
{
    protected $eventName = 'API.TagManager.addContainer.end';
    public function extractParams($eventData)
    {
        list($idContainer, $finalAPIParameters) = $eventData;
        $idSite = $finalAPIParameters['parameters']['idSite'];
        return $this->formatActivityData($idSite, $idContainer, 0, 0);
    }
    public function getTranslatedDescription($activityData, $performingUser)
    {
        $siteName = $this->getSiteNameFromActivityData($activityData);
        $containerName = $this->getContainerNameFromActivityData($activityData);
        // we do not translate them as it would otherwise use the language of the currently logged in user
        $desc = sprintf('added a container "%1$s" for site "%2$s"', $containerName, $siteName);
        return $desc;
    }
}

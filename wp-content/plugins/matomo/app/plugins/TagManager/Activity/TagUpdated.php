<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Activity;

class TagUpdated extends \Piwik\Plugins\TagManager\Activity\TagBaseActivity
{
    protected $eventName = 'API.TagManager.updateContainerTag.end';
    public function extractParams($eventData)
    {
        if (!$this->hasRequestedApiMethod('updateContainerTag')) {
            return \false;
        }
        list($return, $finalAPIParameters) = $eventData;
        $idEntity = $finalAPIParameters['parameters']['idTag'];
        $idSite = $finalAPIParameters['parameters']['idSite'];
        $idContainer = $finalAPIParameters['parameters']['idContainer'];
        $idContainerVersion = $finalAPIParameters['parameters']['idContainerVersion'];
        return $this->formatActivityData($idSite, $idContainer, $idContainerVersion, $idEntity);
    }
    public function getTranslatedDescription($activityData, $performingUser)
    {
        $siteName = $this->getSiteNameFromActivityData($activityData);
        $entityName = $this->getEntityNameFromActivityData($activityData);
        $containerName = $this->getContainerNameFromActivityData($activityData);
        $desc = sprintf('updated the tag "%1$s" in container "%2$s" for site "%3$s"', $entityName, $containerName, $siteName);
        return $desc;
    }
}

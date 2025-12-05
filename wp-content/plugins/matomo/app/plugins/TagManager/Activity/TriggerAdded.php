<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Activity;

class TriggerAdded extends \Piwik\Plugins\TagManager\Activity\TriggerBaseActivity
{
    protected $eventName = 'API.TagManager.addContainerTrigger.end';
    public function extractParams($eventData)
    {
        if (!$this->hasRequestedApiMethod('addContainerTrigger')) {
            return \false;
        }
        list($idEntity, $finalAPIParameters) = $eventData;
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
        // we do not translate them as it would otherwise use the language of the currently logged in user
        $desc = sprintf('added a trigger "%1$s" to container "%2$s" for site "%3$s"', $entityName, $containerName, $siteName);
        return $desc;
    }
}

<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Activity;

class VersionPublished extends \Piwik\Plugins\TagManager\Activity\VersionBaseActivity
{
    protected $eventName = 'API.TagManager.publishContainerVersion.end';
    public function extractParams($eventData)
    {
        list($return, $finalAPIParameters) = $eventData;
        $idSite = $finalAPIParameters['parameters']['idSite'];
        $idContainer = $finalAPIParameters['parameters']['idContainer'];
        $idContainerVersion = $finalAPIParameters['parameters']['idContainerVersion'];
        $activityData = $this->formatActivityData($idSite, $idContainer, $idContainerVersion, 0);
        $activityData['environment'] = $finalAPIParameters['parameters']['environment'];
        return $activityData;
    }
    public function getTranslatedDescription($activityData, $performingUser)
    {
        $siteName = $this->getSiteNameFromActivityData($activityData);
        $entityName = $this->getEntityNameFromActivityData($activityData);
        $containerName = $this->getContainerNameFromActivityData($activityData);
        if (!empty($activityData['environment'])) {
            $environment = $activityData['environment'];
        } else {
            $environment = '-';
        }
        $desc = sprintf('published the version "%1$s" in container "%2$s" to environment "%3$s" for site "%4$s"', $entityName, $containerName, $environment, $siteName);
        return $desc;
    }
}

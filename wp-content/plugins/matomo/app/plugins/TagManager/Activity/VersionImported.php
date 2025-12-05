<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Activity;

class VersionImported extends \Piwik\Plugins\TagManager\Activity\BaseActivity
{
    protected $eventName = 'API.TagManager.importContainerVersion.end';
    public function extractParams($eventData)
    {
        list($return, $finalAPIParameters) = $eventData;
        $idSite = $finalAPIParameters['parameters']['idSite'];
        $idContainer = $finalAPIParameters['parameters']['idContainer'];
        $activityData = $this->formatActivityData($idSite, $idContainer, 0, 0);
        if (!empty($finalAPIParameters['parameters']['backupName'])) {
            $activityData['backupName'] = $finalAPIParameters['parameters']['backupName'];
        } else {
            $activityData['backupName'] = '';
        }
        return $activityData;
    }
    public function getTranslatedDescription($activityData, $performingUser)
    {
        $siteName = $this->getSiteNameFromActivityData($activityData);
        $containerName = $this->getContainerNameFromActivityData($activityData);
        $desc = sprintf('imported a new draft version in container "%1$s" for site "%2$s"', $containerName, $siteName);
        if (!empty($activityData['backupName'])) {
            $desc .= sprintf(' and a backup version %s was created ', $activityData['backupName']);
        }
        return $desc;
    }
}

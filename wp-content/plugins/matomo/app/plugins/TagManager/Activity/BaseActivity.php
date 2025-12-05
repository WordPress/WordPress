<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Activity;

use Piwik\API\Request;
use Piwik\Container\StaticContainer;
use Piwik\Piwik;
use Piwik\Plugins\ActivityLog\Activity\Activity;
use Piwik\Site;
abstract class BaseActivity extends Activity
{
    protected $entityType = '';
    protected function hasRequestedApiMethod($method)
    {
        if (method_exists('Piwik\\API\\Request', 'getRootApiRequestMethod')) {
            $method = 'TagManager.' . $method;
            return $method === Request::getRootApiRequestMethod();
        }
        return \false;
    }
    protected function getContainerNameFromActivityData($activityData)
    {
        if (!empty($activityData['container']['name'])) {
            return $activityData['container']['name'];
        }
        if (!empty($activityData['container']['id'])) {
            return $activityData['container']['id'];
        }
        return '';
    }
    protected function getEntityNameFromActivityData($activityData)
    {
        if (!empty($activityData[$this->entityType]['name'])) {
            return $activityData[$this->entityType]['name'];
        }
        if (!empty($activityData[$this->entityType]['id'])) {
            return $activityData[$this->entityType]['id'];
        }
        return '';
    }
    protected function getSiteNameFromActivityData($activityData)
    {
        if (!empty($activityData['site']['site_name'])) {
            return $activityData['site']['site_name'];
        }
        if (!empty($activityData['site']['site_id'])) {
            return $activityData['site']['site_id'];
        }
        return '';
    }
    protected function formatActivityData($idSite, $idContainer, $idContainerVersion, $idEntity)
    {
        if (!is_numeric($idSite) || !is_numeric($idEntity)) {
            return;
        }
        $params = array('site' => $this->getSiteData($idSite), 'version' => 'v1', 'container' => $this->getContainerData($idSite, $idContainer));
        if ($this->entityType) {
            $params[$this->entityType] = $this->getEntityData($idSite, $idContainer, $idContainerVersion, $idEntity);
        }
        return $params;
    }
    private function getSiteData($idSite)
    {
        return array('site_id' => $idSite, 'site_name' => Site::getNameFor($idSite));
    }
    protected function getContainerData($idSite, $idContainer)
    {
        $container = $this->getContainerDao()->getContainer($idSite, $idContainer);
        if (!empty($container['name'])) {
            $containerName = $container['name'];
        } else {
            // container name might not be set when we are handling "deleted" activity
            $containerName = 'ID: ' . $idContainer;
        }
        return array('id' => $idContainer, 'name' => $containerName);
    }
    private function getContainerDao()
    {
        return StaticContainer::get('Piwik\\Plugins\\TagManager\\Dao\\ContainersDao');
    }
    protected function getEntityData($idSite, $idContainer, $idContainerVersion, $idEntity)
    {
        return array('id' => $idEntity, 'name' => '');
    }
    public function getPerformingUser($eventData = null)
    {
        $login = Piwik::getCurrentUserLogin();
        if ($login === self::USER_ANONYMOUS || empty($login)) {
            // anonymous cannot change an entity, in this case the system changed it
            return self::USER_SYSTEM;
        }
        return $login;
    }
}

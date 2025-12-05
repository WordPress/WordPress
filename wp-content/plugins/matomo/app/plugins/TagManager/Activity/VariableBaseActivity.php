<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Activity;

use Piwik\Container\StaticContainer;
abstract class VariableBaseActivity extends \Piwik\Plugins\TagManager\Activity\BaseActivity
{
    protected $entityType = 'containervariable';
    private function getEntityDao()
    {
        return StaticContainer::get('Piwik\\Plugins\\TagManager\\Dao\\VariablesDao');
    }
    protected function getEntityData($idSite, $idContainer, $idContainerVersion, $idEntity)
    {
        $entity = $this->getEntityDao()->getContainerVariable($idSite, $idContainerVersion, $idEntity);
        if (!empty($entity['name'])) {
            $entityName = $entity['name'];
        } else {
            // entity might not be set when we are handling "deleted" activity
            $entityName = 'ID: ' . (int) $idEntity;
        }
        return array('id' => $idEntity, 'name' => $entityName);
    }
}

<?php

namespace Piwik\Plugins\TagManager\UpdateHelper;

use Piwik\Container\StaticContainer;
use Piwik\Date;
use Piwik\Plugins\TagManager\Dao\ContainersDao;
use Piwik\Plugins\TagManager\Dao\ContainerVersionsDao;
use Piwik\Plugins\TagManager\Dao\VariablesDao;
class NewVariableParameterMigrator
{
    private $containersDao;
    private $containerVersionsDao;
    private $variablesDao;
    private $variablesModel;
    private $variableType;
    private $fieldMap;
    /**
     * @param string $variableType Indicates the type of variable that needs to be migrated, such as 'MatomoConfiguration'
     * @param string $variableFieldName The name of the new field being added to the parameters JSON.
     * @param string $defaultFieldValue The value to default the parameter to. The default is an empty string.
     */
    public function __construct($variableType, $variableFieldName, $defaultFieldValue = '')
    {
        $this->containersDao = new ContainersDao();
        $this->containerVersionsDao = new ContainerVersionsDao();
        $this->variablesDao = new VariablesDao();
        $this->variablesModel = StaticContainer::get('Piwik\\Plugins\\TagManager\\Model\\Variable');
        $this->variablesModel->setCurrentDateTime(gmdate(Date::DATE_TIME_FORMAT));
        $this->variableType = $variableType;
        $this->fieldMap = [$variableFieldName => $defaultFieldValue];
    }
    /**
     * Specify an additional field to include in the migration.
     *
     * @param string $variableFieldName The name of the new field being added to the parameters JSON.
     * @param string $defaultFieldValue The value to default the parameter to. The default is an empty string.
     */
    public function addField($variableFieldName, $defaultFieldValue = '')
    {
        $this->fieldMap[$variableFieldName] = $defaultFieldValue;
    }
    /**
     * Execute the migration of the tags of the specified type and need the field added to their parameters JSON.
     *
     * @return void
     */
    public function migrate()
    {
        $activeContainersInfo = $this->containersDao->getActiveContainersInfo();
        if (!is_array($activeContainersInfo) || !count($activeContainersInfo)) {
            return;
        }
        foreach ($activeContainersInfo as $container) {
            $this->processContainer($container['idsite'], $container['idcontainer']);
        }
    }
    private function processContainer($idSite, $idContainer)
    {
        $activeContainerVersionsInfo = $this->containerVersionsDao->getVersionsOfContainer($idSite, $idContainer);
        $activeContainerVersionsInfo = !is_array($activeContainerVersionsInfo) ? [] : $activeContainerVersionsInfo;
        $draftVersion = $this->containerVersionsDao->getDraftVersion($idSite, $idContainer);
        if (is_array($draftVersion)) {
            $activeContainerVersionsInfo[] = $draftVersion;
        }
        foreach ($activeContainerVersionsInfo as $version) {
            $this->processVersion($idSite, $version['idcontainerversion']);
        }
    }
    private function processVersion($idSite, $idVersion)
    {
        $activeVariableIds = $this->variablesDao->getContainerVariableIdsByType($idSite, $idVersion, $this->variableType);
        if (!is_array($activeVariableIds) || !count($activeVariableIds)) {
            return;
        }
        foreach ($activeVariableIds as $idVariable) {
            $this->updateVariableParameters($idSite, $idVersion, $idVariable);
        }
    }
    private function updateVariableParameters($idSite, $idVersion, $idVariable)
    {
        $variableInfo = $this->variablesDao->getContainerVariable($idSite, $idVersion, $idVariable);
        if (empty($variableInfo['parameters'])) {
            return;
        }
        foreach ($this->fieldMap as $key => $value) {
            // It shouldn't ever already exist, but let's be sure we don't overwrite existing values.
            if (empty($variableInfo['parameters'][$key])) {
                $variableInfo['parameters'][$key] = $value;
            }
        }
        // We use the model method to make sure that the parameters are set in the correct order.
        $this->variablesModel->updateContainerVariable($idSite, $idVersion, $idVariable, $variableInfo['name'], $variableInfo['parameters'], $variableInfo['default_value'], $variableInfo['lookup_table'], $variableInfo['description']);
    }
}

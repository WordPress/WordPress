<?php

namespace Piwik\Plugins\TagManager\UpdateHelper;

use Piwik\Container\StaticContainer;
use Piwik\Date;
use Piwik\Plugins\TagManager\Dao\ContainersDao;
use Piwik\Plugins\TagManager\Dao\ContainerVersionsDao;
use Piwik\Plugins\TagManager\Dao\TagsDao;
class NewTagParameterMigrator
{
    private $containersDao;
    private $containerVersionsDao;
    private $tagsDao;
    private $tagsModel;
    private $tagType;
    private $fieldMap;
    /**
     * @param string $tagType Indicates the type of tag that needs to be migrated, such as 'Matomo', 'CustomHtml', ...
     * @param string $tagFieldName The name of the new field being added to the parameters JSON.
     * @param string $defaultFieldValue The value to default the parameter to. The default is an empty string.
     */
    public function __construct($tagType, $tagFieldName, $defaultFieldValue = '')
    {
        $this->containersDao = new ContainersDao();
        $this->containerVersionsDao = new ContainerVersionsDao();
        $this->tagsDao = new TagsDao();
        $this->tagsModel = StaticContainer::get('Piwik\\Plugins\\TagManager\\Model\\Tag');
        $this->tagsModel->setCurrentDateTime(gmdate(Date::DATE_TIME_FORMAT));
        $this->tagType = $tagType;
        $this->fieldMap = [$tagFieldName => $defaultFieldValue];
    }
    /**
     * Specify an additional field to include in the migration.
     *
     * @param string $tagFieldName The name of the new field being added to the parameters JSON.
     * @param string $defaultFieldValue The value to default the parameter to. The default is an empty string.
     */
    public function addField($tagFieldName, $defaultFieldValue = '')
    {
        $this->fieldMap[$tagFieldName] = $defaultFieldValue;
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
        $activeTagIds = $this->tagsDao->getContainerTagIdsByType($idSite, $idVersion, $this->tagType);
        if (!is_array($activeTagIds) || !count($activeTagIds)) {
            return;
        }
        foreach ($activeTagIds as $idTag) {
            $this->updateTagParameters($idSite, $idVersion, $idTag);
        }
    }
    private function updateTagParameters($idSite, $idVersion, $idTag)
    {
        $tagInfo = $this->tagsDao->getContainerTag($idSite, $idVersion, $idTag);
        foreach ($this->fieldMap as $key => $value) {
            // It shouldn't ever already exist, but let's be sure we don't overwrite existing values.
            if (empty($tagInfo['parameters'][$key])) {
                $tagInfo['parameters'][$key] = $value;
            }
        }
        // We use the model method to make sure that the parameters are set in the correct order.
        $this->tagsModel->updateParameters($idSite, $idVersion, $idTag, $tagInfo['parameters']);
    }
}

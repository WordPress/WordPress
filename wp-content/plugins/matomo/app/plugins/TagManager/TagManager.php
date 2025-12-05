<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager;

use Piwik\Access;
use Piwik\API\Request;
use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Date;
use Piwik\Exception\UnexpectedWebsiteFoundException;
use Piwik\Log;
use Piwik\Piwik;
use Piwik\Plugin;
use Piwik\Plugins\SitesManager\SiteContentDetection\ReactJs;
use Piwik\Plugin\Manager;
use Piwik\Plugins\TagManager\Access\Capability\PublishLiveContainer;
use Piwik\Plugins\TagManager\Access\Capability\TagManagerWrite;
use Piwik\Plugins\TagManager\Access\Capability\UseCustomTemplates;
use Piwik\Plugins\TagManager\API\PreviewCookie;
use Piwik\Plugins\TagManager\Context\BaseContext;
use Piwik\Plugins\TagManager\Dao\ContainerReleaseDao;
use Piwik\Plugins\TagManager\Dao\ContainerVersionsDao;
use Piwik\Plugins\TagManager\Dao\TagManagerDao;
use Piwik\Plugins\TagManager\Dao\ContainersDao;
use Piwik\Plugins\TagManager\Dao\TagsDao;
use Piwik\Plugins\TagManager\Dao\TriggersDao;
use Piwik\Plugins\TagManager\Dao\VariablesDao;
use Piwik\Plugins\CoreHome\SystemSummary;
use Piwik\Plugins\TagManager\Model\Container\ContainerIdGenerator;
use Piwik\Plugins\TagManager\Model\Salt;
use Piwik\Site;
use Piwik\SiteContentDetector;
use Piwik\Url;
use Piwik\View;
use Piwik\Context;
use Piwik\Log\LoggerInterface;
use Piwik\SettingsPiwik;
class TagManager extends \Piwik\Plugin
{
    public static $enableAutoContainerCreation = \true;
    public function registerEvents()
    {
        return array(
            'AssetManager.getStylesheetFiles' => 'getStylesheetFiles',
            'AssetManager.getJavaScriptFiles' => 'getJsFiles',
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys',
            'CoreUpdater.update.end' => 'onPluginActivateOrInstall',
            'PluginManager.pluginActivated' => 'onPluginActivated',
            'PluginManager.pluginInstalled' => 'onPluginActivateOrInstall',
            'PluginManager.pluginDeactivated' => 'onPluginActivateOrInstall',
            'PluginManager.pluginUninstalled' => 'onPluginActivateOrInstall',
            'TagManager.regenerateContainerReleases' => 'regenerateReleasedContainers',
            'Updater.componentUpdated' => 'regenerateReleasedContainers',
            'Controller.CoreHome.checkForUpdates.end' => 'regenerateReleasedContainers',
            'CustomJsTracker.trackerJsChanged' => 'regenerateReleasedContainers',
            // in case a Matomo tracker is bundled
            'SitesManager.deleteSite.end' => 'onSiteDeleted',
            'SitesManager.addSite.end' => 'onSiteAdded',
            'System.addSystemSummaryItems' => 'addSystemSummaryItems',
            'Template.endTrackingCodePage' => 'addTagManagerCode',
            'Template.siteWithoutDataTab.MatomoTagManager.content' => 'setTagManagerCode',
            'Template.endTrackingHelpPage' => 'addTagManagerTrackingCodeHelp',
            'Template.endTrackingCodePageTableOfContents' => 'endTrackingCodePageTableOfContents',
            'Tracker.PageUrl.getQueryParametersToExclude' => 'getQueryParametersToExclude',
            'API.addGlossaryItems' => 'addGlossaryItems',
            'Template.bodyClass' => 'addBodyClass',
            'Access.Capability.addCapabilities' => 'addCapabilities',
            'TwoFactorAuth.requiresTwoFactorAuthentication' => 'requiresTwoFactorAuthentication',
            'Db.getTablesInstalled' => 'getTablesInstalled',
            'Template.siteWithoutDataTab.ReactJs.content' => 'embedReactTagManagerTrackingCode',
            'SitesManager.getMessagesToWarnOnSiteRemoval' => 'getMessagesToWarnOnSiteRemoval',
        );
    }
    /**
     * Register the new tables, so Matomo knows about them.
     *
     * @param array $allTablesInstalled
     */
    public function getTablesInstalled(&$allTablesInstalled)
    {
        $allTablesInstalled[] = Common::prefixTable('tagmanager_container_release');
        $allTablesInstalled[] = Common::prefixTable('tagmanager_container');
        $allTablesInstalled[] = Common::prefixTable('tagmanager_container_version');
        $allTablesInstalled[] = Common::prefixTable('tagmanager_tag');
        $allTablesInstalled[] = Common::prefixTable('tagmanager_trigger');
        $allTablesInstalled[] = Common::prefixTable('tagmanager_variable');
    }
    public function requiresTwoFactorAuthentication(&$requiresAuth, $module, $action, $parameters)
    {
        if ($module == 'TagManager' && $action === 'debug') {
            $requiresAuth = \false;
        }
    }
    public function addBodyClass(&$out, $type)
    {
        if ($type === 'tagmanager') {
            $out .= 'tagmanager';
        }
    }
    public function addCapabilities(&$capabilities)
    {
        $capabilities[] = new TagManagerWrite();
        $capabilities[] = new PublishLiveContainer();
        $systemSettings = StaticContainer::get('Piwik\\Plugins\\TagManager\\SystemSettings');
        $restrictCustomTemplates = $systemSettings->restrictCustomTemplates->getValue();
        if ($restrictCustomTemplates === \Piwik\Plugins\TagManager\SystemSettings::CUSTOM_TEMPLATES_ADMIN) {
            // there is no need to show it when they are completely disabled,
            // when only super users are allowed to use them
            $capabilities[] = new UseCustomTemplates();
        }
        if ($restrictCustomTemplates === \Piwik\Plugins\TagManager\SystemSettings::CUSTOM_TEMPLATES_SUPERUSER && Piwik::hasUserSuperUserAccess()) {
            // there is no need to show it when they are completely disabled,
            // when only super users are allowed to use them
            $capabilities[] = new UseCustomTemplates();
        }
    }
    public function addGlossaryItems(&$glossaryItems)
    {
        Piwik::checkUserHasSomeViewAccess();
        $items = array('title' => Piwik::translate('TagManager_TagManager'), 'entries' => array());
        $contexts = Request::processRequest('TagManager.getAvailableContexts');
        foreach ($contexts as $context) {
            $tagsCategories = Request::processRequest('TagManager.getAvailableTagTypesInContext', array('idContext' => $context['id']));
            foreach ($tagsCategories as $tags) {
                foreach ($tags['types'] as $tag) {
                    if (!empty($tag['description'])) {
                        $items['entries'][] = array('name' => $tag['name'] . ' Tag', 'documentation' => $tag['description']);
                    }
                }
            }
            $triggersCategories = Request::processRequest('TagManager.getAvailableTriggerTypesInContext', array('idContext' => $context['id']));
            foreach ($triggersCategories as $triggers) {
                foreach ($triggers['types'] as $trigger) {
                    if (!empty($trigger['description'])) {
                        $items['entries'][] = array('name' => $trigger['name'] . ' Trigger', 'documentation' => $trigger['description']);
                    }
                }
            }
            $variablesCategories = Request::processRequest('TagManager.getAvailableVariableTypesInContext', array('idContext' => $context['id']));
            foreach ($variablesCategories as $variables) {
                foreach ($variables['types'] as $variable) {
                    if (!empty($variable['description'])) {
                        $items['entries'][] = array('name' => $variable['name'] . ' Variable', 'documentation' => $variable['description']);
                    }
                }
            }
        }
        $variablesProvider = StaticContainer::get('Piwik\\Plugins\\TagManager\\Template\\Variable\\VariablesProvider');
        foreach ($variablesProvider->getPreConfiguredVariables() as $preConfiguredVariable) {
            if ($preConfiguredVariable->getDescription()) {
                $items['entries'][] = array('name' => $preConfiguredVariable->getName() . ' Variable', 'documentation' => $preConfiguredVariable->getDescription(), 'id' => '{{' . $preConfiguredVariable->getId() . '}}');
            }
        }
        usort($items['entries'], function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        $glossaryItems['tagmanager'] = $items;
    }
    public function onPluginActivateOrInstall($pluginName = '')
    {
        if ($pluginName !== 'TagManager') {
            try {
                $this->regenerateReleasedContainers();
            } catch (\Exception $e) {
                Log::warning('Failed to regenerate containers: ' . $e->getMessage());
            }
        }
    }
    public function onPluginActivated($pluginName = '')
    {
        if ($pluginName === 'TagManager') {
            //Need to manually set this since values inc config.php is not loaded
            $pluginDirectory = Plugin\Manager::getPluginDirectory('TagManager');
            $configPhp = (include $pluginDirectory . '/config/config.php');
            foreach ($configPhp as $key => $val) {
                if (!StaticContainer::getContainer()->has($key)) {
                    StaticContainer::getContainer()->set($key, $val);
                }
            }
            $idSite = 1;
            try {
                Site::getSite($idSite);
            } catch (UnexpectedWebsiteFoundException $e) {
                return;
                // site not exists
            }
            $containerModel = StaticContainer::get('Piwik\\Plugins\\TagManager\\Model\\Container');
            if ($containerModel->getContainers($idSite)) {
                // already has a container
                return;
            }
            if (!SettingsPiwik::getPiwikUrl()) {
                // fixes URL in matomo container variable is empty and cannot be detected
                SettingsPiwik::overwritePiwikUrl('https://' . SettingsPiwik::getPiwikInstanceId());
            }
            try {
                StaticContainer::getContainer()->get('Piwik\\Plugins\\TagManager\\Context\\Storage\\StorageInterface');
                //this will throw an error on cloud, so we need to catch this and avoid the exception stack trace
                Request::processRequest('TagManager.createDefaultContainerForSite', ['idSite' => $idSite], []);
            } catch (\Exception $e) {
                //Do nothing here, it fails on cloud always
            }
        } else {
            $this->onPluginActivateOrInstall($pluginName);
        }
    }
    public static function getAbsolutePathToContainerDirectory()
    {
        return PIWIK_DOCUMENT_ROOT . StaticContainer::get('TagManagerContainerStorageDir');
    }
    public function getQueryParametersToExclude(&$parametersToExclude)
    {
        $parametersToExclude[] = PreviewCookie::COOKIE_NAME;
        $parametersToExclude[] = 'mtmSetDebugFlag';
    }
    public function endTrackingCodePageTableOfContents(&$out)
    {
        // Check whether to show the MTM code. If not, simply return early
        if ($this->isAccessRestrictedForUser()) {
            return;
        }
        $out .= '<a href="#/tagmanager">' . Piwik::translate('TagManager_TagManager') . '</a>';
    }
    public function addTagManagerCode(&$out)
    {
        Piwik::checkUserHasSomeViewAccess();
        // Check whether to show the MTM code. If not, simply return early
        if ($this->isAccessRestrictedForUser()) {
            return;
        }
        $model = $this->getContainerModel();
        $view = new View("@TagManager/trackingCode");
        $view->action = Piwik::getAction();
        $view->showContainerRow = $model->getNumContainersTotal() > 1;
        $view->isJsTrackerInstallCheckAvailable = Manager::getInstance()->isPluginActivated('JsTrackerInstallCheck');
        $out .= $view->render();
    }
    public function setTagManagerCode(&$out)
    {
        // Check whether to show the MTM code. If not, simply return early
        if ($this->isAccessRestrictedForUser()) {
            return;
        }
        $newContent = '<h2>' . Piwik::translate('SitesManager_StepByStepGuide') . '</h2>';
        $this->addTagManagerCode($newContent);
        $out = $newContent;
    }
    public function embedReactTagManagerTrackingCode(&$out, SiteContentDetector $detector)
    {
        Piwik::checkUserHasSomeViewAccess();
        // Check whether to show the MTM code. If not, simply return early
        if ($this->isAccessRestrictedForUser()) {
            return;
        }
        $model = $this->getContainerModel();
        $view = new View("@TagManager/trackingCodeReact");
        $view->action = Piwik::getAction();
        $view->wasDetected = $detector->wasDetected(ReactJs::getId());
        $view->showContainerRow = $model->getNumContainersTotal() > 1;
        $out .= $view->render();
    }
    public function addTagManagerTrackingCodeHelp(&$out)
    {
        $idSite = Common::getRequestVar('idSite', 0, 'int');
        if (!empty($idSite) && $this->hasMeasurableTypeWebsite($idSite)) {
            $view = new View("@TagManager/trackingHelp");
            $out .= $view->render();
        }
    }
    public function addSystemSummaryItems(&$systemSummary)
    {
        $model = $this->getContainerModel();
        $numContainers = $model->getNumContainersTotal();
        $systemSummary[] = new SystemSummary\Item($key = 'tagmanagercontainer', Piwik::translate('%s containers (in tag manager)', $numContainers), $value = null, array('module' => 'TagManager', 'action' => 'manageContainers'), '', $order = 20);
    }
    private function getContainerModel()
    {
        return StaticContainer::get('Piwik\\Plugins\\TagManager\\Model\\Container');
    }
    /**
     * @param bool $onlyWithPreviewRelease if true only regenerates containers if there is a preview release.
     */
    public function regenerateReleasedContainers($onlyWithPreviewRelease = \false)
    {
        $pluginManager = Plugin\Manager::getInstance();
        if (!$pluginManager->isPluginInstalled('TagManager')) {
            return;
        }
        try {
            StaticContainer::get(ContainerIdGenerator::class);
        } catch (\Exception $e) {
            // tag manager was likely activated in this request because the DI config could not be resolved.
            // this happens eg when calling "plugin:activate TagManager AnotherPluginName".
            // in this case tag manager gets installed and activated, and then during the same request, when
            // AnotherPluginName is being installed, it will go into this method because we listen to plugin
            // change events and component change events. It will then try to get the container but it fails
            // because at the beginning of the request, the TagManager was not yet activated and therefore the
            // TagManager/config/config.php was not loaded. In this case we skip generating containers as it would fail
            // and a container would not yet exist anyway.
            return;
        }
        Access::doAsSuperUser(function () use($onlyWithPreviewRelease) {
            // we need to run as super user because after a core update the user might not be an admin etc
            // (and admin is needed for debug action)
            $containerModel = StaticContainer::get('Piwik\\Plugins\\TagManager\\Model\\Container');
            try {
                $containers = $containerModel->getActiveContainersInfo();
                foreach ($containers as $container) {
                    try {
                        Context::changeIdSite($container['idsite'], function () use($containerModel, $container, $onlyWithPreviewRelease) {
                            if ($onlyWithPreviewRelease) {
                                $containerModel->generateContainerIfHasPreviewRelease($container['idsite'], $container['idcontainer']);
                            } else {
                                $containerModel->generateContainer($container['idsite'], $container['idcontainer']);
                            }
                        });
                    } catch (UnexpectedWebsiteFoundException $e) {
                        // website was removed, ignore
                    }
                }
            } catch (\Exception $e) {
                StaticContainer::get(LoggerInterface::class)->error('There was an error while regenerating container releases: {exception}', ['exception' => $e]);
            }
        });
    }
    /**
     * @return TagManagerDao[]
     */
    public function getAllDAOs()
    {
        return [new TagsDao(), new TriggersDao(), new VariablesDao(), new ContainersDao(), new ContainerVersionsDao(), new ContainerReleaseDao()];
    }
    public function install()
    {
        foreach ($this->getAllDAOs() as $dao) {
            $dao->install();
        }
        $config = StaticContainer::get('Piwik\\Plugins\\TagManager\\Configuration');
        $config->install();
        $salt = new Salt();
        $salt->generateSaltIfNeeded();
    }
    public function uninstall()
    {
        foreach ($this->getAllDAOs() as $dao) {
            $dao->uninstall();
        }
        $config = StaticContainer::get('Piwik\\Plugins\\TagManager\\Configuration');
        $config->uninstall();
        $salt = new Salt();
        $salt->removeSalt();
        BaseContext::removeAllFilesOfAllContainers();
    }
    public function getClientSideTranslationKeys(&$result)
    {
        $result[] = 'General_Id';
        $result[] = 'General_Name';
        $result[] = 'General_Description';
        $result[] = 'General_Actions';
        $result[] = 'General_LoadingData';
        $result[] = 'General_Save';
        $result[] = 'General_Show';
        $result[] = 'General_Hide';
        $result[] = 'General_Add';
        $result[] = 'General_Remove';
        $result[] = 'General_Edit';
        $result[] = 'General_Or';
        $result[] = 'General_Recommended';
        $result[] = 'General_Website';
        $result[] = 'General_ClickX';
        $result[] = 'General_Update';
        $result[] = 'Goals_Optional';
        $result[] = 'SitesManager_Type';
        $result[] = 'UserCountryMap_None';
        $result[] = 'CoreUpdater_UpdateTitle';
        $result[] = 'TagManager_DetectingChanges';
        $result[] = 'TagManager_NoContainersFound';
        $result[] = 'TagManager_PreConfiguredInfoTitle';
        $result[] = 'TagManager_TriggerConditionNode';
        $result[] = 'TagManager_ConfigureEnvironmentsSuperUser';
        $result[] = 'TagManager_WantToDeployThisChangeCreateVersion';
        $result[] = 'TagManager_ConfigureWhenTagDoes';
        $result[] = 'TagManager_ViewContainerDashboard';
        $result[] = 'TagManager_NoMatomoConfigFoundForContainer';
        $result[] = 'TagManager_PublishLiveEnvironmentCapabilityRequired';
        $result[] = 'TagManager_CapabilityPublishLiveContainer';
        $result[] = 'TagManager_VersionAlreadyPublishedToAllEnvironments';
        $result[] = 'TagManager_UseCustomTemplateCapabilityRequired';
        $result[] = 'TagManager_CapabilityUseCustomTemplates';
        $result[] = 'TagManager_ViewX';
        $result[] = 'TagManager_DeleteX';
        $result[] = 'TagManager_CreateNewX';
        $result[] = 'TagManager_EditX';
        $result[] = 'TagManager_Context';
        $result[] = 'TagManager_ManageContainersIntro';
        $result[] = 'TagManager_ContainerNameHelp';
        $result[] = 'TagManager_ContainerContextHelp';
        $result[] = 'TagManager_ContainerDescriptionHelp';
        $result[] = 'TagManager_TagStartDateHelp';
        $result[] = 'TagManager_TagEndDateHelp';
        $result[] = 'TagManager_CurrentTimeInLocalTimezone';
        $result[] = 'TagManager_TagUsageBenefits';
        $result[] = 'TagManager_TagNameHelpV2';
        $result[] = 'TagManager_NoTagsFound';
        $result[] = 'TagManager_DeleteTagConfirm';
        $result[] = 'TagManager_DeleteVersionConfirm';
        $result[] = 'TagManager_VersionUsageBenefits';
        $result[] = 'TagManager_VersionNameHelp';
        $result[] = 'TagManager_NoVersionsFound';
        $result[] = 'TagManager_NoReleasesFound';
        $result[] = 'TagManager_NoReleasesFoundForContainer';
        $result[] = 'TagManager_Revision';
        $result[] = 'TagManager_VersionRevision';
        $result[] = 'TagManager_ReleasedBy';
        $result[] = 'TagManager_ReleasedOn';
        $result[] = 'TagManager_LearnMore';
        $result[] = 'TagManager_TagFireTriggerRequirement';
        $result[] = 'TagManager_ChooseTagToContinue';
        $result[] = 'TagManager_ChooseTriggerToContinue';
        $result[] = 'TagManager_ChooseVariableToContinue';
        $result[] = 'TagManager_TriggerConditionsHelp';
        $result[] = 'TagManager_TriggerConditionsHelpText';
        $result[] = 'TagManager_EnablingPreviewPleaseWait';
        $result[] = 'TagManager_DisablingPreviewPleaseWait';
        $result[] = 'TagManager_UpdatingDebugSiteUrlPleaseWait';
        $result[] = 'TagManager_DebugUrlNoUrlErrorMessage';
        $result[] = 'TagManager_DebugUrlSameUrlErrorMessage';
        $result[] = 'TagManager_NameOfLatestVersion';
        $result[] = 'TagManager_Created';
        $result[] = 'TagManager_CreateVersionWithoutPublishing';
        $result[] = 'TagManager_PublishVersionToEnvironmentToViewEmbedCode';
        $result[] = 'TagManager_CreateVersionAndPublishRelease';
        $result[] = 'TagManager_VersionName';
        $result[] = 'TagManager_VersionDescription';
        $result[] = 'TagManager_Released';
        $result[] = 'TagManager_ErrorXNotProvided';
        $result[] = 'TagManager_ExportX';
        $result[] = 'TagManager_PublishVersion';
        $result[] = 'TagManager_ReleaseInfo';
        $result[] = 'TagManager_ReleaseVersionInfo';
        $result[] = 'TagManager_PublishRelease';
        $result[] = 'TagManager_ManageX';
        $result[] = 'TagManager_CreatedX';
        $result[] = 'TagManager_UpdatedX';
        $result[] = 'TagManager_UpdatingData';
        $result[] = 'TagManager_DeleteContainerConfirm';
        $result[] = 'TagManager_VersionEnvironmentHelp';
        $result[] = 'TagManager_VersionDescriptionHelp';
        $result[] = 'TagManager_Container';
        $result[] = 'TagManager_Containers';
        $result[] = 'TagManager_Type';
        $result[] = 'TagManager_Types';
        $result[] = 'TagManager_Tag';
        $result[] = 'TagManager_Tags';
        $result[] = 'TagManager_Version';
        $result[] = 'TagManager_Versions';
        $result[] = 'TagManager_Environment';
        $result[] = 'TagManager_Environments';
        $result[] = 'TagManager_Trigger';
        $result[] = 'TagManager_Triggers';
        $result[] = 'TagManager_Variable';
        $result[] = 'TagManager_Variables';
        $result[] = 'TagManager_Names';
        $result[] = 'TagManager_DiffAdded';
        $result[] = 'TagManager_DiffModified';
        $result[] = 'TagManager_DiffDeleted';
        $result[] = 'TagManager_DefaultValue';
        $result[] = 'TagManager_DefaultValueHelp';
        $result[] = 'TagManager_LookupTableTitle';
        $result[] = 'TagManager_LookupTableMatchValue';
        $result[] = 'TagManager_LookupTableOutValue';
        $result[] = 'TagManager_OrCreateAndPublishVersion';
        $result[] = 'TagManager_ConfigureWhatTagDoes';
        $result[] = 'TagManager_ConfigureThisVariable';
        $result[] = 'TagManager_ConfigureThisTrigger';
        $result[] = 'TagManager_OnlyTriggerWhen';
        $result[] = 'TagManager_FireTriggerTitle';
        $result[] = 'TagManager_FireTriggerHelp';
        $result[] = 'TagManager_BlockTriggerTitle';
        $result[] = 'TagManager_BlockTriggerHelp';
        $result[] = 'TagManager_ShowAdvancedSettings';
        $result[] = 'TagManager_HideAdvancedSettings';
        $result[] = 'TagManager_Unlimited';
        $result[] = 'TagManager_OnceLifetime';
        $result[] = 'TagManager_OncePage';
        $result[] = 'TagManager_Once24Hours';
        $result[] = 'TagManager_VersionPublishSuccess';
        $result[] = 'TagManager_FireDelay';
        $result[] = 'TagManager_FireDelayHelp';
        $result[] = 'TagManager_FireLimit';
        $result[] = 'TagManager_FireLimitHelp';
        $result[] = 'TagManager_Priority';
        $result[] = 'TagManager_PriorityHelp';
        $result[] = 'TagManager_DeleteVariableConfirm';
        $result[] = 'TagManager_NoVariablesFound';
        $result[] = 'TagManager_VariableUsageBenefits';
        $result[] = 'TagManager_VariableNameHelp';
        $result[] = 'TagManager_DeleteTriggerConfirm';
        $result[] = 'TagManager_NoTriggersFound';
        $result[] = 'TagManager_TriggerUsageBenefits';
        $result[] = 'TagManager_TriggerNameHelp';
        $result[] = 'TagManager_ContainerX';
        $result[] = 'TagManager_ConfirmImportContainerVersion';
        $result[] = 'TagManager_Filter';
        $result[] = 'TagManager_Import';
        $result[] = 'TagManager_Except';
        $result[] = 'TagManager_EnablePreviewDebug';
        $result[] = 'TagManager_StartDate';
        $result[] = 'TagManager_EndDate';
        $result[] = 'TagManager_ExportDraft';
        $result[] = 'TagManager_PreconfiguredVariables';
        $result[] = 'TagManager_TriggerCannotBeDeleted';
        $result[] = 'TagManager_TriggerBeingUsedBy';
        $result[] = 'TagManager_TriggerBeingUsedNeedsRemove';
        $result[] = 'TagManager_VariableCannotBeDeleted';
        $result[] = 'TagManager_VariableBeingUsedBy';
        $result[] = 'TagManager_VariableBeingUsedNeedsRemove';
        $result[] = 'TagManager_Change';
        $result[] = 'TagManager_ChangesSinceLastVersion';
        $result[] = 'TagManager_LastUpdated';
        $result[] = 'TagManager_CreatedDate';
        $result[] = 'TagManager_LookupTable';
        $result[] = 'TagManager_LastVersions';
        $result[] = 'TagManager_EditVersions';
        $result[] = 'TagManager_EditVersion';
        $result[] = 'TagManager_EditTags';
        $result[] = 'TagManager_EditTag';
        $result[] = 'TagManager_EditVariables';
        $result[] = 'TagManager_EditVariable';
        $result[] = 'TagManager_EditTriggers';
        $result[] = 'TagManager_EditTrigger';
        $result[] = 'TagManager_CreateNewVersion';
        $result[] = 'TagManager_CreateNewTag';
        $result[] = 'TagManager_CreateNewTrigger';
        $result[] = 'TagManager_CreateNewVariable';
        $result[] = 'TagManager_ConfigureX';
        $result[] = 'TagManager_EntityDateTypeMetaInformation';
        $result[] = 'TagManager_ContainerMetaInformation';
        $result[] = 'TagManager_ChooseContainer';
        $result[] = 'TagManager_ChooseVariable';
        $result[] = 'TagManager_ErrorInvalidContainerImportFormat';
        $result[] = 'TagManager_ErrorContainerVersionImportIncomplete';
        $result[] = 'TagManager_VersionImportSuccess';
        $result[] = 'TagManager_VersionImportInfo';
        $result[] = 'TagManager_ImportVersion';
        $result[] = 'TagManager_BackupVersionName';
        $result[] = 'TagManager_BackupVersionNameHelp';
        $result[] = 'TagManager_VersionImportContentTitle';
        $result[] = 'TagManager_VersionImportOverwriteContent';
        $result[] = 'TagManager_CustomVariables';
        $result[] = 'TagManager_EditContainer';
        $result[] = 'TagManager_CreateNewContainer';
        $result[] = 'TagManager_CreateNewContainerNow';
        $result[] = 'TagManager_CreateNewTagNow';
        $result[] = 'TagManager_CreateNewTriggerNow';
        $result[] = 'TagManager_CreateNewVariableNow';
        $result[] = 'TagManager_CreatedOnX';
        $result[] = 'TagManager_ReleasesOverview';
        $result[] = 'TagManager_InstallCode';
        $result[] = 'TagManager_InstallCodePublishEnvironmentNote';
        $result[] = 'CorePluginsAdmin_WhatIsTagManager';
        $result[] = 'TagManager_CreateNewVersionNow';
        $result[] = 'TagManager_TagManager';
        $result[] = 'TagManager_MatomoTagManager';
        $result[] = 'TagManager_TagManagerTrackingInfo';
        $result[] = 'TagManager_InvalidDebugUrlError';
        $result[] = 'TagManager_TagDescriptionHelp';
        $result[] = 'TagManager_TriggerDescriptionHelp';
        $result[] = 'TagManager_VariableDescriptionHelp';
        $result[] = 'TagManager_InstallCodeDataLayerNote';
        $result[] = 'TagManager_TagsNameDescription';
        $result[] = 'TagManager_TagsDescriptionDescription';
        $result[] = 'TagManager_TagsTypeDescription';
        $result[] = 'TagManager_TagsTriggersDescription';
        $result[] = 'TagManager_TagsLastUpdatedDescription';
        $result[] = 'TagManager_TagsActionDescription';
        $result[] = 'TagManager_TriggersNameDescription';
        $result[] = 'TagManager_TriggersDescriptionDescription';
        $result[] = 'TagManager_TriggersTypeDescription';
        $result[] = 'TagManager_TriggersFilterDescription';
        $result[] = 'TagManager_TriggersLastUpdatedDescription';
        $result[] = 'TagManager_TriggersActionDescription';
        $result[] = 'TagManager_VariablesNameDescription';
        $result[] = 'TagManager_VariablesDescriptionDescription';
        $result[] = 'TagManager_VariablesTypeDescription';
        $result[] = 'TagManager_VariablesLookupTableDescription';
        $result[] = 'TagManager_VariablesLastUpdatedDescription';
        $result[] = 'TagManager_VariablesActionDescription';
        $result[] = 'TagManager_VersionsRevisionDescription';
        $result[] = 'TagManager_VersionsNameDescription';
        $result[] = 'TagManager_VersionsDescriptionDescription';
        $result[] = 'TagManager_VersionsEnvironmentsDescription';
        $result[] = 'TagManager_VersionsCreatedDescription';
        $result[] = 'TagManager_VersionsActionDescription';
        $result[] = 'TagManager_CreateNewVersionNow';
        $result[] = 'TagManager_SelectAVariable';
        $result[] = 'TagManager_AddThisTagPubIdTitle';
        $result[] = 'TagManager_AddThisTagPubIdDescription';
        $result[] = 'TagManager_AddThisParentSelectorTitle';
        $result[] = 'TagManager_AddThisParentSelectorDescription';
        $result[] = 'TagManager_BingUETTagIdTitle';
        $result[] = 'TagManager_BingUETTagIdDescription';
        $result[] = 'TagManager_BugsnagTagApiKeyTitle';
        $result[] = 'TagManager_BugsnagTagApiKeyDescription';
        $result[] = 'TagManager_BugsnagTagCollectUserIpTitle';
        $result[] = 'TagManager_BugsnagTagCollectUserIpDescription';
        $result[] = 'TagManager_CustomHtmlTagTitle';
        $result[] = 'TagManager_CustomHtmlTagDescriptionText';
        $result[] = 'TagManager_CustomHtmlTagHelpText';
        $result[] = 'TagManager_CustomHtmlHtmlPositionTitle';
        $result[] = 'TagManager_CustomHtmlHtmlPositionDescription';
        $result[] = 'TagManager_CustomImageTagSrcTitle';
        $result[] = 'TagManager_CustomImageTagSrcDescription';
        $result[] = 'TagManager_CustomImageTagCacheBusterEnabledTitle';
        $result[] = 'TagManager_CustomImageTagCacheBusterEnabledDescription';
        $result[] = 'TagManager_DriftTagDriftIdTitle';
        $result[] = 'TagManager_DriftTagDriftIdDescription';
        $result[] = 'TagManager_EmarsysTagMerchantIdTitle';
        $result[] = 'TagManager_EmarsysTagMerchantIdDescription';
        $result[] = 'TagManager_EmarsysTagCommandCategoryTitleOptional';
        $result[] = 'TagManager_EmarsysTagCommandCategoryDescription';
        $result[] = 'TagManager_EmarsysTagCommandViewTitleOptional';
        $result[] = 'TagManager_EmarsysTagCommandViewDescription';
        $result[] = 'TagManager_EmarsysTagCommandTagTitleOptional';
        $result[] = 'TagManager_EmarsysTagCommandTagDescription';
        $result[] = 'TagManager_EmarsysTagCommandGoTitle';
        $result[] = 'TagManager_EmarsysTagCommandGoDescription';
        $result[] = 'TagManager_EtrackerTagTrackingTypeTitle';
        $result[] = 'TagManager_EtrackerTagTrackingTypeDescription';
        $result[] = 'TagManager_EtrackerTagConfigTitle';
        $result[] = 'TagManager_EtrackerTagConfigDescription';
        $result[] = 'TagManager_EtrackerTagWrapperPageNameTitle';
        $result[] = 'TagManager_EtrackerTagWrapperPageNameDescription';
        $result[] = 'TagManager_EtrackerTagWrapperAreaTitle';
        $result[] = 'TagManager_EtrackerTagWrapperAreaDescription';
        $result[] = 'TagManager_EtrackerTagWrapperTargetTitle';
        $result[] = 'TagManager_EtrackerTagWrapperTvalTitle';
        $result[] = 'TagManager_EtrackerTagWrapperTonrTitle';
        $result[] = 'TagManager_EtrackerTagWrapperTsaleTitle';
        $result[] = 'TagManager_EtrackerTagWrapperTcustTitle';
        $result[] = 'TagManager_EtrackerTagWrapperTBasketTitle';
        $result[] = 'TagManager_EtrackerTagEventCategoryTitle';
        $result[] = 'TagManager_EtrackerTagEventCategoryDescription';
        $result[] = 'TagManager_EtrackerTagEventObjectTitle';
        $result[] = 'TagManager_EtrackerTagEventObjectDescription';
        $result[] = 'TagManager_EtrackerTagEventActionTitle';
        $result[] = 'TagManager_EtrackerTagEventActionDescription';
        $result[] = 'TagManager_EtrackerTagEventTypeTitle';
        $result[] = 'TagManager_EtrackerTagEventTypeDescription';
        $result[] = 'TagManager_FacebookPixelTagPixelIdTitle';
        $result[] = 'TagManager_GoogleAnalyticsUniversalTagPropertyIdTitle';
        $result[] = 'TagManager_GoogleAnalyticsUniversalTagPropertyIdDescription';
        $result[] = 'TagManager_GoogleAnalyticsUniversalTagTrackingTypeTitle';
        $result[] = 'TagManager_GoogleAnalyticsUniversalTagTrackingTypeDescription';
        $result[] = 'TagManager_HoneybadgerTagApiKeyTitle';
        $result[] = 'TagManager_HoneybadgerTagApiKeyDescription';
        $result[] = 'TagManager_HoneybadgerTagEnvironmentDescription';
        $result[] = 'TagManager_HoneybadgerTagRevisionTitle';
        $result[] = 'TagManager_HoneybadgerTagRevisionDescription';
        $result[] = 'TagManager_LinkedinInsightTagPartnerIdTitle';
        $result[] = 'TagManager_LinkedinInsightTagPartnerIdDescription';
        $result[] = 'TagManager_LivezillaDynamicTagIdTitle';
        $result[] = 'TagManager_LivezillaDynamicTagIdDescription';
        $result[] = 'TagManager_LivezillaDynamicTagDomainTitle';
        $result[] = 'TagManager_LivezillaDynamicTagDomainDescription';
        $result[] = 'TagManager_LivezillaDynamicTagDynamicDeferTitle';
        $result[] = 'TagManager_LivezillaDynamicTagDynamicDeferDescription';
        $result[] = 'TagManager_PingdomRUMTagIdTitle';
        $result[] = 'TagManager_PingdomRUMTagIdDescription';
        $result[] = 'TagManager_RaygunTagApiKeyTitle';
        $result[] = 'TagManager_RaygunTagApiKeyDescription';
        $result[] = 'TagManager_RaygunTagEnablePulseTitle';
        $result[] = 'TagManager_RaygunTagEnablePulseDescription';
        $result[] = 'TagManager_SentryRavenTagDSNTitle';
        $result[] = 'TagManager_SentryRavenTagDSNDescription';
        $result[] = 'TagManager_ShareaholicTagInPageAppTitle';
        $result[] = 'TagManager_ShareaholicTagInPageAppDescription';
        $result[] = 'TagManager_ShareaholicTagSiteIdTitle';
        $result[] = 'TagManager_ShareaholicTagSiteIdDescription';
        $result[] = 'TagManager_ShareaholicTagAppIdTitle';
        $result[] = 'TagManager_ShareaholicTagAppIdDescription';
        $result[] = 'TagManager_ShareaholicTagParentSelectorTitle';
        $result[] = 'TagManager_ShareaholicTagParentSelectorDescription';
        $result[] = 'TagManager_TawkToTagIdTitle';
        $result[] = 'TagManager_TawkToTagIdDescription';
        $result[] = 'TagManager_TawkToTagWidgetIdTitle';
        $result[] = 'TagManager_TawkToTagWidgetIdDescription';
        $result[] = 'TagManager_ThemeColorTagThemeColorTitle';
        $result[] = 'TagManager_ThemeColorTagThemeColorDescription';
        $result[] = 'TagManager_VisualWebsiteOptimizerTagAccountIdTitle';
        $result[] = 'TagManager_VisualWebsiteOptimizerTagAccountIdDescription';
        $result[] = 'TagManager_ZendeskChatTagChatIdTitle';
        $result[] = 'TagManager_ZendeskChatTagChatIdDescription';
        $result[] = 'TagManager_AllDownloadsClickTriggerDownloadExtensionsTitle';
        $result[] = 'TagManager_AllDownloadsClickTriggerDownloadExtensionsDescription';
        $result[] = 'TagManager_CustomEventTriggerEventNameDescription';
        $result[] = 'TagManager_ElementVisibilityTriggerSelectionMethodTitle';
        $result[] = 'TagManager_ElementVisibilityTriggerSelectionMethodDescription';
        $result[] = 'TagManager_ElementVisibilityTriggerCssSelectorTitle';
        $result[] = 'TagManager_ElementVisibilityTriggerCssSelectorDescription';
        $result[] = 'TagManager_ElementVisibilityTriggerElementIDTitle';
        $result[] = 'TagManager_ElementVisibilityTriggerElementIDDescription';
        $result[] = 'TagManager_ElementVisibilityTriggerFireTriggerWhenTitle';
        $result[] = 'TagManager_ElementVisibilityTriggerMinPercentVisibleTitle';
        $result[] = 'TagManager_FullscreenTriggerTriggerActionTitle';
        $result[] = 'TagManager_FullscreenTriggerTriggerLimitTitle';
        $result[] = 'TagManager_FullscreenTriggerTriggerLimitDescription';
        $result[] = 'TagManager_ScrollReachTriggerScrollTypeTitle';
        $result[] = 'TagManager_ScrollReachTriggerPixelsTitle';
        $result[] = 'TagManager_ScrollReachTriggerPixelsDescription';
        $result[] = 'TagManager_ScrollReachTriggerPercentageTitle';
        $result[] = 'TagManager_ScrollReachTriggerPercentageDescription';
        $result[] = 'TagManager_TimerTriggerTriggerIntervalTitle';
        $result[] = 'TagManager_TimerTriggerEventNameDescription';
        $result[] = 'TagManager_TimerTriggerTriggerLimitTitle';
        $result[] = 'TagManager_TimerTriggerTriggerLimitDescription';
        $result[] = 'TagManager_WindowLeaveTriggerTriggerLimitTitle';
        $result[] = 'TagManager_WindowLeaveTriggerTriggerLimitDescription';
        $result[] = 'TagManager_CookieVariableCookieNameTitle';
        $result[] = 'TagManager_CookieVariableUrlDecodeTitle';
        $result[] = 'TagManager_CookieVariableUrlDecodeDescription';
        $result[] = 'TagManager_CustomJsFunctionVariableJsFunctionTitle';
        $result[] = 'TagManager_CustomJsFunctionVariableJsFunctionDescription';
        $result[] = 'TagManager_DataLayerVariableNameTitle';
        $result[] = 'TagManager_DataLayerVariableNameDescription';
        $result[] = 'TagManager_DomElementVariableSelectionMethodDescription';
        $result[] = 'TagManager_DomElementVariableCssSelectorDescription';
        $result[] = 'TagManager_DomElementVariableAttributeNameTitle';
        $result[] = 'TagManager_DomElementVariableAttributeNameInlineHelp';
        $result[] = 'TagManager_EtrackerConfigurationVariableIdTitle';
        $result[] = 'TagManager_EtrackerConfigurationVariableIdDescription';
        $result[] = 'TagManager_EtrackerConfigurationVariableBlockCookiesTitle';
        $result[] = 'TagManager_EtrackerConfigurationVariableDNTTitle';
        $result[] = 'TagManager_EtrackerConfigurationVariablePageNameTitle';
        $result[] = 'TagManager_EtrackerConfigurationVariablePageNameDescription';
        $result[] = 'TagManager_EtrackerConfigurationVariableAreaTitle';
        $result[] = 'TagManager_EtrackerConfigurationVariableTargetTitle';
        $result[] = 'TagManager_EtrackerConfigurationVariableTValTitle';
        $result[] = 'TagManager_EtrackerConfigurationVariableTonrTitle';
        $result[] = 'TagManager_EtrackerConfigurationVariableTSaleTitle';
        $result[] = 'TagManager_EtrackerConfigurationVariableBasketTitle';
        $result[] = 'TagManager_EtrackerConfigurationVariableCustTitle';
        $result[] = 'TagManager_EtrackerConfigurationVariableCustomDimensionsTitle';
        $result[] = 'TagManager_EtrackerConfigurationVariableCustomDimensionsDescription';
        $result[] = 'TagManager_JavaScriptVariableNameTitle';
        $result[] = 'TagManager_JavaScriptVariableNameDescription';
        $result[] = 'TagManager_MetaContentVariableNameTitle';
        $result[] = 'TagManager_ReferrerUrlVariableUrlPartTitle';
        $result[] = 'TagManager_ReferrerUrlVariableUrlPartDescription';
        $result[] = 'TagManager_TimeSinceLoadVariableUnitTitle';
        $result[] = 'TagManager_TimeSinceLoadVariableUnitDescription';
        $result[] = 'TagManager_UrlParameterVariableNameTitle';
        $result[] = 'TagManager_UrlParameterVariableNameDescription';
        $result[] = 'TagManager_MatomoTagManagerTrackingInfoLine1';
        $result[] = 'TagManager_MatomoTagManagerTrackingInfoLine2';
        $result[] = 'TagManager_SiteWithoutDataReactIntro';
        $result[] = 'TagManager_SiteWithoutDataReactFollowStepCompleted';
        $result[] = 'SitesManager_SiteWithoutDataCloudflareFollowStepsIntro';
        $result[] = 'TagManager_SPAFollowStep1';
        $result[] = 'TagManager_SPAFollowStep2';
        $result[] = 'TagManager_SPAFollowStep3';
        $result[] = 'TagManager_SPAFollowStep5';
        $result[] = 'TagManager_SPAFollowStep7';
        $result[] = 'TagManager_SPAFollowStep8';
        $result[] = 'TagManager_SPAFollowStep9';
        $result[] = 'TagManager_SPAFollowStep10';
        $result[] = 'TagManager_SPAFollowStep10a';
        $result[] = 'TagManager_SPAFollowStep10b';
        $result[] = 'TagManager_SPAFollowStep11';
        $result[] = 'TagManager_SPAFollowStep13';
        $result[] = 'TagManager_SPAFollowStep14';
        $result[] = 'TagManager_SPAFollowStep15';
        $result[] = 'TagManager_SPAFollowStep16';
        $result[] = 'TagManager_ReactFollowStep16';
        $result[] = 'TagManager_HistoryChangeTriggerName';
        $result[] = 'TagManager_CategoryUserEngagement';
        $result[] = 'TagManager_Publish';
        $result[] = 'TagManager_CustomTitle';
        $result[] = 'TagManager_CustomUrl';
        $result[] = 'TagManager_PageViewTriggerName';
        $result[] = 'TagManager_MatomoTagName';
        $result[] = 'TagManager_SiteWithoutDataMtmIntro';
        $result[] = 'TagManager_SiteWithoutDataMtmStep2';
        $result[] = 'TagManager_SiteWithoutDataMtmStep3';
        $result[] = 'TagManager_IgnoreGtmDataLaterDescription';
        $result[] = 'TagManager_IgnoreGtmDataLaterTitle';
        $result[] = 'TagManager_VersionEditWithNoAccessMessage';
        $result[] = 'TagManager_MtmTrackingCodeIntro';
        $result[] = 'TagManager_OptionallyCustomiseContainer';
        $result[] = 'TagManager_CopyCodePasteInHeader';
        $result[] = 'TagManager_SelectContainerForWebsite';
        $result[] = 'TagManager_NoteAboutContainers';
        $result[] = 'TagManager_CustomiseContainer';
        $result[] = 'TagManager_ManageContainersLink';
        $result[] = 'TagManager_Description';
        $result[] = 'TagManager_TagDescriptionPlaceholder';
        $result[] = 'TagManager_TriggerDescriptionPlaceholder';
        $result[] = 'TagManager_VariableDescriptionPlaceholder';
        $result[] = 'TagManager_VersionDescriptionPlaceholder';
        $result[] = 'TagManager_ContainerDescriptionPlaceholder';
        $result[] = 'TagManager_TagNamePlaceholder';
        $result[] = 'TagManager_TriggerNamePlaceholder';
        $result[] = 'TagManager_VariableNamePlaceholder';
        $result[] = 'TagManager_VersionNamePlaceholder';
        $result[] = 'TagManager_ContainerNamePlaceholder';
        $result[] = 'TagManager_PlaceholderZero';
        $result[] = 'TagManager_PriorityPlaceholder';
        $result[] = 'TagManager_VersionDescriptionOptional';
        $result[] = 'TagManager_BingUETTagIdPlaceholder';
        $result[] = 'TagManager_DriftTagDriftIdPlaceholder';
        $result[] = 'TagManager_EmarsysTagMerchantIdPlaceholder';
        $result[] = 'TagManager_EmarsysTagCommandCategoryPlaceholder';
        $result[] = 'TagManager_EmarsysTagCommandViewPlaceholder';
        $result[] = 'TagManager_EmarsysTagCommandTagPlaceholder';
        $result[] = 'TagManager_FacebookPixelTagPixelIdPlaceholder';
        $result[] = 'TagManager_LinkedinInsightTagPartnerIdPlaceholder';
        $result[] = 'TagManager_LivezillaDynamicTagIdPlaceholder';
        $result[] = 'TagManager_LivezillaDynamicTagDomainPlaceholder';
        $result[] = 'TagManager_RaygunTagApiKeyPlaceholder';
        $result[] = 'TagManager_SentryRavenTagDSNPlaceholder';
        $result[] = 'TagManager_TawkToTagIdPlaceholder';
        $result[] = 'TagManager_TawkToTagWidgetIdPlaceholder';
        $result[] = 'TagManager_ThemeColorPlaceholder';
        $result[] = 'TagManager_VisualWebsiteOptimizerTagAccountIdPlaceholder';
        $result[] = 'TagManager_ZendeskChatTagChatIdPlaceholder';
        $result[] = 'TagManager_AllDownloadsClickTriggerDownloadExtensionsPlaceholder';
        $result[] = 'TagManager_ElementVisibilityTriggerCssSelectorPlaceholder';
        $result[] = 'TagManager_ElementVisibilityTriggerElementIdPlaceholder';
        $result[] = 'TagManager_ElementVisibilityTriggerMinPercentVisiblePlaceholder';
        $result[] = 'TagManager_TimerTriggerTriggerIntervalPlaceholder';
        $result[] = 'TagManager_WindowLeaveTriggerTriggerLimitPlaceholder';
        $result[] = 'TagManager_ClickDataAttributeDataAttributePlaceholder';
        $result[] = 'TagManager_ConstantValuePlaceholder';
        $result[] = 'TagManager_CookieVariableCookieNamePlaceholder';
        $result[] = 'TagManager_CustomJsFunctionVariableJsFunctionPlaceholder';
        $result[] = 'TagManager_DataLayerVariableNamePlaceholder';
        $result[] = 'TagManager_DomElementVariableAttributeNamePlaceholder';
        $result[] = 'TagManager_MatomoConfigurationMatomoCrossDomainLinkingTimeoutPlaceholder';
        $result[] = 'TagManager_MatomoConfigurationMatomoHeartBeatTimePlaceholder';
        $result[] = 'TagManager_MatomoConfigurationMatomoVisitorCookieTimeOutPlaceholder';
        $result[] = 'TagManager_MatomoConfigurationMatomoReferralCookieTimeOutPlaceholder';
        $result[] = 'TagManager_MatomoConfigurationMatomoSessionCookieTimeOutPlaceholder';
        $result[] = 'TagManager_MatomoConfigurationMatomoCookieNamePrefixPlaceholder';
        $result[] = 'TagManager_MatomoConfigurationMatomoCookiePathPlaceholder';
        $result[] = 'TagManager_MatomoConfigurationMatomoJsEndpointCustomPlaceholder';
        $result[] = 'TagManager_MatomoConfigurationMatomoTrackingEndpointCustomPlaceholder';
        $result[] = 'TagManager_MatomoConfigurationMatomoRequestContentTypePlaceholder';
        $result[] = 'TagManager_UrlParameterVariableNamePlaceholder';
        $result[] = 'TagManager_JavaScriptVariableNamePlaceholder';
        $result[] = 'TagManager_DefaultValuePlaceholder';
        $result[] = 'TagManager_PauseX';
        $result[] = 'TagManager_PauseTagConfirm';
        $result[] = 'TagManager_ResumeX';
        $result[] = 'TagManager_ResumeTagConfirm';
        $result[] = 'TagManager_DiffPaused';
        $result[] = 'TagManager_DiffAddedPaused';
        $result[] = 'TagManager_TagFireLimitAllowedInPreviewModeTitle';
        $result[] = 'TagManager_TagFireLimitAllowedInPreviewModeDescription';
        $result[] = 'TagManager_DisablePreview';
        $result[] = 'TagManager_MatomoConfigurationMatomoTrackBotsTitle';
        $result[] = 'TagManager_MatomoConfigurationMatomoTrackBotsDescription';
        $result[] = 'TagManager_PausedTag';
        $result[] = 'TagManager_ResumedTag';
        $result[] = 'TagManager_ActivelySyncGtmDataLayerTitle';
        $result[] = 'TagManager_ActivelySyncGtmDataLayerDescription';
        $result[] = 'TagManager_ContainerIdInformation';
        $result[] = 'TagManager_ContainerDashboardDescription';
        $result[] = 'TagManager_CopyX';
        $result[] = 'TagManager_CopyXDescription';
        $result[] = 'TagManager_CopyContainerDescription';
        $result[] = 'TagManager_ContainerLowercase';
        $result[] = 'TagManager_TagLowercase';
        $result[] = 'TagManager_TriggerLowercase';
        $result[] = 'TagManager_VariableLowercase';
        $result[] = 'TagManager_ChooseWebsite';
        $result[] = 'TagManager_CopyContainerNote';
        $result[] = 'TagManager_CopyXSuccess';
        $result[] = 'TagManager_ContainerLowercase';
        $result[] = 'TagManager_TagLowercase';
        $result[] = 'TagManager_TriggerLowercase';
        $result[] = 'TagManager_VariableLowercase';
        $result[] = 'TagManager_LearnMoreFullStop';
        $result[] = 'TagManager_CustomHTMLTagNameInlineHelpText';
    }
    public function getStylesheetFiles(&$stylesheets)
    {
        $stylesheets[] = "plugins/TagManager/stylesheets/manageList.less";
        $stylesheets[] = "plugins/TagManager/stylesheets/manageEdit.less";
        $stylesheets[] = "plugins/TagManager/vue/src/Tag/TagEdit.less";
        $stylesheets[] = "plugins/TagManager/vue/src/VariableSelectType/VariableSelectType.less";
        $stylesheets[] = "plugins/TagManager/vue/src/Field/FieldVariableTemplate.less";
        $stylesheets[] = "plugins/TagManager/vue/src/ContainerSelector/ContainerSelector.less";
        $stylesheets[] = "plugins/TagManager/vue/src/ContainerDashboard/ContainerDashboard.less";
        $stylesheets[] = "plugins/TagManager/vue/src/Version/VersionEdit.less";
        $stylesheets[] = "plugins/TagManager/vue/src/TagmanagerTrackingCode/TagManagerTrackingCode.less";
        $stylesheets[] = "plugins/TagManager/vue/src/CopyDialog/CopyDialog.less";
    }
    public function getJsFiles(&$jsFiles)
    {
        $jsFiles[] = "plugins/TagManager/libs/jquery-timepicker/jquery.timepicker.min.js";
        $jsFiles[] = "plugins/TagManager/javascripts/tagmanagerHelper.js";
    }
    private function hasMeasurableTypeWebsite($idSite)
    {
        try {
            $type = Site::getTypeFor($idSite);
        } catch (UnexpectedWebsiteFoundException $e) {
            return \false;
            // no longer exists
        }
        return $type === 'website';
    }
    public function onSiteAdded($idSite)
    {
        if (self::$enableAutoContainerCreation && $this->hasMeasurableTypeWebsite($idSite)) {
            Request::processRequest('TagManager.createDefaultContainerForSite', array('idSite' => $idSite), $default = []);
        }
    }
    public function onSiteDeleted($idSite)
    {
        $deletedDate = Date::now()->getDatetime();
        $dao = new TagsDao();
        $dao->deleteTagsForSite($idSite, $deletedDate);
        $dao = new TriggersDao();
        $dao->deleteTriggersForSite($idSite, $deletedDate);
        $dao = new VariablesDao();
        $dao->deleteVariablesForSite($idSite, $deletedDate);
        $dao = new ContainerVersionsDao();
        $dao->deleteAllVersionsForSite($idSite, $deletedDate);
        $dao = new ContainerReleaseDao();
        $dao->deleteAllVersionsForSite($idSite, $deletedDate);
        $dao = new ContainersDao();
        foreach ($dao->getContainersForSite($idSite) as $container) {
            BaseContext::removeAllContainerFiles($container['idcontainer']);
        }
        $dao->deleteContainersForSite($idSite, $deletedDate);
    }
    public function getMessagesToWarnOnSiteRemoval(&$messages, $idSite)
    {
        Piwik::checkUserHasSuperUserAccess();
        $dao = new ContainersDao();
        $containers = $dao->getContainersForSite($idSite);
        if (!empty($containers)) {
            $view = new View('@TagManager/deleteWebsite');
            $view->containers = $containers;
            $view->link = Url::getCurrentUrlWithoutFileName() . 'index.php?' . Url::getQueryStringFromParameters(['idSite' => $idSite, 'module' => 'TagManager', 'action' => 'manageVersions']);
            $messages[] = $view->render();
        }
    }
    private function isAccessRestrictedForUser() : bool
    {
        $idSite = \Piwik\Request::fromRequest()->getIntegerParameter('idSite', 0);
        return !StaticContainer::get(\Piwik\Plugins\TagManager\SystemSettings::class)->doesCurrentUserHaveTagManagerAccess($idSite);
    }
}

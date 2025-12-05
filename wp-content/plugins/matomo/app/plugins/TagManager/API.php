<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager;

use Piwik\API\Request;
use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Date;
use Piwik\Piwik;
use Piwik\Plugins\TagManager\Access\Capability\PublishLiveContainer;
use Piwik\Plugins\TagManager\API\Export;
use Piwik\Plugins\TagManager\API\Import;
use Piwik\Plugins\TagManager\API\PreviewCookie;
use Piwik\Plugins\TagManager\API\TemplateMetadata;
use Piwik\Plugins\TagManager\Context\WebContext;
use Piwik\Plugins\TagManager\Dao\BaseDao;
use Piwik\Plugins\TagManager\Dao\ContainersDao;
use Piwik\Plugins\TagManager\Dao\VariablesDao;
use Piwik\Plugins\TagManager\Exception\EntityRecursionException;
use Piwik\Plugins\TagManager\Input\AccessValidator;
use Piwik\Plugins\TagManager\Model\Comparison;
use Piwik\Plugins\TagManager\Model\Container;
use Piwik\Plugins\TagManager\Model\Environment;
use Piwik\Plugins\TagManager\Model\Tag;
use Piwik\Plugins\TagManager\Model\Trigger;
use Piwik\Plugins\TagManager\Model\Variable;
use Piwik\Plugins\TagManager\Context\ContextProvider;
use Piwik\Plugins\TagManager\Template\Tag\MatomoTag;
use Piwik\Plugins\TagManager\Template\Tag\TagsProvider;
use Piwik\Plugins\TagManager\Template\Trigger\PageViewTrigger;
use Piwik\Plugins\TagManager\Template\Trigger\TriggersProvider;
use Piwik\Plugins\TagManager\Template\Variable\MatomoConfigurationVariable;
use Piwik\Plugins\TagManager\Template\Variable\VariablesProvider;
use Exception;
use Piwik\UrlHelper;
use Piwik\Validators\BaseValidator;
use Piwik\Validators\CharacterLength;
use Piwik\Validators\NotEmpty;
/**
 * API for plugin Tag Manager.
 *
 * Lets you configure all your containers, create, update and delete tags, triggers, and variables. Create and publish
 * new releases, enable and disable preview/debug mode, and much more.
 *
 * Please note: A container may have several versions. The current version that a user is editing is called the "draft"
 * version. You can get the ID of the "draft" version by calling {@link TagManager.getContainer}.
 *
 * @method static \Piwik\Plugins\TagManager\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    /**
     * @var Tag
     */
    private $tags;
    /**
     * @var Trigger
     */
    private $triggers;
    /**
     * @var Variable
     */
    private $variables;
    /**
     * @var Container
     */
    private $containers;
    /**
     * @var TagsProvider
     */
    private $tagsProvider;
    /**
     * @var TriggersProvider
     */
    private $triggersProvider;
    /**
     * @var VariablesProvider
     */
    private $variablesProvider;
    /**
     * @var ContextProvider
     */
    private $contextProvider;
    /**
     * @var Environment
     */
    private $environment;
    /**
     * @var Comparison
     */
    private $comparisons;
    /**
     * @var AccessValidator
     */
    private $accessValidator;
    /**
     * @var Export
     */
    private $export;
    /**
     * @var Import
     */
    private $import;
    /**
     * @var VariablesDao
     */
    private $variablesDao;
    private $enableGeneratePreview = \true;
    public function __construct(Tag $tags, Trigger $triggers, Variable $variables, Container $containers, TagsProvider $tagsProvider, TriggersProvider $triggersProvider, VariablesProvider $variablesProvider, ContextProvider $contextProvider, AccessValidator $validator, Environment $environment, Comparison $comparisons, Export $export, Import $import, VariablesDao $variablesDao)
    {
        //Started updating xdebug.max_nesting_level as infinite loop is detected due to variable is doing a self referencing when xdebug is active and max_nesting_level is set to lower value
        if (extension_loaded('xdebug')) {
            $xdebugMaxNestingLevel = ini_get('xdebug.max_nesting_level');
            if ($xdebugMaxNestingLevel && is_numeric($xdebugMaxNestingLevel) && $xdebugMaxNestingLevel < 2500) {
                ini_set('xdebug.max_nesting_level', 2500);
            }
        }
        $this->tags = $tags;
        $this->triggers = $triggers;
        $this->variables = $variables;
        $this->containers = $containers;
        $this->tagsProvider = $tagsProvider;
        $this->triggersProvider = $triggersProvider;
        $this->variablesProvider = $variablesProvider;
        $this->contextProvider = $contextProvider;
        $this->environment = $environment;
        $this->accessValidator = $validator;
        $this->export = $export;
        $this->import = $import;
        $this->comparisons = $comparisons;
        $this->variablesDao = $variablesDao;
    }
    /**
     * Get a list of all available contexts that can be used on this system. For example "web", "android", "ios"
     * @return array[]
     */
    public function getAvailableContexts()
    {
        Piwik::checkUserHasSomeViewAccess();
        $this->checkUserHasTagManagerAccess();
        $contexts = $this->contextProvider->getAllContexts();
        $return = array();
        foreach ($contexts as $context) {
            $tags = $this->getAvailableTagTypesInContext($context->getId());
            if (!empty($tags)) {
                $return[] = $context->toArray();
            }
        }
        return $return;
    }
    /**
     * Get a list of all available environments such as "live", "dev", "staging"
     * @return array
     */
    public function getAvailableEnvironments()
    {
        Piwik::checkUserHasSomeViewAccess();
        $this->checkUserHasTagManagerAccess();
        return $this->environment->getEnvironments();
    }
    /**
     * Get a list of all available environments such as "live", "dev", "staging" with the permission to publish.
     *
     * @param int $idSite
     * @return array
     */
    public function getAvailableEnvironmentsWithPublishCapability($idSite)
    {
        Piwik::checkUserHasSomeViewAccess();
        $this->checkUserHasTagManagerAccess($idSite);
        $environments = $this->environment->getEnvironments();
        $hasCapability = $this->accessValidator->hasPublishLiveEnvironmentCapability($idSite);
        return array_filter($environments, function ($environment) use($idSite, $hasCapability) {
            if ($environment['id'] === 'live' && !$hasCapability) {
                return \false;
            }
            return \true;
        });
    }
    /**
     * Get a list of all available fire limits which can be used when creating or updating a tag.
     * @return array
     */
    public function getAvailableTagFireLimits()
    {
        Piwik::checkUserHasSomeViewAccess();
        $this->checkUserHasTagManagerAccess();
        return $this->tags->getFireLimits();
    }
    /**
     * Get a list of all available comparisons which can be used for example as part of a trigger condition (filter)
     * or as part of a variable lookup table.
     * @return array
     */
    public function getAvailableComparisons()
    {
        Piwik::checkUserHasSomeViewAccess();
        $this->checkUserHasTagManagerAccess();
        return $this->comparisons->getSupportedComparisons();
    }
    /**
     * Returns a list of all available tag types in the context (for example "web").
     * @param string $idContext  The ID of a context, for example "web", "android" or "ios"
     * @return array
     */
    public function getAvailableTagTypesInContext($idContext)
    {
        Piwik::checkUserHasSomeViewAccess();
        $this->checkUserHasTagManagerAccess();
        $this->contextProvider->checkIsValidContext($idContext);
        $tags = $this->tagsProvider->getAllTags();
        $tagsInContext = [];
        foreach ($tags as $tag) {
            // GA3 tag deprecated
            if ($tag->getId() === 'GoogleAnalyticsUniversal') {
                continue;
            }
            if (in_array($idContext, $tag->getSupportedContexts(), \true)) {
                $tagsInContext[] = $tag;
            }
        }
        $templateMetadata = new TemplateMetadata();
        return $templateMetadata->formatTemplates($tagsInContext);
    }
    /**
     * Returns a list of all available trigger types in the context (for example "web").
     * @param string $idContext  The ID of a context, for example "web", "android" or "ios"
     * @return array
     */
    public function getAvailableTriggerTypesInContext($idContext)
    {
        Piwik::checkUserHasSomeViewAccess();
        $this->checkUserHasTagManagerAccess();
        $this->contextProvider->checkIsValidContext($idContext);
        $triggers = $this->triggersProvider->getAllTriggers();
        $triggersInContext = [];
        foreach ($triggers as $trigger) {
            if (in_array($idContext, $trigger->getSupportedContexts(), \true)) {
                $triggersInContext[] = $trigger;
            }
        }
        $templateMetadata = new TemplateMetadata();
        return $templateMetadata->formatTemplates($triggersInContext);
    }
    /**
     * Returns a list of all available variable types in the context (for example "web").
     * @param string $idContext  The ID of a context, for example "web", "android" or "ios"
     * @return array
     */
    public function getAvailableVariableTypesInContext($idContext)
    {
        Piwik::checkUserHasSomeViewAccess();
        $this->checkUserHasTagManagerAccess();
        $this->contextProvider->checkIsValidContext($idContext);
        $variables = $this->variablesProvider->getAllVariables();
        $variablesInContext = [];
        foreach ($variables as $variable) {
            if (!$variable->isPreConfigured() && in_array($idContext, $variable->getSupportedContexts(), \true)) {
                $variablesInContext[] = $variable;
            }
        }
        $templateMetadata = new TemplateMetadata();
        return $templateMetadata->formatTemplates($variablesInContext);
    }
    private function unsanitizeAssocArray($parameters)
    {
        if (!empty($parameters) && is_array($parameters)) {
            foreach ($parameters as $index => $value) {
                if (is_string($value)) {
                    $parameters[$index] = Common::unsanitizeInputValue($value);
                } elseif (is_array($value)) {
                    $parameters[$index] = $this->unsanitizeAssocArray($value);
                }
            }
        }
        return $parameters;
    }
    /**
     * Get the HTML/JavaScript block which loads a specific container. This allows you to automatically embed
     * a container into your website. It will return an HTML block containing a JavaScript element.
     *
     * Note: This method currently only works for containers in context "web".
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param string $environment The id of an environment, for example "live"
     * @return string
     */
    public function getContainerEmbedCode($idSite, $idContainer, $environment)
    {
        $this->accessValidator->checkViewPermission($idSite);
        $this->containers->checkContainerExists($idSite, $idContainer);
        $instructions = $this->containers->getContainerInstallInstructions($idSite, $idContainer, $environment);
        $instruction = array_shift($instructions);
        return $instruction['embedCode'];
    }
    /**
     * Returns instructions on how to embed the given container.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param string $environment The id of an environment, for example "live"
     * @param string $jsFramework The jsFramework for which instructions need to be fetched, for example "react"
     * @return array[]
     */
    public function getContainerInstallInstructions($idSite, $idContainer, $environment, $jsFramework = '')
    {
        $this->accessValidator->checkViewPermission($idSite);
        $this->containers->checkContainerExists($idSite, $idContainer);
        return $this->containers->getContainerInstallInstructions($idSite, $idContainer, $environment, $jsFramework);
    }
    /**
     * Get a list of all configured tags within the given container version.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of tags will be different per container. Therefore you need to provide
     *                                the ID of the version.
     * @return array
     */
    public function getContainerTags($idSite, $idContainer, $idContainerVersion)
    {
        $this->accessValidator->checkViewPermission($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        return $this->tags->getContainerTags($idSite, $idContainerVersion);
    }
    /**
     * Creates the default container for the given site. This container will automatically have a configured tag
     * to track a Matomo instance and also have a trigger assigned to track a pageview when a page is being viewed.
     *
     * While the Tag Manager creates this container by default for all new websites (measurables), it won't create
     * this container automatically for all previously existing websites if you have used Matomo before without the
     * Tag Manager. This API allows you to easily create this default container for all websites.
     *
     * Note: If the current site already has a default container, another default container will be created.
     *
     * @param int $idSite
     * @return string The ID of the created container.
     */
    public function createDefaultContainerForSite($idSite)
    {
        $this->accessValidator->checkWriteCapability($idSite);
        $loop = 0;
        $idContainer = null;
        while (empty($idContainer) && $loop <= 50) {
            // we try up to 51 times whether a name is available, and otherwise we give up
            $name = Piwik::translate('TagManager_DefaultContainer');
            if ($loop > 0) {
                $name .= ' ' . $loop;
            }
            try {
                $idContainer = Request::processRequest('TagManager.addContainer', array('idSite' => $idSite, 'context' => WebContext::ID, 'name' => $name, 'description' => Piwik::translate('TagManager_AutoGeneratedContainerDescription')), $default = []);
            } catch (Exception $e) {
                if ($e->getCode() !== ContainersDao::ERROR_NAME_IN_USE || $loop === 50) {
                    throw $e;
                }
            }
        }
        $draftVersion = $this->getContainerDraftVersion($idSite, $idContainer);
        $idVariable = Request::processRequest('TagManager.addContainerVariable', array('idSite' => $idSite, 'idContainer' => $idContainer, 'idContainerVersion' => $draftVersion, 'type' => MatomoConfigurationVariable::ID, 'name' => Piwik::translate('TagManager_MatomoConfigurationVariableName')), $default = []);
        $idTrigger = Request::processRequest('TagManager.addContainerTrigger', array('idSite' => $idSite, 'idContainer' => $idContainer, 'idContainerVersion' => $draftVersion, 'type' => PageViewTrigger::ID, 'name' => Piwik::translate('TagManager_PageViewTriggerName')), $default = []);
        $idTag = Request::processRequest('TagManager.addContainerTag', array('idSite' => $idSite, 'idContainer' => $idContainer, 'idContainerVersion' => $draftVersion, 'type' => MatomoTag::ID, 'name' => Piwik::translate('TagManager_MatomoTagName'), 'fireTriggerIds' => array($idTrigger), 'parameters' => array(MatomoTag::PARAM_MATOMO_CONFIG => '{{' . Piwik::translate('TagManager_MatomoConfigurationVariableName') . '}}')), $default = []);
        Request::processRequest('TagManager.createContainerVersion', array('idSite' => $idSite, 'idContainer' => $idContainer, 'name' => substr('0.1.0 - ' . Piwik::translate('TagManager_AutoGenerated'), 0, 50)), $default = []);
        Request::processRequest('TagManager.publishContainerVersion', array('idSite' => $idSite, 'idContainer' => $idContainer, 'idContainerVersion' => $draftVersion, 'environment' => Environment::ENVIRONMENT_LIVE), $default = []);
        return $idContainer;
    }
    /**
     * Creates a new tag within the given container version.
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of tags will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @param string $type The type of tag you want to create, for example "Matomo".
     * @param string $name   The name this tag should have
     * @param array $parameters  A key/value pair to define values for specific parameters. For example array('parameterName' => 'value')
     * @param int[] $fireTriggerIds  A list of trigger IDs that define when this tag should be fired. A tag will be executed as soon as any of these triggers fire. At least one trigger needs to be set.
     * @param int[] $blockTriggerIds Optional, a list of trigger IDs that block the execution of a tag. As soon as any of these triggers have been triggered, the tag will not be executed
     * @param string $fireLimit    Optional, limit how often the tag will be executed. For a list of available fire limits call {@link TagManager.getAvailableTagFireLimits}
     * @param int $fireDelay       Optional, a delay in milliseconds. If specified, instead of the tag being executed right away when a fire trigger is being triggered, the execution will be delayed.
     * @param int $priority       Optional, a custom priority which defines the order in which certain tags will be executed if multiple will be triggered at once. The lower the priority is, the earlier this tag may be fired.
     * @param null|string $startDate     Optional, a start date to ensure the tag will be only executed after this date. Please provide the date in UTC.
     * @param null|string $endDate       Optional, an end date to ensure the tag will not be executed after this date. Please provide the date in UTC.
     * @param null|string $description   Optional description
     *
     * @return int The ID of the created tag.
     */
    public function addContainerTag($idSite, $idContainer, $idContainerVersion, $type, $name, $parameters = [], $fireTriggerIds = [], $blockTriggerIds = [], $fireLimit = 'unlimited', $fireDelay = 0, $priority = 999, $startDate = null, $endDate = null, $description = '', $status = '')
    {
        $name = $this->decodeQuotes($name);
        $this->accessValidator->checkWriteCapability($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        if ($this->tagsProvider->isCustomTemplate($type) && !Piwik::isUserHasCapability($idSite, PublishLiveContainer::ID)) {
            $this->accessValidator->checkUseCustomTemplatesCapability($idSite);
        }
        $parameters = $this->unsanitizeAssocArray($parameters);
        $idTag = $this->tags->addContainerTag($idSite, $idContainerVersion, $type, $name, $parameters, $fireTriggerIds, $blockTriggerIds, $fireLimit, $fireDelay, $priority, $startDate, $endDate, $description, $status);
        $this->updateContainerPreviewRelease($idSite, $idContainer);
        return $idTag;
    }
    /**
     * Updates a specific tag configuration.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of tags will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @param int $idTag The id of the tag you want to update.
     * @param string $name   The name this tag should have
     * @param array $parameters  A key/value pair to define values for specific parameters. For example array('parameterName' => 'value')
     * @param int[] $fireTriggerIds  A list of trigger IDs that define when this tag should be fired. A tag will be executed as soon as any of these triggers fire. At least one trigger needs to be set.
     * @param int[] $blockTriggerIds Optional, a list of trigger IDs that block the execution of a tag. As soon as any of these triggers have been triggered, the tag will not be executed
     * @param string $fireLimit    Optional, limit how often the tag will be executed. For a list of available fire limits call {@link TagManager.getAvailableTagFireLimits}
     * @param int $fireDelay       Optional, a delay in milliseconds. If specified, instead of the tag being executed right away when a fire trigger is being triggered, the execution will be delayed.
     * @param int $priority       Optional, a custom priority which defines the order in which certain tags will be executed if multiple will be triggered at once. The lower the priority is, the earlier this tag may be fired.
     * @param null|string $startDate     Optional, a start date to ensure the tag will be only executed after this date. Please provide the date in UTC.
     * @param null|string $endDate       Optional, an end date to ensure the tag will not be executed after this date. Please provide the date in UTC.
     * @param null|string $description   Optional description
     */
    public function updateContainerTag($idSite, $idContainer, $idContainerVersion, $idTag, $name, $parameters = [], $fireTriggerIds = [], $blockTriggerIds = [], $fireLimit = 'unlimited', $fireDelay = 0, $priority = 999, $startDate = null, $endDate = null, $description = '')
    {
        $name = $this->decodeQuotes($name);
        $this->accessValidator->checkWriteCapability($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        $tag = $this->tags->getContainerTag($idSite, $idContainerVersion, $idTag);
        if (!empty($tag) && $this->tagsProvider->isCustomTemplate($tag['type'])) {
            $this->accessValidator->checkUseCustomTemplatesCapability($idSite);
        }
        $parameters = $this->unsanitizeAssocArray($parameters);
        $return = $this->tags->updateContainerTag($idSite, $idContainerVersion, $idTag, $name, $parameters, $fireTriggerIds, $blockTriggerIds, $fireLimit, $fireDelay, $priority, $startDate, $endDate, $description);
        $this->updateContainerPreviewRelease($idSite, $idContainer);
        return $return;
    }
    /**
     * Delete (remove) the given tag from the given container version.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of tags will be different per container. Therefore you need to provide
     *                                the ID of the version.
     * @param int $idTag The id of the tag you want to delete
     */
    public function deleteContainerTag($idSite, $idContainer, $idContainerVersion, $idTag)
    {
        $this->accessValidator->checkWriteCapability($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        if ($this->getContainerTag($idSite, $idContainer, $idContainerVersion, $idTag)) {
            $this->tags->deleteContainerTag($idSite, $idContainerVersion, $idTag);
            $this->updateContainerPreviewRelease($idSite, $idContainer);
            Piwik::postEvent('TagManager.deleteContainerTag.end', array(array('idSite' => $idSite, 'idContainer' => $idContainer, 'idContainerVersion' => $idContainerVersion, 'idTag' => $idTag)));
        }
    }
    /**
     * Pause the given tag from the given container version.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of tags will be different per container. Therefore you need to provide
     *                                the ID of the version.
     * @param int $idTag The id of the tag you want to pause
     */
    public function pauseContainerTag($idSite, $idContainer, $idContainerVersion, $idTag)
    {
        $this->accessValidator->checkWriteCapability($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        if ($this->getContainerTag($idSite, $idContainer, $idContainerVersion, $idTag)) {
            $this->tags->pauseContainerTag($idSite, $idContainerVersion, $idTag);
            $this->updateContainerPreviewRelease($idSite, $idContainer);
            Piwik::postEvent('TagManager.pauseContainerTag.end', array(array('idSite' => $idSite, 'idContainer' => $idContainer, 'idContainerVersion' => $idContainerVersion, 'idTag' => $idTag)));
            return \true;
        }
        return \false;
    }
    /**
     * Re-acivate the given tag from the given container version.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of tags will be different per container. Therefore you need to provide
     *                                the ID of the version.
     * @param int $idTag The id of the tag you want to re-activate
     */
    public function resumeContainerTag($idSite, $idContainer, $idContainerVersion, $idTag)
    {
        $this->accessValidator->checkWriteCapability($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        if ($this->getContainerTag($idSite, $idContainer, $idContainerVersion, $idTag)) {
            $this->tags->resumeContainerTag($idSite, $idContainerVersion, $idTag);
            $this->updateContainerPreviewRelease($idSite, $idContainer);
            Piwik::postEvent('TagManager.resumeContainerTag.end', array(array('idSite' => $idSite, 'idContainer' => $idContainer, 'idContainerVersion' => $idContainerVersion, 'idTag' => $idTag)));
            return \true;
        }
        return \false;
    }
    /**
     * Get a specific tag configuration.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of tags will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @param int $idTag The id of the tag you want to fetch.
     * @return array
     */
    public function getContainerTag($idSite, $idContainer, $idContainerVersion, $idTag)
    {
        $this->accessValidator->checkViewPermission($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        return $this->tags->getContainerTag($idSite, $idContainerVersion, $idTag);
    }
    /**
     * Returns a list of all places where this trigger is being referenced. This would be typically a list of all
     * tags that have this trigger in use. A trigger can be only deleted if the trigger is no longer referenced, therefore
     * you may need to ensure to first unassign the trigger from all references before deleting a trigger.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of trigger will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @param int $idTrigger The id of the trigger you want to fetch the references for.
     * @return array
     */
    public function getContainerTriggerReferences($idSite, $idContainer, $idContainerVersion, $idTrigger)
    {
        $this->accessValidator->checkViewPermission($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        $references = $this->triggers->getTriggerReferences($idSite, $idContainerVersion, $idTrigger);
        return $references;
    }
    /**
     * Get a list of all triggers within a specific container version.
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of trigger will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @return array
     */
    public function getContainerTriggers($idSite, $idContainer, $idContainerVersion)
    {
        $this->accessValidator->checkViewPermission($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        return $this->triggers->getContainerTriggers($idSite, $idContainerVersion);
    }
    /**
     * Creates a new trigger within the given container version.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of triggers will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @param int $type The type of trigger you want create, for example "AllElements".
     * @param string $name   The name this trigger should have
     * @param array $parameters  A key/value pair to define values for specific parameters. For example array('parameterName' => 'value')
     * @param array[] $conditions An array containing one or multiple conditions to filter when a trigger will be triggered. For example:
     *                            array(array('actual' => 'VARIABLENAME', 'comparison' => 'equals', 'expected' => 'expectedValue'))
     *                           To get a list of available comparisons, call {@link TagManager.getAvailableComparisons}
     * @param null|string $description   Optional description
     *
     * @return int   The id of the created trigger
     */
    public function addContainerTrigger($idSite, $idContainer, $idContainerVersion, $type, $name, $parameters = [], $conditions = [], $description = '')
    {
        $name = $this->decodeQuotes($name);
        $this->accessValidator->checkWriteCapability($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        if ($this->triggersProvider->isCustomTemplate($type) && !Piwik::isUserHasCapability($idSite, PublishLiveContainer::ID)) {
            $this->accessValidator->checkUseCustomTemplatesCapability($idSite);
        }
        $parameters = $this->unsanitizeAssocArray($parameters);
        $conditions = $this->unsanitizeAssocArray($conditions);
        $idTrigger = $this->triggers->addContainerTrigger($idSite, $idContainerVersion, $type, $name, $parameters, $conditions, $description);
        $this->updateContainerPreviewRelease($idSite, $idContainer);
        return $idTrigger;
    }
    /**
     * Updates the configuration of a specific trigger.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of triggers will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @param int $idTrigger The id of the trigger you want to update.
     * @param string $name   The name this trigger should have
     * @param array $parameters  A key/value pair to define values for specific parameters. For example array('parameterName' => 'value')
     * @param array[] $conditions An array containing one or multiple conditions to filter when a trigger will be triggered. For example:
     *                            array(array('actual' => 'VARIABLENAME', 'comparison' => 'equals', 'expected' => 'expectedValue'))
     *                           To get a list of available comparisons, call {@link TagManager.getAvailableComparisons}
     * @param null|string $description   Optional description
     */
    public function updateContainerTrigger($idSite, $idContainer, $idContainerVersion, $idTrigger, $name, $parameters = [], $conditions = [], $description = '')
    {
        $name = $this->decodeQuotes($name);
        $this->accessValidator->checkWriteCapability($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        $trigger = $this->triggers->getContainerTrigger($idSite, $idContainerVersion, $idTrigger);
        if (!empty($trigger) && $this->triggersProvider->isCustomTemplate($trigger['type'])) {
            $this->accessValidator->checkUseCustomTemplatesCapability($idSite);
        }
        $parameters = $this->unsanitizeAssocArray($parameters);
        $conditions = $this->unsanitizeAssocArray($conditions);
        $return = $this->triggers->updateContainerTrigger($idSite, $idContainerVersion, $idTrigger, $name, $parameters, $conditions, $description);
        $this->updateContainerPreviewRelease($idSite, $idContainer);
        return $return;
    }
    /**
     * Delete (remove) the given trigger from the container.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of trigger will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @param int $idTrigger The id of the trigger you want to delete.
     */
    public function deleteContainerTrigger($idSite, $idContainer, $idContainerVersion, $idTrigger)
    {
        $this->accessValidator->checkWriteCapability($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        if ($this->getContainerTrigger($idSite, $idContainer, $idContainerVersion, $idTrigger)) {
            $this->triggers->deleteContainerTrigger($idSite, $idContainerVersion, $idTrigger);
            $this->updateContainerPreviewRelease($idSite, $idContainer);
            Piwik::postEvent('TagManager.deleteContainerTrigger.end', array(array('idSite' => $idSite, 'idContainer' => $idContainer, 'idContainerVersion' => $idContainerVersion, 'idTrigger' => $idTrigger)));
        }
    }
    /**
     * Get the configuration of a specific trigger.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of trigger will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @param int $idTrigger The id of the trigger you want to get.
     * @return array
     */
    public function getContainerTrigger($idSite, $idContainer, $idContainerVersion, $idTrigger)
    {
        $this->accessValidator->checkViewPermission($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        return $this->triggers->getContainerTrigger($idSite, $idContainerVersion, $idTrigger);
    }
    /**
     * Returns a list of all places where this variable is being referenced. This would be typically a list of all
     * tags, triggers, and variables that have this variable in use. A variable can be only deleted if the variable
     * is no longer referenced, therefore you may need to ensure to first unassign/remove the variable from all
     * references before deleting a variable.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of variable will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @param int $idVariable The id of the variable you want to fetch the references for.
     * @return array
     */
    public function getContainerVariableReferences($idSite, $idContainer, $idContainerVersion, $idVariable)
    {
        $this->accessValidator->checkViewPermission($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        $references = $this->variables->getContainerVariableReferences($idSite, $idContainerVersion, $idVariable);
        return $references;
    }
    /**
     * Get a list of all manually configured variables within a container version. This API method does not return any preconfigured
     * variables. To fetch a list of all configured variables and all pre-configured variables, call
     * {@link TagManager.getAvailableContainerVariables}.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of variable will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @return array
     */
    public function getContainerVariables($idSite, $idContainer, $idContainerVersion)
    {
        $this->accessValidator->checkViewPermission($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        return $this->variables->getContainerVariables($idSite, $idContainerVersion);
    }
    /**
     * Get a list of all manually configured and all preconfigured variables within a container version.
     * To fetch a list of only manually configured variables (by a user), call
     * {@link TagManager.getContainerVariables}.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of variable will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @return array
     */
    public function getAvailableContainerVariables($idSite, $idContainer, $idContainerVersion)
    {
        $this->accessValidator->checkViewPermission($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        $variables = $this->getContainerVariables($idSite, $idContainer, $idContainerVersion);
        $containerVars = [];
        foreach ($variables as $index => $variable) {
            $containerVars[] = ['id' => $variable['name'], 'idvariable' => $variable['idvariable'], 'type' => $variable['type'], 'name' => $variable['name'], 'category' => 'Custom', 'description' => '', 'order' => $index, 'is_pre_configured' => \false];
        }
        foreach ($this->variablesProvider->getPreConfiguredVariables() as $variable) {
            $containerVars[] = ['id' => $variable->getId(), 'idvariable' => '', 'type' => $variable->getId(), 'name' => $variable->getName(), 'category' => Piwik::translate($variable->getCategory()), 'description' => $variable->getDescription(), 'order' => $variable->getOrder(), 'is_pre_configured' => \true];
        }
        $metadata = new TemplateMetadata();
        return $metadata->formatTemplates($containerVars);
    }
    /**
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of variable will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @param string $type        The type of variable you want to create.
     * @param string $name   The name this variable should have
     * @param array $parameters  A key/value pair to define values for specific parameters. For example array('parameterName' => 'value')
     * @param null|string $defaultValue   Optionally a default value
     * @param array[] $lookupTable An array containing one or multiple lookup configurations. For example:
     *                             array(array('match_value' => 'inval', 'comparison' => 'equals', 'out_value' => 'outval'))
     *                             For a list of available comparisons see {@link TagManager.getAvailableComparisons}
     * @param null|string $description   Optional description
     *
     * @return int The ID of the created variable
     */
    public function addContainerVariable($idSite, $idContainer, $idContainerVersion, $type, $name, $parameters = [], $defaultValue = \false, $lookupTable = [], $description = '')
    {
        $name = $this->decodeQuotes($name);
        $this->accessValidator->checkWriteCapability($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        if ($this->variablesProvider->isCustomTemplate($type) && !Piwik::isUserHasCapability($idSite, PublishLiveContainer::ID)) {
            $this->accessValidator->checkUseCustomTemplatesCapability($idSite);
        }
        $parameters = $this->unsanitizeAssocArray($parameters);
        $lookupTable = $this->unsanitizeAssocArray($lookupTable);
        $name = urldecode($name);
        $idVariable = $this->variables->addContainerVariable($idSite, $idContainerVersion, $type, $name, $parameters, $defaultValue, $lookupTable, $description);
        try {
            $this->updateContainerPreviewRelease($idSite, $idContainer);
        } catch (EntityRecursionException $e) {
            // we need to delete the previously added variable.... we first have to add the  variable to be able to
            // detect recursion and simulate container generation... if it fails we delete it again
            $this->forceDeleteVariable($idSite, $idContainerVersion, $idVariable);
            $this->updateContainerPreviewRelease($idSite, $idContainer);
            throw $e;
        }
        return $idVariable;
    }
    private function forceDeleteVariable($idSite, $idContainerVersion, $idVariable)
    {
        // we cannot use model here because it would trigger an error when a variable references itself
        // that the variable cannot be deleted because it's still in use by another variable
        $now = Date::now()->getDatetime();
        $this->variablesDao->deleteContainerVariable($idSite, $idContainerVersion, $idVariable, $now);
    }
    /**
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of variable will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @param int $idVariable The id of the variable you want to update.
     * @param string $name   The name this variable should have
     * @param array $parameters  A key/value pair to define values for specific parameters. For example array('parameterName' => 'value')
     * @param null|string $defaultValue   Optionally a default value
     * @param array[] $lookupTable An array containing one or multiple lookup configurations. For example:
     *                             array(array('match_value' => 'inval', 'comparison' => 'equals', 'out_value' => 'outval'))
     *                             For a list of available comparisons see {@link TagManager.getAvailableComparisons}
     * @param null|string $description   Optional description
     */
    public function updateContainerVariable($idSite, $idContainer, $idContainerVersion, $idVariable, $name, $parameters = [], $defaultValue = null, $lookupTable = [], $description = '')
    {
        $name = $this->decodeQuotes($name);
        $this->accessValidator->checkWriteCapability($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        $variable = $this->variables->getContainerVariable($idSite, $idContainerVersion, $idVariable);
        if (!empty($variable) && $this->variablesProvider->isCustomTemplate($variable['type'])) {
            $this->accessValidator->checkUseCustomTemplatesCapability($idSite);
        }
        $parameters = $this->unsanitizeAssocArray($parameters);
        $lookupTable = $this->unsanitizeAssocArray($lookupTable);
        $name = urldecode($name);
        $return = $this->variables->updateContainerVariable($idSite, $idContainerVersion, $idVariable, $name, $parameters, $defaultValue, $lookupTable, $description);
        try {
            $this->updateContainerPreviewRelease($idSite, $idContainer);
        } catch (EntityRecursionException $e) {
            // we need to restore the original value.... we first have to save update the original variable
            // in order to be able to check for recursion by simulating the container... if it fails we restore original value
            $this->variables->updateContainerVariable($variable['idsite'], $variable['idcontainerversion'], $variable['idvariable'], $variable['name'], $variable['parameters'], $variable['default_value'], $variable['lookup_table']);
            $this->updateContainerPreviewRelease($idSite, $idContainer);
            throw $e;
        }
        return $return;
    }
    /**
     * Delete (remove) a specific variable from a container version.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of variable will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @param int $idVariable The id of the variable you want to delete.
     */
    public function deleteContainerVariable($idSite, $idContainer, $idContainerVersion, $idVariable)
    {
        $this->accessValidator->checkWriteCapability($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        if ($this->getContainerVariable($idSite, $idContainer, $idContainerVersion, $idVariable)) {
            $this->variables->deleteContainerVariable($idSite, $idContainerVersion, $idVariable);
            $this->updateContainerPreviewRelease($idSite, $idContainer);
            Piwik::postEvent('TagManager.deleteContainerVariable.end', array(array('idSite' => $idSite, 'idContainer' => $idContainer, 'idContainerVersion' => $idContainerVersion, 'idVariable' => $idVariable)));
        }
    }
    /**
     * Get the configuration of a specific variable.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of variable will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @param int $idVariable The id of the variable you want to get.
     * @return array
     */
    public function getContainerVariable($idSite, $idContainer, $idContainerVersion, $idVariable)
    {
        $this->accessValidator->checkViewPermission($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        return $this->variables->getContainerVariable($idSite, $idContainerVersion, $idVariable);
    }
    /**
     * Get a list of all available containers within a site.
     * @param int $idSite
     * @return array
     */
    public function getContainers($idSite)
    {
        $this->accessValidator->checkViewPermission($idSite);
        return $this->containers->getContainers($idSite);
    }
    /**
     * Create a new container within the given site.
     *
     * @param int $idSite The ID of the site this container should belong to
     * @param string $context  The ID of a context, for example "web". To get a list of available contexts call
     *                      {@link TagManager.getAvailableContexts}
     * @param string $name   The name this container should have.
     * @param string $description Optionally a description for this container
     * @param int $ignoreGtmDataLayer Optionally indicate that we should ignore GTM dataLayer values
     * @param int $isTagFireLimitAllowedInPreviewMode Optionally indicate that we should respect fire tag limits when in preview mode
     * @param int $activelySyncGtmDataLayer Optionally indicate that we should actively sync new events from the GTM dataLayer to MTM
     * @return string The ID of the created container.
     */
    public function addContainer($idSite, $context, $name, $description = '', $ignoreGtmDataLayer = 0, $isTagFireLimitAllowedInPreviewMode = 0, $activelySyncGtmDataLayer = 0)
    {
        $name = $this->decodeQuotes($name);
        $this->accessValidator->checkWriteCapability($idSite);
        return $this->containers->addContainer($idSite, $context, $name, $description, $ignoreGtmDataLayer, $isTagFireLimitAllowedInPreviewMode, $activelySyncGtmDataLayer);
    }
    /**
     * Updates the name and description of the given container.
     *
     * @param int $idSite The ID of the site this container belongs to.
     * @param string $idContainer  The ID of the container you want to update, for example "6OMh6taM".
     * @param string $name   The name this container should have.
     * @param string $description Optionally a description for this container.
     * @param int $ignoreGtmDataLayer Optionally indicate that we should ignore GTM dataLayer values
     * @param int $isTagFireLimitAllowedInPreviewMode Optionally indicate that we should respect fire tag limits when in preview mode
     * @param int $activelySyncGtmDataLayer Optionally indicate that we should actively sync new events from the GTM dataLayer to MTM
     * @return string The ID of the created container.
     */
    public function updateContainer($idSite, $idContainer, $name, $description = '', $ignoreGtmDataLayer = 0, $isTagFireLimitAllowedInPreviewMode = 0, $activelySyncGtmDataLayer = 0)
    {
        $name = $this->decodeQuotes($name);
        $this->accessValidator->checkWriteCapability($idSite);
        $this->containers->checkContainerExists($idSite, $idContainer);
        $this->containers->updateContainer($idSite, $idContainer, $name, $description, $ignoreGtmDataLayer, $isTagFireLimitAllowedInPreviewMode, $activelySyncGtmDataLayer);
        $this->updateContainerPreviewRelease($idSite, $idContainer);
        return $idContainer;
    }
    /**
     * Creates a new version from either the current draft version or the given container version.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param string $name   The name this version should have
     * @param string $description Optionally the description this version should have
     * @param null $idContainerVersion By default a new version based on the current draft version will be created. However,
     *                                 You can also create a new version from a previously created version.
     * @return int  The ID of the created version.
     */
    public function createContainerVersion($idSite, $idContainer, $name, $description = '', $idContainerVersion = null)
    {
        $name = $this->decodeQuotes($name);
        $this->accessValidator->checkWriteCapability($idSite);
        if (!Piwik::isUserHasCapability($idSite, PublishLiveContainer::ID) && !Piwik::isUserHasCapability($idSite, PublishLiveContainer::ID)) {
            $this->accessValidator->checkUseCustomTemplatesCapability($idSite);
        }
        $this->containers->checkContainerExists($idSite, $idContainer);
        BaseValidator::check(Piwik::translate('TagManager_VersionName'), $name, [new NotEmpty(), new CharacterLength(1, 50)]);
        if (empty($idContainerVersion)) {
            $idContainerVersion = $this->getContainerDraftVersion($idSite, $idContainer);
        }
        $this->enableGeneratePreview = \false;
        $container = $this->containers->createContainerVersion($idSite, $idContainer, $idContainerVersion, $name, $description);
        // not needed to create a preview release as no actual change to container was made. Make it faster as the createContainerVersion
        // uses "import" logic which would create a new preview release or check for recursions on every created tag/trigger/...
        $this->enableGeneratePreview = \true;
        return $container;
    }
    /**
     * Updates a container version.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The id of the version you want to update
     * @param string $name   The new name this version should have
     * @param string $description Optionally the new description this version should have
     */
    public function updateContainerVersion($idSite, $idContainer, $idContainerVersion, $name, $description = '')
    {
        $name = $this->decodeQuotes($name);
        $this->accessValidator->checkWriteCapability($idSite);
        if (!Piwik::isUserHasCapability($idSite, PublishLiveContainer::ID) && !Piwik::isUserHasCapability($idSite, PublishLiveContainer::ID)) {
            $this->accessValidator->checkUseCustomTemplatesCapability($idSite);
        }
        BaseValidator::check(Piwik::translate('TagManager_VersionName'), $name, [new NotEmpty(), new CharacterLength(1, 50)]);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        return $this->containers->updateContainerVersion($idSite, $idContainer, $idContainerVersion, $name, $description);
    }
    /**
     * Get a list of all versions that exist for the given container.
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @return array
     */
    public function getContainerVersions($idSite, $idContainer)
    {
        $this->accessValidator->checkViewPermission($idSite);
        $this->containers->checkContainerExists($idSite, $idContainer);
        return $this->containers->getContainerVersions($idSite, $idContainer);
    }
    /**
     * Get details about a specific container version.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of variable will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @return array
     */
    public function getContainerVersion($idSite, $idContainer, $idContainerVersion)
    {
        $this->accessValidator->checkViewPermission($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        return $this->containers->getContainerVersion($idSite, $idContainer, $idContainerVersion);
    }
    /**
     * Delete a specific container version.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of variable will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     */
    public function deleteContainerVersion($idSite, $idContainer, $idContainerVersion)
    {
        $this->accessValidator->checkWriteCapability($idSite);
        $this->accessValidator->checkUseCustomTemplatesCapability($idSite);
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        if ($this->getContainerVersion($idSite, $idContainer, $idContainerVersion)) {
            $this->containers->deleteContainerVersion($idSite, $idContainer, $idContainerVersion);
            Piwik::postEvent('TagManager.deleteContainerVersion.end', array(array('idSite' => $idSite, 'idContainer' => $idContainer, 'idContainerVersion' => $idContainerVersion)));
        }
    }
    /**
     * Publish (release) a container version to the given environment.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of variable will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to.
     * @param string $environment The ID of the environment to which you want to publish this version to
     * @return array
     */
    public function publishContainerVersion($idSite, $idContainer, $idContainerVersion, $environment)
    {
        $this->accessValidator->checkWriteCapability($idSite);
        if ($environment === Environment::ENVIRONMENT_LIVE) {
            $this->accessValidator->checkPublishLiveEnvironmentCapability($idSite);
        } elseif (!Piwik::isUserHasCapability($idSite, PublishLiveContainer::ID)) {
            $this->accessValidator->checkUseCustomTemplatesCapability($idSite);
        }
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        $this->environment->checkIsValidEnvironment($environment);
        $releaseLogin = Piwik::getCurrentUserLogin();
        return $this->containers->publishVersion($idSite, $idContainer, $idContainerVersion, $environment, $releaseLogin);
    }
    /**
     * Deletes a container including all versions, releases, etc.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     */
    public function deleteContainer($idSite, $idContainer)
    {
        $this->accessValidator->checkWriteCapability($idSite);
        $this->containers->checkContainerExists($idSite, $idContainer);
        $this->containers->deleteContainer($idSite, $idContainer);
        Piwik::postEvent('TagManager.deleteContainer.end', array(array('idSite' => $idSite, 'idContainer' => $idContainer)));
    }
    /**
     * Get details about a specific container including existing versions, releases, the ID of the draft version, etc.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @return array
     */
    public function getContainer($idSite, $idContainer)
    {
        $this->accessValidator->checkViewPermission($idSite);
        $this->containers->checkContainerExists($idSite, $idContainer);
        return $this->containers->getContainer($idSite, $idContainer);
    }
    /**
     * Enables the preview/debug mode for the given container. The preview mode will be enabled for all environments a
     * container has releases for. To enable the preview mode for a specific version instead of the current draft,
     * ensure to set a container version.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of variable will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to. If no value is provided, the preview
     *                                mode will be enabled for the current "draft" version.
     */
    public function enablePreviewMode($idSite, $idContainer, $idContainerVersion = null)
    {
        $this->accessValidator->checkWriteCapability($idSite);
        $this->containers->checkContainerExists($idSite, $idContainer);
        if (empty($idContainerVersion)) {
            $idContainerVersion = $this->getContainerDraftVersion($idSite, $idContainer);
        }
        if (empty($idContainerVersion)) {
            throw new Exception(Piwik::translate('TagManager_ErrorContainerVersionDoesNotExist'));
        }
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        $releaseLogin = Piwik::getCurrentUserLogin();
        $this->containers->enablePreviewMode($idSite, $idContainer, $idContainerVersion, $releaseLogin);
        $cookie = new PreviewCookie();
        $cookie->enable($idSite, $idContainer);
    }
    /**
     * Disables the preview/debug mode for the given container.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     */
    public function disablePreviewMode($idSite, $idContainer)
    {
        $this->accessValidator->checkWriteCapability($idSite);
        $this->containers->checkContainerExists($idSite, $idContainer);
        if (empty($idContainerVersion)) {
            $idContainerVersion = $this->getContainerDraftVersion($idSite, $idContainer);
        }
        if (empty($idContainerVersion)) {
            throw new Exception(Piwik::translate('TagManager_ErrorContainerVersionDoesNotExist'));
        }
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        $this->containers->disablePreviewMode($idSite, $idContainer);
        $cookie = new PreviewCookie();
        $cookie->disable($idSite, $idContainer);
        $cookie->disableDebugSiteUrl();
    }
    /**
     * Updates the debug siteurl cookie
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param string $url  The url to enable debug
     */
    public function changeDebugUrl($idSite, $url)
    {
        $this->accessValidator->checkWriteCapability($idSite);
        if (!filter_var($url, \FILTER_VALIDATE_URL) || stripos($url, 'http') !== 0 || !UrlHelper::isLookLikeSafeUrl($url) || !UrlHelper::isLookLikeUrl($url)) {
            throw new Exception(Piwik::translate('TagManager_InvalidDebugUrl'));
        }
        $previewCookie = new PreviewCookie();
        $previewCookie->enableDebugSiteUrl($url);
    }
    /**
     * Exports a container version including all details such as the configured tags, triggers, and variables within
     * this version. You can use this export to import it into a different container version for example. By default,
     * the current draft will be exported unless you specify a specific container version.
     *
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param int $idContainerVersion The ID of the container version, a container may have multiple versions and
     *                                the list of variable will be different per container. Therefore you need to provide
     *                                the ID of the version you are referring to. If no version is provided, the current
     *                                "draft" version will be used.
     * @return array
     */
    public function exportContainerVersion($idSite, $idContainer, $idContainerVersion = null)
    {
        $this->accessValidator->checkViewPermission($idSite);
        $this->containers->checkContainerExists($idSite, $idContainer);
        if (empty($idContainerVersion)) {
            $idContainerVersion = $this->getContainerDraftVersion($idSite, $idContainer);
        }
        if (empty($idContainerVersion)) {
            throw new Exception(Piwik::translate('TagManager_ErrorContainerVersionDoesNotExist'));
        }
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        return $this->export->exportContainerVersion($idSite, $idContainer, $idContainerVersion);
    }
    /**
     * Import all tags, triggers, and variables from the given import. Please note that this will delete / remove
     * the all current tags, triggers, and variables from the current draft version and then import all tags, triggers,
     * and variables from a previously exported container version. To not lose the current draft configuration,
     * specify a backup name so nothing gets lost in case you ever want to revert.
     *
     * @param string $exportedContainerVersion A JSON formatted string containing a previously exported container version.
     * @param int $idSite The id of the site the given container belongs to
     * @param string $idContainer  The id of a container, for example "6OMh6taM"
     * @param string $backupName   If specified, a backup of the current draft will be created under this version name.
     * @return array
     */
    public function importContainerVersion($exportedContainerVersion, $idSite, $idContainer, $backupName = '')
    {
        $this->accessValidator->checkWriteCapability($idSite);
        if (!Piwik::isUserHasCapability($idSite, PublishLiveContainer::ID)) {
            $this->accessValidator->checkUseCustomTemplatesCapability($idSite);
        }
        $this->containers->checkContainerExists($idSite, $idContainer);
        $idContainerVersion = $this->getContainerDraftVersion($idSite, $idContainer);
        if (empty($idContainerVersion)) {
            throw new Exception(Piwik::translate('TagManager_ErrorContainerVersionDoesNotExist'));
        }
        $exportedContainerVersion = Common::unsanitizeInputValue($exportedContainerVersion);
        $exportedContainerVersion = @json_decode($exportedContainerVersion, \true);
        if (empty($exportedContainerVersion) || !is_array($exportedContainerVersion)) {
            throw new Exception(Piwik::translate('TagManager_ErrorInvalidContainerImportFormat'));
        }
        // we validate before actually creating a backup version
        $this->import->checkImportContainerIsPossible($exportedContainerVersion, $idSite, $idContainer);
        if (!empty($backupName)) {
            $this->createContainerVersion($idSite, $idContainer, $backupName);
        }
        $this->containers->checkContainerVersionExists($idSite, $idContainer, $idContainerVersion);
        $this->enableGeneratePreview = \false;
        $this->import->importContainerVersion($exportedContainerVersion, $idSite, $idContainer, $idContainerVersion);
        $this->enableGeneratePreview = \true;
        $this->updateContainerPreviewRelease($idSite, $idContainer);
    }
    private function updateContainerPreviewRelease($idSite, $idContainer)
    {
        if (!$this->enableGeneratePreview) {
            return;
        }
        if ($this->containers->hasPreviewRelease($idSite, $idContainer)) {
            $this->containers->generateContainer($idSite, $idContainer);
        } else {
            // we simulate generate the container to possibly detect if a variable references itself. as there might not be
            // any release and because we only want to simulate the current version we create a "fake" preview release
            $simulatorContext = StaticContainer::get(\Piwik\Plugins\TagManager\SimulatorContext::class);
            $container = $this->getContainer($idSite, $idContainer);
            $container['releases'] = [['idcontainerrelease' => '', 'idcontainer' => $container['idcontainer'], 'idcontainerversion' => $this->getContainerDraftVersion($idSite, $idContainer), 'environment' => Environment::ENVIRONMENT_PREVIEW, 'release_login' => Piwik::getCurrentUserLogin(), 'status' => BaseDao::STATUS_ACTIVE]];
            $simulatorContext->generate($container);
        }
    }
    private function getContainerDraftVersion($idSite, $idContainer)
    {
        $containerVersion = $this->containers->getContainer($idSite, $idContainer);
        if (!empty($containerVersion['draft']['idcontainerversion'])) {
            return $containerVersion['draft']['idcontainerversion'];
        }
    }
    private function decodeQuotes($value)
    {
        return htmlspecialchars_decode($value, \ENT_QUOTES);
    }
    /**
     * Check whether the current user has MTM access. If the site ID isn't provided, try looking it up on the request
     *
     * @param $idSite
     * @return void
     * @throws \Piwik\NoAccessException
     */
    private function checkUserHasTagManagerAccess($idSite = null)
    {
        if (empty($idSite)) {
            $idSite = \Piwik\Request::fromRequest()->getIntegerParameter('idSite', 0);
        }
        $this->accessValidator->checkUserHasTagManagerAccess($idSite);
    }
}

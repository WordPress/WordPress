<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Model;

use Piwik\Container\StaticContainer;
use Piwik\Date;
use Piwik\Piwik;
use Piwik\Plugins\TagManager\Dao\TagsDao;
use Piwik\Plugins\TagManager\Input\IdSite;
use Piwik\Plugins\TagManager\Input\Name;
use Piwik\Plugins\TagManager\Validators\TriggerIds;
use Piwik\Plugins\TagManager\Template\Tag\TagsProvider;
use Piwik\Validators\BaseValidator;
use Piwik\Validators\DateTime;
use Piwik\Validators\NotEmpty;
use Piwik\Validators\NumberRange;
use Piwik\Validators\WhitelistedValue;
class Tag extends \Piwik\Plugins\TagManager\Model\BaseModel
{
    public const FIRE_LIMIT_UNLIMITED = 'unlimited';
    public const FIRE_LIMIT_ONCE_IN_LIFETIME = 'once_lifetime';
    public const FIRE_LIMIT_ONCE_24_HOURS = 'once_24hours';
    public const FIRE_LIMIT_ONCE_PER_PAGE = 'once_page';
    /**
     * @var TagsDao
     */
    private $dao;
    /**
     * @var TagsProvider
     */
    private $tagsProvider;
    public function __construct(TagsDao $tagsDao, TagsProvider $tagsProvider)
    {
        $this->dao = $tagsDao;
        $this->tagsProvider = $tagsProvider;
    }
    public function getFireLimits()
    {
        return [['id' => self::FIRE_LIMIT_UNLIMITED, 'name' => Piwik::translate('TagManager_Unlimited')], ['id' => self::FIRE_LIMIT_ONCE_PER_PAGE, 'name' => Piwik::translate('TagManager_OncePage')], ['id' => self::FIRE_LIMIT_ONCE_24_HOURS, 'name' => Piwik::translate('TagManager_Once24Hours')], ['id' => self::FIRE_LIMIT_ONCE_IN_LIFETIME, 'name' => Piwik::translate('TagManager_OnceLifetime')]];
    }
    public function addContainerTag($idSite, $idContainerVersion, $type, $name, $parameters, $fireTriggerIds, $blockTriggerIds, $fireLimit, $fireDelay, $priority, $startDate, $endDate, $description = '', $status = '')
    {
        $this->validateValues($idSite, $name, $idContainerVersion, $fireTriggerIds, $blockTriggerIds, $fireLimit, $fireDelay, $priority, $startDate, $endDate);
        $this->tagsProvider->checkIsValidTag($type);
        $parameters = $this->formatParameters($type, $parameters);
        $createdDate = $this->getCurrentDateTime();
        return $this->dao->createTag($idSite, $idContainerVersion, $type, $name, $parameters, $fireTriggerIds, $blockTriggerIds, $fireLimit, $fireDelay, $priority, $startDate, $endDate, $createdDate, $description, $status);
    }
    private function validateValues($idSite, $name, $idContainerVersion, $fireTriggerIds, $blockTriggerIds, $fireLimit, $fireDelay, $priority, $startDate, $endDate)
    {
        $site = new IdSite($idSite);
        $site->check();
        $name = new Name($name);
        $name->check();
        if (empty($blockTriggerIds)) {
            $blockTriggerIds = array();
        }
        $firelimits = array();
        foreach ($this->getFireLimits() as $fireLimitArr) {
            $firelimits[] = $fireLimitArr['id'];
        }
        BaseValidator::check('Fire Trigger', $fireTriggerIds, [new NotEmpty(), new TriggerIds($idSite, $idContainerVersion)]);
        BaseValidator::check('Block Trigger', $blockTriggerIds, [new TriggerIds($idSite, $idContainerVersion)]);
        BaseValidator::check('Fire limit', $fireLimit, [new WhitelistedValue($firelimits)]);
        BaseValidator::check('Fire delay', $fireDelay, [new NumberRange(0, NumberRange::MAX_MEDIUM_INT_UNSIGNED)]);
        BaseValidator::check('Priority', $priority, [new NumberRange(0, NumberRange::MAX_SMALL_INT_UNSIGNED)]);
        BaseValidator::check('Start date', $startDate, [new DateTime()]);
        BaseValidator::check('End date', $endDate, [new DateTime()]);
        if ($startDate && $endDate && Date::factory($endDate)->isEarlier(Date::factory($startDate))) {
            throw new \Exception(Piwik::translate('TagManager_ErrorEndDateBeforeStartDate'));
        }
    }
    private function formatParameters($tagType, $parameters)
    {
        $tagTemplate = $this->tagsProvider->getTag($tagType);
        if (empty($tagTemplate)) {
            throw new \Exception('Invalid tag type');
        }
        $params = $tagTemplate->getParameters();
        // we make sure to only save parameters that are defined in the tag template
        $newParameters = [];
        foreach ($params as $param) {
            if (isset($parameters[$param->getName()])) {
                $param->setValue($parameters[$param->getName()]);
                $newParameters[$param->getName()] = $param->getValue();
            } else {
                // we need to set a value to make sure that if for example a value is required, we trigger an error
                $param->setValue($param->getDefaultValue());
            }
        }
        return $newParameters;
    }
    public function updateParameters($idSite, $idContainerVersion, $idTag, $parameters)
    {
        $tag = $this->dao->getContainerTag($idSite, $idContainerVersion, $idTag);
        if (!empty($tag)) {
            $parameters = $this->formatParameters($tag['type'], $parameters);
            $this->updateTagColumns($idSite, $idContainerVersion, $idTag, array('parameters' => $parameters));
        }
    }
    public function updateContainerTag($idSite, $idContainerVersion, $idTag, $name, $parameters, $fireTriggerIds, $blockTriggerIds, $fireLimit, $fireDelay, $priority, $startDate, $endDate, $description = '')
    {
        $this->validateValues($idSite, $name, $idContainerVersion, $fireTriggerIds, $blockTriggerIds, $fireLimit, $fireDelay, $priority, $startDate, $endDate);
        $tag = $this->dao->getContainerTag($idSite, $idContainerVersion, $idTag);
        if (!empty($tag)) {
            $parameters = $this->formatParameters($tag['type'], $parameters);
            $columns = array('name' => $name, 'description' => $description, 'parameters' => $parameters, 'fire_trigger_ids' => $fireTriggerIds, 'block_trigger_ids' => $blockTriggerIds, 'fire_limit' => $fireLimit, 'fire_delay' => $fireDelay, 'priority' => $priority, 'start_date' => empty($startDate) ? null : $startDate, 'end_date' => empty($endDate) ? null : $endDate);
            $this->updateTagColumns($idSite, $idContainerVersion, $idTag, $columns);
        }
    }
    public function getContainerTags($idSite, $idContainerVersion)
    {
        $tags = $this->dao->getContainerTags($idSite, $idContainerVersion);
        return $this->enrichTags($tags);
    }
    public function deleteContainerTag($idSite, $idContainerVersion, $idTag)
    {
        $this->dao->deleteContainerTag($idSite, $idContainerVersion, $idTag, $this->getCurrentDateTime());
    }
    public function pauseContainerTag($idSite, $idContainerVersion, $idTag)
    {
        $this->dao->pauseContainerTag($idSite, $idContainerVersion, $idTag);
    }
    public function resumeContainerTag($idSite, $idContainerVersion, $idTag)
    {
        $this->dao->resumeContainerTag($idSite, $idContainerVersion, $idTag);
    }
    public function getContainerTag($idSite, $idContainerVersion, $idTag)
    {
        $tag = $this->dao->getContainerTag($idSite, $idContainerVersion, $idTag);
        return $this->enrichTag($tag);
    }
    /**
     * Make a copy of a tag. This can either be within the same container or to a different site/container. If within
     *  the same container, only the tag is copied and it uses the same triggers and variables. If it's going to a
     *  different container, it will make copies of all the triggers and variables that the tag references so that the
     *  copy will have the same triggers and variables available.
     *
     * @param int $idSite
     * @param int $idContainerVersion
     * @param int $idTag ID of the tag to make a copy of
     * @param int|null $idDestinationSite Optional ID of the site to copy to the tag to. If not provided the copy goes
     * to the source site and container
     * @param string|null $idDestinationContainer Optional ID of the container to copy the tag to. If not provided the
     * copy goes to the source site and container
     * @return int ID of the newly created Tag
     * @throws \Exception
     */
    public function copyTag(int $idSite, int $idContainerVersion, int $idTag, ?int $idDestinationSite = null, ?string $idDestinationContainer = null) : int
    {
        $tag = $this->getContainerTag($idSite, $idContainerVersion, $idTag);
        $idDestinationVersion = $idContainerVersion;
        if ($idDestinationSite !== null && !empty($idDestinationContainer)) {
            $idDestinationVersion = $this->copyReferencedVariablesAndTriggers($tag, $idSite, $idContainerVersion, $idDestinationSite, $idDestinationContainer);
        }
        // If the destination site isn't set, simply use the source site
        $idDestinationSite = $idDestinationSite ?? $idSite;
        $newName = $this->dao->makeCopyNameUnique($idDestinationSite, $tag['name'], $idDestinationVersion);
        return $this->addContainerTag($idDestinationSite, $idDestinationVersion, $tag['type'], $newName, $tag['parameters'], $tag['fire_trigger_ids'], $tag['block_trigger_ids'], $tag['fire_limit'], $tag['fire_delay'], $tag['priority'], $tag['start_date'], $tag['end_date'], $tag['description'], $tag['status']);
    }
    private function copyReferencedVariablesAndTriggers(array &$tag, int $idSite, int $idContainerVersion, int $idDestinationSite, string $idDestinationContainer) : int
    {
        $idDestinationVersion = $this->getDraftContainerVersion($idDestinationSite, $idDestinationContainer);
        // Copy all the referenced variables and triggers and replace those references with references to the newly copied ones
        StaticContainer::get(\Piwik\Plugins\TagManager\Model\Variable::class)->copyReferencedVariables($tag, $idSite, $idContainerVersion, $idDestinationSite, $idDestinationVersion);
        $tag['fire_trigger_ids'] = $this->copyReferencedTriggers($idSite, $idContainerVersion, $tag['fire_trigger_ids'], $idDestinationSite, $idDestinationVersion);
        $tag['block_trigger_ids'] = $this->copyReferencedTriggers($idSite, $idContainerVersion, $tag['block_trigger_ids'], $idDestinationSite, $idDestinationVersion);
        return $idDestinationVersion;
    }
    private function copyReferencedTriggers(int $idSite, int $idContainerVersion, array $triggerIds, int $idDestinationSite, int $idDestinationVersion) : array
    {
        $newTriggerIds = [];
        foreach ($triggerIds as $triggerId) {
            $newTriggerIds[] = StaticContainer::get(\Piwik\Plugins\TagManager\Model\Trigger::class)->copyTriggerIfNoEquivalent($idSite, $idContainerVersion, $triggerId, $idDestinationSite, $idDestinationVersion);
        }
        return $newTriggerIds;
    }
    private function updateTagColumns($idSite, $idContainerVersion, $idTag, $columns)
    {
        if (!isset($columns['updated_date'])) {
            $columns['updated_date'] = $this->getCurrentDateTime();
        }
        $this->dao->updateTagColumns($idSite, $idContainerVersion, $idTag, $columns);
    }
    private function enrichTags($tags)
    {
        if (empty($tags)) {
            return array();
        }
        foreach ($tags as $index => $tag) {
            $tags[$index] = $this->enrichTag($tag);
        }
        return $tags;
    }
    private function enrichTag($tag)
    {
        if (empty($tag)) {
            return $tag;
        }
        $tag['created_date_pretty'] = $this->formatDate($tag['created_date'], $tag['idsite']);
        $tag['updated_date_pretty'] = $this->formatDate($tag['updated_date'], $tag['idsite']);
        unset($tag['deleted_date']);
        $tag['typeMetadata'] = null;
        if (empty($tag['parameters'])) {
            $tag['parameters'] = array();
        }
        $tagType = $this->tagsProvider->getTag($tag['type']);
        if (!empty($tagType)) {
            $tag['typeMetadata'] = $tagType->toArray();
            foreach ($tag['typeMetadata']['parameters'] as &$parameter) {
                $paramName = $parameter['name'];
                if (isset($tag['parameters'][$paramName])) {
                    $parameter['value'] = $tag['parameters'][$paramName];
                } else {
                    $tag['parameters'][$paramName] = $parameter['defaultValue'];
                }
            }
        }
        return $tag;
    }
}

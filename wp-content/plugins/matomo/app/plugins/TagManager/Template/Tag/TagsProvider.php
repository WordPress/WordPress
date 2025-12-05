<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Tag;

use Piwik\Container\StaticContainer;
use Piwik\Piwik;
use Piwik\Plugin\Manager;
use Piwik\Plugins\TagManager\Configuration;
use Piwik\Plugins\TagManager\SystemSettings;
class TagsProvider
{
    /**
     * @var Manager
     */
    private $pluginManager;
    /**
     * @var Configuration
     */
    private $configuration;
    /**
     * @var BaseTag[]
     */
    private $cached;
    /**
     * @var SystemSettings
     */
    private $settings;
    public function __construct(Manager $pluginManager, Configuration $configuration, SystemSettings $systemSettings)
    {
        $this->pluginManager = $pluginManager;
        $this->configuration = $configuration;
        $this->settings = $systemSettings;
    }
    public function checkIsValidTag($tagId)
    {
        if (!$this->getTag($tagId)) {
            throw new \Exception(sprintf('The tag "%s" is not supported', $tagId));
        }
    }
    /**
     * @param string $tagId  eg "matomo"
     * @return BaseTag|null
     */
    public function getTag($tagId)
    {
        foreach ($this->getAllTags() as $tag) {
            if ($tag->getId() === $tagId) {
                return $tag;
            }
        }
    }
    /**
     * @return BaseTag[]
     */
    public function getAllTags()
    {
        if (!isset($this->cached)) {
            $blockedTags = $this->configuration->getDisabledTags();
            $blockedTags = array_map('strtolower', $blockedTags);
            $tagClasses = $this->pluginManager->findMultipleComponents('Template/Tag', 'Piwik\\Plugins\\TagManager\\Template\\Tag\\BaseTag');
            $tags = array();
            /**
             * Event to add custom tags. To filter tags have a look at the {@hook TagManager.filterTags}
             * event.
             *
             * **Example**
             *
             *     public function addTags(&$tags)
             *     {
             *         $tags[] = new MyCustomTag();
             *     }
             *
             * @param BaseTag[] &$tags An array containing a list of tags.
             */
            Piwik::postEvent('TagManager.addTags', array(&$tags));
            $restrictValue = $this->settings->restrictCustomTemplates->getValue();
            $disableCustomTemplates = $restrictValue === SystemSettings::CUSTOM_TEMPLATES_DISABLED;
            foreach ($tagClasses as $tag) {
                /** @var BaseTag $tagInstance */
                $tagInstance = StaticContainer::get($tag);
                if ($disableCustomTemplates && $tagInstance->isCustomTemplate()) {
                    continue;
                }
                if (in_array(strtolower($tagInstance->getId()), $blockedTags, \true)) {
                    continue;
                }
                $tags[] = $tagInstance;
            }
            /**
             * Triggered to filter / restrict tags.
             *
             * **Example**
             *
             *     public function filterTags(&$tags)
             *     {
             *         foreach ($tags as $index => $tag) {
             *              if ($tag->getId() === 'CustomHtml') {}
             *                  unset($tags[$index]); // remove the tag having this ID
             *              }
             *         }
             *     }
             *
             * @param BaseTag[] &$tags An array containing a list of tags.
             */
            Piwik::postEvent('TagManager.filterTags', array(&$tags));
            $this->cached = $tags;
        }
        return $this->cached;
    }
    public function isCustomTemplate($id)
    {
        foreach ($this->getAllTags() as $tag) {
            if ($tag->isCustomTemplate() && $tag->getId() === $id) {
                return \true;
            }
        }
        return \false;
    }
}

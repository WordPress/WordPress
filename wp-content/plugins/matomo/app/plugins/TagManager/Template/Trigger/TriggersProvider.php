<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Trigger;

use Piwik\Container\StaticContainer;
use Piwik\Piwik;
use Piwik\Plugin\Manager;
use Piwik\Plugins\TagManager\Configuration;
use Piwik\Plugins\TagManager\SystemSettings;
class TriggersProvider
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
     * @var BaseTrigger[]
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
    public function checkIsValidTrigger($triggerId)
    {
        if (!$this->getTrigger($triggerId)) {
            throw new \Exception(sprintf('The trigger "%s" is not supported', $triggerId));
        }
    }
    /**
     * @param string $triggerId  eg "click"
     * @return BaseTrigger|null
     */
    public function getTrigger($triggerId)
    {
        foreach ($this->getAllTriggers() as $trigger) {
            if ($trigger->getId() === $triggerId) {
                return $trigger;
            }
        }
    }
    /**
     * @return BaseTrigger[]
     */
    public function getAllTriggers()
    {
        if (!isset($this->cached)) {
            $blockedTriggers = $this->configuration->getDisabledTriggers();
            $blockedTriggers = array_map('strtolower', $blockedTriggers);
            $triggerClasses = $this->pluginManager->findMultipleComponents('Template/Trigger', 'Piwik\\Plugins\\TagManager\\Template\\Trigger\\BaseTrigger');
            $triggers = array();
            /**
             * Event to add custom triggers. To filter triggers have a look at the {@hook TagManager.filterTriggers}
             * event.
             *
             * **Example**
             *
             *     public function addTriggers(&$triggers)
             *     {
             *         $triggers[] = new MyCustomTrigger();
             *     }
             *
             * @param BaseTrigger[] &$triggers An array containing a list of triggers.
             */
            Piwik::postEvent('TagManager.addTriggers', array(&$triggers));
            $restrictValue = $this->settings->restrictCustomTemplates->getValue();
            $disableCustomTemplates = $restrictValue === SystemSettings::CUSTOM_TEMPLATES_DISABLED;
            foreach ($triggerClasses as $trigger) {
                /** @var BaseTrigger $triggerInstance */
                $triggerInstance = StaticContainer::get($trigger);
                if ($disableCustomTemplates && $triggerInstance->isCustomTemplate()) {
                    continue;
                }
                if (in_array(strtolower($triggerInstance->getId()), $blockedTriggers, \true)) {
                    continue;
                }
                $triggers[] = $triggerInstance;
            }
            /**
             * Event to filter / restrict triggers.
             *
             * **Example**
             *
             *     public function filterTriggers(&$triggers)
             *     {
             *         foreach ($triggers as $index => $trigger) {
             *              if ($trigger->getId() === 'CustomJs') {}
             *                  unset($triggers[$index]); // remove the trigger having this ID
             *              }
             *         }
             *     }
             *
             * @param BaseTrigger[] &$triggers An array containing a list of triggers.
             */
            Piwik::postEvent('TagManager.filterTriggers', array(&$triggers));
            $this->cached = $triggers;
        }
        return $this->cached;
    }
    public function isCustomTemplate($id)
    {
        foreach ($this->getAllTriggers() as $trigger) {
            if ($trigger->isCustomTemplate() && $trigger->getId() === $id) {
                return \true;
            }
        }
        return \false;
    }
}

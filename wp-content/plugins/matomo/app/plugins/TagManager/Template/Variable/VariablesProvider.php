<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Variable;

use Piwik\Container\StaticContainer;
use Piwik\Piwik;
use Piwik\Plugin\Manager;
use Piwik\Plugins\TagManager\Configuration;
use Piwik\Plugins\TagManager\SystemSettings;
class VariablesProvider
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
     * @var BaseVariable[]
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
    public function checkIsValidVariable($variableId)
    {
        if (!$this->getVariable($variableId)) {
            throw new \Exception(sprintf('The variable "%s" is not supported', $variableId));
        }
    }
    /**
     * @param string $variableId  eg "click"
     * @return BaseVariable|null
     */
    public function getVariable($variableId)
    {
        foreach ($this->getAllVariables() as $variable) {
            if ($variable->getId() === $variableId) {
                return $variable;
            }
        }
    }
    /**
     * @param string $variableId  eg "click"
     * @return BaseVariable|null
     */
    public function getVariableIgnoreCase($variableId)
    {
        $variableId = strtolower($variableId);
        foreach ($this->getAllVariables() as $variable) {
            if (strtolower($variable->getId()) === $variableId) {
                return $variable;
            }
        }
    }
    /**
     * @return BaseVariable[]
     */
    public function getAllVariables()
    {
        if (!isset($this->cached)) {
            $blockedVariables = $this->configuration->getDisabledVariables();
            $blockedVariables = array_map('strtolower', $blockedVariables);
            $variableClasses = $this->pluginManager->findMultipleComponents('Template/Variable', 'Piwik\\Plugins\\TagManager\\Template\\Variable\\BaseVariable');
            $variables = array();
            /**
             * Event to add custom variables. To filter variables have a look at the {@hook TagManager.filterVariables}
             * event.
             *
             * **Example**
             *
             *     public function addVariables(&$variables)
             *     {
             *         $variables[] = new MyCustomVariable();
             *     }
             *
             * @param BaseVariable[] &$variables An array containing a list of variables.
             */
            Piwik::postEvent('TagManager.addVariables', array(&$variables));
            $restrictValue = $this->settings->restrictCustomTemplates->getValue();
            $disableCustomTemplates = $restrictValue === SystemSettings::CUSTOM_TEMPLATES_DISABLED;
            foreach ($variableClasses as $variable) {
                /** @var BaseVariable $variableInstance */
                $variableInstance = StaticContainer::get($variable);
                if ($disableCustomTemplates && $variableInstance->isCustomTemplate()) {
                    continue;
                }
                if (in_array(strtolower($variableInstance->getId()), $blockedVariables, \true)) {
                    continue;
                }
                $variables[] = $variableInstance;
            }
            /**
             * Event to filter / restrict variables.
             *
             * **Example**
             *
             *     public function filterVariables(&$variables)
             *     {
             *         foreach ($variables as $index => $variable) {
             *              if ($variable->getId() === 'CustomVariable') {}
             *                  unset($variables[$index]); // remove the variable having this ID
             *              }
             *         }
             *     }
             *
             * @param BaseVariable[] &$variables An array containing a list of variables.
             */
            Piwik::postEvent('TagManager.filterVariables', array(&$variables));
            $this->cached = $variables;
        }
        return $this->cached;
    }
    /**
     * @return BaseVariable
     */
    public function getPreConfiguredVariable($variableId)
    {
        foreach ($this->getPreConfiguredVariables() as $variable) {
            if ($variable->getId() === $variableId) {
                return $variable;
            }
        }
    }
    /**
     * @return BaseVariable[]
     */
    public function getPreConfiguredVariables()
    {
        $variables = $this->getAllVariables();
        $preConfigured = array();
        foreach ($variables as $variable) {
            if ($variable->isPreConfigured()) {
                $preConfigured[] = $variable;
            }
        }
        return $preConfigured;
    }
    public function isCustomTemplate($id)
    {
        foreach ($this->getAllVariables() as $variable) {
            if ($variable->isCustomTemplate() && $variable->getId() === $id) {
                return \true;
            }
        }
        return \false;
    }
}

<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Context\BaseContext;

use Piwik\Plugins\TagManager\Template\Tag\TagsProvider;
use Piwik\Plugins\TagManager\Template\Trigger\TriggersProvider;
use Piwik\Plugins\TagManager\Template\Variable\VariablesProvider;
class TemplateLocator
{
    /**
     * @var array
     */
    protected $templateFunctions = array();
    /**
     * @var TagsProvider
     */
    protected $tagsProvider;
    /**
     * @var TriggersProvider
     */
    protected $triggersProvider;
    /**
     * @var VariablesProvider
     */
    protected $variablesProvider;
    public function __construct(TagsProvider $tagsProvider, TriggersProvider $triggersProvider, VariablesProvider $variablesProvider)
    {
        $this->tagsProvider = $tagsProvider;
        $this->triggersProvider = $triggersProvider;
        $this->variablesProvider = $variablesProvider;
    }
    public function getLoadedTemplates()
    {
        return $this->templateFunctions;
    }
    public function loadTagTemplate($tag, $contextId)
    {
        $tagType = $tag['type'];
        $tagTemplate = $this->tagsProvider->getTag($tagType);
        if ($tagTemplate) {
            $template = $tagTemplate->loadTemplate($contextId, $tag);
            if ($template) {
                $methodName = $tagType . 'Tag';
                $this->templateFunctions[$methodName] = $template;
                return $methodName;
            }
        }
    }
    public function loadTriggerTemplate($trigger, $contextId)
    {
        $triggerType = $trigger['type'];
        $triggerTemplate = $this->triggersProvider->getTrigger($triggerType);
        if ($triggerTemplate) {
            $template = $triggerTemplate->loadTemplate($contextId, $trigger);
            if ($template) {
                $methodName = $triggerType . 'Trigger';
                $this->templateFunctions[$methodName] = $template;
                return $methodName;
            }
        }
    }
    public function loadVariableTemplate($variable, $contextId)
    {
        $variableType = $variable['type'];
        $variableTemplate = $this->variablesProvider->getVariable($variableType);
        if ($variableTemplate) {
            $template = $variableTemplate->loadTemplate($contextId, $variable);
            if ($template) {
                $methodName = $variableType . 'Variable';
                if ($variableTemplate->isCustomTemplate()) {
                    $methodName .= substr(md5(json_encode($variable['parameters'])), 0, 8);
                }
                $this->templateFunctions[$methodName] = $template;
                return $methodName;
            }
        }
    }
    public function updateVariableTemplate($methodName, $template)
    {
        $this->templateFunctions[$methodName] = $template;
    }
    public function getVariableTemplate($methodName)
    {
        if ($this->templateFunctions[$methodName]) {
            return $this->templateFunctions[$methodName];
        }
    }
}

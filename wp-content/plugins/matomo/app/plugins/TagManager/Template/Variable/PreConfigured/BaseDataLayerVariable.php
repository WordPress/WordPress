<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Variable\PreConfigured;

use Piwik\Plugins\TagManager\Context\WebContext;
abstract class BaseDataLayerVariable extends \Piwik\Plugins\TagManager\Template\Variable\PreConfigured\BasePreConfiguredVariable
{
    protected abstract function getDataLayerVariableName();
    public function loadTemplate($context, $entity)
    {
        switch ($context) {
            case WebContext::ID:
                return $this->makeDataLayerTemplateMethod($this->getDataLayerVariableName());
        }
    }
    public function getDataLayerVariableJs()
    {
        $dataLayerVariableName = $this->getDataLayerVariableName();
        if ($dataLayerVariableName) {
            return "TagManager.dataLayer.get('{$dataLayerVariableName}')";
        }
    }
    protected function makeDataLayerTemplateMethod($dataLayerKey)
    {
        $js = 'return parameters.container.dataLayer.get("' . $dataLayerKey . '")';
        return '(function () { return function (parameters, TagManager) { this.get = function () { ' . $js . '   }; } })();';
    }
}

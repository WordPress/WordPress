<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Variable;

use Piwik\Piwik;
use Piwik\Settings\FieldConfig;
use Piwik\Validators\NotEmpty;
class CustomRequestProcessingVariable extends \Piwik\Plugins\TagManager\Template\Variable\BaseVariable
{
    public const ID = 'CustomRequestProcessing';
    public function getId()
    {
        return self::ID;
    }
    public function getCategory()
    {
        return self::CATEGORY_OTHERS;
    }
    public function isCustomTemplate()
    {
        return \true;
    }
    public function getParameters()
    {
        return array($this->makeSetting('jsFunction', "function(request){ return request; }", FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_CustomRequestProcessingVariableJsFunctionTitle');
            $field->description = Piwik::translate('TagManager_CustomRequestProcessingVariableJsFunctionDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXTAREA;
            $field->validators[] = new NotEmpty();
            $field->validate = function ($value) {
                $value = trim($value);
                if (strpos($value, 'function(request){') !== 0) {
                    throw new \Exception('The value needs to start with "function(request){"');
                }
            };
            $field->transform = function ($value) {
                return trim($value);
            };
        }));
    }
    public function loadTemplate($context, $entity)
    {
        if (!empty($entity['parameters']['jsFunction'])) {
            $function = rtrim(trim($entity['parameters']['jsFunction']), ';');
            return '(function () { return function (parameters, TagManager) { this.get = function(){ return ' . $function . '; } ; } })();';
        }
    }
    public function hasAdvancedSettings()
    {
        return \false;
    }
}

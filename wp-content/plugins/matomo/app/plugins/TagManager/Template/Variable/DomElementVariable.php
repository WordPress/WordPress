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
class DomElementVariable extends \Piwik\Plugins\TagManager\Template\Variable\BaseVariable
{
    public function getCategory()
    {
        return self::CATEGORY_PAGE_VARIABLES;
    }
    public function getParameters()
    {
        $selectionMethod = $this->makeSetting('selectionMethod', 'elementId', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_ElementVisibilityTriggerSelectionMethodTitle');
            $field->description = Piwik::translate('TagManager_DomElementVariableSelectionMethodDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->validators[] = new NotEmpty();
            $field->availableValues = array('cssSelector' => 'CSS Selector', 'elementId' => 'Element ID');
        });
        return array($selectionMethod, $this->makeSetting('cssSelector', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($selectionMethod) {
            $field->title = Piwik::translate('TagManager_ElementVisibilityTriggerCssSelectorTitle');
            $field->description = Piwik::translate('TagManager_DomElementVariableCssSelectorDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_ElementVisibilityTriggerCssSelectorPlaceholder')];
            $field->condition = 'selectionMethod == "cssSelector"';
            $field->validate = function ($value) use($selectionMethod, $field) {
                if ($selectionMethod->getValue() === 'cssSelector' && empty($value)) {
                    throw new \Exception('Please specify a value for ' . $field->title);
                }
            };
        }), $this->makeSetting('elementId', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($selectionMethod) {
            $field->title = Piwik::translate('TagManager_ElementVisibilityTriggerElementIDTitle');
            $field->description = Piwik::translate('TagManager_ElementVisibilityTriggerElementIDDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_ElementVisibilityTriggerElementIdPlaceholder')];
            $field->condition = 'selectionMethod == "elementId"';
            $field->validate = function ($value) use($selectionMethod, $field) {
                if ($selectionMethod->getValue() === 'elementId' && empty($value)) {
                    throw new \Exception('Please specify a value for ' . $field->title);
                }
            };
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('attributeName', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_DomElementVariableAttributeNameTitle');
            $field->inlineHelp = Piwik::translate('TagManager_DomElementVariableAttributeNameInlineHelp');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_DomElementVariableAttributeNamePlaceholder')];
            $field->transform = function ($value) {
                return trim($value);
            };
        }));
    }
}

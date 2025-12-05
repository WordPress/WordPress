<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Trigger;

use Piwik\Piwik;
use Piwik\Settings\FieldConfig;
use Piwik\Validators\NotEmpty;
use Piwik\Validators\NumberRange;
class ElementVisibilityTrigger extends \Piwik\Plugins\TagManager\Template\Trigger\BaseTrigger
{
    public function getCategory()
    {
        return self::CATEGORY_USER_ENGAGEMENT;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/show.svg';
    }
    public function getParameters()
    {
        $selectionMethod = $this->makeSetting('selectionMethod', 'elementId', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_ElementVisibilityTriggerSelectionMethodTitle');
            $field->description = Piwik::translate('TagManager_ElementVisibilityTriggerSelectionMethodDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->validators[] = new NotEmpty();
            $field->availableValues = array('cssSelector' => 'CSS Selector', 'elementId' => 'Element ID');
        });
        return array($selectionMethod, $this->makeSetting('cssSelector', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($selectionMethod) {
            $field->title = Piwik::translate('TagManager_ElementVisibilityTriggerCssSelectorTitle');
            $field->description = Piwik::translate('TagManager_ElementVisibilityTriggerCssSelectorDescription');
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
        }), $this->makeSetting('fireTriggerWhen', 'oncePage', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_ElementVisibilityTriggerFireTriggerWhenTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->validators[] = new NotEmpty();
            $field->availableValues = array('oncePage' => 'Once per page', 'onceElement' => 'Once per element', 'every' => 'Every time an element appears on screen');
        }), $this->makeSetting('minPercentVisible', 50, FieldConfig::TYPE_INT, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_ElementVisibilityTriggerMinPercentVisibleTitle');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_ElementVisibilityTriggerMinPercentVisiblePlaceholder')];
            $field->validators[] = new NumberRange($min = 1, $max = 100);
        }), $this->makeSetting('observeDomChanges', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_SettingElementVisibilityObserveDomChangesTitle');
            $field->inlineHelp = Piwik::translate('TagManager_SettingElementVisibilityObserveDomChangesDescription', array('<br><strong>', '</strong>'));
            $field->uiControl = FieldConfig::UI_CONTROL_CHECKBOX;
        }));
    }
}

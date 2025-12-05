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
class TimerTrigger extends \Piwik\Plugins\TagManager\Template\Trigger\BaseTrigger
{
    public function getCategory()
    {
        return self::CATEGORY_OTHERS;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/timer.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('triggerInterval', 3000, FieldConfig::TYPE_INT, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_TimerTriggerTriggerIntervalTitle');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_TimerTriggerTriggerIntervalPlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->validators[] = new NumberRange($min = 50);
        }), $this->makeSetting('eventName', 'mtm.Timer', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('Events_EventName');
            $field->description = Piwik::translate('TagManager_TimerTriggerEventNameDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
        }), $this->makeSetting('triggerLimit', 0, FieldConfig::TYPE_INT, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_TimerTriggerTriggerLimitTitle');
            $field->description = Piwik::translate('TagManager_TimerTriggerTriggerLimitDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_PlaceholderZero')];
            $field->validators[] = new NumberRange($min = 0, $max = 900000);
        }));
    }
}

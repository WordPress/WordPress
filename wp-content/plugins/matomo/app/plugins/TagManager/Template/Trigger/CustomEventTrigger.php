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
use Piwik\Validators\CharacterLength;
use Piwik\Validators\NotEmpty;
class CustomEventTrigger extends \Piwik\Plugins\TagManager\Template\Trigger\BaseTrigger
{
    public const ID = 'CustomEvent';
    public function getId()
    {
        return self::ID;
    }
    public function getCategory()
    {
        return self::CATEGORY_OTHERS;
    }
    public function getParameters()
    {
        return array($this->makeSetting('eventName', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('Events_EventName');
            $field->description = Piwik::translate('TagManager_CustomEventTriggerEventNameDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->validators[] = new NotEmpty();
            $field->validators[] = new CharacterLength($min = 1, $max = 300);
        }));
    }
}

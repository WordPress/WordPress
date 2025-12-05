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
class TimeSinceLoadVariable extends \Piwik\Plugins\TagManager\Template\Variable\BaseVariable
{
    public function getCategory()
    {
        return self::CATEGORY_DATE;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/timer.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('unit', 'ms', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_TimeSinceLoadVariableUnitTitle');
            $field->description = Piwik::translate('TagManager_TimeSinceLoadVariableUnitDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->validators[] = new NotEmpty();
            $field->availableValues = array('ms' => 'Milliseconds', 's' => 'Seconds', 'm' => 'Minutes');
        }));
    }
}

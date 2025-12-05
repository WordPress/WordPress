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
class ScrollReachTrigger extends \Piwik\Plugins\TagManager\Template\Trigger\BaseTrigger
{
    public function getCategory()
    {
        return self::CATEGORY_USER_ENGAGEMENT;
    }
    public function getParameters()
    {
        $scrollType = $this->makeSetting('scrollType', 'verticalpercentage', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_ScrollReachTriggerScrollTypeTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->validators[] = new NotEmpty();
            $field->availableValues = array('verticalpercentage' => 'Vertical - Percentage', 'verticalpixel' => 'Vertical - Pixels', 'horizontalpercentage' => 'Horizontal - Percentage', 'horizontalpixel' => 'Horizontal - Pixels');
        });
        return array($scrollType, $this->makeSetting('pixels', 1, FieldConfig::TYPE_INT, function (FieldConfig $field) use($scrollType) {
            $field->title = Piwik::translate('TagManager_ScrollReachTriggerPixelsTitle');
            $field->description = Piwik::translate('TagManager_ScrollReachTriggerPixelsDescription');
            $field->uiControlAttributes = array('placeholder' => 'eg. 50, 1020, 3059');
            $field->condition = 'scrollType == "verticalpixel" || scrollType == "horizontalpixel"';
            if ($scrollType->getValue() === 'verticalpixel' || $scrollType->getValue() === 'horizontalpixel') {
                $field->validators[] = new NumberRange($min = 1, $max = 900000);
            }
        }), $this->makeSetting('percentage', 50, FieldConfig::TYPE_INT, function (FieldConfig $field) use($scrollType) {
            $field->title = Piwik::translate('TagManager_ScrollReachTriggerPercentageTitle');
            $field->description = Piwik::translate('TagManager_ScrollReachTriggerPercentageDescription');
            $field->uiControlAttributes = array('placeholder' => 'eg. 20, 50, 75, 90');
            $field->condition = 'scrollType == "verticalpercentage" || scrollType == "horizontalpercentage"';
            if ($scrollType->getValue() === 'verticalpercentage' || $scrollType->getValue() === 'horizontalpercentage') {
                $field->validators[] = new NumberRange($min = 1, $max = 100);
            }
        }));
    }
}

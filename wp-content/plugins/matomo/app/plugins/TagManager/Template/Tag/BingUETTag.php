<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Tag;

use Piwik\Piwik;
use Piwik\Settings\FieldConfig;
use Piwik\Validators\NotEmpty;
use Piwik\Validators\NumberRange;
class BingUETTag extends \Piwik\Plugins\TagManager\Template\Tag\BaseTag
{
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/bing.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('bingAdID', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_BingUETTagIdTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = Piwik::translate('TagManager_BingUETTagIdDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_BingUETTagIdPlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->validate = function ($value) {
                $value = trim($value);
                $numberRange = new NumberRange();
                $numberRange->validate($value);
            };
            $field->transform = function ($value) {
                return trim($value);
            };
        }));
    }
    public function getCategory()
    {
        return self::CATEGORY_ADS;
    }
}

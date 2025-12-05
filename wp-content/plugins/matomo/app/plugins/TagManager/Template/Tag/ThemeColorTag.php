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
use Piwik\Plugins\TagManager\Template\Tag\BaseTag;
use Piwik\Settings\Setting;
use Piwik\Validators\NotEmpty;
class ThemeColorTag extends BaseTag
{
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/chrome.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('themeColor', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_ThemeColorTagName');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = Piwik::translate('TagManager_ThemeColorTagThemeColorDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_ThemeColorPlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->validate = function ($value, Setting $setting) {
                $value = trim($value);
                if (!preg_match("/^#(?:[0-9a-fA-F]{3}){1,2}\$/", $value)) {
                    throw new \Exception('Please enter a valid hexadecimal color');
                }
            };
            $field->transform = function ($value) {
                return trim($value);
            };
        }));
    }
    public function getCategory()
    {
        return self::CATEGORY_OTHERS;
    }
}

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
use Piwik\Validators\NotEmpty;
class RaygunTag extends BaseTag
{
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/raygun.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('raygunApiKey', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_RaygunTagApiKeyTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = Piwik::translate('TagManager_RaygunTagApiKeyDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_RaygunTagApiKeyPlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('raygunEnablePulse', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_RaygunTagEnablePulseTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_CHECKBOX;
            $field->description = Piwik::translate('TagManager_RaygunTagEnablePulseDescription');
        }));
    }
    public function getCategory()
    {
        return self::CATEGORY_DEVELOPERS;
    }
}

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
class HoneybadgerTag extends BaseTag
{
    public function getName()
    {
        return "Honeybadger";
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/honeybadger.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('honeybadgerApiKey', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_HoneybadgerTagApiKeyTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = Piwik::translate('TagManager_HoneybadgerTagApiKeyDescription');
            $field->validators[] = new NotEmpty();
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('honeybadgerEnvironment', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_Environment');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_HoneybadgerTagEnvironmentDescription');
        }), $this->makeSetting('honeybadgerRevision', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_HoneybadgerTagRevisionTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_HoneybadgerTagRevisionDescription');
        }));
    }
    public function getCategory()
    {
        return self::CATEGORY_DEVELOPERS;
    }
}

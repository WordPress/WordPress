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
class LinkedinInsightTag extends \Piwik\Plugins\TagManager\Template\Tag\BaseTag
{
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/linkedin.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('partnerId', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_LinkedinInsightTagPartnerIdTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = Piwik::translate('TagManager_LinkedinInsightTagPartnerIdDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_LinkedinInsightTagPartnerIdPlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->validate = function ($value) {
                $value = trim($value);
                $numberRange = new NumberRange();
                $numberRange->validate($value);
            };
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('conversionId', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_LinkedinInsightTagConversionIdTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->customFieldComponent = self::FIELD_TEXTAREA_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_LinkedinInsightTagConversionIdDescription');
            $field->inlineHelp = Piwik::translate('TagManager_LinkedinInsightTagConversionIdHelpText', ['<a href="https://www.linkedin.com/help/lms/answer/a1437736" target="_blank" rel="noreferrer noopener">', '</a>']);
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_BingUETTagIdPlaceholder')];
            $field->transform = function ($value) {
                return trim($value);
            };
        }));
    }
    public function getCategory()
    {
        return self::CATEGORY_SOCIAL;
    }
}

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
use Piwik\Url;
use Piwik\Validators\NotEmpty;
class CustomHtmlTag extends \Piwik\Plugins\TagManager\Template\Tag\BaseTag
{
    public const ID = 'CustomHtml';
    public function getId()
    {
        return self::ID;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/code.svg';
    }
    public function isCustomTemplate()
    {
        return \true;
    }
    public function getParameters()
    {
        return array($this->makeSetting('customHtml', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_CustomHtmlTagName');
            $field->customFieldComponent = self::FIELD_TEXTAREA_VARIABLE_COMPONENT;
            $field->uiControl = FieldConfig::UI_CONTROL_TEXTAREA;
            $field->description = Piwik::translate('TagManager_CustomHtmlTagDescriptionText');
            $field->inlineHelp = Piwik::translate('TagManager_CustomHtmlTagHelpText', ['<a rel="noreferrer noopener" target="_blank" href="' . Url::addCampaignParametersToMatomoLink('https://matomo.org/faq/tag-manager/faq_26815/', null, null, 'App.TagManager.getParameters') . '">', '</a>']);
            $field->validators[] = new NotEmpty();
            $field->uiControlAttributes = ['spellcheck' => 'false'];
        }), $this->makeSetting('htmlPosition', 'bodyEnd', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_CustomHtmlHtmlPositionTitle');
            $field->availableValues = array('headStart' => 'Head Start', 'headEnd' => 'Head End', 'bodyStart' => 'Body Start', 'bodyEnd' => 'Body End');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->description = Piwik::translate('TagManager_CustomHtmlHtmlPositionDescription');
        }));
    }
    public function getCategory()
    {
        return self::CATEGORY_CUSTOM;
    }
}

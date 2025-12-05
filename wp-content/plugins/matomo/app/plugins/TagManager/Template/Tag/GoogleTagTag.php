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
class GoogleTagTag extends BaseTag
{
    public function getName()
    {
        // By default, the name will be automatically fetched from the TagManager_CustomHtmlTagName translation key.
        // you can either adjust/create/remove this translation key, or return a different value here directly.
        return parent::getName();
    }
    public function getDescription()
    {
        // By default, the description will be automatically fetched from the TagManager_CustomHtmlTagDescription
        // translation key. you can either adjust/create/remove this translation key, or return a different value
        // here directly.
        return parent::getDescription();
    }
    public function getHelp()
    {
        // By default, the help will be automatically fetched from the TagManager_CustomHtmlTagHelp translation key.
        // you can either adjust/create/remove this translation key, or return a different value here directly.
        return parent::getHelp();
    }
    public function getCategory()
    {
        return self::CATEGORY_ANALYTICS;
    }
    public function getIcon()
    {
        // You may optionally specify a path to an image icon URL, for example:
        //
        // return 'plugins/TagManager/images/MyIcon.png';
        //
        // to not return default icon call:
        // return parent::getIcon();
        //
        // The image should have ideally a resolution of about 64x64 pixels.
        return 'plugins/TagManager/images/icons/google-tag.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('googleTagId', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_GoogleTagTagIdTitle');
            $field->inlineHelp = Piwik::translate('TagManager_GoogleTagTagIdDescription', array('<a href="https://support.google.com/analytics/answer/9267735" target="_blank" rel="noreferrer noopener">', '</a>'));
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->validators[] = new NotEmpty();
        }));
    }
}

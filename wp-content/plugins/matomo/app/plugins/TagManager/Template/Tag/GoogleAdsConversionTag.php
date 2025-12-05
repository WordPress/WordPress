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
class GoogleAdsConversionTag extends BaseTag
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
        return self::CATEGORY_ADS;
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
        return 'plugins/TagManager/images/icons/google-ads.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('googleAdsConversionId', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_GoogleAdsConversionTagIdTitle');
            $field->inlineHelp = Piwik::translate('TagManager_GoogleAdsConversionTagIdDescription', array('<a href="https://support.google.com/tagmanager/answer/6105160" target="_blank" rel="noreferrer noopener">', '<a>'));
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->validators[] = new NotEmpty();
        }), $this->makeSetting('googleAdsConversionLabel', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_GoogleAdsConversionTagLabelTitle');
            $field->inlineHelp = Piwik::translate('TagManager_GoogleAdsConversionTagLabelDescription', array('<a href="https://support.google.com/tagmanager/answer/6105160" target="_blank" rel="noreferrer noopener">', '<a>'));
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->validators[] = new NotEmpty();
        }), $this->makeSetting('googleAdsConversionValue', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_GoogleAdsConversionTagValueTitle');
            $field->description = Piwik::translate('TagManager_GoogleAdsConversionTagValueDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            //$field->validators[] = new NotEmpty();
        }), $this->makeSetting('googleAdsConversionTransactionId', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_GoogleAdsConversionTagTransactionIdTitle');
            $field->inlineHelp = Piwik::translate('TagManager_GoogleAdsConversionTagTransactionIdDescription', array('<a href="https://support.google.com/google-ads/answer/6386790" target="_blank" rel="noreferrer noopener">', '<a>'));
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            //$field->validators[] = new NotEmpty();
        }), $this->makeSetting('googleAdsConversionCurrency', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_GoogleAdsConversionTagCurrencyTitle');
            $field->inlineHelp = Piwik::translate('TagManager_GoogleAdsConversionTagCurrencyDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            //$field->validators[] = new NotEmpty();
        }));
    }
}

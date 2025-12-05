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
use Piwik\Validators\CharacterLength;
use Piwik\Validators\NotEmpty;
class EmarsysTag extends \Piwik\Plugins\TagManager\Template\Tag\BaseTag
{
    public const ID = 'Emarsys';
    public function getId()
    {
        return self::ID;
    }
    public function getCategory()
    {
        return self::CATEGORY_ANALYTICS;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/emarsys.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('merchantId', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EmarsysTagMerchantIdTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = Piwik::translate('TagManager_EmarsysTagMerchantIdDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_EmarsysTagMerchantIdPlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->validate = function ($value) {
                $value = trim($value);
                $characterLength = new CharacterLength(1, 500);
                $characterLength->validate($value);
            };
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('commandCategory', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EmarsysTagCommandCategoryTitleOptional');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = Piwik::translate('TagManager_EmarsysTagCommandCategoryDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_EmarsysTagCommandCategoryPlaceholder')];
            $field->validators[] = new CharacterLength(0, 500);
        }), $this->makeSetting('commandView', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EmarsysTagCommandViewTitleOptional');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = Piwik::translate('TagManager_EmarsysTagCommandViewDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_EmarsysTagCommandViewPlaceholder')];
            $field->validators[] = new CharacterLength(0, 500);
        }), $this->makeSetting('commandTag', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EmarsysTagCommandTagTitleOptional');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = Piwik::translate('TagManager_EmarsysTagCommandTagDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_EmarsysTagCommandTagPlaceholder')];
            $field->validators[] = new CharacterLength(0, 500);
        }), $this->makeSetting('commandGo', '', FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EmarsysTagCommandGoTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_CHECKBOX;
            $field->description = Piwik::translate('TagManager_EmarsysTagCommandGoDescription');
        }));
    }
}

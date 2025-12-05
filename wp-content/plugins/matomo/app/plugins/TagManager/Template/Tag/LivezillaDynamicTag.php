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
use Piwik\Validators\CharacterLength;
use Piwik\Validators\NotEmpty;
use Piwik\Validators\UrlLike;
class LivezillaDynamicTag extends BaseTag
{
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/livezilla_icon.png';
    }
    public function getCategory()
    {
        return self::CATEGORY_SOCIAL;
    }
    public function getParameters()
    {
        return array($this->makeSetting('LivezillaDynamicID', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_LivezillaDynamicTagIdTitle');
            $field->description = Piwik::translate('TagManager_LivezillaDynamicTagIdDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_LivezillaDynamicTagIdPlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->transform = function ($value) {
                return trim($value);
            };
            $field->validators[] = new CharacterLength(32);
        }), $this->makeSetting('LivezillaDynamicDomain', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_LivezillaDynamicTagDomainTitle');
            $field->description = Piwik::translate('TagManager_LivezillaDynamicTagDomainDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_LivezillaDynamicTagDomainPlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->validate = function ($value) {
                $value = trim($value);
                $urlLike = new UrlLike();
                $urlLike->validate($value);
                $characterLength = new CharacterLength(11, 60);
                $characterLength->validate($value);
            };
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('LivezillaDynamicDefer', \true, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_LivezillaDynamicTagDynamicDeferTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_CHECKBOX;
            $field->description = Piwik::translate('TagManager_LivezillaDynamicTagDynamicDeferDescription');
        }));
    }
}

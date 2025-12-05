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
class AddThisTag extends BaseTag
{
    public function getHelp()
    {
        return '';
    }
    public function getCategory()
    {
        return self::CATEGORY_SOCIAL;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/addthis.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('AddThisPubId', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_AddThisTagPubIdTitle');
            $field->description = Piwik::translate('TagManager_AddThisTagPubIdDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->validators[] = new NotEmpty();
        }), $this->makeSetting('AddThisParentSelector', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_AddThisParentSelectorTitle');
            $field->description = Piwik::translate('TagManager_AddThisParentSelectorDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
        }));
    }
}

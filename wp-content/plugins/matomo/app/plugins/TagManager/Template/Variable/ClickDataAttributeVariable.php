<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Variable;

use Piwik\Piwik;
use Piwik\Settings\FieldConfig;
use Piwik\Validators\NotEmpty;
use Piwik\Plugins\TagManager\Template\Variable\BaseVariable;
class ClickDataAttributeVariable extends BaseVariable
{
    public function getCategory()
    {
        return self::CATEGORY_CLICKS;
    }
    public function getName()
    {
        // By default, the name will be automatically fetched from the TagManager_DataValueVariableName translation key.
        // you can either adjust/create/remove this translation key, or return a different value here directly.
        return parent::getName();
    }
    public function getDescription()
    {
        // By default, the description will be automatically fetched from the TagManager_DataValueVariableDescription
        // translation key. you can either adjust/create/remove this translation key, or return a different value
        // here directly.
        return parent::getDescription();
    }
    public function getHelp()
    {
        // By default, the help will be automatically fetched from the TagManager_DataValueVariableHelp translation key.
        // you can either adjust/create/remove this translation key, or return a different value here directly.
        return parent::getHelp();
    }
    public function getIcon()
    {
        // You may optionally specify a path to an image icon URL, for example:
        //
        // return 'plugins/TagManager/images/MyIcon.png';
        //
        // The image should have ideally a resolution of about 64x64 pixels.
        return 'plugins/TagManager/images/icons/click-data-attribute-variable.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('dataAttribute', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_ClickDataAttributeDataAttributeTitle');
            $field->description = Piwik::translate('TagManager_ClickDataAttributeDataAttributeDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_ClickDataAttributeDataAttributePlaceholder')];
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->validators[] = new NotEmpty();
        }));
    }
}

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
use Piwik\Validators\CharacterLength;
use Piwik\Validators\NotEmpty;
class DataLayerVariable extends \Piwik\Plugins\TagManager\Template\Variable\BaseVariable
{
    public const ID = 'DataLayer';
    public function getId()
    {
        return self::ID;
    }
    public function getCategory()
    {
        return self::CATEGORY_PAGE_VARIABLES;
    }
    public function getParameters()
    {
        return array($this->makeSetting('dataLayerName', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_DataLayerVariableNameTitle');
            $field->description = Piwik::translate('TagManager_DataLayerVariableNameDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_DataLayerVariableNamePlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->validators[] = new CharacterLength(1, 300);
            $field->transform = function ($value) {
                return trim($value);
            };
        }));
    }
}

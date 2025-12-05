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
class DriftTag extends BaseTag
{
    public function getCategory()
    {
        return self::CATEGORY_SOCIAL;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/drift.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('driftId', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_DriftTagDriftIdTitle');
            $field->description = Piwik::translate('TagManager_DriftTagDriftIdDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_DriftTagDriftIdPlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->validate = function ($value) {
                $value = trim($value);
                $characterLength = new CharacterLength(12, 12);
                $characterLength->validate($value);
            };
            $field->transform = function ($value) {
                return trim($value);
            };
        }));
    }
}

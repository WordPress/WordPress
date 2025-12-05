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
class TawkToTag extends BaseTag
{
    public function getName()
    {
        return "Tawk.to";
    }
    public function getCategory()
    {
        return self::CATEGORY_SOCIAL;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/tawk_to.png';
    }
    public function getParameters()
    {
        return array($this->makeSetting('tawkToId', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_TawkToTagIdTitle');
            $field->description = Piwik::translate('TagManager_TawkToTagIdDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_TawkToTagIdPlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->validate = function ($value) {
                $value = trim($value);
                $characterLength = new CharacterLength(16, 30);
                // we limit to 30 so users don't accidentally enter a 32 digit API key
                $characterLength->validate($value);
            };
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('tawkToWidgetId', 'default', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_TawkToTagWidgetIdTitle');
            $field->description = Piwik::translate('TagManager_TawkToTagWidgetIdDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_TawkToTagWidgetIdPlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->validate = function ($value) {
                $value = trim($value);
                $characterLength = new CharacterLength(7, 20);
                // we limit to 20 so users don't accidentally enter a 32 digit API key
                $characterLength->validate($value);
            };
            $field->transform = function ($value) {
                return trim($value);
            };
        }));
    }
}

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
class CookieVariable extends \Piwik\Plugins\TagManager\Template\Variable\BaseVariable
{
    public function getCategory()
    {
        return self::CATEGORY_PAGE_VARIABLES;
    }
    public function getParameters()
    {
        return array($this->makeSetting('cookieName', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_CookieVariableCookieNameTitle');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_CookieVariableCookieNamePlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->validators[] = new CharacterLength(1, 500);
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('urlDecode', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_CookieVariableUrlDecodeTitle');
            $field->inlineHelp = Piwik::translate('TagManager_CookieVariableUrlDecodeDescription');
        }));
    }
}

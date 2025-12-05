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
use Piwik\Settings\Setting;
use Piwik\Validators\CharacterLength;
use Piwik\Validators\NotEmpty;
class ZendeskChatTag extends BaseTag
{
    public function getCategory()
    {
        return self::CATEGORY_SOCIAL;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/zendesk_chat.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('zendeskChatId', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_ZendeskChatTagChatIdTitle');
            $field->description = Piwik::translate('TagManager_ZendeskChatTagChatIdDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_ZendeskChatTagChatIdPlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->validate = function ($value, Setting $setting) {
                $value = trim($value);
                if (substr($value, 0, 1) === "?") {
                    throw new \Exception("The Chat ID shouldn't include the staring '?'");
                }
                $characterLength = new CharacterLength(20, 40);
                $characterLength->validate($value);
            };
            $field->transform = function ($value) {
                return trim($value);
            };
        }));
    }
}

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
class BugsnagTag extends BaseTag
{
    public const ID = 'Bugsnag';
    public function getId()
    {
        return self::ID;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/bugsnag.png';
    }
    public function getParameters()
    {
        return array($this->makeSetting('apiKey', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_BugsnagTagApiKeyTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = Piwik::translate('TagManager_BugsnagTagApiKeyDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_BingUETTagIdPlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('collectUserIp', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_BugsnagTagCollectUserIpTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_CHECKBOX;
            $field->description = Piwik::translate('TagManager_BugsnagTagCollectUserIpDescription');
        }));
    }
    public function getCategory()
    {
        return self::CATEGORY_DEVELOPERS;
    }
}

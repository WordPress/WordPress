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
use Piwik\Validators\NotEmpty;
class PingdomRUMTag extends \Piwik\Plugins\TagManager\Template\Tag\BaseTag
{
    public function getName()
    {
        return "Pingdom Real User Monitoring (RUM)";
    }
    public function getCategory()
    {
        return self::CATEGORY_ANALYTICS;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/pingdom.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('pingdomROMId', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_PingdomRUMTagIdTitle');
            $field->description = Piwik::translate('TagManager_PingdomRUMTagIdDescription');
            $field->uiControlAttributes = ['placeholder' => Piwik::translate('TagManager_RaygunTagApiKeyPlaceholder')];
            $field->validators[] = new NotEmpty();
            $field->transform = function ($value) {
                return trim($value);
            };
        }));
    }
}

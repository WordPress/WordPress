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
class GoogleAnalyticsUniversalTag extends \Piwik\Plugins\TagManager\Template\Tag\BaseTag
{
    public function getCategory()
    {
        return self::CATEGORY_ANALYTICS;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/google-analytics-icon.svg';
    }
    public function getParameters()
    {
        return array($this->makeSetting('propertyId', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_GoogleAnalyticsUniversalTagPropertyIdTitle');
            $field->description = Piwik::translate('TagManager_GoogleAnalyticsUniversalTagPropertyIdDescription');
            $field->validators[] = new NotEmpty();
            $field->validate = function ($value) {
                $value = trim($value);
                if (!preg_match('/^ua-\\d{4,9}-\\d{1,4}$/i', strval($value))) {
                    throw new \Exception('The Property ID seems to not have a valid format');
                }
            };
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('trackingType', 'pageview', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_TrackingType');
            $field->description = Piwik::translate('TagManager_GoogleAnalyticsUniversalTagTrackingTypeDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->validators[] = new NotEmpty();
            $field->availableValues = array('pageview' => 'Pageview');
        }));
    }
}

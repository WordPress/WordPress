<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Tag;

use Piwik\Container\StaticContainer;
use Piwik\Piwik;
use Piwik\Plugins\TagManager\Validators\Numeric;
use Piwik\Settings\FieldConfig;
use Piwik\Validators\CharacterLength;
use Piwik\Validators\NotEmpty;
class MatomoTag extends \Piwik\Plugins\TagManager\Template\Tag\BaseTag
{
    public const ID = 'Matomo';
    public const PARAM_MATOMO_CONFIG = 'matomoConfig';
    public const REPLACE_TRACKER_KEY = "var replaceMeWithTracker='';";
    public function getId()
    {
        return self::ID;
    }
    public function getCategory()
    {
        return self::CATEGORY_ANALYTICS;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/MatomoIcon.png';
    }
    public function getParameters()
    {
        $trackingType = $this->makeSetting('trackingType', 'pageview', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_TrackingType');
            $field->description = Piwik::translate('TagManager_TrackingTypeHelp');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->validators[] = new NotEmpty();
            $field->availableValues = array('pageview' => Piwik::translate('TagManager_PageViewTriggerName'), 'event' => Piwik::translate('Events_Event'), 'goal' => Piwik::translate('General_Goal'), 'initialise' => Piwik::translate('TagManager_InitializeTrackerOnly'));
        });
        $isEcommerceView = $this->makeSetting('isEcommerceView', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoTagEcommerceViewIsEcommerceView');
            $inlineHelpLine1 = StaticContainer::get('TagManager.MatomoTagEcommerceViewIsEcommerceViewHelpOnPremise');
            $field->inlineHelp = $inlineHelpLine1 ? Piwik::translate($inlineHelpLine1) . '<br /><br />' : '';
            $field->inlineHelp .= Piwik::translate('TagManager_MatomoTagEcommerceViewIsEcommerceViewHelpV2');
            $field->condition = 'trackingType == "pageview"';
        });
        return array($this->makeSetting(self::PARAM_MATOMO_CONFIG, '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationVariableName');
            $field->description = Piwik::translate('TagManager_MatomoConfigurationFieldHelp');
            $field->customFieldComponent = self::FIELD_VARIABLE_TYPE_COMPONENT;
            $field->uiControlAttributes = array('variableType' => 'MatomoConfiguration');
            $field->validators[] = new NotEmpty();
        }), $trackingType, $this->makeSetting('idGoal', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_GoalId');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_GoalIdHelp');
            $field->condition = 'trackingType == "goal"';
            if ($trackingType->getValue() === 'goal') {
                $field->validators[] = new NotEmpty();
                $field->validators[] = new CharacterLength(1, 500);
            }
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('goalCustomRevenue', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('Goals_GoalRevenue') . ' ' . Piwik::translate('Goals_Optional');
            $field->description = Piwik::translate('TagManager_GoalRevenueHelp');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "goal"';
            if ($trackingType->getValue() === 'goal') {
                // The tracker.trackGoal JavaScript function indicates that it expects int|float for customRevenue.
                $field->validators[] = new Numeric(\true, \true);
            }
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('documentTitle', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_CustomTitle');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_CustomTitleHelp');
            $field->condition = 'trackingType == "pageview"';
            if ($trackingType->getValue() === 'pageview') {
                $field->validators[] = new CharacterLength(0, 500);
            }
        }), $this->makeSetting('customUrl', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_CustomUrl');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_CustomUrlHelp');
            $field->condition = 'trackingType == "pageview"';
            if ($trackingType->getValue() === 'pageview') {
                $field->validators[] = new CharacterLength(0, 500);
            }
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $isEcommerceView, $this->makeSetting('productSKU', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType, $isEcommerceView) {
            $field->title = Piwik::translate('TagManager_MatomoTagEcommerceViewProductSKU');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_MatomoTagEcommerceViewProductSKUHelp');
            $field->condition = 'trackingType == "pageview" && isEcommerceView';
            if ($trackingType->getValue() === 'pageview' && $isEcommerceView->getValue()) {
                $field->validators[] = new CharacterLength(0, 500);
            }
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('productName', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType, $isEcommerceView) {
            $field->title = Piwik::translate('TagManager_MatomoTagEcommerceViewProductName');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_MatomoTagEcommerceViewProductNameHelp');
            $field->condition = 'trackingType == "pageview" && isEcommerceView';
            if ($trackingType->getValue() === 'pageview' && $isEcommerceView->getValue()) {
                $field->validators[] = new CharacterLength(0, 500);
            }
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('categoryName', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType, $isEcommerceView) {
            $field->title = Piwik::translate('TagManager_MatomoTagEcommerceViewCategoryName');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_MatomoTagEcommerceViewCategoryNameHelp');
            $field->condition = 'trackingType == "pageview" && isEcommerceView';
            if ($trackingType->getValue() === 'pageview' && $isEcommerceView->getValue()) {
                $field->validators[] = new CharacterLength(0, 500);
            }
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('price', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType, $isEcommerceView) {
            $field->title = Piwik::translate('TagManager_MatomoTagEcommerceViewPrice');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_MatomoTagEcommerceViewPriceHelp');
            $field->condition = 'trackingType == "pageview" && isEcommerceView';
            if ($trackingType->getValue() === 'pageview' && $isEcommerceView->getValue()) {
                $field->validators[] = new Numeric(\true, \true);
            }
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('eventCategory', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('Events_EventCategory');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_EventCategoryHelp');
            $field->condition = 'trackingType == "event"';
            if ($trackingType->getValue() === 'event') {
                $field->validators[] = new NotEmpty();
                $field->validators[] = new CharacterLength(1, 500);
            }
        }), $this->makeSetting('eventAction', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('Events_EventAction');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_EventActionHelp');
            $field->condition = 'trackingType == "event"';
            if ($trackingType->getValue() === 'event') {
                $field->validators[] = new NotEmpty();
                $field->validators[] = new CharacterLength(1, 500);
            }
        }), $this->makeSetting('eventName', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('Events_EventName');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_EventNameHelp');
            $field->condition = 'trackingType == "event"';
            $field->validators[] = new CharacterLength(0, 500);
        }), $this->makeSetting('eventValue', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EventValue');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_EventValueDescription');
            $field->inlineHelp = '<br>' . Piwik::translate('TagManager_EventValueInlineHelp', array('<strong>', '</strong>'));
            $field->condition = 'trackingType == "event"';
            $field->validators[] = new CharacterLength(0, 500);
            $field->validators[] = new Numeric(\true, \true);
            $field->transform = function ($value) {
                if ($value === null || $value === \false || $value === '') {
                    // we make sure in those cases we do not case the value to float automatically by Setting class because
                    // the value is optional and we do not want to have "0" in this case
                    return null;
                }
                return $value;
            };
        }), $this->makeSetting('customDimensions', [], FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoConfigurationMatomoCustomDimensionsTitle');
            $field->description = Piwik::translate('TagManager_MatomoTagCustomDimensionsDescription');
            $field->validate = function ($value) {
                if (empty($value)) {
                    return;
                }
                if (!is_array($value)) {
                    throw new \Exception(Piwik::translate('TagManager_MatomoConfigurationMatomoCustomDimensionsException'));
                }
            };
            $field->transform = function ($value) {
                if (empty($value) || !is_array($value)) {
                    return [];
                }
                $withValues = [];
                foreach ($value as $dim) {
                    if (!empty($dim['index']) && !empty($dim['value'])) {
                        $withValues[] = $dim;
                    }
                }
                return $withValues;
            };
            $field->uiControl = FieldConfig::UI_CONTROL_MULTI_TUPLE;
            $field1 = new FieldConfig\MultiPair('Index', 'index', FieldConfig::UI_CONTROL_TEXT);
            $field1->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field2 = new FieldConfig\MultiPair('Value', 'value', FieldConfig::UI_CONTROL_TEXT);
            $field2->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->uiControlAttributes['field1'] = $field1->toArray();
            $field->uiControlAttributes['field2'] = $field2->toArray();
        }), $this->makeSetting('areCustomDimensionsSticky', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_MatomoTagCustomDimensionsSticky');
            $field->inlineHelp = Piwik::translate('TagManager_MatomoTagCustomDimensionsStickyHelpText1') . '<br /><br />';
            $field->inlineHelp .= Piwik::translate('TagManager_MatomoTagCustomDimensionsStickyHelpText2');
        }));
    }
    public function loadTemplate($context, $entity)
    {
        $template = parent::loadTemplate($context, $entity);
        // !isset() because when bundleTracker is not defined for some reason we enable it by default
        $bundleTrackerEnabled = !isset($entity['parameters']['matomoConfig']['parameters']['bundleTracker']) || !empty($entity['parameters']['matomoConfig']['parameters']['bundleTracker']);
        if ($template && $bundleTrackerEnabled) {
            $trackerUpdater = StaticContainer::get('Piwik\\Plugins\\CustomJsTracker\\TrackerUpdater');
            $tracker = $trackerUpdater->getUpdatedTrackerFileContent();
            if (!$tracker) {
                $tracker = @file_get_contents(PIWIK_DOCUMENT_ROOT . '/matomo.js');
            }
            if (!$tracker) {
                $tracker = @file_get_contents(PIWIK_DOCUMENT_ROOT . '/piwik.js');
            }
            return str_replace(self::REPLACE_TRACKER_KEY, $tracker, $template);
        }
        return $template;
    }
    public function getOrder()
    {
        return 1;
    }
}

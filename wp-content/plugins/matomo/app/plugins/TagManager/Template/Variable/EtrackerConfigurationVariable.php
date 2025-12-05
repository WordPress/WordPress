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
class EtrackerConfigurationVariable extends \Piwik\Plugins\TagManager\Template\Variable\BaseVariable
{
    public const ID = 'EtrackerConfiguration';
    public function getId()
    {
        return self::ID;
    }
    public function getDescription()
    {
        return Piwik::translate('TagManager_EtrackerMainVariableDescription');
    }
    public function getCategory()
    {
        return self::CATEGORY_OTHERS;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/etracker.svg';
    }
    public function hasAdvancedSettings()
    {
        return \false;
    }
    public function getParameters()
    {
        return array($this->makeSetting('etrackerID', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EtrackerConfigurationVariableIdTitle');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_EtrackerConfigurationVariableIdDescriptionNew');
            $field->validators[] = new NotEmpty();
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('etrackerBlockCookies', \true, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EtrackerConfigurationVariableBlockCookiesTitle');
        }), $this->makeSetting('etrackerDNT', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EtrackerConfigurationVariableDNTTitle');
        }), $this->makeSetting('et_pagename', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EtrackerConfigurationVariablePageNameTitle');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_EtrackerConfigurationVariablePageNameDescription');
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('et_areas', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EtrackerConfigurationVariableAreaTitle');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_EtrackerConfigurationVariablePageNameDescription');
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('et_target', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EtrackerConfigurationVariableTargetTitle');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_EtrackerConfigurationVariablePageNameDescription');
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('et_tval', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EtrackerConfigurationVariableTValTitle');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_EtrackerConfigurationVariablePageNameDescription');
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('et_tonr', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EtrackerConfigurationVariableTonrTitle');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_EtrackerConfigurationVariablePageNameDescription');
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('et_tsale', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EtrackerConfigurationVariableTSaleTitle');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_EtrackerConfigurationVariablePageNameDescription');
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('et_basket', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EtrackerConfigurationVariableBasketTitle');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_EtrackerConfigurationVariablePageNameDescription');
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('et_cust', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EtrackerConfigurationVariableCustTitle');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->description = Piwik::translate('TagManager_EtrackerConfigurationVariablePageNameDescription');
            $field->transform = function ($value) {
                return trim($value);
            };
        }), $this->makeSetting('customDimensions', array(), FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_EtrackerConfigurationVariableCustomDimensionsTitle');
            $field->description = Piwik::translate('TagManager_EtrackerConfigurationVariableCustomDimensionsDescription');
            $field->validate = function ($value) {
                if (empty($value)) {
                    return;
                }
                if (!is_array($value)) {
                    throw new \Exception('Value needs to be an array');
                }
            };
            $field->transform = function ($value) {
                if (empty($value) || !is_array($value)) {
                    return array();
                }
                $withValues = array();
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
        }));
    }
}

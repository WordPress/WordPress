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
use Piwik\Validators\CharacterLength;
use Piwik\Validators\NumberRange;
class EtrackerTag extends \Piwik\Plugins\TagManager\Template\Tag\BaseTag
{
    public const PARAM_ETRACKER_CONFIG = 'etrackerConfig';
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/etracker.svg';
    }
    public function getParameters()
    {
        $trackingType = $this->makeSetting('trackingType', 'pageview', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_TrackingType');
            $field->description = Piwik::translate('TagManager_EtrackerTagTrackingTypeDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->validators[] = new NotEmpty();
            $field->availableValues = array('pageview' => 'Pageview', 'wrapper' => 'Wrapper', 'event' => 'Event', 'transaction' => 'Transaction', 'addtocart' => 'eCommerce Event - Add to cart', 'form' => 'Form Tracking');
        });
        return array($trackingType, $this->makeSetting(self::PARAM_ETRACKER_CONFIG, '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagConfigTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagConfigDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_TYPE_COMPONENT;
            $field->uiControlAttributes = array('variableType' => 'EtrackerConfiguration');
            $field->condition = 'trackingType == "pageview" || trackingType =="wrapper"';
            if ($trackingType->getValue() === 'pageview' || $trackingType->getValue() === 'wrapper') {
                $field->validators[] = new NotEmpty();
            }
        }), $this->makeSetting('etrackerWrapperPagename', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagWrapperPageNameTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagWrapperPageNameDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "wrapper"';
            if ($trackingType->getValue() === 'wrapper') {
                $field->validators[] = new NotEmpty();
            }
        }), $this->makeSetting('etrackerWrapperArea', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagWrapperAreaTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagWrapperAreaDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "wrapper"';
        }), $this->makeSetting('etrackerWrapperTarget', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagWrapperTargetTitle');
            $field->description = '';
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "wrapper"';
        }), $this->makeSetting('etrackerWrapperTval', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagWrapperTvalTitle');
            $field->description = '';
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "wrapper"';
        }), $this->makeSetting('etrackerWrapperTonr', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagWrapperTonrTitle');
            $field->description = '';
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "wrapper"';
        }), $this->makeSetting('etrackerWrapperTsale', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagWrapperTsaleTitle');
            $field->description = '';
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "wrapper"';
        }), $this->makeSetting('etrackerWrapperCust', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagWrapperTcustTitle');
            $field->description = '';
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "wrapper"';
        }), $this->makeSetting('etrackerWrapperBasket', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagWrapperTBasketTitle');
            $field->description = '';
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "wrapper"';
        }), $this->makeSetting('etrackerEventCategory', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagEventCategoryTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagEventCategoryDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "event"';
            if ($trackingType->getValue() === 'event') {
                $field->validators[] = new NotEmpty();
            }
        }), $this->makeSetting('etrackerEventObject', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagEventObjectTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagEventObjectDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "event"';
        }), $this->makeSetting('etrackerEventAction', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagEventActionTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagEventActionDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "event"';
        }), $this->makeSetting('etrackerEventType', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagEventTypeTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagEventTypeDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "event"';
        }), $this->makeSetting('etrackerTransactionType', 'sale', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagTransactionTypeTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagTransactionTypeDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->condition = 'trackingType == "transaction"';
            $field->availableValues = array('sale' => 'Sale', 'lead' => 'Lead', 'cancellation' => 'Cancellation', 'partial_cancellation' => 'Partial Cancellation');
            if ($trackingType->getValue() === 'transaction') {
                $field->validators[] = new NotEmpty();
            }
        }), $this->makeSetting('etrackerTransactionID', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagTransactionIDTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagTransactionIDDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "transaction"';
            if ($trackingType->getValue() === 'transaction') {
                $field->validators[] = new NotEmpty();
            }
        }), $this->makeSetting('etrackerTransactionValue', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagTransactionValueTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagTransactionValueDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "transaction"';
            if ($trackingType->getValue() === 'transaction') {
                $field->validators[] = new NotEmpty();
            }
        }), $this->makeSetting('etrackerTransactionCurrency', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagTransactionCurrencyTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagTransactionCurrencyDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "transaction"';
            if ($trackingType->getValue() === 'transaction') {
                $field->validators[] = new CharacterLength(3, 3);
            }
        }), $this->makeSetting('etrackerTransactionBasket', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagTransactionBasketTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagTransactionBasketDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "transaction"';
            if ($trackingType->getValue() === 'transaction') {
                $field->validators[] = new NotEmpty();
            }
        }), $this->makeSetting('etrackerTransactionCustomerGroup', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagTransactionCustomerGroupTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagTransactionCustomerGroupDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "transaction"';
        }), $this->makeSetting('etrackerTransactionDeliveryConditions', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagTransactionDeliveryConditionsTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagTransactionDeliveryConditionsDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "transaction"';
        }), $this->makeSetting('etrackerTransactionPaymentConditions', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagTransactionPaymentConditionsTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagTransactionPaymentConditionsDescription');
            $field->title = 'Payment Conditions';
            $field->description = 'optional, e.g. Special payment targets, Cash discount, Payment in instalments';
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "transaction"';
        }), $this->makeSetting('etrackerTransactionDebugMode', \false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagTransactionDebugModeTitle');
            $field->title = 'etracker Ecommerce Debug Mode';
            $field->condition = 'trackingType == "transaction"';
        }), $this->makeSetting('etrackerAddToCartProduct', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagAddToCartProductTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagAddToCartProductDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "addtocart"';
            if ($trackingType->getValue() === 'addtocart') {
                $field->validators[] = new NotEmpty();
            }
        }), $this->makeSetting('etrackerAddToCartNumber', '1', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagAddToCartNumberTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagAddToCartNumberDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "addtocart"';
            if ($trackingType->getValue() === 'addtocart') {
                $field->validators[] = new NumberRange();
            }
        }), $this->makeSetting('etrackerFormType', 'formConversion', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagFormTypeTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagFormTypeDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->condition = 'trackingType == "form"';
            $field->availableValues = array('formConversion' => 'Conversion', 'formView' => 'Form View', 'formFieldsView' => 'Field View', 'formFieldInteraction' => 'Field Interaction', 'formFieldError' => 'Field Error');
            if ($trackingType->getValue() === 'form') {
                $field->validators[] = new NotEmpty();
            }
        }), $this->makeSetting('etrackerFormName', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagFormNameTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagFormNameDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "form"';
            if ($trackingType->getValue() === 'form') {
                $field->validators[] = new NotEmpty();
            }
        }), $this->makeSetting('etrackerFormData', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) use($trackingType) {
            $field->title = Piwik::translate('TagManager_EtrackerTagFormDataTitle');
            $field->description = Piwik::translate('TagManager_EtrackerTagFormDataDescription');
            $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            $field->condition = 'trackingType == "form"';
        }));
    }
    public function getCategory()
    {
        return self::CATEGORY_ANALYTICS;
    }
}

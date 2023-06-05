/**
 * External dependencies
 */
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { __ } from '@wordpress/i18n';
import { getSetting } from '@woocommerce/settings';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Internal dependencies
 */
import { PAYMENT_METHOD_NAME } from './constants';

const settings = getSetting( 'cod_data', {} );
const defaultLabel = __( 'Cash on delivery', 'woocommerce' );
const label = decodeEntities( settings.title ) || defaultLabel;

/**
 * Content component
 */
const Content = () => {
	return decodeEntities( settings.description || '' );
};

/**
 * Label component
 *
 * @param {*} props Props from payment API.
 */
const Label = ( props ) => {
	const { PaymentMethodLabel } = props.components;
	return <PaymentMethodLabel text={ label } />;
};

/**
 * Determine whether COD is available for this cart/order.
 *
 * @param {Object}  props                         Incoming props for the component.
 * @param {boolean} props.cartNeedsShipping       True if the cart contains any physical/shippable products.
 * @param {boolean} props.selectedShippingMethods
 *
 * @return {boolean}  True if COD payment method should be displayed as a payment option.
 */
const canMakePayment = ( { cartNeedsShipping, selectedShippingMethods } ) => {
	if ( ! settings.enableForVirtual && ! cartNeedsShipping ) {
		// Store doesn't allow COD for virtual orders AND
		// order doesn't contain any shippable products.
		return false;
	}

	if ( ! settings.enableForShippingMethods.length ) {
		// Store does not limit COD to specific shipping methods.
		return true;
	}

	// Look for a supported shipping method in the user's selected
	// shipping methods. If one is found, then COD is allowed.
	const selectedMethods = Object.values( selectedShippingMethods );
	// supported shipping methods might be global (eg. "Any flat rate"), hence
	// this is doing a `String.prototype.includes` match vs a `Array.prototype.includes` match.
	return settings.enableForShippingMethods.some( ( shippingMethodId ) => {
		return selectedMethods.some( ( selectedMethod ) => {
			return selectedMethod.includes( shippingMethodId );
		} );
	} );
};

/**
 * Cash on Delivery (COD) payment method config object.
 */
const cashOnDeliveryPaymentMethod = {
	name: PAYMENT_METHOD_NAME,
	label: <Label />,
	content: <Content />,
	edit: <Content />,
	canMakePayment,
	ariaLabel: label,
	supports: {
		features: settings?.supports ?? [],
	},
};

registerPaymentMethod( cashOnDeliveryPaymentMethod );

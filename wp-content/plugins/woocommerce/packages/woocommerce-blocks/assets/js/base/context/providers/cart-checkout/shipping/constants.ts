/**
 * External dependencies
 */
import type { CartShippingAddress } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import type { ShippingDataContextType, ShippingErrorTypes } from './types';

export const ERROR_TYPES = {
	NONE: 'none',
	INVALID_ADDRESS: 'invalid_address',
	UNKNOWN: 'unknown_error',
} as ShippingErrorTypes;

export const shippingErrorCodes = {
	INVALID_COUNTRY: 'woocommerce_rest_cart_shipping_rates_invalid_country',
	MISSING_COUNTRY: 'woocommerce_rest_cart_shipping_rates_missing_country',
	INVALID_STATE: 'woocommerce_rest_cart_shipping_rates_invalid_state',
};

export const DEFAULT_SHIPPING_ADDRESS = {
	first_name: '',
	last_name: '',
	company: '',
	address_1: '',
	address_2: '',
	city: '',
	state: '',
	postcode: '',
	country: '',
} as CartShippingAddress;

export const DEFAULT_SHIPPING_CONTEXT_DATA = {
	shippingErrorStatus: {
		isPristine: true,
		isValid: false,
		hasInvalidAddress: false,
		hasError: false,
	},
	dispatchErrorStatus: ( status ) => status,
	shippingErrorTypes: ERROR_TYPES,
	onShippingRateSuccess: () => () => void null,
	onShippingRateFail: () => () => void null,
	onShippingRateSelectSuccess: () => () => void null,
	onShippingRateSelectFail: () => () => void null,
} as ShippingDataContextType;

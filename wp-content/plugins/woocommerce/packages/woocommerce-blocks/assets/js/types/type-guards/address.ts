/**
 * External dependencies
 */
import type { BillingAddress, ShippingAddress } from '@woocommerce/settings';
import { objectHasProp } from '@woocommerce/types';

export const isShippingAddress = (
	address: unknown
): address is ShippingAddress => {
	const keys = [
		'first_name',
		'last_name',
		'company',
		'address_1',
		'address_2',
		'city',
		'state',
		'postcode',
		'country',
		'phone',
	];
	return keys.every( ( key ) => objectHasProp( address, key ) );
};
export const isBillingAddress = (
	address: unknown
): address is BillingAddress => {
	return isShippingAddress( address ) && objectHasProp( address, 'email' );
};

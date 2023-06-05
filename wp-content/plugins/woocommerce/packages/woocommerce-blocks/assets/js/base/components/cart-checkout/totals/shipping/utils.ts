/**
 * External dependencies
 */
import { getSetting } from '@woocommerce/settings';
import type { CartResponseShippingRate } from '@woocommerce/type-defs/cart-response';

/**
 * Searches an array of packages/rates to see if there are actually any rates
 * available.
 *
 * @param {Array} shippingRatePackages An array of packages and rates.
 * @return {boolean} True if a rate exists.
 */
export const hasShippingRate = (
	shippingRatePackages: CartResponseShippingRate[]
): boolean => {
	return shippingRatePackages.some(
		( shippingRatePackage ) => shippingRatePackage.shipping_rates.length
	);
};

/**
 * Calculates the total shippin value based on store settings.
 */
export const getTotalShippingValue = ( values: {
	total_shipping: string;
	total_shipping_tax: string;
} ): number => {
	return getSetting( 'displayCartPricesIncludingTax', false )
		? parseInt( values.total_shipping, 10 ) +
				parseInt( values.total_shipping_tax, 10 )
		: parseInt( values.total_shipping, 10 );
};

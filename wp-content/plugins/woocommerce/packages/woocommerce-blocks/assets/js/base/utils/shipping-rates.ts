/**
 * External dependencies
 */
import {
	CartShippingPackageShippingRate,
	CartShippingRate,
} from '@woocommerce/type-defs/cart';
import { getSetting } from '@woocommerce/settings';
import { LOCAL_PICKUP_ENABLED } from '@woocommerce/block-settings';

/**
 * Get the number of packages in a shippingRates array.
 *
 * @param {Array} shippingRates Shipping rates and packages array.
 */
export const getShippingRatesPackageCount = (
	shippingRates: CartShippingRate[]
) => {
	return shippingRates.length;
};

const collectableMethodIds = getSetting< string[] >(
	'collectableMethodIds',
	[]
);

/**
 * If the package rate's method_id is in the collectableMethodIds array, return true.
 */
export const isPackageRateCollectable = (
	rate: CartShippingPackageShippingRate
): boolean => collectableMethodIds.includes( rate.method_id );

/**
 * Check if the specified rates are collectable. Accepts either an array of rate names, or a single string.
 */
export const hasCollectableRate = (
	chosenRates: string[] | string
): boolean => {
	if ( ! LOCAL_PICKUP_ENABLED ) {
		return false;
	}
	if ( Array.isArray( chosenRates ) ) {
		return !! chosenRates.find( ( rate ) =>
			collectableMethodIds.includes( rate )
		);
	}
	return collectableMethodIds.includes( chosenRates );
};
/**
 * Get the number of rates in a shippingRates array.
 *
 * @param {Array} shippingRates Shipping rates and packages array.
 */
export const getShippingRatesRateCount = (
	shippingRates: CartShippingRate[]
) => {
	return shippingRates.reduce( function ( count, shippingPackage ) {
		return count + shippingPackage.shipping_rates.length;
	}, 0 );
};

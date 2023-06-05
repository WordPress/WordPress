/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { isObject, objectHasProp } from '@woocommerce/types';
import { isPackageRateCollectable } from '@woocommerce/base-utils';

/**
 * Shows a formatted pickup location.
 */
const PickupLocation = (): JSX.Element | null => {
	const { pickupAddress, pickupMethod } = useSelect( ( select ) => {
		const cartShippingRates = select( 'wc/store/cart' ).getShippingRates();

		const flattenedRates = cartShippingRates.flatMap(
			( cartShippingRate ) => cartShippingRate.shipping_rates
		);
		const selectedCollectableRate = flattenedRates.find(
			( rate ) => rate.selected && isPackageRateCollectable( rate )
		);

		// If the rate has an address specified in its metadata.
		if (
			isObject( selectedCollectableRate ) &&
			objectHasProp( selectedCollectableRate, 'meta_data' )
		) {
			const selectedRateMetaData = selectedCollectableRate.meta_data.find(
				( meta ) => meta.key === 'pickup_address'
			);
			if (
				isObject( selectedRateMetaData ) &&
				objectHasProp( selectedRateMetaData, 'value' ) &&
				selectedRateMetaData.value
			) {
				const selectedRatePickupAddress = selectedRateMetaData.value;
				return {
					pickupAddress: selectedRatePickupAddress,
					pickupMethod: selectedCollectableRate.name,
				};
			}
		}

		if ( isObject( selectedCollectableRate ) ) {
			return {
				pickupAddress: undefined,
				pickupMethod: selectedCollectableRate.name,
			};
		}
		return {
			pickupAddress: undefined,
			pickupMethod: undefined,
		};
	} );

	// If the method does not contain an address, or the method supporting collection was not found, return early.
	if (
		typeof pickupAddress === 'undefined' &&
		typeof pickupMethod === 'undefined'
	) {
		return null;
	}

	// Show the pickup method's name if we don't have an address to show.
	return (
		<span className="wc-block-components-shipping-address">
			{ sprintf(
				/* translators: %s: shipping method name, e.g. "Amazon Locker" */
				__( 'Collection from %s', 'woo-gutenberg-products-block' ),
				typeof pickupAddress === 'undefined'
					? pickupMethod
					: pickupAddress
			) + ' ' }
		</span>
	);
};

export default PickupLocation;

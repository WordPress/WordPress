/**
 * External dependencies
 */
import {
	CART_STORE_KEY as storeKey,
	processErrorResponse,
} from '@woocommerce/block-data';
import { useSelect, useDispatch } from '@wordpress/data';
import { isObject } from '@woocommerce/types';
import { useEffect, useRef, useCallback } from '@wordpress/element';
import {
	hasCollectableRate,
	deriveSelectedShippingRates,
} from '@woocommerce/base-utils';
import isShallowEqual from '@wordpress/is-shallow-equal';
import { previewCart } from '@woocommerce/resource-previews';

/**
 * Internal dependencies
 */
import { useStoreEvents } from '../use-store-events';
import type { ShippingData } from './types';

export const useShippingData = (): ShippingData => {
	const {
		shippingRates,
		needsShipping,
		hasCalculatedShipping,
		isLoadingRates,
		isCollectable,
		isSelectingRate,
	} = useSelect( ( select ) => {
		const isEditor = !! select( 'core/editor' );
		const store = select( storeKey );
		const rates = isEditor
			? previewCart.shipping_rates
			: store.getShippingRates();
		return {
			shippingRates: rates,
			needsShipping: isEditor
				? previewCart.needs_shipping
				: store.getNeedsShipping(),
			hasCalculatedShipping: isEditor
				? previewCart.has_calculated_shipping
				: store.getHasCalculatedShipping(),
			isLoadingRates: isEditor ? false : store.isCustomerDataUpdating(),
			isCollectable: rates.every(
				( { shipping_rates: packageShippingRates } ) =>
					packageShippingRates.find( ( { method_id: methodId } ) =>
						hasCollectableRate( methodId )
					)
			),
			isSelectingRate: isEditor
				? false
				: store.isShippingRateBeingSelected(),
		};
	} );

	// set selected rates on ref so it's always current.
	const selectedRates = useRef< Record< string, string > >( {} );
	useEffect( () => {
		const derivedSelectedRates =
			deriveSelectedShippingRates( shippingRates );
		if (
			isObject( derivedSelectedRates ) &&
			! isShallowEqual( selectedRates.current, derivedSelectedRates )
		) {
			selectedRates.current = derivedSelectedRates;
		}
	}, [ shippingRates ] );

	const { selectShippingRate: dispatchSelectShippingRate } = useDispatch(
		storeKey
	) as {
		selectShippingRate: unknown;
	} as {
		selectShippingRate: (
			newShippingRateId: string,
			packageId?: string | number | undefined
		) => Promise< unknown >;
	};

	const hasSelectedLocalPickup = hasCollectableRate(
		Object.values( selectedRates.current ).map(
			( rate ) => rate.split( ':' )[ 0 ]
		)
	);
	// Selects a shipping rate, fires an event, and catch any errors.
	const { dispatchCheckoutEvent } = useStoreEvents();
	const selectShippingRate = useCallback(
		(
			newShippingRateId: string,
			packageId?: string | number | undefined
		): void => {
			let selectPromise;

			/**
			 * Picking location handling
			 *
			 * Forces pickup location to be selected for all packages since we don't allow a mix of shipping and pickup.
			 */
			if (
				hasCollectableRate( newShippingRateId.split( ':' )[ 0 ] ) ||
				hasSelectedLocalPickup
			) {
				selectPromise = dispatchSelectShippingRate( newShippingRateId );
			} else {
				selectPromise = dispatchSelectShippingRate(
					newShippingRateId,
					packageId
				);
			}
			selectPromise
				.then( () => {
					dispatchCheckoutEvent( 'set-selected-shipping-rate', {
						shippingRateId: newShippingRateId,
					} );
				} )
				.catch( ( error ) => {
					processErrorResponse( error );
				} );
		},
		[
			hasSelectedLocalPickup,
			dispatchSelectShippingRate,
			dispatchCheckoutEvent,
		]
	);

	return {
		isSelectingRate,
		selectedRates: selectedRates.current,
		selectShippingRate,
		shippingRates,
		needsShipping,
		hasCalculatedShipping,
		isLoadingRates,
		isCollectable,
		hasSelectedLocalPickup,
	};
};

/**
 * External dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { CartResponse } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { CART_API_ERROR } from './constants';
import type { CartDispatchFromMap, CartResolveSelectFromMap } from './index';
import { shippingAddressHasValidationErrors } from './utils';

/**
 * Resolver for retrieving all cart data.
 */
export const getCartData =
	() =>
	async ( { dispatch }: { dispatch: CartDispatchFromMap } ) => {
		const cartData = await apiFetch< CartResponse >( {
			path: '/wc/store/v1/cart',
			method: 'GET',
			cache: 'no-store',
		} );

		const { receiveCart, receiveError } = dispatch;
		if ( ! cartData ) {
			receiveError( CART_API_ERROR );
			return;
		}

		if ( ! shippingAddressHasValidationErrors() ) {
			dispatch.setFullShippingAddressPushed( true );
		}
		receiveCart( cartData );
	};

/**
 * Resolver for retrieving cart totals.
 */
export const getCartTotals =
	() =>
	async ( {
		resolveSelect,
	}: {
		resolveSelect: CartResolveSelectFromMap;
	} ) => {
		await resolveSelect.getCartData();
	};

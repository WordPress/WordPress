/**
 * External dependencies
 */
import type {
	Cart,
	CartResponse,
	CartResponseItem,
	ExtensionCartUpdateArgs,
	BillingAddressShippingAddress,
	ApiErrorResponse,
	CartShippingPackageShippingRate,
	CartShippingRate,
} from '@woocommerce/types';
import { camelCase, mapKeys } from 'lodash';
import { BillingAddress, ShippingAddress } from '@woocommerce/settings';
import {
	triggerAddedToCartEvent,
	triggerAddingToCartEvent,
} from '@woocommerce/base-utils';

/**
 * Internal dependencies
 */
import { ACTION_TYPES as types } from './action-types';
import { apiFetchWithHeaders } from '../shared-controls';
import { ReturnOrGeneratorYieldUnion } from '../mapped-types';
import { CartDispatchFromMap, CartSelectFromMap } from './index';
import type { Thunks } from './thunks';

// Thunks are functions that can be dispatched, similar to actions creators
// @todo Many of the functions that return promises in this file need to be moved to thunks.ts.
export * from './thunks';

/**
 * An action creator that dispatches the plain action responsible for setting the cart data in the store.
 *
 * @param  cart the parsed cart object. (Parsed into camelCase).
 */
export const setCartData = ( cart: Cart ): { type: string; response: Cart } => {
	return {
		type: types.SET_CART_DATA,
		response: cart,
	};
};

/**
 * An action creator that dispatches the plain action responsible for setting the cart error data in the store.
 *
 * @param  error the parsed error object (Parsed into camelCase).
 */
export const setErrorData = (
	error: ApiErrorResponse | null
): { type: string; response: ApiErrorResponse | null } => {
	return {
		type: types.SET_ERROR_DATA,
		error,
	};
};

/**
 * Returns an action object used in updating the store with the provided cart.
 *
 * This omits the customer addresses so that only updates to cart items and totals are received. This is useful when
 * currently editing address information to prevent it being overwritten from the server.
 *
 * This is a generic response action.
 *
 * @param {CartResponse} response
 */
export const receiveCartContents = (
	response: CartResponse
): { type: string; response: Partial< Cart > } => {
	const cart = mapKeys( response, ( _, key ) =>
		camelCase( key )
	) as unknown as Cart;
	const { shippingAddress, billingAddress, ...cartWithoutAddress } = cart;
	return {
		type: types.SET_CART_DATA,
		response: cartWithoutAddress,
	};
};

/**
 * Returns an action object used to track when a coupon is applying.
 *
 * @param {string} [couponCode] Coupon being added.
 */
export const receiveApplyingCoupon = ( couponCode: string ) =>
	( {
		type: types.APPLYING_COUPON,
		couponCode,
	} as const );

/**
 * Returns an action object used to track when a coupon is removing.
 *
 * @param {string} [couponCode] Coupon being removed..
 */
export const receiveRemovingCoupon = ( couponCode: string ) =>
	( {
		type: types.REMOVING_COUPON,
		couponCode,
	} as const );

/**
 * Returns an action object for updating a single cart item in the store.
 *
 * @param {CartResponseItem} [response=null] A cart item API response.
 */
export const receiveCartItem = ( response: CartResponseItem | null = null ) =>
	( {
		type: types.RECEIVE_CART_ITEM,
		cartItem: response,
	} as const );

/**
 * Returns an action object to indicate if the specified cart item quantity is
 * being updated.
 *
 * @param {string}  cartItemKey              Cart item being updated.
 * @param {boolean} [isPendingQuantity=true] Flag for update state; true if API
 *                                           request is pending.
 */
export const itemIsPendingQuantity = (
	cartItemKey: string,
	isPendingQuantity = true
) =>
	( {
		type: types.ITEM_PENDING_QUANTITY,
		cartItemKey,
		isPendingQuantity,
	} as const );

/**
 * Returns an action object to remove a cart item from the store.
 *
 * @param {string}  cartItemKey            Cart item to remove.
 * @param {boolean} [isPendingDelete=true] Flag for update state; true if API
 *                                         request is pending.
 */
export const itemIsPendingDelete = (
	cartItemKey: string,
	isPendingDelete = true
) =>
	( {
		type: types.RECEIVE_REMOVED_ITEM,
		cartItemKey,
		isPendingDelete,
	} as const );

/**
 * Returns an action object to mark the cart data in the store as stale.
 *
 * @param {boolean} [isCartDataStale=true] Flag to mark cart data as stale; true if
 *                                         lastCartUpdate timestamp is newer than the
 *                                         one in wcSettings.
 */
export const setIsCartDataStale = ( isCartDataStale = true ) =>
	( {
		type: types.SET_IS_CART_DATA_STALE,
		isCartDataStale,
	} as const );

/**
 * Returns an action object used to track when customer data is being updated
 * (billing and/or shipping).
 */
export const updatingCustomerData = ( isResolving: boolean ) =>
	( {
		type: types.UPDATING_CUSTOMER_DATA,
		isResolving,
	} as const );

/**
 * Returns an action object used to track whether the shipping rate is being
 * selected or not.
 *
 * @param {boolean} isResolving True if shipping rate is being selected.
 */
export const shippingRatesBeingSelected = ( isResolving: boolean ) =>
	( {
		type: types.UPDATING_SELECTED_SHIPPING_RATE,
		isResolving,
	} as const );

/**
 * POSTs to the /cart/extensions endpoint with the data supplied by the extension.
 *
 * @param {Object} args The data to be posted to the endpoint
 */
export const applyExtensionCartUpdate =
	( args: ExtensionCartUpdateArgs ) =>
	async ( { dispatch }: { dispatch: CartDispatchFromMap } ) => {
		try {
			const { response } = await apiFetchWithHeaders( {
				path: '/wc/store/v1/cart/extensions',
				method: 'POST',
				data: { namespace: args.namespace, data: args.data },
				cache: 'no-store',
			} );
			dispatch.receiveCart( response );
			return response;
		} catch ( error ) {
			dispatch.receiveError( error );
			return Promise.reject( error );
		}
	};

/**
 * Applies a coupon code and either invalidates caches, or receives an error if
 * the coupon cannot be applied.
 *
 * @param {string} couponCode The coupon code to apply to the cart.
 * @throws            Will throw an error if there is an API problem.
 */
export const applyCoupon =
	( couponCode: string ) =>
	async ( { dispatch }: { dispatch: CartDispatchFromMap } ) => {
		try {
			dispatch.receiveApplyingCoupon( couponCode );
			const { response } = await apiFetchWithHeaders( {
				path: '/wc/store/v1/cart/apply-coupon',
				method: 'POST',
				data: {
					code: couponCode,
				},
				cache: 'no-store',
			} );
			dispatch.receiveCart( response );
			return response;
		} catch ( error ) {
			dispatch.receiveError( error );
			return Promise.reject( error );
		} finally {
			dispatch.receiveApplyingCoupon( '' );
		}
	};

/**
 * Removes a coupon code and either invalidates caches, or receives an error if
 * the coupon cannot be removed.
 *
 * @param {string} couponCode The coupon code to remove from the cart.
 * @throws            Will throw an error if there is an API problem.
 */
export const removeCoupon =
	( couponCode: string ) =>
	async ( { dispatch }: { dispatch: CartDispatchFromMap } ) => {
		try {
			dispatch.receiveRemovingCoupon( couponCode );
			const { response } = await apiFetchWithHeaders( {
				path: '/wc/store/v1/cart/remove-coupon',
				method: 'POST',
				data: {
					code: couponCode,
				},
				cache: 'no-store',
			} );
			dispatch.receiveCart( response );
			return response;
		} catch ( error ) {
			dispatch.receiveError( error );
			return Promise.reject( error );
		} finally {
			dispatch.receiveRemovingCoupon( '' );
		}
	};

/**
 * Adds an item to the cart:
 * - Calls API to add item.
 * - If successful, yields action to add item from store.
 * - If error, yields action to store error.
 *
 * @param {number} productId    Product ID to add to cart.
 * @param {number} [quantity=1] Number of product ID being added to cart.
 * @throws           Will throw an error if there is an API problem.
 */
export const addItemToCart =
	( productId: number, quantity = 1 ) =>
	async ( { dispatch }: { dispatch: CartDispatchFromMap } ) => {
		try {
			triggerAddingToCartEvent();
			const { response } = await apiFetchWithHeaders( {
				path: `/wc/store/v1/cart/add-item`,
				method: 'POST',
				data: {
					id: productId,
					quantity,
				},
				cache: 'no-store',
			} );
			dispatch.receiveCart( response );
			triggerAddedToCartEvent( { preserveCartData: true } );
			return response;
		} catch ( error ) {
			dispatch.receiveError( error );
			return Promise.reject( error );
		}
	};

/**
 * Removes specified item from the cart:
 * - Calls API to remove item.
 * - If successful, yields action to remove item from store.
 * - If error, yields action to store error.
 * - Sets cart item as pending while API request is in progress.
 *
 * @param {string} cartItemKey Cart item being updated.
 */
export const removeItemFromCart =
	( cartItemKey: string ) =>
	async ( { dispatch }: { dispatch: CartDispatchFromMap } ) => {
		try {
			dispatch.itemIsPendingDelete( cartItemKey );
			const { response } = await apiFetchWithHeaders( {
				path: `/wc/store/v1/cart/remove-item`,
				data: {
					key: cartItemKey,
				},
				method: 'POST',
				cache: 'no-store',
			} );
			dispatch.receiveCart( response );
			return response;
		} catch ( error ) {
			dispatch.receiveError( error );
			return Promise.reject( error );
		} finally {
			dispatch.itemIsPendingDelete( cartItemKey, false );
		}
	};

/**
 * Persists a quantity change the for specified cart item:
 * - Calls API to set quantity.
 * - If successful, yields action to update store.
 * - If error, yields action to store error.
 *
 * @param {string} cartItemKey Cart item being updated.
 * @param {number} quantity    Specified (new) quantity.
 */
export const changeCartItemQuantity =
	(
		cartItemKey: string,
		quantity: number
		// eslint-disable-next-line @typescript-eslint/no-explicit-any -- unclear how to represent multiple different yields as type
	) =>
	async ( {
		dispatch,
		select,
	}: {
		dispatch: CartDispatchFromMap;
		select: CartSelectFromMap;
	} ) => {
		const cartItem = select.getCartItem( cartItemKey );
		if ( cartItem?.quantity === quantity ) {
			return;
		}
		try {
			dispatch.itemIsPendingQuantity( cartItemKey );
			const { response } = await apiFetchWithHeaders( {
				path: '/wc/store/v1/cart/update-item',
				method: 'POST',
				data: {
					key: cartItemKey,
					quantity,
				},
				cache: 'no-store',
			} );
			dispatch.receiveCart( response );
			return response;
		} catch ( error ) {
			dispatch.receiveError( error );
			return Promise.reject( error );
		} finally {
			dispatch.itemIsPendingQuantity( cartItemKey, false );
		}
	};

/**
 * Selects a shipping rate.
 *
 * @param {string}          rateId      The id of the rate being selected.
 * @param {number | string} [packageId] The key of the packages that we will select within.
 */
export const selectShippingRate =
	( rateId: string, packageId = 0 ) =>
	async ( {
		dispatch,
		select,
	}: {
		dispatch: CartDispatchFromMap;
		select: CartSelectFromMap;
	} ) => {
		const selectedShippingRate = select
			.getShippingRates()
			.find(
				( shippingPackage: CartShippingRate ) =>
					shippingPackage.package_id === packageId
			)
			?.shipping_rates.find(
				( rate: CartShippingPackageShippingRate ) =>
					rate.selected === true
			);
		if ( selectedShippingRate?.rate_id === rateId ) {
			return;
		}
		try {
			dispatch.shippingRatesBeingSelected( true );
			const { response } = await apiFetchWithHeaders( {
				path: `/wc/store/v1/cart/select-shipping-rate`,
				method: 'POST',
				data: {
					package_id: packageId,
					rate_id: rateId,
				},
				cache: 'no-store',
			} );
			// Remove shipping and billing address from the response, so we don't overwrite what the shopper is
			// entering in the form if rates suddenly appear mid-edit.
			const {
				shipping_address: shippingAddress,
				billing_address: billingAddress,
				...rest
			} = response;
			dispatch.receiveCart( rest );
			return response as CartResponse;
		} catch ( error ) {
			dispatch.receiveError( error );
			return Promise.reject( error );
		} finally {
			dispatch.shippingRatesBeingSelected( false );
		}
	};

/**
 * Sets billing address locally, as opposed to updateCustomerData which sends it to the server.
 */
export const setBillingAddress = (
	billingAddress: Partial< BillingAddress >
) => ( { type: types.SET_BILLING_ADDRESS, billingAddress } as const );

/**
 * Sets shipping address locally, as opposed to updateCustomerData which sends it to the server.
 */
export const setShippingAddress = (
	shippingAddress: Partial< ShippingAddress >
) => ( { type: types.SET_SHIPPING_ADDRESS, shippingAddress } as const );

/**
 * Updates the shipping and/or billing address for the customer and returns an updated cart.
 */
export const updateCustomerData =
	(
		// Address data to be updated; can contain both billing_address and shipping_address.
		customerData: Partial< BillingAddressShippingAddress >,
		// If the address is being edited, we don't update the customer data in the store from the response.
		editing = true
	) =>
	async ( { dispatch }: { dispatch: CartDispatchFromMap } ) => {
		try {
			dispatch.updatingCustomerData( true );
			const { response } = await apiFetchWithHeaders( {
				path: '/wc/store/v1/cart/update-customer',
				method: 'POST',
				data: customerData,
				cache: 'no-store',
			} );
			if ( editing ) {
				dispatch.receiveCartContents( response );
			} else {
				dispatch.receiveCart( response );
			}
			return response;
		} catch ( error ) {
			dispatch.receiveError( error );
			return Promise.reject( error );
		} finally {
			dispatch.updatingCustomerData( false );
		}
	};

export const setFullShippingAddressPushed = (
	fullShippingAddressPushed: boolean
) => ( {
	type: types.SET_FULL_SHIPPING_ADDRESS_PUSHED,
	fullShippingAddressPushed,
} );

type Actions =
	| typeof addItemToCart
	| typeof applyCoupon
	| typeof changeCartItemQuantity
	| typeof itemIsPendingDelete
	| typeof itemIsPendingQuantity
	| typeof receiveApplyingCoupon
	| typeof receiveCartContents
	| typeof receiveCartItem
	| typeof receiveRemovingCoupon
	| typeof removeCoupon
	| typeof removeItemFromCart
	| typeof selectShippingRate
	| typeof setBillingAddress
	| typeof setCartData
	| typeof setErrorData
	| typeof setIsCartDataStale
	| typeof setShippingAddress
	| typeof shippingRatesBeingSelected
	| typeof updateCustomerData
	| typeof setFullShippingAddressPushed
	| typeof updatingCustomerData;

export type CartAction = ReturnOrGeneratorYieldUnion< Actions | Thunks >;

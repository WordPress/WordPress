/**
 * External dependencies
 */
import type {
	Cart,
	CartTotals,
	CartMeta,
	CartItem,
	CartShippingRate,
	ApiErrorResponse,
} from '@woocommerce/types';
import { BillingAddress, ShippingAddress } from '@woocommerce/settings';

/**
 * Internal dependencies
 */
import { CartState, defaultCartState } from './default-state';

/**
 * Retrieves cart data from state.
 *
 * @param {CartState} state The current state.
 * @return {Cart} The data to return.
 */
export const getCartData = ( state: CartState ): Cart => {
	return state.cartData;
};

export const getCustomerData = (
	state: CartState
): {
	shippingAddress: ShippingAddress;
	billingAddress: BillingAddress;
} => {
	return {
		shippingAddress: state.cartData.shippingAddress,
		billingAddress: state.cartData.billingAddress,
	};
};

/**
 * Retrieves shipping rates from state.
 *
 * @param { CartState } state The current state.
 * @return { CartShippingRate[] } The shipping rates on the cart.
 */
export const getShippingRates = ( state: CartState ): CartShippingRate[] => {
	return state.cartData.shippingRates;
};

/**
 * Retrieves whether the cart needs shipping.
 *
 * @param { CartState } state The current state.
 * @return { boolean } True if the cart needs shipping.
 */
export const getNeedsShipping = ( state: CartState ): boolean => {
	return state.cartData.needsShipping;
};

/**
 * Retrieves whether the cart shipping has been calculated.
 *
 * @param { CartState } state The current state.
 * @return { boolean } True if the shipping has been calculated.
 */
export const getHasCalculatedShipping = ( state: CartState ): boolean => {
	return state.cartData.hasCalculatedShipping;
};

/**
 * Retrieves cart totals from state.
 *
 * @param {CartState} state The current state.
 * @return {CartTotals} The data to return.
 */
export const getCartTotals = ( state: CartState ): CartTotals => {
	return state.cartData.totals || defaultCartState.cartData.totals;
};

/**
 * Retrieves cart meta from state.
 *
 * @param {CartState} state The current state.
 * @return {CartMeta} The data to return.
 */
export const getCartMeta = ( state: CartState ): CartMeta => {
	return state.metaData || defaultCartState.metaData;
};

/**
 * Retrieves cart errors from state.
 */
export const getCartErrors = ( state: CartState ): ApiErrorResponse[] => {
	return state.errors;
};

/**
 * Returns true if any coupon is being applied.
 *
 * @param {CartState} state The current state.
 * @return {boolean} True if a coupon is being applied.
 */
export const isApplyingCoupon = ( state: CartState ): boolean => {
	return !! state.metaData.applyingCoupon;
};

/**
 * Returns true if cart is stale, false if it is not.
 *
 * @param {CartState} state The current state.
 * @return {boolean} True if the cart data is stale.
 */
export const isCartDataStale = ( state: CartState ): boolean => {
	return state.metaData.isCartDataStale;
};

/**
 * Retrieves the coupon code currently being applied.
 *
 * @param {CartState} state The current state.
 * @return {string} The data to return.
 */
export const getCouponBeingApplied = ( state: CartState ): string => {
	return state.metaData.applyingCoupon || '';
};

/**
 * Returns true if any coupon is being removed.
 *
 * @param {CartState} state The current state.
 * @return {boolean} True if a coupon is being removed.
 */
export const isRemovingCoupon = ( state: CartState ): boolean => {
	return !! state.metaData.removingCoupon;
};

/**
 * Retrieves the coupon code currently being removed.
 *
 * @param {CartState} state The current state.
 * @return {string} The data to return.
 */
export const getCouponBeingRemoved = ( state: CartState ): string => {
	return state.metaData.removingCoupon || '';
};

/**
 * Returns cart item matching specified key.
 *
 * @param {CartState} state       The current state.
 * @param {string}    cartItemKey Key for a cart item.
 * @return {CartItem | void} Cart item object, or undefined if not found.
 */
export const getCartItem = (
	state: CartState,
	cartItemKey: string
): CartItem | void => {
	return state.cartData.items.find(
		( cartItem ) => cartItem.key === cartItemKey
	);
};

/**
 * Returns true if the specified cart item quantity is being updated.
 *
 * @param {CartState} state       The current state.
 * @param {string}    cartItemKey Key for a cart item.
 * @return {boolean} True if a item has a pending request to be updated.
 */
export const isItemPendingQuantity = (
	state: CartState,
	cartItemKey: string
): boolean => {
	return state.cartItemsPendingQuantity.includes( cartItemKey );
};

/**
 * Returns true if the specified cart item quantity is being updated.
 *
 * @param {CartState} state       The current state.
 * @param {string}    cartItemKey Key for a cart item.
 * @return {boolean} True if a item has a pending request to be updated.
 */
export const isItemPendingDelete = (
	state: CartState,
	cartItemKey: string
): boolean => {
	return state.cartItemsPendingDelete.includes( cartItemKey );
};
/**
 * Retrieves if the address is being applied for shipping.
 *
 * @param {CartState} state The current state.
 * @return {boolean} are shipping rates loading.
 */
export const isCustomerDataUpdating = ( state: CartState ): boolean => {
	return !! state.metaData.updatingCustomerData;
};

/**
 * Retrieves if the shipping rate selection is being persisted.
 *
 * @param {CartState} state The current state.
 *
 * @return {boolean} True if the shipping rate selection is being persisted to
 *                   the server.
 */
export const isShippingRateBeingSelected = ( state: CartState ): boolean => {
	return !! state.metaData.updatingSelectedRate;
};

/**
 * Retrieves the item keys for items whose quantity is currently being updated.
 */
export const getItemsPendingQuantityUpdate = ( state: CartState ): string[] => {
	return state.cartItemsPendingQuantity;
};
/**
 * Retrieves the item keys for items that are currently being deleted.
 */
export const getItemsPendingDelete = ( state: CartState ): string[] => {
	return state.cartItemsPendingDelete;
};

/**
 * Whether the address has changes that have not been synced with the server.
 */
export const getFullShippingAddressPushed = ( state: CartState ): boolean => {
	return state.metaData.fullShippingAddressPushed;
};

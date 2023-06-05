/**
 * External dependencies
 */
import type { CartItem } from '@woocommerce/types';
import type { Reducer } from 'redux';

/**
 * Internal dependencies
 */
import { ACTION_TYPES as types } from './action-types';
import { defaultCartState, CartState } from './default-state';
import { EMPTY_CART_ERRORS } from '../constants';
import type { CartAction } from './actions';

/**
 * Sub-reducer for cart items array.
 *
 * @param {Array<CartItem>} state  cartData.items state slice.
 * @param {CartAction}      action Action object.
 */
const cartItemsReducer = (
	state: Array< CartItem > = [],
	action: Partial< CartAction >
) => {
	switch ( action.type ) {
		case types.RECEIVE_CART_ITEM:
			// Replace specified cart element with the new data from server.
			return state.map( ( cartItem ) => {
				if ( cartItem.key === action.cartItem?.key ) {
					return action.cartItem;
				}
				return cartItem;
			} );
	}
	return state;
};

/**
 * Reducer for receiving items related to the cart.
 *
 * @param {CartState}  state  The current state in the store.
 * @param {CartAction} action Action object.
 *
 * @return  {CartState}          New or existing state.
 */
const reducer: Reducer< CartState > = (
	state = defaultCartState,
	action: Partial< CartAction >
) => {
	switch ( action.type ) {
		case types.SET_FULL_SHIPPING_ADDRESS_PUSHED:
			state = {
				...state,
				metaData: {
					...state.metaData,
					fullShippingAddressPushed: action.fullShippingAddressPushed,
				},
			};
			break;
		case types.SET_ERROR_DATA:
			if ( action.error ) {
				state = {
					...state,
					errors: [ action.error ],
				};
			}
			break;
		case types.SET_CART_DATA:
			if ( action.response ) {
				state = {
					...state,
					errors: EMPTY_CART_ERRORS,
					cartData: {
						...state.cartData,
						...action.response,
					},
				};
			}
			break;
		case types.APPLYING_COUPON:
			if ( action.couponCode || action.couponCode === '' ) {
				state = {
					...state,
					metaData: {
						...state.metaData,
						applyingCoupon: action.couponCode,
					},
				};
			}
			break;
		case types.SET_BILLING_ADDRESS:
			state = {
				...state,
				cartData: {
					...state.cartData,
					billingAddress: {
						...state.cartData.billingAddress,
						...action.billingAddress,
					},
				},
			};
			break;
		case types.SET_SHIPPING_ADDRESS:
			state = {
				...state,
				cartData: {
					...state.cartData,
					shippingAddress: {
						...state.cartData.shippingAddress,
						...action.shippingAddress,
					},
				},
			};
			break;

		case types.REMOVING_COUPON:
			if ( action.couponCode || action.couponCode === '' ) {
				state = {
					...state,
					metaData: {
						...state.metaData,
						removingCoupon: action.couponCode,
					},
				};
			}
			break;

		case types.ITEM_PENDING_QUANTITY:
			// Remove key by default - handles isQuantityPending==false
			// and prevents duplicates when isQuantityPending===true.
			const keysPendingQuantity = state.cartItemsPendingQuantity.filter(
				( key ) => key !== action.cartItemKey
			);
			if ( action.isPendingQuantity && action.cartItemKey ) {
				keysPendingQuantity.push( action.cartItemKey );
			}
			state = {
				...state,
				cartItemsPendingQuantity: keysPendingQuantity,
			};
			break;
		case types.RECEIVE_REMOVED_ITEM:
			const keysPendingDelete = state.cartItemsPendingDelete.filter(
				( key ) => key !== action.cartItemKey
			);
			if ( action.isPendingDelete && action.cartItemKey ) {
				keysPendingDelete.push( action.cartItemKey );
			}
			state = {
				...state,
				cartItemsPendingDelete: keysPendingDelete,
			};
			break;
		// Delegate to cartItemsReducer.
		case types.RECEIVE_CART_ITEM:
			state = {
				...state,
				errors: EMPTY_CART_ERRORS,
				cartData: {
					...state.cartData,
					items: cartItemsReducer( state.cartData.items, action ),
				},
			};
			break;
		case types.UPDATING_CUSTOMER_DATA:
			state = {
				...state,
				metaData: {
					...state.metaData,
					updatingCustomerData: !! action.isResolving,
				},
			};
			break;
		case types.UPDATING_SELECTED_SHIPPING_RATE:
			state = {
				...state,
				metaData: {
					...state.metaData,
					updatingSelectedRate: !! action.isResolving,
				},
			};
			break;
		case types.SET_IS_CART_DATA_STALE:
			state = {
				...state,
				metaData: {
					...state.metaData,
					isCartDataStale: action.isCartDataStale,
				},
			};
			break;
	}
	return state;
};

export type State = ReturnType< typeof reducer >;

export default reducer;

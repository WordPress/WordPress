/**
 * External dependencies
 */
import deepFreeze from 'deep-freeze';

/**
 * Internal dependencies
 */
import cartReducer from '../reducers';
import { ACTION_TYPES as types } from '../action-types';

describe( 'cartReducer', () => {
	const originalState = deepFreeze( {
		cartData: {
			coupons: [],
			items: [],
			fees: [],
			itemsCount: 0,
			itemsWeight: 0,
			needsShipping: true,
			totals: {},
		},
		metaData: {},
		errors: [
			{
				code: '100',
				message: 'Test Error',
				data: {},
			},
		],
	} );
	it( 'sets expected state when a cart is received', () => {
		const testAction = {
			type: types.SET_CART_DATA,
			response: {
				coupons: [],
				items: [],
				fees: [],
				itemsCount: 0,
				itemsWeight: 0,
				needsShipping: true,
				totals: {},
			},
		};
		const newState = cartReducer( originalState, testAction );
		expect( newState ).not.toBe( originalState );
		expect( newState.cartData ).toEqual( {
			coupons: [],
			items: [],
			fees: [],
			itemsCount: 0,
			itemsWeight: 0,
			needsShipping: true,
			totals: {},
		} );
	} );
	it( 'sets expected state when errors are set', () => {
		const testAction = {
			type: types.SET_ERROR_DATA,
			error: {
				code: '101',
				message: 'Test Error',
				data: {},
			},
		};
		const newState = cartReducer( originalState, testAction );
		expect( newState ).not.toBe( originalState );
		expect( newState.errors ).toEqual( [
			{
				code: '101',
				message: 'Test Error',
				data: {},
			},
		] );
	} );
	it( 'sets expected state when a coupon is applied', () => {
		const testAction = {
			type: types.APPLYING_COUPON,
			couponCode: 'APPLYME',
		};
		const newState = cartReducer( originalState, testAction );
		expect( newState ).not.toBe( originalState );
		expect( newState.metaData.applyingCoupon ).toEqual( 'APPLYME' );
	} );
	it( 'sets expected state when a coupon is removed', () => {
		const testAction = {
			type: types.REMOVING_COUPON,
			couponCode: 'REMOVEME',
		};
		const newState = cartReducer( originalState, testAction );
		expect( newState ).not.toBe( originalState );
		expect( newState.metaData.removingCoupon ).toEqual( 'REMOVEME' );
	} );
} );

/**
 * External dependencies
 */
import { dispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { getCartData } from '../resolvers';
import { CART_STORE_KEY } from '..';

jest.mock( '@wordpress/data-controls' );
jest.mock( '@wordpress/api-fetch' );
describe( 'getCartData', () => {
	it( 'when apiFetch returns a valid response, receives the cart correctly', async () => {
		const mockDispatch = {
			...dispatch( CART_STORE_KEY ),
			receiveCart: jest.fn(),
			receiveError: jest.fn(),
		};
		apiFetch.mockReturnValue( {
			coupons: [],
			items: [],
			fees: [],
			itemsCount: 0,
			itemsWeight: 0,
			needsShipping: true,
			totals: {},
		} );
		await getCartData()( { dispatch: mockDispatch } );
		expect( mockDispatch.receiveCart ).toHaveBeenCalledWith( {
			coupons: [],
			items: [],
			fees: [],
			itemsCount: 0,
			itemsWeight: 0,
			needsShipping: true,
			totals: {},
		} );
		expect( mockDispatch.receiveError ).not.toHaveBeenCalled();
	} );
	it( 'when apiFetch returns an invalid response, dispatches the correct error action', async () => {
		const mockDispatch = {
			...dispatch( CART_STORE_KEY ),
			receiveCart: jest.fn(),
			receiveError: jest.fn(),
		};
		apiFetch.mockReturnValue( undefined );
		await getCartData()( { dispatch: mockDispatch } );
		expect( mockDispatch.receiveCart ).not.toHaveBeenCalled();
		expect( mockDispatch.receiveError ).toHaveBeenCalled();
	} );
} );

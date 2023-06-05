/**
 * External dependencies
 */
import { apiFetch } from '@wordpress/data-controls';
import { controls } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { getRoute, getRoutes } from '../resolvers';
import { receiveRoutes } from '../actions';
import { STORE_KEY } from '../constants';

jest.mock( '@wordpress/data-controls' );
jest.mock( '@wordpress/data' );

describe( 'getRoute', () => {
	it( 'yields select control response', () => {
		const fulfillment = getRoute( 'wc/blocks' );
		fulfillment.next();
		expect( controls.resolveSelect ).toHaveBeenCalledWith(
			STORE_KEY,
			'getRoutes',
			'wc/blocks'
		);
		const { done } = fulfillment.next();
		expect( done ).toBe( true );
	} );
} );
describe( 'getRoutes', () => {
	describe( 'yields with expected responses', () => {
		let fulfillment;
		const rewind = () => ( fulfillment = getRoutes( 'wc/blocks' ) );
		test( 'with apiFetch control invoked', () => {
			rewind();
			fulfillment.next();
			expect( apiFetch ).toHaveBeenCalledWith( { path: 'wc/blocks' } );
		} );
		test( 'with receiveRoutes action with valid response', () => {
			const testResponse = {
				routes: {
					'/wc/blocks/products/attributes': [],
				},
			};
			const { value } = fulfillment.next( testResponse );
			expect( value ).toEqual(
				receiveRoutes( Object.keys( testResponse.routes ), 'wc/blocks' )
			);
		} );
		test( 'with receiveRoutesAction with invalid response', () => {
			rewind();
			fulfillment.next();
			const { value } = fulfillment.next( {} );
			expect( value ).toEqual( receiveRoutes( [], 'wc/blocks' ) );
		} );
	} );
} );

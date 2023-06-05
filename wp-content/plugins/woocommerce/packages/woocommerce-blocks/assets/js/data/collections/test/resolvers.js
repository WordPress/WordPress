/**
 * External dependencies
 */
import { controls } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { getCollection, getCollectionHeader } from '../resolvers';
import { receiveCollection } from '../actions';
import { STORE_KEY as SCHEMA_STORE_KEY } from '../../schema/constants';
import { STORE_KEY } from '../constants';
import { apiFetchWithHeadersControl } from '../../shared-controls';

jest.mock( '@wordpress/data' );

describe( 'getCollection', () => {
	describe( 'yields with expected responses', () => {
		let fulfillment;
		const testArgs = [
			'wc/blocks',
			'products',
			{ foo: 'bar' },
			[ 20, 30 ],
		];
		const rewind = () => ( fulfillment = getCollection( ...testArgs ) );
		test( 'with getRoute call invoked to retrieve route', () => {
			rewind();
			fulfillment.next();
			expect( controls.resolveSelect ).toHaveBeenCalledWith(
				SCHEMA_STORE_KEY,
				'getRoute',
				testArgs[ 0 ],
				testArgs[ 1 ],
				testArgs[ 3 ]
			);
		} );
		test(
			'when no route is retrieved, yields receiveCollection and ' +
				'returns',
			() => {
				const { value } = fulfillment.next();
				const expected = receiveCollection(
					'wc/blocks',
					'products',
					'?foo=bar',
					[ 20, 30 ],
					{
						items: [],
						headers: {
							get: () => undefined,
							has: () => undefined,
						},
					}
				);
				expect( value.type ).toBe( expected.type );
				expect( value.namespace ).toBe( expected.namespace );
				expect( value.resourceName ).toBe( expected.resourceName );
				expect( value.queryString ).toBe( expected.queryString );
				expect( value.ids ).toEqual( expected.ids );
				expect( Object.keys( value.response ) ).toEqual(
					Object.keys( expected.response )
				);
				const { done } = fulfillment.next();
				expect( done ).toBe( true );
			}
		);
		test(
			'when route is retrieved, yields apiFetchWithHeaders control action with ' +
				'expected route',
			() => {
				rewind();
				fulfillment.next();
				const { value } = fulfillment.next( 'https://example.org' );
				expect( value ).toEqual(
					apiFetchWithHeadersControl( {
						path: 'https://example.org?foo=bar',
					} )
				);
			}
		);
		test(
			'when apiFetchWithHeaders does not return a valid response, ' +
				'yields expected action',
			() => {
				const { value } = fulfillment.next( {} );
				expect( value ).toEqual(
					receiveCollection(
						'wc/blocks',
						'products',
						'?foo=bar',
						[ 20, 30 ],
						{ items: [], headers: undefined }
					)
				);
			}
		);
		test(
			'when apiFetch returns a valid response, yields expected ' +
				'action',
			() => {
				rewind();
				fulfillment.next();
				fulfillment.next( 'https://example.org' );
				const { value } = fulfillment.next( {
					response: [ '42', 'cheeseburgers' ],
					headers: { foo: 'bar' },
				} );
				expect( value ).toEqual(
					receiveCollection(
						'wc/blocks',
						'products',
						'?foo=bar',
						[ 20, 30 ],
						{
							items: [ '42', 'cheeseburgers' ],
							headers: { foo: 'bar' },
						}
					)
				);
				const { done } = fulfillment.next();
				expect( done ).toBe( true );
			}
		);
	} );
} );

describe( 'getCollectionHeader', () => {
	let fulfillment;
	const rewind = ( ...testArgs ) =>
		( fulfillment = getCollectionHeader( ...testArgs ) );
	it( 'yields expected select control when called with less args', () => {
		rewind( 'x-wp-total', '/wc/blocks', 'products' );
		const { value } = fulfillment.next();
		expect( value ).toEqual(
			controls.resolveSelect(
				STORE_KEY,
				'getCollection',
				'/wc/blocks',
				'products'
			)
		);
	} );
	it( 'yields expected select control when called with all args', () => {
		const args = [
			'x-wp-total',
			'/wc/blocks',
			'products/attributes',
			{ sort: 'ASC' },
			[ 10 ],
		];
		rewind( ...args );
		const { value } = fulfillment.next();
		expect( value ).toEqual(
			controls.resolveSelect(
				STORE_KEY,
				'/wc/blocks',
				'products/attributes',
				{ sort: 'ASC' },
				[ 10 ]
			)
		);
		const { done } = fulfillment.next();
		expect( done ).toBe( true );
	} );
} );

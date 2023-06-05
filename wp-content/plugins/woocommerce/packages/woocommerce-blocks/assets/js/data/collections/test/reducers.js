/**
 * External dependencies
 */
import deepFreeze from 'deep-freeze';

/**
 * Internal dependencies
 */
import receiveCollection from '../reducers';
import { ACTION_TYPES as types } from '../action-types';

describe( 'receiveCollection', () => {
	const originalState = deepFreeze( {
		'wc/blocks': {
			products: {
				'[]': {
					'?someQuery=2': {
						items: [ 'foo' ],
						headers: { 'x-wp-total': 22 },
					},
				},
			},
		},
	} );
	it(
		'returns original state when there is already an entry in the state ' +
			'for the given arguments',
		() => {
			const testAction = {
				type: types.RECEIVE_COLLECTION,
				namespace: 'wc/blocks',
				resourceName: 'products',
				queryString: '?someQuery=2',
				response: {
					items: [ 'bar' ],
					headers: { foo: 'bar' },
				},
			};
			expect( receiveCollection( originalState, testAction ) ).toBe(
				originalState
			);
		}
	);
	it(
		'returns new state when items exist in collection but the type is ' +
			'for a reset',
		() => {
			const testAction = {
				type: types.RESET_COLLECTION,
				namespace: 'wc/blocks',
				resourceName: 'products',
				queryString: '?someQuery=2',
				response: {
					items: [ 'cheeseburger' ],
					headers: { foo: 'bar' },
				},
			};
			const newState = receiveCollection( originalState, testAction );
			expect( newState ).not.toBe( originalState );
			expect(
				newState[ 'wc/blocks' ].products[ '[]' ][ '?someQuery=2' ]
			).toEqual( {
				items: [ 'cheeseburger' ],
				headers: { foo: 'bar' },
			} );
		}
	);
	it( 'returns new state when items do not exist in collection yet', () => {
		const testAction = {
			type: types.RECEIVE_COLLECTION,
			namespace: 'wc/blocks',
			resourceName: 'products',
			queryString: '?someQuery=3',
			response: { items: [ 'cheeseburger' ], headers: { foo: 'bar' } },
		};
		const newState = receiveCollection( originalState, testAction );
		expect( newState ).not.toBe( originalState );
		expect(
			newState[ 'wc/blocks' ].products[ '[]' ][ '?someQuery=3' ]
		).toEqual( { items: [ 'cheeseburger' ], headers: { foo: 'bar' } } );
	} );
	it( 'sets expected state when ids are passed in', () => {
		const testAction = {
			type: types.RECEIVE_COLLECTION,
			namespace: 'wc/blocks',
			resourceName: 'products/attributes',
			queryString: '?something',
			response: { items: [ 10, 20 ], headers: { foo: 'bar' } },
			ids: [ 30, 42 ],
		};
		const newState = receiveCollection( originalState, testAction );
		expect( newState ).not.toBe( originalState );
		expect(
			newState[ 'wc/blocks' ][ 'products/attributes' ][ '[30,42]' ][
				'?something'
			]
		).toEqual( { items: [ 10, 20 ], headers: { foo: 'bar' } } );
	} );
} );

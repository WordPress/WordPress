/**
 * Internal dependencies
 */
import { getCollection, getCollectionHeader } from '../selectors';

const getHeaderMock = ( total ) => {
	const headers = { total };
	return {
		get: ( key ) => headers[ key ] || null,
		has: ( key ) => !! headers[ key ],
	};
};

const state = {
	'wc/blocks': {
		products: {
			'[]': {
				'?someQuery=2': {
					items: [ 'foo' ],
					headers: getHeaderMock( 22 ),
				},
			},
		},
		'products/attributes': {
			'[10]': {
				'?someQuery=2': {
					items: [ 'bar' ],
					headers: getHeaderMock( 42 ),
				},
			},
		},
		'products/attributes/terms': {
			'[10,20]': {
				'?someQuery=10': {
					items: [ 42 ],
					headers: getHeaderMock( 12 ),
				},
			},
		},
	},
};

describe( 'getCollection', () => {
	it( 'returns empty array when namespace does not exist in state', () => {
		expect( getCollection( state, 'invalid', 'products' ) ).toEqual( [] );
	} );
	it( 'returns empty array when resourceName does not exist in state', () => {
		expect( getCollection( state, 'wc/blocks', 'invalid' ) ).toEqual( [] );
	} );
	it( 'returns empty array when query does not exist in state', () => {
		expect( getCollection( state, 'wc/blocks', 'products' ) ).toEqual( [] );
	} );
	it( 'returns empty array when ids do not exist in state', () => {
		expect(
			getCollection(
				state,
				'wc/blocks',
				'products/attributes',
				'?someQuery=2',
				[ 20 ]
			)
		).toEqual( [] );
	} );
	describe( 'returns expected values for items existing in state', () => {
		test.each`
			resourceName                     | ids             | query                  | expected
			${ 'products' }                  | ${ [] }         | ${ { someQuery: 2 } }  | ${ [ 'foo' ] }
			${ 'products/attributes' }       | ${ [ 10 ] }     | ${ { someQuery: 2 } }  | ${ [ 'bar' ] }
			${ 'products/attributes/terms' } | ${ [ 10, 20 ] } | ${ { someQuery: 10 } } | ${ [ 42 ] }
		`(
			'for "$resourceName", "$ids", and "$query"',
			( { resourceName, ids, query, expected } ) => {
				expect(
					getCollection(
						state,
						'wc/blocks',
						resourceName,
						query,
						ids
					)
				).toEqual( expected );
			}
		);
	} );
} );

describe( 'getCollectionHeader', () => {
	it(
		'returns undefined when there are headers but the specific header ' +
			'does not exist',
		() => {
			expect(
				getCollectionHeader(
					state,
					'invalid',
					'wc/blocks',
					'products',
					{
						someQuery: 2,
					}
				)
			).toBeUndefined();
		}
	);
	it( 'returns null when there are no headers for the given arguments', () => {
		expect( getCollectionHeader( state, 'wc/blocks', 'invalid' ) ).toBe(
			null
		);
	} );
	it( 'returns expected header when it exists', () => {
		expect(
			getCollectionHeader( state, 'total', 'wc/blocks', 'products', {
				someQuery: 2,
			} )
		).toBe( 22 );
	} );
} );

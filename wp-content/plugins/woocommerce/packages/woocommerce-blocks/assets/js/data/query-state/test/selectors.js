/**
 * External dependencies
 */
import deepFreeze from 'deep-freeze';

/**
 * Internal dependencies
 */
import { getValueForQueryKey, getValueForQueryContext } from '../selectors';

const testState = deepFreeze( {
	contexta: JSON.stringify( {
		foo: 'bar',
		cheese: 'pizza',
	} ),
} );

describe( 'getValueForQueryKey', () => {
	it(
		'returns provided default value when there is no state for the ' +
			'given context',
		() => {
			expect(
				getValueForQueryKey( testState, 'invalid', 'foo', 42 )
			).toBe( 42 );
		}
	);
	it(
		'returns provided default value when there is no value for the ' +
			'given context and queryKey',
		() => {
			expect(
				getValueForQueryKey( testState, 'contexta', 'pizza', 42 )
			).toBe( 42 );
		}
	);
	it( 'returns expected value when context and queryKey exist', () => {
		expect( getValueForQueryKey( testState, 'contexta', 'foo', 42 ) ).toBe(
			'bar'
		);
	} );
} );

describe( 'getValueForQueryContext', () => {
	it(
		'returns provided default value when there is no state for the ' +
			'given context',
		() => {
			expect( getValueForQueryContext( testState, 'invalid', 42 ) ).toBe(
				42
			);
		}
	);
	it(
		'returns expected value when selecting a context that exists in ' +
			'state',
		() => {
			expect(
				getValueForQueryContext( testState, 'contexta', 42 )
			).toEqual( JSON.parse( testState.contexta ) );
		}
	);
} );

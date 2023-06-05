/**
 * External dependencies
 */
import deepFreeze from 'deep-freeze';

/**
 * Internal dependencies
 */
import { getRoute, getRoutes } from '../selectors';

const mockHasFinishedResolution = jest.fn().mockReturnValue( false );
jest.mock( '@wordpress/data', () => ( {
	__esModule: true,
	createRegistrySelector: ( callback ) =>
		callback( () => ( {
			hasFinishedResolution: mockHasFinishedResolution,
		} ) ),
} ) );

const testState = deepFreeze( {
	routes: {
		'wc/blocks': {
			'products/attributes': {
				'wc/blocks/products/attributes': [],
			},
			'products/attributes/terms': {
				'wc/blocks/products/attributes/{attribute_id}/terms/{id}': [
					'attribute_id',
					'id',
				],
			},
		},
	},
} );

describe( 'getRoute', () => {
	const invokeTest =
		( namespace, resourceName, ids = [] ) =>
		() => {
			return getRoute( testState, namespace, resourceName, ids );
		};
	describe( 'with throwing errors', () => {
		beforeEach( () => mockHasFinishedResolution.mockReturnValue( true ) );
		it( 'throws an error if there is no route for the given namespace', () => {
			expect( invokeTest( 'invalid' ) ).toThrowError( /given namespace/ );
		} );
		it(
			'throws an error if there are routes for the given namespace, but no ' +
				'route for the given resource',
			() => {
				expect( invokeTest( 'wc/blocks', 'invalid' ) ).toThrowError();
			}
		);
		it(
			'throws an error if there are routes for the given namespace and ' +
				'resource name, but no routes for the given ids',
			() => {
				expect(
					invokeTest( 'wc/blocks', 'products/attributes', [ 10 ] )
				).toThrowError( /number of ids you included/ );
			}
		);
	} );
	describe( 'with no throwing of errors if resolution has not finished', () => {
		beforeEach( () => mockHasFinishedResolution.mockReturnValue( false ) );
		it.each`
			description                                                                                  | args
			${ 'is no route for the given namespace' }                                                   | ${ [ 'invalid' ] }
			${ 'are no routes for the given namespace, but no route for the given resource' }            | ${ [ 'wc/blocks', 'invalid' ] }
			${ 'are routes for the given namespace and resource name, but no routes for the given ids' } | ${ [ 'wc/blocks', 'products/attributes', [ 10 ] ] }
		`( 'does not throw an error if there $description', ( { args } ) => {
			expect( invokeTest( ...args ) ).not.toThrowError();
		} );
	} );
	describe( 'returns expected value for given valid arguments', () => {
		test( 'when there is a route with no placeholders', () => {
			expect( invokeTest( 'wc/blocks', 'products/attributes' )() ).toBe(
				'wc/blocks/products/attributes'
			);
		} );
		test( 'when there is a route with placeholders', () => {
			expect(
				invokeTest(
					'wc/blocks',
					'products/attributes/terms',
					[ 10, 20 ]
				)()
			).toBe( 'wc/blocks/products/attributes/10/terms/20' );
		} );
	} );
} );

describe( 'getRoutes', () => {
	const invokeTest = ( namespace ) => () => {
		return getRoutes( testState, namespace );
	};
	it( 'throws an error if there is no route for the given namespace', () => {
		mockHasFinishedResolution.mockReturnValue( true );
		expect( invokeTest( 'invalid' ) ).toThrowError( /given namespace/ );
	} );
	it( 'returns expected routes for given namespace', () => {
		expect( invokeTest( 'wc/blocks' )() ).toEqual( [
			'wc/blocks/products/attributes',
			'wc/blocks/products/attributes/{attribute_id}/terms/{id}',
		] );
	} );
} );

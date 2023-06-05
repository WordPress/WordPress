// We need to disable the following eslint check as it's only applicable
// to testing-library/react not `react-test-renderer` used here
/* eslint-disable testing-library/await-async-query */
/**
 * External dependencies
 */
import TestRenderer, { act } from 'react-test-renderer';

/**
 * Internal dependencies
 */
import { useShallowEqual } from '../use-shallow-equal';

describe( 'useShallowEqual', () => {
	const TestComponent = ( { testValue } ) => {
		const newValue = useShallowEqual( testValue );
		return <div newValue={ newValue } />;
	};
	let renderer;
	beforeEach( () => ( renderer = null ) );
	it.each`
		testValueA                  | aType         | testValueB                  | bType
		${ { a: 'b', foo: 'bar' } } | ${ 'object' } | ${ { foo: 'bar', a: 'b' } } | ${ 'object' }
		${ [ 'b', 'bar' ] }         | ${ 'array' }  | ${ [ 'b', 'bar' ] }         | ${ 'array' }
		${ 1 }                      | ${ 'number' } | ${ 1 }                      | ${ 'number' }
		${ '1' }                    | ${ 'string' } | ${ '1' }                    | ${ 'string' }
		${ true }                   | ${ 'bool' }   | ${ true }                   | ${ 'bool' }
	`(
		'$testValueA ($aType) and $testValueB ($bType) are expected to be equal',
		( { testValueA, testValueB } ) => {
			let testPropValue;
			act( () => {
				renderer = TestRenderer.create(
					<TestComponent testValue={ testValueA } />
				);
			} );
			testPropValue = renderer.root.findByType( 'div' ).props.newValue;
			expect( testPropValue ).toBe( testValueA );
			// do update
			act( () => {
				renderer.update( <TestComponent testValue={ testValueB } /> );
			} );
			testPropValue = renderer.root.findByType( 'div' ).props.newValue;
			expect( testPropValue ).toBe( testValueA );
		}
	);

	it.each`
		testValueA                  | aType         | testValueB                  | bType
		${ { a: 'b', foo: 'bar' } } | ${ 'object' } | ${ { foo: 'bar', a: 'c' } } | ${ 'object' }
		${ [ 'b', 'bar' ] }         | ${ 'array' }  | ${ [ 'bar', 'b' ] }         | ${ 'array' }
		${ 1 }                      | ${ 'number' } | ${ '1' }                    | ${ 'string' }
		${ 1 }                      | ${ 'number' } | ${ 2 }                      | ${ 'number' }
		${ 1 }                      | ${ 'number' } | ${ true }                   | ${ 'bool' }
		${ 0 }                      | ${ 'number' } | ${ false }                  | ${ 'bool' }
	`(
		'$testValueA ($aType) and $testValueB ($bType) are expected to not be equal',
		( { testValueA, testValueB } ) => {
			let testPropValue;
			act( () => {
				renderer = TestRenderer.create(
					<TestComponent testValue={ testValueA } />
				);
			} );
			testPropValue = renderer.root.findByType( 'div' ).props.newValue;
			expect( testPropValue ).toBe( testValueA );
			// do update
			act( () => {
				renderer.update( <TestComponent testValue={ testValueB } /> );
			} );
			testPropValue = renderer.root.findByType( 'div' ).props.newValue;
			expect( testPropValue ).toBe( testValueB );
		}
	);
} );

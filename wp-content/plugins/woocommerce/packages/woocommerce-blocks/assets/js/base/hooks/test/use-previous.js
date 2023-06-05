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
import { usePrevious } from '../use-previous';

describe( 'usePrevious', () => {
	const TestComponent = ( { testValue, validation } ) => {
		const previousValue = usePrevious( testValue, validation );
		return <div testValue={ testValue } previousValue={ previousValue } />;
	};

	let renderer;
	beforeEach( () => ( renderer = null ) );

	it( 'should be undefined at first pass', () => {
		act( () => {
			renderer = TestRenderer.create( <TestComponent testValue={ 1 } /> );
		} );
		const testValue = renderer.root.findByType( 'div' ).props.testValue;
		const testPreviousValue =
			renderer.root.findByType( 'div' ).props.previousValue;

		expect( testValue ).toBe( 1 );
		expect( testPreviousValue ).toBe( undefined );
	} );

	it( 'test new and previous value', () => {
		let testValue;
		let testPreviousValue;
		act( () => {
			renderer = TestRenderer.create( <TestComponent testValue={ 1 } /> );
		} );

		act( () => {
			renderer.update( <TestComponent testValue={ 2 } /> );
		} );
		testValue = renderer.root.findByType( 'div' ).props.testValue;
		testPreviousValue =
			renderer.root.findByType( 'div' ).props.previousValue;
		expect( testValue ).toBe( 2 );
		expect( testPreviousValue ).toBe( 1 );

		act( () => {
			renderer.update( <TestComponent testValue={ 3 } /> );
		} );
		testValue = renderer.root.findByType( 'div' ).props.testValue;
		testPreviousValue =
			renderer.root.findByType( 'div' ).props.previousValue;
		expect( testValue ).toBe( 3 );
		expect( testPreviousValue ).toBe( 2 );
	} );

	it( 'should not update value if validation fails', () => {
		let testValue;
		let testPreviousValue;
		act( () => {
			renderer = TestRenderer.create(
				<TestComponent testValue={ 1 } validation={ Number.isFinite } />
			);
		} );

		act( () => {
			renderer.update(
				<TestComponent testValue="abc" validation={ Number.isFinite } />
			);
		} );
		testValue = renderer.root.findByType( 'div' ).props.testValue;
		testPreviousValue =
			renderer.root.findByType( 'div' ).props.previousValue;
		expect( testValue ).toBe( 'abc' );
		expect( testPreviousValue ).toBe( 1 );

		act( () => {
			renderer.update(
				<TestComponent testValue={ 3 } validation={ Number.isFinite } />
			);
		} );
		testValue = renderer.root.findByType( 'div' ).props.testValue;
		testPreviousValue =
			renderer.root.findByType( 'div' ).props.previousValue;
		expect( testValue ).toBe( 3 );
		expect( testPreviousValue ).toBe( 1 );
	} );
} );

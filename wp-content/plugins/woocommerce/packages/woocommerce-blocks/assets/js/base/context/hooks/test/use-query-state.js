/**
 * External dependencies
 */
import TestRenderer, { act } from 'react-test-renderer';
import { createRegistry, RegistryProvider } from '@wordpress/data';
import { QUERY_STATE_STORE_KEY as storeKey } from '@woocommerce/block-data';

/**
 * Internal dependencies
 */
import {
	useQueryStateByContext,
	useQueryStateByKey,
	useSynchronizedQueryState,
} from '../use-query-state';

jest.mock( '@woocommerce/block-data', () => ( {
	__esModule: true,
	QUERY_STATE_STORE_KEY: 'test/store',
} ) );

describe( 'Testing Query State Hooks', () => {
	let registry, mocks;
	beforeAll( () => {
		registry = createRegistry();
		mocks = {};
	} );
	/**
	 * Test helper to return a tuple containing the expected query value and the
	 * expected query state action creator from the given rendered test instance.
	 *
	 * @param {Object} testRenderer An instance of the created test component.
	 *
	 * @return {Array} A tuple containing the expected query value as the first
	 *                 element and the expected query action creator as the
	 *                 second argument.
	 */
	const getProps = ( testRenderer ) => {
		//eslint-disable-next-line testing-library/await-async-query
		const props = testRenderer.root.findByType( 'div' ).props;
		return [ props.queryState, props.setQueryState ];
	};

	/**
	 * Returns the given component wrapped in the registry provider for
	 * instantiating using the TestRenderer using the current prepared registry
	 * for the TestRenderer to instantiate with.
	 *
	 * @param {*}      Component The test component to wrap.
	 * @param {Object} props     Props to feed the wrapped component.
	 *
	 * @return {*} Wrapped component.
	 */
	const getWrappedComponent = ( Component, props ) => (
		<RegistryProvider value={ registry }>
			<Component { ...props } />
		</RegistryProvider>
	);

	/**
	 * Returns a TestComponent for the provided hook to test with, and the
	 * expected PropKeys for obtaining the values to be fed to the hook as
	 * arguments.
	 *
	 * @param {Function} hookTested      The hook being tested to use in the
	 *                                   test comopnent.
	 * @param {Array}    propKeysForArgs An array of keys for the props that
	 *                                   will be used on the test component that
	 *                                   will have values fed to the tested
	 *                                   hook.
	 *
	 * @return {*}  A component ready for testing with!
	 */
	const getTestComponent = ( hookTested, propKeysForArgs ) => ( props ) => {
		const args = propKeysForArgs.map( ( key ) => props[ key ] );
		const [ queryValue, setQueryValue ] = hookTested( ...args );
		return (
			<div queryState={ queryValue } setQueryState={ setQueryValue } />
		);
	};

	/**
	 * A helper for setting up the `mocks` object and the `registry` mock before
	 * each test.
	 *
	 * @param {string} actionMockName   This should be the name of the action
	 *                                  that the hook returns. This will be
	 *                                  mocked using `mocks.action` when
	 *                                  registered in the mock registry.
	 * @param {string} selectorMockName This should be the mame of the selector
	 *                                  that the hook uses. This will be mocked
	 *                                  using `mocks.selector` when registered
	 *                                  in the mock registry.
	 */
	const setupMocks = ( actionMockName, selectorMockName ) => {
		mocks.action = jest.fn().mockReturnValue( { type: 'testAction' } );
		mocks.selector = jest.fn().mockReturnValue( { foo: 'bar' } );
		registry.registerStore( storeKey, {
			reducer: () => ( {} ),
			actions: {
				[ actionMockName ]: mocks.action,
			},
			selectors: {
				[ selectorMockName ]: mocks.selector,
			},
		} );
	};
	describe( 'useQueryStateByContext', () => {
		const TestComponent = getTestComponent( useQueryStateByContext, [
			'context',
		] );
		let renderer;
		beforeEach( () => {
			renderer = null;
			setupMocks( 'setValueForQueryContext', 'getValueForQueryContext' );
		} );
		afterEach( () => {
			act( () => {
				renderer.unmount();
			} );
		} );
		it(
			'calls useSelect with the provided context and returns expected' +
				' values',
			() => {
				const { action, selector } = mocks;
				act( () => {
					renderer = TestRenderer.create(
						getWrappedComponent( TestComponent, {
							context: 'test-context',
						} )
					);
				} );
				const [ queryState, setQueryState ] = getProps( renderer );
				// the {} is because all selectors are called internally in the
				// registry with the first argument being the state which is empty.
				expect( selector ).toHaveBeenLastCalledWith(
					{},
					'test-context',
					undefined
				);
				expect( queryState ).toEqual( { foo: 'bar' } );
				expect( action ).not.toHaveBeenCalled();

				//execute dispatcher and make sure it's called.
				act( () => {
					setQueryState( { foo: 'bar' } );
				} );
				expect( action ).toHaveBeenCalledWith( 'test-context', {
					foo: 'bar',
				} );
			}
		);
	} );
	describe( 'useQueryStateByKey', () => {
		const TestComponent = getTestComponent( useQueryStateByKey, [
			'queryKey',
			undefined,
			'context',
		] );
		let renderer;
		beforeEach( () => {
			renderer = null;
			setupMocks( 'setQueryValue', 'getValueForQueryKey' );
		} );
		afterEach( () => {
			act( () => {
				renderer.unmount();
			} );
		} );
		it(
			'calls useSelect with the provided context and returns expected' +
				' values',
			() => {
				const { selector, action } = mocks;
				act( () => {
					renderer = TestRenderer.create(
						getWrappedComponent( TestComponent, {
							context: 'test-context',
							queryKey: 'someValue',
						} )
					);
				} );
				const [ queryState, setQueryState ] = getProps( renderer );
				// the {} is because all selectors are called internally in the
				// registry with the first argument being the state which is empty.
				expect( selector ).toHaveBeenLastCalledWith(
					{},
					'test-context',
					'someValue',
					undefined
				);
				expect( queryState ).toEqual( { foo: 'bar' } );
				expect( action ).not.toHaveBeenCalled();

				//execute dispatcher and make sure it's called.
				act( () => {
					setQueryState( { foo: 'bar' } );
				} );
				expect( action ).toHaveBeenCalledWith(
					'test-context',
					'someValue',
					{ foo: 'bar' }
				);
			}
		);
	} );
	// Note: these tests only add partial coverage because the state is not
	// actually updated by the action dispatch via our mocks.
	describe( 'useSynchronizedQueryState', () => {
		const TestComponent = getTestComponent( useSynchronizedQueryState, [
			'synchronizedQuery',
			'context',
		] );
		const initialQuery = { a: 'b' };
		let renderer;
		beforeEach( () => {
			setupMocks( 'setValueForQueryContext', 'getValueForQueryContext' );
		} );
		it( 'returns provided query state on initial render', () => {
			const { action, selector } = mocks;
			act( () => {
				renderer = TestRenderer.create(
					getWrappedComponent( TestComponent, {
						context: 'test-context',
						synchronizedQuery: initialQuery,
					} )
				);
			} );
			const [ queryState ] = getProps( renderer );
			expect( queryState ).toBe( initialQuery );
			expect( selector ).toHaveBeenLastCalledWith(
				{},
				'test-context',
				undefined
			);
			expect( action ).toHaveBeenCalledWith( 'test-context', {
				foo: 'bar',
				a: 'b',
			} );
		} );
		it( 'returns merged queryState on subsequent render', () => {
			act( () => {
				renderer.update(
					getWrappedComponent( TestComponent, {
						context: 'test-context',
						synchronizedQuery: initialQuery,
					} )
				);
			} );
			// note our test doesn't interact with an actual reducer so the
			// store state is not updated. Here we're just verifying that
			// what is is returned by the state selector mock is returned.
			// However we DO expect this to be a new object.
			const [ queryState ] = getProps( renderer );
			expect( queryState ).not.toBe( initialQuery );
			expect( queryState ).toEqual( { foo: 'bar' } );
		} );
	} );
} );

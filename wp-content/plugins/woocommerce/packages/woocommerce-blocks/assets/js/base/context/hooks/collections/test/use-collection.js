/**
 * External dependencies
 */
import TestRenderer, { act } from 'react-test-renderer';
import { createRegistry, RegistryProvider } from '@wordpress/data';
import { Component as ReactComponent } from '@wordpress/element';
import { COLLECTIONS_STORE_KEY as storeKey } from '@woocommerce/block-data';

/**
 * Internal dependencies
 */
import { useCollection } from '../use-collection';

jest.mock( '@woocommerce/block-data', () => ( {
	__esModule: true,
	COLLECTIONS_STORE_KEY: 'test/store',
} ) );

class TestErrorBoundary extends ReactComponent {
	constructor( props ) {
		super( props );
		this.state = { hasError: false, error: {} };
	}
	static getDerivedStateFromError( error ) {
		// Update state so the next render will show the fallback UI.
		return { hasError: true, error };
	}

	render() {
		if ( this.state.hasError ) {
			return <div error={ this.state.error } />;
		}

		return this.props.children;
	}
}

describe( 'useCollection', () => {
	let registry, mocks, renderer;
	const getProps = ( testRenderer ) => {
		const { results, isLoading } =
			testRenderer.root.findByType( 'div' ).props; //eslint-disable-line testing-library/await-async-query
		return {
			results,
			isLoading,
		};
	};

	const getWrappedComponents = ( Component, props ) => (
		<RegistryProvider value={ registry }>
			<TestErrorBoundary>
				<Component { ...props } />
			</TestErrorBoundary>
		</RegistryProvider>
	);

	const getTestComponent =
		() =>
		( { options } ) => {
			const items = useCollection( options );
			return <div { ...items } />;
		};

	const setUpMocks = () => {
		mocks = {
			selectors: {
				getCollectionError: jest.fn().mockReturnValue( false ),
				getCollection: jest
					.fn()
					.mockImplementation( () => ( { foo: 'bar' } ) ),
				hasFinishedResolution: jest.fn().mockReturnValue( true ),
			},
		};
		registry.registerStore( storeKey, {
			reducer: () => ( {} ),
			selectors: mocks.selectors,
		} );
	};

	beforeEach( () => {
		registry = createRegistry();
		mocks = {};
		renderer = null;
		setUpMocks();
	} );
	it(
		'should throw an error if an options object is provided without ' +
			'a namespace property',
		() => {
			const TestComponent = getTestComponent();
			act( () => {
				renderer = TestRenderer.create(
					getWrappedComponents( TestComponent, {
						options: {
							resourceName: 'products',
							query: { bar: 'foo' },
						},
					} )
				);
			} );
			//eslint-disable-next-line testing-library/await-async-query
			const props = renderer.root.findByType( 'div' ).props;
			expect( props.error.message ).toMatch( /options object/ );
			expect( console ).toHaveErrored( /your React components:/ );
			renderer.unmount();
		}
	);
	it(
		'should throw an error if an options object is provided without ' +
			'a resourceName property',
		() => {
			const TestComponent = getTestComponent();
			act( () => {
				renderer = TestRenderer.create(
					getWrappedComponents( TestComponent, {
						options: {
							namespace: 'test/store',
							query: { bar: 'foo' },
						},
					} )
				);
			} );
			//eslint-disable-next-line testing-library/await-async-query
			const props = renderer.root.findByType( 'div' ).props;
			expect( props.error.message ).toMatch( /options object/ );
			expect( console ).toHaveErrored( /your React components:/ );
			renderer.unmount();
		}
	);
	it(
		'should return expected behaviour for equivalent query on props ' +
			'across renders',
		() => {
			const TestComponent = getTestComponent();
			act( () => {
				renderer = TestRenderer.create(
					getWrappedComponents( TestComponent, {
						options: {
							namespace: 'test/store',
							resourceName: 'products',
							query: { bar: 'foo' },
						},
					} )
				);
			} );
			const { results } = getProps( renderer );
			// rerender
			act( () => {
				renderer.update(
					getWrappedComponents( TestComponent, {
						options: {
							namespace: 'test/store',
							resourceName: 'products',
							query: { bar: 'foo' },
						},
					} )
				);
			} );
			// re-render should result in same products object because although
			// query-state is a different instance, it's still equivalent.
			const { results: newResults } = getProps( renderer );
			expect( newResults ).toBe( results );
			// now let's change the query passed through to verify new object
			// is created.
			// remember this won't actually change the results because the mock
			// selector is returning an equivalent object when it is called,
			// however it SHOULD be a new object instance.
			act( () => {
				renderer.update(
					getWrappedComponents( TestComponent, {
						options: {
							namespace: 'test/store',
							resourceName: 'products',
							query: { foo: 'bar' },
						},
					} )
				);
			} );
			const { results: resultsVerification } = getProps( renderer );
			expect( resultsVerification ).not.toBe( results );
			expect( resultsVerification ).toEqual( results );
			renderer.unmount();
		}
	);
	it(
		'should return expected behaviour for equivalent resourceValues on' +
			' props across renders',
		() => {
			const TestComponent = getTestComponent();
			act( () => {
				renderer = TestRenderer.create(
					getWrappedComponents( TestComponent, {
						options: {
							namespace: 'test/store',
							resourceName: 'products',
							resourceValues: [ 10, 20 ],
						},
					} )
				);
			} );
			const { results } = getProps( renderer );
			// rerender
			act( () => {
				renderer.update(
					getWrappedComponents( TestComponent, {
						options: {
							namespace: 'test/store',
							resourceName: 'products',
							resourceValues: [ 10, 20 ],
						},
					} )
				);
			} );
			// re-render should result in same products object because although
			// query-state is a different instance, it's still equivalent.
			const { results: newResults } = getProps( renderer );
			expect( newResults ).toBe( results );
			// now let's change the query passed through to verify new object
			// is created.
			// remember this won't actually change the results because the mock
			// selector is returning an equivalent object when it is called,
			// however it SHOULD be a new object instance.
			act( () => {
				renderer.update(
					getWrappedComponents( TestComponent, {
						options: {
							namespace: 'test/store',
							resourceName: 'products',
							resourceValues: [ 20, 10 ],
						},
					} )
				);
			} );
			const { results: resultsVerification } = getProps( renderer );
			expect( resultsVerification ).not.toBe( results );
			expect( resultsVerification ).toEqual( results );
			renderer.unmount();
		}
	);
	it( 'should return previous query results if `shouldSelect` is false', () => {
		mocks.selectors.getCollection.mockImplementation(
			( state, ...args ) => {
				return args;
			}
		);
		const TestComponent = getTestComponent();
		act( () => {
			renderer = TestRenderer.create(
				getWrappedComponents( TestComponent, {
					options: {
						namespace: 'test/store',
						resourceName: 'products',
						resourceValues: [ 10, 20 ],
					},
				} )
			);
		} );
		const { results } = getProps( renderer );
		// rerender but with shouldSelect to false
		act( () => {
			renderer.update(
				getWrappedComponents( TestComponent, {
					options: {
						namespace: 'test/store',
						resourceName: 'productsb',
						resourceValues: [ 10, 30 ],
						shouldSelect: false,
					},
				} )
			);
		} );
		const { results: results2 } = getProps( renderer );
		expect( results2 ).toBe( results );
		// expect 2 calls because internally, useSelect invokes callback twice
		// on mount.
		expect( mocks.selectors.getCollection ).toHaveBeenCalledTimes( 2 );

		// rerender again but set shouldSelect to true again and we should see
		// new results
		act( () => {
			renderer.update(
				getWrappedComponents( TestComponent, {
					options: {
						namespace: 'test/store',
						resourceName: 'productsb',
						resourceValues: [ 10, 30 ],
						shouldSelect: true,
					},
				} )
			);
		} );
		const { results: results3 } = getProps( renderer );
		expect( results3 ).not.toEqual( results );
		expect( results3 ).toEqual( [
			'test/store',
			'productsb',
			{},
			[ 10, 30 ],
		] );
	} );
} );

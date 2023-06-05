/**
 * External dependencies
 */
import TestRenderer, { act } from 'react-test-renderer';
import { createRegistry, RegistryProvider } from '@wordpress/data';
import { COLLECTIONS_STORE_KEY as storeKey } from '@woocommerce/block-data';

/**
 * Internal dependencies
 */
import { useStoreProducts } from '../use-store-products';

jest.mock( '@woocommerce/block-data', () => ( {
	__esModule: true,
	COLLECTIONS_STORE_KEY: 'test/store',
} ) );

describe( 'useStoreProducts', () => {
	let registry, mocks, renderer;
	const getProps = ( testRenderer ) => {
		const { products, totalProducts, productsLoading } =
			testRenderer.root.findByType( 'div' ).props; //eslint-disable-line testing-library/await-async-query
		return {
			products,
			totalProducts,
			productsLoading,
		};
	};

	const getWrappedComponents = ( Component, props ) => (
		<RegistryProvider value={ registry }>
			<Component { ...props } />
		</RegistryProvider>
	);

	const getTestComponent =
		() =>
		( { query } ) => {
			const items = useStoreProducts( query );
			return <div { ...items } />;
		};

	const setUpMocks = () => {
		mocks = {
			selectors: {
				getCollectionError: jest.fn().mockReturnValue( false ),
				getCollection: jest
					.fn()
					.mockImplementation( () => ( { foo: 'bar' } ) ),
				getCollectionHeader: jest.fn().mockReturnValue( 22 ),
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
		'should return expected behaviour for equivalent query on props ' +
			'across renders',
		() => {
			const TestComponent = getTestComponent();
			act( () => {
				renderer = TestRenderer.create(
					getWrappedComponents( TestComponent, {
						query: { bar: 'foo' },
					} )
				);
			} );
			const { products } = getProps( renderer );
			// rerender
			act( () => {
				renderer.update(
					getWrappedComponents( TestComponent, {
						query: { bar: 'foo' },
					} )
				);
			} );
			// re-render should result in same products object because although
			// query-state is a different instance, it's still equivalent.
			const { products: newProducts } = getProps( renderer );
			expect( newProducts ).toBe( products );
			// now let's change the query passed through to verify new object
			// is created.
			// remember this won't actually change the results because the mock
			// selector is returning an equivalent object when it is called,
			// however it SHOULD be a new object instance.
			act( () => {
				renderer.update(
					getWrappedComponents( TestComponent, {
						query: { foo: 'bar' },
					} )
				);
			} );
			const { products: productsVerification } = getProps( renderer );
			expect( productsVerification ).not.toBe( products );
			expect( productsVerification ).toEqual( products );
			renderer.unmount();
		}
	);
} );

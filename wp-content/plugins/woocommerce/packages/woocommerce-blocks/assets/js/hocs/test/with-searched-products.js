// We need to disable the following eslint check as it's only applicable
// to testing-library/react not `react-test-renderer` used here
/* eslint-disable testing-library/await-async-query */
/**
 * External dependencies
 */
import TestRenderer, { act } from 'react-test-renderer';
import * as mockUtils from '@woocommerce/editor-components/utils';
import * as mockUseDebounce from 'use-debounce';

/**
 * Internal dependencies
 */
import withSearchedProducts from '../with-searched-products';

jest.mock( '@woocommerce/block-settings', () => ( {
	__esModule: true,
	blocksConfig: {
		productCount: 101,
	},
} ) );

// Mock the getProducts values for tests.
mockUtils.getProducts = jest.fn().mockImplementation( () =>
	Promise.resolve( [
		{ id: 10, name: 'foo', parent: 0 },
		{ id: 20, name: 'bar', parent: 0 },
	] )
);

// Add a mock implementation of debounce for testing so we can spy on the onSearch call.
mockUseDebounce.useDebouncedCallback = jest
	.fn()
	.mockImplementation( ( search ) => () => mockUtils.getProducts( search ) );

describe( 'withSearchedProducts Component', () => {
	const { getProducts } = mockUtils;
	afterEach( () => {
		mockUseDebounce.useDebouncedCallback.mockClear();
		mockUtils.getProducts.mockClear();
	} );
	const TestComponent = withSearchedProducts(
		( { selected, products, isLoading, onSearch } ) => {
			return (
				<div
					products={ products }
					selected={ selected }
					isLoading={ isLoading }
					onSearch={ onSearch }
				/>
			);
		}
	);
	describe( 'lifecycle tests', () => {
		const selected = [ 10 ];
		let props, renderer;

		act( () => {
			renderer = TestRenderer.create(
				<TestComponent selected={ selected } />
			);
		} );

		it( 'has expected values for props', () => {
			props = renderer.root.findByType( 'div' ).props;
			expect( props.selected ).toEqual( selected );
			expect( props.products ).toEqual( [
				{ id: 10, name: 'foo', parent: 0 },
				{ id: 20, name: 'bar', parent: 0 },
			] );
		} );

		it( 'debounce and getProducts is called on search event', async () => {
			props = renderer.root.findByType( 'div' ).props;

			act( () => {
				props.onSearch();
			} );

			expect( mockUseDebounce.useDebouncedCallback ).toHaveBeenCalled();
			expect( getProducts ).toHaveBeenCalledTimes( 1 );
		} );
	} );
} );

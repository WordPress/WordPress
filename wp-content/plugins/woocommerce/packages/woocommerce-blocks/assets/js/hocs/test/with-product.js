// We need to disable the following eslint check as it's only applicable
// to testing-library/react not `react-test-renderer` used here
/* eslint-disable testing-library/await-async-query */
/**
 * External dependencies
 */
import TestRenderer from 'react-test-renderer';
import * as mockUtils from '@woocommerce/editor-components/utils';

/**
 * Internal dependencies
 */
import withProduct from '../with-product';
import * as mockBaseUtils from '../../base/utils/errors';

jest.mock( '@woocommerce/editor-components/utils', () => ( {
	getProduct: jest.fn(),
} ) );

jest.mock( '../../base/utils/errors', () => ( {
	formatError: jest.fn(),
} ) );

const mockProduct = { name: 'T-Shirt' };
const attributes = { productId: 1 };
const TestComponent = withProduct( ( props ) => {
	return (
		<div
			error={ props.error }
			getProduct={ props.getProduct }
			isLoading={ props.isLoading }
			product={ props.product }
		/>
	);
} );
const render = () => {
	return TestRenderer.create( <TestComponent attributes={ attributes } /> );
};

describe( 'withProduct Component', () => {
	let renderer;
	afterEach( () => {
		mockUtils.getProduct.mockReset();
	} );

	describe( 'lifecycle events', () => {
		beforeEach( () => {
			mockUtils.getProduct.mockImplementation( () => Promise.resolve() );
			renderer = render();
		} );

		it( 'getProduct is called on mount with passed in product id', () => {
			const { getProduct } = mockUtils;

			expect( getProduct ).toHaveBeenCalledWith( attributes.productId );
			expect( getProduct ).toHaveBeenCalledTimes( 1 );
		} );

		it( 'getProduct is called on component update', () => {
			const { getProduct } = mockUtils;
			const newAttributes = { ...attributes, productId: 2 };
			renderer.update( <TestComponent attributes={ newAttributes } /> );

			expect( getProduct ).toHaveBeenNthCalledWith(
				2,
				newAttributes.productId
			);
			expect( getProduct ).toHaveBeenCalledTimes( 2 );
		} );

		it( 'getProduct is hooked to the prop', () => {
			const { getProduct } = mockUtils;
			const props = renderer.root.findByType( 'div' ).props;

			props.getProduct();

			expect( getProduct ).toHaveBeenCalledTimes( 2 );
		} );
	} );

	describe( 'when the API returns product data', () => {
		beforeEach( () => {
			mockUtils.getProduct.mockImplementation( ( productId ) =>
				Promise.resolve( { ...mockProduct, id: productId } )
			);
			renderer = render();
		} );

		it( 'sets the product props', () => {
			const props = renderer.root.findByType( 'div' ).props;

			expect( props.error ).toBeNull();
			expect( typeof props.getProduct ).toBe( 'function' );
			expect( props.isLoading ).toBe( false );
			expect( props.product ).toEqual( {
				...mockProduct,
				id: attributes.productId,
			} );
		} );
	} );

	describe( 'when the API returns an error', () => {
		const error = { message: 'There was an error.' };
		const getProductPromise = Promise.reject( error );
		const formattedError = { message: 'There was an error.', type: 'api' };

		beforeEach( () => {
			mockUtils.getProduct.mockImplementation( () => getProductPromise );
			mockBaseUtils.formatError.mockImplementation(
				() => formattedError
			);
			renderer = render();
		} );

		test( 'sets the error prop', async () => {
			await expect( () => getProductPromise() ).toThrow();

			const { formatError } = mockBaseUtils;
			const props = renderer.root.findByType( 'div' ).props;

			expect( formatError ).toHaveBeenCalledWith( error );
			expect( formatError ).toHaveBeenCalledTimes( 1 );
			expect( props.error ).toEqual( formattedError );
			expect( typeof props.getProduct ).toBe( 'function' );
			expect( props.isLoading ).toBe( false );
			expect( props.product ).toBeNull();
		} );
	} );
} );

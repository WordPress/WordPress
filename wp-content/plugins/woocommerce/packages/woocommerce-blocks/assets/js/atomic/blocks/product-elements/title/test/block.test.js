/**
 * External dependencies
 */
import { render } from '@testing-library/react';
import { ProductDataContextProvider } from '@woocommerce/shared-context';

/**
 * Internal dependencies
 */
import { Block } from '../block';

const product = {
	id: 1,
	name: 'Test product',
	permalink: 'https://test.com/product/test-product/',
};

describe( 'Product Title Block', () => {
	describe( 'without product link', () => {
		test( 'should render the product title without an anchor wrapper', () => {
			const component = render(
				<ProductDataContextProvider product={ product }>
					<Block showProductLink={ false } />
				</ProductDataContextProvider>
			);

			const productName = component.getByText( product.name );
			const anchor = productName.closest( 'a' );

			expect( anchor ).toBe( null );
		} );
	} );

	describe( 'with product link', () => {
		test( 'should render an anchor with the product title', () => {
			const component = render(
				<ProductDataContextProvider product={ product }>
					<Block showProductLink={ true } />
				</ProductDataContextProvider>
			);

			const productName = component.getByText( product.name );
			const anchor = productName.closest( 'a' );

			expect( anchor.getAttribute( 'href' ) ).toBe( product.permalink );
			expect( anchor.getAttribute( 'target' ) ).toBeNull();
		} );

		test( 'should render an anchor with the product title and target blank', () => {
			const component = render(
				<ProductDataContextProvider product={ product }>
					<Block showProductLink={ true } linkTarget="_blank" />
				</ProductDataContextProvider>
			);

			const productName = component.getByText( product.name );
			const anchor = productName.closest( 'a' );

			expect( anchor.getAttribute( 'href' ) ).toBe( product.permalink );
			expect( anchor.getAttribute( 'target' ) ).toBe( '_blank' );
		} );
	} );
} );

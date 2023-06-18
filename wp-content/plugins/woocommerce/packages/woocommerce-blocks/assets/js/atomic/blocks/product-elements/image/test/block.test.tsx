/**
 * External dependencies
 */
import { render, fireEvent } from '@testing-library/react';
import { ProductDataContextProvider } from '@woocommerce/shared-context';
import { ProductResponseItem } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { Block } from '../block';
import { ImageSizing } from '../types';

jest.mock( '@woocommerce/base-hooks', () => ( {
	__esModule: true,
	useStyleProps: jest.fn( () => ( {
		className: '',
		style: {},
	} ) ),
} ) );

const productWithoutImages: ProductResponseItem = {
	name: 'Test product',
	id: 1,
	permalink: 'http://test.com/product/test-product/',
	images: [],
	parent: 0,
	type: '',
	variation: '',
	sku: '',
	short_description: '',
	description: '',
	on_sale: false,
	prices: {
		currency_code: 'USD',
		currency_symbol: '',
		currency_minor_unit: 0,
		currency_decimal_separator: '',
		currency_thousand_separator: '',
		currency_prefix: '',
		currency_suffix: '',
		price: '',
		regular_price: '',
		sale_price: '',
		price_range: null,
	},
	price_html: '',
	average_rating: '',
	review_count: 0,
	categories: [],
	tags: [],
	attributes: [],
	variations: [],
	has_options: false,
	is_purchasable: false,
	is_in_stock: false,
	is_on_backorder: false,
	low_stock_remaining: null,
	sold_individually: false,
	add_to_cart: {
		text: '',
		description: '',
		url: '',
		minimum: 0,
		maximum: 0,
		multiple_of: 0,
	},
};

const productWithImages: ProductResponseItem = {
	name: 'Test product',
	id: 1,
	permalink: 'http://test.com/product/test-product/',
	images: [
		{
			id: 56,
			src: 'logo-1.jpg',
			thumbnail: 'logo-1-324x324.jpg',
			srcset: 'logo-1.jpg 800w, logo-1-300x300.jpg 300w, logo-1-150x150.jpg 150w, logo-1-768x767.jpg 768w, logo-1-324x324.jpg 324w, logo-1-416x415.jpg 416w, logo-1-100x100.jpg 100w',
			sizes: '(max-width: 800px) 100vw, 800px',
			name: 'logo-1.jpg',
			alt: '',
		},
		{
			id: 55,
			src: 'beanie-with-logo-1.jpg',
			thumbnail: 'beanie-with-logo-1-324x324.jpg',
			srcset: 'beanie-with-logo-1.jpg 800w, beanie-with-logo-1-300x300.jpg 300w, beanie-with-logo-1-150x150.jpg 150w, beanie-with-logo-1-768x768.jpg 768w, beanie-with-logo-1-324x324.jpg 324w, beanie-with-logo-1-416x416.jpg 416w, beanie-with-logo-1-100x100.jpg 100w',
			sizes: '(max-width: 800px) 100vw, 800px',
			name: 'beanie-with-logo-1.jpg',
			alt: '',
		},
	],
	parent: 0,
	type: '',
	variation: '',
	sku: '',
	short_description: '',
	description: '',
	on_sale: false,
	prices: {
		currency_code: 'USD',
		currency_symbol: '',
		currency_minor_unit: 0,
		currency_decimal_separator: '',
		currency_thousand_separator: '',
		currency_prefix: '',
		currency_suffix: '',
		price: '',
		regular_price: '',
		sale_price: '',
		price_range: null,
	},
	price_html: '',
	average_rating: '',
	review_count: 0,
	categories: [],
	tags: [],
	attributes: [],
	variations: [],
	has_options: false,
	is_purchasable: false,
	is_in_stock: false,
	is_on_backorder: false,
	low_stock_remaining: null,
	sold_individually: false,
	add_to_cart: {
		text: '',
		description: '',
		url: '',
		minimum: 0,
		maximum: 0,
		multiple_of: 0,
	},
};

describe( 'Product Image Block', () => {
	describe( 'with product link', () => {
		test( 'should render an anchor with the product image', () => {
			const component = render(
				<ProductDataContextProvider
					product={ productWithImages }
					isLoading={ false }
				>
					<Block
						showProductLink={ true }
						productId={ productWithImages.id }
						showSaleBadge={ false }
						saleBadgeAlign={ 'left' }
						imageSizing={ ImageSizing.SINGLE }
						isDescendentOfQueryLoop={ false }
					/>
				</ProductDataContextProvider>
			);

			// use testId as alt is added after image is loaded
			const image = component.getByTestId( 'product-image' );
			fireEvent.load( image );

			const productImage = component.getByAltText(
				productWithImages.name
			);
			expect( productImage.getAttribute( 'src' ) ).toBe(
				productWithImages.images[ 0 ].src
			);

			const anchor = productImage.closest( 'a' );
			expect( anchor?.getAttribute( 'href' ) ).toBe(
				productWithImages.permalink
			);
		} );

		test( 'should render an anchor with the placeholder image', () => {
			const component = render(
				<ProductDataContextProvider
					product={ productWithoutImages }
					isLoading={ false }
				>
					<Block
						showProductLink={ true }
						productId={ productWithoutImages.id }
						showSaleBadge={ false }
						saleBadgeAlign={ 'left' }
						imageSizing={ ImageSizing.SINGLE }
						isDescendentOfQueryLoop={ false }
					/>
				</ProductDataContextProvider>
			);

			const placeholderImage = component.getByAltText( '' );
			expect( placeholderImage.getAttribute( 'src' ) ).toBe(
				'placeholder.jpg'
			);

			const anchor = placeholderImage.closest( 'a' );
			expect( anchor?.getAttribute( 'href' ) ).toBe(
				productWithoutImages.permalink
			);
			expect( anchor?.getAttribute( 'aria-label' ) ).toBe(
				`Link to ${ productWithoutImages.name }`
			);
		} );
	} );

	describe( 'without product link', () => {
		test( 'should render the product image without an anchor wrapper', () => {
			const component = render(
				<ProductDataContextProvider
					product={ productWithImages }
					isLoading={ false }
				>
					<Block
						showProductLink={ false }
						productId={ productWithImages.id }
						showSaleBadge={ false }
						saleBadgeAlign={ 'left' }
						imageSizing={ ImageSizing.SINGLE }
						isDescendentOfQueryLoop={ false }
					/>
				</ProductDataContextProvider>
			);
			const image = component.getByTestId( 'product-image' );
			fireEvent.load( image );

			const productImage = component.getByAltText(
				productWithImages.name
			);
			expect( productImage.getAttribute( 'src' ) ).toBe(
				productWithImages.images[ 0 ].src
			);

			const anchor = productImage.closest( 'a' );
			expect( anchor ).toBe( null );
		} );

		test( 'should render the placeholder image without an anchor wrapper', () => {
			const component = render(
				<ProductDataContextProvider
					product={ productWithoutImages }
					isLoading={ false }
				>
					<Block
						showProductLink={ false }
						productId={ productWithoutImages.id }
						showSaleBadge={ false }
						saleBadgeAlign={ 'left' }
						imageSizing={ ImageSizing.SINGLE }
						isDescendentOfQueryLoop={ false }
					/>
				</ProductDataContextProvider>
			);

			const placeholderImage = component.getByAltText( '' );
			expect( placeholderImage.getAttribute( 'src' ) ).toBe(
				'placeholder.jpg'
			);

			const anchor = placeholderImage.closest( 'a' );
			expect( anchor ).toBe( null );
		} );
	} );

	describe( 'without image', () => {
		test( 'should render the placeholder with no inline width or height attributes', () => {
			const component = render(
				<ProductDataContextProvider
					product={ productWithoutImages }
					isLoading={ false }
				>
					<Block
						showProductLink={ true }
						productId={ productWithoutImages.id }
						showSaleBadge={ false }
						saleBadgeAlign={ 'left' }
						imageSizing={ ImageSizing.SINGLE }
						isDescendentOfQueryLoop={ false }
					/>
				</ProductDataContextProvider>
			);

			const placeholderImage = component.getByAltText( '' );
			expect( placeholderImage.getAttribute( 'src' ) ).toBe(
				'placeholder.jpg'
			);
			expect( placeholderImage.getAttribute( 'width' ) ).toBe( null );
			expect( placeholderImage.getAttribute( 'height' ) ).toBe( null );
		} );
	} );
} );

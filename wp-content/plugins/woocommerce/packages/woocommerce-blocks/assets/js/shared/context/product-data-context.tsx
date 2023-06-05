/**
 * External dependencies
 */
import { ProductResponseItem } from '@woocommerce/types';
import { createContext, useContext } from '@wordpress/element';

/**
 * Default product shape matching API response.
 */
const defaultProductData: ProductResponseItem = {
	id: 0,
	name: '',
	parent: 0,
	type: 'simple',
	variation: '',
	permalink: '',
	sku: '',
	short_description: '',
	description: '',
	on_sale: false,
	prices: {
		currency_code: 'USD',
		currency_symbol: '$',
		currency_minor_unit: 2,
		currency_decimal_separator: '.',
		currency_thousand_separator: ',',
		currency_prefix: '$',
		currency_suffix: '',
		price: '0',
		regular_price: '0',
		sale_price: '0',
		price_range: null,
	},
	price_html: '',
	average_rating: '0',
	review_count: 0,
	images: [],
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
		text: 'Add to cart',
		description: 'Add to cart',
		url: '',
		minimum: 1,
		maximum: 99,
		multiple_of: 1,
	},
};

/**
 * This context is used to pass product data down to all children blocks in a given tree.
 *
 * @member {Object} ProductDataContext A react context object
 */
const ProductDataContext = createContext( {
	product: defaultProductData,
	hasContext: false,
	isLoading: false,
} );

export const useProductDataContext = () => useContext( ProductDataContext );

interface ProductDataContextProviderProps {
	product: ProductResponseItem | null;
	children: JSX.Element | JSX.Element[];
	isLoading: boolean;
}

/**
 * This context is used to pass product data down to all children blocks in a given tree.
 *
 * @param {Object}   object           A react context object
 * @param {any|null} object.product   The product data to be passed down
 * @param {Object}   object.children  The product data to be passed down
 * @param {boolean}  object.isLoading The product data to be passed down
 */
export const ProductDataContextProvider = ( {
	product = null,
	children,
	isLoading,
}: ProductDataContextProviderProps ) => {
	const contextValue = {
		product: product || defaultProductData,
		isLoading,
		hasContext: true,
	};

	return (
		<ProductDataContext.Provider value={ contextValue }>
			{ isLoading ? (
				<div className="is-loading">{ children }</div>
			) : (
				children
			) }
		</ProductDataContext.Provider>
	);
};

/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import SortSelect from '@woocommerce/base-components/sort-select';
/**
 * Internal dependencies
 */
import './style.scss';
import { ProductSortSelectProps } from '../types';

const ProductSortSelect = ( {
	onChange,
	value,
}: ProductSortSelectProps ): JSX.Element => {
	return (
		<SortSelect
			className="wc-block-product-sort-select wc-block-components-product-sort-select"
			onChange={ onChange }
			options={ [
				{
					key: 'menu_order',
					label: __(
						'Default sorting',
						'woo-gutenberg-products-block'
					),
				},
				{
					key: 'popularity',
					label: __( 'Popularity', 'woo-gutenberg-products-block' ),
				},
				{
					key: 'rating',
					label: __(
						'Average rating',
						'woo-gutenberg-products-block'
					),
				},
				{
					key: 'date',
					label: __( 'Latest', 'woo-gutenberg-products-block' ),
				},
				{
					key: 'price',
					label: __(
						'Price: low to high',
						'woo-gutenberg-products-block'
					),
				},
				{
					key: 'price-desc',
					label: __(
						'Price: high to low',
						'woo-gutenberg-products-block'
					),
				},
			] }
			screenReaderLabel={ __(
				'Order products by',
				'woo-gutenberg-products-block'
			) }
			value={ value }
		/>
	);
};

export default ProductSortSelect;

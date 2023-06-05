/**
 * External dependencies
 */
import type { Story, Meta } from '@storybook/react';
import { currencyControl } from '@woocommerce/storybook-controls';

/**
 * Internal dependencies
 */
import ProductPrice, { ProductPriceProps } from '..';

const ALLOWED_ALIGN_VALUES = [ 'left', 'center', 'right' ];

export default {
	title: 'WooCommerce Blocks/@base-components/ProductPrice',
	component: ProductPrice,
	argTypes: {
		align: {
			control: { type: 'radio' },
			options: ALLOWED_ALIGN_VALUES,
		},
		currency: currencyControl,
	},
	args: {
		align: 'left',
		format: '<price/>',
		price: 3000,
	},
} as Meta< ProductPriceProps >;

const Template: Story< ProductPriceProps > = ( args ) => (
	<ProductPrice { ...args } />
);

export const Default = Template.bind( {} );
Default.args = {};

export const Sale = Template.bind( {} );
Sale.args = {
	regularPrice: 4500,
};

export const Range = Template.bind( {} );
Range.args = {
	maxPrice: 5000,
	minPrice: 3000,
};

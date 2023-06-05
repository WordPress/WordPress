/**
 * External dependencies
 */
import type { Story, Meta } from '@storybook/react';

/**
 * Internal dependencies
 */
import ProductName, { ProductNameProps } from '..';

export default {
	title: 'WooCommerce Blocks/@base-components/cart-checkout/ProductName',
	component: ProductName,
	args: {
		name: 'Test product',
		permalink: '#',
	},
} as Meta< ProductNameProps >;

const Template: Story< ProductNameProps > = ( args ) => (
	<ProductName { ...args } />
);

export const Default = Template.bind( {} );
Default.args = {
	disabled: false,
};

export const DisabledProduct = Template.bind( {} );
DisabledProduct.args = {
	disabled: true,
};

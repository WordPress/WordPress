/**
 * External dependencies
 */
import type { Story, Meta } from '@storybook/react';
import { currencies, currencyControl } from '@woocommerce/storybook-controls';

/**
 * Internal dependencies
 */
import Subtotal, { SubtotalProps } from '..';

export default {
	title: 'WooCommerce Blocks/Checkout Blocks/totals/Subtotal',
	component: Subtotal,
	argTypes: {
		currency: currencyControl,
	},
	args: {
		values: {
			total_items: '1000',
			total_items_tax: '200',
		},
	},
} as Meta< SubtotalProps >;

type StorybookSubtotalProps = SubtotalProps & { total_items: string };

const Template: Story< StorybookSubtotalProps > = ( args ) => {
	const totalItems = args.total_items;
	const values = {
		total_items: totalItems,
		total_items_tax: args.values.total_items_tax,
	};

	return (
		<Subtotal { ...args } currency={ args.currency } values={ values } />
	);
};

export const Default = Template.bind( {} );
Default.args = {
	currency: currencies.USD,
	total_items: '1000',
};

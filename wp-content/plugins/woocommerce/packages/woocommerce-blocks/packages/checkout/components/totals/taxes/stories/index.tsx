/**
 * External dependencies
 */
import type { Story, Meta } from '@storybook/react';
import { currencies, currencyControl } from '@woocommerce/storybook-controls';

/**
 * Internal dependencies
 */
import Taxes, { TotalsTaxesProps } from '..';

export default {
	title: 'WooCommerce Blocks/Checkout Blocks/totals/Taxes',
	component: Taxes,
	argTypes: {
		currency: currencyControl,
		showRateAfterTaxName: {
			table: { disable: true },
		},
	},
	args: {
		values: {
			tax_lines: [
				{
					name: 'Expensive tax fee',
					price: '1000',
					rate: '500',
				},
			],
			total_tax: '2000',
		},
	},
} as Meta< TotalsTaxesProps >;

const Template: Story< TotalsTaxesProps > = ( args ) => <Taxes { ...args } />;

export const Default = Template.bind( {} );
Default.args = {
	currency: currencies.USD,
};

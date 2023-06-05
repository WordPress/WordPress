/**
 * External dependencies
 */
import type { Story, Meta } from '@storybook/react';
import { currencies, currencyControl } from '@woocommerce/storybook-controls';

/**
 * Internal dependencies
 */
import Item, { TotalsItemProps } from '..';

export default {
	title: 'WooCommerce Blocks/Checkout Blocks/totals/Item',
	component: Item,
	argTypes: {
		currency: currencyControl,
		description: { control: { type: 'text' } },
	},
	args: {
		description: 'This item is so interesting',
		label: 'Interesting item',
		value: 2000,
	},
} as Meta< TotalsItemProps >;

const Template: Story< TotalsItemProps > = ( args ) => <Item { ...args } />;

export const Default = Template.bind( {} );
Default.args = {
	currency: currencies.USD,
	description: 'This item is so interesting',
};

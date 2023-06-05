/**
 * External dependencies
 */
import { useArgs } from '@storybook/client-api';
import type { Story, Meta } from '@storybook/react';

/**
 * Internal dependencies
 */
import QuantitySelector, { QuantitySelectorProps } from '..';

export default {
	title: 'WooCommerce Blocks/@base-components/QuantitySelector',
	component: QuantitySelector,
	args: {
		itemName: 'widgets',
		quantity: 1,
	},
} as Meta< QuantitySelectorProps >;

const Template: Story< QuantitySelectorProps > = ( args ) => {
	const [ {}, setArgs ] = useArgs();

	const onChange = ( newVal: number ) => {
		args.onChange?.( newVal );
		setArgs( { quantity: newVal } );
	};

	return <QuantitySelector { ...args } onChange={ onChange } />;
};

export const Default = Template.bind( {} );
Default.args = {};

export const Disabled = Template.bind( {} );
Disabled.args = {
	disabled: true,
};

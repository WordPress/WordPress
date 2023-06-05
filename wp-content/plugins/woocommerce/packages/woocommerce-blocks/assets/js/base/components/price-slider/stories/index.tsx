/**
 * External dependencies
 */
import type { Story, Meta } from '@storybook/react';
import { useState } from '@wordpress/element';
import { currencies, currencyControl } from '@woocommerce/storybook-controls';

/**
 * Internal dependencies
 */
import PriceSlider, { PriceSliderProps } from '..';

export default {
	title: 'WooCommerce Blocks/@base-components/PriceSlider',
	component: PriceSlider,
	args: {
		currency: currencies.USD,
		maxPrice: 5000,
		maxConstraint: 5000,
		minConstraint: 1000,
		minPrice: 1000,
		step: 250,
	},
	argTypes: {
		currency: currencyControl,
		maxPrice: { control: { disable: true } },
		minPrice: { control: { disable: true } },
	},
} as Meta< PriceSliderProps >;

const Template: Story< PriceSliderProps > = ( args ) => {
	const { maxPrice, minPrice, ...props } = args;
	// PriceSlider expects client to update min & max price, i.e. is a controlled component
	const [ min, setMin ] = useState( minPrice );
	const [ max, setMax ] = useState( maxPrice );

	return (
		<PriceSlider
			{ ...props }
			maxPrice={ max }
			minPrice={ min }
			onChange={ ( [ newMin, newMax ] ) => {
				setMin( newMin );
				setMax( newMax );
			} }
		/>
	);
};

export const Default = Template.bind( {} );

export const WithoutInputs = Template.bind( {} );
WithoutInputs.args = {
	showInputFields: false,
};

export const WithButton = Template.bind( {} );
WithButton.args = {
	showFilterButton: true,
};

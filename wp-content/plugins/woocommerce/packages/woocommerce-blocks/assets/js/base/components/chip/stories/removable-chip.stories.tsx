/**
 * External dependencies
 */
import type { Story, Meta } from '@storybook/react';

/**
 * Internal dependencies
 */
import { RemovableChip, RemovableChipProps } from '../removable-chip';

const availableElements = [ 'li', 'div', 'span' ];

export default {
	title: 'WooCommerce Blocks/@base-components/Chip/RemovableChip',
	component: RemovableChip,
	argTypes: {
		element: {
			control: 'radio',
			options: availableElements,
		},
	},
} as Meta< RemovableChipProps >;

const Template: Story< RemovableChipProps > = ( args ) => (
	<RemovableChip { ...args } />
);

export const Default = Template.bind( {} );
Default.args = {
	element: 'li',
	text: 'Take me to the casino',
	screenReaderText: "I'm a removable chip, me",
};

/**
 * External dependencies
 */
import type { Story, Meta } from '@storybook/react';

/**
 * Internal dependencies
 */
import CheckboxControl, { CheckboxControlProps } from '..';

export default {
	title: 'WooCommerce Blocks/Checkout Blocks/CheckboxControl',
	component: CheckboxControl,
	args: {
		instanceId: 'my-checkbox-id',
		label: 'Check me out',
	},
} as Meta< CheckboxControlProps >;

const Template: Story< CheckboxControlProps > = ( args ) => (
	<CheckboxControl { ...args } />
);

export const Default = Template.bind( {} );
Default.args = {};

/**
 * External dependencies
 */
import type { Story, Meta } from '@storybook/react';

/**
 * Internal dependencies
 */
import Button, { ButtonProps } from '..';
const availableTypes = [ 'button', 'input', 'submit' ];

export default {
	title: 'WooCommerce Blocks/@base-components/Button',
	argTypes: {
		children: {
			control: 'text',
		},
		type: {
			control: 'radio',
			options: availableTypes,
		},
	},
	component: Button,
} as Meta< ButtonProps >;

const Template: Story< ButtonProps > = ( args ) => {
	return <Button { ...args } />;
};

export const Default = Template.bind( {} );
Default.args = {
	children: 'Buy Now',
	disabled: false,
	showSpinner: false,
	type: 'button',
};

export const Disabled = Template.bind( {} );
Disabled.args = {
	...Default.args,
	disabled: true,
};

export const Loading = Template.bind( {} );
Loading.args = {
	...Default.args,
	disabled: true,
	showSpinner: true,
};

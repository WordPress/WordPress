/**
 * External dependencies
 */
import type { Story, Meta } from '@storybook/react';

/**
 * Internal dependencies
 */
import SnackbarList, { SnackbarListProps } from '../';

export default {
	title: 'WooCommerce Blocks/@base-components/SnackbarList',
	args: {
		notices: [
			{
				id: '1',
				content: 'This is a snackbar notice.',
				status: 'success',
				isDismissible: true,
			},
		],
		className: undefined,
		onRemove: () => void 0,
	},
	argTypes: {
		className: {
			description: 'Additional class name to give to the notice.',
			control: 'text',
		},
		notices: {
			description: 'List of notice objects to show as snackbar notices.',
			disable: true,
		},
		onRemove: {
			description: 'Function called when dismissing the notice(s).',
			disable: true,
		},
	},
	component: SnackbarList,
} as Meta< SnackbarListProps >;

const Template: Story< SnackbarListProps > = ( args ) => {
	return <SnackbarList { ...args } />;
};

export const Default = Template.bind( {} );
Default.args = {
	notices: [
		{
			id: '1',
			content: 'This is a snackbar notice.',
			status: 'default',
			isDismissible: true,
		},
		{
			id: '2',
			content: 'This is an informational snackbar notice.',
			status: 'info',
			isDismissible: true,
		},
		{
			id: '3',
			content: 'This is a snackbar error notice.',
			status: 'error',
			isDismissible: true,
		},
		{
			id: '4',
			content: 'This is a snackbar warning notice.',
			status: 'warning',
			isDismissible: true,
		},
		{
			id: '5',
			content: 'This is a snackbar success notice.',
			status: 'success',
			isDismissible: true,
		},
	],
	className: undefined,
	onRemove: () => void 0,
};

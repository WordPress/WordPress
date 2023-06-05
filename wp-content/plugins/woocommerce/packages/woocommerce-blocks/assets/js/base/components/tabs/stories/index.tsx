/**
 * External dependencies
 */
import type { Story, Meta } from '@storybook/react';
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { __TabsWithoutInstanceId as Tabs, TabsProps } from '..';

export default {
	title: 'WooCommerce Blocks/@base-components/Tabs',
	component: Tabs,
	args: {
		tabs: [
			{
				name: 'firstTab',
				title: 'First Tab',
				content: <div>Content of the first tab</div>,
			},
			{
				name: 'secondTab',
				title: 'Second Tab',
				content: <div>Content of the second tab</div>,
			},
		],
		initialTabName: 'firstTab',
	},
	argTypes: {
		initialTabName: {
			control: {
				type: 'select',
				options: [ 'firstTab', 'secondTab' ],
			},
		},
	},
} as Meta< TabsProps >;

const Template: Story< TabsProps > = ( args ) => {
	const [ initialTab, setInitialTab ] = useState( args.initialTabName );

	return (
		<Tabs
			initialTabName={ initialTab }
			onSelect={ ( newTabName ) => {
				setInitialTab( newTabName );
			} }
			{ ...args }
		/>
	);
};

export const Default = Template.bind( {} );

/**
 * External dependencies
 */
import type { Story, Meta } from '@storybook/react';
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import FormTokenField, { Props } from '..';

export default {
	title: 'WooCommerce Blocks/@base-components/FormTokenField',
	argTypes: {},
	component: FormTokenField,
} as Meta< Props >;

const Template: Story< Props > = ( args ) => {
	const [ selected, setSelected ] = useState< string[] >( [] );

	return (
		<FormTokenField
			{ ...args }
			value={ selected }
			onChange={ ( tokens ) => setSelected( tokens ) }
		/>
	);
};

const suggestions = [ 'foo', 'bar', 'baz' ];

export const Default = Template.bind( {} );
Default.args = {
	suggestions,
};

export const Disabled = Template.bind( {} );
Disabled.args = {
	...Default.args,
	disabled: true,
};

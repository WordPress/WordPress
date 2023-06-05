/**
 * External dependencies
 */
import type { Story, Meta } from '@storybook/react';

/**
 * Internal dependencies
 */
import ErrorMessage, { ErrorMessageProps } from '../error-message';

export default {
	title: 'WooCommerce Blocks/editor-components/Errors/Base Error Atom',
	component: ErrorMessage,
} as Meta< ErrorMessageProps >;

const Template: Story< ErrorMessageProps > = ( args ) => (
	<ErrorMessage { ...args } />
);

export const BaseErrorAtom = Template.bind( {} );
BaseErrorAtom.args = {
	error: {
		message:
			'A very generic and unhelpful error. Please try again later. Or contact support. Or not.',
		type: 'general',
	},
};

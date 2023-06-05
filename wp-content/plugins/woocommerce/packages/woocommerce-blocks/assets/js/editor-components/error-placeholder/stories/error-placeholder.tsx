/**
 * External dependencies
 */
import type { Story, Meta } from '@storybook/react';
import { useArgs } from '@storybook/client-api';
import { INTERACTION_TIMEOUT } from '@woocommerce/storybook-controls';

/**
 * Internal dependencies
 */
import ErrorPlaceholder, { ErrorPlaceholderProps } from '..';

export default {
	title: 'WooCommerce Blocks/editor-components/Errors',
	component: ErrorPlaceholder,
} as Meta< ErrorPlaceholderProps >;

const Template: Story< ErrorPlaceholderProps > = ( args ) => {
	const [ { isLoading }, setArgs ] = useArgs();

	const onRetry = args.onRetry
		? () => {
				setArgs( { isLoading: true } );

				setTimeout(
					() => setArgs( { isLoading: false } ),
					INTERACTION_TIMEOUT
				);
		  }
		: undefined;

	return (
		<ErrorPlaceholder
			{ ...args }
			onRetry={ onRetry }
			isLoading={ isLoading }
		/>
	);
};

export const Default = Template.bind( {} );
Default.args = {
	error: {
		message:
			'A very generic and unhelpful error. Please try again later. Or contact support. Or not.',
		type: 'general',
	},
};

export const APIError = Template.bind( {} );
APIError.args = {
	error: {
		message: 'Server refuses to comply. It is a teapot.',
		type: 'api',
	},
};

export const UnknownError = Template.bind( {} );
UnknownError.args = {
	error: {
		message: '',
		type: 'general',
	},
};

export const NoRetry: Story< ErrorPlaceholderProps > = ( args ) => {
	return <ErrorPlaceholder { ...args } onRetry={ undefined } />;
};
NoRetry.args = {
	error: {
		message: '',
		type: 'general',
	},
};

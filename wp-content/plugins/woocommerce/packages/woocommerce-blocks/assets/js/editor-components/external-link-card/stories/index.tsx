/**
 * External dependencies
 */
import type { Story, Meta } from '@storybook/react';

/**
 * Internal dependencies
 */
import ExternalLinkCard, { ExternalLinkCardProps } from '..';

export default {
	title: 'WooCommerce Blocks/editor-components/ExternalLinkCard',
	component: ExternalLinkCard,
} as Meta< ExternalLinkCardProps >;

const Template: Story< ExternalLinkCardProps > = ( args ) => (
	<ExternalLinkCard { ...args } />
);

export const Default = Template.bind( {} );
Default.args = {
	description:
		'This is the description of the link, perhaps a bit of a longer paragraph or a summary of a blog post, or whatever could give more context',
	href: 'https://woocommerce.com/posts/seven-tips-to-extend-holiday-sales-momentum/',
	title: 'Seven tips to extend holiday sales momentum',
};

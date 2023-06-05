/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { starEmpty, Icon } from '@wordpress/icons';

export const BLOCK_TITLE: string = __(
	'Product Rating',
	'woo-gutenberg-products-block'
);
export const BLOCK_ICON: JSX.Element = (
	<Icon
		icon={ starEmpty }
		className="wc-block-editor-components-block-icon"
	/>
);
export const BLOCK_DESCRIPTION: string = __(
	'Display the average rating of a product.',
	'woo-gutenberg-products-block'
);

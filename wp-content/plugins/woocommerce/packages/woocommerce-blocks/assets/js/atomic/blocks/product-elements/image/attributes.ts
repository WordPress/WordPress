/**
 * External dependencies
 */
import type { BlockAttributes } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { ImageSizing } from './types';

export const blockAttributes: BlockAttributes = {
	showProductLink: {
		type: 'boolean',
		default: true,
	},
	showSaleBadge: {
		type: 'boolean',
		default: true,
	},
	saleBadgeAlign: {
		type: 'string',
		default: 'right',
	},
	imageSizing: {
		type: 'string',
		default: ImageSizing.SINGLE,
	},
	productId: {
		type: 'number',
		default: 0,
	},
	isDescendentOfQueryLoop: {
		type: 'boolean',
		default: false,
	},
	isDescendentOfSingleProductBlock: {
		type: 'boolean',
		default: false,
	},
};

export default blockAttributes;

/**
 * External dependencies
 */
import type { BlockAttributes } from '@wordpress/blocks';

export const blockAttributes: BlockAttributes = {
	productId: {
		type: 'number',
		default: 0,
	},
	isDescendentOfQueryLoop: {
		type: 'boolean',
		default: false,
	},
	textAlign: {
		type: 'string',
		default: '',
	},
	width: {
		type: 'number',
	},
};

export default blockAttributes;

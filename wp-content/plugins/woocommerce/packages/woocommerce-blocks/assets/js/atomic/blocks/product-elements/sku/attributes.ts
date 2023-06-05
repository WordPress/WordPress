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
	isDescendentOfSingleProductTemplate: {
		type: 'boolean',
		default: false,
	},
	isDescendantOfAllProducts: {
		type: 'boolean',
		default: false,
	},
	showProductSelector: {
		type: 'boolean',
		default: false,
	},
};

export default blockAttributes;

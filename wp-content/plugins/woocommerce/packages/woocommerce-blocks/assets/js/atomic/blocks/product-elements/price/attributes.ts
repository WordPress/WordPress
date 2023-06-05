/**
 * External dependencies
 */
import { BlockAttributes } from '@wordpress/blocks';

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
	isDescendentOfSingleProductTemplate: {
		type: 'boolean',
		default: false,
	},
	isDescendentOfSingleProductBlock: {
		type: 'boolean',
		default: false,
	},
};

export default blockAttributes;

/**
 * External dependencies
 */
import type { BlockAttributes } from '@wordpress/blocks';

export const blockAttributes: BlockAttributes = {
	productId: {
		type: 'number',
		default: 0,
	},
};

export default blockAttributes;

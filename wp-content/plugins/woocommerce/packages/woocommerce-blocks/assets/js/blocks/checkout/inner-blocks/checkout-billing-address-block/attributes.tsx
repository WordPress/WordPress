/**
 * External dependencies
 */
import type { BlockAttributes } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import formStepAttributes from '../../form-step/attributes';
import { DEFAULT_TITLE, DEFAULT_DESCRIPTION } from './constants';

const attributes: BlockAttributes = {
	...formStepAttributes( {
		defaultTitle: DEFAULT_TITLE,
		defaultDescription: DEFAULT_DESCRIPTION,
	} ),
	className: {
		type: 'string',
		default: '',
	},
	lock: {
		type: 'object',
		default: {
			move: true,
			remove: true,
		},
	},
};
export default attributes;

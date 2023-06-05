/**
 * Internal dependencies
 */
import { defaultButtonLabel } from './constants';

export default {
	checkoutPageId: {
		type: 'number',
		default: 0,
	},
	lock: {
		type: 'object',
		default: {
			move: true,
			remove: true,
		},
	},
	buttonLabel: {
		type: 'string',
		default: defaultButtonLabel,
	},
};

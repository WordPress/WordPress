/**
 * Internal dependencies
 */
import { defaultPlaceOrderButtonLabel } from './constants';

export default {
	cartPageId: {
		type: 'number',
		default: 0,
	},
	showReturnToCart: {
		type: 'boolean',
		default: true,
	},
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
	placeOrderButtonLabel: {
		type: 'string',
		default: defaultPlaceOrderButtonLabel,
	},
};

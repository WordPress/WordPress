/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import formStepAttributes from '../../form-step/attributes';
import { defaultShippingText, defaultLocalPickupText } from './constants';

export default {
	...formStepAttributes( {
		defaultTitle: __( 'Shipping method', 'woo-gutenberg-products-block' ),
		defaultDescription: __(
			'Select how you would like to receive your order.',
			'woo-gutenberg-products-block'
		),
	} ),
	className: {
		type: 'string',
		default: '',
	},
	showIcon: {
		type: 'boolean',
		default: true,
	},
	showPrice: {
		type: 'boolean',
		default: true,
	},
	localPickupText: {
		type: 'string',
		default: defaultLocalPickupText,
	},
	shippingText: {
		type: 'string',
		default: defaultShippingText,
	},
	lock: {
		type: 'object',
		default: {
			move: true,
			remove: true,
		},
	},
	shippingCostRequiresAddress: {
		type: 'boolean',
		default: false,
	},
};

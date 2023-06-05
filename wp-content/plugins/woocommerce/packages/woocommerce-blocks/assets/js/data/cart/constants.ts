/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';

export const STORE_KEY = 'wc/store/cart';
export const CART_API_ERROR = {
	code: 'cart_api_error',
	message: __(
		'Unable to get cart data from the API.',
		'woo-gutenberg-products-block'
	),
	data: {
		status: 500,
	},
};

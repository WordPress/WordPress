/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Icon } from '@wordpress/icons';
import { filledCart, removeCart } from '@woocommerce/icons';

export const blockName = 'woocommerce/mini-cart-contents';

export const attributes = {
	isPreview: {
		type: 'boolean',
		default: false,
	},
	lock: {
		type: 'object',
		default: {
			remove: true,
			move: true,
		},
	},
	currentView: {
		type: 'string',
		default: 'woocommerce/filled-mini-cart-contents-block',
		source: 'readonly', // custom source to prevent saving to post content
	},
	editorViews: {
		type: 'object',
		default: [
			{
				view: 'woocommerce/filled-mini-cart-contents-block',
				label: __( 'Filled Mini Cart', 'woo-gutenberg-products-block' ),
				icon: <Icon icon={ filledCart } />,
			},
			{
				view: 'woocommerce/empty-mini-cart-contents-block',
				label: __( 'Empty Mini Cart', 'woo-gutenberg-products-block' ),
				icon: <Icon icon={ removeCart } />,
			},
		],
	},
	width: {
		type: 'string',
		default: '480px',
	},
};

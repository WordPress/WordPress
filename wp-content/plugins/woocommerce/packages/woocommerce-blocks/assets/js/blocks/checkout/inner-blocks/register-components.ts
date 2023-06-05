/**
 * External dependencies
 */
import { lazy } from '@wordpress/element';
import {
	WC_BLOCKS_BUILD_URL,
	LOCAL_PICKUP_ENABLED,
} from '@woocommerce/block-settings';
import { registerCheckoutBlock } from '@woocommerce/blocks-checkout';

/**
 * Internal dependencies
 */
import metadata from './component-metadata';

// Modify webpack publicPath at runtime based on location of WordPress Plugin.
// eslint-disable-next-line no-undef,camelcase
__webpack_public_path__ = WC_BLOCKS_BUILD_URL;

// @todo When forcing all blocks at once, they will append based on the order they are registered. Introduce formal sorting param.
registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_FIELDS,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/fields" */ './checkout-fields-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_EXPRESS_PAYMENT,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/express-payment" */ './checkout-express-payment-block/block'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_CONTACT_INFORMATION,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/contact-information" */ './checkout-contact-information-block/frontend'
			)
	),
} );

if ( LOCAL_PICKUP_ENABLED ) {
	registerCheckoutBlock( {
		metadata: metadata.CHECKOUT_SHIPPING_METHOD,
		component: lazy(
			() =>
				import(
					/* webpackChunkName: "checkout-blocks/shipping-method" */ './checkout-shipping-method-block/frontend'
				)
		),
	} );
	registerCheckoutBlock( {
		metadata: metadata.CHECKOUT_PICKUP_LOCATION,
		component: lazy(
			() =>
				import(
					/* webpackChunkName: "checkout-blocks/pickup-options" */ './checkout-pickup-options-block/frontend'
				)
		),
	} );
}

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_SHIPPING_ADDRESS,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/shipping-address" */ './checkout-shipping-address-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_BILLING_ADDRESS,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/billing-address" */ './checkout-billing-address-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_SHIPPING_METHODS,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/shipping-methods" */ './checkout-shipping-methods-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_PAYMENT,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/payment" */ './checkout-payment-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_ORDER_NOTE,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/order-note" */ './checkout-order-note-block/block'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_TERMS,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/terms" */ './checkout-terms-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_ACTIONS,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/actions" */ './checkout-actions-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_TOTALS,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/totals" */ './checkout-totals-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_ORDER_SUMMARY,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/order-summary" */ './checkout-order-summary-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_ORDER_SUMMARY_CART_ITEMS,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/order-summary-cart-items" */
				'./checkout-order-summary-cart-items/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_ORDER_SUMMARY_SUBTOTAL,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/order-summary-subtotal" */
				'./checkout-order-summary-subtotal/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_ORDER_SUMMARY_FEE,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/order-summary-fee" */
				'./checkout-order-summary-fee/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_ORDER_SUMMARY_DISCOUNT,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/order-summary-discount" */
				'./checkout-order-summary-discount/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_ORDER_SUMMARY_COUPON_FORM,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/order-summary-coupon-form" */
				'./checkout-order-summary-coupon-form/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_ORDER_SUMMARY_SHIPPING,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/order-summary-shipping" */
				'./checkout-order-summary-shipping/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CHECKOUT_ORDER_SUMMARY_TAXES,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "checkout-blocks/order-summary-taxes" */
				'./checkout-order-summary-taxes/frontend'
			)
	),
} );

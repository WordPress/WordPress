/**
 * External dependencies
 */
import { lazy } from '@wordpress/element';
import { WC_BLOCKS_BUILD_URL } from '@woocommerce/block-settings';
import { registerCheckoutBlock } from '@woocommerce/blocks-checkout';

/**
 * Internal dependencies
 */
import metadata from './component-metadata';

// Modify webpack publicPath at runtime based on location of WordPress Plugin.
// eslint-disable-next-line no-undef,camelcase
__webpack_public_path__ = WC_BLOCKS_BUILD_URL;

registerCheckoutBlock( {
	metadata: metadata.FILLED_CART,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/filled-cart" */
				'./filled-cart-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.EMPTY_CART,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/empty-cart" */
				'./empty-cart-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_ITEMS,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/cart-items" */
				'./cart-items-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_LINE_ITEMS,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/cart-line-items" */
				'./cart-line-items-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_CROSS_SELLS,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/cart-cross-sells" */
				'./cart-cross-sells-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_CROSS_SELLS_PRODUCTS,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/cart-cross-sells-products" */
				'./cart-cross-sells-products/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_TOTALS,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/cart-totals" */
				'./cart-totals-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_EXPRESS_PAYMENT,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/cart-express-payment" */
				'./cart-express-payment-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.PROCEED_TO_CHECKOUT,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/proceed-to-checkout" */
				'./proceed-to-checkout-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_ACCEPTED_PAYMENT_METHODS,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/cart-accepted-payment-methods" */
				'./cart-accepted-payment-methods-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_ORDER_SUMMARY,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/cart-order-summary" */
				'./cart-order-summary-block/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_ORDER_SUMMARY_HEADING,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/order-summary-heading" */
				'./cart-order-summary-heading/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_ORDER_SUMMARY_SUBTOTAL,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/order-summary-subtotal" */
				'./cart-order-summary-subtotal/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_ORDER_SUMMARY_FEE,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/order-summary-fee" */
				'./cart-order-summary-fee/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_ORDER_SUMMARY_DISCOUNT,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/order-summary-discount" */
				'./cart-order-summary-discount/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_ORDER_SUMMARY_COUPON_FORM,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/order-summary-coupon-form" */
				'./cart-order-summary-coupon-form/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_ORDER_SUMMARY_SHIPPING,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/order-summary-shipping" */
				'./cart-order-summary-shipping/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_ORDER_SUMMARY_TAXES,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/order-summary-taxes" */
				'./cart-order-summary-taxes/frontend'
			)
	),
} );

registerCheckoutBlock( {
	metadata: metadata.CART_ORDER_SUMMARY_HEADING,
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "cart-blocks/order-summary-heading" */
				'./cart-order-summary-heading/frontend'
			)
	),
} );

/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { addAction } from '@wordpress/hooks';
import type {
	ProductResponseItem,
	CartResponseItem,
	StoreCart,
} from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { namespace, actionPrefix } from './constants';
import {
	getProductFieldObject,
	getProductImpressionObject,
	trackEvent,
	trackCheckoutStep,
	trackCheckoutOption,
} from './utils';

/**
 * Track customer progress through steps of the checkout. Triggers the event when the step changes:
 * 	1 - Contact information
 * 	2 - Shipping address
 * 	3 - Billing address
 * 	4 - Shipping options
 * 	5 - Payment options
 *
 * @summary Track checkout progress with begin_checkout and checkout_progress
 * @see https://developers.google.com/analytics/devguides/collection/gtagjs/enhanced-ecommerce#1_measure_checkout_steps
 */
addAction(
	`${ actionPrefix }-checkout-render-checkout-form`,
	namespace,
	trackCheckoutStep( 0 )
);
addAction(
	`${ actionPrefix }-checkout-set-email-address`,
	namespace,
	trackCheckoutStep( 1 )
);
addAction(
	`${ actionPrefix }-checkout-set-shipping-address`,
	namespace,
	trackCheckoutStep( 2 )
);
addAction(
	`${ actionPrefix }-checkout-set-billing-address`,
	namespace,
	trackCheckoutStep( 3 )
);
addAction(
	`${ actionPrefix }-checkout-set-phone-number`,
	namespace,
	( { step, ...rest }: { step: string; storeCart: StoreCart } ): void => {
		trackCheckoutStep( step === 'shipping' ? 2 : 3 )( rest );
	}
);

/**
 * Choose a shipping rate
 *
 * @summary Track the shipping rate being set using set_checkout_option
 * @see https://developers.google.com/analytics/devguides/collection/gtagjs/enhanced-ecommerce#2_measure_checkout_options
 */
addAction(
	`${ actionPrefix }-checkout-set-selected-shipping-rate`,
	namespace,
	( { shippingRateId }: { shippingRateId: string } ): void => {
		trackCheckoutOption( {
			step: 4,
			option: __( 'Shipping Method', 'woo-gutenberg-products-block' ),
			value: shippingRateId,
		} )();
	}
);

/**
 * Choose a payment method
 *
 * @summary Track the payment method being set using set_checkout_option
 * @see https://developers.google.com/analytics/devguides/collection/gtagjs/enhanced-ecommerce#2_measure_checkout_options
 */
addAction(
	`${ actionPrefix }-checkout-set-active-payment-method`,
	namespace,
	( { paymentMethodSlug }: { paymentMethodSlug: string } ): void => {
		trackCheckoutOption( {
			step: 5,
			option: __( 'Payment Method', 'woo-gutenberg-products-block' ),
			value: paymentMethodSlug,
		} )();
	}
);

/**
 * Add Payment Information
 *
 * This event signifies a user has submitted their payment information. Note, this is used to indicate checkout
 * submission, not `purchase` which is triggered on the thanks page.
 *
 * @summary Track the add_payment_info event
 * @see https://developers.google.com/gtagjs/reference/ga4-events#add_payment_info
 */
addAction( `${ actionPrefix }-checkout-submit`, namespace, (): void => {
	trackEvent( 'add_payment_info' );
} );

/**
 * Add to cart.
 *
 * This event signifies that an item was added to a cart for purchase.
 *
 * @summary Track the add_to_cart event
 * @see https://developers.google.com/gtagjs/reference/ga4-events#add_to_cart
 */
addAction(
	`${ actionPrefix }-cart-add-item`,
	namespace,
	( {
		product,
		quantity = 1,
	}: {
		product: ProductResponseItem;
		quantity: number;
	} ): void => {
		trackEvent( 'add_to_cart', {
			event_category: 'ecommerce',
			event_label: __( 'Add to Cart', 'woo-gutenberg-products-block' ),
			items: [ getProductFieldObject( product, quantity ) ],
		} );
	}
);

/**
 * Remove item from the cart
 *
 * @summary Track the remove_from_cart event
 * @see https://developers.google.com/gtagjs/reference/ga4-events#remove_from_cart
 */
addAction(
	`${ actionPrefix }-cart-remove-item`,
	namespace,
	( {
		product,
		quantity = 1,
	}: {
		product: CartResponseItem;
		quantity: number;
	} ): void => {
		trackEvent( 'remove_from_cart', {
			event_category: 'ecommerce',
			event_label: __(
				'Remove Cart Item',
				'woo-gutenberg-products-block'
			),
			items: [ getProductFieldObject( product, quantity ) ],
		} );
	}
);

/**
 * Change cart item quantities
 *
 * @summary Custom change_cart_quantity event.
 */
addAction(
	`${ actionPrefix }-cart-set-item-quantity`,
	namespace,
	( {
		product,
		quantity = 1,
	}: {
		product: CartResponseItem;
		quantity: number;
	} ): void => {
		trackEvent( 'change_cart_quantity', {
			event_category: 'ecommerce',
			event_label: __(
				'Change Cart Item Quantity',
				'woo-gutenberg-products-block'
			),
			items: [ getProductFieldObject( product, quantity ) ],
		} );
	}
);

/**
 * Product List View
 *
 * @summary Track the view_item_list event
 * @see https://developers.google.com/gtagjs/reference/ga4-events#view_item_list
 */
addAction(
	`${ actionPrefix }-product-list-render`,
	namespace,
	( {
		products,
		listName = __( 'Product List', 'woo-gutenberg-products-block' ),
	}: {
		products: Array< ProductResponseItem >;
		listName: string;
	} ): void => {
		if ( products.length === 0 ) {
			return;
		}
		trackEvent( 'view_item_list', {
			event_category: 'engagement',
			event_label: __(
				'Viewing products',
				'woo-gutenberg-products-block'
			),
			items: products.map( ( product, index ) => ( {
				...getProductImpressionObject( product, listName ),
				list_position: index + 1,
			} ) ),
		} );
	}
);

/**
 * Product View Link Clicked
 *
 * @summary Track the select_content event
 * @see https://developers.google.com/gtagjs/reference/ga4-events#select_content
 */
addAction(
	`${ actionPrefix }-product-view-link`,
	namespace,
	( {
		product,
		listName,
	}: {
		product: ProductResponseItem;
		listName: string;
	} ): void => {
		trackEvent( 'select_content', {
			content_type: 'product',
			items: [ getProductImpressionObject( product, listName ) ],
		} );
	}
);

/**
 * Product Search
 *
 * @summary Track the search event
 * @see https://developers.google.com/gtagjs/reference/ga4-events#search
 */
addAction(
	`${ actionPrefix }-product-search`,
	namespace,
	( { searchTerm }: { searchTerm: string } ): void => {
		trackEvent( 'search', {
			search_term: searchTerm,
		} );
	}
);

/**
 * Single Product View
 *
 * @summary Track the view_item event
 * @see https://developers.google.com/gtagjs/reference/ga4-events#view_item
 */
addAction(
	`${ actionPrefix }-product-render`,
	namespace,
	( {
		product,
		listName,
	}: {
		product: ProductResponseItem;
		listName: string;
	} ): void => {
		if ( product ) {
			trackEvent( 'view_item', {
				items: [ getProductImpressionObject( product, listName ) ],
			} );
		}
	}
);

/**
 * Track notices as Exception events.
 *
 * @summary Track the exception event
 * @see https://developers.google.com/analytics/devguides/collection/gtagjs/exceptions
 */
addAction(
	`${ actionPrefix }-store-notice-create`,
	namespace,
	( { status, content }: { status: string; content: string } ): void => {
		if ( status === 'error' ) {
			trackEvent( 'exception', {
				description: content,
				fatal: false,
			} );
		}
	}
);

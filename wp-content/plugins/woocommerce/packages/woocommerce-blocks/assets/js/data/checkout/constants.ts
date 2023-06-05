/**
 * External dependencies
 */
import {
	BillingAddress,
	getSetting,
	ShippingAddress,
} from '@woocommerce/settings';

import { CheckoutResponseSuccess } from '@woocommerce/types';

export const STORE_KEY = 'wc/store/checkout';

export enum STATUS {
	// When checkout state has changed but there is no activity happening.
	IDLE = 'idle',
	// After the AFTER_PROCESSING event emitters have completed. This status triggers the checkout redirect.
	COMPLETE = 'complete',
	// This is the state before checkout processing begins after the checkout button has been pressed/submitted.
	BEFORE_PROCESSING = 'before_processing',
	// After BEFORE_PROCESSING status emitters have finished successfully. Payment processing is started on this checkout status.
	PROCESSING = 'processing',
	// After server side checkout processing is completed this status is set
	AFTER_PROCESSING = 'after_processing',
}

const preloadedCheckoutData = getSetting(
	'checkoutData',
	{}
) as Partial< CheckoutResponseSuccess >;

export const checkoutData = {
	order_id: 0,
	customer_id: 0,
	billing_address: {} as BillingAddress,
	shipping_address: {} as ShippingAddress,
	...( preloadedCheckoutData || {} ),
};

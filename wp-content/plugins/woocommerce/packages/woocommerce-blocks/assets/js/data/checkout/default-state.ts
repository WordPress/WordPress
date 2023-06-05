/**
 * External dependencies
 */
import { isSameAddress } from '@woocommerce/base-utils';

/**
 * Internal dependencies
 */
import { STATUS, checkoutData } from './constants';

export type CheckoutState = {
	// Status of the checkout
	status: STATUS;
	// If any of the totals, taxes, shipping, etc need to be calculated, the count will be increased here
	calculatingCount: number;
	// True when the checkout is in an error state. Whatever caused the error (validation/payment method) will likely have triggered a notice.
	hasError: boolean;
	// This is the url that checkout will redirect to when it's ready.
	redirectUrl: string;
	// This is the ID for the draft order if one exists.
	orderId: number;
	// Order notes introduced by the user in the checkout form.
	orderNotes: string;
	// This is the ID of the customer the draft order belongs to.
	customerId: number;
	// Should the billing form be hidden and inherit the shipping address?
	useShippingAsBilling: boolean;
	// Should a user account be created?
	shouldCreateAccount: boolean;
	// If customer wants to checkout with a local pickup option.
	prefersCollection?: boolean | undefined;
	// Custom checkout data passed to the store API on processing.
	extensionData: Record< string, Record< string, unknown > >;
};

export const defaultState: CheckoutState = {
	redirectUrl: '',
	status: STATUS.PRISTINE,
	hasError: false,
	orderId: checkoutData.order_id,
	customerId: checkoutData.customer_id,
	calculatingCount: 0,
	orderNotes: '',
	useShippingAsBilling: isSameAddress(
		checkoutData.billing_address,
		checkoutData.shipping_address
	),
	shouldCreateAccount: false,
	prefersCollection: undefined,
	extensionData: {},
};

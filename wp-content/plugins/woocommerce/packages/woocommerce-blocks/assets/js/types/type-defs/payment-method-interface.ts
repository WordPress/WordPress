/* eslint-disable @typescript-eslint/naming-convention */
/**
 * External dependencies
 */
import type PaymentMethodLabel from '@woocommerce/base-components/cart-checkout/payment-method-label';
import type PaymentMethodIcons from '@woocommerce/base-components/cart-checkout/payment-method-icons';
import type LoadingMask from '@woocommerce/base-components/loading-mask';
import type { ValidationInputError } from '@woocommerce/blocks-checkout';

/**
 * Internal dependencies
 */
import type { Currency } from './currency';
import type { CartBillingAddress, CartShippingRate } from './cart';
import type {
	emitterCallback,
	responseTypes,
	noticeContexts,
} from '../../base/context/event-emit';
import type {
	CartResponseShippingAddress,
	CartResponseCouponItem,
	CartResponseItem,
	CartResponseFeeItem,
	ExtensionsData,
} from './cart-response';
import type { SHIPPING_ERROR_TYPES } from './contexts';

export interface PreparedCartTotalItem {
	// The label for the total item.
	label: string;
	// The value for the total item.
	value: number;
}

export interface BillingDataProps {
	// All the coupons that were applied to the cart/order.
	appliedCoupons: CartResponseCouponItem[];
	// The address used for billing.
	billingData: CartBillingAddress;
	billingAddress: CartBillingAddress;
	// The total item for the cart.
	cartTotal: PreparedCartTotalItem;
	// The various subtotal amounts.
	cartTotalItems: PreparedCartTotalItem[];
	// Currency object.
	currency: Currency;
	// The customer Id the order belongs to.
	customerId: number;
	// True means that the site is configured to display prices including tax.
	displayPricesIncludingTax: boolean;
}

export interface CartDataProps {
	cartItems: CartResponseItem[];
	cartFees: CartResponseFeeItem[];
	extensions: ExtensionsData;
}

export interface CheckoutStatusProps {
	// If true then totals are being calculated in the checkout.
	isCalculating: boolean;
	// If true then the checkout has completed it's processing.
	isComplete: boolean;
	// If true then the checkout is idle (no  activity happening).
	isIdle: boolean;
	// If true then checkout is processing (finalizing) the order with the server.
	isProcessing: boolean;
}

export interface ComponentProps {
	// A wrapper component used for showing a loading state when the isLoading prop is true.
	LoadingMask: typeof LoadingMask;
	// A component used for displaying payment method icons.
	PaymentMethodIcons: typeof PaymentMethodIcons;
	// A component used for displaying payment method labels, including an icon.
	PaymentMethodLabel: typeof PaymentMethodLabel;
	// A container for holding validation errors
	ValidationInputError: typeof ValidationInputError;
}

export interface EmitResponseProps {
	// Response types that can be returned from emitter observers.
	responseTypes: typeof responseTypes;
	// Available contexts that can be returned as the value for the messageContext property on the object  returned from an emitter observer.
	noticeContexts: typeof noticeContexts;
}

export interface EventRegistrationProps {
	// Deprecated in favour of onCheckoutFail.
	onCheckoutAfterProcessingWithError: ReturnType< typeof emitterCallback >;
	// Deprecated in favour of onCheckoutSuccess.
	onCheckoutAfterProcessingWithSuccess: ReturnType< typeof emitterCallback >;
	// Used to subscribe callbacks firing before checkout begins processing.
	onCheckoutBeforeProcessing: ReturnType< typeof emitterCallback >;
	// Used to register a callback that will fire if the api call to /checkout is successful
	onCheckoutSuccess: ReturnType< typeof emitterCallback >;
	// Used to register a callback that will fire if the api call to /checkout fails
	onCheckoutFail: ReturnType< typeof emitterCallback >;
	// Used to register a callback that will fire when the checkout performs validation on the form
	onCheckoutValidation: ReturnType< typeof emitterCallback >;
	// Deprecated in favour of onCheckoutValidation.
	onCheckoutValidationBeforeProcessing: ReturnType< typeof emitterCallback >;
	// Deprecated in favour of onPaymentSetup
	onPaymentProcessing: ReturnType< typeof emitterCallback >;
	// Event registration callback for registering observers for the payment setup event.
	onPaymentSetup: ReturnType< typeof emitterCallback >;
	// Used to subscribe callbacks that will fire when retrieving shipping rates failed.
	onShippingRateFail: ReturnType< typeof emitterCallback >;
	// Used to subscribe callbacks that will fire after selecting a shipping rate unsuccessfully.
	onShippingRateSelectFail: ReturnType< typeof emitterCallback >;
	// Used to subscribe callbacks that will fire after selecting a shipping rate successfully.
	onShippingRateSelectSuccess: ReturnType< typeof emitterCallback >;
	// Used to subscribe callbacks that will fire when shipping rates for a given address have been received successfully.
	onShippingRateSuccess: ReturnType< typeof emitterCallback >;
}

export interface ShippingDataProps {
	// True when rates are being selected.
	isSelectingRate: boolean;
	// True if cart requires shipping.
	needsShipping: boolean;
	// An object containing package IDs as the key and selected rate as the value (rate ids).
	selectedRates: Record< string, unknown >;
	// A function for setting selected rates (receives id).
	setSelectedRates: (
		newShippingRateId: string,
		packageId: string | number
	) => unknown;
	// A function for setting the shipping address.
	setShippingAddress: ( data: CartResponseShippingAddress ) => void;
	// The current set shipping address.
	shippingAddress: CartResponseShippingAddress;
	// All the available shipping rates.
	shippingRates: CartShippingRate[];
	// Whether the rates are loading or not.
	shippingRatesLoading: boolean;
}

export interface ShippingStatusProps {
	// Current error status for shipping.
	shippingErrorStatus: {
		// Whether the status is pristine.
		isPristine: boolean;
		// Whether the status is valid.
		isValid: boolean;
		// Whether the address is invalid.
		hasInvalidAddress: boolean;
		// Whether an error has happened.
		hasError: boolean;
	};
	// An object containing all the possible types for shipping error status.
	shippingErrorTypes: SHIPPING_ERROR_TYPES;
}

export type PaymentMethodInterface = {
	// Indicates what the active payment method is.
	activePaymentMethod: string;
	// Various billing data items.
	billing: BillingDataProps;
	// Data exposed from the cart including items, fees, and any registered extension data. Note that this data should
	// be treated as immutable (should not be modified/mutated) or it will result in errors in your application.
	cartData: CartDataProps;
	// The current checkout status exposed as various boolean state.
	checkoutStatus: CheckoutStatusProps;
	// Components exposed to payment methods for use.
	components: ComponentProps;
	// Utilities for usage in event observer response objects.
	emitResponse: EmitResponseProps;
	// Various event registration helpers for subscribing callbacks for events.
	eventRegistration: EventRegistrationProps;
	// Used to trigger checkout processing.
	onSubmit: () => void;
	// Various payment status helpers.
	paymentStatus: {
		isPristine: boolean;
		isIdle: boolean;
		isStarted: boolean;
		isProcessing: boolean;
		isFinished: boolean;
		hasError: boolean;
		hasFailed: boolean;
		isSuccessful: boolean;
		isDoingExpressPayment: boolean;
	};
	// Deprecated. For setting an error (error message string) for express payment methods. Does not change payment status.
	setExpressPaymentError: ( errorMessage?: string ) => void;
	// Various data related to shipping.
	shippingData: ShippingDataProps;
	// Various shipping status helpers.
	shippingStatus: ShippingStatusProps;
	// A boolean which indicates whether the shopper has checked the save payment method checkbox.
	shouldSavePayment: boolean;
};

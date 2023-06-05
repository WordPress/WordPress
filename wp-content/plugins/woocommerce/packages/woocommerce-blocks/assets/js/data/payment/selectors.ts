/**
 * External dependencies
 */
import { objectHasProp } from '@woocommerce/types';
import deprecated from '@wordpress/deprecated';
import { getSetting } from '@woocommerce/settings';
import type { GlobalPaymentMethod } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { PaymentState } from './default-state';
import { filterActiveSavedPaymentMethods } from './utils/filter-active-saved-payment-methods';
import { STATUS as PAYMENT_STATUS } from './constants';

const globalPaymentMethods: Record< string, string > = {};

if ( getSetting( 'globalPaymentMethods' ) ) {
	getSetting< GlobalPaymentMethod[] >( 'globalPaymentMethods' ).forEach(
		( method ) => {
			globalPaymentMethods[ method.id ] = method.title;
		}
	);
}

export const isPaymentPristine = ( state: PaymentState ) => {
	deprecated( 'isPaymentPristine', {
		since: '9.6.0',
		alternative: 'isPaymentIdle',
		plugin: 'WooCommerce Blocks',
		link: 'https://github.com/woocommerce/woocommerce-blocks/pull/8110',
	} );

	return state.status === PAYMENT_STATUS.IDLE;
};

export const isPaymentIdle = ( state: PaymentState ) =>
	state.status === PAYMENT_STATUS.IDLE;

export const isPaymentStarted = ( state: PaymentState ) => {
	deprecated( 'isPaymentStarted', {
		since: '9.6.0',
		alternative: 'isExpressPaymentStarted',
		plugin: 'WooCommerce Blocks',
		link: 'https://github.com/woocommerce/woocommerce-blocks/pull/8110',
	} );
	return state.status === PAYMENT_STATUS.EXPRESS_STARTED;
};

export const isExpressPaymentStarted = ( state: PaymentState ) => {
	return state.status === PAYMENT_STATUS.EXPRESS_STARTED;
};

export const isPaymentProcessing = ( state: PaymentState ) =>
	state.status === PAYMENT_STATUS.PROCESSING;

export const isPaymentReady = ( state: PaymentState ) =>
	state.status === PAYMENT_STATUS.READY;

export const isPaymentSuccess = ( state: PaymentState ) => {
	deprecated( 'isPaymentSuccess', {
		since: '9.6.0',
		alternative: 'isPaymentReady',
		plugin: 'WooCommerce Blocks',
		link: 'https://github.com/woocommerce/woocommerce-blocks/pull/8110',
	} );

	return state.status === PAYMENT_STATUS.READY;
};

export const hasPaymentError = ( state: PaymentState ) =>
	state.status === PAYMENT_STATUS.ERROR;

export const isPaymentFailed = ( state: PaymentState ) => {
	deprecated( 'isPaymentFailed', {
		since: '9.6.0',
		plugin: 'WooCommerce Blocks',
		link: 'https://github.com/woocommerce/woocommerce-blocks/pull/8110',
	} );

	return state.status === PAYMENT_STATUS.ERROR;
};

export const isExpressPaymentMethodActive = ( state: PaymentState ) => {
	return Object.keys( state.availableExpressPaymentMethods ).includes(
		state.activePaymentMethod
	);
};

export const getActiveSavedToken = ( state: PaymentState ) => {
	return typeof state.paymentMethodData === 'object' &&
		objectHasProp( state.paymentMethodData, 'token' )
		? state.paymentMethodData.token + ''
		: '';
};

export const getActivePaymentMethod = ( state: PaymentState ) => {
	return state.activePaymentMethod;
};

export const getAvailablePaymentMethods = ( state: PaymentState ) => {
	return state.availablePaymentMethods;
};

export const getAvailableExpressPaymentMethods = ( state: PaymentState ) => {
	return state.availableExpressPaymentMethods;
};

export const getPaymentMethodData = ( state: PaymentState ) => {
	return state.paymentMethodData;
};

export const getIncompatiblePaymentMethods = ( state: PaymentState ) => {
	const {
		availablePaymentMethods,
		availableExpressPaymentMethods,
		paymentMethodsInitialized,
		expressPaymentMethodsInitialized,
	} = state;

	if ( ! paymentMethodsInitialized || ! expressPaymentMethodsInitialized ) {
		return {};
	}

	return Object.fromEntries(
		Object.entries( globalPaymentMethods ).filter( ( [ k ] ) => {
			return ! (
				k in
				{
					...availablePaymentMethods,
					...availableExpressPaymentMethods,
				}
			);
		} )
	);
};

export const getSavedPaymentMethods = ( state: PaymentState ) => {
	return state.savedPaymentMethods;
};

/**
 * Filters the list of saved payment methods and returns only the ones which
 * are active and supported by the payment gateway
 */
export const getActiveSavedPaymentMethods = ( state: PaymentState ) => {
	const availablePaymentMethodKeys = Object.keys(
		state.availablePaymentMethods
	);

	return filterActiveSavedPaymentMethods(
		availablePaymentMethodKeys,
		state.savedPaymentMethods
	);
};

export const paymentMethodsInitialized = ( state: PaymentState ) => {
	return state.paymentMethodsInitialized;
};

export const expressPaymentMethodsInitialized = ( state: PaymentState ) => {
	return state.expressPaymentMethodsInitialized;
};

/**
 * @deprecated - Use these selectors instead: isPaymentIdle, isPaymentProcessing,
 * hasPaymentError
 */
export const getCurrentStatus = ( state: PaymentState ) => {
	deprecated( 'getCurrentStatus', {
		since: '8.9.0',
		alternative: 'isPaymentIdle, isPaymentProcessing, hasPaymentError',
		plugin: 'WooCommerce Blocks',
		link: 'https://github.com/woocommerce/woocommerce-blocks/pull/7666',
	} );

	return {
		get isPristine() {
			deprecated( 'isPristine', {
				since: '9.6.0',
				alternative: 'isIdle',
				plugin: 'WooCommerce Blocks',
			} );
			return isPaymentIdle( state );
		}, // isPristine is the same as isIdle.
		isIdle: isPaymentIdle( state ),
		isStarted: isExpressPaymentStarted( state ),
		isProcessing: isPaymentProcessing( state ),
		get isFinished() {
			deprecated( 'isFinished', {
				since: '9.6.0',
				plugin: 'WooCommerce Blocks',
				link: 'https://github.com/woocommerce/woocommerce-blocks/pull/8110',
			} );
			return hasPaymentError( state ) || isPaymentReady( state );
		},
		hasError: hasPaymentError( state ),
		get hasFailed() {
			deprecated( 'hasFailed', {
				since: '9.6.0',
				plugin: 'WooCommerce Blocks',
				link: 'https://github.com/woocommerce/woocommerce-blocks/pull/8110',
			} );
			return hasPaymentError( state );
		},
		get isSuccessful() {
			deprecated( 'isSuccessful', {
				since: '9.6.0',
				plugin: 'WooCommerce Blocks',
				link: 'https://github.com/woocommerce/woocommerce-blocks/pull/8110',
			} );
			return isPaymentReady( state );
		},
		isDoingExpressPayment: isExpressPaymentMethodActive( state ),
	};
};

export const getShouldSavePaymentMethod = ( state: PaymentState ) => {
	return state.shouldSavePaymentMethod;
};

export const getPaymentResult = ( state: PaymentState ) => {
	return state.paymentResult;
};

// We should avoid using this selector and instead use the focused selectors
// We're keeping it because it's used in our unit test: assets/js/blocks/cart-checkout-shared/payment-methods/test/payment-methods.js
// to mock the selectors.
export const getState = ( state: PaymentState ) => {
	return state;
};

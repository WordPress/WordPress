/**
 * External dependencies
 */
import {
	PlainPaymentMethods,
	PlainExpressPaymentMethods,
} from '@woocommerce/types';
import type { PaymentResult } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { ACTION_TYPES } from './action-types';
import { checkPaymentMethodsCanPay } from './utils/check-payment-methods';
import { setDefaultPaymentMethod } from './utils/set-default-payment-method';

// `Thunks are functions that can be dispatched, similar to actions creators
export * from './thunks';

export const __internalSetPaymentIdle = () => ( {
	type: ACTION_TYPES.SET_PAYMENT_IDLE,
} );

export const __internalSetExpressPaymentStarted = () => ( {
	type: ACTION_TYPES.SET_EXPRESS_PAYMENT_STARTED,
} );

export const __internalSetPaymentProcessing = () => ( {
	type: ACTION_TYPES.SET_PAYMENT_PROCESSING,
} );

export const __internalSetPaymentError = () => ( {
	type: ACTION_TYPES.SET_PAYMENT_ERROR,
} );

export const __internalSetPaymentReady = () => ( {
	type: ACTION_TYPES.SET_PAYMENT_READY,
} );

/**
 * Set whether the payment methods have been initialised or not
 *
 * @param  initialized True if the `checkCanPay` methods have been run on all available payment methods
 */
export const __internalSetPaymentMethodsInitialized = (
	initialized: boolean
) => {
	return async ( { select, dispatch } ) => {
		// If the currently selected method is not in this new list, then we need to select a new one, or select a default.
		const methods = select.getAvailablePaymentMethods();
		if ( initialized ) {
			await setDefaultPaymentMethod( methods );
		}
		dispatch( {
			type: ACTION_TYPES.SET_PAYMENT_METHODS_INITIALIZED,
			initialized,
		} );
	};
};

/**
 * Set whether the express payment methods have been initialised or not
 *
 * @param  initialized True if the `checkCanPay` methods have been run on all express available payment methods
 */
export const __internalSetExpressPaymentMethodsInitialized = (
	initialized: boolean
) => ( {
	type: ACTION_TYPES.SET_EXPRESS_PAYMENT_METHODS_INITIALIZED,
	initialized,
} );

/**
 * Set a flag for whether to save the current payment method for next time
 *
 * @param  shouldSavePaymentMethod Whether to save the current payment method for next time
 */
export const __internalSetShouldSavePaymentMethod = (
	shouldSavePaymentMethod: boolean
) => ( {
	type: ACTION_TYPES.SET_SHOULD_SAVE_PAYMENT_METHOD,
	shouldSavePaymentMethod,
} );

/**
 * Set the payment method the user has chosen. This should change every time the user selects a new payment method
 *
 * @param  activePaymentMethod The name of the payment method selected by the user
 * @param  paymentMethodData   The extra data associated with a payment
 */
export const __internalSetActivePaymentMethod = (
	activePaymentMethod: string,
	paymentMethodData: Record< string, unknown > = {}
) => ( {
	type: ACTION_TYPES.SET_ACTIVE_PAYMENT_METHOD,
	activePaymentMethod,
	paymentMethodData,
} );

/**
 * Set the extra data for the chosen payment method
 *
 * @param  paymentMethodData The extra data associated with a payment
 */
export const __internalSetPaymentMethodData = (
	paymentMethodData: Record< string, unknown > = {}
) => ( {
	type: ACTION_TYPES.SET_PAYMENT_METHOD_DATA,
	paymentMethodData,
} );

/**
 * Store the result of the payment attempt from the /checkout StoreApi call
 *
 * @param  data The result of the payment attempt through the StoreApi /checkout endpoints
 */
export const __internalSetPaymentResult = ( data: PaymentResult ) => ( {
	type: ACTION_TYPES.SET_PAYMENT_RESULT,
	data,
} );

/**
 * Set the available payment methods.
 * An available payment method is one that has been validated and can make a payment.
 */
export const __internalSetAvailablePaymentMethods = (
	paymentMethods: PlainPaymentMethods
) => {
	return async ( { dispatch, select } ) => {
		// If the currently selected method is not in this new list, then we need to select a new one, or select a default.
		const activePaymentMethod = select.getActivePaymentMethod();
		if ( ! ( activePaymentMethod in paymentMethods ) ) {
			await setDefaultPaymentMethod( paymentMethods );
		}
		dispatch( {
			type: ACTION_TYPES.SET_AVAILABLE_PAYMENT_METHODS,
			paymentMethods,
		} );
	};
};

/**
 * Set the available express payment methods.
 * An available payment method is one that has been validated and can make a payment.
 */
export const __internalSetAvailableExpressPaymentMethods = (
	paymentMethods: PlainExpressPaymentMethods
) => ( {
	type: ACTION_TYPES.SET_AVAILABLE_EXPRESS_PAYMENT_METHODS,
	paymentMethods,
} );

/**
 * Remove a payment method name from the available payment methods.
 * This is called when a payment method is removed from the registry.
 */
export const __internalRemoveAvailablePaymentMethod = ( name: string ) => ( {
	type: ACTION_TYPES.REMOVE_AVAILABLE_PAYMENT_METHOD,
	name,
} );

/**
 * Remove an express payment method name from the available payment methods.
 * This is called when an express payment method is removed from the registry.
 */
export const __internalRemoveAvailableExpressPaymentMethod = (
	name: string
) => ( {
	type: ACTION_TYPES.REMOVE_AVAILABLE_EXPRESS_PAYMENT_METHOD,
	name,
} );

/**
 * The store is initialised once we have checked whether the payment methods registered can pay or not
 */
export function __internalUpdateAvailablePaymentMethods() {
	return async ( { select, dispatch } ) => {
		const expressRegistered = await checkPaymentMethodsCanPay( true );
		const registered = await checkPaymentMethodsCanPay( false );
		const { paymentMethodsInitialized, expressPaymentMethodsInitialized } =
			select;
		if ( registered && ! paymentMethodsInitialized() ) {
			dispatch( __internalSetPaymentMethodsInitialized( true ) );
		}
		if ( expressRegistered && ! expressPaymentMethodsInitialized() ) {
			dispatch( __internalSetExpressPaymentMethodsInitialized( true ) );
		}
	};
}

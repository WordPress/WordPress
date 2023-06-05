/**
 * External dependencies
 */
import type { EmptyObjectType, PaymentResult } from '@woocommerce/types';
import { getSetting } from '@woocommerce/settings';
import {
	PlainPaymentMethods,
	PlainExpressPaymentMethods,
} from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { SavedPaymentMethod } from './types';
import { STATUS as PAYMENT_STATUS } from './constants';

export interface PaymentState {
	status: string;
	activePaymentMethod: string;
	activeSavedToken: string;
	// Available payment methods are payment methods which have been validated and can make payment.
	availablePaymentMethods: PlainPaymentMethods;
	availableExpressPaymentMethods: PlainExpressPaymentMethods;
	savedPaymentMethods:
		| Record< string, SavedPaymentMethod[] >
		| EmptyObjectType;
	paymentMethodData: Record< string, unknown >;
	paymentResult: PaymentResult | null;
	paymentMethodsInitialized: boolean;
	expressPaymentMethodsInitialized: boolean;
	shouldSavePaymentMethod: boolean;
}

export const defaultPaymentState: PaymentState = {
	status: PAYMENT_STATUS.IDLE,
	activePaymentMethod: '',
	activeSavedToken: '',
	availablePaymentMethods: {},
	availableExpressPaymentMethods: {},
	savedPaymentMethods: getSetting<
		Record< string, SavedPaymentMethod[] > | EmptyObjectType
	>( 'customerPaymentMethods', {} ),
	paymentMethodData: {},
	paymentResult: null,
	paymentMethodsInitialized: false,
	expressPaymentMethodsInitialized: false,
	shouldSavePaymentMethod: false,
};

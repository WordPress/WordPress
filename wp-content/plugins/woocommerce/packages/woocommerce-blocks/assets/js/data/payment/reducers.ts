/**
 * External dependencies
 */
import type { Reducer } from 'redux';
import { objectHasProp, PaymentResult } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { defaultPaymentState, PaymentState } from './default-state';
import { ACTION_TYPES } from './action-types';
import { STATUS } from './constants';

const reducer: Reducer< PaymentState > = (
	state = defaultPaymentState,
	action
) => {
	let newState = state;
	switch ( action.type ) {
		case ACTION_TYPES.SET_PAYMENT_IDLE:
			newState = {
				...state,
				status: STATUS.IDLE,
			};
			break;

		case ACTION_TYPES.SET_EXPRESS_PAYMENT_STARTED:
			newState = {
				...state,
				status: STATUS.EXPRESS_STARTED,
			};
			break;

		case ACTION_TYPES.SET_PAYMENT_PROCESSING:
			newState = {
				...state,
				status: STATUS.PROCESSING,
			};
			break;

		case ACTION_TYPES.SET_PAYMENT_READY:
			newState = {
				...state,
				status: STATUS.READY,
			};
			break;

		case ACTION_TYPES.SET_PAYMENT_ERROR:
			newState = {
				...state,
				status: STATUS.ERROR,
			};
			break;

		case ACTION_TYPES.SET_SHOULD_SAVE_PAYMENT_METHOD:
			newState = {
				...state,
				shouldSavePaymentMethod: action.shouldSavePaymentMethod,
			};
			break;

		case ACTION_TYPES.SET_PAYMENT_METHOD_DATA:
			newState = {
				...state,
				paymentMethodData: action.paymentMethodData,
			};
			break;

		case ACTION_TYPES.SET_PAYMENT_RESULT:
			newState = {
				...state,
				paymentResult: action.data as PaymentResult,
			};
			break;

		case ACTION_TYPES.REMOVE_AVAILABLE_PAYMENT_METHOD:
			const previousAvailablePaymentMethods = {
				...state.availablePaymentMethods,
			};
			delete previousAvailablePaymentMethods[ action.name ];

			newState = {
				...state,
				availablePaymentMethods: {
					...previousAvailablePaymentMethods,
				},
			};
			break;

		case ACTION_TYPES.REMOVE_AVAILABLE_EXPRESS_PAYMENT_METHOD:
			const previousAvailableExpressPaymentMethods = {
				...state.availablePaymentMethods,
			};
			delete previousAvailableExpressPaymentMethods[ action.name ];
			newState = {
				...state,
				availableExpressPaymentMethods: {
					...previousAvailableExpressPaymentMethods,
				},
			};
			break;

		case ACTION_TYPES.SET_PAYMENT_METHODS_INITIALIZED:
			newState = {
				...state,
				paymentMethodsInitialized: action.initialized,
			};
			break;

		case ACTION_TYPES.SET_EXPRESS_PAYMENT_METHODS_INITIALIZED:
			newState = {
				...state,
				expressPaymentMethodsInitialized: action.initialized,
			};
			break;

		case ACTION_TYPES.SET_AVAILABLE_PAYMENT_METHODS:
			newState = {
				...state,
				availablePaymentMethods: action.paymentMethods,
			};
			break;

		case ACTION_TYPES.SET_AVAILABLE_EXPRESS_PAYMENT_METHODS:
			newState = {
				...state,
				availableExpressPaymentMethods: action.paymentMethods,
			};
			break;

		case ACTION_TYPES.SET_ACTIVE_PAYMENT_METHOD:
			const activeSavedToken =
				typeof state.paymentMethodData === 'object' &&
				objectHasProp( action.paymentMethodData, 'token' )
					? action.paymentMethodData.token + ''
					: '';
			newState = {
				...state,
				activeSavedToken,
				activePaymentMethod: action.activePaymentMethod,
				paymentMethodData:
					action.paymentMethodData || state.paymentMethodData,
			};
			break;
		default:
			return newState;
	}
	return newState;
};
export type State = ReturnType< typeof reducer >;

export default reducer;

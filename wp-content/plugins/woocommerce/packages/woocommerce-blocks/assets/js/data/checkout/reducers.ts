/**
 * Internal dependencies
 */
import { ACTION_TYPES as types } from './action-types';
import { STATUS } from './constants';
import { defaultState } from './default-state';
import { CheckoutAction } from './actions';

const reducer = ( state = defaultState, action: CheckoutAction ) => {
	let newState = state;
	switch ( action.type ) {
		case types.SET_IDLE:
			newState =
				state.status !== STATUS.IDLE
					? {
							...state,
							status: STATUS.IDLE,
					  }
					: state;
			break;

		case types.SET_REDIRECT_URL:
			newState =
				action.redirectUrl !== undefined &&
				action.redirectUrl !== state.redirectUrl
					? {
							...state,
							redirectUrl: action.redirectUrl,
					  }
					: state;
			break;

		case types.SET_COMPLETE:
			newState = {
				...state,
				status: STATUS.COMPLETE,
				redirectUrl:
					typeof action.data?.redirectUrl === 'string'
						? action.data.redirectUrl
						: state.redirectUrl,
			};
			break;
		case types.SET_PROCESSING:
			newState = {
				...state,
				status: STATUS.PROCESSING,
				hasError: false,
			};
			break;

		case types.SET_BEFORE_PROCESSING:
			newState = {
				...state,
				status: STATUS.BEFORE_PROCESSING,
				hasError: false,
			};
			break;

		case types.SET_AFTER_PROCESSING:
			newState = {
				...state,
				status: STATUS.AFTER_PROCESSING,
			};
			break;

		case types.SET_HAS_ERROR:
			newState = {
				...state,
				hasError: action.hasError,
				status:
					state.status === STATUS.PROCESSING ||
					state.status === STATUS.BEFORE_PROCESSING
						? STATUS.IDLE
						: state.status,
			};
			break;

		case types.INCREMENT_CALCULATING:
			newState = {
				...state,
				calculatingCount: state.calculatingCount + 1,
			};
			break;

		case types.DECREMENT_CALCULATING:
			newState = {
				...state,
				calculatingCount: Math.max( 0, state.calculatingCount - 1 ),
			};
			break;

		case types.SET_CUSTOMER_ID:
			if ( action.customerId !== undefined ) {
				newState = {
					...state,
					customerId: action.customerId,
				};
			}
			break;

		case types.SET_USE_SHIPPING_AS_BILLING:
			if (
				action.useShippingAsBilling !== undefined &&
				action.useShippingAsBilling !== state.useShippingAsBilling
			) {
				newState = {
					...state,
					useShippingAsBilling: action.useShippingAsBilling,
				};
			}
			break;

		case types.SET_SHOULD_CREATE_ACCOUNT:
			if (
				action.shouldCreateAccount !== undefined &&
				action.shouldCreateAccount !== state.shouldCreateAccount
			) {
				newState = {
					...state,
					shouldCreateAccount: action.shouldCreateAccount,
				};
			}
			break;

		case types.SET_PREFERS_COLLECTION:
			if (
				action.prefersCollection !== undefined &&
				action.prefersCollection !== state.prefersCollection
			) {
				newState = {
					...state,
					prefersCollection: action.prefersCollection,
				};
			}
			break;

		case types.SET_ORDER_NOTES:
			if (
				action.orderNotes !== undefined &&
				state.orderNotes !== action.orderNotes
			) {
				newState = {
					...state,
					orderNotes: action.orderNotes,
				};
			}
			break;

		case types.SET_EXTENSION_DATA:
			if (
				action.extensionData !== undefined &&
				action.namespace !== undefined
			) {
				newState = {
					...state,
					extensionData: {
						...state.extensionData,
						[ action.namespace ]: action.replace
							? action.extensionData
							: {
									...state.extensionData[ action.namespace ],
									...action.extensionData,
							  },
					},
				};
			}
			break;
	}
	return newState;
};

export default reducer;

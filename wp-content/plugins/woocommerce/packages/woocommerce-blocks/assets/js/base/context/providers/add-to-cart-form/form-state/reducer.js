/**
 * Internal dependencies
 */
import { ACTION_TYPES, DEFAULT_STATE, STATUS } from './constants';

const {
	SET_PRISTINE,
	SET_IDLE,
	SET_DISABLED,
	SET_PROCESSING,
	SET_BEFORE_PROCESSING,
	SET_AFTER_PROCESSING,
	SET_PROCESSING_RESPONSE,
	SET_HAS_ERROR,
	SET_NO_ERROR,
	SET_QUANTITY,
	SET_REQUEST_PARAMS,
} = ACTION_TYPES;

const {
	PRISTINE,
	IDLE,
	DISABLED,
	PROCESSING,
	BEFORE_PROCESSING,
	AFTER_PROCESSING,
} = STATUS;

/**
 * Reducer for the checkout state
 *
 * @param {Object} state           Current state.
 * @param {Object} action          Incoming action object.
 * @param {number} action.quantity Incoming quantity.
 * @param {string} action.type     Type of action.
 * @param {Object} action.data     Incoming payload for action.
 */
export const reducer = ( state = DEFAULT_STATE, { quantity, type, data } ) => {
	let newState;
	switch ( type ) {
		case SET_PRISTINE:
			newState = DEFAULT_STATE;
			break;
		case SET_IDLE:
			newState =
				state.status !== IDLE
					? {
							...state,
							status: IDLE,
					  }
					: state;
			break;
		case SET_DISABLED:
			newState =
				state.status !== DISABLED
					? {
							...state,
							status: DISABLED,
					  }
					: state;
			break;
		case SET_QUANTITY:
			newState =
				quantity !== state.quantity
					? {
							...state,
							quantity,
					  }
					: state;
			break;
		case SET_REQUEST_PARAMS:
			newState = {
				...state,
				requestParams: {
					...state.requestParams,
					...data,
				},
			};
			break;
		case SET_PROCESSING_RESPONSE:
			newState = {
				...state,
				processingResponse: data,
			};
			break;
		case SET_PROCESSING:
			newState =
				state.status !== PROCESSING
					? {
							...state,
							status: PROCESSING,
							hasError: false,
					  }
					: state;
			// clear any error state.
			newState =
				newState.hasError === false
					? newState
					: { ...newState, hasError: false };
			break;
		case SET_BEFORE_PROCESSING:
			newState =
				state.status !== BEFORE_PROCESSING
					? {
							...state,
							status: BEFORE_PROCESSING,
							hasError: false,
					  }
					: state;
			break;
		case SET_AFTER_PROCESSING:
			newState =
				state.status !== AFTER_PROCESSING
					? {
							...state,
							status: AFTER_PROCESSING,
					  }
					: state;
			break;
		case SET_HAS_ERROR:
			newState = state.hasError
				? state
				: {
						...state,
						hasError: true,
				  };
			newState =
				state.status === PROCESSING ||
				state.status === BEFORE_PROCESSING
					? {
							...newState,
							status: IDLE,
					  }
					: newState;
			break;
		case SET_NO_ERROR:
			newState = state.hasError
				? {
						...state,
						hasError: false,
				  }
				: state;
			break;
	}
	// automatically update state to idle from pristine as soon as it initially changes.
	if (
		newState !== state &&
		type !== SET_PRISTINE &&
		newState.status === PRISTINE
	) {
		newState.status = IDLE;
	}
	return newState;
};

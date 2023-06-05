/**
 * Internal dependencies
 */
import { ACTION_TYPES } from './constants';

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

/**
 * All the actions that can be dispatched for the checkout.
 */
export const actions = {
	setPristine: () => ( {
		type: SET_PRISTINE,
	} ),
	setIdle: () => ( {
		type: SET_IDLE,
	} ),
	setDisabled: () => ( {
		type: SET_DISABLED,
	} ),
	setProcessing: () => ( {
		type: SET_PROCESSING,
	} ),
	setBeforeProcessing: () => ( {
		type: SET_BEFORE_PROCESSING,
	} ),
	setAfterProcessing: () => ( {
		type: SET_AFTER_PROCESSING,
	} ),
	setProcessingResponse: ( data ) => ( {
		type: SET_PROCESSING_RESPONSE,
		data,
	} ),
	setHasError: ( hasError = true ) => {
		const type = hasError ? SET_HAS_ERROR : SET_NO_ERROR;
		return { type };
	},
	setQuantity: ( quantity ) => ( {
		type: SET_QUANTITY,
		quantity,
	} ),
	setRequestParams: ( data ) => ( {
		type: SET_REQUEST_PARAMS,
		data,
	} ),
};

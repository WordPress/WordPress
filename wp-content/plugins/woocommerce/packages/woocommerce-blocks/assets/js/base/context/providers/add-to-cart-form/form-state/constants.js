/**
 * @type {import("@woocommerce/type-defs/add-to-cart-form").AddToCartFormStatusConstants}
 */
export const STATUS = {
	PRISTINE: 'pristine',
	IDLE: 'idle',
	DISABLED: 'disabled',
	PROCESSING: 'processing',
	BEFORE_PROCESSING: 'before_processing',
	AFTER_PROCESSING: 'after_processing',
};

export const DEFAULT_STATE = {
	status: STATUS.PRISTINE,
	hasError: false,
	quantity: 0,
	processingResponse: null,
	requestParams: {},
};
export const ACTION_TYPES = {
	SET_PRISTINE: 'set_pristine',
	SET_IDLE: 'set_idle',
	SET_DISABLED: 'set_disabled',
	SET_PROCESSING: 'set_processing',
	SET_BEFORE_PROCESSING: 'set_before_processing',
	SET_AFTER_PROCESSING: 'set_after_processing',
	SET_PROCESSING_RESPONSE: 'set_processing_response',
	SET_HAS_ERROR: 'set_has_error',
	SET_NO_ERROR: 'set_no_error',
	SET_QUANTITY: 'set_quantity',
	SET_REQUEST_PARAMS: 'set_request_params',
};

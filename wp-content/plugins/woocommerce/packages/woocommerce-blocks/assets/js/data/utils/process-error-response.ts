/**
 * External dependencies
 */
import { createNotice, DEFAULT_ERROR_MESSAGE } from '@woocommerce/base-utils';
import { decodeEntities } from '@wordpress/html-entities';
import {
	objectHasProp,
	ApiErrorResponse,
	isApiErrorResponse,
} from '@woocommerce/types';
import { noticeContexts } from '@woocommerce/base-context/event-emit/utils';

type ApiParamError = {
	param: string;
	id: string;
	code: string;
	message: string;
};

/**
 * Flattens error details which are returned from the API when multiple params are not valid.
 *
 * - Codes will be prefixed with the param. For example, `invalid_email` becomes `billing_address_invalid_email`.
 * - Additional error messages will be flattened alongside the main error message.
 * - Supports 1 level of nesting.
 * - Decodes HTML entities in error messages.
 */
export const getErrorDetails = (
	response: ApiErrorResponse
): ApiParamError[] => {
	const errorDetails = objectHasProp( response.data, 'details' )
		? Object.entries( response.data.details )
		: null;

	if ( ! errorDetails ) {
		return [];
	}

	return errorDetails.reduce(
		(
			acc,
			[
				param,
				{ code, message, additional_errors: additionalErrors = [] },
			]
		) => {
			return [
				...acc,
				{
					param,
					id: `${ param }_${ code }`,
					code,
					message: decodeEntities( message ),
				},
				...( Array.isArray( additionalErrors )
					? additionalErrors.flatMap( ( additionalError ) => {
							if (
								! objectHasProp( additionalError, 'code' ) ||
								! objectHasProp( additionalError, 'message' )
							) {
								return [];
							}
							return [
								{
									param,
									id: `${ param }_${ additionalError.code }`,
									code: additionalError.code,
									message: decodeEntities(
										additionalError.message
									),
								},
							];
					  } )
					: [] ),
			];
		},
		[] as ApiParamError[]
	);
};

/**
 * Gets appropriate error context from error code.
 */
const getErrorContextFromCode = ( code: string ): string => {
	switch ( code ) {
		case 'woocommerce_rest_missing_email_address':
		case 'woocommerce_rest_invalid_email_address':
			return noticeContexts.CONTACT_INFORMATION;
		default:
			return noticeContexts.CART;
	}
};

/**
 * Gets appropriate error context from error param name.
 */
const getErrorContextFromParam = ( param: string ): string | undefined => {
	switch ( param ) {
		case 'invalid_email':
			return noticeContexts.CONTACT_INFORMATION;
		case 'billing_address':
			return noticeContexts.BILLING_ADDRESS;
		case 'shipping_address':
			return noticeContexts.SHIPPING_ADDRESS;
		default:
			return undefined;
	}
};

/**
 * Processes the response for an invalid param error, with response code rest_invalid_param.
 */
const processInvalidParamResponse = (
	response: ApiErrorResponse,
	context: string | undefined
) => {
	const errorDetails = getErrorDetails( response );

	errorDetails.forEach( ( { code, message, id, param } ) => {
		createNotice( 'error', message, {
			id,
			context:
				context ||
				getErrorContextFromParam( param ) ||
				getErrorContextFromCode( code ),
		} );
	} );
};

/**
 * Takes an API response object and creates error notices to display to the customer.
 *
 * This is where we can handle specific error codes and display notices in specific contexts.
 */
export const processErrorResponse = (
	response: ApiErrorResponse | null,
	context?: string | undefined
) => {
	if ( ! isApiErrorResponse( response ) ) {
		return;
	}

	if ( response.code === 'rest_invalid_param' ) {
		return processInvalidParamResponse( response, context );
	}

	let errorMessage =
		decodeEntities( response.message ) || DEFAULT_ERROR_MESSAGE;

	// Replace the generic invalid JSON message with something more user friendly.
	if ( response.code === 'invalid_json' ) {
		errorMessage = DEFAULT_ERROR_MESSAGE;
	}

	createNotice( 'error', errorMessage, {
		id: response.code,
		context: context || getErrorContextFromCode( response.code ),
	} );
};

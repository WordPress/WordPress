/**
 * Internal dependencies
 */
import { isObject, objectHasProp } from './object';
import type { ApiErrorResponse } from '../type-defs';

// Type guard for ApiErrorResponse.
export const isApiErrorResponse = (
	response: unknown
): response is ApiErrorResponse => {
	return (
		isObject( response ) &&
		objectHasProp( response, 'code' ) &&
		objectHasProp( response, 'message' )
	);
};

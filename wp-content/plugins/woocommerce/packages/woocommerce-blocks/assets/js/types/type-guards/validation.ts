/**
 * External dependencies
 */
import {
	FieldValidationStatus,
	isBoolean,
	isObject,
	isString,
	objectHasProp,
} from '@woocommerce/types';

/**
 * Whether the given status is a valid FieldValidationStatus.
 */
export const isValidFieldValidationStatus = (
	status: unknown
): status is FieldValidationStatus => {
	return (
		isObject( status ) &&
		objectHasProp( status, 'message' ) &&
		objectHasProp( status, 'hidden' ) &&
		isString( status.message ) &&
		isBoolean( status.hidden )
	);
};

/**
 * Whether the passed object is a valid validation errors object. If this is true, it can be set on the
 * wc/store/validation store without any issue.
 */
export const isValidValidationErrorsObject = (
	errors: unknown
): errors is Record< string, FieldValidationStatus > => {
	return (
		isObject( errors ) &&
		Object.entries( errors ).every(
			( [ key, value ] ) =>
				isString( key ) && isValidFieldValidationStatus( value )
		)
	);
};

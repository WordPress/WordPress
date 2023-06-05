/**
 * External dependencies
 */
import deprecated from '@wordpress/deprecated';
import { FieldValidationStatus } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { ACTION_TYPES as types } from './action-types';
import { ReturnOrGeneratorYieldUnion } from '../mapped-types';

export const setValidationErrors = (
	errors: Record< string, FieldValidationStatus >
) => ( {
	type: types.SET_VALIDATION_ERRORS,
	errors,
} );

/**
 * Clears validation errors for the given ids.
 *
 * @param  errors Array of error ids to clear.
 */
export const clearValidationErrors = ( errors?: string[] | undefined ) => ( {
	type: types.CLEAR_VALIDATION_ERRORS,
	errors,
} );

export const clearAllValidationErrors = () => {
	deprecated( 'clearAllValidationErrors', {
		version: '9.0.0',
		alternative: 'clearValidationErrors',
		plugin: 'WooCommerce Blocks',
		link: 'https://github.com/woocommerce/woocommerce-blocks/pull/7601',
		hint: 'Calling `clearValidationErrors` with no arguments will clear all validation errors.',
	} );

	// Return clearValidationErrors which will clear all errors by defaults if no error ids are passed.
	return clearValidationErrors();
};

export const clearValidationError = ( error: string ) => ( {
	type: types.CLEAR_VALIDATION_ERROR,
	error,
} );

export const hideValidationError = ( error: string ) => ( {
	type: types.HIDE_VALIDATION_ERROR,
	error,
} );

export const showValidationError = ( error: string ) => ( {
	type: types.SHOW_VALIDATION_ERROR,
	error,
} );

export const showAllValidationErrors = () => ( {
	type: types.SHOW_ALL_VALIDATION_ERRORS,
} );

export type ValidationAction = ReturnOrGeneratorYieldUnion<
	| typeof setValidationErrors
	| typeof clearAllValidationErrors
	| typeof clearValidationError
	| typeof clearValidationErrors
	| typeof hideValidationError
	| typeof showValidationError
	| typeof showAllValidationErrors
>;

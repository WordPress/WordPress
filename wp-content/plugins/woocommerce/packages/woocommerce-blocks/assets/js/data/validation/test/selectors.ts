/**
 * External dependencies
 */
import { FieldValidationStatus } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import {
	getValidationErrorId,
	getValidationError,
	hasValidationErrors,
} from '../selectors';

describe( 'Validation selectors', () => {
	it( 'Gets the validation error', () => {
		const state: Record< string, FieldValidationStatus > = {
			validationError: {
				message: 'This is a test message',
				hidden: false,
			},
		};
		const validationError = getValidationError( state, 'validationError' );
		expect( validationError ).toEqual( {
			message: 'This is a test message',
			hidden: false,
		} );
	} );

	it( 'Gets the generated validation error ID', () => {
		const state: Record< string, FieldValidationStatus > = {
			validationError: {
				message: 'This is a test message',
				hidden: false,
			},
		};
		const validationErrorID = getValidationErrorId(
			state,
			'validationError'
		);
		expect( validationErrorID ).toEqual( `validate-error-validationError` );
	} );

	it( 'Checks if state has any validation errors', () => {
		const state: Record< string, FieldValidationStatus > = {
			validationError: {
				message: 'This is a test message',
				hidden: false,
			},
		};
		const validationErrors = hasValidationErrors( state );
		expect( validationErrors ).toEqual( true );
		const stateWithNoErrors: Record< string, FieldValidationStatus > = {};
		const stateWithNoErrorsCheckResult =
			hasValidationErrors( stateWithNoErrors );
		expect( stateWithNoErrorsCheckResult ).toEqual( false );
	} );
} );

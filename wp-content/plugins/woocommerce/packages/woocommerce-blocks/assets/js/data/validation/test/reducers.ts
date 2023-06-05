/**
 * External dependencies
 */
import { FieldValidationStatus } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import reducer from '../reducers';
import { ACTION_TYPES as types } from '.././action-types';
import { ValidationAction } from '../actions';

describe( 'Validation reducer', () => {
	it( 'Sets a single validation error', () => {
		const singleValidationAction: ValidationAction = {
			type: types.SET_VALIDATION_ERRORS,
			errors: {
				singleValidationError: {
					message: 'This is a single validation error message',
					hidden: false,
				},
			},
		};
		const nextState = reducer( {}, singleValidationAction );
		expect( nextState ).toEqual( {
			singleValidationError: {
				message: 'This is a single validation error message',
				hidden: false,
			},
		} );
	} );

	it( 'Does not add new errors if the same error already exists in state', () => {
		const state: Record< string, FieldValidationStatus > = {
			existingError: {
				message: 'This is an existing error message',
				hidden: false,
			},
		};
		const existingErrorValidation: ValidationAction = {
			type: types.SET_VALIDATION_ERRORS,
			errors: {
				existingError: {
					message: 'This is an existing error message',
					hidden: false,
				},
			},
		};
		const nextState = reducer( state, existingErrorValidation );
		expect( nextState ).toEqual( {
			existingError: {
				message: 'This is an existing error message',
				hidden: false,
			},
		} );
	} );

	it( 'Does not add new errors if error message is not string, but keeps existing errors', () => {
		const integerErrorAction: ValidationAction = {
			type: types.SET_VALIDATION_ERRORS,
			errors: {
				integerError: {
					// eslint-disable-next-line @typescript-eslint/ban-ts-comment
					// @ts-ignore ignoring because we're testing runtime errors with integers.
					message: 1234,
					hidden: false,
				},
			},
		};
		const nextState = reducer( {}, integerErrorAction );
		expect( nextState ).not.toHaveProperty( 'integerError' );
	} );

	it( 'Updates existing error if message or hidden property changes', () => {
		const state: Record< string, FieldValidationStatus > = {
			existingValidationError: {
				message: 'This is an existing error message',
				hidden: false,
			},
		};
		const updateExistingErrorAction: ValidationAction = {
			type: types.SET_VALIDATION_ERRORS,
			errors: {
				existingValidationError: {
					message: 'This is an existing error message',
					hidden: true,
				},
			},
		};
		const nextState = reducer( state, updateExistingErrorAction );
		expect( nextState ).toEqual( {
			existingValidationError: {
				message: 'This is an existing error message',
				hidden: true,
			},
		} );
	} );

	it( 'Appends new errors to list of existing errors', () => {
		const state: Record< string, FieldValidationStatus > = {
			existingError: {
				message: 'This is an existing error message',
				hidden: false,
			},
		};
		const addNewError: ValidationAction = {
			type: types.SET_VALIDATION_ERRORS,
			errors: {
				newError: {
					message: 'This is a new error',
					hidden: false,
				},
			},
		};
		const nextState = reducer( state, addNewError );
		expect( nextState ).toEqual( {
			existingError: {
				message: 'This is an existing error message',
				hidden: false,
			},
			newError: {
				message: 'This is a new error',
				hidden: false,
			},
		} );
	} );

	it( 'Clears all validation errors', () => {
		const state: Record< string, FieldValidationStatus > = {
			existingError: {
				message: 'This is an existing error message',
				hidden: false,
			},
		};
		const clearAllErrors: ValidationAction = {
			type: types.CLEAR_VALIDATION_ERRORS,
			errors: undefined,
		};
		const nextState = reducer( state, clearAllErrors );
		expect( nextState ).toEqual( {} );
	} );

	it( 'Clears a single validation error', () => {
		const state: Record< string, FieldValidationStatus > = {
			existingError: {
				message: 'This is an existing error message',
				hidden: false,
			},
			testError: {
				message: 'This is error should not be removed',
				hidden: false,
			},
		};
		const clearError: ValidationAction = {
			type: types.CLEAR_VALIDATION_ERROR,
			error: 'existingError',
		};
		const nextState = reducer( state, clearError );
		expect( nextState ).not.toHaveProperty( 'existingError' );
		expect( nextState ).toHaveProperty( 'testError' );
	} );

	it( 'Clears multiple validation errors', () => {
		const state: Record< string, FieldValidationStatus > = {
			existingError: {
				message: 'This is an existing error message',
				hidden: false,
			},
			testError: {
				message: 'This is error should also be removed',
				hidden: false,
			},
		};
		const clearError: ValidationAction = {
			type: types.CLEAR_VALIDATION_ERRORS,
			errors: [ 'existingError', 'testError' ],
		};
		const nextState = reducer( state, clearError );
		expect( nextState ).not.toHaveProperty( 'existingError' );
		expect( nextState ).not.toHaveProperty( 'testError' );
	} );

	it( 'Hides a single validation error', () => {
		const state: Record< string, FieldValidationStatus > = {
			existingError: {
				message: 'This is an existing error message',
				hidden: false,
			},
			testError: {
				message: 'This is error should not be removed',
				hidden: false,
			},
		};
		const testAction: ValidationAction = {
			type: types.HIDE_VALIDATION_ERROR,
			error: 'existingError',
		};
		const nextState = reducer( state, testAction );
		expect( nextState ).toEqual( {
			existingError: {
				message: 'This is an existing error message',
				hidden: true,
			},
			testError: {
				message: 'This is error should not be removed',
				hidden: false,
			},
		} );
	} );

	it( 'Shows a single validation error', () => {
		const state: Record< string, FieldValidationStatus > = {
			existingError: {
				message: 'This is an existing error message',
				hidden: true,
			},
			testError: {
				message: 'This is error should not be removed',
				hidden: true,
			},
			visibleError: {
				message: 'This is error should remain visible',
				hidden: false,
			},
		};
		const testAction: ValidationAction = {
			type: types.SHOW_VALIDATION_ERROR,
			error: 'existingError',
		};
		const nextState = reducer( state, testAction );
		expect( nextState ).toEqual( {
			existingError: {
				message: 'This is an existing error message',
				hidden: false,
			},
			testError: {
				message: 'This is error should not be removed',
				hidden: true,
			},
			visibleError: {
				message: 'This is error should remain visible',
				hidden: false,
			},
		} );
	} );

	it( 'Shows all validation errors', () => {
		const state: Record< string, FieldValidationStatus > = {
			firstExistingError: {
				message: 'This is first existing error message',
				hidden: true,
			},
			secondExistingError: {
				message: 'This is the second existing error message',
				hidden: true,
			},
		};
		const showAllErrors: ValidationAction = {
			type: types.SHOW_ALL_VALIDATION_ERRORS,
		};
		const nextState = reducer( state, showAllErrors );
		expect( nextState ).toEqual( {
			firstExistingError: {
				message: 'This is first existing error message',
				hidden: false,
			},
			secondExistingError: {
				message: 'This is the second existing error message',
				hidden: false,
			},
		} );
	} );
} );

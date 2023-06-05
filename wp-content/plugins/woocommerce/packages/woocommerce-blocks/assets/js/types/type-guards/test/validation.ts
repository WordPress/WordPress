/**
 * Internal dependencies
 */
import {
	isValidFieldValidationStatus,
	isValidValidationErrorsObject,
} from '../validation';

describe( 'validation type guards', () => {
	describe( 'isValidFieldValidationStatus', () => {
		it( 'identifies valid objects', () => {
			const valid = {
				message: 'message',
				hidden: false,
			};
			expect( isValidFieldValidationStatus( valid ) ).toBe( true );
		} );
		it( 'identifies invalid objects', () => {
			const invalid = {
				message: 'message',
				hidden: 'string',
			};
			expect( isValidFieldValidationStatus( invalid ) ).toBe( false );
			const noMessage = {
				hidden: false,
			};
			expect( isValidFieldValidationStatus( noMessage ) ).toBe( false );
		} );
	} );

	describe( 'isValidValidationErrorsObject', () => {
		it( 'identifies valid objects', () => {
			const valid = {
				'billing.first-name': {
					message: 'message',
					hidden: false,
				},
			};
			expect( isValidValidationErrorsObject( valid ) ).toBe( true );
		} );
		it( 'identifies invalid objects', () => {
			const invalid = {
				'billing.first-name': {
					message: 'message',
					hidden: 'string',
				},
			};
			expect( isValidValidationErrorsObject( invalid ) ).toBe( false );
			const noMessage = {
				'billing.first-name': {
					hidden: false,
				},
			};
			expect( isValidValidationErrorsObject( noMessage ) ).toBe( false );
		} );
	} );
} );

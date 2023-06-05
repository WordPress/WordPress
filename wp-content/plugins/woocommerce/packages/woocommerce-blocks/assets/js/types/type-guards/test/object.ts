/**
 * External dependencies
 */
import { isEmptyObject, isObject } from '@woocommerce/types';

describe( 'Object type-guards', () => {
	describe( 'Testing isObject()', () => {
		it( 'Correctly identifies an object', () => {
			expect( isObject( {} ) ).toBe( true );
			expect( isObject( { test: 'object' } ) ).toBe( true );
		} );
		it( 'Correctly rejects object-like things', () => {
			expect( isObject( [] ) ).toBe( false );
			expect( isObject( null ) ).toBe( false );
		} );
	} );
	describe( 'Testing isEmptyObject()', () => {
		it( 'Correctly identifies an empty object', () => {
			expect( isEmptyObject( {} ) ).toBe( true );
		} );
		it( 'Correctly identifies an not empty object', () => {
			expect( isEmptyObject( { name: 'Woo' } ) ).toBe( false );
		} );
	} );
} );

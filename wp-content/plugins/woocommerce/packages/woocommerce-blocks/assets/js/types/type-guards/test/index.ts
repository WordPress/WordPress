/**
 * External dependencies
 */
import { isObject } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { isBillingAddress, isShippingAddress } from '../address';

describe( 'type-guards', () => {
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

	describe( 'testing isShippingAddress()', () => {
		it( 'Correctly identifies a shipping address object', () => {
			expect( isShippingAddress( {} ) ).toBe( false );
			expect( isShippingAddress( { test: 'object' } ) ).toBe( false );

			const shippingAddress = {
				first_name: 'John',
				last_name: 'Doe',
				company: 'ACME',
				address_1: '123 Main St',
				address_2: 'Suite 1',
				city: 'Anytown',
				state: 'CA',
				postcode: '12345',
				country: 'US',
				phone: '555-555-5555',
			};
			expect( isShippingAddress( shippingAddress ) ).toBe( true );
		} );

		it( 'Correctly rejects non-shipping address objects', () => {
			const nonShippingAddress = {
				first_name: 'John',
				last_name: 'Doe',
				company: 'ACME',
				address_1: '123 Main St',
				city: 'Anytown',
				state: 'CA',
				postcode: '12345',
				country: 'US',
				phone: '555-555-5555',
				email: '',
			};
			expect( isShippingAddress( nonShippingAddress ) ).toBe( false );
		} );
	} );
	describe( 'testing isBillingAddress()', () => {
		it( 'Correctly identifies a shipping address object', () => {
			expect( isBillingAddress( {} ) ).toBe( false );
			expect( isBillingAddress( { test: 'object' } ) ).toBe( false );

			const billingAddress = {
				first_name: 'John',
				last_name: 'Doe',
				company: 'ACME',
				address_1: '123 Main St',
				address_2: 'Suite 1',
				city: 'Anytown',
				state: 'CA',
				postcode: '12345',
				country: 'US',
				phone: '555-555-5555',
				email: 'jon@doe.com',
			};
			expect( isBillingAddress( billingAddress ) ).toBe( true );
		} );

		it( 'Correctly rejects non-billing address objects', () => {
			const nonBillingAddress = {
				first_name: 'John',
				last_name: 'Doe',
				company: 'ACME',
				address_1: '123 Main St',
				city: 'Anytown',
				state: 'CA',
				country: 'US',
				phone: '555-555-5555',
				email: '',
			};
			expect( isBillingAddress( nonBillingAddress ) ).toBe( false );
		} );
	} );
} );

/**
 * External dependencies
 */
import { isEmpty } from '@woocommerce/types';

describe( 'Testing isEmpty()', () => {
	it( 'Correctly handles null', () => {
		expect( isEmpty( null ) ).toBe( true );
	} );
	it( 'Correctly handles undefined', () => {
		expect( isEmpty( undefined ) ).toBe( true );
	} );
	it( 'Correctly handles empty objects', () => {
		expect( isEmpty( {} ) ).toBe( true );
	} );
	it( 'Correctly handles empty arrays', () => {
		expect( isEmpty( [] ) ).toBe( true );
	} );
	it( 'Correctly handles empty strings', () => {
		expect( isEmpty( '' ) ).toBe( true );
	} );
	it( 'Correctly handles object with values', () => {
		expect( isEmpty( { a: '1' } ) ).toBe( false );
	} );
} );

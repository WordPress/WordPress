/**
 * External dependencies
 */
import {
	emptyHiddenAddressFields,
	isAddressComplete,
	formatShippingAddress,
} from '@woocommerce/base-utils';

describe( 'emptyHiddenAddressFields', () => {
	it( "Removes state from an address where the country doesn't use states", () => {
		const address = {
			first_name: 'Jonny',
			last_name: 'Awesome',
			company: 'WordPress',
			address_1: '123 Address Street',
			address_2: 'Address 2',
			city: 'Vienna',
			postcode: '1120',
			country: 'AT',
			state: 'CA', // This should be removed.
			email: 'jonny.awesome@email.com',
			phone: '',
		};
		const filteredAddress = emptyHiddenAddressFields( address );
		expect( filteredAddress ).toHaveProperty( 'state', '' );
	} );
} );

describe( 'isAddressComplete', () => {
	it( 'correctly checks empty addresses', () => {
		const address = {
			first_name: '',
			last_name: '',
			company: '',
			address_1: '',
			address_2: '',
			city: '',
			postcode: '',
			country: '',
			state: '',
			email: '',
			phone: '',
		};
		expect( isAddressComplete( address ) ).toBe( false );
	} );

	it( 'correctly checks incomplete addresses', () => {
		const address = {
			first_name: 'John',
			last_name: 'Doe',
			company: 'Company',
			address_1: '409 Main Street',
			address_2: 'Apt 1',
			city: '',
			postcode: '',
			country: '',
			state: '',
			email: 'john.doe@company',
			phone: '+1234567890',
		};
		expect( isAddressComplete( address ) ).toBe( false );

		address.city = 'London';
		expect( isAddressComplete( address ) ).toBe( false );

		address.postcode = 'W1T 4JG';
		address.country = 'GB';
		expect( isAddressComplete( address ) ).toBe( true );
	} );

	it( 'correctly checks complete addresses', () => {
		const address = {
			first_name: 'John',
			last_name: 'Doe',
			company: 'Company',
			address_1: '409 Main Street',
			address_2: 'Apt 1',
			city: 'London',
			postcode: 'W1T 4JG',
			country: 'GB',
			state: '',
			email: 'john.doe@company',
			phone: '+1234567890',
		};
		expect( isAddressComplete( address ) ).toBe( true );
	} );
} );

describe( 'formatShippingAddress', () => {
	it( 'returns null if address is empty', () => {
		const address = {
			first_name: '',
			last_name: '',
			company: '',
			address_1: '',
			address_2: '',
			city: '',
			postcode: '',
			country: '',
			state: '',
			email: '',
			phone: '',
		};
		expect( formatShippingAddress( address ) ).toBe( null );
	} );

	it( 'correctly returns the formatted address', () => {
		const address = {
			first_name: 'John',
			last_name: 'Doe',
			company: 'Company',
			address_1: '409 Main Street',
			address_2: 'Apt 1',
			city: 'London',
			postcode: 'W1T 4JG',
			country: 'GB',
			state: '',
			email: 'john.doe@company',
			phone: '+1234567890',
		};
		expect( formatShippingAddress( address ) ).toBe(
			'W1T 4JG, London, United Kingdom (UK)'
		);
	} );
} );

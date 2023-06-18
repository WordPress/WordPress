/**
 * External dependencies
 */
import * as wpDataFunctions from '@wordpress/data';
import { CART_STORE_KEY, VALIDATION_STORE_KEY } from '@woocommerce/block-data';

/**
 * Internal dependencies
 */
import { pushChanges } from '../push-changes';

// When first updating the customer data, we want to simulate a rejected update.
const updateCustomerDataMock = jest.fn().mockRejectedValue( 'error' );
const getCustomerDataMock = jest.fn().mockReturnValue( {
	billingAddress: {
		first_name: 'John',
		last_name: 'Doe',
		address_1: '123 Main St',
		address_2: '',
		city: 'New York',
		state: 'NY',
		postcode: '10001',
		country: 'US',
		email: 'john.doe@mail.com',
		phone: '555-555-5555',
	},
	shippingAddress: {
		first_name: 'John',
		last_name: 'Doe',
		address_1: '123 Main St',
		address_2: '',
		city: 'New York',
		state: 'NY',
		postcode: '10001',
		country: 'US',
		phone: '555-555-5555',
	},
} );

// Mocking select and dispatch here so we can control the actions/selectors used in pushChanges.
jest.mock( '@wordpress/data', () => ( {
	...jest.requireActual( '@wordpress/data' ),
	__esModule: true,
	select: jest.fn(),
	dispatch: jest.fn(),
} ) );

// Mocking the debounce method so we can use the callback directly without waiting for debounce.
jest.mock( '@woocommerce/base-utils', () => ( {
	...jest.requireActual( '@woocommerce/base-utils' ),
	__esModule: true,
	debounce: jest.fn( ( callback ) => callback ),
} ) );

// Mocking processErrorResponse because we don't actually care about processing the error response, we just don't want
// pushChanges to throw an error.
jest.mock( '../utils', () => ( {
	...jest.requireActual( '../utils' ),
	__esModule: true,
	processErrorResponse: jest.fn(),
} ) );

// Mocking updatePaymentMethods because this uses the mocked debounce earlier, and causes an error. Moreover, we don't
// need to update payment methods, they are not relevant to the tests in this file.
jest.mock( '../update-payment-methods', () => ( {
	debouncedUpdatePaymentMethods: jest.fn(),
	updatePaymentMethods: jest.fn(),
} ) );

describe( 'pushChanges', () => {
	beforeEach( () => {
		wpDataFunctions.select.mockImplementation( ( storeName: string ) => {
			if ( storeName === CART_STORE_KEY ) {
				return {
					...jest
						.requireActual( '@wordpress/data' )
						.select( storeName ),
					hasFinishedResolution: () => true,
					getCustomerData: getCustomerDataMock,
				};
			}
			if ( storeName === VALIDATION_STORE_KEY ) {
				return {
					...jest
						.requireActual( '@wordpress/data' )
						.select( storeName ),
					getValidationError: () => undefined,
				};
			}
			return jest.requireActual( '@wordpress/data' ).select( storeName );
		} );
		wpDataFunctions.dispatch.mockImplementation( ( storeName: string ) => {
			if ( storeName === CART_STORE_KEY ) {
				return {
					...jest
						.requireActual( '@wordpress/data' )
						.dispatch( storeName ),
					updateCustomerData: updateCustomerDataMock,
				};
			}
			return jest
				.requireActual( '@wordpress/data' )
				.dispatch( storeName );
		} );
	} );
	it( 'Keeps props dirty if data did not persist due to an error', async () => {
		// Run this without changing anything because the first run does not push data (the first run is populating what was received on page load).
		pushChanges();

		// Mock the returned value of `getCustomerData` to simulate a change in the shipping address.
		getCustomerDataMock.mockReturnValue( {
			billingAddress: {
				first_name: 'John',
				last_name: 'Doe',
				address_1: '123 Main St',
				address_2: '',
				city: 'New York',
				state: 'NY',
				postcode: '10001',
				country: 'US',
				email: 'john.doe@mail.com',
				phone: '555-555-5555',
			},
			shippingAddress: {
				first_name: 'John',
				last_name: 'Doe',
				address_1: '123 Main St',
				address_2: '',
				city: 'Houston',
				state: 'TX',
				postcode: 'ABCDEF',
				country: 'US',
				phone: '555-555-5555',
			},
		} );

		// Push these changes to the server, the `updateCustomerData` mock is set to reject (in the original mock at the top of the file), to simulate a server error.
		pushChanges();

		// Check that the mock was called with only the updated data.
		await expect( updateCustomerDataMock ).toHaveBeenCalledWith( {
			shipping_address: {
				city: 'Houston',
				state: 'TX',
				postcode: 'ABCDEF',
			},
		} );

		// This assertion is required to ensure the async `catch` block in `pushChanges` is done executing and all side effects finish.
		await expect( updateCustomerDataMock ).toHaveReturned();

		// Reset the mock so that it no longer rejects.
		updateCustomerDataMock.mockReset();
		updateCustomerDataMock.mockResolvedValue( jest.fn() );

		// Simulate the user updating the postcode only.
		getCustomerDataMock.mockReturnValue( {
			billingAddress: {
				first_name: 'John',
				last_name: 'Doe',
				address_1: '123 Main St',
				address_2: '',
				city: 'New York',
				state: 'NY',
				postcode: '10001',
				country: 'US',
				email: 'john.doe@mail.com',
				phone: '555-555-5555',
			},
			shippingAddress: {
				first_name: 'John',
				last_name: 'Doe',
				address_1: '123 Main St',
				address_2: '',
				city: 'Houston',
				state: 'TX',
				postcode: '77058',
				country: 'US',
				phone: '555-555-5555',
			},
		} );

		// Although only one property was updated between calls, we should expect City, State, and Postcode to be pushed
		// to the server because the previous push failed when they were originally changed.
		pushChanges();
		await expect( updateCustomerDataMock ).toHaveBeenLastCalledWith( {
			shipping_address: {
				city: 'Houston',
				state: 'TX',
				postcode: '77058',
			},
		} );
	} );
} );

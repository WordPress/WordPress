/**
 * External dependencies
 */
import { act, render, screen, waitFor } from '@testing-library/react';
import { previewCart } from '@woocommerce/resource-previews';
import * as wpDataFunctions from '@wordpress/data';
import { CART_STORE_KEY, PAYMENT_STORE_KEY } from '@woocommerce/block-data';
import { default as fetchMock } from 'jest-fetch-mock';
import {
	registerPaymentMethod,
	__experimentalDeRegisterPaymentMethod,
} from '@woocommerce/blocks-registry';
import userEvent from '@testing-library/user-event';
import { dispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import PaymentMethods from '../payment-methods';

jest.mock( '../saved-payment-method-options', () => ( { onChange } ) => {
	return (
		<>
			<span>Saved payment method options</span>
			<button onClick={ () => onChange( '0' ) }>Select saved</button>
		</>
	);
} );

jest.mock(
	'@woocommerce/base-components/radio-control-accordion',
	() =>
		( { onChange } ) =>
			(
				<>
					<span>Payment method options</span>
					<button onClick={ () => onChange( 'credit-card' ) }>
						Select new payment
					</button>
				</>
			)
);

const originalSelect = jest.requireActual( '@wordpress/data' ).select;
const selectMock = jest
	.spyOn( wpDataFunctions, 'select' )
	.mockImplementation( ( storeName ) => {
		const originalStore = originalSelect( storeName );
		if ( storeName === PAYMENT_STORE_KEY ) {
			return {
				...originalStore,
				getState: () => {
					const originalState = originalStore.getState();
					return {
						...originalState,
						savedPaymentMethods: {},
						availablePaymentMethods: {},
						paymentMethodsInitialized: true,
					};
				},
			};
		}
		return originalStore;
	} );

const registerMockPaymentMethods = () => {
	[ 'cod', 'credit-card' ].forEach( ( name ) => {
		registerPaymentMethod( {
			name,
			label: name,
			content: <div>A payment method</div>,
			edit: <div>A payment method</div>,
			icons: null,
			canMakePayment: () => true,
			supports: {
				showSavedCards: true,
				showSaveOption: true,
				features: [ 'products' ],
			},
			ariaLabel: name,
		} );
	} );
	dispatch( PAYMENT_STORE_KEY ).__internalUpdateAvailablePaymentMethods();
};

const resetMockPaymentMethods = () => {
	[ 'cod', 'credit-card' ].forEach( ( name ) => {
		__experimentalDeRegisterPaymentMethod( name );
	} );
};

describe( 'PaymentMethods', () => {
	beforeEach( () => {
		fetchMock.mockResponse( ( req ) => {
			if ( req.url.match( /wc\/store\/v1\/cart/ ) ) {
				return Promise.resolve( JSON.stringify( previewCart ) );
			}
			return Promise.resolve( '' );
		} );
		// need to clear the store resolution state between tests.
		wpDataFunctions
			.dispatch( CART_STORE_KEY )
			.invalidateResolutionForStore();
		wpDataFunctions.dispatch( CART_STORE_KEY ).receiveCart( {
			...previewCart,
			payment_methods: [ 'cod', 'credit-card' ],
		} );
	} );

	afterEach( () => {
		fetchMock.resetMocks();
	} );

	test( 'should show no payment methods component when there are no payment methods', async () => {
		render( <PaymentMethods /> );

		await waitFor( () => {
			const noPaymentMethods = screen.queryAllByText(
				/no payment methods available/
			);
			// We might get more than one match because the `speak()` function
			// creates an extra `div` with the notice contents used for a11y.
			expect( noPaymentMethods.length ).toBeGreaterThanOrEqual( 1 );

			// Reset the mock back to how it was because we don't need it anymore after this test.
			selectMock.mockRestore();
		} );
	} );

	test( 'selecting new payment method', async () => {
		const ShowActivePaymentMethod = () => {
			const { activePaymentMethod, activeSavedToken } =
				wpDataFunctions.useSelect( ( select ) => {
					const store = select( PAYMENT_STORE_KEY );
					return {
						activePaymentMethod: store.getActivePaymentMethod(),
						activeSavedToken: store.getActiveSavedToken(),
					};
				} );
			return (
				<>
					<div>
						{ 'Active Payment Method: ' + activePaymentMethod }
					</div>
					<div>{ 'Active Saved Token: ' + activeSavedToken }</div>
				</>
			);
		};

		act( () => {
			registerMockPaymentMethods();
		} );
		// Wait for the payment methods to finish loading before rendering.
		await waitFor( () => {
			expect(
				wpDataFunctions
					.select( PAYMENT_STORE_KEY )
					.getActivePaymentMethod()
			).toBe( 'cod' );
		} );

		render(
			<>
				<PaymentMethods />
				<ShowActivePaymentMethod />
			</>
		);

		await waitFor( () => {
			const savedPaymentMethodOptions = screen.queryByText(
				/Saved payment method options/
			);
			expect( savedPaymentMethodOptions ).not.toBeNull();
		} );

		await waitFor( () => {
			const paymentMethodOptions = screen.queryByText(
				/Payment method options/
			);
			expect( paymentMethodOptions ).not.toBeNull();
		} );

		await waitFor( () => {
			const savedToken = screen.queryByText(
				/Active Payment Method: credit-card/
			);
			expect( savedToken ).toBeNull();
		} );

		userEvent.click( screen.getByText( 'Select new payment' ) );

		await waitFor( () => {
			const activePaymentMethod = screen.queryByText(
				/Active Payment Method: credit-card/
			);
			expect( activePaymentMethod ).not.toBeNull();
		} );

		act( () => resetMockPaymentMethods() );
	} );
} );

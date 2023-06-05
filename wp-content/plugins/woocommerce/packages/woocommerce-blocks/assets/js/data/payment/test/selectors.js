/**
 * External dependencies
 */
import { render, screen, waitFor, act } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { previewCart } from '@woocommerce/resource-previews';
import * as wpDataFunctions from '@wordpress/data';
import {
	CART_STORE_KEY as storeKey,
	PAYMENT_STORE_KEY,
} from '@woocommerce/block-data';
import {
	registerPaymentMethod,
	registerExpressPaymentMethod,
	__experimentalDeRegisterPaymentMethod,
	__experimentalDeRegisterExpressPaymentMethod,
} from '@woocommerce/blocks-registry';
import { default as fetchMock } from 'jest-fetch-mock';

/**
 * Internal dependencies
 */
import {
	CheckoutExpressPayment,
	SavedPaymentMethodOptions,
} from '../../../blocks/cart-checkout-shared/payment-methods';
import { defaultCartState } from '../../cart/default-state';

const originalSelect = jest.requireActual( '@wordpress/data' ).select;
jest.spyOn( wpDataFunctions, 'select' ).mockImplementation( ( storeName ) => {
	const originalStore = originalSelect( storeName );
	if ( storeName === storeKey ) {
		return {
			...originalStore,
			hasFinishedResolution: jest
				.fn()
				.mockImplementation( ( selectorName ) => {
					if ( selectorName === 'getCartTotals' ) {
						return true;
					}
					return originalStore.hasFinishedResolution( selectorName );
				} ),
		};
	}
	return originalStore;
} );

jest.mock( '@woocommerce/settings', () => {
	const originalModule = jest.requireActual( '@woocommerce/settings' );

	return {
		// @ts-ignore We know @woocommerce/settings is an object.
		...originalModule,
		getSetting: ( setting, ...rest ) => {
			if ( setting === 'customerPaymentMethods' ) {
				return {
					cc: [
						{
							method: {
								gateway: 'credit-card',
								last4: '4242',
								brand: 'Visa',
							},
							expires: '12/22',
							is_default: true,
							tokenId: 1,
						},
					],
				};
			}
			return originalModule.getSetting( setting, ...rest );
		},
	};
} );

const registerMockPaymentMethods = ( savedCards = true ) => {
	[ 'cheque', 'bacs' ].forEach( ( name ) => {
		registerPaymentMethod( {
			name,
			label: name,
			content: <div>A payment method</div>,
			edit: <div>A payment method</div>,
			icons: null,
			canMakePayment: () => true,
			supports: {
				features: [ 'products' ],
			},
			ariaLabel: name,
		} );
	} );
	[ 'credit-card' ].forEach( ( name ) => {
		registerPaymentMethod( {
			name,
			label: name,
			content: <div>A payment method</div>,
			edit: <div>A payment method</div>,
			icons: null,
			canMakePayment: () => true,
			supports: {
				showSavedCards: savedCards,
				showSaveOption: true,
				features: [ 'products' ],
			},
			ariaLabel: name,
		} );
	} );
	[ 'express-payment' ].forEach( ( name ) => {
		const Content = ( {
			onClose = () => void null,
			onClick = () => void null,
		} ) => {
			return (
				<>
					<button onClick={ onClick }>
						{ name + ' express payment method' }
					</button>
					<button onClick={ onClose }>
						{ name + ' express payment method close' }
					</button>
				</>
			);
		};
		registerExpressPaymentMethod( {
			name,
			content: <Content />,
			edit: <div>An express payment method</div>,
			canMakePayment: () => true,
			paymentMethodId: name,
			supports: {
				features: [ 'products' ],
			},
		} );
	} );
	wpDataFunctions
		.dispatch( PAYMENT_STORE_KEY )
		.__internalUpdateAvailablePaymentMethods();
};

const resetMockPaymentMethods = () => {
	[ 'cheque', 'bacs', 'credit-card' ].forEach( ( name ) => {
		__experimentalDeRegisterPaymentMethod( name );
	} );
	[ 'express-payment' ].forEach( ( name ) => {
		__experimentalDeRegisterExpressPaymentMethod( name );
	} );
};

describe( 'Payment method data store selectors/thunks', () => {
	beforeEach( () => {
		act( () => {
			registerMockPaymentMethods( false );

			fetchMock.mockResponse( ( req ) => {
				if ( req.url.match( /wc\/store\/v1\/cart/ ) ) {
					return Promise.resolve( JSON.stringify( previewCart ) );
				}
				return Promise.resolve( '' );
			} );

			// need to clear the store resolution state between tests.
			wpDataFunctions.dispatch( storeKey ).invalidateResolutionForStore();
			wpDataFunctions
				.dispatch( storeKey )
				.receiveCart( defaultCartState.cartData );
		} );
	} );

	afterEach( async () => {
		act( () => {
			resetMockPaymentMethods();
			fetchMock.resetMocks();
		} );
	} );

	it( 'toggles active payment method correctly for express payment activation and close', async () => {
		const TriggerActiveExpressPaymentMethod = () => {
			const activePaymentMethod = wpDataFunctions.useSelect(
				( select ) => {
					return select( PAYMENT_STORE_KEY ).getActivePaymentMethod();
				}
			);

			return (
				<>
					<CheckoutExpressPayment />
					{ 'Active Payment Method: ' + activePaymentMethod }
				</>
			);
		};
		const TestComponent = () => {
			return <TriggerActiveExpressPaymentMethod />;
		};

		render( <TestComponent /> );

		// should initialize by default the first payment method.
		await waitFor( () => {
			const activePaymentMethod = screen.queryByText(
				/Active Payment Method: credit-card/
			);
			expect( activePaymentMethod ).not.toBeNull();
		} );

		// Express payment method clicked.
		userEvent.click(
			screen.getByText( 'express-payment express payment method' )
		);

		await waitFor( () => {
			const activePaymentMethod = screen.queryByText(
				/Active Payment Method: express-payment/
			);
			expect( activePaymentMethod ).not.toBeNull();
		} );

		// Express payment method closed.
		userEvent.click(
			screen.getByText( 'express-payment express payment method close' )
		);

		await waitFor( () => {
			const activePaymentMethod = screen.queryByText(
				/Active Payment Method: credit-card/
			);
			expect( activePaymentMethod ).not.toBeNull();
		} );
	} );
} );

describe( 'Testing Payment Methods work correctly with saved cards turned on', () => {
	beforeEach( () => {
		act( () => {
			registerMockPaymentMethods( true );

			fetchMock.mockResponse( ( req ) => {
				if ( req.url.match( /wc\/store\/v1\/cart/ ) ) {
					return Promise.resolve( JSON.stringify( previewCart ) );
				}
				return Promise.resolve( '' );
			} );

			// need to clear the store resolution state between tests.
			wpDataFunctions.dispatch( storeKey ).invalidateResolutionForStore();
			wpDataFunctions
				.dispatch( storeKey )
				.receiveCart( defaultCartState.cartData );
		} );
	} );

	afterEach( async () => {
		act( () => {
			resetMockPaymentMethods();
			fetchMock.resetMocks();
		} );
	} );

	it( 'resets saved payment method data after starting and closing an express payment method', async () => {
		const TriggerActiveExpressPaymentMethod = () => {
			const { activePaymentMethod, paymentMethodData } =
				wpDataFunctions.useSelect( ( select ) => {
					const store = select( PAYMENT_STORE_KEY );
					return {
						activePaymentMethod: store.getActivePaymentMethod(),
						paymentMethodData: store.getPaymentMethodData(),
					};
				} );
			return (
				<>
					<CheckoutExpressPayment />
					<SavedPaymentMethodOptions onChange={ () => void null } />
					{ 'Active Payment Method: ' + activePaymentMethod }
					{ paymentMethodData[ 'wc-credit-card-payment-token' ] && (
						<span>credit-card token</span>
					) }
				</>
			);
		};
		const TestComponent = () => {
			return <TriggerActiveExpressPaymentMethod />;
		};

		render( <TestComponent /> );

		// Should initialize by default the default saved payment method.
		await waitFor( () => {
			const activePaymentMethod = screen.queryByText(
				/Active Payment Method: credit-card/
			);
			expect( activePaymentMethod ).not.toBeNull();
		} );

		await waitFor( () => {
			const creditCardToken = screen.queryByText( /credit-card token/ );
			expect( creditCardToken ).not.toBeNull();
		} );

		// Express payment method clicked.
		userEvent.click(
			screen.getByText( 'express-payment express payment method' )
		);

		await waitFor( () => {
			const activePaymentMethod = screen.queryByText(
				/Active Payment Method: express-payment/
			);
			expect( activePaymentMethod ).not.toBeNull();
		} );

		await waitFor( () => {
			const creditCardToken = screen.queryByText( /credit-card token/ );
			expect( creditCardToken ).toBeNull();
		} );

		// Express payment method closed.
		userEvent.click(
			screen.getByText( 'express-payment express payment method close' )
		);

		await waitFor( () => {
			const activePaymentMethod = screen.queryByText(
				/Active Payment Method: credit-card/
			);
			expect( activePaymentMethod ).not.toBeNull();
		} );

		await waitFor( () => {
			const creditCardToken = screen.queryByText( /credit-card token/ );
			expect( creditCardToken ).not.toBeNull();
		} );
	} );
} );

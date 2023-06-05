/**
 * External dependencies
 */
import { render, screen, waitFor, act } from '@testing-library/react';
import { previewCart } from '@woocommerce/resource-previews';
import { dispatch } from '@wordpress/data';
import { CART_STORE_KEY as storeKey } from '@woocommerce/block-data';
import { default as fetchMock } from 'jest-fetch-mock';
import { registerCheckoutFilters } from '@woocommerce/blocks-checkout';

/**
 * Internal dependencies
 */
import { defaultCartState } from '../../../data/cart/default-state';
import { allSettings } from '../../../settings/shared/settings-init';

import Cart from '../block';

import FilledCart from '../inner-blocks/filled-cart-block/frontend';
import EmptyCart from '../inner-blocks/empty-cart-block/frontend';

import ItemsBlock from '../inner-blocks/cart-items-block/frontend';
import TotalsBlock from '../inner-blocks/cart-totals-block/frontend';

import LineItemsBlock from '../inner-blocks/cart-line-items-block/block';
import OrderSummaryBlock from '../inner-blocks/cart-order-summary-block/frontend';
import ExpressPaymentBlock from '../inner-blocks/cart-express-payment-block/block';
import ProceedToCheckoutBlock from '../inner-blocks/proceed-to-checkout-block/block';
import AcceptedPaymentMethodsIcons from '../inner-blocks/cart-accepted-payment-methods-block/block';
import OrderSummaryHeadingBlock from '../inner-blocks/cart-order-summary-heading/frontend';
import OrderSummarySubtotalBlock from '../inner-blocks/cart-order-summary-subtotal/frontend';
import OrderSummaryShippingBlock from '../inner-blocks/cart-order-summary-shipping/frontend';
import OrderSummaryTaxesBlock from '../inner-blocks/cart-order-summary-taxes/frontend';

const CartBlock = ( {
	attributes = {
		showRateAfterTaxName: false,
		isShippingCalculatorEnabled: false,
		checkoutPageId: 0,
	},
} ) => {
	const {
		showRateAfterTaxName,
		isShippingCalculatorEnabled,
		checkoutPageId,
	} = attributes;
	return (
		<Cart attributes={ attributes }>
			<FilledCart>
				<ItemsBlock>
					<LineItemsBlock />
				</ItemsBlock>
				<TotalsBlock>
					<OrderSummaryBlock>
						<OrderSummaryHeadingBlock />
						<OrderSummarySubtotalBlock />
						<OrderSummaryShippingBlock
							isShippingCalculatorEnabled={
								isShippingCalculatorEnabled
							}
						/>
						<OrderSummaryTaxesBlock
							showRateAfterTaxName={ showRateAfterTaxName }
						/>
					</OrderSummaryBlock>
					<ExpressPaymentBlock />
					<ProceedToCheckoutBlock checkoutPageId={ checkoutPageId } />
					<AcceptedPaymentMethodsIcons />
				</TotalsBlock>
			</FilledCart>
			<EmptyCart>
				<p>Empty Cart</p>
			</EmptyCart>
		</Cart>
	);
};

describe( 'Testing cart', () => {
	beforeEach( () => {
		act( () => {
			fetchMock.mockResponse( ( req ) => {
				if ( req.url.match( /wc\/store\/v1\/cart/ ) ) {
					return Promise.resolve( JSON.stringify( previewCart ) );
				}
				return Promise.resolve( '' );
			} );
			// need to clear the store resolution state between tests.
			dispatch( storeKey ).invalidateResolutionForStore();
			dispatch( storeKey ).receiveCart( defaultCartState.cartData );
		} );
	} );

	afterEach( () => {
		fetchMock.resetMocks();
	} );

	it( 'renders cart if there are items in the cart', async () => {
		render( <CartBlock /> );
		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );

		expect(
			screen.getByText( /Proceed to Checkout/i )
		).toBeInTheDocument();

		expect( fetchMock ).toHaveBeenCalledTimes( 1 );
	} );

	it( 'Contains a Taxes section if Core options are set to show it', async () => {
		allSettings.displayCartPricesIncludingTax = false;
		// The criteria for showing the Taxes section is:
		// Display prices during basket and checkout: 'Excluding tax'.
		render( <CartBlock /> );

		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );
		expect( screen.getByText( /Tax/i ) ).toBeInTheDocument();
	} );

	it( 'Contains a Order summary header', async () => {
		render( <CartBlock /> );

		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );
		expect( screen.getByText( /Cart totals/i ) ).toBeInTheDocument();
	} );

	it( 'Contains a Order summary Subtotal section', async () => {
		render( <CartBlock /> );

		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );
		expect( screen.getByText( /Subtotal/i ) ).toBeInTheDocument();
	} );

	it( 'Shows individual tax lines if the store is set to do so', async () => {
		allSettings.displayCartPricesIncludingTax = false;
		allSettings.displayItemizedTaxes = true;
		// The criteria for showing the lines in the Taxes section is:
		// Display prices during basket and checkout: 'Excluding tax'.
		// Display tax totals: 'Itemized';
		render( <CartBlock /> );
		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );
		expect( screen.getByText( /Sales tax/i ) ).toBeInTheDocument();
	} );

	it( 'Shows rate percentages after tax lines if the block is set to do so', async () => {
		allSettings.displayCartPricesIncludingTax = false;
		allSettings.displayItemizedTaxes = true;
		// The criteria for showing the lines in the Taxes section is:
		// Display prices during basket and checkout: 'Excluding tax'.
		// Display tax totals: 'Itemized';
		render(
			<CartBlock
				attributes={ {
					showRateAfterTaxName: true,
				} }
			/>
		);
		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );
		expect( screen.getByText( /Sales tax 20%/i ) ).toBeInTheDocument();
	} );

	it( 'renders empty cart if there are no items in the cart', async () => {
		act( () => {
			fetchMock.mockResponse( ( req ) => {
				if ( req.url.match( /wc\/store\/v1\/cart/ ) ) {
					return Promise.resolve(
						JSON.stringify( defaultCartState.cartData )
					);
				}
				return Promise.resolve( '' );
			} );
		} );
		render( <CartBlock /> );

		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );
		expect( screen.getByText( /Empty Cart/i ) ).toBeInTheDocument();
		expect( fetchMock ).toHaveBeenCalledTimes( 1 );
	} );

	it( 'renders correct cart line subtotal when currency has 0 decimals', async () => {
		fetchMock.mockResponse( ( req ) => {
			if ( req.url.match( /wc\/store\/v1\/cart/ ) ) {
				const cart = {
					...previewCart,
					// Make it so there is only one item to simplify things.
					items: [
						{
							...previewCart.items[ 0 ],
							totals: {
								...previewCart.items[ 0 ].totals,
								// Change price format so there are no decimals.
								currency_minor_unit: 0,
								currency_prefix: '',
								currency_suffix: '€',
								line_subtotal: '16',
								line_total: '18',
							},
						},
					],
				};

				return Promise.resolve( JSON.stringify( cart ) );
			}
		} );
		render( <CartBlock /> );

		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );
		expect( screen.getAllByRole( 'cell' )[ 1 ] ).toHaveTextContent( '16€' );
	} );

	it( 'updates quantity when changed in server', async () => {
		const cart = {
			...previewCart,
			// Make it so there is only one item to simplify things.
			items: [
				{
					...previewCart.items[ 0 ],
					quantity: 5,
				},
			],
		};
		const itemName = cart.items[ 0 ].name;
		render( <CartBlock /> );

		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );
		const quantityInput = screen.getByLabelText(
			`Quantity of ${ itemName } in your cart.`
		);
		expect( quantityInput.value ).toBe( '2' );

		act( () => {
			dispatch( storeKey ).receiveCart( cart );
		} );

		expect( quantityInput.value ).toBe( '5' );
	} );

	it( 'does not show the remove item button when a filter prevents this', async () => {
		const cart = {
			...previewCart,
			// Make it so there is only one item to simplify things.
			items: [ previewCart.items[ 0 ] ],
		};

		registerCheckoutFilters( 'woo-blocks-test-extension', {
			showRemoveItemLink: ( value, extensions, { cartItem } ) => {
				return cartItem.id !== cart.items[ 0 ].id;
			},
		} );
		render( <CartBlock /> );

		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );

		act( () => {
			dispatch( storeKey ).receiveCart( cart );
		} );

		expect( screen.queryAllByText( /Remove item/i ).length ).toBe( 0 );
	} );
} );

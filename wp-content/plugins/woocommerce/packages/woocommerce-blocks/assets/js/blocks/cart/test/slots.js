/* eslint-disable jest/no-commented-out-tests */
/**
 * External dependencies
 */
import { render, screen, waitFor, act } from '@testing-library/react';
import { previewCart } from '@woocommerce/resource-previews';
import { dispatch } from '@wordpress/data';
import { CART_STORE_KEY as storeKey } from '@woocommerce/block-data';
import { default as fetchMock } from 'jest-fetch-mock';
import { ExperimentalOrderMeta } from '@woocommerce/blocks-checkout';
import { registerPlugin } from '@wordpress/plugins';
/**
 * Internal dependencies
 */
import { defaultCartState } from '../../../data/cart/default-state';

import Cart from '../block';
import OrderSummaryBlock from '../inner-blocks/cart-order-summary-block/frontend';

const SlotFillConsumer = ( { cart } ) => {
	const { billingData } = cart;

	return <p>My address: { billingData.address_1 }</p>;
};

const CartBlock = () => {
	return (
		<Cart>
			<OrderSummaryBlock />
		</Cart>
	);
};

describe( 'Testing Slotfills', () => {
	beforeAll( () => {
		registerPlugin( 'slot-fills-test', {
			render: () => (
				<ExperimentalOrderMeta>
					<SlotFillConsumer />
				</ExperimentalOrderMeta>
			),
			scope: 'woocommerce-checkout',
		} );
	} );
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

	it( 'still expects billingData', async () => {
		fetchMock.mockResponse( ( req ) => {
			if ( req.url.match( /wc\/store\/v1\/cart/ ) ) {
				const cart = {
					...previewCart,
					billing_address: {
						...previewCart.billing_address,
						address_1: 'Street address',
					},
				};

				return Promise.resolve( JSON.stringify( cart ) );
			}
		} );
		render( <CartBlock /> );
		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );

		expect(
			screen.getByText( /My address: Street address/i )
		).toBeInTheDocument();

		expect( fetchMock ).toHaveBeenCalledTimes( 1 );
	} );
} );

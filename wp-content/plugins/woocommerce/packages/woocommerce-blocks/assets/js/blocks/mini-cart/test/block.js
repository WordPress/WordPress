/**
 * External dependencies
 */
import {
	act,
	render,
	screen,
	queryByText,
	waitFor,
	waitForElementToBeRemoved,
} from '@testing-library/react';
import { previewCart } from '@woocommerce/resource-previews';
import { dispatch } from '@wordpress/data';
import { CART_STORE_KEY as storeKey } from '@woocommerce/block-data';
import { SlotFillProvider } from '@woocommerce/blocks-checkout';
import { default as fetchMock } from 'jest-fetch-mock';
import userEvent from '@testing-library/user-event';

/**
 * Internal dependencies
 */
import Block from '../block';
import { defaultCartState } from '../../../data/cart/default-state';

const MiniCartBlock = ( props ) => (
	<SlotFillProvider>
		<Block
			contents='<div class="wc-block-mini-cart-contents"></div>'
			{ ...props }
		/>
	</SlotFillProvider>
);

const mockEmptyCart = () => {
	fetchMock.mockResponse( ( req ) => {
		if ( req.url.match( /wc\/store\/v1\/cart/ ) ) {
			return Promise.resolve(
				JSON.stringify( defaultCartState.cartData )
			);
		}
		return Promise.resolve( '' );
	} );
};

const mockFullCart = () => {
	fetchMock.mockResponse( ( req ) => {
		if ( req.url.match( /wc\/store\/v1\/cart/ ) ) {
			return Promise.resolve( JSON.stringify( previewCart ) );
		}
		return Promise.resolve( '' );
	} );
};

describe( 'Testing Mini Cart', () => {
	beforeEach( () => {
		act( () => {
			mockFullCart();
			// need to clear the store resolution state between tests.
			dispatch( storeKey ).invalidateResolutionForStore();
			dispatch( storeKey ).receiveCart( defaultCartState.cartData );
		} );
	} );

	afterEach( () => {
		fetchMock.resetMocks();
	} );

	it( 'opens Mini Cart drawer when clicking on button', async () => {
		render( <MiniCartBlock /> );
		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );
		userEvent.click( screen.getByLabelText( /items/i ) );

		expect( fetchMock ).toHaveBeenCalledTimes( 1 );
	} );

	it( 'renders empty cart if there are no items in the cart', async () => {
		mockEmptyCart();
		render( <MiniCartBlock /> );

		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );
		userEvent.click( screen.getByLabelText( /items/i ) );

		expect( fetchMock ).toHaveBeenCalledTimes( 1 );
	} );

	it( 'updates contents when removed from cart event is triggered', async () => {
		render( <MiniCartBlock /> );
		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );

		mockEmptyCart();
		// eslint-disable-next-line no-undef
		const removedFromCartEvent = new Event( 'wc-blocks_removed_from_cart' );
		act( () => {
			document.body.dispatchEvent( removedFromCartEvent );
		} );

		await waitForElementToBeRemoved( () =>
			screen.queryByLabelText( /3 items in cart/i )
		);
		await waitFor( () =>
			expect(
				screen.getByLabelText( /0 items in cart/i )
			).toBeInTheDocument()
		);
	} );

	it( 'updates contents when added to cart event is triggered', async () => {
		mockEmptyCart();
		render( <MiniCartBlock /> );
		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );

		mockFullCart();
		// eslint-disable-next-line no-undef
		const addedToCartEvent = new Event( 'wc-blocks_added_to_cart' );
		act( () => {
			document.body.dispatchEvent( addedToCartEvent );
		} );

		await waitForElementToBeRemoved( () =>
			screen.queryByLabelText( /0 items in cart/i )
		);
		await waitFor( () =>
			expect(
				screen.getByLabelText( /3 items in cart/i )
			).toBeInTheDocument()
		);
	} );

	it( 'renders cart price if "Hide Cart Price" setting is not enabled', async () => {
		mockEmptyCart();
		render( <MiniCartBlock /> );
		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );

		await waitFor( () =>
			expect( screen.getByText( '$0.00' ) ).toBeInTheDocument()
		);
	} );

	it( 'does not render cart price if "Hide Cart Price" setting is enabled', async () => {
		mockEmptyCart();
		const { container } = render(
			<MiniCartBlock hasHiddenPrice={ true } />
		);
		await waitFor( () => expect( fetchMock ).toHaveBeenCalled() );

		await waitFor( () =>
			expect( queryByText( container, '$0.00' ) ).not.toBeInTheDocument()
		);
	} );
} );

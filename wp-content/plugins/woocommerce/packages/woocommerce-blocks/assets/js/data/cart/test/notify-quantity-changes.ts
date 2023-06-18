/**
 * External dependencies
 */
import { dispatch, select } from '@wordpress/data';
import { previewCart } from '@woocommerce/resource-previews';
import { Cart } from '@woocommerce/types';
import { camelCaseKeys } from '@woocommerce/base-utils';

/**
 * Internal dependencies
 */
import { notifyQuantityChanges } from '../notify-quantity-changes';

// Deep clone an object to avoid mutating it later.
const cloneObject = ( obj ) => JSON.parse( JSON.stringify( obj ) );

jest.mock( '@wordpress/data' );

const mockedCreateInfoNotice = jest.fn();
dispatch.mockImplementation( ( store ) => {
	if ( store === 'core/notices' ) {
		return {
			createInfoNotice: mockedCreateInfoNotice,
		};
	}
} );

select.mockImplementation( () => {
	return {
		hasFinishedResolution() {
			return true;
		},
	};
} );

/**
 * Clones the preview cart and turns it into a `Cart`.
 */
const getFreshCarts = (): { oldCart: Cart; newCart: Cart } => {
	const oldCart = camelCaseKeys( cloneObject( previewCart ) ) as Cart;
	const newCart = camelCaseKeys( cloneObject( previewCart ) ) as Cart;
	return { oldCart, newCart };
};

describe( 'notifyQuantityChanges', () => {
	afterEach( () => {
		jest.clearAllMocks();
	} );
	it( 'shows notices when the quantity limits of an item change', () => {
		const { oldCart, newCart } = getFreshCarts();
		newCart.items[ 0 ].quantity_limits.minimum = 50;
		notifyQuantityChanges( {
			oldCart,
			newCart,
			cartItemsPendingQuantity: [],
		} );
		expect( mockedCreateInfoNotice ).toHaveBeenLastCalledWith(
			'The quantity of "Beanie" was increased to 50. This is the minimum required quantity.',
			{
				context: 'wc/cart',
				speak: true,
				type: 'snackbar',
				id: '1-quantity-update',
			}
		);

		newCart.items[ 0 ].quantity_limits.minimum = 1;
		newCart.items[ 0 ].quantity_limits.maximum = 10;
		// Quantity needs to be outside the limits for the notice to show.
		newCart.items[ 0 ].quantity = 11;
		notifyQuantityChanges( {
			oldCart,
			newCart,
			cartItemsPendingQuantity: [],
		} );
		expect( mockedCreateInfoNotice ).toHaveBeenLastCalledWith(
			'The quantity of "Beanie" was decreased to 10. This is the maximum allowed quantity.',
			{
				context: 'wc/cart',
				speak: true,
				type: 'snackbar',
				id: '1-quantity-update',
			}
		);
		newCart.items[ 0 ].quantity = 10;
		oldCart.items[ 0 ].quantity = 10;
		newCart.items[ 0 ].quantity_limits.multiple_of = 6;
		notifyQuantityChanges( {
			oldCart,
			newCart,
			cartItemsPendingQuantity: [],
		} );
		expect( mockedCreateInfoNotice ).toHaveBeenLastCalledWith(
			'The quantity of "Beanie" was changed to 6. You must purchase this product in groups of 6.',
			{
				context: 'wc/cart',
				speak: true,
				type: 'snackbar',
				id: '1-quantity-update',
			}
		);
	} );
	it( 'does not show notices if the quantity limit changes, and the quantity is within limits', () => {
		const { oldCart, newCart } = getFreshCarts();
		newCart.items[ 0 ].quantity = 5;
		oldCart.items[ 0 ].quantity = 5;
		newCart.items[ 0 ].quantity_limits.maximum = 10;
		notifyQuantityChanges( {
			oldCart,
			newCart,
			cartItemsPendingQuantity: [],
		} );
		expect( mockedCreateInfoNotice ).not.toHaveBeenCalled();

		newCart.items[ 0 ].quantity_limits.minimum = 4;
		notifyQuantityChanges( {
			oldCart,
			newCart,
			cartItemsPendingQuantity: [],
		} );
		expect( mockedCreateInfoNotice ).not.toHaveBeenCalled();
	} );
	it( 'shows notices when the quantity of an item changes', () => {
		const { oldCart, newCart } = getFreshCarts();
		newCart.items[ 0 ].quantity = 50;
		notifyQuantityChanges( {
			oldCart,
			newCart,
			cartItemsPendingQuantity: [],
		} );
		expect( mockedCreateInfoNotice ).toHaveBeenLastCalledWith(
			'The quantity of "Beanie" was changed to 50.',
			{
				context: 'wc/cart',
				speak: true,
				type: 'snackbar',
				id: '1-quantity-update',
			}
		);
	} );
	it( 'does not show notices when the the item is the one being updated', () => {
		const { oldCart, newCart } = getFreshCarts();
		newCart.items[ 0 ].quantity = 5;
		newCart.items[ 0 ].quantity_limits.maximum = 10;
		notifyQuantityChanges( {
			oldCart,
			newCart,
			cartItemsPendingQuantity: [ '1' ],
		} );
		expect( mockedCreateInfoNotice ).not.toHaveBeenCalled();
	} );
	it( 'does not show notices when a deleted item is the one being removed', () => {
		const { oldCart, newCart } = getFreshCarts();

		// Remove both items from the new cart.
		delete newCart.items[ 0 ];
		delete newCart.items[ 1 ];
		notifyQuantityChanges( {
			oldCart,
			newCart,
			// This means the user is only actively removing item with key '1'. The second item is "unexpected" so we
			// expect exactly one notification to be shown.
			cartItemsPendingDelete: [ '1' ],
		} );
		// Check it was called for item 2, but not item 1.
		expect( mockedCreateInfoNotice ).toHaveBeenCalledTimes( 1 );
	} );

	it( 'shows a notice when an item is unexpectedly removed', () => {
		const { oldCart, newCart } = getFreshCarts();
		delete newCart.items[ 0 ];
		notifyQuantityChanges( {
			oldCart,
			newCart,
		} );
		expect( mockedCreateInfoNotice ).toHaveBeenLastCalledWith(
			'"Beanie" was removed from your cart.',
			{
				context: 'wc/cart',
				speak: true,
				type: 'snackbar',
				id: '1-removed',
			}
		);
	} );
	it( 'does not show notices if the cart has not finished resolving', () => {
		select.mockImplementation( () => {
			return {
				hasFinishedResolution() {
					return false;
				},
			};
		} );
		expect( mockedCreateInfoNotice ).not.toHaveBeenCalled();
	} );
} );

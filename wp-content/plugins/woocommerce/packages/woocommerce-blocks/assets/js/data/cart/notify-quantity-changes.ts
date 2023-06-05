/**
 * External dependencies
 */
import { Cart, CartItem } from '@woocommerce/types';
import { dispatch, select } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { STORE_KEY as CART_STORE_KEY } from './constants';

interface NotifyQuantityChangesArgs {
	oldCart: Cart;
	newCart: Cart;
	cartItemsPendingQuantity?: string[] | undefined;
	cartItemsPendingDelete?: string[] | undefined;
}

const isWithinQuantityLimits = ( cartItem: CartItem ) => {
	return (
		cartItem.quantity >= cartItem.quantity_limits.minimum &&
		cartItem.quantity <= cartItem.quantity_limits.maximum &&
		cartItem.quantity % cartItem.quantity_limits.multiple_of === 0
	);
};

const notifyIfQuantityLimitsChanged = ( oldCart: Cart, newCart: Cart ) => {
	newCart.items.forEach( ( cartItem ) => {
		const oldCartItem = oldCart.items.find( ( item ) => {
			return item && item.key === cartItem.key;
		} );

		// If getCartData has not finished resolving, then this is the first load.
		const isFirstLoad = oldCart.items.length === 0;

		// Item has been removed, we don't need to do any more checks.
		if ( ! oldCartItem && ! isFirstLoad ) {
			return;
		}

		if ( isWithinQuantityLimits( cartItem ) ) {
			return;
		}

		const quantityAboveMax =
			cartItem.quantity > cartItem.quantity_limits.maximum;
		const quantityBelowMin =
			cartItem.quantity < cartItem.quantity_limits.minimum;
		const quantityOutOfStep =
			cartItem.quantity % cartItem.quantity_limits.multiple_of !== 0;

		// If the quantity is still within the constraints, then we don't need to show any notice, this is because
		// QuantitySelector will not automatically update the value.
		if ( ! quantityAboveMax && ! quantityBelowMin && ! quantityOutOfStep ) {
			return;
		}

		if ( quantityOutOfStep ) {
			dispatch( 'core/notices' ).createInfoNotice(
				sprintf(
					/* translators: %1$s is the name of the item, %2$d is the quantity of the item. %3$d is a number that the quantity must be a multiple of. */
					__(
						'The quantity of "%1$s" was changed to %2$d. You must purchase this product in groups of %3$d.',
						'woo-gutenberg-products-block'
					),
					cartItem.name,
					// We round down to the nearest step value here. We need to do it this way because at this point we
					// don't know the next quantity. That only gets set once the HTML Input field applies its min/max
					// constraints.
					Math.floor(
						cartItem.quantity / cartItem.quantity_limits.multiple_of
					) * cartItem.quantity_limits.multiple_of,
					cartItem.quantity_limits.multiple_of
				),
				{
					context: 'wc/cart',
					speak: true,
					type: 'snackbar',
					id: `${ cartItem.key }-quantity-update`,
				}
			);
			return;
		}

		if ( quantityBelowMin ) {
			dispatch( 'core/notices' ).createInfoNotice(
				sprintf(
					/* translators: %1$s is the name of the item, %2$d is the quantity of the item. */
					__(
						'The quantity of "%1$s" was increased to %2$d. This is the minimum required quantity.',
						'woo-gutenberg-products-block'
					),
					cartItem.name,
					cartItem.quantity_limits.minimum
				),
				{
					context: 'wc/cart',
					speak: true,
					type: 'snackbar',
					id: `${ cartItem.key }-quantity-update`,
				}
			);
			return;
		}

		// Quantity is above max, so has been reduced.
		dispatch( 'core/notices' ).createInfoNotice(
			sprintf(
				/* translators: %1$s is the name of the item, %2$d is the quantity of the item. */
				__(
					'The quantity of "%1$s" was decreased to %2$d. This is the maximum allowed quantity.',
					'woo-gutenberg-products-block'
				),
				cartItem.name,
				cartItem.quantity_limits.maximum
			),
			{
				context: 'wc/cart',
				speak: true,
				type: 'snackbar',
				id: `${ cartItem.key }-quantity-update`,
			}
		);
	} );
};

const notifyIfQuantityChanged = (
	oldCart: Cart,
	newCart: Cart,
	cartItemsPendingQuantity: string[]
) => {
	newCart.items.forEach( ( cartItem ) => {
		if ( cartItemsPendingQuantity.includes( cartItem.key ) ) {
			return;
		}
		const oldCartItem = oldCart.items.find( ( item ) => {
			return item && item.key === cartItem.key;
		} );
		if ( ! oldCartItem ) {
			return;
		}

		if ( cartItem.key === oldCartItem.key ) {
			if (
				cartItem.quantity !== oldCartItem.quantity &&
				isWithinQuantityLimits( cartItem )
			) {
				dispatch( 'core/notices' ).createInfoNotice(
					sprintf(
						/* translators: %1$s is the name of the item, %2$d is the quantity of the item. */
						__(
							'The quantity of "%1$s" was changed to %2$d.',
							'woo-gutenberg-products-block'
						),
						cartItem.name,
						cartItem.quantity
					),
					{
						context: 'wc/cart',
						speak: true,
						type: 'snackbar',
						id: `${ cartItem.key }-quantity-update`,
					}
				);
			}
			return cartItem;
		}
	} );
};

/**
 * Checks whether the old cart contains an item that the new cart doesn't, and that the item was not slated for removal.
 *
 * @param  oldCart                The old cart.
 * @param  newCart                The new cart.
 * @param  cartItemsPendingDelete The cart items that are pending deletion.
 */
const notifyIfRemoved = (
	oldCart: Cart,
	newCart: Cart,
	cartItemsPendingDelete: string[]
) => {
	oldCart.items.forEach( ( oldCartItem ) => {
		if ( cartItemsPendingDelete.includes( oldCartItem.key ) ) {
			return;
		}

		const newCartItem = newCart.items.find( ( item: CartItem ) => {
			return item && item.key === oldCartItem.key;
		} );

		if ( ! newCartItem ) {
			dispatch( 'core/notices' ).createInfoNotice(
				sprintf(
					/* translators: %s is the name of the item. */
					__(
						'"%s" was removed from your cart.',
						'woo-gutenberg-products-block'
					),
					oldCartItem.name
				),
				{
					context: 'wc/cart',
					speak: true,
					type: 'snackbar',
					id: `${ oldCartItem.key }-removed`,
				}
			);
		}
	} );
};

/**
 * This function is used to notify the user when the quantity of an item in the cart has changed. It checks both the
 * item's quantity and quantity limits.
 */
export const notifyQuantityChanges = ( {
	oldCart,
	newCart,
	cartItemsPendingQuantity = [],
	cartItemsPendingDelete = [],
}: NotifyQuantityChangesArgs ) => {
	const isResolutionFinished =
		select( CART_STORE_KEY ).hasFinishedResolution( 'getCartData' );
	if ( ! isResolutionFinished ) {
		return;
	}
	notifyIfRemoved( oldCart, newCart, cartItemsPendingDelete );
	notifyIfQuantityLimitsChanged( oldCart, newCart );
	notifyIfQuantityChanged( oldCart, newCart, cartItemsPendingQuantity );
};

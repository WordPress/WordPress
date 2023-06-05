/**
 * External dependencies
 */
import { useState, useEffect, useRef } from '@wordpress/element';
import { useDispatch } from '@wordpress/data';
import { CART_STORE_KEY as storeKey } from '@woocommerce/block-data';
import { decodeEntities } from '@wordpress/html-entities';
import type { CartItem } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { useStoreCart } from './cart/use-store-cart';

/**
 * @typedef {import('@woocommerce/type-defs/hooks').StoreCartItemAddToCart} StoreCartItemAddToCart
 */

interface StoreAddToCart {
	cartQuantity: number;
	addingToCart: boolean;
	cartIsLoading: boolean;
	addToCart: ( quantity?: number ) => Promise< boolean >;
}
/**
 * Get the quantity of a product in the cart.
 *
 * @param {Object} cartItems Array of items.
 * @param {number} productId The product id to look for.
 * @return {number} Quantity in the cart.
 */
const getQuantityFromCartItems = (
	cartItems: Array< CartItem >,
	productId: number
): number => {
	const productItem = cartItems.find( ( { id } ) => id === productId );

	return productItem ? productItem.quantity : 0;
};

/**
 * A custom hook for exposing cart related data for a given product id and an
 * action for adding a single quantity of the product _to_ the cart.
 *
 *
 * @param {number} productId The product id to be added to the cart.
 *
 * @return {StoreCartItemAddToCart} An object exposing data and actions relating
 *                                  to add to cart functionality.
 */
export const useStoreAddToCart = ( productId: number ): StoreAddToCart => {
	const { addItemToCart } = useDispatch( storeKey );
	const { cartItems, cartIsLoading } = useStoreCart();
	const { createErrorNotice, removeNotice } = useDispatch( 'core/notices' );

	const [ addingToCart, setAddingToCart ] = useState( false );
	const currentCartItemQuantity = useRef(
		getQuantityFromCartItems( cartItems, productId )
	);

	const addToCart = ( quantity = 1 ) => {
		setAddingToCart( true );
		return addItemToCart( productId, quantity )
			.then( () => {
				removeNotice( 'add-to-cart' );
			} )
			.catch( ( error ) => {
				createErrorNotice( decodeEntities( error.message ), {
					id: 'add-to-cart',
					context: 'wc/all-products',
					isDismissible: true,
				} );
			} )
			.finally( () => {
				setAddingToCart( false );
			} );
	};

	useEffect( () => {
		const quantity = getQuantityFromCartItems( cartItems, productId );

		if ( quantity !== currentCartItemQuantity.current ) {
			currentCartItemQuantity.current = quantity;
		}
	}, [ cartItems, productId ] );

	return {
		cartQuantity: Number.isFinite( currentCartItemQuantity.current )
			? currentCartItemQuantity.current
			: 0,
		addingToCart,
		cartIsLoading,
		addToCart,
	};
};

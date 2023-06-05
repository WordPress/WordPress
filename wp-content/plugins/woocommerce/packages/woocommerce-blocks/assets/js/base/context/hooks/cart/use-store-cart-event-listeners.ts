/**
 * External dependencies
 */
import { useEffect } from '@wordpress/element';
import { CART_STORE_KEY } from '@woocommerce/block-data';
import { dispatch } from '@wordpress/data';
import {
	translateJQueryEventToNative,
	getNavigationType,
} from '@woocommerce/base-utils';

interface StoreCartListenersType {
	// Counts the number of consumers of this hook so we can remove listeners when no longer needed.
	count: number;
	// Function to remove all registered listeners.
	remove: () => void;
}

interface CartDataCustomEvent extends Event {
	detail?:
		| {
				preserveCartData?: boolean | undefined;
		  }
		| undefined;
}

declare global {
	interface Window {
		wcBlocksStoreCartListeners: StoreCartListenersType;
	}
}

const refreshData = ( event: CartDataCustomEvent ): void => {
	const eventDetail = event?.detail;
	if ( ! eventDetail || ! eventDetail.preserveCartData ) {
		dispatch( CART_STORE_KEY ).invalidateResolutionForStore();
	}
};

/**
 * Refreshes data if the pageshow event is triggered by the browser history.
 *
 * - In Chrome, `back_forward` will be returned by getNavigationType() when the browser history is used.
 * - In safari we instead need to use `event.persisted` which is true when page cache is used.
 */
const refreshCachedCartData = ( event: PageTransitionEvent ): void => {
	if ( event?.persisted || getNavigationType() === 'back_forward' ) {
		dispatch( CART_STORE_KEY ).invalidateResolutionForStore();
	}
};

const setUp = (): void => {
	if ( ! window.wcBlocksStoreCartListeners ) {
		window.wcBlocksStoreCartListeners = {
			count: 0,
			remove: () => void null,
		};
	}
};

// Checks if there are any listeners registered.
const hasListeners = (): boolean => {
	return window.wcBlocksStoreCartListeners?.count > 0;
};

// Add listeners if there are none, otherwise just increment the count.
const addListeners = (): void => {
	setUp();

	if ( hasListeners() ) {
		window.wcBlocksStoreCartListeners.count++;
		return;
	}
	document.body.addEventListener( 'wc-blocks_added_to_cart', refreshData );
	document.body.addEventListener(
		'wc-blocks_removed_from_cart',
		refreshData
	);
	window.addEventListener( 'pageshow', refreshCachedCartData );

	const removeJQueryAddedToCartEvent = translateJQueryEventToNative(
		'added_to_cart',
		`wc-blocks_added_to_cart`
	) as () => () => void;
	const removeJQueryRemovedFromCartEvent = translateJQueryEventToNative(
		'removed_from_cart',
		`wc-blocks_removed_from_cart`
	) as () => () => void;

	window.wcBlocksStoreCartListeners.count = 1;
	window.wcBlocksStoreCartListeners.remove = () => {
		document.body.removeEventListener(
			'wc-blocks_added_to_cart',
			refreshData
		);
		document.body.removeEventListener(
			'wc-blocks_removed_from_cart',
			refreshData
		);
		window.removeEventListener( 'pageshow', refreshCachedCartData );
		removeJQueryAddedToCartEvent();
		removeJQueryRemovedFromCartEvent();
	};
};

const removeListeners = (): void => {
	if ( window.wcBlocksStoreCartListeners.count === 1 ) {
		window.wcBlocksStoreCartListeners.remove();
	}
	window.wcBlocksStoreCartListeners.count--;
};

/**
 * This will keep track of jQuery and DOM events that invalidate the store resolution.
 */
export const useStoreCartEventListeners = (): void => {
	useEffect( () => {
		addListeners();
		return removeListeners;
	}, [] );
};

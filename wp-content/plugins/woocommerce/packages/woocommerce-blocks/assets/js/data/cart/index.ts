/**
 * External dependencies
 */
import { registerStore } from '@wordpress/data';
import { controls as dataControls } from '@wordpress/data-controls';

/**
 * Internal dependencies
 */
import { STORE_KEY } from './constants';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import reducer, { State } from './reducers';
import type { SelectFromMap, DispatchFromMap } from '../mapped-types';
import { pushChanges, flushChanges } from './push-changes';
import {
	updatePaymentMethods,
	debouncedUpdatePaymentMethods,
} from './update-payment-methods';
import { ResolveSelectFromMap } from '../mapped-types';

// Please update from deprecated "registerStore" to "createReduxStore" when this PR is merged:
// https://github.com/WordPress/gutenberg/pull/45513
const registeredStore = registerStore< State >( STORE_KEY, {
	reducer,
	actions,
	// eslint-disable-next-line @typescript-eslint/no-explicit-any
	controls: dataControls,
	selectors,
	resolvers,
	__experimentalUseThunks: true,
} );

registeredStore.subscribe( pushChanges );

// This will skip the debounce and immediately push changes to the server when a field is blurred.
document.body.addEventListener( 'focusout', ( event: FocusEvent ) => {
	if (
		event.target &&
		event.target instanceof Element &&
		event.target.tagName.toLowerCase() === 'input'
	) {
		flushChanges();
	}
} );

// First we will run the updatePaymentMethods function without any debounce to ensure payment methods are ready as soon
// as the cart is loaded. After that, we will unsubscribe this function and instead run the
// debouncedUpdatePaymentMethods function on subsequent cart updates.
const unsubscribeUpdatePaymentMethods = registeredStore.subscribe( async () => {
	const didActionDispatch = await updatePaymentMethods();
	if ( didActionDispatch ) {
		// The function we're currently in will unsubscribe itself. When we reach this line, this will be the last time
		// this function is called.
		unsubscribeUpdatePaymentMethods();
		// Resubscribe, but with the debounced version of updatePaymentMethods.
		registeredStore.subscribe( debouncedUpdatePaymentMethods );
	}
} );

export const CART_STORE_KEY = STORE_KEY;

declare module '@wordpress/data' {
	function dispatch(
		key: typeof CART_STORE_KEY
	): DispatchFromMap< typeof actions >;
	function select( key: typeof CART_STORE_KEY ): SelectFromMap<
		typeof selectors
	> & {
		hasFinishedResolution: ( selector: string ) => boolean;
	};
}

/**
 * CartDispatchFromMap is a type that maps the cart store's action creators to the dispatch function passed to thunks.
 */
export type CartDispatchFromMap = DispatchFromMap< typeof actions >;

/**
 * CartResolveSelectFromMap is a type that maps the cart store's resolvers and selectors to the resolveSelect function
 * passed to thunks.
 */
export type CartResolveSelectFromMap = ResolveSelectFromMap<
	typeof resolvers & typeof selectors
>;

/**
 * CartSelectFromMap is a type that maps the cart store's selectors to the select function passed to thunks.
 */
export type CartSelectFromMap = SelectFromMap< typeof selectors >;

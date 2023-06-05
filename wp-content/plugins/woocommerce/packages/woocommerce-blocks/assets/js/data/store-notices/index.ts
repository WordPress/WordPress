/**
 * External dependencies
 */
import { createReduxStore, register } from '@wordpress/data';

/**
 * Internal dependencies
 */
import * as actions from './actions';
import * as selectors from './selectors';
import reducer from './reducers';
import { DispatchFromMap, SelectFromMap } from '../mapped-types';

const STORE_KEY = 'wc/store/store-notices';
const config = {
	reducer,
	actions,
	selectors,
};
const store = createReduxStore( STORE_KEY, config );
register( store );

export const STORE_NOTICES_STORE_KEY = STORE_KEY;

declare module '@wordpress/data' {
	function dispatch(
		key: typeof STORE_KEY
	): DispatchFromMap< typeof actions >;
	function select( key: typeof STORE_KEY ): SelectFromMap<
		typeof selectors
	> & {
		hasFinishedResolution: ( selector: string ) => boolean;
	};
}

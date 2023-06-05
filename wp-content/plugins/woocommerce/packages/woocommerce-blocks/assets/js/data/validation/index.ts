/**
 * External dependencies
 */
import { createReduxStore, register } from '@wordpress/data';

/**
 * Internal dependencies
 */
import reducer from './reducers';
import { STORE_KEY } from './constants';
import * as actions from './actions';
import * as selectors from './selectors';
import { DispatchFromMap, SelectFromMap } from '../mapped-types';

export const config = {
	reducer,
	selectors,
	actions,
};

const store = createReduxStore( STORE_KEY, config );
register( store );

export const VALIDATION_STORE_KEY = STORE_KEY;

declare module '@wordpress/data' {
	function dispatch(
		key: typeof VALIDATION_STORE_KEY
	): DispatchFromMap< typeof actions >;
	function select( key: typeof VALIDATION_STORE_KEY ): SelectFromMap<
		typeof selectors
	> & {
		hasFinishedResolution: ( selector: string ) => boolean;
	};
}

/**
 * External dependencies
 */
import { createReduxStore, register } from '@wordpress/data';
import { controls as dataControls } from '@wordpress/data-controls';

/**
 * Internal dependencies
 */
import reducer from './reducers';
import { STORE_KEY } from './constants';
import * as actions from './actions';
import { controls as sharedControls } from '../shared-controls';
import * as selectors from './selectors';
import { DispatchFromMap, SelectFromMap } from '../mapped-types';

export const config = {
	reducer,
	selectors,
	actions,
	// eslint-disable-next-line @typescript-eslint/no-explicit-any
	controls: { ...dataControls, ...sharedControls } as any,
	__experimentalUseThunks: true,
};

const store = createReduxStore( STORE_KEY, config );
register( store );

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

export const PAYMENT_STORE_KEY = STORE_KEY;

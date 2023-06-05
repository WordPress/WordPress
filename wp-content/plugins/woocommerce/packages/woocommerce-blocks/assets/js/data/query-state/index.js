/**
 * External dependencies
 */
import { register, createReduxStore } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { STORE_KEY } from './constants';
import * as selectors from './selectors';
import * as actions from './actions';
import reducer from './reducers';

const store = createReduxStore( STORE_KEY, {
	reducer,
	actions,
	selectors,
} );

register( store );

export const QUERY_STATE_STORE_KEY = STORE_KEY;

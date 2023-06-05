/**
 * External dependencies
 */
import { combineReducers } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { ACTION_TYPES as types } from './action-types';
import {
	extractResourceNameFromRoute,
	getRouteIds,
	simplifyRouteWithId,
} from './utils';
import { hasInState, updateState } from '../utils';

/**
 * Reducer for routes
 *
 * @param {Object} state  The current state.
 * @param {Object} action The action object for parsing.
 *
 * @return {Object} The new (or original) state.
 */
export const receiveRoutes = ( state = {}, action ) => {
	const { type, routes, namespace } = action;
	if ( type === types.RECEIVE_MODEL_ROUTES ) {
		routes.forEach( ( route ) => {
			const resourceName = extractResourceNameFromRoute(
				namespace,
				route
			);
			if ( resourceName && resourceName !== namespace ) {
				const routeIdNames = getRouteIds( route );
				const savedRoute = simplifyRouteWithId( route, routeIdNames );
				if (
					! hasInState( state, [
						namespace,
						resourceName,
						savedRoute,
					] )
				) {
					state = updateState(
						state,
						[ namespace, resourceName, savedRoute ],
						routeIdNames
					);
				}
			}
		} );
	}
	return state;
};

export default combineReducers( {
	routes: receiveRoutes,
} );

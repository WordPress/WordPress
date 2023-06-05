/**
 * External dependencies
 */
import { apiFetch } from '@wordpress/data-controls';
import { controls } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { receiveRoutes } from './actions';
import { STORE_KEY } from './constants';

/**
 * Resolver for the getRoute selector.
 *
 * Note: All this essentially does is ensure the routes for the given namespace
 * have been resolved.
 *
 * @param {string} namespace The namespace of the route being resolved.
 */
export function* getRoute( namespace ) {
	// we call this simply to do any resolution of all endpoints if necessary.
	// allows for jit population of routes for a given namespace.
	yield controls.resolveSelect( STORE_KEY, 'getRoutes', namespace );
}

/**
 * Resolver for the getRoutes selector.
 *
 * @param {string} namespace The namespace of the routes being resolved.
 */
export function* getRoutes( namespace ) {
	const routeResponse = yield apiFetch( { path: namespace } );
	const routes =
		routeResponse && routeResponse.routes
			? Object.keys( routeResponse.routes )
			: [];
	yield receiveRoutes( routes, namespace );
}

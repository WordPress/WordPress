/**
 * External dependencies
 */
import { sprintf } from '@wordpress/i18n';
import { createRegistrySelector } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { STORE_KEY } from './constants';

/**
 * Returns the requested route for the given arguments.
 *
 * @param {Object} state        The original state.
 * @param {string} namespace    The namespace for the route.
 * @param {string} resourceName The resource being requested
 *                              (eg. products/attributes)
 * @param {Array}  [ids]        This is for any ids that might be implemented in
 *                              the route request. It is not for any query
 *                              parameters.
 *
 *                              Ids example:
 *                              If you are looking for the route for a single product on the `wc/blocks`
 *                              namespace, then you'd have `[ 20 ]` as the ids.  This would produce something
 *                              like `/wc/blocks/products/20`
 *
 *
 * @throws {Error}  If there is no route for the given arguments, then this will
 *                  throw
 *
 * @return {string} The route if it is available.
 */
export const getRoute = createRegistrySelector(
	( select ) =>
		( state, namespace, resourceName, ids = [] ) => {
			const hasResolved = select( STORE_KEY ).hasFinishedResolution(
				'getRoutes',
				[ namespace ]
			);
			state = state.routes;
			let error = '';
			if ( ! state[ namespace ] ) {
				error = sprintf(
					'There is no route for the given namespace (%s) in the store',
					namespace
				);
			} else if ( ! state[ namespace ][ resourceName ] ) {
				error = sprintf(
					'There is no route for the given resource name (%s) in the store',
					resourceName
				);
			}
			if ( error !== '' ) {
				if ( hasResolved ) {
					throw new Error( error );
				}
				return '';
			}
			const route = getRouteFromResourceEntries(
				state[ namespace ][ resourceName ],
				ids
			);
			if ( route === '' ) {
				if ( hasResolved ) {
					throw new Error(
						sprintf(
							'While there is a route for the given namespace (%1$s) and resource name (%2$s), there is no route utilizing the number of ids you included in the select arguments. The available routes are: (%3$s)',
							namespace,
							resourceName,
							JSON.stringify( state[ namespace ][ resourceName ] )
						)
					);
				}
			}
			return route;
		}
);

/**
 * Return all the routes for a given namespace.
 *
 * @param {Object} state     The current state.
 * @param {string} namespace The namespace to return routes for.
 *
 * @return {Array} An array of all routes for the given namespace.
 */
export const getRoutes = createRegistrySelector(
	( select ) => ( state, namespace ) => {
		const hasResolved = select( STORE_KEY ).hasFinishedResolution(
			'getRoutes',
			[ namespace ]
		);
		const routes = state.routes[ namespace ];
		if ( ! routes ) {
			if ( hasResolved ) {
				throw new Error(
					sprintf(
						'There is no route for the given namespace (%s) in the store',
						namespace
					)
				);
			}
			return [];
		}
		let namespaceRoutes = [];
		for ( const resourceName in routes ) {
			namespaceRoutes = [
				...namespaceRoutes,
				...Object.keys( routes[ resourceName ] ),
			];
		}
		return namespaceRoutes;
	}
);

/**
 * Returns the route from the given slice of the route state.
 *
 * @param {Object} stateSlice This will be a slice of the route state from a
 *                            given namespace and resource name.
 * @param {Array}  [ids=[]]   Any id references that are to be replaced in
 *                            route placeholders.
 *
 * @return {string}  The route or an empty string if nothing found.
 */
const getRouteFromResourceEntries = ( stateSlice, ids = [] ) => {
	// convert to array for easier discovery
	stateSlice = Object.entries( stateSlice );
	const match = stateSlice.find( ( [ , idNames ] ) => {
		return ids.length === idNames.length;
	} );
	const [ matchingRoute, routePlaceholders ] = match || [];
	// if we have a matching route, let's return it.
	if ( matchingRoute ) {
		return ids.length === 0
			? matchingRoute
			: assembleRouteWithPlaceholders(
					matchingRoute,
					routePlaceholders,
					ids
			  );
	}
	return '';
};

/**
 * For a given route, route parts and ids,
 *
 * @param {string} route
 * @param {Array}  routePlaceholders
 * @param {Array}  ids
 *
 * @return {string} Assembled route.
 */
const assembleRouteWithPlaceholders = ( route, routePlaceholders, ids ) => {
	routePlaceholders.forEach( ( part, index ) => {
		route = route.replace( `{${ part }}`, ids[ index ] );
	} );
	return route;
};

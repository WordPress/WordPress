/**
 * External dependencies
 */
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { hasInState } from '../utils';
import { DEFAULT_EMPTY_ARRAY } from './constants';

const getFromState = ( {
	state,
	namespace,
	resourceName,
	query,
	ids,
	type = 'items',
	fallback = DEFAULT_EMPTY_ARRAY,
} ) => {
	// prep ids and query for state retrieval
	ids = JSON.stringify( ids );
	query = query !== null ? addQueryArgs( '', query ) : '';
	if ( hasInState( state, [ namespace, resourceName, ids, query, type ] ) ) {
		return state[ namespace ][ resourceName ][ ids ][ query ][ type ];
	}
	return fallback;
};

const getCollectionHeaders = (
	state,
	namespace,
	resourceName,
	query = null,
	ids = DEFAULT_EMPTY_ARRAY
) => {
	return getFromState( {
		state,
		namespace,
		resourceName,
		query,
		ids,
		type: 'headers',
		fallback: undefined,
	} );
};

/**
 * Retrieves the collection items from the state for the given arguments.
 *
 * @param {Object} state        The current collections state.
 * @param {string} namespace    The namespace for the collection.
 * @param {string} resourceName The resource name for the collection.
 * @param {Object} [query=null] The query for the collection request.
 * @param {Array}  [ids=[]]     Any ids for the collection request (these are
 *                              values that would be added to the route for a
 *                              route with id placeholders)
 * @return {Array} an array of items stored in the collection.
 */
export const getCollection = (
	state,
	namespace,
	resourceName,
	query = null,
	ids = DEFAULT_EMPTY_ARRAY
) => {
	return getFromState( { state, namespace, resourceName, query, ids } );
};

export const getCollectionError = (
	state,
	namespace,
	resourceName,
	query = null,
	ids = DEFAULT_EMPTY_ARRAY
) => {
	return getFromState( {
		state,
		namespace,
		resourceName,
		query,
		ids,
		type: 'error',
		fallback: null,
	} );
};

/**
 * This selector enables retrieving a specific header value from a given
 * collection request.
 *
 * Example:
 *
 * ```js
 * const totalProducts = wp.data.select( COLLECTION_STORE_KEY )
 *   .getCollectionHeader( '/wc/blocks', 'products', 'x-wp-total' )
 * ```
 *
 * @param {string} state        The current collection state.
 * @param {string} header       The header to retrieve.
 * @param {string} namespace    The namespace for the collection.
 * @param {string} resourceName The model name for the collection.
 * @param {Object} [query=null] The query object on the collection request.
 * @param {Array}  [ids=[]]     Any ids for the collection request (these are
 *                              values that would be added to the route for a
 *                              route with id placeholders)
 *
 * @return {*|null} The value for the specified header, null if there are no
 * headers available and undefined if the header does not exist for the
 * collection.
 */
export const getCollectionHeader = (
	state,
	header,
	namespace,
	resourceName,
	query = null,
	ids = DEFAULT_EMPTY_ARRAY
) => {
	const headers = getCollectionHeaders(
		state,
		namespace,
		resourceName,
		query,
		ids
	);
	// Can't just do a truthy check because `getCollectionHeaders` resolver
	// invokes the `getCollection` selector to trigger the resolution of the
	// collection request. Its fallback is an empty array.
	if ( headers && headers.get ) {
		return headers.has( header ) ? headers.get( header ) : undefined;
	}
	return null;
};

/**
 * Gets the last modified header for the collection.
 *
 * @param {string} state The current collection state.
 * @return {number} Timestamp.
 */
export const getCollectionLastModified = ( state ) => {
	return state.lastModified || 0;
};

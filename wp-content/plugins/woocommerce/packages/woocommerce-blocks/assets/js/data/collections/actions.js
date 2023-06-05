/**
 * Internal dependencies
 */
import { ACTION_TYPES as types } from './action-types';

let Headers = window.Headers || null;
Headers = Headers
	? new Headers()
	: { get: () => undefined, has: () => undefined };

/**
 * Returns an action object used in updating the store with the provided items
 * retrieved from a request using the given querystring.
 *
 * This is a generic response action.
 *
 * @param {string}   namespace        The namespace for the collection route.
 * @param {string}   resourceName     The resource name for the collection route.
 * @param {string}   [queryString=''] The query string for the collection
 * @param {Array}    [ids=[]]         An array of ids (in correct order) for the
 *                                    model.
 * @param {Object}   [response={}]    An object containing the response from the
 *                                    collection request.
 * @param {Array<*>} response.items   An array of items for the given collection.
 * @param {Headers}  response.headers A Headers object from the response
 *                                    link https://developer.mozilla.org/en-US/docs/Web/API/Headers
 * @param {boolean}  [replace=false]  If true, signals to replace the current
 *                                    items in the state with the provided
 *                                    items.
 * @return {
 * 	{
 * 		type: string,
 * 		namespace: string,
 * 		resourceName: string,
 * 		queryString: string,
 * 		ids: Array<*>,
 * 		items: Array<*>,
 *	}
 * } Object for action.
 */
export function receiveCollection(
	namespace,
	resourceName,
	queryString = '',
	ids = [],
	response = { items: [], headers: Headers },
	replace = false
) {
	return {
		type: replace ? types.RESET_COLLECTION : types.RECEIVE_COLLECTION,
		namespace,
		resourceName,
		queryString,
		ids,
		response,
	};
}

export function receiveCollectionError(
	namespace,
	resourceName,
	queryString,
	ids,
	error
) {
	return {
		type: 'ERROR',
		namespace,
		resourceName,
		queryString,
		ids,
		response: {
			items: [],
			headers: Headers,
			error,
		},
	};
}

export function receiveLastModified( timestamp ) {
	return {
		type: types.RECEIVE_LAST_MODIFIED,
		timestamp,
	};
}

/**
 * Internal dependencies
 */
import { ACTION_TYPES as types } from './action-types';

/**
 * Action creator for setting a single query-state value for a given context.
 *
 * @param {string} context  Context for query state being stored.
 * @param {string} queryKey Key for query item.
 * @param {*}      value    The value for the query item.
 *
 * @return {Object} The action object.
 */
export const setQueryValue = ( context, queryKey, value ) => {
	return {
		type: types.SET_QUERY_KEY_VALUE,
		context,
		queryKey,
		value,
	};
};

/**
 * Action creator for setting query-state for a given context.
 *
 * @param {string} context Context for query state being stored.
 * @param {*}      value   Query state being stored for the given context.
 *
 * @return {Object} The action object.
 */
export const setValueForQueryContext = ( context, value ) => {
	return {
		type: types.SET_QUERY_CONTEXT_VALUE,
		context,
		value,
	};
};

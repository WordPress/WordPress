/**
 * Internal dependencies
 */
import { getStateForContext } from './utils';

/**
 * Selector for retrieving a specific query-state for the given context.
 *
 * @param {Object} state        Current state.
 * @param {string} context      Context for the query-state being retrieved.
 * @param {string} queryKey     Key for the specific query-state item.
 * @param {*}      defaultValue Default value for the query-state key if it doesn't
 *                              currently exist in state.
 *
 * @return {*} The currently stored value or the defaultValue if not present.
 */
export const getValueForQueryKey = (
	state,
	context,
	queryKey,
	defaultValue = {}
) => {
	let stateContext = getStateForContext( state, context );
	if ( stateContext === null ) {
		return defaultValue;
	}
	stateContext = JSON.parse( stateContext );
	return typeof stateContext[ queryKey ] !== 'undefined'
		? stateContext[ queryKey ]
		: defaultValue;
};

/**
 * Selector for retrieving the query-state for the given context.
 *
 * @param {Object} state        The current state.
 * @param {string} context      The context for the query-state being retrieved.
 * @param {*}      defaultValue The default value to return if there is no state for
 *                              the given context.
 *
 * @return {*} The currently stored query-state for the given context or
 *             defaultValue if not present in state.
 */
export const getValueForQueryContext = (
	state,
	context,
	defaultValue = {}
) => {
	const stateContext = getStateForContext( state, context );
	return stateContext === null ? defaultValue : JSON.parse( stateContext );
};

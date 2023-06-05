/**
 * External dependencies
 */
import { QUERY_STATE_STORE_KEY as storeKey } from '@woocommerce/block-data';
import { useSelect, useDispatch } from '@wordpress/data';
import { useRef, useEffect, useCallback } from '@wordpress/element';
import isShallowEqual from '@wordpress/is-shallow-equal';
import { useShallowEqual, usePrevious } from '@woocommerce/base-hooks';

/**
 * Internal dependencies
 */

import { useQueryStateContext } from '../providers/query-state-context';

/**
 * A custom hook that exposes the current query state and a setter for the query
 * state store for the given context.
 *
 * "Query State" is a wp.data store that keeps track of an arbitrary object of
 * query keys and their values.
 *
 * @param {string} [context] What context to retrieve the query state for. If not
 *                           provided, this hook will attempt to get the context
 *                           from the query state context provided by the
 *                           QueryStateContextProvider
 *
 * @return {Array} An array that has two elements. The first element is the
 *                 query state value for the given context.  The second element
 *                 is a dispatcher function for setting the query state.
 */
export const useQueryStateByContext = ( context ) => {
	const queryStateContext = useQueryStateContext();
	context = context || queryStateContext;
	const queryState = useSelect(
		( select ) => {
			const store = select( storeKey );
			return store.getValueForQueryContext( context, undefined );
		},
		[ context ]
	);
	const { setValueForQueryContext } = useDispatch( storeKey );
	const setQueryState = useCallback(
		( value ) => {
			setValueForQueryContext( context, value );
		},
		[ context, setValueForQueryContext ]
	);

	return [ queryState, setQueryState ];
};

/**
 * A custom hook that exposes the current query state value and a setter for the
 * given context and query key.
 *
 * "Query State" is a wp.data store that keeps track of an arbitrary object of
 * query keys and their values.
 *
 * @param {*}      queryKey       The specific query key to retrieve the value for.
 * @param {*}      [defaultValue] Default value if query does not exist.
 * @param {string} [context]      What context to retrieve the query state for. If
 *                                not provided will attempt to use what is provided
 *                                by query state context.
 *
 * @return {*}  Whatever value is set at the query state index using the
 *              provided context and query key.
 */
export const useQueryStateByKey = ( queryKey, defaultValue, context ) => {
	const queryStateContext = useQueryStateContext();
	context = context || queryStateContext;
	const queryValue = useSelect(
		( select ) => {
			const store = select( storeKey );
			return store.getValueForQueryKey( context, queryKey, defaultValue );
		},
		[ context, queryKey ]
	);

	const { setQueryValue } = useDispatch( storeKey );
	const setQueryValueByKey = useCallback(
		( value ) => {
			setQueryValue( context, queryKey, value );
		},
		[ context, queryKey, setQueryValue ]
	);

	return [ queryValue, setQueryValueByKey ];
};

/**
 * A custom hook that works similarly to useQueryStateByContext. However, this
 * hook allows for synchronizing with a provided queryState object.
 *
 * This hook does the following things with the provided `synchronizedQuery`
 * object:
 *
 * - whenever synchronizedQuery varies between renders, the queryState will be
 *   updated to a merged object of the internal queryState and the provided
 *   object.  Note, any values from the same properties between objects will
 *   be set from synchronizedQuery.
 * - if there are no changes between renders, then the existing internal
 *   queryState is always returned.
 * - on initial render, the synchronizedQuery value is returned.
 *
 * Typically, this hook would be used in a scenario where there may be external
 * triggers for updating the query state (i.e. initial population of query
 * state by hydration or component attributes, or routing url changes that
 * affect query state).
 *
 * @param {Object} synchronizedQuery A provided query state object to
 *                                   synchronize internal query state with.
 * @param {string} [context]         What context to retrieve the query state
 *                                   for. If not provided, will be pulled from
 *                                   the QueryStateContextProvider in the tree.
 */
export const useSynchronizedQueryState = ( synchronizedQuery, context ) => {
	const queryStateContext = useQueryStateContext();
	context = context || queryStateContext;
	const [ queryState, setQueryState ] = useQueryStateByContext( context );
	const currentQueryState = useShallowEqual( queryState );
	const currentSynchronizedQuery = useShallowEqual( synchronizedQuery );
	const previousSynchronizedQuery = usePrevious( currentSynchronizedQuery );
	// used to ensure we allow initial synchronization to occur before
	// returning non-synced state.
	const isInitialized = useRef( false );
	// update queryState anytime incoming synchronizedQuery changes
	useEffect( () => {
		if (
			! isShallowEqual(
				previousSynchronizedQuery,
				currentSynchronizedQuery
			)
		) {
			setQueryState(
				Object.assign( {}, currentQueryState, currentSynchronizedQuery )
			);
			isInitialized.current = true;
		}
	}, [
		currentQueryState,
		currentSynchronizedQuery,
		previousSynchronizedQuery,
		setQueryState,
	] );
	return isInitialized.current
		? [ queryState, setQueryState ]
		: [ synchronizedQuery, setQueryState ];
};

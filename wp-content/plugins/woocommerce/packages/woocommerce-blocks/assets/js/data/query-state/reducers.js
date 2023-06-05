/**
 * Internal dependencies
 */
import { ACTION_TYPES as types } from './action-types';
import { getStateForContext } from './utils';

/**
 * Reducer for processing actions related to the query state store.
 *
 * @param {Object} state  Current state in store.
 * @param {Object} action Action being processed.
 */
const queryStateReducer = ( state = {}, action ) => {
	const { type, context, queryKey, value } = action;
	const prevState = getStateForContext( state, context );
	let newState;
	switch ( type ) {
		case types.SET_QUERY_KEY_VALUE:
			const prevStateObject =
				prevState !== null ? JSON.parse( prevState ) : {};

			// mutate it and JSON.stringify to compare
			prevStateObject[ queryKey ] = value;
			newState = JSON.stringify( prevStateObject );

			if ( prevState !== newState ) {
				state = {
					...state,
					[ context ]: newState,
				};
			}
			break;
		case types.SET_QUERY_CONTEXT_VALUE:
			newState = JSON.stringify( value );
			if ( prevState !== newState ) {
				state = {
					...state,
					[ context ]: newState,
				};
			}
			break;
	}
	return state;
};

export default queryStateReducer;

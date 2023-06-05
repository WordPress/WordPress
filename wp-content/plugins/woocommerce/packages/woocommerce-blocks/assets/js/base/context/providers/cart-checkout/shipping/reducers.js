/**
 * Internal dependencies
 */
import { ERROR_TYPES } from './constants';

/**
 * Reducer for shipping status state
 *
 * @param {string} state       The current status.
 * @param {Object} action      The incoming action.
 * @param {string} action.type The type of action.
 */
export const errorStatusReducer = ( state, { type } ) => {
	if ( Object.values( ERROR_TYPES ).includes( type ) ) {
		return type;
	}
	return state;
};

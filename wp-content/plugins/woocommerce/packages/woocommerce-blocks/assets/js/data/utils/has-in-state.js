/**
 * External dependencies
 */
import { has } from 'lodash';

/**
 * Utility for returning whether the given path exists in the state.
 *
 * @param {Object} state The state being checked
 * @param {Array}  path  The path to check
 *
 * @return {boolean} True means this exists in the state.
 */
export default function hasInState( state, path ) {
	return has( state, path );
}

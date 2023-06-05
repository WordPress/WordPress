/**
 * External dependencies
 */
import { setWith, clone } from 'lodash';

/**
 * Utility for updating state and only cloning objects in the path that changed.
 *
 * @param {Object} state The state being updated
 * @param {Array}  path  The path being updated
 * @param {*}      value The value to update for the path
 *
 * @return {Object} The new state
 */
export default function updateState( state, path, value ) {
	return setWith( clone( state ), path, value, clone );
}

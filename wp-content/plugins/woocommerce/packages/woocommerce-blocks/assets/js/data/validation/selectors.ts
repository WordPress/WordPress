/**
 * Internal dependencies
 */
import type { State } from './reducers';

/**
 * Gets a validation error by ID.
 *
 * @param { State }  state   The current state.
 * @param { string } errorId The error ID.
 * @return { string } 		The validation error.
 */
export const getValidationError = ( state: State, errorId: string ) =>
	state[ errorId ];

/**
 * Gets a validation error ID for use in HTML which can be used as a CSS selector, or to reference an error message.
 *
 * @param { State }  state   The current state.
 * @param { string } errorId The error ID.
 * @return { string } 		The validation error ID.
 */
export const getValidationErrorId = ( state: State, errorId: string ) => {
	if ( ! state.hasOwnProperty( errorId ) || state[ errorId ].hidden ) {
		return;
	}
	return `validate-error-${ errorId }`;
};

/**
 * Whether the store has validation errors.
 *
 * @param { State } state The current state.
 * @return { boolean } 	Whether the store has validation errors or not.
 */
export const hasValidationErrors = ( state: State ) => {
	return Object.keys( state ).length > 0;
};

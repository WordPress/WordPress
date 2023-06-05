/**
 * External dependencies
 */
import { useState, useCallback } from '@wordpress/element';

/**
 * Helper method for throwing an error in a React Hook.
 *
 * @see https://github.com/facebook/react/issues/14981
 */
export const useThrowError = (): ( ( error: Error ) => void ) => {
	const [ , setState ] = useState();
	return useCallback( ( error: Error ): void => {
		setState( () => {
			throw error;
		} );
	}, [] );
};

/**
 * External dependencies
 */
import { useCallback, useEffect, useRef } from '@wordpress/element';

/**
 * Returns a boolean value based on whether the current component has been mounted.
 *
 * @return {boolean} If the component has been mounted.
 *
 * @example
 *
 * ```js
 * const App = () => {
 * 	const isMounted = useIsMounted();
 *
 * 	if ( ! isMounted() ) {
 * 	    return null;
 * 	}
 *
 * 	return </div>;
 * };
 * ```
 */

export function useIsMounted() {
	const isMounted = useRef( false );

	useEffect( () => {
		isMounted.current = true;

		return () => {
			isMounted.current = false;
		};
	}, [] );

	return useCallback( () => isMounted.current, [] );
}

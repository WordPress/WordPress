/* eslint-disable you-dont-need-lodash-underscore/throttle */

/**
 * External dependencies
 */
import { DebouncedFunc, throttle, ThrottleSettings } from 'lodash';
import { useCallback, useEffect, useRef } from '@wordpress/element';

/**
 * Throttles a function inside a React functional component
 */
// Disabling this as lodash expects this and I didn't make using `unknown`
// work in practice.
// eslint-disable-next-line @typescript-eslint/no-explicit-any
export function useThrottle< T extends ( ...args: any[] ) => any >(
	cb: T,
	delay: number,
	options?: ThrottleSettings
): DebouncedFunc< T > {
	const cbRef = useRef( cb );

	useEffect( () => {
		cbRef.current = cb;
	} );

	// Disabling because we can't pass an arrow function in this case
	// eslint-disable-next-line react-hooks/exhaustive-deps
	const throttledCb = useCallback(
		throttle( ( ...args ) => cbRef.current( ...args ), delay, options ),
		[ delay ]
	);

	return throttledCb;
}

/**
 * External dependencies
 */
import { useRef } from '@wordpress/element';
import isShallowEqual from '@wordpress/is-shallow-equal';

/**
 * A custom hook that compares the provided value across renders and returns the
 * previous instance if shallow equality with previous instance exists.
 *
 * This is particularly useful when non-primitive types are used as
 * dependencies for react hooks.
 *
 * @param {*} value Value to keep the same if satisfies shallow equality.
 *
 * @return {*} The previous cached instance of the value if the current has  shallow equality with it.
 */
export function useShallowEqual< T >( value: T ): T {
	const ref = useRef< T >( value );
	if ( ! isShallowEqual( value, ref.current ) ) {
		ref.current = value;
	}
	return ref.current;
}

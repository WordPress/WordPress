/**
 * External dependencies
 */
import { useRef, useEffect } from '@wordpress/element';

interface Validation< T > {
	( value: T, previousValue: T | undefined ): boolean;
}
/**
 * Use Previous based on https://usehooks.com/usePrevious/.
 *
 * @param {*}        value
 * @param {Function} [validation] Function that needs to validate for the value
 *                                to be updated.
 */
export function usePrevious< T >(
	value: T,
	validation?: Validation< T >
): T | undefined {
	const ref = useRef< T >();

	useEffect( () => {
		if (
			ref.current !== value &&
			( ! validation || validation( value, ref.current ) )
		) {
			ref.current = value;
		}
	}, [ value, validation ] );

	return ref.current;
}

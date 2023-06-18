/**
 * Returns an object without a key.
 */
export function objectOmit< T, K extends keyof T >( obj: T, key: K ) {
	const { [ key ]: omit, ...rest } = obj;

	return rest;
}

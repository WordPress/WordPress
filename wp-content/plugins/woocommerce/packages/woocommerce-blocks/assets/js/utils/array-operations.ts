/**
 * Returns the difference between two arrays (A - B)
 */
export function arrayDifferenceBy< T >( a: T[], b: T[], key: keyof T ) {
	const keys = new Set( b.map( ( item ) => item[ key ] ) );

	return a.filter( ( item ) => ! keys.has( item[ key ] ) );
}

/**
 * Returns the union of two arrays (A ∪ B)
 */
export function arrayUnionBy< T >( a: T[], b: T[], key: keyof T ) {
	const difference = arrayDifferenceBy( b, a, key );

	return [ ...a, ...difference ];
}

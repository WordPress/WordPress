/**
 * Internal dependencies
 */

import { isNull } from './null';

export const isObject = < T extends Record< string, unknown >, U >(
	term: T | U
): term is NonNullable< T > => {
	return (
		! isNull( term ) &&
		term instanceof Object &&
		term.constructor === Object
	);
};

export function objectHasProp< P extends PropertyKey >(
	target: unknown,
	property: P
): target is { [ K in P ]: unknown } {
	// The `in` operator throws a `TypeError` for non-object values.
	return isObject( target ) && property in target;
}

export const isEmptyObject = < T extends { [ key: string ]: unknown } >(
	object: T
) => {
	return Object.keys( object ).length === 0;
};

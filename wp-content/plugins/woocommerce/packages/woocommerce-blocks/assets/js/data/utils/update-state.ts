/**
 * Utility for updating nested state in the path that changed.
 */
function updateNested< T >( // The state being updated
	state: T,
	// The path being updated
	path: string[],
	// The value to update for the path
	value: unknown,
	// The current index in the path
	index = 0
): T {
	const key = path[ index ] as keyof T;
	if ( index === path.length - 1 ) {
		return { ...state, [ key ]: value };
	}

	const nextState = state[ key ] || {};
	return {
		...state,
		[ key ]: updateNested( nextState, path, value, index + 1 ),
	} as T;
}

/**
 * Utility for updating state and only cloning objects in the path that changed.
 */
export default function updateState< T >(
	// The state being updated
	state: T,
	// The path being updated
	path: string[],
	// The value to update for the path
	value: unknown
): T {
	return updateNested( state, path, value );
}

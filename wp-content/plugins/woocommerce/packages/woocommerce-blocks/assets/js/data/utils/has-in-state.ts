const has = ( obj: Record< string, unknown >, path: string[] ): boolean => {
	return (
		!! path &&
		!! path.reduce< unknown >(
			( prevObj, key ) =>
				typeof prevObj === 'object' && prevObj !== null
					? ( prevObj as Record< string, unknown > )[ key ]
					: undefined,
			obj
		)
	);
};

/**
 * Utility for returning whether the given path exists in the state.
 *
 * @param {Object} state The state being checked
 * @param {Array}  path  The path to check
 *
 * @return {boolean} True means this exists in the state.
 */
export default function hasInState(
	state: Record< string, unknown >,
	path: string[]
): boolean {
	return has( state, path );
}

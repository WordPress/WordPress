/**
 * This is an assertion utility for validating that the incoming value prop
 * value on a given context provider is valid and throws an error if it isn't.
 *
 * Note: this asserts values that are expected to be an object.
 *
 * The validationMap is expected to be an object in the following shape.
 *
 * {
 *   [expectedPropertyName<String>]: {
 *     required: [expectedRequired<Boolean>]
 *     type: [expectedType<String>]
 *   }
 * }
 *
 * @param {string} contextName   The name of the context provider being
 *                               validated.
 * @param {Object} validationMap A map for validating the incoming value against.
 * @param {Object} value         The value being validated.
 *
 * @throws {Error}
 */
export const assertValidContextValue = (
	contextName,
	validationMap,
	value
) => {
	if ( typeof value !== 'object' ) {
		throw new Error(
			`${ contextName } expects an object for its context value`
		);
	}
	const errors = [];
	for ( const expectedProperty in validationMap ) {
		if (
			validationMap[ expectedProperty ].required &&
			typeof value[ expectedProperty ] === 'undefined'
		) {
			errors.push(
				`The ${ expectedProperty } is required and is not present.`
			);
		} else if (
			typeof value[ expectedProperty ] !== 'undefined' &&
			typeof value[ expectedProperty ] !==
				validationMap[ expectedProperty ].type
		) {
			errors.push(
				`The ${ expectedProperty } must be of ${
					validationMap[ expectedProperty ].type
				} and instead was ${ typeof value[ expectedProperty ] }`
			);
		}
	}
	if ( errors.length > 0 ) {
		throw new Error(
			`There was a problem with the value passed in on ${ contextName }:\n ${ errors.join(
				'\n'
			) }`
		);
	}
};

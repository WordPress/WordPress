export const isEmpty = ( value: unknown ): boolean => {
	return (
		value === null ||
		value === undefined ||
		( typeof value === 'object' && Object.keys( value ).length === 0 ) ||
		( typeof value === 'string' && value.trim().length === 0 )
	);
};

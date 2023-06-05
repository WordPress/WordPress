type Rating = '1' | '2' | '3' | '4' | '5';

export const isRatingQueryCollection = (
	value: unknown
): value is Rating[] => {
	return (
		Array.isArray( value ) &&
		value.every( ( v ) => [ '1', '2', '3', '4', '5' ].includes( v ) )
	);
};

export const keyBy = < T >( array: T[], key: keyof T ) => {
	return array.reduce( ( acc, value ) => {
		const computedKey = key ? String( value[ key ] ) : String( value );
		acc[ computedKey ] = value;
		return acc;
	}, {} as Record< string, T > );
};

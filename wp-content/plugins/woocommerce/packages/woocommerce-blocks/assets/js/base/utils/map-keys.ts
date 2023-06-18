export const mapKeys = (
	obj: object,
	mapper: ( value: unknown, key: string ) => string
) =>
	Object.entries( obj ).reduce(
		( acc, [ key, value ] ) => ( {
			...acc,
			[ mapper( value, key ) ]: value,
		} ),
		{}
	);

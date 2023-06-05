// eslint-disable-next-line @typescript-eslint/ban-types
export const isFunction = < T extends Function, U >(
	term: T | U
): term is T => {
	return typeof term === 'function';
};

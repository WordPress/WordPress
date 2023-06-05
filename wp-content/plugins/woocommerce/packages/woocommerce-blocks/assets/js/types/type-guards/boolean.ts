export const isBoolean = ( term: unknown ): term is boolean => {
	return typeof term === 'boolean';
};

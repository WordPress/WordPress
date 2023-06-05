export const isString = < U >( term: string | U ): term is string => {
	return typeof term === 'string';
};

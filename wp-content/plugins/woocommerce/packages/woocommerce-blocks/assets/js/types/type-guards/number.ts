export const isNumber = < U >( term: number | U ): term is number => {
	return typeof term === 'number';
};

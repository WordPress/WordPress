export const isError = ( term: unknown ): term is Error => {
	return term instanceof Error;
};

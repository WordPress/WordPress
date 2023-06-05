export const isNull = < T >( term: T | null ): term is null => {
	return term === null;
};

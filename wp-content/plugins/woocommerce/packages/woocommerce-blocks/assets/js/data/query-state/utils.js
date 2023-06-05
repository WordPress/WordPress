export const getStateForContext = ( state, context ) => {
	return typeof state[ context ] === 'undefined' ? null : state[ context ];
};

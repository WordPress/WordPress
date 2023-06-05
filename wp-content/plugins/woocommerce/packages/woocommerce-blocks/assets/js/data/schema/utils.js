/**
 * This returns a resource name string as an index for a given route.
 *
 * For example:
 * /wc/blocks/products/attributes/(?P<id>[\d]+)/terms
 * returns
 * /products/attributes/terms
 *
 * @param {string} namespace
 * @param {string} route
 *
 * @return {string} The resource name extracted from the route.
 */
export const extractResourceNameFromRoute = ( namespace, route ) => {
	route = route.replace( `${ namespace }/`, '' );
	return route.replace( /\/\(\?P\<[a-z_]*\>\[\\*[a-z]\]\+\)/g, '' );
};

/**
 * Returns an array of the identifier for the named capture groups in a given
 * route.
 *
 * For example, if the route was this:
 * /wc/blocks/products/attributes/(?P<attribute_id>[\d]+)/terms/(?P<id>[\d]+)
 *
 * ...then the following would get returned
 * [ 'attribute_id', 'id' ]
 *
 * @param {string} route - The route to extract identifier names from.
 *
 * @return {Array}  An array of named route identifier names.
 */
export const getRouteIds = ( route ) => {
	const matches = route.match( /\<[a-z_]*\>/g );
	if ( ! Array.isArray( matches ) || matches.length === 0 ) {
		return [];
	}
	return matches.map( ( match ) => match.replace( /<|>/g, '' ) );
};

/**
 * This replaces regex placeholders in routes with the relevant named string
 * found in the matchIds.
 *
 * Something like:
 * /wc/blocks/products/attributes/(?P<attribute_id>[\d]+)/terms/(?P<id>[\d]+)
 *
 * ..ends up as:
 * /wc/blocks/products/attributes/{attribute_id}/terms/{id}
 *
 * @param {string} route    The route to manipulate
 * @param {Array}  matchIds An array of named ids ( [ attribute_id, id ] )
 *
 * @return {string} The route with new id placeholders
 */
export const simplifyRouteWithId = ( route, matchIds ) => {
	if ( ! Array.isArray( matchIds ) || matchIds.length === 0 ) {
		return route;
	}
	matchIds.forEach( ( matchId ) => {
		const expression = `\\(\\?P<${ matchId }>.*?\\)`;
		route = route.replace( new RegExp( expression ), `{${ matchId }}` );
	} );
	return route;
};

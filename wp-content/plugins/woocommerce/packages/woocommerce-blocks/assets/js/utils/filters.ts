/**
 * External dependencies
 */
import { getQueryArg, getQueryArgs, addQueryArgs } from '@wordpress/url';
import { getSettingWithCoercion } from '@woocommerce/settings';
import { isBoolean } from '@woocommerce/types';

const filteringForPhpTemplate = getSettingWithCoercion(
	'is_rendering_php_template',
	false,
	isBoolean
);

/**
 * Returns specified parameter from URL
 *
 * @param {string} name Parameter you want the value of.
 */

export const PREFIX_QUERY_ARG_QUERY_TYPE = 'query_type_';
export const PREFIX_QUERY_ARG_FILTER_TYPE = 'filter_';

export function getUrlParameter( name: string ) {
	if ( ! window ) {
		return null;
	}
	return getQueryArg( window.location.href, name );
}

/**
 * Change the URL and reload the page if filtering for PHP templates.
 *
 * @param {string} newUrl New URL to be set.
 */
export function changeUrl( newUrl: string ) {
	if ( filteringForPhpTemplate ) {
		window.location.href = newUrl;
	} else {
		window.history.replaceState( {}, '', newUrl );
	}
}

/**
 * Run the query params through buildQueryString to normalise the params.
 *
 * @param {string} url URL to encode the search param from.
 */
export const normalizeQueryParams = ( url: string ) => {
	const queryArgs = getQueryArgs( url );
	return addQueryArgs( url, queryArgs );
};

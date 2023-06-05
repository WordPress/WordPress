/**
 * External dependencies
 */
import { isString } from '@woocommerce/types';
import { getUrlParameter } from '@woocommerce/utils';

/**
 * Internal dependencies
 */
import metadata from './block.json';

export const getActiveFilters = ( queryParamKey = 'filter_rating' ) => {
	const params = getUrlParameter( queryParamKey );

	if ( ! params ) {
		return [];
	}

	const parsedParams = isString( params )
		? params.split( ',' )
		: ( params as string[] );

	return parsedParams;
};

export function generateUniqueId() {
	return Math.floor( Math.random() * Date.now() );
}

export const formatSlug = ( slug: string ) =>
	slug
		.trim()
		.replace( /\s/g, '-' )
		.replace( /_/g, '-' )
		.replace( /-+/g, '-' )
		.replace( /[^a-zA-Z0-9-]/g, '' );

export const parseAttributes = ( data: Record< string, unknown > ) => {
	return {
		showFilterButton: data?.showFilterButton === 'true',
		showCounts: data?.showCounts !== 'false',
		isPreview: false,
		displayStyle:
			( isString( data?.displayStyle ) && data.displayStyle ) ||
			metadata.attributes.displayStyle.default,
		selectType:
			( isString( data?.selectType ) && data.selectType ) ||
			metadata.attributes.selectType.default,
	};
};

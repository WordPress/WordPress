/**
 * External dependencies
 */
import { isString } from '@woocommerce/types';
import { getUrlParameter } from '@woocommerce/utils';

/**
 * Internal dependencies
 */
import metadata from './block.json';

export const getActiveFilters = (
	filters: Record< string, string >,
	queryParamKey = 'filter_stock_status'
) => {
	const params = getUrlParameter( queryParamKey );

	if ( ! params ) {
		return [];
	}

	const parsedParams = isString( params )
		? params.split( ',' )
		: ( params as string[] );

	const filterKeys = Object.keys( filters );

	return parsedParams.filter( ( param ) => filterKeys.includes( param ) );
};

export function generateUniqueId() {
	return Math.floor( Math.random() * Date.now() );
}

export const formatSlug = ( slug: string ) =>
	slug
		.trim()
		.replace( /\s/g, '' )
		.replace( /_/g, '-' )
		.replace( /-+/g, '-' )
		.replace( /[^a-zA-Z0-9-]/g, '' );

export const parseAttributes = ( data: Record< string, unknown > ) => {
	return {
		heading: isString( data?.heading ) ? data.heading : '',
		headingLevel:
			( isString( data?.headingLevel ) &&
				parseInt( data.headingLevel, 10 ) ) ||
			metadata.attributes.headingLevel.default,
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

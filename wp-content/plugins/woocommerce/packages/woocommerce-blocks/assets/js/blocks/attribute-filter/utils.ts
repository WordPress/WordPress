/**
 * External dependencies
 */
import { addQueryArgs, removeQueryArgs } from '@wordpress/url';
import { QueryArgs } from '@wordpress/url/build-types/get-query-args';
import {
	getUrlParameter,
	PREFIX_QUERY_ARG_FILTER_TYPE,
	PREFIX_QUERY_ARG_QUERY_TYPE,
} from '@woocommerce/utils';
import { AttributeObject, isString } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import metadata from './block.json';

interface Param {
	attribute: string;
	operator: string;
	slug: Array< string >;
}

export function generateUniqueId() {
	return Math.floor( Math.random() * Date.now() );
}

export const parseTaxonomyToGenerateURL = ( taxonomy: string ) =>
	taxonomy.replace( 'pa_', '' );

/**
 * Formats filter values into a string for the URL parameters needed for filtering PHP templates.
 *
 * @param {string} url    Current page URL.
 * @param {Array}  params Parameters and their constraints.
 *
 * @return {string}       New URL with query parameters in it.
 */
export const formatParams = ( url: string, params: Array< Param > = [] ) => {
	const paramObject: Record< string, string > = {};

	params.forEach( ( param ) => {
		const { attribute, slug, operator } = param;

		// Custom filters are prefix with `pa_` so we need to remove this.
		const name = parseTaxonomyToGenerateURL( attribute );
		const values = slug.join( ',' );
		const queryType = `${ PREFIX_QUERY_ARG_QUERY_TYPE }${ name }`;
		const type = operator === 'in' ? 'or' : 'and';

		// The URL parameter requires the prefix filter_ with the attribute name.
		paramObject[ `${ PREFIX_QUERY_ARG_FILTER_TYPE }${ name }` ] = values;
		paramObject[ queryType ] = type;
	} );

	// Clean the URL before we add our new query parameters to it.
	const cleanUrl = removeQueryArgs( url, ...Object.keys( paramObject ) );

	return addQueryArgs( cleanUrl, paramObject );
};

export const areAllFiltersRemoved = ( {
	currentCheckedFilters,
	hasSetFilterDefaultsFromUrl,
}: {
	currentCheckedFilters: Array< string >;
	hasSetFilterDefaultsFromUrl: boolean;
} ) => hasSetFilterDefaultsFromUrl && currentCheckedFilters.length === 0;

export const getActiveFilters = (
	attributeObject: AttributeObject | undefined
) => {
	if ( attributeObject ) {
		const defaultAttributeParam = getUrlParameter(
			`filter_${ attributeObject.name }`
		);
		const defaultCheckedValue =
			typeof defaultAttributeParam === 'string'
				? defaultAttributeParam.split( ',' )
				: [];

		return defaultCheckedValue.map( ( value ) =>
			encodeURIComponent( value ).toLowerCase()
		);
	}

	return [];
};

export const isQueryArgsEqual = (
	currentQueryArgs: QueryArgs,
	newQueryArgs: QueryArgs
) => {
	// The user can add same two filter blocks for the same attribute.
	// We removed the query type from the check to avoid refresh loop.
	const filteredNewQueryArgs = Object.entries( newQueryArgs ).reduce(
		( acc, [ key, value ] ) => {
			return key.includes( 'query_type' )
				? acc
				: {
						...acc,
						[ key ]: value,
				  };
		},
		{}
	);

	return Object.entries( filteredNewQueryArgs ).reduce(
		( isEqual, [ key, value ] ) =>
			currentQueryArgs[ key ] === value ? isEqual : false,
		true
	);
};

export const formatSlug = ( slug: string ) =>
	slug
		.trim()
		.replace( /\s/g, '-' )
		.replace( /_/g, '-' )
		.replace( /-+/g, '-' )
		.replace( /[^a-zA-Z0-9-]/g, '' );

export const parseAttributes = ( data: Record< string, unknown > ) => {
	return {
		className: isString( data?.className ) ? data.className : '',
		attributeId: parseInt(
			isString( data?.attributeId ) ? data.attributeId : '0',
			10
		),
		showCounts: data?.showCounts !== 'false',
		queryType:
			( isString( data?.queryType ) && data.queryType ) ||
			metadata.attributes.queryType.default,
		heading: isString( data?.heading ) ? data.heading : '',
		headingLevel:
			( isString( data?.headingLevel ) &&
				parseInt( data.headingLevel, 10 ) ) ||
			metadata.attributes.headingLevel.default,
		displayStyle:
			( isString( data?.displayStyle ) && data.displayStyle ) ||
			metadata.attributes.displayStyle.default,
		showFilterButton: data?.showFilterButton === 'true',
		selectType:
			( isString( data?.selectType ) && data.selectType ) ||
			metadata.attributes.selectType.default,
		isPreview: false,
	};
};

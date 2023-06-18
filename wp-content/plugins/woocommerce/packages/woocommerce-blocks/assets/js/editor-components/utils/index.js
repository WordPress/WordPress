/**
 * External dependencies
 */
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import { getSetting } from '@woocommerce/settings';
import { blocksConfig } from '@woocommerce/block-settings';

/**
 * Get product query requests for the Store API.
 *
 * @param {Object}                     request           A query object with the list of selected products and search term.
 * @param {number[]}                   request.selected  Currently selected products.
 * @param {string=}                    request.search    Search string.
 * @param {(Record<string, unknown>)=} request.queryArgs Query args to pass in.
 */
const getProductsRequests = ( {
	selected = [],
	search = '',
	queryArgs = {},
} ) => {
	const isLargeCatalog = blocksConfig.productCount > 100;
	const defaultArgs = {
		per_page: isLargeCatalog ? 100 : 0,
		catalog_visibility: 'any',
		search,
		orderby: 'title',
		order: 'asc',
	};
	const requests = [
		addQueryArgs( '/wc/store/v1/products', {
			...defaultArgs,
			...queryArgs,
		} ),
	];

	// If we have a large catalog, we might not get all selected products in the first page.
	if ( isLargeCatalog && selected.length ) {
		requests.push(
			addQueryArgs( '/wc/store/v1/products', {
				catalog_visibility: 'any',
				include: selected,
				per_page: 0,
			} )
		);
	}

	return requests;
};

const uniqBy = ( array, iteratee ) => {
	const seen = new Map();
	return array.filter( ( item ) => {
		const key = iteratee( item );
		if ( ! seen.has( key ) ) {
			seen.set( key, item );
			return true;
		}
		return false;
	} );
};

/**
 * Get a promise that resolves to a list of products from the Store API.
 *
 * @param {Object}                     request           A query object with the list of selected products and search term.
 * @param {number[]}                   request.selected  Currently selected products.
 * @param {string=}                    request.search    Search string.
 * @param {(Record<string, unknown>)=} request.queryArgs Query args to pass in.
 * @return {Promise<unknown>} Promise resolving to a Product list.
 * @throws Exception if there is an error.
 */
export const getProducts = ( {
	selected = [],
	search = '',
	queryArgs = {},
} ) => {
	const requests = getProductsRequests( { selected, search, queryArgs } );

	return Promise.all( requests.map( ( path ) => apiFetch( { path } ) ) )
		.then( ( data ) => {
			const flatData = data.flat();
			const products = uniqBy( flatData, ( item ) => item.id );
			const list = products.map( ( product ) => ( {
				...product,
				parent: 0,
			} ) );
			return list;
		} )
		.catch( ( e ) => {
			throw e;
		} );
};

/**
 * Get a promise that resolves to a product object from the Store API.
 *
 * @param {number} productId Id of the product to retrieve.
 */
export const getProduct = ( productId ) => {
	return apiFetch( {
		path: `/wc/store/v1/products/${ productId }`,
	} );
};

/**
 * Get a promise that resolves to a list of attribute objects from the Store API.
 */
export const getAttributes = () => {
	return apiFetch( {
		path: `wc/store/v1/products/attributes`,
	} );
};

/**
 * Get a promise that resolves to a list of attribute term objects from the Store API.
 *
 * @param {number} attribute Id of the attribute to retrieve terms for.
 */
export const getTerms = ( attribute ) => {
	return apiFetch( {
		path: `wc/store/v1/products/attributes/${ attribute }/terms`,
	} );
};

/**
 * Get product tag query requests for the Store API.
 *
 * @param {Object} request          A query object with the list of selected products and search term.
 * @param {Array}  request.selected Currently selected tags.
 * @param {string} request.search   Search string.
 */
const getProductTagsRequests = ( { selected = [], search } ) => {
	const limitTags = getSetting( 'limitTags', false );
	const requests = [
		addQueryArgs( `wc/store/v1/products/tags`, {
			per_page: limitTags ? 100 : 0,
			orderby: limitTags ? 'count' : 'name',
			order: limitTags ? 'desc' : 'asc',
			search,
		} ),
	];

	// If we have a large catalog, we might not get all selected products in the first page.
	if ( limitTags && selected.length ) {
		requests.push(
			addQueryArgs( `wc/store/v1/products/tags`, {
				include: selected,
			} )
		);
	}

	return requests;
};

/**
 * Get a promise that resolves to a list of tags from the Store API.
 *
 * @param {Object} props          A query object with the list of selected products and search term.
 * @param {Array}  props.selected
 * @param {string} props.search
 */
export const getProductTags = ( { selected = [], search } ) => {
	const requests = getProductTagsRequests( { selected, search } );

	return Promise.all( requests.map( ( path ) => apiFetch( { path } ) ) ).then(
		( data ) => {
			const flatData = data.flat();
			return uniqBy( flatData, ( item ) => item.id );
		}
	);
};

/**
 * Get a promise that resolves to a list of category objects from the Store API.
 *
 * @param {Object} queryArgs Query args to pass in.
 */
export const getCategories = ( queryArgs ) => {
	return apiFetch( {
		path: addQueryArgs( `wc/store/v1/products/categories`, {
			per_page: 0,
			...queryArgs,
		} ),
	} );
};

/**
 * Get a promise that resolves to a category object from the API.
 *
 * @param {number} categoryId Id of the product to retrieve.
 */
export const getCategory = ( categoryId ) => {
	return apiFetch( {
		path: `wc/store/v1/products/categories/${ categoryId }`,
	} );
};

/**
 * Get a promise that resolves to a list of variation objects from the Store API.
 *
 * @param {number} product Product ID.
 */
export const getProductVariations = ( product ) => {
	return apiFetch( {
		path: addQueryArgs( `wc/store/v1/products`, {
			per_page: 0,
			type: 'variation',
			parent: product,
		} ),
	} );
};

/**
 * Given a page object and an array of page, format the title.
 *
 * @param {Object} page           Page object.
 * @param {Object} page.title     Page title object.
 * @param {string} page.title.raw Page title.
 * @param {string} page.slug      Page slug.
 * @param {Array}  pages          Array of all pages.
 * @return {string}                Formatted page title to display.
 */
export const formatTitle = ( page, pages ) => {
	if ( ! page.title.raw ) {
		return page.slug;
	}
	const isUnique =
		pages.filter( ( p ) => p.title.raw === page.title.raw ).length === 1;
	return page.title.raw + ( ! isUnique ? ` - ${ page.slug }` : '' );
};

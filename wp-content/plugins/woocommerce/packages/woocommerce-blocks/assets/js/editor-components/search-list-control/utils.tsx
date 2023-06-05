/**
 * External dependencies
 */
import { groupBy, keyBy } from 'lodash';
import { Fragment } from '@wordpress/element';
import { __, _n, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import type { SearchListItem } from './types';

export const defaultMessages = {
	clear: __( 'Clear all selected items', 'woo-gutenberg-products-block' ),
	noItems: __( 'No items found.', 'woo-gutenberg-products-block' ),
	/* Translators: %s search term */
	noResults: __( 'No results for %s', 'woo-gutenberg-products-block' ),
	search: __( 'Search for items', 'woo-gutenberg-products-block' ),
	selected: ( n: number ): string =>
		sprintf(
			/* translators: Number of items selected from list. */
			_n(
				'%d item selected',
				'%d items selected',
				n,
				'woo-gutenberg-products-block'
			),
			n
		),
	updated: __( 'Search results updated.', 'woo-gutenberg-products-block' ),
};

/**
 * Returns terms in a tree form.
 *
 * @param {Array} filteredList Array of terms, possibly a subset of all terms, in flat format.
 * @param {Array} list         Array of the full list of terms, defaults to the filteredList.
 *
 * @return {Array} Array of terms in tree format.
 */
export const buildTermsTree = (
	filteredList: SearchListItem[],
	list = filteredList
): SearchListItem[] | [] => {
	const termsByParent = groupBy( filteredList, 'parent' );
	const listById = keyBy( list, 'id' );
	const builtParents = [ '0' ];

	const getParentsName = ( term = {} as SearchListItem ): string[] => {
		if ( ! term.parent ) {
			return term.name ? [ term.name ] : [];
		}

		const parentName = getParentsName( listById[ term.parent ] );
		return [ ...parentName, term.name ];
	};

	const fillWithChildren = ( terms: SearchListItem[] ): SearchListItem[] => {
		return terms.map( ( term ) => {
			const children = termsByParent[ term.id ];
			builtParents.push( '' + term.id );
			return {
				...term,
				breadcrumbs: getParentsName( listById[ term.parent ] ),
				children:
					children && children.length
						? fillWithChildren( children )
						: [],
			};
		} );
	};

	const tree = fillWithChildren( termsByParent[ '0' ] || [] );

	// Handle remaining items in termsByParent that have not been built (orphaned).
	Object.entries( termsByParent ).forEach( ( [ termId, terms ] ) => {
		if ( ! builtParents.includes( termId ) ) {
			tree.push( ...fillWithChildren( terms || [] ) );
		}
	} );

	return tree;
};

export const getFilteredList = (
	list: SearchListItem[],
	search: string,
	isHierarchical?: boolean | undefined
) => {
	if ( ! search ) {
		return isHierarchical ? buildTermsTree( list ) : list;
	}
	const re = new RegExp(
		search.replace( /[-\/\\^$*+?.()|[\]{}]/g, '\\$&' ),
		'i'
	);
	const filteredList = list
		.map( ( item ) => ( re.test( item.name ) ? item : false ) )
		.filter( Boolean ) as SearchListItem[];

	return isHierarchical ? buildTermsTree( filteredList, list ) : filteredList;
};

export const getHighlightedName = (
	name: string,
	search: string
): ( JSX.Element | string )[] | string => {
	if ( ! search ) {
		return name;
	}
	const re = new RegExp(
		// Escaping.
		`(${ search.replace( /[-\/\\^$*+?.()|[\]{}]/g, '\\$&' ) })`,
		'ig'
	);
	const nameParts = name.split( re );

	return nameParts.map( ( part, i ) => {
		return re.test( part ) ? (
			<strong key={ i }>{ part }</strong>
		) : (
			<Fragment key={ i }>{ part }</Fragment>
		);
	} );
};

export const getBreadcrumbsForDisplay = ( breadcrumbs: string[] ): string => {
	if ( breadcrumbs.length === 1 ) {
		return breadcrumbs.slice( 0, 1 ).toString();
	}
	if ( breadcrumbs.length === 2 ) {
		return (
			breadcrumbs.slice( 0, 1 ).toString() +
			' › ' +
			breadcrumbs.slice( -1 ).toString()
		);
	}
	return (
		breadcrumbs.slice( 0, 1 ).toString() +
		' … ' +
		breadcrumbs.slice( -1 ).toString()
	);
};

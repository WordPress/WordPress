/**
 * External dependencies
 */
import { useSelect } from '@wordpress/data';
import { store as WP_BLOCKS_STORE } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { QUERY_LOOP_ID } from './constants';
import {
	ProductQueryBlock,
	ProductQueryBlockQuery,
	QueryVariation,
} from './types';

/**
 * Creates an array that is the symmetric difference of the given arrays
 */
export function ArrayXOR< T extends Array< unknown > >( a: T, b: T ) {
	return a.filter( ( el ) => ! b.includes( el ) );
}

/**
 * Identifies if a block is a Query block variation from our conventions
 *
 * We are extending Gutenberg's core Query block with our variations, and
 * also adding extra namespaced attributes. If those namespaced attributes
 * are present, we can be fairly sure it is our own registered variation.
 */
export function isWooQueryBlockVariation( block: ProductQueryBlock ) {
	return (
		block.name === QUERY_LOOP_ID &&
		Object.values( QueryVariation ).includes(
			block.attributes.namespace as QueryVariation
		)
	);
}

/**
 * Sets the new query arguments of a Product Query block
 *
 * Shorthand for setting new nested query parameters.
 */
export function setQueryAttribute(
	block: ProductQueryBlock,
	queryParams: Partial< ProductQueryBlockQuery >
) {
	const { query } = block.attributes;

	block.setAttributes( {
		query: {
			...query,
			...queryParams,
		},
	} );
}

// This is a feature flag to enable the custom inherit Global Query implementation.
// This is not intended to be a permanent feature flag, but rather a temporary.
// https://github.com/woocommerce/woocommerce-blocks/pull/7382
export const isCustomInheritGlobalQueryImplementationEnabled = false;

export function isWooInheritQueryEnabled(
	attributes: ProductQueryBlock[ 'attributes' ]
) {
	return isCustomInheritGlobalQueryImplementationEnabled
		? attributes.query.__woocommerceInherit
		: attributes.query.inherit;
}

/**
 * Hook that returns the query properties' names defined by the active
 * block variation, to determine which block inspector controls to show.
 *
 * @param {Object} attributes Block attributes.
 * @return {string[]} An array of the controls keys.
 */
export function useAllowedControls(
	attributes: ProductQueryBlock[ 'attributes' ]
) {
	const isSiteEditor = useSelect( 'core/edit-site' ) !== undefined;

	const controls = useSelect(
		( select ) =>
			select( WP_BLOCKS_STORE ).getActiveBlockVariation(
				QUERY_LOOP_ID,
				attributes
			)?.allowedControls,
		[ attributes ]
	);

	if ( ! isSiteEditor ) {
		return controls.filter( ( control ) => control !== 'wooInherit' );
	}

	return isWooInheritQueryEnabled( attributes )
		? controls.filter( ( control ) => control === 'wooInherit' )
		: controls;
}

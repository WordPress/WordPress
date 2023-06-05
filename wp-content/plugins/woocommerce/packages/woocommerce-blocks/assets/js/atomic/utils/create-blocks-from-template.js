/**
 * External dependencies
 */
import { createBlock } from '@wordpress/blocks';

/**
 * Creates blocks for a given inner blocks Template.
 *
 * @param {Array} template Inner Blocks Template.
 */
export const createBlocksFromTemplate = ( template ) => {
	return template.map( ( [ name, atts = {}, innerBlocks = [] ] ) => {
		const children = innerBlocks
			? createBlocksFromTemplate( innerBlocks )
			: [];
		return createBlock( name, atts, children );
	} );
};

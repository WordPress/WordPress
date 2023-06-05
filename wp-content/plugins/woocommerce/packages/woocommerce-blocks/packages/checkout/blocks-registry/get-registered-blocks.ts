/**
 * Internal dependencies
 */
import { innerBlockAreas, RegisteredBlock } from './types';
import { registeredBlocks } from './registered-blocks';

/**
 * Check if a block/area supports inner block registration.
 */
export const hasInnerBlocks = ( block: string ): block is innerBlockAreas => {
	return Object.values( innerBlockAreas ).includes(
		block as innerBlockAreas
	);
};

/**
 * Returns an array of registered block objects available within a specific parent block/area.
 */
export const getRegisteredBlocks = (
	block: string
): Array< RegisteredBlock > => {
	return hasInnerBlocks( block )
		? Object.values( registeredBlocks ).filter( ( { metadata } ) =>
				( metadata?.parent || [] ).includes( block )
		  )
		: [];
};

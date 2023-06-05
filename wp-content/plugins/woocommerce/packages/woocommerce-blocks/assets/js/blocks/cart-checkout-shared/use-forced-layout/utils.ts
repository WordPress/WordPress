/**
 * External dependencies
 */
import type { BlockInstance, TemplateArray } from '@wordpress/blocks';
import type { MutableRefObject } from 'react';

/**
 * Internal dependencies
 */
import { LockableBlock } from './types';

export const isBlockLocked = ( {
	attributes,
}: {
	attributes: LockableBlock[ 'attributes' ];
} ) => Boolean( attributes.lock?.remove || attributes.lock?.default?.remove );

/**
 * This hook is used to determine which blocks are missing from a block. Given the list of inner blocks of a block, we
 * can check for any registered blocks that:
 * a) Are locked,
 * b) Have the parent set as the current block, and
 * c) Are not present in the list of inner blocks.
 */
export const getMissingBlocks = (
	innerBlocks: BlockInstance[],
	registeredBlockTypes: ( LockableBlock | undefined )[]
) => {
	const lockedBlockTypes = registeredBlockTypes.filter(
		( block: LockableBlock | undefined ) => block && isBlockLocked( block )
	);
	const missingBlocks: LockableBlock[] = [];
	lockedBlockTypes.forEach( ( lockedBlock ) => {
		if ( typeof lockedBlock === 'undefined' ) {
			return;
		}
		const existingBlock = innerBlocks.find(
			( block ) => block.name === lockedBlock.name
		);

		if ( ! existingBlock ) {
			missingBlocks.push( lockedBlock );
		}
	} );
	return missingBlocks;
};

/**
 * This hook is used to determine the position that a missing block should be inserted at.
 *
 * @return The index to insert the missing block at.
 */
export const findBlockPosition = ( {
	defaultTemplatePosition,
	innerBlocks,
	currentDefaultTemplate,
}: {
	defaultTemplatePosition: number;
	innerBlocks: BlockInstance[];
	currentDefaultTemplate: MutableRefObject< TemplateArray >;
} ) => {
	switch ( defaultTemplatePosition ) {
		case -1:
			// The block is not part of the default template, so we append it to the current layout.
			return innerBlocks.length;
		// defaultTemplatePosition defaults to 0, so if this happens we can just return, this is because the block was
		// the first block in the default layout, so we can prepend it to the current layout.
		case 0:
			return 0;
		default:
			// The new layout may have extra blocks compared to the default template, so rather than insert
			// at the default position, we should append it after another default block.
			const adjacentBlock =
				currentDefaultTemplate.current[ defaultTemplatePosition - 1 ];
			const position = innerBlocks.findIndex(
				( { name: blockName } ) => blockName === adjacentBlock[ 0 ]
			);
			return position === -1 ? defaultTemplatePosition : position + 1;
	}
};

/**
 * External dependencies
 */
import { getRegisteredBlockComponents } from '@woocommerce/blocks-registry';
import type { RegisteredBlockComponent } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import '../blocks/component-init';

/**
 * Map named Blocks to defined React Components to render on the frontend.
 *
 * @param {string} blockName Name of the parent block.
 */
export const getBlockMap = (
	blockName: string
): Record< string, RegisteredBlockComponent > =>
	getRegisteredBlockComponents( blockName );

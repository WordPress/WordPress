/**
 * External dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { BLOCK_ICON } from './constants';
import metadata from './block.json';
import edit from './edit';
import save from './save';

// @ts-expect-error: `registerBlockType` is a function that is typed in WordPress core.
registerBlockType( metadata, {
	icon: BLOCK_ICON,
	edit,
	save,
} );

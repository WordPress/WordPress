/**
 * External dependencies
 */
import { box as icon } from '@wordpress/icons';
import { registerBlockSingleProductTemplate } from '@woocommerce/atomic-utils';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import metadata from './block.json';

registerBlockSingleProductTemplate( {
	blockName: metadata.name,
	// @ts-expect-error: `metadata` currently does not have a type definition in WordPress core
	blockMetadata: metadata,
	blockSettings: {
		icon,
		edit,
		save,
		ancestor: [ 'woocommerce/single-product' ],
	},
} );

/**
 * External dependencies
 */
import { registerBlockSingleProductTemplate } from '@woocommerce/atomic-utils';
import { WC_BLOCKS_IMAGE_URL } from '@woocommerce/block-settings';

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
		edit,
		save,
		icon: {
			src: () => (
				<img
					src={ `${ WC_BLOCKS_IMAGE_URL }/blocks/product-meta/product-meta-icon.svg` }
					alt=""
				/>
			),
		},
		ancestor: [ 'woocommerce/single-product' ],
	},
} );

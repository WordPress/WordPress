/**
 * External dependencies
 */
import { registerBlockSingleProductTemplate } from '@woocommerce/atomic-utils';
import { WC_BLOCKS_IMAGE_URL } from '@woocommerce/block-settings';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import edit from './edit';

registerBlockSingleProductTemplate( {
	blockName: metadata.name,
	// @ts-expect-error: `metadata` currently does not have a type definition in WordPress core
	blockMetadata: metadata,
	blockSettings: {
		icon: {
			src: () => {
				return (
					<>
						<img
							src={ `${ WC_BLOCKS_IMAGE_URL }/blocks/product-details/product-details-icon.svg` }
							alt=""
						/>
					</>
				);
			},
		},
		edit,
		ancestor: [ 'woocommerce/single-product' ],
	},
} );

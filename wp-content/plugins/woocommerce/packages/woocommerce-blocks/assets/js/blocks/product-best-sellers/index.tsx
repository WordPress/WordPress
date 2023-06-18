/**
 * External dependencies
 */
import { Icon, trendingUp } from '@wordpress/icons';
import { createBlock, registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import { Edit } from './edit';
import sharedAttributes, {
	sharedAttributeBlockTypes,
} from '../../utils/shared-attributes';

registerBlockType( metadata, {
	icon: {
		src: (
			<Icon
				icon={ trendingUp }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	attributes: {
		...sharedAttributes,
		...metadata.attributes,
	},

	transforms: {
		from: [
			{
				type: 'block',
				blocks: sharedAttributeBlockTypes.filter(
					( value ) => value !== 'woocommerce/product-best-sellers'
				),
				transform: ( attributes ) =>
					createBlock(
						'woocommerce/product-best-sellers',
						attributes
					),
			},
		],
	},

	/**
	 * Renders and manages the block.
	 *
	 * @param {Object} props Props to pass to block.
	 */
	edit: Edit,

	save: () => {
		return null;
	},
} );

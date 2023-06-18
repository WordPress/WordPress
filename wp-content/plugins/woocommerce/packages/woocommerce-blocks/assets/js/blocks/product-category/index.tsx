/**
 * External dependencies
 */
import { createBlock, registerBlockType } from '@wordpress/blocks';
import { Icon, file } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import './editor.scss';
import metadata from './block.json';
import sharedAttributes, {
	sharedAttributeBlockTypes,
} from '../../utils/shared-attributes';
import { Edit } from './edit';

/**
 * Register and run the "Products by Category" block.
 */
registerBlockType( metadata, {
	icon: {
		src: (
			<Icon
				icon={ file }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	attributes: {
		...metadata.attributes,
		...sharedAttributes,
	},

	transforms: {
		from: [
			{
				type: 'block',
				blocks: sharedAttributeBlockTypes.filter(
					( value ) => value !== 'woocommerce/product-category'
				),
				transform: ( attributes ) =>
					createBlock( 'woocommerce/product-category', {
						...attributes,
						editMode: false,
					} ),
			},
		],
	},

	edit: Edit,

	save: () => {
		return null;
	},
} );

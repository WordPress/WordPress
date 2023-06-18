/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { createBlock, registerBlockType } from '@wordpress/blocks';
import { Icon, sparkles } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import Block from './block';
import sharedAttributes, {
	sharedAttributeBlockTypes,
} from '../../utils/shared-attributes';

registerBlockType( 'woocommerce/product-new', {
	title: __( 'Newest Products', 'woocommerce' ),
	icon: {
		src: (
			<Icon
				icon={ sparkles }
				className="wc-block-editor-components-block-icon wc-block-editor-components-block-icon--sparkles"
			/>
		),
	},
	category: 'woocommerce',
	keywords: [ __( 'WooCommerce', 'woocommerce' ) ],
	description: __(
		'Display a grid of your newest products.',
		'woocommerce'
	),
	supports: {
		align: [ 'wide', 'full' ],
		html: false,
	},
	attributes: {
		...sharedAttributes,
	},
	transforms: {
		from: [
			{
				type: 'block',
				blocks: sharedAttributeBlockTypes.filter(
					( value ) => value !== 'woocommerce/product-new'
				),
				transform: ( attributes ) =>
					createBlock( 'woocommerce/product-new', attributes ),
			},
		],
	},

	/**
	 * Renders and manages the block.
	 *
	 * @param {Object} props Props to pass to block.
	 */
	edit( props ) {
		return <Block { ...props } />;
	},

	save() {
		return null;
	},
} );

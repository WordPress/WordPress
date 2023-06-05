/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { createBlock, registerBlockType } from '@wordpress/blocks';
import { without } from 'lodash';
import { Icon, percent } from '@wordpress/icons';
/**
 * Internal dependencies
 */
import Block from './block';
import './editor.scss';
import sharedAttributes, {
	sharedAttributeBlockTypes,
} from '../../utils/shared-attributes';

registerBlockType( 'woocommerce/product-on-sale', {
	title: __( 'On Sale Products', 'woocommerce' ),
	icon: {
		src: (
			<Icon
				icon={ percent }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	category: 'woocommerce',
	keywords: [ __( 'WooCommerce', 'woocommerce' ) ],
	description: __(
		'Display a grid of products currently on sale.',
		'woocommerce'
	),
	supports: {
		align: [ 'wide', 'full' ],
		html: false,
	},
	attributes: {
		...sharedAttributes,

		/**
		 * How to order the products: 'date', 'popularity', 'price_asc', 'price_desc' 'rating', 'title'.
		 */
		orderby: {
			type: 'string',
			default: 'date',
		},
	},
	transforms: {
		from: [
			{
				type: 'block',
				blocks: without(
					sharedAttributeBlockTypes,
					'woocommerce/product-on-sale'
				),
				transform: ( attributes ) =>
					createBlock( 'woocommerce/product-on-sale', attributes ),
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

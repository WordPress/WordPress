/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { Icon, commentContent } from '@wordpress/icons';
/**
 * Internal dependencies
 */
import '../editor.scss';
import Editor from './edit';
import sharedAttributes from '../attributes';
import save from '../save.js';
import { example } from '../example';

/**
 * Register and run the "Reviews by Product" block.
 */
registerBlockType( 'woocommerce/reviews-by-product', {
	apiVersion: 2,
	title: __( 'Reviews by Product', 'woocommerce' ),
	icon: {
		src: (
			<Icon
				icon={ commentContent }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	category: 'woocommerce',
	keywords: [ __( 'WooCommerce', 'woocommerce' ) ],
	description: __(
		'Display reviews for your products.',
		'woocommerce'
	),
	supports: {
		html: false,
		color: {
			background: false,
		},
		typography: {
			fontSize: true,
		},
	},
	example: {
		...example,
		attributes: {
			...example.attributes,
			productId: 1,
		},
	},
	attributes: {
		...sharedAttributes,
		/**
		 * The id of the product to load reviews for.
		 */
		productId: {
			type: 'number',
		},
	},

	/**
	 * Renders and manages the block.
	 *
	 * @param {Object} props Props to pass to block.
	 */
	edit( props ) {
		return <Editor { ...props } />;
	},

	/**
	 * Save the props to post content.
	 */
	save,
} );

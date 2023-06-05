/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { Icon, commentContent } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import Editor from './edit';
import sharedAttributes from '../attributes';
import save from '../save.js';
import { example } from '../example';

/**
 * Register and run the "Reviews by category" block.
 */
registerBlockType( 'woocommerce/reviews-by-category', {
	apiVersion: 2,
	title: __( 'Reviews by Category', 'woocommerce' ),
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
		'Show product reviews from specific categories.',
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
			categoryIds: [ 1 ],
			showProductName: true,
		},
	},
	attributes: {
		...sharedAttributes,
		/**
		 * The ids of the categories to load reviews for.
		 */
		categoryIds: {
			type: 'array',
			default: [],
		},
		/**
		 * Show the product name.
		 */
		showProductName: {
			type: 'boolean',
			default: true,
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

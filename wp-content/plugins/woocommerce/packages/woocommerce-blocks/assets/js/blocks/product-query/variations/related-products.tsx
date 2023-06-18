/**
 * External dependencies
 */
import { BlockAttributes, InnerBlockTemplate } from '@wordpress/blocks';
import { Icon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { stacks } from '@woocommerce/icons';
import { registerBlockSingleProductTemplate } from '@woocommerce/atomic-utils';

/**
 * Internal dependencies
 */
import { QUERY_LOOP_ID } from '../constants';

import { VARIATION_NAME as PRODUCT_TEMPLATE_ID } from './elements/product-template';
import { VARIATION_NAME as PRODUCT_TITLE_ID } from './elements/product-title';

const VARIATION_NAME = 'woocommerce/related-products';

export const BLOCK_ATTRIBUTES = {
	namespace: VARIATION_NAME,
	allowedControls: [],
	displayLayout: {
		type: 'flex',
		columns: 5,
	},
	query: {
		perPage: 5,
		pages: 0,
		offset: 0,
		postType: 'product',
		order: 'asc',
		orderBy: 'title',
		author: '',
		search: '',
		exclude: [],
		sticky: '',
		inherit: false,
	},
	lock: {
		remove: true,
		move: true,
	},
};

export const INNER_BLOCKS_TEMPLATE: InnerBlockTemplate[] = [
	[
		'core/heading',
		{
			level: 2,
			content: __( 'Related products', 'woo-gutenberg-products-block' ),
		},
	],
	[
		'core/post-template',
		{ __woocommerceNamespace: PRODUCT_TEMPLATE_ID },
		[
			[
				'woocommerce/product-image',
				{
					productId: 0,
					imageSizing: 'cropped',
				},
			],
			[
				'core/post-title',
				{
					textAlign: 'center',
					level: 3,
					fontSize: 'medium',
					isLink: true,
					__woocommerceNamespace: PRODUCT_TITLE_ID,
				},
				[],
			],
			[
				'woocommerce/product-price',
				{
					textAlign: 'center',
					fontSize: 'small',
					style: {
						spacing: {
							margin: { bottom: '1rem' },
						},
					},
				},
				[],
			],
			[
				'woocommerce/product-button',
				{
					textAlign: 'center',
					fontSize: 'small',
					style: {
						spacing: {
							margin: { bottom: '1rem' },
						},
					},
				},
				[],
			],
		],
	],
];

registerBlockSingleProductTemplate( {
	blockName: QUERY_LOOP_ID,
	blockMetadata: {},
	blockSettings: {
		description: __(
			'Display related products.',
			'woo-gutenberg-products-block'
		),
		name: 'Related Products Controls',
		title: __(
			'Related Products Controls',
			'woo-gutenberg-products-block'
		),
		// @ts-expect-error: `isActive` exists on Block Variation configuration
		isActive: ( blockAttributes: BlockAttributes ) =>
			blockAttributes.namespace === VARIATION_NAME,
		icon: (
			<Icon
				icon={ stacks }
				className="wc-block-editor-components-block-icon wc-block-editor-components-block-icon--stacks"
			/>
		),
		attributes: BLOCK_ATTRIBUTES,
		// Gutenberg doesn't support this type yet, discussion here:
		// https://github.com/WordPress/gutenberg/pull/43632
		// eslint-disable-next-line @typescript-eslint/ban-ts-comment
		// @ts-ignore
		allowedControls: [],
		innerBlocks: INNER_BLOCKS_TEMPLATE,
		scope: [ 'block' ],
	},
	isVariationBlock: true,
	variationName: VARIATION_NAME,
} );

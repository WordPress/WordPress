/**
 * External dependencies
 */
import { Icon, mediaAndText } from '@wordpress/icons';
import { getBlockMap } from '@woocommerce/atomic-utils';
import type { InnerBlockTemplate } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import { VARIATION_NAME as PRODUCT_TITLE_VARIATION_NAME } from '../product-query/variations/elements/product-title';
import { VARIATION_NAME as PRODUCT_SUMMARY_VARIATION_NAME } from '../product-query/variations/elements/product-summary';
import { ImageSizing } from '../../atomic/blocks/product-elements/image/types';

export const BLOCK_ICON = (
	<Icon
		icon={ mediaAndText }
		className="wc-block-editor-components-block-icon"
	/>
);

export const DEFAULT_INNER_BLOCKS: InnerBlockTemplate[] = [
	[
		'core/columns',
		{},
		[
			[
				'core/column',
				{},
				[
					[
						'woocommerce/product-image',
						{
							showSaleBadge: false,
							isDescendentOfSingleProductBlock: true,
							imageSizing: ImageSizing.SINGLE,
						},
					],
				],
			],
			[
				'core/column',
				{},
				[
					[
						'core/post-title',
						{
							headingLevel: 2,
							isLink: true,
							__woocommerceNamespace:
								PRODUCT_TITLE_VARIATION_NAME,
						},
					],
					[
						'woocommerce/product-rating',
						{ isDescendentOfSingleProductBlock: true },
					],
					[
						'woocommerce/product-price',
						{ isDescendentOfSingleProductBlock: true },
					],
					[
						'core/post-excerpt',
						{
							__woocommerceNamespace:
								PRODUCT_SUMMARY_VARIATION_NAME,
						},
					],
					[ 'woocommerce/add-to-cart-form' ],
					[ 'woocommerce/product-meta' ],
				],
			],
		],
	],
];

export const ALLOWED_INNER_BLOCKS = [
	'core/columns',
	'core/column',
	...Object.keys( getBlockMap( metadata.name ) ),
];

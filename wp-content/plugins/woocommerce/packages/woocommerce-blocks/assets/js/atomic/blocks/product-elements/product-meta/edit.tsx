/**
 * External dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { InnerBlockTemplate } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import './editor.scss';

const Edit = () => {
	const TEMPLATE: InnerBlockTemplate[] = [
		[
			'core/group',
			{ layout: { type: 'flex', flexWrap: 'nowrap' } },
			[
				[
					'woocommerce/product-sku',
					{
						isDescendentOfSingleProductTemplate: true,
					},
				],
				[
					'core/post-terms',
					{
						prefix: 'Category: ',
						term: 'product_cat',
					},
				],
				[
					'core/post-terms',
					{
						prefix: 'Tags: ',
						term: 'product_tag',
					},
				],
			],
		],
	];
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<InnerBlocks template={ TEMPLATE } />
		</div>
	);
};

export default Edit;

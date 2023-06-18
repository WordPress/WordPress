/**
 * External dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { InnerBlockTemplate } from '@wordpress/blocks';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import './editor.scss';

const Edit = () => {
	const isDescendentOfSingleProductTemplate = useSelect( ( select ) => {
		const store = select( 'core/edit-site' );
		const postId = store?.getEditedPostId< string | undefined >();

		return postId?.includes( '//single-product' );
	}, [] );

	const TEMPLATE: InnerBlockTemplate[] = [
		[
			'core/group',
			{ layout: { type: 'flex', flexWrap: 'nowrap' } },
			[
				[
					'woocommerce/product-sku',
					{
						isDescendentOfSingleProductTemplate,
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

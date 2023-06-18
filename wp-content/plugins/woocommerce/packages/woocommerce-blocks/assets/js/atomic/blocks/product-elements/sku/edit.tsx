/**
 * External dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import type { BlockEditProps } from '@wordpress/blocks';
import EditProductLink from '@woocommerce/editor-components/edit-product-link';
import { ProductQueryContext as Context } from '@woocommerce/blocks/product-query/types';
import { useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import Block from './block';
import type { Attributes } from './types';

const Edit = ( {
	attributes,
	setAttributes,
	context,
}: BlockEditProps< Attributes > & { context: Context } ): JSX.Element => {
	const { style, ...blockProps } = useBlockProps( {
		className:
			'wc-block-components-product-sku wp-block-woocommerce-product-sku',
	} );
	const blockAttrs = {
		...attributes,
		...context,
	};
	const isDescendentOfQueryLoop = Number.isFinite( context.queryId );

	const isDescendentOfSingleProductTemplate = useSelect(
		( select ) => {
			const store = select( 'core/edit-site' );
			const postId = store?.getEditedPostId< string | undefined >();

			if ( ! postId ) {
				return false;
			}

			return (
				postId.includes( '//single-product' ) &&
				! isDescendentOfQueryLoop
			);
		},
		[ isDescendentOfQueryLoop ]
	);

	useEffect(
		() =>
			setAttributes( {
				isDescendentOfQueryLoop,
				isDescendentOfSingleProductTemplate,
			} ),
		[
			setAttributes,
			isDescendentOfQueryLoop,
			isDescendentOfSingleProductTemplate,
		]
	);

	return (
		<>
			<EditProductLink />
			<div
				{ ...blockProps }
				/**
				 * If block is decendant of the All Products block, we don't want to
				 * apply style here because it will be applied inside Block using
				 * useColors, useTypography, and useSpacing hooks.
				 */
				style={
					attributes.isDescendantOfAllProducts ? undefined : style
				}
			>
				<Block { ...blockAttrs } />
			</div>
		</>
	);
};

export default Edit;

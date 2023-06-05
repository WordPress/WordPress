/**
 * External dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import type { TemplateArray } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { getMiniCartAllowedBlocks } from '../allowed-blocks';

export const Edit = (): JSX.Element => {
	const blockProps = useBlockProps( {
		className: 'wc-block-mini-cart__items',
	} );

	const defaultTemplate = [
		[ 'woocommerce/mini-cart-products-table-block', {} ],
	].filter( Boolean ) as unknown as TemplateArray;

	return (
		<div { ...blockProps }>
			<InnerBlocks
				template={ defaultTemplate }
				renderAppender={ InnerBlocks.ButtonBlockAppender }
				templateLock={ false }
				allowedBlocks={ getMiniCartAllowedBlocks() }
			/>
		</div>
	);
};

export const Save = (): JSX.Element => {
	return (
		<div { ...useBlockProps.save() }>
			<InnerBlocks.Content />
		</div>
	);
};

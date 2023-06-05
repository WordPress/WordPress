/**
 * External dependencies
 */
import type { TemplateArray } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export const Edit = (): JSX.Element => {
	const blockProps = useBlockProps( {
		className: 'wc-block-cart__cross-sells',
	} );
	const defaultTemplate = [
		[
			'core/heading',
			{
				content: __(
					'You may be interested in…',
					'woo-gutenberg-products-block'
				),
				level: 2,
				fontSize: 'large',
			},
			[],
		],
		[ 'woocommerce/cart-cross-sells-products-block', {}, [] ],
	] as TemplateArray;

	return (
		<div { ...blockProps }>
			<InnerBlocks template={ defaultTemplate } templateLock={ false } />
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

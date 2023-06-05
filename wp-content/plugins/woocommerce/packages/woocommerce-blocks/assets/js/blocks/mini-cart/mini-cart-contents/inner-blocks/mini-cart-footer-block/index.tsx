/**
 * External dependencies
 */
import { Icon, payment } from '@wordpress/icons';
import { createBlock, registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { Edit, Save } from './edit';
import deprecatedAttributes from './attributes';

// eslint-disable-next-line @typescript-eslint/ban-ts-comment
// @ts-ignore -- TypeScript expects some required properties which we already
// registered in PHP.
registerBlockType( 'woocommerce/mini-cart-footer-block', {
	icon: {
		src: (
			<Icon
				icon={ payment }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	deprecated: [
		{
			attributes: deprecatedAttributes,

			migrate( attributes, innerBlocks ) {
				const {
					cartButtonLabel,
					checkoutButtonLabel,
					...restAttributes
				} = attributes;

				return [
					restAttributes,
					[
						createBlock(
							'woocommerce/mini-cart-cart-button-block',
							{
								cartButtonLabel,
							}
						),
						createBlock(
							'woocommerce/mini-cart-checkout-button-block',
							{
								checkoutButtonLabel,
							}
						),
						...innerBlocks,
					],
				];
			},
			isEligible: ( attributes, innerBlocks ) => {
				return ! innerBlocks.length;
			},
			save: (): JSX.Element => {
				return <div { ...useBlockProps.save() }></div>;
			},
		},
	],
	edit: Edit,
	save: Save,
} );

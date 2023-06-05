/**
 * External dependencies
 */
import classnames from 'classnames';
import { fields } from '@woocommerce/icons';
import { Icon } from '@wordpress/icons';
import { registerBlockType, createBlock } from '@wordpress/blocks';
import type { BlockInstance } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { Edit, Save } from './edit';
import { blockAttributes, deprecatedAttributes } from './attributes';
import './inner-blocks';
import metadata from './block.json';

const settings = {
	icon: {
		src: (
			<Icon
				icon={ fields }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	attributes: {
		...metadata.attributes,
		...blockAttributes,
		...deprecatedAttributes,
	},
	edit: Edit,
	save: Save,
	// Migrates v1 to v2 checkout.
	deprecated: [
		{
			attributes: {
				...metadata.attributes,
				...blockAttributes,
				...deprecatedAttributes,
			},
			save( { attributes }: { attributes: { className: string } } ) {
				return (
					<div
						className={ classnames(
							'is-loading',
							attributes.className
						) }
					/>
				);
			},
			migrate: ( attributes: {
				showOrderNotes: boolean;
				showPolicyLinks: boolean;
				showReturnToCart: boolean;
				cartPageId: number;
			} ) => {
				const {
					showOrderNotes,
					showPolicyLinks,
					showReturnToCart,
					cartPageId,
				} = attributes;
				return [
					attributes,
					[
						createBlock(
							'woocommerce/checkout-fields-block',
							{},
							[
								createBlock(
									'woocommerce/checkout-express-payment-block',
									{},
									[]
								),
								createBlock(
									'woocommerce/checkout-contact-information-block',
									{},
									[]
								),
								createBlock(
									'woocommerce/checkout-shipping-address-block',
									{},
									[]
								),
								createBlock(
									'woocommerce/checkout-billing-address-block',
									{},
									[]
								),
								createBlock(
									'woocommerce/checkout-shipping-methods-block',
									{},
									[]
								),
								createBlock(
									'woocommerce/checkout-payment-block',
									{},
									[]
								),
								showOrderNotes
									? createBlock(
											'woocommerce/checkout-order-note-block',
											{},
											[]
									  )
									: false,
								showPolicyLinks
									? createBlock(
											'woocommerce/checkout-terms-block',
											{},
											[]
									  )
									: false,
								createBlock(
									'woocommerce/checkout-actions-block',
									{
										showReturnToCart,
										cartPageId,
									},
									[]
								),
							].filter( Boolean ) as BlockInstance[]
						),
						createBlock( 'woocommerce/checkout-totals-block', {} ),
					],
				];
			},
			isEligible: (
				attributes: Record< string, unknown >,
				innerBlocks: BlockInstance[]
			) => {
				return ! innerBlocks.some(
					( block: { name: string } ) =>
						block.name === 'woocommerce/checkout-fields-block'
				);
			},
		},
	],
};

registerBlockType( metadata, settings );

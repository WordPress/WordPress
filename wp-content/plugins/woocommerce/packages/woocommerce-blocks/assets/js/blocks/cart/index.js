/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import classnames from 'classnames';
import { InnerBlocks } from '@wordpress/block-editor';
import { cart } from '@woocommerce/icons';
import { Icon } from '@wordpress/icons';
import { registerBlockType, createBlock } from '@wordpress/blocks';
/**
 * Internal dependencies
 */
import { Edit, Save } from './edit';
import './style.scss';
import { blockName, blockAttributes } from './attributes';
import './inner-blocks';

/**
 * Register and run the Cart block.
 */
const settings = {
	title: __( 'Cart', 'woocommerce' ),
	icon: {
		src: (
			<Icon
				icon={ cart }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	category: 'woocommerce',
	keywords: [ __( 'WooCommerce', 'woocommerce' ) ],
	description: __( 'Shopping cart.', 'woocommerce' ),
	supports: {
		align: [ 'wide' ],
		html: false,
		multiple: false,
	},
	example: {
		attributes: {
			isPreview: true,
		},
		viewportWidth: 800,
	},
	attributes: blockAttributes,
	edit: Edit,
	save: Save,
	// Migrates v1 to v2 checkout.
	deprecated: [
		{
			attributes: blockAttributes,
			save: ( { attributes } ) => {
				return (
					<div
						className={ classnames(
							'is-loading',
							attributes.className
						) }
					>
						<InnerBlocks.Content />
					</div>
				);
			},
			migrate: ( attributes, innerBlocks ) => {
				const { checkoutPageId, align } = attributes;
				return [
					attributes,
					[
						createBlock(
							'woocommerce/filled-cart-block',
							{ align },
							[
								createBlock( 'woocommerce/cart-items-block' ),
								createBlock(
									'woocommerce/cart-totals-block',
									{},
									[
										createBlock(
											'woocommerce/cart-order-summary-block',
											{}
										),
										createBlock(
											'woocommerce/cart-express-payment-block'
										),
										createBlock(
											'woocommerce/proceed-to-checkout-block',
											{ checkoutPageId }
										),
										createBlock(
											'woocommerce/cart-accepted-payment-methods-block'
										),
									]
								),
							]
						),
						createBlock(
							'woocommerce/empty-cart-block',
							{ align },
							innerBlocks
						),
					],
				];
			},
			isEligible: ( _, innerBlocks ) => {
				return ! innerBlocks.find(
					( block ) => block.name === 'woocommerce/filled-cart-block'
				);
			},
		},
	],
};

registerBlockType( blockName, settings );

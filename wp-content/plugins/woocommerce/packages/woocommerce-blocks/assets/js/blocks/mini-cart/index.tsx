/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { miniCartAlt } from '@woocommerce/icons';
import { Icon } from '@wordpress/icons';
import { registerBlockType } from '@wordpress/blocks';
import type { BlockConfiguration } from '@wordpress/blocks';
import { isFeaturePluginBuild } from '@woocommerce/block-settings';

/**
 * Internal dependencies
 */
import edit from './edit';

const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Mini Cart', 'woo-gutenberg-products-block' ),
	icon: {
		src: (
			<Icon
				icon={ miniCartAlt }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	category: 'woocommerce',
	keywords: [ __( 'WooCommerce', 'woo-gutenberg-products-block' ) ],
	description: __(
		'Display a mini cart widget.',
		'woo-gutenberg-products-block'
	),
	supports: {
		html: false,
		multiple: false,
		color: true,
		typography: {
			fontSize: true,
			...( isFeaturePluginBuild() && {
				__experimentalFontFamily: true,
				__experimentalFontWeight: true,
			} ),
		},
	},
	example: {
		attributes: {
			isPreview: true,
			className: 'wc-block-mini-cart--preview',
		},
	},
	attributes: {
		isPreview: {
			type: 'boolean',
			default: false,
		},
		addToCartBehaviour: {
			type: 'string',
			default: 'none',
		},
		hasHiddenPrice: {
			type: 'boolean',
			default: false,
		},
		cartAndCheckoutRenderStyle: {
			type: 'string',
			default: 'hidden',
		},
	},
	edit,
	save() {
		return null;
	},
};

registerBlockType( 'woocommerce/mini-cart', settings );

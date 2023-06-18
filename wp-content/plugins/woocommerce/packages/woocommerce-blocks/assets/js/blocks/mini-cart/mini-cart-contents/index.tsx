/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { cart } from '@woocommerce/icons';
import { Icon } from '@wordpress/icons';
import { registerBlockType } from '@wordpress/blocks';
import type { BlockConfiguration } from '@wordpress/blocks';
import { isFeaturePluginBuild } from '@woocommerce/block-settings';

/**
 * Internal dependencies
 */
import edit, { Save as save } from './edit';
import { blockName, attributes } from './attributes';
import './inner-blocks';

const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Mini Cart Contents', 'woo-gutenberg-products-block' ),
	icon: {
		src: (
			<Icon
				icon={ cart }
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
		align: false,
		html: false,
		multiple: false,
		reusable: false,
		inserter: false,
		color: {
			link: true,
		},
		lock: false,
		...( isFeaturePluginBuild() && {
			__experimentalBorder: {
				color: true,
				width: true,
			},
		} ),
	},
	attributes,
	example: {
		attributes: {
			isPreview: true,
		},
	},
	edit,
	save,
};

registerBlockType( blockName, settings );

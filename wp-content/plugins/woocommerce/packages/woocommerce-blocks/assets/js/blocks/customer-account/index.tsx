/**
 * External dependencies
 */
import { registerBlockType, registerBlockVariation } from '@wordpress/blocks';
import { Icon } from '@wordpress/icons';
import { customerAccount } from '@woocommerce/icons';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import edit from './edit';

registerBlockType( metadata, {
	icon: {
		src: (
			<Icon
				icon={ customerAccount }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	attributes: {
		...metadata.attributes,
	},
	edit,
	save() {
		return null;
	},
} );

// We needed to change the size of the icon without affecting already existing blocks.
// This is why we are registering a new variation with a different icon class instead of changing directly the icon
// size in the css. By giving it the same name and making it default we are making sure that new blocks will use the
// new icon size and existing blocks will keep using the old one after updating the plugin.
// For more context, see https://github.com/woocommerce/woocommerce-blocks/pull/8594
registerBlockVariation( 'woocommerce/customer-account', {
	name: 'woocommerce/customer-account',
	title: __( 'Customer account', 'woo-gutenberg-products-block' ),
	isDefault: true,
	attributes: {
		...metadata.attributes,
		displayStyle: 'icon_and_text',
		iconStyle: 'default',
		iconClass: 'wc-block-customer-account__account-icon',
	},
} );

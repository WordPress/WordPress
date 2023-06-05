/**
 * External dependencies
 */
import { Icon, mapMarker } from '@wordpress/icons';
import { registerBlockType } from '@wordpress/blocks';
/**
 * Internal dependencies
 */
import { Edit, Save } from './edit';
import attributes from './attributes';

registerBlockType( 'woocommerce/checkout-billing-address-block', {
	icon: {
		src: (
			<Icon
				icon={ mapMarker }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	attributes,
	edit: Edit,
	save: Save,
} );

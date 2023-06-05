/**
 * External dependencies
 */
import { Icon, atSymbol } from '@wordpress/icons';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { Edit, Save } from './edit';
import attributes from './attributes';

registerBlockType( 'woocommerce/checkout-contact-information-block', {
	icon: {
		src: (
			<Icon
				icon={ atSymbol }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	attributes,
	edit: Edit,
	save: Save,
} );

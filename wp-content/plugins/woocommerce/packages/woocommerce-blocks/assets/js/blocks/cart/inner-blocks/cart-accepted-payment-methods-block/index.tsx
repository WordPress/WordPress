/**
 * External dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { Icon, payment } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { Edit, Save } from './edit';

registerBlockType( 'woocommerce/cart-accepted-payment-methods-block', {
	icon: {
		src: (
			<Icon
				icon={ payment }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	edit: Edit,
	save: Save,
} );

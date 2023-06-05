/**
 * External dependencies
 */
import { Icon, customPostType } from '@wordpress/icons';
import { registerBlockType } from '@wordpress/blocks';
/**
 * Internal dependencies
 */
import { Edit, Save } from './edit';

registerBlockType( 'woocommerce/checkout-terms-block', {
	icon: {
		src: (
			<Icon
				icon={ customPostType }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	edit: Edit,
	save: Save,
} );

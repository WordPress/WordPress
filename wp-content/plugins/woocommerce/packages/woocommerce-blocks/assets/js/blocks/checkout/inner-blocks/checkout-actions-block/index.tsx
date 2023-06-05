/**
 * External dependencies
 */
import { Icon, button } from '@wordpress/icons';
import { registerBlockType } from '@wordpress/blocks';
import type { BlockConfiguration } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import attributes from './attributes';
import { Edit, Save } from './edit';

const blockConfig: BlockConfiguration = {
	icon: {
		src: (
			<Icon
				icon={ button }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	attributes,
	save: Save,
	edit: Edit,
};

registerBlockType( 'woocommerce/checkout-actions-block', blockConfig );

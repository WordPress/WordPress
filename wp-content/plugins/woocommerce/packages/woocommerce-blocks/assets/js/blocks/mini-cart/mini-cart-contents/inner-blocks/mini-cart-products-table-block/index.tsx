/**
 * External dependencies
 */
import { Icon, list } from '@wordpress/icons';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { Edit, Save } from './edit';

// eslint-disable-next-line @typescript-eslint/ban-ts-comment
// @ts-ignore -- TypeScript expects some required properties which we already
// registered in PHP.
registerBlockType( 'woocommerce/mini-cart-products-table-block', {
	icon: (
		<Icon icon={ list } className="wc-block-editor-components-block-icon" />
	),
	edit: Edit,
	save: Save,
} );

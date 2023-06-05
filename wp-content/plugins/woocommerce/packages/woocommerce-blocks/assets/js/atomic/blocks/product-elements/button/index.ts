/**
 * External dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import type { BlockConfiguration } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { supports } from './supports';
import attributes from './attributes';
import sharedConfig from '../shared/config';
import edit from './edit';
import save from './save';
import {
	BLOCK_TITLE as title,
	BLOCK_ICON as icon,
	BLOCK_DESCRIPTION as description,
	BLOCK_NAME,
} from './constants';

const blockConfig: BlockConfiguration = {
	...sharedConfig,
	apiVersion: 2,
	title,
	description,
	ancestor: [
		'woocommerce/all-products',
		'woocommerce/single-product',
		'core/post-template',
	],
	usesContext: [ 'query', 'queryId', 'postId' ],
	icon: { src: icon },
	attributes,
	supports,
	edit,
	save,
	styles: [
		{
			name: 'fill',
			label: __( 'Fill', 'woo-gutenberg-products-block' ),
			isDefault: true,
		},
		{
			name: 'outline',
			label: __( 'Outline', 'woo-gutenberg-products-block' ),
		},
	],
};

registerBlockType( BLOCK_NAME, { ...blockConfig } );

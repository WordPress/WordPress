/**
 * External dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import type { BlockConfiguration } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import edit from './edit';

import { supports } from './supports';
import attributes from './attributes';
import sharedConfig from '../shared/config';
import {
	BLOCK_TITLE as title,
	BLOCK_ICON as icon,
	BLOCK_DESCRIPTION as description,
} from './constants';

const blockConfig: BlockConfiguration = {
	...sharedConfig,
	apiVersion: 2,
	name: 'woocommerce/product-image',
	title,
	icon: { src: icon },
	keywords: [ 'WooCommerce' ],
	description,
	usesContext: [ 'query', 'queryId', 'postId' ],
	ancestor: [
		'woocommerce/all-products',
		'woocommerce/single-product',
		'core/post-template',
	],
	textdomain: 'woo-gutenberg-products-block',
	attributes,
	supports,
	edit,
};

registerBlockType( 'woocommerce/product-image', { ...blockConfig } );

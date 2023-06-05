/**
 * External dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import sharedConfig from '../shared/config';
import edit from './edit';
import attributes from './attributes';
import { supports } from './supports';
import {
	BLOCK_TITLE as title,
	BLOCK_ICON as icon,
	BLOCK_DESCRIPTION as description,
} from './constants';

const { ancestor, ...configuration } = sharedConfig;

const blockConfig = {
	...configuration,
	apiVersion: 2,
	title,
	description,
	usesContext: [ 'query', 'queryId', 'postId' ],
	icon: { src: icon },
	attributes,
	supports,
	edit,
};

registerBlockType( 'woocommerce/product-price', blockConfig );

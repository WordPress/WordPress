/**
 * External dependencies
 */
import { registerExperimentalBlockType } from '@woocommerce/block-settings';

/**
 * Internal dependencies
 */
import sharedConfig from '../shared/config';
import edit from './edit';
import attributes from './attributes';
import {
	BLOCK_TITLE as title,
	BLOCK_ICON as icon,
	BLOCK_DESCRIPTION as description,
} from './constants';

const blockConfig = {
	title,
	description,
	icon: { src: icon },
	edit,
	attributes,
};

registerExperimentalBlockType( 'woocommerce/product-add-to-cart', {
	...sharedConfig,
	...blockConfig,
} );

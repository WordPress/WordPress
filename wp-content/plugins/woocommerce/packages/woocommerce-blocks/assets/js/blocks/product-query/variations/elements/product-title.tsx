/**
 * External dependencies
 */
import { Icon } from '@wordpress/components';
import {
	BLOCK_DESCRIPTION,
	BLOCK_TITLE,
} from '@woocommerce/atomic-blocks/product-elements/title/constants';
import { heading } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { registerElementVariation } from './utils';

export const CORE_NAME = 'core/post-title';
export const VARIATION_NAME = 'woocommerce/product-query/product-title';

registerElementVariation( CORE_NAME, {
	blockDescription: BLOCK_DESCRIPTION,
	blockIcon: <Icon icon={ heading } />,
	blockTitle: BLOCK_TITLE,
	variationName: VARIATION_NAME,
} );

/**
 * External dependencies
 */
import { Icon } from '@wordpress/components';
import {
	BLOCK_DESCRIPTION,
	BLOCK_TITLE,
} from '@woocommerce/atomic-blocks/product-elements/summary/constants';
import { page } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { registerElementVariation } from './utils';

export const CORE_NAME = 'core/post-excerpt';
export const VARIATION_NAME = 'woocommerce/product-query/product-summary';

registerElementVariation( CORE_NAME, {
	blockDescription: BLOCK_DESCRIPTION,
	blockIcon: <Icon icon={ page } />,
	blockTitle: BLOCK_TITLE,
	variationName: VARIATION_NAME,
} );

/**
 * External dependencies
 */
import { Icon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { layout } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { registerElementVariation } from './utils';

export const CORE_NAME = 'core/post-template';
export const VARIATION_NAME = 'woocommerce/product-query/product-template';

registerElementVariation( CORE_NAME, {
	blockDescription: __(
		'Contains the block elements used to render a product, like its name, featured image, rating, and more.',
		'woo-gutenberg-products-block'
	),
	blockIcon: <Icon icon={ layout } />,
	blockTitle: __( 'Product template', 'woo-gutenberg-products-block' ),
	variationName: VARIATION_NAME,
} );

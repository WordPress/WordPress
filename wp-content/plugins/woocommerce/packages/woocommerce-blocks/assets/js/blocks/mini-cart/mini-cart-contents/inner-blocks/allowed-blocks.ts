/**
 * External dependencies
 */
import { getBlockTypes } from '@wordpress/blocks';

const EXCLUDED_BLOCKS: readonly string[] = [
	'woocommerce/mini-cart',
	'woocommerce/checkout',
	'woocommerce/cart',
	'woocommerce/single-product',
	'woocommerce/cart-totals-block',
	'woocommerce/checkout-fields-block',
	'core/post-template',
	'core/comment-template',
	'core/query-pagination',
	'core/comments-query-loop',
	'core/post-comments-form',
	'core/post-comments-link',
	'core/post-comments-count',
	'core/comments-pagination',
	'core/post-navigation-link',
	'core/button',
];

export const getMiniCartAllowedBlocks = (): string[] =>
	getBlockTypes()
		.filter( ( block ) => {
			if ( EXCLUDED_BLOCKS.includes( block.name ) ) {
				return false;
			}

			// Exclude child blocks of EXCLUDED_BLOCKS.
			if (
				block.parent &&
				block.parent.filter( ( value ) =>
					EXCLUDED_BLOCKS.includes( value )
				).length > 0
			) {
				return false;
			}

			return true;
		} )
		.map( ( { name } ) => name );

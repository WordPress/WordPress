/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import { BLOCK_NAME as PRODUCT_BUTTON_BLOCK_NAME } from '../../atomic/blocks/product-elements/button/constants';

/**
 * The default layout built from the default template.
 */
export const DEFAULT_PRODUCT_LIST_LAYOUT = [
	[ 'woocommerce/product-image', { imageSizing: 'cropped' } ],
	[ 'woocommerce/product-title' ],
	[ 'woocommerce/product-price' ],
	[ 'woocommerce/product-rating' ],
	[ 'woocommerce/product-button' ],
];

/**
 * Converts innerblocks to a list of layout configs.
 *
 * @param {Object[]} innerBlocks Inner block components.
 */
export const getProductLayoutConfig = ( innerBlocks ) => {
	if ( ! innerBlocks || innerBlocks.length === 0 ) {
		return [];
	}

	return innerBlocks.map( ( block ) => {
		return [
			block.name,
			{
				...block.attributes,
				product: undefined,
				children:
					block.innerBlocks.length > 0
						? getProductLayoutConfig( block.innerBlocks )
						: [],
				/**
				 * Add custom width class to Add to cart button,
				 * This is needed to support "Width Setting" controls available in
				 * "woocommerce/product-button" block.
				 */
				...( block.name === PRODUCT_BUTTON_BLOCK_NAME && {
					className: classnames( block.attributes.className, {
						[ `has-custom-width wp-block-button__width-${ block.attributes?.width }` ]:
							block.attributes?.width,
					} ),
				} ),
				/**
				 * For product elements, special handing is required if product
				 * elements are used in the "All Products" block.
				 */
				isDescendantOfAllProducts: true,
			},
		];
	} );
};

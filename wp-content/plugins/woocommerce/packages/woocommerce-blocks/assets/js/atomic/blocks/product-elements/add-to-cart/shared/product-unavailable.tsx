/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';

const ProductUnavailable = ( {
	reason = __(
		'Sorry, this product cannot be purchased.',
		'woo-gutenberg-products-block'
	),
} ) => {
	return (
		<div className="wc-block-components-product-add-to-cart-unavailable">
			{ reason }
		</div>
	);
};

export default ProductUnavailable;

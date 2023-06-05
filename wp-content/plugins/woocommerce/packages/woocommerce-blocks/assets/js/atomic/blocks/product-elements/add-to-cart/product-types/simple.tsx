/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useAddToCartFormContext } from '@woocommerce/base-context';

/**
 * Internal dependencies
 */
import { AddToCartButton, QuantityInput, ProductUnavailable } from '../shared';

/**
 * Simple Product Add To Cart Form
 */
const Simple = () => {
	// @todo Add types for `useAddToCartFormContext`
	const {
		product,
		quantity,
		minQuantity,
		maxQuantity,
		multipleOf,
		dispatchActions,
		isDisabled,
	} = useAddToCartFormContext();

	if ( product.id && ! product.is_purchasable ) {
		return <ProductUnavailable />;
	}

	if ( product.id && ! product.is_in_stock ) {
		return (
			<ProductUnavailable
				reason={ __(
					'This product is currently out of stock and cannot be purchased.',
					'woo-gutenberg-products-block'
				) }
			/>
		);
	}

	return (
		<>
			<QuantityInput
				value={ quantity }
				min={ minQuantity }
				max={ maxQuantity }
				step={ multipleOf }
				disabled={ isDisabled }
				onChange={ dispatchActions.setQuantity }
			/>
			<AddToCartButton />
		</>
	);
};

export default Simple;

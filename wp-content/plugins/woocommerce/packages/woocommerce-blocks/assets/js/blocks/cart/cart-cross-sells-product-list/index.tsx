/**
 * External dependencies
 */
import { ProductResponseItem } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import CartCrossSellsProduct from './cart-cross-sells-product';

interface CrossSellsProductListProps {
	products: ProductResponseItem[];
	className?: string | undefined;
	columns: number;
}

const CartCrossSellsProductList = ( {
	products,
	columns,
}: CrossSellsProductListProps ): JSX.Element => {
	const crossSellsProducts = products.map( ( product, i ) => {
		if ( i >= columns ) return null;

		return (
			<CartCrossSellsProduct
				// Setting isLoading to false, given this parameter is required.
				isLoading={ false }
				product={ product }
				key={ product.id }
			/>
		);
	} );

	return <div>{ crossSellsProducts }</div>;
};

export default CartCrossSellsProductList;

/**
 * External dependencies
 */
import {
	InnerBlockLayoutContextProvider,
	ProductDataContextProvider,
} from '@woocommerce/shared-context';
import { ProductResponseItem } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { Block as ProductImage } from '../../../atomic/blocks/product-elements/image/block';
import { Block as ProductName } from '../../../atomic/blocks/product-elements/title/block';
import { Block as ProductRating } from '../../../atomic/blocks/product-elements/rating/block';
import { Block as ProductSaleBadge } from '../../../atomic/blocks/product-elements/sale-badge/block';
import { Block as ProductPrice } from '../../../atomic/blocks/product-elements/price/block';
import { Block as ProductButton } from '../../../atomic/blocks/product-elements/button/block';
import AddToCartButton from '../../../atomic/blocks/product-elements/add-to-cart/block';

interface CrossSellsProductProps {
	product: ProductResponseItem;
	isLoading: boolean;
}

const CartCrossSellsProduct = ( {
	product,
}: CrossSellsProductProps ): JSX.Element => {
	return (
		<div className="cross-sells-product">
			<InnerBlockLayoutContextProvider
				parentName={ 'woocommerce/cart-cross-sells-block' }
				parentClassName={ 'wp-block-cart-cross-sells-product' }
			>
				<ProductDataContextProvider
					// Setting isLoading to false, given this parameter is required.
					isLoading={ false }
					product={ product }
				>
					<div>
						<ProductImage
							className={ '' }
							showSaleBadge={ false }
							productId={ product.id }
							showProductLink={ false }
							saleBadgeAlign={ 'left' }
							imageSizing={ 'full-size' }
							isDescendentOfQueryLoop={ false }
						/>
						<ProductName
							align={ '' }
							headingLevel={ 3 }
							showProductLink={ true }
						/>
						<ProductRating />
						<ProductSaleBadge
							productId={ product.id }
							align={ 'left' }
						/>
						<ProductPrice />
					</div>
					{ product.is_in_stock ? (
						<AddToCartButton />
					) : (
						<ProductButton />
					) }
				</ProductDataContextProvider>
			</InnerBlockLayoutContextProvider>
		</div>
	);
};

export default CartCrossSellsProduct;

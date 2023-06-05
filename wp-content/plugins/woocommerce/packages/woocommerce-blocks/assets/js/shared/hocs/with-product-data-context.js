/**
 * External dependencies
 */
import { useStoreProducts } from '@woocommerce/base-context/hooks';
import {
	ProductDataContextProvider,
	useProductDataContext,
} from '@woocommerce/shared-context';

/**
 * Loads the product from the API and adds to the context provider.
 *
 * @param {Object} props Component props.
 */
const OriginalComponentWithContext = ( props ) => {
	const { productId, OriginalComponent, postId, product } = props;

	const id = props?.isDescendentOfQueryLoop ? postId : productId;

	const { products, productsLoading } = useStoreProducts( {
		include: id,
	} );

	const productFromAPI = {
		product: id > 0 && products.length > 0 ? products[ 0 ] : null,
		isLoading: productsLoading,
	};

	if ( product ) {
		return (
			<ProductDataContextProvider product={ product } isLoading={ false }>
				<OriginalComponent { ...props } />
			</ProductDataContextProvider>
		);
	}

	return (
		<ProductDataContextProvider
			product={ productFromAPI.product }
			isLoading={ productFromAPI.isLoading }
		>
			<OriginalComponent { ...props } />
		</ProductDataContextProvider>
	);
};

/**
 * This HOC sees if the Block is wrapped in Product Data Context, and if not, wraps it with context
 * based on the productId attribute, if set.
 *
 * @param {Function} OriginalComponent Component being wrapped.
 */
export const withProductDataContext = ( OriginalComponent ) => {
	return ( props ) => {
		const productDataContext = useProductDataContext();

		// If a product prop was provided, use this as the context for the tree.
		if ( !! props.product || ! productDataContext.hasContext ) {
			return (
				<OriginalComponentWithContext
					{ ...props }
					OriginalComponent={ OriginalComponent }
				/>
			);
		}

		return <OriginalComponent { ...props } />;
	};
};

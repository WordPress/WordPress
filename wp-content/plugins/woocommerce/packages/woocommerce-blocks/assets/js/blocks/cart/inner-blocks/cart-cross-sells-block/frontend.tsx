/**
 * External dependencies
 */
import { useStoreCart } from '@woocommerce/base-context/hooks';

interface Props {
	children?: JSX.Element | JSX.Element[];
	className?: string;
}

const FrontendBlock = ( {
	children,
	className = '',
}: Props ): JSX.Element | null => {
	const { crossSellsProducts, cartIsLoading } = useStoreCart();

	if ( cartIsLoading || crossSellsProducts.length < 1 ) {
		return null;
	}

	return <div className={ className }>{ children }</div>;
};

export default FrontendBlock;

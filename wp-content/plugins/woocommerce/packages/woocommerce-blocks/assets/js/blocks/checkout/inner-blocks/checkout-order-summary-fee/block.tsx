/**
 * External dependencies
 */
import { TotalsFees, TotalsWrapper } from '@woocommerce/blocks-checkout';
import { getCurrencyFromPriceResponse } from '@woocommerce/price-format';
import { useStoreCart } from '@woocommerce/base-context/hooks';

const Block = ( { className = '' }: { className?: string } ): JSX.Element => {
	const { cartFees, cartTotals } = useStoreCart();
	const totalsCurrency = getCurrencyFromPriceResponse( cartTotals );

	return (
		<TotalsWrapper className={ className }>
			<TotalsFees currency={ totalsCurrency } cartFees={ cartFees } />
		</TotalsWrapper>
	);
};

export default Block;

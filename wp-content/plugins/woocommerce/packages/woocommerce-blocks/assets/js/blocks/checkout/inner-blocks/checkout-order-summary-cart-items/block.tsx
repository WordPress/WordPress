/**
 * External dependencies
 */
import { OrderSummary } from '@woocommerce/base-components/cart-checkout';
import { useStoreCart } from '@woocommerce/base-context/hooks';
import { TotalsWrapper } from '@woocommerce/blocks-checkout';

const Block = ( { className }: { className: string } ): JSX.Element => {
	const { cartItems } = useStoreCart();

	return (
		<TotalsWrapper className={ className }>
			<OrderSummary cartItems={ cartItems } />
		</TotalsWrapper>
	);
};

export default Block;

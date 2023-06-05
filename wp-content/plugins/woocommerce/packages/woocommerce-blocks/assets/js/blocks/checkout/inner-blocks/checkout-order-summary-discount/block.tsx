/**
 * External dependencies
 */
import { TotalsDiscount } from '@woocommerce/base-components/cart-checkout';
import { getCurrencyFromPriceResponse } from '@woocommerce/price-format';
import {
	useStoreCartCoupons,
	useStoreCart,
} from '@woocommerce/base-context/hooks';
import {
	ExperimentalDiscountsMeta,
	TotalsWrapper,
} from '@woocommerce/blocks-checkout';

const DiscountSlotFill = (): JSX.Element => {
	// Prepare props to pass to the ExperimentalOrderMeta slot fill. We need to pluck out receiveCart.
	// eslint-disable-next-line no-unused-vars
	const { extensions, receiveCart, ...cart } = useStoreCart();
	const discountsSlotFillProps = {
		extensions,
		cart,
		context: 'woocommerce/checkout',
	};

	return <ExperimentalDiscountsMeta.Slot { ...discountsSlotFillProps } />;
};

const Block = ( { className = '' }: { className?: string } ): JSX.Element => {
	const { cartTotals, cartCoupons } = useStoreCart();
	const { removeCoupon, isRemovingCoupon } =
		useStoreCartCoupons( 'wc/checkout' );
	const totalsCurrency = getCurrencyFromPriceResponse( cartTotals );

	return (
		<>
			<TotalsWrapper className={ className }>
				<TotalsDiscount
					cartCoupons={ cartCoupons }
					currency={ totalsCurrency }
					isRemovingCoupon={ isRemovingCoupon }
					removeCoupon={ removeCoupon }
					values={ cartTotals }
				/>
			</TotalsWrapper>
			<DiscountSlotFill />
		</>
	);
};

export default Block;

/**
 * External dependencies
 */
import { ExperimentalOrderMeta } from '@woocommerce/blocks-checkout';
import { useStoreCart } from '@woocommerce/base-context/hooks';

// @todo Consider deprecating OrderMetaSlotFill and DiscountSlotFill in favour of inner block areas.
export const OrderMetaSlotFill = (): JSX.Element => {
	// Prepare props to pass to the ExperimentalOrderMeta slot fill. We need to pluck out receiveCart.
	// eslint-disable-next-line no-unused-vars
	const { extensions, receiveCart, ...cart } = useStoreCart();
	const slotFillProps = {
		extensions,
		cart,
		context: 'woocommerce/checkout',
	};

	return <ExperimentalOrderMeta.Slot { ...slotFillProps } />;
};

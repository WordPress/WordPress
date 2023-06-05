/**
 * External dependencies
 */
import { ExperimentalOrderMeta } from '@woocommerce/blocks-checkout';
import { useStoreCart } from '@woocommerce/base-context/hooks';

export const OrderMetaSlotFill = (): JSX.Element => {
	// Prepare props to pass to the ExperimentalOrderMeta slot fill. We need to pluck out receiveCart.
	// eslint-disable-next-line no-unused-vars
	const { extensions, receiveCart, ...cart } = useStoreCart();
	const slotFillProps = {
		extensions,
		cart,
		context: 'woocommerce/cart',
	};

	return <ExperimentalOrderMeta.Slot { ...slotFillProps } />;
};

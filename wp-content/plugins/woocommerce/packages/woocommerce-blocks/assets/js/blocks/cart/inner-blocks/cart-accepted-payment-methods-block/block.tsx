/**
 * External dependencies
 */
import { PaymentMethodIcons } from '@woocommerce/base-components/cart-checkout';
import { usePaymentMethods } from '@woocommerce/base-context/hooks';
import { getIconsFromPaymentMethods } from '@woocommerce/base-utils';

const Block = ( { className }: { className: string } ): JSX.Element => {
	const { paymentMethods } = usePaymentMethods();

	return (
		<PaymentMethodIcons
			className={ className }
			icons={ getIconsFromPaymentMethods( paymentMethods ) }
		/>
	);
};

export default Block;

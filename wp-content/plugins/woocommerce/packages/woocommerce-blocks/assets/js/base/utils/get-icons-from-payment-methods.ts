/**
 * External dependencies
 */
import type {
	PaymentMethods,
	PaymentMethodIcons as PaymentMethodIconsType,
} from '@woocommerce/types';

/**
 * Get the provider icons from payment methods data.
 *
 * @param {PaymentMethods} paymentMethods Payment Method data
 * @return {PaymentMethodIconsType} Payment Method icons data.
 */
export const getIconsFromPaymentMethods = (
	paymentMethods: PaymentMethods
): PaymentMethodIconsType => {
	return Object.values( paymentMethods ).reduce( ( acc, paymentMethod ) => {
		if ( paymentMethod.icons !== null ) {
			acc = acc.concat( paymentMethod.icons );
		}
		return acc;
	}, [] as PaymentMethodIconsType );
};
